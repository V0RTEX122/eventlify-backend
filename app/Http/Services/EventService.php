<?php

namespace App\Http\Services;

use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class EventService
{
    /**
     * Create a new event.
     *
     * @param array $data The data to create the event.
     * @return Event The created event instance.
     * @throws Exception If there is an error during the event creation.
     */
    public function createEvent(array $data): Event
    {
        try {
            return Event::create($data);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Retrieve all events.
     *
     * @return Collection<Event> A collection of all event instances.
     * @throws Exception If there is an error during retrieval.
     */
    public function getAllEvents(): Collection
    {
        try {
            return Event::all();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Retrieve a specific event by its ID.
     *
     * @param int $id The ID of the event to retrieve.
     * @return Event|null The event instance or null if not found.
     * @throws Exception If there is an error during retrieval.
     */
    public function getEvent(int $id): ?Event
    {
        try {
            return Event::find($id);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Update an existing event.
     *
     * @param Event $event The event instance to update.
     * @param array $data The data to update the event with.
     * @return Event The updated event instance.
     * @throws Exception If there is an error during the update.
     */
    public function updateEvent(Event $event, array $data): Event
    {
        try {
            $event->update($data);
            return $event;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Delete a specific event.
     *
     * @param Event $event The event instance to delete.
     * @throws Exception If there is an error during the deletion.
     */
    public function deleteEvent(Event $event): void
    {
        try {
            $event->delete();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
