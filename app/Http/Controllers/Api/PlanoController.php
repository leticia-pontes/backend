<?php

namespace App\Http\Controllers\Api;

use App\Models\Plano;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlanoController extends Controller
{
    public function index()
    {
        $planos = Plano::all();
        return response()->json($planos);
    }
}
