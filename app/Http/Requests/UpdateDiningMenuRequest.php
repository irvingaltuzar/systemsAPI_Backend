<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDiningMenuRequest extends FormRequest
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
			'menu_id' => 'required',
			'enabled_days' => 'required',
			'locations_id' => 'required',
			'files' => 'required',
			'start_date' => 'required',
			'finish_date' => 'required',
        ];
    }

	public function attributes()
	{
		return [
			'enabled_days' => 'días disponible',
			'locations_id' => 'locación',
			'files' => 'archivos',
			'start_date' => 'fecha inicio',
			'finish_date' => 'fecha fin',
		];
	}
}
