<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    use HasFactory;

    // Desactivamos updated_at porque tu tabla no la tiene
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'route_id'
    ];

    // Relaciones (N:1)
    public function usuario() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ruta() {
        return $this->belongsTo(Ruta::class, 'route_id');
    }
}