$(document).ready(function() {
  $('#cat_servicios').select2();
	//datepicker active
	$( ".datepick" ).datepicker();
	$(".numeric").numeric(
		{
			negative: false,
		}
	);
});
$(document).on("click", "#submit1", function()
{
	senddata();
})
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
	//var name=$('#name').val();
  var id_categoria=$('#cat_servicios').val();
  var descripcion=$('#descripcion').val();
  var costo=$('#costo').val();
  var precio=$('#precio').val();
  var estado=$('#activo:checked').val();
  //Get the value from form if edit or insert
	var process=$('#process').val();

	if(process=='insert'){
		var id_servicio=0;
		var urlprocess='registrar_servicios.php';
	}
	if(process=='edited'){
		var id_servicio=$('#id_servicio').val();
		var urlprocess='editar_servicios.php';
	}

	var array_json = new Array();
  $("#plus_detalle tr").each(function(index) {
    if (index >= 0)
		{
        var item = $(this).find(".item").val();
        var obj = new Object();
        obj.item = item;
        //convert object to json string
        text=JSON.stringify(obj);
        array_json.push(text);
        //i = i + 1;
    }
  });

  json_arr = '['+array_json+']';
  console.log('jsons:'+json_arr);

	var dataString='process='+process+'&id_servicio='+id_servicio+'&descripcion='+descripcion+'&costo='+costo+'&precio='+precio;
	dataString+='&estado='+estado+'&id_categoria='+id_categoria+"&array_data="+json_arr;
			$.ajax({
				type:'POST',
				url:urlprocess,
				data: dataString,
				dataType: 'json',
				success: function(datax){
					process=datax.process;
					//var maxid=datax.max_id;
					display_notify(datax.typeinfo,datax.msg);
					setInterval("reload1();", 1500);
				}
			});
}
function reload1()
{
     location.href = 'admin_servicio.php';
}
function deleted() {
	var id_servicio = $('#id_servicio').val();
	var dataString = 'process=deleted' + '&id_servicio=' + id_servicio;
	$.ajax({
		type : "POST",
		url : "borrar_servicio.php",
		data : dataString,
		dataType : 'json',
		success : function(datax) {
			display_notify(datax.typeinfo, datax.msg);
			setInterval("reload1();", 1500);
			$('#deleteModal').hide();
		}
	});
}

$(document).on("click", "#plus_item", function()
{
	var linea = "<tr>";
	linea += "<td><input id='item' name='item' class='form-control item'></td>";
	linea += '<td class="Delete"><input  id="delprod" type="button" class="btn btn-danger fa pull-right"  value="&#xf1f8;"></td>';
	linea += "</tr>";
	$("#plus_detalle").append(linea);
});

$(document).on("click", ".Delete", function() {
  var parent = $(this).parents().get(0);
  $(parent).remove();
});
