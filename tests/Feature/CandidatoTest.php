<?php

namespace Tests\Feature;

use App\Http\Resources\CandidatoResource;
use App\Models\Candidato;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CandidatoTest extends TestCase
{
    private $url = 'api/lead/';
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_manager_can_fetch_all_data()
    {
        $role = Role::where('name', 'manager')->first();
        $user = User::factory()->create([
            'role_id' => $role->id,
            'is_active' => 1
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson('api/leads');

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
        $role = Role::where('name', 'manager')->first();
        $user = User::factory()->create([
            'role_id' => $role->id,
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
        $response = $this->actingAs($user, 'api')->getJson($this->url . $oneCandidato->id);

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
        $role = Role::where('name', 'manager')->first();
        $user = User::factory()->create([
            'role_id' => $role->id,
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

    public function test_agent_can_not_fect_all_data()
    {
        $role = Role::where('name', 'agent')->first();
        $user = User::factory()->create([
            'role_id' => $role->id,
            'is_active' => 1
        ]);
        Candidato::factory(5)->create([
            "owner" => $user->id,
            "created_by" => $user->id
        ]);
        $response = $this->actingAs($user, 'api')
            ->getJson('api/leads');

        $response->assertOk()
            ->assertJsonCount(5, 'data');
    }

    public function test_agent_only_fect_own_candidatos()
    {
        $role = Role::where('name', 'agent')->first();
        $user = User::factory()->create([
            'role_id' => $role->id,
            'is_active' => 1
        ]);
        $candidato = Candidato::factory()->create([
            "owner" => $user->id,
            "created_by" => $user->id
        ]);
        $response = $this->actingAs($user, 'api')
            ->getJson($this->url . $candidato->id);
        $response->assertOk()
            ->assertExactJson([
                "meta" => [
                    "success" => true,
                    "errors" => []
                ],
                "data" => [
                    'id' => $candidato->id,
                    'name' => $candidato->name,
                    'source' => $candidato->source,
                    'created_by' => $candidato->created_by,
                    'owner' => $candidato->owner,
                    "created_at" => Carbon::parse($candidato->created_at)->format('Y-m-d H:i:s'),
                    "updated_at" => Carbon::parse($candidato->updated_at)->format('Y-m-d H:i:s')
                ]
            ]);
    }

    public function test_agent_not_fetch_other_oner_candidatos()
    {
        $role = Role::where('name', 'agent')->first();
        $user = User::factory()->create([
            'role_id' => $role->id,
            'is_active' => 1
        ]);
        $candidato = Candidato::factory()->create([
            "owner" => 2,
            "created_by" => 5
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson($this->url  . $candidato->id);

        $response->assertUnauthorized()
            ->assertExactJson([
                "meta" => [
                    "success" => false,
                    "errors" => ['Not found.']
                ]
            ]);
    }

    public function test_agent_can_not_create_new_candidatos()
    {
        $role = Role::where('name', 'agent')->first();
        $user = User::factory()->create([
            'role_id' => $role->id,
            'is_active' => 1
        ]);

        $candidatoData = [
            'name' => 'testing 3',
            'source' => 'three candidate',
            'owner' => 2,
        ];

        $response = $this->actingAs($user, 'api')
            ->postJson('api/lead', $candidatoData);

        $response->assertUnauthorized()
            ->assertExactJson([
                "meta" => [
                    "success" => false,
                    "errors" => [
                        'Solo manager pueden crear Candidatos'
                    ]
                ]
            ]);
    }
}
