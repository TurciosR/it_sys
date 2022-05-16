<?php
	include ("_core.php");
	// Page setup
	$title='Administrar Clausula';
	$_PAGE = array ();
	$_PAGE ['title'] = $title;
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
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

?>

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row" id="row1">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<?php
				if ($links!='NOT' || $admin=='1' ){

				echo "<div class='ibox-title'>";
				$filename='agregar_producto.php';
				$link=permission_usr($id_user,$filename);
				if ($link!='NOT' || $admin=='1' )
					echo "<a href='agregar_clausula.php' class='btn btn-primary' role='button'><i class='fa fa-plus icon-large'></i> Agregar Clausula</a>";


				echo	"</div>";

				?>
				<div class="ibox-content">
					<!--load datables estructure html-->
					<header>
						<h4>Administrar Productos</h4>
					</header>
					<section>
						<table class="table table-striped table-bordered table-hover" id="editable2">
							<thead>
								<tr>
									<th class="col-lg-1">ID</th>
									<th class="col-lg-2">Titulo</th>
									<th class="col-lg-8">Descripción</th>
									<th class="col-lg-1">Acci&oacute;n</th>
								</tr>
							</thead>
							<tbody>

              </tbody>
						</table>
						 <input type="hidden" name="autosave" id="autosave" value="false-0">
					</section>
          <form id="frm1" class="" target="_self" action="ventas.php" method="POST">
            <input type="hidden" id="id_contrato" name="id_contrato" value="">
            <input type="hidden" id="lista_cuota" name="lista_cuota" value="">
          </form>
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
					<div class='modal fade' id='addModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
						<div class='modal-dialog modal-lg'>
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
	echo" <script type='text/javascript' src='js/funciones/funciones_clausula.js'></script>";
	}
	else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
?>
