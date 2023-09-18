<?php

namespace Tests\Feature;

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
    public function test_seeder_all_data()
    {
        // $this->seed();
        $this->assertDatabaseCount('users', 10);
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_failed_login()
    {
        // $user  = User::find(1);
        $user = User::factory()->sequence(
            ['role' => 'manager'],
            ['role' => 'agent'],
        )->sequence(
            ['is_active' => 1],
            ['is_active' => 0],
        )->create();
        $response = $this->postJson('api/auth', [
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
        $user = User::factory()->sequence(
            ['role' => 'manager'],
            ['role' => 'agent'],
        )->sequence(
            ['is_active' => 1],
            ['is_active' => 0],
        )->create();

        $response = $this->postJson('api/auth', [
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
        $response = $this->postJson("api/auth", [
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
        $response = $this->post("api/auth", [
            "email" => "",
            "password" => ""
        ]);

        $response->assertUnauthorized();
        $response->assertExactJson([
            "meta" => [
                "success" => false,
                "errors" => ["correo es campo requerido","password es requerida"]
            ]
        ]);
    }
}
