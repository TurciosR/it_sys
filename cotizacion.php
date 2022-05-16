<?php
include_once "_core.php";
include('num2letras.php');
function initial()
{
    $_PAGE = array();
    $_PAGE ['title'] = 'Cotizacion';
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
                        $fecha_actual=date("Y-m-d"); ?>
                <div class="row">
                    <!-- /widget-header -->
                    <div class='widget-content'>
                      <div id='form_factura_compra'>
                        <div class='row'>
                          <div class="col-md-12">
														<div id='form_datos_cliente' class="form-group col-md-4">
			                          <label>Cliente&nbsp;</label>
			                          <input type="text" name="cliente" id="cliente" class="form-control">
			                          <input type="hidden" name="id_cliente" id="id_cliente" class="form-control">
			  												<label id="text_cliente"></label>
			                      </div>
                            <div  class="form-group col-md-2">
                                <label>Tipo Impresi&oacuten</label>
                                <select name='tipo_doc' id='tipo_doc' class='form-control'>
                                  <option value='COF'>FACTURA</option>
                                  <option value='CCF'>CREDITO FISCAL</option>
                                </select>
                            </div>
                            <div class='col-md-2'>
                              <div class='form-group'>
                                <label>Fecha:</label>
                                <input type='text' placeholder='Fecha' class='datepick2 form-control' value='<?php echo ED($fecha_actual); ?>' id='fecha' name='fecha'></div>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Forma de pago</label>
                                <select name='con_pago' id='con_pago' class='form-control'>
                                  <option value='CON' selected>Contado</option>
                                  <option value='CRE' >Credito</option>
                                  <option value='CHE' >Cheque</option>
                                </select>
                            </div>
														<div class="col-md-1">
			                        <div class="form-group has-info">
			                          <label>Vigencia </label>
			                          <input type="text"  class='form-control' value="3"  id="vigencia">
			                        </div>
			                      </div>
                            <div class="col-md-1">
			                        <div class="form-group has-info">
			                          <label>D/entrega</label>
			                          <input type="text"  class='form-control' value="3"  id="dias_entrega">
			                        </div>
			                      </div>

                          </div>
                        </div>
                        <div class='row'>

                          <div class="col-md-12">
                            <div class="col-md-4">
                              <div class='form-group has-info'><label>Buscar Producto o Servicio</label>
                                <input type="text" id="producto_buscar" name="producto_buscar" class="producto_buscar form-control" placeholder="Ingrese nombre de producto"  data-provide="typeahead">
                                <input type="hidden" id="servicio_buscar" name="servicio_buscar"  class=" form-control usage typeahead" placeholder="Ingrese Descripcion del servicio" data-provide="typeahead">
                              </div>
                            </div>
                            <div class="col-md-1">
                              <div class='form-group' id="caja_dias" hidden>
                                <label>Días Crédito</label>
                                <input type='text' class='form-control' id='dias_credito' name='dias_credito'>
                              </div>
                            </div>
                            <div class="col-md-7">
                              <a class="btn btn-danger pull-right salir" style="margin-left:1%; margin-top:20px;" href="admin_cotizacion.php" id="salir"><i class="fa fa-mail-reply"></i> F4 Salir</a>
                              <button type="button" id="submit1" name="submit1" class="btn btn-primary pull-right usage" style="margin-left:1%; margin-top:20px;"><i class="fa fa-check"></i> F2 Guardar</button>
                              <div hidden id='caja_fp'><button type="button" id="f_producto" style="margin-left:1%; margin-top:20px" name="f_producto" class="btn btn-success pull-right usage"><i class="fa fa-eye"></i> Facturar Producto</button></div>
                              <div id='caja_fs'><button type="button" id="f_servicio" style="margin-left:1%; margin-top:20px" name="f_servicio" class="btn btn-success pull-right usage"><i class="fa fa-eye"></i> Facturar Servicio</button></div>
                              <button type="button" id="plus_servicio" style="margin-left:1%; margin-top:20px" name="plus_servicio" class="btn btn-danger pull-right usage"><i class="fa fa-plus"></i> Agregar Servicio</button>
                              <a href='add_cliente.php' data-toggle='modal' id="add_cliente" name="add_cliente" class="btn btn-warning pull-right usage" style="margin-top:20px;" data-target='#addCliente' data-refresh='true'><i class="fa fa-plus"></i> F8 Agregar Cliente</a>
                            </div>
                          </div>
                      </div>
                    </div>
                  </div>
              </div>
              <div class="ibox">
                <div class="row">
                  <div class="ibox-content">
                    <!--load datables estructure html-->
                    <section>
                      <input type='hidden' name='porc_iva' id='porc_iva' value='<?php echo $iva; ?>'>
                      <input type='hidden' name='monto_percepcion' id='monto_percepcion' value='<?php echo $monto_percepcion; ?>'>
                      <input type="hidden" id="percepcion" name="percepcion" value="0">
                      <div class="col-md-12">
                        <div class="wrap-table1001">
                          <div class="table100 ver1 m-b-10">
                            <div class="table100-head">
                              <table id="inventable1">
                                <thead>
                                  <tr class="row100 head">
                                    <th class="success cell100 column10">Id</th>
                                    <th class='success  cell100 column55'>Nombre</th>
                                    <th class='success  cell100 column10'>Prec. C</th>
                                    <!--<th class='success  cell100 column10'>Prec. V</th>-->
                                    <th class='success  cell100 column10'>Cantidad</th>
                                    <th class='success  cell100 column10'>Vence</th>
                                    <th class='success  cell100 column5'>Acci&oacute;n</th>
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
                                    <td class='cell100 column45 text-bluegrey tr_bb'  id='totaltexto'>&nbsp;</td>
                                    <td class='cell100 column10 leftt  text-bluegrey  tr_bb '></td>
                                    <td class='cell100 column5 text-right text-danger  tr_bb' id='items'></td>
                                    <td class='cell100 column10 leftt  text-bluegrey  tr_bb' >CANT. PROD:</td>
                                    <td class='cell100 column5 text-right text-danger  tr_bb' id='totcant'>0</td>
                                    <td class="cell100 column15  leftt text-bluegrey  tr_bb">TOTALES $:</td>
                                    <td class='cell100 column10 text-right text-green  tr_bb' id='total_gravado'>0.00</td>
                                  </tr>
                                  <tr>
                                    <td class="cell100 column75">&nbsp;</td>
                                    <td class="cell100 column15 leftt text-bluegrey">SUMAS (SIN IVA) $:</td>
                                    <td class="cell100 column10 text-right text-green" id='total_gravado_sin_iva'>0.00</td>
                                  </tr>
                                  <tr>
                                    <td class="cell100 column75">&nbsp;</td>
                                    <td class="cell100 column15  leftt  text-bluegrey ">IVA  $:</td>
                                    <td class="cell100 column10 text-right text-green " id='total_iva'>0.00</td>
                                  </tr>
                                  <tr>
                                      <td class="cell100 column75">&nbsp;</td>
                                    <td class="cell100 column15  leftt text-bluegrey ">SUBTOTAL  $:</td>
                                    <td class="cell100 column10 text-right  text-green" id='total_gravado_iva'>0.00</td>
                                  </tr>
                                  <tr>
                                      <td class="cell100 column75">&nbsp;</td>
                                    <td class="cell100 column15 leftt  text-bluegrey ">VENTA EXENTA $:</td>
                                    <td class="cell100 column10  text-right text-green" id='total_exenta'>0.00</td>
                                  </tr>
                                  <tr>
                                      <td class="cell100 column75">&nbsp;</td>
                                    <td class="cell100 column15  leftt  text-bluegrey ">PERCEPCION $:</td>
                                    <td class="cell100 column10 text-right text-green" id='total_percepcion'>0.00</td>
                                  </tr>
                                  <tr>
                                      <td class="cell100 column75">&nbsp;</td>
                                    <td class="cell100 column15 leftt text-bluegrey ">TOTAL $:</td>
                                    <td class="cell100 column10 text-right  text-green"  id='total_general'>0.00</td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                        <input type="hidden" name="autosave" id="autosave" value="false-0">
                        </section>
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
      </div>

      <?php
      include_once("footer.php");
      echo "<script src='js/plugins/arrowtable/arrow-table.js'></script>";
      //echo "<script src='js/plugins/arrowtable/navigatetable.js'></script>";
      echo "<script src='js/funciones/cotizacion.js'></script>";
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
  //  IMPUESTOS
  $total_iva= $_POST['total_iva'];
  $total_retencion= $_POST['total_retencion'];
  $total_percepcion= $_POST['total_percepcion'];
  $dias_entrega= $_POST['dias_entrega'];
  $tipo_doc= $_POST['tipo_doc'];
  $tipo_pago= $_POST['tipo_pago'];
  $dias_credito= $_POST['dias_credito'];

  $id_empleado=$_SESSION["id_usuario"];
  $id_sucursal=$_SESSION["id_sucursal"];

  $fecha_actual = date('Y-m-d');



  $insertar_fact=false;
  $insertar_fact_dett=true;
  $insertar_numdoc =false;

  $hora=date("H:i:s");
  $xdatos['typeinfo']='';
  $xdatos['msg']='';
  $xdatos['process']='';

  _begin();
  $sql="SELECT cot FROM correlativo WHERE id_sucursal='$id_sucursal'";
  $result= _query($sql);
  $rows=_fetch_array($result);
  $ult=$rows['cot']+1;
  $len = strlen($ult);
  $nceros = 7 - $len;
  $numero_doc = "COT_".ceros_izquierda($nceros,$ult);
  $table_numdoc="correlativo";
  $data_numdoc = array(
    'cot' => $ult,
  );
  $where_clause_n="WHERE  id_sucursal='$id_sucursal'";
  $insertar_numdoc = _update($table_numdoc, $data_numdoc, $where_clause_n);

  if ($cuantos>0)
  {
    $sql_fact="SELECT * FROM cotizacion WHERE id_cliente='$id_cliente' AND total='$total_venta'  AND id_sucursal='$id_sucursal' AND fecha='$fecha_movimiento'";
    $id_fact = 0;
    $result_fact=_query($sql_fact);
    $nrows_fact=_num_rows($result_fact);
    if ($nrows_fact==0)
    {
      $table_fact= 'cotizacion';
      $form_data_fact = array(
        'id_cliente' => $id_cliente,
        'fecha' => $fecha_movimiento,
        'hora' => $hora,
        'vigencia' => $vigencia,
        'numero_doc' => $numero_doc,
        'total' => $total_venta,
        'impresa' => 0,
        'id_empleado' => $id_empleado,
        'id_vendedor'=>$id_vendedor,
        'id_sucursal' => $id_sucursal,
        'entrega' => $dias_entrega,
        'tipo_doc' => $tipo_doc,
        'pago' => $tipo_pago,
        'dias_credito' => $dias_credito,
      );
      $insertar_fact = _insert($table_fact, $form_data_fact);
      $id_fact= _insert_id();
    }
    $array = json_decode($array_json, true);
    foreach ($array as $fila)
    {
      if ($fila['precio']>=0 && $fila['cantidad']>0)
      {
        $id_producto=$fila['id_producto'];
        $cantidad=$fila['cantidad'];
        $precio_venta=$fila['precio'];
        $vencimiento = $fila["vencimiento"];
        $bandera = $fila["bandera"];
        if($bandera == "producto")
        {
          $tipoprodserv = "PRODUCTO";
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
        $table_fact_det= 'cotizacion_detalle';
        $data_fact_det = array(
          'id_cotizacion' => $id_fact,
          'id_prod_serv' => $id_sp,
          'cantidad' => $cantidad_real,
          'precio_venta' => $precio_venta,
          'subtotal' => $subtotal,
          'tipo_prod_serv' => $tipoprodserv,
          'id_sucursal' => $id_sucursal,
          'vencimiento' => $vencimiento,
        );
        if ($cantidad>0 && $id_fact > 0)
        {
          $insertar_fact_det = _insert($table_fact_det, $data_fact_det);
          if(!$insertar_fact_det)
          {
            $insertar_fact_dett = false;
          }
        }
      } // if($fila['cantidad']>0 && $fila['precio']>0){
      } //foreach ($array as $fila){
      if ($insertar_numdoc  && $insertar_fact && $insertar_fact_dett)
      {
        _commit(); // transaction is committed
        $xdatos['typeinfo']='Success';
        $xdatos['msg']='Cotización Numero: <strong>'.$numero_doc.'</strong>  Guardado con Exito !';
        $xdatos['factura']=$id_fact;
        $xdatos['process'] = 'insert';
      }
      else
      {
        _rollback(); // transaction rolls back
        $xdatos['typeinfo']='Error';
        $xdatos['msg']='Cotización no pudo ser registrada!'.$insertar_fact."-".$insertar_numdoc."-".$insertar_fact_dett._error();
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
            $precio= round($row['total_iva'], 3);
          }
          if($tipo_doc == "CCF")
          {
            $precio= $row['total'];
          }
          $select.="<option value='".$precio."'>".$precio."</option>";
        }
        $select.="</select>";

        $xdatos['descripcion'] =$descripcion;
        $xdatos['precio_venta'] = $cp;
        $xdatos['precios'] = $select;
        $xdatos['perecedero'] = $perecedero;
        echo json_encode($xdatos); //Return the JSON Array
    }
}
function consultar_stockO()
{
    $id_producto = $_REQUEST['id_producto'];
    $id_usuario=$_SESSION["id_usuario"];
    $id_sucursal=$_SESSION['id_sucursal'];
    $sql_user="select * from usuario where id_usuario='$id_usuario'";

    $sql2="select * from stock where id_producto='$id_producto' and id_sucursal ='$id_sucursal'";
    $stock2=_query($sql2);
    $row2=_fetch_array($stock2);
    $nrow2=_num_rows($stock2);
    $existencias=$row2['existencias'];


    $sql3="select p.*,c.nombre from productos AS p
		JOIN colores AS c ON (p.id_color=c.id_color)
		where id_producto='$id_producto'
		";
    $result3=_query($sql3);
    $count3=_num_rows($result3);
    if ($count3>0) {
        $row3=_fetch_array($result3);
        $cp=$row3['ultcosto'];
        $pv_base=$row3['precio1'];
        $talla=$row3['talla'];
        $color=$row3['nombre'];
        $exento=$row3['exento'];
        $descripcion=$row3['descripcion'];

        $xdatos['descrip'] =$descripcion;
        $xdatos['costo_prom'] = $cp;
        $xdatos['pv_base'] = $pv_base;
        $xdatos['existencias'] = $existencias;
        $xdatos['color'] = $color;
        $xdatos['talla'] = $talla;
        $xdatos['exento'] = $exento;
        echo json_encode($xdatos); //Return the JSON Array
    }
}
function buscarBarcode()
{
    $query = trim($_POST['id_producto']);
    $sql0="SELECT id_producto as id, descripcion, barcode, estilo FROM productos  WHERE barcode='$query'";
    $result = _query($sql0);
    $numrows= _num_rows($result);

    $array_prod = array();
    $array_prod="";
    while ($row = _fetch_array($result)) {
        $barcod=" [".$row['barcode']."] ";
        $array_prod =$row['id']."|".$barcod.$row['descripcion']."|1";
    }
    $xdatos['array_prod']=$array_prod;
    echo json_encode($xdatos); //Return the JSON Array
}
function buscarpedido()
{
    $id_pedido= trim($_POST['id_pedido']);
    $sql0="SELECT dp.id_producto, dp.cantidad
	FROM pedidos AS p
	JOIN detalle_pedidos AS dp ON(p.idtransace=dp.idtransace)
	WHERE p.idtransace='$id_pedido'";
    $result = _query($sql0);
    $array_prod = array();
    $numrows= _num_rows($result);
    for ($i=0;$i<$numrows;$i++) {
        $row = _fetch_array($result);
        $id_producto =$row['id_producto'];
        $cantidad =$row['cantidad'];
        $array_prod[] = array(
         'id_producto' => $row['id_producto'],
         'cantidad' =>  $row['cantidad'],
  );
    }
    //$xdatos['array_prod']=$array_prod;
    echo json_encode($array_prod); //Return the JSON Array
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
function datos_proveedores()
{
    $id_proveedor = $_POST['id_proveedor'];
    $sql0="SELECT percibe, retiene, retiene10 FROM proveedores  WHERE id_proveedor='$id_proveedor'";
    $result = _query($sql0);
    $numrows= _num_rows($result);
    $row = _fetch_array($result);
    $retiene1=$row['retiene'];
    $retiene10=$row['retiene10'];
    $percibe=$row['percibe'];
    if ($percibe==1) {
        $percepcion=round(1/100, 2);
    } else {
        $percepcion=0;
    }

    if ($retiene1==1) {
        $retencion1=round(1/100, 2);
    } else {
        $retencion1=0;
    }

    if ($retiene10==1) {
        $retencion10=round(10/100, 2);
    } else {
        $retencion10=0;
    }

    $xdatos['retencion1'] = $retencion1;
    $xdatos['retencion10'] = $retencion10;
    $xdatos['percepcion'] = $percepcion;
    echo json_encode($xdatos); //Return the JSON Array
}
function traerdatos()
{
    $keywords = $_POST['keywords'];
    $estilo = $_POST['estilo'];
    $talla= $_POST['talla'];
    $id_color= $_POST['id_color'];
    $barcode= $_POST['barcode'];
    $limite= $_POST['limite'];
    //if(strlen(trim($keywords))>=0) {
    $sqlJoined="SELECT pr.id_producto,pr.descripcion, pr.precio1, pr.costopro, pr.talla,
		 pr.exento, pr.estilo, pr.barcode, co.id_color,co.nombre
		 FROM productos AS pr, colores AS co
		";
    $sqlParcial=get_sql($keywords, $id_color, $estilo, $talla, $barcode, $limite);
    $sql_final= $sqlJoined." ".$sqlParcial." ";
    $query = _query($sql_final);

    $num_rows = _num_rows($query);
    $filas=0;
    if ($num_rows > 0) {
        while ($row = _fetch_array($query)) {
            $id_producto = $row['id_producto'];
            $descripcion=$row["descripcion"];
            $estilo = $row['estilo'];
            $exento = $row['exento'];
            $cp = $row['costopro'];
            $precio = $row['precio1'];
            $talla = $row['talla'];
            $id_color2=$row['id_color'];
            $nombre = $row['nombre'];
            $barcode = $row['barcode'];
            //<i class="fa fa-check"></i>
            $btnSelect='<input type="button" id="btnSelect" class="btn btn-primary fa" value="&#xf00c;">'; ?>
        <tr class='tr1' tabindex="<?php echo $filas; ?>">
          <td class='col12 td1'><input type='hidden' id='exento' name='exento' value='<?php echo $exento; ?>'>
            <h5><?php echo $id_producto; ?></h5></td>
          <td class='col13 td1'>
            <h5><?php echo $descripcion; ?></h5></td>
          <td class='col12 td1'>
            <h5><?php echo $cp; ?></h5></td>
          <td class='col12 td1'>
            <h5 class='text-success'><?php echo $estilo; ?></h5></td>
          <td class='col12 td1'>
            <h5 class='text-success'><?php echo $talla; ?></h5></td>
          <td class='col12 td1'>
            <h5 class='text-success'><?php echo $nombre; ?></h5></td>
        </tr>

        <?php
                    $filas+=1;
        }
    }
    echo '<input type="hidden" id="cuantos_reg"  value="'.$num_rows.'">';
}
function get_sql($keywords, $id_color, $estilo, $talla, $barcode, $limite)
{
    $andSQL='';
    $whereSQL="  WHERE pr.id_color=co.id_color ";

    $keywords=trim($keywords);
    //$andSQL.= " AND co.id_color='$id_color'";

    if (!empty($barcode)) {
        $andSQL.= " AND  pr.barcode LIKE '{$barcode}%'";
    } else {
        if (!empty($keywords)) {
            $andSQL.= "AND  pr.descripcion LIKE '%".$keywords."%'";
            if (!empty($estilo)) {
                $andSQL.= " AND pr.estilo LIKE '{$estilo}%' ";
            }
            if (!empty($talla)) {
                $andSQL.= " AND pr.talla LIKE '%{$talla}%'";
            }
            if ($id_color!=-1) {
                $andSQL.= " AND co.id_color='$id_color'";
            }
        }

        if (empty($keywords)  && !empty($estilo)) {
            $andSQL.= "AND  pr.estilo LIKE '".$estilo."%' ";
            if (!empty($talla)) {
                $andSQL.= " AND pr.talla LIKE '%".$talla."%' ";
            }
            if ($id_color!=-1) {
                $andSQL.= " AND co.id_color='$id_color'";
            }
        }
        if (empty($keywords)  && empty($estilo) && !empty($talla)) {
            $andSQL.= "AND pr.talla LIKE '%".$talla."%' ";
            if ($id_color!=-1) {
                $andSQL.= " AND co.id_color='$id_color'";
            }
        }
        if (empty($keywords)  && empty($estilo) && empty($talla) && ($id_color!=-1)) {
            $limite=1000;
            $andSQL.= " AND co.id_color='".$id_color."'";
        }
    }

    $orderBy=" ";
    $limitSQL=" LIMIT ".$limite;
    $orderBy=" ORDER BY pr.id_producto,pr.descripcion, pr.barcode,pr.estilo,pr.talla,co.id_color ";

    $sql_parcial=$whereSQL.$andSQL.$orderBy.$limitSQL;
    return $sql_parcial;
}

function genera_select()
{
    $id_proveedor=$_POST['id'];
      $id_sucursal= $_SESSION['id_sucursal'];

    $sql="SELECT pedidos.idtransace,pedidos.pares,pedidos.monto FROM pedidos WHERE  finalizado=0 AND anulado=0 AND verificado=1 AND id_proveedor='$id_proveedor' AND id_sucursal='$id_sucursal'";
    $result=_query($sql);
    $count=_num_rows($result);
    if ($count>0) {
        echo '<option value="" selected>Seleccione</option>';
        for ($y=0;$y<$count;$y++) {
            $row=_fetch_array($result);
            $id1=$row['idtransace'];
            $description="Pares: ".$row['pares']."| Monto: ".$row['monto'];
            echo '<option value="'.$id1.'">'.$description.'</option>';
        }
    } else {
        echo '<option value="" selected>NO ASIGNADO</option>';
    }
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
    case 'consultar_stock2':
        consultar_stockO();
        break;
    case 'buscarBarcode':
        buscarBarcode();
        break;
    case 'total_texto':
        total_texto();
        break;
    case 'datos_proveedores':
        datos_proveedores();
        break;
    case 'traerdatos':
        traerdatos();
        break;
    case 'buscarpedido':
        buscarpedido();
        break;
    case 'genera_select':
        genera_select();
        break;
    case 'insert_cliente':
        add_cliente();
        break;
    case 'consultar_servicio':
        consultar_servicio();
        break;
    }

    //}
}
?>
