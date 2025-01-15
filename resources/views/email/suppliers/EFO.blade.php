@component('mail::message', ['data' => $data])

    Buen día <span><strong>{{ $data["data"]['user'] }}</strong></span>.
    <br>
    <br>
    El proveedor <strong>{{$data["data"]['business_name']}}</strong> {{$data['content']}}<strong> Estatus: {{$data["data"]['efo']}}</strong> .

    @component('mail::button', ['url' => 'http://192.168.3.170:8080/proveedores/panel'])
Ingresar
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
