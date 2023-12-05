@extends('email.layout')
@section('body')
    Buen día 
    <br>
    <br>
    Te informamos que el usuario <strong> {{ $data["data"]['user'] }} </strong> ha editado este proveedor:<strong> {{ $data["data"]['business_name'] }}</strong>
<br>
    @component('mail::button', ['url' => 'http://192.168.3.170:8080/proveedores/panel'])
        Ingresar
@endcomponent

@endsection

