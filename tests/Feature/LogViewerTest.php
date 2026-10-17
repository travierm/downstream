<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Opcodes\LogViewer\Facades\LogViewer;
use Tests\TestCase;

class LogViewerTest extends TestCase
{
    public function test_guests_cannot_access_the_ui_or_any_log_api_endpoint()
    {
        foreach ($this->protectedEndpoints() as [$method, $uri]) {
            $this->json($method, $uri)->assertUnauthorized();
        }

        $this->get('/log-viewer')->assertUnauthorized()->assertHeader('WWW-Authenticate');
    }

    public function test_regular_users_cannot_access_logs_even_in_local_environments()
    {
        $this->app->instance('env', 'local');
        $this->withSession(['_token' => 'log-viewer-test'])
            ->withHeader('X-CSRF-TOKEN', 'log-viewer-test');
        $this->actingAs(User::factory()->create(['type' => 'basic']));

        foreach ($this->protectedEndpoints() as [$method, $uri]) {
            $this->json($method, $uri)->assertForbidden();
        }
    }

    public function test_admin_can_sign_in_with_existing_credentials_and_read_logs_in_production()
    {
        $this->app->instance('env', 'production');
        $admin = User::factory()->create(['type' => 'admin']);
        $filename = 'viewer-test-'.Str::uuid().'.log';
        $message = 'Log viewer integration test '.Str::uuid();
        File::put(storage_path('logs/'.$filename), '['.now()->format('Y-m-d H:i:s').'] production.ERROR: '.$message."\n");

        try {
            $this->withServerVariables(['PHP_AUTH_USER' => $admin->email, 'PHP_AUTH_PW' => 'password'])
                ->get('/log-viewer')->assertOk()->assertSee('Log Viewer');

            $file = LogViewer::getFile($filename);
            $this->assertNotNull($file);
            $this->getJson('/log-viewer/api/files')->assertOk()->assertSee($filename);
            $this->getJson('/log-viewer/api/logs?file='.$file->identifier)
                ->assertOk()->assertSee($message);
        } finally {
            File::delete(storage_path('logs/'.$filename));
        }
    }

    public function test_browser_sign_in_rejects_invalid_passwords_and_non_admin_accounts()
    {
        $user = User::factory()->create(['type' => 'basic']);

        $this->withServerVariables(['PHP_AUTH_USER' => $user->email, 'PHP_AUTH_PW' => 'wrong'])
            ->get('/log-viewer')->assertUnauthorized();
        $this->withServerVariables(['PHP_AUTH_USER' => $user->email, 'PHP_AUTH_PW' => 'password'])
            ->get('/log-viewer')->assertForbidden();
    }

    private function protectedEndpoints(): array
    {
        return [
            ['GET', '/log-viewer'],
            ['GET', '/log-viewer/api/hosts'],
            ['GET', '/log-viewer/api/folders'],
            ['GET', '/log-viewer/api/files'],
            ['GET', '/log-viewer/api/logs'],
            ['GET', '/log-viewer/api/files/missing/download/request'],
            ['GET', '/log-viewer/api/files/missing/download'],
            ['GET', '/log-viewer/api/folders/missing/download/request'],
            ['GET', '/log-viewer/api/folders/missing/download'],
            ['POST', '/log-viewer/api/files/missing/clear-cache'],
            ['POST', '/log-viewer/api/folders/missing/clear-cache'],
            ['POST', '/log-viewer/api/clear-cache-all'],
            ['POST', '/log-viewer/api/delete-multiple-files'],
            ['DELETE', '/log-viewer/api/files/missing'],
            ['DELETE', '/log-viewer/api/folders/missing'],
        ];
    }
}
