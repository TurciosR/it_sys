var dataTable ="";
$(document).ready(function() {

	// Clean the modal form
	$(document).on('hidden.bs.modal', function(e) {
		var target = $(e.target);
		target.removeData('bs.modal').find(".modal-content").html('');
	});
	generar();
});
function generar(){
	fechai=$("#fecha_inicio").val();
	fechaf=$("#fecha_fin").val();
	dataTable = $('#editable2').DataTable().destroy()
	dataTable = $('#editable2').DataTable( {
			"pageLength": 50,
			"order":[[ 1, 'desc' ], [ 0, 'desc' ]],
			"processing": true,
			"serverSide": true,
			"ajax":{
					url :"admin_despacho_dt.php?fechai="+fechai+"&fechaf="+fechaf, // json datasource
					//url :"admin_factura_rangos_dt.php", // json datasource
					//type: "post",  // method  , by default get
					error: function(){  // error handling
						$(".editable2-error").html("");
						$("#editable2").append('<tbody class="editable2_grid-error"><tr><th colspan="3">No se encontró información segun busqueda </th></tr></tbody>');
						$("#editable2_processing").css("display","none");
						$( ".editable2-error" ).remove();
						}
					}
				} );

		dataTable.ajax.reload()
	//}
}
$(function (){
	/*$(document).on("click", "#btnDesp", function(event) {
		senddata();
	});*/
	$(document).on("click", "#btnMostrar", function(event) {
		generar();
	});

	//binding event click for button in modal form
		// Clean the modal form
	$(document).on('hidden.bs.modal', function(e) {
		var target = $(e.target);
		target.removeData('bs.modal').find(".modal-content").html('');
	});
	//Reimprimir factura
/*	$(document).on("ifChecked",".cort",function(){
		$(this).parents("tr").find("#idco").each(function() {
				 var pr = $(this).parents("tr");
				 pr.find('#cortesia').val("1");
				 console.log("act");

			});
	});
	$(document).on("ifUnchecked",".cort",function(){
		$(this).parents("tr").find("#idco").each(function() {
				 var pr = $(this).parents("tr");
				 pr.find('#cortesia').val("0");
				 console.log("des");


			});
	});*/

});

function reload1(){
	location.href = 'admin_despachos.php';
}
/*
$(document).on('change', '.cort', function() {
  if ($(this).is(':checked')) {
    $(this).parents("tr").find("#idco").each(function() {
      var tr = $(this).parents("tr");
      tr.find("#cortesia").val(1);


    });
  } else {
    $(this).parents("tr").find("#idco").each(function() {
      var tr = $(this).parents("tr");
      tr.find("#cortesia").val(0);
       });
  }
});*/


/* function senddata() {

	var procces = $("#process").val();
  var i = 0;
  var j = 0;
  var StringDatos = "";
	var despacho=0;
	var array_json = new Array();
  var msg = "";
  error = false;
	var sel_vendedor = 1;
	var id_factura =$("#id_factura").val();
	var id_cliente =$("#id_cliente").val();
	var n =0;
  $("#inventable tr").each(function() {
      despacho = $(this).find('#cortesia').val();
      var serie = $(this).find('#serie').val();
      var dias = $(this).find('#dias').val();
      var id_p = $(this).find('#id_p').val();
      var ti_ga = $(this).find('#ti_ga').val();
			n =$(this).find("#idco").val();

			console.log(despacho);
      if (despacho==1) {
				if (ti_ga==1) {
					var obj = new Object();
					obj.serie = serie;
					obj.id_p = id_p;
					obj.dias = dias;
					text = JSON.stringify(obj);
					array_json.push(text);
					j=j+1;
				}
        i = i + 1;
      } else {
        error = true;
				msg="Hay productos sin despachar!"
				sel_vendedor = 0;
      }
			if(i==n){
				sel_vendedor = 1;
			}

  });

  json_arr = '[' + array_json + ']';
  if (procces == "insert") {
    var urlprocess = "despacho.php";
  }
  var dataString = 'process=insert' + '&cuantos=' + j + '&despacho=' + despacho+ '&id_factura=' + id_factura;
  dataString += '&json_arr=' + json_arr+ '&id_cliente=' + id_cliente;
  if (sel_vendedor==1) {
    $.ajax({
      type: 'POST',
      url: "despacho.php",
      data: dataString,
      dataType: 'json',
      success: function(datax) {
        if (datax.typeinfo == "Success") {
					display_notify(datax.typeinfo, datax.msg);
					//setInterval("reload1();", 1000);

        } else {
          display_notify(datax.typeinfo, datax.msg);
        }
      }
    });
    ///
  } else {
		display_notify('Warning', msg);
  }
}
*/
