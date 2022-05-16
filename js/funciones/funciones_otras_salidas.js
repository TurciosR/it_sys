$(document).ready(function() {

$('#formulario').validate({
	    rules: {
                    descripcion: {
                    required: true,
                     },
                    precio1: {
                    required: true,
                    number: true,
                     },
                 },
        submitHandler: function (form) {
            senddata();
        }
    });

//select2 select autocomplete

$('#categoria').select2();
$('#categoria').select2();
$('#tipo_entrada').select2();


$('#buscador').hide();
$("#producto_buscar").typeahead({
            source: function(query, process) {
            //var textVal=$("#producto_buscar").val();
                $.ajax({
                    url: 'facturacion_autocomplete.php',
                    type: 'POST',
                    data: 'query=' + query ,
                    dataType: 'JSON',
                    async: true,
                    success: function(data) {
                        process(data);

                    }
                });
            },
              updater: function(selection){
					var prod0=selection;
						 var prod= prod0.split("|");
						 var id_prod = prod[0];
						 var descrip = prod[1];
						  var marca = prod[2];
						// alert(id_prod);
						 agregar_producto_lista(id_prod, descrip,marca);
				}
        });

});
$(function (){
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

// Evento para seleccionar una opcion y mostrar datos en un div
$(document).on("change","#tipo_entrada", function (){
	$( ".datepick2" ).datepicker();
	$('#id_proveedor2').select2();
	$('#id_proveedor3').select2();
	$('#id_sucursal3').select2();
	var id=$("select#tipo_entrada option:selected").val();

	if(id!='0')
	{
		$('#caja_x').show();
		$('#caja_y').show();
		$('#caja_k').show();
	}
    else
    {
		$('#caja_x').hide();
		$('#caja_y').hide();
		$('#caja_k').hide();
	}

});

var valor = "";
  $("#barcode").bind('paste', function(e) {
    var pasteData = e.originalEvent.clipboardData.getData('text')
    valor = $(this).val();
    if (pasteData.length >= 2) {
      searchBarcode(pasteData);
    }
  })
  //evento al keyup para buscar si el barcode es de longitud mayor igual a 1 caracteres
  $('#barcode').on('keyup', function(event) {
    if (event.which && this.value.length >= 2 && event.which === 13) {
      valor = $(this).val();
      $('#barcode').val(valor)
      searchBarcode($(this).val());
      $('#barcode').val("");
      $('#barcode').focus();
    }
  });


$(document).on('click', '#loadtable tbody tr', function(event) {
    var id_prod = $(this).find('td:eq(0)').find('h5').html();
  console.log(id_prod);
    searchBarcode(id_prod);

  });

  //evento para buscar por el barcode
  function searchBarcode(barcode) {
    addProductList(barcode, 1)
    //totalfact();
  }
function addProductList(id_prod, cantidad) {
  id_prod = $.trim(id_prod);
  cantidad = parseInt(cantidad)
  var id_prev = "";
  var id_new = id_prod;
  var id_previo = new Array();
  var filas = 0;
  $("#inventable tr").each(function(index) {
    if (index > 0) {
      var campo0, campo1, campo2, campo5;

      $(this).children("td").each(function(index2) {
        var isVisible = false;
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
    url: "otras_salidas.php",
    data: dataString,
    dataType: 'json',
    success: function(data) {
      //  var cp = data.costo_prom;
      var color = data.color;
      var talla = data.talla;
      var exento = data.exento;
      var descrip = data.descrip;
      var ultcosto = data.ultcosto;
      var precio1 = data.precio1;
      var existencias= data.existencias;
      var estilo= data.estilo;
      //  var pv_base = data.pv_base;
      if(existencias==null)
      {
        existencias=0;
      }

      add = "";
      if(cantidad>existencias)
      {
        cantidad=0;
      }

      var tr_add = '<tr class="">';
      tr_add += "<td class='text-success '><input type='hidden'  id='exento' name='exento' value='" + exento + "'><input type='hidden' id='id_p' value='"+id_prod+"'>" + id_prod + "</td>";
      tr_add += '<td  class="text-success ">' + descrip +'</td>';
      tr_add += '<td  class="text-success ">' + estilo + '</td>';
      tr_add += '<td  class="text-success ">' + color + '</td>';
      tr_add += '<td  class="text-success ">' + talla + '</td>';
      tr_add += "<td><div class='col-xs-2'><input type='text'  class='form-control'  id='precio_venta' name='precio_venta' style='width:80px;' value='"+precio1+"' readOnly></div></td>";
      tr_add += "<td><input type='hidden'  class='form-control exisx'  id='exisx' name='exisx' value='"+existencias+"'>"+existencias+"</td>";
      tr_add += "<td class='text-success '><div class='col-xs-4'><input type='text'  class='form-control decimal cantidades' id='cant' name='cant'  value='1' style='width:60px;'></div></td>";
      tr_add += '<td class="Delete"><input id="delprod" type="button" class="btn btn-danger fa"  value="&#xf1f8;"></td>';
      tr_add += '</tr>';
      //agregar columna a la tabla de facturacion
      var existe = false;
      var posicion_fila = 0;
      /*$.each(id_previo, function(i, id_prod_ant) {
        if (id_prod == id_prod_ant) {
          existe = true;
          posicion_fila = i;
          //$("#cant").numeric();
        }
      });
      if (existe == false) {

        if(existencias==0)
        {
          display_notify("Error", "El producto ingresado no tiene existencias");
        }
        else{
          
          totalfact();
          $(".decimal").numeric({
            negative: false
          });
          $(".dis").prop('readOnly', 'true')
        }


      }*/
      var exis = false;
      $("#inventable>tbody tr").each(function ()
      {
        var id_pp = $(this).find("#id_p").val();
        if(id_pp == id_prod)
        {
          exis = true;
          var can = parseFloat($(this).find("#cant").val());
          var n_can = can + 1;
          $(this).find("#cant").val(n_can);
        }
        
      });
      if(exis)
      {
        
      }
      else
      {
        $("#inventable tr:first-child").after(tr_add);   
      }
      $("#cant").numeric(
      {
        decimal: false, 
        negative: false
      });
      /*
      if (existe == true) {
        $(".decimal").numeric();
        posicion_fila = posicion_fila + 2;
        setRowCant(posicion_fila);
        totalfact();
      }*/
    }
  });
  //  totalfact();
}

$(document).on("keyup", "#cant", function()
{
  var val = parseFloat($(this).val());
  var valor = parseFloat($(this).parents("tr").find("#exisx").val());
  console.log(valor);
  if(val > valor)
  {
    $(this).val(valor);
  }

})
function setRowCant(rowId) {
  var cantidad_anterior = $('#inventable tr:nth-child(' + rowId + ')').find('td:eq(5)').find("#cant").val();
  var existencias = $('#inventable tr:nth-child(' + rowId + ')').find('td:eq(6)').find("#existencias").val();
  var cantidad_nueva = parseFloat(cantidad_anterior)+1;
  $('#inventable tr:nth-child(' + rowId + ')').find('td:eq(5)').find("#cant").val(cantidad_nueva);
  totalfact();
}
// Agregar productos a la lista del inventario
/*function agregar_producto_lista(id_prod,descrip,costo){
	var id_prev="";
	//var costo_prom=0;
	var dataString = 'process=consultar_stock' + '&id_producto=' + id_prod;
	$.ajax({
		type : "POST",
		url : "otras_salidas.php",
		data : dataString,
		dataType : 'json',
		success : function(data) {
			//var costo_prom = JSON.parse(data.costo_prom);
			
			var existencias= data.existencias;
			var mensaje = data.mss;
			var estilo = data.estilo;
			var talla = data.talla;
			var letra = data.letra;
			var color = data.color;
			var numera = data.numera;

			 // alert(cp);
			
			var tr_add="";
			tr_add += '<tr>';
			tr_add += '<td><input type="hidden" id="id_p" value="'+id_prod+'">'+id_prod+'</td>';
			tr_add += '<td>'+descrip+'</td>';
			tr_add += '<td>'+estilo+'</td>';
			tr_add += '<td>'+color+'</td>';
			tr_add += '<td>'+talla+'</td>';
			tr_add += '<td>'+numera+'</td>';
			tr_add += '<td>'+letra+'</td>';

			tr_add += '<td>'+existencias+'</td>';
			//tr_add += '<td>'+cp+'</td>';
			//tr_add +="<td><div class='col-xs-2'><input type='text'  class='form-control' id='precio_compra' name='precio_compra' value='"+pre_unit+"' style='width:80px;'></div></td>";
			//tr_add +="<td><div class='col-xs-2'><input type='text'  class='form-control'  id='precio_venta' name='precio_venta' style='width:80px;'></div></td>";
			tr_add +="<td><input type='text'  class='form-control' id='cant' name='cant' value='1'></td>";
			tr_add += "<td class='Delete'><a href='#'><i class='fa fa-times-circle'></i> Borrar</a></td>";
			tr_add += '</tr>';

			if(existencias>0)
			{
				var exis = false;
				$("#inventable>tbody tr").each(function ()
				{
					var id_pp = $(this).find("#id_p").val();
					if(id_pp == id_prod)
					{
						exis = true;
						var can = parseFloat($(this).find("#cant").val());
						var n_can = can + 1;
						$(this).find("#cant").val(n_can);
					}
					
				});
				if(exis)
				{
					
				}
				else
				{
					$("#inventable").append(tr_add);			
				}
			}
			else
			{
				display_notify('Error', mensaje);
			}

			}
		});
	totales();
}*/
//Evento que se activa al perder el foco en precio de venta y cantidad:
$(document).on("blur","#cant, #precio_compra",function(){
  totales();
})

$(document).on("blur","#inventable",function(){
//$('#precio_compra').blur(function() {
  totales();
})
$(document).on("keyup","#cant, #precio_compra",function(){
  totales();
})

// Evento que selecciona la fila y la elimina de la tabla
$(document).on("click",".Delete",function(){
	var parent = $(this).parents().get(0);
	$(parent).remove();
	totales();
});
//Calcular Totales del grid
function totales(){
	var subtotal=0; total=0; totalcantidad=0;  cantidad=0;
	var total_dinero=0; total_cantidad=0;  precio_compra=0; precio_venta=0;
 $("#inventable>tbody tr").each(function (index) {
		 if (index>=0){
           var campo0,campo1, campo2, campo3, campo4, campo5;
            $(this).children("td").each(function (index2) {
                switch (index2){
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
                        break;
                    case 3:
						 campo3= $(this).text();
                        break;

                     case 4:
							 campo4= $(this).find("#precio_compra").val();

						if (isNaN(campo4)==false){
							precio_compra=parseFloat(campo4);
						}
						else{
							precio_compra=0;
						}
						 break;
                    case 5:
                        campo5= $(this).find("#cant").val();
						if (isNaN(campo5)==false){
							cantidad=parseFloat(campo5);
						}
						else{
							cantidad=0;
						}
                        break;
                }


            });

            subtotal=precio_compra*cantidad;
            if (isNaN(cantidad)==true){
				cantidad=0;
			}
            totalcantidad+=cantidad;

            if (isNaN(subtotal)==true){
				subtotal=0;
			}
            total+=subtotal;

          }
        });
         if (isNaN(total)==true){
			total=0;
		}
         total_dinero=total.toFixed(2);
         total_cantidad=totalcantidad.toFixed(2);

         total_dinero=total.toFixed(2);
         total_cantidad=totalcantidad.toFixed(2);

        $('#total_dinero').html("<strong>"+total_dinero+"</strong>");
          $('#totcant').html(total_cantidad);

}

// actualize table
$(document).on("click","#submit1",function(){
	senddata();
});

function senddata(){

	//Calcular los valores a guardar de cada item del inventario
	var i=0;
	var precio_compra,precio_venta, cantidad,id_prod;
	var  StringDatos="";
	var id=$("select#tipo_entrada option:selected").val(); //get the value

	if (id=='0'){
		$('#tipo_entrada').focus();
	}

	var verificar='noverificar';
	var verificador=[];

	 $("#inventable>tbody tr").each(function (index) {
		 if (index>=0){
           var campo0,campo1, campo2, campo3;

            $(this).children("td").each(function (index2) {
                switch (index2){
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
                        break;
                    case 7:
                        campo3= $(this).find("#cant").val();
						if (isNaN(campo3)==false){
							cantidad=parseFloat(campo3);
							verificar='noverificar';
						}
						if (isNaN(parseFloat(cantidad))){
							cantidad=0;
							verificar='verificar';
						}
						break;
                }
            });
            if(campo0!=""|| campo0==undefined ){
				StringDatos+=campo0+"|"+cantidad+"#";
				verificador.push(verificar);
				i=i+1;
			}
          }
        });
       verificador.forEach(function (item, index, array) {
			if (item=='verificar'){
				verificar='verificar';
				//alert(verificar);
			}
		});
	// Captura de variables a enviar
	var fecha_movimiento="";
	var numero_doc=0;
	var id_sucursal=-1;
	var total_compras=$('#total_dinero').text();
	var concepto = $("#concepto").val();

	var fecha_movimiento=$("#fecha1").val();



	var dataString='process=insert'+'&stringdatos='+StringDatos+'&cuantos='+i+'&id='+id+'&fecha_movimiento='+fecha_movimiento+'&total_compras='+total_compras+"&concepto="+concepto; 
	//alert(dataString);
	if(concepto != "")
	{
		if (verificar=='noverificar'){
		$.ajax({
			type:'POST',
			url:'otras_salidas.php',
			data: dataString,
			dataType: 'json',
			success: function(datax){
				process=datax.process;
				//var maxid=datax.max_id;
				display_notify(datax.typeinfo,datax.msg);
				setInterval("reload1();", 1000);
			}
		});
		}
		else
		{
			var typeinfo='Warning';
			var msg='Falta rellenar algun valor de precio o cantidad!';
			display_notify(typeinfo,msg);
		}
	}
	else
	{
		display_notify("Warning", "Debe de agregar un concepto");
	}
	
}



 function reload1(){
	location.href = 'admin_movimiento.php';
}
function deleted() {
	var id_producto = $('#id_producto').val();
	var dataString = 'process=deleted' + '&id_producto=' + id_producto;
	$.ajax({
		type : "POST",
		url : "borrar_producto.php",
		data : dataString,
		dataType : 'json',
		success : function(datax) {
			display_notify(datax.typeinfo, datax.msg);
			setInterval("location.reload();", 1000);
			$('#deleteModal').hide();
		}
	});
}

$('#keywords').on('keyup', function(event) {
    var kw = $('#keywords').val();
    if (kw.length > 0 && event.which !== 8) {
      $("#loadtable").find("tr:gt(0)").remove();
      searchFilter();
    }
  });
  $('#estilo, #talla').on('keyup', function(event) {
    if (event.which !== 8) {
      $("#loadtable").find("tr:gt(0)").remove();
      searchFilter();
    }
  });
  $("#select_colores").on('change', function() {
    $("#loadtable").find("tr:gt(0)").remove();
    var color = $('#select_colores :selected').val();
    searchFilter();
  });
function searchFilter() {
  var keywords = $('#keywords').val();
  var id_color = $('#select_colores :selected').val();
  var talla = $('#talla').val();
  var estilo = $('#estilo').val();
  var barcode = $('#barcode').val();
  var limite = $('#limite').val();

  if (id_color == undefined) {
    id_color = -1;
  }
  getData(keywords, id_color, talla, estilo, barcode, limite)
}

function getData(keywords, id_color, talla, estilo, barcode, limite) {
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
      barcode: barcode,
      limite: limite
    },
    beforeSend: function() {
      $('.loading-overlay').show();
    },
    success: function(html) {
      $('#mostrardatos').html(html);
      var cuantos = $('#cuantos_reg').val();
      if (cuantos > 0) {
        $('.loading-overlay').html("<span class='text-warning'>Buscando....</span>");
        $('#reg_count').val(cuantos);
        $('.loading-overlay').fadeOut("slow");
      } else {
        $('.loading-overlay').fadeOut("slow");
        $('#reg_count').val(0);
      }
    }
  });
}
function totalfact() {
  //  var urlprocess = $('#urlprocess').val();

  var i = 0,
    total = 0;
  totalcantidad = 0;
  cantidad = 0;
  existencias=0;
  costo = 0;
  precio = 0;
  filas = 0;
  items = 0;
  totalfactura = 0;
  var td1;
  var td2;
  var td3;
  var td4;
  var td5;
  var td6;

  $("#inventable  tr").each(function(index) {
    if (index >= 0) {
      var subtotal = 0;
      $(this).children("td").each(function(index2) {
        switch (index2) {
          case 5:
            cantidad = parseFloat($(this).find("#cant").val());
            if (isNaN(cantidad)) {
              cantidad = 0;
            }
            items = 1
            break;
          case 6:
            existencias = parseFloat($(this).find("#existencias").val());
            if (isNaN(existencias)) {
              existencias = 0;
            }
            break;

        }
      });
    }
    if(cantidad>existencias)
    {
      $(this).find("#cant").val(existencias);
      cantidad=existencias;
    }
    filas += items;
    totalcantidad += cantidad;

  });
  $('#items').val(filas);
  $('#pares').val(totalcantidad);
  $('#totcant').html(totalcantidad);

  //  $('#totaltexto').load(urlprocess,{'process': 'total_texto','total':total_final_mostrar});
}