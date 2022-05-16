<?php
	include ("_core.php");
	// Page setup
	$_PAGE = array ();
	$_PAGE ['title'] = 'Administrar Despacho';
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/bootstrap.css" rel="stylesheet">';
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

 	//$sql="SELECT * FROM factura";
	$sql="SELECT factura.*, clientes.nombre
	FROM factura JOIN clientes ON factura.id_cliente=clientes.id_cliente
	WHERE finalizada=1 AND anulada=0 AND entregado=0 order BY id_factura DESC";
	$result=_query($sql);
	$count=_num_rows($result);

?>

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row" id="row1">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<!--load datables estructure html-->

						<div class="panel panel-primary">
					  	<div class="panel-heading text-center"><h1>Despachos Pendientes</h1>
							</div>
					  	<div class="panel-body">
								<div class="row" >

				<?php
 					if ($count>0){
						for($i=0;$i<$count;$i++){
							$row=_fetch_array($result);
							$num_doc=$row['num_fact_impresa'];
							$nom=$row['nombre'];
							$id_factura=$row['id_factura'];
							$tipo_pago=$row['tipo_documento'];
							$tipo="";
							if($tipo_pago=="COF"){
								$tipo="FAT ".$num_doc;
							}else {
								$tipo=$tipo_pago." ".$num_doc;
							}
							$total=$row['total'];
							$fecha=$row['fecha'];

							echo '

							<div class="col-lg-3 ">
								 <div class="thumbnail" style="background-color: rgb(217, 215, 249)">
										 <div class="caption ">
												 <div class="col-lg-12" >
														 <span class="glyphicon glyphicon-th-list"></span>
														 <span></span>
														 <strong>
														 ';
														 echo "Factura No: ".$tipo;
														 echo '
														 </strong>
												 </div>
												 <div class="col-lg-12 well well-add-card" style="width:210px; height:60px;">
														 <h4 style="margin-top:-15px; text-aling:center;">';
														 echo trim($nom);
														 echo '</h4>
												 </div>
												 <div class="col-lg-12" style="margin-top:-20px;">
														 <p><strong>Fecha:
														 ';
														 echo ED($fecha);
														 echo '<br>
														 Total: $';
														 echo $total;
														 echo '
														 </strong></p>
												 </div>
												  <a  href="despacho.php?id_factura='.$id_factura.'" class="btn btn-primary btn-xs btn-update btn-add-card " role="button"  ><i class=""></i> Despachar</a>
									   </div>
								 </div>
							 </div>
							';

						/*	echo "<tr>";
						<a data-toggle="modal" href="despacho.php?id_factura='.$id_factura.'" class="btn btn-primary btn-xs btn-update btn-add-card " role="button" data-target="#viewModalFact" data-refresh="true" ><i class=""></i> Despachar</a>

						<button type="button" class="btn btn-primary btn-xs btn-update btn-add-card">Despachar</button>
							echo "<td>".$row['id_factura']."</td>";
							echo "<td>".$cliente."</td>";
							echo "<td>".$row['fecha']."</td>";
							echo "<td>".$numero_doc.$txt_anulada."</td>";

							echo "<td>"."$ ".$row['total']."</td>";
							echo "<td class='td-actions'>
									<a href='javascript:;' class='btn btn-small btn-primary'>
										<i class='btn-icon-only icon-ok'></i>
									</a>

									<a href='javascript:;' class='btn btn-small'>
										<i class='btn-icon-only icon-remove'></i>
									</a>
								</td>";*/
							/*
							echo"<td>";
							echo "<div class=\"btn-group\">
								<a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\"><i class=\"fa fa-user icon-white\"></i> Menu<span class=\"caret\"></span></a>
								<ul class=\"dropdown-menu dropdown-primary\">";

									echo "<li><a data-toggle='modal' href='reimprimir_factura.php?id_factura=".$row['id_factura']."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-print\"></i> Reimprimir</a></li>";
									echo "<li><a data-toggle='modal' href='anular_factura.php?id_factura=" .  $row ['id_factura']."&process=formDelete"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i> Anular</a></li>";


							echo "	</ul>
									</div>";
							echo "	</td>";

							echo "	</tr>";*/
						}
					}

				?>
				</div>
			</div>
	</div>
							<!--/tbody>
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
						<div class='modal-dialog modal-md'>
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
	echo" <script type='text/javascript' src='js/funciones/admin_despacho.js'></script>";
?>
