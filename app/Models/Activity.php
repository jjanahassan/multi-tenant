<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable =['user_id', 'task_id', 'action', 'description', ];

    public function task(): BelongsTo{
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}
