$(document).ready(function()
{
	$( ".datepick" ).datepicker();
	$("#submit1").click(function()
	{
		cargar();
	})
	$("#btnTur").click(function()
	{
		cargar2();
	})
	$('.select').select2();
});

function cargar()
{
	var fini = $("#fini").val();
	var ffin = $("#ffin").val();
	var sel = $("#ti_re").val();
	if(sel != "")
	{
		if(sel == 0)
		{
			var cadena = "reporte_costo_ganancia.php?fini="+fini+"&ffin="+ffin;
			window.open(cadena, '', '');
		}
		else if(sel == 1)
		{
			var cadena = "reporte_venta_costo.php?fini="+fini+"&ffin="+ffin;
			window.open(cadena, '', '');
		}	
	}
	else
	{
		display_notify("Error", "Debe de seleccionar el tipo de reporte");
	}
	
}
function cargar2()
{
	var fini = $("#fecha11").val();
	var turno = $("#turno").val();
	var sucursal = $("#sucursal").val();
	if(turno != "")
	{
		var cadena = "reporte_venta_diaria.php?fini="+fini+"&turno="+turno+"&sucursal="+sucursal;
		window.open(cadena, '', '');
	}
	else
	{
		display_notify("Error", "Debe de seleccionar un turno");
	}
}
$("#fecha11").change(function()
{
	var fecha = $(this).val();
	$.ajax({
		type:'POST',
		url:"ver_reporte_venta_turno.php",
		data: "process=turno&fecha="+fecha,
		success: function(datax){
			$("#turno").html(datax);
		}
	});
})