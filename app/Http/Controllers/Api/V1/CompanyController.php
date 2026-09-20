<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyController extends Controller
{
    /**
     * Display the authenticated user's company.
     */
    public function show(Request $request): JsonResource
    {
        return new CompanyResource($request->user()->company);
    }
}