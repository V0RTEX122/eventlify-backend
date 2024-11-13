<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\Event;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Exception;

class TaskService
{
    /**
     * Create a new task for a specific event and user.
     *
     * @param User $user
     * @param array $data
     * @param int $eventId
     * @return Task|null
     * @throws Exception
     */
    public function createUserTask(User $user, array $data, int $eventId): ?Task
    {
        try {
            $event = Event::with('creator')->find($eventId);
            if ($event && $event->creator->id === $user->id) {
                $task = Task::create([
                    'event_id' => $eventId,
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'due_date' => $data['due_date'],
                    'assigned_to' => $data['assigned_to'],
                    'status' => 'pending',
                ]);
                return $task;
            }
            return null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Retrieve all tasks for a specific event assigned to a user, with optional status filtering.
     *
     * @param User $user
     * @param array $data
     * @param int $eventId
     * @return Collection|null
     * @throws Exception
     */
    public function getEventTasks(User $user, array $data, int $eventId): ?Collection
    {
        try {
            $query = Task::where('assigned_to', $user->id)->where('event_id', $eventId)->with('event');
            if ($data['status']) {
                $query->where('status', $data['status']);
            }
            $task = $query->get();
            if ($task) {
                return $task;
            }
            return null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Retrieve a specific task by ID, assigned to a user, with optional status filtering.
     *
     * @param User $user
     * @param array $data
     * @param int $taskId
     * @return Task|null
     * @throws Exception
     */
    public function getEventTaskById(User $user, array $data, int $taskId): ?Task
    {
        try {
            $query = Task::where('id', $taskId)->where('assigned_to', $user->id)->with('event');
            if ($data['status']) {
                $query->where('status', $data['status']);
            }
            $task = $query->first();
            if ($task) {
                return $task;
            }
            return null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Update the status of a specific task if assigned to the user.
     *
     * @param User $user
     * @param array $data
     * @param int $taskId
     * @return Task|null
     * @throws Exception
     */
    public function updateEventTask(User $user, array $data, int $taskId): ?Task
    {
        try {
            $task = Task::where('id', $taskId)->where('assigned_to', $user->id)->with('event')->first();
            if ($task) {
                $task->status = $data['status'];
                $task->save();
                return $task;
            }
            return null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
