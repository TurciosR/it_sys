<?php
	include ("_core.php");
function initial()
{
	// Page setup
	$_PAGE = array ();
	$_PAGE ['title'] = 'Administrar caja chica';
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/jqGrid/ui.jqgrid.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
	include_once "header.php";
	include_once "main_menu.php";
	$id_sucursal=$_SESSION["id_sucursal"];
	$fecha_2 = date('Y-m-d');
	$fecha_1 = date('Y-m-01');

	$sql_movimientos = _query("SELECT * FROM mov_caja WHERE entrada = 1 OR salida = 1 OR viatico= 1 AND id_sucursal = '$id_sucursal' AND fecha BETWEEN '$fecha_1' AND '$fecha_2' ORDER BY id_movimiento DESC");
	//echo "SELECT * FROM mov_caja WHERE entrada = 1 OR salida = 1 OR viatico= 1 AND id_sucursal = '$id_sucursal' AND fecha BETWEEN '$fecha_1' AND '$fecha_2' ORDER BY id_movimiento DESC";
	$cuenta = _num_rows($sql_movimientos);
	//permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	date_default_timezone_set('America/El_Salvador');

	//permiso del script
	if ($links!='NOT' || $admin=='1' ){

?>

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row" id="row1">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<?php
				//if ($admin=='t' && $active=='t'){


					echo "
					<div class='ibox-title'>
						<div class='row'>";
					$filename='agregar_ingreso_caja.php';
					$link=permission_usr($id_user,$filename);
					if ($link!='NOT' || $admin=='1' ){
						echo "<div class='col-lg-2'>
								<a data-toggle='modal' href='agregar_ingreso_caja.php' data-target='#viewModal' data-refresh='true' class='btn btn-primary m-t-n-xs'><i class='fa fa-plus icon-large'></i> Agregar ingreso</a>
							</div>";
						}
					$filename='agregar_salida_caja.php';
					$link=permission_usr($id_user,$filename);
					if ($link!='NOT' || $admin=='1' ){
						echo "<div class='col-lg-2'>
								<a data-toggle='modal' href='agregar_salida_caja.php' data-target='#salidaModal' data-refresh='true' class='btn btn-primary m-t-n-xs'><i class='fa fa-plus icon-large'></i> Agregar Vale</a>
							</div>";
						}
						$filename='agregar_viatico_caja.php';
						$link=permission_usr($id_user,$filename);
						if ($link!='NOT' || $admin=='1' ){
							echo "<div class='col-lg-2'>
								<a data-toggle='modal' href='agregar_viatico_caja.php' data-target='#modalviatico' data-refresh='true' class='btn btn-primary m-t-n-xs'><i class='fa fa-plus icon-large'></i> Agregar Viáticos</a>
							</div>";
							}
					echo "</div>
					</div>";
				?>
				<div class="ibox-content">
					<!--load datables estructure html-->

					<header>
						<div class="row">
							<div class="col-xs-6">
								<header>
									<h4>Administrar Movimientos de Caja</h4>
								</header>

							</div>
						</div>
					</header>
					<div class="row">
							<div class="col-lg-3">
								<label>Desde:</label>
								<input type="text" name="fecha1" id="fecha1" class="form-control datepick" value="<?php echo $fecha_1;?>">
							</div>
							<div class="col-lg-3">
								<label>Hasta</label>
								<input type="text" name="fecha2" id="fecha2" class="form-control datepick" value="<?php echo $fecha_2;?>">
							</div>
							<div class="col-lg-1" style="text-align: left;">
							 <label>Buscar</label>
								<a id='search' name='search' class='btn btn-primary m-t-n-xs' style="margin-top: 0.5%;"><i class="fa fa-search"></i> Buscar</a>
							</div>
					</div>
					<section>
						<table class="table table-striped table-bordered table-hover" id="editable">
							<thead>
								<tr>
									<th class="col-lg-1">N°</th>
									<th class="col-lg-4">Concepto</th>
									<th class="col-lg-1">Fecha</th>
									<th class="col-lg-1">Monto</th>
									<th class="col-lg-3">Empleado</th>
									<th class="col-lg-1">T. Mov.</th>
									<th class="col-lg-1">Acción</th>
								</tr>
							</thead>
							<tbody id="caja_x">
				<?php
 					if ($cuenta>0){
 						$i = 1;
						while($row=_fetch_array($sql_movimientos))
						{
							$id_movimiento = $row["id_movimiento"];
							$concepto = $row["concepto"];
							$fecha = ED($row["fecha"]);
							$entrada = $row["entrada"];
							$salida = $row["salida"];
							$viatico = $row["viatico"];
							$monto = $row["valor"];
							$turno = $row["turno"];
							$id_empleado = $row["id_empleado"];
							$id_sucursal = $row["id_sucursal"];
							if($entrada == 1 && $salida == 0 && $viatico==0)
							{
								$tipo = "Entrada";
								$url="editar_movimiento_caja.php";
							}
							else if($salida == 1 && $entrada == 0 && $viatico==0)
							{
								$tipo = "Salida";
								$url="editar_movimiento_caja_s.php";
							}
							else if($viatico== 1 && $salida == 0 && $entrada == 0)
							{
								$tipo = "Viatico";
								$url="editar_movimiento_caja_v.php";
							}

							$sql_empleado = _query("SELECT * FROM empleados, sucursal WHERE id_empleado = '$id_empleado' ");
							$rr = _fetch_array($sql_empleado);
							$nombre = $rr["nombre"];
							$sql_sucursal = _query("SELECT * FROM sucursal WHERE id_sucursal = '$id_sucursal'");
							$ss = _fetch_array($sql_sucursal);
							$nombre_sucursal = $ss["descripcion"];

							echo "<tr>";
							echo"<td>".$i."</td>
								<td>".$concepto."</td>
								<td>".$fecha."</td>
								<td>$".number_format($monto,2,".",",")."</td>
								<td>".$nombre."</td>
								<td>".$tipo."</td>
								<td><div class=\"btn-group\">
								<a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\"><i class=\"fa fa-user icon-white\"></i> Menu<span class=\"caret\"></span></a>
								<ul class=\"dropdown-menu dropdown-primary\">";

									echo "
									<li><a data-toggle='modal' href='$url?id_movimiento=".$id_movimiento."' data-target='#editEModal' data-refresh='true'><i class=\"fa fa-pencil\"></i> Editar</a></li>
									<li><a data-toggle='modal' href='borrar_movimiento_caja.php?id_movimiento=".$id_movimiento."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i> Eliminar</a></li>
									<li><a data-toggle='modal' href='imprimir_movimiento.php?id_movimiento=".$id_movimiento."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-print\"></i> Imprimir</a></li>
									";


							echo "	</ul>
										</div>
										</td>
										</tr>";

							$i += 1;
						}
					}

				?>
							</tbody>
						</table>
						 <input type="hidden" name="autosave" id="autosave" value="false-0">
					</section>
					<!--Show Modal Popups View & Delete -->
					<div class='modal fade' id='viewModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
						<div class='modal-dialog'>
							<div class='modal-content'></div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
					<div class='modal fade' id='modalviatico' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
						<div class='modal-dialog modal-lg'>
							<div class='modal-content modal-lg'></div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
					<div class='modal fade' id='salidaModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
						<div class='modal-dialog modal-md'>
							<div class='modal-content modal-md'></div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
					<div class='modal fade' id='editEModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
						<div class='modal-dialog modal-lg'>
							<div class='modal-content modal-g'></div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->

					<div class='modal fade' id='deleteModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
						<div class='modal-dialog'>
							<div class='modal-content'></div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->

               	</div><!--div class='ibox-content'-->
       		</div><!--<div class='ibox float-e-margins' -->
		</div> <!--div class='col-lg-12'-->
	</div> <!--div class='row'-->
</div><!--div class='wrapper wrapper-content  animated fadeInRight'-->
<?php
	include("footer.php");

	echo" <script type='text/javascript' src='js/funciones/funciones_caja_chica.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}

}
function search()
{
	$id_sucursal = $_SESSION["id_sucursal"];
	$fecha_1 = MD($_POST["fecha1"]);
	$fecha_2 = MD($_POST["fecha2"]);
	$s = 1;
	//$sql_movimientos = _query("SELECT * FROM mov_caja WHERE entrada = 1 OR salida = 1 AND id_sucursal = '$id_sucursal' ORDER BY id_movimiento DESC");
	$sql_movimientos = _query("SELECT * FROM mov_caja WHERE (entrada = 1 OR salida = 1 OR viatico=1) AND id_sucursal = '$id_sucursal' AND fecha BETWEEN '$fecha_1' AND '$fecha_2' ORDER BY id_movimiento DESC");
	$cuenta = _num_rows($sql_movimientos);
	$lista = "";
	if ($cuenta>0){
	$i = 1;
		while($row=_fetch_array($sql_movimientos))
		{
			$id_movimiento = $row["id_movimiento"];
			$concepto = $row["concepto"];
			$fecha = ED($row["fecha"]);
			$entrada = $row["entrada"];
			$salida = $row["salida"];
			$viatico = $row["viatico"];
			$monto = $row["valor"];
			$turno = $row["turno"];
			$id_empleado = $row["id_empleado"];
			$id_sucursal = $row["id_sucursal"];
			if($entrada == 1 && $salida == 0 && $viatico==0)
			{
				$tipo = "Entrada";
				$url="editar_movimiento_caja.php";
			}
			else if($salida == 1 && $entrada == 0 && $viatico==0)
			{
				$tipo = "Salida";
				$url="editar_movimiento_caja_s.php";
			}
			else if($viatico== 1 && $salida == 0 && $entrada == 0)
			{
				$tipo = "Viatico";
				$url="editar_movimiento_caja_v.php";
			}
			$sql_empleado = _query("SELECT * FROM empleados, sucursal WHERE id_empleado = '$id_empleado' ");
			$rr = _fetch_array($sql_empleado);
			$nombre = $rr["nombre"];
			$sql_sucursal = _query("SELECT * FROM sucursal WHERE id_sucursal = '$id_sucursal'");
			$ss = _fetch_array($sql_sucursal);
			$nombre_sucursal = $ss["descripcion"];

			$lista.= "<tr>";
			$lista.="<td>".$i."</td>
				<td>".$concepto."</td>
				<td>".$fecha."</td>
				<td>$".number_format($monto,2,".",",")."</td>
				<td>".$nombre."</td>
				<td>".$tipo."</td>
				<td><div class=\"btn-group\">
				<a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\"><i class=\"fa fa-user icon-white\"></i> Menu<span class=\"caret\"></span></a>
				<ul class=\"dropdown-menu dropdown-primary\">";

					$lista.= "
					<li><a data-toggle='modal' href='$url?id_movimiento=".$id_movimiento."' data-target='#editEModal' data-refresh='true'><i class=\"fa fa-pencil\"></i> Editar</a></li>
					<li><a data-toggle='modal' href='borrar_movimiento_caja.php?id_movimiento=".$id_movimiento."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i> Eliminar</a></li>
					<li><a data-toggle='modal' href='imprimir_movimiento.php?id_movimiento=".$id_movimiento."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-print\"></i> Imprimir</a></li>
					";


			$lista.= "	</ul>
						</div>
						</td>
						</tr>";

			$i += 1;
		}
	}

	echo $lista;

}
if (!isset($_REQUEST['process'])) {
    initial();
}
//else {
if (isset($_REQUEST['process'])) {
    switch ($_REQUEST['process']) {
    case 'ok':
        search();
        break;
    }

 //}
}
?>
