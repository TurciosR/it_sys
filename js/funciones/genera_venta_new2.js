var urlfactura = "";
$(document).ready(function() {
  urlfactura = $("#facturacion").val();
$('#total_dinero').html("<strong>0</strong>");
  //ocultar el buscador 2
$("#producto_buscar2").hide()
$('#busca_descrip_activo').prop("checked", false);
//busqueda con el plugin autocomplete typeahead
   $("#producto_buscar").typeahead({
     source: function(query, process) {
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
       var isbarcode = prod[2];
       if(id_prod!=0 && descrip!="NONE" && isbarcode!="0"){
        agregar_producto_lista(id_prod, descrip, isbarcode);
        //$('input#producto_buscar').focus();
         $('input#producto_buscar').val("");
       }
       else{
          $('input#producto_buscar').focus();
          $('input#producto_buscar').val("");
       }
      // agregar_producto_lista(id_prod, descrip, isbarcode);
     }
   });
   $("#producto_buscar").focus();
		$(document).keydown(function(e){
      if(e.which == 119) {//F8 activar busqueda descripcion y desactiva por barcode
	         $('#busca_descrip_activo').prop("checked", false);
          activar_busqueda()
			}
      if(e.which == 113){ //F2 Desactivar busqueda por barcode y desactiva  descripcion

          $('#busca_descrip_activo').prop("checked", true);
            activar_busqueda()

			}
      if(e.which == 120) { //F9 guarda factura
        e.preventDefault();
        if ($('#total_dinero').text()!=0){
	         senddata();
           $("#inventable").find("tr:gt(0)").remove();
        }
			}
      if(e.which == 112){ //F1 Imprimir modal
        if ($(".modal-body #facturado").val()!=""|| $(".modal-body #fact_num").html()!=""){
          imprime1();
        }
			}
		});
// fin Teclas de funcion
$('#busca_descrip_activo').change(function() {
  activar_busqueda();
})
function   activar_busqueda(){

   if ($('#busca_descrip_activo').is(':checked')) {
     $("#buscar_habilitado").html("Buscar Producto o Servicio (Por Barcode)")
     $("#producto_buscar2").val("");
     $("#producto_buscar").hide()
     $("#producto_buscar2").show()
     $("#producto_buscar2").focus();

   }
   else{
     $("#buscar_habilitado").html("Buscar Producto o Servicio (Por Descripcion)")
     $("#producto_buscar").val("");
    $("#producto_buscar2").hide()
    $("#producto_buscar").show()
    $("#producto_buscar").focus();
   }
}

  $(".decimal").numeric();
  //select2 select autocomplete
  $('#categoria').select2();
  $('#id_cliente').select2();

  $('#form_fact_consumidor').hide();
  $('#form_fact_ccfiscal').hide();

  //Boton de imprimir deshabilitado hasta que se guarde la factura
  $('#print1').prop('disabled', true);
  $('#submit1').prop('disabled', false);


var valor="";
//evento que captura el texto al pegar y lo envia a otro evt de busqueda de barcode
$("#producto_buscar2").bind('paste', function(e) {
  var pasteData = e.originalEvent.clipboardData.getData('text')
  valor=$(this).val();
  if (pasteData.length>=3) {
    buscarBarcode(pasteData);
	}
})
//evento al keyup para buscar si el barcode es de longitud mayor igual a 3 caracteres
$('#producto_buscar2').on('keyup', function(event) {
  if (event.which  && this.value.length>=3 && event.which!==13) {
    valor=$(this).val();
    $('input#producto_buscar2').val(valor)
      buscarBarcode($(this).val());
  }
  if (this.value.length>=3 && (event.which===13 ||event.which===32) ){
      buscarBarcode(valor);
      $('input#producto_buscar2').val("");
      $('input#producto_buscar2').focus();
  }
});
//evento para buscar por el barcode
function buscarBarcode(valor){
var barcode= valor;
  var dataString = 'process=buscarBarcode'+ '&id_producto=' + barcode;
  $.ajax({
    type: "POST",
    url: "facturacion_funciones.php",
    data: dataString,
    dataType: 'json',
    success: function(datax) {
      var producto=datax.array_prod;
      //if (producto!="")

      var prod = producto.split("|");
      var id_prod = prod[0];
      var descrip = prod[1];
      var isbarcode = prod[2];
      if(id_prod!=0 && descrip!="NONE" && isbarcode!="0"){
       agregar_producto_lista(id_prod, descrip, isbarcode);
       $('input#producto_buscar2').focus();
        $('input#producto_buscar2').val("");
      }
      else{
         $('input#producto_buscar2').focus();
      }
    }

  });
}
//Fin busqueda por la caja de texto solo para barcode

  $("#submit1").one("click", function(e) {
    if ($('#total_dinero').text()!=0){
      e.preventDefault();
    senddata();
      $("#inventable").find("tr:gt(0)").remove();
    }
    else{
      var typeinfo = 'Error';
      var msg = 'Debe registrar al menos un producto para la venta !';
      display_notify(typeinfo, msg);
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

// Evento para agregar elementos al grid de factura
function agregar_producto_lista(id_prod, descrip, costo) {
//borrar fila inicial de adorno
$('#inventable').find('tr#filainicial').remove()

  var id_prev = 0;
  var tipoprod = "";
  var id_prevprod = "";
  var descrip_only = "";
  var descripcion = descrip;
  var descrip_only = descrip;
  var tipo = 'PRODUCTO';
  var id_prodtipo = id_prod + tipo;
  var dataString = 'process=consultar_stock' + '&id_producto=' + id_prod + '&tipo=' + tipo;
  var filas = 0;

  if (descrip_only == undefined) {
    descrip_only = descripcion;
  }
  $.ajax({
    type: "POST",
    url: urlfactura,
    data: dataString,
    dataType: 'json',
    success: function(data) {
      var precio_venta = data.precio_venta;
      var existencias = data.existencias;
      var costos_pu = data.costos_pu;
      var precios_vta = data.precios_vta;
      var iva = data.iva;
      var unidades = data.unidades;
      var costo_prom = data.costo_prom;
      var imagen = data.imagen;
      var combo = data.combo;
      var fecha_caducidad = data.fecha_caducidad;
      var stock_fecha = data.stock_fecha;
      var id_prev;
      var oferta = data.oferta;
      var precio_oferta = data.precio_oferta;
      var porc_desc_base = data.porc_desc_base;
      var porc_desc_max = data.porc_desc_max;
      var fechas_vence = data.fechas_vence;
      var stock_vence = data.stock_vence;
      var perecedero = data.perecedero;

      var fecha_fin_oferta = data.fecha_fin_oferta;
      var fecha_hoy = data.fecha_hoy;
      //var precios = data.precio_venta;
      var id_previo = new Array();
      var id_lote_exist = new Array();
      $("#inventable tr").each(function(index) {
        if (index > 0) {
          var campo0, campo1, campo2, campo5;

          $(this).children("td").each(function(index2) {
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

      if (imagen == '') {
        var imagen_div = "<img alt='image' class='img-rounded rounded-border' width='300px' height='160px' src='img/productos/no_disponible.png'>";
      } else {
        var imagen_div = "<img alt='image' class='img-rounded rounded-border' width='300px' height='160px'  src='" + imagen + "'>";
      }
      var description = "<p class='font-bold'>Producto: " + descrip_only + "</p>"
      if (existencias > 0) {
        $("#imagen").html(imagen_div + description);
      } else {
        var imagen_div = "<img alt='image' class='img-rounded rounded-border' width='300px' height='160px' src='img/productos/no_stock.png'>";
        var description = "<p class='font-bold'>" + descrip_only + " Sin Existencias  " + "</p>"
        $("#imagen").html(imagen_div + description);
      }

      var tr_add = "";
      var subtotal = 0;
      if (existencias == null) {
        existencias = 0;
      }
      combo_chk = "<input type='checkbox' class='checkbox' id='check_combo' value='0' />";
      var porcentajes_descuento = '';

      if (oferta == 1) {
        precio_venta_desc = precio_oferta;
        precio_venta = precio_oferta;
        subtotal = round((1 * precio_oferta), 2);
        porcentajes_descuento = "<td><input type='hidden'  id='porc_desc_max' name='porc_desc_max' value='" + porc_desc_max + "'><div class='col-xs-2'><input type='text'  class='form-control decimal'  id='porc_desc' name='porc_desc' value='0' style='width:60px;' readonly></div></td>";
        descrip_only = descrip_only + "<label class='text-success'>" + "En Oferta hasta :" + fecha_fin_oferta + "</label>";
      } else {
        subtotal = round((1 * precio_venta) - (costo_prom * (porc_desc_base / 100)), 2);
        if (porc_desc_base > 0)
          precio_venta_desc = round(precio_venta - (costo_prom * (porc_desc_base / 100)), 2);
        else
          precio_venta_desc = round(precio_venta, 2)
        porcentajes_descuento = "<td><input type='hidden'  id='porc_desc_max' name='porc_desc_max' value='" + porc_desc_max + "'><div class='col-xs-2'><input type='text'  class='form-control decimal'  id='porc_desc' name='porc_desc' value='" + porc_desc_base + "' style='width:60px;'></div></td>";
      }

      subtotal = round((1 * precio_venta) - (costo_prom * (porc_desc_base / 100)), 2);
      if (porc_desc_base > 0)
        precio_venta_desc = round(precio_venta - (costo_prom * (porc_desc_base / 100)), 2);
      else
        precio_venta_desc = round(precio_venta, 2)

      if (perecedero == 1) {
        descrip_only = descrip_only + "&nbsp;<label class='text-danger'>" + "&nbsp;(Prod. Perecedero)" + "</label>";
        //stock_vence.sort(function(a, b){return b-a});
        select_fechas = "<select id='fecha_caducidad' class='form-control'>";
        $.each(stock_vence, function(i, val) {
          var values = val.split('|');

          var val0 = values[0] + '|' + values[1];


          if (values[1] == '00-00-0000') {
            var val1 = 'No_F_Vence|' + values[2];
          } else {
            var val1 = values[1] + '|' + values[2];
          }
          select_fechas += "<option value=" + val0 + ">" + val1 + "</option>";
        });
        select_fechas += "</select>";


      }

      if (perecedero == 0) {
        var val = 'No_F_Vence|' + existencias;
        select_fechas = "<select id='fecha_caducidad' class='form-control' disabled>";
        select_fechas += "<option value=" + val + ">" + val + "</option>";
        select_fechas += "</select>";
      }

        select_precios="<select name='select_precios' id='select_precios"+filas +"' class='form-control'>";


        if (oferta == 1) {
          subtotal = round((1 * precio_oferta), 2);
          select_precios+="<option value="+precio_oferta+">"+precio_oferta+"</option>";
          descrip_only = descrip_only + "<label class='text-success'>" + "En Oferta hasta :" + fecha_fin_oferta + "</label>";
        }else{
          precios_vta.sort(function(a, b){return b-a});
    			$.each(precios_vta, function(i,pr_unit){
    			select_precios+="<option value="+pr_unit+">"+pr_unit+"</option>";
    			});
        }
        select_precios+="</select>"
        pr1=precios_vta[0];
  		  subtotal=1*pr1;
  		  pr_min=precios_vta[3];

      if (unidades > 1) {
        descrip_only = descrip_only + "&nbsp;<label class='text-success'>" + "&nbsp;(Presentacion de: " + unidades  + " unidades)</label>";
        subcantidades = "<td><div class='col-xs-2'><input type='text'  class='form-control decimal' id='subcant' name='subcant' value='1' style='width:60px;'></div></td>";
        cantidades= "<td><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value='0' style='width:60px;'></div></td>";
      } else {
        subcantidades = "<td><div class='col-xs-2'><input type='text'  class='form-control decimal' id='subcant' name='subcant' value='0' style='width:60px;' readonly></div></td>";
        cantidades= "<td><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value='1' style='width:60px;'></div></td>";
      }
      tr_add += "<tr id='"+filas+"'>";
      tr_add += "<td><input type='hidden' id='unidades' name='unidades' value='"+unidades+"' /><input type='hidden'  id='costo_prom' name='costo_prom' value='" + costo_prom + "'>" + id_prod + "</td>";
      tr_add += '<td>' + descrip_only + '</td>';
      tr_add += "<td id='cant_stock'>" + existencias + "</td>";
      tr_add += "<td>"+select_precios+"</td>";
      tr_add +="<td><input type='hidden'  id='precio_venta_inicial' name='precio_venta_inicial' value='"+pr_min+"'><div class='col-xs-2'><input type='text'  class='form-control decimal'  id='precio_venta' name='precio_venta' value='"+pr1+"' readonly style='width:80px;'></div></td>";
      tr_add += cantidades;
      tr_add += subcantidades;
      tr_add += "<td id='subtot' class='text-right'>" + subtotal + "</td>";
      if (combo == '1') {
        tr_add += "<td id='combos'  class='text-center'>" + combo_chk + "</td>";
      } else {
        tr_add += "<td id='nocombos'></td>";
      }
      tr_add += "<td class='Delete'><a><i class='fa fa-trash'></i></a></td>";
      tr_add += '</tr>';
       //numero de filas
       var tipo_impresion = $("select#tipo_impresion option:selected").val();
      if (filas > 14 && tipo_impresion!="TIK") {
        var typeinfo = 'Warning';
        var msg = 'Numero de Filas en Factura excede el maximo permitido !';
        display_notify(typeinfo, msg);
      } else {

        var existe = false;
        var posicion_fila = 0;
        $.each(id_previo, function(i, id_prod_ant) {
          if (id_prod == id_prod_ant) {
            existe = true;
            posicion_fila = i;
          }
        });
        if (existe == false && existencias > 0 && precio_venta > 0) {
          $("#inventable").append(tr_add);
          $(".decimal").numeric();
          $('.checkbox').checkbox();
        }
        if (existe == true && existencias > 0 && combo == 0 && perecedero == 0 && precio_venta > 0) {
          $(".decimal").numeric();
          posicion_fila = posicion_fila + 1;
          //setRowCant(posicion_fila);
        }
        if (existe == true && existencias > 0 && combo == 1 && precio_venta > 0) {

          $("#inventable").append(tr_add);
          $(".decimal").numeric();
          $('.checkbox').checkbox();
        }
        if (existe == true && existencias > 0 && perecedero == 1) {
          $(".decimal").numeric();
          posicion_fila = posicion_fila + 1;
          //setRowCant(posicion_fila);
        }
        totales();
        totalFact();
      }
    }
  });
  //totales();
}

// reemplazar valores de celda cantidades
function setRowCant(rowId) {
  var cantidad_anterior = $('#inventable tr:nth-child(' + rowId + ')').find('td:eq(5)').find("#cant").val();
  var cantidad_nueva = parseFloat(cantidad_anterior) + 1;
  $('#inventable tr:nth-child(' + rowId + ')').find('td:eq(5)').find("#cant").val(cantidad_nueva);
  totales();
  totalFact();
};

//Evento que se activa al perder el foco en precio de venta y cantidad:
$("#inventable").on('change', '#cant', function() {
  totales();
  totalFact();
});

$("#inventable").on('keyup', '#cant', function() {
  totales();
});

$("#inventable").on('change',  '#precio_venta', function(){
  totales();
});
$("#inventable").on('keyup',  '#precio_venta', function(){
  totales();
});
$("#inventable").on('change',  '#subcant', function(){
  totales();
});

$("#inventable").on('keyup', '#subcant', function() {
  totales();
});

$(document).on("blur", "#inventable", function() {
  totales();
})

$(document).on("change", "#check_combo", function() {
  totales();
})
// Evento que selecciona la fila y la elimina de la tabla
$(document).on("click", ".Delete", function() {
  var parent = $(this).parents().get(0);
  $(parent).remove();
  totales();
  totalFact()
});
//function to round 2 decimal places
function round(value, decimals) {
  return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}
//cantidad restar en td stock
function setRowCantDown(rowId, stock_bd, cantidad,unidades, precio_venta, change) {
  if (change == true) {
     var cantidad_nueva = parseFloat(stock_bd / unidades);
	   var subtotal= (precio_venta*cantidad_nueva);
    var new_subtotal=subtotal.toFixed(2);
    $('#inventable').find('tr#'+rowId).find('td:eq(5)').find("#cant").val(cantidad_nueva);
    $('#inventable').find('tr#'+rowId).find('td:eq(6)').find("#subcant").val('0');
    $('#inventable').find('tr#'+rowId).find('td:eq(7)').html(new_subtotal);
    totalFact();
  }
}
// reemplazar valor de select precios modificar para 1 linea
function setRowPrice(fila, new_price, cantidad, unidades, subcantidad, change){
  precio_venta=parseFloat(new_price);
    var  subtotal = 0;
  	  if (change == true) {
        if (isNaN(subcantidad)) {
          subcantidad=0;
        }
         subcantidad=parseFloat(subcantidad)
       if (isNaN(cantidad)) {
         cantidad=0;
       }
        subtotal = (precio_venta*cantidad)+ precio_venta*(subcantidad/unidades);
      var new_subtotal=subtotal.toFixed(2);
      $('#inventable').find('tr#'+fila).find('td:eq(4)').find("#precio_venta").val(new_price);
      $('#inventable').find('tr#'+fila).find('td:eq(7)').html(new_subtotal);
      totalFact();
	}
}
function setRowSubcant(rowId,cantidad, subcantidad,unidades,precio_venta, change) {
  var  subtotal = 0;
	  if (change == true) {
      if (isNaN(subcantidad)) {
        subcantidad=0;
      }
       subcantidad=parseFloat(subcantidad)
     if (isNaN(cantidad)) {
       cantidad=0;
     }
      subtotal = (precio_venta*cantidad)+  precio_venta*(subcantidad/unidades);
    var new_subtotal=subtotal.toFixed(2);
    $('#inventable').find('tr#'+rowId).find('td:eq(7)').html(new_subtotal);
    totalFact();
  }
}
//Funcion para recorrer la tabla completa y pasar los valores de los elementos a un array
function totalFact(){
    var TableData = new Array();
    var i=0,total=0;
    var td1;var td2;var td3;var td4;var td5;var td6;
	var StringDatos='';
	$("#inventable>tbody  tr").each(function (index) {
		 if (index>=0){
           var subtotal=0;
            $(this).children("td").each(function (index2) {
                switch (index2){
                    case 7:
                       subtotal= parseFloat( $(this).text());
                       if (isNaN(subtotal)){
                         subtotal=0;
                       }
                        break;
                }
            });

          }

           total+=subtotal;
        });
	total=round(total,2);
	total_dinero=total.toFixed(2);
	//total_cantidad=totalcantidad.toFixed(2);
	$('#total_dinero').html("<strong>"+total_dinero+"</strong>");
	$('#totalfactura').val(total_dinero);
	$('#totaltexto').load(urlfactura + '?' + 'process=total_texto&total=' + total_dinero);
}
//Calcular Totales del grid
function totales(){
	var subtotal=0; total=0; totalcantidad=0;  cantidad=0;subcantidad =0; unidades=0;
	var total_dinero=0; total_cantidad=0;  precio_venta=0; precio_venta=0;
	var elem1 = '';
	var descripcion='';
	var tipoproducto = '';  tipoprod = '';
	var precio_minimo=0;

	$("#inventable tr").each(function (index) {
		if (index>0){
           //var campo0,campo1, campo2, campo3, campo4, campo5,campo6,campo7,campo8;
           //precios, cantidades y subcantidades
             unidades = parseFloat($('#inventable').find('tr#'+index).find('td:eq(0)').find("#unidades").val());
           stock_bd = parseFloat( $('#inventable').find('tr#'+index).find('td:eq(2)').text());

          cantidad = parseFloat($('#inventable').find('tr#'+index).find("#cant").val());
          subcantidad = parseFloat($('#inventable').find('tr#'+index).find("#subcant").val());
           precio_venta= parseFloat($('#inventable').find('tr#'+index).find('td:eq(4)').find("#precio_venta").val());
             //select precios
             var precio=0;
             change0=false;
 			   $('#inventable').find('tr#'+index).find('td:eq(3)').find('#select_precios'+index).change(function(){
 				      $("#select_precios"+index+" option:selected").each(function() {
                 unidades = parseFloat($('#inventable').find('tr#'+index).find('td:eq(0)').find("#unidades").val());
                 cantidad = parseFloat($('#inventable').find('tr#'+index).find("#cant").val());
                 subcantidad = parseFloat($('#inventable').find('tr#'+index).find("#subcant").val());
 					       precio = $( this ).text();
 					       change0=true;
 					       setRowPrice(index,precio,cantidad,unidades,subcantidad,change0);

 				      });
 			    })
            if (isNaN(cantidad) == true) {
      				cantidad = 0;
      			}
      			if (isNaN(subcantidad) == true) {
      				subcantidad = 0;
      			}
      			if (unidades > 1) {
      				cant_subcat = cantidad * unidades
      			} else {
      				cant_subcat = cantidad
      			}
      			cant_subcat = cant_subcat + subcantidad

              change1 =false;
              change2 =false;
            if (cant_subcat > stock_bd) {
        				change1 = true;
        				setRowCantDown(index, stock_bd, cantidad,unidades, precio_venta, change1);
        	 }
           else{
             change2 = true;
             setRowSubcant(index, cantidad, subcantidad,unidades,precio_venta, change2);

           }

			        if( $(this).find('#check_combo').prop('checked')){
				            $(this).find("#precio_venta").val('0');
                    $(this).find("td:eq(7)").val('0');
				            subtotal=0;
			       }
      }
    });


}

$(document).on("click", ".print1", function() {
  var totalfinal = parseFloat($('#totalfactura').val());
  var facturado = totalfinal.toFixed(2);
  $(".modal-body #facturado").val(facturado);
});
$(document).on("click", "#btnPrintFact", function(event) {
  imprime1();
});
$(document).on("click", "#btnFinFact", function(event) {
  finalizar2();
});
$(document).on("click", "#btnEsc", function(event) {
  reload1();
});
$(document).on("click", "#print2", function() {
  imprime2();
});

function activa_modal(numfact) {
  $('#viewModal').modal({
    backdrop: 'static',
    keyboard: false
  });
  var totalfinal = parseFloat($('#totalfactura').val());
  var numdoc = numfact;
  var facturado = totalfinal.toFixed(2);
  $(".modal-body #facturado").val(facturado);
  $(".modal-body #fact_num").html(numfact);
}

function activa_modal2(numfact) {
  $('#viewModal').modal({
    backdrop: 'static',
    keyboard: false
  });
  var totalfinal = parseFloat($('#totalfactura').val());
  var numdoc = numfact;
  var facturado = totalfinal.toFixed(2);
  $(".modal-body #facturado").val(facturado);
  $(".modal-body #fact_num").html(numdoc);
  $(".modal-body #num_doc_fact").hide();
}

function senddata() {
  //Obtener los valores a guardar de cada item facturado
  var i = 0;
  var precio_venta, precio_venta,precios_venta, cantidad, subcantidad, id_prod, id_empleado, precio_venta_desc, fecha_vencimiento;
  var elem1 = '';
  var descripcion = '';
  var tipoprodserv = '';
  tipoprod = '';
  var StringDatos = "";
  var id = '1';
  var id_empleado = 0;
  var id_cliente = $("select#id_cliente option:selected").val();
  var tipo_impresion = $("select#tipo_impresion option:selected").val();

  var numero_doc = $("#numero_doc").val();
  var total_ventas = $('#total_dinero').text();
  var fecha_movimiento = $("#fecha").val();
  var id_prod=0;
  if (fecha_movimiento == '' || fecha_movimiento == undefined) {
    var typeinfo = 'Warning';
    var msg = 'Falta  Fecha!';
    display_notify(typeinfo, msg);
  }
  if (id_empleado == '' || id_empleado == undefined) {
    id_empleado = 0;
  }
  if (numero_doc == undefined || numero_doc == '') {
    numero_doc = 0;
  }
  var verificaempleado = 'noverificar';
  var verifica = [];
    var array_json = new Array();
  $("#inventable>tbody tr ").each(function(index) {
    if (index >= 0) {
      var campo0, campo1, campo2, campo3, campo4, campo5, campo6, campo7, campo8;
      $(this).children("td").each(function(index2) {
        switch (index2) {
          case 0:
          campo0 = $(this).text();
          if (campo0==undefined){
          campo0='';
          }
          unidades = parseFloat($(this).find("#unidades").val());
          break;
          case 1:
          campo1 = $(this).text();
          break;
          case 2:
          campo2 = $(this).text();
          if (isNaN(campo2)==false)
          cant_stock=parseFloat(campo2);
                  else
          cant_stock=0;
                  break;
          case 3:
          precio = $( this ).text();
          break;
          case 4:
          campo4= $(this).find("#precio_venta").val();
          if (isNaN(campo4)==false){
          precio_venta=parseFloat(campo4);
          }
          else{
          precio_venta=0;
          }
          break;
          case 5:
          campo5= $(this).find("#cant").val();
          if (isNaN(campo5)==false){
          cantidad=parseFloat(campo5);
          }
          else{
          cantidad=0;
          }
          break;
          case 6:
          campo6 = $(this).find("#subcant").val();
          if (isNaN(campo6) == false) {
           subcantidad = parseFloat(campo6);
          } else {
           subcantidad = 0;
          }
          break;
          case 7:
           campo7 = $(this).text();
           if (isNaN(campo7) == false) {
             subtotal = parseFloat(campo7);
           } else {
             subtotal = 0;
           }
           break;
        }
      });
      if (campo0 != "" || campo0 == undefined || isNaN(campo0) == false) {
        StringDatos += campo0 +  "|"+ precio_venta + "|" + cantidad  + "|" + subcantidad+ "|" + unidades+ "|" + subtotal  + "#";
      //  i = i + 1;
        if( campo3!=0){
          var obj = new Object();
          obj.id = campo0;
          obj.precio  =precio_venta;
          obj.cantidad = cantidad ;
          obj.subcantidad = subcantidad;
          obj.unidades =  unidades;
          obj.subtotal  = subtotal;
          //convert object to json string
          text=JSON.stringify(obj);
          array_json.push(text);
          i = i + 1;
        }
      }
    }

  });
  json_arr = '['+array_json+']';
  var dataString = 'process=insert' +  '&cuantos=' + i + '&id=' + id + '&numero_doc=' + numero_doc + '&fecha_movimiento=' + fecha_movimiento + '&id_cliente=' + id_cliente;
   dataString+= '&total_ventas=' + total_ventas + '&id_empleado=' + id_empleado + '&tipo_impresion=' + tipo_impresion+'&json_arr='+json_arr;
  $.ajax({
    type: 'POST',
    url: urlfactura,
    data: dataString,
    dataType: 'json',
    success: function(datax) {
      process = datax.process;
      factura = datax.factura;
      $('#submit1').prop('disabled', true);
      if (urlfactura == 'genera_venta_new.php' || urlfactura == 'editar_factura.php') {
        activa_modal(factura);
        $("#inventable").find("tr:gt(0)").remove();
        $('#total_dinero').html("<strong>0</strong>");
        $('#totaltexto').html("<strong>Son:</strong>");
      }
      if (urlfactura == 'facturacion.php') {
        activa_modal(factura);
        $("#inventable").find("tr:gt(0)").remove();
        $('#total_dinero').html("<strong>0</strong>");
        $('#totaltexto').html("<strong>Son:</strong>");
      }
      if (urlfactura == 'genera_venta_lote.php') {
       // activa_modal2(factura);
        $("#inventable").find("tr:gt(0)").remove();
        display_notify(datax.typeinfo,datax.msg);
        setInterval("reload1();", 500);
      }
    }
  });
}
$(document).on("keyup", "#efectivo", function() {
  total_efectivo();
});

function total_efectivo() {
  var efectivo = parseFloat($('#efectivo').val());
  var totalfinal = parseFloat($('#totalfactura').val());
  var facturado = totalfinal.toFixed(2);
  $('#facturado').val(facturado);
  if (isNaN(parseFloat(efectivo))) {
    efectivo = 0;
  }
  if (isNaN(parseFloat(totalfinal))) {
    totalfinal = 0;
  }
  var cambio = efectivo - totalfinal;
  var cambio = round(cambio, 2);
  var cambio_mostrar = cambio.toFixed(2);

  //$('#cambio').val(cambio_mostrar);
  if ($('#efectivo').val() != '' && efectivo >= totalfinal) {
    $('#cambio').val(cambio_mostrar);
    $('#mensajes').text('');
  } else {
    $('#cambio').val('0');
    if (efectivo < totalfinal) {
      $('#mensajes').html("<h5 class='text-danger'>" + "Falta dinero !!!" + "</h5>");
    }
  }
}


function imprime1(){
  var numero_doc = $(".modal-body #fact_num").html();
  var print = 'imprimir_fact';
  var tipo_impresion = $("select#tipo_impresion option:selected").val();
  var urlfactura= $("#facturacion").val();
  var num_doc_fact = $(".modal-body #num_doc_fact").val();

  var dataString = 'process=' + print + '&numero_doc=' + numero_doc + '&tipo_impresion=' + tipo_impresion;

  $.ajax({
    type: 'POST',
    url: urlfactura,
    data: dataString,
    dataType: 'json',
    success: function(datos) {
      var sist_ope = datos.sist_ope;
      var dir_print=datos.dir_print;
      var shared_printer_win=datos.shared_printer_win;
      var efectivo_fin = parseFloat($('#efectivo').val());
      var cambio_fin = parseFloat($('#cambio').val());

      //esta opcion es para generar recibo en  printer local y validar si es win o linux
      if (tipo_impresion == 'COF') {
        if (sist_ope == 'win') {
            $.post("http://"+dir_print+"/printfactwin1.php", {
              datosventa: datos.facturar,
              efectivo: efectivo_fin,
              cambio: cambio_fin,
              shared_printer_win:shared_printer_win
          })
        } else {

          $.post("http://"+dir_print+"/printfact1.php", {
            datosventa: datos.facturar,
            efectivo: efectivo_fin,
            cambio: cambio_fin
          }, function(data, status) {

            if (status != 'success') {
              alert("No Se envio la impresión " + data);
            } else {
              setInterval("reload1();", 500);
            }

          });
        }
      } else {
        if (sist_ope == 'win') {
          $.post("http://"+dir_print+"/printposwin1.php", {
          datosventa: datos.facturar,
          efectivo: efectivo_fin,
          cambio: cambio_fin,
          shared_printer_win:shared_printer_win
          })
        } else {
          $.post("http://"+dir_print+"/printpos1.php", {
            datosventa: datos.facturar,
            efectivo: efectivo_fin,
            cambio: cambio_fin
          }, function(data, status) {
            if (status != 'success') {
              alert("No Se envio la impresión " + data);
            } else {
              setInterval("reload1();", 500);
            }
          });
        }


    }
    setInterval("reload1();", 500);
  }
  });
  }
function reload1(){
  location.href = "genera_venta_new.php";
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
      //$('#deleteModal').hide();
    }
  });
}


function print2() {
  var id_factura = $('#id_factura').val();
  var dataString = 'process=imprimir_fact' + '&id_factura=' + id_factura;

  $.ajax({
    type: "POST",
    url: "imprimir_factura.php",
    data: dataString,
    dataType: 'json',
    success: function(datos) {
      //display_notify(datax.typeinfo, datax.msg);
      sist_ope = datos.sist_ope;
      var efectivo_fin = parseFloat($('#efectivo').val());
      var cambio_fin = parseFloat($('#cambio').text());

      //esta opcion es para generar recibo en  printer local y validar si es win o linux
      if (sist_ope == 'win') {
        $.post("http://localhost/pueblo/printpos1.php", {
          datosventa: datos.facturar
        })
      } else {
        $.post("http://localhost/pueblo/printpos1.php", {
          datosventa: datos.facturar,
          efectivo: efectivo_fin,
          cambio: cambio_fin
        }, function(data, status) {
          if (status != 'success') {
            alert("No Se envio la impresión " + data);
          } else {
            setInterval("reload2();", 3000);
          }
        });
      }

      setInterval("reload2();", 3000);


    }
  });
}

//Proceso para finalizar la factura, queda el estatus pendiente de impresion, si se elige esta opcion,aunque esta opcion
//puede venir ya si no se imprime y solo se cierra el modal de factura.
////url : "genera_venta_new.php",
function finalizar2() {
  var numero_doc = $(".modal-body #fact_num").html();
  var num_doc_fact = $(".modal-body #num_doc_fact").val();
  var dataString = 'process=finalizar_fact' + '&numero_doc=' + numero_doc + '&num_doc_fact=' + num_doc_fact;
  $.ajax({
    type: "POST",
    url: urlfactura,
    data: dataString,
    dataType: 'json',
    success: function(datax) {
      process = datax.process;
      factura = datax.factura;
      display_notify(datax.typeinfo, datax.msg);
      setInterval("reload1();", 500);
    }
  });
}
