<?php
	include ("_core.php");
	// Page setup
	$title= 'Administrar Consignaciones';
	$_PAGE = array ();
	$_PAGE ['title'] =$title;
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

	//permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

	$id_sucursal=$_SESSION['id_sucursal'];

	$sql="SELECT consignacion.*, cliente.nombre,cliente.apellido,
	usuario.nombre as nombreuser
	FROM consignacion
	JOIN cliente ON consignacion.id_cliente=cliente.id_cliente
	JOIN usuario ON consignacion.id_usuario=usuario.id_usuario
	WHERE finalizada=0
	AND anulada=0
	AND consignacion.id_sucursal='$id_sucursal'";

	$result=_query($sql);
	$count=_num_rows($result);


?>

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row" id="row1">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<?php

				echo"<div class='ibox-title'>";
				$filename='consignacion.php';
				$link=permission_usr($id_user,$filename);
				if ($link!='NOT' || $admin=='1' )
				echo "<a href='consignacion.php' class='btn btn-primary' role='button'><i class='fa fa-plus icon-large'></i> Agregar consignacion</a>";
				echo "</div>";


						//permiso del script
						if ($links!='NOT' || $admin=='1' ){
						echo"
                        <div class='ibox-title'>
                            <h5>$title</h5>
                        </div>";
                        ?>

				<div class="ibox-content">
					<!--load datables estructure html-->

					<section>
						<table class="table table-striped table-bordered table-hover"id="editable">
							<thead>
								<tr>
									<th>Id Consigna.</th>
									<th>Cliente</th>
									<th>Empleado</th>
									<th>Fecha</th>
									<th>Numero Doc</th>
									<th>Total</th>
									<th>Abono / Saldo</th>
									<th>Acci&oacute;n</th>
								</tr>
							</thead>
							<tbody>
				<?php
 					if ($count>0){
						for($i=0;$i<$count;$i++){
							$row=_fetch_array($result);
							$id_consignacion=$row['id_consignacion'];
							$numero_doc=$row['numero_doc'];
							$anulada=$row['anulada'];
							$finalizada=$row['finalizada'];
							$cliente=$row['nombre']." ".$row['apellido'];
							$nombre_empleado=$row['nombreuser'];

							$total_consignado=$row['total'];
							$total_consignado_format=sprintf("%.2f",$total_consignado);
							//Traer sumatoria de facturas hechas con este id consignacion
							$n_facturado=0;
							$sql_facturado="SELECT sum(total) as total_consignado_fact
							FROM factura WHERE id_consignacion='$id_consignacion'
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

							if($anulada==1)
								$txt_anulada=' (ANULADA)';
							else
								$txt_anulada='';
							echo "<tr>";
							echo "<td>".$row['id_consignacion']."</td>";
							echo "<td> <h5 class='text-primary'>".$cliente."</h5></td>";
							echo "<td> <h5 class='text-warning'>".$nombre_empleado."</h5></td>";
							echo "<td>".$row['fecha']."</td>";
							echo "<td> <h5 class='text-danger'>".$numero_doc."</h5></td>";
							echo "<td><h5 class='text-warning'>"."$ ".$total_consignado_format."</h5></td>";
							echo "<td>";
							echo "<h5 class='text-success'>"."ABONADO $ ".$abono_consignado_format."</h5>";
							echo "<h5 class='text-danger'>"."SALDO PENDIENTE $ ".$saldo_pendiente_format."</h5>";
							echo"</td>";

							echo"<td><div class=\"btn-group\">
								<a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\"><i class=\"fa fa-user icon-white\"></i> Menu<span class=\"caret\"></span></a>
								<ul class=\"dropdown-menu dropdown-primary\">";
							$filename='finalizar_consignacion.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' ){
								echo"<li><a href=\"finalizar_consignacion.php?id_consignacion=".$row['id_consignacion']."\"><i class=\"fa fa-check\"></i> Finalizar</a></li>";
							}
							$filename='editar_consignacion.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' ){
								echo"<li><a href=\"$filename?id_consignacion=".$row['id_consignacion']."\"><i class=\"fa fa-pencil-square\"></i> Editar</a></li>";
							}

							$filename='facturar_consignacion.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' ){
								echo"<li><a href=\"$filename?id_consignacion=".$row['id_consignacion']."\"><i class=\"fa fa-check\"></i> Facturar Consignacion</a></li>";
							}

							/*$filename='editar_consignacion.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' ){
								echo"<li><a href=\"editar_consignacion.php?id_consignacion=".$row['id_consignacion']."\"><i class=\"fa fa-pencil\"></i> Editar</a></li>";
							}*/




							$filename='anular_consignacion.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' ){
								if ($finalizada==0 && $anulada==0){
									echo"<li><a data-toggle='modal' href='$filename?id_consignacion=" .  $row ['id_consignacion']."&process=formAnular"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i>Anular</a></li>";
								}
							}



							$filename='ver_reporte_consignacion.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' ){
								echo "<li><a href='$filename?numero_doc=".$ndoc."&id_sucursal=$id_sucursal' target='_blank' ><i class=\"fa fa-search\"></i> Ver Detalle</a></li>";
							}
							//Facturas Consignacion
							$filename='ver_facturas_consignacion.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' )
								echo"<li><a data-toggle='modal' href='$filename?id_consignacion=" .  $row ['id_consignacion']."' data-target='#viewModalFact' data-refresh='true'><i class=\"fa fa-plus\"></i> Ver Facturas Consignacion</a></li>";

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
	echo" <script type='text/javascript' src='js/funciones/funciones_admin_consignacion.js'></script>";
		} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
?>
