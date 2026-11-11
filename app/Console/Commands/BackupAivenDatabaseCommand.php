<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

#[Signature('db:backup {--format=all : Backup format (sql, json, all)}')]
#[Description('Backup full database data and structure from configured MySQL (Aiven) to storage/backups')]
class BackupAivenDatabaseCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dbConfig = config('database.connections.mysql');
        $host = (string) ($dbConfig['host'] ?? 'localhost');
        $dbName = (string) ($dbConfig['database'] ?? 'dyzzdb');
        $timestamp = date('Y_m_d_His');

        $backupDir = storage_path('backups');
        if (! is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $sqlFile = "{$backupDir}/aiven_{$dbName}_backup_{$timestamp}.sql";
        $jsonFile = "{$backupDir}/aiven_{$dbName}_backup_{$timestamp}.json";

        $this->info('Starting database backup from Aiven MySQL...');
        $this->line("Host: {$host}");
        $this->line("Database: {$dbName}");

        try {
            $pdo = DB::connection('mysql')->getPdo();
            $tables = DB::connection('mysql')->select('SHOW TABLES');
            $prop = 'Tables_in_'.$dbName;

            $format = (string) $this->option('format');
            $generateSql = in_array($format, ['sql', 'all'], true);
            $generateJson = in_array($format, ['json', 'all'], true);

            $sqlHandle = null;
            if ($generateSql) {
                $sqlHandle = fopen($sqlFile, 'w');
                if (! $sqlHandle) {
                    throw new RuntimeException("Could not open file for writing: {$sqlFile}");
                }

                $header = "-- ========================================================\n"
                    ."-- Full Database Dump from Aiven MySQL\n"
                    ."-- Database: {$dbName} | Host: {$host}\n"
                    .'-- Generated: '.date('Y-m-d H:i:s T')."\n"
                    ."-- ========================================================\n\n"
                    ."SET FOREIGN_KEY_CHECKS=0;\n"
                    ."SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n"
                    ."SET NAMES utf8mb4;\n"
                    ."SET time_zone = \"+00:00\";\n\n";
                fwrite($sqlHandle, $header);
            }

            $fullJsonData = [
                'metadata' => [
                    'database' => $dbName,
                    'host' => $host,
                    'generated_at' => date('c'),
                    'table_count' => count($tables),
                ],
                'tables' => [],
            ];

            $totalRows = 0;

            foreach ($tables as $t) {
                $table = $t->{$prop} ?? array_values((array) $t)[0];
                $this->output->write("Processing `{$table}`... ");

                if ($generateSql && $sqlHandle) {
                    fwrite($sqlHandle, "\n-- --------------------------------------------------------\n");
                    fwrite($sqlHandle, "-- Table structure for table `{$table}`\n");
                    fwrite($sqlHandle, "-- --------------------------------------------------------\n\n");
                    fwrite($sqlHandle, "DROP TABLE IF EXISTS `{$table}`;\n");

                    $createStmt = DB::connection('mysql')->select("SHOW CREATE TABLE `{$table}`");
                    $createSql = $createStmt[0]->{'Create Table'} ?? '';
                    fwrite($sqlHandle, $createSql.";\n\n");
                    fwrite($sqlHandle, "-- Dumping data for table `{$table}`\n");
                }

                $rows = DB::connection('mysql')->table($table)->get();
                $rowCount = count($rows);
                $totalRows += $rowCount;

                if ($generateJson) {
                    $tableData = [];
                    foreach ($rows as $row) {
                        $tableData[] = (array) $row;
                    }
                    $fullJsonData['tables'][$table] = [
                        'count' => $rowCount,
                        'rows' => $tableData,
                    ];
                }

                if ($generateSql && $sqlHandle) {
                    if ($rowCount > 0) {
                        $cols = array_keys((array) $rows[0]);
                        $colList = implode('`, `', $cols);

                        $chunks = array_chunk($rows->toArray(), 50);
                        foreach ($chunks as $chunk) {
                            $valueStrings = [];
                            foreach ($chunk as $row) {
                                $vals = [];
                                foreach ($cols as $col) {
                                    $val = $row->{$col} ?? null;
                                    if (is_null($val)) {
                                        $vals[] = 'NULL';
                                    } elseif (is_numeric($val) && ! is_string($val)) {
                                        $vals[] = $val;
                                    } else {
                                        $vals[] = $pdo->quote((string) $val);
                                    }
                                }
                                $valueStrings[] = '('.implode(', ', $vals).')';
                            }
                            $insertSql = "INSERT INTO `{$table}` (`{$colList}`) VALUES\n".implode(",\n", $valueStrings).";\n";
                            fwrite($sqlHandle, $insertSql);
                        }
                        fwrite($sqlHandle, "\n");
                    } else {
                        fwrite($sqlHandle, "-- (No rows in `{$table}`)\n\n");
                    }
                }

                $this->info("OK ({$rowCount} rows)");
            }

            if ($generateSql && $sqlHandle) {
                fwrite($sqlHandle, "\nSET FOREIGN_KEY_CHECKS=1;\n-- Dump completed on ".date('Y-m-d H:i:s T')."\n");
                fclose($sqlHandle);
                $this->info('SQL backup saved: '.$sqlFile.' ('.number_format(filesize($sqlFile) / 1024, 2).' KB)');
            }

            if ($generateJson) {
                file_put_contents($jsonFile, json_encode($fullJsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                $this->info('JSON backup saved: '.$jsonFile.' ('.number_format(filesize($jsonFile) / 1024, 2).' KB)');
            }

            $this->info("✅ Backup complete! Successfully backed up {$totalRows} rows across ".count($tables).' tables.');

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Backup failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
