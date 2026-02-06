<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivityLog;
use Carbon\Carbon;

class BackupController extends Controller
{
    /**
     * Display the backup management page.
     */
    public function index()
    {
        $backups = [];
        $disk = Storage::disk('local');
        $backupPath = 'backups';

        if ($disk->exists($backupPath)) {
            $files = $disk->files($backupPath);

            foreach ($files as $file) {
                $backups[] = [
                    'name'      => basename($file),
                    'path'      => $file,
                    'size'      => $this->formatFileSize($disk->size($file)),
                    'date'      => Carbon::createFromTimestamp($disk->lastModified($file))->format('M d, Y h:i A'),
                    'timestamp' => $disk->lastModified($file),
                ];
            }

            // Sort by timestamp descending (newest first)
            usort($backups, function ($a, $b) {
                return $b['timestamp'] - $a['timestamp'];
            });
        }

        return view('backup.index', compact('backups'));
    }

    /**
     * Create a new database backup.
     */
    public function create(Request $request)
    {
        try {
            $disk = Storage::disk('local');
            $backupPath = 'backups';

            // Ensure backup directory exists
            if (!$disk->exists($backupPath)) {
                $disk->makeDirectory($backupPath);
            }

            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';

            // Get database configuration
            $dbHost     = config('database.connections.mysql.host', '127.0.0.1');
            $dbPort     = config('database.connections.mysql.port', '3306');
            $dbName     = config('database.connections.mysql.database');
            $dbUser     = config('database.connections.mysql.username');
            $dbPassword = config('database.connections.mysql.password');

            $storagePath = storage_path("app/{$backupPath}/{$filename}");

            // Build mysqldump command
            $command = sprintf(
                'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                escapeshellarg($dbPassword),
                escapeshellarg($dbName),
                escapeshellarg($storagePath)
            );

            $returnVar = null;
            $output    = null;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                return redirect()->route('backup.index')
                    ->with('error', 'Failed to create database backup. Please check database configuration and ensure mysqldump is available.');
            }

            ActivityLog::create([
                'user_id'    => auth()->id(),
                'action'     => 'Create Backup',
                'module'     => 'Backup',
                'details'    => "Created database backup: {$filename}.",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('backup.index')
                ->with('success', "Database backup '{$filename}' has been created successfully.");

        } catch (\Exception $e) {
            return redirect()->route('backup.index')
                ->with('error', 'Failed to create backup: ' . $e->getMessage());
        }
    }

    /**
     * Restore a database backup.
     */
    public function restore(Request $request)
    {
        $validated = $request->validate([
            'backup_file' => 'required|string',
        ]);

        try {
            $disk = Storage::disk('local');

            if (!$disk->exists($validated['backup_file'])) {
                return redirect()->route('backup.index')
                    ->with('error', 'Backup file not found.');
            }

            // Get database configuration
            $dbHost     = config('database.connections.mysql.host', '127.0.0.1');
            $dbPort     = config('database.connections.mysql.port', '3306');
            $dbName     = config('database.connections.mysql.database');
            $dbUser     = config('database.connections.mysql.username');
            $dbPassword = config('database.connections.mysql.password');

            $storagePath = storage_path("app/{$validated['backup_file']}");

            // Build mysql restore command
            $command = sprintf(
                'mysql --host=%s --port=%s --user=%s --password=%s %s < %s',
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                escapeshellarg($dbPassword),
                escapeshellarg($dbName),
                escapeshellarg($storagePath)
            );

            $returnVar = null;
            $output    = null;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                return redirect()->route('backup.index')
                    ->with('error', 'Failed to restore database backup. Please check database configuration.');
            }

            $filename = basename($validated['backup_file']);

            ActivityLog::create([
                'user_id'    => auth()->id(),
                'action'     => 'Restore Backup',
                'module'     => 'Backup',
                'details'    => "Restored database from backup: {$filename}.",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('backup.index')
                ->with('success', "Database has been restored from backup '{$filename}' successfully.");

        } catch (\Exception $e) {
            return redirect()->route('backup.index')
                ->with('error', 'Failed to restore backup: ' . $e->getMessage());
        }
    }

    /**
     * Format file size to human-readable format.
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen((string) $bytes) - 1) / 3);

        return sprintf("%.2f %s", $bytes / pow(1024, $factor), $units[$factor] ?? 'TB');
    }
}
