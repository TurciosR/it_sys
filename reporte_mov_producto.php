<?php
include_once "_core.php";

function initial()
{
    $title='Reporte de Movimiento de Productos por Rango de Fechas';
    $_PAGE = array();
    $_PAGE ['title'] = $title;
    $_PAGE ['links'] = null;
    $_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';

    $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

    include_once "header.php";
    include_once "main_menu.php";
    $id_sucursal=$_SESSION['id_sucursal'];
    //permiso del script
$id_user=$_SESSION["id_usuario"];
    $admin=$_SESSION["admin"];
    $uri = $_SERVER['SCRIPT_NAME'];
    $filename=get_name_script($uri);
    $links=permission_usr($id_user, $filename);
//permiso del script
if ($links!='NOT' || $admin=='1') {
    ?>
  <div class="row wrapper border-bottom white-bg page-heading">
  	<div class="col-lg-2"></div>
  </div>
  <div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5><?php echo $title; ?></h5>
                        </div>
                        <div class="ibox-content">
                              <!--form name="formulario" id="formulario" method='GET' action='reporte_mov_productos_pdf.php' target='_blank'-->
                              <div class="row">
                                <div class="col-md-4">
                                  <div class="form-group has-info single-line"><label>Fecha Inicio</label> <input type="text" placeholder="Fecha Inicio" class="datepick form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo date("Y-m-d"); ?>"></div>
                                </div>
                                 <div class="col-md-4">
                                  <div class="form-group has-info single-line"><label>Fecha Fin</label> <input type="text" placeholder="Fecha Fin" class="datepick form-control" id="fecha_fin" name="fecha_fin" value="<?php echo date("Y-m-d"); ?>"></div>
                                </div>
                              </div>
                              <div>
                                <button type="submit" id="print1" class="btn btn-primary"><i class="fa fa-print"></i> Imprimir</button>
                                <button type="submit" id="excel1" class="btn btn-primary"><i class="fa fa-check"></i> convertir a Excel</button>
                              </div>
                               <!--/form-->
                        </div>
              </div>
        </div>
    </div>
</div>
<?php
include_once("footer.php");
echo" <script type='text/javascript' src='js/funciones/funciones_kardex.js'></script>";
} //permiso del script
else {
    echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
}
}

if (!isset($_POST['process'])) {
    initial();
} else {
    if (isset($_POST['process'])) {
        switch ($_POST['process']) {
    case 'insert':
        insertar();
        break;

    }
    }
}
?>
