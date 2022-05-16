var urlfactura="";
var table="";

$(document).ready(function() {
  var fecha=$("#fecha_inicio").val();
	var dataString ='process=ver_facturas_diarias' + '&fecha=' + fecha;
	var dataString2 ='admin_facturas_dia_dt.php?process=ver_facturas_diarias' + '&fecha=' + fecha;
	table = $('#editable2').DataTable( {
				ajax: dataString2
			});
	table.ajax.reload();
	$(".decimal").numeric();
		// Clean the modal form
	$(document).on('hidden.bs.modal', function(e) {
		var target = $(e.target);
		target.removeData('bs.modal').find(".modal-content").html('');
	});
});
$(document).one("click", "#btnGenerar", function(event) {
		generar();
});

//Proceso para generar las facturas diarias y mostrare una tabla en un div con las facturas generadas... para el dia posteriormente
function generar() {
	var fecha=$("#fecha_inicio").val();
	var dataString = 'process=generar_facturas_diarias' + '&fecha=' + fecha;
	$.ajax({
		type : "POST",
		url : "generar_facturas_dia3.php",
		data : dataString,
		dataType : 'json',
				success: function(datax){
				//process=datax.process;
				//factura=datax.factura;
				display_notify(datax.typeinfo,datax.msg);

			     setInterval("reload1();", 3000);
           	mostrar();

		}
	});
}
function reload1(){
	location.href ='generar_facturas_dia3.php';
}


$(document).on("click", "#btnMostrar", function(event) {
		mostrar();
});

//Proceso para generar las facturas diarias y mostrare una tabla en un div con las facturas generadas... para el dia posteriormente
function mostrar(){
	var fecha=$("#fecha_inicio").val();
	var dataString ='process=ver_facturas_diarias' + '&fecha=' + fecha;
	var dataString2 ='admin_facturas_dia_dt.php?process=ver_facturas_diarias' + '&fecha=' + fecha;
	 table = $('#editable2').DataTable().destroy()


	table = $('#editable2').DataTable( {
				ajax: dataString2
			});
	table.ajax.reload()


}
$(document).on("click", "#btnPrintFactdia", function(event) {
		imprime1();
});
function imprime1(){
	//var numero_doc=$(".modal-body #fact_num").html();
	var id_factura = $('.modal-body #id_factura').val();
	var fecha= $('.modal-body #fecha_gen').html();
  var num_doc_fact= $('.modal-body #num_doc_fact').val();
	var print='imprimir_fact';
	var dataString='process='+print+'&id_factura='+id_factura + '&id=1'+'&fecha='+fecha+'&num_doc_fact='+num_doc_fact;
  //alert(dataString)
		$.ajax({
				type:'POST',
				url:'imprimir_factura_dia.php',
				data: dataString,
				dataType: 'json',
				success: function(datos){
					sist_ope=datos.sist_ope;
					/*
					var efectivo_fin=parseFloat($('#efectivo').val());
					var cambio_fin=parseFloat($('#cambio').val());
					*/
					//esta opcion es para generar recibo en  printer local y validar si es win o linux
					if (sist_ope=='win'){
						$.post("http://localhost/pueblo/printfact1.php",{datosventa:datos.facturar})
					}
					else {
						$.post("http://localhost/pueblo/printfact1.php",{datosventa:datos.facturar},function(data,status){
						if (status!='success'){
							alert("No Se envio la impresión " +data);
						}
						else{
						//	setInterval("reload1();", 3000);
						}
						});
					}
					setInterval("reload1();", 1000);
				}
			});
}
