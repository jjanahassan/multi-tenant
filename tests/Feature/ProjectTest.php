<?php

use App\Models\Company;
use App\Models\Project;
use App\Models\User;

test('authenticated user can create a project', function () {
    $company = Company::factory()->create();
    $user = User::factory()->create(['company_id' => $company->id, 'role' => 'admin',]);
    $response = $this
        ->actingAs($user)
        ->post(route('projects.store'), [
            'name' => 'Website Redesign',
            'description' => 'Redesign the company website',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('projects', [
        'company_id' => $company->id,
        'name' => 'Website Redesign',
    ]);
});

test('authenticated user can update a project', function () {
    $company = Company::factory()->create();
    $user = User::factory()->create(['company_id' => $company->id, 'role' => 'admin',]);
    $project = Project::factory()->create([
        'company_id' => $company->id,
        'name' => 'Old Name',
    ]);

    $response = $this
        ->actingAs($user)
        ->put(route('projects.update', $project), [
            'name' => 'New Name',
            'description' => 'Updated description',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'New Name',
    ]);
});

test('authenticated user can delete a project', function () {
    $company = Company::factory()->create();
    $user = User::factory()->create(['company_id' => $company->id, 'role' => 'admin',]);
    $project = Project::factory()->create(['company_id' => $company->id,]);

    $response = $this
        ->actingAs($user)
        ->delete(route('projects.destroy', $project));

    $response->assertRedirect(
        route('projects.index')
    );

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);
});

test('project requires a name', function () {
    $company = Company::factory()->create();
    $user = User::factory()->create(['company_id' => $company->id, 'role' => 'admin',]);

    $response = $this
        ->actingAs($user)
        ->from(route('projects.create'))
        ->post(route('projects.store'), [
            'name' => '',
            'description' => 'Test description',
        ]);

    $response
        ->assertRedirect(route('projects.create'))
        ->assertSessionHasErrors('name');

    $this->assertDatabaseCount('projects', 0);
});

test('user cannot access a project belonging to another company', function () {
    $companyA = Company::factory()->create();

    $companyB = Company::factory()->create();

    $userA = User::factory()->create([
        'company_id' => $companyA->id,
    ]);

    $projectB = Project::factory()->create([
        'company_id' => $companyB->id,
    ]);

    $response = $this
        ->actingAs($userA)
        ->get(route('projects.show', $projectB));

    $response->assertNotFound();
});