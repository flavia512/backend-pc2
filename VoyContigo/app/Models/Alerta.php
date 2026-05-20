<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alerta extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'route_id', 'user_id', 'for_datetime', 'status'
    ];

    protected $casts = [
        'for_datetime' => 'datetime'
    ];

    // Relaciones (N:1)
    public function ruta() {
        return $this->belongsTo(Ruta::class, 'route_id');
    }

    public function usuario() {
        return $this->belongsTo(User::class, 'user_id');
    }
}