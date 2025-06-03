<?php

namespace App\Http\Controllers;

use App\Models\Atm;
use Illuminate\Http\Request;

class AtmController extends Controller
{
    public function index()
    {
        return response()->json(Atm::all(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ciudad' => 'required|string',
            'estado' => 'required|string',
            'fecha_repo' => 'nullable|date',
            'saldo' => 'required|integer',
            'ubicacion' => 'required|string',
        ]);

        $atm = Atm::create($request->all());
        return response()->json($atm, 201);
    }

    public function show($id)
    {
        $atm = Atm::findOrFail($id);
        return response()->json($atm);
    }

    public function update(Request $request, $id)
    {
        $atm = Atm::findOrFail($id);

        $request->validate([
            'ciudad' => 'sometimes|required|string',
            'estado' => 'sometimes|required|string',
            'fecha_repo' => 'nullable|date',
            'saldo' => 'sometimes|required|integer',
            'ubicacion' => 'sometimes|required|string',
        ]);

        $atm->update($request->all());
        return response()->json($atm, 200);
    }

    public function destroy($id)
    {
        Atm::destroy($id);
        return response()->json(null, 204);
    }
}
