<html>
	<head>
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
		<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
		<link rel="stylesheet" href="{{ public_path('css/printer.css') }}">
	</head>
	<body>
		<htmlpageheader name="page-header">
			<table cellspacing="5" cellpadding="2">
				<tr style="position: absolute;">
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td class="col-3 text-left subtext-1 text-center">
						<img src="{{ public_path('/img/DMI_logo.svg') }}" class="alineadoTextoImagenCentro" style="width:20%;z-index:1;">
					</td>
					<td class="col-9 text-center"><strong><span class="title" style="font-size: 1.6rem;">SERVICIO DE COMEDOR DEL PERIODO: {{ \Carbon\Carbon::parse($data->start_date)->format('d-m-Y'); }} AL {{ \Carbon\Carbon::parse($data->finish_date)->format('d-m-Y'); }}</span></strong></td>
					<td class="col-3 text-1 text-center">

					</td>
				</tr>
			</table>
		</htmlpageheader>


		<htmlpagefooter name="page-footer">
			Your Footer Content
		</htmlpagefooter>

		<div></div>
		<div class="container">
			<div class="col-md-12">
				<div class="invoice">
					<div class="invoice-content">
						<!-- begin table-responsive -->
						<div class="table-responsive">
						<table class="table table-invoice">
							<thead>
								<tr>
									<th class="text-center main-th" width="10%">Empleado</th>
									<th class="text-center main-th" width="10%">Usuario</th>
									<th class="text-center main-th" width="10%">Lunes</th>
									<th class="text-center main-th" width="10%">Martes</th>
									<th class="text-center main-th" width="10%">Miércoles</th>
									<th class="text-center main-th" width="10%">Jueves</th>
									<th class="text-center main-th" width="10%">Viernes</th>
									<th class="text-center main-th" width="10%">Pedido semanal</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($data->orders as $order)
									<tr class="main-tr">
										<td class="text-center main-td">
											<span class="text-center">{{ $order->user_name }}</span><br>
											<small>{{ $order->user->personal_intelisis ? $order->user->personal_intelisis->personal_id : false}}</small>
											<br>
										</td>
										<td class="text-center main-td">
											<span class="text-center">{{ $order->user->usuario }}</span><br>
										</td>
										<td class="text-center main-td">
											<span class="text-center">{{ $order->products->filter(function ($val) { return $val->work_day_id == 2;})->first() ? $order->products->filter(function ($val) { return $val->work_day_id == 2;})->first()->food_type : 'Ninguno' }}</span><br>
										</td>
										<td class="text-center main-td">
											<span class="text-center">{{ $order->products->filter(function ($val) { return $val->work_day_id == 3;})->first() ? $order->products->filter(function ($val) { return $val->work_day_id == 3;})->first()->food_type : 'Ninguno' }}</span><br>
										</td>
										<td class="text-center main-td">
											<span class="text-center">{{ $order->products->filter(function ($val) { return $val->work_day_id == 4;})->first() ? $order->products->filter(function ($val) { return $val->work_day_id == 4;})->first()->food_type : 'Ninguno' }}</span><br>
										</td>
										<td class="text-center main-td">
											<span class="text-center">{{ $order->products->filter(function ($val) { return $val->work_day_id == 5;})->first() ? $order->products->filter(function ($val) { return $val->work_day_id == 5;})->first()->food_type : 'Ninguno' }}</span><br>
										</td>
										<td class="text-center main-td">
											<span class="text-center">{{ $order->products->filter(function ($val) { return $val->work_day_id == 6;})->first() ? $order->products->filter(function ($val) { return $val->work_day_id == 6;})->first()->food_type : 'Ninguno' }}</span><br>
										</td>
										<td class="text-center main-td">
											<span class="text-center">{{ $order->products->count() }}</span><br>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						</div>
						<!-- end table-responsive -->
						<!-- begin invoice-price -->
						<div class="invoice-price">
						<div class="invoice-price-left">
							<div class="invoice-price-row">
								<div class="sub-price">
									<small><b>Total de platillos:</b></small>
									<span class="text-inverse">{{ $data->orders->map(fn ($order) => $order->products->count())->sum() }}</span>
								</div>
								<div class="sub-price">
									<i class="fa fa-plus text-muted"></i>
								</div>
								<div class="sub-price">
									<small><b>Descuento otorgado  (50%): </b></small>
									<span class="text-inverse">$55.00</span>
								</div>
							</div>
						</div>
						<div class="invoice-price-right">
							<small><b>TOTAL con descuento:</b></small> <span class="f-w-600">${{ ($data->orders->map(fn ($order) => $order->products->count())->sum() * $data->employee_price) }}</span>
						</div>
						</div>
						<!-- end invoice-price -->
					</div>
					<!-- end invoice-content -->
				</div>
			</div>
		</div>

</html>
