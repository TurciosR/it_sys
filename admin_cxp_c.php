<?php
	include ("_core.php");
	// Page setup
	$title =  'Administrar impresion de Voucher';
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



	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$id_sucursal=$_SESSION["id_sucursal"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	$id_proveedor=$_REQUEST['id_proveedor'];
	/*
	SELECT COUNT(*) AS abono_rel,abono_cxp.monto_cheque,abono_cxp.cheque,abono_cxp.fecha_cheque FROM cxp JOIN abono_cxp ON abono_cxp.idtransace=cxp.idtransace WHERE cxp.id_proveedor=3 GROUP BY abono_cxp.cheque ORDER BY abono_cxp.fecha_cheque DESC
	*/

	$sql="SELECT COUNT(DISTINCT abono_cxp.cheque) AS cheques,SUM(abono_cxp.monto) AS montocheques,abono_cxp.fecha,abono_cxp.hora FROM cxp JOIN abono_cxp ON abono_cxp.idtransace=cxp.idtransace JOIN bancos ON bancos.id_banco=abono_cxp.id_banco JOIN cuenta_bancos ON cuenta_bancos.id_cuenta=abono_cxp.id_cuenta WHERE cxp.id_proveedor=$id_proveedor GROUP BY abono_cxp.hora,abono_cxp.fecha ORDER BY abono_cxp.fecha DESC ";


//$user=mysql_fetch_array($query1);

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
					echo"<div class='ibox-title'></div>";


				?>
				<div class="ibox-content">
					<!--load datables estructure html-->
					<header>
						<h4><?php echo  $title; ?></h4>
					</header>
					<section>
						<table class="table table-striped table-bordered table-hover " class="display" id="example">
							<thead>
								<tr>
									<th class="col-lg-2">Numero de cheques</th>
									<th class="col-lg-2">Monto de los cheque</th>
									<th class="col-lg-3">Fecha</th>
									<th class="col-lg-3">Hora</th>
									<th class="col-lg-2">Acci&oacute;n</th>
								</tr>
							</thead>
							<tbody>
							<?php
			 					while($row=_fetch_array($result))
			 					{

									echo "<tr>";
									echo"
									<td>".$row['cheques']."</td>
									<td>".number_format($row['montocheques'],2)."</td>
									<td>".$row['fecha']."</td>
									<td>".$row['hora']."</td>"
										;

									echo"<td><div class=\"btn-group\">
										<a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\"><i class=\"fa fa-user icon-white\"></i> Menu<span class=\"caret\"></span></a>
										<ul class=\"dropdown-menu dropdown-primary\">";
										$filename='editar_almacen.php';
										$link=permission_usr($id_user,$filename);
										if ($link!='NOT' || $admin=='1' )
											echo "<li><a href=\"ver_voucher.php?id_proveedor=".$id_proveedor."&fecha=$row[fecha]&hora=$row[hora]"."\" target='_blank'><i class=\"fa fa-print\"></i> Imprimir</a></li>";
									}
									echo "</ul></div>";

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
	echo" <script type='text/javascript' src='js/funciones/dt.js'></script>";
	} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
?>
