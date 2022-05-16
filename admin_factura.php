<?php
	include ("_core.php");
	// Page setup
	$_PAGE = array ();
	$title='Administrar Factura';
	$_PAGE ['title'] = $title;
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	//$_PAGE ['links'] .= '<link href="css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">';
	//$_PAGE ['links'] .= '<link href="css/plugins/jqGrid/ui.jqgrid.css" rel="stylesheet">';
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

	$sql="SELECT factura.*, cliente.nombre,cliente.apellido,
	usuario.nombre as nombreuser
	FROM factura
	JOIN cliente ON factura.id_cliente=cliente.id_cliente
	JOIN usuario ON factura.id_usuario=usuario.id_usuario
	WHERE  factura.id_sucursal='$id_sucursal'
	order by finalizada";
	$result=_query($sql);
	$count=_num_rows($result);

?>

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row" id="row1">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<?php
				$filename='facturacion.php';
				$link=permission_usr($id_user,$filename);
				echo "<div class='ibox-title'>";
				if ($link!='NOT' || $admin=='1' )
					echo "<a href='facturacion.php' class='btn btn-primary' role='button'><i class='fa fa-plus icon-large'></i> Agregar factura</a>";
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
						<table class="table table-striped table-bordered table-hover" id="editable2">
							<thead>
								<tr>
									<th>Id factura</th>
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
							/*
							$id_factura=$row ['id_factura'];
							$numero_doc=$row['numero_doc'];
							$anulada=$row['anulada'];
							$cliente=$row['nombre']." ".$row['apellido'];
							if($anulada==1)
								$txt_anulada=' (ANULADA)';
							else
								$txt_anulada='';
							echo "<tr>";
							echo "<td>".$row['id_factura']."</td>";
							echo "<td>".$cliente."</td>";
							echo "<td>".$row['fecha']."</td>";
							echo "<td>".$numero_doc.$txt_anulada."</td>";

							echo "<td>"."$ ".$row['total']."</td>";
							echo"<td><div class=\"btn-group\">
								<a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\"><i class=\"fa fa-user icon-white\"></i> Menu<span class=\"caret\"></span></a>
								<ul class=\"dropdown-menu dropdown-primary\">";
							$filename='reimprimir_factura.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' ){
								echo "<li><a data-toggle='modal' href='reimprimir_factura.php?id_factura=".$row['id_factura']."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-print\"></i> Reimprimir</a></li>";
							}
							$filename='anular_factura.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' ){
									echo "<li><a data-toggle='modal' href='anular_factura.php?id_factura=" .  $row ['id_factura']."&process=formDelete"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i> Anular</a></li>";

							}
							$filename='ver_factura.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' )
								echo "<li><a data-toggle='modal' href='$filename?id_factura=$id_factura&numero_doc=$numero_doc&id_sucursal=$id_sucursal'  data-target='#viewModalFact' data-refresh='true'><i class=\"fa fa-check\"></i> Ver Factura</a></li>";

							echo "	</ul>
										</div>
										</td>
										</tr>";

						*/

						$numero_doc=$row['numero_doc'];
							$id_factura=$row ['id_factura'];
							$anulada=$row['anulada'];
							$cliente=$row['nombre']." ".$row['apellido'];

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
								echo "<td>".$row['id_factura']."</td>";
								echo "<td> <h5 class='text-mutted'>".$cliente."</h5></td>";
								echo "<td> <h5 class='text-mutted'>".$nombre_empleado."</h5></td>";
								echo "<td>".$row['fecha']."</td>";
								echo "<td> <h5 class='text-mutted'>".$numero_doc."</h5></td>";
								echo "<td><h5 class='text-mutted'>"."$ ".$row['total']."</h5></td>";
								echo "<td><h5 class='text-mutted'>".$txt_estado."</h5></td>";
							}
							if($txt_estado=='PENDIENTE'){
								echo "<td><strong>".$row['id_factura']."</strong></td>";
								echo "<td> <h5 class='text-primary'>".$cliente."</h5></td>";
								echo "<td> <h5 class='text-warning'>".$nombre_empleado."</h5></td>";
								echo "<td>".$row['fecha']."</td>";
								echo "<td> <h5 class='text-danger'>".$numero_doc."</h5></td>";
								echo "<td><h5 class='text-success'>"."$ ".$row['total']."</h5></td>";
								echo "<td><h5 class='text-danger'>".$txt_estado."</h5></td>";
							}
							echo"<td><div class=\"btn-group\">
								<a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\"><i class=\"fa fa-user icon-white\"></i> Menu<span class=\"caret\"></span></a>
								<ul class=\"dropdown-menu dropdown-primary\">";
								/*
							$filename='imprimir_factura.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' )
								echo "<li><a data-toggle='modal' href='imprimir_factura.php?id_factura=".$row['id_factura']."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-print\"></i> Imprimir</a></li>";
							*/
							$filename='anular_factura.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' )
								echo "<li><a data-toggle='modal' href='anular_factura.php?id_factura=" .  $row ['id_factura']."&process=formDelete"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i> Anular</a></li>";

							$filename='editar_factura.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1'){
								if($finalizada==0 && $anulada==0){
									echo "<li><a  href='editar_factura.php?id_factura=$id_factura&numero_doc=$numero_doc&id_sucursal=$id_sucursal&process=formEdit' ><i class=\"fa fa-pencil\"></i> Editar</a></li>";
								}
							}
							$filename='ver_factura.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' )
								echo "<li><a data-toggle='modal' href='$filename?id_factura=$id_factura&numero_doc=$numero_doc&id_sucursal=$id_sucursal'  data-target='#viewModalFact' data-refresh='true'><i class=\"fa fa-check\"></i> Ver Factura</a></li>";
							//Reimprimir factura
							$filename='reimprimir_factura.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' ){
								if($finalizada==1)
									echo "<li><a data-toggle='modal' href='reimprimir_factura.php?id_factura=".$row['id_factura']."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-print\"></i> Reimprimir</a></li>";
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
						<div class='modal-dialog'>
							<div class='modal-content modal-sm'></div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
					<!--Show Modal Popups View & Delete -->
					<div class='modal fade' id='viewModalFact' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
						<div class='modal-dialog'>
							<div class='modal-content modal-md'></div><!-- /.modal-content -->
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
