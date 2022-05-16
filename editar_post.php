<?php
include_once "_core.php";

function initial() {
    // Page setup
    $id_user=$_SESSION["id_usuario"];

    $_PAGE = array ();
    $_PAGE ['title'] = 'Editar Cabecera';
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

    include_once "header.php";
    include_once "main_menu.php";
    //permiso del script
    $id_conf = $_REQUEST["id_conf"];

    $id_user=$_SESSION["id_usuario"];
    $admin=$_SESSION["admin"];

    $uri = $_SERVER['SCRIPT_NAME'];
    $filename=get_name_script($uri);
    $links=permission_usr($id_user, $filename);

  $sql_pos = _query("SELECT * FROM config_pos WHERE id_config_pos = '$id_conf'");
  $cuenta = _num_rows($sql_pos);

  if($cuenta > 0)
  {
    $row = _fetch_array($sql_pos);
    $header1=$row["header1"];
    $header2=$row["header2"];
    $header3=$row["header3"];
    $header4=$row["header4"];
    $header5=$row["header5"];
    $header6=$row["header6"];
    $header7=$row["header7"];
    $header8=$row["header8"];
    $header9=$row["header9"];
    $header10=$row["header10"];
    $footer1=$row["footer1"];
    $footer2=$row["footer2"];
    $footer3=$row["footer3"];
    $footer4=$row["footer4"];
    $footer5=$row["footer5"];
    $footer6=$row["footer6"];
    $footer7=$row["footer7"];
    $footer8=$row["footer8"];
    $footer9=$row["footer9"];
    $footer10=$row["footer10"];
    $id_sucursal = $row["id_sucursal"];
    $id_config_pos = $row["id_config_pos"];
    $accion = "Modificar";
  }
  else
  {
    $dir_print_script = "";
    $shared_printer_matrix= "";
    $shared_printer_pos= "";
    $shared_printer_barcode= "";
    $header1= "";
    $header2= "";
    $header3= "";
    $header4= "";
    $header5= "";
    $footer1= "";
    $footer2= "";
    $footer3= "";
    $footer4= "";
    $footer5= "";
    $id_sucursal = "";
    $accion = "Insertar";
  }
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
                            <h5>Editar Cabecera</h5>
                        </div>
                        <div class="ibox-content">
                            <form name="formulario" id="formulario" autocomplete='off'>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Header 1</label> 
                                    <input type="text"  class="form-control dis" id="header1" name="header1" value="<?php echo $header1;?>" >
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Header 2</label> 
                                    <input type="text"  class="form-control dis" id="header2" name="header2" value="<?php echo $header2;?>" >
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Header 3</label> 
                                    <input type="text"  class="form-control dis" id="header3" name="header3" value="<?php echo $header3;?>" >
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Header 4</label> 
                                    <input type="text"  class="form-control dis" id="header4" name="header4" value="<?php echo $header4;?>" >
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Header 5</label> 
                                    <input type="text"  class="form-control dis" id="header5" name="header5" value="<?php echo $header5;?>" >
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Header 6</label> 
                                    <input type="text"  class="form-control dis" id="header6" name="header6" value="<?php echo $header6;?>" >
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Header 7</label> 
                                    <input type="text"  class="form-control dis" id="header7" name="header7" value="<?php echo $header7;?>" >
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Header 8</label> 
                                    <input type="text"  class="form-control dis" id="header8" name="header8" value="<?php echo $header8;?>" >
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Header 9</label> 
                                    <input type="text"  class="form-control dis" id="header9" name="header9" value="<?php echo $header9;?>" >
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Header 10</label> 
                                    <input type="text"  class="form-control dis" id="header10" name="header10" value="<?php echo $header10;?>" >
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Footer 1</label> 
                                    <input type="text"  class="form-control dis" id="footer1" name="footer1" value="<?php echo $footer1;?>" >
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Footer 2</label> 
                                    <input type="text"  class="form-control dis" id="footer2" name="footer2" value="<?php echo $footer2;?>" >
                                  </div>
                                </div>
                              </div>

                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Footer 3</label> 
                                    <input type="text"  class="form-control dis" id="footer3" name="footer3" value="<?php echo $footer3;?>" >
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Footer 4</label> 
                                    <input type="text"  class="form-control dis" id="footer4" name="footer4" value="<?php echo $footer4;?>" >
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Footer 5</label> 
                                    <input type="text"  class="form-control dis" id="footer5" name="footer5" value="<?php echo $footer5;?>" >
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Footer 6</label> 
                                    <input type="text"  class="form-control dis" id="footer6" name="footer6" value="<?php echo $footer6;?>" >
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Footer 7</label> 
                                    <input type="text"  class="form-control dis" id="footer7" name="footer7" value="<?php echo $footer7;?>" >
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Footer 8</label> 
                                    <input type="text"  class="form-control dis" id="footer8" name="footer8" value="<?php echo $footer8;?>" >
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Footer 9</label> 
                                    <input type="text"  class="form-control dis" id="footer9" name="footer9" value="<?php echo $footer9;?>" >
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Footer 10</label> 
                                    <input type="text"  class="form-control dis" id="footer10" name="footer10" value="<?php echo $footer10;?>" >
                                  </div>
                                </div>
                              </div>
                              <input type="hidden" name="process" id="process" value="edit">
                              <div class="row">
                              <input type="hidden" name="action" id="action" value="<?php echo $accion;?>">
                              <input type="hidden" name="id_post" id="id_post" <?php if($cuenta>0){ echo "value='$id_config_pos'";} ?>>
                              <input type="submit" id="submit1" name="submit1" value="Guardar" class="btn btn-primary m-t-n-xs pull-right" />  
                              </div>
                          </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>

<?php
include_once ("footer.php");
echo "<script src='js/funciones/funciones_pos.js'></script>";
        } //permiso del script
else {
        echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
    }
}


function editar_post()
{
  $header1 = $_POST["header1"];
  $header2 = $_POST["header2"];
  $header3 = $_POST["header3"];
  $header4 = $_POST["header4"];
  $header5 = $_POST["header5"];
  $header6 = $_POST["header6"];
  $header7 = $_POST["header7"];
  $header8 = $_POST["header8"];
  $header9 = $_POST["header9"];
  $header10 = $_POST["header10"];
  $footer1 = $_POST["footer1"];
  $footer2 = $_POST["footer2"];
  $footer3 = $_POST["footer3"];
  $footer4 = $_POST["footer4"];
  $footer5 = $_POST["footer5"];
  $footer6 = $_POST["footer6"];
  $footer7 = $_POST["footer7"];
  $footer8 = $_POST["footer8"];
  $footer9 = $_POST["footer9"];
  $footer10 = $_POST["footer10"];
  $id_post = $_POST["id_post"];

  $tabla = "config_pos";
  $form_data = array(
    'header1' => $header1, 
    'header2' => $header2, 
    'header3' => $header3, 
    'header4' => $header4, 
    'header5' => $header5, 
    'header6' => $header6, 
    'header7' => $header7, 
    'header8' => $header8, 
    'header9' => $header9, 
    'header10' => $header10,
    'footer1' => $footer1,
    'footer2' => $footer2,
    'footer3' => $footer3,
    'footer4' => $footer4,
    'footer5' => $footer5,
    'footer6' => $footer6,
    'footer7' => $footer7,
    'footer8' => $footer8,
    'footer9' => $footer9,
    'footer10' => $footer10,
    );
  $where_post = "id_config_pos='".$id_post."'";

  $editar = _update($tabla, $form_data, $where_post);

  if($editar)
  {
    _commit(); // transaction is committed
    $xdatos['typeinfo']='Success';
    $xdatos['msg']='Datos actualizados correctamente !';
  }
  else
  {
    $xdatos['typeinfo']='Error';
    $xdatos['msg']='Fallo al actualizar los datos !'._error();
  }
  echo json_encode($xdatos);
}

if(!isset($_POST['process'])){
    initial();
}
else
{
if(isset($_POST['process'])){
switch ($_POST['process']) {
    case 'edit':
    editar_post();
        break;

    }
}
}
?>
