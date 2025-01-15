@component('mail::message')
# Solicitud de justificación pendiente por aprobar
Buen día <b>{{ $data['boss_name'] }}</b>,
<br>
Te informamos que tienes una solicitud de justificación pendiente por aprobar de <b>{{ $data['collaborator_name'] }}</b> con folio <b>{{ $data['folio'] }}</b>.

@if(env('APP_ENV_IS_PROD') == 0)
@component('mail::button', ['url' => 'http://192.168.3.160:8080/authorisations'])
Ingresar
@endcomponent
@else
@component('mail::button', ['url' => 'http://192.168.3.170:8080/authorisations'])
Ingresar
@endcomponent
@endif


@endcomponent

