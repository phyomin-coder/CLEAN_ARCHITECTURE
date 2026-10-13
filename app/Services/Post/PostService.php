<?php
namespace App\Services\Post;

use App\Api\Foundation\Exceptions\NotFoundResourceException;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Repositories\Post\PostRepositoryInterface;
use App\Services\CommonService;
use Exception;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
class PostService extends CommonService{
    protected PostRepositoryInterface $postRepository;
    public function __construct(PostRepositoryInterface $postRepository){
        $this->postRepository = $postRepository;
    }

    public function getAll($request)
    { 
        $data = ['data' => [], 'count' => 0];
        $params = $this->params($request);
        $params = array_merge($params, $this->getFilterParams($request));
        $posts = $this->postRepository->all($params);

        if ($posts->isNotEmpty()) {
            $data['data'] = PostResource::collection($posts)->toArray($request);

            unset($params['limit'], $params['offset']);

            $data['count'] = $this->postRepository->totalCount($params);
        }

        return $data;
    }

    public function createPost(Request $request){
        $input=$this->input($request,$this->postRepository->connection()->getFillable());
        try {
            DB::beginTransaction();

            $prepareInput = $this->prepareInput($request);

            $input = array_merge($input, $prepareInput);

            $post = $this->postRepository->insert($input); 

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return ['stored' => false, 'message' => $e->getMessage()];
        }

        return ['stored' => true];
    }

    public function getPostById($id) : mixed
    {
        $post = $this->postRepository->getDataById($id);
        if (!$post) {
            throw new NotFoundResourceException();
        }
        return $post;
    }
    public function updatePost(Request $request, $id) : mixed{
         $input = $this->input($request, $this->postRepository->connection()->getFillable());

        try {
            DB::beginTransaction();

            $prepareInput = $this->prepareInput($request);

            $input = array_merge($input, $prepareInput);

            $this->postRepository->update($input, $id);

            $post = $this->getPostById($id);
 

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }

        return (new PostResource($post))->toArray($request);
    }
    public function deletePost($id) : array
    {
        $post = $this->getPostById($id);

        try {
            DB::beginTransaction();

            $this->postRepository->destroy($post->id);

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