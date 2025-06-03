<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReposicionDetalle extends Model
{
    use HasFactory;

    protected $fillable = ['cantidad', 'denominacion', 'subtotal', 'reposicion_id'];

    public function reposicion()
    {
        return $this->belongsTo(Reposicion::class);
    }
}
