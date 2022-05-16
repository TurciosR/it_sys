$(document).ready(function() {

/*
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
                    utilidad_seleccion: {  
                    required: true,           
                     },  
                 },
                messages: {
				descripcion: "Por favor ingrese la descripcion del producto",
				descripcion: "Por favor ingrese la descripción",
				utilidad_seleccion: "por favor seleccione la utilidad por defecto",

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
 */                   
    
    $('#categoria').select2();
    $('#id_proveedor').select2();
  

	
	
});
$(function (){
	//binding event click for button in modal form
	$(document).on("click", "#btnDelete", function(event) {
		deleted();
	});
	$(document).on("click", "#submit1", function(event) {
		senddata();
	});
	
	// Clean the modal form
	$(document).on('hidden.bs.modal', function(e) {
		var target = $(e.target);
		target.removeData('bs.modal').find(".modal-content").html('');
	});
	
});	


function senddata(){
	/////////////
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
    * */
	//////////////
	
	//var name=$('#name').val();  
    var descripcion=$('#descripcion').val();
    var barcode=$('#barcode').val();
    var unidad=$('#unidad').val();
    var marca=$('#marca').val();
    var color=$('#color').val();
    var embalaje=$('#embalaje').val();
    var presentacion=$('#presentacion').val();
    var porcentaje_utilidad1=$('#porcentaje_utilidad1').val();
    var porcentaje_utilidad2=$('#porcentaje_utilidad2').val();
    var porcentaje_utilidad3=$('#porcentaje_utilidad3').val();
    var porcentaje_utilidad4=$('#porcentaje_utilidad4').val();
    var existencias_min=$('#existencias_min').val();
    var utilidad_seleccion=$('select#utilidad_seleccion option:selected').val();
    var estado=$('#activo:checked').val();
    var utilidad_activa=$('#utilidad_activa:checked').val();
    var id_proveedor=$('#id_proveedor').val();
    var fecha_caducidad=$('#fecha_caducidad').val();
    var id_categoria=$('select#categoria option:selected').val();
    //Get the value from form if edit or insert
	var process=$('#process').val();
	
	if(process=='insert'){
		var id_producto=0;  
		var urlprocess='agregar_producto.php';
	}	 
	if(process=='edited'){
		var id_producto=$('#id_producto').val();
		var urlprocess='editar_producto.php';  
	}
	 if (estado==undefined){
		 estado=0;
	 }
	 else{
		 estado=1;
	 }
	var dataString='process='+process+'&id_producto='+id_producto+'&barcode='+barcode+'&descripcion='+descripcion+'&unidad='+unidad;
	dataString+='&marca='+marca+'&color='+color+'&embalaje='+embalaje+'&presentacion='+presentacion+'&existencias_min='+existencias_min+'&porcentaje_utilidad1='+porcentaje_utilidad1+'&utilidad_activa='+utilidad_activa;
	dataString+='&estado='+estado+'&id_proveedor='+id_proveedor+'&fecha_caducidad='+fecha_caducidad+'&id_categoria='+id_categoria+'&porcentaje_utilidad2='+porcentaje_utilidad2+'&porcentaje_utilidad3='+porcentaje_utilidad3+'&porcentaje_utilidad4='+porcentaje_utilidad4+'&utilidad_seleccion='+utilidad_seleccion;

			$.ajax({
				type:'POST',
				url:urlprocess,
				data: dataString,			
				dataType: 'json',
				success: function(datax){	
					process=datax.process;	
					display_notify(datax.typeinfo,datax.msg);	
					setInterval("reload1();", 5000);			
				}
			});          
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
