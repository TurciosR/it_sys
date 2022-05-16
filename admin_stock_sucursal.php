<?php
	include ("_core.php");
	// Page setup
	$title='Consultar Stock de Producto por Sucursal';
	$_PAGE = array ();
	$_PAGE ['title'] = $title;
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	//$_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
/*	$_PAGE ['links'] .= '<link href="css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/jqGrid/ui.jqgrid.css" rel="stylesheet">';*/
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
?>

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row" id="row1">
		<?php
						//permiso del script
						if ($links!='NOT' || $admin=='1' ){
						?>
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<!--/div>
				</div-->



				<div class="ibox-content" id='tittle'>
					<div class="row">

						<div class="col-md-6">
															 <div class="form-group">
									 <label>Seleccione Sucursal</label>


							 <?php
								 echo"<select  name='id_sucursal' id='id_sucursal'  class='form-control' style='width:300px;'>";
								 $sql="SELECT * FROM sucursal";
								 $result=_query($sql);
								 $count=_num_rows($result);
								 while($row_suc=_fetch_array($result))
								{
												$id_sucursal=$row_suc['id_sucursal'];
												$nombre_suc=$row_suc['descripcion'];
												echo "<option value='$id_sucursal'>$nombre_suc</option> ";
								}
							  echo "</select>";
						 		echo "</div>"; //<div class='form-group'>
											echo  "</div>";// <div class="col-lg-6">
							?>
						</div>
						<div class="row">
					<header>
						<h4 id='titulo'><?php echo $title; ?></h4>
					</header>
					<section>
						<table class="table table-striped table-bordered table-hover"id="editable2">
							<thead class="filters">
								<tr>
									<th>Codigo</th>
									<th>Descripción</th>
									<th>Color</th>
									<th>Estilo</th>
									<th>Talla</th>
									<th>Numeración</th>
									<th>Existencias</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
						 <input type="hidden" name="autosave" id="autosave" value="false-0">
					</section>
					</div>
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
	echo" <script type='text/javascript' src='js/funciones/funciones_stock_sucursal.js'></script>";
	} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
?>
