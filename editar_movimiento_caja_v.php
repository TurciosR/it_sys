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
	if($cuenta > 0)
	{
	$id_movimiento = $_REQUEST["id_movimiento"];
	$sql_movimiento = _query("SELECT * FROM mov_caja WHERE id_movimiento = '$id_movimiento'");
	$rr = _fetch_array($sql_movimiento);
	$id_apertura = $rr["id_apertura"];
	$entrada = $rr["entrada"];
	$salida = $rr["salida"];
	$viatico = $rr["viatico"];
	$concepto = $rr["concepto"];
	$monto = $rr["valor"];
	$tipo_delige = $rr["tipo_delige"];
	$nombre_p = $rr["nombre_proveedor"];
	$numero_doc = $rr["numero_doc"];
  $n_empleado = $rr["nombre_recibe"];
	$deta = $rr["detalle"];
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

    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group has-info single-line">
          <label>Tipo Diligencia</label>
          <select class="form-control" name="tipo_deli" id="tipo_deli">
            <option <?php if($tipo_delige=="pedido") echo 'selected' ?> value="pedido">Pedido</option>
            <option <?php if($tipo_delige=="cobro") echo 'selected' ?>  value="cobro">Cobro</option>
            <option <?php if($tipo_delige=="Pedido y Cobro") echo 'selected' ?>  value="Pedido y Cobro">Pedido y Cobro</option>
            <option <?php if($tipo_delige=="otro") echo 'selected' ?>  value="otro">Otro</option>
          </select>
        </div>
      </div>
			<div class="col-md-6">
        <div class="has-info single-line">
          <label>Recibe</label>
          <input type="text" name="n_empleado" id="n_empleado" class="form-control" value="<?php echo $n_empleado;?>">
        </div>
      </div>
    </div>
    <div class="row">
          <div class="col-md-6">
            <div class="form-group has-info single-line">
              <label>Concepto</label>
              <input type='text'  class='form-control' id='concepto' name='concepto' value="<?php echo $concepto ?>">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group has-info single-line">
              <label>Monto </label>
							<input type='hidden' id='monto_anti' name='monto_anti' value="<?php echo $monto; ?>">
							<input type='text'  class='form-control numeric' id='monto' name='monto' value="<?php echo $monto; ?>">
            </div>
          </div>
    </div>
		<?php
		$detalles=_query("SELECT *FROM mov_caja_detalle WHERE id_mov_caja='$id_movimiento'");
		?>
		<div class="row">
				<div class="col-md-4">
						<div class="form-group has-info single-line">
						<label>Naturaleza de gasto</label>
						<input id="natu" name="natu" class="form-control clear">
					</div>
				</div>
				<div class="col-md-3">
						<div class="form-group has-info single-line">
						<label>Detalle</label>
						<input id="detalle" name="detalle" class="form-control clear">
					</div>
				</div>
				<div class="col-md-3">
						<div class="form-group has-info single-line">
						<label>Valor </label>
						<input id="valor" name="valor" class="form-control clear">
					</div>
				</div>
				<div class="col-md-2">
						<div class="form-group">
							<br>
						<button  type="button" class="btn btn-primary" id="add_pre">Agregar</button>
					</div>
				</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<table class="table table-hover table-striped table-bordered">
					<thead>
						<tr>
							<th class="col-md-4">Naturaleza de Gasto</th>
							<th class="col-sm-4">Detalle</th>
							<th class="col-md-3">Valor</th>
							<th class="col-md-1">Eliminar</th>
						</tr>
					</thead>
					<tbody id="presentacion_table">
						<?php
							while($fila=_fetch_array($detalles)){
								$natu=$fila['natu_gasto'];
								$detalle=$fila['detalle'];
								$valor=$fila['valor'];
								$id_mcd=$fila['id_mcd'];
								echo "<tr>";
								echo "<td><input type='hidden' class='id_mcd' value='".$id_mcd."'><input class='natu' value='".$natu."'></td>";
								echo "<td><input class='detalle' value='".$detalle."'></td>";
								echo "<td><input class='valor' value='".$valor."'></td>";
								echo "<td class='delete text-center'><i class='fa fa-trash'></i></td>";
								echo "</td>";
							}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<input type="hidden" name="id_movimiento" id="id_movimiento" value="<?php echo $id_movimiento;?>">
	<input type="hidden" name="id_apertura" id="id_apertura" value="<?php echo $id_apertura;?>">

</div>
<div class="modal-footer">
	<input type="hidden" name="process" id="process" value="edited">
	<button type="button" class="btn btn-primary" id="btnViatico">Guardar</button>
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
	$n_empleado = $_POST["recibe"];
	$concepto = $_POST["concepto"];
	$monto_anti = $_POST["monto_anti"];
	$monto = $_POST["monto"];
	$tipo_deli = $_POST["tipo_deli"];
	$lista = $_POST["lista"];
	$cuantos = $_POST["cuantos"];
		_begin();
	$tabla = "mov_caja";
	$form_data = array(
		'valor' => $monto,
		'concepto' => $concepto,
		'tipo_delige' => $tipo_deli,
		'nombre_recibe' =>$n_empleado,
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
			$lista = explode("|", $lista);
			$n = 0;
			$mov_deta_aptu=false;
			$insert_detalle=false;
			for ($i=0; $i < $cuantos ; $i++)
			{
				list($natu,$detalle,$valor,$id_mcd)=explode(',',$lista[$i]);
					$tablee='mov_caja_detalle';
					if($id_mcd=='0'){
						$form_data_detalle = array(
							'natu_gasto' => $natu,
							'detalle' => $detalle,
							'valor' => $valor,
							'id_mov_caja' => $id_movimiento,
						);
					$insert_detalle = _insert($tablee,$form_data_detalle);
				}else{
					$form_data_detalle = array(
						'natu_gasto' => $natu,
						'detalle' => $detalle,
						'valor' => $valor,
					);
					$where_d = "id_mcd='".$id_mcd."'";
					$mov_deta_aptu = _update($tablee, $form_data_detalle, $where_d);
				}
					if($insert_detalle || $mov_deta_aptu){
						$n++;
					}
			}
			if($n == $cuantos)
			{
				$xdatos['typeinfo']='Success';
				$xdatos['msg']='Registro ingresado con exito!';
				$xdatos['process']='insert';
				_commit();
			}
			else
			{
				_rollback();
				$xdatos['typeinfo']='Error';
				$xdatos['msg']='Registro no pudo ser ingresado !';
				$xdatos['process']='insert '.$cuantos." n".$n;
			}
		}
	}
	else
	{_rollback();
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Error al editar el movimiento !'._error();
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
			case 'edited' :
				editar();
				break;
		}
	}
}

?>
