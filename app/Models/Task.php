<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $fillable=['project_id', 'board_column_id', 'assignee_id', 'title', 'description', 'due_date', 'position', ];

    protected function casts(): array{
        return ['due_date'=> 'date', ];
    }

    public function project(): BelongsTo{
        return $this->belongsTo(Project::class);
    }

    public function boardColumn(): BelongsTo{
        return $this->belongsTo(BoardColumn::class);
    }

    public function assignee(): BelongsTo{
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function comments(): MorphMany{
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function activities(): HasMany{
        return $this->hasMany(Activity::class);
    }

    public function dueDateReminders(): HasMany
{
    return $this->hasMany(DueDateReminder::class);
}
}
