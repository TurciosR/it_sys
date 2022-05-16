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
			"order":[[ 5, 'desc' ], [ 7, 'desc' ]],
			"processing": true,
			"serverSide": true,
			"ajax":{
					url :"admin_reserva_rangos_dt.php?fechai="+fechai+"&fechaf="+fechaf, // json datasource
					//url :"admin_factura_rangos_dt.php", // json datasource
					//type: "post",  // method  , by default get
					error: function(){  // error handling
						$(".editable2-error").html("");
						$("#editable2").append('<tbody class="editable2_grid-error"><tr><th colspan="3">No se encontró información segun busqueda </th></tr></tbody>');
						$("#editable2_processing").css("display","none");
						$( ".editable2-error" ).remove();
						}
					},
			"language": {
								"url": "js/funciones/Spanish.json"
						}
				} );

		dataTable.ajax.reload()
	//}
}
$(function (){
	//binding event click for button in modal form
	$(document).on("click", "#btnDelete", function(event) {
		deleted();
	});
	//Reimprimir factura
	$(document).on("click", "#btnPrint", function(event) {
		print1();
	});
	// Clean the modal form
	$(document).on('hidden.bs.modal', function(e) {
		var target = $(e.target);
		target.removeData('bs.modal').find(".modal-content").html('');
	});
	//Reimprimir factura
	$(document).on("click", "#btnPrintFact", function(event) {
		imprime1();
	})
	//Recargar facturas
	$(document).on("click", "#btnReload", function(event) {
		reload2();
	});
	//Recargar facturas
	$(document).on("click", "#btnReload3", function(event) {
		reload3();
	});
	//Finalizar factura
	$(document).on("click", "#btnFinFact", function(event) {
		finalizar2();
	})
});
$(document).on("click", "#btnMostrar", function(event) {
	generar();
});

function reload1(){
	location.href = 'admin_factura_rangos.php';
}
function reload2(){
	location.href = 'admin_venta_nofin.php';
}
function reload3(){
	location.href = 'admin_facturas_vendedor.php';
}
function deleted() {
	var id_transace = $('#id_transace').val();
	var dataString = 'process=deleted'+'&id_transace=' + id_transace;
	$.ajax({
		type : "POST",
		url : "anular_ventas.php",
		data : dataString,
		dataType : 'json',
		success : function(datax)
		{
			display_notify(datax.typeinfo, datax.msg);
			if(datax.typeinfo == "Success")
			{
				setInterval("location.reload();", 1000);
			}
			$('#btnclose').click();
		}
	});
}
function print1() {
	var id_factura = $('#id_factura').val();
	var dataString = 'process=imprimir_fact' + '&id_factura=' + id_factura;
	$.ajax({
		type : "POST",
		url : "reimprimir_factura.php",
		data : dataString,
		dataType : 'json',
		success : function(datos) {
			var sist_ope = datos.sist_ope;
			var dir_print=datos.dir_print;
			var efectivo_fin = parseFloat($('#efectivo').val());
			var cambio_fin = parseFloat($('#cambio').val());
			var tipo_impresion= datos.tipo_impresion;
			//esta opcion es para generar recibo en  printer local y validar si es win o linux
			if (tipo_impresion == 'COF') {
				if (sist_ope == 'win') {
					$.post("http://"+dir_print+"/printfactwin1.php", {
						datosventa: datos.facturar
					})
				} else {

					$.post("http://"+dir_print+"/printfact1.php", {
						datosventa: datos.facturar,
						efectivo: efectivo_fin,
						cambio: cambio_fin
					}, function(data, status) {

						if (status != 'success') {
							alert("No Se envio la impresión " + data);
						} else {
							setInterval("reload1();", 500);
						}

					});
				}
			}

				if (tipo_impresion == 'TIK') {
				if (sist_ope == 'win') {
					$.post("http://"+dir_print+"/printposwin1.php", {
						datosventa: datos.facturar
					})
				} else {
					$.post("http://"+dir_print+"/printpos1.php", {
						datosventa: datos.facturar,
						efectivo: efectivo_fin,
						cambio: cambio_fin
					}, function(data, status) {
						if (status != 'success') {
							alert("No Se envio la impresión " + data);
						} else {
							setInterval("reload1();", 500);
						}
					});
				}
			}
		//  setInterval("reload1();", 500);


		}
	});
}
//Impresion
function imprime1(){

  var id_transace = $("#id_transace").val(); //del modal
  var tipo_impresion = $("#alias_tipodoc").val(); //del modal
  var print = 'imprimir_fact';

  var dataString = 'process=' + print + '&id_transace=' + id_transace+ '&tipo_impresion='+tipo_impresion;
  $.ajax({
    type: 'POST',
    url: "ver_ventas.php",
    data: dataString,
    dataType: 'json',
    success: function(datos) {
			var sist_ope = datos.sist_ope;
	 	 var dir_print=datos.dir_print;
	 	 var shared_printer_win=datos.shared_printer_win;
	 	 var shared_printer_pos=datos.shared_printer_pos;
	 	 var headers=datos.headers;
	 	 var footers=datos.footers;
		 var a_pagar=datos.a_pagar
      //estas opciones son para generar recibo o factura en  printer local y validar si es win o linux
      if (tipo_impresion == 'TIK') {
      				if (sist_ope == 'win') {
                $.post("http://"+dir_print+"printposwin1.php", {
      						datosventa: datos.facturar,
      						efectivo: 0,
      						cambio: 0,
									headers:headers,
									footers:footers,
									  a_pagar:a_pagar,
      						shared_printer_pos:shared_printer_pos
                })
              } else {
                $.post("http://"+dir_print+"printpos1.php", {
                  datosventa: datos.facturar,
                  efectivo: 0,
                  cambio: 0,
									headers:headers,
									footers:footers,
									  a_pagar:a_pagar,
                }, function(data, status) {
                  if (status != 'success') {}
                }
              );
              }
      }
      if (tipo_impresion == 'COF') {
        if (sist_ope == 'win') {
          $.post("http://"+dir_print+"printfactwin1.php", {
						datosventa: datos.facturar,
						efectivo: 0,
						cambio: 0,
						shared_printer_win:shared_printer_win
          })
        } else {
          $.post("http://"+dir_print+"printfact1.php", {
            datosventa: datos.facturar,
            efectivo: 0,
            cambio: 0
          }
          /*
          , function(data, status) {
            if (status != 'success') {}

          }*/
        );
        }
      }

		if (tipo_impresion == 'CCF') {
        if (sist_ope == 'win') {
          $.post("http://"+dir_print+"printcfwin1.php", {
						datosventa: datos.facturar,
						efectivo: 0,
						cambio: 0,
						shared_printer_win:shared_printer_win
          })
        } else {
          $.post("http://"+dir_print+"printcf1.php", {
            datosventa: datos.facturar,
            efectivo: 0,
            cambio: 0
          }, function(data, status) {
          });
        }
      }
    }
  });
}
$(document).on("keyup","#efectivo",function(){
  total_efectivo();
});
function total_efectivo(){
	var efectivo=parseFloat($('#efectivo').val());
	var totalfinal=parseFloat($('#facturado').text());
	var facturado= totalfinal.toFixed(2);
	if (isNaN(parseFloat(efectivo))){
		efectivo=0;
	}
	if (isNaN(parseFloat(totalfinal))){
		totalfinal=0;
	}
	var cambio=efectivo-totalfinal;
	var cambio=round(cambio, 2);
	var	cambio_mostrar=cambio.toFixed(2);
	if($('#efectivo').val()!='' && efectivo>=totalfinal)
		$('#cambio').html("<h5 class='text-success'>"+cambio_mostrar+"</h5>");
	else
		$('#cambio').text('');
	if(efectivo<totalfinal){
		$('#cambio').html("<h5 class='text-danger'>"+"Falta dinero !!!"+"</h5>");
	}
}
//function to round 2 decimal places
function round(value, decimals) {
    return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
}

function finalizar2() {
	var id_factura = $('#id_factura').val();
	var dataString = 'process=finalizar_fact' + '&id_factura=' + id_factura;
	$.ajax({
		type : "POST",
		url : "finalizar_factura.php",
		data : dataString,
		dataType : 'json',
				success: function(datax){
				process=datax.process;
				factura=datax.factura;
				display_notify(datax.typeinfo,datax.msg);


			 setInterval("reload2();", 3000);


		}
	});
}
