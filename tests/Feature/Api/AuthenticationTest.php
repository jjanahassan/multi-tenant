<?php

use App\Models\Company;
use App\Models\User;

test('authenticated user can access the api', function () {
    $user = User::factory()->create();

    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);

    $user->update([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $token = $user->createToken('test-token')->plainTextToken;

    $this->withToken($token)
        ->getJson('/api/v1/test')
        ->assertOk()
        ->assertJson([
            'message' => 'TeamBoard API v1 is working.',
        ]);
});

test('unauthenticated api request returns a json error', function () {
    $response = $this->getJson('/api/v1/test');

    $response
        ->assertUnauthorized()
        ->assertJson([
            'message' => 'Unauthenticated.',
        ]);
});