<?php

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendTeamInvitation;

uses(RefreshDatabase::class);

test('owner can create a teammate invitation', function () {
    Queue::fake();

    $company = Company::create([
        'name' => 'Company A',
        'owner_id' => null,
        'is_active' => true,
    ]);

    $owner = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $this->actingAs($owner);

    $response = $this->post(route('invitations.store'), [
        'email' => 'newmember@example.com',
        'role' => 'member',
    ]);

    $response->assertSessionHas('success');

    $this->assertDatabaseHas('invitations', [
        'company_id' => $company->id,
        'email' => 'newmember@example.com',
        'role' => 'member',
    ]);

    Queue::assertPushed(SendTeamInvitation::class);
});

test('admin can create a teammate invitation', function () {
    Queue::fake();

    $company = Company::create([
        'name' => 'Company A',
        'owner_id' => null,
        'is_active' => true,
    ]);

    $admin = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'admin',
    ]);

    $this->actingAs($admin);

    $response = $this->post(route('invitations.store'), [
        'email' => 'newmember@example.com',
        'role' => 'member',
    ]);

    $response->assertSessionHas('success');

    $this->assertDatabaseHas('invitations', [
        'company_id' => $company->id,
        'email' => 'newmember@example.com',
        'role' => 'member',
    ]);

    Queue::assertPushed(SendTeamInvitation::class);
});

test('member cannot create a teammate invitation', function () {
    $company = Company::create([
        'name' => 'Company A',
        'owner_id' => null,
        'is_active' => true,
    ]);

    $member = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'member',
    ]);

    $this->actingAs($member);

    $response = $this->post(route('invitations.store'), [
        'email' => 'newmember@example.com',
        'role' => 'member',
    ]);

    $response->assertForbidden();

    $this->assertDatabaseCount('invitations', 0);
});

test('invitation role cannot be owner', function () {
    $company = Company::create([
        'name' => 'Company A',
        'owner_id' => null,
        'is_active' => true,
    ]);

    $owner = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $this->actingAs($owner);

    $response = $this->post(route('invitations.store'), [
        'email' => 'newowner@example.com',
        'role' => 'owner',
    ]);

    $response->assertSessionHasErrors('role');

    $this->assertDatabaseCount('invitations', 0);
});

test('invitation always belongs to the authenticated users company', function () {
    Queue::fake();

    $companyA = Company::create([
        'name' => 'Company A',
        'owner_id' => null,
        'is_active' => true,
    ]);

    $companyB = Company::create([
        'name' => 'Company B',
        'owner_id' => null,
        'is_active' => true,
    ]);

    $ownerA = User::factory()->create([
        'company_id' => $companyA->id,
        'role' => 'owner',
    ]);

    $this->actingAs($ownerA);

    $this->post(route('invitations.store'), [
        'email' => 'test@example.com',
        'role' => 'member',
    ]);

    $invitation = Invitation::first();

    expect($invitation->company_id)->toBe($companyA->id);
    expect($invitation->company_id)->not->toBe($companyB->id);
});