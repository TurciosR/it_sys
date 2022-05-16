$(document).ready(function(){
  $('#precio1').numeric({negative:false,decimalPlaces: 2});
  $('#precio2').numeric({negative:false,decimalPlaces: 2});
  $('#precio3').numeric({negative:false,decimalPlaces: 2});
  $('#ultcosto').numeric({negative:false,decimalPlaces: 2});
}); //end document ready
$(document).on('click','#btnPrecios',function(){
      	var tipo='guardar_precios';
      	guardar_precios();
})
function reload1()
{
	location.href = 'admin_producto.php';
}
//function to round 2 decimal places
function round(value, decimals)
{
    return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
}

function guardar_precios()
{
	var process1='guardar_precios';
	var urlprocess='editar_producto2.php';
	var id_producto=$('#id_producto').val();
  	var precio1=$('#precio1').val();
  	var precio2=$('#precio2').val();
  	var precio3=$('#precio3').val();
  	var ultcosto=$('#ultcosto').val();

	if (precio1==undefined || precio1==0 || precio1=="")
	{
		validaprecio=false;
	}
	else
	{
		validaprecio=true;
	}
	if (validaprecio==false)
	{
		typeinfo="Warning";
		msg="Debe ingresar al menos un precio";
		display_notify(typeinfo,msg);
	}
	if(validaprecio==true)
	{
		var dataString='process='+process1+'&id_producto='+id_producto+'&precio1='+precio1+'&precio2='+precio2+'&precio3='+precio3+'&ultcosto='+ultcosto;
		$.ajax({
			type:'POST',
			url:urlprocess,
			data: dataString,
			dataType: 'json',
			success: function(datax)
			{
				process=datax.process;
				id_producto2=datax.id_producto;
				display_notify(datax.typeinfo,datax.msg);
			}
		});
	}
}
