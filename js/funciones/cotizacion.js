var urlprocess = "";
function generar(){
	fechai=$("#fini").val();
	fechaf=$("#fin").val();
	dataTable = $('#editable2').DataTable().destroy()
	dataTable = $('#editable2').DataTable( {
			"pageLength": 50,
			"order":[ 0, 'desc' ],
			"processing": true,
			"serverSide": true,
			"ajax":{
					url :"admin_cotizacion_dt.php?fechai="+fechai+"&fechaf="+fechaf, // json datasource
					//url :"admin_factura_rangos_dt.php", // json datasource
					//type: "post",  // method  , by default get
					error: function(){  // error handling
						$(".editable2-error").html("");
						$("#editable2").prepend('<tbody class="editable2_grid-error"><tr><th colspan="3">No se encontró información segun busqueda </th></tr></tbody>');
						$("#editable2_processing").css("display","none");
						$( ".editable2-error" ).remove();
						}
					}
				} );

		dataTable.ajax.reload()
	//}
}
$(document).ready(function() {
  //$('#inventable').arrowTable();
  //$('#loadtable').arrowTable();

  generar();
	if($("#process").val() == "editar")
  {
    totales();
  }
	$(".sel").select2({
			tags: true,
			createTag: function(tag) {
				if (tag.term.match(/^\d*\.?\d*$/))
					return {
						text: tag.term,
						id: 1
					}
			}
	});
  urlprocess = $('#urlprocess').val();
  $('.loading-overlay').hide();
  $(".datepick2").datepicker({
    format: "dd-mm-yyyy"
  });
  $('#select_proveedores').select2();
  $('#select_colores').select2();
  $('#select_pedidos').select2();
  $('#tipo_doc').select2();
  $('#con_pago').select2();
  //$("#destino").select2();



  $("#producto_buscar").typeahead({
    source: function(query, process) {
      $.ajax({
        url: 'autocomplete_producto.php',
        type: 'POST',
        data: 'query=' + query,
        dataType: 'JSON',
        async: true,
        success: function(data) {
          process(data);
        }
      });
    },
    updater: function(selection) {
      var prod0 = selection;
      var prod = prod0.split("|");
      var id_prod = prod[0];
      var descrip = prod[1];
      agregar_producto(id_prod);
    }
  });

  $("#cliente").typeahead({
    source: function (query, process) {
      $.ajax({
        type: 'POST',
        url: 'cliente_autocomplete.php',
        data: 'query=' + query,
        dataType: 'JSON',
        async: true,
        success: function (data) {
          process(data);
        }
      });
    },
    updater: function (selection) {
      var prod0 = selection;
      var prod = prod0.split("|");
      var id_prod = prod[0];
      var descrip = prod[1];
      $("#id_cliente").val(id_prod);
      $("#text_cliente").text(descrip);
      //datos_clientes();
      // agregar_producto_lista(id_prod, descrip, isbarcode);
    }
  });

	$("#servicio_buscar").typeahead({
    source: function(query, process) {
      $.ajax({
        url: 'autocomplete_servicio.php',
        type: 'POST',
        data: 'query=' + query,
        dataType: 'JSON',
        async: true,
        success: function(data) {
          process(data);
        }
      });
    },
    updater: function(selection) {
      var ser0 = selection;
      var ser = ser0.split("|");
      var id_ser = ser[0];
      var descrip = ser[1];
      agregar_servicio(id_ser);
    }
  });
  //Campos criterio de Busqueda
  $('#keywords').on('keyup', function(event) {
    var kw = $('#keywords').val();
    if (kw.length > 0 && event.which !== 8) {
      $("#loadtable").find("tr:gt(0)").remove();
      searchFilter();
    }
  });
  $('#estilo, #talla').on('keyup', function(event) {
    if (event.which !== 8) {
      $("#loadtable").find("tr:gt(0)").remove();
      searchFilter();
    }
  });
  $("#select_colores").on('change', function() {
    $("#loadtable").find("tr:gt(0)").remove();
    var color = $('#select_colores :selected').val();
    searchFilter();
  });

  //evento change para select proveedores
  $("#select_proveedores").change(function(event) {

    datos_proveedores();
    //cambio_preingreso();
    totalfact();
  });
  //traer valores por pedidos para agregarlos a lista de compras
  $("#select_pedidos").on('change', function() {
    id_pedido = $("#select_pedidos").val();
    $('#inventable tbody').html('');
    $('#loadtable').prop('hidden',false);
    $('#heads').prop('hidden',false);
    $('#heads2').prop('hidden',false);
    $('#content').addClass('widget-content');

    $('#keywords').prop('readOnly', '');
    $('#select_colores').prop('disabled', '');
    $('#barcode').prop('readOnly', '');
    $('#estilo').prop('readOnly', '');
    $('#talla').prop('readOnly', '');

    var barcode = valor;
    var dataString = 'process=buscarpedido' + '&id_pedido=' + id_pedido;
    $.ajax({
      type: "POST",
      url: "compras.php",
      data: dataString,
      dataType: 'json',
      success: function(datax) {
        $.each(datax, function(key, value) {
          var arr = Object.keys(value).map(function(k) {
            return value[k]
          });
          var id_producto = arr[0];
          var cantidad = arr[1];
          addProductList(id_producto, cantidad,0);
          $('#inventable tbody').html('');
          $('#keywords').prop('readOnly', 'true');
          $('#select_colores').prop('disabled', 'true');
          $('#barcode').prop('readOnly', 'true');
          $('#estilo').prop('readOnly', 'true');
          $('#talla').prop('readOnly', 'true');
          $('#loadtable').prop('hidden','true');
          $('#heads').prop('hidden','true');
          $('#heads2').prop('hidden','true');
          $('#content').removeClass('widget-content');
        });
      }
    });
    totalfact();
  });

  //evento que captura el texto al pegar y lo envia a otro evt de busqueda de barcode
  var valor = "";
  $("#barcode").bind('paste', function(e) {
    var pasteData = e.originalEvent.clipboardData.getData('text')
    valor = $(this).val();
    if (pasteData.length >= 2) {
      searchBarcode(pasteData);
    }
  })
  //evento al keyup para buscar si el barcode es de longitud mayor igual a 1 caracteres
  $('#barcode').on('keyup', function(event) {
    if (event.which && this.value.length >= 2 && event.which === 13) {
      valor = $(this).val();
      $('#barcode').val(valor)
      searchBarcode($(this).val());
      $('#barcode').val("");
      $('#barcode').focus();
    }
  });


  $("#submit1").click(function() {
    senddata();
  });
  $(document).keydown(function(e) {

    if (e.which == 113) { //F9 guarda factura
      senddata();
    }
		/*
    if (e.which == 112) { //F1 Imprimir modal
      if ($(".modal-body #facturado").val() != "" || $(".modal-body #fact_num").html() != "") {
        imprime1();
      }
    }*/
		if (e.which == 119) { //F9 guarda factura
      $("#add_cliente").click();
    }
		if (e.which == 115) { //F9 guarda factura
				reload1();
    }
  });
  // fin Teclas de funcion
}); //end document ready

$(document).on('change', "#con_pago", function()
{
  var valor = $(this).val();
  if(valor == "CON")
  {
    $("#caja_dias").attr("hidden", true);
    $("#dias_credito").val("");
  }
	if(valor == "CHE")
  {
    $("#caja_dias").attr("hidden", true);
    $("#dias_credito").val("");
  }
  if(valor == "CRE")
  {
    $("#caja_dias").attr("hidden", false);
    $("#dias_credito").val("");
  }
});
$(document).on("click", "#f_servicio", function()
{
  $("#caja_fp").attr("hidden", false);
  $("#caja_fs").attr("hidden", true);
  $("#servicio_buscar").attr("type", "text");
  $("#producto_buscar").attr("type", "hidden");
})
$(document).on("click", "#f_producto", function()
{
  $("#caja_fp").attr("hidden", true);
  $("#caja_fs").attr("hidden", false);
  $("#servicio_buscar").attr("type", "hidden");
  $("#producto_buscar").attr("type", "text");
})
//datos de proveedores
function datos_proveedores() {
  var id_proveedor = $("select#select_proveedores option:selected").val();
  var urlprocess = $('#urlprocess').val();
  dataString = {
    process: "datos_proveedores",
    id_proveedor: id_proveedor
  };
  $.ajax({
    type: 'POST',
    url: urlprocess,
    data: dataString,
    dataType: 'json',
    success: function(datax) {
      porc_percepcion = datax.percepcion;
      porc_retencion1 = datax.retencion1;
      porc_retencion10 = datax.retencion10;
      $("#porc_retencion1").val(porc_retencion1);
      $("#porc_retencion10").val(porc_retencion10);
      $("#porc_percepcion").val(porc_percepcion);
      totalfact();
    }
  });
}

$(document).on("click", "#btnMostrar", function(event) {
	generar();
});

function searchBarcode(barcode) {
  addProductList(barcode, 1,1)
}

function searchFilter() {
  var keywords = $('#keywords').val();
  var id_color = $('#select_colores :selected').val();
  var talla = $('#talla').val();
  var estilo = $('#estilo').val();
  var barcode = $('#barcode').val();
  var limite = $('#limite').val();

  if (id_color == undefined) {
    id_color = -1;
  }
  getData(keywords, id_color, talla, estilo, barcode, limite)
}

function getData(keywords, id_color, talla, estilo, barcode, limite) {
  urlprocess = $('#urlprocess').val();
  $.ajax({
    type: 'POST',
    url: urlprocess,
    data: {
      process: 'traerdatos',
      keywords: keywords,
      id_color: id_color,
      talla: talla,
      estilo: estilo,
      barcode: barcode,
      limite: limite
    },
    beforeSend: function() {
      $('.loading-overlay').show();
    },
    success: function(html) {
      $('#mostrardatos').html(html);
      var cuantos = $('#cuantos_reg').val();
      if (cuantos > 0) {
        $('.loading-overlay').html("<span class='text-warning'>Buscando....</span>");
        $('#reg_count').val(cuantos);
        $('.loading-overlay').fadeOut("slow");
      } else {
        $('.loading-overlay').fadeOut("slow");
        $('#reg_count').val(0);
      }
    }
  });
}
//fin evento para buscar por el barcode


var isccf = false;
var iscof = true;
$(document).on("change", "#tipo_doc", function()
{
  var tipo_doc = $(this).val();
  if(tipo_doc == "CCF")
  {
	  $("#inventable tr").each(function()
	  {
	      $(this).find(".precios option").each(function()
	      {
	        valor = round($(this).val()/1.13, 4);
	        $(this).val(valor.toFixed(2));
	        $(this).text(valor.toFixed(2));
	      });
	      $(this).find(".precios").select2();
	      $(this).find(".precios").trigger("change");

	      var vs = $(this).find(".precios_ser").val();
	      var vsn = round(vs/1.13, 4);
	      $(this).find(".precios_ser").val(vsn.toFixed(2));
	      $(this).find(".precios_ser").trigger("keyup");
	  });
	  isccf = true;
	  iscof = false;
	}
	else
	{
	  if(isccf == true && iscof == false)
	  {
	    $("#inventable tr").each(function()
	    {
	        $(this).find(".precios option").each(function()
	        {
	          valor = $(this).val()*1.13;
	          $(this).val(valor.toFixed(2));
	          $(this).text(valor.toFixed(2));
	        });
	        $(this).find(".precios").select2();
	        $(this).find(".precios").trigger("change");

	        var vs = $(this).find(".precios_ser").val();
	        var vsn = round(vs*1.13, 4);
	        $(this).find(".precios_ser").val(vsn.toFixed(2));
	        $(this).find(".precios_ser").trigger("keyup");
	    });
	    isccf = false;
	    iscof = false;
	  }
		if(isccf == false && iscof == true)
		{
			$("#inventable tr").each(function()
	    {
	        $(this).find(".precios option").each(function()
	        {
	          valor = $(this).val()*1.13;
	          $(this).val(valor.toFixed(2));
	          $(this).text(valor.toFixed(2));
	        });
	        $(this).find(".precios").select2();
	        $(this).find(".precios").trigger("change");

	        var vs = $(this).find(".precios_ser").val();
	        var vsn = round(vs*1.13, 4);
	        $(this).find(".precios_ser").val(vsn.toFixed(2));
	        $(this).find(".precios_ser").trigger("keyup");
	    });
	    isccf = false;
	    iscof = false;
		}
	}
totalfact();
});


$(function() {
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

$(document).on("change", "#tipo_compra", function()
{
    var tipo = $(this).val();
    if(tipo == 0)
    {
      $("#caja_dias").attr("hidden", true);
    }
    if(tipo == 1)
    {
      $("#caja_dias").attr("hidden", false);
    }
});

$(document).on("keypress", "#precio_compra", function(evt)
{
  if(evt.keyCode == 13)
  {
    if($(this).val()!="")
    {
      var tr = $(this).parents("tr");
      tr.find("#cant").focus();
      //console.log("OK1");
    }
    else {
      display_notify("Error","Ingrese un precio");
    }
  }
})
$(document).on("keypress", "#cant", function(evt)
{
  if(evt.keyCode == 13)
  {
    if($(this).val()!="")
    {
      $("#producto_buscar").focus();
      //console.log("OK");
    }
    else {
      display_notify("Error","Ingrese un precio");
    }
  }
})
$(document).on('change', ".precios", function()
{
  totalfact();
});
// Agregar productos a la lista del inventario
function agregar_producto(id_prod) {
  console.log("ok");
  id_prod = $.trim(id_prod);
	var tipo_doc = $("#tipo_doc").val();
  //cantidad = parseInt(cantidad)
  var id_prev = "";
  var id_new = id_prod;
  var id_previo = new Array();
  var filas = 0;
  var id_previo = new Array();
  $("#inventable>tbody  tr").each(function (index) {
      if (index >= 0) {
        var campo0 = "";
        $(this).children("td").each(function (index2) {
          switch (index2) {
            case 0:
              campo0 = $(this).text();
              if (campo0 != undefined || campo0 != '') {
                id_previo.push(campo0);
              }
              break;
          }
        });
        filas = filas + 1;
      } //if index>0
  });
  urlprocess = $('#urlprocess').val();


  var id_pedido = $("#select_pedidos").val();
  var dataString = 'process=consultar_stock&id_producto=' + id_prod + "&tipo_doc=" + tipo_doc;

    $.ajax({
      type: "POST",
      url: urlprocess,
      data: dataString,
      dataType: 'json',
      success: function(data) {
        var descripcion = data.descripcion;
        var costo = data.precio_venta;
        var precios = data.precios;
        var perecedero = data.perecedero;
        if(perecedero == 1)
        {
          var estado = "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control vence' id='vence' name='vence'  value='' style='width:100px;'></div></td>";
        }
        else
        {
          var estado = "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='hidden'  class='form-control vence' id='vence' name='vence'  value='' style='width:100px;'></div></td>";
        }
        console.log(estado);
        var tr_add = '<tr class="row100 head" id=' + filas + '>';
        tr_add += "<td class='cell100 column10 text-success'><input type='hidden'  id='bandera' name='bandera' value='producto'><input type='hidden'  class='form-control descrip_ser' id='descripcion_ser' name='descripcion_ser' value='''><input type='hidden'  id='id_producto' name='id_producto' value='" + id_prod + "'>" + id_prod + "</td>";
        tr_add += '<td  class="cell100 column55 text-success">' + descripcion + '</td>';
        tr_add += "<td class='cell100 column10 text-success'>"+precios+"</td>";
        //tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'>"+precios+"</div></td>";
        tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value='1' style='width:60px;'></div></td>";
        tr_add += estado
        tr_add += '<td class="Delete cell100 column5 text-success"><input  id="delprod" type="button" class="btn btn-danger fa pull-right"  value="&#xf1f8;"></td>';
        tr_add += '</tr>';
        //agregar columna a la tabla de facturacion
        var existe = false;
        var posicion_fila = 0;
        var posicion_fila2 = 0;
        $.each(id_previo, function(i, id_prod_ant) {
          if (id_prod == id_prod_ant) {
            existe = true;
            posicion_fila = i;
            posicion_fila2 = posicion_fila + 1
            setRowCant(posicion_fila2);
          }
        });
        console.log(existe);
        if (existe == false) {
          $("#first").remove();
          $("#inventable").prepend(tr_add);
          $("#inventable>tbody #"+filas).find(".precio_compra").focus();
          $(".decimal").numeric();
          $(".sel").select2({
              tags: true,
              createTag: function(tag) {
                if (tag.term.match(/^\d*\.?\d*$/))
                  return {
                    text: tag.term,
                    id: 1
                  }
              }
          });
          $(".sel2").trigger('change');
          $(".vence").datepicker({
            format: "dd-mm-yyyy"
          });
          //$('.btn-danger').prop('disabled', 'true')
          //$('.decimal').prop('readOnly', 'true')
        }
        totales();
        totalfact();
      }
    });
}

function agregar_servicio(id_ser) {
  console.log("ok");
	var tipo_doc = $("#tipo_doc").val();
  id_ser = $.trim(id_ser);
  //cantidad = parseInt(cantidad)
  var id_prev = "";
  var id_new = id_ser;
  var id_previo = new Array();
  var filas = 0;
  var id_previo = new Array();
  $("#inventable tr").each(function (index) {
      if (index >= 0) {
        var campo0 = "";
        $(this).children("td").each(function (index2) {
          switch (index2) {
            case 0:
              campo0 = $(this).text();
              if (campo0 != undefined || campo0 != '') {
                id_previo.push(campo0);
              }
              break;
          }
        });
        filas = filas + 1;
      } //if index>0
  });
  urlprocess = $('#urlprocess').val();


  var id_pedido = $("#select_pedidos").val();
  var dataString = 'process=consultar_servicio&id_servicio=' + id_ser + "&tipo_doc=" + tipo_doc;

    $.ajax({
      type: "POST",
      url: urlprocess,
      data: dataString,
      dataType: 'json',
      success: function(data) {
        var descripcion = data.descripcion;
        var stock = data.stock;
        var precios = data.precio;

        var tr_add = '<tr class="row100 head" id=' + filas + '>';
        tr_add += "<td class='cell100 column10 text-success'><input type='hidden'  id='bandera' name='bandera' value='servicio'><input type='hidden'  id='id_producto' name='id_producto' value='" + id_ser + "'>" + id_ser + "</td>";
        tr_add += "<td  class='cell100 column55 text-success'><input type='hidden'  class='form-control descripcion_ser' id='descripcion_ser' name='descripcion_ser' value='''>" + descripcion + "</td>";
        tr_add += "<td class='cell100 column10 text-success'><input type='text'  class='form-control precios_ser' id='precios' name='precios' value='"+precios+"' style='width:90px;'></td>";
        //tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'>"+precios+"</div></td>";
        tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value='1' style='width:60px;'></div></td>";
        tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='hidden'  class='form-control vence' id='vence' name='vence'  value='' style='width:100px;'></div></td>";
        tr_add += '<td class="Delete cell100 column5 text-success"><input  id="delprod" type="button" class="btn btn-danger fa pull-right"  value="&#xf1f8;"></td>';
        tr_add += '</tr>';
        //agregar columna a la tabla de facturacion
        console.log(tr_add);
        var existe = false;
        var posicion_fila = 0;
        var posicion_fila2 = 0;
        $.each(id_previo, function(i, id_prod_ant) {
          if (id_ser == id_prod_ant) {
            existe = true;
            posicion_fila = i;
            posicion_fila2 = posicion_fila + 1
            setRowCant(posicion_fila2);
          }
        });
        console.log(existe);
        if (existe == false) {
          $("#inventable").prepend(tr_add);
          $("#inventable #"+filas).find(".precio_compra").focus();
          $(".decimal").numeric();
          $(".precios_ser").numeric();
          $(".sel").select2({
              tags: true,
              createTag: function(tag) {
                if (tag.term.match(/^\d*\.?\d*$/))
                  return {
                    text: tag.term,
                    id: 1
                  }
              }
          });
          $(".sel2").trigger('change');
          $(".vence").datepicker({
            format: "dd-mm-yyyy"
          });
          //$('.btn-danger').prop('disabled', 'true')
          //$('.decimal').prop('readOnly', 'true')
        }
        totales();
        totalfact();
      }
    });
}

$(document).on("click", ".facturar", function()
{
	var id_cotizacion = $(this).attr("id_cotizacion");
	$("#id_cotizacion").val(id_cotizacion);
	$('#frm1').submit();
});

$(document).on("click","#plus_servicio", function()
{
  var filas = 0;
  var id_previo = new Array();
  $("#inventable tr").each(function (index) {
      if (index >= 0) {
        var campo0 = "";
        $(this).children("td").each(function (index2) {
          switch (index2) {
            case 0:
              campo0 = $(this).text();
              if (campo0 != undefined || campo0 != '') {
                id_previo.push(campo0);
              }
              break;
          }
        });
        filas = filas + 1;
      } //if index>0
  });
	var tr_add = '<tr class="row100 head" id=' + filas + '>';
	tr_add += "<td class='cell100 column10 text-success'><input type='hidden'  id='bandera' name='bandera' value='servicio'><input type='hidden'  id='id_producto' name='id_producto' value='nuevo_servicio'>1</td>";
	tr_add += "<td  class='cell100 column55 text-success'><input type='text'  class='form-control descripcion_ser' id='descripcion_ser' name='descripcion_ser' value='''></td>";
	tr_add += "<td class='cell100 column10 text-success'><input type='text'  class='form-control precios_ser' id='precios' name='precios' value='' style='width:90px;'></td>";
	//tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'>"+precios+"</div></td>";
	tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value='' style='width:60px;'></div></td>";
	tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='hidden'  class='form-control vence' id='vence' name='vence'  value='' style='width:100px;'></div></td>";
	tr_add += '<td class="Delete cell100 column5 text-success"><input  id="delprod" type="button" class="btn btn-danger fa pull-right"  value="&#xf1f8;"></td>';
	tr_add += '</tr>';
  console.log(tr_add);
  $("#inventable").prepend(tr_add);
  $("#inventable #"+filas).find(".descripcion_ser").focus();
  $(".decimal").numeric();
})


$(document).on("click", "#btnAdd", function()
{
	//console.log("OK");
	var nombre = $("#nombre").val();
	var direccion = $("#direccion").val();
	var departamento = $("#departamento").val();
	var municipio = $("#municipio").val();
	var categoria = $("#categoria").val();
	var telefono = $("#telefono").val();
	var email = $("#email").val();

	var dataString = "process=insert_cliente&nombre="+nombre+"&direccion="+direccion+"&departamento="+departamento+"&municipio="+municipio+"&categoria="+categoria+"&telefono="+telefono+"&email="+email;

	if(nombre != "")
	{
		if(direccion != "")
		{
			if(telefono != "")
			{
				$.ajax({
					type: 'POST',
					url: "cotizacion.php",
					data: dataString,
					dataType: 'json',
					success: function(datax) {
						if (datax.typeinfo == "Success") {
							var id_cliente = datax.id_cliente;
							$("#id_cliente").val(id_cliente);
				      $("#text_cliente").text(nombre);
							$('#closeM').click();
							//setInterval("reload1();", 1000);
						}
						else
						{
							display_notify(datax.typeinfo, datax.msg);
						}
					}
				});
			}
			else
			{
				display_notify("Error", "Debe de agregar el telefono");
			}
		}
		else
		{
			display_notify("Error", "Debe de agregar la direccion");
		}
	}
	else
	{
		display_notify("Error", "Debe de ingresar el nombre del cliente");
	}

});
// reemplazar valores de celda cantidades
function setRowCant(rowId) {
  var cantidad_anterior = $('#inventable tr:nth-child(' + rowId + ')').find('td:eq(3)').find("#cant").val();
  var cantidad_nueva = parseFloat(cantidad_anterior) + 1;
  $('#inventable tr:nth-child(' + rowId + ')').find('td:eq(3)').find("#cant").val(cantidad_nueva);
  //totales();
  //totalfact();
}
/*
$("#mostrardatos").on('click', '#btnSelect', function() {
  if ($('#seleccionados').hide()) {
    $('#seleccionados').show();
  }
  var id_prod=$(this).closest("tr").find('td:eq(0)').text();
  addProductList(id_prod,1);
});*/

$('#loadtable tbody ').on('blur', 'tr', function() {
  $(this).removeClass('hoverTable2');
})
$('#loadtable tbody ').on('focus', 'tr', function() {
  $(this).addClass('hoverTable2');
})
//navigate asign tabindex to tr
$('#loadtable ').on('click', 'tr', function() {
  document.onkeydown = checkKey;
  var id_prod = $(this).closest("tr").find('td:eq(0)').text();
  addProductList(id_prod, 1,1);
});
$('#loadtable ').on('focus', 'tr', function() {
  document.onkeydown = checkKey;
});

function checkKey(e) {
  var rowCount = $('#loadtable >tbody >tr').length;
  var event = window.event ? window.event : e;
  if (event.keyCode == 40) { //down
    var idx = $("tr:focus").attr("tabindex");
    $('#loadtable').find("tr[tabindex=" + idx + "]").removeClass('hoverTable2');
    idx++;
    if (idx >= rowCount) {
      idx = 0;
    }
    $("tr[tabindex=" + idx + "]").focus();
    $('#loadtable').find("tr[tabindex=" + idx + "]").addClass('hoverTable2');
  }
  if (event.keyCode == 38) { //up

    var idx = $("tr:focus").attr("tabindex");
    $('#loadtable').find("tr[tabindex=" + idx + "]").removeClass('hoverTable2');
    idx--;
    if (idx < 0) {
      idx = rowCount - 1;
    }
    $("tr[tabindex=" + idx + "]").focus();
    $('#loadtable').find("tr[tabindex=" + idx + "]").addClass('hoverTable2');
  }
  if (event.keyCode == 13) { //up
    var idx = $("tr:focus").attr("tabindex");
    var id_prod = $('#loadtable').find("tr[tabindex=" + idx + "]").find('td:first').text();
    addProductList(id_prod, 1,1); //agregar  producto a lista
  }
}
//end navigate
//Evento que se activa al perder el foco en precio de venta y cantidad:
$(document).on("blur", "#cant, #precio_compra, #descto", function() {
  $(this).removeClass('hoverTable2');
  totales();
  totalfact();
})
/*
$(document).on("focus", "#cant, #precio_compra, #descto", function() {
  totales();
  totalfact();
  $(this).addClass('hoverTable2');
});*/
$(document).on("blur", "#cant, #precio_compra, #descto", function() {
  $(this).removeClass('hoverTable2');
})
$(document).on("blur", "#inventable", function() {
  totales();
  totalfact();
})
$(document).on("keyup", "#cant, #precio_compra, #descto", function() {
  totales();
  totalfact();
})
$(document).on("focus", " #btnSelect", function() {
  $(this).addClass('btn-warning');
  $(this).removeClass('btn-primary');
})
$(document).on("blur", " #btnSelect", function() {
  $(this).removeClass('btn-warning');
  $(this).addClass('btn-primary');
})
$(document).on("focus", " #delprod", function() {
  $(this).addClass('btn-success');
  $(this).removeClass('btn-danger');
})
$(document).on("blur", " #delprod", function() {
  $(this).removeClass('btn-success');
  $(this).addClass('btn-danger');
})
// Evento que selecciona la fila y la elimina de la tabla
$(document).on("click", ".Delete", function() {
  var parent = $(this).parents().get(0);
  $(parent).remove();
  totales();
  totalfact();
});
//function to round 2 decimal places
function round(value, decimals) {
  return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}
//Calcular Totales del grid
function totales() {
  urlprocess = $('#urlprocess').val();

  var subtotal = 0;
  total = 0;
  var descto = 0;
  var subt_descto = 0;
  totalcantidad = 0;
  cantidad = 0;
  var total_dinero = 0;
  total_cantidad = 0;
  precio_compra = 0;
  precio_venta = 0;
  total_descuento = 0;
  var filas = 0;
  $("#inventable>tbody tr").each(function(index) {
    if (index >= 0) {
      var campo0, campo1, campo2, campo3, campo4, campo5;
      /*$(this).children("td").each(function(index2) {
        switch (index2) {
          case 0:
            campo0 = $(this).text();
            if (campo0 == undefined) {
              campo0 = '';
            }
            exento = $(this).find("#exento").val();
            break;
          case 1:
            campo1 = $(this).text();
            break;
          case 2:
            campo2 = $(this).find("#precio_compra").val();

            if (isNaN(campo2) == false) {
              precio_compra = parseFloat(campo2);
            } else {
              precio_compra = 0;
            }

            break;
          case 3:
            campo3 = $(this).find("#cant").val();
            if (isNaN(campo3) == false) {
              cantidad = parseFloat(campo3);
            } else {
              cantidad = 0;
            }
            break;

          case 4:
            campo4 = $(this).find("#descto").val();
            if (isNaN(campo4) == false) {
              descto = parseFloat(campo4);
            } else {
              descto = 0;
            }
            break;
        }
      });*/

      var precio_compra = $(this).parents("tr").find("#precios").val();
      var cantidad = $(this).parents("tr").find("#cant").val();

      if (isNaN(cantidad) == true) {
        cantidad = 0;
      }
      totalcantidad += cantidad;
      total_dinero += precio_compra;

      var subtotal = precio_compra * cantidad;

      if (isNaN(subtotal) == true) {
        subtotal = 0.00;
      }


      //console.log("subt_descto_mostrar:" + subt_descto_mostrar)
      //$(this).closest("tr").find("td:eq(5)").html(subtot_mostrar1)
      //$(this).closest("tr").find("#subt_descto").val(subt_descto_mostrar)
      //$(this).closest("tr").find("td:eq(6)").html(subtot_mostrar2)
      //total_descuento += subt_descto;
      total += subtotal;

    }
  });
  if (isNaN(total) == true) {
    total = 0;
  }
  total_descuento_mostrar = total_descuento.toFixed(2);
  $('#totdescto').html("<strong>" + total_descuento_mostrar + "</strong>");
  totalfact();
}


//Funcion para recorrer la tabla completa y pasar los valores de los elementos a un array
function totalfact() {
  //impuestos
  var iva = $('#porc_iva').val();
  var porc_retencion1 = $("#porc_retencion1").val();
  var porc_retencion10 = $("#porc_retencion10").val();
  var porc_percepcion = $("#porc_percepcion").val();

  var monto_retencion1 = $('#monto_retencion1').val();
  var monto_retencion10 = $('#monto_retencion10').val();
  var monto_percepcion = $('#monto_percepcion').val();
	var tipo_imp = $("#tipo_doc").val();
	//fin impuestos
  var urlprocess = $('#urlprocess').val();
  var i = 0,
    total = 0;
  totalcantidad = 0;
  var td1;
  var td2;
  var td3;
  var td4;
  var td5;
  var td6;
  var total_gravado = 0;
  var total_exento = 0;
  var subt_gravado = 0;
  var subt_exento = 0;
  var total_descto = 0;
  var subt_descto = 0;
  var StringDatos = '';
  var filas = 0;
  $("#inventable>tbody  tr").each(function(index) {
    if (index >= 0) {
      var subtotal = 0;
      $(this).children("td").each(function(index2) {
        switch (index2) {
          case 3:
            cantidad = parseFloat($(this).find("#cant").val());
            if (isNaN(cantidad)) {
              cantidad = 0;
            }
            break;
          case 2:
            subt_gravado = parseFloat($(this).find("#precios").val());
            if (isNaN(subt_gravado)) {
              subt_gravado = 0;
            }
            break;
        }
      });
    }
    filas += 1;
    total_gravado += (subt_gravado * cantidad);
    total += (subt_gravado * cantidad);
    totalcantidad += cantidad;
  });
  // IMPUESTOS
  total = round(total, 2);
  total_dinero = total.toFixed(2);
  total_gravado = round(total_gravado, 4);
  var total_gravado_mostrar = total_gravado.toFixed(2);
  var total_iva = round((total_gravado * iva), 4);
  var total_iva_mostrar = total_iva.toFixed(2);
  $('#total_gravado').html(total_gravado_mostrar);
  $('#total_gravado_sin_iva').html(total_gravado_mostrar);

	if(tipo_imp == "COF")
  {
    total_gravado_iva = total_gravado;
    total_gravado_iva_mostrar = total_gravado_iva.toFixed(2);
    $('#total_iva').html("0.00");
    $('#total_gravado_iva').html(total_gravado_iva_mostrar);
  }
  if(tipo_imp == "CCF")
  {
    total_gravado_iva = total_gravado + total_iva;
    total_gravado_iva_mostrar = total_gravado_iva.toFixed(2);
    $('#total_iva').html(total_iva_mostrar);
    $('#total_gravado_iva').html(total_gravado_iva_mostrar);
  }
  console.log(total_gravado);
  //$('#total_exento').html(total_exento_mostrar);
  //$('#total_exenta').html(total_exento_mostrar);

  var total_percepcion = 0
  var total_retencion1 = 0
  var total_retencion10 = 0
  if (total_gravado >= monto_percepcion)
    total_percepcion = total_gravado * porc_percepcion;
  if (total_gravado >= monto_retencion1)
    total_retencion1 = total_gravado * porc_retencion1;
  if (total_gravado >= monto_retencion10)
    total_retencion10 = total_gravado * porc_retencion10;

  total_percepcion_mostrar = total_percepcion.toFixed(2);
	if(tipo_imp == "COF")
  {
    total_final = total_gravado;
  }
  if(tipo_imp == "CCF")
  {
    total_final = (total_iva + total_gravado ) + (total_percepcion);
  }
  total_final_mostrar = total_final.toFixed(2);
  $('#total_percepcion').html(total_percepcion_mostrar);
  total_retencion1_mostrar = total_retencion1.toFixed(2);
  total_retencion10_mostrar = total_retencion10.toFixed(2);
  $('#total_retencion').html('0.00');
  if (parseFloat(total_retencion1) > 0.0)
    $('#total_retencion').html(total_retencion1_mostrar);
  if (parseFloat(total_retencion10) > 0.0)
    $('#total_retencion').html(total_retencion10_mostrar);
  total_descto_mostrar = total_descto.toFixed(2);
  $('#total_general').html(total_final_mostrar);
  $("#monto_pago").html(total_final_mostrar);
  $('#totcant').html(totalcantidad);
  //    $('#totdescto').html(total_descto_mostrar);
  $('#items').val(filas);
  $('#pares').val(totalcantidad);
  $('#totaltexto').load(urlprocess, {
    'process': 'total_texto',
    'total': total_final_mostrar
  });
}

function senddata() {
  //Obtener los valores a guardar de cada item facturado
  var procces = $("#process").val();
  var i = 0;
  var StringDatos = "";
  var id = '1';
  var id_empleado = 0;
  var id_cliente = $("#id_cliente").val();
  var atencion = $("#atencion").val();
  var items = $("#items").val();
  var msg = "";
  //IMPUESTOS
  var total_retencion = $('#total_retencion').text();
  var total_percepcion = $('#total_percepcion').text();
  var total_iva = $('#total_iva').text();
  var total_venta = $('#total_general').text();

  var vigencia = $('#vigencia').val();
  var dias_entrega = $('#dias_entrega').val();
  var tipo_doc = $('#tipo_doc').val();
  var fecha_movimiento = $("#fecha").val();
	var tipo_pago=$('#con_pago').val();
  var dias_credito=$("#dias_credito").val();

  var id_prod = 0;
  if (fecha_movimiento == '' || fecha_movimiento == undefined) {
    var typeinfo = 'Warning';
    msg = 'Seleccione una Fecha!';
    display_notify(typeinfo, msg);
  }
  var verificaempleado = 'noverificar';
  var verifica = [];
  var array_json = new Array();
  $("#inventable tr").each(function(index) {
      var id_producto = $(this).find("#id_producto").val();
      var precio = $(this).find("#precios").val();
      var cantidad = $(this).find("#cant").val();
      var vencimiento = $(this).find("#vence").val();
      var bandera = $(this).find("#bandera").val();
      var descripcion_ser = $(this).find("#descripcion_ser").val();

        var obj = new Object();
				obj.bandera = bandera;
				obj.descripcion_ser = descripcion_ser;
        obj.id_producto = id_producto;
        obj.precio = precio;
        obj.cantidad = cantidad;
        obj.vencimiento = vencimiento;
        //convert object to json string
        text = JSON.stringify(obj);
        array_json.push(text);
        i = i + 1;
  });
  json_arr = '[' + array_json + ']';
  if(procces == "insert")
  {
    var urlprocess = "cotizacion.php";
    var id_cotizacion = "";
  }
  else
  {
    var urlprocess = "editar_cotizacion.php";
    var id_cotizacion = $("#id_cotizacion").val();
  }
  var dataString = 'process='+ procces + '&cuantos=' + i + '&fecha_movimiento=' + fecha_movimiento;
  dataString += '&id_cliente=' + id_cliente + '&total_venta=' + total_venta;
  dataString += '&json_arr=' + json_arr;
  dataString += '&total_retencion=' + total_retencion;
  dataString += '&total_percepcion=' + total_percepcion;
  dataString += '&total_iva=' + total_iva;
  dataString += '&items=' + items;
  dataString += '&id_cotizacion=' + id_cotizacion;
  dataString += '&vigencia=' + vigencia;
  dataString += '&dias_entrega=' + dias_entrega;
  dataString += '&tipo_doc=' + tipo_doc;
  dataString += '&tipo_pago=' + tipo_pago;
  dataString += '&dias_credito=' + dias_credito;
  //dataString += '&tipo=' + tipo;
  var sel_vendedor = 1;

  if (id_cliente == "") {
    msg = 'Seleccione un Cliente!';
    sel_vendedor = 0;
  }
  if (vigencia == "") {
    msg = 'Ingrese el numero de dias de vigencia!';
    sel_vendedor = 0;
  }
  if (sel_vendedor == 1) {
    $("#inventable tr").remove();
    $.ajax({
      type: 'POST',
      url: urlprocess,
      data: dataString,
      dataType: 'json',
      success: function(datax) {
        display_notify(datax.typeinfo, datax.msg);
        if (datax.typeinfo == "Success") {
          setInterval("reload1();", 1000);
        }
      }
    });
  } else {
    display_notify('Warning', msg);
  }
}

function reload1() {
  location.href = "admin_cotizacion.php";
}

function cambio_preingreso(){
	var id = $('#select_proveedores').val();
	$("#select_pedidos").empty().trigger('change')
	$.post("compras.php", {process:'genera_select',id:id }, function(data){
					 $("#select_pedidos").html(data);
	 });
}

function deleted() {
  var id_producto = $('#id_producto').val();
  var dataString = 'process=deleted' + '&id_producto=' + id_producto;
  $.ajax({
    type: "POST",
    url: "borrar_producto.php",
    data: dataString,
    dataType: 'json',
    success: function(datax) {
      display_notify(datax.typeinfo, datax.msg);
      setInterval("location.reload();", 3000);
      $('#deleteModal').hide();
    }
  });
}
