<?php
include ("_core.php");
include ('num2letras.php');
include ('facturacion_funcion_imprimir.php');
function initial(){
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$id_sucursal=$_SESSION['id_sucursal'];
	date_default_timezone_set('America/El_Salvador');
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	//permiso del script

	//include ('facturacion_funcion_imprimir.php');
	//$sql="SELECT * FROM factura WHERE id_factura='$id_factura'";
	$sql_apertura = _query("SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal'");
	$cuenta = _num_rows($sql_apertura);
	$row_apertura = _fetch_array($sql_apertura);
	$id_apertura = $row_apertura["id_apertura"];
	$empleado = $row_apertura["id_empleado"];
	$turno = $row_apertura["turno"];
	$fecha_apertura = $row_apertura["fecha"];
	$hora_apertura = $row_apertura["hora"];
	$monto_apertura = $row_apertura["monto_apertura"];
	$caja = $row_apertura["caja"];
	if($admin==1){
		$caja=1;
	}else{
		$caja=$caja;
	}

	$hora_actual = date('H:i:s');
	if($cuenta > 0 || $admin==1)
	{
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Agregar Ingreso</h4>
</div>
<div class="modal-body">
	<!--div class="wrapper wrapper-content  animated fadeInRight"-->
	<div class="row" id="row1">
		<!--div class="col-lg-12"-->
		<?php
		if($admin == 1)
        {
      ?>
      <div class="row">
        <div class="form-group col-md-6">
          <label>Fecha</label>
          <input type="text"  class='form-control datepick' id="fecha_fac"  name="fecha_fac" value='<?php echo date("d-m-Y");?>'/>

        </div>
        <div class="form-group col-md-6">
          <label>Cajero</label>
          <select class="form-control select" name="cajero" id="cajero" style="width:100%;">
            <?php
               $sql_cajeros = _query("SELECT * FROM empleados WHERE tipo_empleado = 3");
               $cuenta_cajero = _num_rows($sql_cajeros);
               if($cuenta_cajero)
               {
                 while ($row_cajero = _fetch_array($sql_cajeros))
                 {
                   $id_empleado = $row_cajero["id_empleado"];
                   $nombre_cajero = $row_cajero["nombre"];

                   echo "<option value='".$id_empleado."'>".$nombre_cajero."</option>";
                 }
               }

             ?>
          </select>
        </div>

      </div>
      <div class="row">
        <div class="form-group col-md-6">
          <label>Turno</label>
          <select class="form-control select" name="turno_fac" id="turno_fac" style="width: 100%">
            <option value="1">Turno 1</option>
            <option value="2">Turno 2</option>
            <option value="3">Turno 3</option>
          </select>

        </div>


      </div>
    <?php } ?>
		<div class="row">
			<div class="col-md-12">
	          <div class="form-group has-info single-line">
	          	<label>Concepto</label>
	          	<input type='text'  class='form-control' id='concepto' name='concepto'>
	          </div>
			</div>
    	</div>
    	<div class="row">
    		<div class="col-md-12">
	          <div class="form-group has-info single-line">
	          	<label>Monto </label> <input type='text'  class='form-control' id='monto' name='monto'>
	          </div>
			</div>
    	</div>
	</div>
		<!--/div-->
		<!--/div-->
	<input type="hidden" name="id_empleado" id="id_empleado" value="<?php echo $empleado;?>">
	<input type="hidden" name="turno" id="turno" value="<?php echo $turno;?>">
	<input type="hidden" name="id_apertura" id="id_apertura" value="<?php echo $id_apertura;?>">
	<input type="hidden" name="admin" id="admin" value="<?php echo $admin;?>">
	<input type="hidden" name="caja" id="caja" value="<?php echo $caja;?>">

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btnIngreso">Guardar</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div>
<!--/modal-footer -->
<script type="text/javascript">
	$(".datepick").datepicker({
		format: 'dd-mm-yyyy',
		language:'es',
	});

</script>
<?php

}
else
{
	echo "<div></div><br><br><div class='alert alert-warning text-center'>No se ha encontrado una apertura vigente.</div>";
}
}

function ingreso()
{
	date_default_timezone_set("America/El_Salvador");
	$id_empleado = $_POST["id_empleado"];
	$id_apertura = $_POST["id_apertura"];
	$caja = $_POST["caja"];
	$turno = $_POST["turno"];
	$concepto = $_POST["concepto"];
	$monto = $_POST["monto"];
	$id_sucursal=$_SESSION['id_sucursal'];

	//$fecha = date("Y-m-d");
	//$hora = date("H:i:s");
	$admin=$_POST["admin"];
	if($admin==1){
		$fecha=MD($_POST["fecha_fac"]);
		$hora = "00:00:00";

	}else
	{
		$fecha = date("Y-m-d");
		$hora = date("H:i:s");
	}
	//echo $id_apertura." ".$turno." ".$fecha." ".$id_empleado;
	//si es admin
	//apertura caja
	$sql_apertura = _query("SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal' AND fecha='$fecha' AND id_empleado = '$id_empleado'");
  $cuenta = _num_rows($sql_apertura);
  $turno_vigente=0;
  if ($cuenta>0) {
    $row_apertura = _fetch_array($sql_apertura);
    $id_apertura = $row_apertura["id_apertura"];
    $turno = $row_apertura["turno"];
    $caja = $row_apertura["caja"];
    $fecha_apertura = $row_apertura["fecha"];
    $hora_apertura = $row_apertura["hora"];
    $turno_vigente = $row_apertura["vigente"];
    $id_apertura_pagada=$id_apertura;
  }
  else
  {
    $sql_other = _query("SELECT * FROM apertura_caja WHERE id_sucursal = '$id_sucursal'
      AND fecha='$fecha'");
    $cuenta_other = _num_rows($sql_other);
    if($cuenta_other > 0)
    {
      $row_other = _fetch_array($sql_other);
      $id_apertura = $row_other["id_apertura"];
      $id_apertura_pagada = $id_apertura;
      $caja = $row_other["caja"];
      $sql_turno_other = _query("SELECT * FROM detalle_apertura WHERE id_apertura = '$id_apertura' AND turno = '$turno'");
      $cuenta_tur = _num_rows($sql_turno_other);
      if($cuenta_tur > 0)
      {
        $turno = $turno;
      }
      else
      {
        if($turno == 1)
        {
          $hora_i = "08:00:00";
        }
        if($turno == 2)
        {
          $hora_i = "12:00:00";
        }
        if($turno == 3)
        {
          $hora_i = "13:00:00";
        }
        $tabla12 = "detalle_apertura";
        $form_data12 = array(
            'id_apertura' => $id_apertura,
            'turno' => $turno,
            'id_usuario' => $id_empleado,
            'fecha' => $fecha,
            'hora' => $hora_i,
            'caja' => 1,
            );
        $insert_de1 = _insert($tabla12,$form_data12);
        if($insert_de1)
        {
          $turno = $turno;
        }
      }
    }
    else
    {
      $tab = "apertura_caja";
      $form_datax = array(
          'fecha' => $fecha,
          'id_empleado' => $id_empleado,
          'turno' => $turno,
          'monto_apertura' => 100,
          'id_sucursal' => $id_sucursal,
          'hora' => "08:00:00",
          'caja' => 1,
          'monto_ch' => 125,
          'monto_ch_actual' => 125,
        );
        $insertan = _insert($tab, $form_datax);
        $id_apertura = _insert_id();
        $caja = 1;
        $id_apertura_pagada = $id_apertura;
        if($insertan)
        {
          if($turno == 1)
          {
            $hora_i = "08:00:00";
          }
          if($turno == 2)
          {
            $hora_i = "12:00:00";
          }
          if($turno == 3)
          {
            $hora_i = "13:00:00";
          }
          $tabla1 = "detalle_apertura";
          $form_data1 = array(
              'id_apertura' => $id_apertura,
              'turno' => $turno,
              'id_usuario' => $id_empleado,
              'fecha' => $fecha,
              'hora' => $hora_i,
              'caja' => 1,
              );
          $insert_de = _insert($tabla1,$form_data1);
          if($insert_de)
          {
            $turno = $turno;
          }
        }
    }
  }
              //finaliza lo de la apertura
	//agregar correlativo agregar vale
	$sql_num = _query("SELECT ai FROM correlativo WHERE id_sucursal='$id_sucursal'");
	$datos_num = _fetch_array($sql_num);
	$ult = $datos_num["ai"]+1;
	$len_ult = strlen($ult);
	$cantidad_ceros = 7-$len_ult;
	$numero_doc=ceros_izquierda($cantidad_ceros,$ult).'_AI';
	/*actualizar los correlativos de II*/
	$corr=1;
	$table="correlativo";
	$form_data = array(
		'ai' =>$ult,
	);
	$where_clause_c="id_sucursal='".$id_sucursal."'";
	$up_corr=_update($table,$form_data,$where_clause_c);
	if ($up_corr) {
		# code...
	}
	else {
		$corr=0;
	}
	_begin();
	$tabla = "mov_caja";
	$form_data = array(
		'fecha' => $fecha,
		'hora' => $hora,
		'valor' => $monto,
		'concepto' => $concepto,
		'id_empleado' => $id_empleado,
		'id_sucursal' => $id_sucursal,
		'entrada' => 1,
		'turno' => $turno,
		'id_apertura' => $id_apertura,
		'correlativo' =>$numero_doc,
		'caja' => $caja,
		);
	$insetar = _insert($tabla, $form_data);
	$id_mov= _insert_id();
	if($insetar)
	{
		$sql_dato = _query("SELECT monto_ch_actual FROM apertura_caja WHERE id_apertura='$id_apertura'");
		$sql_monto=_fetch_array($sql_dato);
		$monto_a=$sql_monto["monto_ch_actual"]+$monto;
		$table2 = 'apertura_caja';
    $form_apertura_caja = array(
      'monto_ch_actual' =>$monto_a,
    );
    $where2 = "id_apertura='".$id_apertura."'";
    $update2 = _update($table2, $form_apertura_caja, $where2);
		if($update2)
		{
			 _commit();
			$xdatos['typeinfo']='Success';
			$xdatos['msg']='Ingreso agregado correctamente !';
			$xdatos['process']='insert';
			$xdatos['id_mov']=$id_mov;
		}
	}
	else
	{
		_rollback();
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Error al realizar el ingreso !'._error();
	}
	echo json_encode($xdatos);
}


function  imprimir(){
	$id_movimiento = $_POST["id_movimiento"];
	$id_sucursal=$_SESSION['id_sucursal'];
	//directorio de script impresion cliente
	$sql_dir_print="SELECT *  FROM config_dir WHERE id_sucursal='$id_sucursal'";
	$result_dir_print=_query($sql_dir_print);
	$row0=_fetch_array($result_dir_print);
	$dir_print=$row0['dir_print_script'];
	$shared_printer_win=$row0['shared_printer_matrix'];
	$shared_printer_pos=$row0['shared_printer_pos'];

	$info_mov=print_vale($id_movimiento);
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';
	$nreg_encode['shared_printer_win'] =$shared_printer_win;
	$nreg_encode['shared_printer_pos'] =$shared_printer_pos;

	$nreg_encode['dir_print'] =$dir_print;
	$nreg_encode['movimiento'] =$info_mov;
	$nreg_encode['sist_ope'] =$so_cliente;

	echo json_encode($nreg_encode);
}

function  imprimir_viatico(){
	$id_movimiento = $_POST["id_movimiento"];
	$id_sucursal=$_SESSION['id_sucursal'];
	//directorio de script impresion cliente
	$sql_dir_print="SELECT *  FROM config_dir WHERE id_sucursal='$id_sucursal'";
	$result_dir_print=_query($sql_dir_print);
	$row0=_fetch_array($result_dir_print);
	$dir_print=$row0['dir_print_script'];
	$shared_printer_win=$row0['shared_printer_matrix'];
	$shared_printer_pos=$row0['shared_printer_pos'];

	$info_mov=print_viatico($id_movimiento);
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';
	$nreg_encode['shared_printer_win'] =$shared_printer_win;
	$nreg_encode['shared_printer_pos'] =$shared_printer_pos;

	$nreg_encode['dir_print'] =$dir_print;
	$nreg_encode['movimiento'] =$info_mov;
	$nreg_encode['sist_ope'] =$so_cliente;

	echo json_encode($nreg_encode);
}

if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'formDelete' :
				initial();
				break;
			case 'ingreso' :
				ingreso();
				break;
			case 'imprimir':
			 imprimir();
			 break;
		 case 'imprimir_viatico':
			 imprimir_viatico();
			 break;
		}
	}
}

?>
