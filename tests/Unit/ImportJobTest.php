<?php

namespace Tests\Unit;

use App\Models\ImportJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_job_can_be_created(): void
    {
        $user = User::factory()->create();
        
        $importJob = ImportJob::create([
            'user_id' => $user->id,
            'filename' => 'test.csv',
            'file_path' => 'imports/test.csv',
            'file_type' => 'csv',
            'status' => 'pending',
            'total_records' => 100,
            'processed_records' => 0,
            'failed_records' => 0,
            'mapping' => ['name' => 'full_name', 'email' => 'email_address'],
            'options' => ['delimiter' => ',', 'has_header' => true]
        ]);

        $this->assertInstanceOf(ImportJob::class, $importJob);
        $this->assertEquals('test.csv', $importJob->filename);
        $this->assertEquals('pending', $importJob->status);
        $this->assertEquals(100, $importJob->total_records);
    }

    public function test_import_job_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $importJob = ImportJob::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $importJob->user);
        $this->assertEquals($user->id, $importJob->user->id);
    }

    public function test_import_job_has_many_import_items(): void
    {
        $importJob = ImportJob::factory()->create();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $importJob->items);
    }

    public function test_import_job_calculates_progress_percentage(): void
    {
        $importJob = ImportJob::factory()->create([
            'total_records' => 100,
            'processed_records' => 25
        ]);

        $this->assertEquals(25, $importJob->progress_percentage);
    }

    public function test_import_job_handles_zero_total_records(): void
    {
        $importJob = ImportJob::factory()->create([
            'total_records' => 0,
            'processed_records' => 0
        ]);

        $this->assertEquals(0, $importJob->progress_percentage);
    }

    public function test_import_job_validates_required_fields(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        ImportJob::create([
            // Missing required fields
        ]);
    }

    public function test_import_job_casts_json_fields(): void
    {
        $mapping = ['name' => 'full_name', 'email' => 'email_address'];
        $options = ['delimiter' => ',', 'has_header' => true];
        
        $importJob = ImportJob::factory()->create([
            'mapping' => $mapping,
            'options' => $options
        ]);

        $this->assertIsArray($importJob->mapping);
        $this->assertIsArray($importJob->options);
        $this->assertEquals($mapping, $importJob->mapping);
        $this->assertEquals($options, $importJob->options);
    }
}
