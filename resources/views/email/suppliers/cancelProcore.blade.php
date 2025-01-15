@component('mail::message', ['data' => $data])
{{ $data->supplier['responsable_name'] }},

Te informamos que {{ Auth()->user()->personal_intelisis->name }} {{ Auth()->user()->personal_intelisis->last_name }} ha rechazado tu solicitud de alta del proveedor: {{ $data->supplier['business_name'] }}
<br>

<b>Motivo: </b> {{ $data->comment }}

@component('mail::button', ['url' => 'https://lbu16a4tf2oyjpqtimv2yinwvk.grupodmi.com.mx:17050'])
Ingresar
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
