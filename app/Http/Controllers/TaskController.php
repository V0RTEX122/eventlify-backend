<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\CreateEventTaskRequest;
use App\Http\Requests\EventTaskRequest;
use App\Http\Resources\EventTaskResource;
use App\Http\Services\TaskService;
use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
use Exception;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Create a new task associated with a specific event.
     *
     * @param CreateEventTaskRequest $request
     * @param int $eventId
     * @return JsonResponse
     */
    public function createEventTask(CreateEventTaskRequest $request, int $eventId): JsonResponse
    {
        try {
            $result = $this->taskService->createUserTask($request->user(), $request->validated(), $eventId);
            if ($result) {
                return response()->json(SuccessResponse::create('success', 'User event task created successfully.', new EventTaskResource($result)), 201);
            }
            return response()->json(SuccessResponse::create('error', 'User or event not found', []), 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }

    /**
     * Retrieve all tasks associated with a specific event.
     *
     * @param EventTaskRequest $request
     * @param int $eventId
     * @return JsonResponse
     */
    public function getEventTasks(EventTaskRequest $request, int $eventId): JsonResponse
    {
        try {
            $tasks = $this->taskService->getEventTasks($request->user(), $request->validated(), $eventId);
            if (!$tasks->isEmpty()) {
                return response()->json(SuccessResponse::create('success', 'Tasks data retrieved successfully.', EventTaskResource::collection($tasks)), 200);
            }
            return response()->json(SuccessResponse::create('error', 'No tasks found', []), 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }

    /**
     * Retrieve a specific task by ID for an event.
     *
     * @param EventTaskRequest $request
     * @param int $taskId
     * @return JsonResponse
     */
    public function getEventTaskById(EventTaskRequest $request, int $taskId): JsonResponse
    {
        try {
            $task = $this->taskService->getEventTaskById($request->user(), $request->validated(), $taskId);
            if ($task) {
                return response()->json(SuccessResponse::create('success', 'Task data retrieved successfully.', new EventTaskResource($task)), 200);
            }
            return response()->json(SuccessResponse::create('error', 'Task not found', []), 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }

    /**
     * Update a specific task by ID for an event.
     *
     * @param EventTaskRequest $request
     * @param int $taskId
     * @return JsonResponse
     */
    public function updateEventTask(EventTaskRequest $request, int $taskId): JsonResponse
    {
        try {
            $invitation = $this->taskService->updateEventTask($request->user(), $request->validated(), $taskId);
            if ($invitation) {
                return response()->json(SuccessResponse::create('success', 'Task data updated successfully.', new EventTaskResource($invitation)), 200);
            }
            return response()->json(SuccessResponse::create('error', 'Task not found', []), 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }
}
