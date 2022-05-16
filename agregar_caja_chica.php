<?php
include_once "_core.php";

function initial() {


	$_PAGE = array ();
	$_PAGE ['title'] = 'Ingresos y Egresos Caja Chica';
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';

	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/jqGrid/ui.jqgrid.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri=$_SERVER['REQUEST_URI'];
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
                            <h5>Registrar Ingresos y Egresos de caja chica</h5>
                        </div>
                        <div class="ibox-content">


                              <form name="formulario" id="formulario">
                              <div class="row">
                                <div class="col-md-4">
                                  <div class="form-group has-info single-line">
                                    <label class="control-label">Tipo de Transacción</label>
                                    <select  id="tipo_proceso" name="tipo_proceso" class="form-control">
                                    <option value=''>Seleccione..</option>
                                    <option value='ENTRADA'>ENTRADA</option>
                                    <option value='SALIDA'>SALIDA</optio>
                                    </select>

                                  </div>
                                </div>
                              </div>

                              <div class="row">
                                <div class="col-md-4">
                                  <div class="form-group has-info single-line"><label>Monto</label> <input type="text" placeholder="Monto" class="form-control" id="monto" name="monto"></div>
                                </div>

                                <div class="col-md-8">
                                  <div class="form-group has-info single-line">
                                      <label class="control-label" for="observaciones">Detalle Transacción</label>
                                      <input type="text" id="observaciones" name="observaciones" value="" placeholder="Detalle Transacción" class="form-control">
                                  </div>
                                </div>

                              </div>

                              <div class="row">
                                <div class="col-md-4">
                                 <div class="form-group has-info single-line">
                                  <label>Seleccione Proveedor</label>
                                  <div class="input-group">
                                  <select  name='id_proveedor' id='id_proveedor'   style="width:200px;">
                                    <option value=''>Seleccione</option>

                                   <?php
                                   $qproveedor=_query("SELECT * FROM proveedor ORDER BY nombre_proveedor ");
                                   while($row_proveedor=_fetch_array($qproveedor))
                                   {
                                       $id_proveedor=$row_proveedor["id_proveedor"];
                                       $nombre_proveedor=$row_proveedor["nombre_proveedor"];
                                       echo "
                                   <option value='$id_proveedor'>$nombre_proveedor</option>
                                   ";
                                   }
                                   ?>
                                  </select>
                                  </div>
                                  </div>
                                </div>
                                 <div class="col-md-4">
                                  <div class="form-group has-info single-line">
                                      <label class="control-label" for="Tipo documento">Tipo documento</label>
                                      <select  id="tipo_doc" name="tipo_doc" class="form-control">
                                        <option value=''>Seleccione..</option>
                                        <option value='TICKET'>TICKET</option>
                                        <option value='RECIBO'>RECIBO</optio>
                                        <option value='CREDITO FISCAL'>CREDITO FISCAL</optio>
                                        <option value='CREDITO FISCAL'>CHEQUE</optio>
                                      </select>
                                  </div>
                              </div>
                              <div class="col-md-4">
                                  <div class="form-group has-info single-line">
                                      <label class="control-label" for="observaciones">Número de Documento</label>
                                      <input type="text" id="numero_doc" name="numero_doc" value="" placeholder="Número de documento" class="form-control">
                                  </div>
                              </div>
                             </div>
                          <div class="row">
                               <div class="col-md-4">
                            <div class="form-group has-info single-line"><label>Fecha:</label> <input type="text" placeholder="Fecha" class="datepick form-control" id="fecha_proceso" name="fecha_proceso"></div>
                          </div>


                          </div>




                                    <input type="hidden" name="process" id="process" value="insert"><br>
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
echo "<script src='js/funciones/funciones_caja_chica.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function insertar(){
	 //$id_producto=$_POST["id_producto"];
    $monto=$_POST["monto"];
    $observaciones=$_POST["observaciones"];
    $tipo_proceso=$_POST["tipo_proceso"];
    $numero_doc=$_POST["numero_doc"];
    $tipo_doc=$_POST["tipo_doc"];
    $id_proveedor=$_POST["id_proveedor"];
    $fecha_proceso=$_POST["fecha_proceso"];
    $id_sucursal=$_SESSION["id_sucursal"];

    if($tipo_proceso=="ENTRADA")
    {

    $sql_result=_query("SELECT monto,saldo FROM caja_chica");
    $row=_fetch_array($sql_result);
    $monto_tabla=$row['monto'];
    $saldo_tabla=$row['saldo'];
    $contar=_num_rows($sql_result);
    if($contar==0)
    {

    $table = 'caja_chica';
    $form_data = array(
    'saldo' => $monto,
    'id_sucursal' => $id_sucursal
    );

    $insertar = _insert($table,$form_data );
    if($insertar){
      $table1="caja_chica_mov";
      $field="id_caja_chica";
      $maximo_caja_id=max_id($field,$table);

      $form_detalle = array(
      'id_caja_chica' => $maximo_caja_id,
      'fecha_mov' => $fecha_proceso,
      'ingreso' => $monto,
      'numero_doc' => $numero_doc,
      'tipo_proceso' => $tipo_proceso,
      'observaciones' => $observaciones,
      'id_proveedor' => $id_proveedor,
      'tipo_doc' => $tipo_doc,
      'id_sucursal' => $id_sucursal

      );
       $insertar1 = _insert($table1,$form_detalle);
       $xdatos['typeinfo']='Success';
       $xdatos['msg']='Registro guardado con exito!';
       $xdatos['process']='insert';
    }
    else{
       $xdatos['typeinfo']='Error';
       $xdatos['msg']='Record not inserted successfully !';
    }
    echo json_encode($xdatos);

    }
    else
    {

    $table = 'caja_chica';
    $field="id_caja_chica";
    $maximo_caja_id=max_id($field,$table);

    $total_saldo=$saldo_tabla+$monto;

    $form_data = array(
    'saldo' => $total_saldo

    );

    $where_clause = "id_caja_chica='" . $maximo_caja_id."'";
    $updates = _update ( $table, $form_data, $where_clause );

    if($updates){
      $table1="caja_chica_mov";
      $field="id_caja_chica";
      $maximo_caja_id=max_id($field,$table);

      $form_detalle = array(
      'id_caja_chica' => $maximo_caja_id,
      'fecha_mov' => $fecha_proceso,
      'ingreso' => $monto,
      'numero_doc' => $numero_doc,
      'tipo_proceso' => $tipo_proceso,
      'observaciones' => $observaciones,
      'id_proveedor' => $id_proveedor,
      'tipo_doc' => $tipo_doc,
      'id_sucursal' => $id_sucursal

      );
       $insertar1 = _insert($table1,$form_detalle);
       $xdatos['typeinfo']='Success';
       $xdatos['msg']='Registro guardado con exito!';
       $xdatos['process']='insert';
    }
    else{
       $xdatos['typeinfo']='Error';
       $xdatos['msg']='Record not inserted successfully !';
    }
    echo json_encode($xdatos);
    }



    } //finaliza if de validacion de entrada

    if($tipo_proceso=="SALIDA")
    {
      $sql_result=_query("SELECT monto,saldo FROM caja_chica");
      $row=_fetch_array($sql_result);
      $monto_tabla=$row['monto'];
      $saldo_tabla=$row['saldo'];

      if($saldo_tabla >= $monto)
      {

        $table = 'caja_chica';
        $field="id_caja_chica";
        $maximo_caja_id=max_id($field,$table);


        $total_saldo=$saldo_tabla-$monto;



        $form_data = array(
        'saldo' => $total_saldo

        );

        $where_clause = "id_caja_chica='" . $maximo_caja_id."'";
        $updates = _update ( $table, $form_data, $where_clause );

    if($updates){
      $table1="caja_chica_mov";
      $field="id_caja_chica";
      $maximo_caja_id=max_id($field,$table);

      $form_detalle = array(
      'id_caja_chica' => $maximo_caja_id,
      'fecha_mov' => $fecha_proceso,
      'egreso' => $monto,
      'numero_doc' => $numero_doc,
      'tipo_proceso' => $tipo_proceso,
      'observaciones' => $observaciones,
      'id_proveedor' => $id_proveedor,
      'tipo_doc' => $tipo_doc,
      'id_sucursal' => $id_sucursal

      );
       $insertar1 = _insert($table1,$form_detalle);
       $xdatos['typeinfo']='Success';
       $xdatos['msg']='Registro guardado con exito!';
       $xdatos['process']='insert';
    }
    else{
       $xdatos['typeinfo']='Error';
       $xdatos['msg']='Record not inserted successfully !';
    }
    //echo json_encode($xdatos);

      }
      else
      {
      $xdatos['typeinfo']='Error';
      $xdatos['msg']='No tiene suficiente saldo para generar esta salida !';

      }
    echo json_encode($xdatos);

    }



}

if(!isset($_POST['process'])){
	initial();
}
else
{
if(isset($_POST['process'])){
switch ($_POST['process']) {
	case 'insert':
		insertar();
		break;

	}
}
}
?>
