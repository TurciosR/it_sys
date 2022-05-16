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
	//Reimprimir pedido
	$(document).on("click", "#btnPrint", function(event) {
		print();
	});
	// Clean the modal form
	$(document).on('hidden.bs.modal', function(e) {
		var target = $(e.target);
		target.removeData('bs.modal').find(".modal-content").html('');
	});
	//convertir pedido a consignacion
	$(document).on("click", "#btnConvertir", function(event) {
		convertir();
	});
	
});	

function reload1(){
	location.href = 'admin_pedido_nofin.php';	
}

function anular() {
	var id_pedido = $('#id_pedido').val();
	var dataString = 'process=anular' + '&id_pedido=' + id_pedido;
	$.ajax({
		type : "POST",
		url : "anular_pedido.php",
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
	var id_pedido = $('#id_pedido').val();
	var dataString = 'process=deleted' + '&id_pedido=' + id_pedido;
	$.ajax({
		type : "POST",
		url : "borrar_pedido.php",
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
	var id_pedido = $('#id_pedido').val();
	var dataString = 'process=reimprimir' + '&id_pedido=' + id_pedido;
	$.ajax({
		type : "POST",
		url : "reimprimir_pedido.php",
		data : dataString,
		dataType : 'json',
		success : function(datos) {
			//display_notify(datax.typeinfo, datax.msg);
				sist_ope=datos.sist_ope;
					//esta opcion es para generar recibo en  printer local y validar si es win o linux
					if (sist_ope=='win'){
						$.post("http://localhost:8080/premier/printpos1.php",{datosventa:datos.pedidor})
					}
					else {
						$.post("http://localhost/premier/printpos1.php",{datosventa:datos.pedidor})
					}
			 //setInterval("location.reload();", 3000);
			$('#deleteModal').hide(); 
			
		}
	});
}

//convertir pedido a Consignacion
function convertir() {
	var id_pedido = $('#id_pedido').val();
	var dataString = 'process=convertir' + '&id_pedido=' + id_pedido;
	$.ajax({
		type : "POST",
		url : "pedido_para_consigna.php",
		data : dataString,
		dataType : 'json',
		success : function(datax) {
			display_notify(datax.typeinfo, datax.msg);
			setInterval("location.reload();", 3000);
			$('#deleteModal').hide(); 
		}
	});
}
