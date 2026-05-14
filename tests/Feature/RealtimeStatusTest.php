<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RealtimeStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_can_check_realtime_status_when_reverb_is_unreachable(): void
    {
        $admin = User::factory()->create();

        config([
            'broadcasting.default' => 'reverb',
            'broadcasting.connections.reverb.key' => 'test-key',
            'broadcasting.connections.reverb.secret' => 'test-secret',
            'broadcasting.connections.reverb.app_id' => 'test-app',
            'broadcasting.connections.reverb.options' => [
                'host' => '127.0.0.1',
                'port' => 65530,
                'scheme' => 'http',
                'useTLS' => false,
            ],
        ]);

        $this->actingAs($admin)
            ->getJson(route('system.realtime.status'))
            ->assertOk()
            ->assertJson([
                'enabled' => true,
                'configured' => true,
                'reachable' => false,
                'healthy' => false,
                'driver' => 'reverb',
                'host' => '127.0.0.1',
                'port' => 65530,
            ]);
    }

    public function test_admins_can_see_when_realtime_is_disabled_in_configuration(): void
    {
        $admin = User::factory()->create();

        config([
            'broadcasting.default' => 'log',
        ]);

        $this->actingAs($admin)
            ->getJson(route('system.realtime.status'))
            ->assertOk()
            ->assertJson([
                'enabled' => false,
                'configured' => false,
                'reachable' => false,
                'healthy' => false,
                'driver' => 'log',
            ]);
    }
}
