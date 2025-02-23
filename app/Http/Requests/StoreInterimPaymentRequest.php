<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInterimPaymentRequest extends FormRequest
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
			'diot_date' => 'required|date',
			'diot_id_transaction' => 'required',
			'dyp_date' => 'required|date',
			'dyp_id_transaction' => 'required',
			'yearly' => 'required',
            'month' => 'required',
			'year' => 'numeric|required|min:1950|max:2300',
        ];
    }

	public function attributes()
	{
		return [
			'company_id' => 'empresa',
			'diot_date' => 'fecha de presentación de DIOT',
			'diot_id_transaction' => 'folio de transacción de DIOT',

			'dyp_date' => 'fecha de presentación de DIOT',
			'dyp_id_transaction' => 'folio de transacción de DIOT',
            'month' => 'mes',
			'year' => 'año',
		];
	}
}
