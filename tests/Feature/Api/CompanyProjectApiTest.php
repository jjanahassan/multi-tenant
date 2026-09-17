<?php

use App\Models\Company;
use App\Models\Project;
use App\Models\User;

test('authenticated user can view their company through the api', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
    ]);

    $token = $user->createCompanyToken('test-token');

    $response = $this->withToken($token->plainTextToken)->getJson('/api/v1/company');

    $response
        ->assertOk()
        ->assertJsonPath('data.id', $company->id)
        ->assertJsonPath('data.name', $company->name);
});

test('authenticated user can list projects from their company', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $token = $user->createCompanyToken('test-token');

    $response = $this->withToken($token->plainTextToken)->getJson('/api/v1/projects');

    $response
        ->assertOk()
        ->assertJsonPath('data.0.id', $project->id);
});

test('authenticated user can create a project through the api', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $token = $user->createCompanyToken('test-token');

    $response = $this->withToken($token->plainTextToken)->postJson('/api/v1/projects', [
        'name' => 'API Project',
        'description' => 'Created through the API',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.name', 'API Project')
        ->assertJsonPath('data.description', 'Created through the API')
        ->assertJsonPath('data.company_id', $company->id);

    $this->assertDatabaseHas('projects', [
        'name' => 'API Project',
        'company_id' => $company->id,
    ]);
});

test('authenticated user can view a project from their company', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $token = $user->createCompanyToken('test-token');

    $response = $this->withToken($token->plainTextToken)->getJson("/api/v1/projects/{$project->id}");

    $response
        ->assertOk()
        ->assertJsonPath('data.id', $project->id);
});

test('authenticated user can update a project from their company', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $token = $user->createCompanyToken('test-token');

    $response = $this->withToken($token->plainTextToken)->putJson("/api/v1/projects/{$project->id}", [
        'name' => 'Updated API Project',
        'description' => 'Updated description',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('data.name', 'Updated API Project')
        ->assertJsonPath('data.description', 'Updated description');
});

test('authenticated user can delete a project from their company', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $token = $user->createCompanyToken('test-token');

    $response = $this->withToken($token->plainTextToken)->deleteJson("/api/v1/projects/{$project->id}");

    $response
        ->assertOk()
        ->assertJson([
            'message' => 'Project deleted successfully.',
        ]);

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);
});

test('api user cannot access a project belonging to another company', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();

    $userA = User::factory()->create([
        'company_id' => $companyA->id,
    ]);

    $projectB = Project::factory()->create([
        'company_id' => $companyB->id,
    ]);

    $token = $userA->createCompanyToken('test-token');

    $response = $this->withToken($token->plainTextToken)->getJson("/api/v1/projects/{$projectB->id}");

    $response->assertNotFound();
});

test('api project list does not include projects from another company', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();

    $userA = User::factory()->create([
        'company_id' => $companyA->id,
    ]);

    $projectA = Project::factory()->create([
        'company_id' => $companyA->id,
    ]);

    $projectB = Project::factory()->create([
        'company_id' => $companyB->id,
    ]);

    $token = $userA->createCompanyToken('test-token');

    $response = $this->withToken($token->plainTextToken)->getJson('/api/v1/projects');

    $response
        ->assertOk()
        ->assertJsonFragment([
            'id' => $projectA->id,
        ])
        ->assertJsonMissing([
            'id' => $projectB->id,
        ]);
});