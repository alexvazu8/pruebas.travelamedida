<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Reserva
 *
 * @property $id
 * @property $Localizador
 * @property $Importe_Reserva
 * @property $Nombre_Cliente
 * @property $Email_contacto_reserva
 * @property $Comentarios
 * @property $Usuario_id
 * @property $created_at
 * @property $updated_at
 *
 * @property User $user
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Reserva extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['guid','Localizador', 'Importe_Reserva', 'Nombre_Cliente','Apellido_Cliente','Telefono_Cliente','Email_contacto_reserva', 'Comentarios', 'Usuario_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'Usuario_id', 'id');
    }
    
}
