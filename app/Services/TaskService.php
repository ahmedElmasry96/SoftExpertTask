<?php

namespace App\Services;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskDependency;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class TaskService
{
    use ApiResponseTrait;

    public function getAllTasks(Request $request): JsonResponse
    {
        $user = auth()->user();
        $query = $this->applyFilters(Task::query(), $request, $user);
        $tasks = $query->orderByDesc('id')->get();
        $tasks->load([
            'created_by_user', 'assigned_to_user', 'dependent_tasks', 'dependent_tasks.created_by_user', 'dependent_tasks.assigned_to_user',
        ]);

        return $this->returnData(TaskResource::collection($tasks), 'Tasks retrieved successfully.');
    }

    private function applyFilters($query, Request $request, $user)
    {
        if ($user->role != 'manager') {
            $query->where('assigned_to_user_id', $user->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('due_date_from')) {
            $query->whereDate('due_date', '>=', Carbon::parse($request->due_date_from)->format('Y-m-d'));
        }
        if ($request->has('due_date_to')) {
            $query->whereDate('due_date', '<=', Carbon::parse($request->due_date_to)->format('Y-m-d'));
        }

        if ($request->has('assigned_to_user_id')) {
            $query->where('assigned_to_user_id', $request->assigned_to_user_id);
        }

        return $query;
    }

    public function createTask(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $task = $this->storeTask($request);
            $this->createTaskDependencies($task, $request->dependent_on_task_ids);

            DB::commit();

            $task->load([
                'created_by_user', 'assigned_to_user', 'dependent_tasks', 'dependent_tasks.created_by_user', 'dependent_tasks.assigned_to_user',
            ]);

            return $this->returnData(new TaskResource($task), "Task created successfully.", Response::HTTP_CREATED);

        } catch (Exception $e) {
            DB::rollBack();
            return $this->returnError('Error, please try again', Response::HTTP_BAD_REQUEST);
        }
    }

    private function storeTask(Request $request)
    {
        return Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => $request->status ?? 'pending',
            'assigned_to_user_id' => $request->assigned_to_user_id,
            'created_by_user_id' => auth()->user()->id,
        ]);
    }

    public function updateTask(Request $request, string $id): JsonResponse
    {
        $task = Task::findOrFail($id);
        try {
            DB::beginTransaction();
            $checkForDependentTasks = $this->checkForDependentTasks($task, $request->status);
            if ($checkForDependentTasks) return $checkForDependentTasks;

            $task->update([
                'title' => $request->title,
                'description' => $request->description ?? $task->description,
                'due_date' => $request->due_date,
                'status' => $request->status ?? $task->status,
                'assigned_to_user_id' => $request->assigned_to_user_id ?? $task->assigned_to_user_id,
            ]);

            $this->deleteOldTaskDependencies($request, $task);
            $this->createTaskDependencies($task, $request->dependent_on_task_ids);

            DB::commit();

            $task->load([
                'created_by_user', 'assigned_to_user', 'dependent_tasks', 'dependent_tasks.created_by_user', 'dependent_tasks.assigned_to_user',
            ]);

            return $this->returnData(new TaskResource($task), "Task updated successfully.", Response::HTTP_CREATED);

        } catch (Exception $e) {
            DB::rollBack();
            return $this->returnError('Error, Please try again', Response::HTTP_BAD_REQUEST);
        }
    }

    private function createTaskDependencies(Task $task, ?array $dependentTaskIds): void
    {
        if (empty($dependentTaskIds)) {
            return;
        }

        $dependencies = [];
        $timestamp = now();

        foreach ($dependentTaskIds as $dependentTaskId) {
            $dependencies[] = [
                'task_id' => $task->id,
                'dependent_on_task_id' => $dependentTaskId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        TaskDependency::insert($dependencies);
    }

    private function deleteOldTaskDependencies(Request $request, Task $task): void
    {
        if ($request->dependent_on_task_ids) {
            TaskDependency::where('task_id',  $task->id)->delete();
        }
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $task = Task::findOrFail($id);
        Gate::authorize('updateStatus', $task);
        try {
            $checkForDependentTasks = $this->checkForDependentTasks($task, $request->status);
            if ($checkForDependentTasks) return $checkForDependentTasks;

            $task->update([
                'status' => $request->status,
            ]);

            $task->load([
                'created_by_user', 'assigned_to_user', 'dependent_tasks', 'dependent_tasks.created_by_user', 'dependent_tasks.assigned_to_user',
            ]);

            return $this->returnData(new TaskResource($task), "Task status updated successfully.", Response::HTTP_CREATED);

        } catch (Exception $e) {
            return $this->returnError('Error, Please try again', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @throws Exception
     */
    private function checkForDependentTasks(Task $task, $status): false|JsonResponse
    {
        if ($task->dependent_tasks->isNotEmpty() && $status === 'completed') {
            $dependent_tasks_status = $task->dependent_tasks->pluck('status')->toArray();

            if (!in_array('completed', $dependent_tasks_status, true)) {
                return $this->returnError('This task has dependent tasks not completed.', Response::HTTP_BAD_REQUEST);
            }
        }
        return false;
    }

    public function showDetails(string $id): JsonResponse
    {
        $task = Task::findOrFail($id);
        Gate::authorize('view', $task);
        return $this->returnData(new TaskResource($task));
    }

    public function deleteTask(string $id): JsonResponse
    {
        $task = Task::findOrFail($id);
        Gate::authorize('delete', $task);
        try {
            DB::beginTransaction();
            $task->dependent_tasks()->delete();
            $task->delete();
            DB::commit();
            return $this->returnSuccessMessage('Task deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->returnError('Error, Please try again', Response::HTTP_BAD_REQUEST);
        }

    }
}
