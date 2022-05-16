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
//datepicker active
//$( ".datepick" ).datepicker();
//ocultar div
$('#datos_entrada').hide();
$('#form_averia').hide();
$('#form_vencimiento').hide();
$('#form_sucursal').hide();
$('#form_consumo').hide();

$('#buscador').hide();
$("#producto_buscar").typeahead({
            source: function(query, process) {
            //var textVal=$("#producto_buscar").val();
                $.ajax({
                    url: 'inventario_inicial2.php',
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
	$('#id_proveedor2').select2();
	$('#id_proveedor3').select2();
	$('#id_sucursal3').select2();
	var id=$("select#tipo_entrada option:selected").val();

	if(id!='0')
		$('#buscador').show();
    else
		$('#buscador').hide();

	if (id=='1')
		$('#form_averia').show();
	else
		$('#form_averia').hide();
	if (id=='2')
		$('#form_vencimiento').show();
	else
		$('#form_vencimiento').hide();
    if (id=='3')
		$('#form_sucursal').show();
	else
		$('#form_sucursal').hide();
	if (id=='4')
		$('#form_consumo').show();
	else
		$('#form_consumo').hide();
});


// Agregar productos a la lista del inventario
 function agregar_producto_lista(id_prod,descrip,costo){
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
			//var costo_prom = JSON.parse(data.costo_prom);
			 var  cp  = data.costo_prom;
			 var  pre_unit  = data.pre_unit;
			 var existencias= data.existencias;
			 var fechas_vence=data.fechas_vence;
			 var stock_vence=data.stock_vence;
			 var perecedero=data.perecedero;
			 // alert(cp);
			$("#inventable tr").each(function (index) {
           	id_prev=$(this).closest('tr').children('td:first').text();
			if(id_prev==id_prod){
				id_prod="";
			}
			});
	if (perecedero==1){
			//descrip_only=descrip_only+"<label class='text-danger'>"+"Producto Perecedero"+"</label>";
			//stock_vence.sort(function(a, b){return b-a});
			select_fechas="<select id='fecha_caducidad' class='form-control'>";
			$.each(stock_vence, function(i,val){
			var values=val.split('|');

			var val0=values[0]+'|'+values[1];


			if(values[1]=='00-00-0000'){
				var val1='No_F_Vence|'+values[2];
			}
			else{
				var val1=values[1]+'|'+values[2];
			}
			select_fechas+="<option value="+val0+">"+val1+"</option>";
			});
			select_fechas+="</select>";


		}
		if (perecedero==0){
			var val='No_F_Vence|'+existencias;
			select_fechas="<select id='fecha_caducidad' class='form-control' disabled>";
			select_fechas+="<option value="+val+">"+val+"</option>";
			select_fechas+="</select>";
		}
			 // alert(cp);
			$("#editable tr").each(function (index) {
           	id_prev=$(this).closest('tr').children('td:first').text();
			if(id_prev==id_prod){
				id_prod="";
			}
	});

	 var tr_add="";
	 tr_add += '<tr>';
		tr_add += '<td>'+id_prod+'</td>';
		tr_add += '<td>'+descrip+'</td>';
		tr_add += '<td>'+existencias+'</td>';
		tr_add += '<td>'+cp+'</td>';
		tr_add +="<td><div class='col-xs-2'><input type='text'  class='form-control' id='precio_compra' name='precio_compra' value='"+pre_unit+"' style='width:80px;'></div></td>";
		//tr_add +="<td><div class='col-xs-2'><input type='text'  class='form-control'  id='precio_venta' name='precio_venta' style='width:80px;'></div></td>";
		tr_add +="<td><div class='col-xs-2'><input type='text'  class='form-control' id='cant' name='cant'  style='width:60px;'></div></td>";
		tr_add += "<td id='fecha_caducidadd' class='text-right'>"+select_fechas+"</td>";
		tr_add += "<td class='Delete'><a href='#'><i class='fa fa-times-circle'></i> Borrar</a></td>";
		tr_add += '</tr>';
		if(id_prev!=id_prod && id_prod!="" ){
			$("#editable").append(tr_add);
		}

		}
	});
	totales();
}
//Evento que se activa al perder el foco en precio de venta y cantidad:
$(document).on("blur","#cant, #precio_compra",function(){
  totales();
})

$(document).on("blur","#editable",function(){
//$('#precio_compra').blur(function() {
  totales();
})
$(document).on("keyup","#cant, #precio_compra",function(){
  totales();
})
//function to round 2 decimal places
function round(value, decimals) {
    return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
    //round "original" to two decimals
	//var result=Math.round(original*100)/100  //returns 28.45
}
// Evento que selecciona la fila y la elimina de la tabla
$(document).on("click",".Delete",function(){
	var parent = $(this).parents().get(0);
	$(parent).remove();
	totales();
});
//Calcular Totales del grid
function totales(){
	var subtotal=0; total=0; totalcantidad=0;  cantidad=0; diferencia=0; txt_diferencia="";
	var total_dinero=0; total_cantidad=0;  precio_compra=0; precio_venta=0;
 $("#editable>tbody tr").each(function (index) {
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
						campo4=$(this).text();
						if (isNaN(campo4)==false){
							existencias=parseFloat(campo4);
						}
						else{
							existencias=0;
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

            subtotal=cantidad-existencias;
            if (isNaN(cantidad)==true){
				cantidad=0;
			}


            if (isNaN(subtotal)==true){
				subtotal="";
				subt="";
				diferencia="";
				txt_diferencia=" ";
			}
			else{
				subt=round(subtotal, 2);
				diferencia=subt.toFixed(2);
				if(subtotal>0)
					txt_diferencia="Hay una diferencia positiva de:"+subt;
				if(subtotal<0)
					txt_diferencia="Hay una diferencia negativa de:"+subt;
				if(subtotal==0)
					txt_diferencia=" ";
             }

			$(this).find("#diferencia").html(diferencia);
			$(this).find("#observacion").val(txt_diferencia);
          }
        });



}

// actualize table
$(document).on("click","#submit1",function(){
	senddata();
});

function senddata(){

	//Calcular los valores a guardar de cada item del inventario
	var i=0;
	var subtotal=0; total=0; totalcantidad=0;  cantidad=0; diferencia=0; txt_diferencia="";
	var total_dinero=0; total_cantidad=0;  precio_compra=0; precio_venta=0;
	var  StringDatos="";
	var qty=0;
	var verificar='noverificar';
	var verificador=[];

	 $("#editable>tbody tr").each(function (index) {
		 if (index>=0){
           var campo0,campo1, campo2, campo3, campo4, campo5, campo6, campo7;

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
						 campo3=$(this).text();
                        break;

					case 4:
						campo4=$(this).text();
						if (isNaN(campo4)==false){
							existencias=parseFloat(campo4);
							//verificar='noverificar';
						}
						if (isNaN(parseFloat(existencias))){
							existencias=0;
							//verificar='verificar';
						}
						break;
                    case 5:
                        campo5= $(this).find("#cant").val();
						if (isNaN(campo5)==false){
							cantidad=parseFloat(campo5);
							verificar='noverificar';
						}
						if (isNaN(parseFloat(cantidad))){
							cantidad=0;
							verificar='verificar';
						}
						break;
					 case 6:
                        campo6= $(this).text();
						if (isNaN(campo5)==false){
							diferencia=parseFloat(campo6);

						}
						if (isNaN(parseFloat(diferencia))){
							diferencia=0;
						}
						break;
					 case 7:
                        campo7= $(this).find("#observacion").val();
                        observacion=campo7;
						break;
                }
            });

            if(campo0!=""|| campo0==undefined ){
				if (cantidad>0){
				StringDatos+=campo0+"|"+existencias+"|"+cantidad+"|"+diferencia+"|"+observacion+"|"+"#";
				qty=qty+1;
			    }
				verificador.push(verificar);
				i=i+1;
			}
          }
        });
       verificador.forEach(function (item, index, array) {
			if (item=='verificar'){
				verificar='verificar';
			}
		});
	// Captura de variables a enviar
	var fecha_movimiento="";
	var numero_doc=0;
	var id_sucursal=-1;

	var fecha_movimiento=$("#fecha1").val();

	var dataString='process=insert'+'&stringdatos='+StringDatos+'&cuantos='+qty+'&fecha_movimiento='+fecha_movimiento;
     //anular verificacion si el campo de conteo fisico no esta relleno
	//if (verificar=='noverificar'){
		$.ajax({
				type:'POST',
				url:'ajuste_inventario.php',
				data: dataString,
				dataType: 'json',
				success: function(datax){
					process=datax.process;
					display_notify(datax.typeinfo,datax.msg);
					//setInterval("reload1();", 5000);
				}
			});
	/*
	}
		else{
			var typeinfo='Warning';
			var msg='Falta rellenar algun valor de cantidad de existencias!';
			display_notify(typeinfo,msg);
		}
		*/
}



 function reload1(){
	location.href = 'admin_producto.php';
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
// actualize table
$(document).on("click","#print1",function(){
	imprimir_ajustes_pdf();
});
function imprimir_ajustes_pdf(){
	var fecha_movimiento=$("#fecha1").val();

	var dataString='process=imprimir_ajuste'+'&fecha_movimiento='+fecha_movimiento;
		cadena = "ver_reporte_ajustes.php?"+dataString ;
		window.open(cadena, '', '');

}
