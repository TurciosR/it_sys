var urlprocess = "";
$(document).ready(function() {
//$('#inventable').arrowTable();
urlprocess = $('#urlprocess').val();
$('#barcode').focus();


  //evento que captura el texto al pegar y lo envia a otro evt de busqueda de barcode
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
//evento para buscar por el barcode
function searchBarcode(barcode) {
  addProductList(barcode,1)
    //totalfact();
}

});

$("#submit1").click(function(){
  senddata();
});
$(function() {
  //binding event click for button in modal form
  $(document).on("click", "#btnDelete", function(event) {
    deleted();
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
  $("#inventable tr").each(function(index) {
    if (index > 0) {
      var campo0, campo1, campo2, campo5;

      $(this).children("td").each(function(index2) {
        var isVisible =false;
        switch (index2) {
          case 0:
            campo0 = $(this).text();
            //isVisible = $(this).filter(":visible").length > 0;
          //  if (isVisible == true) {
                if (campo0 != undefined || campo0 != '') {
                  id_previo.push(campo0);
                }
          //  }
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
    url: "pedidos.php",
    data: dataString,
    dataType: 'json',
    success: function(data) {
     //  var cp = data.costo_prom;
      var color = data.color;
      var talla = data.talla;
      var exento = data.exento;
      var descrip=data.descrip;
     //  var existencias= data.existencias;
    //  var pv_base = data.pv_base;

      add = "";

      var   tr_add = '<tr class="tr1">';
      tr_add += "<td class='col1 td1'><input type='hidden'  id='exento' name='exento' value='" + exento + "'>" + id_prod + "</td>";
      tr_add += '<td  class="col2 td1">' + descrip +' '+color+' talla:'+talla+ '</td>';
      tr_add += "<td class='col3 td1'><div class='col-xs-4'><input type='text'  class='form-control decimal cantidades' id='cant' name='cant'  value='"+cantidad+"'></div></td>";
      tr_add += '<td class="Delete col4 td1"><input id="delprod" type="button" class="btn btn-danger fa"  value="&#xf1f8;"></td>';
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
            totalfact();
      }
    }
  });
  //  totalfact();
}


//Evento que se activa al perder el foco en precio de venta y cantidad:
$(document).on("blur", "#cant", function() {
  $(this).removeClass('hoverTable2');

  totalfact();
  //$(".hoverTable").css("color", "#3F729B");
})

// reemplazar valores de celda cantidades
function setRowCant(rowId){
    var cantidad_anterior=$('#inventable tr:nth-child('+rowId+')').find('td:eq(2)').find("#cant").val();
    var cantidad_nueva= parseFloat(cantidad_anterior)+1;
    $('#inventable tr:nth-child('+rowId+')').find('td:eq(2)').find("#cant").val(cantidad_nueva);
      totalfact();
}
$(document).on("focus", "#cant, #precio_compra, #descto", function() {

  totalfact();
  $(this).addClass('hoverTable2');
  })
  $(document).on("blur", "#cant", function() {
    $(this).removeClass('hoverTable2');
  })
  /*
$(document).on("blur", "#inventable", function() {

  totalfact();
})
*/
$(document).on("keyup", "#cant", function() {
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

  totalfact();
});
//function to round 2 decimal places
function round(value, decimals) {
  return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}

//Funcion para recorrer la tabla completa y pasar los valores de los elementos a un array
function totalfact(){
//  var urlprocess = $('#urlprocess').val();

  var i=0,total=0; totalcantidad =0; cantidad =0; filas=0; items=0;
  var td1;var td2;var td3;var td4;var td5;var td6;

	$("#inventable  tr").each(function (index) {
		 if (index>=0){
           var subtotal=0;
            $(this).children("td").each(function (index2) {
                switch (index2){
                  case 2:
                     cantidad= parseFloat( $(this).find("#cant").val());
                     if (isNaN(cantidad)){
                       cantidad=0;
                     }
                     items=1
                     break;

                }
            });
          }
           filas+=items;
           totalcantidad += cantidad;

        });
    $('#totcant').html(totalcantidad);
    $('#items').val(filas);
    $('#pares').val(totalcantidad);

//  $('#totaltexto').load(urlprocess,{'process': 'total_texto','total':total_final_mostrar});
}

function senddata() {
  //Calcular los valores a guardar de cada item del inventario
  var i = 0;
  urlprocess = $('#urlprocess').val();
  var precio_compra= 0;  cantidad= 0; descto= 0;
  var subtotal=0; subt_exento=0; subt_gravado=0;
  var StringDatos = "";
  var fecha_movimiento = $("#fecha2").val();
  var  items=$('#items').val();
  var pares=$('#pares').val();
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
              campo2 = $(this).find("#cant").val();
              if (isNaN(campo2) == false) {
                cantidad = parseFloat(campo2);
              } else {
                cantidad = 0;
              }
              if(  cantidad  === null)
                   cantidad  = 0;
              break;
        }
      });
      if( cantidad ){
        var obj = new Object();
        obj.id = campo0;
        obj.cantidad = cantidad;
        //convert object to json string
        text=JSON.stringify(obj);
        array_json.push(text);
        i = i + 1;
      }
    }
  });
  json_arr = '['+array_json+']';

  var seleccion = 0;

  if (fecha_movimiento == undefined ||fecha_movimiento == '') {
    seleccion = 0;
  } else {
    seleccion = 1;
  }
  if (seleccion == 0) {
    msg = 'Falta Fecha !';
  }
  var dataString = 'process=insert' +  '&cuantos=' + i +'&json_arr='+json_arr+'&fecha_movimiento=' + fecha_movimiento;
    dataString+='&pares='+pares+'&items='+items;

  if (seleccion == 1 ) {
    $.ajax({
      type: 'POST',
      url: urlprocess,
      data: dataString,
      dataType: 'json',
      success: function(datax) {
        process = datax.process;
        display_notify(datax.typeinfo, datax.msg);
        setInterval("reload1();", 250);
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
/*
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
*/
