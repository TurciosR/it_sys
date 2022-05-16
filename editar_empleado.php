<?php
include_once "_core.php";

function initial() {


	$_PAGE = array ();
	$_PAGE ['title'] = 'Editar Empleados';
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
                            <h5>Editar Empleados</h5>
                        </div>
                        <div class="ibox-content">


                              <form name="formulario" id="formulario">
							<?php
								if ($count > 0) {
									for($i = 0; $i < $count; $i ++) {
										$row = _fetch_array ( $result);
                    $id_empleado=$row["id_empleado"];
                    $nombre=$row["nombre"];
                    //$apellido=$row["apellido"];
                    $nit=$row["nit"];
                    $dui=$row["dui"];
                    $telefono1=$row["telefono1"];
                    $telefono2=$row["telefono2"];
                    $email=$row["email"];
                    $salariobase=$row["salario"];
                    $fecha_nace = $row["fechan"];
                    $fecha_registro = $row["fechaing"];
                    $departamento = $row["depto"];
                    $municipio = $row["municipio"];
                    $n_cuenta = $row["cuentabco"];
                    $cargo = $row["cargo"];
                    $profecion = $row["profesion"];
                    $n_isss = $row["noisss"];
                    $n_afp = $row["noafp"];
                    $sucursal = $row["id_sucursal"];
                    $usuario_sistema = $row["acceso_sistema"];
                    $direccion = $row["direccion"];
                    $sexo = $row["sexo"];
                    $usuario = $row["usuario"];
                    $pass = $row["password"];
                    $admin_sistema = $row["admin"];
										$vendedor = $row["vendedor"];
									}
								}
							?>


                                   <!-- //////////////////////////////////////////////////////////////////// -->
                              <div class="row">
                                <div class="col-md-8">
                                  <div class="form-group has-info single-line"><label>Nombre y Apellido <span style="color:red;">*</span></label> <input type="text" placeholder="nombre empleado" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre ?> "></div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group has-info single-line"><label>Género <span style="color:red;">*</span></label>
                                    <select class="col-md-12 select" id="sexo" name="sexo">
                                      <option value="0">Seleccione</option>
                                      <option value="1"
                                      <?php if($sexo == 1)
                                      {
                                        echo "selected";
                                      }
                                      ?>>Femenino</option>
                                      <option value="2"
                                      <?php if($sexo == 2)
                                      {
                                        echo "selected";
                                      }
                                      ?>>Masculino</option>
                                    </select>
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line"><label>Fecha nacimiento <span style="color:red;">*</span></label> <input type="text"  class="form-control datepick" id="fecha_nace" name="fecha_nace" value="<?php echo $fecha_nace?>"></div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line"><label>Fecha de registro</label> <input type="text"  class="form-control datepick" id="fecha_registro" name="fecha_registro" value="<?php echo $fecha_registro?>"></div>
                                </div>
                              </div>

                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line"><label>NIT <span style="color:red;">*</span></label> <input type="text" placeholder="NIT" class="form-control" id="nit" name="nit" value="<?php echo $nit ?> "></div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line"><label>DUI <span style="color:red;">*</span></label> <input type="text" placeholder="DUI" class="form-control" id="dui" name="dui" value="<?php echo $dui ?> "></div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="form-group has-info single-line"><label>Direccion <span style="color:red;">*</span></label> <input type="text" placeholder="direccion" class="form-control" id="direccion" name="direccion" value="<?php echo $direccion ?> "></div>
                                </div>

                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-info single-line">
                                        <label>Departamento <span style="color:red;">*</span></label>
                                        <select class="col-md-12 select" id="departamento" name="departamento">
                                            <option value="">Seleccione</option>
                                            <?php
                                                $sqld = "SELECT * FROM departamento";
                                                $resultd=_query($sqld);
                                                while($depto = _fetch_array($resultd))
                                                {
                                                    echo "<option value='".$depto["id_departamento"]."'";
                                                    if($depto["id_departamento"] == $departamento)
                                                    {
                                                      echo "selected";
                                                    }
                                                    echo">".$depto["nombre_departamento"]."</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-info single-line">
                                        <label>Municipio <span style="color:red;">*</span></label>
                                        <select class="col-md-12 select" id="municipio" name="municipio">
                                            <option value="">Primero seleccione un departamento</option>
                                            <?php
                                                $sqld = "SELECT * FROM municipio";
                                                $resultd=_query($sqld);
                                                while($depto = _fetch_array($resultd))
                                                {
                                                    echo "<option value='".$depto["id_municipio"]."'";
                                                    if($depto["id_municipio"] == $municipio)
                                                    {
                                                      echo "selected";
                                                    }
                                                    echo">".$depto["nombre_municipio"]."</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line"><label>Banco</label>
                                    <select class="col-md-12 select" id="banco" name="banco">
                                        <option value="">Seleccione</option>
                                        <?php
                                            $sqld = "SELECT * FROM bancos";
                                            $resultd=_query($sqld);
                                            while($depto = _fetch_array($resultd))
                                            {
                                                echo "<option value='".$depto["id_banco"]."'";

                                                echo">".$depto["nombre"]."</option>";
                                            }
                                        ?>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line"><label>Numéro de cuenta <span style="color:red;">*</span></label> <input type="text" placeholder="Numero de cuenta para deposito" class="form-control" id="n_cuenta" name="n_cuenta" value="<?php echo $n_cuenta?>"></div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line"><label>Telefono 1 <span style="color:red;">*</span></label> <input type="text" placeholder="telefono1" class="form-control" id="telefono1" name="telefono1" value="<?php echo $telefono1 ?> "></div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line"><label>Telefono 2</label> <input type="text" placeholder="telefono2" class="form-control" id="telefono2" name="telefono2" value="<?php echo $telefono2 ?> "></div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line"><label>Email</label> <input type="text" placeholder="email" class="form-control" id="email" name="email" value="<?php echo $email ?> "></div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line"><label>Salario base <span style="color:red;">*</span></label> <input type="text" placeholder="salario base" class="form-control" id="salariobase" name="salariobase" value="<?php echo $salariobase?>"></div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line"><label>Cargo</label> <input type="text" placeholder="Cargo" class="form-control" id="cargo" name="cargo" value="<?php echo $cargo?>"></div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line"><label>Profesión</label> <input type="text" placeholder="Profeción" class="form-control" id="profecion" name="profecion" value="<?php echo $profecion ?>"></div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line"><label>Número de ISSS <span style="color:red;">*</span></label> <input type="text" placeholder="Número de ISSS" class="form-control" id="n_isss" name="n_isss" value="<?php echo $n_isss ?>"></div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line"><label>Número de AFP <span style="color:red;">*</span></label> <input type="text" placeholder="Número de AFP" class="form-control" id="n_afp" name="n_afp" value="<?php echo $n_afp?>"></div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line"><label>Sucursal <span style="color:red;">*</span></label>
                                    <select class="col-md-12 select" id="sucursal" name="sucursal">
                                        <option value="">Seleccione</option>
                                        <?php
                                            $sqld = "SELECT * FROM sucursal";
                                            $resultd=_query($sqld);
                                            while($depto = _fetch_array($resultd))
                                            {
                                                echo "<option value='".$depto["id_sucursal"]."'";
                                                if($depto["id_sucursal"] == $sucursal)
                                                {
                                                  echo "selected";
                                                }
                                                echo">".$depto["descripcion"]."</option>";
                                            }
                                        ?>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="form-group has-info single-line">
                                    <div class='radio i-checks'><label><input id='usuario_sis' name='usuario_sis' type='checkbox' <?php if($usuario_sistema){ echo " checked "; }?>> <span class="label-text"><b>Usuario del sistema</b></span></label></div>
                                    <input type="hidden" name="usuario_sistema" id="usuario_sistema" value="<?php echo $usuario_sistema ?>">
                                  </div>
                                </div>
																<div class="col-md-3">
                                  <div class="form-group has-info single-line">
                                    <div class='radio i-checks'><label><input id='vendedor' name='vendedor' type='checkbox'  <?php if($vendedor){ echo " checked "; }?>> <span class="label-text"><b>Vendedor</b></span></label></div>
                                    <input type="hidden" name="vendedor1" id="vendedor1" value="<?php echo $vendedor;?>">
                                  </div>
                                </div>
                              </div>
                              <div class="row" id="caja_user" hidden="true">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Usuario</label>
                                    <input type="text" placeholder="Agrege un usuario" class="form-control" id="usuario" name="usuario" value="<?php echo $usuario?>">
                                  </div>
                                </div>
                                <div class="col-md-6" id="caja_pass" hidden="true">
                                  <div class="form-group has-info single-line">
                                    <label>Contraseña</label>
                                    <input type="password" placeholder="Agrege un contraseña" class="form-control" id="pass" name="pass" value="<?php echo $pass?>">
                                  </div>
                                </div>
                              </div>
                              <div class="row" id="caja_admin" hidden="true">
                                <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <div class='radio i-checks'><label><input id='admin_sis' name='admin_sis' type='checkbox' <?php if($admin_sistema){ echo " checked "; }?>> <span class="label-text"><b>Administrador</b></span></label></div>
                                    <input type="hidden" name="admin_sistema" id="admin_sistema" value="<?php echo $admin_sistema?>">
                                  </div>
                                </div>
                              </div>

                                   <input type="hidden" name="process" id="process" value="edited">
                                   	<input type="hidden" name="id_empleado" id="id_empleado" value="<?php echo $_REQUEST['id_empleado']?> ">
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
echo "<script src='js/funciones/funciones_empleado.js'></script>";
		} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function edited(){
    $usuario = "";
    $pass = "";
    $admin_sistema = 0;
	  $id_empleado=$_POST["id_empleado"];
    $nombre=$_POST["nombre"];
    //$apellido=$_POST["apellido"];
    $nit=$_POST["nit"];
    $dui=$_POST["dui"];
    $telefono1=$_POST["telefono1"];
    $telefono2=$_POST["telefono2"];
    $email=$_POST["email"];
    $salariobase=$_POST["salariobase"];
    $fecha_nace = $_POST["fecha_nace"];
    $fecha_registro = $_POST["fecha_registro"];
    $departamento = $_POST["departamento"];
    $municipio = $_POST["municipio"];
    $banco = $_POST["banco"];
    $n_cuenta = $_POST["n_cuenta"];
    $cargo = $_POST["cargo"];
    $profecion = $_POST["profecion"];
    $n_isss = $_POST["n_isss"];
    $n_afp = $_POST["n_afp"];
    $sucursal = $_POST["sucursal"];
    $usuario_sistema = $_POST["usuario_sistema"];
    $direccion = $_POST["direccion"];
    $sexo = $_POST["sexo"];
		$vendedor = $_POST["vendedor"];

    $ed = explode("-", $fecha_nace);
    $anio_nace = $ed[0];
    $anio_actual = date('Y');

    $edad = $anio_actual - $anio_nace;
    if($usuario_sistema == 1)
    {
      $usuario = $_POST["usuario"];
      $pass = $_POST["pass"];
      $admin_sistema = $_POST["admin_sistema"];

      $string_user="SELECT password FROM empleados WHERE id_empleado='$id_empleado'";
      $clave=_query($string_user);
      $clave_row=_fetch_array($clave);
      $clave_user=$clave_row['password'];
      if($clave_user==$pass){
        $clave_cambio=$clave_user;
      }
      else
      {
        $clave_cambio=md5($_POST["pass"]);
      }
    }
    else
    {
      $usuario = "";
      $pass = "";
      $admin_sistema = 0;
      $clave_cambio = "";
    }

    $string_user="SELECT password FROM empleados WHERE id_empleado='$id_empleado'";
    $clave=_query($string_user);
    $clave_row=_fetch_array($clave);
    $clave_user=$clave_row['password'];
    if($clave_user==$pass){
      $clave_cambio=$clave_user;
    }
    else
    {
      $clave_cambio=md5($_POST["pass"]);
    }
     //'id_empleado' => $id_empleado,
    $table = 'empleados';
    $form_data = array(
    'id_empleado'=> $id_empleado,
    'nombre' => $nombre,
    'nit' => $nit,
    'dui' => $dui,
    'telefono1' => $telefono1,
    'telefono2' => $telefono2,
    'email' => $email,
    'salario' => $salariobase,
    'direccion' => $direccion,
    'municipio' => $municipio,
    'depto' => $departamento,
    'fechan' => $fecha_nace,
    'fechaing' => $fecha_registro,
    'sexo' => $sexo,
    'edad' => $edad,
    'cargo' => $cargo,
    'profesion' => $profecion,
    'noisss' => $n_isss,
    'noafp' => $n_afp,
    'cuentabco' => $n_cuenta,
    'acceso_sistema' => $usuario_sistema,
    'Usuario' => $usuario,
    'password' => $clave_cambio,
    'admin' => $admin_sistema,
    'id_sucursal' => $sucursal,
		'vendedor' => $vendedor,
    );




   $where_clause = "id_empleado='" . $id_empleado . "'";
	$updates = _update( $table, $form_data, $where_clause );
   	if($updates){

       $xdatos['typeinfo']='Success';
       $xdatos['msg']='Registro editado con exito!';
       $xdatos['process']='insert';
    }
    else{
       $xdatos['typeinfo']='Error';
       $xdatos['msg']='Registro no editado !'._error();
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
