function round(value, decimals)
{
    return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
}

$(document).ready(function() {
	$(".select").select2({
    placeholder: {
      id: '',
      text: 'Seleccione',
    },
    allowClear: true,
  });
  $("#submit1").click(function()
  {
    senddata();
  })

  $(".decimal").numeric();
});
////#agregar remesas a la tabla
$(document).on("click", "#add_pre", function(){
    var n_reme = $("#n_remesaa").val();
    var reme = $("#remesaa").val();
    var id_cuenta = $("#cuentaa").val();
    var text_cuenta=$('select[name="cuentaa"] option:selected').text();
    if (n_reme != "" &&  reme != "" && id_cuenta != "" ) {
        var fila = "<tr>";
        fila += "<td class='n_reme'>"+n_reme+"</td>";
        fila += "<td class='reme'>"+reme+"</td>";
        fila += "<td class='id_cuenta' hidden>"+id_cuenta+"</td>";
        fila += "<td>"+text_cuenta+"</td>";
        fila += "<td class='delete text-center'><a class='btn Delete'><i class='fa fa-trash'></i></a></td>";
        fila +="</tr>";
        $("#presentacion_table").append(fila);
        $(".clear").val("");
    } else {
      display_notify("Error", "Por favor complete todos los campos para la remesa");
    }
      /*var importe_total = 0;
      $(".").each(
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
    );*/
  });
  $(document).on("click", ".Delete", function(){
    $(this).parents("tr").remove();
  });
  ////finalizo agregar remesa
$(document).on("change", "#bancoo", function(event) {
    var id_banco = $(this).val();
    $("#cuentaa").val("");
    $("#cuentaa").empty().trigger('change');
    $.ajax({
      type: "POST",
      url: "admin_mov_cta_banco.php",
      data: "process=val&id_banco=" + id_banco,
      dataType: "JSON",
      success: function(datax) {
        if (datax.typeinfo == "Success") {
          $("#cuentaa").html(datax.opt);
        }
      }
    });

  });

$(function (){
	//binding event click for button in modal form
	$(document).on("click", "#btnDelete", function(event) {
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

$("#tipo_corte").change(function()
{
	var tipo = $(this).val();
	if(tipo == "C")
	{
		$("#table_mov").attr("hidden", false);
		$("#table_dev").attr("hidden", false);
		$("#caja_dev").attr("hidden", false);
		$("#caja_mov").attr("hidden", false);
    $("#table_nc").attr("hidden", true);
		$("#caja_nc").attr("hidden", true);
    $("#tabla_no_pago").attr("hidden", false);
    $("#caja_no_pago").attr("hidden", false);
    $("#tabla_recuperacion").attr("hidden", false);
    $("#caja_recuperacion").attr("hidden", false);
    $("#tabla_recuperacion_1").attr("hidden", false);
    $("#caja_recuperacion_1").attr("hidden", false);
    $("#tabla_contado").attr("hidden", false);
    $("#caja_contado").attr("hidden", false);
    $("#caja_ventaxz").attr("hidden", true);
    $("#tabla_ventaxz").attr("hidden", true);
    $("#caja_facturado").attr("hidden", false);
    $("#table_facturado").attr("hidden", false);
    $("#caja_t").attr("hidden", false);
    $("#table_t").attr("hidden", false);
    $("#caja_aper").attr("hidden", false);
    $("#table_aper").attr("hidden", false);
    $("#caja_ve").attr("hidden", true);
    $("#table_ve").attr("hidden", true);
    $("#add").attr("hidden",false);
    //cambio(tipo);
		total();
	}
	else if(tipo == "X")
	{
		$("#table_mov").attr("hidden", true);
		$("#table_dev").attr("hidden", false);
		$("#caja_dev").attr("hidden", false);
		$("#caja_mov").attr("hidden", true);
		$("#table_nc").attr("hidden", false);
		$("#caja_nc").attr("hidden", false);
    $("#tabla_no_pago").attr("hidden", true);
    $("#caja_no_pago").attr("hidden", true);
    $("#tabla_recuperacion").attr("hidden", true);
    $("#caja_recuperacion").attr("hidden", true);
    $("#tabla_recuperacion_1").attr("hidden", true);
    $("#caja_recuperacion_1").attr("hidden", true);
    $("#tabla_contado").attr("hidden", true);
    $("#caja_contado").attr("hidden", true);
    $("#caja_ventaxz").attr("hidden", false);
    $("#tabla_ventaxz").attr("hidden", false);
    $("#caja_facturado").attr("hidden", true);
    $("#table_facturado").attr("hidden", true);
    $("#caja_t").attr("hidden", true);
    $("#table_t").attr("hidden", true);
    $("#caja_aper").attr("hidden", true);
    $("#table_aper").attr("hidden", true);
    $("#caja_ve").attr("hidden", false);
    $("#table_ve").attr("hidden", false);
    $("#add").attr("hidden",true);
    //cambio(tipo);
		total();
	}
	else if(tipo == "Z")
	{
		$("#table_mov").attr("hidden", true);
		$("#table_dev").attr("hidden", false);
		$("#caja_dev").attr("hidden", false);
		$("#caja_mov").attr("hidden", true);
		$("#table_nc").attr("hidden", false);
		$("#caja_nc").attr("hidden", false);
    $("#tabla_no_pago").attr("hidden", true);
    $("#caja_no_pago").attr("hidden", true);
    $("#tabla_recuperacion").attr("hidden", true);
    $("#caja_recuperacion").attr("hidden", true);
    $("#tabla_recuperacion_1").attr("hidden", true);
    $("#caja_recuperacion_1").attr("hidden", true);
    $("#tabla_contado").attr("hidden", true);
    $("#caja_contado").attr("hidden", true);
    $("#caja_ventaxz").attr("hidden", false);
    $("#tabla_ventaxz").attr("hidden", false);
    $("#caja_facturado").attr("hidden", true);
    $("#table_facturado").attr("hidden", true);
    $("#caja_t").attr("hidden", true);
    $("#table_t").attr("hidden", true);
    $("#caja_aper").attr("hidden", true);
    $("#table_aper").attr("hidden", true);
    $("#caja_ve").attr("hidden", false);
    $("#table_ve").attr("hidden", false);
    $("#add").attr("hidden",true);
    //cambio(tipo);
		total();
	}
})

/*function cambio(tipo)
{
  var aper_id = $("#aper_id").val();
  var dev = $("#total_dev").val();
  $.ajax({
    type:'POST',
    url:"corte_caja_diario.php",
    data: "process=cambio&tipo_corte="+tipo+"&aper_id="+aper_id,
    dataType: 'json',
    success: function(datax){
          var total_corte = parseFloat(datax.total_corte);
          ////////////////////////////////////
          var t_tike = datax.t_tike;
          var t_factuta = datax.t_factuta;
          var t_credito = datax.t_credito;
          ////////////////////////////////////
          var total_contado = datax.total_contado;
          var total_transferencia = datax.total_transferencia;
          var total_cheque = datax.total_cheque;
          ////////////////////////////////////
          var total_tike = datax.total_tike;
          var total_factura = datax.total_factura;
          var total_credito_fiscal = datax.total_credito_fiscal;
          ////////////////////////////////////
          var tike_max = datax.tike_max;
          var tike_min = datax.tike_min;
          var factura_max = datax.factura_max;
          var factura_min = datax.factura_min;
          var credito_fiscal_max = datax.credito_fiscal_max;
          var credito_fiscal_min = datax.credito_fiscal_min;
          ///////////////////////////////////
          var monto_apertura = parseFloat(datax.monto_apertura);
          var monto_ch = parseFloat(datax.monto_ch);
          var total_caja_chica = parseFloat($("#total_ch").val());
          var recuperacion = parseFloat($("#recuperacion").val());



          if(tipo == 'Z' || tipo == 'X')
          {

            //$("#total_corte").val(total_corte);
            var total_corte1 = round(total_corte,2);
            var total_corte2 = round(total_corte+monto_apertura,2);
            console.log(total_corte1);
            $("#tt_fin").text("Total Caja");
            var fila = "<tr><td>TIQUETE</td><td>"+tike_min+"</td><td>"+tike_max+"</td><td>"+t_tike+"</td><td><label class='pull-right'>$ "+round(total_tike, 2)+"</label></td></tr>";
            fila += "<tr><td>FACTURA</td><td>"+factura_min+"</td><td>"+factura_max+"</td><td>"+t_factuta+"</td><td><label class='pull-right'>$ "+round(total_factura, 2)+"</label></td></tr>";
            fila += "<tr><td>CREDITO FISCAL</td><td>"+credito_fiscal_min+"</td><td>"+credito_fiscal_max+"</td><td>"+t_credito+"</td><td><label class='pull-right'>$ "+round(total_credito_fiscal, 2)+"</label></td></tr><tr>";
            fila += "<tr><td colspan='4'>TOTAL</td><td><label id='id_total' class='pull-right'>$ "+round(total_corte1, 2)+"</label></td></tr>";

            var fila1 = "<tr><td><input type='text' id='total_efectivo' name='total_efectivo' value='"+total_corte2+"'  class='form-control decimal decimal' readOnly></td>";
            fila1 += "<td style='text-align: center'><label id='id_diferencia'>0.0</label></td>";
            fila1 += "<td style='text-align: center'><label id='sobrante'>0.0</label></td>";
            fila1 += "<td style='text-align: center'><label id='faltante'>0.0</label></td></tr>";

            var fila3 = " <tr><td class='col-md-11'>MONTO APERTURA</td><td class='col-md-1'><label id='id_total12'>$ "+monto_apertura+"</label></td></tr>";
            fila3 += "<tr><td class='col-md-11'>TOTAL VENTAS</td><td class='col-md-1'><label id='id_total12'>$ "+total_corte1+"</label></td></tr>";
            fila3 += "<tr><td class='col-md-11'>TOTAL</td><td class='col-md-1'><label id='id_total12'>$ "+total_corte2+"</label></td></tr>";
            $("#caja_o").html(fila3);

            var fila2 = "<tr><th class='col-lg-2'>TIPO DOCUMENTO</th><th class='col-lg-1'>N° INICIO</th><th class='col-lg-1'>N° FINAL</th><th class='col-lg-1'>N° DOCUMENTO</th><th class='col-lg-2'><i class='pull-right'>TOTAL</i></th></tr>";
            $("#encabeza_contado").html(fila2);
          }
          else
          {

            var salidas =  parseFloat($("#total_salida").val());
            var entrada = parseFloat($("#total_entrada").val());
            //console.log(salidas);
            //console.log(entrada);
            var total_corte1 = total_corte;
            var tt = total_caja_chica + monto_apertura + recuperacion + total_corte1;
            var total_corte2 = round(tt, 2);
            var total_corte3 = round(total_corte+monto_apertura+recuperacion, 2);
            $("#tt_fin").text("Total Caja y Caja Chica");
            //$("#total_corte").val(total_corte1);
            var fila = "<tr><td>TIQUETE</td><td>"+tike_min+"</td><td>"+tike_max+"</td><td>"+t_tike+"</td><td>$ "+total_tike+"</td></tr>";
            fila += "<tr><td>FACTURA</td><td>"+factura_min+"</td><td>"+factura_max+"</td><td>"+t_factuta+"</td><td>$ "+total_factura+"</td></tr>";
            fila += "<tr><td>CREDITO FISCAL</td><td>"+credito_fiscal_min+"</td><td>"+credito_fiscal_max+"</td><td>"+t_credito+"</td><td>$ "+total_credito_fiscal+"</td></tr><tr>";
            fila += "<tr><td colspan='4'>TOTAL</td><td><label id='id_total'>$ "+total_corte1+"</label></td></tr>";

            var fila1 = "<tr><td><input type='text' id='total_efectivo' name='total_efectivo' value=''  class='form-control decimal decimal'></td>";
            fila1 += "<td style='text-align: center'><label id='id_total_general'>"+total_corte2+"</label></td>";
            fila1 += "<td style='text-align: center'><label id='sobrante' style='color: blue'>0.0</label></td>";
            fila1 += "<td style='text-align: center'><label id='faltante' style='color: red'>"+total_corte2+"</label></td></tr>";

            var fila3 = " <tr><td class='col-md-11'>MONTO APERTURA</td><td class='col-md-1'><label id='id_total12'>$ "+monto_apertura+"</label></td></tr>";
            fila3 += "<tr><td class='col-md-11'>TOTAL VENTAS</td><td class='col-md-1'><label id='id_total12'>$ "+total_corte1+"</label></td></tr>";
            fila3 += "<tr><td class='col-md-11'>TOTAL RECUPERACIÓN</td><td class='col-md-1'><label id='id_total12'>$ "+recuperacion+"</label></td></tr>";
            fila3 += "<tr><td class='col-md-11'>TOTAL</td><td class='col-md-1'><label id='id_total12'>$ "+total_corte3+"</label></td></tr>";
            $("#caja_o").html(fila3);
          }

          $("#tabla_doc").html(fila);
          $("#table_data").html(fila1);

          ////////////////////////////////////
          $("#t_tike").val(t_tike);
          $("#t_factuta").val(t_factuta);
          $("#t_credito").val(t_credito);
          ////////////////////////////////////
          $("#total_tike").val(total_tike);
          $("#total_factura").val(total_factura);
          $("#total_credito").val(total_credito_fiscal);
          ////////////////////////////////////
          $("#tike_max").val(tike_max);
          $("#tike_min").val(tike_min);
          $("#factura_max").val(factura_max);
          $("#factura_min").val(factura_min);
          $("#credito_fiscal_max").val(credito_fiscal_max);
          $("#credito_fiscal_min").val(credito_fiscal_min);
      }
  });
}*/

$(document).on("keyup, focusout, blur","#fecha",function(){
	var fecha=$('#fecha').val();
	dataString='process=total_sistema&fecha='+fecha;
	//alert(dataString);
	$.ajax({
				type:'POST',
				url:"corte_caja_diario.php",
				data: dataString,
				dataType: 'json',
				success: function(datax){
					var total=datax.total;
					$('#total_sistema').val(total);
					totales();
				}
			});

	totales();
});

//Eventos que pueden enviar a calular totales corte de caja
$(document).on("keyup","#efectivo, #tarjeta, #cheque",function(){
  totales();
});
function totales(){
	var total_sistema=parseFloat($('#total_sistema').val());
	var efectivo=parseFloat($('#efectivo').val());
	var tarjeta=parseFloat($('#tarjeta').val());
	var cheque=parseFloat($('#cheque').val());
	var observ="";

	if (isNaN(parseFloat(efectivo))){
		efectivo=0;
	}
	if (isNaN(parseFloat(tarjeta))){
		tarjeta=0;
	}
	if (isNaN(parseFloat(cheque))){
		cheque=0;
	}
	var total_corte=efectivo+tarjeta+cheque;
	var diferencia=total_corte-total_sistema;

	var total_cortado=round(total_corte, 2);
	var	total_corte_mostrar=total_cortado.toFixed(2);

	var dif=round(diferencia, 2);
	var	dif_mostrar=dif.toFixed(2);
	if(diferencia>0){
		observ="Hay una diferencia positiva de "+dif_mostrar +" dolares";
	}
	if(diferencia<0){
		observ="Hay una diferencia negativa de "+dif_mostrar +" dolares";
	}
	$('#total_corte').val(total_corte_mostrar);
	$('#diferencia').val(dif_mostrar);
	$('#observaciones').val(observ);
}

function senddata(){
  var tipo_corte = $("#tipo_corte").val();
	var fecha=$('#fecha').val();
	var lista_dev = $("#lista_dev").val();
	var lista_nc = $("#lista_nc").val();
  var t_tike = $("#t_tike").val();
  var t_factura = $("#t_factura").val();
  var t_credito = $("#t_credito").val();

  var t_tikexz = $("#t_tikexz").val();
  var t_facturaxz = $("#t_facturaxz").val();
  var t_creditoxz = $("#t_creditoxz").val();

  var t_dev = $("#t_dev").val();
  var t_nc = $("#t_nc").val();

  var total_tike = $("#total_tike").val();
  var total_factura = $("#total_factura").val();
  var total_credito = $("#total_credito").val();
  var total_dev = $("#total_dev").val();
  var total_nc= $("#total_nc").val();

  var total_tikexz = $("#total_tikexz").val();
  var total_facturaxz = $("#total_facturaxz").val();
  var total_creditoxz = $("#total_creditoxz").val();

  var id_empleado = $("#id_empleado").val();
  var turno = $("#turno").val();
  var id_apertura = $("#id_apertura").val();
  var caja_apertura = $("#caja_apertura").val();

  var tike_min = $("#tike_min").val();
  var tike_max = $("#tike_max").val();
  var factura_min = $("#factura_min").val();
  var factura_max = $("#factura_max").val();
  var credito_fiscal_min = $("#credito_fiscal_min").val();
  var credito_fiscal_max = $("#credito_fiscal_max").val();

  var tike_minxz = $("#tike_minxz").val();
  var tike_maxxz = $("#tike_maxxz").val();
  var factura_minxz = $("#factura_minxz").val();
  var factura_maxxz = $("#factura_maxxz").val();
  var credito_fiscal_minxz = $("#credito_fiscal_minxz").val();
  var credito_fiscal_maxxz = $("#credito_fiscal_maxxz").val();

  var monto_apertura = $("#monto_apertura").val();
  var recuperacion = $("#recuperacion").val();
  var total_ch = $("#total_ch").val();
  var monto_ch = $("#monto_ch").val();
  var aper_id = $("#aper_id").val();
  var viaticos = $("#viaticos").val();
  var abonos_credito = $("#abonos_credito").val();

  var total_corte = $("#total_corte").val();
  var total_corte_z = $("#total_corte_z").val();
  var total_entrada = $("#total_entrada").val();
  var total_salida = $("#total_salida").val();



  var total_vcontado = $("#total_vcontado").val();
  var total_vpendiente = $("#total_vpendiente").val();
  var total_vcredito = $("#total_vcredito").val();
  var total_vcheque = $("#total_vcheque").val();
  var total_vtransferencia = $("#total_vtransferencia").val();

  var recuperacion_doc = $("#recuperacion_doc").val();
  var total_rcredito = $("#total_rcredito").val();
  var remesa = $("#remesaa").val();
  var n_remesa = $("#n_remesaa").val();
  var total_facturado = $("#total_facturado").val();
  var caja_saldo = 0;
  $("#table_t tbody tr").each(function()
  {
    caja_saldo = $("#saldo_caja").val();
  });
  var sobrante = $("#sobrante").val();
  var faltante = $("#faltante").val();

  var abono_creditoE = $("#abono_creditoE").val();
  var abono_creditoC = $("#abono_creditoC").val();
  var abono_creditoT = $("#abono_creditoT").val();

  var total_RE = $("#total_RE").val();
  var total_RC = $("#total_RC").val();
  var total_RT = $("#total_RT").val();

  var pedido_pendiente = $("#pedido_pendiente").val();
  var cobro_pendiente = $("#cobro_pendiente").val();
  var mensaje = $("#mensaje").val();
  // OBTENEMOS LOS VALORES DE REMESA
  var lista = "";
  var cuantos = 0;
  if ($("#presentacion_table tr").length > 0){
    $("#presentacion_table tr").each(function(){
      var n_reme = $(this).find(".n_reme").text();
      var reme = $(this).find(".reme").text();
      var id_cuenta = $(this).find(".id_cuenta").text();
      lista += n_reme + "," + reme + "," + id_cuenta + "|";
      cuantos += 1;
    });
  }
  //Get the value from form if edit or insert
	var process=$('#process').val();
  var dataString = ""
	if(process=='insert'){
		var id_caja_chica=0;
		var urlprocess='corte_caja_diario.php';
    if(tipo_corte == 'C')
    {
      dataString+='process='+process+'&tipo_corte='+tipo_corte+'&fecha='+fecha+'&lista_dev='+lista_dev+'&lista_nc='+lista_nc+'&t_tike='+t_tike+'&t_factuta='+t_factura+'&t_credito='+t_credito+'&t_dev='+t_dev+'&t_nc='+t_nc;
      dataString+='&total_tike='+total_tike+'&total_factura='+total_factura+'&total_credito='+total_credito+'&total_dev='+total_dev+'&total_nc='+total_nc+'&total_corte='+total_corte;
      dataString+='&tike_min='+tike_min+'&tike_max='+tike_max+'&factura_min='+factura_min+'&factura_max='+factura_max+'&credito_fiscal_min='+credito_fiscal_min+'&credito_fiscal_max='+credito_fiscal_max;
      dataString+='&id_empleado='+id_empleado+'&turno='+turno+'&id_apertura='+id_apertura+'&caja_apertura='+caja_apertura;
      dataString+='&monto_apertura='+monto_apertura+'&recuperacion='+recuperacion+'&total_ch='+total_ch+'&monto_ch='+monto_ch+'&aper_id='+aper_id+'&viaticos='+viaticos+'&abono_credito='+abono_credito;
    	dataString+='&total_entrada='+total_entrada+'&total_salida='+total_salida;
      dataString+='&total_vcontado='+total_vcontado+'&total_vpendiente='+total_vpendiente+'&total_vcredito='+total_vcredito+'&total_vcheque='+total_vcheque+'&total_vtransferencia='+total_vtransferencia;
      dataString+='&recuperacion_doc='+recuperacion_doc+'&total_rcredito='+total_rcredito+'&remesa='+remesa+'&total_facturado='+total_facturado+'&saldo_caja='+caja_saldo+'&sobrante='+sobrante+'&faltante='+faltante;
      dataString+='&abono_creditoE='+abono_creditoE+'&abono_creditoC='+abono_creditoC+'&abono_creditoT='+abono_creditoT+'&total_RE='+total_RE+'&total_RC='+total_RC+'&total_RT='+total_RT+'&n_remesa='+n_remesa;
      dataString+='&pedido_pendiente='+pedido_pendiente+'&cobro_pendiente='+cobro_pendiente+'&mensaje='+mensaje+'&lista='+lista+'&cuantos='+cuantos;
    }
    else
    {
      dataString='process='+process+'&tipo_corte='+tipo_corte+'&fecha='+fecha+'&lista_dev='+lista_dev+'&lista_nc='+lista_nc+'&t_tike='+t_tikexz+'&t_factuta='+t_facturaxz+'&t_credito='+t_creditoxz+'&t_dev='+t_dev+'&t_nc='+t_nc;
      dataString+='&total_tike='+total_tikexz+'&total_factura='+total_facturaxz+'&total_credito='+total_creditoxz+'&total_dev='+total_dev+'&total_nc='+total_nc+'&total_corte='+total_corte_z;
      dataString+='&tike_min='+tike_minxz+'&tike_max='+tike_maxxz+'&factura_min='+factura_minxz+'&factura_max='+factura_maxxz+'&credito_fiscal_min='+credito_fiscal_minxz+'&credito_fiscal_max='+credito_fiscal_maxxz;
      dataString+='&id_empleado='+id_empleado+'&turno='+turno+'&id_apertura='+id_apertura+'&caja_apertura='+caja_apertura;
      dataString+='&monto_apertura='+monto_apertura+'&recuperacion='+recuperacion+'&total_ch='+total_ch+'&monto_ch='+monto_ch+'&aper_id='+aper_id+'&viaticos='+viaticos+'&abono_credito='+abono_credito;
    	dataString+='&total_entrada='+total_entrada+'&total_salida='+total_salida;
      dataString+='&total_vcontado='+total_vcontado+'&total_vpendiente='+total_vpendiente+'&total_vcredito='+total_vcredito+'&total_vcheque='+total_vcheque+'&total_vtransferencia='+total_vtransferencia;
      dataString+='&recuperacion_doc='+recuperacion_doc+'&total_rcredito='+total_rcredito+'&remesa='+remesa+'&total_facturado='+total_facturado+'&saldo_caja='+caja_saldo+'&sobrante='+sobrante+'&faltante='+faltante;
      dataString+='&abono_creditoE='+abono_creditoE+'&abono_creditoC='+abono_creditoC+'&abono_creditoT='+abono_creditoT+'&total_RE='+total_RE+'&total_RC='+total_RC+'&total_RT='+total_RT+'&n_remesa='+n_remesa;
      dataString+='&pedido_pendiente='+pedido_pendiente+'&cobro_pendiente='+cobro_pendiente+'&mensaje='+mensaje;
    }
	}
	if(cuantos>0 || tipo_corte != 'C')
	//if(1)
	{
		$.ajax({
		type:'POST',
		url:urlprocess,
		data: dataString,
		dataType: 'json',
		success: function(datax){
				var id_corte=datax.id_corte;
        		display_notify(datax.typeinfo,datax.msg);
				if(datax.typeinfo == "Success")
				{
					//imprimir_corte(id_corte)
					setInterval("reload1();", 5000);
				}

			}
		});
	}else
	{
		display_notify("Success","Debe agregar remesa");
	}
}

function reload1(){
	location.href = 'admin_corte.php';
}
function deleted() {
	var id_producto = $('#id_producto').val();
	var dataString = 'process=deleted' + '&id_producto=' + id_producto;
	$.ajax({
		type : "POST",
		url : "borrar_producto.php",
		data : dataString,
		dataType : 'json',
		success : function(datax) {
			display_notify(datax.typeinfo, datax.msg);
			setInterval("location.reload();", 3000);
			$('#deleteModal').hide();
		}
	});
}

/*$(document).on("keyup","#total_efectivo", function()
{
	var total_corte = $("#total_corte").val();
	var total_efectivo = $(this).val();

	if(total_efectivo == total_corte)
	{
		$("#diferencia").val("0.0");
		$("#id_diferencia").text("0.00");
    $("#sobrante").text("0.00");
    $("#faltante").text("0.00");
	}
	else if(total_efectivo > total_corte)
	{
		var valor = total_efectivo - total_corte;
		$("#diferencia").val(round(valor, 2));
		$("#id_diferencia").text(round(valor, 2));
    $("#sobrante").text(round(valor, 2));
    $("#faltante").text("0.00");
	}
	else if(total_corte > total_efectivo)
	{
		var valor = total_corte - total_efectivo;
		$("#diferencia").val(round(valor, 2));
		$("#id_diferencia").text(round(valor, 2));
    $("#sobrante").text("0.00");
    $("#faltante").text(round(valor, 2));
	}
})*/


$(document).on("keyup","#saldo_caja", function()
{
	var saldo_caja = $("#caja_saldo").val();
  console.log(saldo_caja);
	var total_efectivo = $(this).val();

	if(total_efectivo == saldo_caja)
	{
    $("#sobrante").val("0");
    $("#faltante").val("0");
    $(".sobrante").text("0.00");
    $(".faltante").text("0.00");
	}
	else if(total_efectivo > saldo_caja)
	{
		var valor = total_efectivo - saldo_caja;
    $("#sobrante").val(round(valor, 2));
    $("#faltante").val("0");
    $(".sobrante").text(round(valor, 2));
    $(".faltante").text("0.00");
    $("#caja_saldo1").val(total_efectivo);
	}
	else if(saldo_caja > total_efectivo)
	{
		var valor = saldo_caja - total_efectivo;
    $("#sobrante").val("0");
    $("#faltante").val(round(valor, 2));
    $(".sobrante").text("0.00");
    $(".faltante").text(round(valor, 2));
    $("#caja_saldo1").val(total_efectivo);
	}
})

/*function corte()
{
  var monto_caja = 0;
  $("#table_t tbody tr").each(function()
  {
    monto_caja = $("#saldo_caja").val();
  });
  console.log(monto_caja);

  var n_remesa = $("#n_remesa").val();
  console.log(n_remesa);
  if(n_remesa != "")
  {
    if(n_remesa != 0)
    {
      if(monto_caja != "")
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
            url         : 'corte_caja_diario.php',
            cache       : false,
            data        : formdata ? formdata : form.serialize(),
            contentType : false,
            processData : false,
            dataType : 'json',
            success: function(datax)
            {
    		    display_notify(datax.typeinfo, datax.msg)
    		    if(datax.typeinfo == "Success")
    		    {
    	          	var id_corte=datax.id_corte;
    	          	//imprimir_corte(id_corte)
    	          	setInterval("reload1();", 1000);
    		    }
    	    }
        });
      }
      else
      {
        display_notify("Error","Debe de ingresar un saldo de caja");
      }
    }
    else
    {
      display_notify("Error","Debe de ingresa el Número de remesa");
    }
  }
  else
  {
    display_notify("Error","Debe de ingresa el Número de remesa");
  }
}
*/
function total()
{
	var tipo_corte = $("#tipo_corte").val();
	var t_t = parseFloat($("#total_tike").val());
	var t_f = parseFloat($("#total_factura").val());
	var t_c = parseFloat($("#total_credito").val());
	var t_e_c = parseFloat($("#total_entrada").val());
	var t_s_c = parseFloat($("#total_salida").val());
	var t_dev = parseFloat($("#total_dev").val());
	var t_nc = parseFloat($("#total_nc").val());
	//console.log(t_dev);
	var m_p = parseFloat($("#monto_apertura").val());
	var m_c = parseFloat($("#monto_ch").val());
	//var d_t = d_g + d_e;
	//console.log(m_c);
	var total_all = 0;
	if(tipo_corte == "C")
	{
		var total_c = t_t + t_f + t_c + m_p + m_c + t_e_c - t_s_c ;
		total_all = round(total_c, 2);
    //console.log(total_c);
	}
	else if(tipo_corte == "X")
	{
		var total_x = t_t + t_f + t_c  + m_p;
		total_all = round(total_x, 2);
	}
	else if(tipo_corte == "Z")
	{
		var total_z = t_t + t_f + t_c  + m_p;
		total_all = round(total_z, 2);
	}
	//alert(total_all);
  //console.log(total_all);

	$("#total_corte").val(total_all);
	$("#id_total_general").text(total_all);
	$("#id_diferencia").text("-"+total_all);
	$("#id_total").text(total_all);
}

function imprimir_corte(id_corte){
	var datoss = "process=imprimir"+"&id_corte="+id_corte;
	$.ajax({
		type : "POST",
		url :"corte_caja_diario.php",
		data : datoss,
		dataType : 'json',
		success : function(datos) {
			var sist_ope = datos.sist_ope;
			var dir_print=datos.dir_print;
			var shared_printer_win=datos.shared_printer_win;
			var shared_printer_pos=datos.shared_printer_pos;

				if (sist_ope == 'win') {
					$.post("http://"+dir_print+"printcortewin1.php", {
						datosvale: datos.movimiento,
						shared_printer_win:shared_printer_win,
						shared_printer_pos:shared_printer_pos,
					})
				} else {
					$.post("http://"+dir_print+"printcorte1.php", {
						datosvale: datos.movimiento
					});
				}

		}
	});
}

function reimprimir()
{
	var id_corte = $("#id_corte").val();
	imprimir_corte(id_corte);
	$('#viewModal').hide();
	setInterval("location.reload();", 500);
}
