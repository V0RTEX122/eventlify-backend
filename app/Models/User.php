<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'agree_terms',
        'profile_picture',
        'gender',
        'birth_date',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'agree_terms' => 'boolean',  // Converts tinyint to boolean
    ];

    /**
     * Get the events created by the user.
     *
     * @return HasMany<Event>
     */
    public function createdEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    /**
     * Tasks assigned to the user.
     *
     * @return HasMany
     */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /**
     * All tasks under events created by the user.
     *
     * @return HasManyThrough
     */
    public function tasksUnderCreatedEvents(): HasManyThrough
    {
        return $this->hasManyThrough(Task::class, Event::class, 'created_by', 'event_id');
    }

    /**
     * Get the events the user is participating in.
     *
     * @return BelongsToMany<Event>
     */
    public function participatingEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_participants')
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Get the invitations created by the user.
     *
     * @return HasMany<Invitation>
     */
    public function createdInvitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'user_id');
    }

    /**
     * Get the invitations received by the user.
     *
     * @return HasMany<Invitation>
     */
    public function receivedInvitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'email', 'email');
    }
}
