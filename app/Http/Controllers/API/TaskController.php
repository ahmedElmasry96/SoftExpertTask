<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Tasks\TaskRequest;
use App\Http\Requests\API\Tasks\UpdateTaskStatusRequest;
use App\Services\TaskService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use ApiResponseTrait;

    private TaskService $taskService;
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Request $request): JsonResponse
    {
        return $this->taskService->getAllTasks($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request): JsonResponse
    {
        return $this->taskService->createTask($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        return $this->taskService->showDetails($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, string $id): JsonResponse
    {
        return $this->taskService->updateTask($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        return $this->taskService->deleteTask($id);
    }

    public function updateStatus(UpdateTaskStatusRequest $request, string $id): JsonResponse
    {
        return $this->taskService->updateStatus($request, $id);
    }


}
