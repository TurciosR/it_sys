
var ads = 0;
var urlprocess = "";
$(document).ready(function()
{
  $("#keywords").focus();
  //ocultar el div de encabezado de la factura
  $("#datos_encabezado").hide();
  urlprocess = $('#urlprocess').val();
  $('#inventable').arrowTable();
  $('#loadtable').arrowTable();
  $('#numero_doc2').prop('readonly', true);

  $('.loading-overlay').hide();
  $(".datepick2").datepicker();
  $('#select_clientes').select2();
  $(".sel_prec").select2();
  $('#select_tipo_pago').select2();
  $(".select2").select2();

  $('#ver_vales').on('keyup', function(event){
   if (event.which==13)
   {
      cargar_vale($(this).val());
   }
  });
  $('#ver_reserva').on('keyup', function(event){
   if (event.which==13)
   {
      cargar_reserva($(this).val());
   }
  });
 //Campos criterio de Busqueda
 $('#keywords').on('keyup', function(event) {
   var kw=$('#keywords').val();
   var tecla = event.keyCode;
   if(tecla == 13)
   {
      if($("#mostrardatos tr").length > 0)
      {
        $("#mostrardatos > tr:first").addClass("hoverTable2");
        $("#mostrardatos > tr:first").focus();
      }
   }
   else
   {
      if (event.which!==8)
      {
        if(kw.length>=3)
        {
          $("#loadtable").find("tr:gt(0)").remove();
          searchFilter();
        }
      }
       else
      {
        searchFilter();
      }
  }
 });
 $('#modelo').on('keyup', function(event){
   if (event.which!==8)
   {
      $("#loadtable").find("tr:gt(0)").remove();
       searchFilter();
   }
   else
   {
      searchFilter();
   }
 });
 $('#marca').on('keyup', function(event){
   var val = $(this).val();
   if (event.which!==8)
   {
      if(val.length>=2)
      {
        $("#loadtable").find("tr:gt(0)").remove();
        searchFilter();
      }
   }
   else
   {
      searchFilter();
   }
 });
 $('#serie').on('keyup', function(event){
    var val = $(this).val();
    if (event.which!==8)
    {
       if(val.length>=2)
       {
         $("#loadtable").find("tr:gt(0)").remove();
         searchFilter();
       }
    }
    else
    {
       searchFilter();
    }
  });
  $('html,body').animate({
      scrollTop: $("#content").offset().top
  }, 1500);


  //vales por devolucion o por reserva
  $("#select_valeaa").on('change',function(){
  if($('#select_vales :selected').val()==-1){
    var val=0.0;
  }
  else{
    var data=$('#select_vales :selected').html()
    valor_vales=data.split('|')
    var val=valor_vales[2]
    var id_vale=valor_vales[0]
    var tipo_vale=valor_vales[3]
    var tipo_vale_txt='* VALOR VALE '+tipo_vale;
    if(tipo_vale=='RESERVA PRODUCTO')
    {
      //$("#inventable").find("tr:gt(0)").remove();
      var dataString = 'process=buscar_reserva'+'&id_reserva='+id_vale;
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
          addProductList(id_producto, cantidad,0);
        });
      }
    });
    }

  }
  $('#tipo_vale').html("<strong>"+tipo_vale_txt+"</strong>");
  $('#valor_vale').html(val);
  totales();
}); //end document ready
$(document).on('keyup keypress', '.decimal_positive', function()
{
  var aValue = $(this).val();
  if((aValue)<0){
    $(this).val(aValue.slice(0,-1));
  }
  if($.isNumeric(aValue) === false){
    $(this).val(aValue.slice(0,-1));
  }

});
//evento para mostrar el encabezado de la factura
$(document).on("click", "#btnFact", function() {
    $("#datos_encabezado").show();
    esperar();
    $('html, body').animate({
      scrollTop: $("#datos_encabezado").offset().top
    }, 1000);

});
function esperar(){

  setTimeout(function() {
    for (i = 0; i < 100; i++){text="abc";};
  }, 100);
   $('#select_clientes').select2('open').select2('close');
}

// eventos teclas de funcion
$(document).keydown(function(e){
  if(e.which == 120) { //F9 guarda factura
    e.preventDefault();
    if ($('#total_gravado').text()!=0 && $("#items").val()>0){
       senddata();
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
  if(e.which == 113){ //F2
      $("#btnFact").click();
  }
  if(e.which == 115)
  {
    $("#cstok").click();
  }
  if ( e.altKey &&  e.which === 85  ) { //alt + u  return to text #keywords
      $("#keywords").focus();
      $("#keywords").val("");
  }
  if ( e.altKey &&  e.which === 73  ) {  //calt + i return to table inventable
   if($("#inventable > tbody> tr").length > 0){
      $("#inventable > tbody> tr:first").find('td:eq(5)').find("#cant").focus();
   }
  }

});
// fin Teclas de funcion
//evento change para select clientes
 $("#select_clientes").change(function(event){
   datos_clientes();
   totales();
 });
 //si cambia el tipo doc

  $("#select_documento").on('change',function(){
    var alias=$("#select_documento").val();
    if (alias=='TIK'){
      $('#numero_doc2').prop('readonly', true);
    }  else {
      $('#numero_doc2').prop('readonly', false);
    }
    totales();
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
       totales();
     }
   });
}
function searchBarcode(barcode) {
  addProductList(barcode,1)
}

function searchFilter() {
  var keywords = $('#keywords').val();
  var serie = $('#serie').val();
  var modelo = $('#modelo').val();
  var marca = $('#marca').val();
  var barcode = $('#barcode').val();
  var limite = $('#limite').val();

  getData(keywords,marca,modelo,serie,barcode,limite)
}

function getData(keywords,marca,modelo,serie,barcode,limite){
  urlprocess = $('#urlprocess').val();
  $.ajax({
    type: 'POST',
    url: urlprocess,
    data: {
      process: 'traerdatos',
      keywords: keywords,
      serie: serie,
      modelo: modelo,
      marca: marca,
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
  $("#inventable >tbody >tr").each(function(index){
    if (index >=0) {
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
    success: function(datax) {
      var cp = datax.costo_prom;
      var marca = datax.marca;
      var modelo = datax.modelo;
      var serie = datax.serie;
      var existencias= datax.existencias;
      var precio1 = datax.precio1;
      var exento = datax.exento;
      var descuento = datax.descuento;
      var descrip=datax.descrip;
      var precios_vta = datax.precios_vta;
      var precios=datax.precios;
      var tiene_garantia=datax.tg;
      if(precio1==null)
         precio1=0
      if(cp==null)
         cp=0
      add = "";
       subtotal=0.0;
        var  valor_descuento=0;
       if(parseFloat(descuento)>0){
          valor_descuento=precio1*(descuento/100);
          valor_descuento=round(valor_descuento,2);
       }
      // var subtotal=parseFloat(precios_vta)*cantidad;

       if (exento==1){
         var subt_gravado=0.0;
         var subt_exento=parseFloat(precios_vta)*cantidad-valor_descuento;
          subt_exento=round(subt_exento,2)
       }
       else {
         var subt_gravado=parseFloat(precio1)*cantidad-valor_descuento;
         subt_gravado=round(subt_gravado,2)
         var subt_exento=0.0;
       }

        pr1=precios_vta[0];
  		  var subtotal=subt(1,datax.precio1);
        subt_mostrar=subtotal.toFixed(2);
  		  pr_min=precios_vta[3];

    //agregar columna a la tabla de facturacion
    var resta_stock=0;

    $.each(id_previo, function(i, id_prod_ant){
      if (id_prod == id_prod_ant) {
        resta_stock+=1;
      }
    });
    var garantia="";
      //existencias=  existencias - resta_stock;
    //  if (existencias>0){
    filas2=filas;
    var read="";
    if(tiene_garantia==1)
     read="readOnly"
    existencias=parseInt(existencias);
   var lista1=datax.select_precios;
     tr_add = '';
     tr_add += "<tr class='tr1'  id='"+filas+"'>";
     tr_add += "<td class='col5'><input type='hidden'  id='exento' name='exento' value='" + exento + "'>" + id_prod + "</td>";
     tr_add += "<td class='col20 text_left'>" + descrip +' '+marca+' '+modelo+ "</td>";
     //tr_add += "<td class='text-success col5' id='cant_stock1'>"+existencias+"</td>";
     tr_add += "<td class='col5'>"+existencias+"</td>";
     tr_add += "<td class='col10 td1' id='select_prec'>"+lista1+ "</td>";
     tr_add += "<td class='col10 td1'><input type='hidden'  id='precio_venta_inicial' name='precio_venta_inicial' value='"+datax.precio1+"'><input type='text'  class='decimal_positive td_success'  id='precio_venta' name='precio_venta' value='"+datax.precio1+"' style='width:60px;'></td>";
     tr_add += "<td class='col10 td1'><input type='hidden'  id='tiene_garantia' name='tiene_garantia' value='"+tiene_garantia+"'><div class='col-xs-2'><input type='text'  class='decimal qty' id='cant' name='cant' value='"+cantidad+"' style='width:60px;'"+read+"></div></td>";
     tr_add += "<td class='col10 td1'><input type='text'  class='decimal' id='subtotal' name='subtotal'  value='"+subt_mostrar+"' style='width:60px;' readOnly></td>";
     tr_add += "<td class='col10 td1 subtotals'><div class='col-xs-2'><input type='text'  class='decimal_positive' id='descto' name='descto'  value='"+valor_descuento+"' style='width:60px;'></div></td>"
     tr_add += "<td class='col10 td1'>"+"<input type='hidden'  id='subt_descto' name='subt_descto' value='0.0'>" +"<input type='text'  class='decimal' id='subtotal_fin' name='subtotal_fin'  value='"+subt_gravado+"' style='width:60px;' readOnly></td>";
     tr_add += '<td class="Delete col10 td1"><input id="delprod" type="button" class="btn btn-danger fa"  value="&#xf1f8;"></td>';
     tr_add += '</tr>';

     var tipo_impresion = $("#tipo_impresion  option:selected").val();
            var pass=true;
            if (filas > 9 && tipo_impresion==="CCF") {
              pass=false;

            }
            if (filas>9 && tipo_impresion==="COF") {
              pass=false;
            }
            if (filas > 19 && tipo_impresion=="ENV") {
              pass=false;
            }
            if (filas > 19 && tipo_impresion=="NCR") {
              pass=false;
            }

           if (pass===false) {
             var typeinfo = 'Warning';
             var msg = 'Numero de Filas en Factura excede el maximo permitido !';
             display_notify(typeinfo, msg);
           } else {
             var existe = 0;
             var posicion_fila = 0;
             var posicion_fila2 = 0;
             $.each(id_previo, function(i, id_prod_ant) {
               if (id_prod ==id_prod_ant) {
                 existe = 1;
                 //posicion_fila = i
                 posicion_fila=i+1
                   setRowCant(posicion_fila);
               }
             });
             if (existe == 0 && existencias>0){
               $("#inventable>tbody").append(tr_add);
               $(".sel").select2();
               $(".decimal").numeric();
               $(".decimal-2-places").numeric({ decimalPlaces: 2 });
             }
      }
      totales();
      ads = 0;
  }
})
}

// reemplazar valores de celda cantidades
function setRowCant(rowId) {
  var stock1 =0
 var  tr=$('#inventable>tbody> tr:nth-child('+rowId+')')
/* if (rowId==1){
   stock1 = $("#inventable > tbody> tr:first").find('td:eq(2)').text();
 }
 else{*/
   stock1 =$('#inventable>tbody> tr:nth-child('+rowId+')').find('td:eq(2)').text()
 //}
 stock1 =tr.find('td:eq(2)').text();
  stock1 = parseInt(stock1)

  var cantidad_anterior = tr.find('td:eq(5)').find("#cant").val();
  var cantidad_nueva = parseFloat(cantidad_anterior) + 1;
  if (cantidad_nueva>stock1){
    cantidad_nueva=stock1;
  }
  //alert("cantidad:"+cantidad_nueva+" stock:"+stocka)

  tr.find('td:eq(5)').find("#cant").val(cantidad_nueva);
  actualiza_total(tr);
}
//Reemplazar el precio de acuerda al valor seleccionado
$(document).on('change', '.sel_prec', function(){
  var tr= $(this).parents("tr");
  var precio = $(this).find(':selected').text();
  tr.find('td:eq(4)').find('#precio_venta').val(precio);

  actualiza_total(tr);
});
//cantidad y cant_stock
$(document).on("keyup", "#cant, #precio_venta,#descto", function(){
  var tr= $(this).parents("tr");
  actualiza_total(tr);
});

function actualiza_total(tr){
  var existencias = tr.find('td:eq(2)').text();
  var cantidad=tr.find('td:eq(5)').find('#cant').val();
  if (isNaN(cantidad)|| cantidad==""){
    cantidad=0;
  }
  if (parseInt(cantidad)>parseInt(existencias)){
    tr.find('td:eq(5)').find("#cant").val(existencias);
  }
  if (parseInt(existencias)>0 && parseInt(cantidad)==0 ){
    tr.find('td:eq(5)').find("#cant").val("");
  }
  var precio=tr.find('td:eq(4)').find('#precio_venta').val();
  if (isNaN(precio)|| precio==""){
    precio=0;
  }
  var subtotal=subt(cantidad,precio);
  subt_mostrar=subtotal.toFixed(2);
  tr.find('td:eq(6)').find("#subtotal").val(subt_mostrar);
  descuento=  tr.find('td:eq(7)').find("#descto").val();
  if (isNaN(descuento)|| descuento==""){
   descuento=0;
  }
  if (parseInt(descuento)>parseInt(precio)){
    tr.find('td:eq(7)').find("#descto").val("");
  }
  if(parseFloat(descuento)<parseFloat(subtotal)){
   var subtotal2=parseFloat(subtotal)-parseFloat(descuento);
  }
  else{
   var subtotal2=0
  }
  var subt_mostrar2=subtotal2.toFixed(2);
  tr.find('td:eq(8)').find("#subtotal_fin").val(subt_mostrar2);
  totales();
}

$('#loadtable tbody ').on( 'blur', 'tr', function () {
  $(this).removeClass('hoverTable2');
})
$('#loadtable tbody').on( 'focus', 'tr', function () {
  $(this).addClass('hoverTable2');
})
//navigate asign tabindex to tr
$('#loadtable').on( 'click', 'tr', function ()
{
  if(ads == 0)
  {
    document.onkeydown = checkKey;
    var id_prod=$(this).closest("tr").find('td:eq(0)').text();
    addProductList(id_prod,1);
  }
  ads = 1;
});
$('#loadtable').on( 'focus', 'tr', function () {
document.onkeydown = checkKey
});

function checkKey(e) {
    var rowCount = $("#loadtable >tbody >tr").length;

    var event = window.event ? window.event : e;
    if(event.keyCode == 40){ //down
      var idx = $("tr:focus").attr("tabindex");
        $("#loadtable").find("tr[tabindex="+idx+"]").removeClass('hoverTable2');
      idx++;
      if(idx >=rowCount){
        idx = 0;
      }
      $("tr[tabindex="+idx+"]").focus();
    $("#loadtable").find("tr[tabindex="+idx+"]").addClass('hoverTable2');
    }
    if(event.keyCode == 38){ //up

      var idx = $("tr:focus").attr("tabindex");
        $("#loadtable").find("tr[tabindex="+idx+"]").removeClass('hoverTable2');
      idx--;
      if(idx < 0){
        idx = rowCount-1;
      }
      $("tr[tabindex="+idx+"]").focus();
      $("#loadtable").find("tr[tabindex="+idx+"]").addClass('hoverTable2');
    }
    if(event.keyCode == 13){ //up
      var idx = $("tr:focus").attr("tabindex");

      var id_prod = $('#loadtable').find("tr[tabindex="+idx+"]").find('td:first').text();
       addProductList(id_prod,1); //agregar  producto a lista
    }
}
//end navigate
//Evento que se activa al perder el foco en precio de venta y cantidad:
$(document).on("focus", "#cant, #precio_venta, #descto", function() {
  $(this).addClass('hoverTable2');
  })
  $(document).on("blur", "#cant, #precio_venta, #descto", function() {
    $(this).removeClass('hoverTable2');
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
});
//function to round 2 decimal places
function round(value, decimals) {
  return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}
//obtener subtotal cantidad x precio
function subt(qty,price){
  subtotal=parseFloat(qty)*parseFloat(price);
  subtotal=round( subtotal,2);
  return subtotal;
}

//Funcion para recorrer la tabla completa y pasar los valores de los elementos a un array
function totales(){
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

  var total_gravado=0;
  var total_exento=0;
  var subt_gravado=0;
  var  subt_exento=0;
  var  subtotal=0;
  var total_descto=0;
  var total_sin_descto=0;
  var subt_descto=0;
  var total_final=0;
	var StringDatos='';
  var filas=0;

	$("#inventable>tbody tr").each(function (index) {
		 if (index>=0){
       subt_cant=$(this).find("td:eq(5)").find("#cant").val()
       subtotal_sindesc=$(this).find("td:eq(6)").find("#subtotal").val()

       subtotal_desc=$(this).find("td:eq(7)").find("#descto").val()
       if (isNaN( subtotal_desc)||  subtotal_desc==""){
         subtotal_desc=0;
       }
       if (isNaN( subt_cant)||  subt_cant==""){
         subt_cant=0;
       }
       subtotal_final=$(this).find("td:eq(8)").find("#subtotal_fin").val()

       total_sin_descto+=parseFloat(subtotal_sindesc)
       total_descto+=parseFloat( subtotal_desc)
       total_final+=parseFloat(subtotal_final);
       totalcantidad +=parseFloat(subt_cant);
       filas+=1;
      }


        });
  // IMPUESTOS
  total_final=round(total_final,2);
  var total_mostrar=total_final.toFixed(2)
  totcant_mostrar=totalcantidad.toFixed(2)
  var  total_descto_mostrar=total_descto.toFixed(2)
  $('#totcant').text(totcant_mostrar);
  $('#totfin').text(total_mostrar);
  $('#totalfactura').val(total_mostrar);
  $('#totdescto').text(total_descto_mostrar);

	total_dinero=total_final.toFixed(2);

  var total_sin_descto_mostrar = total_sin_descto.toFixed(2)
  var total_descto_mostrar = total_descto.toFixed(2)

    var total_iva =0.00
  if (id_tipodoc=='CCF'){
   total_iva = round((total_final * iva), 4);
  }
  var tot_discount=$("#totdescto").text();
  txt_war="class='text-danger'"
  var total_iva_mostrar = total_iva.toFixed(2);
  $('#total_sin_descto').html(total_sin_descto_mostrar);

  $('#total_gravado').html(total_mostrar);
  total_exento=0;
  $('#total_gravado_sin_iva').html(total_sin_descto_mostrar);
  $('#total_descuento_final').html(tot_discount);
  $('#total_exento').html(total_exento);
  $('#total_exenta').html(total_exento);

  $('#total_iva').html(total_iva_mostrar);
  total_gravado_iva= total_final+ total_iva;
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
  var total_final=(total_exento+total_iva +total_final+total_percepcion)-(total_retencion1+total_retencion10);
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
  $('#totaltexto').load(urlprocess,{'process': 'total_texto','total':total_mostrar});
  $('#totalesura').val(total_final_mostrar);
  // a pagar, por si hay uso de vale por devoluciones
  valor_vale=parseFloat($('#valor_vale').text());
  valor_reserva=parseFloat($('#valor_reserva').text());
  monto_pago=total_final-(valor_vale+valor_reserva);
  monto_pago_mostrar=monto_pago.toFixed(2);
  monto_pago_mostrar="<strong>"+monto_pago_mostrar+"</strong>";
  $('#monto_pago').html(monto_pago_mostrar);
}

function senddata() {
  //Calcular los valores a guardar de cada item del inventario
  var i = 0;
  var precio_venta= 0;  cantidad= 0; descto= 0;
  var subtotal=0; subt_exento=0; subt_gravado=0;
  var StringDatos = "";
  var total_venta = $('#total_final').text();
  var fecha_movimiento = $("#fecha2").val();
  var numero_doc = $("#numero_doc2").val();
  var alias_tipodoc = $("select#select_documento option:selected").val();
  var id_cliente = $("select#select_clientes option:selected").val();
  var id_vendedor = $("#select_vendedor").val();
  var total_gravado= $('#total_gravado_sin_iva').text();
  var total_exento= $('#total_exenta').text();
  var total_iva= $('#total_iva').text();
  var total_retencion= $('#total_retencion').text();
  var total_percepcion= $('#total_percepcion').text();
  var numero_dias=$("#numero_dias").val();
  var id_tipo_pago=$('#select_tipo_pago').val();
  var items=$("#items").val();

  var total_descto= $("#totdescto").text();
  //si usa vale devolucion
  var  monto_pago= $('#monto_pago').text();
  var id_vale=$('#select_vales').val()
  var id_reserva=$('#select_reserva').val()
  var valor_vale=$('#valor_vale').html();
  var valor_reserva=$('#valor_reserva').html();

  var array_json = new Array();
  var verificar = 'noverificar';
  var verificador = [];

  $("#inventable>tbody tr").each(function(index) {
    if (index >=0) {
        var id=$(this).find("td:eq(0)").text();
        var precio_venta= $(this).find("td:eq(4)").find("#precio_venta").val();
        var cantidad=$(this).find("td:eq(5)").find("#cant").val();
        var subtotal_sindesc=$(this).find("td:eq(6)").find("#subtotal").val()
        var descto=$(this).find("td:eq(7)").find("#descto").val()
        var  subtotal_final=$(this).find("td:eq(8)").find("#subtotal_fin").val()

        if( cantidad && precio_venta){
          var obj = new Object();
          obj.id = id;
          obj.precio_venta = precio_venta;
          obj.cantidad = cantidad;
          obj.descto = descto;
          obj.subt_gravado =  subtotal_final;
          obj.subt_exento =0
          //convert object to json string
          text=JSON.stringify(obj);
          array_json.push(text);
          i = i + 1;
        }
    }
  });
  json_arr = '['+array_json+']';

  var seleccion = 0;
  var seleccion_doc = 0;
  var sel_tipo_doc= 0;
  var sel_vendedor=0;
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
 if( id_vendedor==-1){
    msg = 'Falta digitar  Vendedor!';
    sel_vendedor=0;
 }
 else{
   sel_vendedor=1;
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
  dataString+= '&items='+items;
  dataString+= '&total_descuento='+total_descto;
  dataString+= '&monto_pago='+monto_pago;
  dataString+= '&id_vale='+id_vale;
  dataString+= '&valor_vale='+valor_vale;
  dataString+= '&id_reserva='+id_reserva;
  dataString+= '&valor_reserva='+valor_reserva;
  dataString+= '&id_vendedor='+id_vendedor;
  if (seleccion == 1 && seleccion_doc == 1  && sel_tipo_doc == 1 && sel_vendedor== 1) {
    $.ajax({
      type: 'POST',
      url: urlprocess,
      data: dataString,
      dataType: 'json',
      success: function(datax) {
        process = datax.process;
        factura = datax.factura;
        id_garantia=datax.id_garantia;
        numero_doc=datax.numero_doc;
        //display_notify(datax.typeinfo, datax.msg);

      activa_modal(factura,numero_doc,id_garantia);
      $("#inventable").find("tr:gt(0)").remove();
      $('#total_dinero').html("<strong>0</strong>");
      $('#totaltexto').html("<strong>Son:</strong>");
      // $("#inventable").find("tr:gt(0)").remove();
      }
    });

  }
  else {
    var typeinfo = 'Warning';
    display_notify(typeinfo, msg);
    dataString+= '&items='+items;
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
function activa_modal(numfact,numdoc,id_garantia){
	urlprocess=$('#urlprocess').val();
	$('#viewModal').modal({backdrop: 'static',keyboard: false});
  //  $(".modal-body #fact_num").text(numfact)
  $(".modal-body #id_factura").val(numfact);
  $('#tipo_pago_tarjeta').hide();
  $('#tipo_pago_efectivo').hide();
  $('#garantia_cte').hide();
	var totalfinal=parseFloat($('#totalfactura').val());
  var tipo_impresion = $("select#select_documento option:selected").val();
	//var tipo_impresion=$('#tipo_impresion option:selected').val();
  var tipo_pago=$('select#select_tipo_pago  option:selected').text();
  var tipopago = tipo_pago.split("|");
  var alias_tipopago =tipopago[1];
  var  monto_pago= $('#monto_pago').text();
  //si tiene garantia
  $("#id_garantia").val(id_garantia);
  if  (id_garantia !=-1){
    $('#garantia_cte').show();
  }

  if  (alias_tipopago =="CON"){
    $('#tipo_pago_tarjeta').hide();
    $('#tipo_pago_efectivo').show();
  }
  if  (alias_tipopago =="TAR"){
    $('#tipo_pago_efectivo').hide();
    $('#tipo_pago_tarjeta').show();
  }

	if (tipo_impresion=="TIK"){
		$('#fact_cf').hide();
	}
	else{
		$('#fact_cf').show();
    var numero_doc2 = $("#numero_doc2").val(); //es el del form
    $(".modal-body #num_doc_fact").val(numero_doc2); //del modal
	}
	if (tipo_impresion=="CCF"){
		$('#ccf').show();
		var id_cliente = $("select#select_clientes option:selected").val();
		var dataString = 'process=mostrar_datos_cliente' + '&id_client=' + id_cliente;
		$.ajax({
			type: 'POST',
			url: urlprocess,
			data: dataString,
			dataType: 'json',
			success: function(data) {
				nit = data.nit;
				registro = data.registro;
				nombreape= data.nombreape;
				$('#nit').val(nit);
				$('#nrc').val(registro);
				$('#nombreape').val(nombreape);
			}
		});

	}
	else{
		$('#ccf').hide();
	}
	var facturado= totalfinal.toFixed(2);
  $(".modal-body #facturado").val(totalfinal);
  $(".modal-body #a_pagar").val(totalfinal);
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
  var tipo_impresion = $("select#select_documento option:selected").val();
  var id_factura=$(".modal-body #id_factura").val();
  var tipo_pago=$('select#select_tipo_pago  option:selected').text();
  var tipopago = tipo_pago.split("|");
  var id_garantia=$(".modal-body #id_garantia").val();
  var nombre_garantia=$(".modal-body #nombre_garantia").val();
  var alias_tipopago =tipopago[1];
	if (tipo_impresion=="TIK"){
		  var num_doc_fact = '';
			numero_factura_consumidor='';
	}
	else{
		var numero_factura_consumidor = $(".modal-body #num_doc_fact").val();
		var num_doc_fact = $(".modal-body #num_doc_fact").val();
	}
	var dataString = 'process=' + print + '&numero_doc=' + numero_doc + '&tipo_impresion=' + tipo_impresion
    dataString+=  '&num_doc_fact=' + id_factura+'&numero_factura_consumidor='+numero_factura_consumidor + '&total=' + total;

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
    var efectivo_fin =0;
    var cambio_fin =0;
if (alias_tipopago =="CON"){
   efectivo_fin = parseFloat($('#efectivo').val());
  cambio_fin = parseFloat($('#cambio').val());
}
  dataString+=  '&efectivo_fin='+efectivo_fin+'&cambio_fin=' +  cambio_fin;
  dataString+=  '&id_garantia='+id_garantia+'&nombre_garantia=' +nombre_garantia;
	if (tipo_impresion=="CCF"){
		nit=$('.modal-body #nit').val();
		nrc=$('.modal-body #nrc').val();
		nombreape=$('.modal-body #nombreape').val();
    if(nit =="" || nrc == "" || nombreape=="")
    {
      pass = false;
    }
		dataString +='&nit=' + nit+ '&nrc=' + nrc+'&nombreape=' + nombreape;
 	}

  //si hay vale ver que tipo y mostrarlo enticket
  var monto_vale="";
  var id_vale=$('#select_vales').val()
  var id_reserva=$('#select_reserva').val()
  if(id_vale!=""){
    var valor_vale= $('#valor_vale').text();
    valor_vale=valor_vale.toFixed(2);
    var monto_vale="APLICAR VALE  DEVOLUCION  #"+ id_vale + "  POR $ "+ valor_vale;
  }
  if(id_reserva!=""){
    var valor_vale= $('#valor_reserva').text();
      valor_vale=valor_vale.toFixed(2);
    var monto_vale="APLICAR VALE  RESERVA  #"+ id_reserva + "  POR $ "+ valor_vale;
  }
  if(pass && pass2){
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

      var a_pagar=  $(".modal-body #a_pagar").val();
      //estas opciones son para generar recibo o factura en  printer local y validar si es win o linux
      if (tipo_impresion == 'TIK') {
      				if (sist_ope == 'win') {
                $.post("http://"+dir_print+"printposwin1.php", {
      						datosventa: datos.facturar,
      						efectivo: efectivo_fin,
      						cambio: cambio_fin,
      						shared_printer_pos:shared_printer_pos,
                  headers:headers,
                  footers:footers,
                  a_pagar:a_pagar,
                  monto_vale: monto_vale,
                })
              } else {
                $.post("http://"+dir_print+"printpos1.php", {
                  datosventa: datos.facturar,
                  efectivo: efectivo_fin,
                  cambio: cambio_fin,
                  headers:headers,
                  footers:footers,
                  a_pagar:a_pagar,
                   monto_vale: monto_vale,
                });
              }
      }
      if (tipo_impresion == 'COF') {
        if (sist_ope == 'win') {
          $.post("http://"+dir_print+"printfactwin1.php", {
						datosventa: datos.facturar,
						efectivo: efectivo_fin,
						cambio: cambio_fin,
						shared_printer_win:shared_printer_win
          })
        } else {
          $.post("http://"+dir_print+"printfact1.php", {
            datosventa: datos.facturar,
            efectivo: efectivo_fin,
            cambio: cambio_fin
          }
        );
        }
      }

			if (tipo_impresion == 'CCF') {
        if (sist_ope == 'win') {
          $.post("http://"+dir_print+"printcfwin1.php", {
						datosventa: datos.facturar,
						efectivo: efectivo_fin,
						cambio: cambio_fin,
						shared_printer_win:shared_printer_win
          })
        } else {
          $.post("http://"+dir_print+"printcf1.php", {
            datosventa: datos.facturar,
            efectivo: efectivo_fin,
            cambio: cambio_fin
          }, function(data, status) {
          });
        }
      }
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

function cargar_vale(numero){
  var dataString = 'process=buscar_vale'+'&numero='+numero;
  $.ajax({
    type: "POST",
    url: "ventas.php",
    data: dataString,
    dataType: 'json',
    success: function(datax)
    {
      if(datax.typeinfo == "Success")
      {
        if(datax.cobrado == "No")
        {
          swal({
              title: "Vale de descuento por $"+datax.valor,
              text: "Desea aplicar este vale?",
              type: "success",
              showCancelButton: true,
              confirmButtonColor: "#69F0AE",
              confirmButtonText: "Aplicar",
              cancelButtonText: "Cancelar",
              closeOnConfirm: true,
              closeOnCancel: true },
              function(isConfirm)
              {
                if (isConfirm)
                {
                  $('#tipo_vale').html("<strong>"+datax.concepto+"</strong>");
                  $('#valor_vale').html(datax.valor);
                  totales();
                  $("#select_vales").val(datax.id_vale);
                  $("#ver_vales").val("");
                }
                else
                {
                    $("#ver_vales").val("");
                }
              });
        }
        else
        {
           display_notify("Warning", "Este vale ya fue cobrado");
           $("#ver_vales").val("");
        }
      }
      else
      {
        display_notify(datax.typeinfo, datax.msg);
        $("#ver_vales").val("");
      }
    }
  });
}
function cargar_reserva(numero)
{
  var dataString = 'process=buscar_reserva'+'&numero='+numero;
  $.ajax({
    type: "POST",
    url: "ventas.php",
    data: dataString,
    dataType: 'json',
    success: function(datax)
    {
      if(datax.typeinfo == "Success")
      {
        if(datax.cobrado == "No")
        {
          swal({
              title: "Reserva de producto por $"+datax.valor,
              text: "Desea aplicar esta reserva?",
              type: "success",
              showCancelButton: true,
              confirmButtonColor: "#69F0AE",
              confirmButtonText: "Aplicar",
              cancelButtonText: "Cancelar",
              closeOnConfirm: true,
              closeOnCancel: true },
              function(isConfirm)
              {
                if (isConfirm)
                {
                  var cantidades = datax.cantidades.split(",");
                  var ids = datax.ids.split(",");
                  for(var l=0; l<ids.length; l++)
                  {
                    var id_producto = ids[l];
                    var cantidad = cantidades[l];
                    addProductList(id_producto, cantidad,0);
                  }
                  $('#tipo_reserva').html("<strong>"+datax.concepto+"</strong>");
                  $('#valor_reserva').html(datax.valor);
                  totales();
                  $("#select_reserva").val(datax.id_vale);
                  $("#ver_reserva").val("");
                }
                else
                {
                    $("#ver_reserva").val("");
                }

              });
        }
        else
        {
           display_notify("Warning", "Esta reservacion ya fue cobrado");
           $("#ver_reserva").val("");
        }
      }
      else
      {
        display_notify(datax.typeinfo, datax.msg);
        $("#ver_reserva").val("");
      }
    }
  });
}
$(document).on("keyup", "#efectivo", function() {
  total_efectivo();
});
function total_efectivo() {
  var efectivo = parseFloat($('#efectivo').val());
  var totalfinal=parseFloat($('#totalfactura').val());
  var facturado = totalfinal.toFixed(2);
//  $('#facturado').val(facturado);
  var mensaje="";
  var monto_pago=parseFloat($('#a_pagar').val());
  if (isNaN(parseFloat(efectivo))) {
    efectivo = 0;
  }
  if ( monto_pago>0.0 && efectivo>=monto_pago){
    var cambio = efectivo - monto_pago;
  }
  else {
      var cambio = 0.0;
      mensaje="<h3 class='text-danger'>" + "Falta dinero !!!" + "</h3>"
  }
  var cambio = round(cambio, 2);
  var cambio_mostrar = cambio.toFixed(2);
  $('#cambio').val(cambio_mostrar);
  $('#mensajes').html(mensaje);

}
