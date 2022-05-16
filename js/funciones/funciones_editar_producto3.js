$(document).ready(function(){


	$('.color1').select2();
	$('#letra').select2();

	$("#guardarCaracteristicas").click(function(){
  	senddata();
	});
}); //end document ready

function reload1(){
 location.href = 'admin_producto.php';
}

function senddata() {
  //Obtener los valores a guardar de cada item
  var urlprocess=$('#urlprocess').val();
	var verificar = 'noverificar'
  var id_producto=$('#id_producto').val();
	var estilo=$('#estilo').val();
	var id_color=$('#color1').val();
	var talla=$('#talla').val();
	var numera=$('#numeracion').val();

  var  dataString='process=insert'+ '&id_producto=' + id_producto + '&estilo=' + estilo + '&id_color=' + id_color ;
   dataString+= '&talla=' + talla+ '&numera=' +numera;
  if (verificar == 'noverificar') {
  	$.ajax({
    type: 'POST',
    url: 'editar_producto3.php',
    data: dataString,
    encoding:"UTF-8",
    dataType: 'json',
    success: function(datax){
				id_producto=datax.id_producto;
				$('#id_producto').val(id_producto);
      	display_notify(datax.typeinfo,datax.msg);
				setInterval("location.reload();", 1500);
    	}
  	});
	}
}
