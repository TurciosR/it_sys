$(document).ready(function()
{
	$( ".datepick" ).datepicker();
	$("#submit1").click(function()
	{
		cargar();
	})
});

function cargar()
{
	var fini = $("#fini").val();
	var ffin = $("#ffin").val();

	var cadena = "reporte_ingresos.php?fini="+fini+"&ffin="+ffin;
	window.open(cadena, '', '');
}