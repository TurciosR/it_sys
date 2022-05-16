$(document).ready(function(){
$(".decimal").numeric();
$(".numeric").numeric();
$(document).on('keyup keypress', '.decimal_positive', function()
{
  var aValue = $(this).val();
  if($.isNumeric(aValue) === false){
    $(this).val(aValue.slice(0,-1));
  }
});
$(document).on('keyup keypress', '.integer_positive', function(e)
{
  if (/\D/g.test(this.value))
  {
    // Filter non-digits from input value.
    this.value = this.value.replace(/\D/g, '');
  }
});
$('.select2').select2({
	placeholder: {
    id: '-1', // the value of the option
    text: 'Seleccione'
  },
    allowClear: true
});
//validar los campos del form
$('#formulario1').validate({
	    rules: {
                    descripcion: {
                    required: true,
                     },
                 },
                messages: {
				descripcion: "Por favor ingrese Nombre",
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
	$("#fianly").click(function(){
		reload1();
	});

	 $(".i-checks").iCheck({
     checkboxClass: "icheckbox_square-green",
         radioClass: "iradio_square-green",
      });

}); //end document ready

function reload1(){
	location.href = 'admin_producto.php';
}
function senddata(){
    var descripcion=$('#descripcion').val();
    var barcode=$('#barcode').val();
    var unidad=$('#unidad').val();
    var marca=$('#marca').val();
    var color=$('#color').val();
    var embalaje=$('#embalaje').val();
    var id_presentacion=$('#id_presentacion').val();
    var porcentaje_utilidad1=$('#porcentaje_utilidad1').val();
    var porcentaje_utilidad2=$('#porcentaje_utilidad2').val();
    var porcentaje_utilidad3=$('#porcentaje_utilidad3').val();
    var porcentaje_utilidad4=$('#porcentaje_utilidad4').val();
    var existencias_min=$('#existencias_min').val();
    var estado=$('#activo:checked').val();
    var combo=$('#combo:checked').val();
 		//stock
    var stock=$('#stock').val();
    var costo_promedio=$('#costo_promedio').val();
    var id_descripcion=$('#id_descripcion').val();
    var id_categoria=$('select#categoria option:selected').val();

    //Get the value from form if edit or insert
	var process=$('#process').val();
	var perecedero=$('#perecedero:checked').val();
  var fecha_vence=$('#fecha_vence1').val();

	urlprocess=$('#urlprocess').val();
	if(process=='insert'){
		var id_producto=0;
		var urlprocess='agregar_producto.php';
	}
	if(process=='edited'){
		var id_producto=$('#id_producto').val();
		var urlprocess='editar_producto1.php';
	}


	 if (estado==undefined){
		 estado=0;
	 }
	 else{
		 estado=1;
	 }

	  if (stock==undefined){
		 stock=0;
	 }
	  if (costo_promedio==undefined){
		 costo_promedio=0;
	 }
	 if (fecha_vence==undefined){
	  fecha_vence="NULL";
	 }
	 if (perecedero==0){
	  fecha_vence="NULL";
	}

  // var  dataString=$("#formulario1").serialize();

  var dataString='process='+process+'&id_producto='+id_producto+'&barcode='+barcode+'&descripcion='+descripcion+'&unidad='+unidad;
	dataString+='&marca='+marca+'&color='+color+'&embalaje='+embalaje+'&id_presentacion='+id_presentacion;
	dataString+='&estado='+estado+'&id_descripcion='+id_descripcion+'&id_categoria='+id_categoria+'&combo='+combo+'&perecedero='+perecedero;
	dataString+='&costo_promedio='+costo_promedio+'&stock='+stock+'&fecha_vence='+fecha_vence;
	$.ajax({
				type:'POST',
				url:urlprocess,
				data: dataString,
				dataType: 'json',
				success: function(datax){
					process=datax.process;
					id_producto2=datax.id_producto;
					//var maxid=datax.max_id;
					display_notify(datax.typeinfo,datax.msg);

					if (process=="insert"){
						//setInterval("reload1();", 500);
						location.href = 'editar_producto.php?id_producto='+id_producto2;
					}
					if (process=="edited"){
						//setInterval("reload1();", 500);
					}
				}
			});
}
