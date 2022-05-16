$(document).ready(function()
{

	$('#formulario').validate({
	    rules: {
	        select_almacen: {
	        	required: true,
	        },
					descripcion: {
					 required: true,
				 },
	     },
	    messages: {
	        select_almacen: "Seleccione un Almacen",
					descripcion: "Ingrese la descripcion",
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

$(".select").select2();/*select2 normal*/

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
	var select_almacen = $('#select_almacen').val();
	var descripcion = $('#descripcion').val();

	var process=$('#process').val();

	if(process=='insert')
	{
		var urlprocess='agregar_estante.php';
		var dataString='process='+process+'&id_almacen='+select_almacen+'&descripcion='+descripcion;
	}
	if(process=='edited')
	{
		var id_estante =$('#id_estante').val();
		var urlprocess='editar_estante.php';
		var dataString='process='+process+'&id_estante='+id_estante+'&id_almacen='+select_almacen+'&descripcion='+descripcion;
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
     location.href = 'admin_estante.php';
}
function cambio_estantes(){
	var id = $('#select_almacen').val();
	$("#select_estante").empty().trigger('change')
	$.post("agregar_estante.php", {process:'genera_select',id:id }, function(data){
					 $("#select_estante").html(data);
	 });
   $('.select').select2({
 		placeholder: {
     	id: '', // the value of the option
     	text: 'Seleccione'
   	}
 	});
}
function deleted()
{
	var id_estante = $('#id_estante').val();
	var dataString = 'process=deleted' + '&id_estante=' + id_estante;
	$.ajax({
		type : "POST",
		url : "borrar_estante.php",
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
