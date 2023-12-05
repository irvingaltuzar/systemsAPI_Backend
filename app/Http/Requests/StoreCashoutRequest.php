<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCashoutRequest extends FormRequest
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
			'payment_day' => 'required',
			'start_date' => 'required',
			'finish_date' => 'required',
        ];
    }

	public function attributes()
	{
		return [
			'payment_day' => 'quincena cubierta',
			'start_date' => 'fecha inicio',
			'finish_date' => 'fecha fin',
		];
	}
}
