@component('mail::message')
# Solicitud de cambio de horario pendiente por aprobar
Buen día <b>{{ $data['rrhh_full_name'] }}</b>,
<br>
<br>
Te informamos que tienes una solicitud de cambio de horario pendiente por validar de <b>{{ $data['collaborator_full_name'] }}</b>, aprobado por <b>{{ $data['boss_full_name'] }}</b>.

@if (env("APP_ENV_IS_PROD") == 1)
@component('mail::button', ['url' => 'http://192.168.3.170:8080/PanelAprobarHorarios'])
Ingresar
@endcomponent
@else
@component('mail::button', ['url' => 'http://192.168.3.160:8080/PanelAprobarHorarios'])
Ingresar
@endcomponent
@endif

@endcomponent

