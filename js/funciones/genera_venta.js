var urlfactura="";
$(document).ready(function() {
	 urlfactura=$("#facturacion").val();
	    
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
						 agregar_producto_lista(id_prod, descrip,marca);
				}
        });
        
//  $(document).one("click","#submit1",function(){	
$("#submit1").one("click",function(){	
//$(".bet").one('click',function() {	 
   senddata();
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
			if (index>=0){	
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
			if (existe==false && existencias>0){
				$("#inventable").append(tr_add);
				$(".decimal").numeric();		
				$('.checkbox').checkbox();  
			}
			if (existe==true && existencias>0){
				$(".decimal").numeric();
				posicion_fila=posicion_fila+1;
				setRowCant(posicion_fila);
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
		 //totales()
		 var new_subtotal=parseFloat(cantidad)*new_price2;
		 new_subtotal=round(new_subtotal,2);
		 var new_subtotal2=new_subtotal.toFixed(2);	
		// console.log('new subt:'+new_subtotal2);
		//$('#inventable').find('tr#'+fila).find('td:eq(6)').find("#subtot").html(new_subtotal2); 
		$('#inventable').find('tr#'+fila).find('td:eq(6)').html(new_subtotal2); 
		totalFact();
	}
};

//Evento que se activa al perder el foco en precio de venta y cantidad:
$(document).on("blur","#cant, #precio_venta",function(){
  totales();
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
	//totales();
	totalFact();
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
	var precio_minimo=0;	 
			
	$("#inventable tr").each(function (index) {
		if (index>0){		
           var campo0,campo1, campo2, campo3, campo4, campo5,campo7;
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
                   case 7:
                        if( $(this).find('#check_combo').prop('checked')){
							combo_check=1;
						}
						else{
							combo_check=0;
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
            
            var precios=$('#inventable').find('tr#'+index).find('td:eq(3)').find('#select_precios'+index+' option:selected').val();
            var precio = "";
            change=false;
			$('#inventable').find('tr#'+index).find('td:eq(3)').find('#select_precios'+index).change(function(){
				//var fecha_lote = "";
				$("#select_precios"+index+" option:selected").each(function() {
					precio = $( this ).text();
					change=true;
					setRowPrice(index,precio,change);
					
				});							
			})
        
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
        if (isNaN(total)==true){
			total=0;
		}	
        total_dinero=total.toFixed(2);
        total_cantidad=totalcantidad.toFixed(2);
        
        $('#total_dinero').html("<strong>"+total_dinero+"</strong>");
        $('#totcant').html(total_cantidad);
		$('#totalfactura').val(total_dinero);
		$('#totaltexto').load('genera_venta.php?'+'process=total_texto&total='+total_dinero);
}	
//tomar totales de subtotal unicamente para mejor velocidad
//Funcion para recorrer la tabla completa y pasar los valores de los elementos a un array
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

//fin totales 2

$(document).on("click", ".print1", function () {
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

function activa_modal(numfact){
	$('#viewModal').modal({backdrop: 'static',keyboard: false});	
	var totalfinal=parseFloat($('#totalfactura').val());
	var numdoc=numfact;
	var facturado= totalfinal.toFixed(2);
    $(".modal-body #facturado").val(facturado);
    $(".modal-body #fact_num").html(numdoc);
}	
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
	var id_cliente=$("select#id_cliente option:selected").val(); 
	var numero_doc=$("#numero_doc").val();
	var total_ventas=$('#total_dinero').text();
	var fecha_movimiento=$("#fecha").val();
	var id_sucursal=$("#id_sucursal").val();
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
				//verifica.push(verificaempleado);
				i=i+1;
			}
          } 
       
        });
	
	if (id=='1'){		
		var dataString='process=insert'+'&stringdatos='+StringDatos+'&cuantos='+i+'&id='+id+'&numero_doc='+numero_doc+'&fecha_movimiento='+fecha_movimiento+'&id_cliente='+id_cliente; 
		dataString+='&total_ventas='+total_ventas+'&id_empleado='+id_empleado+'&id_sucursal='+id_sucursal;			
	}
	
	if (id=='2'){
		var dataString='process=insert'+'&stringdatos='+StringDatos+'&cuantos='+i+'&id='+id+'&numero_doc='+numero_doc2+'&fecha_movimiento='+fecha_movimiento2+'&id_cliente='+id_cliente; 
			dataString+='&total_ventas='+total_ventas+'&id_empleado='+id_empleado+'&id_sucursal='+id_sucursal;
	}

	$.ajax({
			type:'POST',
			url:urlfactura,
			data: dataString,			
			dataType: 'json',
			success: function(datax){	
				process=datax.process;
				factura=datax.factura;
				display_notify(datax.typeinfo,datax.msg);		
				$('#submit1').prop('disabled', true);
				if (urlfactura=='genera_venta.php' || urlfactura=='editar_factura.php'){
					
					$("#inventable").find("tr:gt(0)").remove();
					$('#total_dinero').html("<strong>0</strong>");
					$('#totaltexto').html("<strong>Son:</strong>");
					setInterval("reload1();", 2000);
				}
				if (urlfactura=='facturacion.php'){
					activa_modal(factura);
				}
			}
	}); 
}
$(document).on("keyup","#efectivo",function(){
  total_efectivo();
});
function total_efectivo(){
	var efectivo=parseFloat($('#efectivo').val());
	var totalfinal=parseFloat($('#totalfactura').val());
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
	var numero_doc=$(".modal-body #fact_num").html();
	var print='imprimir_fact';
	var dataString='process='+print+'&numero_doc='+numero_doc;
		$.ajax({
				type:'POST',
				url:'facturacion.php',
				data: dataString,			
				dataType: 'json',
				success: function(datos){	
					sist_ope=datos.sist_ope;
					var efectivo_fin=parseFloat($('#efectivo').val());
					var cambio_fin=parseFloat($('#cambio').val());
					//esta opcion es para generar recibo en  printer local y validar si es win o linux
					if (sist_ope=='win'){
						$.post("http://localhost:8080/variedades/printpos1.php",{datosventa:datos.facturar})
					}
					else {
						$.post("http://localhost/variedades/printpos1.php",{datosventa:datos.facturar,efectivo:efectivo_fin,cambio:cambio_fin},function(data,status){
						if (status!='success'){								
							alert("No Se envio la impresión " +data);
						}
						else{
							setInterval("reload1();", 3000);
						}	
						});
					}
				}
			}); 
}

function reload1(){
	//location.href = urlfactura;	
	if (urlfactura=='facturacion.php')
		location.href = "facturacion.php";
	if (urlfactura=='genera_venta.php'){
		location.href = "admin_facturas_vendedor.php";
		}
	if (urlfactura=='editar_factura.php')
		location.href = "admin_facturas_vendedor.php";
	
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
