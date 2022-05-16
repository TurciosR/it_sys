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
	// Setup - add a text input to each header or footer cell
	var header1="	<tr>"
		var header2="	<tr>"
	header1+="<th>Cod. Producto</th>";
			header1+="<th>Barcode</th>";
			header1+="<th>Descripción</th>";
			header1+="<th>Costo</th>";
			header1+="<th>Precio</th>";
			header1+="<th>Stock</th>";
			header1+="<th>Ubicaci&oacute;n</th>";
			header1+="<th>Acci&oacute;n</th>";
		header1+="</tr>";
		$('#editable2 thead').append(header1);
    $('#editable2 thead th').each( function () {
        var title = $(this).text();
      //  header2+= '<th><input type="text"  class="form-group col-md-8 col-xs-12" placeholder="Buscar '+title+'" /></th>';
    } );
		header2+= '<th><input type="text"  class="form-group col-md-8 col-xs-12" placeholder="Buscar " /></th>';
		header2+= '<th><input type="text"  class="form-group col-md-8 col-xs-12" placeholder="Buscar " /></th>';
		header2+= '<th><input type="text"  class="form-group col-md-8 col-xs-12" placeholder="Buscar " /></th>';
		header2+= '<th><input type="text"  class="form-group col-md-8 col-xs-12" placeholder="Buscar " /></th>';
		header2+= '<th><input type="text"  class="form-group col-md-8 col-xs-12" placeholder="Buscar " /></th>';
		header2+= '<th><input type="text"  class="form-group col-md-8 col-xs-12" placeholder="Buscar " /></th>';
	header2+= '<th>--</th>';
		header2+= '<th>--</th>';
	header2+="</tr>";
		$('#editable2 thead').append(header2);
	dataTable = $('#editable2').DataTable().destroy()
	dataTable = $('#editable2').DataTable( {
		 //"searching": false,
			"pageLength": 50,
			"order":[[ 0, 'asc' ]],
			"processing": true,
			"serverSide": true,
			"ajax":"admin_stock_dt.php",

					error: function(){  // error handling
						//$(".editable2-error").html("");
						$("#editable2").append('<tbody class="editable2_grid-error"><tr><th colspan="3">No se encontró información segun busqueda </th></tr></tbody>');
						$("#editable2_processing").css("display","none");
						$( ".editable2-error" ).remove();
					},
					initComplete: function() {
						var api = this.api();

						// Apply the search
						api.columns().every(function() {
							var that = this;

							$('input', this.header()).on('keyup change', function() {
								if (that.search() !== this.value) {
									that
										.search(this.value)
										.draw();
								}
							});
						});
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
$(document).on("click", "#excel1", function(event) {
	generar_xls();
});
$(document).on("click", "#print1", function(event) {
	imprimir_pdf();
});

function generar_xls(){
	var dataString = 'reporte_stock_xls.php';
	window.open(dataString , '', '');
}
function imprimir_pdf(){
	var dataString = 'ver_reporte_stock.php';
	window.open(dataString , '', '');
}
