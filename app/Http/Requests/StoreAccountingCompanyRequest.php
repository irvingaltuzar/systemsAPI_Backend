<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountingCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        return [
			'has_law' => 'required',
			'manager_id' => 'required',
			'accountant_id' => 'required',
			'erp_id' => 'required',
			'work_station_id' => 'required',
			'company_name' => 'required',
        ];
    }

	public function attributes()
	{
		return [
			'has_law' => 'ley antilavado',
			'manager_id' => 'gerente',
			'accountant_id' => 'contador',
			'erp_id' => 'erp',
			'work_station_id' => 'se lleva en',
			'company_name' => 'empresa',
		];
	}
}
