<?php
include_once "_core.php";
function initial()
{
    $title = 'Editar Clientes';
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

    $id_cliente = $_REQUEST["id_cliente"];
    $sql = _query("SELECT * FROM clientes WHERE id_cliente='$id_cliente'");
    $datos = _fetch_array($sql);

    $nombre = $datos["nombre"];
    $direccion = $datos["direccion"];
    $departamento = $datos["depto"];
    $municipio = $datos["municipio"];
    $dui = $datos["dui"];
    $nit = $datos["nit"];
    $nrc = $datos["nrc"];
    $giro = $datos["giro"];
    $categoria = $datos["categoria"];
    $retiene = $datos["retiene"];
    $retiene10 = $datos["retiene10"];
    $percibe = $datos["percibe"];
    $telefono1 = $datos["telefono1"];
    $telefono2 = $datos["telefono2"];
    $fax = $datos["fax"];
    $email = $datos["email"];
    $no_retiene = 0;
    $retie = 0;
    if($percibe == 0 && $retiene == 0 && $retiene10 == 0)
    {
        $no_retiene = 1;
    }
    if($retiene == 1 || $retiene10 == 1)
    {
        $retie = 1;
    }
    $tipo = $datos["tipo"];
    $descuento = $datos["descuento"];
    $dias_credito = $datos["dias_credito"];
    $retiene_iva = $datos["retiene_iva"];
    $retiene_renta = $datos["retiene_renta"];
    $chiva = "";
    if($retiene_iva == 1)
    {
      $chiva = "checked";
    }
    $chrenta = "";
    if($retiene_renta == 1)
    {
      $chrenta = "checked";
    }
    $tipo_documento = $datos["tipo_documento"];
    $facturacion = $datos["facturacion"];

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
                            <input type="text" placeholder="Nombre del cliente" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre; ?>">
                        </div>
                        <div class="form-group has-info single-line">
                            <label>Dirección</label>
                            <input type="text" placeholder="Dirección" class="form-control" id="direccion" name="direccion" value="<?php echo $direccion; ?>">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group has-info single-line">
                                    <label>Departamento <span style="color:red;">*</span></label>
                                    <select class="col-md-12 select" id="departamento" name="departamento">
                                        <?php
                                            $sqld = "SELECT * FROM departamento";
                                            $resultd=_query($sqld);
                                            while($depto = _fetch_array($resultd))
                                            {
                                                echo "<option value='".$depto["id_departamento"]."'";
                                                if($departamento == $depto["id_departamento"])
                                                {
                                                    echo " selected ";
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
                                       <?php
                                            $sqld = "SELECT * FROM municipio";
                                            $resultd=_query($sqld);
                                            while($depto = _fetch_array($resultd))
                                            {
                                                echo "<option value='".$depto["id_municipio"]."'";
                                                if($municipio == $depto["id_municipio"])
                                                {
                                                    echo " selected ";
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
                                <div class="form-group has-info single-line">
                                    <label>DUI</label>
                                    <input type="text" placeholder="00000000-0" class="form-control" id="dui" name="dui" value="<?php echo $dui; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                 <div class="form-group has-info single-line">
                                    <label>NIT  <span style="color:red;">*</span></label>
                                    <input type="text" placeholder="0000-000000-000-0" class="form-control" id="nit" name="nit" value="<?php echo $nit; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group has-info single-line">
                                    <label>NRC  <span style="color:red;">*</span></label>
                                    <input type="text" placeholder="Registro" class="form-control" id="nrc" name="nrc" value="<?php echo $nrc; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group has-info single-line">
                                    <label>Giro  <span style="color:red;">*</span></label>
                                    <input type="text" placeholder="Giro del cliente" class="form-control" id="giro" name="giro" value="<?php echo $giro; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group has-info single-line">
                                    <label>Categoria del Cliente <span style="color:red;">*</span></label>
                                    <select class="col-md-12 select" id="categoria" name="categoria">
                                        <?php
                                            $sqld = "SELECT * FROM categoria_proveedor";
                                            $resultd=_query($sqld);
                                            while($depto = _fetch_array($resultd))
                                            {
                                                echo "<option value='".$depto["id_categoria"]."'";
                                                if($categoria == $depto["id_categoria"])
                                                {
                                                    echo " selected ";
                                                }
                                                echo">".$depto["nombre"]."</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group has-info single-line">
                                    <div class='radio i-checks'><label><input id='percibe' name='percibe' type='radio' <?php if($percibe){ echo " checked "; }?>> <span class="label-text"><b>Percibe 1%</b></span></label></div>
                                    <input type="hidden" name="hi_percibe" id="hi_percibe" value="<?php echo $percibe;?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group has-info single-line">
                                    <div class='radio i-checks'><label><input id='retiene' name='retiene' type='radio' <?php if($retiene || $retiene10){ echo " checked "; }?>> <span class="label-text"><b>Retiene</b></span></label></div>
                                    <input type="hidden" name="hi_retiene" id="hi_retiene" value="<?php echo $retie;?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group has-info single-line">
                                    <div class='radio i-checks'><label><input id='no_retiene' name='no_retiene' type='radio' <?php if($no_retiene){ echo " checked "; }?>><span class="label-text"><b>No retiene</b></span></label></div>
                                    <input type="hidden" name="hi_no_retiene" id="hi_no_retiene" value="<?php echo $no_retiene;?>">
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group has-info single-line">
                                    <label>Teléfono 1 <span style="color:red;">*</span></label>
                                    <input type="text" placeholder="0000-0000" class="form-control tel" id="telefono1" name="telefono1" value="<?php echo $telefono1; ?>">
                                </div>
                            </div>
                            <div class="col-md-6" id="retiene_select">
                                <div class="form-group has-info single-line">
                                    <label>Porcentaje de Retención <span style="color:red;">*</span></label>
                                    <select class="col-md-12 select" id="porcentaje" name="porcentaje">
                                        <option value="0">Sin Retención</option>
                                        <option value="1" <?php if($retiene){ echo " selected "; }?>>1%</option>
                                        <option value="10" <?php if($retiene10){ echo " selected "; }?>>10%</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Teléfono 2</label>
                                    <input type="text" placeholder="0000-0000" class="form-control tel" id="telefono2" name="telefono2" value="<?php echo $telefono2; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group has-info single-line">
                                    <label>Fax</label>
                                    <input type="text" placeholder="0000-0000" class="form-control tel" id="fax" name="fax" value="<?php echo $fax; ?>">
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                  <div class="form-group has-info single-line">
                                    <label>Correo</label>
                                    <input type="text" placeholder="mail@server.com" class="form-control" id="correo" name="correo" value="<?php echo $email; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group has-info single-line">
                                    <label>Tipo Cliente <span style="color:red;">*</span></label>
                                    <select class="col-md-12 select" id="tipo_cliente" name="tipo_cliente">
                                        <?php
                                            $sqld = "SELECT * FROM tipo_cliente ORDER BY id_tipo DESC";
                                            $resultd=_query($sqld);
                                            while($depto = _fetch_array($resultd))
                                            {
                                                echo "<option value='".$depto["id_tipo"]."'";
                                                if($tipo == $depto["id_tipo"])
                                                {
                                                  echo "selected";
                                                }
                                                echo">".$depto["descripcion"]."</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                          <div class="col-md-3">
                            <div class="form-group has-info single-line">
                              <label>Porcentaje Descuento</label>
                              <input type="text" placeholder="10%" class="form-control decimal" id="porcentaje_descuento" name="porcentaje_descuento" value="<?php echo $descuento; ?>">
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group has-info single-line">
                              <label>Días Crédito</label>
                              <input type="text" placeholder="10%" class="form-control decimal" id="dias_credito" name="dias_credito" value="<?php echo $dias_credito; ?>">
                            </div>
                          </div>
                          <div class="col-md-2" hidden>
                            <div class="form-group has-info single-line">
                              <div class='radio i-checks'><label><input id='percibe_1' name='percibe_1' type='checkbox'> <span class="label-text"><b>Percibe 1%</b></span></label></div>
                              <input type="hidden" name="hi_percibe1" id="hi_percibe1" value="0">
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group has-info single-line">
                              <div class='radio i-checks'><label><input id='retiene_iva' name='retiene_iva' type='checkbox' <?php echo $chiva; ?>> <span class="label-text"><b>Retiene IVA</b></span></label></div>
                              <input type="hidden" name="retiene_iva_valor" id="retiene1" value="<?php echo $retiene_iva; ?>">
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group has-info single-line">
                              <div class='radio i-checks'><label><input id='retiene_renta' name='retiene_renta' type='checkbox' <?php echo $chrenta; ?>> <span class="label-text"><b>Retiene Renta</b></span></label></div>
                              <input type="hidden" name="retiene_renta_valor" id="retiene10" value="<?php echo $retiene_renta; ?>">
                            </div>
                          </div>

                          <!--<div class="col-md-4" hidden="true" id="retiene_select">
                            <div class="form-group has-info single-line">
                              <label>Porcentaje de Retención <span style="color:red;">*</span></label>
                              <select class="col-md-12 select" id="porcentaje" name="porcentaje">
                                <option value="0">Sin Retención</option>
                                <option value="1">1%</option>
                                <option value="10">10%</option>
                              </select>
                            </div>
                          </div>-->
                        </div>
                        <div class="row" id="info">
                        <div class="col-md-6">
                            <label>TIPO DE DOCUMENTO</label>
                            <select name='tipo_impresion' id='tipo_impresion' class='col-md-12 select form-control'>
                              <option value="COF"
                              <?php if($tipo_documento == "COF")
                              {
                                echo "selected";
                              }
                              ?>
                              >FACTURA</option>
                              <option value="CCF"
                              <?php if($tipo_documento == "CCF")
                              {
                                echo "selected";
                              }
                              ?>
                              >CREDITO FISCAL</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>FORMA DE FACTURACION</label>
                            <select name='tipo_fact' id='tipo_fact' class='col-md-12 select form-control'>
                              <option value="0">CONCEPTO</option>
                              <option value="1">DETALLE </option>
                            </select>
                        </div>

                      </div>
                        <input type="hidden" name="id_cliente" id="id_cliente" value="<?php echo $id_cliente; ?>">
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
        echo "<script src='js/funciones/funciones_cliente.js'></script>";
	} //permiso del script
    else
    {
    		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
    }
}

function insertar()
{
    $id_cliente=$_POST["id_cliente"];
    $nombre=$_POST["nombre"];
    $direccion=$_POST["direccion"];
    $departamento=$_POST["departamento"];
    $municipio=$_POST["municipio"];
	$dui=$_POST["dui"];
    $nit=$_POST["nit"];
    $nrc=$_POST["nrc"];
    $giro=$_POST["giro"];
    $categoria=$_POST["categoria"];
    $porcentaje=$_POST["porcentaje"];

    $tipo_cliente = $_POST["tipo_cliente"];
    $retiene_iva_valor = $_POST["retiene_iva_valor"];
    $retiene_renta_valor = $_POST["retiene_renta_valor"];
    $descuento = $_POST["porcentaje_descuento"];
    $dias_credito = $_POST["dias_credito"];
    $impresion = $_POST["tipo_impresion"];
    $tipo_fact = $_POST["tipo_fact"];
    $hi_no_retiene = $_POST["hi_no_retiene"];
    $hi_retiene = $_POST["hi_retiene"];
    $hi_percibe = $_POST["hi_percibe"];

    $percibe = 0;
    $retiene = 0;
    $retiene10 = 0;
    if ($hi_retiene == 1)
    {
      if($porcentaje == 1)
      {
          $retiene = 1;
          $retiene10 = 0;
          $percibe = 0;
      }
      if($porcentaje == 10)
      {
          $retiene = 0;
          $retiene10 = 1;
          $percibe = 0;
      }
      if($porcentaje == 0)
      {
          $retiene = 0;
          $retiene10 = 0;
          $percibe = 0;
      }
    }

    if($hi_percibe == 1)
    {
      $percibe = 1;
      $retiene = 0;
      $retiene10 = 0;
    }
    if($hi_no_retiene == 1)
    {
        $percibe = 0;
        $retiene10 = 0;
        $retiene = 0;
    }
    // echo $retiene;
    $telefono1=$_POST["telefono1"];
    $telefono2=$_POST["telefono2"];
    $fax=$_POST["fax"];
    $correo=$_POST["correo"];

    $sql_exis=_query("SELECT id_cliente FROM clientes WHERE nombre='$nombre' AND nit ='$nit' AND id_cliente != '$id_cliente'");
    $num_exis = _num_rows($sql_exis);
    if($num_exis > 0)
    {
        $xdatos['typeinfo']='Error';
        $xdatos['msg']='Este cliente ya fue registrado!';
    }
    else
    {
        $table = 'clientes';
        $form_data = array(
        'categoria' => $categoria,
        'nombre' => $nombre,
        'direccion' => $direccion,
        'municipio' => $municipio,
        'depto' => $departamento,
        'nrc' => $nrc,
        'nit' => $nit,
        'dui' => $dui,
        'giro' => $giro,
        'telefono1' => $telefono1,
        'telefono2' => $telefono2,
        'fax' => $fax,
        'email' => $correo,
        'percibe' => $percibe,
        'retiene' => $retiene,
        'retiene10' => $retiene10,
        'tipo' => $tipo_cliente,
        'descuento' => $descuento,
        'dias_credito' => $dias_credito,
        'retiene_iva' => $retiene_iva_valor,
        'retiene_renta' => $retiene_renta_valor,
        'tipo_documento' => $impresion,
        'facturacion' => $tipo_fact,
        );
        $where = "id_cliente='".$id_cliente."'";
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
