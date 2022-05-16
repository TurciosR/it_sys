$(document).ready(function() {

//validar los campos del form
$('#formulario').validate({		
	    rules: {
                    nombre: {  
                    required: true,           
                     }, 
                 },
                messages: {
				nombre: "Por favor ingrese el nombre de la sucursal",
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
	var nombre=$('#nombre').val();  
    var direccion=$('#direccion').val();
     var casa=$('#casa:checked').val();
  
    //Get the value from form if edit or insert
	var process=$('#process').val();
	
    if(process=='insert'){	
		var id_sucursal=0;
		var urlprocess='agregar_sucursal.php';
	 }
	 
	if(process=='edited'){	
		var id_sucursal=$('#id_sucursal').val(); ;
		var urlprocess='editar_sucursal.php';  
	}

	var dataString='process='+process+'&id_sucursal='+id_sucursal+'&nombre='+nombre+'&direccion='+direccion+'&casa='+casa;
	//alert(dataString);
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
	location.href = 'admin_sucursal.php';	
}
function deleted() {
	var id_sucursal = $('#id_sucursal').val();
	var dataString = 'process=deleted' + '&id_sucursal=' + id_sucursal;
	$.ajax({
		type : "POST",
		url : "borrar_sucursal.php",
		data : dataString,
		dataType : 'json',
		success : function(datax) {
			display_notify(datax.typeinfo, datax.msg);
			setInterval("location.reload();", 3000);
			$('#deleteModal').hide(); 
		}
	});
}
