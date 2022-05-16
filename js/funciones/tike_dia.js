$(document).ready(function()
{
	var fecha = $("#fecha").val();
	$.ajax({
		type:'POST',
		url:"ticket_dia.php",
		data: "process=tiket&fecha="+fecha,
		success: function(datax)
		{
			$("#t_mov").html(datax);
		}
	});

	$("#fecha").change(function()
	{
		var fecha = $(this).val();
		$.ajax({
			type:'POST',
			url:"ticket_dia.php",
			data: "process=tiket&fecha="+fecha,
			success: function(datax)
			{
				$("#t_mov").html(datax);
			}
		});
	})

	$("#submit").click(function()
	{
		cargar();
	})

});

function cargar()
{
	var fecha = $("#fecha").val();
	var id_sucursal = $("#id_sucursal").val();
	var cadena = "reporte_ticket.php?fecha="+fecha+"&id_sucursal="+id_sucursal;
	window.open(cadena, '', '');
}