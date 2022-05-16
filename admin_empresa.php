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
  $_PAGE ['links'] .= '<link href="css/plugins/fileinput/fileinput.css" media="all" rel="stylesheet" type="text/css"/>';
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

  $sql_empresa = _query("SELECT * FROM empresa WHERE idempresa = 1");
  $cuenta = _num_rows($sql_empresa);

  if($cuenta > 0)
  {
    $row = _fetch_array($sql_empresa);
    $logo = $row["logo"];
    $nombre=$row['nombre'];
    $razon=$row["razonsocial"];
    $direccion=$row["direccion"];
    $telefono1=$row["telefono1"];
    $telefono2=$row["telefono2"];
    $nit=$row["nit"];
    $nrc=$row["nrc"];
    $iva=$row["iva"];
    $giro = $row["giro"];
    $monto_retencion1 = $row["monto_retencion1"];
    $monto_retencion10 = $row["monto_retencion10"];
    $monto_percepcion = $row["monto_percepcion"];
    $id_empresa = $row["idempresa"];
    $accion = "Modificar";
  }
  else
  {
    $logo = "";
    $nombre= "";
    $razon= "";
    $direccion= "";
    $telefono1= "";
    $telefono2= "";
    $nit= "";
    $nrc= "";
    $iva= "";
    $giro = "";
    $monto_retencion1 = "";
    $monto_retencion10 = "";
    $monto_percepcion = "";
    $id_empresa = "";
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
                            <h5>Registrar Empresa</h5>
                        </div>
                        <div class="ibox-content">


                                <form name="formulario" id="formulario" autocomplete='off'>

                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Nombre</label> 
                                    <input type="text" placeholder="Nombre empresa" class="form-control dis" id="empresa" name="empresa" value="<?php echo $nombre;?>" readonly>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Razón Social</label> 
                                    <input type="text" placeholder="Razón Social" class="form-control dis" id="razon" name="razon" value="<?php echo $razon;?>" readonly>
                                  </div>
                                </div>
                              </div>
                              
                              <div class="form-group has-info single-line">
                                  <label>Dirección</label> 
                                  <input type="text" placeholder="Dirección" class="form-control dis" id="direccion" name="direccion" value="<?php echo $direccion;?>" readonly>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                      <label>Teléfono 1</label> 
                                      <input type="text" placeholder="Teléfono 1" class="form-control dis" id="telefono1" name="telefono1" value="<?php echo $telefono1;?>" readonly>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Teléfono 2</label> 
                                    <input type="text" placeholder="Teléfono 2" class="form-control dis" id="telefono2" name="telefono2" value="<?php echo $telefono2;?>" readonly>
                                  </div>
                                </div>
                              </div>
                              
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>NIT</label> 
                                    <input type="text" placeholder="NIT" class="form-control dis" id="nit" name="nit" value="<?php echo $nit;?>" readonly>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>NRC</label> 
                                    <input type="text" placeholder="NRC" class="form-control dis" id="nrc" name="nrc" value="<?php echo $nrc;?>" readonly>
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>IVA</label> 
                                    <input type="text" placeholder="IVA" class="form-control dis" id="iva" name="iva" value="<?php echo $iva;?>" readonly>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Giro</label> 
                                    <input type="text" placeholder="Giro" class="form-control dis" id="giro" name="giro" value="<?php echo $giro;?>" readonly>
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Monto inicial de retención 1%</label> 
                                    <input type="text" placeholder="Monto inicial de retencion 1%" class="form-control dis" id="monto_retencion1" name="monto_retencion1" value="<?php echo $monto_retencion1;?>" readonly>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Monto inicial de retención 10%</label> 
                                    <input type="text" placeholder="Monto inicial de retencion 10%" class="form-control dis" id="monto_retencion10" name="monto_retencion10" value="<?php echo $monto_retencion10;?>" readonly>
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Monto inicial de percepción</label> 
                                    <input type="text" placeholder="Monto inicial de percepción" class="form-control dis" id="monto_percepcion" name="monto_percepcion" value="<?php echo $monto_percepcion;?>" readonly>
                                  </div>
                                </div>
                              </div>
                              <div class="row">      
                                <div class="col-md-6">
                                  <div class="form-group has-info">
                                      <label>Logo</label>
                                      <input type="file" name="logo" id="logo" class="file" data-preview-file-type="image">
                                  </div> 
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info">
                                      <img id="logo_view" src="<?php echo $logo;?>" style='width: 200px; height: 100px;'>
                                  </div> 
                                </div>
                              </div>
                    				    <input type="hidden" name="process" id="process" value="edit">
                                <div>
                                  <input type="hidden" name="action" id="action" value="<?php echo $accion;?>">
                                <input type="hidden" name="id_empresa" id="id_empresa" <?php if($cuenta>0){ echo "value='$id_empresa'";} ?>>
                                <a id="btn_edit" class="btn btn-primary m-t-n-xs pull-right"><i class="fa fa-pencil"></i> Editar</a>
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
/////////////////////////////////////////////////////////////////////////
function editar()
{
    require_once 'class.upload.php';
    if ($_FILES["logo"]["name"]!="")
    { 
    $foo = new Upload($_FILES['logo'],'es_ES');
    if ($foo->uploaded) {
        $pref = uniqid()."_";
        $foo->file_force_extension = false;
        $foo->no_script = false;
        $foo->file_name_body_pre = $pref;
       // save uploaded image with no changes
       $foo->Process('img/');
       if ($foo->processed) 
       {
        $query = _query("SELECT logo FROM empresa WHERE idempresa='1'");
        $result = _fetch_array($query);
        $urlb=$result["logo"];
        if($urlb!="")
        {
            unlink($urlb);
        } 
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
        $cuerpo=quitar_tildes($foo->file_src_name_body);
        $cuerpo=trim($cuerpo);
        $url = 'img/'.$pref.$cuerpo.".".$foo->file_src_name_ext;
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
        'monto_percepcion' => $monto_percepcion,
        'logo' => $url
        );
        $where_clause = "idempresa='1'";
        $editar =_update($table, $form_data, $where_clause);
        if($editar)
        {
           $xdatos['typeinfo']='Success';
           $xdatos['msg']='Datos de empresa editados correctamente !';
           $xdatos['process']='edit';
        }
        else
        {
           $xdatos['typeinfo']='Error';
           $xdatos['msg']='Datos de empresa no pudieron ser editados!'._error();
        }    
       }
       else
       {
          $xdatos['typeinfo']='Error';
          $xdatos['msg']='Error al guardar la imagen!'; 
       }
    }
    else
    {
        $xdatos['typeinfo']='Error';
        $xdatos['msg']='Error al subir la imagen!';
    }
    }
    else
    {
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
        $where_clause = "idempresa='1'";
        $editar =_update($table, $form_data, $where_clause);
        if($editar)
        {
           $xdatos['typeinfo']='Success';
           $xdatos['msg']='Datos de empresa editados correctamente !';
           $xdatos['process']='edit';
        }
        else
        {
           $xdatos['typeinfo']='Error';
           $xdatos['msg']='Datos de empresa no pudieron ser editados!';
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
	case 'edit':
		//insertar_empresa();
    editar();
		break;

	}
}
}
?>
