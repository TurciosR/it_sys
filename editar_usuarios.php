<?php
include_once "_core.php";

function initial() {
	// Page setup
	$_PAGE = array ();
  $_PAGE ['title'] = 'Editar Usuarios';
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
	$id_usuario= $_REQUEST['id_usuario'];

     $sql="SELECT * FROM usuario
     WHERE
     id_usuario='$id_usuario'";
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
                            <h5>Editar Usuarios</h5>
                        </div>
                        <div class="ibox-content">
                                <form name="formulario" id="formulario">
                                  <?php
                if ($count>0){

                  for($i=0;$i<$count;$i++){
                    $row=_fetch_array($result);
                    $nombre=$row['nombre'];
                    $usuario=$row['usuario'];
                    $id_sucursal=$row['id_sucursal'];
                    $password=$row['password'];
                    $tipo_usuario=$row['tipo_usuario'];

                    }
                  }

                ?>


                              <div class="form-group has-info single-line"><label>Nombre</label> <input type="text" placeholder="Ingresa nombre" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre ?>"></div>
                              <div class="form-group has-info single-line"><label>Usuario</label> <input type="text" placeholder="Ingrese el usuario" class="form-control" id="usuario" name="usuario" value="<?php echo $usuario ?>"></div>
                              <div class="form-group has-info single-line"><label>Clave</label> <input type="password" placeholder="Ingrese la clave" class="form-control" id="clave" name="clave" value="<?php echo $password ?>"></div>
                             	 <div class="form-group has-info single-line">
                                     <label>Seleccione sucursal</label>
                                   <div class="input-group">
                                <select  name='id_sucursal' id='id_sucursal'   style='width:300px'>

                                   <?php

                                   $qsucursal=_query("SELECT *FROM sucursal ORDER BY descripcion ");
                                   while($row_sucursal=_fetch_array($qsucursal))
                                   {
                                    $id_sucursal_base=$row_sucursal["id_sucursal"];

                                    $nombre=$row_sucursal["descripcion"];

                                      if($id_sucursal==$row_sucursal['id_sucursal'])
                                      {
                                      echo "<option value='".$row_sucursal['id_sucursal']."' selected>".$row_sucursal['descripcion']."</option>";
                                      }
                                      else
                                      {
                                      echo "<option value='".$row_sucursal['id_sucursal']."'>".$row_sucursal['descripcion']."</option>";
                                      }

                                   }



                                   ?>
                              </select>
                                   </div>
                                   </div>
                                    <div class="form-group has-info single-line">
                                    <div class="form-group"><label class="col-sm-2 control-label">Administrador </label>
                                    <div class="col-sm-10">
                                      <?php
                                      if($tipo_usuario==1){
                                        echo "<div class=\"checkbox i-checks\"><label> <input type=\"checkbox\"  id=\"activo\" name=\"activo\" value=\"1\" checked> <i></i>  </label></div>";
                                      }
                                      else
                                      {
                                        echo "<div class=\"checkbox i-checks\"><label> <input type=\"checkbox\"  id=\"admin\" name=\"admin\" value=\"1\"> <i></i>  </label></div>";
                                      }
                                      ?>
                                    </div>
                                  </div>
                                   <input type="hidden" name="process" id="process" value="edited"><br>
                                </div>

                                <input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_REQUEST['id_usuario']?> ">

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


function edited(){
  $id_usuario=$_POST["id_usuario"];
  $password=$_POST["clave"];
  $string_user="SELECT password FROM usuario WHERE id_usuario='$id_usuario'";
  $clave=_query($string_user);
  $clave_row=_fetch_array($clave);
  $clave_user=$clave_row['password'];
  if($clave_user==$password){
    $clave_cambio=$clave_user;
  }
  else
  {
    $clave_cambio=md5($_POST["clave"]);
  }

    $nombre=$_POST["nombre"];
    $id_sucursal=$_POST["id_sucursal"];
    $usuario=$_POST["usuario"];
    $tipo_usuario=$_POST["admin"];
    $table='usuario';

    $form_data = array (
    'nombre' => $nombre,
    'usuario' => $usuario,
    'password' => $clave_cambio,
    'tipo_usuario' => $tipo_usuario,
    'id_sucursal' => $id_sucursal
    );

   $where_clause = "id_usuario='" . $id_usuario . "'";
  $updates = _update ( $table, $form_data, $where_clause );
    if($updates){
      $xdatos['typeinfo']='Success';
      $xdatos['msg']='Registro autualizado ';
      $xdatos['process']='edited';
    }
    else{
      $xdatos['typeinfo']='Error';
      $xdatos['msg']='Registro no editado';
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
