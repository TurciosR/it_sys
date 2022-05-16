<?php
include_once "_core.php";

function initial() {


	$_PAGE = array ();
	$_PAGE ['title'] = 'Editar Tipo Empleado';
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
	$id_tipo_empleado= $_REQUEST['id_tipo_empleado'];

     $sql="SELECT * FROM tipo_empleado
     WHERE
     id_tipo_empleado='$id_tipo_empleado'";
     $result=_query($sql);
     $count=_num_rows($result);
		 //permiso del script
	  	$id_user=$_SESSION["id_usuario"];
	 	$admin=$_SESSION["admin"];

	 	$uri = $_SERVER['SCRIPT_NAME'];
	 	$filename=get_name_script($uri);
	 	$links=permission_usr($id_user,$filename);
	 	//permiso del script
?>

            <div class="row wrapper border-bottom white-bg page-heading">

                <div class="col-lg-2">

                </div>
            </div>
        <div class="wrapper wrapper-content  animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
						<?php
						//permiso del script
						if ($links!='NOT' || $admin=='1' ){
					?>
                        <div class="ibox-title">
                            <h5>Editar tipo empleado</h5>
                        </div>
                        <div class="ibox-content">


                          <form name="formulario" id="formulario">
							  <?php
							  if ($count>0){

								for($i=0;$i<$count;$i++){
									$row=_fetch_array($result);
									$descripcion=$row['descripcion'];
							  ?>
                              <div class="form-group has-info single-line"><label>Descripción</label> <input type="text" placeholder="Descripcion" class="form-control" id="descripcion" name="descripcion" value="<?php echo $descripcion ?> "></div>

                                <?php
									}
								}
                                ?>
                               <input type="hidden" name="process" id="process" value="edited">
									<input type="hidden" name="id_tipo_empleado" id="id_tipo_empleado" value="<?php echo $_REQUEST['id_tipo_empleado']?> ">
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
echo "<script src='js/funciones/funciones_tipo_empleado.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function edited(){
	$id_tipo_empleado=$_POST["id_tipo_empleado"];
    $descripcion=$_POST["descripcion"];
    $table = 'tipo_empleado';
    $form_data = array (
    	'descripcion' => $descripcion
    );

   $where_clause = "id_tipo_empleado='" . $id_tipo_empleado . "'";
	$updates = _update ( $table, $form_data, $where_clause );
		if($updates){
			$xdatos['typeinfo']='Success';
			$xdatos['msg']='Registro Editado !';
			$xdatos['process']='edited';
		}
		else{
			$xdatos['typeinfo']='Error';
			$xdatos['msg']='Registro No Editado ';
		}
	echo json_encode($xdatos);
}

if(!isset($_REQUEST['process'])){
	initial();
}
else
{
if(isset($_REQUEST['process'])){
switch ($_REQUEST['process']) {
	case 'edited':
		edited();
		break;
	case 'formEdit' :
		initial();
		break;
	}
}
}
?>
