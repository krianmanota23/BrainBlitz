<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Quiz extends Model
{
    protected $fillable = [
        'title', 
        'room_code', 
        'created_by', 
        'topic_mode', 
        'time_per_question', 
        'max_participants', 
        'status'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class, 'quiz_topics');
    }

    // placeholder for room relationship
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function latestRoom()
    {
        return $this->hasOne(Room::class)->latestOfMany();
    }
}
