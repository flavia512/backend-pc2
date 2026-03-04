<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViajeCompartido extends Model
{
    use HasFactory;

    protected $table = 'viaje_compartido';

    protected $fillable = [
        'driver_user_id', 'route_id', 'station_name', 
        'trip_datename', 'seats_total', 'seats_available', 'status'
    ];

    protected $casts = [
        'trip_datename' => 'datetime'
    ];

    // Relación: El viaje le pertenece a un conductor (Usuario)
    public function conductor() {
        return $this->belongsTo(User::class, 'driver_user_id');
    }

    // Relación: El viaje se basa en una ruta
    public function ruta() {
        return $this->belongsTo(Ruta::class, 'route_id');
    }

    // Relación: Un viaje tiene muchas reservas (1:N)
    public function reservas() {
        return $this->hasMany(Reserva::class, 'trip_id');
    }
}