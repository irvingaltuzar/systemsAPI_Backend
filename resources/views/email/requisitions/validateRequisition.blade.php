@component('mail::message', ['data' => $data])

    Buen día <span><strong>{{ $data["data"]['user'] }}</strong></span>
    <br>
    <br>
    te informamos que tu solicitud de requisición con folio: <b>{{ $data["data"]['id'] }}</b> con Vacante: 
   <b>{{ $data["data"]['vacancy'] }}</b> ha sido validada por Recursos Humanos y se encuentra en recaudacion de firmas,
    ingresa para firmar tu requisición
    
@component('mail::button', ['url' => 'http://192.168.3.170:8080/MisRequisiciones'])
Ingresar
@endcomponent

@endcomponent
