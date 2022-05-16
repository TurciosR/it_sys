$(document).ready(function() {
  $('#example').dataTable({
    order: [[2,"desc"],[3,"desc"]],
    "language": {
            "lengthMenu": "Mostrando _MENU_ registros por pagina",
            "zeroRecords": "No se encontraron registros",
            "info": "Mostrando pagina _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(Filtrando de _MAX_ registros )",
            "paginate": {
              "previous": "Anterior",
              "next": "Siguiente"
            }
        }
  });
});
