<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class UserService
{
    /**
     * Register a new user.
     *
     * @param array $data User registration data.
     * @return User The created user instance.
     * @throws Exception If there is an error during registration.
     */
    public function register(array $data): User
    {
        try {
            $data['password'] = Hash::make($data['password']);
            return User::create($data);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Log in a user and create an authentication token.
     *
     * @param array $credentials User login credentials.
     * @return object|null An object containing the token and user information, or null if login fails.
     * @throws Exception If there is an error during login.
     */
    public function login(array $credentials): ?object
    {
        try {
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken(env('APP_NAME'))->plainTextToken;

                return (object) [
                    'token' => $token,
                    'user' => $user,
                ];
            }
            return null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Log out the user by deleting their current access token.
     *
     * @param User $user The user to log out.
     * @return void
     * @throws Exception If there is an error during logout.
     */
    public function logout(User $user): void
    {
        try {
            if ($user->currentAccessToken()) {
                $user->currentAccessToken()->delete();
            } else {
                throw new Exception('No active token found for this user.');
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Log out the user from all devices by deleting all their tokens.
     *
     * @param User $user The user to log out from all devices.
     * @return void
     * @throws Exception If there is an error during logout.
     */
    public function logoutAllDevices(User $user): void
    {
        try {
            $user->tokens()->delete();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Update user information.
     *
     * @param array $validatedData The data to update the user with.
     * @param User $user The user instance to update.
     * @return User The updated user instance.
     * @throws Exception If there is an error during the update.
     */
    public function updateUser(array $validatedData, User $user): User
    {
        try {
            if (!empty($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            }

            $user->update($validatedData);
            return $user;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Get the events created by a user.
     *
     * @param User $user The user whose created events to retrieve.
     * @return Collection<Event> A collection of events created by the user.
     * @throws Exception If there is an error during retrieval.
     */
    public function getCreatedEventsOfUser(User $user): Collection
    {
        try {
            return $user->createdEvents()->get();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Get a specific event for the user by ID.
     *
     * @param User $user The user requesting the event.
     * @param int $id The ID of the event to retrieve.
     * @return Event|null The event instance if found, or null if not found.
     * @throws Exception If there is an error during retrieval.
     */
    public function getEventForUser(User $user, int $id): ?Event
    {
        try {
            $event = Event::where('id', $id)->first();
            if (!$event) {
                return null;
            }
            if ($event->isPublic()) {
                return $event->load('participants');
            }
            if ($event->isPrivate() && $this->canAccessEvent($event, $user->id)) {
                return $event->load('participants');
            }
            return null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Get all events accessible to a user.
     *
     * @param User $user The user to retrieve events for.
     * @return Collection<Event> A collection of events accessible to the user.
     * @throws Exception If there is an error during retrieval.
     */
    public function getAllEventsForUser(User $user): Collection
    {
        try {
            $userId = $user->id;
            $events = Event::with('participants')
                ->where(function ($query) use ($userId) {
                    $query->where('visibility', 'public')
                        ->orWhere(function ($query) use ($userId) {
                            $query->where('visibility', 'private')
                                ->where('created_by', $userId);
                        })
                        ->orWhereHas('participants', function ($query) use ($userId) {
                            $query->where('user_id', $userId);
                        });
                })
                ->get();
            return $events;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Search for users based on a query string.
     *
     * @param string $query The search query.
     * @return Collection<User> A collection of users matching the search query.
     * @throws Exception If there is an error during the search.
     */
    public function searchUsers(string $query): Collection
    {
        try {
            $users =  User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            // ->select('id', 'name', 'email')
            ->take(10)
            ->get();
            return $users;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Check if the user can access a specific event.
     *
     * @param Event $event The event to check access for.
     * @param int $userId The ID of the user.
     * @return bool True if the user can access the event, otherwise false.
     */
    private function canAccessEvent(Event $event, int $userId): bool
    {
        $isCreator = $event->created_by === $userId;
        $isParticipant = $event->participants->contains('id', $userId);
        return $isCreator || $isParticipant;
    }
}
