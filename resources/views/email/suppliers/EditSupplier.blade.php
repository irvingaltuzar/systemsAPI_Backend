@extends('email.layout')
@section('body')
    Buen día 
    <br>
    <br>
    Te informamos que el usuario <strong> {{ $data["data"]['user'] }} </strong> ha editado este proveedor:<strong> {{ $data["data"]['business_name'] }}</strong>
<br>
    @component('mail::button', ['url' => 'http://localhost:8082'])
        Ingresar
@endcomponent

@endsection

