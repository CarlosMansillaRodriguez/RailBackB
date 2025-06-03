<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Reporte extends Model
{
    use HasFactory;

    protected $table = 'reportes';

    protected $fillable = [
        'tipo',
        'formato_de1_exportacion',
        'fecha_de_inicio',
        'fecha_de_final',
        'filtro',
        'usuario_id',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class); 
    }
}
