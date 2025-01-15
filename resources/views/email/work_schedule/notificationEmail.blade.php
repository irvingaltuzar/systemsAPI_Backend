@component('mail::message')
# Solicitud de cambio de horario
Buen día <b>{{ $data['collaborator_full_name'] }}</b>,
<br>
<br>
@if ($data['status'] == 'Autorizado')
Te informamos que <b>{{ $data['boss_full_name'] }}</b> a <b>{{ $data['status'] }}</b> tu solicitud de cambio de horario, ahora pasará a RRHH para ser <b>validado</b>.    
@elseif($data['status'] == 'Aprobado')
Te informamos que <b>{{ $data['rrhh_full_name'] }}</b> valido y <b>{{ $data['status'] }}</b> tu solicitud de cambio de horario, con fecha de inicio <b>{{ $data['start_date'] }}</b>.
@else
@if (isset($data['boss_full_name']))
Te informamos que <b>{{ $data['boss_full_name'] }}</b> a <b>{{ $data['status'] }}</b> tu solicitud de cambio de horario.    
@else
Te informamos que <b>{{ $data['rrhh_full_name'] }}</b> a <b>{{ $data['status'] }}</b> tu solicitud de cambio de horario.
@endif
@endif 

@if (env("APP_ENV_IS_PROD") == 1)
@component('mail::button', ['url' => 'http://192.168.3.170:8080/SolicitarCambiarHorario'])
Ingresar
@endcomponent
@else
@component('mail::button', ['url' => 'http://192.168.3.160:8080/SolicitarCambiarHorario'])
Ingresar
@endcomponent
@endif


@endcomponent

