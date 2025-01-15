@component('mail::message')
Estimado <b>{{ $data['name_boss'] }}</b>,
<br>
<h3 style="color:#ff4949;">Agradezco de antemano su tiempo para revisar la información</h3>
Le informamos que ya esté disponible el reporte de incidencias de tu personal en <strong>INTRANET</strong>,correspondiente al período <strong>{{ $data['start_date'] }}</strong> al <strong>{{ $data['end_date'] }}</strong>.
Solicitamos su amable apoyo para validar cuáles de estas incidencias son justificables.
<br>
<br>
Personal incluido en el reporte: {{ $data['list_user'] }}
<br>
<br>
Aquellas incidencias que no cuenten con justificación seran consideradas para aplicar los <strong>descuentos</strong> en el período de pago mencionado.
<br>
<br>

@if(env('APP_ENV_IS_PROD') == 0)
@component('mail::button', ['url' => 'http://192.168.3.160:8080/rh/cai/validar-incidencias'])
Ir a Validar Incidencias
@endcomponent
@else
@component('mail::button', ['url' => 'http://192.168.3.170:8080/rh/cai/validar-incidencias'])
Ir a Validar Incidencias
@endcomponent
@endif


@endcomponent

