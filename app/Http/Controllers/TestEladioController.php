<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Dmirh\DmirhWorkPermit;
use App\Services\SendEmailService;
use App\Models\DmiControlProcess;
use Carbon\Carbon;
use App\Models\Accounting\MonthlyClosure;


class TestEladioController extends Controller
{


	function index(){
		//echo "Test - Eladio <br>";
		//dd(Carbon::parse(Carbon::now())->format("Y"));
		return DmiControlProcess::all();

	}

	public function graficaCierreMensual(){
		$request = (object) array(
			"finish_date" => ["id" => "02" ,'value' => "2023-02-10"],
			"accountant_id" => "luz.zermeno",
			"month" => "02",
			"year" => "2023",
		);

		$finish_date = Carbon::parse($request->finish_date['value']);

		$electronic_accounting = MonthlyClosure::with(['accounting_companies'])
			->whereRelation('accounting_companies', function ($query) use ($request) {
				return $query->where('accountant_id', 'like', "%$request->accountant_id%");
			})
			->where(function ($q) use ($request){
				$q->where('month', $request->month)->where('year', $request->year);
			})
			->get();
			return ($electronic_accounting);

		$in_time = $electronic_accounting->filter( function ($q) use ($finish_date) {
			return $q->date_accounting <= $finish_date;
		})->count();

		$lately = $electronic_accounting->filter( function ($q) use ($finish_date) {
			return $q->date_accounting >= $finish_date;
		})->count();

		$data = [$in_time, $lately];

		return $data;
	}

	public function test_email_permisos(){
		$_work_permit_id = 9;
		$permit = DmirhWorkPermit::with('personal_intelisis')->where('id',$_work_permit_id)->first();

        $subject="Permiso de Trabajo";
        $name=strtoupper($permit->personal_intelisis->name.' '.$permit->personal_intelisis->last_name);
        $email=$permit->personal_intelisis->email;

        $data = [
                    'data' =>[
						'date' => $permit->updated_at,
						'subject' => $subject,
						'name' => $name,
						'status' => strtoupper($permit->status),
					],
                    'module' => "work_permit",
                    'to_email' => $email,
                ];

		$this->SendEmailService = new SendEmailService();
		$this->SendEmailService->notificationEmailRRHH($data);

	}




}
