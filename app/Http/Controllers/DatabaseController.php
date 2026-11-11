<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class DatabaseController extends Controller
{
    /**
     * Get active database status, latency, and available backups.
     */
    public function status(): JsonResponse
    {
        $defaultConn = config('database.default', 'mysql');
        $dbConfig = config("database.connections.{$defaultConn}", []);

        $host = (string) ($dbConfig['host'] ?? '127.0.0.1');
        $port = (string) ($dbConfig['port'] ?? '3306');
        $database = (string) ($dbConfig['database'] ?? '');
        $username = (string) ($dbConfig['username'] ?? '');

        $connected = false;
        $latencyMs = 0;
        $tables = [];
        $error = null;

        try {
            $start = microtime(true);
            $pdo = DB::connection()->getPdo();
            $latencyMs = round((microtime(true) - $start) * 1000, 2);
            $connected = true;

            if ($defaultConn === 'mysql' || $defaultConn === 'mariadb') {
                $rawTables = DB::select('SHOW TABLES');
                $prop = 'Tables_in_'.$database;
                foreach ($rawTables as $t) {
                    $tables[] = $t->{$prop} ?? array_values((array) $t)[0];
                }
            } elseif ($defaultConn === 'pgsql') {
                $rawTables = DB::select("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname != 'pg_catalog' AND schemaname != 'information_schema'");
                foreach ($rawTables as $t) {
                    $tables[] = $t->tablename;
                }
            } elseif ($defaultConn === 'sqlite') {
                $rawTables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                foreach ($rawTables as $t) {
                    $tables[] = $t->name;
                }
            }
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }

        $backupDirs = [
            storage_path('backups'),
            database_path('backups'),
        ];
        $backups = [];
        $seen = [];

        foreach ($backupDirs as $dir) {
            if (is_dir($dir)) {
                $files = scandir($dir);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..' && $file !== '.gitignore' && ! isset($seen[$file])) {
                        $filePath = "{$dir}/{$file}";
                        $seen[$file] = true;
                        $backups[] = [
                            'name' => $file,
                            'size' => round(filesize($filePath) / 1024, 2).' KB',
                            'modified' => date('Y-m-d H:i:s', filemtime($filePath)),
                            'is_sql' => str_ends_with($file, '.sql'),
                            'is_json' => str_ends_with($file, '.json'),
                        ];
                    }
                }
            }
        }
        usort($backups, fn ($a, $b) => strcmp($b['modified'], $a['modified']));

        return response()->json([
            'success' => true,
            'connection' => [
                'driver' => $defaultConn,
                'host' => $host,
                'port' => $port,
                'database' => $database,
                'username' => $username,
                'connected' => $connected,
                'latency_ms' => $latencyMs,
                'tables_count' => count($tables),
                'tables' => $tables,
                'error' => $error,
            ],
            'backups' => $backups,
        ]);
    }

    /**
     * Resolve CA certificate path for secure TLS/SSL MySQL connections.
     */
    protected function resolveCaPath(): ?string
    {
        $candidates = [
            base_path('ca.pem'),
            '/etc/ssl/certs/ca-certificates.crt',
            '/etc/pki/tls/certs/ca-bundle.crt',
        ];

        foreach ($candidates as $cand) {
            if (file_exists($cand)) {
                return $cand;
            }
        }

        return null;
    }

    /**
     * Build PDO options configured with SSL when using MySQL.
     */
    protected function getPdoOptions(string $driver): array
    {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 8,
        ];

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $ca = $this->resolveCaPath();
            if ($ca) {
                $options[PDO::MYSQL_ATTR_SSL_CA] = $ca;
            }
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }

        return $options;
    }

    /**
     * Test a proposed database connection before applying it.
     */
    public function testConnection(Request $request): JsonResponse
    {
        $driver = $request->input('driver', 'mysql');
        $host = trim((string) $request->input('host', ''));
        $port = trim((string) $request->input('port', '3306'));
        $database = trim((string) $request->input('database', ''));
        $username = trim((string) $request->input('username', ''));
        $password = (string) $request->input('password', '');

        if (empty($host) || empty($database)) {
            return response()->json([
                'success' => false,
                'message' => 'Host and Database name are required.',
            ], 422);
        }

        try {
            $start = microtime(true);
            $options = $this->getPdoOptions($driver);

            if ($driver === 'mysql' || $driver === 'mariadb') {
                try {
                    $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
                    $pdo = new PDO($dsn, $username, $password, $options);
                } catch (Throwable $dbErr) {
                    if (str_contains($dbErr->getMessage(), 'Unknown database') || str_contains($dbErr->getMessage(), '1049')) {
                        $rootDsn = "mysql:host={$host};port={$port};charset=utf8mb4";
                        $rootPdo = new PDO($rootDsn, $username, $password, $options);
                        $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                        $pdo = new PDO($dsn, $username, $password, $options);
                    } else {
                        throw $dbErr;
                    }
                }
            } elseif ($driver === 'pgsql') {
                $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
                $pdo = new PDO($dsn, $username, $password, $options);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Driver '{$driver}' is not supported for dynamic testing.",
                ], 422);
            }

            $latencyMs = round((microtime(true) - $start) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => "Successfully connected to {$database} on {$host}:{$port} ({$latencyMs} ms)!",
                'latency_ms' => $latencyMs,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: '.$e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update active database credentials in .env file after verifying connection.
     */
    public function updateConnection(Request $request): JsonResponse
    {
        $driver = (string) $request->input('driver', 'mysql');
        $host = trim((string) $request->input('host', ''));
        $port = trim((string) $request->input('port', '3306'));
        $database = trim((string) $request->input('database', ''));
        $username = trim((string) $request->input('username', ''));
        $password = (string) $request->input('password', '');

        if (empty($host) || empty($database)) {
            return response()->json([
                'success' => false,
                'message' => 'Host and Database name are required.',
            ], 422);
        }

        // 1. Verify connection first to guarantee zero downtime
        try {
            $options = $this->getPdoOptions($driver);

            if ($driver === 'mysql' || $driver === 'mariadb') {
                try {
                    $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
                    new PDO($dsn, $username, $password, $options);
                } catch (Throwable $dbErr) {
                    if (str_contains($dbErr->getMessage(), 'Unknown database') || str_contains($dbErr->getMessage(), '1049')) {
                        $rootDsn = "mysql:host={$host};port={$port};charset=utf8mb4";
                        $rootPdo = new PDO($rootDsn, $username, $password, $options);
                        $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                        new PDO($dsn, $username, $password, $options);
                    } else {
                        throw $dbErr;
                    }
                }
            } elseif ($driver === 'pgsql') {
                $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
                new PDO($dsn, $username, $password, $options);
            }
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Refused to apply: Connection test failed ('.$e->getMessage().'). Please check credentials.',
            ], 400);
        }

        // 2. Persist new connection config to storage/app/db_connection.json
        $storageAppDir = storage_path('app');
        if (! is_dir($storageAppDir)) {
            mkdir($storageAppDir, 0755, true);
        }
        $dbConfigPath = storage_path('app/db_connection.json');
        file_put_contents($dbConfigPath, json_encode([
            'driver' => $driver,
            'host' => $host,
            'port' => $port,
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'updated_at' => date('c'),
        ], JSON_PRETTY_PRINT));

        // 3. Update or create .env file if filesystem permits
        $envPath = base_path('.env');
        $envContent = file_exists($envPath) ? (string) file_get_contents($envPath) : '';
        $replacements = [
            'DB_CONNECTION' => $driver,
            'DB_HOST' => $host,
            'DB_PORT' => $port,
            'DB_DATABASE' => $database,
            'DB_USERNAME' => $username,
            'DB_PASSWORD' => $password,
        ];

        foreach ($replacements as $key => $value) {
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $escapedVal = (str_contains($value, ' ') || str_contains($value, '#')) ? '"'.$value.'"' : $value;
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$escapedVal}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        @file_put_contents($envPath, $envContent);

        // 4. Dynamically reconfigure live application runtime
        $runtimeOptions = $this->getPdoOptions($driver);
        config([
            'database.default' => $driver,
            "database.connections.{$driver}.host" => $host,
            "database.connections.{$driver}.port" => $port,
            "database.connections.{$driver}.database" => $database,
            "database.connections.{$driver}.username" => $username,
            "database.connections.{$driver}.password" => $password,
            "database.connections.{$driver}.options" => $runtimeOptions,
        ]);
        DB::purge($driver);

        // 5. If new database is completely empty, auto-restore bundled snapshot
        try {
            $tableCount = count(DB::select('SHOW TABLES'));
            if ($tableCount === 0) {
                $bundled = database_path('backups/latest_backup.sql');
                if (file_exists($bundled)) {
                    DB::unprepared((string) file_get_contents($bundled));
                }
            }
        } catch (Throwable) {
            // Safe fallback
        }

        // 6. Clear config cache
        try {
            Artisan::call('config:clear');
        } catch (Throwable) {
            // Safe to ignore if config not cached
        }

        // 7. Record audit log
        try {
            AuditLog::record(
                action: 'DATABASE_CONNECTION_UPDATED',
                details: "Database switched to {$driver}://{$username}@{$host}:{$port}/{$database}",
                request: $request
            );
        } catch (Throwable) {
            // Safe if audit log table not yet migrated on fresh DB
        }

        return response()->json([
            'success' => true,
            'message' => "Database connection successfully switched to {$database} on {$host}:{$port}!",
            'active' => [
                'driver' => $driver,
                'host' => $host,
                'port' => $port,
                'database' => $database,
                'username' => $username,
            ],
        ]);
    }

    /**
     * Trigger on-demand backup of active database.
     */
    public function triggerBackup(): JsonResponse
    {
        try {
            Artisan::call('db:backup');

            return response()->json([
                'success' => true,
                'message' => 'Full database backup generated successfully!',
                'output' => Artisan::output(),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restore database from an existing backup file or uploaded file.
     */
    public function restoreBackup(Request $request): JsonResponse
    {
        $filename = (string) $request->input('filename', '');
        $filePath = null;

        // Support direct file upload from admin browser
        if ($request->hasFile('sql_file')) {
            $uploaded = $request->file('sql_file');
            $filePath = $uploaded->getRealPath();
            $filename = $uploaded->getClientOriginalName();
        } elseif (! empty($filename)) {
            $candidates = [
                storage_path('backups/'.basename($filename)),
                database_path('backups/'.basename($filename)),
            ];
            foreach ($candidates as $cand) {
                if (file_exists($cand)) {
                    $filePath = $cand;
                    break;
                }
            }
        } else {
            // Find latest .sql file from all candidate directories
            $candidates = array_merge(
                glob(database_path('backups/*.sql')) ?: [],
                glob(storage_path('backups/*.sql')) ?: []
            );

            if (! empty($candidates)) {
                usort($candidates, fn ($a, $b) => filemtime($b) - filemtime($a));
                $filePath = $candidates[0];
                $filename = basename($filePath);
            }
        }

        if (! $filePath || ! file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'No SQL backup file found on server. Please upload a .sql file or run a backup first.',
            ], 404);
        }

        try {
            $sql = file_get_contents($filePath);
            DB::unprepared($sql);

            try {
                AuditLog::record(
                    action: 'DATABASE_BACKUP_RESTORED',
                    details: "Restored database from {$filename}",
                    request: $request
                );
            } catch (Throwable) {
                // Ignore if audit_logs table was recreated during restore
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully restored database from {$filename}!",
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Restore failed: '.$e->getMessage(),
            ], 500);
        }
    }
}
