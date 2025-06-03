<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Celular extends Model
{
    use HasFactory;

    protected $table = 'celulares'; 
    protected $fillable = ['ip', 'modelo'];

    public function cuentas()
    {
        return $this->hasMany(Cuenta::class);
    }
}
