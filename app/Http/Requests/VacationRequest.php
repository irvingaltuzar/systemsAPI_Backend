<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VacationRequest extends FormRequest
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
            'personal_id' => 'required|string|min:1',
            'personal_intelisis_usuario_ad' => 'required|string|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'total_days' => 'required|numeric|min:1',
        ];
    }

    public function attributes()
    {
        return [
            'personal_intelisis_usuario_ad' => 'usuario',
            'personal_id' => 'usuario',
            'start_date' => 'fecha inicio',
            'end_date' => 'fecha término',
            'total_days' => 'dias totales',
        ];
    }
}
