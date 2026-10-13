<?php
namespace App\Services\ToDo;

use App\Api\Foundation\Exceptions\NotFoundResourceException;
use App\Http\Resources\ToDoResource;
use App\Repositories\ToDo\ToDoRepositoryInterface;
use App\Services\CommonService;
use Exception;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
class ToDoService extends CommonService{
    protected ToDoRepositoryInterface $toDoRepository;
    public function __construct(ToDoRepositoryInterface $toDoRepository)
    {
        $this->toDoRepository = $toDoRepository;
    }
    public function getAllToDo($request){
        $data = ['data' => [], 'count' => 0];
        $params = $this->params($request);
        $params = array_merge($params, $this->getFilterParams($request));
        $todos = $this->toDoRepository->all($params);

        if ($todos->isNotEmpty()) {
            $data['data'] = ToDoResource::collection($todos)->toArray($request);

            unset($params['limit'], $params['offset']);

            $data['count'] = $this->toDoRepository->totalCount($params);
        }

        return $data;
    }

    public function createToDo(Request $request){
        $input=$this->input($request,$this->toDoRepository->connection()->getFillable());
        $created_by= auth()->id();
        $input['created_by']=$created_by;
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
    public function getToDoById($id){
         $todo = $this->toDoRepository->getDataById($id);
        if (!$todo) {
            throw new NotFoundResourceException();
        }
        return $todo;
    }

    public function updateToDo(Request $request, $id) : mixed{
         $input = $this->input($request, $this->toDoRepository->connection()->getFillable());
        $updated_by= auth()->id();
        $input['updated_by']=$updated_by;
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

    public function deleteToDo($id) : array
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
     protected function getFilterParams(Request $request, $wantFilter = true): array
    {
        $params = [];

        if ($request->filled(key: 'search')) {
            $params['search'] = $request->get(key: 'search');
        }

        if ($request->filled(key: 'type')) {
            $params['type'] = $request->get(key: 'type');
        }

        if ($request->filled(key: 'filter') && !empty($request->get(key: 'filter')) && $wantFilter) {
            $params['filter'] = (int)$request->get(key: 'filter');
        }

        if ($request->filled(key: 'sort_by')) {
            $params['sort_by'] = $request->get(key: 'sort_by');
        }

        if ($request->filled(key: 'order_by')) {
            $params['order_by'] = $request->get(key: 'order_by');
        }

        return $params;
    }
}