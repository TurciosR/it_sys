<?php
include ("_core.php");

function initial(){
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$id_sucursal=$_SESSION['id_sucursal'];
	date_default_timezone_set('America/El_Salvador');
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	//permiso del script

	//include ('facturacion_funcion_imprimir.php');
	//$sql="SELECT * FROM factura WHERE id_factura='$id_factura'";
	$sql_apertura = _query("SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal'");
	$cuenta = _num_rows($sql_apertura);
	$row_apertura = _fetch_array($sql_apertura);
	$id_apertura = $row_apertura["id_apertura"];
	$turno = $row_apertura["turno"];
	$fecha_apertura = $row_apertura["fecha"];
	$hora_apertura = $row_apertura["hora"];
	$monto_apertura = $row_apertura["monto_apertura"];
	$hora_actual = date('H:i:s');
	if($cuenta > 0)
	{

	$id_movimiento = $_REQUEST["id_movimiento"];
	$sql_movi = _query("SELECT * FROM mov_caja WHERE id_movimiento = '$id_movimiento' AND turno='$turno' AND id_empleado='$id_user'");
	$cuenta2=_num_rows($sql_movi);
	if($cuenta2>0 || $admin=='1'){
	$sql_movimiento = _query("SELECT * FROM mov_caja WHERE id_movimiento = '$id_movimiento'");
	$rr = _fetch_array($sql_movimiento);
	$entrada = $rr["entrada"];
	$salida = $rr["salida"];
	$viatico = $rr["viatico"];
	$concepto = $rr["concepto"];
	$monto = $rr["valor"];
	$detalle = "";
	if($entrada == 1 && $salida == 0 && $viatico == 0)
	{
		$detalle = "Entrada";
		$alert = "alert-success";
	}
	else if($salida == 1 && $entrada == 0 && $viatico == 0)
	{
		$detalle = "Salida";
		$alert = "alert-warning";
	}
	else if($salida == 0 && $entrada == 0 && $viatico == 1)
	{
		$detalle = "Viatico";
		$alert = "alert-warning";
	}
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Eliminar Movimiento</h4>
</div>
<div class="modal-body">
	<!--div class="wrapper wrapper-content  animated fadeInRight"-->
	<div class="row" id="row1">
		<!--div class="col-lg-12"-->
		<?php
					//permiso del script
			if ($links!='NOT' || $admin=='1' ){
		?>
		<div class="row">
			<div class="col-md-12">
	          <div class="form-group has-info text-center alert <?php echo $alert; ?>">
	          	<label><?php echo $detalle; ?></label>
	          </div>
			</div>
    	</div>
		<div class="row">
			<div class="col-md-12">
	          <table class="table table-border">
	          	<tr>
	          		<th>Concepto</th>
	          		<th><?php echo $concepto; ?></th>
	          	</tr>
	          	<tr>
	          		<th>Monto</th>
	          		<th><?php echo $monto?></th>
	          	</tr>
	          </table>
			</div>
    	</div>
	</div>
		<!--/div-->
		<!--/div-->
	<input type="hidden" name="id_empleado" id="id_empleado" value="<?php echo $empleado;?>">
	<input type="hidden" name="turno" id="turno" value="<?php echo $turno;?>">
	<input type="hidden" name="id_apertura" id="id_apertura" value="<?php echo $id_apertura;?>">
	<input type="hidden" name="id_movimiento" id="id_movimiento" value="<?php echo $id_movimiento;?>">
	<input type="hidden" name="monto" id="monto" value="<?php echo $monto;?>">
	<input type="hidden" name="tipo_movimiento" id="tipo_movimiento" value="<?php echo $detalle;?>">
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-warning" id="btnEliminar">Eliminar</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div>
<!--/modal-footer -->

<?php

} //permiso del script
else
{
	echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
}
}
else
{
	echo "<div></div><br><br><div class='alert alert-warning text-center'> Este movimiento no fue realizado en su turno. .</div>";
}
}
else
{
	echo "<div></div><br><br><div class='alert alert-warning text-center'>No se ha encontrado una apertura vigente.</div>";
}
}
function eliminar()
{
	date_default_timezone_set("America/El_Salvador");
	$id_movimiento = $_POST["id_movimiento"];
	$id_apertura = $_POST["id_apertura"];
	$monto = $_POST["monto"];
	$tipo_movimiento = $_POST["tipo_movimiento"];
	_begin();
	$tabla = "mov_caja";
	$where_mov = "id_movimiento='".$id_movimiento."'";
	$delete = _delete($tabla, $where_mov);
	if($delete)
	{
		$mont=_query("SELECT monto_ch_actual FROM apertura_caja WHERE id_apertura='$id_apertura'");
		$monto_a=_fetch_array($mont);
		$monto_ap=$monto_a["monto_ch_actual"];
		if($tipo_movimiento=="Entrada"){
			$opera_a=$monto_ap-$monto;
		}
		if($tipo_movimiento=="Salida"){
			$opera_a=$monto_ap+$monto;
		}
		if($tipo_movimiento=="Viatico"){
			$opera_a=$monto_ap+$monto;
			$tabla_detalle = "mov_caja_detalle";
			$where_detalle = "id_mov_caja='".$id_movimiento."'";
			$delete_detalle = _delete($tabla_detalle, $where_detalle);
		}
		$tabla_aper = "apertura_caja";
		$form_aper = array(
			'monto_ch_actual' => $opera_a,
		);
		$where_m = "id_apertura='".$id_apertura."'";
		$update_apertura = _update($tabla_aper, $form_aper, $where_m);
		if($update_apertura)
		{ _commit();
			$xdatos['typeinfo']='Success';
			$xdatos['msg']='Movimiento eliminado correctamente !';
			$xdatos['process']='eliminar';
		}else{
			_rollback();
			$xdatos['typeinfo']='Error';
			$xdatos['msg']='No se pudo actualizar el monto !';
		}
	}
	else
	{ _rollback();
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Error al eliminar el movimiento !'._error();
	}
	echo json_encode($xdatos);
}
if (! isset ( $_REQUEST ['process'] )) {
	initial();
}else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'formDelete' :
				initial();
				break;
			case 'eliminar' :
				eliminar();
				break;
		}
	}
}

?>
