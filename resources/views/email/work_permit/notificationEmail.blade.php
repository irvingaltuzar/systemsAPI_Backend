@component('mail::message')
# {{$data['name']}}, buen día.

<br>
Se te informa que tu <strong>solicitud de permiso</strong> con folio <strong>{{$data['id']}}</strong> ha sido <strong>{{$data['status']}}</strong> el día {{$data['date']}}.
<br>
@endcomponent
