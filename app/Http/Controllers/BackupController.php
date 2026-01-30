<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class BackupController extends Controller
{
    public function index()
    {
        $files = Storage::files('backups');
        return view('admin.backups.index', compact('files'));
    }

    public function create()
    {
        $filename = 'backup_mtk_' . date('Y-m-d') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        $db = config('database.connections.mysql');

        $command = "mysqldump --user={$db['username']} --password={$db['password']} --host={$db['host']} {$db['database']} > {$path}";

        $result = null;
        $output = null;
        exec($command, $output, $result);

        if ($result === 0) {
            return redirect()->back()->with('success', 'Backup creado exitosamente.');
        } else {
            return redirect()->back()->with('error', 'Error al crear el backup.');
        }
    }

    public function download($filename)
    {
        $path = storage_path("app/backups/{$filename}");

        if (File::exists($path)) {
            return response()->download($path);
        }

        return redirect()->back()->with('error', 'Archivo no encontrado.');
    }
}
