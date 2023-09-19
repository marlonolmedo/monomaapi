<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCandidatoRequest;
use App\Http\Requests\UpdateCandidatoRequest;
use App\Http\Resources\CandidatoResource;
use App\Models\Candidato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class CandidatoController extends Controller
{

    /**
     * Create a new CandidatoController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        if (!Cache::has('candidatos')) {
            Cache::put('Candidatos', Candidato::all());
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->role == 'agent') {
            $candidatos = Cache::get('candidatos', function () {
                return Candidato::all();
            })->where("owner", $user->id);
        } else {
            $candidatos = Cache::get('candidatos', function () {
                return Candidato::all();
            });
        }
        return response()->json([
            "meta" => [
                "success" => true,
                "errors" => []
            ],
            "data" => CandidatoResource::collection($candidatos)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCandidatoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('create', Candidato::class)) {
            return response()->json([
                "meta" => [
                    "success" => false,
                    "errors" => [
                        'Solo manager pueden crear Candidatos'
                    ]
                ]
            ], 401);
        }
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:30',
            'source' => "nullable",
            'owner' => "required|integer|exists:users,id"
        ], [
            'name.required' => 'name es requerido',
            'name.string' => 'name debe ser cadena de texto',
            'name.max' => 'name no puede superar los 30 caracteres',
            'owner.required' => 'owner es requerido',
            'owner.integer' => 'owner debe ser numero',
            'owner.exists' => 'owner debe ser un usuario existente'
        ]);
        if ($validation->fails()) {
            $mensajes = collect($validation->errors()->messages())->flatten(1);
            return response()->json([
                "meta" => [
                    "success" => false,
                    "errors" => $mensajes
                ]
            ], 401);
        }

        $validado = $validation->validated();

        $candidato = new Candidato();
        $validado['created_by'] = $request->user()->id;
        $candidato->fill($validado);
        $candidato->save();

        Cache::put('candidatos', Candidato::all());

        return response()->json([
            "meta" => [
                "success" => true,
                "errors" => []
            ],
            "data" => new CandidatoResource($candidato)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Candidato  $candidato
     * @return \Illuminate\Http\Response
     */
    public function show(Candidato $candidato)
    {
        $response = Gate::inspect('view', $candidato);
        if ($response->denied()) {
            return response()->json([
                "meta" => [
                    "success" => false,
                    "errors" => [
                        'Not found.'
                    ]
                ]
            ], 401);
        }
        return response()->json([
            "meta" => [
                "success" => true,
                "errors" => []
            ],
            "data" => new CandidatoResource($candidato)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCandidatoRequest  $request
     * @param  \App\Models\Candidato  $candidato
     * @return \Illuminate\Http\Response
     */
    // public function update(UpdateCandidatoRequest $request, Candidato $candidato)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Candidato  $candidato
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Candidato $candidato)
    // {
    //     //
    // }
}
