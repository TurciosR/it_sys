<?php
include_once "_core.php";
function initial() 
{
    $title = 'Editar Colores';
	$_PAGE = array ();
	$_PAGE ['title'] = $title;
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
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

    $id_color = $_REQUEST["id_color"];
    $sql = _query("SELECT * FROM colores WHERE id_color='$id_color'");
    $datos = _fetch_array($sql);

    $nombre = $datos["nombre"]; 
    $codicolor = $datos["codicolor"]; 

?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-2">
    </div>
</div>
<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
    			<?php
    	   		  //permiso del script
        			if ($links!='NOT' || $admin=='1' ){
    			?>
                <div class="ibox-title">
                    <h5><?php echo $title; ?></h5>
                </div>
                <div class="ibox-content">
                    <form name="formulario" id="formulario">
                        <div class="form-group has-info single-line">
                            <label>Nombre  <span style="color:red;">*</span></label>
                            <input type="text" placeholder="Color" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre; ?>">
                        </div>
                        <div class="form-group has-info single-line">
                            <label>Codigo <span style="color:red;">*</span></label>
                            <input type="text" placeholder="Codigo" class="form-control" id="codigo" name="codigo" value="<?php echo $codicolor; ?>">
                        </div>
                        <input type="hidden" name="id_color" id="id_color" value="<?php echo $id_color; ?>">
                        <input type="hidden" name="process" id="process" value="edit"><br>
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
        echo "<script src='js/funciones/funciones_color.js'></script>";
	} //permiso del script
    else 
    {
    		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
    }
}

function insertar()
{
    $id_color=$_POST["id_color"];
    $nombre=$_POST["nombre"];
    $codigo=$_POST["codigo"];
    
    $sql_exis=_query("SELECT id_color FROM colores WHERE nombre='$nombre' AND codicolor ='$codigo' AND id_color != '$id_color'");
    $num_exis = _num_rows($sql_exis);
    if($num_exis > 0)
    {
        $xdatos['typeinfo']='Error';
        $xdatos['msg']='Este cliente ya fue registrado!';
    }
    else
    {
        $table = 'colores';
        $form_data = array(
        'nombre' => $nombre,
        'codicolor' => $codigo,
        );
        $where = "id_color='".$id_color."'";
        $upadte = _update($table,$form_data,$where);
        if($upadte)
        {
           $xdatos['typeinfo']='Success';
           $xdatos['msg']='Registro actualizado con exito!';
           $xdatos['process']='insert';
        }
        else
        {
           $xdatos['typeinfo']='Error';
           $xdatos['msg']='Registro no pudo ser actualizado !';
    	}
    }
	echo json_encode($xdatos);
}
if(!isset($_POST['process']))
{
	initial();
}
else
{
    if(isset($_POST['process']))
    {
        switch ($_POST['process'])
        {
        	case 'edit':
                insertar();
                break;
        }
    }
}
?>
