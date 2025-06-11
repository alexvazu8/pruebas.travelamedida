<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    use HasFactory;

    // Nombre de la tabla explícito (opcional si Laravel ya lo infiere correctamente)
    protected $table = 'ciudades';

    // Primary key personalizada
    protected $primaryKey = 'id_ciudad';

    // Si la primary key no es autoincremental, indícalo (por defecto Laravel lo asume)
    public $incrementing = true;

  

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'nombre_ciudad',
        'pais_id_pais',
    ];

    // Relación con el modelo `Pais`
    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id_pais', 'id_pais');
    }
}
