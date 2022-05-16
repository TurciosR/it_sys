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
	fechai=$("#fecha_inicio").val();
	fechaf=$("#fecha_fin").val();
	var header1="	<tr>"
		var header2="	<tr>"
			header1+="<th>Id</th>";
			header1+="<th>Numero Doc</th>";
			header1+="<th>Tipo Doc</th>";
			header1+="<th>fecha</th>";
			header1+="<th>Empleado</th>";
			header1+="<th>Cliente</th>";
			header1+="<th>Estado</th>";
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
		//$('#editable2 thead').append(header2);
		dataTable = $('#editable2').DataTable().destroy()
		dataTable = $('#editable2').DataTable( {
		 //"searching": false,
			"pageLength": 50,
			"order":[[ 0, 'asc' ]],
			//"processing": true,
			"serverSide": true,

		//	"ajax":"admin_garantia_cliente_dt.php",
			"ajax":{
					url :"admin_garantia_cliente_dt.php?fechai="+fechai+"&fechaf="+fechaf, // json datasource
					//type: "post",  // method  , by default get
					error: function(){  // error handling
						$(".editable2-error").html("");
						$("#editable2").append('<tbody class="editable2_grid-error"><tr><th colspan="3">No se encontró información segun busqueda </th></tr></tbody>');
						$("#editable2_processing").css("display","none");
						$( ".editable2-error" ).remove();
						}
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


$(document).on("click", ".dele", function() {
  var parent = $(this).parents().get(0);
	console.log(parent);
  $(parent).remove();
});

$(document).on("click", "#btnAgregar", function(event) {
	var pol=$("#politica_text").val();
	var pol_caja=$("#politica").val();
	var pol_id=$("#politica_id").val();
	if (pol!="") {
		tr_add = '';
		tr_add += "<tr><input type='hidden' name='pol_id'  id='pol_id' value='"+pol_id+"'>";
		tr_add += "<td id='desc'>"+pol+"</td>";
		tr_add += '</tr>';
		$("#inventable2").append(tr_add);
		$("#politica").val("");
		$("#politica_text").val("");
		$("#politica_text").css("display", "none");
		$("#politica").removeAttr("style");
		$("#politica_id").val("");
	}
	else
	{
		tr_add = '';
		tr_add += "<tr><input type='hidden' name='pol_id'  id='pol_id' value='0'>";
		tr_add += "<td id='desc'>"+pol_caja+"</td>";
		tr_add += '</tr>';
		$("#inventable2").append(tr_add);
		$("#politica").val("");
		$("#politica_text").val("");
		$("#politica_text").css("display", "none");
		$("#politica").removeAttr("style");
		$("#politica_id").val("");
	}
	console.log(pol);
});

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

$(document).on("click","#btnGua", function()
{
	console.log("OK");
	var i = 0;
	var k = 0;
	var id_garantia = $("#id_garantia").val();
	var array_json = new Array();
  var verificar = 'noverificar';
  var verificador = [];

  $("#lista_poli tr").each(function(index) {
		var id_po=$(this).find("#pol_id").val();
		var desc=$(this).find("#desc").text();
		if (desc!="") {
			var obj = new Object();
			obj.desc = desc;
			obj.pol_id = id_po;
			text1 = JSON.stringify(obj);
			array_json.push(text1);
			k=k+1;
		}
  });
  json_arr = '['+array_json+']';

	 var dataString = 'process=edit_po' +  '&cuantos=' + k +'&json_arr='+json_arr + "&id_garantia=" + id_garantia;
	 $.ajax({
		 type: 'POST',
		 url: "editar_politicas.php",
		 data: dataString,
		 dataType: 'json',
		 success: function(datax) {
			 display_notify(datax.typeinfo, datax.msg);
			 if(datax.datax.typeinfo == "Success")
			 {
				 setInterval("reload1();", 1500);
			 }
		 }
	 });
})

function reload1() {
  location.href = "admin_garantia_cliente.php";
}
