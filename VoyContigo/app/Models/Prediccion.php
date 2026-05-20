<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediccion extends Model
{
    use HasFactory;

    protected $table = 'predicciones';

    protected $fillable = [
        'route_id', 'ml_model_id', 'resultado'
    ];

    // Relación (N:1)
    public function ruta() {
        return $this->belongsTo(Ruta::class, 'route_id');
    }
}