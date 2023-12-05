<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        return [
			'supplier_id' => 'required',
			'business_name' => 'required',
			'type_person' => 'required',
			'rfc' => 'required',
			'email' => 'required',
			'phone' => 'required',
			'contact' => 'required',
			'address' => 'required',
			'suburb' => 'required',
			'cp' => 'required',
			'country' => 'required',
			'state' => 'required',
			'city' => 'required',
			'bank' => 'required',
			'bank_account' => 'required',
			'bank_clabe' => 'required',
			'credit_days' => 'required',
			'currency' => 'required',
        ];
    }

	public function attributes()
	{
		return [
			'business_name' => 'días disponible',
			'type_person' => 'tipo de persona',
			'rfc' => 'rfc',
			'email' => 'correo',
			'contact' => 'contacto de proveedor',
			'address' => 'calle y número',
			'suburb' => 'colonia',
			'cp' => 'codigo postal',
			'country' => 'país',
			'state' => 'estados',
			'city' => 'ciudad',
			'bank' => 'banco',
			'bank_account' => 'número de cuenta',
			'bank_clabe' => 'CLABE',
			'credit_days' => 'días de crédito',
			'currency' => 'tipo de moneda',
		];
	}
}
