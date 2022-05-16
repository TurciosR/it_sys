var urlprocess = "";
$(document).ready(function() {
urlprocess = $('#urlprocess').val();
  $(".datepick2").datepicker();
  $('#select_proveedores').select2();
    $('#select_colores').select2();
  $('#select_pedidos').select2();

  $("#producto_buscar2").hide();
  $("#producto_buscar3").hide();

  $('#tipo0').on('ifChecked', function(event){
    $("#buscar_habilitado").html("Buscar Producto  (Por Descripcion)")
    $("#producto_buscar").val("");
    $("#producto_buscar").show()
    $("#producto_buscar").focus();
    $("#producto_buscar2").hide();
    $("#producto_buscar3").hide();
	});
  $('#tipo1').on('ifChecked', function(event){
    $("#buscar_habilitado").html("Buscar Producto  (Por Barcode)")
    $("#producto_buscar2").val("");
    $("#producto_buscar2").show()
    $("#producto_buscar2").focus();
    $("#producto_buscar").hide();
    $("#producto_buscar3").hide();
 });

 //filtro Busqueda
 $('.select2').select2({
  placeholder: {
     id: '-1',
     text: 'Seleccione'
   },
     allowClear: true
 });

 $('#keywords, #estilo, #talla, #barcode').on('keyup', function() {
   searchFilter();
 });
 $('#tipo2').on('ifChecked', function(event){
   $("#buscar_habilitado").html("Buscar Producto  (Por Estilo)")
   $("#producto_buscar3").val("");
   $("#producto_buscar3").show()
   $("#producto_buscar3").focus();
   $("#producto_buscar").hide();
   $("#producto_buscar2").hide();
});
//evento change para select dependientes
 $("#select_proveedores").change(function(event){
   //var id_proveedor = $("select#select_proveedores option:selected").val();
   datos_proveedores();
   totalfact();
 });

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
      // alert(id_prod);
      agregar_producto_lista(id_prod, descrip, marca);
    }
  });

  var valor="";
  //evento que captura el texto al pegar y lo envia a otro evt de busqueda de barcode
  $("#producto_buscar2").bind('paste', function(e) {
    var pasteData = e.originalEvent.clipboardData.getData('text')
    valor=$(this).val();
    if (pasteData.length>=2) {
      buscarBarcode(pasteData);
  	}
  })
  //evento al keyup para buscar si el barcode es de longitud mayor igual a 3 caracteres
  $('#producto_buscar2').on('keyup', function(event) {
    if (event.which  && this.value.length>=2 && event.which!==13) {
      valor=$(this).val();
      $('input#producto_buscar2').val(valor)
        buscarBarcode($(this).val());
    }
    if (this.value.length>=2 && (event.which===13 ||event.which===32) ){
        buscarBarcode(valor);
        $('input#producto_buscar2').val("");
        $('input#producto_buscar2').focus();
    }
  });


//datos d eproveedores
function datos_proveedores(){
  var id_proveedor = $("select#select_proveedores option:selected").val();
  var urlprocess=$('#urlprocess').val();


  dataString={process:"datos_proveedores", id_proveedor:id_proveedor} ;

   $.ajax({
     type:'POST',
     url:urlprocess,
     data: dataString,
     dataType: 'json',
     success: function(datax){
       porc_percepcion=datax.percepcion;
       porc_retencion1=datax.retencion1;
       porc_retencion10=datax.retencion10;
      $("#porc_retencion1").val(porc_retencion1);
      $("#porc_retencion10").val(porc_retencion10);
      $("#porc_percepcion").val(porc_percepcion);
       totalfact();
     }
   });
}
function searchFilter(page_num) {
  page_num = page_num ? page_num : 0;
  var keywords = $('#keywords').val();
  var id_color = $('#select_colores :selected').val();
  var talla = $('#talla').val();
  var estilo = $('#estilo').val();
  var barcode = $('#barcode').val();

  if(id_color==undefined)
    id_color=-1;

  $.ajax({
    type: 'POST',
    url: 'compras3.php',
    data: {
      process: 'traerdatos',
      keywords: keywords,
      id_color: id_color,
      talla: talla,
      estilo: estilo,
      barcode:barcode

    },
    beforeSend: function() {
      $('.loading-overlay').show();
    //  $('#encabezado_buscador').show();
    },
    success: function(html) {
      $('#mostrardatos').html(html);
      $('.loading-overlay').fadeOut("slow");

    }
  });

}
//evento para buscar por el barcode
function buscarBarcode(valor){
var barcode= valor;
  var dataString = 'process=buscarBarcode'+ '&id_producto=' + barcode;
  $.ajax({
    type: "POST",
    url: "compras.php",
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
$("#submit1").click(function(){
  senddata();
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
  /*
  $(document).on("click", "#submit1", function(event) {
    senddata();
  });
  */
});

// Evento para seleccionar una opcion y mostrar datos en un div
$(document).on("change", "#tipo_entrada", function() {
  $(".datepick2").datepicker();
  $('#id_proveedor2').select2();
  var id = $("select#tipo_entrada option:selected").val();
});

// Agregar productos a la lista del inventario
function agregar_producto_lista(id_prod, descrip, costo) {
  var id_prev = "";
  //var costo_prom=0;
  urlprocess = $('#urlprocess').val();
  var dataString = 'process=consultar_stock' + '&id_producto=' + id_prod;
  $.ajax({
    type: "POST",
    url: urlprocess,
    data: dataString,
    dataType: 'json',
    success: function(data) {

      var cp = data.costo_prom;
      var color = data.color;
      var talla = data.talla;
      var existencias= data.existencias;
      var pv_base = data.pv_base;
      var exento = data.exento;
      $("#inventable tr").each(function(index) {
        id_prev = $(this).closest('tr').children('td:first').text();
        if (id_prev == id_prod ) {
          id_prod = "";
        } else
          id_prod = id_prod;
      });

      if(pv_base==null)
         pv_base=0
      if(cp==null)
         cp=0

      add = "";
       subtotal=0.0;
       subt_exento=0.0;
       subt_gravado=0.0;
      var   tr_add = '<tr class="tr1">';
      tr_add += "<td class='col1 td1'><input type='hidden'  id='exento' name='exento' value='" + exento + "'>" + id_prod + "</td>";
      tr_add += '<td  class="col2 td1">' + descrip +' '+color+' talla:'+talla+ '</td>';
      tr_add += "<td class='col3 td1'><div class='col-xs-2'>"+"<input type='text'  class='form-control' id='precio_compra' name='precio_compra' value='"+cp+"' style='width:80px;'></div></td>";
      tr_add += "<td class='col4 td1'><div class='col-xs-2'><input type='text'  class='form-control' id='cant' name='cant'  style='width:60px;'></div></td>";
      tr_add += "<td class='col5 td1'><div class='col-xs-2'><input type='text'  class='form-control' id='descto' name='descto'  value='0' style='width:60px;'></div></td>";
      tr_add += '<td class="col6 td1" id="subt_exento">' + subt_exento+ '</td>';
      tr_add += '<td class="col7 td1">'+"<input type='hidden'  id='subt_descto' name='subt_descto' value='0.0'>"  + subt_gravado+ '</td>';
      tr_add += "<td class='Delete col8 td1'><a class='btn btn-danger'><i class='fa fa-trash'></i> </a></td>";
      tr_add += '</tr>';
      if (id_prev != id_prod && id_prod != "") {
        $("#inventable").append(tr_add);
      }

    }
  });
  totales();
}
//Evento que se activa al perder el foco en precio de venta y cantidad:
$(document).on("blur", "#cant, #precio_compra, #descto", function() {
  totales();
  totalfact();
})
$(document).on("blur", "#inventable", function() {
  //$('#precio_compra').blur(function() {
  totales();
  totalfact();
})
$(document).on("keyup", "#cant, #precio_compra, #descto", function() {
  totales();
  totalfact();
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
  var descto=0;
  var subt_descto=0;
  totalcantidad = 0;
  cantidad = 0;
  var total_dinero = 0;
  total_cantidad = 0;
  precio_compra = 0;
  precio_venta = 0;
  total_descuento = 0;
  $("#inventable>tbody tr").each(function(index) {
    if (index >= 0) {
      var campo0, campo1, campo2, campo3, campo4, campo5;
      $(this).children("td").each(function(index2) {
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
      });

      if (isNaN(cantidad) == true) {
        cantidad = 0;
      }
      totalcantidad += cantidad;
      if(descto>0){
        descto=round(descto/100,2);

      }else{
        descto=0;
      }
        subt_descto=precio_compra*descto* cantidad;
      if (exento==1){

        subt_exento = precio_compra* cantidad- subt_descto;
        subt_gravado =0;
      }
      else{
        subt_gravado = precio_compra* cantidad- subt_descto;
        subt_exento =0;
      }

      if (isNaN(subtotal) == true) {
        subtotal = 0.00;
      }

      subtot_mostrar1=subt_exento.toFixed(2)
      subtot_mostrar2=subt_gravado.toFixed(2)
      subt_descto_mostrar=subt_descto.toFixed(2)
        console.log("subt_descto_mostrar:"+subt_descto_mostrar)
      $(this).closest("tr").find("td:eq(5)").html(subtot_mostrar1)
      $(this).closest("tr").find("#subt_descto").val(subt_descto_mostrar)
      $(this).closest("tr").find("td:eq(6)").html(subtot_mostrar2)
      total_descuento+=subt_descto;
      total += subtotal;

    }
  });
  if (isNaN(total) == true) {
    total = 0;
  }
  if (isNaN(total_descuento) == true) {
  total_descuento = 0;
  }
  total_descuento_mostrar= total_descuento.toFixed(2);
  $('#totdescto').html("<strong>" +total_descuento_mostrar+ "</strong>");
  /*
  total_dinero = total.toFixed(2);
  total_cantidad = totalcantidad.toFixed(2);


  $('#total_dinero').html("<strong>" + total_dinero + "</strong>");
  $('#totcant').html("<strong>" +total_cantidad+ "</strong>");
  $('#totaltexto').load(urlprocess + '?' + 'process=total_texto&total=' + total_dinero);
  */
 totalfact();
}

//Funcion para recorrer la tabla completa y pasar los valores de los elementos a un array
function totalfact(){
  //impuestos
  var iva=$('#porc_iva').val();
  var porc_retencion1=$("#porc_retencion1").val();
  var porc_retencion10=$("#porc_retencion10").val();
  var porc_percepcion=$("#porc_percepcion").val();

  var monto_retencion1=$('#monto_retencion1').val();
  var monto_retencion10=$('#monto_retencion10').val();
  var monto_percepcion =$('#monto_percepcion').val();
  //fin impuestos
  var urlprocess = $('#urlprocess').val();
  var i=0,total=0; totalcantidad =0;
  var td1;var td2;var td3;var td4;var td5;var td6;
  var total_gravado=0;
  var total_exento=0;
  var subt_gravado=0;
  var  subt_exento=0;
  var total_descto=0;
  var subt_descto=0;
	var StringDatos='';
	$("#inventable>tbody  tr").each(function (index) {
		 if (index>=0){
           var subtotal=0;
            $(this).children("td").each(function (index2) {
                switch (index2){
                  case 3:
                     cantidad= parseFloat( $(this).find("#cant").val());
                     if (isNaN(cantidad)){
                       cantidad=0;
                     }
                     break;
                     /*
                     case 4:
                        descto= parseFloat( $(this).find("#cant").val());
                        if (isNaN(descto)){
                          descto=0;
                        }
                        break;*/
                    case 5:
                    break;
                       subt_exento= parseFloat( $(this).text());
                       if (isNaN(subt_exento)){
                         subt_exento=0;
                       }
                        break;
                    case 6:
                       subt_gravado= parseFloat( $(this).text());
                       if (isNaN(subt_gravado)){
                             subt_gravado=0;
                           }
                           subt_descto= parseFloat($(this).find("#subt_descto").val());
                          // alert(subt_descto)
                           if (isNaN(subt_descto)){
                             subt_descto=0;
                          }
                            break;
                }
            });
          }

           total_exento+=subt_exento;
           total_gravado+=subt_gravado;
           total+=subt_exento+subt_gravado;
           total_descto+=subt_descto;
           totalcantidad += cantidad;
           console.log("subt_descto:"+subt_descto)
        });
  // IMPUESTOS
  total=round(total,2);
  total_descto=round(subt_descto,2);
	total_dinero=total.toFixed(2);
   total_gravado = round(total_gravado, 4);
    total_exento = round(total_exento, 4);
  var total_gravado_mostrar = total_gravado.toFixed(2)
  var total_exento_mostrar = total_exento.toFixed(2);
  var total_iva = round((total_gravado * iva), 4);
  var total_iva_mostrar = total_iva.toFixed(2);
  $('#total_gravado').html(total_gravado_mostrar);
  $('#total_gravado_sin_iva').html(total_gravado_mostrar);
  $('#total_exento').html(total_exento_mostrar);
  $('#total_exenta').html(total_exento_mostrar);
  $('#total_iva').html(total_iva_mostrar);
  total_gravado_iva= total_gravado + total_iva;
  total_gravado_iva_mostrar=total_gravado_iva.toFixed(2);
  $('#total_gravado_iva').html(total_gravado_iva_mostrar); //total gravado con iva
  var total_percepcion=0
    var total_retencion1=0
    var total_retencion10=0
 if (total_gravado>=monto_percepcion)
     total_percepcion=total_gravado*porc_percepcion;
 if (total_gravado>=monto_retencion1)
   total_retencion1=total_gravado*porc_retencion1;
 if (total_gravado>=monto_retencion10)
    total_retencion10=total_gravado*porc_retencion10;

  total_percepcion_mostrar=total_percepcion.toFixed(2);
  var total_final=(total_exento+total_iva +total_gravado+total_percepcion)-(total_retencion1+total_retencion10);
  total_final_mostrar=total_final.toFixed(2);
  $('#total_percepcion').html(total_percepcion_mostrar);
  total_retencion1_mostrar=total_retencion1.toFixed(2);
  total_retencion10_mostrar=total_retencion10.toFixed(2);
  $('#total_retencion').html('0.00');
  if (parseFloat(total_retencion1)>0.0)
    $('#total_retencion').html(total_retencion1_mostrar);
  if (parseFloat(total_retencion10)>0.0)
  $('#total_retencion').html(total_retencion10_mostrar);
total_descto_mostrar=total_descto.toFixed(2);
    $('#total_final').html(total_final_mostrar);
      $('#totcant').html(totalcantidad);
    //    $('#totdescto').html(total_descto_mostrar);
  $('#totaltexto').load(urlprocess,{'process': 'total_texto','total':total_dinero});
}

function senddata() {
  //Calcular los valores a guardar de cada item del inventario
  var i = 0;
  var precio_compra= 0;  cantidad= 0;
  var subtotal=0;
  var StringDatos = "";
var total_compras = $('#total_dinero').text();
var fecha_movimiento = $("#fecha2").val();
var numero_doc = $("#numero_doc2").val();
var id_proveedor = $("select#select_proveedores option:selected").val();

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
          case 2:
            campo2 = $(this).find("#precio_compra").val();
            if (isNaN(campo2) == false) {
              precio_compra = parseFloat(campo2);
            } else {
              precio_compra = 0;
            }
            if(precio_compra === null)
               precio_compra = 0;
            break;
            case 3:
              campo3 = $(this).find("#cant").val();
              if (isNaN(campo3) == false) {
                cantidad = parseFloat(campo3);
              } else {
                cantidad = 0;
              }
              if(  cantidad  === null)
                   cantidad  = 0;
              break;
              case 4:
              subtotal= $(this).text();
        }
      });
      if( cantidad && precio_compra){
        var obj = new Object();
        obj.id = campo0;
        obj.precio = precio_compra;
        obj.cantidad = cantidad;
        obj.subtotal = subtotal;
        //convert object to json string
        text=JSON.stringify(obj);
        array_json.push(text);
        i = i + 1;
      }
    }
  });
  json_arr = '['+array_json+']';
  console.log('jsons:'+json_arr);

  var seleccion = 0;
  var seleccion_doc = 0;
  if (numero_doc == undefined || numero_doc == '') {
    seleccion_doc = 0;
  } else {
    seleccion_doc = 1;
  }
  if (!id_proveedor || id_proveedor != '-1') {
    seleccion = 0;
  } else {
    seleccion = 1;
  }

  if (seleccion == 0) {
    msg = 'Falta digitar Proveedor !';
  }
  if (seleccion_doc == 0) {
     msg = 'Falta digitar Numero de Documento de compra!';
  }


  var dataString = 'process=insert' + '&stringdatos=' + StringDatos + '&cuantos=' + i +'&json_arr='+json_arr;
  dataString+= '&fecha_movimiento=' + fecha_movimiento + '&numero_doc=' + numero_doc
   dataString+= '&id_proveedor=' + id_proveedor + '&total_compras=' + total_compras;
   alert(dataString)
if (seleccion == 1 && seleccion_doc == 1) {
    $.ajax({
      type: 'POST',
      url: urlprocess,
      data: dataString,
      dataType: 'json',
      success: function(datax) {
        process = datax.process;
        display_notify(datax.typeinfo, datax.msg);
      //  setInterval("reload1();", 5000);
      }
    });

  }
  else {
    var typeinfo = 'Warning';
    display_notify(typeinfo, msg);
  }

}

function reload1() {
  location.href = urlprocess;
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
