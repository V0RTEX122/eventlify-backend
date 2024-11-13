<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\Event;
use App\Models\Invitation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Exception;

class InvitationService
{
    /**
     * Send an invitation to a user for a specific event.
     *
     * @param User $user The user sending the invitation.
     * @param array $data The invitation data including the recipient's email.
     * @param int $eventId The ID of the event to invite the user to.
     * @return Invitation|null The created invitation or null if the participant or event is not found.
     * @throws Exception If there is an error during the invitation process.
     */
    public function sendInvitation(User $user, array $data, int $eventId): ?Invitation
    {
        try {
            $participant = User::where('email', $data['email'])->first();
            $event = Event::find($eventId);
            if ($participant && $event) {
                $invitation = Invitation::create([
                    'event_id' => $eventId,
                    'user_id' => $user->id,
                    'email' => $data['email'],
                    'status' => 'pending',
                ]);
                return $invitation;
            }
            return null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Retrieve invitations for a user based on specified criteria.
     *
     * @param User $user The user whose invitations to retrieve.
     * @param array $data Filter criteria for retrieving invitations.
     * @return Collection<Invitation> A collection of invitations for the user.
     * @throws Exception If there is an error during retrieval.
     */
    public function getUserInvitations(User $user, array $data): Collection
    {
        try {
            $query = Invitation::query();
            if (isset($data['type'])) {
                if ($data['type'] === 'sent') {
                    $query->whereIn('id', $user->createdInvitations()->pluck('id'));
                } elseif ($data['type'] === 'received') {
                    $query->whereIn('id', $user->receivedInvitations()->pluck('id'));
                }
            } else {
                $query->where(function ($q) use ($user) {
                    $q->whereIn('id', $user->createdInvitations()->pluck('id'))
                      ->orWhereIn('id', $user->receivedInvitations()->pluck('id'));
                });
            }
            if (isset($data['status'])) {
                $query->where('status', $data['status']);
            }
            $invitations = $query->get();
            return $invitations;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Handle a user's response to an invitation.
     *
     * @param User $user The user responding to the invitation.
     * @param int $invitationId The ID of the invitation being accepted or declined.
     * @param array $data The data containing the action to take ('accept' or 'decline').
     * @return Invitation|null The updated invitation or null if not found.
     */
    public function handleUserInvitation(User $user, int $invitationId, array $data): ?Invitation
    {
        $invitation = Invitation::where('id', $invitationId)
            ->where('email', $user->email)
            ->where('status', 'pending')
            ->first();
        if (!$invitation) {
            return null;
        }
        if ($data['action'] === 'accept') {
            $invitation->status = 'accepted';
        } elseif ($data['action'] === 'decline') {
            $invitation->status = 'declined';
        }
        $invitation->save();
        return $invitation->fresh();
    }
}
