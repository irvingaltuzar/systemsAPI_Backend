<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOverviewRequest extends FormRequest
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
			'company_id' => 'required',
			'date' => 'required|date',
			'comment' => 'required',
			'overview_id' => 'required',
        ];
    }

	public function attributes()
	{
		return [
			'company_id' => 'empresa',
			'date' => 'fecha de solicitud',
			'comment' => 'comentarios',
			'overview_id' => 'opinión',
		];
	}
}
