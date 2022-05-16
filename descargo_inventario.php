<?php
include_once "_core.php";
include('num2letras.php');
function initial()
{
    $_PAGE = array();
    $_PAGE ['title'] = 'Descargo Inventario';
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
														<div class='col-md-4'>
                              <div class='form-group'>
                                <label>Concepto</label>
                                <input type='text' class='form-control' id='concepto_des' name='concepto_des' value="DESCARGO DE INVENTARIO">
                              </div>
                            </div>
														<div class="col-lg-4">
						                  <div class="form-group has-info">
						                    <label>Tipo</label>
						                    <select class="form-control select2" id="tipo" name="tipo">
						                      <option value="VENCIMIENTO">VENCIMIENTO</option>
						                      <option value="DESCARTE">DESCARTE</option>
						                      <option value="PRODUCTO DAÑADO">PRODUCTO DAÑADO</option>
						                      <option value="CONSUMO INTERNO">CONSUMO INTERNO</option>
						                    </select>
						                  </div>
						                </div>
                            <div class='col-md-2'>
                              <div class='form-group'>
                                <label>Fecha:</label>
                                <input type='text' placeholder='Fecha' class='datepick2 form-control' value='<?php echo ED($fecha_actual); ?>' id='fecha2' name='fecha2'></div>
                            </div>
                            <div class="col-md-2" hidden>
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
                            <div class="col-md-2" hidden>
                              <div class="form-group"> <label>Tipo Documento &nbsp;</label>
                                <?php
                                $nombre_select0="select_documento";
                                $idd0=-1;
                                //$style='width:0px';
                                $select0=crear_select2($nombre_select0, $array3, $idd0,"");
                                echo $select0; ?>
                              </div>
                            </div>
                            <div class='col-md-2' hidden>
                              <div class='form-group'>
                                <label>Numero de Documento</label>
                                <input type='text' placeholder='Numero de Documento' class='form-control' id='numero_doc2' name='numero_doc2'>
                              </div>
                            </div>
                            <div class="col-lg-2">
                              <div class="form-group has-info">
                                <label>Origen</label>
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
                            <div class='col-md-2' hidden>
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
                              <a class="btn btn-danger pull-right" style="margin-left:1%; margin-top:20px;" href="admin_movimiento.php" id='salir'><i class="fa fa-mail-reply"></i> F4 Salir</a>
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
                                    <th class='success  cell100 column10'>Precio. C</th>
                                    <th class='success  cell100 column10'>Existencia</th>
                                    <!--<th class='success  cell100 column10'>Prec. V</th>-->
                                    <th class='success  cell100 column10'>Cantidad</th>
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
                                  <tr hidden>
                                    <td class="cell100 column75">&nbsp;</td>
                                    <td class="cell100 column15 leftt text-bluegrey">SUMAS (SIN IVA) $:</td>
                                    <td class="cell100 column10 text-right text-green" id='total_gravado_sin_iva'>0.00</td>
                                  </tr>
                                  <tr hidden>
                                    <td class="cell100 column75">&nbsp;</td>
                                    <td class="cell100 column15  leftt  text-bluegrey ">IVA  $:</td>
                                    <td class="cell100 column10 text-right text-green " id='total_iva'>0.00</td>
                                  </tr>
                                  <tr hidden>
                                      <td class="cell100 column75">&nbsp;</td>
                                    <td class="cell100 column15  leftt text-bluegrey ">SUBTOTAL  $:</td>
                                    <td class="cell100 column10 text-right  text-green" id='total_gravado_iva'>0.00</td>
                                  </tr>
                                  <tr hidden>
                                      <td class="cell100 column75">&nbsp;</td>
                                    <td class="cell100 column15 leftt  text-bluegrey ">VENTA EXENTA $:</td>
                                    <td class="cell100 column10  text-right text-green" id='total_exenta'>0.00</td>
                                  </tr>
                                  <tr hidden>
                                      <td class="cell100 column75">&nbsp;</td>
                                    <td class="cell100 column15  leftt  text-bluegrey ">PERCEPCION $:</td>
                                    <td class="cell100 column10 text-right text-green" id='total_percepcion'>0.00</td>
                                  </tr>
                                  <tr hidden>
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
      echo "<script src='js/funciones/funciones_descargo.js'></script>";
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
    //$numero_doc = $_POST['numero_doc'];
    $id_tipodoc = $_POST['id_tipodoc'];
    //$id_pedido = $_POST['id_pedido'];
    $id_proveedor= $_POST['id_proveedor'];
    $total_compras = $_POST['total_compras'];
    $total_gravado= $_POST['total_gravado'];
    $total_iva= $_POST['total_iva'];
    $total_percepcion= $_POST['total_percepcion'];
    $array_json=$_POST['json_arr'];
    $tipo_compra = $_POST["tipo_compra"];
    $numero_dias = $_POST["numero_dias"];

    $id_usuario=$_SESSION["id_usuario"];
    $id_sucursal= $_SESSION['id_sucursal'];
    $id_empleado = $_SESSION["id_usuario"];
    $numero_dias= $_POST['numero_dias'];
    $origen= $_POST['origen'];

    $insertar1=false;
    $insertar2=false;
    $insertar3=false;
    $fecha_vencimiento=sumar_dias_Ymd($fecha_movimiento, $numero_dias);

    $id_sucursal = $_SESSION["id_sucursal"];
    $sql_num = _query("SELECT di FROM correlativo WHERE id_sucursal='$id_sucursal'");
    $datos_num = _fetch_array($sql_num);
    $ult = $datos_num["di"]+1;
    $numero_doc=$ult.'_DI';
    _begin();
    $z=1;

    $corr=1;
    $table="correlativo";
    $form_data = array(
      'di' =>$ult
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


        $table='movimiento_producto';
        $form_data = array(
          'id_sucursal' => $id_sucursal,
          'correlativo' => $numero_doc,
          'concepto' => "DESCARGO DE INVENTARIO",
          'total' => $total_compras,
          'tipo' => 'SALIDA',
          'proceso' => 'DI',
          'referencia' => $numero_doc,
          'id_empleado' => $id_empleado,
          'fecha' => $fecha_movimiento,
          'hora' => $hora,
          'id_suc_origen' => $id_sucursal,
          'id_suc_destino' => $id_sucursal,
          'id_proveedor' => 0,
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
								$stock_pos = $fila["stock"];
                $suu = round($cantidad * $precio_compra, 3);
								$cantidado=$cantidad;
								$cantidad_prod=$cantidad;
								$existencias=0;
								$nrow2=0;

								$sql2="SELECT productos.id_producto, productos.perecedero,
								stock.stock as existencias, stock.costo_unitario
								from productos,stock
								where productos.id_producto='$id_producto'
								and productos.id_producto=stock.id_producto
								and stock.id_sucursal='$id_sucursal'";
								$stock2=_query($sql2);
								$nrow2=_num_rows($stock2);

								if ($nrow2>0) {
									$row2=_fetch_array($stock2);
									//$unidad=$row2['unidad'];
									$unidad=1;
									$existencias=$row2['existencias'];
									$perecedero=$row2['perecedero'];
									$costo=$row2['costo_unitario'];

									$cantidad_stock=$existencias-$cantidad;
									if ($cantidad_stock<0) {
										$cantidad_stock=0;
									}
									$cant_facturar=$cantidad;

									$table2= 'stock';
									$where_clause2="WHERE id_producto='$id_producto' and id_sucursal='$id_sucursal'";

									$form_data2 = array(
										'stock' => $cantidad_stock,
									);
									$insertar2 = _update($table2, $form_data2, $where_clause2);
								}

								//movimiento_detalle
								$sql_lot = _query("SELECT MIN(numero) AS ultimo FROM lote
								WHERE id_producto='$id_producto'  and id_sucursal='$id_sucursal'");
								$datos_lot = _fetch_array($sql_lot);
								$lote = $datos_lot["ultimo"];
								$t_movdet= 'movimiento_producto_detalle';
								$form_movdet = array(
									'id_movimiento'=>$id_movimiento,
									'id_producto' => $id_producto,
									'cantidad' => $cantidad,
									'costo' => $costo,
									'precio' => $precio_compra,
									'stock_anterior'=>$existencias,
									'stock_actual'=>$cantidad_stock,
									'lote' => $lote,
								);
								$insert_mov_det = _insert($t_movdet, $form_movdet);

								$cant_sale=0;
								$sql_4 = "SELECT su.id_su, su.id_producto, su.cantidad, su.id_ubicacion, su.id_sucursal, u.id_ubicacion, u.bodega
								FROM stock_ubicacion AS su, ubicacion AS u
								WHERE su.id_producto = '$id_producto' AND su.id_ubicacion = u.id_ubicacion AND u.id_ubicacion = '$origen' AND su.cantidad > 0
								AND su.id_sucursal = '$id_sucursal' ORDER BY su.id_su ASC";
								$result4 = _query($sql_4);
								$num4 = _num_rows($result4);

								$can_su = $cantidad;
								if ($num4 > 0) {
									while ($row_su = _fetch_array($result4)) {
										$id_su = $row_su["id_su"];
										$id_pro_su = $row_su["id_producto"];
										$cantidad = $row_su["cantidad"];
										$tabla_su = "stock_ubicacion";
										if ($can_su > 0) {
											if ($cantidad >= $can_su) {
												$sub_su = $cantidad - $can_su;
												$form_su = array(
													'cantidad' => $sub_su,
												);
												$where_su = "id_su='".$id_su."'";
												$actualiza_su = _update($tabla_su, $form_su, $where_su);
												$can_su = 0;
											} elseif ($can_su >= $cantidad) {
												$sub_su = $can_su - $cantidad;
												$form_su = array(
													'cantidad' => 0,
												);
												$where_su = "id_su='".$id_su."'";
												$actualiza_su = _update($tabla_su, $form_su, $where_su);
												$can_su = $sub_su;
											}
										}
									}
								}

								//lote
								$table_lote='lote';
								// ojo revisar bien la logica de los lotes para irlos venciendo!!!!!!!!!! 24 agosto 2018
								$sql_lote = "SELECT id_lote, id_producto, fecha_entrada, precio, cantidad,salida, estado, numero,
								id_sucursal, vencimiento, referencia
								FROM lote WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal'
								AND estado='VIGENTE' AND (vencimiento>='$fecha_ing' OR  vencimiento='0000-00-00')
								ORDER BY id_lote,vencimiento ASC";
								$result_lote=_query($sql_lote);

								$nrow_lote=_num_rows($result_lote);
								$fecha_mov=ED($fecha_movimiento);
								$diferencia=0;
								if ($nrow_lote>0) {
									for ($j=0;$j<$nrow_lote;$j++) {
										$row_lote=_fetch_array($result_lote);
										$id_lote_prod=$row_lote['id_lote'];
										$cantidad_lote=$row_lote['cantidad'];
										$salida=$row_lote['salida'];
										$fecha_caducidad=$row_lote['vencimiento'];
										$fecha_caducidad=ED($fecha_caducidad);
										//caso 1 cuando cantidad en lote es mayor que salida sumado con cantidad a descontar
										$stock_fecha= $cantidad_lote-$salida;
										if ($stock_fecha>$cantidad_prod) {
											$cant_sale=$salida+$cantidad_prod;
											$diferencia=0;
											$estado='VIGENTE';
										}
										if ($stock_fecha==$cantidad_prod) {
											$cant_sale=$salida+$cantidad_prod;
											$diferencia=0;
											$estado='FINALIZADO';
										}
										if ($stock_fecha<$cantidad_prod) {
											$cant_sale=$cantidad_lote;
											$diferencia=$cantidad_prod-$stock_fecha;
											$cantidad_prod=  $diferencia;
											$estado='FINALIZADO';
										}

										if ($fecha_caducidad!="0000-00-00" || $fecha_caducidad!="00-00-0000" || $fecha_caducidad!=null || $fecha_caducidad!="") {
											$comparafecha=compararFechas("-", $fecha_caducidad, $fecha_mov);
										} else {
											$comparafecha=99;
										}
										if ($fecha_caducidad===null) {
											$comparafecha=99;
										}



										//valida si la fecha de vencimineto ya expiro
										if ($comparafecha<0) {
											$estado='VENCIDO';
										}

										$where_clause_lote="WHERE id_producto='$id_producto'
										AND id_sucursal='$id_sucursal'
										AND cantidad>=salida
										AND id_lote='$id_lote_prod'";
										$form_data_lote = array(
											'salida' => $cant_sale,
											'estado' => $estado
										);
										$insertar4 = _update($table_lote, $form_data_lote, $where_clause_lote);
										//si la cantidad vendida no se pasa de la existencia de x lote perecedero  se sale del bucle for
										if ($diferencia==0) {
											break;
										}
									}
								}
                /*cantidad de una presentacion por la unidades que tiene*/

        } // FOREACH
    }//if $cuantos>0

//  if ($insertar1 && $insertar2 && $insertar3){
if ($insertar2 && $insert_mov_det && $insert_mov)
{
	_commit(); // transaction is committed
	$xdatos['typeinfo']='Success';
	$xdatos['msg']='Descargo de inventario guardado con exito !';
	$xdatos['process']='insert';
} else {
	_rollback(); // transaction rolls back
	$xdatos['typeinfo']='Error';
	$xdatos['msg']='Registro de Factura no pudo ser Actualizado !'._error();
	$xdatos['process']='noinsert';
}
echo json_encode($xdatos);
}
function consultar_stock()
{
    //$id_pedido = $_REQUEST['id_pedido'];
    $id_producto = $_REQUEST['id_producto'];
		$id_ubicacion = $_POST["id_ubicacion"];
    $id_usuario=$_SESSION["id_usuario"];
    $id_sucursal=$_SESSION['id_sucursal'];
    $sql_user="select * from usuario where id_usuario='$id_usuario'";

    $sql3="SELECT * FROM productos WHERE id_producto = '$id_producto'";

		$sql1 = "SELECT p.id_producto,p.id_categoria, p.barcode, p.descripcion, p.perecedero, p.exento, p.id_categoria, SUM(su.cantidad) as stock FROM productos AS p JOIN stock_ubicacion as su ON su.id_producto=p.id_producto JOIN ubicacion as u ON u.id_ubicacion=su.id_ubicacion  WHERE  p.id_producto = '$id_producto' AND u.id_ubicacion='$id_ubicacion' AND su.id_sucursal='$id_sucursal'";
		$stock1=_query($sql1);
		$row1=_fetch_array($stock1);
		$nrow1=_num_rows($stock1);
		if ($nrow1>0)
		{
			$hoy=date("Y-m-d");
			$perecedero=$row1['perecedero'];
			$barcode = $row1["barcode"];
			$descripcion = $row1["descripcion"];
			$perecedero = $row1["perecedero"];
			$exento = $row1["exento"];
			$categoria=$row1['id_categoria'];


			$stock= $row1["stock"];

		}
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


    }
		$xdatos['stock']= $stock;
		$xdatos['descripcion'] =$descripcion;
		$xdatos['precio_venta'] = $cp;
		$xdatos['precios'] = $select;
		$xdatos['perecedero'] = $perecedero;
		echo json_encode($xdatos); //Return the JSON Array
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
