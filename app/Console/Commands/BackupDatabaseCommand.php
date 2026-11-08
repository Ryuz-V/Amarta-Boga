<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupDatabaseCommand extends Command
{
    protected $signature = 'app:backup-db';
    protected $description = 'Melakukan pencadangan (backup) otomatis database MySQL ke storage/app/backups';

    public function handle(): int
    {
        $this->info('Memulai proses backup database Amarta Boga POS...');

        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $database = config('database.connections.mysql.database');
        $filename = 'backup_' . $database . '_' . date('Y_m_d_His') . '.sql';
        $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        try {
            $tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' . $database;

            $sqlDump = "-- ========================================================\n";
            $sqlDump .= "-- Amarta Boga POS - Database Backup\n";
            $sqlDump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $sqlDump .= "-- Database: {$database}\n";
            $sqlDump .= "-- ========================================================\n\n";
            $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $t) {
                $tableName = $t->$tableKey;
                $this->line("• Memproses tabel: {$tableName}");

                // 1. Structure
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $createSql = $createTable[0]->{'Create Table'} ?? null;

                if ($createSql) {
                    $sqlDump .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                    $sqlDump .= $createSql . ";\n\n";
                }

                // 2. Data
                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $sqlDump .= "INSERT INTO `{$tableName}` VALUES \n";
                    $valuesArr = [];
                    foreach ($rows as $row) {
                        $escapedValues = array_map(function ($val) {
                            if ($val === null) return 'NULL';
                            return "'" . addslashes((string) $val) . "'";
                        }, (array) $row);
                        $valuesArr[] = "(" . implode(', ', $escapedValues) . ")";
                    }
                    $sqlDump .= implode(",\n", $valuesArr) . ";\n\n";
                }
            }

            $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

            File::put($filepath, $sqlDump);

            $fileSizeBytes = File::size($filepath);
            $fileSizeKb = round($fileSizeBytes / 1024, 2);

            $this->info("✓ Berhasil! Cadangan database disimpan di:");
            $this->info("  {$filepath} ({$fileSizeKb} KB)");

            // Hapus backup lama yang berusia lebih dari 14 hari
            $files = File::files($backupDir);
            $deletedCount = 0;
            $fourteenDaysAgo = now()->subDays(14)->timestamp;

            foreach ($files as $file) {
                if ($file->getMTime() < $fourteenDaysAgo && $file->getExtension() === 'sql') {
                    File::delete($file->getPathname());
                    $deletedCount++;
                }
            }

            if ($deletedCount > 0) {
                $this->line("• Membersihkan {$deletedCount} file backup lama (> 14 hari).");
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Gagal melakukan backup database: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
