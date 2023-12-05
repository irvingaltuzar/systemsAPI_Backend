
@extends('layouts.mainIndex')

@section('title', 'Otra Pagina')

@section('content')




     <!-- DataTales Example -->
     <div class="card shadow mb-4">
       <div class="card-header py-3">
         <h6 class="m-0 font-weight-bold text-primary">DataTables Example</h6>
       </div>
       <div class="card-body">
         <div class="table-responsive">
           <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
             <thead>
               <tr>
                 <th>Folio</th>
                 <th>Registro</th>
                 <th>RFC</th>
                 <th>Contacto</th>
                 <th>EFO</th>
                 <th>Fecha Registro</th>
               </tr>
             </thead>
             <tfoot>
               <tr>
                <th>Folio</th>
                <th>Registro</th>
                <th>RFC</th>
                <th>Contacto</th>
                <th>EFO</th>
                <th>Fecha Registro</th>
               </tr>
             </tfoot>
             <tbody>
            
             </tbody>
           </table>
         </div>
       </div>
     </div>

     <script>
function datatbl(){
    $.ajax({
        url: "prov",
        type: "GET"
    }).done(function(response){
        var data=JSON.parse(response);
        console.log(data);
        console.log(response);
        // $.each(data,function(key,value){
        //   $("#dataTable").append("<tr><td>"+value["id"]+"</td><td>"+value["rfc"]+"</td><td>"+value["correo"]+"</td><td>"+value["contacto"]+"</td><td>"+value["estatus"]+"</td><td>"+value["fecha"]+"</td></tr>");
        // })
       $('#dataTable').DataTable({
            data: data,
            language: {
            "decimal": "",
            "emptyTable": "No hay información",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
            "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "Sin resultados encontrados",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Siguiente",
                "previous": "Anterior"
            }
          },
        // },
            columns: [
                { data: "id" },
                { data: "rfc" },
                { data: "correo" },
                { data: "contacto" },
                { data: "estatus" },
                { data: "fecha" }
            ],
            
        });
    }).fail(function(respuesta){
        console.log(respuesta);
    })
    
  
   
}
$(document).ready(function(){

datatbl();
});


     </script>
     

 @endsection

