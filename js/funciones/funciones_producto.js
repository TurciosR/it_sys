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

////////////////////////////////////////////////////////////////Funciones de camnbios de precios////////////////////////////////////////////////////////////////
$(document).on("click", ".td_porcentaje", function()
{
		var valor =  $(this).html();
		var valorx = $(this).parents("tr").find(".porcentaje_td").val();
		$(this).html('');
		var input = "<input type='text' class='form-control porcentaje_td' id='porcentaje_td' name='porcentaje_td' value=''>";
		$(this).html(input);
		$(this).find("#porcentaje_td").val(valorx);
		$(this).find("#porcentaje_td").focus();
});

$(document).on("keypress", ".porcentaje_td", function(e)
{
	var costo = parseFloat($(this).closest("tr").find(".costo_td").val());
	var a = $(this).parents("tr");
	if(e.keyCode == 13)
	{
		var valor =  parseFloat($(this).val());
		var valorx = valor.toFixed(2);
		var input = valorx+"%<input type='hidden' class='form-control porcentaje_td' id='porcentaje_td' name='porcentaje_td' value='"+valorx+"'>";
		//$(this).parents("tr").find(".td_porcentaje").text("%"+valorx);
		console.log(input);
		$(this).parents("tr").find(".td_porcentaje").html(input);
		//$(this).parents("tr").find("#porcentaje_td").attr("hidden", true);

		console.log(costo);
		var n_ganancia = (valorx/100) * costo;
		var n_total = costo + n_ganancia;
		var n_total_iva = (n_total * 1.13);
		var ganancia_input = "$ "+n_ganancia.toFixed(2)+"<input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value='"+n_ganancia.toFixed(2)+"'>";
		console.log(ganancia_input);
		a.find(".td_ganancia").html(ganancia_input);
		var total_input = "$ "+n_total.toFixed(2)+"<input type='hidden' class='form-control precio_td' id='precio_td' name='precio_td' value='"+n_total.toFixed(2)+"'>";
		var total_input_iva = "$ "+n_total_iva.toFixed(2)+"<input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='"+n_total_iva.toFixed(2)+"'>";
		console.log(total_input);
		a.find(".td_precio").html(total_input);
		a.find(".td_precio_iva").html(total_input_iva);
		//$(this).attr("hidden", true);
	}
});
$(document).on("blur", ".porcentaje_td", function(e)
{
		var costo = parseFloat($(this).closest("tr").find(".costo_td").val());
		var a = $(this).parents("tr");
		var valor =  parseFloat($(this).val());
		var valorx = valor.toFixed(2);
		var input = valorx+"%<input type='hidden' class='form-control porcentaje_td' id='porcentaje_td' name='porcentaje_td' value='"+valorx+"'>";
		//$(this).parents("tr").find(".td_porcentaje").text("%"+valorx);
		console.log(input);
		$(this).parents("tr").find(".td_porcentaje").html(input);
		//$(this).parents("tr").find("#porcentaje_td").attr("hidden", true);

		console.log(costo);
		var n_ganancia = (valorx/100) * costo;
		var n_total = costo + n_ganancia;
		var n_total_iva = (n_total * 1.13);
		var ganancia_input = "$ "+n_ganancia.toFixed(2)+"<input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value='"+n_ganancia.toFixed(2)+"'>";
		console.log(ganancia_input);
		a.find(".td_ganancia").html(ganancia_input);
		var total_input = "$ "+n_total.toFixed(2)+"<input type='hidden' class='form-control precio_td' id='precio_td' name='precio_td' value='"+n_total.toFixed(2)+"'>";
		var total_input_iva = "$ "+n_total_iva.toFixed(2)+"<input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='"+n_total_iva.toFixed(2)+"'>";
		console.log(total_input);
		a.find(".td_precio").html(total_input);
		a.find(".td_precio_iva").html(total_input_iva);
		//$(this).attr("hidden", true);
});

$(document).on("keypress", ".precio_td", function(e)
{
	var costo = parseFloat($(this).closest("tr").find(".costo_td").val());
	var a = $(this).parents("tr");
	if(e.keyCode == 13)
	{
		var valor =  parseFloat($(this).val());
		var valorx = valor.toFixed(2);
		var input = "$"+valorx+"<input type='hidden' class='form-control precio_td' id='precio_td' name='precio_td' value='"+valorx+"'>";
		//$(this).parents("tr").find(".td_porcentaje").text("%"+valorx);
		console.log(input);
		$(this).parents("tr").find(".td_precio").html(input);
		//$(this).parents("tr").find("#porcentaje_td").attr("hidden", true);

		console.log(costo);
		var n_ganancia = valor - costo;
		var n_total_iva = (valor * 1.13);
		var porcentaje = (n_ganancia / costo) * 100;
		var ganancia_input = "$ "+n_ganancia.toFixed(2)+"<input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value='"+n_ganancia.toFixed(2)+"'>";
		console.log(ganancia_input);
		a.find(".td_ganancia").html(ganancia_input);
		var total_input = porcentaje.toFixed(2)+"%<input type='hidden' class='form-control porcentaje_td' id='porcentaje_td' name='porcentaje_td' value='"+porcentaje.toFixed(2)+"'>";
		console.log(total_input);
		a.find(".td_porcentaje").html(total_input);

		var total_input_iva = "$ "+n_total_iva.toFixed(2)+"<input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='"+n_total_iva.toFixed(2)+"'>";
		a.find(".td_precio_iva").html(total_input_iva);
		//$(this).attr("hidden", true);
	}
});
$(document).on("blur", ".precio_td", function(e)
{
		var costo = parseFloat($(this).closest("tr").find(".costo_td").val());
		var a = $(this).parents("tr");
		var valor =  parseFloat($(this).val());
		var valorx = valor.toFixed(2);
		var input = "$"+valorx+"<input type='hidden' class='form-control precio_td' id='precio_td' name='precio_td' value='"+valorx+"'>";
		//$(this).parents("tr").find(".td_porcentaje").text("%"+valorx);
		console.log(input);
		$(this).parents("tr").find(".td_precio").html(input);
		//$(this).parents("tr").find("#porcentaje_td").attr("hidden", true);

		console.log(costo);
		var n_ganancia = valor - costo;
		var porcentaje = (n_ganancia / costo) * 100;
		var ganancia_input = "$ "+n_ganancia.toFixed(2)+"<input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value='"+n_ganancia.toFixed(2)+"'>";
		console.log(ganancia_input);
		a.find(".td_ganancia").html(ganancia_input);
		var total_input = porcentaje.toFixed(2)+"%<input type='hidden' class='form-control porcentaje_td' id='porcentaje_td' name='porcentaje_td' value='"+porcentaje.toFixed(2)+"'>";
		console.log(total_input);
		a.find(".td_porcentaje").html(total_input);
		var n_total_iva = (valor * 1.13);
		var total_input_iva = "$ "+n_total_iva.toFixed(2)+"<input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='"+n_total_iva.toFixed(2)+"'>";
		a.find(".td_precio_iva").html(total_input_iva);
		//$(this).attr("hidden", true);
});

$(document).on("click", ".td_precio", function()
{
		var valor =  $(this).html();
		var valorx = $(this).parents("tr").find(".precio_td").val();
		$(this).html('');
		var input = "<input type='text' class='form-control precio_td' id='precio_td' name='precio_td' value=''>";
		$(this).html(input);
		$(this).find("#precio_td").val(valorx);
		$(this).find("#precio_td").focus();
});

$(document).on("click", ".td_precio_iva", function()
{
		var valor =  $(this).html();
		var valorx = $(this).parents("tr").find(".precio_td_iva").val();
		$(this).html('');
		var input = "<input type='text' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value=''>";
		$(this).html(input);
		$(this).find("#precio_td_iva").val(valorx);
		$(this).find("#precio_td_iva").focus();
});

$(document).on("keypress", ".precio_td_iva", function(e)
{
	var costo = parseFloat($(this).closest("tr").find(".costo_td").val());
	var a = $(this).parents("tr");
	if(e.keyCode == 13)
	{
		var valor =  parseFloat($(this).val());
		var valorx = valor.toFixed(2);
		var input = "$"+valorx+"<input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='"+valorx+"'>";
		//$(this).parents("tr").find(".td_porcentaje").text("%"+valorx);
		console.log(input);
		$(this).parents("tr").find(".td_precio_iva").html(input);
		//$(this).parents("tr").find("#porcentaje_td").attr("hidden", true);

		console.log(costo);
		var n_ganancia = (valor/1.13) - costo;
		var n_total = (valor / 1.13);
		var porcentaje = (n_ganancia / costo) * 100;
		var ganancia_input = "$ "+n_ganancia.toFixed(2)+"<input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value='"+n_ganancia.toFixed(2)+"'>";
		console.log(ganancia_input);
		a.find(".td_ganancia").html(ganancia_input);
		var total_input = porcentaje.toFixed(2)+"%<input type='hidden' class='form-control porcentaje_td' id='porcentaje_td' name='porcentaje_td' value='"+porcentaje.toFixed(2)+"'>";
		console.log(total_input);
		a.find(".td_porcentaje").html(total_input);

		var total_input = "$ "+n_total.toFixed(2)+"<input type='hidden' class='form-control precio_td' id='precio_td' name='precio_td' value='"+n_total.toFixed(2)+"'>";
		a.find(".td_precio").html(total_input);
		//$(this).attr("hidden", true);
	}
});
$(document).on("blur", ".precio_td", function(e)
{
		var costo = parseFloat($(this).closest("tr").find(".costo_td").val());
		var a = $(this).parents("tr");
		var valor =  parseFloat($(this).val());
		var valorx = valor.toFixed(2);
		var input = "$"+valorx+"<input type='hidden' class='form-control precio_td' id='precio_td' name='precio_td' value='"+valorx+"'>";
		//$(this).parents("tr").find(".td_porcentaje").text("%"+valorx);
		console.log(input);
		$(this).parents("tr").find(".td_precio").html(input);
		//$(this).parents("tr").find("#porcentaje_td").attr("hidden", true);

		console.log(costo);
		var n_ganancia = valor - costo;
		var porcentaje = (n_ganancia / costo) * 100;
		var ganancia_input = "$ "+n_ganancia.toFixed(2)+"<input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value='"+n_ganancia.toFixed(2)+"'>";
		console.log(ganancia_input);
		a.find(".td_ganancia").html(ganancia_input);
		var total_input = porcentaje.toFixed(2)+"%<input type='hidden' class='form-control porcentaje_td' id='porcentaje_td' name='porcentaje_td' value='"+porcentaje.toFixed(2)+"'>";
		console.log(total_input);
		a.find(".td_porcentaje").html(total_input);
		var n_total_iva = (valor * 1.13);
		var total_input_iva = "$ "+n_total_iva.toFixed(2)+"<input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='"+n_total_iva.toFixed(2)+"'>";
		a.find(".td_precio_iva").html(total_input_iva);
		//$(this).attr("hidden", true);
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
	var origin=$('#origin').val();
	var id_proveedor=$('#id_proveedor').val();
	var barcode=$('#barcode').val();
	var descripcion=$('#descripcion').val();
	var comentario=$('#comentario').val();
	var ultcosto=$('#ultcosto').val();
	var descuento=$('#descuento').val();
	var marca=$('#marca').val();
	var modelo=$('#modelo').val();
  var tiene_garantia=$('#tiene_garantia').val();
	var serie = $("#serie").val();
	var categoria = $("#categoria").val();
	var descuento = $("#descuento").val();
	var tiempo_garantia = $("#tiempo_garantia").val();
	var periodo = $("#periodo").val();

	var i = 0;
	var array_json = new Array();
	$("#precios tr").each(function(index) {
      var costo = $(this).find("#costo_td").val();
      var costo_iva = $(this).find("#costo_td_iva").val();
      var porcentaje = $(this).find("#porcentaje_td").val();
      var ganancia = $(this).find("#ganancia_td").val();
      var precio = $(this).find("#precio_td").val();
      var precio_iva = $(this).find("#precio_td_iva").val();

      if (ganancia && precio && ganancia && costo)
			{
        var obj = new Object();
        obj.costo = costo;
        obj.costo_iva = costo_iva;
        obj.porcentaje = porcentaje;
        obj.precio = precio;
        obj.precio_iva = precio_iva;
        obj.ganancia = ganancia;
        //convert object to json string
        text = JSON.stringify(obj);
        array_json.push(text);
        i = i + 1;
      }
  });
	var json_arr = '[' + array_json + ']';

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
  console.log('jsons:'+json_arr);
  console.log('jsons:'+json_arr1);
	if(process=='insert')
	{
		var id_producto=0;
		var urlprocess='agregar_producto.php';
		var dataString='process='+process+'&id_producto='+id_producto+'&id_proveedor='+id_proveedor+'&barcode='+barcode;
		dataString+='&descripcion='+descripcion+'&comentario='+comentario+'&ultcosto='+ultcosto;
		dataString+='&descuento='+descuento+'&origin='+origin;
	  dataString+='&marca='+marca+'&modelo='+modelo+'&tiene_garantia='+tiene_garantia+"&precios="+json_arr+"&serie="+serie+"&categoria="+categoria;
	  dataString+='&descuento='+descuento+'&tiempo_garantia='+tiempo_garantia;
	  dataString+='&items='+json_arr1;
	  dataString+='&periodo='+periodo;
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
				if(origin == 'insert')
				{
					display_notify(datax.typeinfo, 'Registro Ingresado con exito');
					$("#origin").val("");
				}
				else if(origin == 'edited')
				{
					display_notify(datax.typeinfo, 'Registro Ingresado con exito');
					setInterval("location.replace('editar_producto.php?id_producto="+datax.id_producto+"');",500);
				}
				else
				{
					$("#id_id_p").val(datax.id_producto);
					img();
				}
			}
			else
			{
				display_notify(datax.typeinfo, datax.msg);
			}
		}
	});
}
$(document).on("click", "#btn_edatos", function()
{
	editar_datos();
})
function editar_datos()
{
	var process="editar_datos";
	var origin=$('#origin').val();
	var id_proveedor=$('#id_proveedor').val();
	var barcode=$('#barcode').val();
	var descripcion=$('#descripcion').val();
	var ultcosto=$('#ultcosto').val();
	var marca=$('#marca').val();
	var modelo=$('#modelo').val();
  var tiene_garantia=$('#tiene_garantia').val();
	var serie = $("#serie").val();
	var categoria = $("#categoria").val();
	var periodo = $("#periodo").val();

	var id_producto=$('#id_producto').val();
	var urlprocess='editar_producto.php';
	var inactivo = $("#inactivo").val();
	var descuento = $("#descuento").val();
	var tiempo_garantia = $("#tiempo_garantia").val();


	var dataString='process='+process+'&id_producto='+id_producto+'&id_proveedor='+id_proveedor+'&barcode='+barcode;
	dataString+='&descripcion='+descripcion;
	dataString+='&ultcosto='+ultcosto;
	dataString+='&marca='+marca+'&modelo='+modelo+'&tiene_garantia='+tiene_garantia+"&serie="+serie+"&categoria="+categoria;
	dataString+='&descuento='+descuento+'&tiempo_garantia='+tiempo_garantia;
	dataString+='&periodo='+periodo;

	$.ajax({
		type:'POST',
		url:urlprocess,
		data: dataString,
		dataType: 'json',
		success: function(datax)
		{
			display_notify(datax.typeinfo, datax.msg);
			if(datax.typeinfo == "Success")
			{

			}
		}
	});
}
$(document).on("click", "#btn_eprecio", function()
{
	editar_precios();
})
function editar_precios()
{
	var id_producto=$('#id_producto').val();
	var costo = $("#costo_e").val();
	var i = 0;
	var array_json = new Array();
	$("#precios tr").each(function(index) {
      var costo = $(this).find("#costo_td").val();
      var costo_iva = $(this).find("#costo_td_iva").val();
      var porcentaje = $(this).find("#porcentaje_td").val();
      var ganancia = $(this).find("#ganancia_td").val();
      var precio = $(this).find("#precio_td").val();
      var precio_iva = $(this).find("#precio_td_iva").val();

      if (ganancia && precio && ganancia && costo)
			{
        var obj = new Object();
        obj.costo = costo;
        obj.costo_iva = costo_iva;
        obj.porcentaje = porcentaje;
        obj.precio = precio;
        obj.precio_iva = precio_iva;
        obj.ganancia = ganancia;
        //convert object to json string
        text = JSON.stringify(obj);
        array_json.push(text);
        i = i + 1;
      }
  });
	var json_arr = '[' + array_json + ']';

	$.ajax({
		type:'POST',
		url:"editar_producto.php",
		data: "process=editar_precio&precios="+json_arr+"&cuantos="+i+"&id_producto="+id_producto+"&costo="+costo,
		dataType: 'json',
		success: function(datax)
		{
			display_notify(datax.typeinfo, datax.msg);
			if(datax.typeinfo == "Success")
			{

			}
		}
	});
}

$(document).on("click", "#btn_eimg", function()
{
	editar_img();
})

function editar_img()
{
	var form = $("#formulario_pro");
  var formdata = false;
  if(window.FormData)
  {
      formdata = new FormData(form[0]);
  }
  var formAction = form.attr('action');
  $.ajax({
      type        : 'POST',
      url         : "editar_producto.php",
      cache       : false,
      data        : formdata ? formdata : form.serialize(),
      contentType : false,
      processData : false,
      dataType : 'json',
      success: function(datax)
      {
        display_notify(datax.typeinfo, datax.msg);
        if (datax.typeinfo == "Success")
        {
					var img = datax.img;
					var cadena = '<img src="'+img+'" alt="" class="img-rounded" style="height: 300px; width: 350px;">';
					$("#caja_img").html(cadena);
					$(".fileinput-remove-button").click();
          //setInterval("reload();", 1000);
        }
      }
  });
}
function copiar_prod(id)
{
	$.ajax({
		type: 'POST',
		url: 'agregar_producto.php',
		data: 'process=consp&id='+id,
		dataType: 'JSON',
		success: function(datax)
		{
			$("#descripcion").val(datax.descripcion);
		//	$("#letra").select2({placeholder:datax.letra});
			$("#id_proveedor").val(datax.id_proveedor);
			$("#mostrar_proveedor").html(datax.proveedor);
			$("#comentario").val(datax.comentario);
			$("#ultcosto").val(datax.ultcosto);
			$("#precio1").val(datax.precio1);
			$("#precio2").val(datax.precio2);
			$("#precio3").val(datax.precio3);
			$("#descuento").val(datax.descuento);
		}
	});
}
function reload1()
{
	location.href = 'editar_productos.php?id_producto='+id_producto;
}
function deleted()
{
	var id_producto = $('#id_producto').val();
	var dataString = 'process=deleted' + '&id_producto=' + id_producto;
	$.ajax({
		type : "POST",
		url : "borrar_producto.php",
		data : dataString,
		dataType : 'json',
		success : function(datax)
		{
			display_notify(datax.typeinfo, datax.msg);
			if(datax.typeinfo == "Success")
			{
				setInterval("location.reload();", 1000);
				$('#deleteModal').hide();
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

$(document).on("keyup", "#ultcosto", function()
{
	precios();
})

$(document).on("click", "#plus_precio", function()
{
	var costo = parseFloat($(".ccos").val());
	var costo_iva = costo * 1.13;
	var lista = "";
	lista += "<tr>";

	lista += "<td style='text-align: right' class='td_costo'><input type='hidden' class='form-control costo_td' id='costo_td' name='costo_td' value='"+costo.toFixed(2)+"'>$"+costo.toFixed(2)+"</td>";
	lista += "<td style='text-align: right' class='td_costo_iva'><input type='hidden' class='form-control costo_td_iva' id='costo_td_iva' name='costo_td_iva' value='"+costo_iva.toFixed(2)+"'>$"+costo_iva.toFixed(2)+"</td>";
	lista += "<td style='text-align: right' class='td_precio'><input type='hidden' class='form-control precio_td' id='precio_td' name='precio_td' value=''></td>";
	lista += "<td style='text-align: right' class='td_precio_iva'><input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value=''></td>";
	lista += "<td style='text-align: right' class='td_porcentaje'><input type='hidden' class='form-control porcentaje_td' id='porcentaje_td' name='porcentaje_td' value=''></td>";
	lista += "<td style='text-align: right' class='td_ganancia'><input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value=''></td>";
	lista += "<td style='text-align: right'><input id='delete' type='button' class='btn btn-success fa delete'  value='&#xf1f8;'></td>";
	lista += "</tr>";

	if(!isNaN(costo))
	{
		$("#precios").append(lista);
	}
})

function precios()
{
	var costo = $("#ultcosto").val();
	var process = "precios";
	if(costo != "")
	{
		$.ajax({
			type : "POST",
			url : "agregar_producto.php",
			data : "process=precios&costo="+costo,
			success : function(datax)
			{
				$("#precios").html(datax);
			}
		});
	}

}

function img()
{
  var form = $("#formulario_pro");
  var formdata = false;
  if(window.FormData)
  {
      formdata = new FormData(form[0]);
  }
  var formAction = form.attr('action');
  $.ajax({
      type        : 'POST',
      url         : "agregar_producto.php",
      cache       : false,
      data        : formdata ? formdata : form.serialize(),
      contentType : false,
      processData : false,
      dataType : 'json',
      success: function(datax)
      {
        display_notify(datax.typeinfo, datax.msg);
        if (datax.typeinfo == "Success")
        {
          setInterval("reload();", 1000);
        }
      }
  });
}
function reload()
{
	location.replace('admin_productos.php');
}

$(document).on("keyup", "#costo_e", function()
{
	var costo = $(this).val();
	var i = 0;
	var array_json = new Array();
	$("#precios tr").each(function(index) {

      var porcentaje = $(this).find("#porcentaje_td").val();
      var obj = new Object();
      obj.porcentaje = porcentaje;
      //convert object to json string
      text = JSON.stringify(obj);
      array_json.push(text);
      i = i + 1;
  });
	var json_arr = '[' + array_json + ']';

	if(costo != "")
	{
		$.ajax({
			type : "POST",
			url : "editar_producto.php",
			data : "process=costo&costo="+costo+"&precios="+json_arr+"&cuantos="+i,
			success : function(datax)
			{
				$("#precios").html(datax);
			}
		});
	}

})

$(document).on("click", "#plus_item", function()
{
	var linea = "<tr>";
	linea += "<td><input id='item' name='item' class='form-control item'></td>";
	linea += '<td class="Delete"><input  id="delprod" type="button" class="btn btn-danger fa pull-right"  value="&#xf1f8;"></td>';
	linea += "</tr>";
	$("#plus_detalle").append(linea);
});

$(document).on("click", "#btn_ecaracte", function()
{
	editar_carac();
})

function editar_carac()
{
	var id_producto=$('#id_producto').val();
	var array_json = new Array();
	var i = 0;
  $("#plus_detalle tr").each(function(index) {
    if (index >= 0)
		{
        var item = $(this).find(".item").val();
        var obj = new Object();
        obj.item = item;
        //convert object to json string
        text=JSON.stringify(obj);
        array_json.push(text);
        i = i + 1;
    }
  });

  json_arr = '['+array_json+']';
  console.log('jsons:'+json_arr);

	$.ajax({
		type:'POST',
		url:"editar_producto.php",
		data: "process=editar_carac&items="+json_arr+"&cuantos="+i+"&id_producto="+id_producto,
		dataType: 'json',
		success: function(datax)
		{
			display_notify(datax.typeinfo, datax.msg);
			if(datax.typeinfo == "Success")
			{

			}
		}
	});
}
