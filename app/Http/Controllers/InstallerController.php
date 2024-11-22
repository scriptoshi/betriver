<?php

namespace App\Http\Controllers;


use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Validation\ValidationException;

class InstallerController extends Controller
{
    public function welcome()
    {
        return Inertia::render('Installer/Welcome');
    }

    public function requirements()
    {
        $requirements = [
            'PHP Version >= 8.0' => PHP_VERSION_ID >= 80000,
            'PDO PHP Extension' => extension_loaded('pdo'),
            'Mbstring PHP Extension' => extension_loaded('mbstring'),
            'Tokenizer PHP Extension' => extension_loaded('tokenizer'),
        ];

        return Inertia::render('Installer/Requirements', [
            'requirements' => $requirements
        ]);
    }

    public function permissions()
    {
        $permissions = [
            'storage/framework/' => is_writable(storage_path('framework')),
            'storage/logs/' => is_writable(storage_path('logs')),
            'bootstrap/cache/' => is_writable(base_path('bootstrap/cache')),
        ];

        return Inertia::render('Installer/Permissions', [
            'permissions' => $permissions
        ]);
    }

    public function databaseForm()
    {
        return Inertia::render('Installer/Database');
    }


    public function saveDatabase(Request $request)
    {
        $validated = $request->validate([
            'connection' => 'required|in:mysql,pgsql,sqlite',
            'host' => 'required_unless:connection,sqlite',
            'port' => 'required_unless:connection,sqlite',
            'database' => 'required',
            'username' => 'required_unless:connection,sqlite',
            'password' => 'nullable',
        ]);
        $this->updateEnvironmentFile($validated);
        return redirect()->route('installer.migration');
    }


    private function updateEnvironmentFile(array $data): void
    {
        $envPath = base_path('.env');
        $currentValues = [
            'DB_CONNECTION' => config('database.default'),
            'DB_HOST' => config('database.connections.' . config('database.default') . '.host'),
            'DB_PORT' => config('database.connections.' . config('database.default') . '.port'),
            'DB_DATABASE' => config('database.connections.' . config('database.default') . '.database'),
            'DB_USERNAME' => config('database.connections.' . config('database.default') . '.username'),
            'DB_PASSWORD' => config('database.connections.' . config('database.default') . '.password'),
        ];

        $newValues = [
            'DB_CONNECTION' => $data['connection'],
            'DB_HOST' => $data['connection'] === 'sqlite' ? '' : $data['host'],
            'DB_PORT' => $data['connection'] === 'sqlite' ? '' : $data['port'],
            'DB_DATABASE' => $data['database'],
            'DB_USERNAME' => $data['connection'] === 'sqlite' ? '' : $data['username'],
            'DB_PASSWORD' => $data['connection'] === 'sqlite' ? '' : ($data['password'] ?? ''),
        ];

        // Check if any values have changed
        $hasChanges = false;
        foreach ($currentValues as $key => $value) {
            if ((string)$value !== (string)$newValues[$key]) {
                $hasChanges = true;
                break;
            }
        }

        if (!$hasChanges) {
            return;
        }

        // Read .env file
        $envContent = file_get_contents($envPath);

        // Update each configuration value
        foreach ($newValues as $key => $value) {
            $envContent = $this->updateEnvVariable($envContent, $key, $value);
        }

        // Write the updated content back to .env file
        file_put_contents($envPath, $envContent);

        // Clear config cache after updating
        Artisan::call('config:clear');
    }

    private function updateEnvVariable(string $envContent, string $key, string $value): string
    {
        $value = $this->formatEnvValue($value);

        // Check if key exists
        $keyPosition = strpos($envContent, "{$key}=");

        // If key exists, replace its value
        if ($keyPosition !== false) {
            $endOfLinePosition = strpos($envContent, "\n", $keyPosition);
            $oldLine = substr($envContent, $keyPosition, $endOfLinePosition !== false ? $endOfLinePosition - $keyPosition : strlen($envContent));
            $newLine = "{$key}={$value}";
            return str_replace($oldLine, $newLine, $envContent);
        }

        // If key doesn't exist, add it at the end
        return $envContent . "\n{$key}={$value}";
    }

    private function formatEnvValue(string $value): string
    {
        // Check if value contains spaces or special characters
        if (str_contains($value, ' ') || preg_match('/[^A-Za-z0-9_.-]/', $value)) {
            // Escape quotes within the value
            $value = str_replace('"', '\"', $value);
            // Wrap in quotes
            $value = "\"{$value}\"";
        }

        return $value;
    }

    public function migration()
    {
        return Inertia::render('Installer/Migration');
    }

    public function migrate()
    {
        Artisan::call('config:clear');
        try {
            DB::connection()->getPDO();
        } catch (\Exception $e) {
            return redirect()->route('installer.database')->with('error', 'Invalid DB creds');
        }
        try {
            // Disable foreign key checks before migrations
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Run migrations
            Artisan::call('migrate', ['--force' => true]);
            $migrationOutput = Artisan::output();

            // Run seeders
            Artisan::call('db:seed', ['--force' => true]);
            $seederOutput = Artisan::output();

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Check for any error messages in the output
            if (Str::contains($migrationOutput, 'error') || Str::contains($seederOutput, 'error')) {
                throw new \Exception("Error during migration or seeding process.");
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Database migration and seeding completed successfully.'
            ]);
        } catch (\Exception $e) {
            report($e); // Log the error
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during the migration process.',
                'error' => $e->getMessage(),
                'migration_output' => $migrationOutput ?? null,
                'seeder_output' => $seederOutput ?? null
            ], 500);
        }
    }

    public function finish()
    {
        // Mark the application as installed
        file_put_contents(storage_path('installed'), 'Installation completed on ' . date('Y-m-d H:i:s'));
        return  redirect('/');
    }
}
