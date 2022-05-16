<?php
include_once "_core.php";
include('num2letras.php');
function initial()
{
    $_PAGE = array();
    $_PAGE ['title'] = 'Contrato';
    $_PAGE ['links'] = null;
    $_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">';
    //$_PAGE ['links'] .= '<link href="css/style_table2.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/style_table3.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/plugins/perfect-scrollbar/perfect-scrollbar.css">';
    $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/util.css">';
    $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/main.css">';

    include_once "header.php";
    //include_once "main_menu.php";

    //permiso del script
    $id_user=$_SESSION["id_usuario"];
    $admin=$_SESSION["admin"];
    $id_sucursal = $_SESSION["id_sucursal"];

    $uri = $_SERVER['SCRIPT_NAME'];
    $filename=get_name_script($uri);
    $links=permission_usr($id_user, $filename);

    //crear array proveedores
    $sql0="SELECT * FROM proveedores";
    $result0=_query($sql0);
    $count0=_num_rows($result0);
    $array0 =array(-1=>"Seleccione");
    for ($x=0;$x<$count0;$x++) {
        $row0=_fetch_array($result0);
        $id0=$row0['id_proveedor'];
        $description=$row0['nombre'];
        $array0[$id0] = $description;
    }
    //crear array pedidos
    $array1 = array(-1=>"Seleccione");
    //crear array colores
    $sql2="SELECT * FROM colores";
    $result2=_query($sql2);
    $count2=_num_rows($result2);
    $array2= array(-1=>"Seleccione");
    for ($y=0;$y<$count2;$y++) {
        $row2=_fetch_array($result2);
        $id2=$row2['id_color'];
        $description2=$row2['nombre'];
        $array2[$id2] = $description2;
    }
    $iva=0;
    $sql_iva="select iva,monto_retencion1,monto_retencion10,monto_percepcion from empresa";
    $result_IVA=_query($sql_iva);
    $row_IVA=_fetch_array($result_IVA);
    $iva=$row_IVA['iva']/100;
    $monto_retencion1=$row_IVA['monto_retencion1'];
    $monto_retencion10=$row_IVA['monto_retencion10'];
    $monto_percepcion=$row_IVA['monto_percepcion'];
    //array de tipos Documento
    $sql3='SELECT idtipodoc, nombredoc, provee,  alias FROM tipodoc WHERE provee=1';
    $result3=_query($sql3);
    $count3=_num_rows($result3);
    $array3= array(-1=>"Seleccione");
    for ($z=0;$z<$count3;$z++) {
        $row3=_fetch_array($result3);
        $id3=$row3['alias'];
        $description3=$row3['nombredoc'];
        $array3[$id3] = $description3;
    }
    //array de Pedidos

    $array4= array(-1=>"Seleccione");
    /*
    $sql4='SELECT * FROM pedidos WHERE  finalizado=0 AND anulado=0 AND verificado=1';
    $result4=_query($sql4);
    $count4=_num_rows($result4);
    for ($a=0;$a<$count4;$a++) {
        $row4=_fetch_array($result4);
        $id4=$row4['idtransace'];
        $fechapedido=ed($row4['fecha']);
        $description4=$row4['idtransace']." (".$fechapedido.")";
        $array4[$id4] = $description4;
    } */?>
<style media="screen">
  .my-custom-scrollbar {
  position: relative;
  height: 450px;
  overflow: auto;
  }
  .table-wrapper-scroll-y {
  display: block;
  }
</style>
  <div class="gray-bg">
  <div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
      <div class="col-lg-12">
        <!--Primero si e si es inv. inicial ,factura de compra, compra caja chica, traslado de otra sucursal; luego Registrar No. de Factura , lote, proveedor -->
        <div class="ibox">
          <?php
                        //permiso del script
                        if ($links!='NOT' || $admin=='1') {
                            ?>
            <div class="ibox-content">
              <input type='hidden' name='porc_iva' id='porc_iva' value='<?php echo $iva; ?>'>
              <input type='hidden' name='monto_retencion1' id='monto_retencion1' value='<?php echo $monto_retencion1; ?>'>
              <input type='hidden' name='monto_retencion10' id='monto_retencion10' value='<?php echo $monto_retencion10; ?>'>
              <input type='hidden' name='monto_percepcion' id='monto_percepcion' value='<?php echo $monto_percepcion; ?>'>
              <input type='hidden' name='porc_retencion1' id='porc_retencion1' value=0>
              <input type='hidden' name='porc_retencion10' id='porc_retencion10' value=0>
              <input type='hidden' name='porc_percepcion' id='porc_percepcion' value=0>
              <?php
                        //1 INVENTARIO INICIAL
              $fecha_actual=date("Y-m-d");
              $f = explode("-", $fecha_actual);
              $anhio_proximo = $f[0] + 1;
              $fecha_vence = date($anhio_proximo."-m-d");
              ?>


                <div class="row">
                    <!-- /widget-header -->
                    <div class='widget-content'>
                      <div id='form_factura_compra'>
                        <div class='row'>
                          <div class="col-md-12">



														<div class="col-md-2">
			                        <div class="form-group has-info" hidden>
			                          <label>Vigencia (Dias)</label>
			                          <input type="text"  class='form-control' value="3"  id="vigencia">
			                        </div>
			                      </div>
                            <div class="col-md-2">
			                        <div class="form-group has-info" hidden>
			                          <label>Dias de entrega</label>
			                          <input type="text"  class='form-control' value="3"  id="dias_entrega">
			                        </div>
			                      </div>

                          </div>
                        </div>
                        <div class='row'>

                          <div class="col-md-12">

                            <div class="col-md-2" hidden>
                              <div class='form-group' id="caja_dias">
                                <label>Días Crédito</label>
                                <input type='text' class='form-control' id='dias_credito' name='dias_credito'>
                              </div>
                            </div>
                            <div id='form_datos_cliente' class="col-md-6">
                              <label>Cliente&nbsp;</label>
                              <input type="text" name="cliente" id="cliente" class="form-control">
                              <input type="hidden" name="id_cliente" id="id_cliente" class="form-control">
                              <label id="text_cliente"></label>
                            </div>
                            <div  class="form-group col-md-2">
                                <label>Tipo de contrato</label>
                                <select name='tipo_contrato' id='tipo_contrato' class='form-control select2'>
                                  <option value='GER'>GENERAL</option>
                                  <option value='SER'>SERVICIO</option>
                                  <option value='SIS'>SISTEMA</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                              <div class='form-group has-info'><label>Buscar Servicio o Sistema</label>
                                <!--<input type="text" id="producto_buscar" name="producto_buscar" class="producto_buscar form-control" placeholder="Ingrese nombre de producto"  data-provide="typeahead">-->
                                <input type="text" id="ss_buscar" name="ss_buscar"  class=" form-control usage typeahead" placeholder="Ingrese descripcion del servicio o sistema" data-provide="typeahead">
                                <input type="hidden" id="servicio_buscar" name="servicio_buscar"  class=" form-control usage typeahead" placeholder="Ingrese descripcion del servicio" data-provide="typeahead">
                                <input type="hidden" id="sistema_buscar" name="sistema_buscar"  class=" form-control usage typeahead" placeholder="Ingrese descripcion del sistema" data-provide="typeahead">
                              </div>
                            </div>
                          </div>
                      </div>
                    </div>
                  </div>
              </div>
              <div class="ibox">
                <div class="row">
                    <!--load datables estructure html-->
                      <input type='hidden' name='porc_iva' id='porc_iva' value='<?php echo $iva; ?>'>
                      <input type='hidden' name='monto_percepcion' id='monto_percepcion' value='<?php echo $monto_percepcion; ?>'>
                      <input type="hidden" id="percepcion" name="percepcion" value="0">

                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-12">
                            <div class='form-group'>
                              <label>Concepto</label>
                              <input type='text' class='form-control' id='concepto' name='concepto'>
                            </div>
                          </div>
                          <div class="form-group col-md-3">
                            <div class='form-group' id="caja_dias">
                              <label>Forma de contrato</label>
                              <select name='forma_contrato' id='forma_contrato' class='form-control select2'>
                                <option value='PF'>PLAZO FIJO</option>
                                <option value='PFR'>RENOVACIÓN</option>
                              </select>
                            </div>
                          </div>
                          <div class="form-group col-md-3">
                            <div class='form-group' id="caja_periodo" hidden>
                              <label>Periodo</label>
                              <select name='periodo_contrato' id='periodo_contrato' class='form-control select2' style="width:100%">
                                <option value='MENSUAL'>MENSUAL</option>
                                <option value='BIMESTRAL'>BIMESTRAL</option>
                                <option value='TRIMESTRAL'>TRIMESTRAL</option>
                                <option value='SEMESTRAL'>SEMESTRAL</option>
                                <option value='ANUAL'>ANUAL</option>
                              </select>
                            </div>
                          </div>
                          <div class='form-group col-md-3 caja_ini' hidden>
                            <div class='form-group'>
                              <label>Fecha Inicio:</label>
                              <input type='text' placeholder='Fecha' class='datepick2 form-control' value='<?php echo ED($fecha_actual); ?>' id='fecha' name='fecha'>
                            </div>
                          </div>
                          <div class='form-group col-md-3 caja_ffin' hidden>
                            <div class='form-group'>
                              <label>Fecha Fin:</label>
                              <input type='text' placeholder='Fecha' class='datepick2 form-control' value='<?php echo ED($fecha_vence); ?>' id='fecha_vence' name='fecha_vence'>
                            </div>
                          </div>

                        </div>
                        <div class="row"  hidden>
                          <div  class="form-group col-md-3">
                            <label>Documento</label>
                            <select name='tipo_doc' id='tipo_doc' class='form-control select2'>
                              <option value='COF'>FACTURA</option>
                              <option value='CCF'>CREDITO FISCAL</option>
                            </select>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <label>Clausula</label>
                            <input type="text" name="clausula" id="clausula" class="form-control">
                            <input type="hidden" name="id_clausula" id="id_clausula" class="form-control">
                            <label id="text_clausula"></label>
                          </div>
                        </div>
                        <div class="row table-wrapper-scroll-y my-custom-scrollbar">
                          <div class="col-md-12">
                            <table class="table bordered">
                              <thead>
                                <th class="col-md-4"><label>Clausula</label></th>
                                <th class="col-md-7"><label>Descripción</label></th>
                                <th class="col-md-1"><label>Acción</label></th>
                              </thead>
                              <tbody id='caja_clausula'>
                                <?php
                                  $sql = _query("SELECT * FROM clausula WHERE fijo = 1");
                                  $lista = "";
                                  while ($row = _fetch_array($sql)) {
                                    $id = $row['id_clausula'];
                                    $descripcion = $row["descripcion"];
                                    $titulo = $row["titulo"];

                                    $lista .= "<tr><td class='td_titulo'><input type='hidden' id='titulo' value='".$titulo."' class='titulo_td'>".$titulo."</td>";
                                    $lista .= "<td class='td_descripcion'><input type='hidden' id='descripcion' class='descripcion_td' value='".$descripcion."'>".$descripcion."</td>";
                                    $lista .= "<td class='borrar text-success'><input type='hidden' id='id_clausula' value='".$id."'><input  id='delprod' type='button' class='btn btn-danger fa pull-right'  value='&#xf1f8;'></td></tr>";
                                  }
                                  echo $lista;
                                ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="wrap-table1001">
                          <div class="table100 ver1 m-b-10">
                            <div class="table100-head">
                              <table id="inventable1">
                                <thead>
                                  <tr class="row100 head">
                                    <th class="success cell100 column10">Id</th>
                                    <th class='success  cell100 column50'>Nombre</th>
                                    <th class='success  cell100 column15'>Prec. C</th>
                                    <!--<th class='success  cell100 column10'>Prec. V</th>-->
                                    <th class='success  cell100 column15'>Cantidad</th>
                                    <th class='success  cell100 column10'>Acci&oacute;n</th>
                                  </tr>
                                </thead>
                              </table>
                            </div>
                            <div class="table100-body js-pscroll">
                              <table id="inventable">
                                <tbody >

                                </tbody>
                              </table>
                            </div>
                            <div class="table101-body">
                              <table>
                                <tbody>
                                  <tr>
                                    <td class="cell100 column100">&nbsp;</td>
                                  </tr>
                                  <tr>
                                    <td class='cell100 column50 text-danger tr_bb'  id='totaltexto'>&nbsp;</td>
                                    <td class='cell100 column10 leftt  text-bluegrey  tr_bb' >CANT.:</td>
                                    <td class='cell100 column5 text-right text-danger  tr_bb' id='totcant'>0</td>
                                    <td class="cell100 column25  leftt text-bluegrey  tr_bb">TOTALES $:</td>
                                    <td class='cell100 column10 text-right text-green  tr_bb' id='total_gravado'>0.00</td>
                                  </tr>
                                  <tr>
                                    <td class="cell100 column25 leftt text-bluegrey">SUMAS (SIN IVA) $:</td>
                                    <td class="cell100 column10 text-right text-green" id='total_gravado_sin_iva'>0.00</td>
                                    <td class="cell100 column10 leftt  text-bluegrey ">IVA  $:</td>
                                    <td class="cell100 column5 text-right text-green " id='total_iva'>0.00</td>
                                    <td class="cell100 column15 leftt text-bluegrey ">SUBTOTAL  $:</td>
                                    <td class="cell100 column10 text-right  text-green" id='total_gravado_iva'>0.00</td>
                                    <td class="cell100 column15 leftt text-bluegrey ">TOTAL $:</td>
                                    <td class="cell100 column10 text-right  text-green"  id='total_general'>0.00</td>
                                  </tr>
                                  <tr hidden>
                                      <td class="cell100 column65">&nbsp;</td>
                                    <td class="cell100 column25 leftt  text-bluegrey ">VENTA EXENTA $:</td>
                                    <td class="cell100 column10  text-right text-green" id='total_exenta'>0.00</td>
                                  </tr>
                                  <tr hidden>
                                      <td class="cell100 column65">&nbsp;</td>
                                    <td class="cell100 column25  leftt  text-bluegrey ">PERCEPCION $:</td>
                                    <td class="cell100 column10 text-right text-green" id='total_percepcion'>0.00</td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <a class="btn btn-danger salir pull-right" style="margin-left:1%;" href="admin_cotizacion.php" id="salir"><i class="fa fa-mail-reply"></i> F4 Salir</a>
                          <button type="button" id="submit1" name="submit1" class="btn btn-primary usage pull-right" style="margin-left:1%;"><i class="fa fa-check"></i> F2 Guardar</button>
                          <div hidden id='caja_fp'><button type="button" id="f_producto" style="margin-left:1%; " name="f_producto" class="btn btn-success usage"><i class="fa fa-eye"></i> Facturar Producto</button></div>
                          <div hidden id='caja_fs'><button type="button" id="f_servicio" style="margin-left:1%; " name="f_servicio" class="btn btn-success usage"><i class="fa fa-eye"></i> Facturar Servicio</button></div>
                          <button type="button" id="plus_servicio" style="margin-left:1%; " name="plus_servicio" class="btn btn-success usage pull-right"><i class="fa fa-plus"></i> Agregar Servicio</button>
                          <a href='add_cliente.php' data-toggle='modal' id="add_cliente" name="add_cliente" class="btn btn-warning usage pull-right" style="" data-target='#addCliente' data-refresh='true'><i class="fa fa-plus"></i> F8 Agregar Cliente</a>
                        </div>
                      </div>
                      <div class='row'>

                      </div>
                      <input type="hidden" name="autosave" id="autosave" value="false-0">
                      <input type="hidden" name="process" id="process" value="insert"><br>
                    <div>
                      <input type='hidden' name='urlprocess' id='urlprocess'value="<?php echo $filename ?> ">
                    </div>
                  </form>
                  <div class='modal fade' id='addCliente' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
      							<div class='modal-dialog'>
      								<div class='modal-content'></div><!-- /.modal-content -->
      							</div><!-- /.modal-dialog -->
      						</div><!-- /.modal -->
              </div>
            </div>
            </div>
        </div>
      </div>

      <?php
      include_once("footer.php");
      echo "<script src='js/plugins/arrowtable/arrow-table.js'></script>";
      //echo "<script src='js/plugins/arrowtable/navigatetable.js'></script>";
      echo "<script src='js/funciones/funciones_contrato.js'></script>";
} //permiso del script
else {
    echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
}
}

function insertar()
{
  //date_default_timezone_set('America/El_Salvador');
  $fecha_movimiento= MD($_POST['fecha_movimiento']);
  $id_cliente=$_POST['id_cliente'];
  $total_venta = $_POST['total_venta'];
  $id_vendedor=$_SESSION["id_usuario"];
  $vigencia=$_POST['vigencia'];
  $cuantos = $_POST['cuantos'];
  $array_json=$_POST['json_arr'];
  $array_json1=$_POST['json_arr1'];
  //  IMPUESTOS
  $total_iva= $_POST['total_iva'];
  $total_sin_iva= $_POST['total_sin_iva'];
  $total_retencion= $_POST['total_retencion'];
  $total_percepcion= $_POST['total_percepcion'];
  $dias_entrega= $_POST['dias_entrega'];
  $tipo_doc= $_POST['tipo_doc'];
  $tipo_contrato= $_POST['tipo_contrato'];
  $fecha_vence= MD($_POST['fecha_vence']);
  $forma_contrato = $_POST["forma_contrato"];
  $concepto = $_POST["concepto"];

  $id_empleado=$_SESSION["id_usuario"];
  $id_sucursal=$_SESSION["id_sucursal"];
  $periodo = $_POST["periodo"];

  $fecha_actual = date('Y-m-d');



  $insertar_fact=false;
  $insertar_fact_dett=true;
  $insertar_clausula=true;
  $insertar_numdoc =false;

  $hora=date("H:i:s");
  $xdatos['typeinfo']='';
  $xdatos['msg']='';
  $xdatos['process']='';

  _begin();
  $sql="SELECT con FROM correlativo WHERE id_sucursal='$id_sucursal'";
  $result= _query($sql);
  $rows=_fetch_array($result);
  $ult=$rows['con']+1;
  $len = strlen($ult);
  $nceros = 7 - $len;
  $numero_doc = "CON_".ceros_izquierda($nceros,$ult);
  $table_numdoc="correlativo";
  $data_numdoc = array(
    'con' => $ult,
  );
  $where_clause_n="WHERE  id_sucursal='$id_sucursal'";
  $insertar_numdoc = _update($table_numdoc, $data_numdoc, $where_clause_n);

  if ($cuantos>0)
  {
    $sql_fact="SELECT * FROM contrato WHERE id_cliente='$id_cliente' AND monto='$total_venta'  AND id_sucursal='$id_sucursal' AND fecha='$fecha_movimiento'";
    $id_contrato = 0;
    $result_fact=_query($sql_fact);
    $nrows_fact=_num_rows($result_fact);
    if ($nrows_fact==0)
    {
      $table_fact= 'contrato';
      $form_data_fact = array(
        'id_cliente' => $id_cliente,
        'fecha' => $fecha_actual,
        'hora' => $hora,
        'numero_doc' => $numero_doc,
        'id_empleado' => $id_empleado,
        'id_sucursal' => $id_sucursal,
        'activo' => 1,
        'monto' => $total_sin_iva,
        'tipo' => $tipo_contrato,
        'cobro' => $tipo_doc,
        'iva' => $total_iva,
        'fecha_vence' => $fecha_vence,
        'forma' => $forma_contrato,
        'fecha_inicio' => $fecha_movimiento,
        'periodo' => $periodo,
        'concepto' => $concepto,
      );
      $insertar_fact = _insert($table_fact, $form_data_fact);
      $id_contrato= _insert_id();
      //echo _error();
    }
    $array = json_decode($array_json, true);
    foreach ($array as $fila)
    {
      if ($fila['precio']>=0 && $fila['cantidad']>0)
      {
        $id_producto=$fila['id_producto'];
        $cantidad=$fila['cantidad'];
        $precio_venta=$fila['precio'];
        $bandera = $fila["bandera"];
        if($bandera == "sistema")
        {
          $tipoprodserv = "SISTEMA";
          $tipo = 0;
          $id_sp = $id_producto;
        }
        $descripcionion_ser = $fila["descripcion_ser"];
        if($bandera == "servicio")
        {
          if($id_producto == "nuevo_servicio")
          {
            $subtotal = round($precio_venta * $cantidad, 2);
            $descripcion = $fila["descripcion_ser"];
            $tab_ser = "servicios";

            $form_dataser = array(
              'descripcion' => $descripcion,
              'estado' => 1,
              'id_categoria' => 1,
              'costo' => $precio_venta,
              'precio' => $precio_venta,
              'tipo_prod_servicio' => "SERVICIO",
            );
            $inser_ser = _insert($tab_ser, $form_dataser);
            $id_sp = _insert_id();
          }
          else
          {
              $id_sp = $id_producto;
          }
          $tipoprodserv = "SERVICIO";
          $tipo = 1;
        }
        $cantidad_real=$cantidad;

				$subtotal = round($precio_venta * $cantidad, 2);
        $table_fact_det= 'contrato_detalle';
        $data_fact_det = array(
          'id_contrato' => $id_contrato,
          'id_ser_sis' => $id_sp,
          'cantidad' => $cantidad_real,
          'precio_venta' => $precio_venta,
          'subtotal' => $subtotal,
          'tipo_ser_sis' => $tipoprodserv,
          'id_sucursal' => $id_sucursal,
        );
        if ($cantidad>0 && $id_contrato > 0)
        {
          $insertar_fact_det = _insert($table_fact_det, $data_fact_det);
          echo _error();
          if(!$insertar_fact_det)
          {
            $insertar_fact_dett = false;
          }
        }

        } // if($fila['cantidad']>0 && $fila['precio']>0){
      } //foreach ($array as $fila){

      $array1 = json_decode($array_json1, true);
      foreach ($array1 as $fila1)
      {
        $id_clausula=$fila1['id_clausula'];
        $titulo=$fila1['titulo'];
        $descripcion=$fila1['descripcion'];

        $table_fact_det= 'contrato_clausulas';
        $data_fact_det = array(
          'id_contrato' => $id_contrato,
          'id_clausula' => $id_clausula,
          'titulo' => $titulo,
          'descripcion' => $descripcion,
        );
        $insertar_fact_det = _insert($table_fact_det, $data_fact_det);
        echo _error();
        if(!$insertar_clausula)
        {
          $insertar_clausula = false;
        }
      }
      //echo "Aqui".$insertar_fact;
      if ($insertar_numdoc  && $insertar_fact && $insertar_fact_dett && $insertar_clausula)
      {
        _commit(); // transaction is committed
        $xdatos['typeinfo']='Success';
        $xdatos['msg']='Contrato Numero:'.$numero_doc.'  Guardado con Exito !';
        $xdatos['id_contrato']=$id_contrato;
        $xdatos['fecha']=$fecha_movimiento;
        $xdatos['forma'] = $forma_contrato;
        $xdatos['process'] = 'insert';
      }
      else
      {
        _rollback(); // transaction rolls back
        $xdatos['typeinfo']='Error';
        $xdatos['msg']='Contrato no pudo ser registrada!'.$insertar_fact."-".$insertar_numdoc."-".$insertar_fact_dett."-".$insertar_clausula._error();
      }
    }
  echo json_encode($xdatos);
}
function consultar_stock()
{
    //$id_pedido = $_REQUEST['id_pedido'];
    $tipo_doc = $_POST["tipo_doc"];
    $id_producto = $_REQUEST['id_producto'];
    $id_usuario=$_SESSION["id_usuario"];
    $id_sucursal=$_SESSION['id_sucursal'];
    $sql_user="select * from usuario where id_usuario='$id_usuario'";

    $sql3="SELECT * FROM productos WHERE id_producto = '$id_producto'";

    $result3=_query($sql3);
    $count3=_num_rows($result3);
    if ($count3>0) {
        $row3=_fetch_array($result3);
        $cp=$row3['ultcosto'];
        $descripcion=$row3['descripcion'];
        $perecedero = $row3["perecedero"];
        $i=0;
        $sql_p=_query("SELECT * FROM precio_producto WHERE id_producto='$id_producto'");
        $select="<select class='sel precios' style='width:100px;' id='precios'>";
        while ($row=_fetch_array($sql_p))
        {
          $id_precio = $row["id_precio"];
          if($tipo_doc == "COF")
          {
            $precio= round($row['total_iva'], 2);
          }
          if($tipo_doc == "CCF")
          {
            $precio= round($row['total'], 2);
          }
          $select.="<option value='".$precio."'>".number_format($precio, 2)."</option>";
        }
        $select.="</select>";

        $xdatos['descripcion'] =$descripcion;
        $xdatos['precio_venta'] = $cp;
        $xdatos['precios'] = $select;
        $xdatos['perecedero'] = $perecedero;
        echo json_encode($xdatos); //Return the JSON Array
    }
}

function total_texto()
{
    $total=$_REQUEST['total'];
    list($entero, $decimal)=explode('.', $total);
    $enteros_txt=num2letras($entero);
    $decimales_txt=num2letras($decimal);

    if ($entero>1) {
        $dolar=" dolares";
    } else {
        $dolar=" dolar";
    }
    $cadena_salida= "<h3 class='text-danger'>Son: <strong>".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.</strong>&nbsp;&nbsp;</h3>";
    echo $cadena_salida;
}
function add_cliente()
{
  $nombre = $_POST["nombre"];
  $direccion=$_POST["direccion"];
  $departamento=$_POST["departamento"];
  $municipio=$_POST["municipio"];
  $categoria=$_POST["categoria"];
  $telefono=$_POST["telefono"];
  $email=$_POST["email"];

  $sql_exis=_query("SELECT id_cliente FROM clientes WHERE nombre ='$nombre'");
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
    'telefono1' => $telefono,
    'email' => $email,
    );
    $insertar = _insert($table,$form_data );
    if($insertar)
    {
        $id_cliente = _insert_id();
       $xdatos['typeinfo']='Success';
       $xdatos['msg']='Registro guardado con exito!';
       $xdatos['process']='insert';
       $xdatos['id_cliente'] = $id_cliente;
    }
    else
    {
       $xdatos['typeinfo']='Error';
       $xdatos['msg']='Registro no pudo ser guardado !'._error();
    }
  }
  echo json_encode($xdatos);
}

function consultar_servicio()
{
  $tipo_doc = $_POST["tipo_doc"];
  $id_servicio = $_POST["id_servicio"];
  $id_usuario = $_SESSION["id_usuario"];

  $id_sucursal=$_SESSION['id_sucursal'];
  $precio=0;
  $categoria="";

  $sql_servicio = _query("SELECT * FROM servicios WHERE id_servicio = '$id_servicio'");
  $num_rows = _num_rows($sql_servicio);
  if($num_rows > 0)
  {
    $row1 = _fetch_array($sql_servicio);
    $descripcion = $row1["descripcion"];
    $categoria=$row1['id_categoria'];
    if($tipo_doc == "COF")
    {
      $precio = $row1["precio_iva"];
    }
    if($tipo_doc == "CCF")
    {
      $precio = $row1["precio"];
    }
  }
  $hoy=date("Y-m-d");

  $xdatos['descripcion']= $descripcion;
  $xdatos['stock']= 1;
  $xdatos['categoria']=$categoria;
  $xdatos['precio'] = $precio;
  echo json_encode($xdatos);
}

function consultar_sistema()
{
  $tipo_doc = $_POST["tipo_doc"];
  $id_sistema = $_POST["id_sistema"];
  $id_usuario = $_SESSION["id_usuario"];

  $id_sucursal=$_SESSION['id_sucursal'];
  $precio=0;
  $categoria="";

  $sql_sistema = _query("SELECT * FROM sistema WHERE id_sistema = '$id_sistema'");
  $num_rows = _num_rows($sql_sistema);
  if($num_rows > 0)
  {
    $row1 = _fetch_array($sql_sistema);
    $nombre = $row1["nombre"];
    if($tipo_doc == "COF")
    {
      $precio = $row1["precio_iva"];
    }
    if($tipo_doc == "CCF")
    {
      $precio = $row1["precio"];
    }
  }
  $hoy=date("Y-m-d");

  $xdatos['descripcion']= $nombre;
  $xdatos['stock']= 1;
  $xdatos['categoria']=1;
  $xdatos['precio'] = $precio;
  echo json_encode($xdatos);
}
function consultar_ss()
{
  $tipo_doc = $_POST["tipo_doc"];
  $id_ss = $_POST["id_ss"];
  $id_usuario = $_SESSION["id_usuario"];
  $tipo = $_POST["tipo"];

  $id_sucursal=$_SESSION['id_sucursal'];
  $precio=0;
  $categoria="";

  if($tipo == "servicio")
  {
    $sql_servicio = _query("SELECT * FROM servicios WHERE id_servicio = '$id_ss'");
    $num_rows = _num_rows($sql_servicio);
    if($num_rows > 0)
    {
      $row1 = _fetch_array($sql_servicio);
      $nombre = $row1["descripcion"];
      $categoria=$row1['id_categoria'];
      if($tipo_doc == "COF")
      {
        $precio = $row1["precio_iva"];
      }
      if($tipo_doc == "CCF")
      {
        $precio = $row1["precio"];
      }
    }
  }
  if($tipo == "sistema")
  {
    $sql_sistema = _query("SELECT * FROM sistema WHERE id_sistema = '$id_ss'");
    $num_rows = _num_rows($sql_sistema);
    if($num_rows > 0)
    {
      $row1 = _fetch_array($sql_sistema);
      $nombre = $row1["nombre"];
      if($tipo_doc == "COF")
      {
        $precio = $row1["precio_iva"];
      }
      if($tipo_doc == "CCF")
      {
        $precio = $row1["precio"];
      }
    }
  }

  $hoy=date("Y-m-d");

  $xdatos['descripcion']= $nombre;
  $xdatos['stock']= 1;
  $xdatos['categoria']=1;
  $xdatos['precio'] = $precio;
  echo json_encode($xdatos);
}
function cuota()
{
  $id_contrato = $_POST["id_contrato"];
  $sql_contrato = _query("SELECT * FROM contrato WHERE id_contrato = '$id_contrato'");
  $cuenta = _num_rows($sql_contrato);
  if($cuenta > 0)
  {
    $row = _fetch_array($sql_contrato);
    $monto = $row["monto"];
    $iva = $row["iva"];
    $periodo = $row["periodo"];
    $numero_doc = $row["numero_doc"];

    $sub_total = round($monto + $iva, 3);
    $fecha = $row["fecha_inicio"];
    list($a,$m,$d) = explode("-", $fecha);
    $mes = $m;
    $anhio = $a;
    if($periodo == 'MENSUAL')
    {
      $m = $m;
    }
    if($periodo == 'BIMESTRAL')
    {
      $m = $m+1;
    }
    if($periodo == 'TRIMESTRAL')
    {
      $m = $m+2;
    }
    if($periodo == 'SEMESTRAL')
    {
      $m = $m+5;
    }
    if($periodo == 'ANUAL')
    {
      $a = $a+1;
    }

    $ultimo_dia = date("d",(mktime(0,0,0,$m+1,1,$a)-1));
    $fecha_vence = $a."-".$m."-".$ultimo_dia;

    $tabla = "cuota_contrato";
    $lista = array(
      'id_contrato' => $id_contrato,
      'monto' =>$sub_total,
      'mes' => $mes,
      'anhio' => $anhio,
      'fecha_vence' => $fecha_vence,
    );
    $insert_cuota = _insert($tabla, $lista);
    if ($insert_cuota)
    {
      _commit(); // transaction is committed
      $xdatos['typeinfo']='Success';
      $xdatos['msg']='Contrato Numero:'.$numero_doc.'  Guardado con Exito !';
      $xdatos['id_contrato']=$id_contrato;
      $xdatos['process'] = 'insert';
    }
    else
    {
      _rollback(); // transaction rolls back
      $xdatos['typeinfo']='Error';
      $xdatos['msg']='Contrato no pudo ser registrada!'._error();
    }
  }
  else
  {
    _rollback(); // transaction rolls back
    $xdatos['typeinfo']='Error';
    $xdatos['msg']='No se ha encontrado el contrato!'._error();
  }
  echo json_encode($xdatos);
}
function clausula()
{
  $id = $_POST["id"];
  $sql = _query("SELECT * FROM clausula WHERE id_clausula = '$id'");
  $row = _fetch_array($sql);
  $descripcion = $row["descripcion"];
  $titulo = $row["titulo"];
  $xdatos['descripcion']= $descripcion;
  $xdatos['titulo']= $titulo;
  echo json_encode($xdatos);
}
//functions to load
if (!isset($_REQUEST['process'])) {
    initial();
}
//else {
if (isset($_REQUEST['process'])) {
    switch ($_REQUEST['process']) {
    case 'insert':
        insertar();
        break;
    case 'consultar_stock':
        consultar_stock();
        break;
    case 'total_texto':
        total_texto();
        break;
    case 'insert_cliente':
        add_cliente();
        break;
    case 'consultar_servicio':
        consultar_servicio();
        break;
    case 'consultar_sistema':
        consultar_sistema();
        break;
    case 'consultar_ss':
        consultar_ss();
        break;
    case 'clausula':
        clausula();
        break;
    case 'unicobro':
        cuota();
        break;
    }

    //}
}
?>
