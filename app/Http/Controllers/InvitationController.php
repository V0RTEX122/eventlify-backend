<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\InvitationRequest;
use App\Http\Requests\GetUserInvitationsRequest;
use App\Http\Requests\TakeInvitationActionRequest;
use App\Http\Resources\InvitationInfoResource;
use App\Http\Services\InvitationService;
use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
use Exception;

class InvitationController extends Controller
{
    protected $invitationService;

    public function __construct(InvitationService $invitationService)
    {
        $this->invitationService = $invitationService;
    }

    /**
     * Send an invitation to a user by email for a specific event.
     *
     * @param InvitationRequest $request
     * @param int $eventId
     * @return JsonResponse
     */
    public function sendInvitation(InvitationRequest $request, int $eventId): JsonResponse
    {
        try {
            $result = $this->invitationService->sendInvitation($request->user(), $request->validated(), $eventId);
            if ($result) {
                return response()->json(SuccessResponse::create('success', 'User invited successfully.', new InvitationInfoResource($result)), 201);
            }
            return response()->json(SuccessResponse::create('error', 'User or event not found', []), 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }

    /**
     * Get all pending invitations for the authenticated user.
     *
     * @param InvitationRequest $request
     * @return JsonResponse
     */
    public function getUserInvitations(GetUserInvitationsRequest $request): JsonResponse
    {
        try {
            $invitations = $this->invitationService->getUserInvitations($request->user(), $request->validated());
            if (!$invitations->isEmpty()) {
                return response()->json(SuccessResponse::create('success', 'Invitations data retrieved successfully.', InvitationInfoResource::collection($invitations)), 200);
            }
            return response()->json(SuccessResponse::create('error', 'No invitations found', []), 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }

    /**
     * Take action on an invitation based on the user's request.
     *
     * @param TakeInvitationActionRequest $request The request containing the user's action.
     * @param int $invitationId The ID of the invitation.
     * @return JsonResponse The response indicating success or failure.
     */
    public function handleUserInvitation(TakeInvitationActionRequest $request, int $invitationId): JsonResponse
    {
        try {
            $invitation = $this->invitationService->handleUserInvitation($request->user(), $invitationId, $request->validated());
            if ($invitation) {
                return response()->json(SuccessResponse::create('success', 'Invitation data updated successfully.', new InvitationInfoResource($invitation)), 200);
            }
            return response()->json(SuccessResponse::create('error', 'Invitation not found', []), 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(ErrorResponse::create('error', $e->getMessage(), []), 500);
        }
    }
}
