<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdateUserInfoRequest;
use App\Http\Resources\UserEventResource;
use App\Http\Resources\UserInfoResource;
use App\Http\Resources\UserLoginResource;
use App\Http\Resources\UserRegisterResource;
use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Services\UserService;
use App\Http\Services\EventService;
use Exception;

class UserController extends Controller
{
    protected $userService;
    protected $eventService;

    public function __construct(
        UserService $userService,
        EventService $eventService
    ) {
        $this->userService = $userService;
        $this->eventService = $eventService;
    }

    /**
     * Handle user registration.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->userService->register($request->validated());
            return response()->json(SuccessResponse::create('success', 'User registered successfully.', new UserRegisterResource($result)), 201);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }

    /**
     * Handle user login.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->userService->login($request->validated());
            if ($result) {
                return response()->json(SuccessResponse::create('success', 'User logged in successfully.', new UserLoginResource($result)), 200);
            }
            return response()->json(ErrorResponse::create('error', 'Invalid email or password.', []), 401);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }

    /**
     * Handle user logout from current session/device.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->userService->logout($request->user());
            return response()->json(SuccessResponse::create('success', 'User logged out from this device successfully.', [], 200));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }

    /**
     * Handle user logout from all devices/sessions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logoutAllDevices(Request $request): JsonResponse
    {
        try {
            $this->userService->logoutAllDevices($request->user());
            return response()->json(SuccessResponse::create('success', 'User logged out from all devices successfully.', [], 200));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }

    /**
     * Get authenticated user's information.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUser(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if ($user !== null) {
                return response()->json(SuccessResponse::create('success', 'User data retrieved successfully.', new UserInfoResource($user)), 200);
            }
            return response()->json(ErrorResponse::create('error', 'No authenticated user', []), 401);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }

    /**
     * Handle user info updates.
     *
     * @param UpdateUserInfoRequest $request
     * @return JsonResponse
     */
    public function updateUser(UpdateUserInfoRequest $request): JsonResponse
    {
        try {
            $updatedUser = $this->userService->updateUser($request->validated(), $request->user());
            return response()->json(SuccessResponse::create('success', 'User information updated successfully.', $updatedUser, 201));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }

    /**
     * Get all events details along with participants if the user is authorized to access it.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllEventsForUser(Request $request): JsonResponse
    {
        try {
            $events = $this->userService->getAllEventsForUser($request->user());
            if ($events) {
                return response()->json(SuccessResponse::create('success', 'User events data retrieved successfully.', UserEventResource::collection($events)), 200);
            }
            return response()->json(SuccessResponse::create('error', 'Events not found', []), 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }

    /**
     * Get event details along with participants if the user is authorized to access it.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function getEventForUser(Request $request, int $id): JsonResponse
    {
        try {
            $event = $this->userService->getEventForUser($request->user(), $id);
            if ($event) {
                return response()->json(SuccessResponse::create('success', 'User event data retrieved successfully.', new UserEventResource($event)), 200);
            }
            return response()->json(SuccessResponse::create('error', 'Event not found', []), 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }

    /**
     * Search for users based on a query string.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchUsers(Request $request): JsonResponse
    {
        try {
            $query = $request->input('query');
            $users = $this->userService->searchUsers($query);
            if (!$users->isEmpty()) {
                return response()->json(SuccessResponse::create('success', 'Users data retrieved successfully', UserInfoResource::collection($users)), 200);
            }
            return response()->json(SuccessResponse::create('error', 'No users found', []), 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }

        return response()->json($users);
    }

    // Other methods for handling user-related actions can be added here
}
