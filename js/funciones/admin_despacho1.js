var dataTable ="";


$(document).ready(function() {
$(".select").select2();
	// Clean the modal form
	/*$(document).on('hidden.bs.modal', function(e) {
		var target = $(e.target);
		target.removeData('bs.modal').find(".modal-content").html('');
	});*/

});

$(function (){
	$(document).on("click", "#btnDesp", function(event) {
		senddata();
	});

	$(document).on("click", "#btnAgregar", function(event) {
		var pol=$("#politica_text").val();
		var pol_caja=$("#politica").val();
		var pol_id=$("#politica_id").val();
		if (pol!="") {
			tr_add = '';
			tr_add += "<tr><input type='hidden' name='pol_id'  id='pol_id' value='"+pol_id+"'>";
			tr_add += "<td id='desc'>"+pol+"</td>";
			tr_add += '</tr>';
			$("#inventable2").append(tr_add);
			$("#politica").val("");
			$("#politica_text").val("");
			$("#politica_text").css("display", "none");
			$("#politica").removeAttr("style");
			$("#politica_id").val("");
		}
		else
		{
			tr_add = '';
			tr_add += "<tr><input type='hidden' name='pol_id'  id='pol_id' value='0'>";
			tr_add += "<td id='desc'>"+pol_caja+"</td>";
			tr_add += '</tr>';
			$("#inventable2").append(tr_add);
			$("#politica").val("");
			$("#politica_text").val("");
			$("#politica_text").css("display", "none");
			$("#politica").removeAttr("style");
			$("#politica_id").val("");
		}
		console.log(pol);
	});
	//binding event click for button in modal form
		// Clean the modal form
	/*$(document).on('hidden.bs.modal', function(e) {
		var target = $(e.target);
		target.removeData('bs.modal').find(".modal-content").html('');
	});*/
	//Reimprimir factura
	$(document).on("ifChecked",".cort",function(){
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
	});

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
$(document).on("")

function senddata() {
  //Obtener los valores a guardar de cada item facturado
  var procces = $("#process").val();
  var i = 0;
  var j = 0;
  var k = 0;
  var StringDatos = "";
	var despacho=0;
	var array_json = new Array();
	var array_json1 = new Array();
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
      var tipo = $(this).find('#tipo_periodo').val();
      var cant = $(this).find('#cant').val();
      var precio = $(this).find('#precio_v').val();
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
					obj.precio = precio;
					obj.cantidad = cant;
					obj.tipo_periodo = tipo;
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
	$("#inventable2 tr").each(function() {
		var id_po=$(this).find("#pol_id").val();
		var desc=$(this).find("#desc").text();
		if (desc!="") {
			var obj1 = new Object();
			obj1.desc = desc;
			obj1.pol_id = id_po;
			text1 = JSON.stringify(obj1);
			array_json1.push(text1);
			k=k+1;
		}

	});

  json_arr = '[' + array_json + ']';
  json_arr1 = '[' + array_json1 + ']';
  if (procces == "insert") {
    var urlprocess = "despacho.php";
  }
  var dataString = 'process=insert' + '&cuantos=' + j + '&despacho=' + despacho+ '&id_factura=' + id_factura;
  dataString += '&json_arr=' + json_arr+ '&id_cliente=' + id_cliente+'&json_arr1=' + json_arr1+'&cuantos1=' + k;
  if (sel_vendedor==1) {
    $.ajax({
      type: 'POST',
      url: "despacho.php",
      data: dataString,
      dataType: 'json',
      success: function(datax) {
        if (datax.typeinfo == "Success") {
					display_notify(datax.typeinfo, datax.msg);
					setInterval("reload1();", 1000);

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
