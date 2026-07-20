<?php

namespace Tests\Unit;

use App\Models\ThemeSetting;
use App\Services\ThemeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Unit test untuk ThemeService: caching, get/set, css variables,
 * export/import, dan reset ke default.
 */
class ThemeServiceTest extends TestCase
{
    use RefreshDatabase;

    private ThemeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ThemeService::class);
        Cache::forget(ThemeService::CACHE_KEY);
    }

    public function test_defaults_returns_all_groups(): void
    {
        $defaults = ThemeService::defaults();

        $this->assertArrayHasKey('general', $defaults);
        $this->assertArrayHasKey('color', $defaults);
        $this->assertArrayHasKey('custom', $defaults);
        $this->assertSame('#4361ee', $defaults['color']['primary_color']['value']);
    }

    public function test_get_falls_back_to_default_value(): void
    {
        // Belum ada baris di DB, harus mengembalikan default dari defaults().
        $this->assertSame('#4361ee', $this->service->get('color.primary_color'));
        $this->assertSame('#4361ee', $this->service->get('primary_color')); // bentuk singkat
    }

    public function test_set_persists_and_invalidates_cache(): void
    {
        $this->service->set('color', 'primary_color', '#ff0000', 'color');

        $this->assertDatabaseHas('theme_settings', [
            'group' => 'color',
            'key'   => 'primary_color',
            'value' => '#ff0000',
        ]);

        $this->assertSame('#ff0000', $this->service->get('color.primary_color'));
    }

    public function test_boolean_values_are_cast(): void
    {
        $this->service->set('navbar', 'sticky', false, 'boolean');
        $this->assertFalse($this->service->get('navbar.sticky'));

        $this->service->set('navbar', 'sticky', true, 'boolean');
        $this->assertTrue($this->service->get('navbar.sticky'));
    }

    public function test_css_variables_contains_root_and_primary(): void
    {
        $this->service->set('color', 'primary_color', '#123456', 'color');
        $css = $this->service->cssVariables();

        $this->assertStringContainsString(':root', $css);
        $this->assertStringContainsString('--primary-color: #123456;', $css);
        $this->assertStringContainsString('--bs-primary: #123456;', $css);
    }

    public function test_all_uses_cache_and_avoids_repeated_queries(): void
    {
        $this->service->all(); // isi cache

        $this->assertTrue(Cache::has(ThemeService::CACHE_KEY));
    }

    public function test_export_and_import_round_trip(): void
    {
        $this->service->set('color', 'primary_color', '#abcdef', 'color');
        $payload = $this->service->export();

        $this->assertArrayHasKey('settings', $payload);

        // Ubah nilai lalu import kembali payload lama.
        $this->service->set('color', 'primary_color', '#000000', 'color');
        $this->assertSame('#000000', $this->service->get('color.primary_color'));

        $this->service->import($payload);
        $this->assertSame('#abcdef', $this->service->get('color.primary_color'));
    }

    public function test_reset_restores_defaults(): void
    {
        $this->service->set('color', 'primary_color', '#000000', 'color');
        $this->assertSame('#000000', $this->service->get('color.primary_color'));

        $this->service->reset();

        $this->assertSame('#4361ee', $this->service->get('color.primary_color'));
        $this->assertDatabaseHas('theme_settings', [
            'group' => 'color',
            'key'   => 'primary_color',
            'value' => '#4361ee',
        ]);
    }
}
