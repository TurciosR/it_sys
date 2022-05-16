$(document).ready(function() {

//validar los campos del form
$('#formulario').validate({		
	    rules: {
                    descripcion: {  
                    required: true,           
                     },

                },
                messages: {
				descripcion: "Por favor ingrese la descripcion",
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
    var description=$('#descripcion').val();
  
    //Get the value from form if edit or insert
	var process=$('#process').val();
	
    if(process=='insert'){	
		var id_tipo_empleado=0;
		var urlprocess='agregar_tipo_empleado.php';
	 }
	 
	if(process=='edited'){	
		var id_tipo_empleado=$('#id_tipo_empleado').val(); ;
		var urlprocess='editar_tipo_empleado.php';  
	}

	var dataString='process='+process+'&id_tipo_empleado='+id_tipo_empleado+'&descripcion='+description;
	//alert(dataString);
			$.ajax({
				type:'POST',
				url:urlprocess,
				data: dataString,			
				dataType: 'json',
				success: function(datax){	
					process=datax.process;
					
						//var maxid=datax.max_id;
						display_notify(datax.typeinfo,datax.msg);	
						setInterval("reload1();", 5000);					
						
					   	       
				}
			});          
}

function reload1(){
     location.href = 'admin_tipo_empleado.php';	
}
function deleted() {
	var id_tipo_empleado = $('#id_tipo_empleado').val();
	var dataString = 'process=deleted' + '&id_tipo_empleado=' + id_tipo_empleado;
	$.ajax({
		type : "POST",
		url : "borrar_tipo_empleado.php",
		data : dataString,
		dataType : 'json',
		success : function(datax) {
			display_notify(datax.typeinfo, datax.msg);
			setInterval("location.reload();", 3000);
			$('#deleteModal').hide(); 
		}
	});
}
