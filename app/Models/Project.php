<?php

namespace App\Models;

use App\Traits\CompanyScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory, CompanyScoped;

    protected $fillable=['name', 'company_id',];

    public function company(){
        return $this->belongsTo(Company::class);
    }
}
