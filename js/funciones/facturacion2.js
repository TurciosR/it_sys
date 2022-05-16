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
                    
$(".decimal").numeric();
                   
//select2 select autocomplete
$('#categoria').select2();
$('#id_cliente').select2();

$('#form_fact_consumidor').hide();
$('#form_fact_ccfiscal').hide(); 

//Boton de imprimir deshabilitado hasta que se guarde la factura
$('#print1').prop('disabled', true);
//$('#print1').prop('disabled', false);

//$('#buscador').hide(); 
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
						 var prod= prod0.split("-");
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
$(document).on("change","#id_cliente", function (){
	var id=$("select#id_cliente option:selected").val(); //get the value
	$('#datos_cliente').load('facturacion.php?'+'process=mostrar_datos_cliente'+'&id='+id); 
});		

// Seleccionar el tipo de factura
$(document).on("change","#tipo_entrada", function(){
	var id=$("select#tipo_entrada option:selected").val(); //get the value
	//alert(id);
	$('#mostrar_numero_doc').load('facturacion.php?'+'process=mostrar_numfact'+'&id='+id); 
});	

// Agregar productos a la lista del inventario
function cargar_empleados(){
	$('#inventable>tbody>tr').find("#select_empleado").each(function(){
			$(this).load('facturacion.php?'+'process=cargar_empleados');	
			totales();
		});
}
	
function agregar_producto_lista(id_prod,descrip,costo){	
//var filas=0; 
	var id_prev="";
	var tipoprod ="";
	var id_prevprod="";
	var elem = descrip.split('(');
	var descripcion= elem[0];
	var tipoprodserv = elem[1];
	var ln= tipoprodserv.length-1;
	tipo = tipoprodserv.substring(0,ln);
	var id_prodtipo=id_prod+tipo;
	var dataString = 'process=consultar_stock' + '&id_producto=' + id_prod+'&tipo='+tipo;
	var filas=0;
	$.ajax({
		type : "POST",
		url : "facturacion.php",
		data : dataString,
		dataType : 'json',
		success : function(data) {
			var  pv  = data.precio_venta;
			var existencias= data.existencias;
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
		
	var select_empleados="";					
	 
	 if (tipo=='SERVICIO'){
		select_empleados="<select name='select_empleado' id='select_empleado' class='form-control' ></select>";
	 }
	 else{
		 select_empleados="<select name='select_empleado' id='select_empleado' class='form-control' disable>";
		  select_empleados+="<option value='-1'>Mostrador</option>";
		  select_empleados+="</select>";
	 }
	 var tr_add="";
	 var subtotal=0;
	 if (existencias==null){
		 existencias=0;
	 }
	 tr_add += '<tr>';
		tr_add += '<td>'+id_prod+'</td>';
		tr_add += '<td>'+descrip+'</td>';	
		tr_add += '<td>'+existencias+'</td>';	
		subtotal=1*pv;	
		tr_add +="<td><div class='col-xs-2'><input type='text'  class='form-control decimal'  id='precio_venta' name='precio_venta' value='"+pv+"' style='width:80px;'></div></td>";
		tr_add +="<td><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value='1' style='width:60px;'></div></td>";
		tr_add += "<td id='subtot'>"+subtotal+"</td>";
		tr_add += "<td>"+select_empleados+"</td>";
		tr_add += "<td class='Delete'><a href='#'><i class='fa fa-times-circle'></i></a></td>";
		
		tr_add += '</tr>';
		totales();
		if(id_prevprod!=id_prodtipo && id_prodtipo!="" && existencias>0 ){
			if (filas<=5){
			$("#inventable").append(tr_add);	
			$(".decimal").numeric();			
				cargar_empleados();
				totales();
			}	
				
		}
		if(id_prevprod==id_prodtipo && tipo=="SERVICIO" ){
			if (filas<=5){
				$("#inventable").append(tr_add);	
				$(".decimal").numeric();
				cargar_empleados();
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
$(document).on("keyup","#cant, #precio_venta",function(){
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
                        break;    
                    case 3:
						 campo3= $(this).find("#precio_venta").val();
                       
						if (isNaN(campo3)==false){ 
							precio_venta=parseFloat(campo3);
						}
						else{
							precio_venta=0;
						}
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
                }
            });
            
            subtotal=precio_venta*cantidad;
              if (isNaN(cantidad)==true){
				cantidad=0;
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

	$('#totaltexto').load('facturacion.php?'+'process=total_texto&total='+total_dinero);
}	

// actualize table data to server
$(document).on("click","#submit1",function(){
	senddata();
});

$(document).on("click","#print1",function(){
	imprime1();
});

$(document).on("click","#print2",function(){
	imprime2();
});

function senddata(){
	//Obtenerr los valores a guardar de cada item facturado
	var i=0;
	var precio_venta,precio_venta, cantidad,id_prod,id_empleado;
	var elem1 = '';
	var descripcion='';
	var tipoprodserv = '';  tipoprod = '';
	var  StringDatos="";
	var id=$("select#tipo_entrada option:selected").val(); 
	
	var id_cliente=$("select#id_cliente option:selected").val(); 
	if (id=='0'){
		$('#tipo_entrada').focus();
	}
	var numero_doc=$("#numero_doc").val();
	var numero_doc2=$("#numero_doc2").val();
	var total_ventas=$('#total_dinero').text();
	var fecha_movimiento=$("#fecha").val();
	var fecha_movimiento2=$("#fecha2").val();
	
	
	if (numero_doc==undefined || numero_doc=='' || fecha_movimiento=='' || fecha_movimiento==undefined){
		 //numero_doc=0;
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
				//StringDatos+=campo0+"|"+tipoprodserv+"|"+precio_venta+"|"+cantidad+"|"+id_empleado+"|"+verificaempleado+"#";
				StringDatos+=campo0+"|"+tipoprodserv+"|"+precio_venta+"|"+cantidad+"|"+id_empleado+"#";
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
		var dataString='process=insert'+'&stringdatos='+StringDatos+'&cuantos='+i+'&id='+id+'&numero_doc='+numero_doc+'&fecha_movimiento='+fecha_movimiento+'&id_cliente='+id_cliente; 
			dataString+='&total_ventas='+total_ventas+'&verificaempleado='+verificaempleado;
	}
	if (id=='2'){
		var dataString='process=insert'+'&stringdatos='+StringDatos+'&cuantos='+i+'&id='+id+'&numero_doc='+numero_doc2+'&fecha_movimiento='+fecha_movimiento2+'&id_cliente='+id_cliente; 
			dataString+='&total_ventas='+total_ventas+'&verificaempleado='+verificaempleado;
	}
	if (verificaempleado=='noverificar'){
		$.ajax({
				type:'POST',
				url:'facturacion.php',
				data: dataString,			
				dataType: 'json',
				success: function(datax){	
					process=datax.process;
					display_notify(datax.typeinfo,datax.msg);		
					$('#print1').prop('disabled', false);
					$('#submit1').prop('disabled', true);
				}
			}); 
		}
		else{
			var typeinfo='Warning';
			var msg='Falta seleccionar Empleado que brinda algun servicio en Factura !';
			display_notify(typeinfo,msg);
		}
			
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
	
	if (numero_doc==undefined || numero_doc=='' || fecha_movimiento=='' || fecha_movimiento==undefined){
		 //numero_doc=0;
		var typeinfo='Warning';
			var msg='Falta Numero de Factura  o Fecha!';
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
	var id=$("select#tipo_entrada option:selected").val(); //get the value
	if (id=='1'){
		var dataString='process=print1'+'&stringdatos='+StringDatos+'&cuantos='+i+'&id='+id+'&numero_doc='+numero_doc+'&fecha_movimiento='+fecha_movimiento+'&id_cliente='+id_cliente; 
			dataString+='&total_ventas='+total_ventas+'&verificaempleado='+verificaempleado;
	}
	if (id=='2'){
		var dataString='process=print1'+'&stringdatos='+StringDatos+'&cuantos='+i+'&id='+id+'&numero_doc='+numero_doc2+'&fecha_movimiento='+fecha_movimiento2+'&id_cliente='+id_cliente; 
			dataString+='&total_ventas='+total_ventas+'&verificaempleado='+verificaempleado;
	}
	if (verificaempleado=='noverificar'){
		$.ajax({
				type:'POST',
				url:'facturacion.php',
				data: dataString,			
				dataType: 'json',
				success: function(datos){	
					sist_ope=datos.sist_ope;
					//esta opcion es para generar recibo en  printer local y validar si es win o linux
					if (sist_ope=='win'){
						$.post("http://localhost:8080/premier/printpos1.php",{datosventa:datos.facturar})
					}
					else {
						$.post("http://localhost/premier/printpos1.php",{datosventa:datos.facturar})
					}
					setInterval("reload1();", 5000);
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
				url:'facturacion.php',
				data: dataString,			
				dataType: 'json',
				success: function(datos){	
						sist_ope=datos.sist_ope;
					//esta opcion es para generar recibo en  printer local y validar si es win o linux
					if (sist_ope=='win'){
						$.post("http://localhost:8080/premier/printpos2.php",{datosventa:datos.facturar})
					}
					else {
						$.post("http://localhost/premier/printpos2.php",{datosventa:datos.facturar})
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
	location.href = 'facturacion.php';	
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

