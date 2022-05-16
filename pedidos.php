<?php
include_once "_core.php";
include('num2letras.php');
function initial()
{
    $_PAGE = array();
    $_PAGE ['title'] = 'Preingreso de Producto';
    $_PAGE ['links'] = null;
    $_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
    //	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/style_table_ped.css" rel="stylesheet">';

    $_PAGE ['links'] .= '<link href="css/style_table3.css" rel="stylesheet">';

    include_once "header.php";
    include_once "main_menu.php";

    //permiso del script
    $id_user=$_SESSION["id_usuario"];
    $admin=$_SESSION["admin"];

    $uri = $_SERVER['SCRIPT_NAME'];
    $filename=get_name_script($uri);
    $links=permission_usr($id_user, $filename);

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

    $iva=0;
    $sql_iva="select iva,monto_retencion1,monto_retencion10,monto_percepcion from empresa";
    $result_IVA=_query($sql_iva);
    $row_IVA=_fetch_array($result_IVA);
    $iva=$row_IVA['iva']/100;
    $monto_retencion1=$row_IVA['monto_retencion1'];
    $monto_retencion10=$row_IVA['monto_retencion10'];
    $monto_percepcion=$row_IVA['monto_percepcion'];

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

    //array de tipos Documento
    /*
    $sql3='SELECT idtipodoc, nombredoc, provee,  alias FROM tipodoc WHERE provee=1';
    $result3=_query($sql3);
    $count3=_num_rows($result3);
    $array3= array(-1=>"Seleccione");
    for ($z=0;$z<$count3;$z++){
        $row3=_fetch_array($result3);
        $id3=$row3['alias'];
        $description3=$row3['nombredoc'];
        $array3[$id3] = $description3;
    }*/
    $fecha_actual=date("Y-m-d"); ?>

    <style media="screen">
      .fixed_header{
      width: 100%;
      table-layout: fixed;
      border-collapse: collapse;
      }

      .fixed_header tbody{
        display:block;
        width: 100%;
        overflow: auto;
        height: 120px;
      }

      .fixed_header thead tr {
         display: block;
      }

      .fixed_header tbody tr:hover {
        background-color: rgba(199, 199, 199, 0.38) !important
      }

      .fixed_header thead {

      }

      .fixed_header th, .fixed_header td {
       height: 30px;
      }
    </style>

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
              <div class="widget">
                <div class="widget-header">
                  <div class="row">
                    <div class="col-md-12">
                      <h3 class="text-navy" id='title-table'>Preingreso de Producto</h3>
                    </div>
                  </div>
                </div>



                <div class="">
                  <div class="row">
                    <div class="form-group col-md-4">
                      <label>Proveedor</label>
                        <?php
                        $nombre_select0="id_proveedor";
                        $idd0=-1;
                        //$style='width:400px';
                        $style='';
                        $select0=crear_select2($nombre_select0, $array0, $idd0, $style);
                        echo $select0; ?>
                        <!--
                      <input type="text" name="proveedor" id="proveedor" size="30" class="form-control" placeholder="Ingrese criterio de busqueda" data-provide="typeahead">
                      <label id="mostrar_proveedor"></label>
                      <input hidden type="text" id="id_proveedor" name="id_proveedor" value="">
                    -->
                    </div>

                    <div class="col-md-2">
                      <div class="form-group">
                        <label>Items&nbsp;</label>
                        <input type="text" class='form-control' id="items" value=0 readOnly />
                      </div>
                    </div>
                    <div class="col-md-2">

                      <div class="form-group">
                        <label>Pares/Unidades&nbsp;</label>
                        <input type="text" class='form-control' id="pares" value=0 readOnly />
                      </div>
                    </div>
                    <div class="col-md-2">
                      <label>Monto</label>
                      <input readonly type="text" name="monto" id="monto" size="30" class="form-control" placeholder="0" data-provide="typeahead">
                      <input type="hidden" id="retencion1" name="retencion1" value="0">
                      <input type="hidden" id="retencion10" name="retencion10" value="0">
                      <input type="hidden" id="percepcion" name="percepcion" value="0">
                    </div>
                    <div class='col-md-2'>
                      <div class='form-group'>
                        <label>Fecha:</label>
                        <input type='text' placeholder='Fecha' class='datepick form-control' value='<?php echo $fecha_actual; ?>' id='fecha2' name='fecha2'>
                      </div>
                    </div>
                  </div>
                  </div>

                      <div class="widget-header">
                        <div class="row">
                          <div class="col-md-4">&nbsp;&nbsp;
                            <i class="fa fa-search"> </i>
                            <h3 class="text-navy" id='title-table'>&nbsp;Lista  de Productos encontrados</h3>
                          </div>
                          <div class="form-group col-md-4">
                            <label>Limite Busqueda&nbsp;
  				                      <input type="text"  class='form-control input_header_panel'  id="limite" value=400 /></label>
                          </div>

                          <div class="form-group col-md-4">
                            <label>Reg. Encontrados&nbsp;
  				                        <input type="text"  class='form-control input_header_panel' id='reg_count' value=0 readOnly /></label>
                          </div>
                        </div>
                      </div>
                      <!-- /widget-header -->




                      <div class="">
                        <div class="row">

                          <div class="col-md-2">
                            <div class="form-group">
                              <label>Barcode</label>
                              <input type="text" id="barcode" class='form-control' placeholder="Barcode" />
                            </div>
                          </div>

                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Descripción</label>
                              <input type="text" id="keywords" class='form-control' placeholder="Descripción" />

                            </div>
                          </div>





                          <div class="col-md-2">
                            <div class="form-group">
                              <label>Estilo</label>
                              <input type="text" id="estilo" class='form-control' placeholder="Estilo" />
                            </div>
                          </div>

                          <div class="col-md-2">
                            <div class="form-group">
                              <div><label>Color&nbsp;</label></div>
                              <?php
                              // se va filtrar por descripcion, estilo, talla, color, barcode
                              $nombre_select="select_colores";
                              $id_val=-1;
                              $style='';
                              $select=crear_select2($nombre_select, $array2, $id_val, $style);
                              echo $select; ?>
                            </div>
                          </div>

                          <div class="col-md-2">
                            <div class="form-group">
                              <label>Talla</label>
                              <input type="text" id="talla" class='form-control' placeholder="Talla" />
                            </div>
                          </div>
                        </div>
                        <div class='row' id='encabezado_buscador'>

                        </div>

                        <div class="post-wrapper">
                          <div class="loading-overlay">
                            <div class="overlay-content" id='reg_count0'>Cargando.....</div>
                          </div>

                        </div>
                      </div>

                      <div class='widget-content' id="content">
                        <div class="row">
                          <div class="col-md-12">
                            <table class="fixed_header table-striped " id='loadtable'>
                              <thead >
                                <tr>

                                  <th class="text-success col-lg-1">Barcode</th>
                                  <th class="text-success col-lg-5 ">Nombre</th>
                                  <th class="text-success col-lg-1 ">Estilo</th>
                                  <th class="text-success col-lg-2 ">Color</th>
                                  <th class="text-success col-lg-1 ">Talla</th>
                                  <th class="text-success col-lg-1 ">Costo</th>
                                  <th class="text-success col-lg-1 ">Agregar</th>
                                </tr>
                              </thead>
                              <tbody class='' id="mostrardatos">
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <!--cierra bloque totales-->
                    <!--/div-->

                <!--/div>

 <div class="widget stacked widget-table action-table"-->
 <br>
                <div class="">
                  <div class="form-group ">
                    <table class="table table-hover table-striped table-responsive" id="inventable">
                      <tr class=''>
                        <th class="text-success col col-lg-1">Barcode</th>
                        <th class="text-success col col-lg-4">Descripci&oacute;n</th>
                        <th class="text-success col col-lg-1">Estilo</th>
                        <th class="text-success col col-lg-1">Color</th>
                        <th class="text-success col col-lg-1">Talla</th>
                        <th class="text-success col col-lg-1">Cantidad</th>
                        <th class="text-success col col-lg-1">Costo</th>
                        <th class="text-success col col-lg-1">Precio</th>
                        <th class="text-success col col-lg-1">Acci&oacute;n</th>
                      </tr>

                    </table>
                    <!--/div-->
                    <table class="table table2">
                        <tr>
                          <td class='col-lg-1'>Totales</td>
                          <td class='col-lg-7'></td>
                          <td class='col-lg-2' id='totcant'>0</td>
                          <td class='col-lg-2'> </td>
                          <td class='col-lg-2'> </td>
                        </tr>
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
                      <button type="button" id="submit1" name="submit1" class="btn btn-primary"><i class="fa fa-save"></i> F9 Guardar</button>
                    </div>

                  </div>
                </div>
                <input type='hidden' name='urlprocess' id='urlprocess' value="<?php echo $filename; ?>">
                <input type="hidden" name="process" id="process" value="insert"><br>



              </div>
            </div>
        </div>
      </div>
    </div>
    </div>
    <?php
include_once("footer.php");
                            //echo "<script src='js/plugins/arrowtable/arrow-table.js'></script>";
                            echo "<script src='js/funciones/pedidos.js'></script>";
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
    $fecha_movimiento= $_POST['fecha_movimiento'];
    $items= $_POST['items'];
    $pares= $_POST['pares'];
    $id_proveedor= $_POST['id_proveedor'];
    $monto= $_POST['monto'];
    $array_json=$_POST['json_arr'];
    $id_usuario=$_SESSION["id_usuario"];
    $id_sucursal= $_SESSION['id_sucursal'];

    $insertar1=false;
    $insertar2=false;

    if ($cuantos>0) {
        _begin();
        $hora=date("H:i:s");
        $fecha_ing=date('Y-m-d');
        //pedidos
        // LISTA DE PEDIDO ID 16
        $alias_tipodoc='LPE';
        $id_tipodoc='16'; // falta  traerlo con una query
        // SELECT `idtransace`, `fecha`, `id_tipodoc`, `idemple`, `pares`, `almacen`, `hora`, `aplicado`, `actualizado`, `estante`, `items`, `alias_tipodoc` FROM `pedidos` WHERE 1
        $table= 'pedidos';
        $form_data = array(
                'alias_tipodoc'=> $alias_tipodoc,
                'id_tipodoc'=> $id_tipodoc,
                'fecha' => $fecha_movimiento,
                'pares' => $pares,
                'items' => $items,
                'id_empleado' => $id_usuario,
                'hora' => $hora,
                'id_sucursal' => $id_sucursal,
                'almacen' => $id_sucursal,
                'id_proveedor' => $id_proveedor,
                'monto' => $monto,
                );
        //
        $insertar1 = _insert($table, $form_data);
        $id_transac= _insert_id();

        $array = json_decode($array_json, true);
        foreach ($array as $fila) {
            if ($fila['cantidad']>0) {
                $id_producto=$fila['id'];
                $cantidad=$fila['cantidad'];
                $ultcosto=$fila['ultcosto'];
                $precio1=$fila['precio1'];

                //detalle_ pedidos
                //SELECT `id_det_pedido`, `idtransace`, `idproducto`, `cantidad`, `gravado`, `almacen`, `cantidad2`, `si`, `codebarra`, `idpro` FROM `detalle_pedidos` WHERE 1
                $table2= 'detalle_pedidos';
                $form_data2 = array(
                    'idtransace' => $id_transac,
                    'id_producto' => $id_producto,
                    'cantidad' => $cantidad,
                    'ultcosto' => $ultcosto,
                    'precio1' => $precio1,
                );
                $insertar2 = _insert($table2, $form_data2);
            } //  	if( $fila['precio_compra']>0 && $fila['cantidad']>0 ){
        } // FOREACH
    }//if $cuantos>0

//  if ($insertar1 && $insertar2 && $insertar3){
    if ($insertar1) {
        _commit(); // transaction is committed
        $xdatos['typeinfo']='Success';
        $xdatos['msg']='Registro de Preingreso Actualizado !';
        $xdatos['process']='insert';
    //$xdatos['guardar']="pedidos: ".$insertar1." det pedidos: ".$insertar2." ";
    } else {
        _rollback(); // transaction not committed
        $xdatos['typeinfo']='Error';
        $xdatos['msg']='Registro de Preingreso no pudo ser Actualizado !';
        // $xdatos['guardar']="pedidos: ".$insertar1." det pedidos: ".$insertar2." ";
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


    $sql3="select p.talla,p.estilo,p.descripcion,p.id_color,p.exento,c.nombre,p.ultcosto,p.precio1,p.ultcosto
	FROM productos AS p
		JOIN colores AS c ON (p.id_color=c.id_color)
		where p.id_producto='$id_producto'
		";


    $result3=_query($sql3);
    $count3=_num_rows($result3);
    if ($count3>0) {
        $row3=_fetch_array($result3);
        $estilo=$row3['estilo'];
        $descripcion=$row3['descripcion'];
        $color=$row3['nombre'];
        $talla=$row3['talla'];
        $ultcosto=$row3['ultcosto'];
        $precio1=$row3['precio1'];
        $exento=$row3['exento'];
        $xdatos['descrip'] =$descripcion;
        $xdatos['color'] = $color;
        $xdatos['talla'] = $talla;
        $xdatos['exento'] = $exento;
        $xdatos['ultcosto'] = $ultcosto;
        $xdatos['precio1'] = $precio1;
        $xdatos['estilo'] = $estilo;
        /*
        $cp=$row3['costopro'];
        $pv_base=$row3['precio1'];

        */
        /*
        $xdatos['costo_prom'] = $cp;
        $xdatos['pv_base'] = $pv_base;
        $xdatos['existencias'] = $existencias;

        */

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
function proveedores()
{
    $query = $_POST['query'];
    $sql0="SELECT id_proveedor,nombre,percibe, retiene, retiene10 FROM proveedores  WHERE nombre LIKE '%$query%'";
    $result = _query($sql0);
    $numrows= _num_rows($result);
    $row = _fetch_array($result);

    while ($row = mysqli_fetch_assoc($result)) {
        $array_prod[] =$row['id_proveedor']."|".$row['nombre']."|";
    }

    echo json_encode($array_prod); //Return the JSON Array
}
function productos()
{
    $query = $_POST['query'];
    $query=trim($query);
    $sql0="SELECT productos.id_producto,productos.descripcion, productos.estilo, productos.talla, productos.numera, colores.nombre FROM productos JOIN colores ON colores.id_color=productos.id_color WHERE productos.descripcion LIKE '%$query%' OR productos.id_producto LIKE '%$query%' OR productos.estilo LIKE '%$query%' LIMIT 8";
    $result = _query($sql0);
    $numrows= _num_rows($result);

    while ($row = mysqli_fetch_assoc($result)) {
        $array_prod[] =$row['id_producto']."|".$row['descripcion']."|Estilo:".$row['estilo']."|Talla:".$row['talla']."|".$row['numera']."| ".$row['nombre'];
    }
    echo json_encode($array_prod); //Return the JSON Array
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
      <tr>
        <td style="width: 10%;"><h5><?php echo $id_producto; ?></h5></td>
        <td style="width: 43%;"><h5><?php echo $descripcion; ?></h5></td>
        <td style="width: 8.56%;"><h5 class='text-success'><?php echo $estilo; ?></h5></td>
        <td style="width: 18%;"><h5 class='text-success'><?php echo $nombre; ?></h5></td>
        <td style="width: 8%;"><h5 ><?php echo $talla; ?></h5></td>
        <td style="width: 8.5%;"><h5><?php echo $cp; ?></h5></td>
        <td style="width: 10%;"><h5 class='text-success'><?php echo $btnSelect; ?></h5></td>
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
            $andSQL.= "AND  pr.estilo LIKE '%".$estilo."' ";
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
    $orderBy=" ";

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
    case 'proveedores':
        proveedores();
        break;
    case 'productos':
        productos();
        break;
    case 'traerdatos':
        traerdatos();
        break;

    }

    //}
}
?>
