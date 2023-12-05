<?php

namespace App\Repositories;

use App\Models\CatErp;
use App\Models\CatPaymentSchedule;
use App\Models\CatWorkStation;
use App\Models\Location;
use Carbon\Carbon;

class ToolsRepository
{
	public function getLocations()
	{
		return Location::get();
	}

	public function getErp()
	{
		return CatErp::get();
	}

	public function getWorkStation()
	{
		return CatWorkStation::get();
	}

	public function getSchedule(int $id, $personal, $start_date)
	{
		$now = Carbon::now();
		$year = Carbon::now()->format("Y");
		$check_in = Carbon::parse($start_date);

		$schedule = CatPaymentSchedule::where(function ($q) use ($check_in) {
			$q->where(function ($q) use ($check_in) {
				$q->where([
					['fecha_inicio', '<=', $check_in],
					['fecha_fin', '>=', $check_in]
				]);
			})->orWhere(function ($q) use ($check_in) {
				$q->where([
					['fecha_inicio', '>=', $check_in],
					['fecha_fin', '<=', $check_in]
				]);
			})->orWhere(function ($q) use ($check_in) {
				$q->whereRaw("? between fecha_inicio and fecha_fin", $check_in->format('Y-m-d H:i:s'));
			});
		})
		->where('tipo_pago', 'like', "%$personal->payment_period%")
		->first();

		return $schedule;
	}

	public function getFilename(int $id, $personal, $start_date)
	{
		$schedule = $this->getSchedule($id, $personal, $start_date);

		$name = str_replace(" ", "_", $personal->full_name);

		$period = substr($personal->payment_period, 0, 1);

		$number = sprintf("%02d", $schedule->numero);

		return $file_name = "{$personal->personal_id}_{$name}_{$id}_{$period}{$number}.pdf";
	}
}
