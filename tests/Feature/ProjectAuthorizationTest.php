<?php

use App\Models\Company;
use App\Models\Project;
use App\Models\User;

test('admin can create a project', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'admin',
    ]);

    $response = $this
        ->actingAs($user)
        ->post(
            route('projects.store'),
            [
                'name' => 'New Project',
                'description' => 'Project description',
            ]
        );

    $response->assertRedirect();

    $this->assertDatabaseHas('projects', [
        'company_id' => $company->id,
        'name' => 'New Project',
    ]);
});

test('member cannot create a project', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'member',
    ]);

    $response = $this
        ->actingAs($user)
        ->post(
            route('projects.store'),
            [
                'name' => 'New Project',
                'description' => 'Project description',
            ]
        );

    $response->assertForbidden();
});

test('admin can update a project in their company', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'admin',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
        'name' => 'Old Name',
    ]);

    $response = $this
        ->actingAs($user)
        ->put(
            route('projects.update', $project),
            [
                'name' => 'Updated Project',
                'description' => 'Updated description',
            ]
        );

    $response->assertRedirect();

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Updated Project',
    ]);
});

test('member cannot update a project', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'member',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $response = $this
        ->actingAs($user)
        ->put(
            route('projects.update', $project),
            [
                'name' => 'Updated Project',
                'description' => 'Updated description',
            ]
        );

    $response->assertForbidden();
});

test('admin cannot update a project from another company', function () {
    $companyA = Company::factory()->create();

    $companyB = Company::factory()->create();

    $userA = User::factory()->create([
        'company_id' => $companyA->id,
        'role' => 'admin',
    ]);

    $projectB = Project::factory()->create([
        'company_id' => $companyB->id,
    ]);

    $response = $this
        ->actingAs($userA)
        ->put(
            route('projects.update', $projectB),
            [
                'name' => 'Unauthorized Update',
                'description' => 'Should not work',
            ]
        );

    $response->assertNotFound();
});

test('admin can delete a project in their company', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'admin',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $response = $this
        ->actingAs($user)
        ->delete(
            route('projects.destroy', $project)
        );

    $response->assertRedirect();

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);
});

test('member cannot delete a project', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'member',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $response = $this
        ->actingAs($user)
        ->delete(
            route('projects.destroy', $project)
        );

    $response->assertForbidden();
});

test('admin cannot delete a project from another company', function () {
    $companyA = Company::factory()->create();

    $companyB = Company::factory()->create();

    $userA = User::factory()->create([
        'company_id' => $companyA->id,
        'role' => 'admin',
    ]);

    $projectB = Project::factory()->create([
        'company_id' => $companyB->id,
    ]);

    $response = $this
        ->actingAs($userA)
        ->delete(
            route('projects.destroy', $projectB)
        );

    $response->assertNotFound();
});

test('user can only view projects from their own company', function () {
    $companyA = Company::factory()->create();

    $companyB = Company::factory()->create();

    $userA = User::factory()->create([
        'company_id' => $companyA->id,
        'role' => 'admin',
    ]);

    $projectA = Project::factory()->create([
        'company_id' => $companyA->id,
    ]);

    $projectB = Project::factory()->create([
        'company_id' => $companyB->id,
    ]);

    $this->actingAs($userA);

    $projects = Project::all();

    $this->assertTrue(
        $projects->contains('id', $projectA->id)
    );

    $this->assertFalse(
        $projects->contains('id', $projectB->id)
    );
});