$(document).ready(function() {

$('#formulario').validate({		
	    rules: {
                    nombre: {  
                    required: true,
                            
                     }, 
                    usuario: {  
                    required: true,           
                     }, 
                    clave: {  
                    required: true,        
                     },
                     id_sucursal: {  
                    required: true,        
                     },  
                 },
                messages: {
				nombre: "Por favor ingrese el nombre del usuario",
				usuario: "Por favor ingrese el usuario",
				clave: "Por favor ingrese la clave",
				id_sucursal: "Por favor seleccione la sucursal",
				},

        submitHandler: function (form) { 
            senddata();
        }
    });
                    
    
    $('#id_sucursal').select2();
  
	//datepicker active
	$( ".datepick" ).datepicker(); 
	
	//$('#div_modules_edit').show(); //csmbioo de hide ashow
	
	$('#admin').on('ifChecked', function(event){
		//$('#div_modules_edit').hide(); 
		$('.i-checks').iCheck('check');
	});
	$('#admin').on('ifUnchecked', function(event){
		//$('#div_modules_edit').show(); 
		$('.i-checks').iCheck('uncheck');
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

function autosave(val){
	var name=$('#name').val(); 
	if (name==''|| name.length == 0){
		var	typeinfo="Info";
		var msg="The field name is required";
		display_notify(typeinfo,msg);
		$('#name').focus();
	}
	else{
		senddata();
	}	
}	

function senddata(){
    var nombre=$('#nombre').val();
    var usuario=$('#usuario').val();
    var clave=$('#clave').val();
    var admin=$('#admin:checked').val();
    var id_sucursal=$('#id_sucursal').val();
    //Get the value from form if edit or insert
	var process=$('#process').val();
	if (admin==undefined){
		admin=0;
	 }
	 else{
		 admin=1;
	 }
	if(process=='insert'){
		var id_usuario=0;  
		var urlprocess='agregar_usuarios.php';
		var dataString='process='+process+'&nombre='+nombre+'&usuario='+usuario+'&clave='+clave+'&admin='+admin+'&id_usuario='+id_usuario+'&id_sucursal='+id_sucursal;
	}	 
	if(process=='edited'){
		var id_usuario=$('#id_usuario').val();
		var urlprocess='editar_usuarios.php';  
		var dataString='process='+process+'&nombre='+nombre+'&usuario='+usuario+'&clave='+clave+'&admin='+admin+'&id_usuario='+id_usuario+'&id_sucursal='+id_sucursal;
	}
	if(process=='permissions'){
		var id_usuario=$('#id_usuario').val();
		var urlprocess='permiso_usuario.php';  
		var myCheckboxes = new Array();
        var cuantos=0;
		var chequeado=false;
        $("input[name='myCheckboxes']:checked").each(function(index) {
			var est=$('#myCheckboxes').eq(index).attr('checked');
			chequeado=true;
            myCheckboxes.push($(this).val());
            cuantos=cuantos+1;
		});   
		if (cuantos==0){
			myCheckboxes='0';
		}
		var dataString='process='+process+'&admin='+admin+'&id_usuario='+id_usuario+'&myCheckboxes='+myCheckboxes+'&qty='+cuantos;  
		//alert(dataString);
	}
	
	
	

			$.ajax({
				type:'POST',
				url:urlprocess,
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
function reload1(){
   location.href = 'admin_empleado.php';	
}
function deleted() {
	var id_usuario = $('#id_usuario').val();
	var dataString = 'process=deleted' + '&id_usuario=' + id_usuario;
	$.ajax({
		type : "POST",
		url : "borrar_usuario.php",
		data : dataString,
		dataType : 'json',
		success : function(datax) {
			display_notify(datax.typeinfo, datax.msg);
			setInterval("reload1();", 5000);
			$('#deleteModal').hide(); 
		}
	});
}

