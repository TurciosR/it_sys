$(document).ready(function()
{
	//$('#datos_garantia').hide();
	$("select").select2();
	$("#skardex").click(function(){
        kardex();
    });
	$("#sucursal").change(function(){
		kardex();
	});
	$("#hkardex").click(function(){
        kardex();
    });
	$("#pkardex").click(function(){
        var fini = $("#fini").val();
        var fin = $("#fin").val();
        var id_producto = $("#id_producto").val();
        var sucursal = $("#sucursal").val();
        window.open('reporte_kardex.php?id_producto='+id_producto+'&sucursal='+sucursal+'&fini='+fini+'&fin='+fin, '','');

    });
    $("#srotacion").click(function(){
        rotacion();
    });
	$("#sucursal1").change(function(){
		rotacion();
	});
	$("#hrotacion").click(function(){
        rotacion();
    });
	$(".upper").keyup(function(){
		$(this).val($(this).val().toUpperCase());
	});
	$('#descuento').numeric({negative:false, decimal: false});

	$("#descuento").keyup(function(){
		if(parseInt($(this).val()) > 100)
		{
			$(this).val(100);
		}
	});
	$('.numeric').numeric({negative:false,decimalPlaces: 2});
	$('.datepicker').datepicker({
	format: 'yyyy-mm-dd',
	 language: 'es-ES'
	//startDate: '1d'
});
	$("#proveedor").typeahead({
	 source: function(query, process) {
		$.ajax({
			url: 'agregar_producto.php',
			type: 'POST',
			data: 'process=auto'+'&query=' + query ,
			dataType: 'JSON',
			async: true,
			success: function(data)
			{
				process(data);
			}
	 	});
	 },
	updater: function(selection)
	{
		var prod0=selection;
		var prod= prod0.split("|");
		var id_prod = prod[0];
		var proveedor = prod[1];

		$("#mostrar_proveedor").html(proveedor);
		$("#id_proveedor").val(id_prod);
	}
	});
	$(".i-checks").iCheck({
	  checkboxClass: "icheckbox_square-green",
	    radioClass: "iradio_square-green",
	});

	$('#inactivo').on('ifChecked', function(event)
	{
		$('#inactivo').iCheck('check');
		$('#inactivo').val("0");
	});
	$('#inactivo').on('ifUnchecked', function(event)
	{
		$('#inactivo').iCheck('uncheck');
		$('#inactivo').val("1");
	});
//

	$('#tiene_garantia').on('ifChecked', function(event)
	{
		$('#tiene_garantia').iCheck('check');
		$('#tiene_garantia').val("1");
		$("#tg").attr("hidden", false);
	//	$('#datos_garantia').show();

	});
	$('#tiene_garantia').on('ifUnchecked', function(event)
	{
		$('#tiene_garantia').iCheck('uncheck');
		$('#tiene_garantia').val("0");
		$("#tg").attr("hidden", true);
	//	$('#datos_garantia').hide();
	});
});
$(function ()
{
	//binding event click for button in modal form
	$(document).on("click", "#btnDelete", function(event)
	{
		deleted();
	});
	// Clean the modal form
	/*$(document).on('hidden.bs.modal', function(e)
	{
		var target = $(e.target);
		target.removeData('bs.modal').find(".modal-content").html('');
	});*/

});
$(document).on("click", "#duplicar", function(event)
{
	if($("#process").val() == "edited")
	{

		$("#process").val("insert");
		$("#origin").val("edited");
	}
	else
	{
		$("#origin").val("insert");
		$("#submit1").click();
	}
});


////////////////////////////////////////////////-----------------------------------------------------////////////////////////////////////////////////////////////

$(document).on("click", "#btn_confirmar", function()
{
	senddata();
})
$(document).on("click", "#btn_img", function()
{
	$('#viewModal').modal({backdrop: 'static',keyboard: false});
});
$("#btnGimg").click(function()
{
	$("#cerrar_ven").click();
});

$(".bb").click(function()
{
	$(".close").click();
});

function senddata()
{
	var process=$('#process').val();
	var nombre=$('#nombre').val();
	var array_json1 = new Array();
  $("#plus_detalle tr").each(function(index) {
    if (index >= 0)
		{
        var item = $(this).find(".item").val();
        var obj1 = new Object();
        obj1.item = item;
        //convert object to json string
        text=JSON.stringify(obj1);
        array_json1.push(text);
        //i = i + 1;
    }
  });

  var json_arr1 = '['+array_json1+']';
  console.log('jsons:'+json_arr1);
	if(process=='insert')
	{
		var id_producto=0;
		var urlprocess='agregar_modulo.php';
		var dataString='process='+process+'&nombre='+nombre;
	  dataString+='&items='+json_arr1;
	}

	$.ajax({
		type:'POST',
		url:urlprocess,
		data: dataString,
		dataType: 'json',
		success: function(datax)
		{
			if(datax.typeinfo == "Success")
			{
				display_notify(datax.typeinfo, 'Registro Ingresado con exito');
				setInterval("reload();",500);
			}
			else
			{
				display_notify(datax.typeinfo, datax.msg);
			}
		}
	});
}


$(document).on("click", ".delete", function() {
  $(this).parents("tr").remove();
});

$(document).on("click", ".Delete", function() {
  var parent = $(this).parents().get(0);
  $(parent).remove();
});

function reload()
{
	location.replace('admin_modulo.php');
}


$(document).on("click", "#plus_item", function()
{
	var linea = "<tr>";
	linea += "<td><input id='item' name='item' class='form-control item'></td>";
	linea += '<td class="Delete"><input  id="delprod" type="button" class="btn btn-danger fa pull-right"  value="&#xf1f8;"></td>';
	linea += "</tr>";
	$("#plus_detalle").append(linea);
});
