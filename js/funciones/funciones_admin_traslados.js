$(document).ready(function() {
    $('#categoria').select2();
    $('#id_proveedor').select2();  
});
$(function (){
	//binding event click for button in modal form
	$(document).on("click", "#btnAnular", function(event) {
		anular();
	});
		$(document).on("click", "#btnDelete", function(event) {
		deleted();
	});
	//Reimprimir traslado
	$(document).on("click", "#btnPrint", function(event) {
		print();
	});
	// Clean the modal form
	$(document).on('hidden.bs.modal', function(e) {
		var target = $(e.target);
		target.removeData('bs.modal').find(".modal-content").html('');
	});
	
});	

function reload1(){
	location.href = 'admin_traslados_recibidos_nofin.php';	
}

function anular() {
	var id_traslado = $('#id_traslado').val();
	var dataString = 'process=anular' + '&id_traslado=' + id_traslado;
	$.ajax({
		type : "POST",
		url : "anular_traslado.php",
		data : dataString,
		dataType : 'json',
		success : function(datax) {
			display_notify(datax.typeinfo, datax.msg);
			setInterval("location.reload();", 3000);
			$('#deleteModal').hide(); 
		}
	});
}

function deleted() {
	var id_traslado = $('#id_traslado').val();
	var dataString = 'process=deleted' + '&id_traslado=' + id_traslado;
	$.ajax({
		type : "POST",
		url : "borrar_traslado.php",
		data : dataString,
		dataType : 'json',
		success : function(datax) {
			display_notify(datax.typeinfo, datax.msg);
			setInterval("location.reload();", 3000);
			$('#deleteModal').hide(); 
		}
	});
}

function print() {
	var id_traslado = $('#id_traslado').val();
	var dataString = 'process=reimprimir' + '&id_traslado=' + id_traslado;
	$.ajax({
		type : "POST",
		url : "reimprimir_traslado.php",
		data : dataString,
		dataType : 'json',
		success : function(datos) {
			//display_notify(datax.typeinfo, datax.msg);
				sist_ope=datos.sist_ope;
					//esta opcion es para generar recibo en  printer local y validar si es win o linux
					if (sist_ope=='win'){
						$.post("http://localhost:8080/premier/printpos1.php",{datosventa:datos.consignacionr})
					}
					else {
						$.post("http://localhost/premier/printpos1.php",{datosventa:datos.consignacionr})
					}
			 //setInterval("location.reload();", 3000);
			$('#deleteModal').hide(); 
			
		}
	});
}

