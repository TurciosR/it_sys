<?php
include_once "_core.php";

function initial() {
	// Page setup
	$_PAGE = array ();
	$_PAGE ['title'] = 'Agregar Usuarios';
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
                            <h5>Registrar Usuarios</h5>
                        </div>
                        <div class="ibox-content">


                                <form name="formulario" id="formulario">
                              <div class="form-group has-info single-line"><label>Nombre</label> <input type="text" placeholder="Ingresa nombre" class="form-control" id="nombre" name="nombre"></div>
                              <div class="form-group has-info single-line"><label>Usuario</label> <input type="text" placeholder="Ingrese usuario" class="form-control" id="usuario" name="usuario"></div>
                              <div class="form-group has-info single-line"><label>Clave</label> <input type="password" placeholder="Ingrese la contraseña " class="form-control" id="clave" name="clave"></div>

                                   <div class="form-group has-info single-line">
                                <label>Seleccione Sucursal</label>
                                <div class="input-group">
                                <select  name='id_sucursal' id='id_sucursal'   style="width:200px;">
                                <option value=''>Seleccione</option>

                                   <?php
                                   $qsucursal=_query("SELECT * FROM sucursal ORDER BY descripcion ");
                                   while($row_sucursal=_fetch_array($qsucursal))
                                   {
                                       $id_sucursal=$row_sucursal["id_sucursal"];
                                       $descripcion=$row_sucursal["descripcion"];
                                       echo "
                                   <option value='$id_sucursal'>$descripcion</option>
                                   ";
                                   }
                                   ?>
                                  </select>
                                   </div>
                                   </div>

                                     <div class="form-group has-info single-line">
                                    <div class="form-group"><label class="col-sm-2 control-label">Administrador</label>
                                    <div class="col-sm-10">
                                        <div class="checkbox i-checks"><label> <input type="checkbox"  id="admin" name="admin" value="1"> <i></i>  </label></div>
                                    </div>
                                  </div>
                                     <input type="hidden" name="process" id="process" value="insert"><br>
                                </div>
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
echo "<script src='js/funciones/funciones_usuarios.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}


function insertar_usuario(){

	//$id_usuario=$_POST["id_usuario"];
  $nombre=$_POST["nombre"];
	$usuario=$_POST["usuario"];
	$clave=md5($_POST["clave"]);
  $admin=$_POST["admin"];
  $id_sucursal=$_POST["id_sucursal"];

    $sql_result=_query("SELECT id_usuario,nombre FROM usuario WHERE usuario='$usuario'");
    $row_update=_fetch_array($sql_result);
    $id_update=$row_update["id_usuario"];
    $numrows=_num_rows($sql_result);
    //$num=_num_rows($contar);


    $table = 'usuario';
    $form_data = array (
    'nombre' => $nombre,
    'usuario' => $usuario,
    'password' => $clave,
    'id_sucursal' => $id_sucursal,
    'tipo_usuario' => $admin
    );

    if($numrows == 0 && trim($usuario)!=''){

    $insertar = _insert($table,$form_data);
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
		insertar_usuario();
		break;

	}
}
}
?>
