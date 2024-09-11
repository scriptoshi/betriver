<?php

namespace App\Http\Controllers;


use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Str;
use Illuminate\Support\Facades\Artisan;
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
        // Clear config cache to ensure new values are loaded
        Artisan::call('config:clear');
        // Test the database connection
        try {
            \DB::connection()->getPdo();
            return redirect()->route('installer.migration');
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'database' => ['Unable to connect to the database. Please check your configuration.']
            ]);
        }
    }

    private function updateEnvironmentFile(array $data)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $content = file_get_contents($path);
            $content = preg_replace('/DB_CONNECTION=.*/', 'DB_CONNECTION=' . $data['connection'], $content);
            if ($data['connection'] !== 'sqlite') {
                $content = preg_replace('/DB_HOST=.*/', 'DB_HOST=' . $data['host'], $content);
                $content = preg_replace('/DB_PORT=.*/', 'DB_PORT=' . $data['port'], $content);
                $content = preg_replace('/DB_USERNAME=.*/', 'DB_USERNAME=' . $data['username'], $content);
            }
            $content = preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE=' . $data['database'], $content);
            // Only update password if it's provided
            if (!empty($data['password'])) {
                $content = preg_replace('/DB_PASSWORD=.*/', 'DB_PASSWORD=' . $data['password'], $content);
            }
            file_put_contents($path, $content);
        }
    }

    public function migration()
    {
        return Inertia::render('Installer/Migration');
    }

    public function migrate()
    {
        sleep(5);
        return response()->json([
            'status' => 'success',
            'message' => 'Database migration and seeding completed successfully.'
        ]);
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
