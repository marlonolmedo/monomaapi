<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory;

class CandidatoValidationTest extends TestCase
{
    private $url = 'api/lead';
    public function test_validar_camposRequeridos()
    {
        $user = User::find(1);

        $response = $this->actingAs($user, 'api')
            ->postJson($this->url, []);

        $response->assertUnauthorized()
            ->assertExactJson([
                "meta" => [
                    "success" => false,
                    "errors" => [
                        "name es requerido",
                        "owner es requerido"
                    ]
                ]
            ]);
    }

    public function test_name_should_be_string()
    {
        $user = User::find(1);

        $candidatoData = [
            'name' => 766767,
            'source' => 'two candidate',
            'owner' => 2
        ];

        $response = $this->actingAs($user, 'api')
            ->postJson($this->url, $candidatoData);

        $response->assertUnauthorized()
            ->assertExactJson([
                "meta" => [
                    "success" => false,
                    "errors" => [
                        "name debe ser cadena de texto"
                    ]
                ]
            ]);
    }

    public function test_name_should_be_les_than_30_character()
    {
        $user = User::find(1);
        $fake = Factory::create();
        $candidatoData = [
            'name' => $fake->text(200),
            'source' => 'test 1',
            'owner' => 2
        ];
        $response = $this->actingAs($user, 'api')
            ->postJson($this->url, $candidatoData);

        $response->assertUnauthorized()
            ->assertExactJson([
                "meta" => [
                    "success" => false,
                    "errors" => [
                        // "name debe ser cadena de texto",
                        "name no puede superar los 30 caracteres"
                    ]
                ]
            ]);
    }

    public function test_owner_should_be_integer()
    {
        $user = User::find(1);
        $candidatoData = [
            'name' => 'perueba 2',
            'source' => 'test 2',
            'owner' => 'ewew'
        ];
        $response = $this->actingAs($user, 'api')
            ->postJson($this->url, $candidatoData);

        $response->assertUnauthorized()
            ->assertExactJson([
                "meta" => [
                    "success" => false,
                    "errors" => [
                        "owner debe ser numero"
                    ]
                ]
            ]);
    }

    public function test_owner_should_be_existen_user()
    {
        $user = User::find(1);
        $candidatoData = [
            'name' => 'perueba 3',
            'source' => 'test 3',
            'owner' => 205
        ];
        $response = $this->actingAs($user, 'api')
            ->postJson($this->url, $candidatoData);

        $response->assertUnauthorized()
            ->assertExactJson([
                "meta" => [
                    "success" => false,
                    "errors" => [
                        "owner debe ser un usuario existente"
                    ]
                ]
            ]);
    }
}
