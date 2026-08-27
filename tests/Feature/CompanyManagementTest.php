<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_remove_a_teammate(): void
    {
        $company = Company::factory()->create();

        $owner = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'owner',
        ]);

        $company->update([
            'owner_id' => $owner->id,
        ]);

        $member = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'member',
        ]);

        $response = $this
            ->actingAs($owner)
            ->delete(route('company.users.destroy', [
                'company' => $company,
                'user' => $member,
            ]));

        $response->assertRedirect();

        $this->assertDatabaseMissing('users', [
            'id' => $member->id,
        ]);
    }

    public function test_member_cannot_remove_a_teammate(): void
    {
        $company = Company::factory()->create();

        $member = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'member',
        ]);

        $otherMember = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'member',
        ]);

        $response = $this
            ->actingAs($member)
            ->delete(route('company.users.destroy', [
                'company' => $company,
                'user' => $otherMember,
            ]));

        $response->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $otherMember->id,
        ]);
    }

    public function test_admin_can_remove_a_teammate(): void
    {
        $company = Company::factory()->create();

        $owner = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'owner',
        ]);

        $company->update([
            'owner_id' => $owner->id,
        ]);

        $admin = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'admin',
        ]);

        $member = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'member',
        ]);

        $response = $this
            ->actingAs($admin)
            ->delete(route('company.users.destroy', [
                'company' => $company,
                'user' => $member,
            ]));

        $response->assertRedirect();

        $this->assertDatabaseMissing('users', [
            'id' => $member->id,
        ]);
    }

    public function test_user_cannot_remove_teammate_from_another_company(): void
    {
        $companyA = Company::factory()->create();

        $ownerA = User::factory()->create([
            'company_id' => $companyA->id,
            'role' => 'owner',
        ]);

        $companyA->update([
            'owner_id' => $ownerA->id,
        ]);

        $companyB = Company::factory()->create();

        $memberB = User::factory()->create([
            'company_id' => $companyB->id,
            'role' => 'member',
        ]);

        $response = $this
            ->actingAs($ownerA)
            ->delete(route('company.users.destroy', [
                'company' => $companyA,
                'user' => $memberB,
            ]));

        $response->assertNotFound();

        $this->assertDatabaseHas('users', [
            'id' => $memberB->id,
        ]);
    }

        public function test_owner_can_delete_their_company(): void
    {
        $company = Company::factory()->create();

        $owner = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'owner',
        ]);

        $company->update([
            'owner_id' => $owner->id,
        ]);

        $response = $this
            ->actingAs($owner)
            ->delete(route('company.destroy', $company));

        $response->assertRedirect();

        $this->assertDatabaseMissing('companies', [
            'id' => $company->id,
        ]);
    }

    public function test_admin_cannot_delete_company(): void
    {
        $company = Company::factory()->create();

        $owner = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'owner',
        ]);

        $company->update([
            'owner_id' => $owner->id,
        ]);

        $admin = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin)
            ->delete(route('company.destroy', $company));

        $response->assertForbidden();

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
        ]);
    }

    public function test_member_cannot_delete_company(): void
    {
        $company = Company::factory()->create();

        $owner = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'owner',
        ]);

        $company->update([
            'owner_id' => $owner->id,
        ]);

        $member = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'member',
        ]);

        $response = $this
            ->actingAs($member)
            ->delete(route('company.destroy', $company));

        $response->assertForbidden();

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
        ]);
    }

    public function test_owner_cannot_delete_another_company(): void
    {
        $companyA = Company::factory()->create();

        $ownerA = User::factory()->create([
            'company_id' => $companyA->id,
            'role' => 'owner',
        ]);

        $companyA->update([
            'owner_id' => $ownerA->id,
        ]);

        $companyB = Company::factory()->create();

        $ownerB = User::factory()->create([
            'company_id' => $companyB->id,
            'role' => 'owner',
        ]);

        $companyB->update([
            'owner_id' => $ownerB->id,
        ]);

        $response = $this
            ->actingAs($ownerA)
            ->delete(route('company.destroy', $companyB));

        $response->assertForbidden();

        $this->assertDatabaseHas('companies', [
            'id' => $companyB->id,
        ]);
    }
}