<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    protected $table = 'cuentas';

    protected $fillable = [
        'numero_cuenta',
        'estado',
        'fecha_de_apertura',
        'saldo',
        'tipo_de_cuenta',
        'moneda',
        'intereses',
        'limite_de_retiro',
        'cliente_id'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    //Cambio de Carlos
    public function tarjetas()
{
    return $this->hasMany(Tarjeta::class);
}
public function celular()
{
    return $this->belongsTo(Celular::class);
}
}

