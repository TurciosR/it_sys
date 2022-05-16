var urlprocess = "";
$(document).ready(function() {

  $("#abono").numeric({negative:false,decimalPlaces:2})
  $("#efectivo").numeric({negative:false,decimalPlaces:2})

  $('#inventable').arrowTable();
  $('#loadtable').arrowTable();
  $('#numero_doc2').prop('readonly', true);
  urlprocess = $('#urlprocess').val();
  $('.loading-overlay').hide();
  $(".datepick2").datepicker();
  //$('#select_clientes').select2();
  $('#select_colores').select2();
  $('#select_tipo_pago').select2();
  $("#barcode").focus();
  $('.select2').select2({
     allowClear: true
   });

   //Agregar cliente desde selec2 si no existe
   $('#select_clientes').select2({
        allowClear: true,
        escapeMarkup: function (markup) { return markup; },
        placeholder: "Buscar Cliente",
        language: {
            noResults: function () {
							var modalcliente="<a href='modal_cliente.php' data-toggle='modal' data-target='#clienteModal'>";
  						modalcliente+="Agregar Cliente</a>";
								 return modalcliente;
            }
        }
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
//vales por devolucion
$("#select_vales").on('change',function(){
  if($('#select_vales :selected').val()==-1){
    var val=0.0;
  }
  else{
    var data=$('#select_vales :selected').html()
    valor_vales=data.split('|')
    var val=valor_vales[2]
  }
  $('#valor_vale').html(val);
  totalfact();
});
// eventos teclas de funcion
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
//evento change para select clientes
 $("#select_clientes").change(function(event){
   datos_clientes();
   totalfact();
 });
 //si cambia el tipo doc

  $("#select_documento").on('change',function(){
    var alias=$("#select_documento").val();
    if (alias=='TIK'){
      $('#numero_doc2').prop('readonly', true);
    }  else {
      $('#numero_doc2').prop('readonly', false);
    }
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


//fin evento para buscar por el barcode
$("#submit1").click(function(){
  senddata();
});

});

//datos de clientes
function datos_clientes(){
  var id_cliente = $("select#select_clientes option:selected").val();
  var urlprocess=$('#urlprocess').val();
  dataString={process:"datos_clientes", id_cliente:id_cliente} ;
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
      var precios_vta = data.precios_vta;
      var descuento = data.descuento;

      if(cp==null)
         cp=0
         add = "";
         subtotal=0.0;
         var  valor_descuento=0;

         if(parseFloat(descuento)>0){
            valor_descuento=parseFloat(precios_vta)*(descuento/100);
            valor_descuento=round(valor_descuento,2)
         }

         var subtotal=parseFloat(precios_vta)*cantidad;

         if (exento==1){
           var subt_gravado=0.0;
           var subt_exento=parseFloat(precios_vta)*cantidad-valor_descuento;
            subt_exento=round(subt_exento,2)
         }
         else {
           var subt_gravado=parseFloat(precios_vta)*cantidad-valor_descuento;
           subt_gravado=round(subt_gravado,2)
           var subt_exento=0.0;
         }


      //agregar columna a la tabla de facturacion
      var resta_stock=0;

      $.each(id_previo, function(i, id_prod_ant){
        if (id_prod == id_prod_ant) {
          resta_stock+=1;
        }
      });
        existencias=  existencias - resta_stock;
        if (existencias>0){
        tr_add = '';
        tr_add += '<tr class="tr1">';

       tr_add += "<td class='col1 td1'><input type='hidden'  id='exento' name='exento' value='" + exento + "'>" + id_prod + "</td>";
       tr_add += '<td  class="col2 td1">' + descrip +' '+color+' talla:'+talla+ '</td>';
       tr_add += "<td class='text-success col1 td1' id='cant_stock'>" + existencias + "</td>";

       tr_add += "<td class='text-success col3 td1'><input type='hidden'  id='precio_venta_inicial' name='precio_venta_inicial' value='"+precios_vta+"'><div class='col-xs-2'><input type='text'  class='form-control decimal'  id='precio_venta' name='precio_venta' value='"+precios_vta+"' style='width:60px;' readonly></div></td>";
       tr_add += "<td class='text-success col4 td1'><input type='hidden'  id='cant_restada' name='cant_restada' value='"+"1"+"'><div class='col-xs-2'><input type='text'  class='form-control decimal qty' id='cant' name='cant' value='"+cantidad+"' style='width:60px;' readOnly></div></td>";
       tr_add += "<td class='text-success col1 td1 subtotals' id='subtotal'>"+subtotal+"</td>";
       tr_add += "<td class='col5 td1'><div class='col-xs-2'><input type='text'  class='form-control decimal' id='descto' name='descto'  value='"+valor_descuento+"' style='width:60px;'></div></td>";

       tr_add += '<td class="col6 td1" id="subt_exento">' + subt_exento+ '</td>';
       tr_add += '<td class="col7 td1">'+"<input type='hidden'  id='subt_descto' name='subt_descto' value='0.0'>"  + subt_gravado+ '</td>';
       tr_add += '<td class="Delete col8 td1"><input id="delprod" type="button" class="btn btn-danger fa"  value="&#xf1f8;"></td>';
       tr_add += '</tr>';

        $("#inventable").append(tr_add);
          $(".decimal").numeric();
          existencias=0;
        }

        totales();
      totalfact();
    }
  });


}
// reemplazar valores de celda cantidades
function setRowCant(rowId){
  var cantidad_anterior = $('#inventable tr:nth-child(' + rowId + ')').find('td:eq(5)').find("#cant").val();
  var cantidad_nueva = parseFloat(cantidad_anterior) + 1;
  $('#inventable tr:nth-child(' + rowId + ')').find('td:eq(5)').find("#cant").val(cantidad_nueva);

  //totalfact();
}
//reemplazar el precio de acuerda al valor seleccionado
$(document).on('change', '.sel_prec', function(){
  var tr = $(this).parents("tr");
  var precio = $(this).find(':selected').val();
  tr.find("#precio_venta").val(precio);
  totales();
});
//cantidad y cant_stock
$(document).on('keyup', '#cant', function(){
  var tr = $(this).parents("tr");
  var existencias = tr.find('td:eq(2)').text();
  var cantidad=tr.find('td:eq(5)').find('#cant').val();
  /*if (cantidad==""){
    cantidad=0
  }*/
  if (parseInt(cantidad)>parseInt(existencias)){
    tr.find('td:eq(5)').find("#cant").val(existencias);
  }
  if (parseInt(existencias)>0 && parseInt(cantidad)==0 ){
    tr.find('td:eq(5)').find("#cant").val("1");
  }
  totales();
});

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
$(document).on("blur", "#cant, #precio_venta, #descto", function() {
  $(this).removeClass('hoverTable2');
  totales();
  totalfact();
})

$(document).on("focus", "#cant, #precio_venta, #descto", function() {
  totales();
  totalfact();
  $(this).addClass('hoverTable2');
  })
  $(document).on("blur", "#cant, #precio_venta, #descto", function() {
    $(this).removeClass('hoverTable2');
  })
$(document).on("blur", "#inventable", function() {
  totales();
  totalfact();
})
$(document).on("keyup", "#precio_venta, #descto", function() {
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
/*
function totales(){
  urlprocess = $('#urlprocess').val();

  var subtotal = 0;
  total = 0;
  var descto=0;
  var subt_descto=0;
  totalcantidad = 0;
  cantidad = 0;
  var total_dinero = 0;
  total_cantidad = 0;
  precio_venta = 0;
  precio_venta = 0;
  total_descuento = 0;
    existencias = 0;
  var filas=0;
  $("#inventable>tbody tr").each(function(index) {
    if (index >= 0) {
      var campo0, campo1, campo2, campo3, campo4, campo5,campo6;
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
              campo2 = $(this).find("cant_stock").val();

              if (isNaN(campo2) == false) {
              existencias= parseFloat(campo2);
              } else {
              existencias = 0;
              }
              break;
          case 4:
            campo4 = $(this).find("#precio_venta").val();

            if (isNaN(campo4) == false) {
              precio_venta = parseFloat(campo4);
            } else {
              precio_venta = 0;
            }

            break;
            case 5:
              campo3 = $(this).find("#cant").val();
              if (isNaN(campo3) == false) {
                cantidad = parseFloat(campo3);
              } else {
                cantidad = 0;
              }
              break;

            case 6:
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
      if(isNaN(descto)==true){
        descto=0;
      }
        subt_descto=precio_venta*cantidad-descto;
      if (exento==1){
        subt_exento = precio_venta* cantidad- subt_descto;
        subt_gravado =0;
      }
      else{
        subt_gravado = precio_venta* cantidad- subt_descto;
        subt_exento =0;
      }

      if (isNaN(subtotal) == true) {
        subtotal = 0.00;
      }


      subtot_mostrar1=subt_exento.toFixed(2)
      subtot_mostrar2=subt_gravado.toFixed(2)
      subt_descto_mostrar=subt_descto.toFixed(2)
        console.log("subt_descto_mostrar:"+subt_descto_mostrar)
      $(this).closest("tr").find("td:eq(7)").html(subtot_mostrar1)
      $(this).closest("tr").find("#subt_descto").val(subt_descto_mostrar)
      $(this).closest("tr").find("td:eq(8)").html(subtot_mostrar2)
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
*/
//Calcular Totales del grid
function totales(){
  urlprocess = $('#urlprocess').val();

  var subtotal = 0;
  total = 0;
  var descto=0;
  var subt_descto=0;
  totalcantidad = 0;
  cantidad = 0;
  var total_dinero = 0;
  total_cantidad = 0;
  precio_venta = 0;
  precio_venta = 0;
  total_descuento = 0;
    existencias = 0;
  var filas=0;
  $("#inventable>tbody tr").each(function(index) {
    if (index >= 0) {
      var campo0, campo1, campo2, campo3, campo4, campo5,campo6;
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
              campo2 = $(this).find("cant_stock").val();

              if (isNaN(campo2) == false) {
              existencias= parseFloat(campo2);
              } else {
              existencias = 0;
              }
              break;
          case 3:
            campo4 = $(this).find("#precio_venta").val();

            if (isNaN(campo4) == false) {
              precio_venta = parseFloat(campo4);
            } else {
              precio_venta = 0;
            }

            break;
            case 4:
              campo3 = $(this).find("#cant").val();
              if (isNaN(campo3) == false) {
                cantidad = parseFloat(campo3);
              } else {
                cantidad = 0;
              }
              break;

            case 6:
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
      if (isNaN(  descto) == true) {
        descto=0;
      }
      if (descto>precio_venta){
      descto=precio_venta
      }
      subt_descto=descto;

      if (exento==1){
        subt_exento = precio_venta* cantidad- subt_descto;
        subt_gravado =0;
      }
      else{
        subt_gravado = precio_venta* cantidad- subt_descto;
        subt_exento =0;
      }

      if (isNaN(subtotal) == true) {
        subtotal = 0.00;
      }
      subtotal = precio_venta*cantidad;
      subtot_mostrar0=subtotal.toFixed(2)
      subtot_mostrar1=subt_exento.toFixed(2)
      subtot_mostrar2=subt_gravado.toFixed(2)
      subt_descto_mostrar=subt_descto.toFixed(2)
      //  console.log("subt_descto_mostrar:"+subt_descto_mostrar)
      $(this).closest("tr").find("td:eq(5)").html(subtot_mostrar0)
      $(this).closest("tr").find("td:eq(7)").html(subtot_mostrar1)
      $(this).closest("tr").find("#subt_descto").val(subt_descto_mostrar)
      $(this).closest("tr").find("td:eq(8)").html(subtot_mostrar2)
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
//validar descuento
$(document).on('keyup', '#descto', function(){
  var tr = $(this).parents("tr");
  var precio = tr.find('td:eq(3)').find('#precio_venta').val();
  var descuento = $(this).val();
  if (parseInt(descuento)>parseInt(precio)){
    tr.find('td:eq(6)').find("#descto").val(precio);
  }

  totales();
});
//Funcion para recorrer la tabla completa y pasar los valores de los elementos a un array
/*
function totalfact(){
  //impuestos
  var iva=$('#porc_iva').val();
  var porc_retencion1=$("#porc_retencion1").val();
  var porc_retencion10=$("#porc_retencion10").val();
  var porc_percepcion=$("#porc_percepcion").val();
  var id_tipodoc = $("select#select_documento option:selected").val();
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
                  case 5:
                     cantidad= parseFloat( $(this).find("#cant").val());
                     if (isNaN(cantidad)){
                       cantidad=0;
                     }
                     break;
                    case 7:
                    break;
                       subt_exento= parseFloat( $(this).text());
                       if (isNaN(subt_exento)){
                         subt_exento=0;
                       }
                        break;
                    case 8:
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
    var total_iva =0.00
  if (id_tipodoc=='CCF'){
   total_iva = round((total_gravado * iva), 4);
  }
  txt_war="class='text-danger'"
  var total_iva_mostrar = total_iva.toFixed(2);
  $('#total_gravado').html(total_gravado_mostrar);
  $('#total_gravado_sin_iva').html(total_gravado_mostrar);
  $('#total_exento').html(total_exento_mostrar);
  $('#total_exenta').html(total_exento_mostrar);

    $('#total_iva').html(total_iva_mostrar);
    total_gravado_iva= total_gravado + total_iva;
    total_gravado_iva_mostrar=total_gravado_iva.toFixed(2);
    $('#total_gravado_iva').html(total_gravado_iva_mostrar); //total gravado con iva

      $('#total_iva').html(total_iva_mostrar);


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
  $('#items').val(filas);
  $('#pares').val(totalcantidad);
  $('#totaltexto').load(urlprocess,{'process': 'total_texto','total':total_final_mostrar});
  $('#totalfactura').val(total_final_mostrar);
  // a pagar, por si hay uso de vale por devoluciones
  valor_vale=parseFloat($('#valor_vale').text());
  monto_pago=total_final-valor_vale;
  monto_pago_mostrar=monto_pago.toFixed(2);
  $('#monto_pago').html(monto_pago_mostrar);
}
*/
function totalfact(){
  //impuestos
  var iva=$('#porc_iva').val();
  var porc_retencion1=$("#porc_retencion1").val();
  var porc_retencion10=$("#porc_retencion10").val();
  var porc_percepcion=$("#porc_percepcion").val();
  var id_tipodoc = $("select#select_documento option:selected").val();
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
  var  subtotal=0;
  var total_descto=0;
  var total_sin_descto=0;
  var subt_descto=0;
	var StringDatos='';
  var filas=0;
	$("#inventable>tbody  tr").each(function (index) {
		 if (index>=0){
           //var subtotal=0;
            $(this).children("td").each(function (index2) {
                switch (index2){
                  case 4:
                     cantidad= parseFloat( $(this).find("#cant").val());
                     if (isNaN(cantidad)){
                       cantidad=0;
                     }
                     break;
                     case 5:

                        subtotal= parseFloat($(this).text());
                        if (isNaN(subtotal)){
                          subtotal=0;
                        }
                         break;
                    case 7:
                    break;
                       subt_exento= parseFloat( $(this).text());
                       if (isNaN(subt_exento)){
                         subt_exento=0;
                       }
                        break;
                    case 8:
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
           total_sin_descto+=subtotal;
           total_exento+=subt_exento;
           total_gravado+=subt_gravado;
           total+=subt_exento+subt_gravado;

           total_descto+=subt_descto;
           totalcantidad += cantidad;
           console.log("subtotal:"+subtotal)
        });
  // IMPUESTOS
  total=round(total,2);
  total_descto=round(total_descto,2);
  total_sin_descto=round(total_sin_descto,2);
	total_dinero=total.toFixed(2);
  total_gravado = round(total_gravado, 4);
  total_exento = round(total_exento, 4);
  var total_sin_descto_mostrar = total_sin_descto.toFixed(2)
  var total_descto_mostrar = total_descto.toFixed(2)
  var total_gravado_mostrar = total_gravado.toFixed(2)
  var total_exento_mostrar = total_exento.toFixed(2);
    var total_iva =0.00
  if (id_tipodoc=='CCF'){
   total_iva = round((total_gravado * iva), 4);
  }
  var tot_discount=$("#totdescto").text();
  txt_war="class='text-danger'"
  var total_iva_mostrar = total_iva.toFixed(2);
  $('#total_sin_descto').html(total_sin_descto_mostrar);
  //$('#total_sin_descto').html("total_sin_descto_mostrar");
  $('#total_gravado').html(total_gravado_mostrar);
  //$('#total_gravado_sin_iva').html(total_gravado_mostrar);
  $('#total_gravado_sin_iva').html(total_sin_descto_mostrar);
  $('#total_descuento_final').html(tot_discount);
  $('#total_exento').html(total_exento_mostrar);
  $('#total_exenta').html(total_exento_mostrar);

    $('#total_iva').html(total_iva_mostrar);
    total_gravado_iva= total_gravado + total_iva;
    total_gravado_iva_mostrar=total_gravado_iva.toFixed(2);
    $('#total_gravado_iva').html(total_gravado_iva_mostrar); //total gravado con iva

      $('#total_iva').html(total_iva_mostrar);


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
  $('#items').val(filas);
  $('#pares').val(totalcantidad);
  $('#totaltexto').load(urlprocess,{'process': 'total_texto','total':total_final_mostrar});
  $('#totalfactura').val(total_final_mostrar);
  // a pagar, por si hay uso de vale por devoluciones
  valor_vale=parseFloat($('#valor_vale').text());
  valor_reserva=parseFloat($('#valor_reserva').text());
  monto_pago=total_final-valor_vale-valor_reserva;
  monto_pago_mostrar=monto_pago.toFixed(2);
  $('#monto_pago').html(monto_pago_mostrar);
}

function senddata(){
  //Calcular los valores a guardar de cada item del inventario
  var i = 0;
  var precio_venta= 0;  cantidad= 0; descto= 0;
  var subtotal=0; subt_exento=0; subt_gravado=0;
  var StringDatos = "";
  var tot_g=parseFloat($('#total_gravado').text());
  var tot_e=parseFloat($('#total_exento').text());
  var total_venta = tot_g+tot_e;
  total_venta = round(total_venta,2)
  var fecha_movimiento = $("#fecha2").val();
  var id_cliente = $("select#select_clientes option:selected").val();
  var id_vendedor = $("select#select_vendedor option:selected").val();

  var id_tipo_pago=$('#select_tipo_pago').val();
  var items=$("#items").val();
  var pares=$("#pares").val();
  var total_descto= $("#totdescto").text();
  var  monto_pago= $('#monto_pago').text();
  var abono=$('#abono').val();


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
          case 3:
              campo2 = $(this).find("#precio_venta").val();
              if (isNaN(campo2) == false) {
                precio_venta = parseFloat(campo2);
              } else {
                precio_venta = 0;
              }
              if(precio_venta === null)
                 precio_venta = 0;
              break;
          case 4:
                campo3 = $(this).find("#cant").val();
                if (isNaN(campo3) == false) {
                  cantidad = parseFloat(campo3);
                } else {
                  cantidad = 0;
                }
                if(  cantidad  === null)
                     cantidad  = 0;
                break;
          case 5:
                   subtotal= parseFloat($(this).text());
                   if (isNaN(subtotal)){
                     subtotal=0;
                   }
                    break;
          case 6:
                  campo4 = $(this).find("#descto").val();
                  if (isNaN(campo4) == false) {
                    descto = parseFloat(campo4);
                  } else {
                    descto = 0;
                  }
                  break;
          case 7:
                  subt_exento = $(this).text();
                    break;
          case 8:
                    subt_gravado = $(this).text();
                    break;
        }
      });
      if( cantidad && precio_venta){
        var obj = new Object();
        obj.id = campo0;
        obj.precio_venta = precio_venta;
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


  var seleccion = 0;

  if (!id_cliente || id_cliente == '-1') {
    seleccion = 0;
  } else {
    seleccion = 1;
  }
  if (seleccion == 0) {
    msg = 'Falta digitar cliente !';
  }
  var dataString = 'process=insert' +  '&cuantos=' + i +'&json_arr='+json_arr;
  dataString+= '&fecha_movimiento=' + fecha_movimiento;
  dataString+= '&id_cliente=' + id_cliente + '&total_venta=' + total_venta;
  dataString+= '&id_tipo_pago='+id_tipo_pago;
  dataString+= '&pares='+pares;
  dataString+= '&items='+items;
  dataString+= '&total_descuento='+total_descto;
  dataString+= '&monto_pago='+monto_pago;
  dataString+= '&abono='+abono;
  dataString+= '&id_vendedor='+id_vendedor;
  if (seleccion == 1) {
    $.ajax({
      type: 'POST',
      url: urlprocess,
      data: dataString,
      dataType: 'json',
      success: function(datax) {
        process = datax.process;
        factura = datax.factura;
        numero_doc=datax.numero_doc;
      //  display_notify(datax.typeinfo, datax.msg);

      activa_modal(factura,numero_doc);
      $("#inventable").find("tr:gt(0)").remove();
      $('#total_dinero').html("<strong>0</strong>");
      $('#totaltexto').html("<strong>Son:</strong>");
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
$(document).on("click", "#btnEsc", function(event)
{
var tipo_impresion = $("select#select_documento option:selected").val();
  if(tipo_impresion == "CCF")
  {
    nit=$('.modal-body #nit').val();
    nrc=$('.modal-body #nrc').val();
    nombreape=$('.modal-body #nombreape').val();
    if(nit =="" || nrc == "" || nombreape=="")
    {
      display_notify("Error", "Por favor complete los datos de facturacion");
    }
    else
    {
        reload1();
    }
  }
  else
  {
    reload1();
  }
});
function activa_modal(numfact,numdoc){
  urlprocess=$('#urlprocess').val();
	$('#viewModal').modal({backdrop: 'static',keyboard: false});
  $(".modal-body #id_factura").val(factura);
  $('#tipo_pago_tarjeta').hide();
  $('#tipo_pago_efectivo').hide();
	var totalfinal=parseFloat($('#totalfactura').val());
  var tipo_impresion = "RES";
  var abono=$('#abono').val();
  var tipo_pago=$('select#select_tipo_pago  option:selected').text();
  var tipopago = tipo_pago.split("|");
  var alias_tipopago =tipopago[1];
  var  monto_pago= $('#monto_pago').text();

  if  (alias_tipopago =="CON"){
    $('#tipo_pago_tarjeta').hide();
    $('#tipo_pago_efectivo').show();

  }
  if  (alias_tipopago =="TAR"){
    $('#tipo_pago_efectivo').hide();
    $('#tipo_pago_tarjeta').show();
  }

	//para traer datos de cliente si existe
	var id_cliente = $("select#select_clientes option:selected").val();
	var dataString = 'process=mostrar_datos_cliente' + '&id_client=' + id_cliente;
	$.ajax({
			type: 'POST',
			url: urlprocess,
			data: dataString,
			dataType: 'json',
			success: function(data) {
				dui = data.dui;
				tele1 = data.tele1;
        tele2 = data.tele2;
				nombreape= data.nombreape;
				$('#tele1').val(tele1);
        $('#tele2').val(tele2);
				$('#dui2').val(dui);
				$('#nombreape').val(nombreape);
			}
		});


	var facturado= totalfinal.toFixed(2);
  $(".modal-body #facturado").val(facturado);
    $(".modal-body #a_pagar").val(monto_pago);
  $(".modal-body #fact_num").html(numdoc);
}
$(document).on("click", "#btnPrintFact", function(event) {
  imprime1();
});
//Impresion
function imprime1(){

  var numero_doc = $(".modal-body #fact_num").html(); //del modal
  var print = 'imprimir_fact';
  var pass = true;
  var pass2 = true;
  var total=  $(".modal-body #facturado").val();
  var tipo_impresion ="RES";
  var id_factura=$(".modal-body #id_factura").val();
  var tipo_pago=$('select#select_tipo_pago  option:selected').text();
  var tipopago = tipo_pago.split("|");
  var alias_tipopago =tipopago[1];
  var abono=$('#abono').val()

	var dataString = 'process=' + print + '&numero_doc=' + numero_doc + '&tipo_impresion=' + tipo_impresion
    dataString+=  '&num_doc_fact=' + id_factura+ '&total=' + total;
    dataString+= '&abono='+abono;

  if  (alias_tipopago =="TAR"){
      var  emisor=$('#emisor').val();
      var voucher=$('#voucher').val();
      var numero_tarjeta=$('#numero_tarjeta').val();
      if( emisor =="" || numero_tarjeta == "" || voucher=="")
      {
        pass2 = false;
      }
      dataString+=  '&emisor=' + emisor+'&voucher='+voucher+'& numero_tarjeta='+ numero_tarjeta;
  }

	if (tipo_impresion=="RES"){
		dui=$('.modal-body #dui2').val();
		tel1=$('.modal-body #tele1').val();
    tel2=$('.modal-body #tele2').val();
		nombreape=$('.modal-body #nombreape').val();
    if(dui =="" || tel1 == "" || nombreape=="" || abono == ""|| abono <=0)
    {
      pass = false;
    }
		dataString +='&dui=' + dui+ '&tel1=' + tel1+ '&tel2=' + tel2+'&nombreape=' + nombreape;
 	}


  if(pass && pass2){
  $.ajax({
    type: 'POST',
    url: urlprocess,
    data: dataString,
    dataType: 'json',
    success: function(datos) {

      $('#nombreape').prop('readOnly', 'true');
      $('#dui2').prop('readOnly', 'true');
      $('#tele1').prop('readOnly', 'true');
      $('#tele2').prop('readOnly', 'true');
      $('#abono').prop('readOnly', 'true');
      $('#efectivo').prop('readOnly', 'true');
      $('#numero_tarjeta').prop('readOnly', 'true');
      $('#emisor').prop('readOnly', 'true');
      $('#voucher').prop('readOnly', 'true');
      $('#btnEsc').prop('disabled', false);

			var sist_ope = datos.sist_ope;
      var dir_print=datos.dir_print;
      var shared_printer_win=datos.shared_printer_win;
			var shared_printer_pos=datos.shared_printer_pos;
      var headers=datos.headers;
      var footers=datos.footers;
      var efectivo_fin = parseFloat($('#efectivo').val());
      var cambio_fin = parseFloat($('#cambio').val());
      var a_pagar=  $(".modal-body #a_pagar").val();
      //estas opciones son para generar recibo o factura en  printer local y validar si es win o linux
      if (tipo_impresion == 'RES') {
      				if (sist_ope == 'win') {
                $.post("http://"+dir_print+"printreswin1.php", {
      						datosventa: datos.facturar,
      						efectivo: efectivo_fin,
      						cambio: cambio_fin,
      						shared_printer_pos:shared_printer_pos,
                  headers:headers,
                  footers:footers,
                  a_pagar:a_pagar,
                })
              } else {
                $.post("http://"+dir_print+"printres1.php", {
                  datosventa: datos.facturar,
                  efectivo: efectivo_fin,
                  cambio: cambio_fin,
                  headers:headers,
                  footers:footers,
                  a_pagar:a_pagar,
                }, function(data, status) {
                  if (status != 'success') {}
                }
              );
              }
      }



    }
  });
  }
  else
  {
      display_notify("Error", "Por favor complete los datos solicitados");
  }
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


$(document).on("keyup", "#efectivo", function() {
  total_efectivo();
});

$(document).on("keyup", "#abono", function() {
  total_efectivo();
  saldo_pendiente=$('#facturado').val();
  saldo_pendiente = parseFloat(saldo_pendiente);
  saldo_pendiente = round(saldo_pendiente,2);
  abono = $('#abono').val();
  abono = parseFloat(abono);
  abono = round(abono,2);
  if(abono>saldo_pendiente)
  {
    $(this).val(saldo_pendiente);
  }
});
function total_efectivo() {
  var facturado =$('#facturado').val();
  var efectivo = parseFloat($('#efectivo').val());
  var abono = parseFloat($('#abono').val());
  var mensaje="";
  if (isNaN(parseFloat(efectivo))) {
    efectivo = 0.0;
  }
  if (isNaN(parseFloat(abono))) {
  abono = 0.0;
  }
  if (  abono>0.0 && efectivo>=abono){
    var cambio = efectivo - abono;
  }
  else {
      var cambio = 0.0;
      mensaje="<h3 class='text-danger'>" + "Falta dinero !!!" + "</h3>";

  }
  var cambio = round(cambio, 2);
  //alert("cambio:"+cambio)

  var tipo_pago=$('select#select_tipo_pago  option:selected').text();
  var tipopago = tipo_pago.split("|");
  var alias_tipopago =tipopago[1];
  if(alias_tipopago=='CON')
  {
    var cambio_mostrar = cambio.toFixed(2);
    $('#cambio').val(cambio_mostrar);
    $('#mensajes').html(mensaje);

    if(mensaje!="")
    {

    }
  }
  else
  {
    $('#mensajes').html('');
  }
}

//agregar cliente desde el modal de clientes
$(document).on("click", "#btnAddClient", function(event) {
  agregarcliente();
});
function agregarcliente() {
	urlprocess=$('#urlprocess').val();
  var nombress = $(".modal-body #nombress").val();
  var duii = $(".modal-body #duii").val();
  var tel1 = $(".modal-body #tel1").val();
  var tel2 = $(".modal-body #tel2").val();
  var dataString = 'process=agregar_cliente' + '&nombress=' + nombress;
  dataString += '&dui=' + duii + '&tel1=' + tel1 + '&tel2=' + tel2;
  $.ajax({
    type: "POST",
    url: urlprocess,
    data: dataString,
    dataType: 'json',
    success: function(datax) {
      var process = datax.process;
      var id_client = datax.id_client;
      // Agragar datos a select2
      //var nombreape = nombress + " " + apellidoss;
      $("#select_clientes").append("<option value='" + id_client + "' selected>" + nombress + "</option>");
      $("#select_clientes").trigger('change');

      //Cerrar Modal
      $('#clienteModal').modal('hide');
      //Agregar NRC y NIT al form de Credito Fiscal
			display_notify(datax.typeinfo, datax.msg);
			$(document).on('hidden.bs.modal', function(e) {
				var target = $(e.target);
				target.removeData('bs.modal').find(".modal-content").html('');
			});
    }
  });
}
$(document).on("click", "#btnEsc", function (event) {
  $('#clienteModal').modal('hide');
		//reload1();
});
