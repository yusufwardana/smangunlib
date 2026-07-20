<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    private bool $installedFileExisted = false;

    protected function setUp(): void
    {
        parent::setUp();

        // Landing page terlindungi middleware CheckIfInstalled; pastikan file
        // penanda "installed" tersedia agar rute publik dapat diakses saat test.
        $this->installedFileExisted = file_exists(storage_path('app/installed'));

        if (! $this->installedFileExisted) {
            if (! is_dir(storage_path('app'))) {
                mkdir(storage_path('app'), 0777, true);
            }

            file_put_contents(storage_path('app/installed'), now()->toDateTimeString());
        }

        $this->seed(RolePermissionSeeder::class);
    }

    protected function tearDown(): void
    {
        if (! $this->installedFileExisted && file_exists(storage_path('app/installed'))) {
            unlink(storage_path('app/installed'));
        }

        parent::tearDown();
    }

    private function createUser(string $role): User
    {
        $user = User::create([
            'name' => 'User '.$role,
            'email' => $role.'@example.test',
            'password' => Hash::make('password'),
        ]);

        $user->assignRole($role);

        return $user;
    }

    public function test_guest_can_open_landing_page(): void
    {
        $this->get('/')->assertOk();
        $this->get('/home')->assertOk();
        $this->get('/beranda')->assertOk();
    }

    public function test_authenticated_user_can_open_landing_page(): void
    {
        $user = $this->createUser('siswa');

        $this->actingAs($user)->get('/')->assertOk();
    }

    public function test_guest_sees_login_button_and_no_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('login'));
        $response->assertDontSee('>Dashboard<', false);
    }

    public function test_authenticated_user_sees_dashboard_button(): void
    {
        $user = $this->createUser('siswa');

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
        $response->assertSee(route('dashboard'));
        $response->assertSee($user->name);
    }

    public function test_authenticated_user_is_not_forced_to_dashboard_on_landing(): void
    {
        $user = $this->createUser('guru');

        // Membuka landing page tidak boleh me-redirect ke dashboard.
        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertViewIs('landing');
    }

    public function test_dashboard_requires_authentication(): void
    {
        // Guest diarahkan ke login.
        $this->get('/dashboard')->assertRedirect('/login');

        // User login dapat membuka dashboard.
        $user = $this->createUser('super_admin');
        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_login_redirects_to_dashboard_by_default(): void
    {
        $this->createUser('siswa');

        $response = $this->post('/login', [
            'email' => 'siswa@example.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_login_redirects_to_intended_protected_page(): void
    {
        $this->createUser('super_admin');

        // Akses halaman terlindungi terlebih dahulu -> tersimpan sebagai intended.
        $this->get('/system/info')->assertRedirect('/login');

        $response = $this->post('/login', [
            'email' => 'super_admin@example.test',
            'password' => 'password',
        ]);

        $response->assertRedirect('/system/info');
    }
}
