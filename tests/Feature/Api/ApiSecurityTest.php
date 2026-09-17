<?php

use App\Models\Company;
use App\Models\User;

test('api token is bound to the user company', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $token = $user->createToken('test-token');

    $token->accessToken->company_id = $company->id;
    $token->accessToken->save();

    $this->withToken($token->plainTextToken)
        ->getJson(route('api.v1.company'))
        ->assertOk();
});

test('api token from one company cannot be used after user switches companies', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $companyA->id,
        'role' => 'owner',
    ]);

    $token = $user->createToken('test-token');

    $token->accessToken->company_id = $companyA->id;
    $token->accessToken->save();

    $user->update([
        'company_id' => $companyB->id,
    ]);

    $this->withToken($token->plainTextToken)
        ->getJson(route('api.v1.company'))
        ->assertForbidden()
        ->assertJson([
            'message' => 'Token is not valid for the current company.',
        ]);
});

test('api routes are rate limited', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $token = $user->createToken('rate-limit-test');

    $token->accessToken->company_id = $company->id;
    $token->accessToken->save();

    for ($i = 0; $i < 60; $i++) {
        $this->withToken($token->plainTextToken)
            ->getJson(route('api.v1.company'))
            ->assertOk();
    }

    $this->withToken($token->plainTextToken)
        ->getJson(route('api.v1.company'))
        ->assertStatus(429);
});