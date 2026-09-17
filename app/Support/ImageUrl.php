<?php

namespace App\Support;

final class ImageUrl
{
    public static function resolve(?string $path): string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return asset('assets/image-6.png');
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        $clean = ltrim(
            preg_replace('#^(\.\.?/)+#', '', str_replace('\\', '/', $path)),
            '/'
        );
        $clean = preg_replace('#^shizen-home/public/#i', '', $clean);
        $clean = preg_replace('#^shizen_movil/#i', '', $clean);

        if (is_file(public_path($clean))) {
            return asset($clean);
        }

        $catalogPath = 'images/catalogo/' . $clean;
        if (is_file(public_path($catalogPath))) {
            return asset($catalogPath);
        }

        $basename = basename($clean);
        $pruebaPath = 'images/catalogo/Imagenes_prueba/' . $basename;
        if (is_file(public_path($pruebaPath))) {
            return asset($pruebaPath);
        }

        return asset('assets/image-6.png');
    }
}
