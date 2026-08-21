<?php

namespace App\Models;

use App\Traits\CompanyScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory, CompanyScoped;

    protected $fillable=['name', 'company_id', description,];

    public function company(): BelongsTo {
        return $this->belongsTo(Company::class);
    }

    public function boardColumns(): HasMany{
        return $this->hasMany(BoardColumn::class)->orderBy('position');
    }
}
