<?php

namespace Tests\Feature\File;

use App\Models\User;
use App\Models\UserFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake(config('files.disk'));
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    public function test_unauthenticated_user_cannot_list_files(): void
    {
        $this->getJson('/api/files')->assertStatus(401);
    }

    public function test_user_can_list_their_files(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        UserFile::factory()->count(2)->for($user)->create();
        UserFile::factory()->count(3)->for($other)->create();

        $this->actingAs($user)->getJson('/api/files')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_file_list_is_paginated(): void
    {
        $user = User::factory()->create();
        UserFile::factory()->count(20)->for($user)->create();

        $response = $this->actingAs($user)->getJson('/api/files');

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 20)
            ->assertJsonCount(15, 'data');
    }

    public function test_user_can_search_files_by_name(): void
    {
        $user = User::factory()->create();
        UserFile::factory()->for($user)->create(['original_name' => 'report_2024.pdf']);
        UserFile::factory()->for($user)->create(['original_name' => 'photo.png']);

        $this->actingAs($user)->getJson('/api/files?search=report')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'report_2024.pdf');
    }

    // -------------------------------------------------------------------------
    // Upload
    // -------------------------------------------------------------------------

    public function test_unauthenticated_user_cannot_upload(): void
    {
        $this->postJson('/api/files', [
            'files' => [UploadedFile::fake()->create('test.pdf', 100, 'application/pdf')],
        ])->assertStatus(401);
    }

    public function test_user_can_upload_a_single_file(): void
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->postJson('/api/files', [
            'files' => [$file],
        ]);

        $response->assertStatus(201)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure(['data' => [['id', 'name', 'size', 'mime_type', 'created_at']]]);

        $this->assertDatabaseHas('user_files', [
            'user_id' => $user->id,
            'original_name' => 'document.pdf',
        ]);

        Storage::disk(config('files.disk'))->assertExists(
            UserFile::where('user_id', $user->id)->first()->path
        );
    }

    public function test_user_can_upload_multiple_files(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/files', [
            'files' => [
                UploadedFile::fake()->create('a.pdf', 100, 'application/pdf'),
                UploadedFile::fake()->create('b.png', 100, 'image/png'),
                UploadedFile::fake()->create('c.jpg', 100, 'image/jpeg'),
            ],
        ]);

        $response->assertStatus(201)->assertJsonCount(3, 'data');
        $this->assertDatabaseCount('user_files', 3);
    }

    public function test_upload_rejects_more_than_5_files(): void
    {
        $user = User::factory()->create();

        $files = array_fill(0, 6, UploadedFile::fake()->create('x.pdf', 100, 'application/pdf'));

        $this->actingAs($user)->postJson('/api/files', ['files' => $files])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['files']);
    }

    public function test_upload_rejects_disallowed_mime_type(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/api/files', [
            'files' => [UploadedFile::fake()->create('malware.exe', 100, 'application/x-msdownload')],
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['files.0']);
    }

    public function test_upload_requires_files_field(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/api/files', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['files']);
    }

    // -------------------------------------------------------------------------
    // Download
    // -------------------------------------------------------------------------

    public function test_unauthenticated_user_cannot_download(): void
    {
        $userFile = UserFile::factory()->create();

        $this->getJson("/api/files/{$userFile->id}/download")->assertStatus(401);
    }

    public function test_user_can_download_their_own_file(): void
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $path = $file->store(config('files.upload_path').'/'.$user->id, config('files.disk'));

        $userFile = UserFile::factory()->for($user)->create([
            'path' => $path,
            'disk' => config('files.disk'),
            'original_name' => 'document.pdf',
        ]);

        $this->actingAs($user)->get("/api/files/{$userFile->id}/download")
            ->assertStatus(200);
    }

    public function test_user_cannot_download_another_users_file(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $userFile = UserFile::factory()->for($other)->create();

        $this->actingAs($user)->getJson("/api/files/{$userFile->id}/download")
            ->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function test_unauthenticated_user_cannot_delete(): void
    {
        $userFile = UserFile::factory()->create();

        $this->deleteJson("/api/files/{$userFile->id}")->assertStatus(401);
    }

    public function test_user_can_delete_their_own_file(): void
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $path = $file->store(config('files.upload_path').'/'.$user->id, config('files.disk'));

        $userFile = UserFile::factory()->for($user)->create([
            'path' => $path,
            'disk' => config('files.disk'),
        ]);

        $this->actingAs($user)->deleteJson("/api/files/{$userFile->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('user_files', ['id' => $userFile->id]);
        Storage::disk(config('files.disk'))->assertMissing($path);
    }

    public function test_user_cannot_delete_another_users_file(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $userFile = UserFile::factory()->for($other)->create();

        $this->actingAs($user)->deleteJson("/api/files/{$userFile->id}")
            ->assertStatus(403);
    }
}
