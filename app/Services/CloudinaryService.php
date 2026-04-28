<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    private string $cloudName;
    private string $apiKey;
    private string $apiSecret;

    public function __construct()
    {
        $this->cloudName = config('services.cloudinary.cloud_name');
        $this->apiKey    = config('services.cloudinary.key');
        $this->apiSecret = config('services.cloudinary.secret');
    }

    // ─────────────────────────────────────────
    // Métodos públicos
    // ─────────────────────────────────────────

    public function uploadAvatar(UploadedFile $file, int $userId): array
    {
        return $this->upload($file, 'nexa/avatares', "user_{$userId}");
    }

    public function uploadAvatarFromUrl(string $url, int $userId): array
    {
        return $this->uploadUrl($url, 'nexa/avatares', "user_{$userId}");
    }

    public function uploadGallery(UploadedFile $file, int $userId, int $index): array
    {
        return $this->upload($file, 'nexa/galeria', "user_{$userId}_{$index}");
    }

    public function delete(string $publicId): void
    {
        $timestamp = time();

        $signature = $this->sign([
            'public_id' => $publicId,
            'timestamp' => $timestamp,
        ]);

        Http::withOptions(['verify' => false])
            ->asMultipart()
            ->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy", [
                ['name' => 'public_id', 'contents' => $publicId],
                ['name' => 'api_key',   'contents' => $this->apiKey],
                ['name' => 'timestamp', 'contents' => $timestamp],
                ['name' => 'signature', 'contents' => $signature],
            ]);
    }

    // ─────────────────────────────────────────
    // Métodos privados
    // ─────────────────────────────────────────

    private function upload(UploadedFile $file, string $folder, string $publicId): array
    {
        $timestamp = time();

        $signature = $this->sign([
            'folder'    => $folder,
            'public_id' => $publicId,
            'timestamp' => $timestamp,
        ]);

        $response = Http::withOptions(['verify' => false])
            ->asMultipart()
            ->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload", [
                ['name' => 'file',      'contents' => fopen($file->getRealPath(), 'r'), 'filename' => $file->getClientOriginalName()],
                ['name' => 'api_key',   'contents' => $this->apiKey],
                ['name' => 'timestamp', 'contents' => $timestamp],
                ['name' => 'signature', 'contents' => $signature],
                ['name' => 'public_id', 'contents' => $publicId],
                ['name' => 'folder',    'contents' => $folder],
            ]);

        if ($response->failed()) {
            Log::error('Cloudinary upload error', ['body' => $response->body()]);
            throw new \Exception('Cloudinary error: ' . $response->body());
        }

        return [
            'url'       => $response->json('secure_url'),
            'public_id' => $response->json('public_id'),
        ];
    }

    private function uploadUrl(string $url, string $folder, string $publicId): array
    {
        $timestamp = time();

        $signature = $this->sign([
            'folder'    => $folder,
            'public_id' => $publicId,
            'timestamp' => $timestamp,
        ]);

        $response = Http::withOptions(['verify' => false])
            ->asMultipart()
            ->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload", [
                ['name' => 'file',      'contents' => $url],
                ['name' => 'api_key',   'contents' => $this->apiKey],
                ['name' => 'timestamp', 'contents' => $timestamp],
                ['name' => 'signature', 'contents' => $signature],
                ['name' => 'public_id', 'contents' => $publicId],
                ['name' => 'folder',    'contents' => $folder],
            ]);

        if ($response->failed()) {
            Log::error('Cloudinary uploadUrl error', ['body' => $response->body()]);
            throw new \Exception('Cloudinary error: ' . $response->body());
        }

        return [
            'url'       => $response->json('secure_url'),
            'public_id' => $response->json('public_id'),
        ];
    }

    /**
     * Genera la firma según la especificación de Cloudinary:
     * 1. Parámetros ordenados alfabéticamente (SIN api_key, SIN file)
     * 2. String: "key=value&key=value"
     * 3. Concatenar el API Secret al final
     * 4. SHA-1 (NO hash_hmac)
     */
    private function sign(array $params): string
    {
        ksort($params);

        // http_build_query codifica '/' como '%2F'
        // Cloudinary espera las barras sin codificar → urldecode
        $stringToSign = urldecode(http_build_query($params)) . $this->apiSecret;

        return sha1($stringToSign);
    }
}
