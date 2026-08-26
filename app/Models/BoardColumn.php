<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class BoardColumn extends Model
{
    use HasFactory;

    protected $fillable= ['project_id', 'name', 'position',];

    protected static function booted(): void
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if (Auth::check()) {
                $builder->whereHas('project', function (Builder $query) {
                    $query->where(
                        'company_id',
                        Auth::user()->company_id
                    );
                });
            }
        });
    }
    
    public function project(): BelongsTo{
        return $this->belongsTo(Project::class);
    }
}
