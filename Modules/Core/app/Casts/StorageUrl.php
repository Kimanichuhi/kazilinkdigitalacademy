<?php

namespace Modules\Core\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Core\Livewire\ImageUpload bakes an absolute asset() URL into the value
 * it hands back to the parent form, which becomes stale the moment
 * APP_URL changes — dev -> ngrok tunnel -> production -> a later, different
 * tunnel. Every previously uploaded image then points at a dead host and
 * gets CSP-blocked as cross-origin content.
 *
 * This cast stores only the storage-relative path and re-resolves it
 * against the *current* APP_URL on every read, so it can never go stale.
 * get() also self-heals old rows that already have a stale absolute URL
 * baked in — no data migration needed. Values that aren't ours at all
 * (an externally hosted image URL with no '/storage/' segment) are left
 * untouched in both directions.
 */
class StorageUrl implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): ?string
    {
        if (blank($value)) {
            return $value;
        }

        $relative = $this->relativePath($value);

        return $relative !== null ? asset('storage/'.$relative) : $value;
    }

    public function set($model, string $key, $value, array $attributes): ?string
    {
        if (blank($value)) {
            return $value;
        }

        return $this->relativePath($value) ?? $value;
    }

    private function relativePath(string $value): ?string
    {
        $marker = '/storage/';
        $pos = strpos($value, $marker);

        if ($pos !== false) {
            return substr($value, $pos + strlen($marker));
        }

        return preg_match('#^https?://#', $value) ? null : ltrim($value, '/');
    }
}
