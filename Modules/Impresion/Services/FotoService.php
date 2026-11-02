<?php

namespace Modules\Impresion\Services;

use Illuminate\Support\Str;
use Modules\Impresion\Entities\Crey;

class FotoService
{
    public function safeName(?string $value, string $fallback = 'SIN DATO'): string
    {
        $value = mb_strtoupper(trim($value ?? ''), 'UTF-8');
        $value = preg_replace('/[\x00-\x1F\x7F<>:"\/\\\\|?*]+/u', ' ', $value);
        $value = trim(preg_replace('/\s+/u', ' ', $value), ". \t");
        $value = mb_substr($value ?: $fallback, 0, 120);
        if (preg_match('/^(CON|PRN|AUX|NUL|COM[1-9]|LPT[1-9])(?:\.|$)/i', $value)) {
            $value = '_'.$value;
        }

        return $value;
    }

    protected function inside(string $root, string $relative): ?string
    {
        if (str_contains($relative, "\0") || str_contains($relative, '..')) {
            return null;
        }
        $base = realpath($root);
        $path = realpath($root.DIRECTORY_SEPARATOR.$relative);
        if (!$base || !$path || !is_file($path) || !is_readable($path)) {
            return null;
        }
        $prefix = rtrim(str_replace('\\', '/', $base), '/').'/';
        $normalized = str_replace('\\', '/', $path);
        $contained = PHP_OS_FAMILY === 'Windows'
            ? str_starts_with(strtolower($normalized), strtolower($prefix))
            : str_starts_with($normalized, $prefix);

        return $contained ? $path : null;
    }

    public function imageInfo(string $path): ?array
    {
        $info = @getimagesize($path);
        $extension = match ($info['mime'] ?? '') {
            'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', default => null,
        };

        return $extension ? ['path' => $path, 'mime' => $info['mime'], 'extension' => $extension] : null;
    }

    public function resolve(Crey $alumno): ?array
    {
        if ($alumno->foto_path) {
            $path = $this->inside(storage_path('app/impresion'), $alumno->foto_path);
            // Una foto administrada sustituye cualquier copia heredada.
            return $path ? $this->imageInfo($path) : null;
        }
        $parts = array_map(fn ($v) => Str::upper(Str::slug($v ?? '', '_')),
            [$alumno->curso ?? 'sin_curso', $alumno->modalidad ?? 'sin_modalidad', $alumno->seccion ?? 'sin_seccion']);
        $nombre = Str::upper(Str::slug($alumno->nombres.' '.$alumno->apellidos, '_'));
        foreach (['jpg', 'jpeg', 'png', 'webp', 'JPG', 'PNG'] as $ext) {
            $path = $this->inside(public_path('uploads/Crey/Estudiantes'), implode('/', $parts).'/'.$nombre.'.'.$ext);
            if ($path && ($info = $this->imageInfo($path))) {
                return $info;
            }
        }
        $codigo = trim($alumno->observaciones ?? '');
        if ($codigo === '' || strcasecmp($codigo, 'Pendiente') === 0
            || preg_match('/[\/\\\\:\x00-\x1F]/', $codigo) || str_contains($codigo, '..')) {
            return null;
        }
        $files = pathinfo($codigo, PATHINFO_EXTENSION) ? [$codigo] : [$codigo.'.jpg', $codigo.'.png', $codigo.'.jpeg', $codigo.'.JPG'];
        foreach ([storage_path('app/public/carnetizacion/crey'), public_path('storage/carnetizacion/crey')] as $root) {
            foreach ($files as $file) {
                $path = $this->inside($root, $file);
                if ($path && ($info = $this->imageInfo($path))) {
                    return $info;
                }
            }
        }

        return null;
    }

    public function importOriginal(string $name): ?string
    {
        $root = config('impresion.fotos_origen');
        if (!$root || preg_match('/[\/\\\\:\x00-\x1F]/', $name) || str_contains($name, '..')) {
            return null;
        }
        foreach (pathinfo($name, PATHINFO_EXTENSION) ? [$name] : [$name.'.jpg', $name.'.png', $name.'.JPG'] as $file) {
            $path = $this->inside($root, $file);
            $info = $path ? $this->imageInfo($path) : null;
            if (!$info) {
                continue;
            }
            $relative = 'fotos/'.Str::uuid().'.'.$info['extension'];
            $destino = storage_path('app/impresion/'.$relative);
            if (!is_dir(dirname($destino))) {
                mkdir(dirname($destino), 0775, true);
            }
            if (!copy($path, $destino)) {
                throw new \RuntimeException('No se pudo copiar una foto de la importación.');
            }

            return $relative;
        }

        return null;
    }

    public function fingerprint(Crey $alumno, array $foto): string
    {
        return hash('sha256', json_encode([
            $alumno->only(['id', 'curso', 'seccion', 'modalidad', 'nombres', 'apellidos', 'foto_path', 'observaciones']),
            $foto['path'], hash_file('sha256', $foto['path']),
        ], JSON_UNESCAPED_UNICODE));
    }
}
