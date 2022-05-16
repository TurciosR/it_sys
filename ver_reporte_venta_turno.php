<?php
include_once "_core.php";

function initial() {
	// Page setup
	$id_user=$_SESSION["id_usuario"];

	$_PAGE = array ();
	$_PAGE ['title'] = 'Reporte Ventas Diarias';
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/jqGrid/ui.jqgrid.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/fileinput/fileinput.css" media="all" rel="stylesheet" type="text/css"/>';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
  date_default_timezone_set('America/El_Salvador');
  $fecha_actual = date('Y-m-d');
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user, $filename);
 // echo "SELECT * FROM detalle_apertura WHERE fecha = '$fecha_actual'";
  
	//permiso del script
	if ($links!='NOT' || $admin=='1' ){
?>
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-2">
                </div>
            </div>
        <div class="wrapper wrapper-content  animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Reporte Ventas Diarias por Turno</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group has-info single-line">
                                  <label>Sucursal</label> 
                                  <select class="select form-control" id="sucursal">
                                    <?php
                                      $sql_sucursal = _query("SELECT * FROM sucursal");
                                      $cuenta = _num_rows($sql_sucursal);
                                      if($cuenta > 0)
                                      {
                                        while ($row1 = _fetch_array($sql_sucursal)) 
                                        {
                                          $sucursal = $row1["descripcion"];
                                          $id_su = $row1["id_sucursal"];
                                          echo "<option value='".$id_su."'>".$sucursal."</option>";
                                        }
                                      }
                                    ?>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group has-info single-line">
                                  <label>Fecha Inicio</label> 
                                  <input type="text" class="form-control datepick" id="fecha11" name="fecha11" value="<?php echo date('Y-m-d');?>">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group has-info single-line">
                                  <label>Turno</label> 
                                  <select class="form-control" id="turno">
                                    <?php
                                      $sql_detalle = _query("SELECT * FROM detalle_apertura WHERE fecha = '$fecha_actual' order by turno");
                                      $cuenta = _num_rows($sql_detalle);
                                      if($cuenta > 0)
                                      {
                                        while ($row = _fetch_array($sql_detalle)) 
                                        {
                                          $turno = $row["turno"];
                                          echo "<option value='".$turno."'>".$turno."</option>";
                                        }
                                      }
                                      else
                                      {
                                        echo "<option value=''>No se han encontrado turnos para este dia</option>";
                                      }
                                    ?>
                                  </select>
                                </div>
                              </div>
                            </div>
                  				  <input type="hidden" name="process" id="process" value="edit">
                            <div class="row">
                              <div class="col-md-12">
                                <div class="form-group">
                                  <input type="hidden" name="id_sucursal" id="id_sucursal">
                                  <input type="submit" id="btnTur" name="btnTur" value="Imprimir" class="btn btn-primary m-t-n-xs pull-right" />
                                </div>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

<?php
include_once ("footer.php");
echo "<script src='js/funciones/funciones_costo_ganancia.js'></script>";
		} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function turnos()
{
  $fecha = $_POST["fecha"];
  $lista = "";
  $sql_detalle = _query("SELECT * FROM detalle_apertura WHERE fecha = '$fecha' order by turno");
  $cuenta = _num_rows($sql_detalle);
  if($cuenta > 0)
  {
    while ($row = _fetch_array($sql_detalle)) 
    {
      $turno = $row["turno"];
      $lista .= "<option value='".$turno."'>".$turno."</option>";
    }
  }
  else
  {
    $lista .="<option value=''>No se han encontrado turnos para este dia</option>";
  }
  echo $lista;
}

if(!isset($_POST['process'])){
	initial();
}
else
{
if(isset($_POST['process'])){
switch ($_POST['process']) {
	case 'turno':
    turnos();
		break;

	}
}
}
?>
