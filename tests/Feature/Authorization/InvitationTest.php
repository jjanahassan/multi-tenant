
<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function createCompanyForInvitationTest(): Company
{
    return Company::create([
        'name' => 'Company A',
        'owner_id' => null,
        'is_active' => true,
    ]);
}

function createUserForInvitationTest(
    Company $company,
    string $role
): User {
    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => $role,
    ]);

    if ($role === 'owner') {
        $company->update(['owner_id' => $user->id]);
    }

    return $user;
}

test('owner can create a member account', function () {
    $company = createCompanyForInvitationTest();
    $owner = createUserForInvitationTest($company, 'owner');

    $response = $this->actingAs($owner)->post(route('invitations.store'), [
        'name' => 'New Member',
        'email' => 'newmember@example.com',
        'role' => 'member',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'name' => 'New Member',
        'email' => 'newmember@example.com',
        'company_id' => $company->id,
        'role' => 'member',
    ]);

    $newUser = User::where('email', 'newmember@example.com')->firstOrFail();

    expect(Hash::check('Password123!', $newUser->password))->toBeTrue();

    $this->assertDatabaseCount('invitations', 0);
});

test('admin can create a member account', function () {
    $company = createCompanyForInvitationTest();
    $admin = createUserForInvitationTest($company, 'admin');

    $response = $this->actingAs($admin)->post(route('invitations.store'), [
        'name' => 'New Member',
        'email' => 'newmember@example.com',
        'role' => 'member',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'name' => 'New Member',
        'email' => 'newmember@example.com',
        'company_id' => $company->id,
        'role' => 'member',
    ]);

    $this->assertDatabaseCount('invitations', 0);
});

test('owner can create an admin account', function () {
    $company = createCompanyForInvitationTest();
    $owner = createUserForInvitationTest($company, 'owner');

    $response = $this->actingAs($owner)->post(route('invitations.store'), [
        'name' => 'New Admin',
        'email' => 'newadmin@example.com',
        'role' => 'admin',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('users', [
        'name' => 'New Admin',
        'email' => 'newadmin@example.com',
        'company_id' => $company->id,
        'role' => 'admin',
    ]);
});

test('admin cannot create an admin account', function () {
    $company = createCompanyForInvitationTest();
    $admin = createUserForInvitationTest($company, 'admin');

    $response = $this->actingAs($admin)->post(route('invitations.store'), [
        'name' => 'Another Admin',
        'email' => 'anotheradmin@example.com',
        'role' => 'admin',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertSessionHasErrors('role');

    $this->assertDatabaseMissing('users', [
        'email' => 'anotheradmin@example.com',
    ]);
});

test('member cannot create a teammate account', function () {
    $company = createCompanyForInvitationTest();
    $member = createUserForInvitationTest($company, 'member');

    $response = $this->actingAs($member)->post(route('invitations.store'), [
        'name' => 'Another Member',
        'email' => 'anothermember@example.com',
        'role' => 'member',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertForbidden();

    $this->assertDatabaseMissing('users', [
        'email' => 'anothermember@example.com',
    ]);

    $this->assertDatabaseCount('invitations', 0);
});

test('owner cannot create an account with the owner role', function () {
    $company = createCompanyForInvitationTest();
    $owner = createUserForInvitationTest($company, 'owner');

    $response = $this->actingAs($owner)->post(route('invitations.store'), [
        'name' => 'Another Owner',
        'email' => 'anotherowner@example.com',
        'role' => 'owner',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertSessionHasErrors('role');

    $this->assertDatabaseMissing('users', [
        'email' => 'anotherowner@example.com',
    ]);
});

test('new teammate account always belongs to the authenticated users company', function () {
    $companyA = createCompanyForInvitationTest();

    $companyB = Company::create([
        'name' => 'Company B',
        'owner_id' => null,
        'is_active' => true,
    ]);

    $ownerA = createUserForInvitationTest($companyA, 'owner');

    $response = $this->actingAs($ownerA)->post(route('invitations.store'), [
        'name' => 'Company A Member',
        'email' => 'companyamember@example.com',
        'role' => 'member',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertSessionHasNoErrors();

    $newUser = User::where('email', 'companyamember@example.com')->firstOrFail();

    expect($newUser->company_id)->toBe($companyA->id);
    expect($newUser->company_id)->not->toBe($companyB->id);
});

test('account creation requires a unique email and matching passwords', function () {
    $company = createCompanyForInvitationTest();
    $owner = createUserForInvitationTest($company, 'owner');

    User::factory()->create([
        'company_id' => $company->id,
        'email' => 'existing@example.com',
    ]);

    $response = $this->actingAs($owner)->post(route('invitations.store'), [
        'name' => 'New Member',
        'email' => 'existing@example.com',
        'role' => 'member',
        'password' => 'Password123!',
        'password_confirmation' => 'DifferentPassword123!',
    ]);

    $response->assertSessionHasErrors(['email', 'password']);
});

test('account creation requires a name and password', function () {
    $company = createCompanyForInvitationTest();
    $owner = createUserForInvitationTest($company, 'owner');

    $response = $this->actingAs($owner)->post(route('invitations.store'), [
        'email' => 'incomplete@example.com',
        'role' => 'member',
    ]);

    $response->assertSessionHasErrors(['name', 'password']);

    $this->assertDatabaseMissing('users', [
        'email' => 'incomplete@example.com',
    ]);
});
