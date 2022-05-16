$(document).ready(function() {
	// Clean the modal form
	$(document).on('hidden.bs.modal', function(e) {
		var target = $(e.target);
		target.removeData('bs.modal').find(".modal-content").html('');

	});
	$('#viewModal').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
});
generar();
}); //end $(document).ready(function() {
	$(function (){
		//binding event click for button in modal form
		$(document).on("click", "#btnDelete", function(event) {
			deleted();
		});
		// Clean the modal form
		$(document).on('hidden.bs.modal', function(e) {
			var target = $(e.target);
			target.removeData('bs.modal').find(".modal-content").html('');
		});

	});
function generar(){
	dataTable = $('#editable2').DataTable().destroy()
	dataTable = $('#editable2').DataTable( {
			"pageLength": 50,
			"order":[[ 0, 'asc' ], [ 1, 'asc' ]],
			"processing": true,
			"serverSide": true,
			"ajax":{
					url :"admin_productos_dt.php",

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

//function to round 2 decimal places
function round(value, decimals) {
    return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
}

function deleted() {
	var id_producto = $('#id_producto').val();
	var dataString = 'process=deleted' + '&id_producto=' + id_producto;
	$.ajax({
		type : "POST",
		url : "borrar_producto.php",
		data : dataString,
		dataType : 'json',
		success : function(datax) {
			display_notify(datax.typeinfo, datax.msg);
			setInterval("location.reload();", 500);
			$('#deleteModal').hide();
		}
	});
}
$(document).on("click", "#btnPrintBcodes", function(event) {
		print_bcodes();
	});

	function print_bcodes(){
	  var id_producto = $('#id_producto').val();
		var qty = $('#qty').val();
	  var dataString = 'process=buscarprodcant' + '&id_producto=' + id_producto+ '&qty=' +qty;
	  $.ajax({
	    type: "POST",
	    url:"ver_producto.php",
	    data: dataString,
	    dataType: 'json',
	    success: function(datos) {
	      config=datos.pop()

	      var sist_ope = config.sist_ope;
	      var dir_print=config.dir_print;
	      var shared_printer_barcode=config.shared_printer_barcode;
	      //alert(dir_print+" "+sist_ope+" "+shared_printer_barcode)
	      if (sist_ope == 'win') {
	        $.post("http://"+dir_print+"printbcodewin1.php", {
	          datosproductos:datos,
	          shared_printer_barcode:shared_printer_barcode
	        })
	      } else {
	        $.post("http://"+dir_print+"printbcode1.php", {
	            datosproductos: datos
	        });
	      }
	    }
	});

	}
