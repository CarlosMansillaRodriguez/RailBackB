<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Heredero extends Model
{
    protected $fillable=[
        'nombre_completo',
        'ci',
        'telefono',
        'parentesco',
        'fecha_registro',
        'monto',
        'cuenta_id',
    ];

    public function Cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id');
    }

}
