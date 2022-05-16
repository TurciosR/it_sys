<?php
include_once "_core.php";

function initial() {


	$_PAGE = array ();
	$_PAGE ['title'] = 'Agregar Sucursal';
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
                            <h5>Agregar Sucursal</h5>
                        </div>
                        <div class="ibox-content">


                          <form name="formulario" id="formulario">
                              <div class="form-group has-info single-line"><label class="control-label" for="Nombre">Descripción</label> <input type="text" placeholder="Digite Nombre" class="form-control" id="nombre" name="nombre"></div>
                              <div class="form-group has-info single-line"><label class="control-label" for="Dirección">Dirección</label> <input type="text" placeholder="Dirección" class="form-control" id="direccion" name="direccion"></div>

                                <div class="form-group has-info single-line">
                                    <div class="form-group"><label class="col-sm-2 control-label">Casa Matriz</label>
                                    	<div class="col-sm-10">
                                        	<div class="checkbox i-checks"><label> <input type="checkbox"  id="casa" name="casa" value="1"> <i></i>  </label></div>
                                    	</div>
                                  </div>
                                     <input type="hidden" name="process" id="process" value="insert"><br>
                                </div>

                                    <div>

                                       <input type="submit" id="submit1" name="submit1" value="Guardar" class="btn btn-primary m-t-n-xs" />

                                    </div>
                                </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>

<?php
include_once ("footer.php");
echo "<script src='js/funciones/funciones_sucursal.js'></script>";
		} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function insert_sucursal(){
	//$id_sucursal=$_POST["id_sucursal"];
	$descripcion=$_POST["nombre"];
    $direccion=$_POST["direccion"];
    $casa=$_POST["casa"];


    $sql_result= _query("SELECT * FROM sucursal WHERE descripcion='$descripcion'");
    $numrows=_num_rows($sql_result);


    $table = 'sucursal';
    $form_data = array (
    	'descripcion' => $descripcion,
    	'direccion' => $direccion,
    	'casa_matriz' => $casa
    );

    if($numrows == 0){

    $insertar = _insert($table,$form_data);

    if($insertar){
       $xdatos['typeinfo']='Success';
       $xdatos['msg']='Registro insertado con éxito !';
       $xdatos['process']='insert';
    }
    else{
       $xdatos['typeinfo']='Error';
       $xdatos['msg']='Error al insertar!';
        $xdatos['process']='none';
		}
    }
   //  $xdatos['process']='none';
    /*
    else{
		$xdatos['max_id']=$id_update;
		$where_clause = "name='" . $name . "' AND id_country='$id_update'";
		$update = update ( $table, $form_data, $where_clause );
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='The country name already exists!';
		$xdatos['process']='edited';
    }
    */
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
		insert_sucursal();
		break;

	}
}
}
?>
