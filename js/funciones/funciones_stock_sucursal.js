var dataTable ="";
$(document).ready(function() {
	// Clean the modal form
	$(document).on('hidden.bs.modal', function(e) {
		var target = $(e.target);
		target.removeData('bs.modal').find(".modal-content").html('');
	});
	generar();
	var suc=$("#id_sucursal").find("option:first").text();
	var texto="<strong>"+"Sucursal Seleccionada: "+suc+"</strong>";
	$("#titulo").html(texto);

});
function generar(){
	id_sucursal=$("#id_sucursal").val();
	$('#editable2 tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );
	dataTable = $('#editable2').DataTable().destroy()
	dataTable = $('#editable2').DataTable({
			"pageLength": 50,
			"order":[[ 1, 'desc' ], [ 0, 'desc' ]],
			"processing": true,
			"serverSide": true,
			"ajax":{
					url :"admin_stock_sucursal_dt.php?id_sucursal="+id_sucursal, // json datasource
					/*
					error: function(){  // error handling
						$(".editable2-error").html("");
						$("#editable2").append('<tbody class="editable2_grid-error"><tr><th colspan="3">No se encontró información segun busqueda </th></tr></tbody>');
						$("#editable2_processing").css("display","none");
						$( ".editable2-error" ).remove();
					}*/
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
				});

		dataTable.ajax.reload()
		/*
		dataTable.columns().eq( 0 ).each( function ( colIdx ){
				$( 'input', $('.filters th')[colIdx] ).on( 'keyup change', function () {
						dataTable
								.column( colIdx )
								.search( this.value )
								.draw();
				} );
		});*/
}


$(function (){
	//binding event click for button in modal form
	$(document).on("click", "#btnDelete", function(event) {
		deleted();
	});
 //on change elect sucursal
 $(document).on("change","#id_sucursal",function() {

	 var suc=$("#id_sucursal").find("option:selected").text();
	 var texto="<strong>"+"Sucursal Seleccionada: "+suc+"</strong>";
	 $("#titulo").html(texto);
	generar();
})

	//Reimprimir factura
	$(document).on("click", "#btnPrint", function(event) {
		print1();
	});
	// Clean the modal form
	$(document).on('hidden.bs.modal', function(e) {
		var target = $(e.target);
		target.removeData('bs.modal').find(".modal-content").html('');
	});
	//Reimprimir factura
	$(document).on("click", "#btnPrintFact", function(event) {
		print2();
	})
	//Recargar facturas
	$(document).on("click", "#btnReload", function(event) {
		reload2();
	});
	//Recargar facturas
	$(document).on("click", "#btnReload3", function(event) {
		reload3();
	});
	//Finalizar factura
	$(document).on("click", "#btnFinFact", function(event) {
		finalizar2();
	})
});
$(document).on("click", "#btnMostrar", function(event) {
	generar();
});

function reload1(){
	location.href = 'admin_factura.php';
}
function reload2(){
	location.href = 'admin_venta_nofin.php';
}
function reload3(){
	location.href = 'admin_facturas_vendedor.php';
}
function deleted() {
	var id_factura = $('#id_factura').val();
	var dataString = 'process=deleted' + '&id_factura=' + id_factura;
	$.ajax({
		type : "POST",
		url : "anular_factura.php",
		data : dataString,
		dataType : 'json',
		success : function(datax) {
			display_notify(datax.typeinfo, datax.msg);
			setInterval("location.reload();", 3000);
			$('#deleteModal').hide();
		}
	});
}
function print1() {
	var id_factura = $('#id_factura').val();
	var dataString = 'process=imprimir_fact' + '&id_factura=' + id_factura;
	$.ajax({
		type : "POST",
		url : "reimprimir_factura.php",
		data : dataString,
		dataType : 'json',
		success : function(datos) {
				sist_ope=datos.sist_ope;
					//esta opcion es para generar recibo en  printer local y validar si es win o linux
					if (sist_ope=='win'){
						$.post("http://localhost:8080/variedades/printpos1.php",{datosventa:datos.facturar})
					}
					else {
						$.post("http://localhost/variedades/printpos1.php",{datosventa:datos.facturar},function(data,status){
                        });

					}
			 setInterval("reload1();", 3000);


		}
	});
}
function print2() {
	var id_factura = $('#id_factura').val();
	var dataString = 'process=imprimir_fact' + '&id_factura=' + id_factura;
	$.ajax({
		type : "POST",
		url : "imprimir_factura.php",
		data : dataString,
		dataType : 'json',
		success : function(datos) {
			//display_notify(datax.typeinfo, datax.msg);
				sist_ope=datos.sist_ope;
				var efectivo_fin=parseFloat($('#efectivo').val());
				var cambio_fin=parseFloat($('#cambio').text());
					//esta opcion es para generar recibo en  printer local y validar si es win o linux
					if (sist_ope=='win'){
						$.post("http://localhost:8080/variedades/printpos1.php",{datosventa:datos.facturar})
					}
					else {
						$.post("http://localhost/variedades/printpos1.php",{datosventa:datos.facturar,efectivo:efectivo_fin,cambio:cambio_fin},function(data,status){
							if (status!='success'){
								alert("No Se envio la impresión " +data);
							}
							else{
								setInterval("reload2();", 3000);
							}
                        });
					}

		}
	});
}
$(document).on("keyup","#efectivo",function(){
  total_efectivo();
});
function total_efectivo(){
	var efectivo=parseFloat($('#efectivo').val());
	var totalfinal=parseFloat($('#facturado').text());
	var facturado= totalfinal.toFixed(2);
	if (isNaN(parseFloat(efectivo))){
		efectivo=0;
	}
	if (isNaN(parseFloat(totalfinal))){
		totalfinal=0;
	}
	var cambio=efectivo-totalfinal;
	var cambio=round(cambio, 2);
	var	cambio_mostrar=cambio.toFixed(2);
	if($('#efectivo').val()!='' && efectivo>=totalfinal)
		$('#cambio').html("<h5 class='text-success'>"+cambio_mostrar+"</h5>");
	else
		$('#cambio').text('');
	if(efectivo<totalfinal){
		$('#cambio').html("<h5 class='text-danger'>"+"Falta dinero !!!"+"</h5>");
	}
}
//function to round 2 decimal places
function round(value, decimals) {
    return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
}

function finalizar2() {
	var id_factura = $('#id_factura').val();
	var dataString = 'process=finalizar_fact' + '&id_factura=' + id_factura;
	$.ajax({
		type : "POST",
		url : "finalizar_factura.php",
		data : dataString,
		dataType : 'json',
				success: function(datax){
				process=datax.process;
				factura=datax.factura;
				display_notify(datax.typeinfo,datax.msg);


			 setInterval("reload2();", 3000);


		}
	});
}
