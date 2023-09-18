<?php

namespace Tests\Feature;

use App\Models\Candidato;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CandidatoTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_manager_can_fetch_all_data()
    {
        $user = User::factory()->create([
            'role' => 'manager',
            'is_active' => 1
        ]);

        $response = $this->actingAs($user, 'api')->getJson('api/leads');

        $response->assertStatus(200)
            ->assertJsonStructure([
                "meta" => [
                    "success",
                    "errors"
                ],
                "data" => [
                    '*' => [
                        "id",
                        "name",
                        "source",
                        "created_by",
                        "owner",
                        "created_at",
                        "updated_at"
                    ]
                ]
            ]);
    }

    public function test_manager_can_get_any_one_lead()
    {
        $user = User::factory()->create([
            'role' => 'manager',
            'is_active' => 1
        ]);

        $candidatoData = [
            'id' => 200,
            'name' => 'testing 1',
            'source' => 'one candidate',
            'owner' => 3,
            'created_by' => 1,
            'created_at' => '2023-10-05 05:14:18',
            'updated_at' => '2023-10-05 05:14:19'
        ];

        $oneCandidato = Candidato::factory()->create($candidatoData);

        $response = $this->actingAs($user, 'api')->getJson('api/lead/' . $oneCandidato->id);

        $response->assertOk()->assertExactJson([
            "meta" => [
                "success" => true,
                "errors" => []
            ],
            "data" => $candidatoData
        ]);
    }

    public function test_manager_can_create_new_candidato()
    {
        $user = User::factory()->create([
            'role' => 'manager',
            'is_active' => 1
        ]);

        $candidatoData = [
            // 'id' => 201,
            'name' => 'testing 2',
            'source' => 'two candidate',
            'owner' => 2,
            // 'created_by' => 4,
            // 'created_at' => '2023-10-05 05:14:18',
            // 'updated_at' => '2023-10-05 05:14:19'
        ];

        $response = $this->actingAs($user, 'api')
            ->postJson('api/lead', $candidatoData);

        $response->assertCreated()
            ->assertJsonStructure([
                "meta" => [
                    "success",
                    "errors"
                ],
                "data" => [
                    "id",
                    "name",
                    "source",
                    "created_by",
                    "owner",
                    "created_at",
                    "updated_at"
                ]
            ]);
    }

    public function test_agent_can_not_fect_all_data(){
        $user = User::factory()->create([
            'role' => 'agent',
            'is_active' => 1
        ]);

        $candidatos = Candidato::factory(5)->create([
            "owner" => $user->id
        ]);

        //TODO: terminar esta validacion
    }

    public function test_agent_only_fect_own_candidatos(){
        //TODO: terminar tambien este testeo
    }

    public test_agent_can_not_create_new_candidato(){
        //TODO: crear esta validacion
    }
    public function test_validar_camposRequeridos()
    {
        $user = User::find(1);

        $response = $this->actingAs($user, 'api')
            ->postJson('api/lead', []);

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
            ->postJson('api/lead', $candidatoData);

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
            ->postJson('api/lead', $candidatoData);

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
        // $fake = Factory::create();
        $candidatoData = [
            'name' => 'perueba 1',
            'source' => 'test 1',
            'owner' => 'ewew'
        ];
        // dd($candidatoData);
        $response = $this->actingAs($user, 'api')
            ->postJson('api/lead', $candidatoData);

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
            'name' => 'perueba 1',
            'source' => 'test 1',
            'owner' => 205
        ];
        // dd($candidatoData);
        $response = $this->actingAs($user, 'api')
            ->postJson('api/lead', $candidatoData);

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
