<?php
include_once "_core.php";

function initial() {
	// Page setup
	$_PAGE = array ();
  $_PAGE ['title'] = 'Editar Empresa';
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
	$id_empresa= $_REQUEST['id_empresa'];

     $sql="SELECT * FROM empresa
     WHERE
     id_empresa='$id_empresa'";
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
                            <h5>Registrar Servicios</h5>
                        </div>
                        <div class="ibox-content">
                                <form name="formulario" id="formulario">
                                  <?php
                if ($count>0){

                  for($i=0;$i<$count;$i++){
                    $row=_fetch_array($result);
                    $nombre=$row['nombre'];
                    $razon=$row["razonsocial"];
                    $direccion=$row["direccion"];
                    $telefono1=$row["telefono1"];
                    $telefono2=$row["telefono2"];
                    $web=$row["website"];
                    $email=$row["email"];
                    $nit=$row["nit"];
										$nrc=$row["nrc"];
                    $iva=$row["iva"];

                    }
                  }

                ?>


                              <div class="form-group has-info single-line"><label>Nombre</label> <input type="text" placeholder="Nombre empresa" class="form-control" id="empresa" name="empresa" value="<?php echo $nombre ?>"></div>
                              <div class="form-group has-info single-line"><label>Razón Social</label> <input type="text" placeholder="Razón Social" class="form-control" id="razon" name="razon" value="<?php echo $razon ?>"></div>
                              <div class="form-group has-info single-line"><label>Dirección</label> <input type="text" placeholder="Dirección" class="form-control" id="direccion" name="direccion" value="<?php echo $direccion ?>"></div>
                              <div class="form-group has-info single-line"><label>Teléfono 1</label> <input type="text" placeholder="Teléfono 1" class="form-control" id="telefono1" name="telefono1" value="<?php echo $telefono1 ?>"></div>
                              <div class="form-group has-info single-line"><label>Teléfono 2</label> <input type="text" placeholder="Teléfono 2" class="form-control" id="telefono2" name="telefono2" value="<?php echo $telefono2 ?>"></div>
                              <div class="form-group has-info single-line"><label>Website</label> <input type="text" placeholder="Sitio Web" class="form-control" id="web" name="web" value="<?php echo $web ?>"></div>
                              <div class="form-group has-info single-line"><label>Email</label> <input type="text" placeholder="Correo electrónico" class="form-control" id="email" name="email" value="<?php echo $email ?>"></div>
                              <div class="form-group has-info single-line"><label>NIT</label> <input type="text" placeholder="NIT" class="form-control" id="nit" name="nit" value="<?php echo $nit ?>"></div>
															<div class="form-group has-info single-line"><label>NRC</label> <input type="text" placeholder="NRC" class="form-control" id="nrc" name="nrc" value="<?php echo $nrc ?>"></div>
                              <div class="form-group has-info single-line"><label>IVA</label> <input type="text" placeholder="IVA" class="form-control" id="iva" name="iva" value="<?php echo $iva ?>"></div>

                                <input type="hidden" name="process" id="process" value="edited"><br>


                                <input type="hidden" name="id_empresa" id="id_empresa" value="<?php echo $_REQUEST['id_empresa']?> ">

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


function edited(){
  $id_empresa=$_POST["id_empresa"];
  $empresa=$_POST["empresa"];
  $razon=$_POST["razon"];
  $direccion=$_POST["direccion"];
  $telefono1=$_POST["telefono1"];
  $telefono2=$_POST["telefono2"];
  $web=$_POST["web"];
  $email=$_POST["email"];
  $nit=$_POST["nit"];
	$nrc=$_POST["nrc"];
  $iva=$_POST["iva"];

    $table='empresa';
    $form_data = array (
    'nombre' => $empresa,
    'razonsocial' => $razon,
    'direccion' => $direccion,
    'telefono1' => $telefono1,
    'telefono2' => $telefono2,
    'website' => $web,
    'email' => $email,
    'nit' => $nit,
		'nrc' => $nrc,
    'iva' => $iva
    );

   $where_clause = "id_empresa='" . $id_empresa . "'";
  $updates = _update ( $table, $form_data, $where_clause );
    if($updates){
      $xdatos['typeinfo']='Success';
      $xdatos['msg']='Registro actualizado ';
      $xdatos['process']='edited';
    }
    else{
      $xdatos['typeinfo']='Error';
      $xdatos['msg']='Registro no actualizado';
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
	case 'edited':
		edited();
		break;

	}
}
}
?>
