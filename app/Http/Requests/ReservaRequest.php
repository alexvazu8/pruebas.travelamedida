<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			'Localizador' => 'required|string',
			'Importe_Reserva' => 'required',
			'Nombre_Cliente' => 'required|string',
			'Email_contacto_reserva' => 'required|string',
			'Comentarios' => 'string',
			'Usuario_id' => 'required',
        ];
    }
}
