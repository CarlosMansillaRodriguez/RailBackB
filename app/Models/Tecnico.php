<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tecnico extends Model
{
    protected $fillable = ['nombre_empresa', 'telefono', 'usuario_id'];

    public function atms()
    {
        return $this->belongsToMany(Atm::class, 'tecno_atms');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
