<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruta extends Model {

use HasFactory;
    protected $table = 'rutas';
    protected $fillable = [
        'user_id', 'origin_text', 'origin_lat', 'origin_lng', 
        'dest_text', 'dest_lat', 'dest_lng', 'arrival_time', 'duration_min'
    ];

    // Relación (N:1) - Una ruta pertenece a un usuario
    public function usuario() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    // Relaciones (1:N) - Lo que depende de esta ruta
    public function viajes() {
        return $this->hasMany(ViajeCompartido::class, 'route_id');
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