<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
    'monto',
    'guid',
    'token',
    'expiration_token',
    'metodo_pago',
    'usuario_id',
    'estado',
    'transaction_id_metodo_pago',
    'fecha_pago',
];
    //
}
