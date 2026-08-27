<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CompanyController extends Controller
{
    public function users()
    {
        $company = auth()->user()->company;
        Gate::authorize('removeUser', $company);
        $users = $company->users()->orderBy('name')->get();

        return view('company.users', compact('company', 'users'));
    }

     public function removeUser(Company $company, User $user)
    {
        Gate::authorize('removeUser', $company);

        abort_unless($user->company_id === $company->id, 404);

        abort_if($user->id === $company->owner_id, 403);

        $user->delete();

        return back()->with('success', 'Teammate removed successfully.');
    }

    public function destroy(Company $company)
    {
        Gate::authorize('delete', $company);
        $user = auth()->user();
        Auth::logout();
        $company->delete();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }
}
