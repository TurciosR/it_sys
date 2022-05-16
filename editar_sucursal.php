<?php
include_once "_core.php";

function initial() {


	$_PAGE = array ();
	$_PAGE ['title'] = 'Editar Sucursal';
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
	$id_sucursal= $_REQUEST['id_sucursal'];

     $sql="SELECT * FROM sucursal
     WHERE
     id_sucursal='$id_sucursal'";
     $result=_query($sql);
     $count=_num_rows($result);
		 //permiso del script
	  	$id_user=$_SESSION["id_usuario"];
	 	$admin=$_SESSION["admin"];

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
                            <h5>Editar Sucursal</h5>
                        </div>
                        <div class="ibox-content">


                          <form name="formulario" id="formulario">
							  <?php
							  if ($count>0){

								for($i=0;$i<$count;$i++){
									$row=_fetch_array($result);

									$descripcion=$row['descripcion'];
									$direccion=$row['direccion'];
									$casa_matriz=$row['casa_matriz'];

							  ?>
                              <div class="form-group has-info single-line"><label>Nombre</label> <input type="text" placeholder="Digite Nombre" class="form-control" id="nombre" name="nombre" value="<?php echo $descripcion ?> "></div>
                              <div class="form-group has-info single-line"><label>Dirección</label> <input type="text" placeholder="Dirección" class="form-control" id="direccion" name="direccion" value="<?php echo $direccion ?> "></div>
                                <div class="form-group has-info single-line">
                                    <div class="form-group"><label class="col-sm-2 control-label">Casa Matriz </label>
                                    <div class="col-sm-10">
                                      <?php
                                      if($casa_matriz==1){
                                        echo "<div class=\"checkbox i-checks\"><label> <input type=\"checkbox\"  id=\"casa\" name=\"casa\" value=\"1\" checked> <i></i>  </label></div>";
                                      }
                                      else
                                      {
                                        echo "<div class=\"checkbox i-checks\"><label> <input type=\"checkbox\"  id=\"casa\" name=\"casa\" value=\"1\"> <i></i>  </label></div>";
                                      }
                                      ?>
                                    </div>
                                  </div>
                                   <input type="hidden" name="process" id="process" value="edited"><br>
                                </div>
                                <?php
									}
								}
                                ?>
                               <input type="hidden" name="process" id="process" value="edited">
									<input type="hidden" name="id_sucursal" id="id_sucursal" value="<?php echo $_REQUEST['id_sucursal']?> ">
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

function edited(){
	$id_sucursal=$_POST["id_sucursal"];
	$descripcion=$_POST["nombre"];
    $direccion=$_POST["direccion"];
    $casa_matriz=$_POST["casa"];
    $table = 'sucursal';
    $form_data = array (
    	'descripcion' => $descripcion,
    	'direccion' => $direccion,
    	'casa_matriz' => $casa_matriz
    );

   $where_clause = "id_sucursal='" . $id_sucursal . "'";
	$updates = _update ( $table, $form_data, $where_clause );
		if($updates){
			$xdatos['typeinfo']='Success';
			$xdatos['msg']='Registro Actualizado con éxito';
			$xdatos['process']='edited';
		}
		else{
			$xdatos['typeinfo']='Error';
			$xdatos['msg']='Error al Editar';
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
