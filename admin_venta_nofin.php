<?php
	include ("_core.php");
	// Page setup
	$title='Administrar Facturas No Finalizadas';
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

 	//permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

	$id_sucursal=$_SESSION['id_sucursal'];
	//permiso del script

	$sql="SELECT factura.*, cliente.nombre,cliente.apellido,
	usuario.nombre as nombreuser
	FROM factura
	JOIN cliente ON factura.id_cliente=cliente.id_cliente
	JOIN usuario ON factura.id_usuario=usuario.id_usuario
	WHERE finalizada=0
	AND anulada=0
	AND factura.id_sucursal='$id_sucursal'";
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
						  <button type="button" id="btnReload" name="btnReload" class="btn btn-primary"><i class="fa fa-refresh"></i> Recargar</button>
						<table class="table table-striped table-bordered table-hover"id="editable">
							<thead>
								<tr>
									<th>Id factura</th>
									<th>Cliente</th>
									<th>Empleado</th>
									<th>Fecha</th>
									<th>Numero Doc</th>
									<th>Total</th>
									<th>Acci&oacute;n</th>
								</tr>
							</thead>
							<tbody>
				<?php
 					if ($count>0){
						for($i=0;$i<$count;$i++){
							$row=_fetch_array($result);
							$id_factura=$row ['id_factura'];
							$numero_doc=$row['numero_doc'];
							$anulada=$row['anulada'];
							$cliente=$row['nombre']." ".$row['apellido'];
							$nombre_empleado=$row['nombreuser'];
							if($anulada==1)
								$txt_anulada=' (ANULADA)';
							else
								$txt_anulada='';
							echo "<tr>";
							echo "<td>".$row['id_factura']."</td>";
							echo "<td> <h5 class='text-primary'>".$cliente."</h5></td>";
							echo "<td> <h5 class='text-warning'>".$nombre_empleado."</h5></td>";
							echo "<td>".$row['fecha']."</td>";
							echo "<td> <h5 class='text-danger'>".$numero_doc.$txt_anulada."</h5></td>";
							echo "<td><h5 class='text-success'>"."$ ".$row['total']."</h5></td>";
							echo"<td><div class=\"btn-group\">
								<a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\"><i class=\"fa fa-user icon-white\"></i> Menu<span class=\"caret\"></span></a>
								<ul class=\"dropdown-menu dropdown-primary\">";

							$filename='imprimir_factura.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' )
								echo "<li><a data-toggle='modal' href='imprimir_factura.php?id_factura=".$row['id_factura']."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-print\"></i> Imprimir y Finalizar</a></li>";

							$filename='finalizar_factura.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' )
								echo "<li><a data-toggle='modal' href='$filename?id_factura=".$row['id_factura']."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-check\"></i> Finalizar Venta</a></li>";

							$filename='anular_factura.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' )
									echo "<li><a data-toggle='modal' href='anular_factura.php?id_factura=" .  $row ['id_factura']."&process=formDelete"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i> Anular</a></li>";

							$filename='ver_factura.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' )
								echo "<li><a data-toggle='modal' href='$filename?id_factura=$id_factura&numero_doc=$numero_doc&id_sucursal=$id_sucursal'  data-target='#viewModalFact' data-refresh='true'><i class=\"fa fa-check\"></i> Ver Factura</a></li>";

							echo "</ul>
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
	echo" <script type='text/javascript' src='js/funciones/funciones_admin_fact.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
?>
