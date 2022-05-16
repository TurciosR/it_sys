$(document).ready(function() {

$('#formulario').validate({
rules: {
    header1: 
    {
    	required: true,
    },
    header2: 
    {
        required: true,
    },
    header3: 
    {
        required: true,
    },
    header4: 
    {
        required: true,
    },
    header5: 
    {
        required: true,
    },
    header6: 
    {
        required: true,
    },
    header7: 
    {
        required: true,
    },
},
messages: {
    s_p_m: "Por favor ingrese Shared Pinter Matrix",
    s_p_p: "Por favor ingrese Shared Pinter Pos",
    s_p_b: "Por favor ingrese Shared Pinter Barcode",
    d_p_s: "Por favor ingrese Dir Print Script",
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
        url         : 'editar_post.php',
        cache       : false,
        data        : formdata ? formdata : form.serialize(),
        contentType : false,
        processData : false,
        dataType : 'json',	
        success: function(data)
        {  
		    display_notify(data.typeinfo,data.msg,data.process);
            if(data.typeinfo == "Success")
            {
                setInterval("reload1();", 1500);
            }
	    }
    });   
}
function reload1(){
     location.href = 'admin_cabecera.php';
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
