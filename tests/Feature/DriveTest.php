<?php

namespace Tests\Feature;

use App\Models\DriveFile;
use App\Models\DriveFolder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DriveTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_authenticated_user_can_create_a_folder_and_upload_a_file(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('drive.folders.store'), [
                'name' => 'Client Files',
            ])
            ->assertRedirect(route('dashboard', absolute: false));

        $folder = $user->driveFolders()->first();

        $this->assertNotNull($folder);
        $this->assertSame('Client Files', $folder->name);

        $uploadResponse = $this->actingAs($user)->post(route('drive.files.store'), [
            'folder_id' => $folder->id,
            'files' => [
                UploadedFile::fake()->create('report.pdf', 128, 'application/pdf'),
            ],
        ]);

        $uploadResponse->assertRedirect(route('dashboard', ['folder' => $folder->id], absolute: false));

        $file = $user->driveFiles()->first();

        $this->assertNotNull($file);
        $this->assertSame('report.pdf', $file->name);
        $this->assertSame($folder->id, $file->drive_folder_id);
        Storage::disk('local')->assertExists($file->path);

        $this->actingAs($user)
            ->get(route('dashboard', ['folder' => $folder->id]))
            ->assertOk()
            ->assertSee('Client Files')
            ->assertSee('report.pdf');
    }

    public function test_users_cannot_download_files_owned_by_another_account(): void
    {
        Storage::fake('local');

        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        Storage::disk('local')->put('drive/'.$owner->id.'/shared.txt', 'secret');

        $file = DriveFile::create([
            'user_id' => $owner->id,
            'drive_folder_id' => null,
            'name' => 'shared.txt',
            'disk' => 'local',
            'path' => 'drive/'.$owner->id.'/shared.txt',
            'mime_type' => 'text/plain',
            'extension' => 'txt',
            'size' => 6,
        ]);

        $this->actingAs($otherUser)
            ->get(route('drive.files.download', $file))
            ->assertNotFound();
    }

    public function test_users_can_switch_to_largest_files_view_for_cleanup(): void
    {
        $user = User::factory()->create();

        $folder = DriveFolder::create([
            'user_id' => $user->id,
            'parent_id' => null,
            'name' => 'Archive',
        ]);

        DriveFile::create([
            'user_id' => $user->id,
            'drive_folder_id' => $folder->id,
            'name' => 'small-note.txt',
            'disk' => 'local',
            'path' => 'drive/'.$user->id.'/small-note.txt',
            'mime_type' => 'text/plain',
            'extension' => 'txt',
            'size' => 128,
        ]);

        DriveFile::create([
            'user_id' => $user->id,
            'drive_folder_id' => null,
            'name' => 'big-video.mp4',
            'disk' => 'local',
            'path' => 'drive/'.$user->id.'/big-video.mp4',
            'mime_type' => 'video/mp4',
            'extension' => 'mp4',
            'size' => 5_000_000,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard', ['file_view' => 'largest']))
            ->assertOk()
            ->assertSee('Largest Files')
            ->assertSeeInOrder(['big-video.mp4', 'small-note.txt']);
    }
}
