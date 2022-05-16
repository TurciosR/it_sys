/*
$(document).ready(function() {
	//generar();
});
*/
$(document).on("click", "#excel1", function(event) {
	generar();
});
$(document).on("click", "#print1", function(event) {
	imprimir_pdf();
});

function generar(){
	//alert("x");
	var fechaini=$("#fecha_inicio").val();
	var fechafin=$("#fecha_fin").val();
	var dataString = 'reporte_kardex_xls.php?' + '&fecha_inicio=' + fechaini + '&fecha_fin=' + fechafin;
	window.open(dataString , '', '');
	/*

	var dataString ='fecha_inicio=' + fechaini + '&fecha_fin=' + fechafin;
	alert(dataString);
	$.ajax({
		type : "POST",
		url : "reporte_kardex_xls.php",
		data : dataString,
		dataType : 'json',
				success: function(datax){
				display_notify(datax.typeinfo,datax.msg);
			    setInterval("reload1();", 3000);

		}
	});
	*/
}
function imprimir_pdf(){
	var fechaini=$("#fecha_inicio").val();
	var fechafin=$("#fecha_fin").val();
	var dataString = 'reporte_mov_productos_pdf.php?' + '&fecha_inicio=' + fechaini + '&fecha_fin=' + fechafin;
	window.open(dataString , '', '');

}

function reload1(){
	location.href = 'reporte_mov_producto.php';
}
