<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable= ['company_id', 'email', 'role', 'token', 'expires_at',];

    protected function casts():array
    {
        return ['expires_at' => 'datetime',];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
