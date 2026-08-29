<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory, CompanyScoped;

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

    public function assignee():BelongsTo{
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
