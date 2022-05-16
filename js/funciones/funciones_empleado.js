$(document).ready(function() {

$("#usuario_sis").on("ifChecked", function () {
	$("#caja_user").attr('hidden', false);
	$("#caja_pass").attr('hidden', false);
	$("#caja_admin").attr("hidden",false);
	$("#usuario_sistema").val(1);

});
$("#usuario_sis").on("ifUnchecked", function () {
	//alert($(this).val());
	$("#caja_user").attr('hidden', true);
	$("#caja_pass").attr('hidden', true);
	$("#caja_admin").attr("hidden",true);
	$("#usuario").val("");
	$("#pass").val("");
	$("#admin_sis").iCheck('uncheck');
	$("#admin_sistema").val(0);
	$("#usuario_sistema").val(0);
	//alert("ok");
});

$("#admin_sis").on("ifChecked",function()
{
	$("#admin_sistema").val(1);
});
$("#admin_sis").on("ifUnchecked", function()
{
	$("#admin_sistema").val(0);
});

$("#vendedor").on("ifChecked",function()
{
	$("#vendedor1").val(1);
});
$("#vendedor").on("ifUnchecked", function()
{
	$("#vendedor1").val(0);
});

if($("#usuario_sistema").val() == 1)
{
	$("#caja_user").attr('hidden', false);
	$("#caja_pass").attr('hidden', false);
	$("#caja_admin").attr("hidden",false);
	$("#usuario_sistema").val(1);
}
else if($("#usuario_sistema").val() == 0)
{
	$("#caja_user").attr('hidden', true);
	$("#caja_pass").attr('hidden', true);
	$("#caja_admin").attr("hidden",true);
	$("#usuario_sistema").val(0);
	$("#usuario").val("");
	$("#pass").val("");
	$("#admin_sis").iCheck('uncheck');
	$("#admin_sistema").val(0);
}

$('#formulario').validate({
	    rules: {
                    nombre:
                    {
                    	required: true,
                    },
                    sexo:
                    {
                    	required: true
                    },
                    fecha_nace:
                    {
                    	required: true
                    },
                    nit:
                    {
                    	required: true
                    },
                    dui:
                    {
                    	required: true
                    },
                    direccion:
                    {
                    	required: true
                    },
                    departamento:
                    {
                    	required: true
                    },
                    municipio:
                    {
                    	required: true
                    },
                    telefono1:
                    {
                    	required: true
                    },
                    salariobase:
                    {
                    	required: true
                    },
                    /*n_isss:
                    {
                    	required: true
                    },
                    n_afp:
                    {
                    	required: true
                    },*/
                    sucursal:
                    {
                    	required: true
                    },
                    /*n_cuenta:
                    {
                    	required: true
                    }*/
                 },
                messages: {
				nombre: "Por favor ingrese el nombre",
				sexo: "Debe ingresar el sexo",
				fecha_nace: "Ingrese la fecha de nacimiento",
				nit: "Ingrese el NIT del empleado",
				dui: "Ingrese el DUI del empleado",
				direccion: "Debe de ingresar una direccion del empleado",
				departamento: "Ingrese un departamento",
				municipio: "Ingrese un municipio",
				telefono1: "Ingrese el numero de telefono",
				salariobase: "Ingrese un salario",
				/*n_isss: "Ingrese el numero de ISSS",
				n_afp: "Ingrese el numero de AFP",*/
				sucursal: "Ingrese la sucursal",
				/*n_cuenta: "Debe de ingresar un numero de cuenta",*/
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


    $('#tipo_empleado').select2();

	//datepicker active
	$("#fecha_registro").datepicker({
		dateFormat: 'dd/mm/yy',
	});

	$('.select').select2();
$("#departamento").change(function()
{
 	$("#municipio *").remove();
 	$("#select2-municipio-container").text("");
 	var ajaxdata = { "process" : "municipio", "id_departamento": $("#departamento").val() };
    $.ajax({
      	url:"agregar_empleado.php",
      	type: "POST",
      	data: ajaxdata,
      	success: function(opciones)
      	{
			$("#select2-municipio-container").text("Seleccione");
	        $("#municipio").html(opciones);
	        $("#municipio").val("");
    	}
    })
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
	//var id_empleado, nombre, apellido,
	//  nit, dui, direccion, telefono1, telefono2, email, salariobase
    var nombre=$('#nombre').val();
    //var apellido=$('#apellido').val();
    var sexo = $("#sexo").val();
    var fecha_nace = $("#fecha_nace").val();
    var fecha_registro = $("#fecha_registro").val();
    var nit=$('#nit').val();
    var dui=$('#dui').val();
    var direccion=$('#direccion').val();
    var telefono1=$('#telefono1').val();
    var telefono2=$('#telefono2').val();
    var email=$('#email').val();
    var salariobase=$('#salariobase').val();
    var departamento = $("#departamento").val();
    var municipio = $("#municipio").val();
    var banco = $("#banco").val();
    var n_cuenta = $("#n_cuenta").val();
    var cargo = $("#cargo").val();
    var profecion = $("#profecion").val();
    var n_isss = $("#n_isss").val();
    var n_afp = $("#n_afp").val();
    var sucursal = $("#sucursal").val();
    var usuario_sistema = $("#usuario_sistema").val();
    var usuario = $("#usuario").val();
    var pass = $("#pass").val();
    var admin_sistema = $("#admin_sistema").val();
		var vendedor=$("#vendedor1").val();
    //Get the value from form if edit or insert
	var process=$('#process').val();

	if(process=='insert'){
		var id_empleado=0;
		var urlprocess='agregar_empleado.php';
	}
	if(process=='edited'){
		var id_empleado=$('#id_empleado').val();
		var urlprocess='editar_empleado.php';
	}
	var dataString='process='+process+'&id_empleado='+id_empleado+'&nombre='+nombre+'&nit='+nit+'&dui='+dui;
	dataString+='&direccion='+direccion+'&telefono1='+telefono1+'&telefono2='+telefono2+'&email='+email+'&salariobase='+salariobase;
	dataString+='&fecha_nace='+fecha_nace+'&fecha_registro='+fecha_registro+'&departamento='+departamento+'&municipio='+municipio+'&banco='+banco;
	dataString+='&n_cuenta='+n_cuenta+'&cargo='+cargo+'&profecion='+profecion+'&n_isss='+n_isss+'&n_afp='+n_afp+'&sucursal='+sucursal;
	dataString+='&usuario_sistema='+usuario_sistema+'&usuario='+usuario+'&pass='+pass+'&admin_sistema='+admin_sistema+'&sexo='+sexo;
	dataString+='&vendedor='+vendedor;

			$.ajax({
				type:'POST',
				url:urlprocess,
				data: dataString,
				dataType: 'json',
				success: function(datax){
					process=datax.process;
					//var maxid=datax.max_id;
					display_notify(datax.typeinfo,datax.msg);
					if(datax.typeinfo == "Success")
					{
						setInterval("reload1();", 5000);
					}
				}
			});
}

function reload1(){
     location.href = 'admin_empleado.php';
}
function deleted() {
	var id_empleado = $('#id_empleado').val();
	var dataString = 'process=deleted' + '&id_empleado=' + id_empleado;
	$.ajax({
		type : "POST",
		url : "borrar_empleado.php",
		data : dataString,
		dataType : 'json',
		success : function(datax) {
			display_notify(datax.typeinfo, datax.msg);
			setInterval("location.reload();", 3000);
			$('#deleteModal').hide();
		}
	});
}
