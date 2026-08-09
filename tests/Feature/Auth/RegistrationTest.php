<?php

use Laravel\Fortify\Features;
use App\Models\Company;
use App\Models\User;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register and are assigned to their own company', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
    $user= User::where('email', 'test@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->company_id)->not->toBeNull();

    $company= Company::find($user->company_id);

    expect($company)->not->toBeNull();
    expect($company->owner_id)->toBe($user->id);
});

test('different users are assigned to different companies', function () {
    $this->post(route('register.store'),[
        'name'=> 'User 1',
        'email'=> 'user1@example.com',
        'password'=> 'password',
        'password_confirmation'=> 'password',
    ]);

    $firstUser= User::where('email', 'user1@example.com')->first();
     expect($firstUser)->not->toBeNull();

    $firstCompanyId = $firstUser->company_id;

    $this->post(route('logout'));

    $this->post(route('register.store'), [
        'name'=> 'User 2',
        'email'=> 'user2@example.com',
        'password'=> 'password',
        'password_confirmation'=> 'password',
    ]);

    $secondUser= User::where('email', 'user2@example.com')->first();

    expect($secondUser)->not->toBeNull();
    expect($firstUser->company_id)->not->toBe($secondUser->company_id);
});