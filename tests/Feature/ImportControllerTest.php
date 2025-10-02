<?php

/**
 * Import Controller Feature Tests
 *
 * Tests for the Import API controller functionality including
 * authentication, authorization, and CRUD operations.
 *
 * @category Tests
 * @package  Tests\Feature
 * @author   Sellora Team
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace Tests\Feature;

use App\Models\ImportJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Import Controller Test Class
 *
 * Comprehensive tests for import functionality including RBAC controls
 *
 * @category Tests
 * @package  Tests\Feature
 * @author   Sellora Team
 * @license  MIT License
 * @link     https://sellora.com
 */
class ImportControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that unauthenticated users cannot access import endpoints
     *
     * @return void
     */
    public function testUnauthenticatedUserCannotAccessImportEndpoints(): void
    {
        $response = $this->getJson('/api/imports');
        $response->assertStatus(401);

        $response = $this->postJson('/api/imports');
        $response->assertStatus(401);
    }

    /**
     * Test that unauthorized roles cannot access import endpoints
     *
     * @return void
     */
    public function testUnauthorizedRoleCannotAccessImportEndpoints(): void
    {
        $user = User::factory()->create(['role' => 'MR']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/imports');
        $response->assertStatus(403);

        $response = $this->postJson('/api/imports');
        $response->assertStatus(403);
    }

    /**
     * Test that admin users can list import jobs
     *
     * @return void
     */
    public function testAdminCanListImportJobs(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        Sanctum::actingAs($admin);

        ImportJob::factory()->count(3)->create(['user_id' => $admin->id]);

        $response = $this->getJson('/api/imports');
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'filename',
                             'status',
                             'total_records',
                             'processed_records',
                             'failed_records',
                             'created_at'
                         ]
                     ],
                     'meta'
                 ]);
    }

    /**
     * Test that admin users can create import jobs
     *
     * @return void
     */
    public function testAdminCanCreateImportJob(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        Sanctum::actingAs($admin);

        $file = UploadedFile::fake()->create(
            'test.csv',
            100,
            'text/csv'
        );

        $response = $this->postJson(
            '/api/imports',
            [
                'file' => $file,
                'file_type' => 'csv',
                'mapping' => ['name' => 'full_name'],
                'options' => ['delimiter' => ',', 'has_header' => true]
            ]
        );

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'filename',
                         'status',
                         'file_type'
                     ]
                 ]);

        $this->assertDatabaseHas(
            'import_jobs',
            [
                'user_id' => $admin->id,
                'filename' => 'test.csv',
                'file_type' => 'csv',
                'status' => 'pending'
            ]
        );
    }

    /**
     * Test that manager users can access import functionality
     *
     * @return void
     */
    public function testManagerCanAccessImportFunctionality(): void
    {
        $manager = User::factory()->create(['role' => 'Manager']);
        Sanctum::actingAs($manager);

        $response = $this->getJson('/api/imports');
        $response->assertStatus(200);
    }

    /**
     * Test that author users can access import functionality
     *
     * @return void
     */
    public function testAuthorCanAccessImportFunctionality(): void
    {
        $author = User::factory()->create(['role' => 'Author']);
        Sanctum::actingAs($author);

        $response = $this->getJson('/api/imports');
        $response->assertStatus(200);
    }

    /**
     * Test that import job validation fails with invalid data
     *
     * @return void
     */
    public function testImportJobValidationFailsWithInvalidData(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        Sanctum::actingAs($admin);

        $response = $this->postJson(
            '/api/imports',
            [
                'file_type' => 'invalid',
                'mapping' => 'not_an_array'
            ]
        );

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['file', 'file_type', 'mapping']);
    }

    /**
     * Test that users can show specific import jobs
     *
     * @return void
     */
    public function testCanShowSpecificImportJob(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        Sanctum::actingAs($admin);

        $importJob = ImportJob::factory()->create(['user_id' => $admin->id]);

        $response = $this->getJson("/api/imports/{$importJob->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'filename',
                         'status',
                         'progress_percentage',
                         'error_message'
                     ]
                 ]);
    }

    /**
     * Test that users can delete import jobs
     *
     * @return void
     */
    public function testCanDeleteImportJob(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        Sanctum::actingAs($admin);

        $importJob = ImportJob::factory()->create(['user_id' => $admin->id]);

        $response = $this->deleteJson("/api/imports/{$importJob->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('import_jobs', ['id' => $importJob->id]);
    }

    /**
     * Test that users cannot access other users' import jobs
     *
     * @return void
     */
    public function testCannotAccessOtherUsersImportJobs(): void
    {
        $user1 = User::factory()->create(['role' => 'Manager']);
        $user2 = User::factory()->create(['role' => 'Manager']);
        
        $importJob = ImportJob::factory()->create(['user_id' => $user2->id]);
        
        Sanctum::actingAs($user1);

        $response = $this->getJson("/api/imports/{$importJob->id}");
        $response->assertStatus(404);
    }
}
