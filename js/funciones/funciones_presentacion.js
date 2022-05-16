$(document).ready(function() {

//validar los campos del form
$('#formulario').validate({
	    rules: {
                    nombre: {
                    required: true,
                     },
                 },
                messages: {
				nombre: "Por favor ingrese el nombre de la presentacion",
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
//Agregar y editar
$(document).on('click','#btnGuardarPresenta',function(){
	guardar_presentacion('agregar_presentacion');
});

$(document).on('click','#btnEditarPresenta',function(){
	guardar_presentacion('editar_presentacion');
});
//Anular
$(document).on('click','#btnAnularPresenta',function(){
	guardar_presentacion('anular_presentacion');
});

function guardar_presentacion(tipo){
	var process=tipo;
	var id_presentacion = $('#id_presentacion').val();
	switch (process){
		case 'agregar_presentacion':
			var urlprocess='agregar_presentacion.php';
			var id_presentacion=0;
			break;
        case 'editar_presentacion':
			var urlprocess='editar_presentacion.php';

            break;
        case 'anular_presentacion':
			var urlprocess='borrar_presentacion.php';
            break;
	}

  var descrip_corta=$('#descrip_corta').val();
  var descripcion=$('#descripcion').val();

	var dataString='process='+process+'&id_presentacion='+id_presentacion+'&descripcion='+descripcion+'&descrip_corta='+descrip_corta;
	//alert(dataString);
			$.ajax({
				type:'POST',
				url:urlprocess,
				data: dataString,
				dataType: 'json',
				success: function(datax){
					process=datax.process;
					display_notify(datax.typeinfo,datax.msg);
					setInterval("reload1();", 500);

				}
			});
}

function reload1(){
	location.href = 'admin_presentacion.php';
}
function deleted() {
	var id_presentacion = $('#id_presentacion').val();
	var dataString = 'process=deleted' + '&id_presentacion=' + id_presentacion;
	$.ajax({
		type : "POST",
		url : "borrar_presentacion.php",
		data : dataString,
		dataType : 'json',
		success : function(datax) {
			display_notify(datax.typeinfo, datax.msg);
			setInterval("location.reload();", 500);
			$('#deleteModal').hide();
		}
	});
}
