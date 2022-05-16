var urlfactura = "";
$(document).ready(function() {


		$(document).keydown(function(e){

      if(e.which == 120) { //F9 guarda factura
        e.preventDefault();
	      senddata();
			}
		});

totalFact();

  $(".decimal").numeric();


  $('#form_fact_consumidor').hide();
  $('#form_fact_ccfiscal').hide();

  //Boton de imprimir deshabilitado hasta que se guarde la factura
  $('#print1').prop('disabled', true);
  $('#submit1').prop('disabled', false);


var valor="";
  $("#submit1").on("click", function(e) {
      e.preventDefault();
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
});

//Evento que se activa al perder el foco en precio de venta y cantidad:
$("#inventable").on('change', '#cant', function() {
  totales();
  totalFact();
});

$("#inventable").on('keyup', '#cant', function() {
  totales();
  totalFact();
});
$("#inventable").on('keydown', '#cant', function() {
  totales();
  totalFact();
});
$(document).on("focusout","#cant",function(){
  totales();
})

$(document).on("blur","#inventable",function(){
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
//Calcular Totales del grid
function totales(){
  $("#inventable tr").each(function (index) {
    if (index>0) {
       if( $('#inventable tr').is(":visible") ){
        var cantidad =parseFloat($(this).closest("tr").find('td:eq(4)').find("#cant").val());
      var precio= parseFloat($(this).closest("tr").find('td:eq(3)').text());
      var stock=  parseFloat($(this).closest("tr").find('td:eq(2)').text());
      if (cantidad>stock){
         cantidad = parseFloat(stock);
         $(this).closest("tr").find('td:eq(4)').find("#cant").val(cantidad);
      }

     subtotal = precio*cantidad;
     if (isNaN(subtotal))
      subtotal=0
     if (subtotal>0)
       var new_subtotal='<span class="text-warning">'+subtotal.toFixed(2)+'</span>';
    // $('#inventable').find('tr#'+index).find('td:eq(6)').html(new_subtotal);
    $(this).closest("tr").find('td:eq(5)').html(new_subtotal);
    }
  }
  });
   totalFact();
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
          case 5:
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
  $('#totaltexto').load('editar_factura_dia.php?' + 'process=total_texto&total=' + total_dinero);

  console.log('total:' + total_dinero);
}


function senddata() {
  //Obtener los valores a guardar de cada item facturado
  urlfactura= $("#urlprocess").val();
  var i = 0;
  var precio_venta, precio_venta,precios_venta, cantidad, subcantidad, id_prod, id_empleado, precio_venta_desc, fecha_vencimiento;
  var elem1 = '';
  var descripcion = '';
  var StringDatos = "";
  var id_empleado = 0;
  var id_factura = $("#id_factura").val();
  var id_cliente = $("#id_cliente").val();
  var total_ventas = $('#total_dinero').text();
  var fecha_movimiento = $("#fecha").val();
  var id_prod=0;

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
          campo4= $(this).find("#cant").val();
          if (isNaN(campo4)==false){
          cantidad=parseFloat(campo4);
          }
          else{
          cantidad=0;
          }
          break;
          case 5:
           campo5 = $(this).text();
           if (isNaN(campo5) == false) {
             subtotal = parseFloat(campo5);
           } else {
             subtotal = 0;
           }
           break;
        }
      });
      if (campo0 != "" || campo0 == undefined || isNaN(campo0) == false) {
        StringDatos += campo0 +  "|"+ cant_stock + "|" + precio + "|" + cantidad+ "|"  + subtotal  + "#";
        i = i + 1;
      }
    }

  });

  var dataString = 'process=insert' + '&stringdatos=' + StringDatos + '&cuantos=' + i + '&fecha_movimiento=' + fecha_movimiento + '&id_cliente=' + id_cliente;
   dataString+= '&total_ventas=' + total_ventas + '&id_empleado=' + id_empleado+ '&id_factura=' + id_factura;
  // alert(dataString)
  $.ajax({
    type: 'POST',
    url: urlfactura,
    data: dataString,
    dataType: 'json',
    success: function(datax) {
      process = datax.process;
      factura = datax.factura;
      $('#submit1').prop('disabled', true);
      $("#inventable").find("tr:gt(0)").remove();
      display_notify(datax.typeinfo,datax.msg);
      setInterval("reload1();", 500);
    }
  });
}
function reload1() {
   location.href = "generar_facturas_dia3.php";
}
