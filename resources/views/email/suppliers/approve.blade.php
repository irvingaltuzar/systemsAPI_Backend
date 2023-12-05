@component('mail::message', ['data' => $data])
{{ $data['responsable_name'] }},

Te informamos que {{ Auth()->user()->personal_intelisis->name }} {{ Auth()->user()->personal_intelisis->last_name }} ha aprobado tu solicitud de alta del proveedor: {{ $data->business_name }}
<br>


@component('mail::button', ['url' => 'http://192.168.3.170:8080/proveedores/registroproveedores'])
Ingresar
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
