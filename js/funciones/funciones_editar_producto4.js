$(document).ready(function(){


  $('.select').select2({
    placeholder: {
      id: '', // the value of the option
      text: 'Seleccione'
    },
    allowClear: true
  });
    //evento change para select dependientes
  	$("#select_almacen").change(function(event){
  		cambio_estantes();
  		//alert( $('#select_almacen :selected').val())
  	});

  	$("#select_estante").change(function(event){
  		cambio_ubicacion();
  		//alert( $('#select_estante :selected').val())
  	});

}); //end document ready

function reload1(){
	location.href = 'admin_producto.php';
}
function cambio_estantes(){
	var id = $('#select_almacen').val();
	$("#select_estante").empty().trigger('change')
	$.post("editar_producto4.php", {process:'genera_select',id:id }, function(data){
					 $("#select_estante").html(data);
	 });
}
function cambio_ubicacion(){
	var id1 = $('#select_almacen :selected').val();
	var id2 = $('#select_estante :selected').val();
	$("#select_ubicacion").empty().trigger('change')
	$.post("editar_producto4.php", {process:'genera_selectt',id1:id1,id2:id2}, function(data){
					 $("#select_ubicacion").html(data);
	 });

}

$(document).on('click','#btn',function(){
	var  tipo='guardar'
	guardar();
})

function guardar(){
	var process='guardar';
	var urlprocess='editar_producto4.php';
  var id_posicion=$('#select_ubicacion').val();
  var id_producto=$('#id_producto').val();

	if (process=='guardar'){
		if (id_posicion==undefined ||id_posicion==0 ||id_posicion==""){
			valida_ubicacion=false;
		}
		else{
			valida_ubicacion=true;
		}
		if (valida_ubicacion==false ){
			typeinfo="Warning";
			msg="Debe seleccionar una posicion";
			display_notify(typeinfo,msg);
		}
	}
	else{
		valida_ubicacion=true;
	}

if (valida_ubicacion==true ){
	if(process=='guardar'){
	var dataString='process='+process+'&id_producto='+id_producto+'&id_posicion='+id_posicion;
	}
	//alert(dataString);
			$.ajax({
				type:'POST',
				url:urlprocess,
				data: dataString,
				dataType: 'json',
				success: function(datax){
					process=datax.process;
					display_notify(datax.typeinfo,datax.msg);
  				setInterval("location.reload();", 1500);
				}
			});
    }
}
