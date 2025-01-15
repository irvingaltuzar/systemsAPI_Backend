<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEAccountingRequest extends FormRequest
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
			'id_transaction' => 'required',
			'yearly' => 'required',
			'e_accounting_status' => 'required',
			'month' => 'required',
			'year' => 'numeric|required|min:1950|max:2300',
        ];
    }

	public function attributes()
	{
		return [
			'company_id' => 'empresa',
			'date' => 'fecha de solicitud',
			'id_transaction' => 'folio de transacción',
			'e_accounting_status' => 'estatus',
			'yearly' => 'anual',
			'month' => 'mes',
			'year' => 'año',
		];
	}
}
