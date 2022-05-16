<?php
include_once "_core.php";
include('num2letras.php');
function initial()
{
    $_PAGE = array();
    $_PAGE ['title'] = 'Verificacion de Preingreso';
    $_PAGE ['links'] = null;
    $_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/style_table_ped.css" rel="stylesheet">';

    include_once "header.php";
    //include_once "main_menu.php";
    //load get or request
    $id_pedido=$_GET['id_pedido'];
    //permiso del script
    $id_user=$_SESSION["id_usuario"];
    $admin=$_SESSION["admin"];

    $uri = $_SERVER['SCRIPT_NAME'];
    $filename=get_name_script($uri);
    $links=permission_usr($id_user, $filename);

    // 	JOIN usuario AS usr ON (ped.id_empleado=usr.id_usuario)
    //array de tipos Documento
    $sql="SELECT idtransace,fecha,pares,items
    FROM pedidos
    WHERE idtransace='$id_pedido'";
    $result=_query($sql);
    $count=_num_rows($result);
    for ($i=0;$i<$count;$i++) {
        $row=_fetch_array($result);
        $fecha=$row['fecha'];
        $pares=$row['pares'];
        $items=$row['items'];
    }
    $fecha_actual=date("Y-m-d"); ?>
        <div class="gray-bg">
        <div class="row">
        <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary" href="admin_pedido_rangos.php"><i class="fa fa-mail-reply"></i> </a>
        </div>
        </nav>
        </div>


  <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-2"></div>
  </div>
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
              <input type='hidden' name='id_pedido' id='id_pedido' value='<?php echo $id_pedido; ?>'>

              <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Barcode</label>
                        <input type="text" id="barcode" class='form-control' placeholder="Barcode" />
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>Items&nbsp;</label>
                        <input type="text" class='form-control' id="items" value='<?php echo $items; ?>' readOnly />
                        <input type="hidden" class='form-control' id="itemsa" value='<?php echo $items; ?>' />
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>Pares/Unidades&nbsp;</label>
                        <input type="text" class='form-control' id="pares" value='<?php echo $pares; ?>' readOnly />
                      </div>
                    </div>
                    <div class='col-md-2'>
                      <div class='form-group'>
                        <label>Fecha:</label>
                        <input type='text' placeholder='Fecha' class='datepick form-control' value='<?php echo $fecha; ?>' id='fecha2' name='fecha2' readonly>
                      </div>
                    </div>
              </div>
              <div class="row">
                <div class="col-md-6">&nbsp;&nbsp;
                  <h3 class="text-navy" id='title-table'>&nbsp;Lista  de Productos Preingreso No. <?php echo $id_pedido; ?></h3>
                </div>
              </div>


              <div class="row">
                <div class="form-group col-lg-12">
                  <table class="table table-hover table-striped table-responsive" id="inventable">
                    <tr class=''>
                      <th class="col col-lg-1">C&oacute;digo</th>
                      <th class="col col-lg-2">Descripci&oacute;n</th>
                      <th class="col col-lg-1">Estilo</th>
                      <th class="col col-lg-1">Color</th>
                      <th class="col col-lg-1">Talla</th>
                      <th class="col col-lg-2">Cant.</th>
                      <th class="col col-lg-2">Recv.</th>
                      <th class="col col-lg-2">Comentario.</th>
                      <!--
                      <th class="col col-lg-1">Acci&oacute;n</th>
                    -->
                    </tr>
                  </table>
                  <!--/div-->
                  <table class="table table2">
                    <tbody class='tbody3'>
                      <tr>
                        <td class='col1'>Total Esperado</td>
                        <td class='col2' id='totaltexto1'></td>
                        <td class='col3' id='totcant'>0</td>
                        <td class='col4'> </td>
                      </tr>
                    </tbody>
                  </table>

                  <table class="table table2">
                    <tbody class='tbody3'>
                      <tr>
                        <td class='col1'>Total obtenido</td>
                        <td class='col2' id='totaltexto2'></td>
                        <td class='col3' id='totcant2'>0</td>
                        <td class='col4'> </td>
                      </tr>
                    </tbody>
                  </table>

                  <table class="table table2">
                    <tbody class='tbody3'>
                      <tr>
                        <td class='col1'>Estado</td>
                        <td class='col2' id='estatustext'></td>
                        <td class='col3' id='estatustd'>SN</td>
                        <input hidden type="text" id="estatus" name="estatus" value="">
                        <td class='col4'> </td>
                      </tr>
                    </tbody>
                  </table>



                  <div class='row'>
                    <div class='col-md-12 pull-right' id="totaltexto">
                      <h3 class='text-danger'></h3>&nbsp;</div>
                  </div>
                </div>
                <!-- /widget-content -->
              </div>
              <!-- /widget -->

              <div class='row'>
                <div class="col-md-12">&nbsp;
                  <div class="title-action" id='botones'>
                    <button type="button" id="submit1" disabled name="submit1" class="btn btn-primary"><i class="fa fa-save"></i> Verificar</button>
                  </div>
                </div>
              </div>
              <input type='hidden' name='urlprocess' id='urlprocess' value="<?php echo $filename; ?>">
              <input type="hidden" name="process" id="process" value="insert"><br>
            </div>
        </div>
      </div>
    </div>


  <?php
include_once("footer.php");
                            echo "<script src='js/plugins/arrowtable/arrow-table.js'></script>";
                            echo "<script src='js/funciones/verificar_pedido.js'></script>";
                        } //permiso del script
    else {
        echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
    }
}

function insertar()
{
    //ACA se va insertar a las sig tablas:
    // pedido, pedido_detalle

    //  'process=insert' +  '&cuantos=' + i +'&json_arr='+json_arr+'&fecha_movimiento=' + fecha_movimiento;
    $cuantos = $_POST['cuantos'];
    $id_pedido=$_POST["id_pedido"];
    $fecha_movimiento= $_POST['fecha_movimiento'];
    $items= $_POST['items'];
    $pares= $_POST['pares'];
    $array_json=$_POST['json_arr'];
    $id_usuario=$_SESSION["id_usuario"];
    $id_sucursal= $_SESSION['id_sucursal'];

    $insertar1=false;
    $insertar2=false;

    if ($cuantos>0) {
        #Crear tabla temporal

        _begin();
        $hora=date("H:i:s");
        $fecha_ing=date('Y-m-d');
        //pedidos
        // LISTA DE PEDIDO ID 16
        $alias_tipodoc='LPE';
        $id_tipodoc='16'; // falta  traerlo con una query
        // SELECT idtransace, fecha, id_tipodoc, idemple, pares, almacen, hora, aplicado, actualizado, estante, items, alias_tipodoc FROM pedidos WHERE 1
        $sql_pedido="SELECT * FROM pedidos WHERE idtransace='$id_pedido' AND id_sucursal='$id_sucursal'";
        $result_fc=_query($sql_pedido);
        $row_fc=_fetch_array($result_fc);
        $nrows_fc=_num_rows($result_fc);
        if ($nrows_fc>0) {
            $table= 'pedidos';
            $form_data = array(
                'verificado' => 1,

                );
            //
            $where_clause="WHERE idtransace='$id_pedido'";
            $insertar1 = _update($table, $form_data, $where_clause);
            $table2="detalle_pedidos_recibido";
            $eliminar1 = _delete($table2, $where_clause);
            //$id_transac= _insert_id();
        }
        //borrar los valores antiguos del pedido aun no finalizado

        $array = json_decode($array_json, true);

        foreach ($array as $fila) {
            if ($fila['cantidad']>0) {
                $id_producto=$fila['id'];
                $cantidad=$fila['cantidad'];
                $recibido=$fila['cantidad2'];
                //detalle_ detalle_pedidos_recibido
                $table2= 'detalle_pedidos_recibido';
                $form_data2 = array(
                    'idtransace' =>$id_pedido,
                    'id_producto' => $id_producto,
                    'cantidad' => $cantidad,
                    'recibido' => $recibido,
                );
                $insertar2 = _insert($table2, $form_data2);
            } //  	if( $fila['precio_compra']>0 && $fila['cantidad']>0 ){
        } // FOREACH
    }//if $cuantos>0

//  if ($insertar1 && $insertar2 && $insertar3){
    if ($insertar1 && $eliminar1 && $insertar2) {
        _commit(); // transaction is committed
        $xdatos['typeinfo']='Success';
        $xdatos['msg']='Registro de Preingresos Actualizado !';
        $xdatos['process']='insert';
    } else {
        _rollback(); // transaction not committed
        $xdatos['typeinfo']='Error';
        $xdatos['msg']='Registro de Preingresos no pudo ser Actualizado !';
    }

    echo json_encode($xdatos);
}
function consultar_stock()
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


    $sql3="select p.*,c.nombre  from productos AS p
		JOIN colores AS c ON (p.id_color=c.id_color)
		where p.id_producto='$id_producto'
		";


    $result3=_query($sql3);
    $count3=_num_rows($result3);
    if ($count3>0) {
        $row3=_fetch_array($result3);
        $cp=$row3['costopro'];
        $pv_base=$row3['precio1'];
        $talla=$row3['talla'];
        $color=$row3['nombre'];
        $exento=$row3['exento'];
        $descripcion=$row3['descripcion'];
        $estilo=$row3['estilo'];

        $xdatos['descrip'] =$descripcion;
        $xdatos['costo_prom'] = $cp;
        $xdatos['pv_base'] = $pv_base;
        $xdatos['existencias'] = $existencias;
        $xdatos['color'] = $color;
        $xdatos['talla'] = $talla;
        $xdatos['exento'] = $exento;
        $xdatos['estilo'] = $estilo;
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
function buscarcompras()
{
    $id_compras= trim($_POST['id_compras']);
    $sql0="SELECT dc.id_producto, dc.cantidad
	FROM compras AS cp
	JOIN detalle_compras AS dc ON(cp.id_compras=dc.id_compras)
	WHERE cp.id_compras='$id_compras'";
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
    <tr class='tr1'>
      <td class='col1 td1'><input type='hidden' id='exento' name='exento' value='<?php echo $exento; ?>'>
        <h5><?php echo $id_producto; ?></h5></td>
      <td class='col2 td1'>
        <h5><?php echo $descripcion; ?></h5></td>
      <td class='col1 td1'>
        <h5><?php echo $cp; ?></h5></td>
      <td class='col1 td1'>
        <h5 class='text-success'><?php echo $estilo; ?></h5></td>
      <td class='col1 td1'>
        <h5 class='text-success'><?php echo $talla; ?></h5></td>
      <td class='col1 td1'>
        <h5 class='text-success'><?php echo $nombre; ?></h5></td>
      <td class='col1 td1'>
        <h5 class='text-success'><?php echo $barcode; ?></h5></td>
      <td class='col1 td1'>
        <h5 class='text-success'><?php echo $btnSelect; ?></h5></td>
    </tr>

    <?php
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
    }

    //}
}
?>
