var urlfactura="";
$(document).ready(function() {
urlfactura=$("#facturacion").val();
$(".decimal").numeric();
$('.checkbox').checkbox();
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
						 agregar_producto_lista(id_prod, descrip,marca);
				}
        });

$( "select" ).change(function () {
  totales();
 })
//
$("#inventable>tbody  tr").each(function (index) {
if (index>=0){
	$('#inventable').find('tr#'+index).find('td:eq(3)').find('#select_precios'+index).change(function(){
				console.log("entor al select:"+index );
				$("#select_precios"+index+" option:selected").each(function() {
					precio = $( this ).text();
					change=true;
					setRowPrice(index,precio,change);
				});

			})
	 }
	 });
//
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
/*
$(document).on("change","#id_cliente", function (){
	var id=$("select#id_cliente option:selected").val(); //get the value
	$('#datos_cliente').load('pedido.php?'+'process=mostrar_datos_cliente'+'&id='+id);
});
*/
// Seleccionar el tipo de factura
/*
$(document).on("change","#tipo_entrada", function(){
	var id=$("select#tipo_entrada option:selected").val(); //get the value
	//alert(id);
	$('#mostrar_numero_doc').load('pedido.php?'+'process=mostrar_numfact'+'&id='+id);
});
*/
/*
function cargar_empleados(){
	$('#inventable>tbody>tr').find("#select_empleado").each(function(){
			$(this).load('pedido.php?'+'process=cargar_empleados');
			totales();
		});
}
* */
// Evento para agregar elementos al grid de factura
function agregar_producto_lista(id_prod,descrip,costo){
	var id_prev=0;
	var tipoprod ="";
	var id_prevprod="";
	var descrip_only="";
	var elem = descrip.split('(');
	var descripcion= elem[0];
	var elem2 = descripcion.split(']');
	descrip_only= elem2[1];
	var tipoprodserv = elem[1];
	var ln= tipoprodserv.length-1;
	tipo = tipoprodserv.substring(0,ln);
	var id_prodtipo=id_prod+tipo;
	var dataString = 'process=consultar_stock' + '&id_producto=' + id_prod+'&tipo='+tipo;
	var filas=0;

	if (descrip_only==undefined){
		descrip_only=descripcion;
	}
	$.ajax({
		type : "POST",
		url : urlfactura,
		data : dataString,
		dataType : 'json',
		success : function(data) {
			var  pv  = data.precio_venta;
			var existencias= data.existencias;
			var costos_pu= data.costos_pu;
			var precios_vta= data.precios_vta;
			var iva= data.iva;
			var unidades= data.unidades;
			var costo_prom=data.costo_prom;
			var imagen=data.imagen;
			var combo=data.combo;
			var fecha_caducidad=data.fecha_caducidad;
			var stock_fecha=data.stock_fecha;
			var id_prev;
			var id_previo=new Array();
			$("#inventable tr").each(function (index) {
			if (index>0){
			var campo0,campo1, campo2,campo5;

            $(this).children("td").each(function (index2) {
           	  switch (index2){
                    case 0:
						campo0 = $(this).text();

						if (campo0!=undefined || campo0!=''){
							id_previo.push(campo0);
						}
						break;
					}
				});
				filas=filas+1;
				 } //if index>0
		});
			if (imagen==''){
				var imagen_div="<img alt='image' class='img-rounded rounded-border' width='300px' height='160px' src='img/productos/no_disponible.png'>";
		    }
		    else{
				var imagen_div="<img alt='image' class='img-rounded rounded-border' width='300px' height='160px'  src='"+imagen+"'>";
			}
			var description="<p class='font-bold'>Producto: "+descrip_only +"</p>"
			if (existencias>0){
				$("#imagen").html(imagen_div+description);
			}
			else{
				var imagen_div="<img alt='image' class='img-rounded rounded-border' width='300px' height='160px' src='img/productos/no_stock.png'>";
				var description="<p class='font-bold'>"+descrip_only+" Sin Existencias  "+"</p>"
				$("#imagen").html(imagen_div+description);
			}

		//select_precios="<select name='select_precios' id='select_precios' class='form-control'>";
		select_precios="<select name='select_precios' id='select_precios"+filas +"' class='form-control'>";
		if (tipo=='SERVICIO'){
			pr1=pv;pr2=pv;pr3=pv;pr4=pv;
			pr_unit=pv;
			select_precios+="<option value="+pr_unit+">"+pr_unit+"</option>";
		}
		else{
			precios_vta.sort(function(a, b){return b-a});
			$.each(precios_vta, function(i,pr_unit){
			select_precios+="<option value="+pr_unit+">"+pr_unit+"</option>";
			});
		}
		select_precios+="</select>";
		var tr_add="";
		var subtotal=0;
		if (existencias==null){
			existencias=0;
		}
		combo_chk="<input type='checkbox' class='checkbox' id='check_combo' value='0' />";

		pr1=precios_vta[0];
		subtotal=1*pr1;
		pr_min=precios_vta[3];
		if (fecha_caducidad!="0000-00-00"){
			descrip=descrip+"<label class='text-danger'>"+"--> Existencias:"+stock_fecha+" Fecha prox. Caducidad: "+fecha_caducidad+"</label>";
		}
		tr_add += "<tr id='"+filas+"'>";
		tr_add += '<td>'+id_prod+'</td>';
		tr_add += '<td>'+descrip+'</td>';
		tr_add += "<td id='cant_stock'>"+existencias+"</td>";
		tr_add += "<td>"+select_precios+"</td>";
		tr_add +="<td><input type='hidden'  id='precio_venta_inicial' name='precio_venta_inicial' value='"+pr_min+"'><div class='col-xs-2'><input type='text'  class='form-control decimal'  id='precio_venta' name='precio_venta' value='"+pr1+"' style='width:80px;'></div></td>";
		tr_add +="<td><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value='1' style='width:60px;'></div></td>";
		tr_add += "<td id='subtot' class='text-right'>"+subtotal+"</td>";
		if (combo=='1'){
			tr_add += "<td id='combos'  class='text-center'>"+combo_chk+"</td>";
		}
		else {
			tr_add += "<td id='nocombos'></td>";
		}
		tr_add += "<td class='Delete'><a><i class='fa fa-times-circle'></i></a></td>";
		tr_add += '</tr>';

		if (filas>1000){
			var typeinfo='Warning';
			var msg='Numero de Filas en Factura excede el maximo permitido !';
			display_notify(typeinfo,msg);
		}
		else{
			var existe=false;
			var posicion_fila=0;
			$.each(id_previo, function(i,id_prod_ant){
				if(id_prod==id_prod_ant  ){
					existe=true;
					posicion_fila=i;
				}
			});
			if (existe==false && existencias>0 ){
				$("#inventable").append(tr_add);
				$(".decimal").numeric();
				$('.checkbox').checkbox();
			}
			if (existe==true && existencias>0 && combo==0){
			//if (existe==true && existencias>0 ){
				$(".decimal").numeric();
				posicion_fila=posicion_fila+1;
				setRowCant(posicion_fila);
			}
			if (existe==true && existencias>0 && combo==1){
			//if (existe==true && existencias>0 ){
				$("#inventable").append(tr_add);
				$(".decimal").numeric();
				$('.checkbox').checkbox();
			}
			totales();
		}
	}
});
	totales();
}
// reemplazar valores de celda cantidades
function setRowCant(rowId){
    var cantidad_anterior=$('#inventable tr:nth-child('+rowId+')').find('td:eq(5)').find("#cant").val();
    var cantidad_nueva= parseFloat(cantidad_anterior)+1;
    $('#inventable tr:nth-child('+rowId+')').find('td:eq(5)').find("#cant").val(cantidad_nueva);
};

// reemplazar valor de select precios modificar para 1 linea
function setRowPrice(rowId, new_price,stat){
	fila=rowId;
	new_price2=parseFloat(new_price);
	if(stat==true){
		$('#inventable').find('tr#'+fila).find('td:eq(4)').find("#precio_venta").val(new_price);
		var cantidad=$('#inventable').find('tr#'+fila).find('td:eq(5)').find("#cant").val();
		var new_subtotal=parseFloat(cantidad)*new_price2;
		new_subtotal=round(new_subtotal,2);
		var new_subtotal2=new_subtotal.toFixed(2);
		console.log('new subt:'+new_subtotal2);
		$('#inventable').find('tr#'+fila).find('td:eq(6)').html(new_subtotal2);
		totalFact();
	}
};
//tomar totales de subtotal unicamente para mejor velocidad totales fact 2
function totalFact(){
    var TableData = new Array();
    var i=0,total=0;
    var td1;var td2;var td3;var td4;var td5;var td6;
	var StringDatos='';
	$("#inventable>tbody  tr").each(function (index) {
		 if (index>=0){
           var subtotal;
            $(this).children("td").each(function (index2) {
                switch (index2){
                    case 6:
                       subtotal= parseFloat( $(this).text());
                        break;
                }
            });

          }

           total+=subtotal;
        });
	total=round(total,2);
	total_dinero=total.toFixed(2);
	//total_cantidad=totalcantidad.toFixed(2);
	$('#total_dinero').html("<strong>"+total_dinero+"</strong>");
	$('#totalfactura').val(total_dinero);
	$('#totaltexto').load('genera_venta.php?'+'process=total_texto&total='+total_dinero);
}
//fin totales fact 2
//Evento que se activa al perder el foco en precio de venta y cantidad:
//Evento que se activa al perder el foco en precio de venta y cantidad:
$(document).on("blur","#cant, #precio_venta",function(){
  totales();
  totalFact()
})
//Evento que valida el enter a traves del teclado
$(document).on("keydown","#precio_venta", function(event){
	var keycode = (event.keyCode ? event.keyCode : event.which);
	if(keycode == '13'){
		totales();
	}
})

$(document).on("focusout","#cant, #precio_venta",function(){
	totales();
})

$(document).on("blur","#inventable",function(){
	totales();
})

$(document).on("keyup","#cant", function(){
	totales();
})
$(document).on("change","#check_combo",function() {
	totales();
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
	var total_dinero=0; total_cantidad=0;  precio_venta=0;cant_dev=0;
	var total_fact=0; dev_stock=0; cant_fact=0;subtot_consigna=0;total_dev_stock=0;subtotal_fact=0;
	var total_cant_consigna=0;total_cant_fact=0; total_dinero_fact=0;total_dinero_consigna=0;
	var cant_max_facturar=0; cant_facturado_ante=0;
	//$("#inventable>tbody tr").each(function (index) {
		$("#inventable tr").each(function (index) {
		if (index>0){

           var campo0,campo1, campo2, campo3, campo4, campo5, cant_consigna,campo6,campo7 ;
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
							cant_consigna=parseFloat(campo2);
                        else
							cant_consigna=0;
                        break;
                    case 3:
						campo3 = $(this).text();
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
                    /*
						campo4=  $(this).text();
						if (isNaN(campo4)==false)
							subtot_consigna=parseFloat(campo4);
						else
							subtot_consigna=0;
                        break;
                     */
                    case 5:
                     campo5= $(this).find("#cant").val();
						if (isNaN(campo5)==false){
							cantidad=parseFloat(campo5);
						}
						else{
							cantidad=0;
						}
                        break;
                    /*
                        campo5= $(this).find("#cant_fact").val();
						if (isNaN(campo5)==false)
							cant_fact=parseFloat(campo5);
						else
							cant_fact=0;

                        break;
                        */
                   case 6:
						campo6=$(this).text();
						if (isNaN(campo6)==false)
							cant_dev=parseFloat(campo6);
						else
							cant_dev=0;

						//cant_max_facturar=cant_consigna-cant_facturado_ante;
                        break;
                    case 7:
						campo7=$(this).text();
						if (isNaN(campo7)==false)
							cant_facturado_ante=parseFloat(campo7);
						else
							cant_facturado_ante=0;

						cant_max_facturar=cant_consigna-cant_facturado_ante;
						if(cant_fact>=cant_max_facturar){
							$(this).find("#cant_fact").val(cant_max_facturar);
						}
						else{
							$(this).find("#cant_fact").val(cant_fact);
						}
                        break;
                }
            });
            /*
            cant_max_facturar=cant_consigna-cant_facturado_ante;
             if(cant_fact>=cant_max_facturar){
				$(this).find("#cant_fact").val(cant_max_facturar);
				//$(this).find("#cant_fact").val(cant_consigna);
				$(this).find("#dev_stock").html("0");
				cant_fact=cant_max_facturar;
				dev_stock=0;
				cant_dev=0;
				subtotal_fact=precio_venta*cant_max_facturar;
				$(this).find("#subtot_fact").html(subtotal_fact);
			}
			else{
				dev_stock=cant_max_facturar-cant_fact;
				cant_dev=cant_max_facturar-cant_fact;

				if (isNaN(dev_stock)==true)
					dev_stock=cant_max_facturar;

				//dev_stock_fin=dev_stock.toFixed(2);
				dev_stock_fin=cant_dev.toFixed(2);
				$(this).find("#dev_stock").html(dev_stock_fin);

				subtotal_fact=precio_venta*cant_fact;
				if (isNaN(subtotal_fact)==true)
					subtotal_fact=0;
				subtotal_fin=subtotal_fact.toFixed(2);
				$(this).find("#subtot_fact").html(subtotal_fin);

				cant_fact=parseFloat($(this).find("#cant_fact").val());
				if (isNaN(cant_fact)==true){
					cant_fact=0;
					cant_dev=cant_consigna-cant_facturado_ante;
					$(this).find("#dev_stock").html(cant_dev);
				}
			}*/
			 if (isNaN(cantidad)==true){
				cantidad=0;
			}
			if(cantidad>=cant_stock){
				$(this).find("#cant").val(cant_stock);
				cantidad=cant_stock;
			}
			subtotal=precio_venta*cantidad;

			var pv_inicial= $(this).find("#precio_venta_inicial").val();
			if(precio_venta<pv_inicial){
				$(this).find("#precio_venta").val(pv_inicial);
				subtotal=pv_inicial*cantidad;
			}
			else{
				subtotal=precio_venta*cantidad;
            }

			 var precio = "";
            change=false;
			$('#inventable').find('tr#'+index).find('td:eq(3)').find('#select_precios'+index).change(function(){
				console.log("entor al select:"+index );
				$("#select_precios"+index+" option:selected").each(function() {
					precio = $( this ).text();
					change=true;
					setRowPrice(index,precio,change);
				});
			})

			//calcular totales	finales
			/*
			total_cant_consigna=parseFloat($('#total_cant_consigna').html());
			total_cant_fact_ante=parseFloat($('#total_cant_facturado_ante').html());
			total_cant_fact=cant_fact+total_cant_fact;
			total_dinero_consigna=subtot_consigna+total_dinero_consigna;
			total_dinero_fact=subtotal_fact+total_dinero_fact;
			total_dev_stock=total_cant_consigna -(total_cant_fact + total_cant_fact_ante);
			*/
			 if( $(this).find('#check_combo').prop('checked')){
				$(this).find("#precio_venta").val('0');
				subtotal=0;
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
    /*
    total_dinero=total_dinero_fact.toFixed(2);
    total_dev_stock_fin=total_dev_stock.toFixed(2);
    $('#total_cant_fact').html(total_cant_fact);
    $('#total_dev_stock').html(total_dev_stock_fin);
	$('#total_dinero_fact').html("<strong>"+total_dinero+"</strong>");
	$('#totaltexto').load(urlfactura+'?'+'process=total_texto&total='+total_dinero);
	*/
	 if (isNaN(total)==true){
			total=0;
		}
        total_dinero=total.toFixed(2);
        total_cantidad=totalcantidad.toFixed(2);

        $('#total_dinero').html("<strong>"+total_dinero+"</strong>");
        $('#totcant').html(total_cantidad);
		$('#totalfactura').val(total_dinero);
		$('#totaltexto').load('editar_pedido.php?'+'process=total_texto&total='+total_dinero);
}

// actualize table data to server
$(document).on("click","#submit1",function(){
	senddata();
	// activa_modal();
});

$(document).on("click","#print1",function(){
	imprimir_pdf();
	reload1();
});

$(document).on("click","#print2",function(){
	imprime2();
});
function activa_modal(numfact){
	$('#viewModal').modal({backdrop: 'static',keyboard: false});
	//var totalfinal=parseFloat($('#totalfactura').val());
	var totalfinal=parseFloat($('#total_dinero_fact').text());
	//var numdoc=$('#numero_doc').val()
	var numdoc=numfact
	var facturado= totalfinal.toFixed(2);
    $(".modal-body #facturado").val(facturado);
     $(".modal-body #fact_num").html(numdoc);
}

$(document).on("click", "#btnPrintFact", function (event) {
	imprime1();
});
$(document).on("click", "#btnEsc", function (event) {
		finalizar2();
	});

function senddata(){
	//Obtener los valores a guardar de cada item facturado
	var i=0;
	var precio_venta,precio_venta, cantidad,id_prod,id_empleado;
	var elem1 = '';
	var descripcion='';
	var tipoprodserv = '';  tipoprod = '';
	var  StringDatos="";
	var id='1';
	var id_empleado=0;
	var id_cliente=$("#id_cliente").val();
	var numero_doc=$("#numero_doc").val();
	var total_ventas=$('#total_dinero').text();
	var fecha_movimiento=$("#fecha").val();
	var fecha_entrega=$("#fecha1").val();
	var lugar_entrega=$("#lugar_entrega").val();
	var observacion=$("#observacion").val();

	if (fecha_movimiento=='' || fecha_movimiento==undefined){
		var typeinfo='Warning';
		var msg='Falta  Fecha!';
		display_notify(typeinfo,msg);
	}
	if (id_empleado=='' || id_empleado==undefined){
		 id_empleado=0;
	}
	if (numero_doc==undefined || numero_doc==''){
		 numero_doc=0;
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


						tipoprodserv = 'PRODUCTO';
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
                         case 6:
						campo6 = $(this).text();
						 break;
                }
            });
            if(campo0!=""|| campo0==undefined || isNaN(campo0)==false ){
				StringDatos+=campo0+"|"+tipoprodserv+"|"+precio_venta+"|"+cantidad+"|"+campo6+"#";
				//verifica.push(verificaempleado);
				i=i+1;
			}
          }

        });

		var dataString='process=insert'+'&stringdatos='+StringDatos+'&cuantos='+i+'&id='+id+'&numero_doc='+numero_doc+'&fecha_movimiento='+fecha_movimiento+'&id_cliente='+id_cliente;
		dataString+='&total_ventas='+total_ventas+'&id_empleado='+id_empleado;
		dataString+='&lugar_entrega='+lugar_entrega+'&fecha_entrega='+fecha_entrega+'&observacion='+observacion;


	$.ajax({
			type:'POST',
			url:urlfactura,
			data: dataString,
			dataType: 'json',
			success: function(datax){
				process=datax.process;

				display_notify(datax.typeinfo,datax.msg);

			}
	});
}
/*
function senddata(){
	//Obtenerr los valores a guardar de cada item a facturar
	var i=0;
	var precio_venta,precio_venta, cantidad,id_prod,id_empleado;
	var elem1 = '';
	var  StringDatos="";

	var numero_doc=$("#numero_doc").val();
	var fecha_movimiento=$("#fecha").val();
	//var total_ventas=$('#total_dinero_fact').text();
	var total_ventas=$('#total_dinero').text();
	//valores totales a enviar para validar pedido y facturar
	var  total_cant_fact=$('#total_cant_fact').text();
	var  total_cant_consigna=$('#total_cant_consigna').text();
	var total_cant_facturado_ante=$('#total_cant_facturado_ante').text();

	if (numero_doc==undefined || numero_doc=='' || fecha_movimiento=='' || fecha_movimiento==undefined){
		var typeinfo='Warning';
		var msg='Falta Numero de Factura  o Fecha!';
		display_notify(typeinfo,msg);
	}
	//var verificaempleado='noverificar';
	//var verifica=[];
	$("#inventable>tbody tr ").each(function (index) {
		if (index>=0){
           var campo0,campo1, campo2, campo3, campo4, campo5, campo6,id_prod,subtot_fact;
            $(this).children("td").each(function (index2) {
                           switch (index2){
                    case 0:
						campo0 = $(this).text();
						if (campo0==undefined){
							campo0='';
						}
						id_prod=campo0;
                        break;
                    case 1:
						campo1 = $(this).text();
                        break;
                    case 2:
						campo2 = $(this).text();
						if (isNaN(campo2)==false)
							precio_venta=parseFloat(campo2);
						else
							precio_venta=0;
                        break;
                    case 3:
						campo3 = $(this).text();
						if (isNaN(campo3)==false)
							cant_consigna=parseFloat(campo3);
                        else
							cant_consigna=0;
                        break;
                    case 4:
						campo4=  $(this).text();
						if (isNaN(campo4)==false)
							subtot_consigna=parseFloat(campo4);
						else
							subtot_consigna=0;
                        break;

                    case 5:
                        campo5= $(this).find("#cant_fact").val();
						if (isNaN(campo5)==false)
							cant_fact=parseFloat(campo5);
						else
							cant_fact=0;

                        break;
                   case 6:
						campo6=$(this).text();
						if (isNaN(campo6)==false)
							cant_dev=parseFloat(campo6);
						else
							cant_dev=0;

                        break;
                   case 7:
						campo7=  $(this).text();
						if (isNaN(campo7)==false)
							subtot_fact=parseFloat(campo7);
						else
							subtot_fact=0;
                        break;
                }
            });
            if(campo0!=""|| campo0==undefined || isNaN(campo0)==false ){
				StringDatos+=id_prod+"|"+precio_venta+"|"+cant_fact+"|"+cant_dev+"#";
				i=i+1;
			}
          }
        });
	var id=$("select#tipo_entrada option:selected").val(); //get the value
	var dataString='process=insert'+'&stringdatos='+StringDatos+'&cuantos='+i+'&numero_doc='+numero_doc;
	dataString+='&fecha_movimiento='+fecha_movimiento+'&total_ventas='+total_ventas;
	dataString+='&total_cant_fact='+total_cant_fact+'&total_cant_consigna='+total_cant_consigna+'&total_cant_facturado_ante='+total_cant_facturado_ante;
	var numfact=0;
	$.ajax({
			type:'POST',
			url:urlfactura,
			data: dataString,
			dataType: 'json',
			success: function(datax){
				process=datax.process;
				numfact=datax.numfact;
				if (numfact!="NO_FACT"){
					display_notify(datax.typeinfo,datax.msg);
					$('#submit1').prop('disabled', true);
					activa_modal(numfact);
				}
				else {
					display_notify("Success","Consignacioin finalizada sin Factura Adicional");
					reload1();
				}
			}
	});
}
*/
$(document).on("keyup","#efectivo",function(){
	total_efectivo();
});
function total_efectivo(){
	var efectivo=parseFloat($('#efectivo').val());
	//var totalfinal=parseFloat($('#totalfactura').val());
	var totalfinal=parseFloat($('#total_dinero_fact').text());
	var facturado= totalfinal.toFixed(2);
	$('#facturado').val(facturado);
	if (isNaN(parseFloat(efectivo))){
		efectivo=0;
	}
	if (isNaN(parseFloat(totalfinal))){
		totalfinal=0;
	}
	var cambio=efectivo-totalfinal;
	var cambio=round(cambio, 2);
	var	cambio_mostrar=cambio.toFixed(2);
	$('#cambio').val(cambio_mostrar);
}
function imprime1(){
	//Utilizar la libreria esc pos php
	//Calcular los valores a guardar de cad item del inventario
	var id_factura= $(".modal-body #fact_num").text();
	id_factu=id_factura.split('_');
	id_fact=id_factu[0];
	var print='imprimir_fact';
		var dataString='process='+print+'&numero_doc='+id_fact;
		$.ajax({
				type:'POST',
				url:urlfactura,
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
	/*
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

	if (numero_doc==undefined || numero_doc=='' || fecha_movimiento=='' || fecha_movimiento==undefined){
		var typeinfo='Warning';
		var msg='Falta Numero de Factura  o Fecha!';
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

	var print='imprimir_fact';
	var id=$("select#tipo_entrada option:selected").val(); //get the value
		var dataString='process='+print+'&stringdatos='+StringDatos+'&cuantos='+i+'&id='+id+'&numero_doc='+numero_doc+'&fecha_movimiento='+fecha_movimiento+'&id_cliente='+id_cliente;
			dataString+='&total_ventas='+total_ventas+'&verificaempleado='+verificaempleado;
		$.ajax({
				type:'POST',
				url:'pedido.php',
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
*/
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
				url:'pedido.php',
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
	location.href = 'admin_pedido_nofin.php';
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
			$('#deleteModal').hide();
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
//Generar pdf de pedido
function imprimir_pdf(){
	/*
	var fecha_inicio=$("#fecha_inicio").val();
	var fecha_fin=$("#fecha_fin").val();
	var tipo=$("select#tipo option:selected").val(); //get the value
	*/
	var numero_doc=$("#numero_doc").val();
	var dataString='numero_doc='+numero_doc;
	cadena = "ver_reporte_pedido.php?"+dataString ;
	window.open(cadena, '', '');
}
function finalizar2() {
	//var id_factura = $('#id_factura').val();
	var id_factura= $(".modal-body #fact_num").text();
	id_factu=id_factura.split('_');
	id_fact=id_factu[0];
	//var dataString = 'process=reimprimir' + '&id_factura=' + id_factura;
	var dataString = 'process=finalizar_fact' + '&id_factura=' + id_factura;
	//alert("Aqui......."+dataString);
	$.ajax({
		type : "POST",
		url : "finalizar_pedido.php",
		data : dataString,
		dataType : 'json',
				success: function(datax){
				process=datax.process;
				factura=datax.factura;
				display_notify(datax.typeinfo,datax.msg);


			 setInterval("reload1();", 3000);


		}
	});
}
