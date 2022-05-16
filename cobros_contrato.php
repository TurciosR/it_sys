<?php
include_once "_core.php";

function initial()
{
	$title='Generar Cobros';
	$_PAGE = array ();
	$_PAGE ['title'] = $title;
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/upload_file/fileinput.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	//permiso del script
  $id_contrato = $_REQUEST["id_contrato"];
  $sql_contrato = _query("SELECT con.*, cl.nombre as nombre_cliente FROM contrato as con, clientes as cl WHERE id_contrato = '$id_contrato' AND con.id_cliente = cl.id_cliente");
  $row = _fetch_array($sql_contrato);
  $nombre_cliente = $row["nombre_cliente"];
  $numero_doc = $row["numero_doc"];
  $fecha = $row["fecha"];
  $monto = $row["monto"];
  $iva = $row["iva"];
  $tipo = $row["tipo"];
  $sub_total = round($monto + $iva, 3);


	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	$fecha_hoy=date("Y-m-d");
?>
<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
			<?php
				if ($links!='NOT' || $admin=='1' ){
			?>
	            <div class="ibox-title">
	                <h5><?php echo $title; ?></h5>

	            </div>
							<input type="hidden" name="id_contrato" id="id_contrato" value="<?php echo $id_contrato; ?>">
	            <div class="ibox-content">
                <div class="row">
                  <div class="col-md-12">
                    <table class="table bordered">
                      <thead>
                        <tr>
                          <td class="col-md-3"><label>Nombre cliente</label></td>
                          <td class="col-md-9"><label><?php echo $nombre_cliente; ?></label></td>
                        </tr>
                        <tr>
                          <td class="col-md-3"><label>Fecha</label></td>
                          <td class="col-md-9"><label><?php echo ED($fecha); ?></label></td>
                        </tr>
                        <tr>
                          <td class="col-md-3"><label>Monto</label></td>
                          <td class="col-md-9"><label>$<?php echo number_format($sub_total, 2); ?></label> <input type="hidden" name="val_monto" id="val_monto" value="<?php echo $sub_total; ?>"> </td>
                        </tr>
                        <tr>
                          <td class="col-md-3"><label>Falta</label></td>
                          <td class="col-md-9 " style="color: red"><label class="faltante_text">$<?php echo number_format($sub_total, 2); ?></label> <input type="hidden" name="val_faltante" id="val_faltante" value="<?php echo $sub_total; ?>"></td>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-md-3">
                    <label>Monto</label>
                    <input type="text" name="monto" id="monto" class="form-control" value="">
                  </div>
                  <div class="col-lg-3">
                    <label>Tiempo de contrato</label>
                    <div class="row">
                      <div class="col-md-6">
                          <input type="text" placeholder="" class="form-control" id="tiempo" name="tiempo">
                      </div>
                      <div class="col-md-6">
                          <select class="select form-control" name="periodo" id="periodo">
                            <option value="0">Mes/es</option>
                            <option value="1">Año/s</option>
                          </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <label>Fecha inicio</label>
                    <input type="text" name="fecha_inicio" id="fecha_inicio" class="form-control datepick2" value="<?php echo date("d-m-Y"); ?>">
                  </div>
                  <div class="col-md-2">
                    <div id="caja_f" hidden>
                      <label>Frecuencia de cobro</label>
                      <select class="select form-control" name="frecuencia_cobro" id="frecuencia_cobro" style="width:100%">
                        <option value="0">Mesual</option>
                        <option value="1">Anual</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <a id="btn_add" name="btn_add" class="btn btn-primary m-t-n-xs pull-right" style="margin-top:20px;"><i class="fa fa-plus"></i> Agregar Periodo</a>
                  </div>
                </div>
                <hr>
                <div class="row" id="periodos">
                  <div class="row" id='caja_periodo_1'>
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <div class="col-md-2">
                                  <h5 style="color:#000;">Periodo</h5>
                                </div>
                                <div class="col-md-7">

                                </div>
                                <div class="col-md-3">
                                  <div class="ibox-tools">
                                      <a class="collapse-link desplegar" style="color:#000;">
                                        Mas detalles <i class="fa fa-chevron-down" style="color:#000;"></i>
                                      </a>
                                  </div>
                                </div>
                            </div>
                            <div class="ibox-content desp" style="margin-top: 1.8px; display: none">
                              <table class="table bordered">
                                <thead>
                                  <tr>
                                    <th>Detalles</th>
                                    <th>Fecha Vencimiento</th>
                                    <th>Monto ($)</th>
                                    <th>Accion</th>
                                  </tr>
                                </thead>
                                <tbody id="lista_periodos">

                                </tbody>
                              </table>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
								<?php if($tipo == "SIS" || $tipo == "GER"){ ?>
								<div class="row">
                  <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <div class="col-md-2">
                                  <h5 style="color:#000;">Modulos de sistema</h5>
                                </div>
                                <div class="col-md-7">

                                </div>
                                <div class="col-md-3">
                                  <div class="ibox-tools">
                                      <a class="collapse-link desplegar" style="color:#000;">
                                        Mas detalles <i class="fa fa-chevron-down" style="color:#000;"></i>
                                      </a>
                                  </div>
                                </div>
                            </div>
                            <div class="ibox-content desp" style="margin-top: 1.8px; display: none">
                              <?php
																$sql_menus="SELECT id_modulo, nombre FROM modulos_sistema";
																//order by prioridad
																$result=_query($sql_menus);
																$numrows=_num_rows($result);
																if($numrows > 0)
																{
																	$paneln=0;
																	while ($row = _fetch_array($result))
																	{
																		$menuname=$row['nombre'];
																		$id_menu=$row['id_modulo'];
																		//$icono=$row['icono'];

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

																		echo "<div class='panel-body' style='width: 100%; height:150px; overflow:auto;'>";
																			$sql_links="SELECT distinct ms.id_modulo, ms.nombre as nombremenu,
																			dms.id_detalle_modulo, dms.nombre as nombremodulo
																			FROM modulos_sistema as ms, modulos_sistema_detalle as dms
																			WHERE ms.id_modulo= '$id_menu'
																			AND ms.id_modulo=dms.id_modulo_sistema
																			";
																			$result_modules=_query($sql_links);
																			$numrow2=_num_rows($result_modules);
																			if($numrow2>0){
																			for($j=0;$j<$numrow2;$j++){
																				$row_modules=_fetch_array($result_modules);
																				//$lnk=strtolower($row_modules['filename']);
																				$modulo=$row_modules['nombremodulo'];
																				$id_modulo=$row_modules['id_detalle_modulo'];

																				echo"<p>";
																				echo"<div class='checkbox i-checks'><label> <input id='myCheckboxes' name='myCheckboxes' type='checkbox' value='$id_modulo'> <i></i> ".ucfirst($modulo)."</label></div>";
																				echo"</p>";



																		}
																		}
																		echo"</div>"; //panel-body
																		echo"</div>";//panel panel-primary';
																		echo"</div>"; //  <div class='col-lg-4'>
																	}
																}
															?>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
							<?php } ?>
									<div class="row">
										<div class="col-lg-12">
											<input type="hidden" name="process" id="process" value="insert">
											<input type="hidden" name="origin" id="origin" value="">
											<input type="hidden" name="tipo_con" id="tipo_con" value="<?php echo $tipo; ?>">
											<input type="submit" id="btn_cuotas" name="btn_cuotas" value="Guardar" class="btn btn-primary m-t-n-xs pull-right" />
											<!--<a id="duplicar" name="duplicar" class="btn btn-primary m-t-n-xs">Guardar y Duplicar</a>-->
										</div>
									</div>

                </div>
            </div>
        </div>
    </div>
</div>
<?php
	include_once ("footer.php");
		echo "<script src='js/funciones/funciones_contrato.js'></script>";
	}
	else
	{
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function add_periodo()
{
  $monto = $_POST["monto"];
  $tiempo = $_POST["tiempo"];
  $periodo = $_POST["periodo"];
	$mes_x = $_POST["mes_ultimo"];
	$anhio_x = $_POST["anhio_ultimo"];

	if($mes_x > 0 && $anhio_x > 0)
	{
		$mes_x = $mes_x + 1;
		if($mes_x > 12)
		{
			$anhio_x +=1;
		}
		$fecha = "01-".$mes_x."-".$anhio_x;
	}
	else
	{
		$fecha = $_POST["fecha"];
	}
  $frecuencia = $_POST["frecuencia"];

  if($periodo == 1)
  {

    if($frecuencia == 0)
    {
      $cantidad = $tiempo * 12;
    }
    else
    {
      $cantidad = $tiempo;
    }
    $cuota = round(($monto / $cantidad), 4);
  }
  else
  {
    $cantidad = $tiempo;
    $cuota = round(($monto / $cantidad), 4);
  }

  $f = explode("-",$fecha);
  $dia_inicio = $f[0];
  $mes_inicio = $f[1];
  $anhio_inicio = $f[2];
  if($dia_inicio > 25)
  {
    $mes_inicio += 1;
  }

  $lista = "";
  for ($i=0; $i < $cantidad; $i++)
  {
    $ultimo_dia = date("d",(mktime(0,0,0,$mes_inicio+1,1,$anhio_inicio)-1));
    $fecha_vence = $anhio_inicio."-".$mes_inicio."-".$ultimo_dia;

    list($a,$m,$d) = explode("-", $fecha_vence);
    $fecha_detalle= $d." de ".ucwords(Minu(meses($m)))." de ".$a;

    $lista .= "<tr>";
    $lista .= "<td>Cuota de ".ucwords(Minu(meses($mes_inicio)))." ".$anhio_inicio."<input  id='mes_ultimo' type='hidden' class='mes_ultimo'  value='".$mes_inicio."'><input  id='anhio_ultimo' type='hidden' class='anhio_ultimo' value='".$anhio_inicio."'></td>";
    $lista .= "<td>".$fecha_detalle." <input  id='fecha_vence' type='hidden' class='fecha_vence'  value='".$fecha_vence."'></td>";
    $lista .= "<td>".number_format($cuota, 2)."<input  id='cuota' type='hidden' class='cuota'  value='".$cuota."'></td>";
    $lista .= "<td class='delete_fila'><input  id='delprod' type='button' class='btn btn-danger fa pull-right'  value='&#xf1f8;'></td>";
    $lista .= "</tr>";
    if($mes_inicio >= 12)
    {
      $mes_inicio = 1;
      $anhio_inicio += 1;
    }
    else
    {
      $mes_inicio += 1;
    }
  }
  echo $lista;

}

function insert()
{
	$id_contrato = $_POST["id_contrato"];
	$cuantos = $_POST["cuantos"];
	$array_json=$_POST['json_arr'];
	$lista_chekes=$_POST['lista_chekes'];
	$cuantos_chekes = $_POST["qty"];
	$i = 0;
	$i1 = 0;
	if($cuantos > 0)
	{
		$array = json_decode($array_json, true);
		foreach ($array as $fila)
		{
			$mes = $fila["mes"];
			$anhio = $fila["anhio"];
			$cuota = $fila["cuota"];
			$fecha_vence = $fila["fecha_vence"];
			$tabla = "cuota_contrato";
			$form_array = array(
				'id_contrato' => $id_contrato,
				'monto' => $cuota,
				'mes' => $mes,
				'anhio' => $anhio,
				'fecha_vence' => $fecha_vence,
			);
			$insert = _insert($tabla, $form_array);
			if($insert)
			{
				$i += 1;
			}
		}
	}
	if($cuantos_chekes > 0)
	{
		$ex = explode(",", $lista_chekes);
		for ($ij=0; $ij < $cuantos_chekes ; $ij++) {
			$id_modulo = $ex[$ij];
			$tabla = "modulos_contrato";
			$form_array = array(
				'id_contrato' => $id_contrato,
				'id_modulo' => $id_modulo,
			);
			$insert1 = _insert($tabla, $form_array);
			if($insert1)
			{
				$i1 += 1;
			}
		}
	}

	if($cuantos == $i && $cuantos_chekes == $i1)
	{
		$xdatos['typeinfo']='Success';
		$xdatos['msg']='Cuotas guardadas con exito!';
		$xdatos['process']='insert';
	}
	else
	{
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Error al guardar cuotas!';
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
	case 'insert_cuotas':
		insert();
		break;
  case 'add_periodo':
		add_periodo();
		break;
	}
}
}
?>
