<?php

namespace App\Helpers;

use App\Models\LogoIdentitas;
use Illuminate\Support\Facades\Storage;

class LogoHelper
{
    /**
     * Get logo URL untuk tenant
     * 
     * @param int|null $tenantId
     * @param string $type ('navbar', 'favicon', 'sidebar', etc)
     * @return string
     */
    public static function getLogoUrl($tenantId = null, $type = 'navbar')
    {
        if (!$tenantId) {
            $tenantId = tenancy()->tenant?->id;
        }

        if (!$tenantId) {
            return self::getDefaultLogo($type);
        }

        // Cek apakah ada logo untuk tenant ini di database
        $logo = LogoIdentitas::where('tenant_id', $tenantId)
            ->orderBy('uploaded_at', 'desc')
            ->first();

        if ($logo && Storage::disk('public')->exists($logo->path)) {
            return asset('storage/' . $logo->path);
        }

        return self::getDefaultLogo($type);
    }

    /**
     * Get default logo URL
     * 
     * @param string $type
     * @return string
     */
    private static function getDefaultLogo($type = 'navbar')
    {
        // Return default logos berdasarkan type
        $defaults = [
            'navbar' => asset('images/default-logo-navbar.png'),
            'favicon' => asset('images/default-favicon.png'),
            'sidebar' => asset('images/default-logo-sidebar.png'),
        ];

        return $defaults[$type] ?? asset('images/default-logo.png');
    }

    /**
     * Get all logos untuk tenant
     * 
     * @param int|null $tenantId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTenantLogos($tenantId = null)
    {
        if (!$tenantId) {
            $tenantId = tenancy()->tenant?->id;
        }

        if (!$tenantId) {
            return collect();
        }

        return LogoIdentitas::where('tenant_id', $tenantId)
            ->orderBy('uploaded_at', 'desc')
            ->get();
    }

    /**
     * Check apakah tenant punya custom logo
     * 
     * @param int|null $tenantId
     * @return bool
     */
    public static function hasCustomLogo($tenantId = null)
    {
        if (!$tenantId) {
            $tenantId = tenancy()->tenant?->id;
        }

        if (!$tenantId) {
            return false;
        }

        return LogoIdentitas::where('tenant_id', $tenantId)->exists();
    }

    /**
     * Format file size untuk display
     * 
     * @param int $bytes
     * @return string
     */
    public static function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
