<?php

namespace App\Console\Commands;

use App\Models\Story;
use App\Models\User;
use App\Models\UserPhoto;
use App\Services\CloudinaryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupCloudinaryOrphans extends Command
{
    protected $signature = 'cleanup:cloudinary-orphans
                           {--dry-run : Only show what would be deleted without actually deleting}';

    protected $description = 'Delete orphaned Cloudinary images (avatars, gallery, stories) no longer referenced in the database';

    public function handle(CloudinaryService $cloudinary): void
    {
        $dryRun = $this->option('dry-run');

        $this->info($dryRun
            ? ' DRY-RUN mode — no changes will be made'
            : ' Cleaning up Cloudinary orphans...');

        $this->cleanExpiredStories($cloudinary, $dryRun);
        $this->cleanOrphanAvatars($cloudinary, $dryRun);
        $this->cleanOrphanGallery($cloudinary, $dryRun);

        $this->newLine();
        $this->info(' Done.');
    }

    private function cleanExpiredStories(CloudinaryService $cloudinary, bool $dryRun): void
    {
        $this->newLine();
        $this->line(' Stories expiredas...');

        $expired = Story::where('expires_at', '<=', now())->get();

        if ($expired->isEmpty()) {
            $this->warn('  No se encontraron stories expiradas.');
            return;
        }

        $this->line("  Se encontraron {$expired->count()} stories expiradas.");

        $bar = $this->output->createProgressBar($expired->count());
        $bar->start();

        foreach ($expired as $story) {
            if ($story->public_id) {
                $this->line("    → Eliminando: {$story->public_id}");
                if (!$dryRun) {
                    try {
                        $cloudinary->delete($story->public_id);
                    } catch (\Exception $e) {
                        Log::warning("Error eliminando story {$story->id} de Cloudinary: " . $e->getMessage());
                    }
                }
            }

            if (!$dryRun) {
                $story->views()->delete();
                $story->delete();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function cleanOrphanAvatars(CloudinaryService $cloudinary, bool $dryRun): void
    {
        $this->newLine();
        $this->line(' Avatares huerfanos...');

        $validPublicIds = User::whereNotNull('avatar_public_id')
            ->pluck('avatar_public_id')
            ->toArray();

        try {
            $cloudinaryResources = $cloudinary->listResources('nexa/avatares/');
        } catch (\Exception $e) {
            $this->error('  Error al listar avatares en Cloudinary: ' . $e->getMessage());
            return;
        }

        $orphans = [];
        foreach ($cloudinaryResources as $resource) {
            if (!in_array($resource['public_id'], $validPublicIds, true)) {
                $orphans[] = $resource['public_id'];
            }
        }

        if (empty($orphans)) {
            $this->warn('  No se encontraron avatares huérfanos.');
            return;
        }

        $this->line('  Se encontraron ' . count($orphans) . ' avatar(es) huérfano(s):');

        foreach ($orphans as $publicId) {
            $this->line("    → Eliminando: {$publicId}");
            if (!$dryRun) {
                try {
                    $cloudinary->delete($publicId);
                } catch (\Exception $e) {
                    $this->error("    Error: {$e->getMessage()}");
                }
            }
        }
    }

    private function cleanOrphanGallery(CloudinaryService $cloudinary, bool $dryRun): void
    {
        $this->newLine();
        $this->line(' Fotos de galeria huerfanas...');

        $validPublicIds = UserPhoto::whereNotNull('public_id')
            ->pluck('public_id')
            ->toArray();

        try {
            $cloudinaryResources = $cloudinary->listResources('nexa/galeria/');
        } catch (\Exception $e) {
            $this->error('  Error al listar galería en Cloudinary: ' . $e->getMessage());
            return;
        }

        $orphans = [];
        foreach ($cloudinaryResources as $resource) {
            if (!in_array($resource['public_id'], $validPublicIds, true)) {
                $orphans[] = $resource['public_id'];
            }
        }

        if (empty($orphans)) {
            $this->warn('  No se encontraron fotos de galería huérfanas.');
            return;
        }

        $this->line('  Se encontraron ' . count($orphans) . ' foto(s) de galería huérfana(s):');

        foreach ($orphans as $publicId) {
            $this->line("    → Eliminando: {$publicId}");
            if (!$dryRun) {
                try {
                    $cloudinary->delete($publicId);
                } catch (\Exception $e) {
                    $this->error("    Error: {$e->getMessage()}");
                }
            }
        }
    }
}
