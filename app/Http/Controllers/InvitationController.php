<?php

namespace App\Http\Controllers;

use App\Jobs\SendTeamInvitation;
use App\Models\Company;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function create()
    {
        $company= auth()->user()->company;
        Gate::authorize('invite', $company);
        return view('invitations.create');
    }

    public function store(Request $request)
    {
        $company= auth()->user()->company;
        Gate::authorize('invite', $company);

        $validated = $request->validate([
            'email'=> ['required', 'email'],
            'role'=> ['required', 'in:admin,member'],
        ]);

        $invitation= Invitation::create([
            'company_id'=> $company->id,
            'email'=> $validated['email'],
            'role'=>$validated['role'],
            'token'=> Str::random(64),
            'expires_at'=> now()->addDays(7),
        ]);

        SendTeamInvitation::dispatch($invitation);

        return back()->with('success', 'Invitation created successfully.');
    }
}
