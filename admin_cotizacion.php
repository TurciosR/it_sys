<?php
include ("_core.php");
// Page setup
$_PAGE = array ();
$title='Administrar Cotizaciones';
$_PAGE ['title'] = $title;
$_PAGE ['links'] = null;
$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
include_once "header.php";
include_once "main_menu.php";

//permiso del script

$admin=$_SESSION["admin"];
$uri = $_SERVER['SCRIPT_NAME'];
$filename=get_name_script($uri);
$links=permission_usr($id_user,$filename);
$fin=date("d-m-Y");
$fini = "01-".date("m")."-".date("Y");

?>

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row" id="row1">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<?php 	if($links!='NOT' || $admin=='1' ){
					$filename='cotizacion.php';
					$link=permission_usr($id_user,$filename);
					echo "<div class='ibox-title'>";
					if ($link!='NOT' || $admin=='1' )
					echo "<a href='cotizacion.php' class='btn btn-primary' role='button'><i class='fa fa-plus icon-large'></i> Agregar Cotización</a>";
					echo "</div>";
					//permiso del script
					?>
					<div class="ibox-content">
						<header>
							<h4><?php echo  $title; ?></h4>
						</header>
						<!--load datables estructure html-->
						<div class="row">
							<div class="input-group">
								<div class="col-md-4">
									<div class="form-group">
										<label>Fecha Inicio</label>
										<input type="text" placeholder="Fecha Inicio" class="datepick2 form-control" id="fini" name="fini" value="<?php echo $fini;?>">
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label>Fecha Fin</label>
										<input type="text" placeholder="Fecha Fin" class="datepick2 form-control" id="fin" name="fin" value="<?php echo $fin;?>">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<div><label>Buscar</label> </div>
										<button type="button" id="btnMostrar" name="btnMostrar" class="btn btn-primary"><i class="fa fa-check"></i> Mostrar</button>
									</div>
								</div>
							</div>
						</div>
						<section>
							<table class="table table-striped table-bordered table-hover" id="editable2">
								<thead>
									<tr>
										<th class="col-lg-1">Id</th>
										<th class="col-lg-1">Fecha</th>
										<th class="col-lg-4">Cliente</th>
										<th class="col-lg-1">Num Doc</th>
										<th class="col-lg-2">Vendedor</th>
										<th class="col-lg-1">Total</th>
										<th class="col-lg-1">Impresa</th>
										<th class="col-lg-1">Acci&oacute;n</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</section>
						<form id="frm1" class="" target="_self" action="ventas.php" method="POST">
							<input type="hidden" id="id_cotizacion" name="id_cotizacion" value="">
						</form>
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
						<div class='modal fade' id='viewModalCot' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
							<div class='modal-dialog'>
								<div class='modal-content modal-lg'></div><!-- /.modal-content -->
							</div><!-- /.modal-dialog -->
						</div><!-- /.modal -->

					</div><!--div class='ibox-content'-->
				</div><!--<div class='ibox float-e-margins' -->
				</div> <!--div class='col-lg-12'-->
			</div> <!--div class='row'-->
		</div><!--div class='wrapper wrapper-content  animated fadeInRight'-->
		<?php
		include("footer.php");
		echo" <script type='text/javascript' src='js/funciones/cotizacion.js'></script>";
	} //permiso del script
	else {
		$mensaje = mensaje_permiso();
		echo "<br><br>$mensaje</div></div></div></div>";
		include "footer.php";
	}

	?>
