<?php
	include ("_core.php");
	// Page setup
	$_PAGE = array ();
	$_PAGE ['title'] = 'Administrar Empleados';
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
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
	include_once "header.php";
	include_once "main_menu.php";

 	$sql="SELECT * FROM empleados";
	$result=_query($sql);
	$count=_num_rows($result);
	//permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	//permiso del script
?>

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row" id="row1">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<?php
				if ($links!='NOT' || $admin=='1' ){
				echo "<div class='ibox-title'>";
					//permiso del script
				$filename='agregar_tipo_empleado.php';
				$link=permission_usr($id_user,$filename);
				if ($link!='NOT' || $admin=='1' )
					echo "<a href='agregar_empleado.php' class='btn btn-primary' role='button'><i class='fa fa-plus icon-large'></i> Agregar empleado</a>";
				echo "</div>";

				?>
				<div class="ibox-content">
					<!--load datables estructure html-->
					<header>
						<h4>Administrar empleado</h4>
					</header>
					<section>
						<table class="table table-striped table-bordered table-hover"id="editable">
							<thead>
								<tr>
									<th>Id empleado</th>
									<th>Nombre</th>
									<th>Telefonos</th>
									<th>Correo</th>
									<th>Acci&oacute;n</th>
								</tr>
							</thead>
							<tbody>
				<?php
 					if ($count>0){
						for($i=0;$i<$count;$i++){
							$row=_fetch_array($result);
							$contacto=$row['nombre'];
							$acceso_sistema=$row['acceso_sistema'];
							$telefonos=$row['telefono1']." -- ".$row['telefono2'];
							$salariobase=$row['salario'];
							$correo=$row['email'];
							echo "<tr>";
							echo"<td>".$row['id_empleado']."</td>
								<td>".$contacto."</td>
								<td>".$telefonos."</td>
								<td>".$correo."</td>";
							echo"<td><div class=\"btn-group\">
								<a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\"><i class=\"fa fa-user icon-white\"></i> Menu<span class=\"caret\"></span></a>
								<ul class=\"dropdown-menu dropdown-primary\">";
							$filename='editar_empleado.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' )
								echo "<li><a href=\"editar_empleado.php?id_empleado=".$row['id_empleado']."\"><i class=\"fa fa-pencil\"></i> Editar</a></li>";
							$filename='borrar_empleado.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' )
								echo "<li><a data-toggle='modal' href='borrar_empleado.php?id_empleado=" .  $row ['id_empleado']."&process=formDelete"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i> Eliminar</a></li>";
							$filename='ver_empleado.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' )
							echo "<li><a data-toggle='modal' href='ver_empleado.php?id_empleado=".$row['id_empleado']."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-search\"></i> Ver Detalle</a></li>";
							if($acceso_sistema)
							{
								$filename='permiso_usuario.php';
								$link=permission_usr($id_user,$filename);
								if ($link!='NOT' || $admin=='1' )
								echo "<li><a href='permiso_usuario.php?id_empleado=".$row['id_empleado']."'><i class=\"fa fa-lock\"></i> Permisos</a></li>";
							}
							echo "	</ul>
									</div>
									</td>
									</tr>";
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
	echo" <script type='text/javascript' src='js/funciones/funciones_empleado.js'></script>";
		} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
?>
