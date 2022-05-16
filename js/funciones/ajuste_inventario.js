var urlscript = '';
$(document).ready(function() {
  urlscript = $("urlscript").val();
  $('#formulario').validate({
    rules: {
      descripcion: {
        required: true,
      },
      precio1: {
        required: true,
        number: true,
      },
    },
    submitHandler: function(form) {
      senddata();
    }
  });

  $('#tipo_entrada').select2();
  $("#producto_buscar").typeahead({
    source: function(query, process) {
      //var textVal=$("#producto_buscar").val();
      $.ajax({
        url: 'facturacion_autocomplete.php',
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
      var marca = prod[2];
      agregar_producto_lista(id_prod, descrip, marca);
    }
  });

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
// Agregar productos a la lista del inventario
function agregar_producto_lista(id_prod, descrip, costo) {
  var id_prev = "";
  var dataString = 'process=consultar_stock' + '&id_producto=' + id_prod;
  $.ajax({
    type: "POST",
    url: urlscript,
    data: dataString,
    dataType: 'json',
    success: function(data) {
      var filasx = data.filass;
      $("#inventable tr").each(function(index) {
        id_prev = $(this).closest('tr').children('td:first').text();
        if (id_prev == id_prod) {
          id_prod = "";
        }
      });
      $.each(filasx, function(i, valor) {
        var values = valor.split('|');
        var existencias = values[3];
         var unidad = values[2];
         var cp = values[1];

         if (unidad > 1) {
           descrip = descrip + ' (Presentacion de ' + unidad + ' unidades, Ultimo Precio compra: '+cp +')';
           td_subcant="<td><div class='col-xs-2'><input type='text'  class='form-control' id='subcant' name='subcant'  style='width:60px;'></div></td>";
         } else {
           descrip = descrip + ' (Presentacion de ' + unidad + ' unidad, Ultimo Precio compra: '+cp +')'
           td_subcant="<td><div class='col-xs-2'><input type='text'  class='form-control' id='subcant' name='subcant'  style='width:60px;' readonly></div></td>";
         }
        var tr_add = "";
        tr_add += '<tr>';
        tr_add += '<td>' + id_prod + '</td>';
        tr_add += '<td>' + descrip + '</td>';
        tr_add += '<td>' + existencias+'</td>';
        tr_add += "<td><input type='hidden' id='unidades' name='unidades' value='" + unidad + "'><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant'  style='width:60px;'></div></td>";
        tr_add += td_subcant;
				tr_add += "<td><div class='col-xs-2'><input type='text'  class='form-control' id='observaciones' name='observaciones'  style='width:150px;'></div></td>";
        tr_add += "<td class='Delete'><a href='#'><i class='fa fa-times-circle'></i> Borrar</a></td>";
        tr_add += '</tr>';
        if (id_prev != id_prod && id_prod != "" && existencias > 0) {
          $("#inventable").append(tr_add);
					$(".decimal").numeric();
        }
      }); //$.each(filasx, function(i,valor){
      $.each(id_previo, function(i, id_prod_ant) {
        if (id_prod == id_prod_ant) {
          existe = true;
          posicion_fila = i;
        }
      });
    } //success : function(data) {
  }); //ajax
  totales();
}
//Evento que se activa al perder el foco en precio de venta y cantidad:
$(document).on("blur", "#cant, #precio_compra", function() {
  totales();
})

$(document).on("blur", "#inventable", function() {
  //$('#precio_compra').blur(function() {
  totales();
})
$(document).on("keyup", "#cant, #precio_compra", function() {
  totales();
})

// Evento que selecciona la fila y la elimina de la tabla
$(document).on("click", ".Delete", function() {
  var parent = $(this).parents().get(0);
  $(parent).remove();
  totales();
});
//Calcular Totales del grid
function totales() {
  var subtotal = 0;
  total = 0;
  totalcantidad = 0;
  cantidad = 0;
  var total_dinero = 0;
  total_cantidad = 0;
  precio_compra = 0;
  precio_venta = 0;
  $("#inventable>tbody tr").each(function(index) {
    if (index >= 0) {
      var campo0, campo1, campo2, campo3, campo4, campo5;
      $(this).children("td").each(function(index2) {
        switch (index2) {
          //Id Producto 	Nombre 	 Existencias 	 	Cant.G    Subcant	 	Observaciones 	Acción
          case 0:
            campo0 = $(this).text();
            if (campo0 == undefined) {
              campo0 = '';
            }
            break;
          case 1:
            campo1 = $(this).text();
            break;
          case 2:
            campo2 = $(this).text();
            break;
          case 3:
            campo3 = $(this).find("#cant").val();
            if (isNaN(campo3) == false) {
              cantidad = parseFloat(campo3);
            } else {
              cantidad = 0;
            }
            break;subcant
          case 4:
              campo4 = $(this).find("#subcant").val();
              if (isNaN(campo4) == false) {
                subcantidad = parseFloat(campo4);
              } else {
                subcantidad = 0;
              }
              break;
         }
      });
    }
  });
}
$(document).on("click", "#submit1", function() {
  senddata();
});

function senddata() {
	urlscript = $("#urlscript").val();
  //Calcular los valores a guardar de cada item del inventario
  var i = 0;
  var precio_compra, precio_venta, cantidad, id_prod, id_producto,existencias,subcantidad,observaciones,unidad;
  var StringDatos = "";
  var id = $("select#tipo_entrada option:selected").val(); //get the value
  var totcant=$('#totcant').text();
  if (id == '0') {
    $('#tipo_entrada').focus();
  }

  var verificar = 'noverificar';
  var verificador = [];

  $("#inventable>tbody tr").each(function(index) {
    if (index >= 0) {
      var campo0, campo1, campo2, campo3, campo4, campo5,campo6,campo7;

      $(this).children("td").each(function(index2) {
        switch (index2) {
          //Id Producto 	Nombre 	 Existencias 	 	Cant.G    Subcant	 	Observaciones 	Acción
          case 0:
            id_producto = $(this).text();
            if (  id_producto == undefined) {
              id_producto = '';
            }
            break;
          case 1:
            campo1 = $(this).text();
            break;
          case 2:
            existencias=parseFloat($(this).text());
            break;
          case 3:
            unidad = parseFloat($(this).find("#unidades").val());
            cantidad = parseFloat($(this).find("#cant").val());
            if (isNaN(cantidad) == true) {
              cantidad = 0;
            }
            break;
          case 4:
            subcantidad = parseFloat($(this).find("#subcant").val());
              if (isNaN(subcantidad) == true) {
                subcantidad = 0;
              }
              break;
          case 5:
            observaciones = $(this).find("#observaciones").val();
            break;
        }
      });
      if (id_producto != "" || id_producto == undefined) {
        //Id Producto 	Nombre 	Existencias 		Precio Unit. 	Cant. Sale 	Lote/F. Caduc. observaciones 	Acción
        StringDatos += id_producto+ "|" +existencias + "|" + cantidad + "|" + subcantidad+ "|" + unidad+ "|"+ observaciones + "#";
        verificador.push(verificar);
        i = i + 1;
      }
    }
  });

  verificador.forEach(function(item, index, array) {
    if (item == 'verificar') {
      verificar = 'verificar';
    }
  });
  // Captura de variables a enviar
  var total_compras = 0;
  var fecha_movimiento = $("#fecha1").val();

  var dataString = 'process=insert' + '&stringdatos=' + StringDatos + '&cuantos=' + i +'&fecha_movimiento='+fecha_movimiento+'&totcant=' + totcant;

  verificar = 'noverificar';
  if (verificar == 'noverificar') {
    $.ajax({
      type: 'POST',
      url: urlscript,
      data: dataString,
      dataType: 'json',
      success: function(datax) {
        process = datax.process;
        display_notify(datax.typeinfo, datax.msg);
        setInterval("reload1();", 500);
      }
    });
  } else {
    var typeinfo = 'Warning';
    var msg = 'Falta rellenar algun valor de precio o cantidad!';
    display_notify(typeinfo, msg);
  }
}

function reload1() {
  location.href = urlscript;
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
      setInterval("location.reload();", 500);
      $('#deleteModal').hide();
    }
  });
}
//setRowCantLote(index, valueSelected ,change)
function setRowCantLote(rowId, cantidad, cant_lote, change) {

  if (change == true) {
    var cantidad_nueva = parseFloat(cant_lote);
    $('#inventable tr:nth-child(' + rowId + ')').find('td:eq(5)').find("#cant").val(cantidad_nueva);
    totales();
  }
};
