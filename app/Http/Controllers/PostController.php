<?php

namespace App\Http\Controllers;

use App\Api\Foundation\Routing\Controller;
use App\Http\Requests\Admin\StorePostRequest;
use App\Http\Requests\Admin\UpdatePostRequest;
use App\Services\Admin\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected PostService $postService;
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }
    
    public function index(Request $request) : JsonResponse
    { 
        $response=$this->postService->getAll($request); 
        return $this->response($response["data"], $response["count"],true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request) : JsonResponse
    {
    $post = $this->postService->createPost($request);
    return $this->response(array($post));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = $this->postService->getPostById($id);
        return $this->response(array($post));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, string $post)
    { 
        $response=$this->postService->updatePost($request, $post);
        return $this->response($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         $response = $this->postService->deletePost($id);

        return $this->response((array)$response);
    }
}
