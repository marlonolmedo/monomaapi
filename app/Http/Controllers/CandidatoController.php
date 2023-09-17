<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCandidatoRequest;
use App\Http\Requests\UpdateCandidatoRequest;
use App\Models\Candidato;
use Illuminate\Http\Request;
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
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            "meta" => [
                "success" => true,
                "errors" => []
            ],
            "data" => Candidato::all()
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
        $validation = Validator::make($request->all(), [
            'name' => 'required|String|max:255',
            // 'source' => "required",
            'owner' => "required|integer|exists:users,id"
        ], [
            'name.required' => 'name es requerido',
            'name.String' => 'name debe ser cadena de texto',
            'name.max' => 'name no puede superar los 255 caracteres',
            'owner.required' => 'owner es requerido',
            'owner.integer' => 'owner debe ser numero',
        ]);
        if ($validation->fails()) {
            $mensajes = collect($validation->errors()->messages())->flatten(1);
            return response()->json([
                "meta" => [
                    "success" => false,
                    "errors" => $mensajes
                ]
            ]);
        }

        $validado = $validation->validated();

        $candidato = new Candidato();
        $validado['created_by'] = $request->user()->id;
        $candidato->fill($validado);
        $candidato->save();

        return response()->json([
            "meta" => [
                "success" => true,
                "errors" => []
            ],
            "data" => $candidato
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
        return response()->json([
            "meta" => [
                "success" => true,
                "errors" => []
            ],
            "data" => $candidato
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCandidatoRequest  $request
     * @param  \App\Models\Candidato  $candidato
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCandidatoRequest $request, Candidato $candidato)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Candidato  $candidato
     * @return \Illuminate\Http\Response
     */
    public function destroy(Candidato $candidato)
    {
        //
    }
}
