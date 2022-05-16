<?php
	include ("_core.php");
	// Page setup
	$_PAGE = array ();
	$_PAGE ['title'] = 'Administrar pedidos';
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

	$sql="SELECT pedido.*, cliente.nombre,cliente.apellido,
	usuario.nombre as nombreuser
	FROM pedido
	JOIN cliente ON pedido.id_cliente=cliente.id_cliente
	JOIN usuario ON pedido.id_usuario=usuario.id_usuario
	WHERE pedido.id_sucursal='$id_sucursal'
	ORDER BY finalizada
	";

	$result=_query($sql);
	$count=_num_rows($result);
	//permiso del script
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
				echo"<div class='ibox-title'>";
				$filename='pedido.php';
				$link=permission_usr($id_user,$filename);
				if ($link!='NOT' || $admin=='1' )
				echo "<a href='pedido.php' class='btn btn-primary' role='button'><i class='fa fa-plus icon-large'></i> Agregar pedido</a>";
				echo "</div>";

				?>
				<div class="ibox-content">
					<!--load datables estructure html-->
					<header>
						<h4>Administrar pedido</h4>
					</header>
					<section>
						<table class="table table-striped table-bordered table-hover"id="editable">
							<thead>
								<tr>
									<th>Id pedido</th>
									<th>Cliente</th>
									<th>Empleado</th>
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
							$id_pedido=$row['id_pedido'];
							$numero_doc=$row['numero_doc'];
							$anulada=$row['anulada'];
							$cliente=$row['nombre']." ".$row['apellido'];
							$anulada=$row['anulada'];
							$nombre_empleado=$row['nombreuser'];
							$finalizada=$row['finalizada'];
							$txt_estado="";
							$total_consignado=$row['total'];
							$total_consignado_format=sprintf("%.2f",$total_consignado);
							//Traer sumatoria de facturas hechas con este id pedido
							$n_facturado=0;

							$sql_facturado="SELECT sum(total) as total_consignado_fact
							FROM factura WHERE id_pedido='$id_pedido'
							AND id_sucursal='$id_sucursal'
							";
							$result_facturado=_query($sql_facturado);
							$n_facturado=_num_rows($result_facturado);
							$rows_facturado=_fetch_array($result_facturado);

							if ($n_facturado>0){
								 $abono_consignado_fact=$rows_facturado['total_consignado_fact'];
							 }
							 else
								 $abono_consignado_fact=0;
							 $abono_consignado_format=sprintf("%.2f",  $abono_consignado_fact);
							//SALDO PENDIENTE
							$saldo_pendiente=0;
							$saldo_pendiente=$total_consignado-$abono_consignado_fact;
							$saldo_pendiente_format=sprintf("%.2f",$saldo_pendiente);

							list($ndoc,$tipo)=explode("_",$numero_doc);
							if($finalizada==1 && $anulada==0)
								$txt_estado='FINALIZADO';

							if($finalizada==0 && $anulada==1 )
								$txt_estado='NULA';

							if($finalizada==1 && $anulada==1 )
								$txt_estado='NULA';

							if($finalizada==0 && $anulada==0)
								$txt_estado='PENDIENTE';
							echo "<tr>";
							if($txt_estado=='NULA'|| $txt_estado=='FINALIZADO' ){
								echo "<td>".$id_pedido."</td>";
								echo "<td> <h5 class='text-mutted'>".$cliente."</h5></td>";
								echo "<td> <h5 class='text-mutted'>".$nombre_empleado."</h5></td>";
								echo "<td>".$row['fecha']."</td>";
								echo "<td> <h5 class='text-mutted'>".$numero_doc."</h5></td>";
								echo "<td><h5 class='text-primary'>"."$ ".$total_consignado_format."</h5></td>";
								echo "<td><h5 class='text-mutted'>".$txt_estado."</h5></td>";
							}
							if($txt_estado=='PENDIENTE'){
								echo "<td><strong>".$id_pedido."</strong></td>";
								echo "<td> <h5 class='text-primary'>".$cliente."</h5></td>";
								echo "<td> <h5 class='text-warning'>".$nombre_empleado."</h5></td>";
								echo "<td>".$row['fecha']."</td>";
								echo "<td> <h5 class='text-danger'>".$numero_doc."</h5></td>";
								echo "<td><h5 class='text-warning'>"."$ ".$total_consignado_format."</h5></td>";
								echo "<td><h5 class='text-danger'>".$txt_estado."</h5></td>";
							}

							echo"<td><div class=\"btn-group\">
								<a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\"><i class=\"fa fa-user icon-white\"></i> Menu<span class=\"caret\"></span></a>
								<ul class=\"dropdown-menu dropdown-primary\">";
							$filename='editar_pedido.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1'){
								if ($finalizada==0 && $anulada==0){
									//echo"<li><a data-toggle='modal' href='$filename?id_pedido=" .  $row ['id_pedido']."&process=formAnular"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i>Anular</a></li>";
									echo"<li><a  href='$filename?id_pedido=" .  $row ['id_pedido']."'><i class='fa fa-check'></i> Editar</a></li>";
								}
							}
							$filename='anular_pedido.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1'){
								if ($finalizada==0 && $anulada==0){
									echo"<li><a data-toggle='modal' href='$filename?id_pedido=" .  $row ['id_pedido']."&process=formAnular"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i>Anular</a></li>";
								}
							}
							$filename='ver_reporte_pedido.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' ){
								echo "<li><a href='$filename?numero_doc=".$ndoc."&id_sucursal=$id_sucursal' target='_blank' ><i class=\"fa fa-search\"></i> Ver Detalle</a></li>";
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
							<div class='modal-content modal-sm'></div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
					<div class='modal fade' id='deleteModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
						<div class='modal-dialog'>
							<div class='modal-content modal-sm'></div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
					<!--Show Modal Popup View Fact -->
					<div class='modal fade' id='viewModalFact' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
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
	echo" <script type='text/javascript' src='js/funciones/funciones_admin_pedido.js'></script>";
		} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
?>
