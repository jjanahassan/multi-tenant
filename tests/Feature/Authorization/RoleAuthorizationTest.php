<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


test('owner can invite a teammate', function () {
    $company = Company::create([
        'name' => 'Company A',
        'owner_id' => null,
        'is_active' => true,
    ]);

    $owner = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    expect($owner->can('invite', $company))->toBeTrue();
});

test('owner can remove a teammate', function () {
    $company = Company::create([
        'name' => 'Company A',
        'owner_id' => null,
        'is_active' => true,
    ]);

    $owner = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    expect($owner->can('removeUser', $company))->toBeTrue();
});

test('owner can delete their company', function () {
    $company = Company::create([
        'name' => 'Company A',
        'owner_id' => null,
        'is_active' => true,
    ]);

    $owner = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    expect($owner->can('delete', $company))->toBeTrue();
});


test('admin can invite a teammate', function () {
    $company = Company::create([
        'name' => 'Company A',
        'owner_id' => null,
        'is_active' => true,
    ]);

    $admin = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'admin',
    ]);

    expect($admin->can('invite', $company))->toBeTrue();
});

test('admin can remove a teammate', function () {
    $company = Company::create([
        'name' => 'Company A',
        'owner_id' => null,
        'is_active' => true,
    ]);

    $admin = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'admin',
    ]);

    expect($admin->can('removeUser', $company))->toBeTrue();
});

test('admin cannot delete the company', function () {
    $company = Company::create([
        'name' => 'Company A',
        'owner_id' => null,
        'is_active' => true,
    ]);

    $admin = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'admin',
    ]);

    expect($admin->can('delete', $company))->toBeFalse();
});


test('member cannot invite a teammate', function () {
    $company = Company::create([
        'name' => 'Company A',
        'owner_id' => null,
        'is_active' => true,
    ]);

    $member = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'member',
    ]);

    expect($member->can('invite', $company))->toBeFalse();
});

test('member cannot remove a teammate', function () {
    $company = Company::create([
        'name' => 'Company A',
        'owner_id' => null,
        'is_active' => true,
    ]);

    $member = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'member',
    ]);

    expect($member->can('removeUser', $company))->toBeFalse();
});

test('member cannot delete the company', function () {
    $company = Company::create([
        'name' => 'Company A',
        'owner_id' => null,
        'is_active' => true,
    ]);

    $member = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'member',
    ]);

    expect($member->can('delete', $company))->toBeFalse();
});


test('user cannot perform company actions on another company', function () {
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

    expect($ownerA->can('invite', $companyB))->toBeFalse();
    expect($ownerA->can('removeUser', $companyB))->toBeFalse();
    expect($ownerA->can('delete', $companyB))->toBeFalse();
});