<?php

namespace Tests\Feature;

use App\Models\ThemeSetting;
use App\Models\User;
use App\Services\ThemeService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature test modul Theme Manager: akses halaman, simpan (AJAX),
 * upload aset, preview, reset, export, import, dan otorisasi peran.
 */
class ThemeManagerTest extends TestCase
{
    use RefreshDatabase;

    private bool $installedFileExisted = false;

    protected function setUp(): void
    {
        parent::setUp();

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

    private function admin(): User
    {
        $user = User::create([
            'name'     => 'Theme Admin',
            'email'    => 'theme.admin@example.test',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('super_admin');

        return $user;
    }

    private function staff(): User
    {
        $user = User::create([
            'name'     => 'Staff Biasa',
            'email'    => 'staff@example.test',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('siswa');


        return $user;
    }

    public function test_admin_can_view_theme_manager_page(): void
    {
        $this->actingAs($this->admin())
            ->get('/system/theme')
            ->assertOk()
            ->assertSee('Theme Manager');
    }

    public function test_admin_can_update_color_group_via_ajax(): void
    {
        $response = $this->actingAs($this->admin())
            ->postJson('/system/theme', [
                'group'    => 'color',
                'settings' => ['primary_color' => '#ff5733'],
            ]);

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure(['css_variables', 'values']);

        $this->assertDatabaseHas('theme_settings', [
            'group' => 'color',
            'key'   => 'primary_color',
            'value' => '#ff5733',
        ]);
    }

    public function test_admin_can_upload_logo(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('logo.png', 200, 200);

        $this->actingAs($this->admin())
            ->post('/system/theme', [
                'group'   => 'logo',
                'uploads' => ['logo_sekolah' => $file],
            ], ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk();


        $path = app(ThemeService::class)->get('logo.logo_sekolah');
        $this->assertNotEmpty($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_upload_rejects_oversized_or_invalid_file(): void
    {
        Storage::fake('public');

        // File melebihi 5 MB.
        $big = UploadedFile::fake()->create('big.png', 6000, 'image/png');

        $this->actingAs($this->admin())
            ->post('/system/theme', [
                'group'   => 'logo',
                'uploads' => ['logo_sekolah' => $big],
            ])
            ->assertSessionHasErrors('uploads.logo_sekolah');
    }

    public function test_preview_returns_css_without_persisting(): void
    {
        $this->actingAs($this->admin())
            ->postJson('/system/theme/preview', [
                'group'    => 'color',
                'settings' => ['primary_color' => '#0d6efd'],
            ])
            ->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonFragment(['success' => true]);

        // Tidak tersimpan ke DB.
        $this->assertDatabaseMissing('theme_settings', [
            'group' => 'color',
            'key'   => 'primary_color',
            'value' => '#0d6efd',
        ]);
    }

    public function test_admin_can_reset_theme(): void
    {
        $service = app(ThemeService::class);
        $service->set('color', 'primary_color', '#000000', 'color');

        $this->actingAs($this->admin())
            ->post('/system/theme/reset')
            ->assertRedirect();

        $this->assertSame('#4361ee', app(ThemeService::class)->get('color.primary_color'));
    }

    public function test_admin_can_export_theme_as_json(): void
    {
        app(ThemeService::class)->set('color', 'primary_color', '#abcdef', 'color');

        $response = $this->actingAs($this->admin())->get('/system/theme/export');

        $response->assertOk()
            ->assertHeader('content-disposition')
            ->assertJsonStructure(['name', 'exported_at', 'settings']);
    }

    public function test_admin_can_import_theme_from_json(): void
    {
        Storage::fake('local');

        $payload = [
            'name'     => 'Imported',
            'settings' => [
                ['group' => 'color', 'key' => 'primary_color', 'value' => '#654321', 'type' => 'color'],
            ],
        ];

        $file = UploadedFile::fake()->createWithContent('theme.json', json_encode($payload));

        $this->actingAs($this->admin())
            ->post('/system/theme/import', ['file' => $file])
            ->assertRedirect();

        $this->assertSame('#654321', app(ThemeService::class)->get('color.primary_color'));
    }

    public function test_non_admin_cannot_access_theme_manager(): void
    {
        $this->actingAs($this->staff())
            ->get('/system/theme')
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/system/theme')->assertRedirect('/login');
    }
}
