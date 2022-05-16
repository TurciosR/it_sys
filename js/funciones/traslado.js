$(document).ready(function() {

$(".decimal").numeric();

//select2 select autocomplete
$('#categoria').select2();
$('#id_cliente').select2();

$('#form_fact_consumidor').hide();
$('#form_fact_ccfiscal').hide();

//Boton de imprimir deshabilitado hasta que se guarde la factura
$('#print1').prop('disabled', true);
$('#submit1').prop('disabled', false);

$("#producto_buscar").typeahead({
            source: function(query, process) {
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
	$('#id_proveedor').select2();
	var id=$("select#tipo_entrada option:selected").val(); //get the value
	if(id!='0'){
		$('#buscador').show();
    }
    else
		$('#buscador').hide();
	if (id=='1')
		$('#form_fact_consumidor').show();
	else
		$('#form_fact_consumidor').hide();
    if (id=='2')
		$('#form_fact_ccfiscal').show();
	else
		$('#form_fact_ccfiscal').hide();

});
// Seleccionar el tipo de factura
$(document).on("change","#tipo_entrada", function(){
	var id=$("select#tipo_entrada option:selected").val(); //get the value
	$('#mostrar_numero_doc').load('enviar_traslado_sucursal.php?'+'process=mostrar_numfact'+'&id='+id);
});

// Agregar productos a la lista del inventario
function cargar_empleados(){
	$('#inventable>tbody>tr').find("#select_empleado").each(function(){
			$(this).load('enviar_traslado_sucursal.php?'+'process=cargar_empleados');
			totales();
		});
}

function agregar_producto_lista(id_prod,descrip,costo){
	var id_prev="";
	var tipoprod ="";
	var id_prevprod="";
	var elem = descrip.split('(');
	var descripcion= elem[0];
	var elem2 = descripcion.split(']');
	var descrip_only= elem2[1];

	tipo = "PRODUCTO";
	var id_prodtipo=id_prod+tipo;
	var dataString = 'process=consultar_stock' + '&id_producto=' + id_prod+'&tipo='+tipo;
	var filas=0;
	$.ajax({
		type : "POST",
		url : "enviar_traslado_sucursal.php",
		data : dataString,
		dataType : 'json',
		success : function(data) {
			var  pv  = data.precio_venta;
			var existencias= data.existencias;
			var costos_pu= data.costos_pu;
			var iva= data.iva;
			var unidades= data.unidades;
			var costo_prom=data.costo_prom;
			var imagen=data.imagen;
			if (imagen==''){
				var imagen_div="<img alt='image' class='img-rounded rounded-border' width='200px' height='100px' src='img/productos/no_disponible.png'>";
		    }
		    else{
				var imagen_div="<img alt='image' class='img-rounded rounded-border' width='150px' height='100px'  src='"+imagen+"'>";
			}
			var description="<p class='font-bold'>Imagen: "+descrip_only +"</p>"
			$("#imagen").html(imagen_div+description);
			$("#inventable tbody>tr").each(function (index) {

			var campo0,campo1, campo2;
            $(this).children("td").each(function (index2) {
           	  switch (index2){
                    case 0:
						campo0 = $(this).text();
						if (campo0==undefined){
							campo0='';
						}
							id_prev =campo0;
                        break;
            case 1:
						campo1 = $(this).text();
						tipoprod=campo1;
						var descripcion1= elem2[0];
						id_prevprod=id_prev;



						break;
					}
				});
				filas=filas+1;
		});

	select_precios="<select name='select_precios' id='select_precios' class='form-control'>";
	if (tipo=='SERVICIO'){
			pr1=pv;pr2=pv;pr3=pv;pr4=pv;
			pr_unit=pv;
			select_precios+="<option value="+pr_unit+">"+pr_unit+"</option>";
	}
	else{
		var cprom0=parseFloat(costo_prom/unidades);
		var pr1=parseFloat(cprom0)+parseFloat(cprom0*(costos_pu[0]/100));
		if (iva>0){
			pr1=pr1+(pr1*(parseFloat(iva)/100));
		}
		pr1=round(pr1,2);
		$.each(costos_pu, function(i,costo_unit){
			var pr_unit=parseFloat(cprom0)+parseFloat(cprom0*(costo_unit/100));
			if (iva>0){
				pr_unit=pr_unit+(pr_unit*(parseFloat(iva)/100));
			}
			pr_unit=round(pr_unit,2);
			select_precios+="<option value="+pr_unit+">"+pr_unit+"</option>";
		});
	}
	select_precios+="</select>";
	 var tr_add="";
	 var subtotal=0;
	 if (existencias==null){
		 existencias=0;
	 }

	subtotal=1*pr1;
	//tr_add += "<tr id='gris'>";
	tr_add += "<tr>";
	tr_add += '<td>'+id_prod+'</td>';
	tr_add += '<td>'+descrip+'</td>';
	tr_add += "<td id='cant_stock'>"+existencias+"</td>";
	tr_add += "<td>"+select_precios+"</td>";
	tr_add +="<td><input type='hidden'  id='precio_venta_inicial' name='precio_venta_inicial' value='"+pr1+"'><div class='col-xs-2'><input type='text'  class='form-control decimal'  id='precio_venta' name='precio_venta' value='"+pr1+"' style='width:80px;'></div></td>";
	tr_add +="<td><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value='1' style='width:60px;'></div></td>";
	tr_add += "<td id='subtot' class='text-right'>"+subtotal+"</td>";
	tr_add += "<td class='Delete'><a href='#'><i class='fa fa-times-circle'></i></a></td>";
	tr_add += '</tr>';

	totales();
	if(id_prevprod!=id_prodtipo && id_prodtipo!="" && existencias>0 ){
		if (filas<=900){
			//$("#gris").css("background-color","#d9d9d9");
			$("#inventable").append(tr_add);
			$(".decimal").numeric();
			totales();
		}
	}
	if(id_prevprod==id_prodtipo && tipo=="SERVICIO" ){
		if (filas<=900){
				//$("#gris").css("background-color","#d9d9d9");
				$("#inventable").append(tr_add);
				$(".decimal").numeric();
				//cargar_empleados();
				totales();
		}
	}
		if (filas>900){
			var typeinfo='Warning';
			var msg='Numero de Filas en Factura excede el maximo permitido !';
			display_notify(typeinfo,msg);

		}
		totales();
		}
	});
	totales();
	totales();
}
//Evento que se activa al perder el foco en precio de venta y cantidad:
$(document).on("blur","#cant, #precio_venta",function(){
  totales();
})
$(document).on("focusout","#cant, #precio_venta",function(){
  totales();
})

$(document).on("blur","#inventable",function(){
//$('#precio_venta').blur(function() {
  totales();
})
$(document).on("keyup","#cant",function(){
  totales();
})
$(document).on("keydown","#precio_venta", function(event){
  var keycode = (event.keyCode ? event.keyCode : event.which);
	if(keycode == '13'){
		totales();
	}

})

// Evento que selecciona la fila y la elimina de la tabla
$(document).on("click",".Delete",function(){
	var parent = $(this).parents().get(0);
	$(parent).remove();
	totales();
});
//function to round 2 decimal places
function round(value, decimals) {
    return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
    //round "original" to two decimals
	//var result=Math.round(original*100)/100  //returns 28.45
}
//Calcular Totales del grid
function totales(){
	var subtotal=0; total=0; totalcantidad=0;  cantidad=0;
	var total_dinero=0; total_cantidad=0;  precio_venta=0; precio_venta=0;
	var elem1 = '';
	var descripcion='';
	var tipoproducto = '';  tipoprod = '';
	$("#inventable tr").each(function (index) {
		if (index>0){
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
						if (isNaN(campo2)==false)
							cant_stock=parseFloat(campo2);
                        else
							cant_stock=0;
                        break;
                    case 3:
						campo3 = $(this).text();
						precio_lista= $(this).find("#select_precios option:selected").val();
						$(this).find("#precio_venta").val(precio_lista);
                        break;
                    case 4:
						//campo4= $(this).find("#precio_venta").val(precio_lista);
						campo4= $(this).find("#precio_venta").val();

						if (isNaN(campo4)==false){
							precio_venta=parseFloat(campo4);
						}
						else{
							precio_venta=0;
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

            if (isNaN(cantidad)==true){
				cantidad=0;
			}
			if(cantidad>=cant_stock){
				$(this).find("#cant").val(cant_stock);
				cantidad=cant_stock;
			}
			var pv_inicial= $(this).find("#precio_venta_inicial").val();
			if(precio_venta<pv_inicial){
				$(this).find("#precio_venta").val(pv_inicial);
				subtotal=pv_inicial*cantidad;
			}
			else{
				subtotal=precio_venta*cantidad;
            }

            totalcantidad=cantidad+totalcantidad;

            if (isNaN(subtotal)==true){
				subtotal=0;
			}
            subt=round(subtotal, 2);
			subtotal_fin=subt.toFixed(2);
			$(this).find("#subtot").html(subtotal_fin);
            total+=subtotal;
          }
        });
        if (isNaN(total)==true){
			total=0;
		}
        total_dinero=total.toFixed(2);
        total_cantidad=totalcantidad.toFixed(2);

        $('#total_dinero').html("<strong>"+total_dinero+"</strong>");
        $('#totcant').html(total_cantidad);
		$('#totalfactura').val(total_dinero);
		$('#totaltexto').load('enviar_traslado_sucursal.php?'+'process=total_texto&total='+total_dinero);
}

// actualize table data to server
$(document).on("click","#submit1",function(){
	senddata();
});

$(document).on("click", ".print1", function () {
	//alert("hola");
    var totalfinal=parseFloat($('#totalfactura').val());
	 var facturado= totalfinal.toFixed(2);
     $(".modal-body #facturado").val(facturado);
});
$(document).on("click", "#btnPrintFact", function (event) {
	imprime1();
});
	$(document).on("click", "#btnEsc", function (event) {
		reload1();
	});
$(document).on("click","#print2",function(){
	imprime2();
});

function activa_modal(){
	$('#viewModal').modal({backdrop: 'static',keyboard: false});
	var totalfinal=parseFloat($('#totalfactura').val());
	var numdoc=$('#numero_doc').val()
	var facturado= totalfinal.toFixed(2);
    $(".modal-body #facturado").val(facturado);
     $(".modal-body #fact_num").html("Factura No :"+numdoc);
}

function senddata(){
	//Obtener los valores a guardar de cada item facturado
	var i=0;
	var precio_venta,precio_venta, cantidad,id_prod,id_empleado;
	var elem1 = '';
	var descripcion='';
	var tipoprodserv = '';  tipoprod = '';
	var  StringDatos="";
	var id=$("select#tipo_entrada option:selected").val();
	var id_empleado=$("select#empleados option:selected").val();

	var id_cliente=$("select#id_cliente option:selected").val();
	if (id=='0'){
		$('#tipo_entrada').focus();
	}
	var numero_doc=$("#numero_doc").val();
	//var numero_doc2=$("#numero_doc2").val();
	var total_ventas=$('#total_dinero').text();
	var fecha_movimiento=$("#fecha").val();
	//var fecha_movimiento2=$("#fecha2").val();


	if (fecha_movimiento=='' || fecha_movimiento==undefined){
		var typeinfo='Warning';
		var msg='Falta  Fecha!';
		display_notify(typeinfo,msg);
	}
	var verificaempleado='noverificar';
	var verifica=[];
	 $("#inventable>tbody tr ").each(function (index) {
		 if (index>=0){
           var campo0,campo1, campo2, campo3, campo4, campo5, campo6;
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
						                    elem1 = campo1.split('(');
						                          descripcion= elem1[0];

						                                tipoprodserv ="PRODUCTO";
                        break;
                    case 2:
						campo2 = $(this).text();
                        break;
                    case 3:
						campo3 = $(this).text();
						precio_lista= $(this).find("#select_precios option:selected").val();
						$(this).find("#precio_venta").val(precio_lista);
                        break;
                     case 4:
						campo4= $(this).find("#precio_venta").val();

						if (isNaN(campo4)==false){
							precio_venta=parseFloat(campo4);
						}
						else{
							precio_venta=0;
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
            if(campo0!=""|| campo0==undefined || isNaN(campo0)==false ){
				StringDatos+=campo0+"|"+tipoprodserv+"|"+precio_venta+"|"+cantidad+"#";
				verifica.push(verificaempleado);
				i=i+1;
			}
          }

        });
	var id='1';
	var id_sucursal_destino=$("select#id_sucursal_destino option:selected").val();
	if (id=='1'){
		var dataString='process=insert'+'&stringdatos='+StringDatos+'&cuantos='+i+'&fecha_movimiento='+fecha_movimiento+'&id_cliente='+id_cliente;
			dataString+='&total_ventas='+total_ventas+'&id_sucursal_destino='+id_sucursal_destino;
	}
	if (id=='2'){
		var dataString='process=insert'+'&stringdatos='+StringDatos+'&cuantos='+i+'&id='+id+'&numero_doc='+numero_doc2+'&fecha_movimiento='+fecha_movimiento2+'&id_cliente='+id_cliente;
			dataString+='&total_ventas='+total_ventas+'&id_empleado='+id_empleado;
	}
	if (id_sucursal_destino=='' || id_sucursal_destino==undefined){
		var typeinfo='Warning';
		var msg='Falta  Sucursal Destino!';
		display_notify(typeinfo,msg);
		//$('#submit1').prop('enabled', true);
	}
	else{
		$.ajax({
			type:'POST',
			url:'enviar_traslado_sucursal.php',
			data: dataString,
			dataType: 'json',
			success: function(datax){
				process=datax.process;
				display_notify(datax.typeinfo,datax.msg);
				$('#submit1').prop('disabled', true);
				setInterval("reload1();", 3000);
			}
		});
	}
}
$(document).on("keyup","#efectivo",function(){
  total_efectivo();
});
function total_efectivo(){
	var efectivo=parseFloat($('#efectivo').val());
	var totalfinal=parseFloat($('#totalfactura').val());
	var facturado= totalfinal.toFixed(2);
	$('#facturado').val(facturado);
	//alert("Total:"+totalfinal);
	if (isNaN(parseFloat(efectivo))){
		efectivo=0;
	}
	if (isNaN(parseFloat(totalfinal))){
		totalfinal=0;
	}
	var cambio=efectivo-totalfinal;
	var cambio=round(cambio, 2);
	var	cambio_mostrar=cambio.toFixed(2);
	//alert(cambio_mostrar);
	$('#cambio').val(cambio_mostrar);

}
function imprime1(){
	//Utilizar la libreria esc pos php
	//Calcular los valores a guardar de cad item del inventario
	var i=0;
	var precio_venta,precio_venta, cantidad,id_prod,id_empleado;
	var elem1 = '';
	var descripcion='';
	var tipoprodserv = '';  tipoprod = '';
	var  StringDatos="";
	var id=$("select#tipo_entrada option:selected").val(); //get the value

	var id_cliente=$("select#id_cliente option:selected").val(); //get the value
	if (id=='0'){
		$('#tipo_entrada').focus();
	}
	var numero_doc=$("#numero_doc").val();
	var numero_doc2=$("#numero_doc2").val();
	var total_ventas=$('#total_dinero').text();
	var fecha_movimiento=$("#fecha").val();
	var fecha_movimiento2=$("#fecha2").val();

	if (fecha_movimiento=='' || fecha_movimiento==undefined){
		 //numero_doc=0;
		var typeinfo='Warning';
			var msg='Falta Fecha!';
			display_notify(typeinfo,msg);
	}
	var verificaempleado='noverificar';
	var verifica=[];
	 $("#inventable>tbody tr ").each(function (index) {
		 if (index>=0){
			//verificaempleado=false;
           var campo0,campo1, campo2, campo3, campo4, campo5, campo6;
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
						elem1 = campo1.split('(');
						descripcion= elem1[0];
						var tipoprodserv1 = elem1[1];
						var ln= tipoprodserv1.length-1;
						tipoprodserv = tipoprodserv1 .substring(0,ln);

                        break;
                    case 2:
						campo2 = $(this).text();
                        break;
                    case 3:
						 campo3= $(this).find("#precio_venta").val();
						if (isNaN(campo3)==false){
							precio_venta=parseFloat(campo3);
						}
                        break;
                    case 4:
                        campo4= $(this).find("#cant").val();
						if (isNaN(campo4)==false){
							cantidad=parseFloat(campo4);
						}
                        break;
                    case 5:
						campo5 = $(this).text();
                    case 6:
						id_empleado= $(this).find("#select_empleado option:selected").val();
						campo6=id_empleado;
						if(id_empleado=='-1' && tipoprodserv=='SERVICIO'){
							verificaempleado='verificar';
							$(this).find("#select_empleado").focus();
							//return false;
					    }
					    else {
							verificaempleado='noverificar';
						}
						 break;
                }
            });
            if(campo0!=""|| campo0==undefined || isNaN(campo0)==false ){
				//StringDatos+=campo0+"|"+tipoprodserv+"|"+precio_venta+"|"+cantidad+"|"+id_empleado+"|"+verificaempleado+"#";
				StringDatos+=campo0+"|"+descripcion+"|"+tipoprodserv+"|"+precio_venta+"|"+cantidad+"|"+id_empleado+"#";
				verifica.push(verificaempleado);
				i=i+1;
			}
          }

        });
       verifica.forEach(function (item, index, array) {
			if (item=='verificar'){
				verificaempleado='verificar';
			}
		});
	//var print='print1';
	var print='imprimir_fact';
	var id=$("select#tipo_entrada option:selected").val(); //get the value
	if (id=='1'){
		var dataString='process='+print+'&stringdatos='+StringDatos+'&cuantos='+i+'&id='+id+'&numero_doc='+numero_doc+'&fecha_movimiento='+fecha_movimiento+'&id_cliente='+id_cliente;
			dataString+='&total_ventas='+total_ventas+'&verificaempleado='+verificaempleado;
	}
	if (id=='2'){
		var dataString='process='+print+'&stringdatos='+StringDatos+'&cuantos='+i+'&id='+id+'&numero_doc='+numero_doc2+'&fecha_movimiento='+fecha_movimiento2+'&id_cliente='+id_cliente;
			dataString+='&total_ventas='+total_ventas+'&verificaempleado='+verificaempleado;
	}
	if (verificaempleado=='noverificar'){
		$.ajax({
				type:'POST',
				url:'enviar_traslado_sucursal.php',
				data: dataString,
				dataType: 'json',
				success: function(datos){
					sist_ope=datos.sist_ope;
					//esta opcion es para generar recibo en  printer local y validar si es win o linux
					if (sist_ope=='win'){
						$.post("http://localhost:8080/variedades/printpos1.php",{datosventa:datos.facturar})
					}
					else {
						$.post("http://localhost/variedades/printpos1.php",{datosventa:datos.facturar})
					}
					setInterval("reload1();", 500);
				}
			});
		}
		else{
			var typeinfo='Warning';
			var msg='Falta seleccionar Empleado que brinda algun servicio en Factura !';
			display_notify(typeinfo,msg);
		}

}

function imprime2(){
	//Utilizar la libreria esc pos php
	//Calcular los valores a guardar de cad item del inventario
	var i=0;
	var precio_venta,precio_venta, cantidad,id_prod,id_empleado;
	var elem1 = '';
	var descripcion='';
	var tipoprodserv = '';  tipoprod = '';
	var  StringDatos="";
	var id=$("select#tipo_entrada option:selected").val(); //get the value

	var id_cliente=$("select#id_cliente option:selected").val(); //get the value
	if (id=='0'){
		$('#tipo_entrada').focus();
	}
	var numero_doc=$("#numero_doc").val();
	var numero_doc2=$("#numero_doc2").val();
	var total_ventas=$('#total_dinero').text();
	var fecha_movimiento=$("#fecha").val();
	var fecha_movimiento2=$("#fecha2").val();

	if (numero_doc==undefined || numero_doc==''){
		 numero_doc=0;
	}
	var verificaempleado;
	var verifica=[];
	 $("#inventable>tbody tr ").each(function (index) {
		 if (index>=0){
			//verificaempleado=false;
           var campo0,campo1, campo2, campo3, campo4, campo5, campo6;
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
						elem1 = campo1.split('(');
						descripcion=elem1[0];
						var tipoprodserv1 = elem1[1];
						var ln= tipoprodserv1.length-1;
						tipoprodserv = tipoprodserv1 .substring(0,ln);

                        break;
                    case 2:
						campo2 = $(this).text();
                        break;
                    case 3:
						 campo3= $(this).find("#precio_venta").val();
						if (isNaN(campo3)==false){
							precio_venta=parseFloat(campo3);
						}
                        break;
                    case 4:
                        campo4= $(this).find("#cant").val();
						if (isNaN(campo4)==false){
							cantidad=parseFloat(campo4);
						}
                        break;
                    case 5:
						campo5 = $(this).text();
                    case 6:
						id_empleado= $(this).find("#select_empleado option:selected").val();
						campo6=id_empleado;
						if(id_empleado=='-1' && tipoprodserv=='SERVICIO'){
							verificaempleado='verificar';
							$(this).find("#select_empleado").focus();
							//return false;
					    }
					    else {
							verificaempleado='noverificar';
						}
						 break;
                }


            });

            if(campo0!=""|| campo0==undefined || isNaN(campo0)==false ){
				//StringDatos+=campo0+"|"+tipoprodserv+"|"+precio_venta+"|"+cantidad+"|"+id_empleado+"|"+verificaempleado+"#";
				StringDatos+=campo0+"|"+descripcion+"|"+tipoprodserv+"|"+precio_venta+"|"+cantidad+"|"+id_empleado+"#";
				verifica.push(verificaempleado);
				i=i+1;
			}
          }

        });
       verifica.forEach(function (item, index, array) {
			if (item=='verificar'){
				verificaempleado='verificar';
			}
		});
	var id=$("select#tipo_entrada option:selected").val(); //get the value
	if (id=='1'){
		var dataString='process=print2'+'&stringdatos='+StringDatos+'&cuantos='+i+'&id='+id+'&numero_doc='+numero_doc+'&fecha_movimiento='+fecha_movimiento+'&id_cliente='+id_cliente;
			dataString+='&total_ventas='+total_ventas+'&verificaempleado='+verificaempleado;
	}
	if (id=='2'){
		var dataString='process=print2'+'&stringdatos='+StringDatos+'&cuantos='+i+'&id='+id+'&numero_doc='+numero_doc2+'&fecha_movimiento='+fecha_movimiento2+'&id_cliente='+id_cliente;
			dataString+='&total_ventas='+total_ventas+'&verificaempleado='+verificaempleado;
	}
	if (verificaempleado=='noverificar'){
		$.ajax({
				type:'POST',
				url:'enviar_traslado_sucursal.php',
				data: dataString,
				dataType: 'json',
				success: function(datos){
						sist_ope=datos.sist_ope;
					//esta opcion es para generar recibo en  printer local y validar si es win o linux
					if (sist_ope=='win'){
						$.post("http://localhost:8080/variedades/printpos2.php",{datosventa:datos.facturar})
					}
					else {
						$.post("http://localhost/variedades/printpos2.php",{datosventa:datos.facturar})
					}
				}
			});
		}
		else{
			var typeinfo='Warning';
			var msg='Falta seleccionar Empleado que brinda algun servicio en Factura !';
			display_notify(typeinfo,msg);
		}

}


 function reload1(){
	location.href = 'enviar_traslado_sucursal.php';
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
			setInterval("location.reload();", 3000);
			//$('#deleteModal').hide();
		}
	});
}
$("#inventable").on('change', '#select_precios', function(){
	subtotales();
	totales();
});
function subtotales(){
	$("#inventable tbody>tr").each(function (index) {
		precio_lista= $(this).find("#select_precios option:selected").val();
		$(this).find("#precio_venta").val(precio_lista);
	});
}
