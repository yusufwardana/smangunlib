<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_matches_installation_state(): void
    {
        if (file_exists(storage_path('app/installed'))) {
            // Setelah terinstall, landing page publik tampil langsung di "/".
            $this->get('/')->assertOk();

            return;
        }

        // Belum terinstall: seluruh rute non-installer diarahkan ke installer.
        $this->get('/')->assertRedirect('/install');
    }

    public function test_installer_route_matches_installation_state(): void
    {
        $response = $this->get('/install');

        if (file_exists(storage_path('app/installed'))) {
            $response->assertRedirect('/login');
            return;
        }

        $response->assertOk();
    }
}
