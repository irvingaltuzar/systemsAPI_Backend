@component('mail::message', ['data' => $data])

    Buen día:
    <br>
    <br>
    te informamos que tienes una solicitud de requisición pendiente de validar con folio: <b>{{ $data["data"]['id'] }}</b>
    del usuario <span><strong>{{ $data["data"]['user'] }}</strong></span>
    
@component('mail::button', ['url' => 'http://192.168.3.170:8080/PanelRequisiciones'])
Ingresar
@endcomponent

@endcomponent
