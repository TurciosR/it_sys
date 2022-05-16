<?php
include_once "_core.php";

function initial() {
	// Page setup
	$id_user=$_SESSION["id_usuario"];

	$_PAGE = array ();
	$_PAGE ['title'] = 'Agregar Empresa';
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
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user, $filename);
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
                            <h5>Registrar Empresa</h5>
                        </div>
                        <div class="ibox-content">


                                <form name="formulario" id="formulario">

                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Nombre</label> 
                                    <input type="text" placeholder="Nombre empresa" class="form-control" id="empresa" name="empresa">
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Razón Social</label> 
                                    <input type="text" placeholder="Razón Social" class="form-control" id="razon" name="razon">
                                  </div>
                                </div>
                              </div>
                              
                              <div class="form-group has-info single-line">
                                  <label>Dirección</label> 
                                  <input type="text" placeholder="Dirección" class="form-control" id="direccion" name="direccion">
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                      <label>Teléfono 1</label> 
                                      <input type="text" placeholder="Teléfono 1" class="form-control" id="telefono1" name="telefono1">
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Teléfono 2</label> 
                                    <input type="text" placeholder="Teléfono 2" class="form-control" id="telefono2" name="telefono2">
                                  </div>
                                </div>
                              </div>
                              
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>NIT</label> 
                                    <input type="text" placeholder="NIT" class="form-control" id="nit" name="nit">
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>NRC</label> 
                                    <input type="text" placeholder="NRC" class="form-control" id="nrc" name="nrc">
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>IVA</label> 
                                    <input type="text" placeholder="IVA" class="form-control" id="iva" name="iva">
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Giro</label> 
                                    <input type="text" placeholder="Giro" class="form-control" id="giro" name="giro">
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Monto inicial de retención 1%</label> 
                                    <input type="text" placeholder="Monto inicial de retencion 1%" class="form-control" id="monto_retencion1" name="monto_retencion1">
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Monto inicial de retención 10%</label> 
                                    <input type="text" placeholder="Monto inicial de retencion 10%" class="form-control" id="monto_retencion10" name="monto_retencion10">
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Monto inicial de percepción</label> 
                                    <input type="text" placeholder="Monto inicial de percepción" class="form-control" id="monto_percepcion" name="monto_percepcion">
                                  </div>
                                </div>
                              </div>
                    				    <input type="hidden" name="process" id="process" value="insert">
                                <div>
                                  <input type="submit" id="submit1" name="submit1" value="Submit" class="btn btn-primary m-t-n-xs" />
                                </div>
                                </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>

<?php
include_once ("footer.php");
echo "<script src='js/funciones/funciones_empresa.js'></script>";
		} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}


function insertar_empresa(){

	$empresa=$_POST["empresa"];
  $razon=$_POST["razon"];
  $direccion=$_POST["direccion"];
  $telefono1=$_POST["telefono1"];
  $telefono2=$_POST["telefono2"];
  $nit=$_POST["nit"];
	$nrc=$_POST["nrc"];
  $iva=$_POST["iva"];
  $giro = $_POST["giro"];
  $monto_retencion1 = $_POST["monto_retencion1"];
  $monto_retencion10 = $_POST["monto_retencion10"];
  $monto_percepcion = $_POST["monto_percepcion"];

    $sql_result=_query("SELECT idempresa,nombre FROM empresa WHERE nombre='$empresa'");
    $numrows=_num_rows($sql_result);
    //$num=_num_rows($contar);


    $table = 'empresa';
    $form_data = array (
		'nombre' => $empresa,
    'razonsocial' => $razon,
    'direccion' => $direccion,
    'telefono1' => $telefono1,
    'telefono2' => $telefono2,
    'nit' => $nit,
		'nrc' => $nrc,
    'iva' => $iva,
    'giro' => $giro,
    'monto_retencion1' => $monto_retencion1,
    'monto_retencion10' => $monto_retencion10,
    'monto_percepcion' => $monto_percepcion
    );
    if($numrows == 0 ){

    $insertar = _insert($table,$form_data);
    echo _error();
    if($insertar){
       //$field='id_country';
       //$max_country = max_id($field,$table);
       //$xdatos['max_id']=$max_country;
       $xdatos['typeinfo']='Success';
       $xdatos['msg']='Registro insertado correctamente !';
       $xdatos['process']='insert';
    }
    else{
       $xdatos['typeinfo']='Error';
       $xdatos['msg']='Registro no insertado !';
		}
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
	case 'insert':
		insertar_empresa();
		break;

	}
}
}
?>
