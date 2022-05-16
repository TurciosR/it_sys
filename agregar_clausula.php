<?php
include_once "_core.php";

function initial() {


	$_PAGE = array ();
	$_PAGE ['title'] = 'Agregar Clausula';
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';

	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/jqGrid/ui.jqgrid.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri=$_SERVER['REQUEST_URI'];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
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
                            <h5>Agregar Clausula</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                              <div class="col-md-12">
                                <label>Titulo</label>
                                <input type="text" name="titulo" class="form-control" id="titulo">
                              </div>
                            </div>
                            <br>
                            <div class="row">
                              <div class="col-md-12">
                                  <div class="form-group has-info single-line">
                                      <label class="control-label">Descripción</label>
                                      <textarea style="height: 250px" class="form-control" id="descripcion" name="descripcion"></textarea>
                                  </div>
                              </div>
                            </div>
                            <input type="hidden" name="process" id="process" value="insert"><br>
                            <div class="row">
                              <div class="col-md-12">
                                <a id="submit1" name="submit1" class="btn btn-primary m-t-n-xs pull-right">Guardar</a>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

<?php
include_once ("footer.php");
echo "<script src='js/funciones/funciones_clausula.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}
function insert()
{
	$titulo = $_POST["titulo"];
	$descripcion = $_POST["descripcion"];
	$sql_clausula = _query("SELECT * FROM clausula WHERE titulo = '$titulo'");
	$cuenta = _num_rows($sql_clausula);
	if($cuenta == 0)
	{
		$tabla = "clausula";
		$lista = array(
			'titulo' => $titulo,
			'descripcion' => $descripcion,
		);
		$insert = _insert($tabla, $lista);
		if($insert)
		{
			$xdatos['typeinfo']='Success';
			$xdatos['msg']='Registro guardado con exito!';
			$xdatos['process']='insert';
		}
		else
		{
			$xdatos['typeinfo']='Error';
			$xdatos['msg']='Error al guardar el registro!'._error();
		}
	}
	else
	{
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Ya existe una clausula con este mismo titulo!';
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
		insert();
		break;
	}
}
}
?>
