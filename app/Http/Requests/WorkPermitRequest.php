<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkPermitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'personal_intelisis_usuario_ad' => 'required|string|min:1',
            'type_permit_id' => 'required|numeric|min:1',
            'permit_concept_id' => 'nullable|numeric',
            'reason' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'total_days' => 'required|numeric|min:1',
            'comments' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'personal_intelisis_usuario_ad' => 'usuario',
            'type_permit_id' => 'tipo de permiso',
            'permit_concept_id' => 'motivo',
            'reason' => 'motivo',
            'start_date' => 'fecha inicio',
            'end_date' => 'fecha final',
            'total_days' => 'dias totales',
            'comments' => 'comentarios',
        ];
    }
}
