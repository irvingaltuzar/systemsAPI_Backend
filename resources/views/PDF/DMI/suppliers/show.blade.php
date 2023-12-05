<html>
	<head>
		<link rel="stylesheet" href="{{ public_path('css/proveedores.css') }}">
		{{-- <link rel="stylesheet" href="{{ url('css/proveedores.css') }}"> --}}



	</head>
	<body>

		<table width="100%">
			<tr>
				<td width="100%" class=" label text-right">
					<img width="25%" src="{{ public_path('img/logo-dmi.jpeg') }}" alt="">
				</td>
			</tr>
		</table>
		<br>
		<div class="content">
			<table width="100%">
				<tr>
					<td class="document-title">
						<span>SOLICITUD DE ALTA DE PROVEEDORES</span>
					</td>
				</tr>
				<tr>
					<td>
					<br>
					</td>
				</tr>
				<tr>
					<td class="label-bold">
						<span>Empresa del Grupo que requiere el alta:</span>
					</td>
				</tr>
				<tr>
					<td class="border">
						<br>
						<br>
						<br>
					</td>
				</tr>
			</table>
			<br>
		</div>

		<div class="content">
			<table>
				<tr>
					<td colspan="4"  class="bg-dmi border table-title">
						<span>Generales</span>
					</td>
				</tr>
				<tr>
					<td width="40%" class="border label text-right">
						Proveedor / Beneficiario (Razón Social) &nbsp;
					</td>
					<td width="30%" class="border label text-center">
						&nbsp;{{ $data->business_name }}
					</td>
					<td width="20%" class="border label text-center">
						&nbsp;Moral = M, Física = F
					</td>
					<td width="10%" class="border label text-center">
						&nbsp;{{ $data->type_person }}
					</td>
				</tr>
				<tr>
					<td width="40%" class="border label text-right">
						RFC &nbsp;
					</td>
					<td colspan="3" class="border label text-center">
						&nbsp;{{ $data->rfc }}
					</td>
				</tr>
			</table>
			<table width="100%">
				<tr>
					<td width="40%" class="border label text-right">
						Domicilio: Calle, Número &nbsp;
					</td>
					<td width="30%" class="border label text-center">
						&nbsp;{{ $data->address }}
					</td>
					<td width="10%" class="border label text-center">
						&nbsp;Colonia
					</td>
					<td width="20%" class="border label text-center">
						&nbsp;{{ $data->suburb }}
					</td>
				</tr>
			</table>
			<table width="100%">
				<tr>
					<td width="40%" class="border label text-right">
						Ciudad, C.P., Estado &nbsp;
					</td>
					<td width="60%" class="border label text-center">
						&nbsp;{{ $data->city }}, {{ $data->cp }}, {{ $data->state_name }}
					</td>
				</tr>
			</table>
			<table width="100%">
				<tr>
					<td width="40%" class="border label text-right">
						Días de Crédito &nbsp;
					</td>
					<td width="60%" class="border label text-center">
						&nbsp;{{ $data->credit_days }}
					</td>
				</tr>
			</table>
			<table width="100%">
				<tr>
					<td width="40%" class="border label text-right">
						Contacto de Ventas &nbsp;
					</td>
					<td width="60%" class="border label text-center">
						&nbsp;{{ $data->contact }}
					</td>
				</tr>
			</table>
			<table width="100%">
				<tr>
					<td width="40%" class="border label text-right">
						Teléfono &nbsp;
					</td>
					<td width="60%" class="border label text-center">
						&nbsp;{{ $data->phone }}
					</td>
				</tr>
			</table>
			<table width="100%">
				<tr>
					<td width="40%" class="border label text-right">
						Correo Eletrónico &nbsp;
					</td>
					<td width="60%" class="border label text-center">
						&nbsp;{{ $data->email }}
					</td>
				</tr>
			</table>
			<table width="100%">
				<tr>
					<td width="40%" class="border label text-right">
						Especialidades &nbsp;
					</td>
					<td width="60%" class="border label text-center">
						&nbsp;
						@foreach ($data->specialities as $item)
							{{ $item->specialty_name }},
						@endforeach
					</td>
				</tr>
			</table>

			<br>
			<br>
			<br>

			<table width="100%">
				<tr>
					<td colspan="2"  class="bg-dmi border table-title">
						<span>Términos de Pago </span>
					</td>
				</tr>
				<tr>
					<td width="40%" class="border label text-right">
						Forma de Pago &nbsp;
					</td>
					<td width="60%" class="border label text-center">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td width="40%" class="border label text-right">
						Moneda &nbsp;
					</td>
					<td width="60%" class="border label text-center">
						&nbsp;{{ $data->currency }}
					</td>
				</tr>
			</table>

			<br>
			<br>
			<br>

			<span style="font-size:10px">En caso de que la forma de pago sea transferencia, llene la siguiente sección:</span>
			<br>
			<table width="100%">
				<tr>
					<td colspan="2"  class="bg-dmi border table-title">
						<span>Datos de Transferencia						</span>
					</td>
				</tr>
				<tr>
					<td width="45%" class="border label text-right">
						Banco, sucursal y Plaza &nbsp;
					</td>
					<td width="55%" class="border label text-center">
						&nbsp;{{ $data->bank_name }}
					</td>
				</tr>
				<tr>
					<td width="45%" class="border label text-right">
						N° de Cuenta &nbsp;
					</td>
					<td width="55%" class="border label text-center">
						&nbsp;{{ $data->bank_account }}
					</td>
				</tr>
				<tr>
					<td width="45%" class="border label text-right">
						CLABE (18 digitos y sin espacio) OBLIGATORIA &nbsp;
					</td>
					<td width="55%" class="border label text-center">
						&nbsp;{{ $data->bank_clabe }}
					</td>
				</tr>
				<tr>
					<td width="45%" class="border label text-right">
						SWIFT (en caso de Divisa Americana)  &nbsp;
					</td>
					<td width="55%" class="border label text-center">
						&nbsp;{{ $data->bank_swift }}
					</td>
				</tr>
			</table>


		</div>


	</body>
</html>
