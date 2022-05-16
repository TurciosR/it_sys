$(document).ready(function()
{
	$('#formulario').validate({
	    rules: {

					descripcion: {
					 required: true,
				 },
	     },
	    messages: {
	        descripcion: "Ingrese el nombre del almacen",
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
	var descripcion = $('#descripcion').val();

	var process=$('#process').val();

	if(process=='insert')
	{
		var urlprocess='agregar_almacen.php';
		var dataString='process='+process+'&descripcion='+descripcion;
	}
	if(process=='edited')
	{
		var id_almacen =$('#id_almacen').val();
		var urlprocess='editar_almacen.php';
		var dataString='process='+process+'&id_almacen='+id_almacen+'&descripcion='+descripcion;
	}
	$.ajax({
		type:'POST',
		url:urlprocess,
		data: dataString,
		dataType: 'json',
		success: function(datax)
		{
			display_notify(datax.typeinfo,datax.msg);
			if(datax.typeinfo == "Success")
			{
				setInterval("reload1();", 1000);
			}
		}
	});
}
function reload1()
{
     location.href = 'admin_almacen.php';
}
function deleted()
{
	var id_almacen = $('#id_almacen').val();
	var dataString = 'process=deleted' + '&id_almacen=' + id_almacen;
	$.ajax({
		type : "POST",
		url : "borrar_almacen.php",
		data : dataString,
		dataType : 'json',
		success : function(datax)
		{
			display_notify(datax.typeinfo, datax.msg);
			if(datax.typeinfo == "Success")
			{
				setInterval("location.reload();", 1000);
				$('#deleteModal').hide();
			}
		}
	});
}
