<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reposicion extends Model
{
    use HasFactory;

    protected $fillable = ['fecha', 'monto_repuesto', 'atm_id'];

    public function atm()
    {
        return $this->belongsTo(Atm::class);
    }

    public function detalles()
    {
        return $this->hasMany(ReposicionDetalle::class);
    }
}
