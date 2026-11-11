<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Load dynamically switched database configuration if saved by admin
        $dbConfigPath = storage_path('app/db_connection.json');
        if (file_exists($dbConfigPath)) {
            try {
                $savedConfig = json_decode((string) file_get_contents($dbConfigPath), true);
                if (is_array($savedConfig) && ! empty($savedConfig['host']) && ! empty($savedConfig['database'])) {
                    $driver = $savedConfig['driver'] ?? 'mysql';
                    config([
                        'database.default' => $driver,
                        "database.connections.{$driver}.host" => $savedConfig['host'],
                        "database.connections.{$driver}.port" => $savedConfig['port'] ?? ($driver === 'mysql' ? 3306 : 5432),
                        "database.connections.{$driver}.database" => $savedConfig['database'],
                        "database.connections.{$driver}.username" => $savedConfig['username'] ?? '',
                        "database.connections.{$driver}.password" => $savedConfig['password'] ?? '',
                    ]);
                }
            } catch (Throwable) {
                // Fallback gracefully to default environment configuration
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
