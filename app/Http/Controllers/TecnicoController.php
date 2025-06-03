<?php
 namespace App\Http\Controllers;

 use App\Models\Tecnico;
 use Illuminate\Http\Request;

class TecnicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Tecnico::with('atms')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_empresa' => 'required|string',
            'telefono' => 'required|string',
            'usuario_id' => 'required|exists:usuarios,id',
        ]);
    
        return Tecnico::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    $tecnico = Tecnico::with('usuario')->find($id);

    if (!$tecnico) {
        return response()->json(['mensaje' => 'Técnico no encontrado'], 404);
    }

    return response()->json($tecnico);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tecnico = Tecnico::findOrFail($id);
        $tecnico->update($request->all());
        return $tecnico;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Tecnico::destroy($id);
    return response()->json(null, 204);
    }
}
