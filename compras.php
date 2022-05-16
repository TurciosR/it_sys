<?php
include_once "_core.php";
include('num2letras.php');
function initial()
{
    $_PAGE = array();
    $_PAGE ['title'] = 'Compra de Producto';
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
                            <div class='col-md-2'>
                              <div class='form-group'>
                                <label>Fecha:</label>
                                <input type='text' placeholder='Fecha' class='datepick2 form-control' value='<?php echo ED($fecha_actual); ?>' id='fecha2' name='fecha2'></div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <label>Proveedores &nbsp;</label>
                                <?php
                                $nombre_select0="select_proveedores";
                                $idd0=-1;
                                //$style='width:400px';
                                $style='';
                                $select0=crear_select2($nombre_select0, $array0, $idd0, $style);
                                echo $select0; ?>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group"> <label>Tipo Documento &nbsp;</label>
                                <?php
                                $nombre_select0="select_documento";
                                $idd0=-1;
                                //$style='width:0px';
                                $select0=crear_select2($nombre_select0, $array3, $idd0,"");
                                echo $select0; ?>
                              </div>
                            </div>
                            <div class='col-md-2'>
                              <div class='form-group'>
                                <label>Numero de Documento</label>
                                <input type='text' placeholder='Numero de Documento' class='form-control' id='numero_doc2' name='numero_doc2'>
                              </div>
                            </div>
                            <div class="col-lg-2">
                              <div class="form-group has-info">
                                <label>Destino</label>
                                <select class="form-control select2" id="destino" name="destino">
                                  <?php
                                  $sql1 = _query("SELECT * FROM ubicacion WHERE id_sucursal='$id_sucursal' ORDER BY descripcion ASC");

                                  while($row = _fetch_array($sql1))
                                  {
                                    echo "<option value='".$row["id_ubicacion"]."'>".$row["descripcion"]."</option>";
                                  }
                                  ?>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-2" hidden>
                              <div class="form-group"> <label>Cargar Preingreso &nbsp;</label>
                                <?php
                                $nombre_select1="select_pedidos";
                                $idd1=-1;
                                $style='';
                                $select1=crear_select2($nombre_select1, $array4, $idd1, $style);
                                echo $select1; ?>
                              </div>
                            </div>
                            <div class='col-md-2'>
                              <div class='form-group'>
                                <label>Tipo compra</label>
                                <select class="form-control select2" name="tipo_compra" id="tipo_compra">
                                  <option value="0">Contado</option>
                                  <option value="1">Crédito</option>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class='row'>

                          <div class="col-md-12">
                            <div class="col-md-6">
                              <div class='form-group has-info'><label>Buscar Producto o Servicio</label>
                                <input type="text" id="producto_buscar" name="producto_buscar" class="producto_buscar form-control" placeholder="Ingrese nombre de producto"  data-provide="typeahead">
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class='form-group' id="caja_dias" hidden>
                                <label>Días Crédito</label>
                                <input type='text' class='form-control' id='dias_credito' name='dias_credito'>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <a class="btn btn-danger pull-right" style="margin-left:1%; margin-top:20px;" href="admin_compras_fecha.php" id='salir'><i class="fa fa-mail-reply"></i> F4 Salir</a>
                              <button type="button" id="submit1" name="submit1" class="btn btn-primary pull-right usage" style="margin-top:20px;"><i class="fa fa-check"></i> F2 Guardar</button>
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
      echo "<script src='js/funciones/compras.js'></script>";
} //permiso del script
else {
    echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
}
}

function insertar()
{
    //ACA se va insertar a las sig tablas:
    // compras, cxc, detalle_compras, correlativos, stock y kardex ,(otras probables cuenta_proveedor,)
    $cuantos = $_POST['cuantos'];
    $fecha_movimiento= MD($_POST['fecha_movimiento']);
    $numero_doc = $_POST['numero_doc'];
    $id_tipodoc = $_POST['id_tipodoc'];
    //$id_pedido = $_POST['id_pedido'];
    $id_proveedor= $_POST['id_proveedor'];
    $total_compras = $_POST['total_compras'];
    $total_gravado= $_POST['total_gravado'];
    $total_iva= $_POST['total_iva'];
    $total_percepcion= $_POST['total_percepcion'];
    $array_json=$_POST['json_arr'];
    $tipo_compra = $_POST["tipo_compra"];
    $destino = $_POST["destino"];
    $numero_dias = $_POST["numero_dias"];

    $id_usuario=$_SESSION["id_usuario"];
    $id_sucursal= $_SESSION['id_sucursal'];
    $id_empleado = $_SESSION["id_usuario"];
    $numero_dias= $_POST['numero_dias'];

    $insertar1=false;
    $insertar2=false;
    $insertar3=false;
    $fecha_vencimiento=sumar_dias_Ymd($fecha_movimiento, $numero_dias);

    $id_sucursal = $_SESSION["id_sucursal"];
    $sql_num = _query("SELECT ii FROM correlativo WHERE id_sucursal='$id_sucursal'");
    $datos_num = _fetch_array($sql_num);
    $ult = $datos_num["ii"]+1;
    $numero_doc=$ult.'_II';
    _begin();
    $z=1;

    $corr=1;
    $table="correlativo";
    $form_data = array(
      'ii' =>$ult
    );
    $where_clause_c="id_sucursal='".$id_sucursal."'";
    $up_corr=_update($table,$form_data,$where_clause_c);
    if ($up_corr) {
      # code...
    }
    else {
      $corr=0;
    }

    $a = 1 ;

    if ($cuantos>0) {
        _begin();
        $hora=date("H:i:s");
        $fecha_ing=date('Y-m-d');

        $table_fc= 'compra';
        $form_data_fc = array(
            'id_proveedor' => $id_proveedor,
            'alias_tipodoc'=>$id_tipodoc,
            'fecha' => $fecha_ing,
            'fecha_ingreso' => $fecha_movimiento,
            'numero_doc' => $numero_doc,
            'total' => $total_compras,
            'total_percepcion'=>$total_percepcion,
            'id_empleado' => $id_empleado,
            'id_sucursal' => $id_sucursal,
            'iva' => $total_iva,
            'hora' => $hora,
            'dias_credito' => $numero_dias,
            'finalizada' =>1,
            );
          //falta en compras vencimiento a 30, 60, 90 dias y vence iva
          $insertar_fc = _insert($table_fc, $form_data_fc);
          if($insertar_fc)
          {
          }
          else {
            $a = 0;
          }
          $id_fact= _insert_id();
        //Pedidos si hay
        /*$table_ped= 'pedidos';
        $form_data_ped = array(
                'finalizado' =>1,
            );
        $where_clause_ped=" WHERE idtransace='$id_pedido'";
        $insertar_ped = _update($table_ped, $form_data_ped, $where_clause_ped);*/

        //cxp  Insertar a Cuentas por pagar !!!
        /*$table_cxp= 'cxp';
        $sql_cxp="SELECT * FROM $table_cxp WHERE numero_doc='$numero_doc'
   		  AND  fecha='$fecha_movimiento'
   			AND id_proveedor='$id_proveedor'
   			AND alias_tipodoc='$id_tipodoc'
   		  AND id_sucursal='$id_sucursal'";
        $result_cxp=_query($sql_cxp);
        $row_cxp=_fetch_array($result_cxp);
        $nrows_cxp=_num_rows($result_cxp);
        if ($nrows_cxp==0) {
            $form_data_cxp = array(
                'id_proveedor' => $id_proveedor,
                'alias_tipodoc'=>$id_tipodoc,
                'fecha' => $fecha_movimiento,
                'fecha_vence' => $fecha_vencimiento,
                'numero_doc' => $numero_doc,
                'monto' => $total_compras,
                'saldo_pend'=> $total_compras,
                'id_empleado' => $id_usuario,
                'id_sucursal' => $id_sucursal,
                'hora' => $hora,
                'dias_credito' => $numero_dias,
                'id_compras' => $id_fact,
                );
            //falta en compras vencimiento a 30, 60, 90 dias y vence iva
            $insertar_cxp = _insert($table_cxp, $form_data_cxp);
        }*/

        if ($tipo_compra == 1)
        {
          # code...
          $table_cxp= 'cuenta_pagar';
          $fecha_vencimiento=sumar_dias_Ymd($fecha_movimiento, $numero_dias);
          $form_data_cxp = array(
              'id_proveedor' => $id_proveedor,
              'alias_tipodoc'=>$id_tipodoc,
              'fecha' => $fecha_movimiento,
              'fecha_vence' => $fecha_vencimiento,
              'numero_doc' => $numero_doc,
              'monto' => $total_compras,
              'saldo_pend'=> $total_compras,
              'id_empleado' => $id_empleado,
              'id_sucursal' => $id_sucursal,
              'hora' => $hora,
              'dias_credito' => $numero_dias,
              'id_compra' => $id_fact,
              );
          $insertar_cxp = _insert($table_cxp, $form_data_cxp);
        }

        $table='movimiento_producto';
        $form_data = array(
          'id_sucursal' => $id_sucursal,
          'correlativo' => $numero_doc,
          'concepto' => "COMPRA DE PRODUCTO",
          'total' => $total_compras,
          'tipo' => 'ENTRADA',
          'proceso' => 'II',
          'referencia' => $numero_doc,
          'id_empleado' => $id_empleado,
          'fecha' => $fecha_movimiento,
          'hora' => $hora,
          'id_suc_origen' => $id_sucursal,
          'id_suc_destino' => $id_sucursal,
          'id_proveedor' => $id_proveedor,
          'id_compra' => $id_fact,
        );
        $insert_mov =_insert($table,$form_data);
        $id_movimiento=_insert_id();

        $j = 1 ;
        $k = 1 ;
        $l = 1 ;
        $m = 1 ;
        $d = 1 ;
        $b = 1 ;

        $array = json_decode($array_json, true);
        foreach ($array as $fila) {
                $id_producto=$fila['id_producto'];
                $cantidad=$fila['cantidad'];
                $precio_compra=$fila['precio_compra'];
                $fecha_caduca = $fila["vence"];
                $suu = round($cantidad * $precio_compra, 3);
                $sql_su="SELECT id_su, cantidad FROM stock_ubicacion WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal' AND id_ubicacion='$destino' AND id_estante=0 AND id_posicion=0";
                $stock_su=_query($sql_su);
                $nrow_su=_num_rows($stock_su);
                $id_su="";
                /*cantidad de una presentacion por la unidades que tiene*/


                $cantidad=$cantidad;
                if($nrow_su >0)
                {
                  $row_su=_fetch_array($stock_su);
                  $cant_exis = $row_su["cantidad"];
                  $id_su = $row_su["id_su"];
                  $cant_new = $cant_exis + $cantidad;
                  $form_data_su = array(
                    'cantidad' => $cant_new,
                  );
                  $table_su = "stock_ubicacion";
                  $where_su = "id_su='".$id_su."'";
                  $insert_su = _update($table_su, $form_data_su, $where_su);
                }
                else
                {
                  $form_data_su = array(
                    'id_producto' => $id_producto,
                    'id_sucursal' => $id_sucursal,
                    'cantidad' => $cantidad,
                    'id_ubicacion' => $destino,
                  );
                  $table_su = "stock_ubicacion";
                  $insert_su = _insert($table_su, $form_data_su);
                  $id_su=_insert_id();
                }
                if(!$insert_su)
                {
                  $m=0;
                }

                $sql2="SELECT stock FROM stock WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal'";
                $stock2=_query($sql2);
                $row2=_fetch_array($stock2);
                $nrow2=_num_rows($stock2);
                //echo "aqui 2";
                if ($nrow2>0)
                {
                  $existencias=$row2['stock'];
                }
                else
                {
                  $existencias=0;
                }

                $sql_lot = _query("SELECT MAX(numero) AS ultimo FROM lote WHERE id_producto='$id_producto'");
                $datos_lot = _fetch_array($sql_lot);
                $lote = $datos_lot["ultimo"]+1;
                $table1= 'movimiento_producto_detalle';
                $cant_total=$cantidad+$existencias;
                $form_data1 = array(
                  'id_movimiento'=>$id_movimiento,
                  'id_producto' => $id_producto,
                  'cantidad' => $cantidad,
                  'costo' => $precio_compra,
                  //'precio' => $precio_venta,
                  'stock_anterior'=>$existencias,
                  'stock_actual'=>$cant_total,
                  'lote' => $lote,
                  //'id_presentacion' => $id_presentacion,
                );
                $insert_mov_det = _insert($table1,$form_data1);
                if(!$insert_mov_det)
                {
                  $j = 0;
                }
                $table2= 'stock';
                if($nrow2==0)
                {
                  $cant_total=$cantidad;
                  $form_data2 = array(
                    'id_producto' => $id_producto,
                    'stock' => $cant_total,
                    'costo_unitario'=>$precio_compra,
                    //'precio_unitario'=>$precio_venta,
                    'create_date'=>$fecha_movimiento,
                    'update_date'=>$fecha_movimiento,
                    'id_sucursal' => $id_sucursal
                  );
                  $insert_stock = _insert($table2,$form_data2 );
                }
                else
                {
                  $cant_total=$cantidad+$existencias;
                  $form_data2 = array(
                    'id_producto' => $id_producto,
                    'stock' => $cant_total,
                    'costo_unitario'=>round(($precio_compra),2),
                    //'precio_unitario'=>round(($precio_venta/$unidades),2),
                    'update_date'=>$fecha_movimiento,
                    'id_sucursal' => $id_sucursal
                  );
                  $where_clause="WHERE id_producto='$id_producto' and id_sucursal='$id_sucursal'";
                  $insert_stock = _update($table2,$form_data2, $where_clause );
                }

                if(!$insert_stock)
                {
                  $k = 0;
                }
                if ($fecha_caduca!="0000-00-00" && $fecha_caduca!="")
                {
                  $sql_caduca="SELECT * FROM lote WHERE id_producto='$id_producto' and fecha_entrada='$fecha_movimiento' and vencimiento='$fecha_caduca' ";
                  $result_caduca=_query($sql_caduca);
                  $row_caduca=_fetch_array($result_caduca);
                  $nrow_caduca=_num_rows($result_caduca);
                  /*if($nrow_caduca==0){*/
                  $table_perece= 'lote';

                  if($fecha_movimiento>=$fecha_caduca)
                  {
                    $estado='VENCIDO';
                  }
                  else
                  {
                    $estado='VIGENTE';
                  }
                  $form_data_perece = array(
                    'id_producto' => $id_producto,
                    'referencia' => $numero_doc,
                    'numero' => $lote,
                    'fecha_entrada' => $fecha_movimiento,
                    'vencimiento'=>$fecha_caduca,
                    'precio' => $precio_compra,
                    'cantidad' => $cantidad,
                    'estado'=>$estado,
                    'id_sucursal' => $id_sucursal,
                    //'id_presentacion' => $id_presentacion,
                  );
                  $insert_lote = _insert($table_perece,$form_data_perece );
                }
                else
                {
                  $sql_caduca="SELECT * FROM lote WHERE id_producto='$id_producto' AND fecha_entrada='$fecha_movimiento'";
                  $result_caduca=_query($sql_caduca);
                  $row_caduca=_fetch_array($result_caduca);
                  $nrow_caduca=_num_rows($result_caduca);
                  $table_perece= 'lote';
                  $estado='VIGENTE';

                  $form_data_perece = array(
                    'id_producto' => $id_producto,
                    'referencia' => $numero_doc,
                    'numero' => $lote,
                    'fecha_entrada' => $fecha_movimiento,
                    'vencimiento'=>$fecha_caduca,
                    'precio' => $precio_compra,
                    'cantidad' => $cantidad,
                    'estado'=>$estado,
                    'id_sucursal' => $id_sucursal,
                    //'id_presentacion' => $id_presentacion,
                  );
                  $insert_lote = _insert($table_perece,$form_data_perece );
                }
                if(!$insert_lote)
                {
                  $l = 0;
                }

                $table="movimiento_stock_ubicacion";
                $form_data = array(
                  'id_producto' => $id_producto,
                  'id_origen' => 0,
                  'id_destino'=> $id_su,
                  'cantidad' => $cantidad,
                  'fecha' => $fecha_movimiento,
                  'hora' => $hora,
                  'anulada' => 0,
                  'afecta' => 0,
                  'id_sucursal' => $id_sucursal,
                  //'id_presentacion'=> $id_presentacion,
                  'id_mov_prod' => $id_movimiento,
                );

                $insert_mss =_insert($table,$form_data);

                if ($insert_mss) {
                  # code...
                }
                else {
                  # code...
                  $z=0;
                }


                //detalle de compras
                $table_dc= 'detalle_compras';
                $form_data_dc = array(
                    'id_compras' => $id_fact,
                    'id_producto' => $id_producto,
                    // 'numero_doc' => $numero_doc,
                    'cantidad' => $cantidad,
                    'ultcosto' => $precio_compra,
                    //'descuento' => $descto, //es el porcentaje descto sin dividir entre 100
                    'subtotal' => $suu,
                    //'exento' => $exento,
                );
                $insertar_dc = _insert($table_dc, $form_data_dc);

                $sql_pro = _query("SELECT ultcosto FROM productos WHERE id_producto = '$id_producto'");
                $rowp = _fetch_array($sql_pro);
                $costo_anterior = $rowp['ultcosto'];
                if($precio_compra != $costo_anterior)
                {
                  $tab_pp = "productos";
                  $arrpp = array(
                    'ultcosto' => $precio_compra,
                  );
                  $wpa = "id_producto='".$id_producto."'";
                  $upp = _update($tab_pp, $arrpp, $wpa);
                  if($upp)
                  {
                    $sql_por = _query("SELECT * FROM precio_producto WHERE id_producto = '$id_producto'");
                    $cuenta_por = _num_rows($sql_por);
                    if($cuenta_por > 0)
                    {
                      $lista_por = "";
                      $cn = 0;
                      while ($rowpor = _fetch_array($sql_por))
                      {
                        $porcentaje = $rowpor["porcentaje"];
                        $lista_por .= $porcentaje.",";
                        $cn += 1;
                      }
                      //echo $lista_por;
                      $tabla_pre = "precio_producto";
                      $wwp = "id_producto='".$id_producto."'";
                      $delete = _delete($tabla_pre, $wwp);
                      if($delete)
                      {
                        for ($i=0; $i < $cn; $i++)
                        {
                          $expp = explode("," , $lista_por);
                          $porcentaje = $expp[$i];
                          $resultado = round($precio_compra* ($porcentaje / 100) , 2);
                          $resultado1 = $precio_compra + $resultado;
                          $for_lis = array(
                            'porcentaje' => $porcentaje,
                            'ganancia' => $resultado,
                            'costo' => $precio_compra,
                            'id_producto' => $id_producto,
                            'total' => $resultado1,
                          );
                          $inser = _insert($tabla_pre, $for_lis);
                        }
                      }
                    }
                  }

                }
        } // FOREACH
    }//if $cuantos>0

//  if ($insertar1 && $insertar2 && $insertar3){
if($insert_mov &&$insertar_fc && $insertar_dc &&$corr &&$z && $j && $k && $l && $m)
{
  _commit();
  $xdatos['typeinfo']='Success';
  $xdatos['msg']='Registro ingresado con exito!';
  $xdatos['datos'] = $z." ".$j." ".$k." ".$l." ".$m;
}
else
{
  _rollback();
  $xdatos['typeinfo']='Error';
  $xdatos['msg']='Registro de no pudo ser ingresado!';
}
echo json_encode($xdatos);
}
function consultar_stock()
{
    //$id_pedido = $_REQUEST['id_pedido'];
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
        $select="<select class='sel' style='width:100px;'>";
        while ($row=_fetch_array($sql_p))
        {
          $id_precio = $row["id_precio"];
          $precio=$row['total'];
          $select.="<option value='".$id_precio."'>".$precio."</option>";
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
    }

    //}
}
?>
