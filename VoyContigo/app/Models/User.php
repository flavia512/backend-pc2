<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

protected $fillable = [
        'email', 'full_name', 'password_hash', 'puntos', 'last_login_at', 'is_active', 'rol'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // Relaciones (1:N)
    public function rutas() {
        return $this->hasMany(Ruta::class, 'user_id');
    }

    public function viajesComoConductor() {
        return $this->hasMany(ViajeCompartidos::class, 'driver_user_id');
    }

    public function reservas() {
        return $this->hasMany(Reserva::class, 'user_id');
    }

    public function alertas() {
        return $this->hasMany(Alerta::class, 'user_id');
    }

    public function favoritos() {
        return $this->hasMany(Favorito::class, 'user_id');
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

}


