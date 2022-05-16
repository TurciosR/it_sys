<?php
include ("_core.php");
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
	$sql_empleado = _query("SELECT nombre FROM empleados WHERE  id_empleado = '$empleado'");
	$nombre= _fetch_array($sql_empleado);
	$nombre_empleado=$nombre["nombre"];
	$hora_actual = date('H:i:s');
	if($admin==1){
		$caja=1;
	}else
	{
		$caja=$caja;
	}
	if($cuenta > 0 || $admin==1)
	{
		if($id_user==$empleado || $admin=='1' ){
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Costos</h4>
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
					<label>Proceso </label>
					<select class="form-control" name="proceso" id="proceso" onchange="proceso_d()">
						<option value="1">Pendiente</option>
						<option value="0">Finalizado</option>
					</select>
				</div>
    		</div>
    	</div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group has-info single-line">
					<label>Tipo Documento </label>
					<select class="form-control" name="tipo_doc" id="tipo_doc">
						<option value="CCF">Credito Fiscal</option>
						<option value="COF">Factura</option>
						<option value="RE">Recibo</option>
						<option value="VAL">Vale</option>
					</select>
				</div>
			</div>
			<div class="col-md-6" id="div_docu" hidden="true">
				<div class="has-info single-line">
					<label>Numero de Documento</label>
					<input type="text" name="n_doc" id="n_doc" class="form-control" value="0000">
				</div>
			</div>
		</div>
		<div class="row">
					<div class="col-md-6">
	          <div class="form-group has-info single-line">
	          	<label>Concepto</label>
	          	<input type='text'  class='form-control' id='concepto' name='concepto'>
	          </div>
					</div>
					<div class="col-md-6">
	          <div class="form-group has-info single-line">
	          	<label>Proveedor/Otro </label> <input type='text'  class='form-control' id='proveedor' name='proveedor'>
	          </div>
					</div>
    	</div>
    	<div class="row">
    			<div class="col-md-6">
	          <div class="form-group has-info single-line">
	          	<label>Monto </label> <input type='text'  class='form-control numeric' id='monto' name='monto'>
	          </div>
					</div>
					<div class="col-md-6">
	          <div class="form-group has-info single-line">
	          	<label>Recibe </label> <input type='text'  class='form-control' id='recibe' name='recibe'>
	          </div>
					</div>
    	</div>


			<div class="row">

    	</div>
	</div>
		<!--/div-->
		<!--/div-->
	<input type="hidden" name="id_empleado" id="id_empleado" value="<?php echo $empleado;?>">
	<input type="hidden" name="caja" id="caja" value="<?php echo $caja;?>">
	<input type="hidden" name="turno" id="turno" value="<?php echo $turno;?>">
	<input type="hidden" name="id_apertura" id="id_apertura" value="<?php echo $id_apertura;?>">
	<input type="hidden" name="admin" id="admin" value="<?php echo $admin;?>">

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btnSalida">Guardar</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div>
<script type="text/javascript">
	$(".numeric").numeric(
		{
			negative:false,
		}
	);
	$(".datepick").datepicker({
		format: 'dd-mm-yyyy',
		language:'es',
	});
	$(document).ready(function() {
  	$("#proveedor").typeahead({
		source: function(query, process) {
			$.ajax({
				type: 'POST',
				url: 'autocomplete_proveedor.php',
				data: 'query=' + query,
				dataType: 'JSON',
				async: true,
				success: function(data) {
					process(data);
				}
			});
		},
	});
});
</script>
<!--/modal-footer -->

<?php
}
else
{
	echo "<div></div><br><br><div class='alert alert-warning text-center'> Ya existe una apertura de caja realizada por '".$nombre_empleado."' Debe de realizar el corte para poder iniciar una nueva apertura de caja. .</div>";
}
}
else
{
	echo "<div></div><br><br><div class='alert alert-warning text-center'>No se ha encontrado una apertura vigente.</div>";
}
}

function salida()
{
	date_default_timezone_set("America/El_Salvador");
	$id_empleado = $_POST["id_empleado"];
	$id_apertura = $_POST["id_apertura"];
	$caja = $_POST["caja"];
	$turno = $_POST["turno"];
	$concepto = $_POST["concepto"];
	$monto = $_POST["monto"];
	$id_sucursal=$_SESSION['id_sucursal'];
	$proveedor = $_POST["proveedor"];
	$tipo_doc = $_POST["tipo_doc"];
	$n_doc = $_POST["n_doc"];
	$recibe = $_POST["recibe"];
	$estado = $_POST["estado"];
	$autoriza = "Lic. Silvia de Melendez";

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
	$iva = 0;
	if($tipo_doc == "CCF")
	{
		$iva = round($monto - ($monto / 1.13), 2);
	}
	//agregar correlativo agregar vale
	$sql_num = _query("SELECT av FROM correlativo WHERE id_sucursal='$id_sucursal'");
	$datos_num = _fetch_array($sql_num);
	$ult = $datos_num["av"]+1;
	$len_ult = strlen($ult);
	$cantidad_ceros = 7-$len_ult;
	$numero_doc=ceros_izquierda($cantidad_ceros,$ult).'_AV';
	/*actualizar los correlativos de II*/
	$corr=1;
	$table="correlativo";
	$form_data = array(
		'av' =>$ult,
	);
	$where_clause_c="id_sucursal='".$id_sucursal."'";
	$up_corr=_update($table,$form_data,$where_clause_c);
	if ($up_corr) {
		# code...
	}
	else {
		$corr=0;
	}

	$tabla = "mov_caja";
	$form_data = array(
		'fecha' => $fecha,
		'hora' => $hora,
		'valor' => $monto,
		'concepto' => $concepto,
		'id_empleado' => $id_empleado,
		'id_sucursal' => $id_sucursal,
		'salida' => 1,
		'turno' => $turno,
		'id_apertura' => $id_apertura,
		'nombre_proveedor' => $proveedor,
		'nombre_autoriza' => $autoriza,
		'tipo_doc' => $tipo_doc,
		'numero_doc' => $n_doc,
		'iva' => $iva,
		'nombre_recibe' => $recibe,
		'correlativo' => $numero_doc,
		'caja' =>$caja,
		'estado'=>$estado
		);
	$insetar = _insert($tabla, $form_data);
	$id_mov= _insert_id();
	if($insetar)
	{
		$mont=_query("SELECT monto_ch_actual FROM apertura_caja WHERE id_apertura='$id_apertura'");
		$monto_a=_fetch_array($mont);
		$monto_ap=$monto_a["monto_ch_actual"];
		$resta=$monto_ap-$monto;
		$tabla_aper = "apertura_caja";
		$form_aper = array(
			'monto_ch_actual' => $resta,
			);
			$where_m = "id_apertura='".$id_apertura."'";
	    $update_apertura = _update($tabla_aper, $form_aper, $where_m);
		if($update_apertura){
			$xdatos['typeinfo']='Success';
			$xdatos['msg']='Vale agregado correctamente !';
			$xdatos['process']='insert';
			$xdatos['id_mov']=$id_mov;
		}

	}
	else
	{
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Error al realizar el vale !'._error();
	}
	echo json_encode($xdatos);
}

if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'formDelete' :
				initial();
				break;
			case 'salida' :
				salida();
				break;
		}
	}
}

?>
