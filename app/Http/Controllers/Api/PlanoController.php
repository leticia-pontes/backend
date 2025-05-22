<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models;
use Illuminate\Http\Request;

class PlanoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $planos = Plano::all();
        return response()->json($planos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_plano' => '',
            'nome_plano' => '',
            'valor' => '',
            'descricao' => '',
            'data_criacao' => '',
            'id_empresa' => '',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
