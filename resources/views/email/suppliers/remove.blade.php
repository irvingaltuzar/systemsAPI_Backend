@component('mail::message')
{{ $data->responsable_name }}

El proveedor {{ $data->business_name }} ha sido dado de baja, por: {{ $data->removed_by }}.
<br>
Motivo: {{ $data->comment }}

@component('mail::button', ['url' => 'http://192.168.3.170:8006'])
Ingresar
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
