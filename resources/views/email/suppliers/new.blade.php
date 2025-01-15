@component('mail::message')

    Buen día 
    <br>
    <br>
    Te informamos que el usuario <strong> {{ $data["data"]['user'] }} </strong> ha agregado un nuevo proveedor:<strong> {{ $data["data"]['business_name'] }}</strong>
    <br>
    @component('mail::button', ['url' => 'http://localhost:8082'])
        Ingresar
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent

