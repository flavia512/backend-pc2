<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ruta extends Model {

use HasFactory, SoftDeletes;
    protected $table = 'rutas';
    protected $fillable = [
        'user_id', 'nombre', 'origin_text', 'origin_lat', 'origin_lng',
        'dest_text', 'dest_lat', 'dest_lng', 'arrival_time', 'duration_min',
        'hora_salida', 'pasa_por_m30'
    ];

    // Relación (N:1) - Una ruta pertenece a un usuario
    public function usuario() {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relaciones (1:N) - Lo que depende de esta ruta
    public function viajes() {
        return $this->hasMany(ViajeCompartidos::class, 'route_id');
    }

    public function alertas() {
        return $this->hasMany(Alerta::class, 'route_id');
    }

    public function predicciones() {
        return $this->hasMany(Prediccion::class, 'route_id');
    }

    public function favoritos() {
        return $this->hasMany(Favorito::class, 'route_id');
    }
}
