<?php
include_once "_core.php";

function initial() {
	// Page setup
	$_PAGE = array ();
  $_PAGE ['title'] = 'Permisos de Usuario';
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
	$id_empleado= $_REQUEST['id_empleado'];

  $sql="SELECT * FROM empleados
     WHERE
     id_empleado='$id_empleado'";
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
                            <h5>Permisos de Usuario</h5>
                        </div>
                        <div class="ibox-content">
                                <form name="formulario" id="formulario">
                                  <?php
                if ($count>0){

                  for($i=0;$i<$count;$i++){
                    $row=_fetch_array($result);
                    $nombre=$row['nombre'];
                    $usuario=$row['usuario'];
                  	$administrador=$row['admin'];
                    }
                  }

                ?>


                              <div class="form-group has-info single-line"><label>Nombre</label> <input type="text" placeholder="Ingresa nombre" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre ?>" readonly></div>
                              <div class="form-group has-info single-line"><label>Usuario</label> <input type="text" placeholder="Ingrese el usuario" class="form-control" id="usuario" name="usuario" value="<?php echo $usuario ?>"  readonly></div>


                                     <div class="form-group has-info single-line">
                                    <div class="form-group"><label class="col-sm-2 control-label">Administrador </label>
                                    <!--div class="col-sm-10"-->
                                      <?php
                                       if ($administrador)
			                           {
											echo "<div class='checkbox i-checks'><label id='adminx'> <input type='checkbox' id='admin' name='admin' checked> <i></i> Administrador </label></div> ";
										}
										else {
											echo "<div class='checkbox i-checks'><label id='adminx'> <input type='checkbox' id='admin' name='admin'> <i></i>  Administrator</label></div> ";
										}

                                      // echo "<br>";
                                    //    echo"</div>" ;
                                          echo"</div>" ;
                                            echo"</div>" ;

                                    // if ($administrador!='1'){
                                    echo"
								<div class='form-group' id='div_modules_edit'>
								<label class='col-sm-2 control-label'>Modulos <br/>
								<!--small class='text-navy'>Modulos</small--></label>
								<div class='col-sm-10'>";
                                      $sql_menus="SELECT id_menu, nombre, prioridad,icono FROM menu";
                                      //order by prioridad
								$result=_query($sql_menus);
								$numrows=_num_rows($result);
								$main_lnk='dashboard.php';
								echo"<div class='row'>";
								$paneln=0;
								for($i=0;$i<$numrows;$i++){
									$row=_fetch_array($result);
									$menuname=$row['nombre'];
									$id_menu=$row['id_menu'];
									$icono=$row['icono'];

									$paneln=$paneln+1;
									if ($paneln>3){
										if ($paneln%3==0){
											$paneln=1;
										}
										else{
											$paneln=$paneln%3;
										}
									}

									switch ($paneln){
										case 1:
											$panel='panel-info';
											break;
										case 2:
											$panel='panel-warning';
											break;
										case 3:
											$panel='panel-success';
											break;
									}
									echo" <div class='col-lg-4'>
										<div class='panel ".$panel."'>";
									//if ($namemenu!='User'){
											echo"	<div class='panel-heading'>$menuname</div>";
									//}

									echo "<div class='panel-body'>";
									if ($administrador)
									{
										$sql_links="SELECT distinct menu.id_menu, menu.nombre as nombremenu, menu.prioridad,
										modulo.id_modulo, modulo.nombre as nombremodulo, modulo.descripcion, modulo.filename
										FROM menu, modulo
										WHERE menu.id_menu='$id_menu'
										AND menu.id_menu=modulo.id_menu
										";
										$result_modules=_query($sql_links);
										$numrow2=_num_rows($result_modules);
										if($numrow2>0){
										for($j=0;$j<$numrow2;$j++){
											$row_modules=_fetch_array($result_modules);
											$lnk=strtolower($row_modules['filename']);
											$modulo=$row_modules['nombremodulo'];
											$id_modulo=$row_modules['id_modulo'];

											echo"<p>";
											echo"<div class='checkbox i-checks'><label> <input id='myCheckboxes' name='myCheckboxes' type='checkbox' value='$id_modulo' checked> <i></i>".ucfirst($modulo)."</label></div>";
											echo"</p>";



									}
									}
									}
									else{

										$sql_links="SELECT distinct menu.id_menu, menu.nombre as nombremenu, menu.prioridad,
										modulo.id_modulo, modulo.nombre as nombremodulo, modulo.descripcion, modulo.filename
										FROM menu, modulo
										WHERE menu.id_menu='$id_menu'
										AND menu.id_menu=modulo.id_menu
										";
										$result_modules=_query($sql_links);
										$numrow2=_num_rows($result_modules);
										if($numrow2>0){
										for($j=0;$j<$numrow2;$j++){
											$row_modules=_fetch_array($result_modules);
											$lnk=strtolower($row_modules['filename']);
											$modulo=$row_modules['nombremodulo'];
											$id_modulo=$row_modules['id_modulo'];
											$sql_link_user="SELECT menu.id_menu, menu.nombre as nombremenu, menu.prioridad, modulo.id_modulo,
											 modulo.nombre as nombremodulo, modulo.descripcion, modulo.filename, usuario_modulo.id_usuario
											  FROM menu, modulo, usuario_modulo, empleados
											  WHERE empleados.id_empleado='$id_empleado'
											  AND empleados.id_empleado=usuario_modulo.id_usuario
											  AND usuario_modulo.id_modulo=modulo.id_modulo
											  AND menu.id_menu=modulo.id_menu
											  AND usuario_modulo.id_modulo='$id_modulo'
											";
											$result_link_user=_query($sql_link_user);
											$numrow3=_num_rows($result_link_user);
											$row_link_user=_fetch_array($result_link_user);
											$id_modulo_user=$row_link_user['id_modulo'];
											if ($id_modulo==$id_modulo_user){
											echo"<p>";
											echo"<div class='checkbox i-checks'><label> <input id='myCheckboxes' name='myCheckboxes' type='checkbox' value='$id_modulo' checked> <i></i>".ucfirst($modulo)."</label></div>";
											echo"</p>";
											}
											else{
											echo"<p>";
											echo"<div class='checkbox i-checks'><label> <input id='myCheckboxes' name='myCheckboxes' type='checkbox' value='$id_modulo'> <i></i>".ucfirst($modulo)."</label></div>";
											echo"</p>";
											}



								}

									}
								}
									echo"</div>"; //panel-body
									echo"</div>";//panel panel-primary';
									echo"</div>"; //  <div class='col-lg-4'>
								}
							  // }

                                  echo"</div>" ;//row;
                                  echo"</div>" ;//<div class='form-group' id='div_modules_edit'>;
                                  echo"</div>" ;//<div class='form-group' id='div_modules_edit'>;
                                      ?>
                                    <!--/div-->
                                  <div>
									<input type="hidden" name="process" id="process" value="permissions">
                                     <input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $id_empleado;?> ">
									<input type="submit" id="submit1" name="submit1" value="Guardar" class="btn btn-primary m-t-n-xs" />
                                </div>
                         </form>
                        <!--/div>
                    </div-->
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


function permissions(){
	$id_empleado=$_POST["id_usuario"];
	/*$password=$_POST["clave"];
	$string_user="SELECT password FROM usuario WHERE id_usuario='$id_usuario'";
	$clave=_query($string_user);
	$clave_row=_fetch_array($clave);
	$clave_user=$clave_row['password'];
	* */
	$mod = $_POST ["myCheckboxes"];
	$cuantos = $_POST ["qty"];
	$admin = $_POST ["admin"];
	$listadatos=explode(',',$mod);

    $table='usuario';
    $form_data = array (
    'tipo_usuario' => $admin
    );

	$where_clause = "id_usuario='" . $id_empleado . "'";
	$updates = _update ( $table, $form_data, $where_clause );

  $table='usuario_modulo';
 // $where_clause="WHERE  id_usuario='$id_usuario'";
  $insertar1 = _delete($table, $where_clause );


    for ($i=0;$i<$cuantos ;$i++){
		$id_modulo=$listadatos[$i];
		$sql1="SELECT * FROM usuario_modulo
			   WHERE id_usuario='$id_empleado'
			   AND id_modulo='$id_modulo'";
		$result1=_query($sql1);
		$row1=_fetch_array($result1);
		$nrow1=_num_rows($result1);

		$table='usuario_modulo';
		$form_data = array (
		'id_modulo' => $id_modulo,
		'id_usuario' => $id_empleado
		);
		if ($nrow1==0){
			$insertar1 = _insert($table,$form_data );
		}
		else{
			$where_clause="WHERE  id_usuario='$id_empleado' AND id_modulo='$id_modulo'";
			$insertar1 = _update($table,$form_data, $where_clause );
		}

	}

    if($insertar1){
      $xdatos['typeinfo']='Success';
      $xdatos['msg']='Registro actualizado ';
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
	case 'permissions':
		permissions();
		break;

	}
}
}
?>
