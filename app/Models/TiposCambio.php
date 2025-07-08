<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TiposCambio
 *
 * @property $id
 * @property $moneda_origen
 * @property $moneda_destino
 * @property $tasa_cambio
 * @property $fecha_validez
 * @property $created_at
 * @property $updated_at
 *
 * @property Pago[] $pagos
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class TiposCambio extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['moneda_origen', 'moneda_destino', 'tasa_cambio', 'fecha_validez'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pagos()
    {
        return $this->hasMany(\App\Models\Pago::class, 'id', 'tipo_cambio_id');
    }
    
}
