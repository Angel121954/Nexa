<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserPhoto;
use App\Services\CloudinaryService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MigrateCloudinary extends Command
{
    protected $signature = 'migrate:cloudinary';
    protected $description = 'Migrate local storage images to Cloudinary';

    public function handle(CloudinaryService $cloudinary): void
    {
        $disk = Storage::disk('public');

        $this->migrateAvatars($cloudinary, $disk);
        $this->migrateGallery($cloudinary, $disk);

        $this->info('Migration completed.');
    }

    private function migrateAvatars(CloudinaryService $cloudinary, $disk): void
    {
        $users = User::whereNotNull('avatar')
            ->where('avatar', 'not like', 'http%')
            ->where('avatar', 'not like', 'https%')
            ->get();

        if ($users->isEmpty()) {
            $this->info('No local avatars to migrate.');
            return;
        }

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            if (!$disk->exists($user->avatar)) {
                $this->warn("Avatar file not found for user {$user->id}: {$user->avatar}");
                $bar->advance();
                continue;
            }

            try {
                $file = new UploadedFile(
                    $disk->path($user->avatar),
                    basename($user->avatar)
                );

                $uploaded = $cloudinary->uploadAvatar($file, $user->id);
                $oldPath = $user->avatar;

                $user->update([
                    'avatar'           => $uploaded['url'],
                    'avatar_public_id' => $uploaded['public_id'],
                ]);

                $disk->delete($oldPath);
            } catch (\Exception $e) {
                $this->error("Failed to migrate avatar for user {$user->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function migrateGallery(CloudinaryService $cloudinary, $disk): void
    {
        $photos = UserPhoto::where('path', 'not like', 'http%')
            ->where('path', 'not like', 'https%')
            ->get();

        if ($photos->isEmpty()) {
            $this->info('No local gallery photos to migrate.');
            return;
        }

        $bar = $this->output->createProgressBar($photos->count());
        $bar->start();

        foreach ($photos as $photo) {
            if (!$disk->exists($photo->path)) {
                $this->warn("Photo file not found for photo {$photo->id}: {$photo->path}");
                $bar->advance();
                continue;
            }

            try {
                $file = new UploadedFile(
                    $disk->path($photo->path),
                    basename($photo->path)
                );

                $uploaded = $cloudinary->uploadGallery(
                    $file,
                    $photo->user_id,
                    $photo->id
                );
                $oldPath = $photo->path;

                $photo->update([
                    'path'      => $uploaded['url'],
                    'public_id' => $uploaded['public_id'],
                ]);

                $disk->delete($oldPath);
            } catch (\Exception $e) {
                $this->error("Failed to migrate photo {$photo->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }
}
