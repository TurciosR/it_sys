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
/*	
function agregar_producto_lista(id_prod,descrip,costo){	
	var id_prev="";
	var tipoprod ="";
	var id_prevprod="";
	var elem = descrip.split('(');
	var descripcion= elem[0];
	var elem2 = descripcion.split(']');
	var descrip_only= elem2[1];
	var tipoprodserv = elem[1];
	var ln= tipoprodserv.length-1;
	tipo = tipoprodserv.substring(0,ln);
	var id_prodtipo=id_prod+tipo;
	var dataString = 'process=consultar_stock' + '&id_producto=' + id_prod+'&tipo='+tipo;
	var filas=0;
	
	$.ajax({
		type : "POST",
		url : "pedido.php",
		data : dataString,
		dataType : 'json',
		success : function(data) {
			var  pv  = data.precio_venta;
			var existencias= data.existencias;
			var costos_pu= data.costos_pu;
			var unidades= data.unidades;
			var costo_prom=data.costo_prom;
			var imagen=data.imagen;
			if (imagen==''){
				//imagen="no_disponible.png";
				var imagen_div="<img alt='image' class='img-rounded rounded-border' width='200px' height='100px' src='img/productos/no_disponible.png'>";
		    }
		    else{			   
		    var imagen_div="<img alt='image' class='img-rounded rounded-border' width='150px' height='100px'  src='img/productos/"+imagen+"'>";
			}
			var description="<p class='font-bold'>Imagen: "+descrip_only +"</p>"
		    //alert(imagen_div);
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
						var elem2 = tipoprod.split('(');
						var descripcion1= elem2[0];
						var tipoproducto = elem2[1];
						
						var lnprod=tipoproducto.length-1;
						tipoproduct = tipoproducto.substring(0,lnprod);
						id_prevprod=id_prev+tipoproduct;
           
						if(id_prevprod==id_prodtipo && tipo=='PRODUCTO'){
							id_prodtipo="";
						}	
						if(id_prevprod==id_prodtipo && tipo=='SERVICIO'){
							id_prodtipo=id_prevprod;
						}	
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
		var pr1=round(parseFloat(cprom0)+parseFloat(cprom0*(costos_pu[0]/100)),2);	
		$.each(costos_pu, function(i,costo_unit){
		var pr_unit=round(parseFloat(cprom0)+parseFloat(cprom0*(costo_unit/100)),2);
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
	tr_add += '<tr>';
	tr_add += '<td>'+id_prod+'</td>';
	tr_add += '<td>'+descrip+'</td>';	
	tr_add += '<td>'+existencias+'</td>';	
	tr_add += "<td>"+select_precios+"</td>";	
	tr_add +="<td><div class='col-xs-2'><input type='text'  class='form-control decimal'  id='precio_venta' name='precio_venta' value='"+pr1+"' style='width:80px;'></div></td>";
	tr_add +="<td><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value='1' style='width:60px;'></div></td>";
	tr_add += "<td id='subtot'>"+subtotal+"</td>";
	//tr_add += "<td>"+select_empleados+"</td>";
	tr_add += "<td class='Delete'><a href='#'><i class='fa fa-times-circle'></i></a></td>";	
	tr_add += '</tr>';
	
	totales();
	if(id_prevprod!=id_prodtipo && id_prodtipo!="" && existencias>0 ){
		if (filas<=5){
			$("#inventable").append(tr_add);	
			$(".decimal").numeric();			
			//cargar_empleados();
			totales();
		}				
	}
	if(id_prevprod==id_prodtipo && tipo=="SERVICIO" ){
		if (filas<=5){
				$("#inventable").append(tr_add);	
				$(".decimal").numeric();
				//cargar_empleados();
				totales();
		}				
	}
		if (filas>5){
			var typeinfo='Warning';
			var msg='Numero de Filas en Factura excede el maximo permitido !';
			display_notify(typeinfo,msg);	
	
		}
		}
	});	
	
	totales();						
}
*/
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
$(document).on("keyup","#cant, #precio_venta, #cant_fact, #dev_stock",function(){
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
						/*	
						cant_max_facturar=cant_consigna-cant_facturado_ante;		
						if(cant_fact>cant_max_facturar){
							$(this).find("#cant_fact").val(cant_max_facturar);
							$(this).find("#dev_stock").html("0");
							cant_fact=cant_max_facturar;
						}
						else{
							$(this).find("#cant_fact").val(cant_fact);	
						}
						cant_dev=cant_consigna-cant_fact;
						*/
                        break;     
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
			}
			//calcular totales	finales
			total_cant_consigna=parseFloat($('#total_cant_consigna').html());
			total_cant_fact_ante=parseFloat($('#total_cant_facturado_ante').html());
			total_cant_fact=cant_fact+total_cant_fact;
			total_dinero_consigna=subtot_consigna+total_dinero_consigna;
			total_dinero_fact=subtotal_fact+total_dinero_fact;
			total_dev_stock=total_cant_consigna -(total_cant_fact + total_cant_fact_ante);   		  
          } 
        });
       
    total_dinero=total_dinero_fact.toFixed(2);
    total_dev_stock_fin=total_dev_stock.toFixed(2);
    $('#total_cant_fact').html(total_cant_fact);
    $('#total_dev_stock').html(total_dev_stock_fin);
	$('#total_dinero_fact').html("<strong>"+total_dinero+"</strong>");	
	$('#totaltexto').load('pedido.php?'+'process=total_texto&total='+total_dinero);
}	

// actualize table data to server
$(document).one("click","#submit1",function(){
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
	//Obtenerr los valores a guardar de cada item a facturar
	var i=0;
	var precio_venta,precio_venta, cantidad,id_prod,id_empleado;
	var elem1 = '';
	var  StringDatos="";
	/*
	var id_empleado=$("select#empleados option:selected").val(); 	
	var id_cliente=$("select#id_cliente option:selected").val(); 
	*/
	var numero_doc=$("#numero_doc").val();
	var fecha_movimiento=$("#fecha").val();
	var total_ventas=$('#total_dinero_fact').text();
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
						/*
						if(cant_fact>cant_consigna){
							$(this).find("#cant_fact").val(cant_consigna);
							$(this).find("#dev_stock").html("0");
							cant_fact=cant_consigna;
						}
						cant_dev=cant_consigna-cant_fact;
						*/
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
