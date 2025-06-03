<?php
//Archivo creado por Carlos
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarjeta extends Model
{
    use HasFactory;

    protected $fillable=[
        'numero',
        'tipo',
        'estado',
        'cvc',
        'fecha_vencimiento',
        'cuenta_id',
    ];
    
    public function Cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id');
    }

}
