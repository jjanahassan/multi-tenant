<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user can obtain a company-bound api token', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
        'password' => Hash::make('password'),
    ]);

    $response = $this->postJson(route('api.v1.auth.token'), [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'Postman',
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'token',
            'token_type',
            'company_id',
        ])
        ->assertJsonPath('token_type', 'Bearer')
        ->assertJsonPath('company_id', $company->id);

    $this->assertDatabaseHas('personal_access_tokens', [
        'name' => 'Postman',
        'company_id' => $company->id,
    ]);
});

test('invalid api token credentials are rejected', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'password' => Hash::make('password'),
    ]);

    $this->postJson(route('api.v1.auth.token'), [
        'email' => $user->email,
        'password' => 'wrong-password',
        'device_name' => 'Postman',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('email');
});