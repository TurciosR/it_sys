$(document).ready(function() {
  $('#valor').numeric({
    negative: false,
    decimalPlaces: 4
  });

  $("#proceso").change(function()
  {
	alert("hola");
	//$("#n_doc").hide();
	//var tipo_mo = $("#tipo_movimiento").val();
	})
});

function proceso_d(){
	var tipo_mo=$("#proceso").val();

	if(tipo_mo=="1"){
		$("#div_docu").hide();
		$("#n_doc").val("0000");
	}else{
		$("#div_docu").show();
		$("#n_doc").val("");
	}
}
	$(function (){
		//binding event click for button in modal form
		$(document).on("click", "#btnIngreso", function(event) {
			agregar_ingreso();
		});
		$(document).on("click", "#btnSalida", function(event) {
			agregar_salida();
		});
		$(document).on("click", "#btnViatico", function(event) {
			agregar_viatico();
		});
		$(document).on("click", "#btnEditar", function(event) {
			editar_movimiento();
		});
		$(document).on("click", "#btnEditar_s", function(event) {
			editar_movimiento_s();
		});
		$(document).on("click", "#btnEditar_v", function(event) {
			editar_movimiento_v();
		});
		$(document).on("click", "#btnEliminar", function(event) {
			deleted();
		});
		$(document).on("click", "#btnReimprimir", function(event) {
			reimprimir();
		});
		// Clean the modal form
		$(document).on('hidden.bs.modal', function(e) {
			var target = $(e.target);
			target.removeData('bs.modal').find(".modal-content").html('');
		});

	});

	 function reload1(){
		location.href = 'admin_caja_chica.php';
	}
  function deleted() {
		var id_movimiento = $("#id_movimiento").val();
		var id_apertura = $("#id_apertura").val();
		var monto = $("#monto").val();
		var tipo_movimiento = $("#tipo_movimiento").val();
		var datos = "process=eliminar"+"&id_movimiento="+id_movimiento+"&id_apertura="+id_apertura+"&monto="+monto+"&tipo_movimiento="+tipo_movimiento;
		$.ajax({
			type : "POST",
			url : "borrar_movimiento_caja.php",
			data : datos,
			dataType : 'json',
			success : function(datax) {
				display_notify(datax.typeinfo, datax.msg);

				if(datax.typeinfo == "Success")
				{
					setInterval("location.reload();", 1000);
					$('#deleteModal').hide();
				}
			}
		});
	}
	function agregar_ingreso()
	{
		//var id_empleado = $("#id_empleado").val();
		//var id_apertura = $("#id_apertura").val();
		var caja = $("#caja").val();
		//var turno = $("#turno").val();
		var monto = $("#monto").val();
		var concepto = $("#concepto").val();
		var admin=$("#admin").val();
		if(admin==1){
			var id_empleado=$("#cajero").val();
			var id_apertura=0;
			var turno=$("#turno_fac").val();
			var fecha_fac=$("#fecha_fac").val();

		}else{
			var id_empleado = $("#id_empleado").val();
			var id_apertura = $("#id_apertura").val();
			var turno = $("#turno").val();
			var fecha_fac=0;

		}
		var datos = "process=ingreso"+"&id_apertura="+id_apertura+"&id_empleado="+id_empleado+"&turno="+turno+"&monto="+monto+"&concepto="+concepto+"&caja="+caja+'&admin='+admin+'&fecha_fac='+fecha_fac;
		//alert(id_apertura+" "+turno+" "+fecha_fac+" "+id_empleado);
		if(concepto!=""){
		if(monto!=""){
		$.ajax({
			type : "POST",
			url : "agregar_ingreso_caja.php",
			data : datos,
			dataType : 'json',
			success : function(datax) {
				display_notify(datax.typeinfo, datax.msg);
				if(datax.typeinfo == "Success")
				{

					//imprimir_vale(datax.id_mov);
					setInterval("location.reload();", 1000);
					$('#viewModal').hide();
				}
			}
		});
	}else{display_notify('Warning', 'El monto es requerido!');}
	}else{display_notify('Warning', 'El concepto es requerido!');}
	}
	$(document).on("keyup","#monto", function()
	{
		var monto = parseFloat($(this).val());
		var tipo_doc = $("#tipo_doc").val();
		if(tipo_doc == "CCF")
		{
			var result = monto - (monto/1.13);
			var iva = monto * 0.13;

			//$("#monto").val(result);
			$(".caja_iva").attr("hidden", false);
			$("#iva").val(iva);

			if(monto == "")
			{
				$(".caja_iva").attr("hidden", true);
				$("#iva").val("0");
			}
			else if(monto == 0)
			{
				$(".caja_iva").attr("hidden", true);
				$("#iva").val("0");
			}
		}
		else
		{
			$(".caja_iva").attr("hidden", true);
			$("#iva").val("0");
		}
	});
	function agregar_salida()
	{
		//var id_empleado = $("#id_empleado").val();
		//var id_apertura = $("#id_apertura").val();
		//var turno = $("#turno").val();
		var monto = $("#monto").val();
		var concepto = $("#concepto").val();
		var tipo_doc = $("#tipo_doc").val();
		var n_doc = $("#n_doc").val();
		var recibe = $("#recibe").val();
		var estado = $("#proceso").val();
		var admin=$("#admin").val();
		if(admin==1){
			var id_empleado=$("#cajero").val();
			var id_apertura=0;
			var turno=$("#turno_fac").val();
			var fecha_fac=$("#fecha_fac").val();

		}else{
			var id_empleado = $("#id_empleado").val();
			var id_apertura = $("#id_apertura").val();
			var turno = $("#turno").val();
			var fecha_fac=0;

		}
		//alert(id_apertura+" "+id_empleado+" "+fecha_fac+" "+turno);
    var pro = $("#proveedor").val();
    var prove=pro.split("|");
    var proveedor="";
    if(prove.length>1){
      proveedor=prove[1];
    }else{
      proveedor=prove[0];
    }
		var caja = $("#caja").val();
		var datos = "process=salida"+"&id_apertura="+id_apertura+"&id_empleado="+id_empleado+"&turno="+turno+"&monto="+monto+"&concepto="+concepto+"&proveedor="+proveedor+"&tipo_doc="+tipo_doc+"&n_doc="+n_doc+"&recibe="+recibe+"&caja="+caja+'&fecha_fac='+fecha_fac+'&admin='+admin+'&estado='+estado;
	if(n_doc!=""){
		if(concepto!=""){
			if(monto!=""){
				if(recibe!=""){
		$.ajax({
			type : "POST",
			url : "agregar_salida_caja.php",
			data : datos,
			dataType : 'json',
			success : function(datax) {
				display_notify(datax.typeinfo, datax.msg);
				if(datax.typeinfo == "Success")
				{
				//	imprimir_vale(datax.id_mov);
					setInterval("location.reload();", 1000);
					$('#salidaModal').hide();
				}
			}
		});
	}else{display_notify('Warning', 'Recibe  es requerido!');}
	}else{display_notify('Warning', 'El monto es requerido!');}
	}else{display_notify('Warning', 'El concepto es requerido!');}
	}else{display_notify('Warning', 'El número de documento es requerido!');}

	}
	function agregar_viatico()
	{
    if ($("#presentacion_table tr").length > 0) {
	    var tipo_deli = $('#tipo_deli').val();
	    var concepto = $('#concepto').val();
	    var monto = $('#monto').val();
		var recibe = $('#n_empleado').val();
		var caja = $("#caja").val();
		//si es un admin
		var admin=$("#admin").val();
		if(admin==1){
			var id_empleado=$("#cajero").val();
			var id_apertura=0;
			var turno=$("#turno_fac").val();
			var fecha_fac=$("#fecha_fac").val();

		}else{
			var id_empleado = $("#id_empleado").val();
			var id_apertura = $("#id_apertura").val();
			var turno = $("#turno").val();
			var fecha_fac=0;

		}
		//alert(id_apertura+" "+turno+" "+fecha_fac+" "+id_empleado);
	    var lista = "";
	    var cuantos = 0;
	    //Get the value from form if edit or insert
	    var process = $('#process').val();
	    if (process == 'insert') {
        var id_movimiento ="0";
        var monto_anti ="";
	      var urlprocess = 'agregar_viatico_caja.php';
	    }
	    if (process == 'edited') {
	      var id_movimiento = $('#id_movimiento').val();
	      var urlprocess = 'editar_movimiento_caja_v.php';
        var monto_anti = $("#monto_anti").val();
        var id_empleado = "0";
        var caja = "0";
        var turno = "0";

	    }
	    $("#presentacion_table tr").each(function() {
	      var natu = $(this).find(".natu").val();
        var id_mcd = $(this).find(".id_mcd").val();
	      var detalle = $(this).find(".detalle").val();
	      var valor = $(this).find(".valor").val();
	      lista += natu + "," + detalle + "," + valor +","+ id_mcd+ "|";
	      cuantos += 1;
	    });
	    var dataString = 'process=' + process + '&tipo_deli=' + tipo_deli + '&concepto=' + concepto + '&monto=' + monto;
	    dataString += '&recibe=' + recibe + '&id_empleado=' + id_empleado + '&caja=' + caja + '&id_apertura=' + id_apertura + '&lista=' + lista;
	    dataString += '&turno=' + turno + '&cuantos='+ cuantos+'&id_movimiento='+id_movimiento+"&monto_anti="+monto_anti+'&admin='+admin+'&fecha_fac='+fecha_fac;
      if(concepto){
      if(monto){
      if(monto){
      $.ajax({
        type: 'POST',
        url: urlprocess,
        data: dataString,
        dataType: 'json',
        success: function(datax) {
         // imprimir_viatico(datax.id_mov);
          display_notify(datax.typeinfo, datax.msg);
          if (datax.typeinfo == "Success") {
            setInterval("location.reload();", 1000);
          }
        }
      });
    }else{display_notify("Warning", "Debe ingresar concepto");}
  }else{display_notify("Warning", "Debe ingresar monto");}
}else{display_notify("Warning", "Debe ingresar recibe");}
	  } else {
	    display_notify("Warning", "Debe ingresar al menos un detalle");
	  }
}
	function editar_movimiento()
	{
		var id_empleado = $("#id_empleado").val();
		var id_apertura = $("#id_apertura").val();
		var turno = $("#turno").val();
		var monto = $("#monto").val();
		var concepto = $("#concepto").val();
		var id_movimiento = $("#id_movimiento").val();

		var datos = "process=editar"+"&id_apertura="+id_apertura+"&id_empleado="+id_empleado+"&turno="+turno+"&monto="+monto+"&concepto="+concepto+"&id_movimiento="+id_movimiento;

		$.ajax({
			type : "POST",
			url : "editar_movimiento_caja.php",
			data : datos,
			dataType : 'json',
			success : function(datax) {
				display_notify(datax.typeinfo, datax.msg);

				if(datax.typeinfo == "Success")
				{
					setInterval("location.reload();", 1000);
					$('#editEModal').hide();
				}
			}
		});
	}
	function editar_movimiento_s()
	{
		var id_empleado = $("#id_empleado").val();
    var monto_anti = $("#monto_anti").val();
		var monto = $("#monto").val();
    var id_apertura = $("#id_apertura").val();
		var concepto = $("#concepto").val();
		var tipo_doc= $("#tipo_doc").val();
		var n_doc=$("#n_doc").val();
		var estado=$("#proceso").val();
		var iva=$("#iva").val();
			var proveedor=$("#proveedor").val();
		var id_movimiento = $("#id_movimiento").val();

		var datos = "process=editar"+"&id_empleado="+id_empleado+"&monto="+monto+"&concepto="+concepto+"&id_movimiento="+id_movimiento+"&tipo_doc="+tipo_doc+"&n_doc="+n_doc+"&iva="+iva+"&proveedor="+proveedor+"&monto_anti="+monto_anti+"&id_apertura="+id_apertura+'&estado='+estado;
		$.ajax({
			type : "POST",
			url : "editar_movimiento_caja_s.php",
			data : datos,
			dataType : 'json',
			success : function(datax) {
				display_notify(datax.typeinfo, datax.msg);

				if(datax.typeinfo == "Success")
				{
					//imprimir_vale(id_movimiento);
					setInterval("location.reload();", 1000);
					$('#editEModal').hide();
				}
			}
		});
	}
	function imprimir_vale(id_movimiento){
		var datoss = "process=imprimir"+"&id_movimiento="+id_movimiento;
		$.ajax({
			type : "POST",
			url :"agregar_ingreso_caja.php",
			data : datoss,
			dataType : 'json',
			success : function(datos) {
				var sist_ope = datos.sist_ope;
				var dir_print=datos.dir_print;
				var shared_printer_win=datos.shared_printer_win;
				var shared_printer_pos=datos.shared_printer_pos;

					if (sist_ope == 'win') {
						$.post("http://"+dir_print+"printvalewin1.php", {
							datosvale: datos.movimiento,
							shared_printer_win:shared_printer_win,
							shared_printer_pos:shared_printer_pos
						})
					} else {
						$.post("http://"+dir_print+"printvale1.php", {
							datosvale: datos.movimiento
						});
					}
			}
		});
	}

  function imprimir_viatico(id_movimiento){
		var datoss = "process=imprimir_viatico"+"&id_movimiento="+id_movimiento;
		$.ajax({
			type : "POST",
			url :"agregar_ingreso_caja.php",
			data : datoss,
			dataType : 'json',
			success : function(datos) {
				var sist_ope = datos.sist_ope;
				var dir_print=datos.dir_print;
				var shared_printer_win=datos.shared_printer_win;
				var shared_printer_pos=datos.shared_printer_pos;

					if (sist_ope == 'win') {
						$.post("http://"+dir_print+"printvalewin1.php", {
							datosvale: datos.movimiento,
							shared_printer_win:shared_printer_win,
							shared_printer_pos:shared_printer_pos
						})
					} else {
						$.post("http://"+dir_print+"printvale1.php", {
							datosvale: datos.movimiento
						});
					}
			}
		});
	}
	function reimprimir()
	{
		var id_movimiento = $("#id_movimiento").val();
    var ds = $("#tipo").val();
    if(ds != 3)
    {
      imprimir_vale(id_movimiento);
    }
    else
    {
      imprimir_viatico(id_movimiento);
    }
		//$('#viewModal').hide();
		//setInterval("location.reload();", 500);
	}

	$("#search").click(function()
	{
		var fecha1 = $("#fecha1").val();
		var fecha2 = $("#fecha2").val();
		var process = "ok";

		$.ajax({
			type:'POST',
			url:"admin_movimiento_caja.php",
			data: "process="+process+"&fecha1="+fecha1+"&fecha2="+fecha2,
			success: function(datax){
				$("#caja_x").html(datax);
			}
		});
	});
	$(document).on("click", "#add_pre", function() {

	  var natu = $("#natu").val();
	  var detalle = $("#detalle").val();
	  var valor = $("#valor").val();
	  if (natu != "" &&  detalle != "" && valor != "" ) {
	      var fila = "<tr>";
	      fila += "<td ><input type='text' class='natu' value='"+natu+"'><input type='hidden' class='id_mcd'  value='0'></td>";
	      fila += "<td ><input type='text'  class='detalle' value='"+detalle+"'></td>";
	      fila += "<td ><input type='text' class='valor numeric' value='"+valor+"'></td>";
	      fila += "<td class='delete text-center'><a class='btn Delete'><i class='fa fa-trash'></i></a></td>";
        fila +="</tr>";
	      $("#presentacion_table").append(fila);
	      $(".clear").val("");
	  } else {
	    display_notify("Error", "Por favor complete todos los campos");
	  }
      var importe_total = 0;
      $(".valor").each(
      function(index, value) {
        if ( $.isNumeric( $(this).val() ) ){
        importe_total = importe_total + eval($(this).val());
        //console.log(importe_total);
        var monto=$("#monto").val();
        if(importe_total>monto){
          var resta=importe_total- eval($(this).val());
          var reset=monto-resta;
          eval($(this).val(reset));
          display_notify('Warning', 'La suma de los detalles sobrepaso el monto: verifique!');
        }
        }
      }
    );
  });
	$(document).on("click", ".Delete", function() {
	  $(this).parents("tr").remove();
	});
