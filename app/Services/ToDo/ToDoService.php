<?php

namespace App\Services\ToDo;

use App\Api\Foundation\Exceptions\NotFoundResourceException;
use App\Http\Resources\ToDoResource;
use App\Repositories\ToDo\ToDoRepositoryInterface;
use App\Services\CommonService;
use Exception;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class ToDoService extends CommonService
{
    protected ToDoRepositoryInterface $toDoRepository;
    public function __construct(ToDoRepositoryInterface $toDoRepository)
    {
        $this->toDoRepository = $toDoRepository;
    }
    public function getAllToDo($request)
    {
        $data = ['data' => [], 'count' => 0];
        $params = $this->params($request);
        $params = array_merge($params, $this->getFilterParams($request));

        $params['with'] = ['creator', 'updater'];
        $todos = $this->toDoRepository->all($params);

        if ($todos->isNotEmpty()) {
            $data['data'] = ToDoResource::collection($todos)->toArray($request);

            unset($params['limit'], $params['offset']);

            $data['count'] = $this->toDoRepository->totalCount($params);
        }

        return $data;
    }

    public function createToDo(Request $request)
    {
        $input = $this->input($request, $this->toDoRepository->connection()->getFillable());
        try {
            DB::beginTransaction();

            $prepareInput = $this->prepareInput($request);

            $input = array_merge($input, $prepareInput);

            $post = $this->toDoRepository->insert($input);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return ['stored' => false, 'message' => $e->getMessage()];
        }

        return ['stored' => true];
    }
    public function getToDoById(string $id)
    {
        $relations = ['creator', 'updater'];
        $todo = $this->toDoRepository->getDataById($id, $relations);
        if (!$todo) {
            throw new NotFoundResourceException();
        }
        return (new ToDoResource($todo));
    }

    public function updateToDo(Request $request, string $id): mixed
    {
        $input = $this->input($request, $this->toDoRepository->connection()->getFillable());
        try {
            DB::beginTransaction();

            $prepareInput = $this->prepareInput($request);

            $input = array_merge($input, $prepareInput);

            $this->toDoRepository->update($input, $id);

            $todo = $this->getToDoById($id);


            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }

        return (new ToDoResource($todo))->toArray($request);
    }

    public function deleteToDo($id): array
    {
        $todo = $this->getToDoById($id);

        try {
            DB::beginTransaction();

            $this->toDoRepository->destroy($todo->id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return ['deleted' => false, 'message' => $e->getMessage()];
        }

        return ['deleted' => true];
    }
}
