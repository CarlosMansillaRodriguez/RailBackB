<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atm extends Model
{
    protected $fillable = [
        'ciudad',
        'estado',
        'fecha_repo',
        'saldo',
        'ubicacion',
    ];
}
