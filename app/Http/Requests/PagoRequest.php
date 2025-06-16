<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PagoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'guid' => 'required|string|unique:pagos,guid',
            'token' => 'required|string',
            'expiration_token' => 'required|integer|min:0',
            'usuario_id' => 'required|integer|exists:usuarios,id',
            'monto' => 'required|numeric|min:2.01',
            'metodo_pago' => 'required|string|in:StereumPay,tarjeta',
            'estado' => 'required|string|in:pendiente,pagado,cancelado',
            'transaction_id_metodo_pago' => 'nullable|string',
            'fecha_pago' => 'nullable|date',
        ];
    }
    public function messages(): array
    {
        return [
            'guid.required' => 'Este GUID es obligatorio.',
            'guid.unique' => 'Este GUID ya está en uso.',
            'token.required' => 'El token es obligatorio.',
            'expiration_token.required' => 'La expiración del token es obligatoria.',
            'expiration_token.date' => 'La expiración del token debe ser un entero valido.',
            'usuario_id.required' => 'El usuario es obligatorio.',
            'usuario_id.exists' => 'El usuario seleccionado no existe.',
            'monto.required' => 'El monto es obligatorio.',
            'monto.numeric' => 'El monto debe ser numérico.',
            'monto.min' => 'El monto debe ser mayor a 2.',
            'metodo_pago.required' => 'El método de pago es obligatorio.',
            'metodo_pago.in' => 'El método de pago no es válido.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser: pendiente, pagado o cancelado.',
            'fecha_pago.date' => 'La fecha de pago debe ser una fecha válida.',
        ];
    }
}
