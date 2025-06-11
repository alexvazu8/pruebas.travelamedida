<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioTraslado extends Model
{
    use HasFactory;

    // Nombre de la tabla (si es diferente del plural del modelo)
    protected $table = 'servicio_traslados';

    // Definir los campos que pueden ser asignados masivamente
    protected $fillable = [
        'tipo_servicio_transfer',
        'zona_origen_id',
        'zona_destino_id'
    ];

    // Relación con la tabla 'zonas' para 'zona_origen'
    public function zonaOrigen()
    {
        return $this->belongsTo(Zona::class, 'zona_origen_id');
    }

    // Relación con la tabla 'zonas' para 'zona_destino'
    public function zonaDestino()
    {
        return $this->belongsTo(Zona::class, 'zona_destino_id');
    }
}