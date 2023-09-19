<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\CandidatoSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    protected $seeder = DatabaseSeeder::class;
    private $baseUrl = 'api/auth';
    public function test_seeder_all_data()
    {
        $this->assertDatabaseCount('users', 15); //se agregaron 5 mas desactivados
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_failed_login()
    {
        $roles = Role::all()->map(function ($item) {
            return ['role_id' => $item->id];
        })->toArray();
        $user = User::factory()->sequence(
            ...$roles
        )->create();
        $response = $this->postJson($this->baseUrl, [
            "email" => $user->email,
            "password" => "sdjhhwdbhs"
        ]);

        $response->assertStatus(401);
        $response->assertUnauthorized();
        $response->assertExactJson([
            "meta" => [
                "success" => false,
                "errors" => [
                    "Password incorrect for: " . $user->email
                ]
            ]
        ]);
    }

    public function test_user_can_login_successfully()
    {
        $roles = Role::all()->map(function ($item) {
            return ['role_id' => $item->id];
        })->toArray();
        $user = User::factory()->sequence(
            ...$roles
        )->create();

        $response = $this->postJson($this->baseUrl, [
            "email" => $user->email,
            "password" => "password"
        ]);

        $response->assertStatus(200);
        $response->assertExactJson([
            "meta" => [
                "success" => true,
                "errors" => []
            ],
            "data" => [
                "token" => $response["data"]["token"],
                "minutes_to_expire" => $response['data']["minutes_to_expire"]
            ]
        ]);
    }

    public function test_login_validation_email_format()
    {
        $response = $this->postJson($this->baseUrl, [
            "email" => "sdjhzyxcghuw",
            "password" => "sdjhhwdbhs"
        ]);

        $response->assertUnauthorized();
        $response->assertExactJson([
            "meta" => [
                "success" => false,
                "errors" => ["El campor email debe ser un correo valido"]
            ]
        ]);
    }

    public function test_login_validation_required_email_and_password()
    {
        $response = $this->post($this->baseUrl, [
            "email" => "",
            "password" => ""
        ]);

        $response->assertUnauthorized();
        $response->assertExactJson([
            "meta" => [
                "success" => false,
                "errors" => ["correo es campo requerido", "password es requerida"]
            ]
        ]);
    }

    public function test_not_login_user_inactivated()
    {
        $roles = Role::all()->map(function ($item) {
            return ['role_id' => $item->id];
        })->toArray();
        $user = User::factory()->sequence(
            ...$roles
        )->create([
            "is_active" => 0
        ]);

        $response = $this->postJson($this->baseUrl, [
            "email" => $user->email,
            "password" => "password"
        ]);

        $response->assertUnauthorized()
            ->assertExactJson([
                "meta" => [
                    "success" => false,
                    "errors" => [
                        "usuario desactivado."
                    ]
                ]
            ]);
    }
}
