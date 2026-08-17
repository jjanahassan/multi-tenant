<?php

use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can only see projects belonging to their company', function () {
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

    $userA = User::factory()->create([
        'company_id' => $companyA->id,
        'role' => 'owner',
    ]);

    $userB = User::factory()->create([
        'company_id' => $companyB->id,
        'role' => 'owner',
    ]);

    $projectA = Project::withoutGlobalScopes()->create([
        'company_id' => $companyA->id,
        'name' => 'Company A Project',
    ]);

    $projectB = Project::withoutGlobalScopes()->create([
        'company_id' => $companyB->id,
        'name' => 'Company B Project',
    ]);

    $this->actingAs($userA);

    expect(Project::all())->toHaveCount(1);
    expect(Project::first()->id)->toBe($projectA->id);
    expect(Project::first()->id)->not->toBe($projectB->id);
});

test('user cannot edit a project belonging to another company', function () {
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

    $userA = User::factory()->create([
        'company_id' => $companyA->id,
        'role' => 'owner',
    ]);

    $projectB = Project::withoutGlobalScopes()->create([
        'company_id' => $companyB->id,
        'name' => 'Company B Project',
    ]);

    $this->actingAs($userA);

    $project = Project::find($projectB->id);

    expect($project)->toBeNull();
});

test('new projects are automatically assigned to the authenticated users company', function () {
    $company = Company::create([
        'name' => 'Company A',
        'owner_id' => null,
        'is_active' => true,
    ]);

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $this->actingAs($user);

    $project = Project::create([
        'name' => 'My Project',
    ]);

    expect($project->company_id)->toBe($company->id);
});

test('user from company A cannot query data from company B', function () {
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

    $userA = User::factory()->create([
        'company_id' => $companyA->id,
        'role' => 'owner',
    ]);

    $projectB = Project::withoutGlobalScopes()->create([
        'company_id' => $companyB->id,
        'name' => 'Company B Project',
    ]);

    $this->actingAs($userA);

    $result = Project::where('id', $projectB->id)->first();

    expect($result)->toBeNull();
});
