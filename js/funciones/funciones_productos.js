$(document).ready(function() {
//for plugin upload images
/*
   var ruta=$("#ruta").val();
    $('#producto-img').fileinput({
        language: 'es',
        previewFileType: "image",
        uploadAsync: false,
        width: '200px',
        uploadUrl: 'http://localhost/variedades/upload.php',
        allowedFileExtensions : ['jpg', 'png','gif'],
        //var idProd=('#id_producto').val()
        uploadExtraData: function() {
            return {
                idProd: $("#id_producto").val(),
                username: $("#descripcion").val()
            };
        },
    initialPreview: [
    "<img src='"+ruta+"' class='file-preview-image' alt='Desert' title='Desert'>",
    ],
       
    });

    $("#producto-img").fileinput({
        'allowedFileExtensions' : ['jpg', 'png','gif'],
    });
*/
//end for plugin upload images


$('#formulario').validate({		
	    rules: {
                    descripcion: {  
                    required: true,           
                     },

                    unidad: {  
                    required: true, 
                    number: true,            
                     },
                    porcentaje_utilidad1: {  
                    required: true, 
                    number: true,        
                     }, 
                    porcentaje_utilidad2: {  
                    required: true, 
                    number: true,        
                     },
                     porcentaje_utilidad3: {  
                    required: true, 
                    number: true,        
                     }, 
                    porcentaje_utilidad4: {  
                    required: true, 
                    number: true,        
                     },  
                    
                 },
                messages: {
				descripcion: "Por favor ingrese la descripcion del producto",
				descripcion: "Por favor ingrese la descripción",

				unidad:{
				 required: "Por favor ingrese la unidad",
				 number: "Este campo solo puede tener números"
				},
				porcentaje_utilidad1: {
					required: "Por favor ingrese el porcentaje_utilidad1",
					number: "Este campo solo puede tener números"
				},
				porcentaje_utilidad2: {
					required: "Por favor ingrese el porcentaje_utilidad2",
					number: "Este campo solo puede tener números"
				},
				porcentaje_utilidad3: {
					required: "Por favor ingrese el porcentaje_utilidad3",
					number: "Este campo solo puede tener números"
				},
				porcentaje_utilidad4: {
					required: "Por favor ingrese el porcentaje_utilidad4",
					number: "Este campo solo puede tener números"
				},
			},
			highlight: function(element) {
					$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
				},
				success: function(element) {
					$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
				},

        submitHandler: function (form) { 
            senddata();
        }
    });
                    
    
    $('#categoria').select2();
    $('#id_proveedor').select2();
  
	//datepicker active
	//$( ".datepick" ).datepicker(); 
	
	
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


function senddata(){
	//var name=$('#name').val();  
    var descripcion=$('#descripcion').val();
    var barcode=$('#barcode').val();
    var unidad=$('#unidad').val();
    var marca=$('#marca').val();
    var color=$('#color').val();
    var embalaje=$('#embalaje').val();
    var presentacion=$('#presentacion').val();

    var estado=$('#activo:checked').val();
    var combo=$('#combo:checked').val();
    var id_proveedor=$('#id_proveedor').val();
    var id_categoria=$('select#categoria option:selected').val();
    //Get the value from form if edit or insert
	var process=$('#process').val();
	var perecedero=$('#perecedero:checked').val();
	
	if(process=='insert'){
		var id_producto=0;  
		var urlprocess='agregar_producto.php';
	}	 
	if(process=='edited'){
		var id_producto=$('#id_producto').val();
		var urlprocess='editar_productos.php';  
	}
	 if (estado==undefined){
		 estado=0;
	 }
	 else{
		 estado=1;
	 }
	  if (stock==undefined){
		 stock=0;
	 }
	var dataString='process='+process+'&id_producto='+id_producto+'&barcode='+barcode+'&descripcion='+descripcion+'&unidad='+unidad;
	dataString+='&marca='+marca+'&color='+color+'&embalaje='+embalaje+'&presentacion='+presentacion;
	dataString+='&estado='+estado+'&id_proveedor='+id_proveedor+'&id_categoria='+id_categoria+'&combo='+combo+'&perecedero='+perecedero;

			$.ajax({
				type:'POST',
				url:urlprocess,
				data: dataString,			
				dataType: 'json',
				success: function(datax){	
					process=datax.process;
					id_producto2=datax.id_producto;
					//var maxid=datax.max_id;
					display_notify(datax.typeinfo,datax.msg);
						
					if (process=="insert"){	
						//setInterval("reload1();", 5000);
						location.href = 'editar_productos.php?id_producto='+id_producto2;		
					}
											
					if (process=="edited"){	
						//setInterval("reload1();", 5000);
						location.href = 'editar_productos.php?id_producto='+id_producto;	
					}			
				}
			});          
}
 function reload1(){
	var id_producto = $('#id_producto').val();
	location.href = 'editar_productos.php?id_producto='+id_producto;	
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
function totales(){
	var cp=parseFloat($("#costo_promedio").val());       
    var pu1=parseFloat($("#porcentaje_utilidad1").val()); 
    var pu2=parseFloat($("#porcentaje_utilidad2").val()); 
    var pu3=parseFloat($("#porcentaje_utilidad3").val()); 
    var pu4=parseFloat($("#porcentaje_utilidad4").val()); 
  
    //Precio = Costo + (Costo * %margen)
	//Precio con descuento: Precio - (costo * %descuento)
	 if (isNaN(cp)==true){
		cp=0; pv1=0;pv2=0;pv3=0;pv4=0;		
	}
		
	
	var pv1 =round((cp+(cp*pu1)/100),2);
    var pv2 =round((cp+(cp*pu2)/100),2);
    var pv3 =round((cp+(cp*pu3)/100),2);
    var pv4 =round((cp+(cp*pu4)/100),2);
    
   
	
    if (isNaN(pu1)==true){
		pv1=0;		
	}
	if (isNaN(pu2)==true){
		pv2=0;		
	}
	if (isNaN(pu3)==true ||isNaN(cp)==true){
		pv3=0;		
	}
	if (isNaN(pu4)==true ||isNaN(cp)==true){
		pv4=0;		
	}
   
	pv1_fin=pv1.toFixed(2);	
   pv2_fin=pv2.toFixed(2);
   pv3_fin=pv3.toFixed(2);		
   pv4_fin=pv4.toFixed(2);	
   
    $("#precio_venta1").val(pv1_fin); 
    $("#precio_venta2").val(pv2_fin); 
    $("#precio_venta3").val(pv3_fin); 
    $("#precio_venta4").val(pv4_fin);  
	
}
$(document).on("blur","#costo_promedio,#porcentaje_utilidad1,#porcentaje_utilidad2,#porcentaje_utilidad3,#porcentaje_utilidad4",function(){
  totales();
})
$(document).on("keyup","#costo_promedio,#porcentaje_utilidad1,#porcentaje_utilidad2,#porcentaje_utilidad3,#porcentaje_utilidad4",function(){

  totales();
})
//Evento que valida el enter a traves del teclado
$(document).on("keydown","#costo_promedio,#porcentaje_utilidad1,#porcentaje_utilidad2,#porcentaje_utilidad3,#porcentaje_utilidad4", function(event){
	var keycode = (event.keyCode ? event.keyCode : event.which);
	if(keycode == '13'){
		totales();
	}
})

//function to round 2 decimal places
function round(value, decimals) {
    return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
}
//Agregar precios
$(document).on('click','#btnPrecios',function(){  
	guardar_precios();
})

function guardar_precios(){	 
	var process='guardar_precios';
	var urlprocess='editar_productos.php';		

	var id_producto=$('#id_product').val();
	var costo_promedio=$("#costo_promedio").val();       
    var stock=$('#stock').val();
    var porcentaje_utilidad1=$('#porcentaje_utilidad1').val();
    var porcentaje_utilidad2=$('#porcentaje_utilidad2').val();
    var porcentaje_utilidad3=$('#porcentaje_utilidad3').val();
    var porcentaje_utilidad4=$('#porcentaje_utilidad4').val();
    var existencias_min=$('#existencias_min').val();
    var id_sucursal=$('#id_sucursal').val();

	var dataString='process='+process+'&id_producto='+id_producto+'&costo_promedio='+costo_promedio;
	dataString+='&porcentaje_utilidad1='+porcentaje_utilidad1+'&porcentaje_utilidad2='+porcentaje_utilidad2;
	dataString+='&porcentaje_utilidad3='+porcentaje_utilidad3+'&porcentaje_utilidad4='+porcentaje_utilidad4;
	dataString+='&stock='+stock+'&existencias_min='+existencias_min;
	$.ajax({
		type:'POST',
		url:urlprocess,
		data: dataString,			
		dataType: 'json',
		success: function(datax){	
				process=datax.process;
				id_producto2=datax.id_producto;
				display_notify(datax.typeinfo,datax.msg);
				setInterval("reload1();", 3000);
		}
	});
          
}
