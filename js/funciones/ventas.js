var ads = 0;
var urlprocess = "";
$(document).ready(function()
{
  var contador = 0;
  $("#inventable tr").each(function(index) {
    contador += 1;
  });
  if(contador > 0)
  {
    $(".decimal").numeric();
    $(".precios_ser").numeric();
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
    totales();
    totalfact();
  }
  urlprocess = $('#urlprocess').val();
  $('#inventable').arrowTable();
  $('#loadtable').arrowTable();
  $('#numero_doc2').prop('readonly', true);

  $('.loading-overlay').hide();
  $(".datepick2").datepicker({
    format: "dd-mm-yyyy",
  });
  $("#tipo_impresion").select2();
  $("#con_pago").select2();
  $("#barcode").focus();
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

  $("#cliente").typeahead({
    source: function (query, process) {
      $.ajax({
        type: 'POST',
        url: 'cliente_autocomplete.php',
        data: 'query=' + query,
        dataType: 'JSON',
        async: true,
        success: function (data) {
          process(data);
        }
      });
    },
    updater: function (selection) {
      var prod0 = selection;
      var prod = prod0.split("|");
      var id_prod = prod[0];
      var descrip = prod[1];
      $("#id_cliente").val(id_prod);
      $("#text_cliente").text(descrip);
      datos_clientes();
      setTimeout("totalfact();",500);
      // agregar_producto_lista(id_prod, descrip, isbarcode);
    }
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
  $("#servicio_buscar").typeahead({
    source: function(query, process) {
      $.ajax({
        url: 'autocomplete_servicio.php',
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
      var ser0 = selection;
      var ser = ser0.split("|");
      var id_ser = ser[0];
      var descrip = ser[1];
      agregar_servicio(id_ser);
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
  $("#keywords").focus();

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
});

$(document).on("click", "#f_servicio", function()
{
  $("#caja_fp").attr("hidden", false);
  $("#caja_fs").attr("hidden", true);
  $("#servicio_buscar").attr("type", "text");
  $("#producto_buscar").attr("type", "hidden");
})
$(document).on("click", "#f_producto", function()
{
  $("#caja_fp").attr("hidden", true);
  $("#caja_fs").attr("hidden", false);
  $("#servicio_buscar").attr("type", "hidden");
  $("#producto_buscar").attr("type", "text");
})
// eventos teclas de funcion
$(document).keydown(function(e){
  if(e.which == 119) { //F9 guarda factura
       senddata();
  }
  /*if(e.which == 112){ //F1 Imprimir modal
    if ($(".modal-body #facturado").val()!=""|| $(".modal-body #fact_num").html()!=""){
      imprime1();
    }
  }*/
  if(e.which == 115)
  { //F1 Imprimir modal
    location.href = "admin_factura_rangos.php";
  }
});
// fin Teclas de funcion
//evento change para select clientes
 $("#select_clientes").change(function(event){
   datos_clientes();
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
$("#btn_venta").click(function(){
  senddata();
});

});


$(document).on('change', "#con_pago", function()
{
  var valor = $(this).val();
  if(valor == 0)
  {
    $("#dias_caja").attr("hidden", true);
  }
  if(valor == 1)
  {
    $("#dias_caja").attr("hidden", false);
  }
});

$(document).on('change', ".precios", function()
{
  var valor = parseFloat($(this).val());
  var cantidad = $(this).parents("tr").find('#cant').val();

  if(cantidad != "")
  {
    var sub = valor * cantidad;
    var cantidad = $(this).parents("tr").find('#sub_to').val(sub.toFixed(2));
  }
  else
  {
    var cantidad = $(this).parents("tr").find('#sub_to').val("0");
  }
  totalfact();
});
$(document).on('keyup', ".precios_ser", function()
{
  var valor = parseFloat($(this).val());
  var cantidad = $(this).parents("tr").find('#cant').val();

  if(cantidad != "")
  {
    var sub = valor * cantidad;
    var cantidad = $(this).parents("tr").find('#sub_to').val(sub.toFixed(2));
  }
  else
  {
    var cantidad = $(this).parents("tr").find('#sub_to').val("0");
  }
  totalfact();
});
$(document).on('keyup', "#cant", function()
{
  var valor = $(this).val();
  var cantidad = parseFloat($(this).parents("tr").find('#stock').val());
  var bandera= $(this).parents("tr").find('#bandera').val();
  var precio = parseFloat($(this).parents('tr').find('.ps').val());
  if(bandera == "producto")
  {
    if(valor > cantidad)
    {
      $(this).val("1");
      $(this).parents("tr").find('#sub_to').val(precio.toFixed(2));
    }
    else
    {
      if(valor != "")
      {
        var sub = precio * valor;
        $(this).parents("tr").find('#sub_to').val(sub.toFixed(2));
      }
      else
      {
        $(this).parents("tr").find('#sub_to').val("0");
      }
    }
  }
  if(bandera == "servicio")
  {
    if(valor != "")
    {
      var sub = precio * valor;
      $(this).parents("tr").find('#sub_to').val(sub.toFixed(2));
    }
    else
    {
      $(this).parents("tr").find('#sub_to').val("0");
    }
  }
  totalfact();
});

$(document).on('change', "#tipo_impresion", function()
{
  totalfact();
});

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
  $("#inventable tr").each(function(index) {
    if (index >= 0) {
      var campo0, campo1, campo2, campo3, campo4, campo5;

      var precio_compra = $(this).parents("tr").find("#precios").val();
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
var isccf = false;
$(document).on("change", "#tipo_impresion", function()
{
  var tipo_doc = $(this).val();
  if(tipo_doc == "CCF")
  {
  $("#inventable tr").each(function()
  {
      $(this).find(".precios option").each(function()
      {
        valor = round($(this).val()/1.13, 4);
        $(this).val(valor.toFixed(4));
        $(this).text(valor.toFixed(4));
      });
      $(this).find(".precios").select2();
      $(this).find(".precios").trigger("change");

      var vs = $(this).find(".precios_ser").val();
      var vsn = round(vs/1.13, 4);
      $(this).find(".precios_ser").val(vsn.toFixed(4));
      $(this).find(".precios_ser").trigger("keyup");
  });
  isccf = true;
}
else
{
  if(isccf)
  {
    $("#inventable tr").each(function()
    {
        $(this).find(".precios option").each(function()
        {
          valor = $(this).val()*1.13;
          $(this).val(valor.toFixed(4));
          $(this).text(valor.toFixed(4));
        });
        $(this).find(".precios").select2();
        $(this).find(".precios").trigger("change");

        var vs = $(this).find(".precios_ser").val();
        var vsn = round(vs*1.13, 4);
        $(this).find(".precios_ser").val(vsn.toFixed(4));
        $(this).find(".precios_ser").trigger("keyup");
    });
    isccf = false;
  }
}
});

function totalfact() {
  //impuestos
  var iva = $('#porc_iva').val();
  var porc_retencion1 = $("#porc_retencion1").val();
  var porc_retencion10 = $("#porc_retencion10").val();
  var porc_percepcion = $("#porc_percepcion").val();

  var monto_retencion1 = $('#monto_retencion1').val();
  var monto_retencion10 = $('#monto_retencion10').val();
  var monto_percepcion = $('#monto_percepcion').val();
  var tipo_imp = $("#tipo_impresion").val();
  var retiene_cli =$("#retiene_cli").val();
  var retiene_cli1 =$("#retiene_cli1").val();
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
  var total_rs = 0;
  var StringDatos = '';
  var filas = 0;
  $("#inventable tr").each(function(index) {
    if (index >= 0) {
      var subtotal = 0;
      $(this).children("td").each(function(index2) {
        switch (index2) {
          case 2:
            cantidad = parseFloat($(this).find("#cant").val());
            if (isNaN(cantidad)) {
              cantidad = 0;
            }
            break;
          case 3:
            subt_gravado = parseFloat($(this).find("#precios").val());;
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
    var bandera = $(this).find("#bandera").val();


    if(bandera == "producto")
    {

    }
    else
    {
      if(retiene_cli == "1")
      {
      //  total_rs += ((total/(1+parseFloat(iva))) * porc_retencion1);
      }
    }
  });
  if(retiene_cli == "1" && total>= monto_retencion1)
  {
    total_rs = ((total/(1+parseFloat(iva))) * porc_retencion1);
  }
  else if(retiene_cli1 == "1" && total>= monto_retencion10) {
    total_rs = ((total/(1+parseFloat(iva))) * porc_retencion10);
    console.log(total_rs);
  }
  else
  {
    total_rs = 0;
  }
  // IMPUESTOS
  total = round(total, 2);
  total_dinero = total.toFixed(2);
  total_gravado = round(total_gravado, 4);
  var total_gravado_mostrar = total_gravado.toFixed(2);
  var total_iva = round((total_gravado * iva), 4);
  var total_iva_mostrar = total_iva.toFixed(2);
  var total_rs_mostrar = round(total_rs, 2);
  $('#total_gravado').html(total_gravado_mostrar);
  $('#total_gravado_sin_iva').html(total_gravado_mostrar);
  //console.log(total_gravado);
  //$('#total_exento').html(total_exento_mostrar);
  //$('#total_exenta').html(total_exento_mostrar);



  if(tipo_imp == "TIK")
  {
    total_gravado_iva = total_gravado;
    total_gravado_iva_mostrar = total_gravado_iva.toFixed(2);
    $('#total_iva').html("0.00");
    $('#total_gravado_iva').html(total_gravado_iva_mostrar);
    $("#monto_rs").html(total_rs_mostrar.toFixed(2));
  }
  if(tipo_imp == "COF")
  {
    total_gravado_iva = total_gravado;
    total_gravado_iva_mostrar = total_gravado_iva.toFixed(2);
    $('#total_iva').html("0.00");
    $('#total_gravado_iva').html(total_gravado_iva_mostrar);
    $("#monto_rs").html(total_rs_mostrar.toFixed(2));
  }
  if(tipo_imp == "CCF")
  {
    total_gravado_iva = total_gravado + total_iva;
    total_gravado_iva_mostrar = total_gravado_iva.toFixed(2);
    $('#total_iva').html(total_iva_mostrar);
    $('#total_gravado_iva').html(total_gravado_iva_mostrar);
    $("#monto_rs").html(total_rs_mostrar.toFixed(2));
  }

   //total gravado con iva
  var total_percepcion = 0
  var total_retencion1 = 0
  var total_retencion10 = 0
  /*if (total_gravado >= monto_percepcion)
    total_percepcion = total_gravado * porc_percepcion;
  if (total_gravado >= monto_retencion1)
    total_retencion1 = total_gravado * porc_retencion1;
  if (total_gravado >= monto_retencion10)
    total_retencion10 = total_gravado * porc_retencion10;
    */
   total_retencion1 = total_rs_mostrar;
   total_retencion10 = total_rs_mostrar;

  total_percepcion_mostrar = total_percepcion.toFixed(2);

  var total_final = 0;
  if(tipo_imp == "TIK")
  {
    total_final = total_gravado - total_rs;
  }
  if(tipo_imp == "COF")
  {
    total_final = total_gravado - total_rs;
  }
  if(tipo_imp == "CCF")
  {
    total_final = (total_iva + total_gravado - total_rs) + (total_percepcion);
  }
  total_final_mostrar = total_final.toFixed(2);
  $('#total_percepcion').html(total_percepcion_mostrar);
  total_retencion1_mostrar = total_retencion1.toFixed(2);
  total_retencion10_mostrar = total_retencion10.toFixed(2);
    $('#total_retencion').html(total_retencion1_mostrar);
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



function agregar_producto(id_prod) {
  var tipo_doc = $("#tipo_impresion").val();
  id_prod = $.trim(id_prod);
  //cantidad = parseInt(cantidad)
  var id_prev = "";
  var id_new = id_prod;
  var id_previo = new Array();
  var filas = 0;
  var id_previo = new Array();
  $("#inventable tr").each(function (index) {
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
  var dataString = 'process=consultar_stock&id_producto=' + id_prod + "&tipo_doc=" + tipo_doc;

    $.ajax({
      type: "POST",
      url: urlprocess,
      data: dataString,
      dataType: 'json',
      success: function(data) {
        var descripcion = data.descripcion;
        var stock = data.stock;
        var precios = data.select;
        var precio_inicial = data.precio;
        var tr_add = '<tr class="row100 head" id=' + filas + '>';
        tr_add += "<td  class='cell100 column50 text-success'><input type='hidden'  id='bandera' name='bandera' value='producto'><input type='hidden'  class='form-control descripcion_ser' id='descripcion_ser' name='descripcion_ser' value='''><input type='hidden'  id='id_producto' name='id_producto' value='" + id_prod + "'>" + descripcion + "</td>";
        tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control stock' id='stock' name='stock' value='" + stock + "' style='width:80px;' readOnly></div></td>";
        //tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'>"+precios+"</div></td>";
        tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value='1' style='width:60px;'></div></td>";
        tr_add += "<td class='cell100 column10 text-success'>"+precios+"</td>";
        tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control sub_to' id='sub_to' name='sub_to' value='"+precio_inicial+"' style='width:80px;' readOnly></div></td>";
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
        if (existe == false) {
          if(stock != 0)
          {
            if(stock != "")
            {
              if(stock != "null")
              {
                $("#inventable").prepend(tr_add);
                $("#inventable #"+filas).find(".precio_compra").focus();
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
              }
              else
              {
                display_notify("Error", "EL producto no tiene registro de stock");
              }
            }
            else
            {
              display_notify("Error", "EL producto no tiene registro de stock");
            }
          }
          else
          {
            display_notify("Error", "Este prducto no tiene existencias");
          }

          //$('.btn-danger').prop('disabled', 'true')
          //$('.decimal').prop('readOnly', 'true')
        }
        totales();
        totalfact();
      }
    });
}

function agregar_servicio(id_ser) {
  var tipo_doc = $("#tipo_impresion").val();
  id_ser = $.trim(id_ser);
  //cantidad = parseInt(cantidad)
  var id_prev = "";
  var id_new = id_ser;
  var id_previo = new Array();
  var filas = 0;
  var id_previo = new Array();
  $("#inventable tr").each(function (index) {
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
  var dataString = 'process=consultar_servicio&id_servicio=' + id_ser + "&tipo_doc=" + tipo_doc;

    $.ajax({
      type: "POST",
      url: urlprocess,
      data: dataString,
      dataType: 'json',
      success: function(data) {
        var descripcion = data.descripcion;
        var stock = data.stock;
        var precios = data.precio;
        var tr_add = '<tr class="row100 head" id=' + filas + '>';
        tr_add += "<td  class='cell100 column50 text-success'><input type='hidden'  id='bandera' name='bandera' value='servicio'><input type='hidden'  class='form-control descrip_ser' id='descripcion_ser' name='descripcion_ser' value='''><input type='hidden'  id='id_producto' name='id_producto' value='" + id_ser + "'>" + descripcion + "</td>";
        tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control stock' id='stock' name='stock' value='" + stock + "' style='width:70px;' readOnly></div></td>";
        //tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'>"+precios+"</div></td>";
        tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value='1' style='width:60px;'></div></td>";
        tr_add += "<td class='cell100 column10 text-success'><input type='text'  class='form-control ps precios_ser' id='precios' name='precios' value='"+precios+"' style='width:90px;'></td>";
        tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control sub_to' id='sub_to' name='sub_to' value='"+precios+"' style='width:80px;' readOnly></div></td>";
        tr_add += '<td class="Delete cell100 column5 text-success"><input  id="delprod" type="button" class="btn btn-danger fa pull-right"  value="&#xf1f8;"></td>';
        tr_add += '</tr>';
        //agregar columna a la tabla de facturacion
        var existe = false;
        var posicion_fila = 0;
        var posicion_fila2 = 0;
        $.each(id_previo, function(i, id_prod_ant) {
          if (id_ser == id_prod_ant) {
            existe = true;
            posicion_fila = i;
            posicion_fila2 = posicion_fila + 1
            setRowCant(posicion_fila2);
          }
        });
        if (existe == false) {
          $("#inventable").prepend(tr_add);
          $("#inventable #"+filas).find(".precio_compra").focus();
          $(".decimal").numeric();
          $(".precios_ser").numeric();
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

//datos de clientes
function datos_clientes(){
  var id_cliente = $("#id_cliente").val();
  var urlprocess=$('#urlprocess').val();
  dataString={process:"mostrar_datos_cliente", id_cliente:id_cliente} ;
   $.ajax({
     type:'POST',
     url:urlprocess,
     data: dataString,
     dataType: 'json',
     success: function(datax){
       porc_percepcion=datax.percepcion;
       porc_retencion1=datax.retencion;
       porc_retencion10=datax.retencion10;
      $("#retiene_cli1").val(porc_retencion1);
      $("#retiene_cli").val(porc_retencion10);
      $("#percibe_cli").val(porc_percepcion);
       totales();
     }
   });
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


$('#loadtable tbody ').on( 'blur', 'tr', function () {
  $(this).removeClass('hoverTable2');
})
$('#loadtable tbody ').on( 'focus', 'tr', function () {
  $(this).addClass('hoverTable2');
})
//navigate asign tabindex to tr
$('#loadtable ').on( 'click', 'tr', function ()
{
  if(ads == 0)
  {
    document.onkeydown = checkKey;
    var id_prod=$(this).closest("tr").find('td:eq(0)').text();
    addProductList(id_prod,1);
  }
  ads = 1;
});
$('#loadtable ').on( 'focus', 'tr', function () {
document.onkeydown = checkKey;
});
function checkKey(e) {
    var rowCount = $('#loadtable tr').length;
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
//obtener subtotal cantidad x precio
function subt(qty,price){
  subtotal=parseFloat(qty)*parseFloat(price);
  subtotal=round( subtotal,2);
  return subtotal;
}
$(document).on("click","#plus_servicio", function()
{
  var filas = 0;
  var id_previo = new Array();
  $("#inventable tr").each(function (index) {
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
  var tr_add = '<tr class="row100 head" id=' + filas + '>';
  tr_add += "<td  class='cell100 column50 text-success'><input type='hidden'  id='bandera' name='bandera' value='servicio'><input type='hidden'  id='id_producto' name='id_producto' value='nuevo_servicio'><input type='text'  class='form-control descripcion_ser' id='descripcion_ser' name='descripcion_ser' value='''></td>";
  tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control stock' id='stock' name='stock' value='1' style='width:80px;' readOnly></div></td>";
  //tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'>"+precios+"</div></td>";
  tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value='1' style='width:60px;'></div></td>";
  tr_add += "<td class='cell100 column10 text-success'><input type='text'  class='form-control precios precios_ser' id='precios' name='precios' value='' style='width:90px;'></td>";
  tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control sub_to' id='sub_to' name='sub_to' value='0' style='width:80px;' readOnly></div></td>";
  tr_add += '<td class="Delete cell100 column5 text-success"><input  id="delprod" type="button" class="btn btn-danger fa pull-right"  value="&#xf1f8;"></td>';
  tr_add += '</tr>';
  $("#inventable").prepend(tr_add);
  $("#inventable #"+filas).find(".descripcion_ser").focus();
  $(".decimal").numeric();
})

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
  var td1;var td2;var td3;var td4;var td5;var td6;
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

	$("#inventable>tbody  tr").each(function (index) {
		 if (index>=0){
       subt_cant=$(this).find("td:eq(5)").find("#cant").val()
       subtotal_sindesc=$(this).find("td:eq(6)").text()
       subtotal_desc=$(this).find("td:eq(7)").find("#descto").val()
       subtotal_final=$(this).find("td:eq(8)").text()

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
  //$('#pares').val(totalcantidad);
  $('#totaltexto').load(urlprocess,{'process': 'total_texto','total':total_mostrar});
  $('#totalesura').val(total_final_mostrar);
  // a pagar, por si hay uso de vale por devoluciones
  valor_vale=parseFloat($('#valor_vale').text());
  valor_reserva=parseFloat($('#valor_reserva').text());
  monto_pago=total_final-(valor_vale+valor_reserva);
  monto_pago_mostrar=monto_pago.toFixed(2);
  $('#monto_pago').html(monto_pago_mostrar);
}

function senddata() {
  //Calcular los valores a guardar de cada item del inventario
  var i = 0;
  var precio_venta= 0;  cantidad= 0; descto= 0;
  var subtotal=0; subt_exento=0; subt_gravado=0;
  var StringDatos = "";
  var total_venta = $('#monto_pago').text();
  var fecha_movimiento = $("#fecha_movimiento").val();
  var numero_doc = $("#numero_doc2").val();
  var alias_tipodoc = $("#tipo_impresion").val();
  var id_cliente = $("#id_cliente").val();
  var id_vendedor = $("#select_vendedor").val();
  var total_gravado= $('#total_gravado_sin_iva').text();
  var total_exento= $('#total_exenta').text();
  var total_iva= $('#total_iva').text();
  var total_retencion= $('#total_retencion').text();
  var total_percepcion= $('#total_percepcion').text();
  var numero_dias=$("#numero_dias").val();
  var id_tipo_pago=$('#con_pago').val();
  var items=$("#items").val();
  var dias_credito=$("#dias_credito").val();
  var coti = $("#cotizacion_fac").val();
  //var pares=$("#pares").val();
  var total_descto= $("#totdescto").text();
  //si usa vale devolucion
  var  monto_pago= $('#monto_pago').text();
  var id_vale=$('#select_vales').val()
  var id_reserva=$('#select_reserva').val()
  var valor_vale=$('#valor_vale').html();
  var valor_reserva=$('#valor_reserva').html();
  var id_apertura = $("#id_apertura").val();
  var caja = $("#caja").val();
  var tuno = $("#tuno").val();
  var id_contrato = $("#id_contrato").val();
  var id_contrato = $("#id_contrato").val();

  var array_json = new Array();
  var verificar = 'noverificar';
  var verificador = [];

  $("#inventable tr").each(function(index) {
    if (index >= 0) {
      var campo0, campo1, campo2, campo3, campo4, campo5, campo6;

        var cantidad = $(this).find("#cant").val();
        var precio = $(this).find("#precios").val();
        var id_producto = $(this).find("#id_producto").val();
        var bandera = $(this).find("#bandera").val();
        var descripcion_ser = $(this).find("#descripcion_ser").val();

        var obj = new Object();
        obj.id_producto = id_producto;
        obj.precio = precio;
        obj.cantidad = cantidad;
        obj.bandera = bandera;
        obj.descripcion_ser = descripcion_ser;
        //convert object to json string
        text=JSON.stringify(obj);
        array_json.push(text);
        i = i + 1;
      }
  });
  json_arr = '['+array_json+']';

  var seleccion = 0;
  var seleccion_doc = 1;
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
  dataString+= '&fecha_movimiento=' + fecha_movimiento + '&alias_tipodoc=' + alias_tipodoc
  dataString+= '&id_cliente=' + id_cliente + '&total_venta=' + total_venta;
  dataString+= '&total_gravado='+ total_gravado;
  dataString+= '&total_exento='+total_exento;
  dataString+= '&total_iva='+total_iva;
  dataString+='&total_retencion='+ total_retencion;
  dataString+= '&total_percepcion='+total_percepcion;
  dataString+= '&numero_dias='+numero_dias;
  dataString+= '&id_tipo_pago='+id_tipo_pago;
  //dataString+= '&pares='+pares;
  dataString+= '&items='+items;
  dataString+= '&total_descuento='+total_descto;
  dataString+= '&monto_pago='+monto_pago;
  dataString+= '&id_vale='+id_vale;
  dataString+= '&valor_vale='+valor_vale;
  dataString+= '&id_reserva='+id_reserva;
  dataString+= '&valor_reserva='+valor_reserva;
  dataString+= '&id_vendedor='+id_vendedor;
  dataString+= '&id_apertura='+id_apertura;
  dataString+= '&caja='+caja;
  dataString+= '&turno='+turno;
  dataString+= '&dias_credito='+dias_credito;
  dataString+= '&coti='+coti;
  dataString+= '&id_contrato='+id_contrato;

  if (seleccion == 1  && sel_tipo_doc == 1 && sel_vendedor== 1) {
    $.ajax({
      type: 'POST',
      url: urlprocess,
      data: dataString,
      dataType: 'json',
      success: function(datax) {
        process = datax.process;
        factura = datax.factura;
        numero_doc=datax.numero_doc;
        var num_dc = datax.num_dc;
        var nombre_c = datax.nombre_c;
        var direccion_c = datax.direccion_c;
        var nit_c = datax.nit_c;
        var nrc_c = datax.nrc_c;
        //display_notify(datax.typeinfo, datax.msg);

      //activa_modal(factura,numero_doc);

      $("#nomcli").val(nombre_c);
      $("#dircli").val(direccion_c);
      $("#nitcli").val(nit_c);
      $("#nrccli").val(nrc_c);
      $("#corr_in").val(numero_doc);
      $("#tot_fdo").val(total_venta);
      $("#id_facf").val(factura);

      if(alias_tipodoc == "TIK")
      {
        $("#numdoc").val(num_dc);
        $("#efectivov").focus();
      }
      if(alias_tipodoc == "COF")
      {
        $("#numdoc").attr("readonly", false);
        $("#nomcli").attr("readonly", false);
        $("#dircli").attr("readonly", false);
        $("#numdoc").focus();
      }
      if(alias_tipodoc == "CCF")
      {
        $("#numdoc").attr("readonly", false);
        $("#nomcli").attr("readonly", false);
        $("#dircli").attr("readonly", false);
        $("#nitcli").attr("readonly", false);
        $("#nrccli").attr("readonly", false);
        $("#numdoc").focus();
      }
      $("#inventable").html("");
      $("#btn_venta").attr("disabled", true);
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

$(document).on("keyup", "#efectivov", function(e)
{
  var valor = parseFloat($(this).val());
  var total = parseFloat($("#tot_fdo").val());
  var id_factura = $("#id_facf").val();
  var tipo_impresion = $("#tipo_impresion").val();
  var numero_doc = $("#numdoc").val();
  var nombre_cli = $("#nomcli").val();
  var direccion = $("#dircli").val();
  var nit =  $("#nitcli").val();
  var nrc = $("#nrccli").val();
  $("#tot_fdo").val();
  var tt = valor - total;
  $("#cambiov").val(tt.toFixed(2));

  if (e.which == 13)
  {
    var dataString = "process=imprimir_fact&id_factura="+id_factura+"&numero_doc="+numero_doc+"&tipo_impresion="+tipo_impresion;
    dataString += "&nombre_cliente="+nombre_cli+"&direccion="+direccion+"&nit="+nit+"&nrc="+nrc;
    $.ajax({
      type: 'POST',
      url: urlprocess,
      data: dataString,
      dataType: 'json',
      success: function(datos)
      {
        var sist_ope = datos.sist_ope;
        var dir_print=datos.dir_print;
        var shared_printer_win=datos.shared_printer_win;
  			var shared_printer_pos=datos.shared_printer_pos;
        var headers=datos.headers;
        var footers=datos.footers;

        if (tipo_impresion == 'TIK')
        {
            // if (sist_ope == 'win') {
            //   $.post("http://"+dir_print+"printposwin1.php", {
            //     datosventa: datos.facturar,
            //     efectivo: efectivo_fin,
            //     cambio: cambio_fin,
            //     shared_printer_pos:shared_printer_pos,
            //     headers:headers,
            //     footers:footers,
            //     a_pagar:a_pagar,
            //     monto_vale: monto_vale,
            //   })
            // } else {
            //   $.post("http://"+dir_print+"printpos1.php", {
            //     datosventa: datos.facturar,
            //     efectivo: efectivo_fin,
            //     cambio: cambio_fin,
            //     headers:headers,
            //     footers:footers,
            //     a_pagar:a_pagar,
            //      monto_vale: monto_vale,
            //   });
            // }
            setInterval("reload1();", 1500);
        }
        else
        {
          //console.log("ok");
          swal({
              title: "Factura N° "+numero_doc,
              text: "Descea imprimir esta factura",
              type: "success",
              showCancelButton: true,
              confirmButtonColor: "#69F0AE",
              confirmButtonText: "Confirmar",
              cancelButtonText: "Cancelar",
              closeOnConfirm: true,
              closeOnCancel: true },
              function(isConfirm)
              {
                if (isConfirm)
                {

                  if (tipo_impresion == 'COF') {
                    if (sist_ope == 'win') {
                      $.post("http://"+dir_print+"printfactwin1.php", {
            						datosventa: datos.facturar,
            						shared_printer_win:shared_printer_win
                      })
                    } else {
                      $.post("http://"+dir_print+"printfact1.php", {
                        datosventa: datos.facturar,
                      }
                    );
                    }
                  }

            			if (tipo_impresion == 'CCF') {
                    if (sist_ope == 'win') {
                      $.post("http://"+dir_print+"printcfwin1.php", {
            						datosventa: datos.facturar,
            						shared_printer_win:shared_printer_win
                      })
                    } else {
                      $.post("http://"+dir_print+"printcf1.php", {
                        datosventa: datos.facturar,
                      }, function(data, status) {
                      });
                    }
                  }

                }
                else
                {
                  reload1();
                }
                setTimeout("reload1();", 1500);
              });

        }


      }
    });
  }
});

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
	var totalfinal=parseFloat($('#totalesura').val());
  var tipo_impresion = $("select#select_documento option:selected").val();
	//var tipo_impresion=$('#tipo_impresion option:selected').val();
  var tipo_pago=$('select#select_tipo_pago  option:selected').text();
  var tipopago = tipo_pago.split("|");
  var alias_tipopago =tipopago[1];
  var  monto_pago= $('#monto_pago').text();
  //HACER para credito por si se diera el caso..... pendiente

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


		//para traer datos de cliente si existe
		//var id_client = $('#id_cliente').val();
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
  var tipo_impresion = $("select#select_documento option:selected").val();
  var id_factura=$(".modal-body #id_factura").val();
  var tipo_pago=$('select#select_tipo_pago  option:selected').text();
  var tipopago = tipo_pago.split("|");
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
  var totalfinal = parseFloat($('#totalesura').val());
  var facturado = totalfinal.toFixed(2);
  $('#facturado').val(facturado);
  var mensaje="";
  var monto_pago=parseFloat($('#a_pagar').val());
 //alert(monto_pago)
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
  //alert("cambio:"+cambio)
  var cambio_mostrar = cambio.toFixed(2);
  $('#cambio').val(cambio_mostrar);
  $('#mensajes').html(mensaje);

  /*
  if (isNaN(parseFloat(totalfinal))) {
    totalfinal = 0;
  }
  if  monto_pago>0.0){
    var cambio = efectivo - monto_pago;
  }
  else {
      var cambio = efectivo - totalfinal;
  }
  var cambio = round(cambio, 2);
  //alert("cambio:"+cambio)
  var cambio_mostrar = cambio.toFixed(2);

  //$('#cambio').val(cambio_mostrar);
  if ($('#efectivo').val() != '' && cambio >=0.0) {
    $('#cambio').val(cambio_mostrar);
    $('#mensajes').text('');
  } else {
    $('#cambio').val('0');

      $('#mensajes').html("<h5 class='text-danger'>" + "Falta dinero !!!" + "</h5>");

  }
  */
}
