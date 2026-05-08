<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FileUploadService
{
    private ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Upload foto profil untuk guru atau admin.
     * Resize ke 300x300, simpan di storage/app/tenants/{tenant_id}/profile_photos/{user_type}/{user_id}.jpg
     */
    public function uploadProfilePhoto(UploadedFile $file, string $userType, int|string $userId): string
    {
        $tenantId = $this->getTenantId();
        $directory = "tenants/{$tenantId}/profile_photos/{$userType}";
        $filename = "{$userId}.jpg";
        $path = "{$directory}/{$filename}";

        $image = $this->imageManager->read($file->getRealPath());
        $image->cover(300, 300);

        Storage::put($path, $image->toJpeg(85)->toString());

        return $path;
    }

    /**
     * Upload foto siswa.
     * Resize ke 200x200, simpan di storage/app/tenants/{tenant_id}/student_photos/{siswa_id}.jpg
     */
    public function uploadStudentPhoto(UploadedFile $file, int|string $siswaId): string
    {
        $tenantId = $this->getTenantId();
        $directory = "tenants/{$tenantId}/student_photos";
        $filename = "{$siswaId}.jpg";
        $path = "{$directory}/{$filename}";

        $image = $this->imageManager->read($file->getRealPath());
        $image->cover(200, 200);

        Storage::put($path, $image->toJpeg(85)->toString());

        return $path;
    }

    /**
     * Upload logo sekolah (navbar_logo atau favicon).
     * favicon: resize ke 32x32
     * navbar_logo: resize ke 200x50 (contain, tidak crop)
     */
    public function uploadLogo(UploadedFile $file, string $tenantId, string $logoType): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $directory = "tenants/{$tenantId}/logos";
        $filename = "{$logoType}.{$extension}";
        $path = "{$directory}/{$filename}";

        // SVG tidak perlu di-resize
        if ($extension === 'svg') {
            Storage::put($path, file_get_contents($file->getRealPath()));
            return $path;
        }

        $image = $this->imageManager->read($file->getRealPath());

        if ($logoType === 'favicon') {
            $image->cover(32, 32);
        } elseif ($logoType === 'navbar_logo') {
            $image->scale(width: 200, height: 50);
        } else {
            $image->scale(width: 400);
        }

        Storage::put($path, $image->toJpeg(90)->toString());

        return $path;
    }

    /**
     * Hapus file dari storage.
     */
    public function deleteFile(string $filePath): bool
    {
        if (Storage::exists($filePath)) {
            return Storage::delete($filePath);
        }

        return false;
    }

    /**
     * Ambil tenant_id dari context tenancy yang aktif.
     */
    private function getTenantId(): string
    {
        $tenant = tenancy()->tenant;

        if ($tenant) {
            return $tenant->id;
        }

        // Fallback untuk super_admin context
        return 'global';
    }
}
