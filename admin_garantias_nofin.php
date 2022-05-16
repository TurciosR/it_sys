<?php
	include ("_core.php");
	// Page setup
	$title= 'Administrar Garantias Solicitadas al Proveedor';
	$_PAGE = array ();
	$_PAGE ['title'] =$title ;
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
 	//permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

	$sql="SELECT garantia.*,
	usuario.nombre as nombreuser,proveedor.nombre_proveedor, proveedor.telefono1
	FROM garantia
	JOIN usuario ON garantia.id_usuario=usuario.id_usuario
	JOIN proveedor ON garantia.id_proveedor=proveedor.id_proveedor
	WHERE  garantia.id_sucursal='$id_sucursal'
	ORDER BY finalizada
	limit 1000
	";
	$result=_query($sql);
	$count=_num_rows($result);
?>

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row" id="row1">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">

				<?php
						//permiso del script
						if ($links!='NOT' || $admin=='1' ){
						echo"
                        <div class='ibox-title'>
                            <h5>$title</h5>
                        </div>";
                        ?>

				  <div class="ibox-content">
					</header>
					<section>
						 <?php
						  $filename='garantia.php';
							$link=permission_usr($id_user,$filename);

							echo " <div class='row'>";
							echo "<div class='col-xs-2'>";
							 echo "<button type='button' id='btnReload3' name='btnReload3' class='btn btn-primary'><i class='fa fa-refresh'></i> Recargar</button>";
						echo "</div>";
						echo "<div class='col-xs-2'>	";
							if ($link!='NOT' || $admin=='1' )

								echo "<a href='$filename' class='btn btn-primary' role='button'><i class='fa fa-plus icon-large'></i> Nueva Garantia</a>";
								echo "</div>";
							 echo "</div>"
							?>



						<table class="table table-striped table-bordered table-hover"id="editable2">
							<thead>
								<tr>
									<th>Id garantia</th>
									<th>Proveedor</th>
									<th>Envia</th>
									<th>Fecha</th>
									<th>Numero Doc</th>
									<th>Total</th>
									<th>Estado</th>
									<th>Acci&oacute;n</th>
								</tr>
							</thead>
							<tbody>
				<?php
 					if ($count>0){
						for($i=0;$i<$count;$i++){
							$row=_fetch_array($result);
							$numero_doc=$row['numero_doc'];
							$id_garantia=$row ['id_garantia'];
							$anulada=$row['anulada'];


							if ($row['telefono1']!='')
								$proveedor=$row['nombre_proveedor']. " Tel. ".$row['telefono1'];
							else
								$proveedor=$row['nombre_proveedor'];
							$nombre_empleado=$row['nombreuser'];
							$finalizada=$row['finalizada'];
							$txt_estado="";
							if($finalizada==1 && $anulada==0)
								$txt_estado='FINALIZADA';

							if($finalizada==0 && $anulada==1 )
								$txt_estado='NULA';

							if($finalizada==1 && $anulada==1 )
								$txt_estado='NULA';

							if($finalizada==0 && $anulada==0)
								$txt_estado='PENDIENTE';

							echo "<tr>";

							if($txt_estado=='NULA'|| $txt_estado=='FINALIZADA' ){
								echo "<td>".$row['id_garantia']."</td>";
								echo "<td> <h5 class='text-mutted'>".$proveedor."</h5></td>";
								echo "<td> <h5 class='text-mutted'>".$nombre_empleado."</h5></td>";
								echo "<td>".$row['fecha']."</td>";
								echo "<td> <h5 class='text-mutted'>".$numero_doc."</h5></td>";
								echo "<td><h5 class='text-mutted'>"."$ ".$row['total']."</h5></td>";
								echo "<td><h5 class='text-mutted'>".$txt_estado."</h5></td>";
							}
							if($txt_estado=='PENDIENTE'){
								echo "<td><strong>".$row['id_garantia']."</strong></td>";
								echo "<td> <h5 class='text-primary'>".$proveedor."</h5></td>";
								echo "<td> <h5 class='text-warning'>".$nombre_empleado."</h5></td>";
								echo "<td>".$row['fecha']."</td>";
								echo "<td> <h5 class='text-danger'>".$numero_doc."</h5></td>";
								echo "<td><h5 class='text-success'>"."$ ".$row['total']."</h5></td>";
								echo "<td><h5 class='text-danger'>".$txt_estado."</h5></td>";
							}
							echo"<td><div class=\"btn-group\">
								<a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\"><i class=\"fa fa-user icon-white\"></i> Menu<span class=\"caret\"></span></a>
								<ul class=\"dropdown-menu dropdown-primary\">";
							if($txt_estado=='PENDIENTE' || $finalizada==0){
								$filename='finalizar_garantia.php';
								$link=permission_usr($id_user,$filename);
								if ($link!='NOT' || $admin=='1' ){
									echo"<li><a href=\"$filename?id_garantia=".$row['id_garantia']."&id_sucursal=$id_sucursal\"><i class=\"fa fa-check\"></i> Finalizar</a></li>";
								}

								$filename='borrar_garantia.php';
								$link=permission_usr($id_user,$filename);
								if ($link!='NOT' || $admin=='1' ){
									echo"<li><a data-toggle='modal' href='$filename?id_garantia=" .  $row ['id_garantia']."&process=formDelete"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i> Eliminar</a></li>";
								}
							}
							/*
							$filename='imprimir_garantia.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' )
								echo "<li><a data-toggle='modal' href='imprimir_garantia.php?id_garantia=".$row['id_garantia']."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-print\"></i> Imprimir</a></li>";

							$filename='anular_garantia.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' )
								echo "<li><a data-toggle='modal' href='anular_garantia.php?id_garantia=" .  $row ['id_garantia']."&process=formDelete"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i> Anular</a></li>";


							$filename='editar_garantia.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1'){
								if($txt_estado=='PENDIENTE' || $finalizada==0){
									echo "<li><a  href='editar_garantia.php?id_garantia=$id_garantia&numero_doc=$numero_do&process=formEdit' ><i class=\"fa fa-pencil\"></i> Editar</a></li>";
								}
							}
							*/
							echo "	</ul>
										</div>
										</td>
										";

							echo "	</tr>";
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
							<div class='modal-content modal-sm'></div><!-- /.modal-content -->
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
	echo" <script type='text/javascript' src='js/funciones/funciones_admin_garantia.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
?>
