var urlprocess = "";
$(document).ready(function() {
  $('#inventable').arrowTable();
  urlprocess = $('#urlprocess').val();
  $(".datepick2").datepicker();
  $('#select_clientes').select2();
  $('#select_tipo_pago').select2();
  $('.select2').select2({
     allowClear: true
   });
   $(".i-checks").iCheck({
     checkboxClass: "icheckbox_square-green",
         radioClass: "iradio_square-green",
      });

//evento change para numero documento
var idtransace=$("#idtransace").val();
buscarVenta(idtransace);
buscarEncabezadoVenta(idtransace);
$("#numero_doc2").on('keyup', function(event){
  if (event.which  && this.value.length>=2 && event.which===13) {
    var alias_tipodoc = $("select#select_documento option:selected").val();
    $("#inventable").find("tr:gt(0)").remove();
      buscarVenta($(this).val(),alias_tipodoc);
      buscarEncabezadoVenta($(this).val(),alias_tipodoc);
  }
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
/*
  $("#select_documento").on('change',function(){
    var alias=$("#select_documento").val();
    if (alias=='TIK'){
      $('#numero_doc2').prop('readonly', true);
    }  else {
      $('#numero_doc2').prop('readonly', false);
    }
  });
*/
//fin evento para buscar por el barcode
$("#submit1").click(function(){
  senddata();
});

});
//buscar venta
function buscarVenta(idtransace){
  //para cargar compras y detalle compras
  urlprocess = $('#urlprocess').val();
  var dataString = 'process=buscarventa'+ '&idtransace=' + idtransace;
  $.ajax({
        type: "POST",
        url: urlprocess,
        data: dataString,
        dataType: 'json',
        success: function(datax){
          $.each(datax, function(key, value){
          var arr = Object.keys(value).map(function(k) { return value[k]});
          var id_producto= arr[0];
          var cantidad= arr[1];
          var id_fact= arr[2];
            console.log(id_producto+" "+cantidad)
            addProductList(id_producto, cantidad,id_fact);
          });
        }
   });
}

function buscarEncabezadoVenta(idtransace){
  //para cargar compras y detalle compras
    urlprocess = $('#urlprocess').val();

      var dataString = 'process=buscarencabezadoventa'+ '&idtransace=' + idtransace;
      $.ajax({
        type: "POST",
        url: urlprocess,
        data: dataString,
        dataType: 'json',
        success: function(datax) {
          id_cliente=datax.id_cliente
          tipopago=datax.tipopago
          alias_tipodoc=datax.alias_tipodoc
          numero_doc=datax.numero_doc
          tiene_dev=datax.tiene_dev;
          if (tiene_dev==1){
            $("#select_clientes").val(id_cliente).trigger("change"); //lo selecciona
            $("#select_tipo_pago").val(tipopago).trigger("change"); //lo selecciona
            $("#select_documento").val(alias_tipodoc).trigger("change"); //lo selecciona
            $("#numero_doc2").val(numero_doc); //lo selecciona
            $("#mensaje").html("");
          }
          else{
              $("#mensaje").html("<h5 class='text-danger'> No hay productos para realizar otra devolucion en esta venta!</h5>"); //lo selecciona

          }
        }
  });
}
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
function addProductList(id_prod,cantidad,id_fact) {
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
  var dataString = 'process=consultar_stock' + '&id_producto=' + id_prod+ '&id_fact=' + id_fact;
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
      var subt_exento = data.subt_exento;
      var subt_gravado=data.gravado;
      var descrip=data.descrip;
      var precios_vta = data.precios_vta;
      var descuento=data.descuento;
      if(pv_base==null)
         pv_base=0
      if(cp==null)
         cp=0
      add = "";
       subtotal=0.0;

       select_precios="<select name='select_precios' id='select_precios"+filas +"' class=' sel_prec form-control'  style='width:70px;'>";
       precios_vta.sort(function(a, b){return b-a});
        $.each(precios_vta, function(i,pr_unit){
        select_precios+="<option value="+pr_unit+">"+pr_unit+"</option>";
        });
        select_precios+="</select>";
        var pr_min = precios_vta[precios_vta.length - 1];
        var pr_ini = precios_vta[0];
        if (exento==1){
           subt_gravado=0.0;
          //var subt_exento=parseFloat(pr_ini)*cantidad;
           subt_exento=round(subt_exento,2)
        }
        else {
          //var subt_gravado=parseFloat(pr_ini)*cantidad;
          subt_gravado=round(subt_gravado,2)
           subt_exento=0.0;
        }

      var   tr_add = '<tr class="tr1">';


      tr_add += "<td class='col1 td1'><input type='hidden'  id='exento' name='exento' value='" + exento + "'>" + id_prod + "</td>";
      tr_add += '<td  class="col2 td1">' + descrip +' '+color+' talla:'+talla+ '</td>';
      tr_add += "<td class='text-success col1 td1' id='cant_stock'>" + existencias + "</td>";
      tr_add += "<td class='text-success col1 td1 preccs'>"+select_precios+"</td>";
      tr_add += "<td class='text-success col3 td1'><input type='hidden'  id='precio_venta_inicial' name='precio_venta_inicial' value='"+pr_ini+"'><div class='col-xs-2'><input type='text'  class='form-control decimal'  id='precio_venta' name='precio_venta' value='"+pr_ini+"' style='width:60px;' readonly></div></td>";
      tr_add += "<td class='text-success col4 td1'><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value='"+cantidad+"' style='width:60px;'></div></td>";
      tr_add += "<td class='col5 td1'><div class='col-xs-2'><input type='text'  class='form-control decimal' id='descto' name='descto'   value='"+descuento+"' style='width:60px;' readOnly></div></td>";
      tr_add += '<td class="col6 td1" id="subt_exento">' + subt_exento+ '</td>';
      tr_add += '<td class="col7 td1">'+"<input type='hidden'  id='subt_descto' name='subt_descto' value='0.0'>"  + subt_gravado+ '</td>';
      //tr_add += '<td class="Delete col8 td1"><input id="delprod" type="button" class="btn btn-danger fa"  value="&#xf1f8;"></td>';
      tr_add +='<td class="col7 td1">'+"<div class='checkbox i-checks'><label> <input id='myCheckboxes' name='myCheckboxes' type='checkbox' value='1'> <i></i></label></div>"+ '</td>';;
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
          //  setRowCant(posicion_fila2);
        }
      });
      if (existe == false) {
        $("#inventable").append(tr_add);
        $(".i-checks").iCheck({
          checkboxClass: "icheckbox_square-green",
              radioClass: "iradio_square-green",
           });
        $(".decimal").numeric();
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
//ver cambios en checkbox para totalizar

$(document).on('ifChecked', '#myCheckboxes', function(){
      totales();
      totalfact();
});

$(document).on('ifUnchecked', '#myCheckboxes', function(){

      totales();
      totalfact();
});


  totales();
  totalfact();
//});
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
              campo2 = parseFloat($(this).text());

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
      if(descto>0){
        descto=round(descto/100,2);

      }else{
        descto=0;
      }
      if (exento==1){

        subt_exento = precio_venta* cantidad- subt_descto;
        subt_gravado =0;
      }
      else{
        subt_gravado = precio_venta* cantidad- subt_descto;
        subt_descto=precio_venta*descto* cantidad;
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

// reemplazar valor de select precios modificar para 1 linea
function setRowPrice(fila, new_price, cantidad)
{
      precio_venta=parseFloat(new_price);
      var  subtotal = 0;
      if(cantidad>0 && cantidad != "")
      {
        subtotal = (precio_venta*cantidad);
        var new_subtotal=subtotal.toFixed(2);
      }
      else
      {
        var new_subtotal = 0;
      }
      $('#inventable').find('tr#'+fila).find('td:eq(8)').html(new_subtotal);
      totalfact();
}

//Funcion para recorrer la tabla completa y pasar los valores de los elementos a un array
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
  var devtot_exento= 0;
  var devtot_gravado=0;
	$("#inventable>tbody  tr").each(function (index) {
		 if (index>=0){
           var subtotal=0; t_exento= 0; t_gravado=0;sel_prod=0;
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
                      case 9:
                         seleccionado= $(this).find("#myCheckboxes:checked").val();

                        if(seleccionado==undefined){
                          seleccionado=0
                        }
                        else{
                            seleccionado=1
                            t_exento= parseFloat($(this).closest("tr").find('td:eq(7)').html());
                            t_gravado= parseFloat($(this).closest("tr").find('td:eq(8)').html());

                            sel_prod++
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
           devtot_exento+=t_exento;
           devtot_gravado+=t_gravado;
           console.log("devtot_gravado:"+devtot_gravado)
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
  total_devolucion=devtot_exento+devtot_gravado;
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
  total_devolucion_mostrar=total_devolucion.toFixed(2);
$('#total_devolucion').html(total_devolucion_mostrar);
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
  $('#totalfactura').val(total_final_mostrar);
}

function senddata(){
  array_seleccionados=new Array()
  var   seleccionado2 = "";
  var cuantos_select=0;
  $("#inventable :checked").each(function()
  {
    tr = $(this).parents("tr");
     seleccionado2 = tr.find('td:eq(1)').text();
    array_seleccionados.push(seleccionado2);
      cuantos_select ++;

  });

  var i = 0;
  var precio_venta= 0;  cantidad= 0; descto= 0;
  var subtotal=0; subt_exento=0; subt_gravado=0;
  var StringDatos = "";
  var total_venta = $('#total_final').text();
  var fecha_movimiento = $("#fecha2").val();
  var numero_doc = $("#numero_doc2").val();
  var alias_tipodoc = $("select#select_documento option:selected").val();
  var id_cliente = $("select#select_clientes option:selected").val();
  //var tipo_pago= $("select#select_tipo_pago option:selected").val();
  var total_gravado= $('#total_gravado_sin_iva').text();
  var total_exento= $('#total_exenta').text();
  var total_iva= $('#total_iva').text();
  var total_retencion= $('#total_retencion').text();
  var total_percepcion= $('#total_percepcion').text();
  var numero_dias=$("#numero_dias").val();
  var id_tipo_pago=$('#select_tipo_pago').val();
  var items=$("#items").val();
  var pares=$("#pares").val();
  var total_descto= $("#totdescto").val();
  var array_json = new Array();
  var verificar = 'noverificar';
  var verificador = [];
  var idtransace=$("#idtransace").val();
  var total_devolucion=$('#total_devolucion').text();
 var seleccionado=0
 var sel_prod = 0
 var total_devuelto_g=0;
 var total_devuelto_e=0;
 var devtot_exento=0;
 var devtot_gravado=0;
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
          case 4:
            campo2 = $(this).find("#precio_venta").val();
            if (isNaN(campo2) == false) {
              precio_venta = parseFloat(campo2);
            } else {
              precio_venta = 0;
            }
            if(precio_venta === null)
               precio_venta = 0;
            break;
            case 5:
              campo3 = $(this).find("#cant").val();
              if (isNaN(campo3) == false) {
                cantidad = parseFloat(campo3);
              } else {
                cantidad = 0;
              }
              if(  cantidad  === null)
                   cantidad  = 0;
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
                case 9:
                   seleccionado= $(this).find("#myCheckboxes:checked").val();
                  if(seleccionado==undefined){
                    seleccionado=0
                  }
                  else{
                      seleccionado=1
                      sel_prod++
                      t_exento= parseFloat($(this).closest("tr").find('td:eq(7)').html());
                      t_gravado= parseFloat($(this).closest("tr").find('td:eq(8)').html());
                  }
                  break;
        }
      });

      if( cantidad && precio_venta && seleccionado==1){
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
        devtot_exento+=t_exento;
        devtot_gravado+=t_gravado;
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
  if (!id_cliente || id_cliente == '-1') {
    seleccion = 0;
  } else {
    seleccion = 1;
  }
  if (!alias_tipodoc || alias_tipodoc == '-1' ) {
    sel_tipo_doc = 0;
  } else {
    sel_tipo_doc = 1;
  }

  if(alias_tipodoc=='TIK' ||alias_tipodoc=='DEV'){
    sel_tipo_doc = 1;
    seleccion_doc =1;
  }
  if (seleccion == 0) {
    msg = 'Falta digitar cliente !';
  }
  if (seleccion_doc == 0 ) {
     msg = 'Falta digitar Numero de Documento de Venta!';
  }
  if (sel_tipo_doc == 0) {
     msg = 'Falta digitar Tipo  de Documento de Venta!';
  }
  if (sel_prod == 0) {
     msg = 'Seleccione al menos un  producto!';
  }
  var dataString = 'process=insert' +  '&cuantos=' + i +'&json_arr='+json_arr;
  dataString+= '&fecha_movimiento=' + fecha_movimiento + '&numero_doc=' + numero_doc+ '&alias_tipodoc=' + alias_tipodoc
  dataString+= '&id_cliente=' + id_cliente + '&total_venta=' + total_venta;
  dataString+= '&total_gravado='+ total_gravado;
  dataString+= '&total_exento='+total_exento;
  dataString+= '&total_iva='+total_iva;
  dataString+='&total_retencion='+ total_retencion;
  dataString+= '&total_percepcion='+total_percepcion;
  dataString+= '&numero_dias='+numero_dias;
  dataString+= '&id_tipo_pago='+id_tipo_pago;
  dataString+= '&pares='+pares;
  dataString+= '&items='+items;
  dataString+= '&idtransace='+idtransace;
  dataString+= '&total_descuento='+total_descto;
  dataString+= '&total_devuelto_e='+devtot_exento;
  dataString+= '&total_devuelto_g='+devtot_gravado;
  dataString+= '&total_devolucion='+total_devolucion;


  if (seleccion == 1 && seleccion_doc == 1  && sel_tipo_doc == 1 && sel_prod>0) {
    $.ajax({
      type: 'POST',
      url: urlprocess,
      data: dataString,
      dataType: 'json',
      success: function(datax) {
        process = datax.process;
        factura = datax.factura;
        numero_doc=datax.numero_doc;
        //display_notify(datax.typeinfo, datax.msg);

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
  location.href ="admin_factura_rangos.php";
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
  var tipo_impresion='DEV';
  $(".modal-body #id_factura").val(factura);
  $('#tipo_pago_tarjeta').hide();
  $('#tipo_pago_efectivo').hide();
	var totalfinal=parseFloat($('#total_devolucion').text());

  var numero_doc2 = $("#numero_doc2").val(); //es el del form
  $(".modal-body #num_doc_fact").val(numero_doc2); //del modal


		//para traer datos de cliente si existe
		var id_client = $('#id_cliente').val();
		var dataString = 'process=mostrar_datos_cliente' + '&id_client=' + id_client;
		$.ajax({
			type: 'POST',
			url: urlprocess,
			data: dataString,
			dataType: 'json',
			success: function(data) {
			  dui = data.dui;
				nombreape= data.nombreape;
				$('#dui').val(dui);
				$('#nombreape').val(nombreape);
			}
		});


	var facturado= totalfinal.toFixed(2);
  $(".modal-body #facturado").val(facturado);
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

  var total=  $(".modal-body #facturado").val();
  var tipo_impresion ='DEV';
  var id_factura=$(".modal-body #id_factura").val();
  var	dui=$('#dui').val();
  var nombreape= $('#nombreape').val();
  if(dui =="" || nombreape=="")
  {
    pass = false;
  }
	var dataString = 'process=' + print + '&numero_doc=' + numero_doc + '&tipo_impresion=' + tipo_impresion
    dataString+=  '&num_doc_fact=' + id_factura+ '&total=' + total;
    dataString +='&dui=' + dui+'&nombreape=' + nombreape;

  if(pass ){
  $.ajax({
    type: 'POST',
    url: urlprocess,
    data: dataString,
    dataType: 'json',
    success: function(datos) {
			var sist_ope = datos.sist_ope;
      var dir_print=datos.dir_print;
      var shared_printer_win=datos.shared_printer_win;
			var shared_printer_pos=datos.shared_printer_pos;
      var headers=datos.headers;
      var footers=datos.footers;
      //estas opciones son para generar recibo de DEVOLUCION en  printer local y validar si es win o linux

              if (sist_ope == 'win') {
                $.post("http://"+dir_print+"printdevwin1.php", {
                  datosventa: datos.facturar,
                  efectivo: 0,
                  cambio: 0,
                  shared_printer_pos:shared_printer_pos,
                  headers:headers,
                  footers:footers,
                })
              } else {
                $.post("http://"+dir_print+"printdev1.php", {
                  datosventa: datos.facturar,
                  efectivo: 0,
                  cambio: 0,
                  headers:headers,
                  footers:footers,
                }
                /*, function(data, status) {
                  if (status != 'success') {}
                } */
              );
              }
    //  }

    }
  });
  }
  else
  {
      display_notify("Error", "Por favor complete los datos de facturacion");
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
