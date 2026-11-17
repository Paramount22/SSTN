<?php

namespace App\Support;

final class Assets
{
    /**
     * URL of a file in public/ with its modification time appended (?v=…), so browsers
     * fetch the new version after every change instead of a cached copy.
     */
    public static function url(string $path): string
    {
        $file = public_path($path);

        return asset($path).(is_file($file) ? '?v='.filemtime($file) : '');
    }
}
