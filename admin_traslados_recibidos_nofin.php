<?php
	include ("_core.php");
	// Page setup
	$title= 'Administrar Traslados Recibidos';
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

	$sql="SELECT traslado.*, usuario.nombre as nombreuser,sucursal.descripcion FROM traslado JOIN usuario ON traslado.id_empleado=usuario.id_usuario JOIN sucursal ON traslado.id_sucursal_origen=sucursal.id_sucursal WHERE traslado.id_sucursal_destino='$id_sucursal' and traslado.anulado!=1 ";
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
						  $filename='traslados.php';
							$link=permission_usr($id_user,$filename);

							echo " <div class='row'>";

						echo "<div class='col-xs-2'>	";
							if ($link!='NOT' || $admin=='1' )

								echo "<a href='$filename' class='btn btn-primary' role='button'><i class='fa fa-plus icon-large'></i> Nuevo Traslado</a>";
								echo "</div>";
							 echo "</div>"
							?>
						  <!--button type="button" id="btnReload3" name="btnReload3" class="btn btn-primary"><i class="fa fa-refresh"></i> Recargar</button-->
						<table class="table table-striped table-bordered table-hover"id="editable2">
							<thead>
								<tr>
									<th>Id Traslado</th>
									<th>Sucursal Origen</th>
									<th>Envia</th>
									<th>Fecha</th>
									<th>Items</th>
									<th>Pares</th>
									<th>Estado</th>
									<th>Acci&oacute;n</th>
								</tr>
							</thead>
							<tbody>
				<?php
 					if ($count>0){
						for($i=0;$i<$count;$i++){
							$row=_fetch_array($result);
							$id_traslado=$row ['idtransace'];
							$anulada=$row['anulado'];
							$sucursal_origen=$row['descripcion'];
							$nombre_empleado=$row['nombreuser'];
							$finalizada=$row['finalizado'];
							$verificado=$row['verificado'];
							$fecha_recibe=$row['fecha_recibido'];

							$txt_estado="";
							$txt_estado2=' ';
							if($finalizada==1 && $anulada==0){
								$txt_estado='FINALIZADA';
								if($fecha_recibe!='0000-00-00'){
									$txt_estado2=' '.$fecha_recibe;
								}
								else{
									$txt_estado2=' ';
								}
							}
								else{
									$txt_estado2=' ';
								}
							if($finalizada==0 && $anulada==1 )
								$txt_estado='NULA';

							if($finalizada==1 && $anulada==1 )
								$txt_estado='NULA';

							if($finalizada==0 && $anulada==0)
								$txt_estado='PENDIENTE';
							if($finalizada==0 && $anulada==0 && $verificado==1)
									$txt_estado='VERIFICADO';

							echo "<tr>";

							if($txt_estado=='NULA'|| $txt_estado=='FINALIZADA' ){
								echo "<td>".$row['idtransace']."</td>";
								echo "<td> <h5 class='text-mutted'>".$sucursal_origen."</h5></td>";
								echo "<td> <h5 class='text-mutted'>".$nombre_empleado."</h5></td>";
								echo "<td>".$row['fecha']."</td>";
								echo "<td>".$row['items']."</td>";
								echo "<td>".$row['pares']."</td>";
								echo "<td><h5 class='text-mutted'>".$txt_estado.$txt_estado2."</h5></td>";
							}
							if($txt_estado=='PENDIENTE' || $txt_estado=='VERIFICADO'){
								echo "<td><strong>".$row['idtransace']."</strong></td>";
								echo "<td> <h5 class='text-primary'>".$sucursal_origen."</h5></td>";
								echo "<td> <h5 class='text-warning'>".$nombre_empleado."</h5></td>";
								echo "<td>".$row['fecha']."</td>";
								echo "<td>".$row['items']."</td>";
								echo "<td>".$row['pares']."</td>";
								echo "<td><h5 class='text-danger'>".$txt_estado."</h5></td>";
							}
							echo"<td><div class=\"btn-group\">
								<a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\"><i class=\"fa fa-user icon-white\"></i> Menu<span class=\"caret\"></span></a>
								<ul class=\"dropdown-menu dropdown-primary\">";

								$filename='ver_reporte_ficha_traslado.php';
								$link=permission_usr($id_user,$filename);
								if ($link!='NOT' || $admin=='1' )
									echo "<li><a href='$filename?id_traslado=".$row ['idtransace']."&id_sucursal=$id_sucursal' target='_blank' ><i class=\"fa fa-search\"></i> Ver Ficha Traslado</a></li>";


							if ($finalizada==1) {
								# code...
								$filename='ver_reporte_traslados.php';
								$link=permission_usr($id_user,$filename);
								if ($link!='NOT' || $admin=='1' )
									echo "<li><a href='$filename?id_traslado=".$row ['idtransace']."&id_sucursal=$id_sucursal' target='_blank' ><i class=\"fa fa-search\"></i> Ver Detalle Ingreso</a></li>";


							}



							if($txt_estado=='PENDIENTE' || $finalizada==0){
								/*
								$filename='finalizar_traslado.php';
								$link=permission_usr($id_user,$filename);
								if ($link!='NOT' || $admin=='1' ){
									echo"<li><a href=\"$filename?id_traslado=".$row['id_traslado']."&id_sucursal_destino=$id_sucursal\"><i class=\"fa fa-check\"></i> Finalizar</a></li>";
								}
								*/
								$filename='editar_traslado.php';
								/*
								$link=permission_usr($id_user,$filename);
								if ($link!='NOT' || $admin=='1'){
									echo "<li><a  href='editar_traslado.php?id_traslado=$id_traslado&numero_doc=$numero_doc&process=formEdit' ><i class=\"fa fa-pencil\"></i> Editar</a></li>";
								}
								*/
								if($verificado==0)
								{
									$filename='verificar_traslado.php';
									$link=permission_usr($id_user,$filename);
									if ($link!='NOT' || $admin=='1'){
										echo "<li><a href='verificar_traslado.php?id_traslado=" .  $row ['idtransace'].""."'><i class=\"fa fa-check\"></i> Verificar Traslado</a></li>";
									}
								}

							}

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
	echo" <script type='text/javascript' src='js/funciones/funciones_admin_traslados.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
?>
