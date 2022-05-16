var urlprocess = "print_bcode_preingreso.php";
$(document).ready(function(){
    //urlprocess = $('#urlprocess').val();
urlprocess = "print_bcode_preingreso.php";
id_compras=$('#id_compras').val();
buscarCompras(id_compras);
  $('.loading-overlay').hide();
  $(".datepick2").datepicker();
  $('#select_proveedores').select2();
  $('#select_colores').select2();
  $('#select_pedidos').select2();
  $('#select_documento').select2();


  //datos_proveedores();

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
$("#submit1").click(function(){
  senddata();
});

function buscarCompras(id_compras){
  //para cargar compras y detalle compras

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
          var descrip= arr[1];
          var cantidad= arr[2];
          var estilo=arr[3];
          var talla=arr[4];
          var color=arr[5];
          var costo= arr[6];
          var precio= arr[7];
            var   tr_add = '<tr class="tr1">';
            tr_add += "<td class='col1 td1'>" + id_producto + "</td>";
            tr_add += '<td  class="col2 td1">' + descrip+ '</td>';
            tr_add += '<td  class="col2 td1">' + estilo+ '</td>';
            tr_add += '<td  class="col2 td1">' + talla+ '</td>';
            tr_add += '<td  class="col2 td1">' + color+ '</td>';
            tr_add += '<td  class="col2 td1">' + cantidad+ '</td>';
            tr_add += '<td  class="col2 td1">' + costo+ '</td>';
            tr_add += '<td  class="col2 td1">' + precio+ '</td>';
            tr_add += '</tr>';
            $("#inventable").append(tr_add);
          //addProductList(id_producto, cantidad);
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
//  boton imprimir barcodes
// Agregar productos a la lista del inventario
function addProductList(id_prod,cantidad) {
  id_prod=$.trim(id_prod);
  cantidad=parseInt(cantidad)
  var id_prev = "";
  var id_new=id_prod;
  var id_previo = new Array();
  var filas=0;
  $("#inventable tr").each(function(index) {
    if (index > 0) {
      var campo0, campo1, campo2, campo5;

      $(this).children("td").each(function(index2) {
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
      add = "";

      var   tr_add = '<tr class="tr1">';
      tr_add += "<td class='col1 td1'><input type='hidden'  id='exento' name='exento' value='" + exento + "'>" + id_prod + "</td>";
      tr_add += '<td  class="col2 td1">' + descrip +' '+color+' talla:'+talla+ '</td>';
      tr_add += '<td  class="col2 td1">' +cp+ '</td>';
      tr_add += '<td  class="col2 td1">' + cantidad+ '</td>';
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
        $("#inventable").append(tr_add);
          totalfact();
        //$(".decimal").numeric();
      }
      if (existe==true ){
            $(".decimal").numeric();
            posicion_fila=posicion_fila+1;
            setRowCant(posicion_fila);
      }
    }
  });
    totalfact();
}


// reemplazar valores de celda cantidades
function setRowCant(rowId){
  var cantidad_anterior = $('#inventable tr:nth-child(' + rowId + ')').find('td:eq(3)').find("#cant").val();
  var cantidad_nueva = parseFloat(cantidad_anterior) + 1;
  $('#inventable tr:nth-child(' + rowId + ')').find('td:eq(3)').find("#cant").val(cantidad_nueva);
  //totales();
  //totalfact();
}

$(document).on("blur", "#inventable", function() {
  totales();
  totalfact();
})

//function to round 2 decimal places
function round(value, decimals) {
  return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}
//Calcular Totales del grid
function totales() {

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


function reload1() {
  location.href = urlprocess;
}

function print_bcodes(){
  var id_compras=$('#id_compras').val();
  var dataString = 'process=buscarprodcant'+ '&id_compras=' + id_compras;
  $.ajax({
    type: "POST",
    url: urlprocess,
    data: dataString,
    dataType: 'json',
    success: function(datos) {
      config=datos.pop()

      var sist_ope = config.sist_ope;
      var dir_print=config.dir_print;
      var shared_printer_barcode=config.shared_printer_barcode;
      //alert(dir_print+" "+sist_ope+" "+shared_printer_barcode)
      if (sist_ope == 'win') {
        $.post("http://"+dir_print+"printbcodewin1.php", {
          datosproductos:datos,
          shared_printer_barcode:shared_printer_barcode
        })
      } else {
        $.post("http://"+dir_print+"printbcode1.php", {
            datosproductos: datos
        });
      }
    }
});

}
