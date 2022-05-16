var cont_canasta = 0;
$(document).ready(function() {
  $(".decimal").numeric();
  $(".numeric").numeric();
  // Clean the modal form
	$('body').on('hidden.bs.modal', '.modal', function () {
        $(this).removeData('bs.modal');
      });
			$('#viewModal').on('hidden.bs.modal', function () {
		      $(this).removeData('bs.modal');
		});

    searchFilter();

$(document).on('keyup keypress', '.decimal_positive', function()
{
  var aValue = $(this).val();
  if($.isNumeric(aValue) === false){
    $(this).val(aValue.slice(0,-1));
  }
});

 $(document).keydown(function(e){

   if(e.which == 113){ //F2 Guardar
     e.stopPropagation();
     senddata();
   }
   if(e.which == 119) {//F8 Imprimir
      e.stopPropagation();
      finalizar2();
   }
   if(e.which == 120) { //F9 Salir
   //PENDIENTE
   }
 });

$(document).on('keyup keypress', '.integer_positive', function(e)
{
  if (/\D/g.test(this.value))
  {
    // Filter non-digits from input value.
    this.value = this.value.replace(/\D/g, '');
  }
});


  $('.select2').select2({
  	placeholder: {
      id: '-1',
      text: 'Seleccione'
    },
      allowClear: true
  });

 $('#keywords, #talla, #estilo, #barcode').on('keyup', function(event) {
    searchFilter();
  });
  $("#select_colores").change(function(event){
    searchFilter();
  });
  /*
  $('#keywords, #anio').on('keyup', function() {
    searchFilter();
  });

*/

  $('.loading-overlay').hide();
//  $('#encabezado_buscador').hide();
  //$('#seleccionados').hide();


});

function searchFilter(page_num){
  page_num = page_num ? page_num : 0;
  var keywords = $('#keywords').val();
  var id_color = $('#select_colores :selected').val();
  var talla = $('#talla').val();
  var estilo = $('#estilo').val();
  var barcode = $('#barcode').val();
  //var limite = $('#limite').val();

  if(id_color==undefined){
    id_color=-1;
  }
  getData(keywords,id_color,talla,estilo,barcode,page_num)
}

function getData(keywords,id_color,talla,estilo,barcode,page_num){
  var sortBy = $('#sortBy').val();
  var records = $('#records').val();
  urlprocess = $('#urlprocess').val();
  $.ajax({
    type: 'POST',
    url: urlprocess,
    data: {
      process: 'traerdatos',
      page: page_num,
      keywords: keywords,
      id_color: id_color,
      talla: talla,
      estilo: estilo,
      barcode:barcode,
      sortBy: sortBy,
      records: records
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

  $.ajax({
    type: 'POST',
    url: urlprocess,
    data: {
      process: 'traerpaginador',
      page: page_num,
      keywords: keywords,
      id_color: id_color,
      talla: talla,
      estilo: estilo,
      barcode:barcode,
      sortBy: sortBy,
      records: records
    },
    success: function(value) {
      $('#encabezado_buscador').show();
      $('#paginador').html(value);
      //$('#seleccionados').show();
    }
  });
}
/*
function searchFilter(page_num){
  page_num = page_num ? page_num : 0;
  var keywords = $('#keywords').val();
  //var marca = $('#marca').val();
  var anio = $('#anio').val();
  //var modelo = $('#modelo').val();
  var id_marca = $('#select_marcas :selected').val();
  var id_modelo = $('#select_modelos :selected').val();
  var sortBy = $('#sortBy').val();
  var records = $('#records').val();

  if(id_marca==undefined)
    id_marca=-1;
  if(id_modelo==undefined)
    id_modelo=-1;

  $.ajax({
    type: 'POST',
    url: 'getData.php',
    data: {
      process: 'traerdatos',
      page: page_num,
      keywords: keywords,
      anio: anio,
      id_marca: id_marca,
      id_modelo: id_modelo,
      sortBy: sortBy,
      records: records
    },
    beforeSend: function() {
      $('.loading-overlay').show();
      $('#encabezado_buscador').show();
    },
    success: function(html) {
      $('#mostrardatos').html(html);
      $('.loading-overlay').fadeOut("slow");

    }
  });
  $.ajax({
    type: 'POST',
    url: 'getData.php',
    data: {
      process: 'traerpaginador',
      page: page_num,
      keywords: keywords,
      anio: anio,
      id_marca: id_marca,
      id_modelo: id_modelo,
      sortBy: sortBy,
      records: records
    },
    success: function(value) {
      $('#encabezado_buscador').show();
      $('#paginador').html(value);
    }
  });
}
*/
$("#mostrardatos").on('click', '#btnSelect', function() {
  if ($('#seleccionados').hide()) {
    $('#seleccionados').show();
  }
  var id_previo = new Array();
  var id_lote_exist = new Array();
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
  //var tr_add = '<tr>';
  var tr_add = "<tr id='"+filas+"'>";
  var col0 = $(this).closest("tr").find('td:first').text();
  var col1 = $(this).closest("tr").find('td:eq(1)').html();
  var col2 = $(this).closest("tr").find('td:eq(2)').text();
  var col3 = $(this).closest("tr").find('td:eq(3)').text();
  var col4 = $(this).closest("tr").find('td:eq(4)').text();
  var col5 = $(this).closest("tr").find('td:eq(5)').html();
  var col6 = $(this).closest("tr").find('td:eq(6)').html();
  var col11 = $(this).closest("tr").find('td:eq(1)').find("#stock").val();
  var col51 = $(this).closest("tr").find('td:eq(5)').find("#nuevo").val();
  var subt=col5;
  var cant= "<div class='col-xs-2'><input type='text' class='form-control decimal' id='cant' name='cant' value='1' style='width:60px;'></div>";
  var col7 = "<td class='Delete text-center'><a class='btn btn-danger'><i class='fa fa-times'></i></a></td>";
  tr_add += '<td>' + col0 + '</td><td>' + col1 + '</td><td>' + col2 +" - "+ col3+' ('+ col4 +')</td>';
  tr_add += '<td>' + col6 + '</td>';
  tr_add += '<td>' + col5 + '</td>';
  tr_add += '<td>' + cant + '</td>';
  tr_add += '<td>' + col5 + '</td>';
  tr_add += col7;
  tr_add += '</tr>';
  //agregar columna a la tabla de facturacion
  var existe = false;
  var posicion_fila = 0;
  $.each(id_previo, function(i, id_prod_ant) {
    if (col0 == id_prod_ant) {
      existe = true;
      posicion_fila = i;
    }
  });
  if (existe == false) {
    $("#inventable").append(tr_add);
    cont_canasta++;
    $("#cont_canasta").text(cont_canasta);
    $(".decimal").numeric();
  }
  //total de tabla de facturacion
  totalFact();
});
/*
$("#mostrardatos").on('click', '#btnVer', function() {
  //alert('pendiente....')
});
*/
$(document).on("click", ".Delete", function() {
  var parent = $(this).parents().get(0);
  $(parent).remove();
  if(cont_canasta>0)
  {
    cont_canasta--;
    $("#cont_canasta").text(cont_canasta);
    if(cont_canasta == 0)
    {
      $("#cont_canasta").text("");
    }

  }
  //no remover sino ocultar
  //$(parent).hide();
  totalFact();
});
//function to round 2 decimal places
function round(value, decimals) {
  return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}

$("#inventable").on('change',  '#cant', function(){
  totales();
  totalFact();
});

$("#inventable").on('keyup', '#cant', function() {
  totales();
  totalFact();
});
function totales(){
  $("#inventable tr").each(function (index) {
    if (index>0) {
       if( $('#inventable tr').is(":visible") ){
        var cantidad =parseFloat($(this).closest("tr").find('td:eq(5)').find("#cant").val());
      //var precio= parseFloat($('#inventable').find('tr#'+index).find('td:eq(4)').text());
      var precio= parseFloat($(this).closest("tr").find('td:eq(4)').text());
      var stock=  parseFloat($(this).closest("tr").find('td:eq(3)').text());
      if (cantidad>stock){
         cantidad = parseFloat(stock);
         //$('#inventable').find('tr#'+index).find('td:eq(5)').find("#cant").val(cantidad);
         $(this).closest("tr").find('td:eq(5)').find("#cant").val(cantidad);
      }

     subtotal = precio*cantidad;
     if (isNaN(subtotal))
      subtotal=0
     var new_subtotal='<span class="text-warning">'+subtotal.toFixed(2)+'</span>';
    // $('#inventable').find('tr#'+index).find('td:eq(6)').html(new_subtotal);
    $(this).closest("tr").find('td:eq(6)').html(new_subtotal);
    }
  }
  });
}

function totalFact(){
  var TableData = new Array();
  var i = 0;
  var total = 0;
  var StringDatos = '';
  $("#inventable>tbody  tr").each(function(index) {
  if (index >= 0) {
      var subtotal = 0;
      $(this).children("td").each(function(index2) {
        switch (index2) {
          case 6:
            var isVisible = false
            isVisible = $(this).filter(":visible").length > 0;
            if (isVisible == true) {
              subtotal = parseFloat($(this).text());

              if (isNaN(subtotal)) {
                subtotal = 0;
              }
            } else {
              subtotal = 0;
            }
            break;
        }
      });

      //  }

      total += subtotal;
    }
  });
  total = round(total, 2);
  total_dinero = total.toFixed(2);
  //total_cantidad=totalcantidad.toFixed(2);

  $('#total_dinero').html("<strong>" + total_dinero + "</strong>");
  //$('#totalfactura').val(total_dinero);
  $('#totaltexto').load('getData.php?' + 'process=total_texto&total=' + total_dinero);

  console.log('total:' + total_dinero);
}

function cambio_marcas(id_modelo){
  //select cambiar marcas
	var id = $('#select_marcas :selected').val();

$("#select_modelos").empty().trigger('change')
	$.post("getData.php", {process:'genera_select',id:id }, function(data){
					 $("#select_modelos").html(data);
	 });

}
//seleccionar modelos
function cambiar_modelo(id_modelo){
	$('#select_modelos').val('').trigger('change');
	console.log(id_modelo)
	setTimeout(function() {
		for (i = 0; i < 100; i++){text="abc";};
		//$('#select_modelos').val(null).trigger('change');

    $('#select_modelos').val(id_modelo).change();
	}, 200);
}

$(document).on("click", "#btnSend", function(event) {
//event.stopImmediatePropagation();
  event.stopPropagation();
  senddata();
});

function senddata() {
	urlfactura='getData.php';
  //Obtener los valores a guardar de cada item facturado
  var i = 0;
  var StringDatos = "";
  var id = '1';
  var id_empleado =$("select#select_empleados option:selected").val();
  var id_cliente = $("select#id_cliente option:selected").val();
  var numero_doc = $("#numero_doc").val();
  var total_ventas = $('#total_dinero').text();
  var array_json = new Array();
  var text = '';
  $("#inventable>tbody tr ").each(function(index) {
    if (index >= 0) {
      var campo0, campo1, campo2, campo3, campo4, campo5, campo6, campo7, campo8;
      $(this).children("td").each(function(index2) {
        switch (index2) {
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
            campo3 = $(this).text();
            break;
          case 4:
            campo4 = $(this).text();
          case 5:
            campo5 = $(this).find("#cant").val();
            break;
          case 6:
            campo6 = $(this).text();
        }
      });
      if (campo0 != "" || campo0 == undefined || isNaN(campo0) == false) {
        StringDatos += campo0 + "|" + campo3 + "|" + campo4 + "|" + campo5 + "|" + campo6 + "#";
        if( campo3!=0){
          var obj = new Object();
          obj.id = campo0;
          obj.descrip  =campo1;
          obj.mamoan = campo2;
          obj.stock = campo3;
          obj.precio = campo4;
          obj.cantidad = campo5;
          obj.subtotal = campo6;
          //convert object to json string
          text=JSON.stringify(obj);
          array_json.push(text);
          i = i + 1;
        }
      }
    }
  });
  json_arr = '['+array_json+']';
  console.log('jsons:'+json_arr);
  var dataString = 'process=insertar_venta';
  dataString += '&cuantos='+i+'&id_empleado='+id_empleado+'&total_ventas='+total_ventas+'&json_arr='+json_arr;
  //alert(dataString)
  $.ajax({
    type: 'POST',
    url: urlfactura,
    data: dataString,
    dataType: 'json',
    success: function(datax) {
      process = datax.process;
      var factura = datax.factura;
      //display_notify(datax.typeinfo, datax.msg);

        $("#inventable").find("tr:gt(0)").remove();
        $('#total_dinero').html("<strong>0</strong>");
        $('#totaltexto').html("<strong>Son:</strong>");

        activa_modal(factura);

    }
  });
}
$(document).on("click", "#btnEsc", function (event) {
		reload1();
});
$(document).on("click", "#btnFinFact", function (event) {
  finalizar2();
  event.stopPropagation();
});
function activa_modal(numdoc){
	$('#identificaModal').modal({backdrop: 'static',keyboard: false});
  $(".modal-body #fact_num").html("<h3 class='text-danger'>"+numdoc+"</h3>");
	//alert(numdoc)
}
function finalizar2() {
  var	urlfactura='getData.php';
	var numero_doc=$(".modal-body #fact_num").text();
  var nombre_facturar=$(".modal-body #nombre_facturar").val();
	var dataString = 'process=finalizar_fact' + '&numero_doc=' + numero_doc + '&nombre_facturar=' +nombre_facturar;
	$.ajax({
		type : "POST",
		url : urlfactura,
		data : dataString,
		dataType : 'json',
				success: function(datax){
				process=datax.process;
				factura=datax.factura;
				display_notify(datax.typeinfo,datax.msg);
			 setInterval("reload1();", 1000);
		}
	});
}

function reload1(){
 location.href = 'buscador1.php';
}
