var dataTable ="";
$(document).ready(function() {
	// Clean the modal form
	$(document).on('hidden.bs.modal', function(e) {
		var target = $(e.target);
		target.removeData('bs.modal').find(".modal-content").html('');
	});
	generar();
});
function generar(){
	fechai=$("#fecha_inicio").val();
	fechaf=$("#fecha_fin").val();
	dataTable = $('#editable2').DataTable().destroy()
	dataTable = $('#editable2').DataTable( {
			"pageLength": 50,
			"order":[[ 1, 'desc' ], [ 0, 'desc' ]],
			"processing": true,
			"serverSide": true,
			"ajax":{
					url :"admin_traslados_recibidos_rangos_nofin_dt.php?fechai="+fechai+"&fechaf="+fechaf, // json datasource
					//url :"admin_factura_rangos_dt.php", // json datasource
					//type: "post",  // method  , by default get
					error: function(){  // error handling
						$(".editable2-error").html("");
						$("#editable2").append('<tbody class="editable2_grid-error"><tr><th colspan="3">No se encontró información segun busqueda </th></tr></tbody>');
						$("#editable2_processing").css("display","none");
						$( ".editable2-error" ).remove();
					}
				},
				"columnDefs": [ {
			"targets": 1,//index of column starting from 0
			"render": function ( data, type, full, meta ) {
				if(data!=null)
				return '<p class="text-success"><strong>'+data+'</strong></p>';
				else
				 return '';
			}
		} ],
		"language": {
							"url": "js/funciones/Spanish.json"
					}
			} );
		dataTable.ajax.reload()
	//}
}
$(function (){
	//binding event click for button in modal form
	$(document).on("click", "#btnAnular", function(event) {
		anular();
	});
	$(document).on("click", "#btnMostrar", function(event) {
		generar();
	});

});

function reload1(){
	location.href = 'admin_traslados_enviados.php';
}

function anular() {
	var id_traslado = $('#id_traslado').val();
	var dataString = 'process=anular' + '&id_traslado=' + id_traslado;
	$.ajax({
		type : "POST",
		url : "anular_traslado.php",
		data : dataString,
		dataType : 'json',
		success : function(datax) {
			display_notify(datax.typeinfo, datax.msg);
			setInterval("location.reload();", 3000);
			$('#deleteModal').hide();
		}
	});
}
