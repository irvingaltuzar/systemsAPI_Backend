@component('mail::message')
# {{$data['name_coordinador']}}, buen día.

<br>
Se te informa que a sido autorizado un permiso de <strong>{{$data['name']}}</strong> con folio <strong>{{$data['id']}}</strong>, el día {{$data['date']}}.
{{-- Se te informa que tu <strong>solicitud de permiso</strong> con folio <strong>{{$data['id']}}</strong> ha sido <strong>{{$data['status']}}</strong> el día {{$data['date']}}. --}}
<br>
@endcomponent
