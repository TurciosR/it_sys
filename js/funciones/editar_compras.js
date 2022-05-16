var urlprocess = "";
$(document).ready(function(){
  $('#inventable').arrowTable();
  $('#loadtable').arrowTable();

  urlprocess = $('#urlprocess').val();
  $('.loading-overlay').hide();
  $(".datepick2").datepicker();
  $('#select_proveedores').select2();
  $('#select_colores').select2();
  $('#select_pedidos').select2();
  $('#select_documento').select2();
  id_compras=$('#id_compras').val();

  datos_proveedores();
  buscarCompras(id_compras);

  $('.select2').select2({
    placeholder: {
     id: '-1',
     text: 'Seleccione'
    },
     allowClear: true
   });
   //Campos criterio de Busqueda
  $('#keywords').on('keyup', function(event) {
   var kw=$('#keywords').val();
   if (kw.length>0  && event.which!==8){
      $("#loadtable").find("tr:gt(0)").remove();
       searchFilter();
   }
 });
 $('#estilo, #talla').on('keyup', function(event) {
   if (event.which!==8){
      $("#loadtable").find("tr:gt(0)").remove();
       searchFilter();
   }
 });
 $("#select_colores").on('change',function(){
   $("#loadtable").find("tr:gt(0)").remove();
   var color=$('#select_colores :selected').val();
  searchFilter();
});

  //evento change para select proveedores
  $("#select_proveedores").change(function(event){
   datos_proveedores();
   totalfact();
 });
 //traer valores por pedidos para agregarlos a lista de compras
 $("#select_pedidos").on('change',function(){
   id_pedido=$("#select_pedidos").val();
     urlprocess = $('#urlprocess').val();
  var barcode= valor;
    var dataString = 'process=buscarpedido'+ '&id_pedido=' + id_pedido;
    $.ajax({
      type: "POST",
      url:  urlprocess ,
      data: dataString,
      dataType: 'json',
      success: function(datax) {
        $.each(datax, function(key, value){
        var arr = Object.keys(value).map(function(k) { return value[k]});
        var id_producto= arr[0];
        var cantidad= arr[1];
        addProductList(id_producto, cantidad);
        });
      }
    });

  });

  //evento que captura el texto al pegar y lo envia a otro evt de busqueda de barcode
    var valor="";
  $("#barcode").bind('paste', function(e) {
    var pasteData = e.originalEvent.clipboardData.getData('text')
    valor=$(this).val();
    if (pasteData.length>=2) {
      searchBarcode(pasteData);
  	}
  })
  //evento al keyup para buscar si el barcode es de longitud mayor igual a 1 caracteres
  $('#barcode').on('keyup', function(event) {
    if (event.which  && this.value.length>=2 && event.which===13) {
      valor=$(this).val();
      $('#barcode').val(valor)
        searchBarcode($(this).val());
        $('#barcode').val("");
        $('#barcode').focus();
    }
  });
  $(document).keydown(function(e){

    if(e.which == 120) { //F9 guarda factura
      e.preventDefault();
      if ($('#total_final').text()!=0 && $("#items").val()>0){
         senddata();
         $("#inventable").find("tr:gt(0)").remove();
      }
      else{
            display_notify('Error', 'Debe haber al menos un producto registrado');
      }
    }
    if(e.which == 112){ //F1 Imprimir modal
      if ($(".modal-body #facturado").val()!=""|| $(".modal-body #fact_num").html()!=""){
        imprime1();
      }
    }
  });
  // fin Teclas de funcion

}); //end document ready

//datos de proveedores
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
function searchBarcode(barcode) {
  addProductList(barcode,1)
}

function searchFilter() {
  var keywords = $('#keywords').val();
  var id_color = $('#select_colores :selected').val();
  var talla = $('#talla').val();
  var estilo = $('#estilo').val();
  var barcode = $('#barcode').val();
  var limite = $('#limite').val();

  if(id_color==undefined){
    id_color=-1;
  }
  getData(keywords,id_color,talla,estilo,barcode,limite)
}

function getData(keywords,id_color,talla,estilo,barcode,limite){
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
      barcode:barcode,
      limite:limite
    },
    beforeSend: function() {
      $('.loading-overlay').show();
    },
    success: function(html) {
        $('#mostrardatos').html(html);
        var cuantos=  $('#cuantos_reg').val();
      if (cuantos>0){
          $('.loading-overlay').html("<span class='text-warning'>Buscando....</span>");
          $('#reg_count').val(cuantos);
          $('.loading-overlay').fadeOut("slow");
      }
    else{
        $('.loading-overlay').fadeOut("slow");
        $('#reg_count').val(0);
      }
    }
  });
}
//fin evento para buscar por el barcode
$("#submit1").click(function(){
  senddata();
});

function buscarCompras(id_compras){
  //para cargar compras y detalle compras
    urlprocess = $('#urlprocess').val();

      var dataString = 'process=buscarcompras'+ '&id_compras=' + id_compras;
      $.ajax({
        type: "POST",
        url: urlprocess,
        data: dataString,
        dataType: 'json',
        success: function(datax) {
          $.each(datax, function(key, value){
          var arr = Object.keys(value).map(function(k) { return value[k]});
          var id_producto= arr[0];
          var cantidad= arr[1];
            console.log(id_producto+" "+cantidad)
          addProductList(id_producto, cantidad);
          });
        }
  });
}

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
function addProductList(id_prod,cantidad) {
  id_prod=$.trim(id_prod);
  cantidad=parseInt(cantidad)
  var id_prev = "";
  var id_new=id_prod;
  var id_previo = new Array();
  var filas=0;
  $("#inventable tr").each(function(index){
    if (index > 0) {
      var campo0, campo1, campo2, campo5;
      $(this).children("td").each(function(index2){
        var isVisible =false;
        switch (index2) {
          case 0:
            campo0 = $(this).text();
            isVisible = $(this).filter(":visible").length > 0;
            if (isVisible == true) {
                if (campo0 != undefined || campo0 != '') {
                  id_previo.push(campo0);
                }
            }
            break;
        }
      });
      filas = filas + 1;
    } //if index>0
  });
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
      var descrip=data.descrip;
      if(pv_base==null)
         pv_base=0
      if(cp==null)
         cp=0
      add = "";
       subtotal=0.0;
       if (exento==1){
         var subt_gravado=0.0;
         var subt_exento=parseFloat(cp)*cantidad;
          subt_exento=round(subt_exento,2)
       }
       else {
         var subt_gravado=parseFloat(cp)*cantidad;
         subt_gravado=round(subt_gravado,2)
         var subt_exento=0.0;
       }
      var   tr_add = '<tr class="tr1">';
      tr_add += "<td class='col11 td1'><input type='hidden'  id='exento' name='exento' value='" + exento + "'>" + id_prod + "</td>";
      tr_add += '<td  class="col14 td1">' + descrip +' '+color+' talla:'+talla+ '</td>';
      tr_add += "<td class='col11 td1'><div class='col-xs-2'>"+"<input type='text'  class='form-control decimal' id='precio_compra' name='precio_compra' value='"+cp+"' style='width:80px;'></div></td>";
      tr_add += "<td class='col11 td1'><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value='"+cantidad+"' style='width:60px;'></div></td>";
      tr_add += "<td class='col11 td1'><div class='col-xs-2'><input type='text'  class='form-control decimal' id='descto' name='descto'  value='0' style='width:60px;'></div></td>";
      tr_add += '<td class="col11 td1" id="subt_exento">' + subt_exento+ '</td>';
      tr_add += '<td class="col11 td1">'+"<input type='hidden'  id='subt_descto' name='subt_descto' value='0.0'>"  + subt_gravado+ '</td>';
      tr_add += '<td class="Delete col11 td1"><input id="delprod" type="button" class="btn btn-danger fa"  value="&#xf1f8;"></td>';
      tr_add += '</tr>';
      //agregar columna a la tabla de facturacion
      var existe = false;
      var posicion_fila = 0;
      var posicion_fila2 = 0;
      $.each(id_previo, function(i, id_prod_ant){
        if (id_prod == id_prod_ant) {
          existe = true;
          posicion_fila = i;
          posicion_fila2=posicion_fila+1
            setRowCant(posicion_fila2);
        }
      });
      if (existe == false) {
        $("#inventable").append(tr_add);
        $(".decimal").numeric();
      }
        totales();
      totalfact();
    }
  });


}
// reemplazar valores de celda cantidades
function setRowCant(rowId){
  var cantidad_anterior = $('#inventable tr:nth-child(' + rowId + ')').find('td:eq(3)').find("#cant").val();
  var cantidad_nueva = parseFloat(cantidad_anterior) + 1;
  $('#inventable tr:nth-child(' + rowId + ')').find('td:eq(3)').find("#cant").val(cantidad_nueva);
  //totales();
  //totalfact();
}

$('#loadtable tbody ').on( 'blur', 'tr', function () {
  $(this).removeClass('hoverTable2');
})
$('#loadtable tbody ').on( 'focus', 'tr', function () {
  $(this).addClass('hoverTable2');
})
//navigate asign tabindex to tr
$('#loadtable ').on( 'click', 'tr', function () {
document.onkeydown = checkKey;
var id_prod=$(this).closest("tr").find('td:eq(0)').text();
addProductList(id_prod,1);
});
$('#loadtable ').on( 'focus', 'tr', function () {
document.onkeydown = checkKey;
});
function checkKey(e) {
    var rowCount = $('#loadtable >tbody >tr').length;
    var event = window.event ? window.event : e;
    if(event.keyCode == 40){ //down
      var idx = $("tr:focus").attr("tabindex");
        $('#loadtable').find("tr[tabindex="+idx+"]").removeClass('hoverTable2');
      idx++;
      if(idx >=rowCount){
        idx = 0;
      }
      $("tr[tabindex="+idx+"]").focus();
    $('#loadtable').find("tr[tabindex="+idx+"]").addClass('hoverTable2');
    }
    if(event.keyCode == 38){ //up

      var idx = $("tr:focus").attr("tabindex");
        $('#loadtable').find("tr[tabindex="+idx+"]").removeClass('hoverTable2');
      idx--;
      if(idx < 0){
        idx = rowCount-1;
      }
      $("tr[tabindex="+idx+"]").focus();
     $('#loadtable').find("tr[tabindex="+idx+"]").addClass('hoverTable2');
    }
    if(event.keyCode == 13){ //up
      var idx = $("tr:focus").attr("tabindex");
      var id_prod = $('#loadtable').find("tr[tabindex="+idx+"]").find('td:first').text();
       addProductList(id_prod,1); //agregar  producto a lista
    }
}
//end navigate
//Evento que se activa al perder el foco en precio de venta y cantidad:
$(document).on("blur", "#cant, #precio_compra, #descto", function() {
  $(this).removeClass('hoverTable2');
  totales();
  totalfact();
})

$(document).on("focus", "#cant, #precio_compra, #descto", function() {
  totales();
  totalfact();
  $(this).addClass('hoverTable2');
  })
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
  var descto=0;
  var subt_descto=0;
  totalcantidad = 0;
  cantidad = 0;
  var total_dinero = 0;
  total_cantidad = 0;
  precio_compra = 0;
  precio_venta = 0;
  total_descuento = 0;
  var filas=0;
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
  var filas=0;
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
                       if (isNaN(subt_descto)){
                           subt_descto=0;
                      }
                      break;
                }
            });
          }
           filas+=1;
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
    $('#items').val(filas);
    $('#pares').val(totalcantidad);
  $('#totaltexto').load(urlprocess,{'process': 'total_texto','total':total_final_mostrar});
}

function senddata() {
  //Calcular los valores a guardar de cada item del inventario
  var i = 0;
  var precio_compra= 0;  cantidad= 0; descto= 0;
  var subtotal=0; subt_exento=0; subt_gravado=0;
  var StringDatos = "";
  var total_compras = $('#total_final').text();
  var fecha_movimiento = $("#fecha2").val();
  var numero_doc = $("#numero_doc2").val();
  var id_tipodoc = $("select#select_documento option:selected").val();
  var id_proveedor = $("select#select_proveedores option:selected").val();
  var total_gravado= $('#total_gravado').text();
  var total_exento= $('#total_exento').text();
  var total_iva= $('#total_iva').text();
  var total_retencion= $('#total_retencion').text();
  var total_percepcion= $('#total_percepcion').text();
  var numero_dias=$("#numero_dias").val();
  var id_pedido=$('select#select_pedidos option:selected').val();
  var id_compras=$("#id_compras").val();
  var items= $('#items').val();
  var  pares= $('#pares').val();
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
                campo4 = $(this).find("#descto").val();
                if (isNaN(campo4) == false) {
                  descto = parseFloat(campo4);
                } else {
                  descto = 0;
                }
                break;
                case 5:
                  subt_exento = $(this).text();
                  break;
                case 6:
                  subt_gravado = $(this).text();
                  break;
        }
      });
      if( cantidad && precio_compra){
        var obj = new Object();
        obj.id = campo0;
        obj.precio_compra = precio_compra;
        obj.cantidad = cantidad;
        obj.descto = descto;
        obj.subt_exento = subt_exento;
        obj.subt_gravado = subt_gravado;
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
  var sel_tipo_doc= 0;
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

  var dataString = 'process=insert' +  '&cuantos=' + i +'&json_arr='+json_arr;
  dataString+= '&fecha_movimiento=' + fecha_movimiento + '&numero_doc=' + numero_doc+ '&id_tipodoc=' + id_tipodoc
  dataString+= '&id_proveedor=' + id_proveedor + '&total_compras=' + total_compras;
  dataString+= '&total_gravado='+ total_gravado;
  dataString+= '&total_exento='+total_exento;
  dataString+= '&total_iva='+total_iva;
  dataString+='&total_retencion='+ total_retencion;
  dataString+= '&total_percepcion='+total_percepcion;
  dataString+= '&numero_dias='+numero_dias;
  dataString+= '&id_pedido='+id_pedido;
  dataString+= '&id_compras='+id_compras;
  dataString+= '&pares='+pares;
  dataString+= '&items='+items;
  if (seleccion == 1 && seleccion_doc == 1  && sel_tipo_doc == 1) {
    $.ajax({
      type: 'POST',
      url: urlprocess,
      data: dataString,
      dataType: 'json',
      success: function(datax) {
        process = datax.process;
        display_notify(datax.typeinfo, datax.msg);
         setInterval("reload1();", 500);
      }
    });

  }
  else {
    var typeinfo = 'Warning';
    display_notify(typeinfo, msg);
  }
}

function reload1() {
  location.href = "admin_compras_fecha.php";
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
