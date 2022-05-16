$(document).ready(function()
{
  generar();
  $("#submit1").click(function()
  {
    senddata();
  });
});

function generar(){
	dataTable = $('#editable2').DataTable().destroy()
	dataTable = $('#editable2').DataTable( {
			"pageLength": 50,
			"order":[[ 0, 'DESC' ], [ 1, 'DESC' ]],
			"processing": true,
			"serverSide": true,
			"ajax":{
					url :"admin_clausula_dt.php",

					error: function(){  // error handling
						//$(".editable2-error").html("");
						$("#editable2").append('<tbody class="editable2_grid-error"><tr><th colspan="3">No se encontró información segun busqueda </th></tr></tbody>');
						$("#editable2_processing").css("display","none");
						$( ".editable2-error" ).remove();
						}
					},
			"language": {
								"url": "js/funciones/Spanish.json"
						},
					"columnDefs": [ {
		    "targets": 1,//index of column starting from 0
		    "render": function ( data, type, full, meta ) {
					if(data!=null)
		      return '<p class="text-success"><strong>'+data+'</strong></p>';
					else
					 return '';
		    }
		  } ]
				} );

		dataTable.ajax.reload()
}
//CKEDITOR.replace('descripcion');
function senddata()
{
  var titulo = $("#titulo").val();
  var descripcion = $("#descripcion").val();
  var process = $("#process").val();
  var dataString = "";
  if (process == 'insert')
  {
    dataString += "process="+process+"&titulo="+titulo+"&descripcion="+descripcion;
    url = "agregar_clausula.php";
  }
  else
  {
    var id_clausula = $("#id_clausula").val();
    dataString += "process="+process+"&titulo="+titulo+"&descripcion="+descripcion+"&id_clausula="+id_clausula;
    url = "editar_clausula.php";
  }

  $.ajax({
    type: 'POST',
    url: url,
    data: dataString,
    dataType: 'json',
    success: function(datax)
    {
      display_notify(datax.typeinfo, datax.msg);
      if(datax.typeinfo == "Success")
      {
        setInterval("reload1();", 1500);
      }
    }
  });
}

function reload1() {
  location.href = "admin_clausula.php";
}
