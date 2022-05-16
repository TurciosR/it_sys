var urlprocess = "";
$(document).ready(function() {
  $('#inventable').arrowTable();
  $('#loadtable').arrowTable();

  urlprocess = $('#urlprocess').val();
  $('.loading-overlay').hide();
  $(".datepick2").datepicker({
    format: "dd-mm-yyyy"
  });
  $('#select_proveedores').select2();
  $('#select_colores').select2();
  $('#select_pedidos').select2();
  //$("#destino").select2();

  $('.select2').select2({
    placeholder: {
      id: '-1',
      text: 'Seleccione'
    },
    allowClear: true
  });

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

    if (e.which == 120) { //F9 guarda factura
      e.preventDefault();
      if ($('#total_final').text() != 0 && $("#items").val() > 0) {
        senddata();
        $("#inventable").find("tr:gt(0)").remove();
      } else {
        display_notify('Error', 'Debe haber al menos un producto registrado');
      }
    }
    if (e.which == 112) { //F1 Imprimir modal
      if ($(".modal-body #facturado").val() != "" || $(".modal-body #fact_num").html() != "") {
        imprime1();
      }
    }
  });
  // fin Teclas de funcion
}); //end document ready

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
// Agregar productos a la lista del inventario
function agregar_producto(id_prod) {
  console.log("ok");
  id_prod = $.trim(id_prod);
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
  var dataString = 'process=consultar_stock&id_producto=' + id_prod;

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
        tr_add += "<td class='cell100 column10 text-success'><input type='hidden'  id='id_producto' name='id_producto' value='" + id_prod + "'>" + id_prod + "</td>";
        tr_add += '<td  class="cell100 column55 text-success">' + descripcion + '</td>';
        tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control precio_compra' id='precio_compra' name='precio_compra' value='" + costo + "' style='width:80px;'></div></td>";
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
          $("#inventable").append(tr_add);
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

      var precio_compra = $(this).parents("tr").find("#precio_compra").val();
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
            subt_gravado = parseFloat($(this).find("#precio_compra").val());;
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
  console.log(total_gravado);
  //$('#total_exento').html(total_exento_mostrar);
  //$('#total_exenta').html(total_exento_mostrar);
  $('#total_iva').html(total_iva_mostrar);
  total_gravado_iva = total_gravado + total_iva;
  total_gravado_iva_mostrar = total_gravado_iva.toFixed(2);
  $('#total_gravado_iva').html(total_gravado_iva_mostrar); //total gravado con iva
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
  var total_final = (total_iva + total_gravado ) + (total_percepcion);
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
  //Calcular los valores a guardar de cada item del inventario
  var i = 0;
  //var precio_compra = 0;
  //cantidad = 0;
  //descto = 0;
  var subtotal = 0;
  subt_exento = 0;
  subt_gravado = 0;
  var StringDatos = "";
  var total_compras = $('#total_general').text();
  var fecha_movimiento = $("#fecha2").val();
  var numero_doc = $("#numero_doc2").val();
  var id_tipodoc = $("select#select_documento option:selected").val();
  var id_proveedor = $("select#select_proveedores option:selected").val();
  var total_gravado = $('#total_gravado_sin_iva').text();
  //var total_exento = $('#total_exento').text();
  var total_iva = $('#total_iva').text();
  //var total_retencion = $('#total_retencion').text();
  var total_percepcion = $('#total_percepcion').text();
  var numero_dias = $("#dias_credito").val();
  //var id_pedido = $('#select_pedidos').val();
  //var items = $('#items').val();
  //var pares = $('#pares').val();
  var tipo_compra = $("#tipo_compra").val();
  var destino = $("#destino").val();
  var array_json = new Array();
  var verificar = 'noverificar';
  var verificador = [];

  $("#inventable>tbody  tr").each(function(index) {
    if (index >= 0)
    {
      var cantidad = $(this).find("#cant").val();
      var precio_compra = $(this).find("#precio_compra").val();
      var id_producto = $(this).find("#id_producto").val();
      var vence = $(this).find("#vence").val();
      console.log(cantidad);
      console.log(precio_compra);
        var obj = new Object();
        obj.id_producto = id_producto;
        obj.precio_compra = precio_compra;
        obj.cantidad = cantidad;
        obj.vence = vence;
        //convert object to json string
        text = JSON.stringify(obj);
        array_json.push(text);
        i = i + 1;
    }
  });
  var json_arr = '[' + array_json + ']';
  console.log('jsons:' + json_arr);

  var seleccion = 0;
  var seleccion_doc = 0;
  var sel_tipo_doc = 0;
  if (numero_doc == undefined || numero_doc == '') {
    seleccion_doc = 0;
  } else {
    seleccion_doc = 1;
  }
  if (!id_proveedor || id_proveedor == '-1') {
    seleccion = 0;
  } else {
    seleccion = 1;
  }
  if (!id_tipodoc || id_tipodoc == '-1') {
    sel_tipo_doc = 0;
  } else {
    sel_tipo_doc = 1;
  }
  if (seleccion == 0) {
    msg = 'Falta digitar Proveedor !';
  }
  if (seleccion_doc == 0) {
    msg = 'Falta digitar Numero de Documento de Compra!';
  }
  if (sel_tipo_doc == 0) {
    msg = 'Falta digitar Tipo  de Documento de Compra!';
  }

  var dataString = 'process=insert' + '&cuantos=' + i + '&json_arr=' + json_arr;
  dataString += '&fecha_movimiento=' + fecha_movimiento + '&numero_doc=' + numero_doc + '&id_tipodoc=' + id_tipodoc
  dataString += '&id_proveedor=' + id_proveedor + '&total_compras=' + total_compras;
  dataString += '&total_gravado=' + total_gravado;
  dataString += '&total_iva=' + total_iva;
  dataString += '&total_percepcion=' + total_percepcion;
  dataString += '&tipo_compra=' + tipo_compra;
  dataString += '&destino=' + destino;
  dataString += '&numero_dias=' + numero_dias;

  if (seleccion == 1 && seleccion_doc == 1 && sel_tipo_doc == 1) {
    $.ajax({
      type: 'POST',
      url: urlprocess,
      data: dataString,
      dataType: 'json',
      success: function(datax) {
        process = datax.process;
        display_notify(datax.typeinfo, datax.msg);
        if(datax.typeinfo == "Success")
        {
          $("#inventable").html("");
          $("#submit1").attr("disabled", true);
          setInterval("reload1();", 1500);
        }
      }
    });

  } else {
    var typeinfo = 'Warning';
    display_notify(typeinfo, msg);
  }
}

function reload1() {
  location.href = urlprocess;
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
