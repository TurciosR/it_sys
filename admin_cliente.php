<?php
	include ("_core.php");
	// Page setup
	$title =  'Administrar Clientes';
	$_PAGE = array ();
	$_PAGE ['title'] = $title;
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

 	$sql="SELECT c.*, d.nombre_departamento, m.nombre_municipio FROM clientes as c
	LEFT JOIN municipio as m ON c.municipio = m.id_municipio 
	LEFT JOIN departamento as d ON c.depto = d.id_departamento WHERE c.nombre !='MOSTRADOR' ORDER BY c.nombre ASC ";


//$user=mysql_fetch_array($query1);

	$result=_query($sql);
	$count=_num_rows($result);

	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri=$_SERVER['REQUEST_URI'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
?>

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row" id="row1">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<?php
					//permiso del script
					if ($links!='NOT' || $admin=='1' ){
					echo"<div class='ibox-title'>";
					$filename='agregar_cliente.php';
					$link=permission_usr($id_user,$filename);
					if ($link!='NOT' || $admin=='1' )
						echo "<a href='agregar_cliente.php' class='btn btn-primary' role='button'><i class='fa fa-plus icon-large'></i> Agregar cliente</a>";
					echo "</div>";

				?>
				<div class="ibox-content">
					<!--load datables estructure html-->
					<header>
						<h4><?php echo  $title; ?></h4>
					</header>
					<section>
						<table class="table table-striped table-bordered table-hover" id="editable">
							<thead>
								<tr>
									<th class="col-lg-1">Id</th>
									<th class="col-lg-4">Nombre</th>
									<th class="col-lg-3">Dirección</th>
									<th class="col-lg-1">NIT</th>
									<th class="col-lg-2">Telefonos</th>
									<th class="col-lg-1">Acci&oacute;n</th>
								</tr>
							</thead>
							<tbody>
							<?php
			 					while($row=_fetch_array($result))
			 					{
			 						$id_cliente = $row['id_cliente'];
									$nit=$row['nit'];
									$nombre=$row['nombre'];
									$direccion=$row['direccion'];
									$telefonos = "";
									if($row['telefono1'] != "")
									{
										$telefonos .= $row['telefono1'];
									}
									if($row['telefono2'] != "")
									{
										if($telefonos != "")
										{
											$telefonos .= " y ".$row['telefono2'];
										}
										else
										{
											$telefonos .= $row['telefono2'];
										}
									}

									echo "<tr>";
									echo"<td>".$id_cliente."</td>
										<td>".$nombre."</td>
										<td>".$direccion."</td>
										<td>".$nit."</td>
										<td>".$telefonos."</td>";

									echo"<td><div class=\"btn-group\">
										<a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\"><i class=\"fa fa-user icon-white\"></i> Menu<span class=\"caret\"></span></a>
										<ul class=\"dropdown-menu dropdown-primary\">";
										$filename='editar_cliente.php';
										$link=permission_usr($id_user,$filename);
										if ($link!='NOT' || $admin=='1' )
											echo "<li><a href=\"editar_cliente.php?id_cliente=".$id_cliente."\"><i class=\"fa fa-pencil\"></i> Editar</a></li>";

										$filename='borrar_cliente.php';
										$link=permission_usr($id_user,$filename);
										if ($link!='NOT' || $admin=='1' )
											echo "<li><a data-toggle='modal' href='borrar_cliente.php?id_cliente=" .  $row ['id_cliente']."&process=formDelete"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i> Eliminar</a></li>";

										$filename='ver_cliente.php';
										$link=permission_usr($id_user,$filename);
										if ($link!='NOT' || $admin=='1' )
										echo "<li><a data-toggle='modal' href='ver_cliente.php?id_cliente=".$id_cliente."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-search\"></i> Ver Detalle</a></li>";

									echo "	</ul>
												</div>
												</td>
												</tr>";
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
						<div class='modal-dialog modal-sm'>
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
	echo" <script type='text/javascript' src='js/funciones/funciones_cliente.js'></script>";
	} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
?>
