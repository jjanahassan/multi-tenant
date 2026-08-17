<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait CompanyScoped
{
    protected static function bootCompanyScoped(): void
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where(
                    $builder->getModel()->getTable() . '.company_id',
                    Auth::user()->company_id
                );
            }
        });

        static::creating(function (Model $model) {
            if (Auth::check()) {
                $model->company_id = Auth::user()->company_id;
            }
        });
    }
}