<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        return DB::transaction(function () use ($input){
            $company= Company::create([
                'name'=> $input['name'] . "'s Company",
                'owner_id'=> null,
                'is_active'=> true,
            ]);

            $user= User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'company_id' => $company->id,
            ]);

            $company->owner_id= $user->id;
            $company-> save();

            return $user;
        });
        
    }
}
