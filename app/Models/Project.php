<?php

namespace App\Models;

use App\Traits\CompanyScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Factories\ProjectFactory;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, CompanyScoped;

    protected $fillable=['name', 'company_id', 'description',];

    protected static function booted(): void
    {
        static::created(function (Project $project) {
            $project->boardColumns()->createMany([
                ['name' => 'To Do', 'position' => 1, ],
                ['name' => 'In Progress', 'position' => 2, ],
                ['name' => 'Done', 'position' => 3, ],
            ]);
        });
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return HasMany<BoardColumn, $this>
     */
    public function boardColumns(): HasMany{
        return $this->hasMany(BoardColumn::class)->orderBy('position');
    }

    /**
     * @return HasMany<Task, $this>
     */
    public function tasks(): HasMany{
        return $this->hasMany(Task::class);
    }
}
