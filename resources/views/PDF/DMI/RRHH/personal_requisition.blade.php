<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- <link href='https://fonts.googleapis.com/css?family=Great Vibes' rel='stylesheet'> -->
		<link rel="stylesheet" href="{{ public_path('css/personal_requisition_style.css') }}" />
		<!-- {{-- <link rel="stylesheet" href="{{ url('css/personal_requisition_style.css') }}"> --}} -->

	</head>

	<body>

		<div class="content">
			<table width="100%">
				<tr>
					<td colspan="3" class="document-title">
						<span>REQUISICIÓN DE PERSONAL</span>
					</td>
				</tr>
			</table>
			<!-- <br> -->
			<table width="100%">
				<tr>
					<td colspan="3" class="subtitle">
						DATOS GENERALES
					</td>
				</tr>
				<tr>
					<td>
						<span class="label">Fecha de solicitud: </span> <span class="label-shadow">{{$data->date}}</span>
					</td>
					<td>
						<span class="label">Fecha de Recepción: </span> <span class="label-shadow">{{$data->date_received_rh}}</span>
					</td>
					<td>
						<span class="label">Fecha de Cobertura: </span> <span class="label-shadow">{{$data->date_estimate_coverage}}</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="label">Puesto Requerido: </span> <span class="label-shadow">{{$data->vacancy}}</span>
					</td>
					<td>
						<span class="label">Nivel de Posición: </span> <span class="label-shadow">{{$data->level_position}}</span>
					</td>
					<td>
						<span class="label">Num Vacantes: </span> <span class="label-shadow">{{$data->num_vacancy}}</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="label">Departamento: </span> <span class="label-shadow">{{$data->department}}</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="label">Razón social: </span> <span class="label-shadow">{{$data->company_name}}</span>
					</td>
					<td>
						<span class="label">Sucursal: </span> <span class="label-shadow">{{$data->branch_code}}</span>
					</td>
					</tr>
					<tr>
					<td>
						<span class="label">Lugar de Trabajo (Describe la ubicación física):: </span> <span class="label-shadow">{{$data->personal_location}}</span>
					</td>
				</tr>
			</table>
			<!-- <br> -->
			<table width="100%">
				<tr>
					<td colspan="4" class="subtitle">
						MOTIVO DE LA VACANTE
					</td>
				</tr>

			</table >
			<!-- <br> -->
			<table width="100%" >
				<tr >
					<td colspan="2" class="subtitle-small">
					REEMPLAZO:
					</td>
					<td colspan="2" class="subtitle-small">
					PERSONAL TEMPORAL:
					</td>
					<td colspan="2" class="subtitle-small">
					REQUISITOS:
					</td>
				</tr>
				<tr>
				@if(isset($data->reason_replacement) &&$data->reason_replacement == 'Baja')
					<td class="td_custom" colspan="2">
						<span class="label">Por Renuncia/Baja: </span> <span class="label-shadow">SI</span>
					</td>
					@else
					<td  class="td_custom" colspan="2">
						<span class="label">Por Renuncia/Baja: </span> <span class="label-shadow">NO</span>
					</td>
					@endif
				@if(isset($data->temp_reason) &&$data->temp_reason == 'Incapacidad')
					<td  class="td_custom" colspan="2">
						<span class="label">Incapacidad: </span> <span class="label-shadow">SI   {{ $data->days_temp_reason}} Dias</span>
					</td>
					@else
					<td class="td_custom" colspan="2">
						<span class="label">Incapacidad: </span> <span class="label-shadow">NO </span>
					</td>
					@endif
					@if(isset($data->time_travel) )
					<td class="td_custom" colspan="3">
						<span class="label">Disponibilidad para viajar: </span> <span class="label-shadow">SI</span>
					</td>
					@else
					<td class="td_custom" colspan="3">
						<span class="label">Disponibilidad para viajar: </span> <span class="label-shadow">NO</span>
					</td>
					@endif
				<tr>
				@if(isset($data->reason_replacement) && $data->reason_replacement == 'Promocion')
					<td class="td_custom" colspan="2">
						<span class="label">Por Promoción: </span> <span class="label-shadow">SI</span>
					</td>
					@else
					<td class="td_custom" colspan="2">
						<span class="label">Por Promoción: </span> <span class="label-shadow">NO</span>
					</td>
					@endif
					@if(isset($data->temp_reason) &&$data->temp_reason == 'Carga de Trabajo')
					<td class="td_custom" colspan="2">
						<span class="label">Carga de Trabajo: </span> <span class="label-shadow">SI   {{ $data->days_temp_reason}} Dias</span>
					</td>
					@else
					<td class="td_custom" colspan="2">
						<span class="label">Carga de Trabajo: </span> <span class="label-shadow">NO</span>
					</td>
					@endif
					<td class="td_custom" colspan="3">
						<span class="label">Periocidad de Viaje: </span> <span class="label-shadow">{{$data->days_travel}} {{$data->time_travel}}</span>
					</td>
				<tr>
				@if(isset($data->type) && $data->type == 'Nueva creacion')
					<td class="td_custom" colspan="2">
						<span class="label">Nueva Creación: </span> <span class="label-shadow">SI</span>
					</td>
				@else
					<td class="td_custom" colspan="2">
						<span class="label">Nueva Creación: </span> <span class="label-shadow">NO</span>
					</td>
					@endif
					@if(isset($data->temp_reason) && $data->temp_reason == 'Proyecto')
					<td class="td_custom" colspan="2">
						<span class="label">Proyecto: </span> <span class="label-shadow">SI   {{ $data->days_temp_reason}} Dias</span>
					</td>
					@else
					<td class="td_custom" colspan="2">
						<span class="label">Proyecto: </span> <span class="label-shadow">NO</span>
					</td>
					@endif
				</tr>

				<tr >
					<td colspan="3" class="subtitle-small">
					SUELDO NOMINAL:
					</td>
					@if(isset($data->personal_substitution) && $data->personal_substitution!=="" && $data->personal_substitution!=="null")
					<td  colspan="3" class="subtitle-small">
					PERSONAL A REEMPLAZAR:
					</td>
					@endif

					@if(isset($data->plaza_id) && $data->plaza_id !== "" && $data->plaza_id!=="null")
					<td  colspan="3" class="subtitle-small">
					PUESTO A REEMPLAZAR:
					</td>
					@endif
				</tr>
				<tr >
					<td colspan="3" class="td_custom" >
						<span  class="label">Sueldo nominal: </span><span class="label-shadow">$ {{$data->salary}} M.N.</span>
					</td>
					@if(isset($data->personal_substitution) && isset($data->dmi_personal_substitution))
					<td colspan="3" class="td_custom" >
						<span class="label">Empleado: </span><span class="label-shadow">{{$data->personal_substitution}} {{$data->dmi_personal_substitution->name}} {{$data->dmi_personal_substitution->last_name}}</span>
					</td>
					@endif
					@if(isset($data->plaza_id) && isset($data->personal_intelisis_plaza))
					<td colspan="3" class="td_custom" >
						<span class="label">Puesto: </span><span class="label-shadow"> {{$data->personal_intelisis_plaza->position_company_full_plazas}}</span>
					</td>
					@endif
				<tr >
					<td colspan="4">
						<span class="text-center"></span>  <span class="label-shadow text-center">* En ningún motivo se pueden ofertar sueldos netos</span>
					</td>

				</tr>
				</table>
			<!-- </div> -->

			<!-- </table> -->
			<!-- <br> -->
			<table width="100%">
				<tr>
					<td colspan="4" class="subtitle">
						SISTEMAS
					</td>
				</tr>

			</table>
			<!-- <br> -->

			<table  width="100%">

				<tr>
					<td colspan="5" >
						<span class="label-shadow">En caso de solicitar una posición de nueva creación es indispensable considerar las herramientas que requiere</span>
					</td>
					</tr><br>
				<tr>
					<td colspan="4" >
						<span class="label"></span> Marque con una X las herramientas que se solicitarán al área de sistemas: <span class="label-shadow"></span>
					</td>

				</tr><br>

				<tr>
				@if(is_array($data->resources) && in_array(2, $data->resources))
					<td colspan="2" >
						<span class="label">Laptop: </span> <span class="label-shadow">SI</span>
					</td>
					@else
					<td colspan="2" >
						<span class="label">Laptop: </span> <span class="label-shadow">NO</span>
					</td>
					@endif
					@if(is_array($data->resources) && in_array(3, $data->resources))
					<td colspan="2 " >
						<span class="label">Soft Phone (diadema): </span> <span class="label-shadow">SI</span>
					</td>
					@else
					<td colspan="2 " >
						<span class="label">Soft Phone (diadema): </span> <span class="label-shadow">NO</span>
					</td>
					@endif
					@if(is_array($data->resources) && in_array(1, $data->resources))
					<td colspan="2" >
						<span class="label">Teléfono Fijo: </span> <span class="label-shadow">SI</span>
					</td>
					@else
					<td colspan="2" >
						<span class="label">Teléfono Fijo: </span> <span class="label-shadow">NO</span>
					</td>
					@endif
				</tr>
				<tr colspan="4">
				@if(is_array($data->resources) && in_array(4, $data->resources))
					<td colspan="2" >
						<span class="label">Cuenta Correo: </span> <span class="label-shadow">SI</span><br>
					</td>
					<td colspan="2" >
						<span class="label">Dominio: </span> <span class="label-shadow">{{$data->dmi_control_email_domain->domain}}</span>
					</td>
					@else
					<td colspan="2" >
						<span class="label">Cuenta Correo: </span> <span class="label-shadow">NO</span><br>
					</td>
					<td colspan="2" >
						<span class="label">Dominio: </span> <span class="label-shadow"></span>
					</td>
					@endif
				</tr><br><br>

				<tr>
					<td colspan="4" >
						<span class="label">Software Adicional (Especifique):
					</td>
				</tr>
					<tr>
					<td colspan="4" >
					   <span class="label-shadow">{{$data->software_aditional}}</span>
					</td>
						</tr><br><br>

						<tr >
					<td colspan="5" class="text-center" >
				 <span class="label-shadow"><strong>La firma en esta requisición autoriza la compra de las herramientas y equipos marcados</strong></span>
					</td>
						</tr>
			</table>
			<!-- <br> -->
			<table width="100%">
				<tr>
					<td colspan="2" class="subtitle">
						AUTORIZACIONES
					</td>
				</tr>

			</table>
			<br>
			<table width="95%" style="margin-left:10%;  border: 1px solid black;
  border-collapse: collapse;">
  <tr>
					<td colspan="2" class="label-shadow">
						FECHA DE VALIDACION: {{$data->date_validation_rh}}
					</td>
						</tr>
				<tr>
				@if(isset($sign[0]))
					<td class="td_custom">
					<span class="label">{{$sign[0]->personal_intelisis->name}} {{$sign[0]->personal_intelisis->last_name}} </span> <br>
					<span class="label">{{$sign[0]->personal_intelisis->position_company_full}}  </span>

					</td>
					<td class="td_custom">
					@if(isset($sign[0]->signed_date) || $sign[0]->status=="firmado" || $sign[0]->status=="completada")
					<span class="handwritten-sign">{{$sign[0]->personal_intelisis->name}} {{$sign[0]->personal_intelisis->last_name}} </span> <br>
					<span class="label"> Firma Digital - {{ strtotime($sign[0]->signed_date) }} </span> <br>
					<span class="label">{{$sign[0]->signed_date}}  </span> <br>
					@endif
					</td>
			  @endif
				</tr>
				<tr>
				@if(isset($sign[1]))
				<td class="td_custom">
					<span class="label">{{$data->signatures[1]->personal_intelisis->name}} {{$data->signatures[1]->personal_intelisis->last_name}} </span> <br>
					<span class="label">{{$data->signatures[1]->personal_intelisis->position_company_full}}  </span>

					</td>
					<td class="td_custom">
					@if(($sign[1]->personal_intelisis_usuario_ad=="juanjose" || $sign[1]->personal_intelisis_usuario_ad=="alvarol" || $sign[1]->personal_intelisis_usuario_ad=="eduardo") && ($data->signatures[1]->status=="firmado" || $data->signatures[1]->status=="completada"))
					<span class="label">Se cuenta con firma autógrafa: (firma por delegación)</span> <br>
					@endif
					@if(isset($audit[0]) && ($sign[1]->personal_intelisis_usuario_ad=="juanjose" || $sign[1]->personal_intelisis_usuario_ad=="alvarol" || $sign[1]->personal_intelisis_usuario_ad=="eduardo") && ($data->signatures[1]->status=="firmado" || $data->signatures[1]->status=="completada"))
					<span class="handwritten-sign">{{$audit[0]->personal_intelisis_requisition->name}} {{$audit[0]->personal_intelisis_requisition->last_name}} </span> <br>
					<span class="label">Firma Digital - {{ strtotime($sign[1]->signed_date) }} </span> <br>
					<span class="label">{{$sign[1]->signed_date}}  </span> <br>
					@elseif (isset($sign[1]->signed_date) || $data->signatures[1]->status=="firmado" || $sign[1]->status=="completada")
					<span class="handwritten-sign">{{$sign[1]->personal_intelisis->name}} {{$sign[1]->personal_intelisis->last_name}} </span> <br>
					<span class="label">Firma Digital - {{ strtotime($sign[1]->signed_date) }} </span> <br>
					<span class="label">{{$sign[1]->signed_date}}  </span> <br>
					@endif
					</td>
					@endif
				</tr>
				<tr>
				@if(isset($sign) && isset($sign[2]) )

				<td class="td_custom">
					<span class="label">{{$sign[2]->personal_intelisis->name}} {{$sign[2]->personal_intelisis->last_name}} </span> <br>
					<span class="label">{{$sign[2]->personal_intelisis->position_company_full}}  </span>

					</td>
					<td class="td_custom">
					@if(($sign[2]->personal_intelisis_usuario_ad=="juanjose" || $sign[2]->personal_intelisis_usuario_ad=="alvarol" || $sign[2]->personal_intelisis_usuario_ad=="eduardo")  && ($data->signatures[2]->status=="firmado" || $data->signatures[2]->status=="completada"))
					<span class="label">Se cuenta con firma autógrafa: (firma por delegación)</span> <br>
					@endif
					@if(isset($audit[0]) && ($sign[2]->personal_intelisis_usuario_ad=="juanjose" || $sign[2]->personal_intelisis_usuario_ad=="alvarol" || $sign[2]->personal_intelisis_usuario_ad=="eduardo") && ($data->signatures[2]->status=="firmado" || $data->signatures[2]->status=="completada"))
					<span class="handwritten-sign">{{$audit[0]->personal_intelisis_requisition->name}} {{$audit[0]->personal_intelisis_requisition->last_name}} </span> <br>
					<span class="label">Firma Digital - {{ strtotime($data->signatures[2]->signed_date) }} </span> <br>
					<span class="label">{{$data->signatures[2]->signed_date}}  </span> <br>
					@elseif(isset($sign[2]->signed_date) || $sign[2]->status=="firmado" || $sign[2]->status=="completada")
					<span class="handwritten-sign">{{$sign[2]->personal_intelisis->name}} {{$sign[2]->personal_intelisis->last_name}} </span> <br>
					<span class="label">Firma Digital - {{ strtotime($sign[2]->signed_date) }} </span> <br>
					<span class="label">{{$sign[2]->signed_date}}  </span> <br>
					@endif
					</td>
					@endif

				</tr>
				<tr>
				@if(isset($sign) && isset($sign[3]))

				<td class="td_custom">
					<span class="label">{{$sign[3]->personal_intelisis->name}} {{$sign[3]->personal_intelisis->last_name}} </span> <br>
					<span class="label">{{$sign[3]->personal_intelisis->position_company_full}}  </span>

					</td>
					<td class="td_custom">
					@if(isset($sign[3]->signed_date) || $sign[3]->status=="firmado" || $sign[3]->status=="completada")
					<span class="handwritten-sign">{{$sign[3]->personal_intelisis->name}} {{$sign[3]->personal_intelisis->last_name}} </span> <br>
					<span class="label"> Firma Digital - {{ strtotime($sign[3]->signed_date) }} </span> <br>
					<span class="label">{{$sign[3]->signed_date}}  </span> <br>
					@endif
					</td>
					@endif

				</tr>

			</table><br>
		</div>
	</body>
</html>
