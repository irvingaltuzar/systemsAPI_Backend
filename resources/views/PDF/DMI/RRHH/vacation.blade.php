<html>
	<head>
		<link rel="stylesheet" href="{{ public_path('css/work_permit_style.css') }}">
		{{-- <link rel="stylesheet" href="{{ url('css/work_permit_style.css') }}"> --}}



	</head>
	<body>

		<div class="content">
			<table width="100%">
				<tr>
					<td colspan="3" class="document-title">
						<span>SOLICITUD DE VACACIONES</span>
					</td>
				</tr>
			</table>
			<br>
			<table width="100%">
				<tr>
					<td colspan="3" class="subtitle">
						DATOS GENERALES
					</td>
				</tr>
				<tr>
					<td>
						<span class="label">No. Empleado: </span> <span class="label-shadow">{{$data->personal_intelisis->personal_id}}</span>
					</td>
					<td>
						<span class="label">Fecha de solicitud: </span> <span class="label-shadow">{{\Carbon\Carbon::parse($data->date_request)->format('d-m-Y')}}</span>
					</td>
					<td>
						<span class="label">Jefe inmediato: </span> <span class="label-shadow">{{$data->personal_intelisis->immediate_boss->name}} {{$data->personal_intelisis->immediate_boss->last_name}}</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="label">Nombre: </span> <span class="label-shadow">{{$data->personal_intelisis->name}} {{$data->personal_intelisis->last_name}}</span>
					</td>
					<td>
						<span class="label">Puesto: </span> <span class="label-shadow">{{$data->personal_intelisis->position_company_full}}</span>
					</td>
					<td>
						<span class="label">Jornada: </span> <span class="label-shadow">{{$data->workday}}</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="label">Razón social: </span> <span class="label-shadow">{{$data->personal_intelisis->company_name}}</span>
					</td>
					<td>
						<span class="label">Ubicación: </span> <span class="label-shadow">{{$data->personal_intelisis->location}}</span>
					</td>
					<td>
						<span class="label">Departamento: </span> <span class="label-shadow">{{$data->personal_intelisis->deparment}}</span>
					</td>
				</tr>
			</table>
			<br>
			<br>
			<table width="100%">
				<tr>
					<td colspan="3" class="subtitle">
						VACACIONES
					</td>
				</tr>
				<tr>
					<td colspan="3" class="text-center">
						<span class="label">Folio: </span> <span class="label-shadow">{{$data->id}}</span><br>
						<span class="label">Estatus: </span> <span class="label-shadow">{{Str::upper($data->status)}}</span><br>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="text-center">
						<br>
						<span class="label">
							Por medio de la presente, solicito se me autorice tomar <span class="label-bold">{{$data->total_days}} {{$data->total_days > 1 ? 'días' : 'día'}}</span> de mi periodo vacacional, el cuál laboré<br>
							del <span class="label-bold">{{$period_text}}</span> y que pretendo disfrutar del <span class="label-bold">{{\Carbon\Carbon::parse($data->start_date)->format('d-m-Y')}}</span> al <span class="label-bold">{{\Carbon\Carbon::parse($data->end_date)->format('d-m-Y')}}</span>.</span>
						<br>
						<br>
						<br>
					</td>
				</tr>
				<tr>
					<td  class="text-center"><span class="label">Periodo correspondiente: </span> <span class="label-shadow">{{$data->period}}</span></td>
					<td></td>
					<td  class="text-center"><span class="label">Días otorgados al año: </span> <span class="label-shadow">{{$vacation_days_law}}</span></td>
				</tr>
				<tr>
					<td class="text-center"><span class="label">Saldo inicial: </span> <span class="label-shadow">{{$saldo_inicial}} {{$saldo_inicial > 1 ? 'días' : 'día'}}</span></td>
					<td class="text-center"><span class="label">Solicitado: </span> <span class="label-shadow">{{$data->total_days}}</span></td>
					<td class="text-center"><span class="label">Saldo final: </span> <span class="label-shadow">{{$saldo_inicial - $data->total_days}}</span></td>
				</tr>

			</table>
			<br>
			<br>
			<table width="100%">
				<tr>
					<td colspan="4" class="subtitle">
						AUTORIZACIONES
					</td>
				</tr>
				<tr>
					<td width="50%" colspan="2" class="text-center">
						@if(isset($data->signatures) && isset($data->signatures[0]) && $data->signatures[0]->order == 1)
							<div>
								<br>
								@if($data->signatures[0]->status != null && $data->signatures[0]->status != "pendiente")
									<span class="handwritten-sign">{{$data->signatures[0]->personal_intelisis->name}} {{$data->signatures[0]->personal_intelisis->last_name}}</span>
									<br>
									<span class="digital-sign">ESTATUS - {{$data->signatures[0]->status}}</span>
									<br>
									<span class="digital-sign">FIRMA DIGITAL - {{ strtotime($data->signatures[0]->signed_date) }}</span>
									<br>
									<span class="digital-sign">{{\Carbon\Carbon::parse($data->signatures[0]->signed_date)->format('d-m-Y h:i:s')}}</span>
								@endif
								<br>
								<br>
								<span class="title-sign">{{$data->signatures[0]->personal_intelisis->name}} {{$data->signatures[0]->personal_intelisis->last_name}}</span>
								<br>
								<span class="title-sign">{{$data->signatures[0]->personal_intelisis->position_company_full}}</span>
								<hr>
								<span class="label">FIRMA DEL EMPLEADO</span>
								<br>
								<br>
							</div>
						@endif
					</td>
					<td width="50%" colspan="2" class="text-center">
						@if(isset($data->signatures) && isset($data->signatures[1]) && $data->signatures[1]->order == 2)
							<div>
								<br>
								@if($data->signatures[1]->status != null && $data->signatures[1]->status != "pendiente")
									@if($data->sign_behalf != null && $data->sign_behalf->origin_table == 'control_signatures_behalves')
										<span style="font-size:11px">Se cuenta con firma autográfa<br>Firma por delegación<br></span>
										<br>
										<span class="handwritten-sign">{{$data->sign_behalf->personal_intelisis->name}} {{$data->sign_behalf->personal_intelisis->last_name}}</span>
									
									@elseif($data->sign_behalf != null && $data->sign_behalf->origin_table == 'control_plaza_substitution')
										<span style="font-size:11px">Firma por Delegación de Permisos<br></span>
										<br>
										<span class="handwritten-sign">{{$data->sign_behalf->personal_intelisis->name}} {{$data->sign_behalf->personal_intelisis->last_name}}</span>
									@else
										<span class="handwritten-sign">{{$data->signatures[1]->personal_intelisis->name}} {{$data->signatures[1]->personal_intelisis->last_name}}</span>
									@endif

									<br>
									<span class="digital-sign">ESTATUS - {{$data->signatures[1]->status}}</span>
									<br>
									<span class="digital-sign">FIRMA DIGITAL - {{ strtotime($data->signatures[1]->signed_date) }}</span>
									<br>
									<span class="digital-sign">{{\Carbon\Carbon::parse($data->signatures[1]->signed_date)->format('d-m-Y h:i:s')}}</span>

								@endif
								<br>
								<br>
								<span class="title-sign">{{$data->signatures[1]->personal_intelisis->name}} {{$data->signatures[1]->personal_intelisis->last_name}}</span>
								<br>
								<span class="title-sign">{{$data->signatures[1]->personal_intelisis->position_company_full}}</span>
								<hr>
								<span class="label">JEFE INMEDIATO</span>
								<br>
								<br>
							</div>
						@endif
					</td>
				</tr>
				<tr>
					<td width="50%" colspan="2" class="text-center">
						@if(isset($data->signatures) && isset($data->signatures[2]) && $data->signatures[2]->order == 3)
							<div>
								<br>
								{{-- @if($data->signatures[2]->status != null && $data->signatures[2]->status != "pendiente")
									<span class="handwritten-sign">{{$data->signatures[2]->personal_intelisis->name}} {{$data->signatures[2]->personal_intelisis->last_name}}</span>
									<br>
									<span class="digital-sign">ESTATUS - {{$data->signatures[2]->status}}</span>
									<br>
									<span class="digital-sign">FIRMA DIGITAL - {{ strtotime($data->signatures[2]->signed_date) }}</span>
									<br>
									<span class="digital-sign">{{\Carbon\Carbon::parse($data->signatures[2]->signed_date)->format('d-m-Y h:i:s')}}</span>
								@endif --}}
								<br>
								<br>
								<br>
								<span class="title-sign">{{$data->signatures[2]->personal_intelisis->name}} {{$data->signatures[2]->personal_intelisis->last_name}}</span>
								<br>
								<span class="title-sign">{{$data->signatures[2]->personal_intelisis->position_company_full}}</span>
								<hr>
								<span class="label">COORDINADOR DE RRHH</span>
								<br>
								<br>
							</div>
						@endif
					</td>

				</tr>
			</table>


		</div>
	</body>
</html>
