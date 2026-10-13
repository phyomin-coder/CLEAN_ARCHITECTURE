<?php

namespace App\Http\Controllers;

use App\Api\Foundation\Routing\Controller;
use App\Http\Requests\ToDo\StoreToDoRequest;
use App\Http\Requests\ToDo\UpdateToDoRequest;
use App\Services\ToDo\ToDoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request; 

class ToDoController extends Controller
{
    protected ToDoService $toDoService;
    public function __construct(ToDoService $toDoService)
    {
        $this->toDoService = $toDoService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    { 
        $response=$this->toDoService->getAllToDo($request);  
        return $this->response($response["data"], $response["count"],true);
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(StoreToDoRequest $request) : JsonResponse
    {
    $post = $this->toDoService->createToDo($request);
    return $this->response(array($post));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $todo = $this->toDoService->getToDoById($id);
        return $this->response(array($todo));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateToDoRequest $request, string $todo)
    {
       $response=$this->toDoService->updateToDo($request, $todo);
        return $this->response($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $response = $this->toDoService->deleteToDo($id);

        return $this->response((array)$response);
    }
}
