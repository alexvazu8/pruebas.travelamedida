<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'zonas';

    // Primary key personalizada
    protected $primaryKey = 'Id_Zona';

    // Indicar que la tabla usa timestamps
    public $timestamps = true;

    // Campos asignables masivamente
    protected $fillable = [
        'nombre_zona',
        'ciudad_id_ciudad',
    ];

    // Relación con el modelo Ciudad
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id_ciudad', 'id_ciudad');
    }
}