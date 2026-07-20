<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InstallerService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class InstallerController extends Controller
{
    protected $installerService;

    public function __construct(InstallerService $installerService)
    {
        $this->installerService = $installerService;
    }

    public function index()
    {
        return view('installer.welcome');
    }

    public function requirements()
    {
        $requirements = $this->installerService->checkRequirements();
        $allPassed = !in_array(false, $requirements, true);
        return view('installer.requirements', compact('requirements', 'allPassed'));
    }

    public function permissions()
    {
        $permissions = $this->installerService->checkPermissions();
        $allPassed = !in_array(false, $permissions, true);
        return view('installer.permissions', compact('permissions', 'allPassed'));
    }

    public function database()
    {
        return view('installer.database');
    }

    public function databaseTest(Request $request)
    {
        $result = $this->installerService->testDbConnection(
            $request->db_host,
            $request->db_port,
            $request->db_name,
            $request->db_user,
            $request->db_password ?? ''
        );

        if ($result === true) {
            session([
                'db_host' => $request->db_host,
                'db_port' => $request->db_port,
                'db_name' => $request->db_name,
                'db_user' => $request->db_user,
                'db_password' => $request->db_password ?? ''
            ]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => $result]);
    }

    public function appConfig()
    {
        return view('installer.app');
    }

    public function appConfigStore(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string',
            'app_url' => 'required|url',
        ]);

        session([
            'app_name' => $request->app_name,
            'app_url' => $request->app_url,
        ]);

        return redirect()->route('installer.process');
    }

    public function process()
    {
        return view('installer.process');
    }

    // --- AJAX endpoints for Step 6 to 10 ---

    public function processEnv()
    {
        $data = [
            'APP_NAME' => session('app_name', 'SMANGUNLIB'),
            'APP_URL' => session('app_url', url('/')),
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => session('db_host', '127.0.0.1'),
            'DB_PORT' => session('db_port', '3306'),
            'DB_DATABASE' => session('db_name', 'laravel'),
            'DB_USERNAME' => session('db_user', 'root'),
            'DB_PASSWORD' => session('db_password', ''),
            'CACHE_DRIVER' => 'file',
            'SESSION_DRIVER' => 'file',
            'QUEUE_CONNECTION' => 'sync',
            'MAIL_MAILER' => 'smtp',
        ];

        $success = $this->installerService->setEnv($data);
        if ($success) {
            // Need to reload env for next steps in this request cycle if necessary
            return response()->json(['success' => true, 'message' => '.env file generated.']);
        }
        return response()->json(['success' => false, 'message' => 'Gagal menulis .env. Periksa permission.']);
    }

    public function processKey()
    {
        try {
            Artisan::call('key:generate', ['--force' => true]);
            return response()->json(['success' => true, 'message' => 'APP_KEY generated.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function processSymlink()
    {
        $success = $this->installerService->createSymlink();
        if ($success) {
            return response()->json(['success' => true, 'message' => 'Storage symlink created.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Gagal membuat symlink. Buat manual di cPanel.']);
        }
    }

    public function processMigrate()
    {
        try {
            // We force database connection reset in case it still uses old config
            DB::purge('mysql');
            Artisan::call('migrate', ['--force' => true]);
            return response()->json(['success' => true, 'message' => 'Database migrated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function processSeed()
    {
        try {
            Artisan::call('db:seed', ['--force' => true]);
            return response()->json(['success' => true, 'message' => 'Database seeded successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // --- End AJAX ---

    public function admin()
    {
        return view('installer.admin');
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('super_admin');

        // Simpan hanya email, TIDAK menyimpan password di session
        session(['admin_email' => $user->email]);

        return redirect()->route('installer.config');
    }

    public function initialConfig()
    {
        return view('installer.config');
    }

    public function initialConfigStore(Request $request)
    {
        Setting::set('tahun_ajaran', $request->tahun_ajaran);
        Setting::set('semester', $request->semester);
        Setting::set('timezone', $request->timezone);
        Setting::set('locale', $request->locale);
        Setting::set('denda_per_hari', $request->denda_per_hari);
        Setting::set('lama_pinjam_default', $request->lama_pinjam_default);
        Setting::set('maksimal_pinjam', $request->maksimal_pinjam);

        return redirect()->route('installer.finish');
    }

    public function finish()
    {
        // Mark as installed
        File::put(storage_path('app/installed'), date('Y-m-d H:i:s'));
        
        $email = session('admin_email');

        // Clear installer session
        session()->forget(['db_host', 'db_port', 'db_name', 'db_user', 'db_password', 'app_name', 'app_url', 'admin_email']);

        return view('installer.finish', compact('email'));
    }
}
