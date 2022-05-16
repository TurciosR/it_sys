<?php
include_once "_core.php";
// Page setup
$_PAGE = array();
$_PAGE['title'] = 'Dashboard';
$_PAGE['links'] = null;
$_PAGE['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/plugins/jqGrid/ui.jqgrid.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/plugins/morris/morris-0.4.3.min.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/animate.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/style.css" rel="stylesheet">';


include_once "header.php";
include_once "main_menu.php";
 //permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
date_default_timezone_set('America/El_Salvador');
$fecha_actual = date("Y-m-d");
$sql_apertura = _query("SELECT * FROM apertura_caja WHERE id_sucursal = '$id_sucursal' AND vigente = 1");
$cuenta = _num_rows($sql_apertura);
if($cuenta > 0)
{
    $row_a = _fetch_array($sql_apertura);
    $id_apertura = $row_a["id_apertura"];
    $fecha_ape = $row_a['fecha'];
    if($fecha_actual != $fecha_ape)
    {
        $tabla = "apertura_caja";
        $form_data = array(
            'vigente' => 0,
            'turno_vigente' => 0, 
            );
        $where_up = "id_apertura='".$id_apertura."'";
        $update = _update($tabla, $form_data, $where_up);
        if($update)
        {
            $table_up = "detalle_apertura";
            $form_up = array(
                'vigente' => 0, 
                );
            $where_deta = "id_apertura='".$id_apertura."' AND vigente = 1";
            $up_date = _update($table_up,$form_up, $where_deta);
            
        }
    }
}
//permiso del script
if ($links!='NOT' || $admin=='1' ){
    $id_sucursal=$_SESSION["id_sucursal"];
    $qsucursal=_query("SELECT descripcion FROM sucursal WHERE id_sucursal='$id_sucursal'");
    $row_sucursal=_fetch_array($qsucursal);
    $sucursal=$row_sucursal["descripcion"];

?>

        <div class="row">
            <div class="col-lg-12">
            <div class="wrapper wrapper-content">
            <div class="row">
							<div></div><br><br>
							<div class='alert alert-success'>Bienvenido, las opciones a las que tiene acceso estan en el menu lateral!</div>
            </div>


					  </div>
				  </div>
        </div>
<?php
include("footer.php");
} //permiso del script
else {
        echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
    }
?>
