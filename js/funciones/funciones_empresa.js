$(document).ready(function() {

$("#logo").fileinput({'showUpload':true, 'previewFileType':'image'});
$("#logo").attr('disabled',true);
if($("#id_empresa").val()>0)
{
    $("#submit1").hide();
}
else
{
    $("#btn_edit").hide();
}

$("#btn_edit").click(function(){
    $(this).hide();
    $(".dis").attr("readonly", false);
    $("#submit1").show();
    $(".select").select2({disabled: false});
    $("#logo").attr('disabled',false);
});
$('#formulario').validate({
	    rules: {
                    empresa: 
                    {
                    	required: true,
                    },
                    iva: 
                    {
	                    required: true,
	                    number: true,
                    },
                    razon:
                    {
                    	required: true,
                    },
                    direccion:
                    {
                    	required: true,
                    },
                    telefono1:
                    {
                    	required: true,
                    },
                    nit:
                    {
                    	required: true,
                    },
                    nrc:
                    {
                    	required: true,
                    },
                    giro:
                    {
                    	required: true,
                    },
                    monto_retencion1:
                    {
                    	required: true,
                    },
                    monto_retencion10:
                    {
                    	required: true,
                    },
                    monto_percepcion:
                    {
                    	required: true,
                    }
                 },
                messages: {
				empresa: "Por favor ingrese el nombre de la empresa",
				iva: {
					required: "Por favor ingrese el valor del iva",
					number: "Este campo solo puede tener números"
				},
				razon: "Ingrese una razon social",
				direccion: "Ingrese la direccion de la empresa",
				telefono1: "Ingrese un numero de telefono",
				nit: "Ingrese el NIT de la empresa",
				nrc: "Ingrese el NRC de la empresa",
				giro: "Ingrese el giro de la empresa",
				monto_retencion1: "Ingrese el monto inicial de retencion del 1%",
				monto_retencion10: "Ingrese el monto inicial de retencion del 10%",
				monto_percepcion: "Ingrese el monto inicial de percepcion",
				/*
				password: {
					required: "Por favor ingrese su password",
					minlength: "Su password debe de tener como minimo 5 caracteres"
				*/
				},

        submitHandler: function (form) {
            senddata();
        }
    });


    $('#cat_servicios').select2();

	//datepicker active
	$( ".datepick" ).datepicker();
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
/*
function senddata(){
	//var name=$('#name').val();

    var empresa=$('#empresa').val();
    var razon=$('#razon').val();
	var giro=$('#giro').val();
    var direccion=$('#direccion').val();
    var telefono1=$('#telefono1').val();
    var telefono2=$('#telefono2').val();
    var nit=$('#nit').val();
	var nrc=$('#nrc').val();
    var iva=$('#iva').val();
    var monto_retencion1 = $("#monto_retencion1").val();
    var monto_retencion10 = $("#monto_retencion10").val();
    var monto_percepcion = $("#monto_percepcion").val();

    //Get the value from form if edit or insert
	var process=$('#process').val();

	if(process=='insert'){
		var id_empresa=0;
		var urlprocess='agregar_empresa.php';
	}
	if(process=='edited'){
		var id_empresa=$('#id_empresa').val();
		var urlprocess='editar_empresa.php';
	}
	var dataString='process='+process+'&id_empresa='+id_empresa+'&empresa='+empresa+'&razon='+razon+'&direccion='+direccion+'&telefono1='+telefono1;
	dataString+='&telefono2='+telefono2+'&nit='+nit+'&iva='+iva+'&nrc='+nrc+'&giro='+giro+"&monto_retencion1="+monto_retencion1+"&monto_retencion10="+monto_retencion10+"&monto_percepcion="+monto_percepcion;

	$.ajax({
		type:'POST',
		url:urlprocess,
		data: dataString,
		dataType: 'json',
		success: function(datax){
			process=datax.process;
			//var maxid=datax.max_id;
			display_notify(datax.typeinfo,datax.msg);
			setInterval("reload1();", 5000);
		}
	});
}*/
function senddata()
{
    var form = $("#formulario");
    var formdata = false;
    if(window.FormData)
    {
        formdata = new FormData(form[0]);
    }
    var formAction = form.attr('action');
    $.ajax({
        type        : 'POST',
        url         : 'admin_empresa.php',
        cache       : false,
        data        : formdata ? formdata : form.serialize(),
        contentType : false,
        processData : false,
        dataType : 'json',	
        success: function(data)
        {  
		    display_notify(data.typeinfo,data.msg,data.process);
            $("#submit1").hide();
            $("#btn_edit").show();
            $(".dis").attr("readonly", true);
            $(".select").select2({disabled: true});
            $("#logo").attr('disabled',true);
	    }
    });   
}
function reload1(){
     location.href = 'admin_empresa.php';
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
			setInterval("reload1();", 5000);
			$('#deleteModal').hide();
		}
	});
}
