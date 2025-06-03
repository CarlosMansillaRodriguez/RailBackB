<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaccion extends Model
{
   use HasFactory;

    protected $table = 'transacciones';
    protected $primaryKey = 'id';

    /**
     * Atributos que pueden ser asignados en masa.
     */
    protected $fillable = [
        'codigo_transaccion',
        'id_cuenta_origen',
        'id_cuenta_destino',
        'monto',
        'tipo_transaccion',
        'estado_transaccion', // Nuevo campo agregado
        'fecha_transaccion',
        'descripcion',
    ];

    /**
     * Relación con la cuenta de origen.
     */
    public function cuentaOrigen()
    {
        return $this->belongsTo(Cuenta::class, 'id_cuenta_origen', 'id');
    }

    /**
     * Relación con la cuenta de destino.
     */
    public function cuentaDestino()
    {
        return $this->belongsTo(Cuenta::class, 'id_cuenta_destino', 'id');
    }

    /**
     * Relación con notificaciones (opcional).
     */
    public function notificaciones()
    {
        return $this->hasMany(Notificacione::class, 'id_transaccion', 'id');
    }

    /**
     * Lista de estados permitidos para una transacción.
     */
    public static function estadosValidos()
    {
        return ['pendiente', 'completada', 'rechazada', 'anulada'];
    }

    /**
     * Determina si la transacción está completada.
     */
    public function estaCompletada()
    {
        return $this->estado_transaccion === 'completada';
    }

    /**
     * Determina si la transacción está pendiente.
     */
    public function estaPendiente()
    {
        return $this->estado_transaccion === 'pendiente';
    }
/*     protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaccion) {
            $transaccion->codigo_transaccion = 'TX-' . now()->format('Ymd') . '-' . Str::random(6);
        });
    } */ 

}
