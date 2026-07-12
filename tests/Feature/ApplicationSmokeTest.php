<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApplicationSmokeTest extends TestCase
{
    public function test_home_redirect_matches_installation_state(): void
    {
        $expected = file_exists(storage_path('app/installed')) ? '/landing' : '/install';

        $this->get('/')->assertRedirect($expected);
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
