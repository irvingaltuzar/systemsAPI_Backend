@component('mail::message')
# Solicitud de vacaciones pendiente de firmar

{{ $data->name_user_sign }}, te informamos <br>
que tienes una solicitud de vacaciones pendiente de firmar, de: <b>{{ $data->owner_full_name }}</b> con folio: <b>{{ $data->origin_record_id }}</b>

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
