<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    use HasFactory;

    // Especificar el nombre de la tabla explícitamente
    protected $table = 'paises';

    // Primary key personalizada
    protected $primaryKey = 'Id_Pais';




    // Indicar que esta tabla usa timestamps (ya que existen las columnas created_at y updated_at)
    public $timestamps = true;

    // Campos asignables masivamente
    protected $fillable = [
        'Nombre_Pais',
    ];

    // Relación con el modelo `Ciudad`
    public function ciudades()
    {
        return $this->hasMany(Ciudad::class, 'pais_id_pais', 'Id_Pais');
    }
}