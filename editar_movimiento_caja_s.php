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
	$sql_apertura = _query("SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal' AND id_empleado = '$id_user'");
	$cuenta = _num_rows($sql_apertura);
	$row_apertura = _fetch_array($sql_apertura);
	$empleado = $row_apertura["id_empleado"];
	$turno = $row_apertura["turno"];
	$fecha_apertura = $row_apertura["fecha"];
	$hora_apertura = $row_apertura["hora"];
	$monto_apertura = $row_apertura["monto_apertura"];

	$hora_actual = date('H:i:s');
	if($cuenta > 0 || $admin=='1')
	{
	$id_movimiento = $_REQUEST["id_movimiento"];
	$sql_movimiento = _query("SELECT * FROM mov_caja WHERE id_movimiento = '$id_movimiento'");
	$rr = _fetch_array($sql_movimiento);
	$entrada = $rr["entrada"];
	$salida = $rr["salida"];
	$viatico = $rr["viatico"];
	$concepto = $rr["concepto"];
	$monto = $rr["valor"];
	$tipo_doc = $rr["tipo_doc"];
	$nombre_p = $rr["nombre_proveedor"];
	$id_apertura = $row_apertura["id_apertura"];
	$numero_doc = $rr["numero_doc"];
	$estado=$rr["estado"];
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
	<h4 class="modal-title">Editar Movimiento</h4>
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
			<div class="col-md-6">
				<div class="form-group has-info single-line">
					<label>Tipo Documento </label>
					<select class="form-control" name="tipo_doc" id="tipo_doc">
						<option <?php if($tipo_doc=="CCF") echo 'selected' ?> value="CCF">Credito Fiscal</option>
						<option <?php if($tipo_doc=="COF") echo 'selected'?> value="COF">Factura</option>
						<option <?php if($tipo_doc=="RE") echo 'selected' ?> value="RE">Recibo</option>
						<option <?php if($tipo_doc=="VAL") echo 'selected' ?> value="VAL">Vale</option>
					</select>
				</div>
			</div>
			<div class="col-md-6">
				<div class="has-info single-line">
					<label>Numero de Documento</label>
					<input type="text" name="n_doc" id="n_doc" class="form-control" value="<?php echo $numero_doc; ?>">
				</div>
			</div>
		</div>
		<div class="row">
					<div class="col-md-6">
	          <div class="form-group has-info single-line">
	          	<label>Concepto</label>
	          	<input type='text'  class='form-control' id='concepto' name='concepto' value="<?php echo $concepto;?>">
	          </div>
					</div>
					<div class="col-md-6">
	          <div class="form-group has-info single-line">
	          	<label>Proveedor/Otro </label> <input type='text'  class='form-control' id='proveedor' name='proveedor' value='<?php echo $nombre_p;?>'>
	          </div>
					</div>
    	</div>
    	<div class="row">
    		<div class="col-md-6">
	            <div class="form-group has-info single-line">
	          	<label>Monto </label>
							<input type='hidden' id='monto_anti' name='monto_anti' value="<?php echo $monto; ?>">
							<input type='text'  class='form-control numeric' id='monto' name='monto' value="<?php echo $monto; ?>">
	           </div>
			</div>
			<div class="col-md-6 caja_iva" hidden>
	            <div class="form-group has-info single-line">
	          	<label>IVA </label> <input type='text'  class='form-control numeric' id='iva' name='iva' readonly>
	            </div>
			</div>
    		<div class="col-md-6">
    			<div class="form-group has-info single-line">
					<label>Proceso </label>
					<select class="form-control" name="proceso" id="proceso" onchange="proceso_d()">
						<option value="1" <?php if($estado=="1"){echo "selected";}?>>Pendiente</option>
						<option value="0" <?php if($estado=="0"){echo "selected";}?>>Finalizado</option>
					</select>
				</div>
    		</div>
    	</div>
	</div>
	<input type="hidden" name="id_movimiento" id="id_movimiento" value="<?php echo $id_movimiento;?>">
	<input type="hidden" name="id_apertura" id="id_apertura" value="<?php echo $id_apertura;?>">

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btnEditar_s">Guardar</button>
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
	echo "<div></div><br><br><div class='alert alert-warning text-center'>No se ha encontrado una apertura vigente.</div>";
}
}

function editar()
{
	date_default_timezone_set("America/El_Salvador");
	$id_movimiento = $_POST["id_movimiento"];
		$id_apertura = $_POST["id_apertura"];
	$concepto = $_POST["concepto"];
	$monto_anti = $_POST["monto_anti"];
	$monto = $_POST["monto"];
	$id_sucursal=$_SESSION['id_sucursal'];
	$proveedor = $_POST["proveedor"];
	$tipo_doc = $_POST["tipo_doc"];
	$n_doc = $_POST["n_doc"];
	$estado = $_POST["estado"];
	$autoriza = "Lic. Silvia de Melendez";

	$fecha = date("Y-m-d");
	$hora = date("H:i:s");
	$iva = 0;
	if($tipo_doc == "CCF")
	{
		$iva = $monto - ($monto / 1.13);
	}
	_begin();
	$tabla = "mov_caja";
	$form_data = array(
		'valor' => $monto,
		'concepto' => $concepto,
		'tipo_doc' => $tipo_doc,
		'numero_doc' => $n_doc,
		'iva'	=>$iva,
		'estado'=>$estado
		);
	$where_mov = "id_movimiento='".$id_movimiento."'";
	$update = _update($tabla, $form_data, $where_mov);
	if($update)
	{
		$mont=_query("SELECT monto_ch_actual FROM apertura_caja WHERE id_apertura='$id_apertura'");
		$monto_a=_fetch_array($mont);
		$monto_ap=$monto_a["monto_ch_actual"];

		$monto_ch_actual_mas=0;
		$monto_ch_actual_menos=0;
		$monto_ch_actual_igual=0;
	if($monto>$monto_anti){
		$monto_ch_actual_menos=$monto-$monto_anti;
	}else if($monto<$monto_anti){
		$monto_ch_actual_mas=$monto_anti-$monto;
	}else if($monto==$monto_anti){
		$monto_ch_actual_igual=$monto;
	}
	if($monto_ch_actual_mas!=0){
		$resta=$monto_ap+$monto_ch_actual_mas;
	}else if($monto_ch_actual_menos!=0){
		$resta=$monto_ap-$monto_ch_actual_menos;
	}else if($monto_ch_actual_igual!=0){
		$resta=$monto_ap;
	}
		$tabla_aper = "apertura_caja";
		$form_aper = array(
			'monto_ch_actual' => $resta,
			);
			$where_m = "id_apertura='".$id_apertura."'";
			$update_apertura = _update($tabla_aper, $form_aper, $where_m);
		if($update_apertura){
			$xdatos['typeinfo']='Success';
			$xdatos['msg']='Movimiento editado correctamente !';
			$xdatos['process']='insert';
			_commit();
		}else{
			$xdatos['typeinfo']='Error';
			$xdatos['msg']='Error al editar registro !'._error();
			_rollback();
		}
	}
	else
	{
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Error al editar el movimiento !'._error();
		_rollback();
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
			case 'editar' :
				editar();
				break;
		}
	}
}

?>
