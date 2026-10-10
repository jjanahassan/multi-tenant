<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function create(): View
    {
        $company = auth()->user()->company;

        Gate::authorize('invite', $company);

        $allowedRoles = auth()->user()->role === 'owner'
            ? ['admin', 'member']
            : ['member'];

        return view('invitations.create', compact('allowedRoles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $creator = $request->user();
        $company = $creator->company;

        Gate::authorize('invite', $company);

        $allowedRoles = $creator->role === 'owner'
            ? ['admin', 'member']
            : ['member'];

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in($allowedRoles)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        DB::transaction(function () use ($validated, $company) {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'company_id' => $company->id,
                'role' => $validated['role'],
            ]);
        });

        return back()->with(
            'success',
            'Teammate account created successfully. They can now log in using their email and password.'
        );
    }
}
