<?php
	include ("_core.php");
	// Page setup
	function initial() {


	$_PAGE = array ();
	$_PAGE ['title'] = 'Administrar Cabeceras';
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
	$sql_cabecera = _query("SELECT * FROM config_pos WHERE id_sucursal = '$id_sucursal'");
	$cuenta = _num_rows($sql_cabecera);
	//permiso del script
	if ($links!='NOT' || $admin=='1' ){
?>

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row" id="row1">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<!--load datables estructure html-->
					<header>
						<h4>Admin Cabeceras</h4>
					</header>
					<section>
						<table class="table table-striped table-bordered table-hover" id="editable">
							<thead>
								<th class="col-lg-1">N°</th>
								<th class="col-lg-4">Documento</th>	
								<th class="col-lg-5">Sucursal</th>							
								<th class="col-lg-2">Acciones</th>								
							</thead>
							<tbody id="t_mov">
								<?php
									if($cuenta > 0)
									{
										$n = 1;
										while ($row = _fetch_array($sql_cabecera)) 
										{
											$id_conf = $row["id_config_pos"];
											$header4 = $row["header4"];
											$alias_tipodoc = $row["alias_tipodoc"];
											if($alias_tipodoc == "TIK")
											{
												$txt = "TIQUETE";
											}	
											else if($alias_tipodoc == "DEV")
											{
												$txt = "DEVOLUCIÓN";
											}
											else if($alias_tipodoc == "RES")
											{
												$txt = "RESERVA";
											}
											echo "<tr>";
											echo "<td>".$n."</td>";
											echo "<td>".$txt."</td>";
											echo "<td>".$header4."</td>";
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
											echo "<li><a href='editar_post.php?id_conf=".$id_conf."'><i class=\"fa fa-pencil\"></i> Editar</a></li>";
										$link=permission_usr($id_user,$filename);
										if ($link!='NOT' || $admin=='1' )
											echo "<li><a data-toggle='modal' href='ver_cabecera.php?id_conf=".$id_conf."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-eye\"></i> Ver detalles</a></li>";

										/*$filename='borrar_movimiento.php';
										$link=permission_usr($id_user,$filename);
										if ($link!='NOT' || $admin=='1' )
											echo "<li><a data-toggle='modal' href='borrar_movimiento.php?numero_doc=".$numero_doc."&alias_tipodoc=".$alias_tipodoc."&process=formDelete"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i> Eliminar</a></li>";*/

											echo "</ul>
													</div>
													</td>";
											echo "</tr>";
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
	echo "<script src='js/funciones/funciones_pos.js'></script>";
	} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

if(!isset($_POST['process'])){
	initial();
}
else
{
  if(isset($_POST['process']))
  {
    switch ($_POST['process']) 
    {
    	case 'tiket':
    	break;
  	}
  }
}
?>
