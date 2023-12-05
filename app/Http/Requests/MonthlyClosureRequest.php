<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonthlyClosureRequest extends FormRequest
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
            'dmi_accounting_companies_id' => 'required|string|min:1',
            'month' => 'required|string|min:2|max:2',
            'year' => 'required|string|min:4|max:4',
        ];
    }

	public function attributes(){
		return [
            'dmi_accounting_companies_id' => 'empresa',
            'month' => 'mes',
            'year' => 'año',
        ];
	}
}
