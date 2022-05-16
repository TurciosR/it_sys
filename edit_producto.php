<?php
include_once "_core.php";
if(!isset($_REQUEST['id_producto'])){
	 $id_producto = -1;
}
else{
	$id_producto = $_REQUEST['id_producto'];
}
	$_PAGE = array ();
	$title='Editar Producto';
	$_PAGE ['title'] = $title;
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet">';
	$_PAGE ['links'] .='<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2-bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/upload_file/fileinput.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	//permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	$img_ruta="img/productos/no_disponible.png";
?>
  <div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
      <div class="col-lg-12">
        <div class="ibox">
			  <?php
						//permiso del script
					if ($links!='NOT' || $admin=='1' ){
						echo '<input type="hidden" name="id_producto" id="id_producto" value="'.$id_producto.'">';
					?>
          <div class="ibox-title">
          <h5><?php echo $title;?></h5>
          </div>
          <div class="ibox-content">
					<div class="panel blank-panel">
							<div class="panel-heading">
									<div class="panel-title m-b-md"><h4>Informacion de Partes</h4></div>
									<div class="panel-options">
										<div id="tabs">
											<ul class="nav nav-tabs">
												<li class="active" id='tabsform'><a href="#tab1" id='tab-1' data-url="editar_producto1.php?id_producto=<?php echo $id_producto;?>">Informacion General</a></li>
												<li><a href="#tab2" id='ap2' data-url="editar_producto2.php?id_producto=<?php echo $id_producto;?>">Costos-Precios</a></li>
												<li><a  href="#tab3" id='ap3' data-url="editar_producto3.php?id_producto=<?php echo $id_producto;?>">Ubicación</a></li>
												<li><a  href="#tab3" id='ap4' data-url="editar_producto4.php?id_producto=<?php echo $id_producto;?>">Ofertas</a></li>
                        <li><a  href="#tab3" id='ap5' data-url="editar_producto5.php?id_producto=<?php echo $id_producto;?>">Imágen</a></li>
											</ul>
										</div>
									</div> <!--div class="panel-options"-->
								</div><!--div class="panel-heading"-->
                <div class="panel-body">
                  <div class="tab-content">
                    <div id="tab1" class="tab-pane active"></div><!--div id="tab1" class="tab-pane"-->
                    <div id="tab2" class="tab-pane active"></div><!--div id="tab2" class="tab-pane"-->
                    <div id="tab3" class="tab-pane active"></div><!--div id="tab3" class="tab-pane"-->
                    <div id="tab4" class="tab-pane active"></div><!--div id="tab4" class="tab-pane"-->
										<div id="tab5" class="tab-pane active"></div><!--div id="tab4" class="tab-pane"-->
                  </div>
                </div>
                <!--div id="tab-1" class="tab-pane"-->
              </div>
						 </div><!--div class='ibox-content'-->
					 </div><!--<div class='ibox float-e-margins' -->
				</div> <!--div class='col-lg-12'-->
	   </div> <!--div class='row'-->
	</div><!--div class='wrapper wrapper-content  animated fadeInRight'-->
<?php
include_once ("footer.php");
echo "<script src='js/funciones/funciones_edit_producto.js'></script>";

} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
?>
