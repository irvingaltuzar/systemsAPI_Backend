@component('mail::message', ['data' => $data])
    Buen día:
    <br>
    <br>
    te informamos que tienes una solicitud de requisición pendiente de firmar con folio: <b>{{ $data["data"]['id'] }}</b>
    ingresa al apartado de <b>Solicitudes pendientes de Firma</b>

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
