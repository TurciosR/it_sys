<?php
include ("_core.php");
function initial(){
	$id_contrato = $_REQUEST ['id_contrato'];
	$id_sucursal=$_SESSION['id_sucursal'];
	$sql="SELECT contrato.*, clientes.nombre FROM contrato JOIN clientes
	ON contrato.id_cliente=clientes.id_cliente
	WHERE id_contrato='$id_contrato' and contrato.id_sucursal='$id_sucursal'
	";
	$result = _query( $sql );
	$count = _num_rows( $result );
	$row = _fetch_array( $result );
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Anular factura</h4>
</div>
<div class="modal-body">
	<div class="wrapper wrapper-content  animated fadeInRight">
			<div class="row">
        <div class="col-md-12">
            <div class="form-group has-info single-line">
                <label class="control-label">Fecha</label>
                <input type="text" name="fecha" class="form-control" id="fecha" value="<?php echo ED($row['fecha_inicio']);?>">
            </div>
        </div>
      </div>
	</div>
</div>
<input type='hidden' nombre='id_contratox' id='id_contratox' value='<?php echo $id_contrato;?>'>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btnUpdate">Guardar</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

</div>
<!--/modal-footer -->
<style media="screen">
  .date_field {position: relative; z-index:100;}
</style>
<script type="text/javascript">
  $("#fecha").datepicker({
    format: "dd-mm-yyyy",
  });
</script>
<?php

}
function editar_contrato()
{
  $id_contrato = $_POST["id_contrato"];
  $fecha = $_POST["fecha"];

  $sql_contrato = _query("SELECT * FROM contrato WHERE id_contrato = '$id_contrato'");
  $cuenta = _num_rows($sql_contrato);
  if($cuenta > 0)
  {
    $row = _fetch_array($sql_contrato);
    $monto = $row["monto"];
    $iva = $row["iva"];
    $periodo = $row["periodo"];
    $numero_doc = $row["numero_doc"];
    $forma = $row["forma"];
    $sql_cuotas = _query("SELECT * FROM cuota_contrato WHERE id_contrato = '$id_contrato'");
    $cuenta_cc = _num_rows($sql_cuotas);

    $wp = "id_contrato='".$id_contrato."'";
    $delete = _delete("cuota_contrato",$wp);

    $sub_total = round($monto + $iva, 3);
    // $fecha = $row["fecha_inicio"];
    list($a,$m,$d) = explode("-", $fecha);
    $mes = $m;
    $anhio = $a;
    if($periodo == 'MENSUAL')
    {
      $m = $m;
    }
    if($periodo == 'BIMESTRAL')
    {
      $m = $m+1;
    }
    if($periodo == 'TRIMESTRAL')
    {
      $m = $m+2;
    }
    if($periodo == 'SEMESTRAL')
    {
      $m = $m+5;
    }
    if($periodo == 'ANUAL')
    {
      $a = $a+1;
    }

    $ultimo_dia = date("d",(mktime(0,0,0,$m+1,1,$a)-1));
    $fecha_vence = $a."-".$m."-".$ultimo_dia;

    $tabla = "cuota_contrato";
    $lista = array(
      'id_contrato' => $id_contrato,
      'monto' =>$sub_total,
      'mes' => $mes,
      'anhio' => $anhio,
      'fecha_vence' => $fecha_vence,
    );
    $insert_cuota = _insert($tabla, $lista);

    if($forma == 'PF')
    {
      for ($i=0; $i < $cuenta_cc ; $i++)
      {

      }
    }
    if ($insert_cuota)
    {
      _commit(); // transaction is committed
      $xdatos['typeinfo']='Success';
      $xdatos['msg']='Contrato Numero:'.$numero_doc.'  Guardado con Exito !';
      $xdatos['id_contrato']=$id_contrato;
      $xdatos['process'] = 'insert';
    }
    else
    {
      _rollback(); // transaction rolls back
      $xdatos['typeinfo']='Error';
      $xdatos['msg']='Contrato no pudo ser registrada!'._error();
    }
  }
  else
  {
    _rollback(); // transaction rolls back
    $xdatos['typeinfo']='Error';
    $xdatos['msg']='No se ha encontrado el contrato!'._error();
  }
  echo json_encode($xdatos);
}

if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'formDelete' :
				initial();
				break;
			case 'deleted' :
				deleted();
				break;
		}
	}
}

?>
