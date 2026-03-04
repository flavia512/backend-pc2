<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';
    protected $primaryKey = 'reserva_id'; // Llave primaria personalizada

    protected $fillable = [
        'user_id', 'trip_id', 'seats', 'status'
    ];

    // Relaciones (N:1)
    public function usuario() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function viaje() {
        return $this->belongsTo(ViajeCompartido::class, 'trip_id');
    }
}