<?php
include_once "_core.php";
include('num2letras.php');
include('facturacion_funcion_imprimir.php');
//include("escpos-php/Escpos.php");
function initial()
{
    $id_reserva=$_REQUEST["id_reserva"];
    //permiso del script
    $id_user=$_SESSION["id_usuario"];
    $admin=$_SESSION["admin"];
    $uri = $_SERVER['SCRIPT_NAME'];
    $filename=get_name_script($uri);
    $links=permission_usr($id_user, $filename);
    $fecha=date('d-m-Y');
    $id_sucursal=$_SESSION['id_sucursal'];
    $sql0="SELECT id_reserva,id_cliente,total,nombre,telefono, numero_doc, saldo, abono, fecha_doc
	FROM reservas
	WHERE id_reserva='$id_reserva'";
    $result = _query($sql0);
    $numrows= _num_rows($result);
    for ($i=0;$i<$numrows;$i++) {
        $row = _fetch_array($result);
        $total=$row['total'];
        $numero_doc=$row['numero_doc'];
        $saldo_pend=$row['saldo'];
        $fecha_doc=$row['fecha_doc'];
        $id_cliente=$row['id_cliente'];
        $telefono1=$row["telefono"];
        $nombre=$row["nombre"];
        $id_reserva=$row['id_reserva'];
    }

    $sql="SELECT * FROM clientes WHERE	id_cliente='$id_cliente'";
    $result=_query($sql);
    $count=_num_rows($result);
    if ($count > 0) {
        for ($i = 0; $i < $count; $i ++) {
            $row = _fetch_array($result);
            $id_cliente=$row["id_cliente"];
            $nit=$row["nit"];
            $dui=$row["dui"];
            $telefono2=$row["telefono2"];
            $registro=$row["nrc"];
            $direccion=$row["direccion"];

        }
      }

      //array de tipo_pagos
      $sql4="SELECT * FROM tipo_pago WHERE  inactivo=0 AND alias_tipopago!='CRE' ";
      $result4=_query($sql4);
      $count4=_num_rows($result4);
      for ($a=0;$a<$count4;$a++){
      	$row4=_fetch_array($result4);
      	$id4=$row4['id_tipopago'];
      	$alias_tp=$row4['alias_tipopago'];
      	$description4=$row4['descripcion']." |".$alias_tp;
      	$array4[$id4] = $description4;
      }
     ?>
<div class="modal-header">

	<h4 class="modal-title">Abonar Reserva</h4>
</div>
			<?php
                if ($links!='NOT' || $admin=='1') {
                    ?>
                    <div class="modal-body">
                      <div class="wrapper wrapper-content  animated fadeInRight">

                        <div class="row">
                          <input type='hidden' name='id_factura' id='id_factura' value='<?php echo $id_reserva ?>'>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label><h5 class='text-navy'>Numero Reserva: </h5></label>
                            </div>
                          </div>
                          <div class="col-md-6" >
                            <div class="form-group text-navy" id='fact_num'><?php echo $numero_doc ?></div>
                          </div>
                        </div>
                        <div id="select_container" class="row">
                          <input type='hidden' name='id_factura' id='id_factura' value=''>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label><h5 class='text-navy'>Condici&oacute;n Pago</h5></label>
                            </div>
                          </div>
                          <div class="col-md-6" >
                            <div class="form-group">
                      			<?php
                      			$nombre_select1="select_tipo_pago";
                      			$idd1=1;
                      			$style='width:100%';
                      			$select1=crear_select2($nombre_select1,$array4,$idd1,$style);
                      			echo $select1;
                      			?>
                      			</div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label><h5 class='text-navy'>Total Productos $: </h5></label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <input type="text" id="facturado" name="facturado" value='<?php echo $total ?>'  class="form-control decimal" readonly >
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label><h5 class='text-navy'>Saldo Pediente $: </h5></label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <input type="text" id="saldo_pendiente" name="saldo_pendiente" value='<?php echo $saldo_pend ?>'  class="form-control decimal" readonly >
                            </div>
                          </div>
                        </div>
                        <div class="row" id='ccf'>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label><strong><h5 class='text-navy'>Nombre Cliente: </h5></strong></label>
                            </div>
                          </div>
                          <div class="col-md-8">
                            <div class="form-group">
                             <input type="text" id='nombreape' name='nombreape' value='<?php echo $nombre ?>'  class="form-control" >
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>DUI Cliente</label>
                            </div>
                          </div>
                          <div class="col-md-8">
                            <div class="form-group">
                              <input type='text' placeholder='dui' class='form-control' id='dui2' name='dui2' value='<?php echo $dui ?>'>
                            </div>
                          </div>
                          <div class="col-md-4">
                                  <div class="form-group">
                                    <label>Telefonos Cliente</label>
                                  </div>
                          </div>
                          <div class="col-md-4">
                                  <div class="form-group">
                                    <input type='text' placeholder='tel1' class='form-control' id='tele1' name='tele1' value='<?php echo $telefono1 ?>'>
                                  </div>
                          </div>
                          <div class="col-md-4">
                                  <div class="form-group">
                                    <input type='text' placeholder='tel2' class='form-control' id='tele2' name='tele2' value='<?php echo $telefono2 ?>'>
                                  </div>
                          </div>
                        </div>
                        <div class="row" id='div_abono'>
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label>Abono $</label>
                                  </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <input type="text" id="abono" name="abono" value=""  class="form-control decimal">
                                  </div>
                                  </div>
                        </div>
                                  <div class="row" id='tipo_pago_efectivo'>
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label>Efectivo $</label>
                                  </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <input type="text" id="efectivo" name="efectivo" value=""  class="form-control decimal">
                                  </div>
                                  </div>

                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label>Cambio $</label>
                                  </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <input type="text" id="cambio" name="cambio" value=0 placeholder="cambio" class="form-control decimal" readonly >
                                  </div>
                            </div>
                          </div>
                          <div class="row" id='tipo_pago_tarjeta'>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>Número Tarjeta</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <input type="text" id="numero_tarjeta" name="numero_tarjeta" placeholder="Número Tarjeta" value=""  class="form-control decimal">
                              </div>
                            </div>
                          <!--/div>
                          <div class="row"-->
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>Emisor</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <input type="text" id="emisor" name="emisor" value=0 placeholder="Emisor" class="form-control" >
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>No. Transacción (Voucher)</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <input type="text" id="voucher" name="voucher" value=0 placeholder="No. Transacción (Voucher)" class="form-control decimal"  >
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group" id='mensajes'></div>
                          </div>
                        </div>
                      </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btnAbonar">Abonar</button>
                        <button type="button" class="btn btn-primary" disabled id="btnPrintFac">Imprimir</button>
                        <button type="button" class="btn btn-warning" id="btnEsc">Salir</button>
                      </div>
                      </div>
                    </div>

<script type="text/javascript">
	$(document).ready(function(){
    $('#viewModal').modal({backdrop: 'static',keyboard: false});
		$(".select2").select2(
    );
		$("#monto").numeric({negative:false,decimalPlaces:2});
    $("#abono").numeric({negative:false,decimalPlaces:2})
    $("#efectivo").numeric({negative:false,decimalPlaces:2})

    var tipo_pago=$('select#select_tipo_pago  option:selected').text();
    var tipopago = tipo_pago.split("|");
    var alias_tipopago =tipopago[1];
    if  (alias_tipopago =="CON"){
      $('#tipo_pago_tarjeta').hide();
      $('#tipo_pago_efectivo').show();

    }
    if  (alias_tipopago =="TAR"){
      $('#tipo_pago_efectivo').hide();
      $('#tipo_pago_tarjeta').show();
    }

    $("#select_tipo_pago").on('change',function(){
      $('#tipo_pago_tarjeta').hide();
      $('#tipo_pago_efectivo').hide();

      var tipo_pago=$('select#select_tipo_pago  option:selected').text();
      var tipopago = tipo_pago.split("|");
      var alias_tipopago =tipopago[1];
      if  (alias_tipopago =="CON"){
        $('#tipo_pago_tarjeta').hide();
        $('#tipo_pago_efectivo').show();
        $('#mensajes').show();

      }
      if  (alias_tipopago =="TAR"){
        $('#tipo_pago_efectivo').hide();
        $('#tipo_pago_tarjeta').show();
        $('#mensajes').hide();
        $('#efectivo').val('');
        $('#abono').val('');
        $('#cambio').val(0);
      }
   });
	});

  $(document).on("keyup", "#efectivo", function() {
    total_efectivo();
  });

  $(document).on("keyup", "#abono", function() {
    total_efectivo();
    saldo_pendiente=$('#saldo_pendiente').val();
    saldo_pendiente = parseFloat(saldo_pendiente);
    saldo_pendiente = round(saldo_pendiente,2);
    abono = $('#abono').val();
    abono = parseFloat(abono);
    abono = round(abono,2);
    if(abono>saldo_pendiente)
    {
      $(this).val(saldo_pendiente);
    }
  });
  function total_efectivo() {
    var facturado =$('#facturado').val();
    var efectivo = parseFloat($('#efectivo').val());
    var abono = parseFloat($('#abono').val());
    var mensaje="";
    if (isNaN(parseFloat(efectivo))) {
      efectivo = 0.0;
    }
    if (isNaN(parseFloat(abono))) {
    abono = 0.0;
    }
    if (  abono>0.0 && efectivo>=abono){
      var cambio = efectivo - abono;
    }
    else {
        var cambio = 0.0;
        mensaje="<h3 class='text-danger'>" + "Falta dinero !!!" + "</h3>";

    }
    var cambio = round(cambio, 2);
    //alert("cambio:"+cambio)


    var tipo_pago=$('select#select_tipo_pago  option:selected').text();
    var tipopago = tipo_pago.split("|");
    var alias_tipopago =tipopago[1];
    if(alias_tipopago=='CON')
    {
      var cambio_mostrar = cambio.toFixed(2);
      $('#cambio').val(cambio_mostrar);
      $('#mensajes').html(mensaje);

      if(mensaje!="")
      {

      }
    }
    else
    {
      $('#mensajes').html('');
    }
}

$(document).on("click", "#btnAbonar", function(event) {
  abonar();
});
//Impresion
function abonar(){

  var numero_doc = $(".modal-body #fact_num").html(); //del modal
  var print = 'abonar';
  var pass = true;
  var pass2 = true;
  var total=  $(".modal-body #facturado").val();
  var tipo_impresion ="RES";
  var id_factura=$(".modal-body #id_factura").val();
  var tipo_pago=$('select#select_tipo_pago  option:selected').text();
  var tipopago = tipo_pago.split("|");
  var alias_tipopago =tipopago[1];
  var abono=$('#abono').val()

  var dataString = 'process=' + print + '&numero_doc=' + numero_doc + '&tipo_impresion=' + tipo_impresion
    dataString+=  '&num_doc_fact=' + id_factura+ '&total=' + total;
    dataString+= '&abono='+abono;

  if  (alias_tipopago =="TAR"){
      var  emisor=$('#emisor').val();
      var voucher=$('#voucher').val();
      var numero_tarjeta=$('#numero_tarjeta').val();
      if( emisor =="" || numero_tarjeta == "" || voucher=="")
      {
        pass2 = false;
      }
      dataString+=  '&emisor=' + emisor+'&voucher='+voucher+'& numero_tarjeta='+ numero_tarjeta;
  }

  if (tipo_impresion=="RES"){
    dui=$('.modal-body #dui2').val();
    tel1=$('.modal-body #tele1').val();
    tel2=$('.modal-body #tele2').val();
    nombreape=$('.modal-body #nombreape').val();
    if(dui =="" || tel1 == "" || nombreape=="" || abono == ""|| abono <=0)
    {
      pass = false;
    }
    dataString +='&dui=' + dui+ '&tel1=' + tel1+ '&tel2=' + tel2+'&nombreape=' + nombreape;
  }


  if(pass && pass2){
  $.ajax({
    type: 'POST',
    url: "admin_reservas_abonar.php",
    data: dataString,
    dataType: 'json',
    success: function(xdatos) {
      display_notify(xdatos.typeinfo, xdatos.msg);
      $('#nombreape').prop('readOnly', 'true');
      $('#dui2').prop('readOnly', 'true');
      $('#tele1').prop('readOnly', 'true');
      $('#tele2').prop('readOnly', 'true');

      $('#abono').prop('readOnly', 'true');
      $('#efectivo').prop('readOnly', 'true');

      $('#numero_tarjeta').prop('readOnly', 'true');
      $('#emisor').prop('readOnly', 'true');
      $('#voucher').prop('readOnly', 'true');

      $('#btnPrintFac').prop('disabled', false);
      $('#btnAbonar').prop('disabled', 'true');
      $('#select_container').prop('hidden', 'true');

    }
  });
  }
  else
  {
      display_notify("Error", "Por favor complete los datos solicitados");
  }
}
  $(document).on("click", "#btnPrintFac", function(event) {
    imprime1();
  });
  //Impresion
  function imprime1(){

    var numero_doc = $(".modal-body #fact_num").html(); //del modal
    var print = 'imprimir_fact';
    var pass = true;
    var pass2 = true;
    var total=  $(".modal-body #facturado").val();
    var tipo_impresion ="RES";
    var id_factura=$(".modal-body #id_factura").val();
    var tipo_pago=$('select#select_tipo_pago  option:selected').text();
    var tipopago = tipo_pago.split("|");
    var alias_tipopago =tipopago[1];
    var abono=$('#abono').val()

  	var dataString = 'process=' + print + '&numero_doc=' + numero_doc + '&tipo_impresion=' + tipo_impresion
      dataString+=  '&num_doc_fact=' + id_factura+ '&total=' + total;
      dataString+= '&abono='+abono;

    if  (alias_tipopago =="TAR"){
        var  emisor=$('#emisor').val();
        var voucher=$('#voucher').val();
        var numero_tarjeta=$('#numero_tarjeta').val();
        if( emisor =="" || numero_tarjeta == "" || voucher=="")
        {
          pass2 = false;
        }
        dataString+=  '&emisor=' + emisor+'&voucher='+voucher+'& numero_tarjeta='+ numero_tarjeta;
    }

  	if (tipo_impresion=="RES"){
  		dui=$('.modal-body #dui2').val();
  		tel1=$('.modal-body #tele1').val();
      tel2=$('.modal-body #tele2').val();
  		nombreape=$('.modal-body #nombreape').val();
      if(dui =="" || tel1 == "" || nombreape=="" || abono == ""|| abono <=0)
      {
        pass = false;
      }
  		dataString +='&dui=' + dui+ '&tel1=' + tel1+ '&tel2=' + tel2+'&nombreape=' + nombreape;
   	}


    if(pass && pass2){
    $.ajax({
      type: 'POST',
      url: "admin_reservas_abonar.php",
      data: dataString,
      dataType: 'json',
      success: function(datos) {
  			var sist_ope = datos.sist_ope;
        var dir_print=datos.dir_print;
        var shared_printer_win=datos.shared_printer_win;
  			var shared_printer_pos=datos.shared_printer_pos;
        var headers=datos.headers;
        var footers=datos.footers;
        var efectivo_fin = parseFloat($('#efectivo').val());
        var cambio_fin = parseFloat($('#cambio').val());
        var a_pagar=  $(".modal-body #a_pagar").val();
        //estas opciones son para generar recibo o factura en  printer local y validar si es win o linux
        if (tipo_impresion == 'RES') {
        				if (sist_ope == 'win') {
                  $.post("http://"+dir_print+"printreswin1.php", {
        						datosventa: datos.facturar,
        						efectivo: efectivo_fin,
        						cambio: cambio_fin,
        						shared_printer_pos:shared_printer_pos,
                    headers:headers,
                    footers:footers,
                    a_pagar:a_pagar,
                  })
                } else {
                  $.post("http://"+dir_print+"printres1.php", {
                    datosventa: datos.facturar,
                    efectivo: efectivo_fin,
                    cambio: cambio_fin,
                    headers:headers,
                    footers:footers,
                    a_pagar:a_pagar,
                  }, function(data, status) {
                    if (status != 'success') {}
                  }
                );
                }
        }
      }
    });
    }
    else
    {
        display_notify("Error", "Por favor complete los datos solicitados");
    }
  }

  function round(value, decimals) {
    return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
  }
  $(document).on("click", "#btnEsc", function (event) {

  		reload2();
  });
  function reload2() {
    location.href = 'admin_reserva_rangos.php';
  }
</script>
<?php
                } //permiso del script
    else {
        echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
    }
}

function abonar()
{

  _begin();
  $hora=date("H:i:s");
  $fecha=date('Y-m-d');

  $id_sucursal=$_SESSION['id_sucursal'];
  $id_usuario=$_SESSION['id_usuario'];
  //turno de caja
  $sql_turno="SELECT id_apertura,  turno FROM apertura_caja
  WHERE  vigente=1 AND  id_sucursal='$id_sucursal'";
  $result_turno= _query($sql_turno);
 $rows_turno=_fetch_array($result_turno);
 $nrows_turno=_num_rows($result_turno);
 $turno=$rows_turno['turno'];
 $id_apertura=$rows_turno['id_apertura'];

 $alias_tipodoc='RES';

  $numero_doc = $_POST['numero_doc'];
  $totalfact = $_POST['total'];
  $tipo_impresion= $_POST['tipo_impresion'];
  $id_factura= $_POST['num_doc_fact'];
  $abono= $_POST['abono'];
  $dui= $_POST['dui'];
  $tel1= $_POST['tel1'];
  $tel2= $_POST['tel2'];
  $nombreape= $_POST['nombreape'];

  $tipo_entrada_salida="RESERVA";


  $voucher=-1;
  $id_pago=0;

  $val1=0;
  $val2=0;
  $val3=0;




  if (isset($_POST['numero_tarjeta'])){
    $numero_tarjeta=$_POST['numero_tarjeta'];
  }
  if (isset($_POST['emisor'])){
    $emisor=$_POST['emisor'];
  }
  /*insertar pago con targeta*/
  if (isset($_POST['voucher'])){
    $voucher=$_POST['voucher'];
      $fecha_movimiento=$fecha;
      $table_pt= 'pago_tarjeta';
      $form_data_pt = array(
          'idtransace' => $id_factura,
          'voucher' => $voucher,
          'emisor' => $emisor,
          'fecha' =>$fecha_movimiento,
          'numero_tarjeta' => $numero_tarjeta,
          'monto' => $abono,
          'alias_tipodoc'=>'RES',
      );
      /*$where_clause="WHERE idtransace='$id_factura' AND alias_tipodoc='RES'";*/
      $actualizar = _insert($table_pt,$form_data_pt);
      if($actualizar)
      {

      }
      else {
        $val1=1;
      }
        $id_pago= _insert_id();
  }

  $sql_fact="SELECT * FROM reservas WHERE id_reserva='$id_factura'";
  $result_fact=_query($sql_fact);
  $row_fact=_fetch_array($result_fact);
  $nrows_fact=_num_rows($result_fact);
  /*actualizar saldo pendiente y abonar*/
  if($nrows_fact>0){

    $concepto='RESERVA PRODUCTO';
    //Insertar para generar vale por devolucion
    $table_vale='mov_caja';
    $form_data_vale = array(
    'idtransace' => $id_factura,
    'alias_tipodoc'=>$alias_tipodoc,
    'numero_doc'=>$numero_doc,
    'fecha' => $fecha,
    'hora' => $hora,
    'valor' => $abono,
    'turno' => $turno,
    'id_apertura' => $id_apertura,
    'concepto' => $concepto,
    'id_empleado' =>  $id_usuario,
    'id_sucursal' => $id_sucursal,
    'entrada'=>1,
    );
    $insertar_vale = _insert($table_vale, $form_data_vale);

    if($insertar_vale)
    {

    }
    else {
      $val2=1;
    }

    $fecha_movimiento=$row_fact['fecha_doc'];
    $total=$row_fact['saldo'];
    $total_a=$row_fact['total'];
    $abono_anterior=$row_fact['abono'];
    $total_abono=$abono_anterior+$abono;
    $saldo=$total_a-  $total_abono;

    $saldo=round($saldo,2);
    $total_abono=round($total_abono,2);
    $table_fact= 'reservas';
    $form_data_fact = array(
      'finalizada' => '0',
      'id_pago_tarjeta' => $id_pago,
      'nombre' => $nombreape,
      'telefono'=>$tel1,
      'abono'=>$total_abono,
      'saldo'=>$saldo,

    );

    $where_clause="WHERE id_reserva='$id_factura'";
    $actualizar = _update($table_fact,$form_data_fact, $where_clause );
    if($actualizar)
    {

    }
    else {
      $val3=1;
    }

  }

  if ($val1==0&&$val2==0&&$val3==0){
  	_commit(); // transaction is committed
  	$xdatos['typeinfo']='Success';
  		 $xdatos['msg']='Abono a Reservacion Actualizado !';
  		 $xdatos['process']='insert';
  	}
  	else{
  	_rollback(); // transaction not committed
  		 $xdatos['typeinfo']='Error';
  		 $xdatos['msg']='Abono a Reservacion no pudo ser Actualizado !';
  }

  echo json_encode($xdatos);


}

function imprimir_fact() {
	$numero_doc = $_POST['numero_doc'];
	$totalfact = $_POST['total'];
  $tipo_impresion= $_POST['tipo_impresion'];
  $id_factura= $_POST['num_doc_fact'];
	$abono= $_POST['abono'];
	$dui= $_POST['dui'];
	$tel1= $_POST['tel1'];
	$tel2= $_POST['tel2'];
	$nombreape= $_POST['nombreape'];

  $tipo_entrada_salida="RESERVA";
	$id_sucursal=$_SESSION['id_sucursal'];

	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';

  $headers=""; $footers="";
	$info_facturas=print_ticket_res($id_factura,$dui,$nombreape,$tel1,$tel2);

	$sql_pos="SELECT *  FROM config_pos  WHERE id_sucursal='$id_sucursal' AND alias_tipodoc='RES'";

	$result_pos=_query($sql_pos);
	$row1=_fetch_array($result_pos);

	$headers=$row1['header1']."|".$row1['header2']."|".$row1['header3']."|".$row1['header4']."|".$row1['header5']."|";
	$headers.=$row1['header6']."|".$row1['header7']."|".$row1['header8']."|".$row1['header9']."|".$row1['header10'];
	$footers=$row1['footer1']."|".$row1['footer2']."|".$row1['footer3']."|".$row1['footer4']."|".$row1['footer5']."|";
	$footers.=$row1['footer6']."|".$row1['footer7']."|".$row1['footer8']."|".$row1['footer8']."|".$row1['footer10']."|";
	//directorio de script impresion cliente
	$sql_dir_print="SELECT *  FROM config_dir WHERE id_sucursal='$id_sucursal'";
	$result_dir_print=_query($sql_dir_print);
	$row0=_fetch_array($result_dir_print);
	$dir_print=$row0['dir_print_script'];
	$shared_printer_win=$row0['shared_printer_matrix'];
	$shared_printer_pos=$row0['shared_printer_pos'];

	$nreg_encode['shared_printer_win'] =$shared_printer_win;
	$nreg_encode['shared_printer_pos'] =$shared_printer_pos;
	$nreg_encode['dir_print'] =$dir_print;
	$nreg_encode['facturar'] =$info_facturas;
	$nreg_encode['sist_ope'] =$so_cliente;
	$nreg_encode['headers'] =$headers;
	$nreg_encode['footers'] =$footers;

	echo json_encode($nreg_encode);
}
//functions to load
if (!isset($_REQUEST['process'])) {
    initial();
}
//else {
if (isset($_REQUEST['process'])) {
    switch ($_REQUEST['process']) {
    case 'formEdit':
        initial();
        break;
    case 'val':
        cuentas_b();
        break;
    case 'imprimir_fact':
  		imprimir_fact();
      break;
    case 'abonar':
  		abonar();
      break;
    }

    //}
}
?>
