<?php
	include ("_core.php");
	// Page setup
	$_PAGE = array ();
	$_PAGE ['title'] = 'Administrar Movimientos';
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
	include_once "header.php";
	include_once "main_menu.php";
	$id_sucursal=$_SESSION['id_sucursal'];
	$id_user = $_SESSION["id_usuario"];
	date_default_timezone_set('America/El_Salvador');
	$fecha_actual = date("Y-m-d");
	$hora_actual = date("H:i:s");
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	//permiso del script
	if ($links!='NOT' || $admin=='1' ){
?>

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row" id="row1">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<?php
				echo"<div class='ibox-title'>
					<a href='inventario_inicial.php' class='btn btn-primary' role='button'><i class='fa fa-plus icon-large'></i> Cargar Inventario</a>
					<a href='otras_salidas.php' class='btn btn-primary' role='button'><i class='fa fa-plus icon-large'></i> Descargar Inventario</a>
					</div>";
				?>
				<div class="ibox-content">
					<!--load datables estructure html-->
					<header>
						<h4>Administrar Movimientos</h4>
					</header>
					<section>
						<table class="table table-striped table-bordered table-hover" id="editable">
							<thead>
								<tr>
									<th class="col-lg-1">N°</th>
									<th class="col-lg-2">Fecha</th>
									<th class="col-lg-5">Concepto</th>
									<th class="col-lg-3">Tipo</th>
									<th class="col-lg-1">Acci&oacute;n</th>
								</tr>
							</thead>
							<tbody id="t_mov">
							<?php
								$sql_movimientos = _query("SELECT * FROM movimiento_producto");
								$cuenta = _num_rows($sql_movimientos);
								if($cuenta > 0)
								{
									$n = 1;
									while ($row = _fetch_array($sql_movimientos))
									{
										$tipo = $row["tipo"];
										$fecha = ED($row["fecha"]);
										$numero_doc = $row["correlativo"];
										$concepto = $row["concepto"];
										$id_kardex = $row["id_movimiento"];



										echo "<tr>";
										echo "<td>".$n."</td>";
										echo "<td>".$fecha."</td>";
										echo "<td>".$concepto."</td>";
										echo "<td>".$tipo."</td>";
										echo"<td><div class=\"btn-group\">
										<a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\"><i class=\"fa fa-user icon-white\"></i> Menu<span class=\"caret\"></span></a>
										<ul class=\"dropdown-menu dropdown-primary\">";
										/*$filename='editar_movimiento.php';
										$link=permission_usr($id_user,$filename);
										if ($link!='NOT' || $admin=='1' )
											echo "<li><a href=\"editar_movimiento.php?numero_doc=".$numero_doc."&alias_tipodoc=".$alias_tipodoc."\"><i class=\"fa fa-pencil\"></i> Editar</a></li>";
										$filename='ver_movimiento_inventario.php';*/
										$link=permission_usr($id_user,$filename);
										if ($link!='NOT' || $admin=='1' )
											echo "<li><a data-toggle='modal' href=\"ver_movimiento_inventario.php?id_kardex=".$id_kardex."&alias_tipodoc=".$tipo."\" data-target='#viewModal' data-refresh='true'><i class=\"fa fa-eye\"></i> Ver detalles</a></li>";

										/*$filename='borrar_movimiento.php';
										$link=permission_usr($id_user,$filename);
										if ($link!='NOT' || $admin=='1' )
											echo "<li><a data-toggle='modal' href='borrar_movimiento.php?numero_doc=".$numero_doc."&alias_tipodoc=".$alias_tipodoc."&process=formDelete"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i> Eliminar</a></li>";*/

									echo "	</ul>
												</div>
												</td>
												</tr>";
										$n += 1;
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
					<div class='modal fade' id='deleteModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
						<div class='modal-dialog'>
							<div class='modal-content modal-sm'></div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
               	</div><!--div class='ibox-content'-->
       		</div><!--<div class='ibox float-e-margins' -->
		</div> <!--div class='col-lg-12'-->
	</div> <!--div class='row'-->
</div><!--div class='wrapper wrapper-content  animated fadeInRight'-->
<?php
	include("footer.php");
	echo" <script type='text/javascript' src='js/funciones/funciones_admin_fact.js'></script>";
	} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
?>
