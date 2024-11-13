<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use App\Http\Services\EventService;
use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
use Exception;

class EventController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Store a new event.
     *
     * @param EventRequest $request
     * @return JsonResponse
     */
    public function storeEvent(EventRequest $request): JsonResponse
    {
        try {
            $event = $this->eventService->createEvent($request->validated());
            return response()->json(SuccessResponse::create('success', 'Event created successfully.', new EventResource($event)), 201);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }

    /**
     * Retrieve all events.
     *
     * @return JsonResponse
     */
    public function getAllEvents(): JsonResponse
    {
        try {
            $events = $this->eventService->getAllEvents();
            return response()->json(SuccessResponse::create('success', 'Events data retrieved successfully.', EventResource::collection($events)), 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }

    /**
     * Retrieve an event by its ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getEvent(int $id): JsonResponse
    {
        try {
            $event = $this->eventService->getEvent($id);
            if ($event) {
                return response()->json(SuccessResponse::create('success', 'Event data retrieved successfully.', new EventResource($event)), 200);
            }
            return response()->json(ErrorResponse::create('error', 'Event not found.', []), 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }

    /**
     * Update an event by its ID.
     *
     * @param EventRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateEvent(EventRequest $request, int $id): JsonResponse
    {
        try {
            $event = $this->eventService->getEvent($id);
            if ($event) {
                $updatedEvent = $this->eventService->updateEvent($event, $request->validated());
                return response()->json(SuccessResponse::create('success', 'Event data updated successfully.', new EventResource($updatedEvent)), 201);
            }
            return response()->json(ErrorResponse::create('error', 'Event not found.', []), 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }

    /**
     * Delete an event by its ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deleteEvent(int $id): JsonResponse
    {
        try {
            $event = $this->eventService->getEvent($id);
            if ($event) {
                $this->eventService->deleteEvent($event);
                return response()->json(SuccessResponse::create('success', 'Event deleted successfully.', []), 200);
            }
            return response()->json(ErrorResponse::create('error', 'Event not found.', []), 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }
}

