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
						<span>SOLICITUD DE PERMISO</span>
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
						<span class="label">Fecha Recepción RRHH: </span> <span class="label-shadow">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
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
					<td colspan="4" class="subtitle">
						PERMISO
					</td>
				</tr>
				<tr>
					<td colspan="4" class="text-center">
						<span class="label">Folio: </span> <span class="label-shadow">{{$data->id}}</span><br>
						<span class="label">Estatus: </span> <span class="label-shadow">{{Str::upper($data->status)}}</span><br>
					</td>
				</tr>
				<tr>
					<td width="30%">
						<span class="label" style="position: relative;top: 25%;">Fechas solicitadas</span>
					</td>
					<td colspan="3">
						Del: <span class="label-shadow">{{\Carbon\Carbon::parse($data->start_date)->format('d-m-Y')}}</span>
						<br>
						Al: <span class="label-shadow">{{\Carbon\Carbon::parse($data->end_date)->format('d-m-Y')}}</span>
					</td>
				</tr>
				<tr>
					<td width="25%">
						<span class="label">Con goce de sueldo: </span>
					</td>
					<td width="25%">
						<span class="label-shadow">&nbsp;&nbsp;{{$data->type_permit->with_pay == 1 ? "Si" : "-"}}&nbsp;&nbsp;</span>
					</td>
					<td width="25%">
						<span class="label">Sin goce de sueldo: </span>
					</td>
					<td width="25%">
						<span class="label-shadow">&nbsp;&nbsp;{{$data->type_permit->with_pay == 0 ? "Si" : "-"}}&nbsp;&nbsp;</span>
					</td>
				</tr>
				<tr>
					<td colspan="4" class="subtitle-small">
						Motivo
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<span class="label label-bold">{{ $data->dmirh_permit_concepts_id != null ? $data->permit_concept->description : $data->reason }}</span>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<span class="label">{{ $data->comments}}</span>
					</td>
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
					<td colspan="4">
						<span class="label"><strong>Nota:</strong> Si es un permiso con goce de sueldo se requiere firma del Director de Unidad de Negocio </span>
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
								@if($data->signatures[1]->status != null && $data->signatures[1]->status != "pendiente" )
									@if($data->sign_behalf != null && $data->sign_behalf->origin_table == 'control_signatures_behalves')
										<span style="font-size:11px">Se cuenta con firma autográfa <br> Firma por delegación <br></span>
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
								@if($data->signatures[2]->status != null && $data->signatures[2]->status != "pendiente" && $data->signatures[2]->is_automatic == 0)
									@if($data->sign_behalf_direct != null && $data->sign_behalf_direct->origin_table == 'control_signatures_behalves')
										<span style="font-size:11px">Se cuenta con firma autográfa <br> Firma por delegación <br></span>
										<br>
										<span class="handwritten-sign">{{$data->sign_behalf_direct->personal_intelisis->name}} {{$data->sign_behalf_direct->personal_intelisis->last_name}}</span>
									@elseif($data->sign_behalf_direct != null && $data->sign_behalf_direct->origin_table == 'control_plaza_substitution')
										<span style="font-size:11px">Firma por Delegación de Permisos<br></span>
										<br>
										<span class="handwritten-sign">{{$data->sign_behalf_direct->personal_intelisis->name}} {{$data->sign_behalf_direct->personal_intelisis->last_name}}</span>
									@else
										<span class="handwritten-sign">{{$data->signatures[2]->personal_intelisis->name}} {{$data->signatures[2]->personal_intelisis->last_name}}</span>
									@endif
									<br>
									<span class="digital-sign">ESTATUS - {{$data->signatures[2]->status}}</span>
									<br>
									<span class="digital-sign">FIRMA DIGITAL - {{ strtotime($data->signatures[2]->signed_date) }}</span>
									<br>
									<span class="digital-sign">{{\Carbon\Carbon::parse($data->signatures[2]->signed_date)->format('d-m-Y h:i:s')}}</span>
								@endif
								<br>
								<br>
								<br>
								<span class="title-sign">{{$data->signatures[2]->personal_intelisis->name}} {{$data->signatures[2]->personal_intelisis->last_name}}</span>
								<br>
								<span class="title-sign">{{$data->signatures[2]->personal_intelisis->position_company_full}}</span>
								<hr>
								<span class="label">{{$data->signatures[2]->personal_intelisis->position_company_full}}</span>
								<br>
								<br>
							</div>
						@endif
					</td>
					<td width="50%" colspan="2" class="text-center">
						@if(isset($data->signatures) && isset($data->signatures[3]) && $data->signatures[3]->order == 4)
								<div>
									<br>
									{{-- @if($data->signatures[3]->status != null && $data->signatures[3]->is_automatic == 0 && $data->signatures[3]->status != "pendiente")
										<span class="handwritten-sign">{{$data->signatures[3]->personal_intelisis->name}} {{$data->signatures[3]->personal_intelisis->last_name}}</span>
										<br>
										<span class="digital-sign">ESTATUS - {{$data->signatures[3]->status}}</span>
										<br>
										<span class="digital-sign">FIRMA DIGITAL - {{ strtotime($data->signatures[3]->signed_date) }}</span>
										<br>
										<span class="digital-sign">{{\Carbon\Carbon::parse($data->signatures[3]->signed_date)->format('d-m-Y h:i:s')}}</span>
									@endif --}}

									<br>
									<br>
									<span class="title-sign">{{$data->signatures[3]->personal_intelisis->name}} {{$data->signatures[3]->personal_intelisis->last_name}}</span>
									<br>
									<span class="title-sign">{{$data->signatures[3]->personal_intelisis->position_company_full}}</span>
									<hr>
									<span class="label">{{$data->signatures[3]->personal_intelisis->position_company_full}}</span>
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
