@component('mail::message')
{{ $data->responsable_name }}

El proveedor {{ $data->business_name }} ha sido dado de baja, por: {{ $data->removed_by }}.
<br>
Motivo: {{ $data->comment }}

Gracias,<br>
{{ config('app.name') }}
@endcomponent
