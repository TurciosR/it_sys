var urlprocess = "";
$(document).ready(function() {
  $('#inventable').arrowTable();
  urlprocess = $('#urlprocess').val();
  id_pedido = $('#id_pedido').val();
  $("#monto").numeric({
    negative: false,
    decimalPlaces: 2
  });
  //evento que captura el texto al pegar y lo envia a otro evt de busqueda de barcode
  /*$("#barcode").bind('paste', function(e) {
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

  });*/

  $('.select2').select2({
    placeholder: {
      id: '-1',
      text: 'Seleccione'
    },
    allowClear: true
  });

  /*
  $("#barcode").typeahead({
    source: function(query, process) {
      $.ajax({
        url: 'pedidos.php',
        type: 'POST',
        data: 'process=productos&query=' + query,
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

      searchBarcode(id_prod);

    }

  });
  */
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


  $("#proveedor").typeahead({
    source: function(query, process) {
      $.ajax({
        url: 'pedidos.php',
        type: 'POST',
        data: 'process=proveedores&query=' + query,
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
      var proveedor = prod[1];

      $("#mostrar_proveedor").html(proveedor);
      $("#id_proveedor").val(id_prod);

      $.ajax({
        url: 'pedidos.php',
        type: 'POST',
        data: 'process=datos_proveedores&id_proveedor=' + id_prod,
        dataType: 'JSON',
        async: true,
        success: function(data) {
          var retencion1 = data.retencion1;
          var retencion10 = data.retencion10;
          var percepcion = data.percepcion;
          $('#retencion1').val(retencion1);
          $('#retencion10').val(retencion10);
          $('#percepcion').val(percepcion);
          totalfact();
        }
      });

    }

  });


  buscarPedido(id_pedido)
});

$(document).on('click', '#loadtable tbody tr', function(event) {
  var id_prod = $(this).find('td:eq(0)').find('h5').html();
console.log(id_prod);
  searchBarcode(id_prod);

});


$(document).on('click', '#btnSelect', function(event) {


});
$(document).on('change', '#id_proveedor', function(event) {

  id_prod=$("#id_proveedor").val();

  $.ajax({
    url: 'pedidos.php',
    type: 'POST',
    data: 'process=datos_proveedores&id_proveedor=' + id_prod,
    dataType: 'JSON',
    async: true,
    success: function(data) {
      var retencion1 = data.retencion1;
      var retencion10 = data.retencion10;
      var percepcion = data.percepcion;
      $('#retencion1').val(retencion1);
      $('#retencion10').val(retencion10);
      $('#percepcion').val(percepcion);
      totalfact();
    }
  });


});
//evento para buscar por el barcode
function searchBarcode(barcode) {
  addProductList(barcode, 1,1)
  //totalfact();
}

function buscarPedido(id_pedido) {
  //para cargar pedido y detalle pedido
  urlprocess = $('#urlprocess').val();

  var dataString = 'process=buscarpedido' + '&id_pedido=' + id_pedido;
  $.ajax({
    type: "POST",
    url: urlprocess,
    data: dataString,
    dataType: 'json',
    success: function(datax) {
      $.each(datax, function(key, value) {
        var arr = Object.keys(value).map(function(k) {
          return value[k]
        });
        var id_producto = arr[0];
        var cantidad = arr[1];
        console.log(id_producto + " " + cantidad)
        addProductList(id_producto, cantidad,0);
      });
    }
  });
}
$("#submit1").click(function() {
  senddata();
});
$(function() {
  //binding event click for button in modal form
  $(document).on("click", "#btnDelete", function(event) {
    deleted();
  });

});
// Agregar productos a la lista del inventario
function addProductList(id_prod, cantidad,tipe) {
  id_prod = $.trim(id_prod);
  cantidad = parseInt(cantidad)
  var id_prev = "";
  var id_new = id_prod;
  var id_previo = new Array();
  var filas = 0;
  $("#inventable tr").each(function(index) {
    if (index > 0) {
      var campo0, campo1, campo2, campo5;

      $(this).children("td").each(function(index2) {
        var isVisible = false;
        switch (index2) {
          case 0:
            campo0 = $(this).text();
            //isVisible = $(this).filter(":visible").length > 0;
            //  if (isVisible == true) {
            if (campo0 != undefined || campo0 != '') {
              id_previo.push(campo0);
            }
            //  }
            break;
        }
      });
      filas = filas + 1;
    } //if index>0
  });
  urlprocess = $('#urlprocess').val();
  var id_pedido=$('#id_pedido').val();
  if(tipe==0){
    var dataString = 'process=consultar_stock'+ '&id_pedido=' + id_pedido + '&id_producto=' + id_prod;
  }
  else
  {
    var dataString = 'process=consultar_stock2'+ '&id_pedido=' + id_pedido + '&id_producto=' + id_prod;
  }

  $.ajax({
    type: "POST",
    url: "editar_pedidos.php",
    data: dataString,
    dataType: 'json',
    success: function(data) {
      //  var cp = data.costo_prom;
      var color = data.color;
      var talla = data.talla;
      var exento = data.exento;
      var descrip = data.descrip;
      var ultcosto = data.ultcosto;
      var precio1 = data.precio1;
      var estilo = data.estilo;
      //  var existencias= data.existencias;
      //  var pv_base = data.pv_base;

      add = "";

      var tr_add = '<tr class="">';
      tr_add += "<td class='text-success '><input type='hidden'  id='exento' name='exento' value='" + exento + "'>" + id_prod + "</td>";
      tr_add += '<td  class="text-success ">' + descrip +'</td>';
      tr_add += '<td  class="text-success ">' + estilo + '</td>';
      tr_add += '<td  class="text-success ">' + color + '</td>';
      tr_add += '<td  class="text-success ">' + talla + '</td>';
      tr_add += "<td class='text-success '><div class=''><input type='text'  class='form-control decimal cantidades' id='cant' name='cant'  value='" + cantidad + "' style='width:80px;'></div></td>";
      tr_add += "<td class='text-success '><div class=''><input type='text'  class='form-control decimal cantidades' id='costo' name='costo'  value='" + ultcosto + "' style='width:80px;'></div></td>";
      tr_add += "<td class='text-success '><div class=''><input type='text'  class='form-control decimal cantidades' id='precio' name='precio'  value='" + precio1 + "' style='width:80px;'></div></td>";
      tr_add += '<td class="Delete"><input id="delprod" type="button" class="btn btn-danger fa"  value="&#xf1f8;"></td>';
      tr_add += '</tr>';
      //agregar columna a la tabla de facturacion
      var existe = false;
      var posicion_fila = 0;
      $.each(id_previo, function(i, id_prod_ant) {
        if (id_prod == id_prod_ant) {
          existe = true;
          posicion_fila = i;
          //$("#cant").numeric();
        }
      });
      if (existe == false) {
        $("#inventable tr:first-child").after(tr_add);
        totalfact();
        $(".decimal").numeric({
          negative: false
        });
      }
      if (existe == true) {
        $(".decimal").numeric();
        posicion_fila = posicion_fila + 2;
        setRowCant(posicion_fila);
        totalfact();
      }
    }
  });
  //  totalfact();
}


//Evento que se activa al perder el foco en precio de venta y cantidad:
$(document).on("blur", "#cant, #precio_compra, #descto", function() {
  $(this).removeClass('hoverTable2');

  totalfact();
  //$(".hoverTable").css("color", "#3F729B");
})

// reemplazar valores de celda cantidades
function setRowCant(rowId) {
  var cantidad_anterior = $('#inventable tr:nth-child(' + rowId + ')').find('td:eq(5)').find("#cant").val();
  var cantidad_nueva = parseFloat(cantidad_anterior) + 1;
  $('#inventable tr:nth-child(' + rowId + ')').find('td:eq(5)').find("#cant").val(cantidad_nueva);
  totalfact();
}
$(document).on("focus", "#cant, #precio_compra, #descto", function() {

  totalfact();
  $(this).addClass('hoverTable2');
})
$(document).on("blur", "#cant,#costo, #precio_compra, #descto", function() {
  $(this).removeClass('hoverTable2');
})
$(document).on("blur", "#inventable", function() {
  //$('#precio_compra').blur(function() {

  totalfact();
})
$(document).on("keyup", "#cant,#costo, #precio_compra, #descto", function() {

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

  totalfact();
});
//function to round 2 decimal places
function round(value, decimals) {
  return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}

//Funcion para recorrer la tabla completa y pasar los valores de los elementos a un array
function totalfact() {
  //  var urlprocess = $('#urlprocess').val();

  var i = 0,
    total = 0;
  totalcantidad = 0;
  cantidad = 0;
  costo = 0;
  precio = 0;
  filas = 0;
  items = 0;
  totalfactura = 0;
  var td1;
  var td2;
  var td3;
  var td4;
  var td5;
  var td6;

  $("#inventable  tr").each(function(index) {
    if (index >= 0) {
      var subtotal = 0;
      $(this).children("td").each(function(index2) {
        switch (index2) {
          case 5:
            cantidad = parseFloat($(this).find("#cant").val());
            if (isNaN(cantidad)) {
              cantidad = 0;
            }
            items = 1
            break;
          case 6:
            precio = parseFloat($(this).find("#costo").val());
            if (isNaN(precio)) {
              precio = 0;
            }
            break;

        }
      });
    }
    filas += items;
    totalcantidad += cantidad;
    totalfactura += round((precio * cantidad), 2);

  });

  retencion1 = $('#retencion1').val();
  retencion10 = $('#retencion10').val();
  percepcion = $('#percepcion').val();

  var monto_retencion1 = $('#monto_retencion1').val();
  var monto_retencion10 = $('#monto_retencion10').val();
  var monto_percepcion = $('#monto_percepcion').val();
  var iva = $('#porc_iva').val();

  total_retencion1=0;
  total_percepcion=0;
  total_retencion10=0;




  iva = round((totalfactura * iva), 4);

  if (totalfactura >= monto_percepcion)
    total_percepcion = round((totalfactura * percepcion), 4);
  if (totalfactura >= monto_retencion1)
    total_retencion1 = round((totalfactura * retencion1), 4);
  if (totalfactura >= monto_retencion10)
    total_retencion10 = round((totalfactura * retencion10), 4);

  totalfactura += total_percepcion;
  totalfactura += total_retencion1;
  totalfactura += total_retencion10;
  totalfactura += iva;

  totalfactura = round(totalfactura, 2);
  $('#totcant').html(totalcantidad);
  $('#monto').val(totalfactura);
  $('#items').val(filas);
  $('#pares').val(totalcantidad);

  //  $('#totaltexto').load(urlprocess,{'process': 'total_texto','total':total_final_mostrar});
}

function senddata() {
  //Calcular los valores a guardar de cada item del inventario
  var i = 0;
  urlprocess = $('#urlprocess').val();
  var precio_compra = 0;
  var precio_venta = 0;
  cantidad = 0;
  descto = 0;
  var subtotal = 0;
  subt_exento = 0;
  subt_gravado = 0;
  var StringDatos = "";
  var fecha_movimiento = $("#fecha2").val();
  var id_proveedor = $("#id_proveedor").val();
  var monto = $("#monto").val();
  var items = $('#items').val();
  var pares = $('#pares').val();
  var array_json = new Array();
  var verificar = 'noverificar';
  var verificador = [];
  $("#inventable>tbody tr").each(function(index) {
    if (index >= 0) {
      var campo0, campo1, campo2, campo3, campo4, campo5, campo6;

      $(this).children("td").each(function(index2) {
        switch (index2) {
          case 0:
            campo0 = $(this).text();
            break;
          case 1:
            campo1 = $(this).text();
            break;
          case 5:
            campo2 = $(this).find("#cant").val();
            if (isNaN(campo2) == false) {
              cantidad = parseFloat(campo2);
            } else {
              cantidad = 0;
            }
            if (cantidad === null)
              cantidad = 0;
            break;
          case 6:
            campo3 = $(this).find("#costo").val();
            if (isNaN(campo3) == false) {
              precio_compra = parseFloat(campo3);
            } else {
              precio_compra = 0;
            }
            if (precio_compra === null)
              precio_compra = 0;
            break;
          case 7:
            campo4 = $(this).find("#precio").val();
            if (isNaN(campo4) == false) {
              precio_venta = parseFloat(campo4);
            } else {
              precio_venta = 0;
            }
            if (precio_venta === null)
              precio_venta = 0;
            break;
        }
      });
      if (cantidad) {
        var obj = new Object();
        obj.id = campo0;
        obj.cantidad = cantidad;
        obj.ultcosto = precio_compra;
        obj.precio1 = precio_venta;
        //convert object to json string
        text = JSON.stringify(obj);
        array_json.push(text);
        i = i + 1;
      }
    }
  });

  json_arr = '[' + array_json + ']';

  var seleccion = 0;

  if (fecha_movimiento == undefined || fecha_movimiento == '') {
    seleccion = 0;
  } else {
    seleccion = 1;
  }
  if (seleccion == 0) {
    msg = 'Falta Fecha !';
  }

  var proveedor = 0;
  if (id_proveedor == undefined || id_proveedor == '' || id_proveedor == -1) {
    proveedor = 0;
  } else {
    proveedor = 1;
  }
  if (proveedor == 0) {
    msg = 'Falta Seleccionar proveedor !';
  }

  var montoval = 0;
  if (monto == undefined || monto == '') {
    montoval = 0;
  } else {
    montoval = 1;
  }
  if (montoval == 0) {
    msg = 'No ha seleccionado ningun producto!';
  }
  var id_pedido=$('#id_pedido').val();
  var dataString = 'process=insert'+ '&id_pedido=' + id_pedido  + '&cuantos=' + i + '&json_arr=' + json_arr + '&fecha_movimiento=' + fecha_movimiento;
  dataString += '&pares=' + pares + '&items=' + items + '&id_proveedor=' + id_proveedor + '&monto=' + monto;

  if (seleccion == 1 && proveedor == 1 && montoval == 1) {
    $.ajax({
      type: 'POST',
      url: urlprocess,
      data: dataString,
      dataType: 'json',
      success: function(datax) {
        process = datax.process;
        display_notify(datax.typeinfo, datax.msg);
        setInterval("reload1();", 250);
      }
    });

  } else {
    var typeinfo = 'Warning';
    display_notify(typeinfo, msg);
  }

}

function reload1() {
  urlprocess2 = 'admin_pedido_rangos.php';
  location.href = urlprocess2;
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
