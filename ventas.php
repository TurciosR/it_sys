<?php
include_once "_core.php";
include('num2letras.php');
include('facturacion_funcion_imprimir.php');
function initial()
{
  //$id_factura=$_REQUEST["id_factura"];
  $title="Venta";
  $_PAGE = array();
  $_PAGE ['title'] = $title;
  $_PAGE ['links'] = null;
  $_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/select2/select2-bootstrap.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/bootstrap-checkbox/bootstrap-checkbox.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/plugins/perfect-scrollbar/perfect-scrollbar.css">';
  $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/util.css">';
  $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/main.css">';
  include_once "header.php";
  //include_once "main_menu.php";
  date_default_timezone_set('America/El_Salvador');
  $fecha_actual = date('Y-m-d');

  $id_sucursal=$_SESSION['id_sucursal'];
  //permiso del script
  $id_user=$_SESSION["id_usuario"];
  $id_contrato = 0;
  if (isset($_REQUEST['id_cotizacion']))
  {
    $id_cotizacion = $_POST["id_cotizacion"];
    $sql_cotizacion = _query("SELECT co.*, cl.nombre FROM cotizacion as co, clientes as cl WHERE co.id_cliente = cl.id_cliente AND co.id_cotizacion = '$id_cotizacion'");
    $cuenta = _num_rows($sql_cotizacion);
    if($cuenta != 0)
    {
      $row_coti = _fetch_array($sql_cotizacion);
      $nombre = $row_coti["nombre"];
      $id_cliente = $row_coti["id_cliente"];
      $fecha_actual = $row_coti["fecha"];
      $tipo_doc = $row_coti["tipo_doc"];
      $coti = $id_cotizacion;
    }
  }
  $concepto_contrato = "";
  if (isset($_REQUEST['id_contrato']))
  {
    $id_contrato = $_REQUEST["id_contrato"];
    //echo $id_contrato;
    $sql_cuota = _query("SELECT c.id_cliente, c.concepto, cl.nombre FROM contrato as c JOIN clientes as cl ON c.id_cliente = cl.id_cliente WHERE c.id_contrato = '$id_contrato'");
    $cuenta_cuota = _num_rows($sql_cuota);
    if($cuenta_cuota > 0)
    {
      $row_cuota = _fetch_array($sql_cuota);
      $nombre = $row_cuota["nombre"];
      $id_cliente = $row_cuota["id_cliente"];
      $concepto_contrato = $row_cuota["concepto"];
      $fecha_actual = date('Y-m-d');
      $tipo_doc = "";
      $coti = "";
    }
  }
  else
  {
    $nombre = "";
    $id_cliente = "";
    $fecha_actual=date("Y-m-d");
    $tipo_doc = "";
    $coti = "";
  }
  //echo $tipo_doc;

  $fecha_ap = date("Y-m-d");
  $sql_apertura = _query("SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal' AND fecha='$fecha_ap' AND id_empleado = '$id_user'");
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
  }

  //impuestos
  $sql_iva="SELECT * FROM config_impuestos WHERE nombre ='IVA'";
  $result_iva=_query($sql_iva);
  $row_iva=_fetch_array($result_iva);
  $iva=$row_iva['porcentaje']/100;

  $sql_ret="SELECT * FROM config_impuestos WHERE nombre ='RETENCION'";
  $result_ret=_query($sql_ret);
  $row_ret=_fetch_array($result_ret);
  $retencion=$row_ret['porcentaje']/100;
  $monto_retencion1=$row_ret['desde_valor'];

  $sql_ret="SELECT * FROM config_impuestos WHERE nombre ='RETENCION10'";
  $result_ret=_query($sql_ret);
  $row_ret=_fetch_array($result_ret);
  $retencion10=$row_ret['porcentaje']/100;
  $monto_retencion10=$row_ret['desde_valor'];

  $sql_per="SELECT * FROM config_impuestos WHERE nombre ='PERCEPCION'";
  $result_per=_query($sql_per);
  $row_per=_fetch_array($result_per);
  $percepcion=$row_per['porcentaje']/100;
  $monto_percepcion=$row_per['desde_valor'];
  /////////////////////////////////////////////////////
  $admin=$_SESSION["admin"];
  $uri = $_SERVER['SCRIPT_NAME'];
  $filename=get_name_script($uri);
  $links=permission_usr($id_user, $filename);
  $id_usuario=$id_user;

  //$fecha_actual=date("Y-m-d");
  //array clientes$fecha = $row_coti["fecha"];

  //clientes

  //factura
  ?>

  <style media="screen">
    .sweet-alert i
    {
      color:#FF0000;
    }
  </style>
  <style media="screen">
  span.select2-container--open {
  	z-index: 9999 !important;
  }
  </style>

  <div class="gray-bg">
    <div class="wrapper wrapper-content  animated fadeInRight">
      <div class="row">
        <div class="col-lg-12">
          <div class="ibox ">
            <?php
            //permiso del script
            if ($links!='NOT' || $admin=='1') {
              if (true){
                ?>
                <div class="ibox-content">
                  <input type="hidden" id="fecha" value="<?php echo $fecha_actual; ?>">
                    <div class="row">
                      <div id='form_datos_cliente' class="form-group col-md-4">
                          <label>Cliente&nbsp;</label>
                          <input type="text" name="cliente" id="cliente" class="form-control">
                          <input type="hidden" name="id_cliente" id="id_cliente" class="form-control" value="<?php echo $id_cliente; ?>">
  												<label id="text_cliente"><?php echo $nombre; ?></label>
                      </div>
                      <div  class="form-group col-md-2">
                          <label>Tipo Impresi&oacuten</label>
                          <select name='tipo_impresion' id='tipo_impresion' class='form-control'>
                            <option value='TIK'
                            <?php
                              if($tipo_doc != "" && $tipo_doc == "TIK" && $tipo_doc != "REB" && $tipo_doc != "COF" && $tipo_doc != "COF")
                              {
                                echo "selected";
                              }
                            ?>
                            >TICKET</option>
                            <option value='REB'
                            <?php
                              if($tipo_doc != "" && $tipo_doc == "REB" && $tipo_doc != "TIK" && $tipo_doc != "COF" && $tipo_doc != "COF")
                              {
                                echo "selected";
                              }
                            ?>
                            >RECIBO</option>
                            <option value='COF'
                            <?php
                              if($tipo_doc != "" && $tipo_doc == "COF" && $tipo_doc != "REB" && $tipo_doc != "TIK" && $tipo_doc != "CCF")
                              {
                                echo "selected";
                              }
                            ?>
                            >FACTURA</option>
                            <option value='CCF'
                            <?php
                              if($tipo_doc != "" && $tipo_doc == "CCF" && $tipo_doc != "REB" && $tipo_doc != "COF" && $tipo_doc != "TIK")
                              {
                                echo "selected";
                              }
                            ?>
                            >CREDITO FISCAL</option>
                          </select>
                      </div>
                      <div class="form-group col-md-2">
                          <label>Seleccione tipo de pago</label>
                          <select name='con_pago' id='con_pago' class='form-control'>
                            <option value='0' selected>Contado</option>
                            <option value='1' >Credito</option>
                          </select>
                      </div>
                      <div class="form-group col-md-2">
                        <div id="dias_caja" hidden>
                          <label>Dias credito</label>
                          <input type="text" name="dias_credito" id="dias_credito" class="form-control">
                        </div>
                      </div>
                      <div class="form-group col-md-2">
                          <label>Fecha</label>
                          <input type="text" name="fecha_movimiento" id="fecha_movimiento" class='form-control datepick2' value="<?php echo ED($fecha_actual); ?>">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-5">
                        <label id='buscar_habilitado'>Buscar Producto/Servicio (Descripci&oacute;n)</label>
                        <input type="text" id="producto_buscar" name="producto_buscar"  class=" form-control usage typeahead" placeholder="Ingrese Descripcion de producto" data-provide="typeahead">
                        <input type="hidden" id="servicio_buscar" name="servicio_buscar"  class=" form-control usage typeahead" placeholder="Ingrese Descripcion del servicio" data-provide="typeahead">
                      </div>
                      <div class="col-md-7">
                        <a class="btn btn-danger pull-right" style="margin-left:1%;margin-top:15px" href="admin_factura_rangos.php" id='salir'><i class="fa fa-mail-reply"></i> F4 Salir</a>
                        <button type="button" id="btn_venta" style="margin-left:1%; margin-top:15px" name="preventa" class="btn btn-primary pull-right usage"><i class="fa fa-save"></i> F8 Guardar</button>
                        <div hidden id='caja_fp'><button type="button" id="f_producto" style="margin-left:1%; margin-top:15px" name="f_producto" class="btn btn-success pull-right usage"><i class="fa fa-eye"></i> Facturar Producto</button></div>
                        <div id='caja_fs'><button type="button" id="f_servicio" style="margin-left:1%; margin-top:15px" name="f_servicio" class="btn btn-success pull-right usage"><i class="fa fa-eye"></i> Facturar Servicio</button></div>
                        <button type="button" id="plus_servicio" style="margin-left:1%; margin-top:15px" name="plus_servicio" class="btn btn-danger pull-right usage"><i class="fa fa-plus"></i> Agregar Servicio</button>
                      </div>
                    </div>
                    </div>
                  <!--load datables estructure html-->
                  <header>
                    <section>
                      <input type='hidden' name='porc_iva' id='porc_iva' value='<?php echo $iva; ?>'>
                      <input type='hidden' name='monto_retencion1' id='monto_retencion1' value='<?php echo $monto_retencion1 ?>'>
                      <input type='hidden' name='monto_retencion10' id='monto_retencion10' value='<?php echo $monto_retencion10 ?>'>
                      <input type='hidden' name='monto_percepcion' id='monto_percepcion' value="<?php echo $monto_percepcion; ?>">
                      <input type='hidden' name='porc_retencion1' id='porc_retencion1' value='<?php echo $retencion; ?>'>
                      <input type='hidden' name='porc_retencion10' id='porc_retencion10' value='<?php echo $retencion10; ?>'>
                      <input type='hidden' name='porc_percepcion' id='porc_percepcion' value='<?php echo $percepcion; ?>'>
                      <input type='hidden' name='retiene_cli' id='retiene_cli' value='0'>
                      <input type='hidden' name='retiene_cli1' id='retiene_cli1' value='0'>
                      <input type='hidden' name='percibe_cli' id='percibe_cli' value='0'>
                      <input type='hidden' name='porcentaje_descuento' id='porcentaje_descuento' value=0>
                      <input type='hidden' name='cotizacion_fac' id='cotizacion_fac' value="<?php echo $coti; ?>">
                      <input type='hidden' name='pago_cuota' id='pago_cuota' value="<?php echo $id_cuota; ?>">
                      <input type='hidden' name='id_contrato' id='id_contrato' value="<?php echo $id_contrato; ?>">

                      <div class="">
                        <div class="row">
                          <div class="col-md-9">
                            <div class="wrap-table1001">
                              <div class="table100 ver1 m-b-10">
                                <div class="table100-head">
                                  <table id="inventable1">
                                    <thead>
                                      <tr class="row100 head">
                                        <th hidden class="success cell100 column10">Id</th>
                                        <th class='success  cell100 column50'>Descripci&oacute;n</th>
                                        <th class='success  cell100 column10'>Stock</th>
                                        <th class='success  cell100 column10'>Cantidad</th>
                                        <th class='success  cell100 column10'>Precio</th>
                                        <th hidden class='success  cell100 column10'></th>
                                        <th class='success  cell100 column10'>Subtotal</th>
                                        <th class='success  cell100 column10'>Acci&oacute;n</th>
                                      </tr>
                                    </thead>
                                  </table>
                                </div>
                                <div class="table100-body js-pscroll">
                                  <table>
                                    <tbody id="inventable">
                                      <?php
                                        if (isset($_REQUEST['id_cotizacion']))
                                        {
                                          $id_cotizacion = $_POST["id_cotizacion"];
                                          $sql_detalle = _query("SELECT * FROM cotizacion_detalle WHERE id_cotizacion = '$id_cotizacion'");
                                          $cue = _num_rows($sql_detalle);
                                          if($cue > 0)
                                          {
                                            $tr_add = "";
                                            $filas = 1;
                                            while ($row_de = _fetch_array($sql_detalle))
                                            {
                                              $id_ps = $row_de["id_prod_serv"];
                                              $cantidad = $row_de["cantidad"];
                                              $precio_venta = number_format(round($row_de['precio_venta'],3),2);
                                              $subtotal = number_format(round($row_de["subtotal"],3),2);
                                              $tipo_prod_serv = $row_de["tipo_prod_serv"];

                                              if($tipo_prod_serv == "PRODUCTO")
                                              {
                                                $id_producto = $id_ps;
                                                $id_usuario=$_SESSION["id_usuario"];

                                                $id_sucursal=$_SESSION['id_sucursal'];
                                                $precio=0;
                                                $categoria="";

                                                $sql1 = "SELECT p.id_producto,p.id_categoria, p.barcode, p.descripcion, p.perecedero, p.exento, p.id_categoria, SUM(su.cantidad) as stock FROM productos AS p JOIN stock_ubicacion as su ON su.id_producto=p.id_producto JOIN ubicacion as u ON u.id_ubicacion=su.id_ubicacion  WHERE  p.id_producto = '$id_producto' AND u.bodega=0 AND su.id_sucursal='$id_sucursal'";
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
                                                  if($stock != NULL)
                                                  {
                                                    $stock = $stock;
                                                  }
                                                  else {
                                                    $stock = 0;
                                                  }
                                                  $i=0;
                                                  $unidadp=0;
                                                  $preciop=0;
                                                  $descripcionp=0;
                                                  $sql_p=_query("SELECT total, total_iva FROM precio_producto WHERE id_producto = '$id_producto'");
                                                  $select="<select class='sel form-control precios' id='precios'>";
                                                  while ($row=_fetch_array($sql_p))
                                                  {
                                                      if($tipo_doc == "CCF")
                                                      {
                                                        $preciop=number_format(round($row['total'],3),2);
                                                      }
                                                      else
                                                      {
                                                        $preciop=number_format(round($row['total_iva'],3),2);
                                                      }

                                                      $xc=0;
                                                      if($i == 0)
                                                      {
                                                        $precio_inicial = $preciop;
                                                      }
                                                      $select.="<option value='$preciop'";
                                                      if($precio_venta == $preciop)
                                                      {
                                                        $select.="selected";
                                                      }
                                                      $select.=">$preciop</option>";
                                                      $i=$i+1;
                                                  }
                                                }
                                                $select.="</select>";
                                                $tr_add .= '<tr class="row100 head" id=' . $filas . '>';
                                                $tr_add .= "<td  class='cell100 column50 text-success'><input type='hidden'  id='bandera' name='bandera' value='producto'><input type='hidden'  class='form-control descripcion_ser' id='descripcion_ser' name='descripcion_ser' value='''><input type='hidden'  id='id_producto' name='id_producto' value='" . $id_producto . "'>" .$descripcion . "</td>";
                                                $tr_add .= "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control stock' id='stock' name='stock' value='" . $stock . "' style='width:80px;' readOnly></div></td>";
                                                //tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'>"+precios+"</div></td>";
                                                $tr_add .= "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value='".$cantidad."' style='width:60px;'></div></td>";
                                                $tr_add .= "<td class='cell100 column10 text-success'>".$select."</td>";
                                                $tr_add .= "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control sub_to' id='sub_to' name='sub_to' value='".$subtotal."' style='width:80px;' readOnly></div></td>";
                                                $tr_add .= '<td class="Delete cell100 column5 text-success"><input  id="delprod" type="button" class="btn btn-danger fa pull-right"  value="&#xf1f8;"></td>';
                                                $tr_add .= '</tr>';
                                                $bandera = "producto";
                                              }
                                              if($tipo_prod_serv == "SERVICIO")
                                              {
                                                $id_servicio = $id_ps;
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
                                                  $precio = $row1["precio"];
                                                }
                                                $hoy=date("Y-m-d");
                                                $tr_add .= '<tr class="row100 head" id=' . $filas . '>';
                                                $tr_add .= "<td  class='cell100 column50 text-success'><input type='hidden'  id='bandera' name='bandera' value='servicio'><input type='hidden'  class='form-control descrip_ser' id='descripcion_ser' name='descripcion_ser' value='''><input type='hidden'  id='id_producto' name='id_producto' value='" . $id_servicio . "'>" . $descripcion . "</td>";
                                                $tr_add .= "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control stock' id='stock' name='stock' value='1' style='width:70px;' readOnly></div></td>";
                                                //tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'>"+precios+"</div></td>";
                                                $tr_add .= "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value='".$cantidad."' style='width:60px;'></div></td>";
                                                $tr_add .= "<td class='cell100 column10 text-success'><input type='text'  class='form-control precios precios_ser' id='precios' name='precios' value='".$precio_venta."' style='width:90px;'></td>";
                                                $tr_add .= "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control sub_to' id='sub_to' name='sub_to' value='".$subtotal."' style='width:80px;' readOnly></div></td>";
                                                $tr_add .= '<td class="Delete cell100 column5 text-success"><input  id="delprod" type="button" class="btn btn-danger fa pull-right"  value="&#xf1f8;"></td>';
                                                $tr_add .= '</tr>';
                                                $bandera = "servicio";
                                              }
                                            }
                                            echo $tr_add;
                                          }
                                        }
                                        if (isset($_REQUEST['cuota']))
                                        {
                                          $tr_add = "";
                                          $filas = 1;
                                          $cuotas = $_REQUEST["cuota"];
                                          $ex = explode("|", $cuotas);
                                          $cuenta_cuotas = count($ex)-1;
                                          //echo "Total".$cuenta_cuotas;
                                          $n = 1;
                                          $mes_inicio = 0;
                                          $anhio_inicio = 0;
                                          $mes_fin = 0;
                                          $anhio_fin = 0;
                                          $monto_total = 0;
                                          for ($i=0; $i < $cuenta_cuotas ; $i++)
                                          {
                                            $id_cuota = $ex[$i];
                                            $sql_cuota1 = _query("SELECT cc.*, c.id_cliente, c.concepto FROM cuota_contrato as cc JOIN contrato as c ON cc.id_contrato = c.id_contrato WHERE cc.id_cuota = '$id_cuota' AND cc.cancelada != 1");
                                            $cuenta_cuota1 = _num_rows($sql_cuota1);
                                            $row_cuota1 = _fetch_array($sql_cuota1);
                                            $monto = $row_cuota1["monto"];
                                            $mes = $row_cuota1["mes"];
                                            $anhio = $row_cuota1["anhio"];
                                            if($n == 1)
                                            {
                                              $mes_inicio = $mes;
                                              $anhio_inicio = $anhio;
                                            }
                                            if($n == $cuenta_cuotas)
                                            {
                                              $mes_fin = $mes;
                                              $anhio_fin = $anhio;
                                            }
                                            $monto_total += $monto;
                                            $n+=1;
                                          }
                                          // echo "valor de n".$n;
                                          // echo "Mes inicio: ".$mes_inicio."\n";
                                          // echo "Mes fin".$mes_fin."\n";
                                          // echo "Anhio inicio".$anhio_inicio."\n";
                                          // echo "Anhio fin".$anhio_fin."\n";
                                          //echo $monto_total."\n";
                                          if($mes_fin != 0 AND $anhio_fin != 0)
                                          {
                                              if($anhio_inicio == $anhio_fin)
                                              {
                                                  if($mes_inicio == $mes_fin)
                                                  {
                                                      $fech="CORRESPONDIENTE AL MES DE ".meses($mes_inicio)." DE $anhio_inicio";
                                                  }
                                                  else
                                                  {
                                                      $fech="CORRESPONDIENTE A LOS MESES DE ".meses($mes_inicio)." HASTA ".meses($mes_fin)." DE $anhio_inicio";
                                                  }
                                              }
                                              else
                                              {
                                                  $fech="CORRESPONDIENTE DEL MES DE ".meses($mes_inicio)." DEL $anhio_inicio HASTA ".meses($mes_fin)." DE $anhio_fin";
                                              }
                                              $descripcion = Mayu($concepto_contrato)." ".$fech;
                                          }
                                          else
                                          {
                                            $fech="CORRESPONDIENTE AL MES DE ".meses($mes_inicio)." DE $anhio_inicio";
                                            $descripcion = Mayu($concepto_contrato)." ".$fech;
                                          }

                                          $tr_add .= '<tr class="row100 head" id=' . $filas . '>';
                                          $tr_add .= "<td  class='cell100 column50 text-success'><input type='hidden'  id='bandera' name='bandera' value='cuota'><input type='hidden'  class='form-control descrip_ser' id='descripcion_ser' name='descripcion_ser' value='".$descripcion."'><input type='hidden'  id='id_producto' name='id_producto' value='".$cuotas."'>" . $descripcion . "</td>";
                                          $tr_add .= "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control stock' id='stock' name='stock' value='1' style='width:70px;' readOnly></div></td>";
                                          //tr_add += "<td class='cell100 column10 text-success'><div class='col-xs-2'>"+precios+"</div></td>";
                                          $tr_add .= "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value='1' style='width:60px;'></div></td>";
                                          $tr_add .= "<td class='cell100 column10 text-success'><input type='text'  class='form-control precios precios_ser' id='precios' name='precios' value='".$monto_total."' style='width:90px;'></td>";
                                          $tr_add .= "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control sub_to' id='sub_to' name='sub_to' value='".$monto_total."' style='width:80px;' readOnly></div></td>";
                                          $tr_add .= '<td class="Delete cell100 column5 text-success"><input  id="delprod" type="button" class="btn btn-danger fa pull-right"  value="&#xf1f8;"></td>';
                                          $tr_add .= '</tr>';
                                          echo $tr_add;
                                        }
                                      ?>
                                    </tbody>
                                  </table>
                                </div>
                                <div class="table101-body">
                                  <table>
                                    <tbody>
                                      <tr>
                                        <td class='cell100 column50 text-bluegrey'  id='totaltexto'>&nbsp;</td>
                                        <td class='cell100 column15 leftt  text-bluegrey ' >CANT. PROD:</td>
                                        <td class='cell100 column10 text-right text-danger' id='totcant'>0</td>
                                        <td class="cell100 column10  leftt text-bluegrey ">TOTALES $:</td>
                                        <td class='cell100 column15 text-right text-green' id='total_gravado'>0.00</td>

                                      </tr>
                                      <tr>
                                        <td class="cell100 column15 leftt text-bluegrey ">SUMAS (SIN IVA) $:</td>
                                        <td  class="cell100 column10 text-right text-green" id='total_gravado_sin_iva'>0.00</td>
                                        <td class="cell100 column15  leftt  text-bluegrey ">IVA  $:</td>
                                        <td class="cell100 column10 text-right text-green " id='total_iva'>0.00</td>
                                        <td class="cell100 column15  leftt text-bluegrey ">SUBTOTAL  $:</td>
                                        <td class="cell100 column10 text-right  text-green" id='total_gravado_iva'>0.00</td>
                                        <td class="cell100 column15 leftt  text-bluegrey ">VENTA EXENTA $:</td>
                                        <td class="cell100 column10  text-right text-green" id='total_exenta'>0.00</td>
                                      </tr>
                                      <tr>
                                        <td class="cell100 column15 leftt text-bluegrey ">PERCEPCION $:</td>
                                        <td class="cell100 column10 text-right  text-green"  id='total_percepcion'>0.00</td>
                                        <td class="cell100 column15  leftt  text-bluegrey ">RETENCION $:</td>
                                        <td class="cell100 column10 text-right text-green" id='total_retencion'>0.00</td>
                                        <td class="cell100 column15 leftt text-bluegrey ">DESCUENTO $:</td>
                                        <td class="cell100 column10  text-right text-green"  id='total_final'>0.00</td>
                                        <td class="cell100 column15 leftt  text-bluegrey">A PAGAR $:</td>
                                        <td class="cell100 column10  text-right text-green"  id='monto_pago'>0.00</td>
                                      </tr>
                                      <tr>
                                        <td class="cell100 column15 leftt  text-bluegrey">RETENCION POR SERVICIOS $:</td>
                                        <td class="cell100 column10  text-right text-green"  id='monto_rs'>0.00</td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="wrap-table1001">
                              <div class="table100 ver1 m-b-10">
                                <div class="table100-head">
                                  <table id="inventable1">
                                    <thead>
                                      <tr class="row100 head">
                                        <th class="success cell100 column100 text-center">PAGO Y CAMBIO</th>
                                        </tr>
                                    </thead>
                                  </table>
                                </div>
                                <div class="table101-body">
                                  <table>
                                    <tbody>
                                      <tr>
                                        <td class='cell100 column50 text-success'>CORRELATIVO:</td>
                                        <td class='cell100 column50'><input type="text" id="corr_in" class="form-control"  value="" readOnly></td>
                                      </tr>
                                      <tr>
                                        <td class='cell100 column50 text-success'>TOTAL: $</td>
                                        <td class='cell100 column50'><input type="text" id="tot_fdo" class="form-control"   value="" readOnly></td>
                                      </tr>
                                      <tr>
                                        <td class='cell100 column50 text-success'>NUM. DOCUMENTO: </td>
                                        <td class='cell100 column50'><input type="text" id="numdoc" class="form-control"   value="" readOnly></td>
                                      </tr>
                                      <tr>
                                        <td class='cell100 column50 text-success'>CLIENTE: </td>
                                        <td class='cell100 column50'><input type="text" id="nomcli" class="form-control"  value="" readOnly></td>
                                      </tr>
									                    <tr>
                                        <td class='cell100 column50 text-success'>DIRECCION: </td>
                                        <td class='cell100 column50'><input type="text" id="dircli" class="form-control"  value="" readOnly></td>
                                      </tr>
                                      <tr>
                                        <td class='cell100 column50 text-success'>NIT: </td>
                                        <td class='cell100 column50'><input type="text" id="nitcli" class="form-control"    value="" readOnly></td>
                                      </tr>
                                      <tr>
                                        <td class='cell100 column50 text-success'>NRC: </td>
                                        <td class='cell100 column50'><input type="text" id="nrccli" class="form-control"   value="" readOnly></td>
                                      </tr>
                                      <tr>
                                        <td class='cell100 column50 text-success'>EFECTIVO: $</td>
                                        <td class='cell100 column50'> <input type="text" id="efectivov" class="form-control"   value=""> </td>
                                      </tr>
                                      <tr>
                                        <td class='cell100 column50 text-success'>CAMBIO: $</td>
                                        <td class='cell100 column50'><input type="text" id="cambiov" class="form-control"   value="" readOnly></td>
                                      </tr>

                                    </tbody>
                                  </table>
                                  <input type="hidden" name="id_facf" id="id_facf" value="0">
                                </div>

                              </div>
                            </div>
                          </div>
                        </div>
                        <?php

                        echo "<input type='hidden' name='id_empleado' id='id_empleado' >";
                        echo "<input type='hidden' name='numero_doc' id='numero_doc' >";
                        echo "<input type='hidden' name='id_factura' id='id_factura' >";
                        echo "<input type='hidden' name='urlprocess' id='urlprocess' value='$filename'>"; ?>
                        <input type='hidden' name='totalfactura' id='totalfactura' value='0'>
                        <input type="hidden" id="imprimiendo" name="imprimiendo" value="0">

                        <input type='hidden' name='id_apertura' id='id_apertura' value='<?php echo $id_apertura; ?>'>
                        <input type='hidden' name='turno' id='turno' value='<?php echo $turno; ?>'>
                        <input type='hidden' name='caja' id='caja' value='<?php echo $caja; ?>'>
                      </div>
                      <!--div class="table-responsive m-t"-->
                    </section>

                  </div>
                  <!--div class='ibox-content'-->
                  <!-- Modal -->
                  <div class='modal fade' id='salidaModal' style="overflow:hidden;" role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
        						<div class='modal-dialog modal-lg'>
        							<div class='modal-content modal-lg'></div><!-- /.modal-content -->
        						</div><!-- /.modal-dialog -->
        					</div><!-- /.modal -->
                  <div class='modal fade' id='viewModal2' style="overflow:hidden;" role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
        						<div class='modal-dialog'>
        							<div class='modal-content'></div><!-- /.modal-content -->
        						</div><!-- /.modal-dialog -->
        					</div><!-- /.modal -->
                  <div class='modal fade' id='busqueda' style="overflow:hidden;" role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
        						<div class='modal-dialog modal-lg'>
        							<div class='modal-content modal-lg'></div><!-- /.modal-content -->
        						</div><!-- /.modal-dialog -->
        					</div><!-- /.modal -->
                  <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content modal-md">
                        <div class="modal-header">
                          <h4 class="modal-title" id="myModalLabel">Pago y Cambio</h4>
                        </div>
                        <div class="modal-body">
                          <div class="wrapper wrapper-content  animated fadeInRight">
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label><h5 class='text-navy'>Numero factura Interno:</h5></label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group" id='fact_num'></div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label><h5 class='text-navy'>Facturado $:</h5></label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <input type="text" id="facturado" name="facturado" value=0 class="form-control decimal" readonly>
                                </div>
                              </div>
                            </div>

                            <div class="row" id='fact_cf'>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label><strong><h5 class='text-danger'>Num. Factura/ Credito Fiscal/ Nota de Envio: </h5></strong></label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <input type="text" id='num_doc_fact' name='num_doc_fact' value='' class="form-control">
                                </div>
                              </div>
                            </div>
                            <div class="row" id='ccf'>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label><strong><h5 class='text-navy'>Nombre de Cliente Credito Fiscal: </h5></strong></label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <input type="text" id='nombreape' name='nombreape' value='' class="form-control">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>NIT Cliente</label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <input type='text' placeholder='NIT Cliente' class='form-control' id='nit' name='nit' value=''>
                                </div>
                              </div>


                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Registro Cliente(NRC)</label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <input type='text' placeholder='Registro (NRC) Cliente' class='form-control' id='nrc' name='nrc' value=''>
                                </div>
                              </div>


                            </div>

                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Efectivo $</label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <input type="text" id="efectivo" name="efectivo" value="" class="form-control decimal" autofocus>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Cambio $</label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <input type="text" id="cambio" name="cambio" value=0 placeholder="cambio" class="form-control decimal" readonly>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-primary" id="btnPrintFact">Imprimir</button>
                          <button type="button" class="btn btn-warning" id="btnEsc">Salir</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-container">
                    <div class="modal fade" id="clienteModal" tabindex="-2" role="dialog" aria-labelledby="myModalCliente" aria-hidden="true">
                      <div class="modal-dialog model-sm">
                        <div class="modal-content"> </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!--<div class='ibox float-e-margins' -->
              </div>
              <!--div class='col-lg-12'-->
            <!--div class='row'-->
            <!--div class='wrapper wrapper-content  animated fadeInRight'-->

            <?php
          }   //apertura de caja
          else {
            echo "<br><br><div class='alert alert-warning'><h3 class='text-danger'> No Hay Apertura de Caja vigente para este turno!!! aperture <a href='admin_corte.php'>aquí</a>  </h3></div></div></div></div></div>";
            include_once("footer.php");
          }  //apertura de caja
          include_once("footer.php");
          echo "<script src='js/funciones/ventas.js'></script>";
          echo "<script src='js/plugins/arrowtable/arrow-table.js'></script>";
          echo "<script src='js/plugins/bootstrap-checkbox/bootstrap-checkbox.js'></script>";
          echo '<script src="js/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>';
        } //permiso del script
        else {
          echo "<br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div></div></div></div></div>";
          include_once("footer.php");
        }
      }

      function cargar_data()
      {
        $id_sucursal = $_SESSION["id_sucursal"];
        $n_ref = $_POST["n_ref"];
        $fecha = date("Y-m-d");

        /////////////////////// FACTURA
        $sql_fact="SELECT factura.id_factura, factura.id_cliente,factura.id_empleado,
        factura.fecha,  factura.numero_doc, factura.tipo_documento
        FROM factura WHERE numero_ref = $n_ref AND fecha = '$fecha' AND finalizada != 1";
        //echo $sql_fact;
        $result_fact=_query($sql_fact);
        $count_fact=_num_rows($result_fact);

        if($count_fact > 0)
        {
          $row_fact=_fetch_array($result_fact);
          $fecha=$row_fact['fecha'];
          $id_factura = $row_fact["id_factura"];
          $numero_doc=$row_fact['numero_doc'];
          $alias_tipodoc = $row_fact["tipo_documento"];
          $id_empleado=$row_fact['id_empleado'];

          /////////////////////////CLIENTE
          $sql_cliente1="SELECT cliente.id_cliente,cliente.retiene,cliente.retiene10,factura.id_factura,factura.fecha,
          cliente.nombre
          FROM cliente,factura
          where id_factura='$id_factura'
          and factura.id_cliente=cliente.id_cliente
          ORDER BY cliente.nombre";
          //echo $sql_cliente1;
          $id_cliente=0;
          $nombre_cliente = "";
          $retencion1=0;
          $retencion10=0;

          $qcliente=_query($sql_cliente1);
          while ($row_cliente=_fetch_array($qcliente)) {
            $id_cliente=$row_cliente['id_cliente'];
            $nombre_cliente=$row_cliente['nombre'];
            if ($row_cliente['retiene']==1) {
              # code...
              $retencion1=0.01;
            }
            if ($row_cliente['retiene10']==1) {
              # code...
              $retencion1=0.1;
            }
          }
          //////////////DETALLE FACTURA
          $sql_fact_det="SELECT factura.id_factura, factura.id_cliente,factura.id_empleado,  factura.fecha, factura.numero_doc, factura.total,
          factura.id_usuario, factura.anulada, factura.id_usuario, factura.finalizada, factura.id_sucursal,
          factura_detalle.id_factura_detalle, factura_detalle.id_prod_serv,factura_detalle.cantidad,
          factura_detalle.precio_venta, factura_detalle.subtotal, factura_detalle.tipo_prod_serv,
          producto.descripcion, producto.id_producto,producto.id_categoria,producto.exento,factura_detalle.id_presentacion
          FROM factura
          JOIN factura_detalle  ON factura.id_factura=factura_detalle.id_factura
          JOIN producto  ON producto.id_producto=factura_detalle.id_prod_serv
          WHERE factura.id_factura='$id_factura'
          AND factura.id_sucursal='$id_sucursal'

          ";

          $result_fact_det=_query($sql_fact_det);
          $count_fact_det=_num_rows($result_fact_det);
          //echo $sql_fact_det;
          $total=0;
          $lista = "";
          for ($i=0;$i<$count_fact_det;$i++) {
            $row=_fetch_array($result_fact_det);
            $numero_doc=$row['numero_doc'];
            $id_factura=$row['id_factura'];
            $id_producto=$row['id_prod_serv'];
            $tipo_prod=$row['tipo_prod_serv'];
            $anulada=$row['anulada'];
            $cantidad=$row['cantidad'];
            $precio_venta=$row['precio_venta'];
            $subtotal=$row['subtotal'];
            $categoria=$row['id_categoria'];
            $total=$row['total'];
            $id_usuario=$row['id_usuario'];
            $id_empleado=$row['id_empleado'];
            $id_producto=$row['id_producto'];
            $id_pre = $row["id_presentacion"];
            $total=sprintf("%.2f", $total);
            $exento=$row['exento'];



            $sql_ss=_query("SELECT presentacion.nombre, presentacion_producto.descripcion,presentacion_producto.id_presentacion,presentacion_producto.unidad,presentacion_producto.precio FROM presentacion_producto JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion WHERE presentacion_producto.id_producto='$id_producto' AND presentacion_producto.activo=1 AND  presentacion_producto.id_sucursal=$id_sucursal");
            //echo "SELECT presentacion.nombre, presentacion_producto.descripcion,presentacion_producto.id_presentacion,presentacion_producto.unidad,presentacion_producto.precio FROM presentacion_producto JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion WHERE presentacion_producto.id_producto='$id_producto' AND presentacion_producto.activo=1";
            $y = 0;
            $unidadp = 0;
            $preciop = 0;
            $select_rank="<select class='sel_r form-control'>";
            $select="<select class='sel form-control'>";
            while ($rowx=_fetch_array($sql_ss)) {
              # code...
              if ($y==0) {
                # code...
                $unidadp=$rowx['unidad'];
                $preciop=$rowx['precio'];
                $descripcionp=$rowx['descripcion'];


                $xc=0;

                $sql_rank=_query("SELECT presentacion_producto_precio.id_prepd,presentacion_producto_precio.desde,presentacion_producto_precio.hasta,presentacion_producto_precio.precio FROM presentacion_producto_precio WHERE presentacion_producto_precio.id_presentacion=$row[id_presentacion] AND presentacion_producto_precio.precio>=$precio_venta  AND presentacion_producto_precio.id_sucursal=$_SESSION[id_sucursal] ORDER BY presentacion_producto_precio.desde ASC
                  ");

                  while ($rowr=_fetch_array($sql_rank)) {
                    # code...
                    $select_rank.="<option value='$rowr[precio]'";
                    if($xc==0)
                    {
                      $select_rank.="selected";
                      $preciop=$rowr['precio'];
                    }
                    $select_rank.=">$rowr[precio]</option>";
                  }
                  $select_rank.="</select>";
                }
                $select.="<option value='$rowx[id_presentacion]'";
                if($id_pre == $rowx["id_presentacion"])
                {
                  $select.="selected";
                }
                $select.=">$rowx[nombre]</option>";
                $y=$y+1;

              }
              $select.="</select>";
              $sql_cc = _query("SELECT * FROM presentacion_producto WHERE id_presentacion = '$id_pre'");
              $roq = _fetch_array($sql_cc);
              $unidadq=$roq['unidad'];
              $precioq=$roq['precio'];
              $descripcionq=$roq['descripcion'];
              $cc = $cantidad / $unidadq;
              //$unidades=round($row['unidad'], 2);

              //$id_posicion=$row['id_posicion'];
              $descripcion=$row['descripcion'];
              //$id_marca=$row['id_marca'];

              $sql_s = _fetch_array(_query("SELECT p.id_sucursal,SUM(su.cantidad) as stock FROM producto AS p JOIN stock_ubicacion as su ON su.id_producto=p.id_producto JOIN ubicacion as u ON u.id_ubicacion=su.id_ubicacion  WHERE  p.id_producto ='$id_producto' AND u.bodega=0 AND su.id_sucursal=$id_sucursal"));
              $stock_r=$sql_s['stock'];

              $hoy=date("Y-m-d");
              $sql_res_pre=_fetch_array(_query("SELECT SUM(factura_detalle.cantidad) as reserva FROM factura JOIN factura_detalle ON factura_detalle.id_factura=factura.id_factura WHERE factura_detalle.id_prod_serv=$id_producto AND factura.id_sucursal=$id_sucursal AND factura.fecha = '$hoy' AND factura.finalizada=0 "));
              $reserva=$sql_res_pre['reserva'];

              $sql_res_esto=_fetch_array(_query("SELECT SUM(factura_detalle.cantidad) as reservado FROM factura JOIN factura_detalle ON factura_detalle.id_factura=factura.id_factura WHERE factura_detalle.id_prod_serv=$id_producto AND factura.id_factura=$id_factura"));
              $reservado=$sql_res_esto['reservado'];


              $existencias=$stock_r+$reservado-$reserva;

              $descprod=$descripcion;
              //$ubica=ubicacionn($id_posicion);
              $ubicacion="";

              if ($existencias<=$cantidad) {
                $existencias=$cantidad;
              }
              $sqkl=_fetch_array(_query("SELECT iva FROM sucursal WHERE id_sucursal=$id_sucursal"));
              $iva=$sqkl['iva']/100;
              $iva=1+$iva;

              $descripcion.=$ubicacion;
              $lista.= "<tr class='row100 head'>";
              $lista.= "<td hidden class='cell100 column10 text-success id_pps'><input type='hidden' id='unidades' name='unidades' value='" . $unidadq . "'>".$id_producto."</td>";
              $lista.= "<td class='cell100 column30 text-success'>".$descripcion."<input type='hidden' id='exento' name='exento' value='".$exento."'>"."</td>";


              $lista.= "<td class='cell100 column10 text-success' id='cant_stock'>".$existencias."</td>";
              $lista.= "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control decimal $categoria cant' id='cant' name='cant' value=".$cc." style='width:60px;'></div></td>";

              $lista.= "<td class='cell100 column10 text-success preccs'>".$select."</td>";
              $lista.= "<td class='cell100 column10 text-success descp'>"."<input type'text' id='dsd' value='" . $descripcionp. "' class='form-control' readonly>"."</td>";
              $lista.= "<td class='cell100 column10 text-success rank_s'>".  $select_rank . "</td>";
              $lista.= "<td hidden class='cell100 column10 text-success'><input type='hidden'  id='precio_venta_inicial' name='precio_venta_inicial' value='".$precio_venta."' ><input type='hidden'  id='precio_sin_iva' name='precio_sin_iva' value='" . round(($precio_venta/$iva),8,PHP_ROUND_HALF_DOWN) . "'><input type='text'  class='form-control decimal' readOnly  id='precio_venta' name='precio_venta' value='".$precio_venta."' style='width:80px;'></td>";

              $lista.= "<td class='ccell100 column10'>"."<input type='hidden'  id='subtotal_fin' name='subtotal_fin' value='".$subtotal."'>" . "<input type='text'  class='decimal form-control' id='subtotal_mostrar' name='subtotal_mostrar'  value='" . round($subtotal,2) . "'readOnly>"."</td>";
              $lista.= "<td class='cell100 column10 Delete text-center'><input id='delprod' type='button' class='btn btn-danger fa'  value='&#xf1f8;'></td>";
              $lista.= "</tr>";
            }
            $select_vendedor="";
            $sqlemp=_query("SELECT id_empleado, nombre FROM empleado WHERE id_sucursal='$id_sucursal' AND id_tipo_empleado=2");
            while($row_emp = _fetch_array($sqlemp))
            {
              if ($row_emp["id_empleado"]==$id_empleado) {
                $select_vendedor .= "<option value='".$row_emp["id_empleado"]."' selected>".$row_emp["nombre"]."</option>";
              }
              else {
                $select_vendedor .= "<option value='".$row_emp["id_empleado"]."'>".$row_emp["nombre"]."</option>";
              }

            }

            $select_cliente="";
            $select_cliente="<option value=''>Seleccione</option>";
            $sqlcli=_query("SELECT * FROM cliente WHERE id_sucursal='$id_sucursal' ORDER BY nombre");
            while($row_cli = _fetch_array($sqlcli))
            {
              if ($row_cli["id_cliente"]==$id_cliente) {
                # code...
                $select_cliente.= "<option value='".$row_cli["id_cliente"]."' selected>".$row_cli["nombre"]."</option>";
              }
              else
              {
                $select_cliente.= "<option value='".$row_cli["id_cliente"]."'>".$row_cli["nombre"]."</option>";
              }

            }

            $select_tipo_impresion="";

            if ("TIK"==$alias_tipodoc) {
              # code...
              $select_tipo_impresion.="<option value='TIK' selected>TICKET</option>";
            }
            else {
              # code...
              $select_tipo_impresion.="<option value='TIK'>TICKET</option>";
            }

            if ("COF"==$alias_tipodoc) {
              # code...
              $select_tipo_impresion.="<option value='COF' selected>FACTURA CONSUMIDOR FINAL</option>";
            }
            else {
              # code...
              $select_tipo_impresion.="<option value='COF'>FACTURA CONSUMIDOR FINAL</option>";
            }

            if ("CCF"==$alias_tipodoc) {
              # code...
              $select_tipo_impresion.="<option value='CCF' selected  >CREDITO FISCAL</option>";
            }
            else {
              # code...
              $select_tipo_impresion.="<option value='CCF'>CREDITO FISCAL</option>";
            }





            $xdatos['typeinfo'] = "Success";
            $xdatos['msg'] = "";
            $xdatos['id_cliente'] = $id_cliente;
            $xdatos['select_cliente'] = $select_cliente;
            $xdatos['select_tipo_impresion'] = $select_tipo_impresion;
            $xdatos['select_vendedor'] = $select_vendedor;
            $xdatos['nombre_cliente'] = $nombre_cliente;
            $xdatos['alias_tipodoc'] = $alias_tipodoc;
            $xdatos['lista'] = $lista;
            $xdatos['id_empleado'] = $id_empleado;
            $xdatos['numero_doc'] = $numero_doc;
            $xdatos['id_factura'] = $id_factura;
            $xdatos['retencion1']= $retencion1;
            $xdatos['retencion10']= $retencion10;
          }
          else
          {
            $xdatos['typeinfo'] = "Error";
            $xdatos['msg'] = "No se encontro documento";
            $xdatos['id_cliente'] = "";
            $xdatos['nombre_cliente'] = "";
            $xdatos['alias_tipodoc'] = "";
            $xdatos['lista'] = "";
            $xdatos['id_empleado'] = "";
            $xdatos['numero_doc'] = "";
            $xdatos['id_factura'] = "";
            $xdatos['retencion1']= 0;
            $xdatos['retencion10']= 0;
          }
          echo json_encode($xdatos);
        }
        function consultar_stock()
        {
          $id_producto = $_REQUEST['id_producto'];
          $tipo_doc = $_POST["tipo_doc"];
          $id_usuario=$_SESSION["id_usuario"];

          $id_sucursal=$_SESSION['id_sucursal'];
          $precio=0;
          $categoria="";

          $sql1 = "SELECT p.id_producto,p.id_categoria, p.barcode, p.descripcion, p.perecedero, p.exento, p.id_categoria, SUM(su.cantidad) as stock FROM productos AS p JOIN stock_ubicacion as su ON su.id_producto=p.id_producto JOIN ubicacion as u ON u.id_ubicacion=su.id_ubicacion  WHERE  p.id_producto = '$id_producto' AND u.bodega=0 AND su.id_sucursal='$id_sucursal'";
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
            if($stock != NULL)
            {
              $stock = $stock;
            }
            else {
              $stock = 0;
            }
            $i=0;
            $unidadp=0;
            $preciop=0;
            $descripcionp=0;
            $sql_p=_query("SELECT total, total_iva FROM precio_producto WHERE id_producto = '$id_producto'");
            $select="<select class='sel form-control ps precios' id='precios'>";
            while ($row=_fetch_array($sql_p))
            {
                if($tipo_doc == "TIK")
                {
                  $preciop= round($row['total_iva'],4);
                }
                if($tipo_doc == "COF")
                {
                  $preciop= round($row['total_iva'], 4);
                }
                if($tipo_doc == "CCF")
                {
                  $preciop= $row['total'];
                }

                $xc=0;
                if($i == 0)
                {
                  $precio_inicial = number_format($preciop, 4);
                }
                $select.="<option value='$preciop'>".number_format($preciop, 4)."</option>";
                $i=$i+1;
            }
          }
          $select.="</select>";
          $xdatos['perecedero']=$perecedero;
          $xdatos['descripcion']= $descripcion;
          $xdatos['select']= $select;
          $xdatos['stock']= $stock;
          $xdatos['categoria']=$categoria;
          $xdatos['precio'] = $precio_inicial;
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
            $cadena_salida= "Son: <strong>".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.</strong>";
            echo $cadena_salida;
          }

          function numero_tiquete($ult_doc, $tipo)
          {
            $ult_doc=trim($ult_doc);
            $len_ult_valor=strlen($ult_doc);
            $long_num_fact=10;
            $long_increment=$long_num_fact-$len_ult_valor;
            $valor_txt="";
            if ($len_ult_valor<$long_num_fact) {
              for ($j=0;$j<$long_increment;$j++) {
                $valor_txt.="0";
              }
            } else {
              $valor_txt="";
            }
            $valor_txt=$valor_txt.$ult_doc."_".$tipo;
            return $valor_txt;
          }

          function insertar()
          {
            //date_default_timezone_set('America/El_Salvador');
            $fecha_movimiento= MD($_POST['fecha_movimiento']);
            $id_cliente=$_POST['id_cliente'];
            $sql_cliente = _query("SELECT * FROM clientes WHERE id_cliente = '$id_cliente'");
            $cuenta_cliente = _num_rows($sql_cliente);
            $row_cliente = _fetch_array($sql_cliente);
            $name_cliente = $row_cliente["nombre"];
            $direccion_cliente = $row_cliente["direccion"];
            $nit_cliente = $row_cliente["nit"];
            $nrc_cliente = $row_cliente["nrc"];
            $coti = $_POST["coti"];

            //$id_factura=$_POST['id_factura'];

            $id_vendedor=$_SESSION["id_usuario"];;
            $cuantos = $_POST['cuantos'];
            $array_json=$_POST['json_arr'];
            $fecha=date("Y-m-d");
            //  IMPUESTOS
            $total_percepcion= $_POST['total_percepcion'];

            $subtotal=$_POST['total_venta'];
            //$sumas=$_POST['sumas'];
            $suma_gravada=$_POST['total_gravado'];
            $iva= $_POST['total_iva'];
            $retencion= $_POST['total_retencion'];
            $venta_exenta= $_POST['total_exento'];
            $total_general=$_POST['total_venta'];
            $total = $retencion+$_POST['total_venta'];
            $tiene_credito = $_POST["id_tipo_pago"];

            $id_empleado=$_SESSION["id_usuario"];
            if($id_vendedor == "")
            {
              $id_vendedor = $id_empleado;
            }
            $id_sucursal=$_SESSION["id_sucursal"];
            $fecha_actual = date('Y-m-d');

            $id_apertura=$_POST['id_apertura'];
            $turno=$_POST['turno'];
            $caja=$_POST['caja'];
            $tipo_documento=$_POST['alias_tipodoc'];
            $id_contrato = $_POST["id_contrato"];
            $tipo_impresion=$tipo_documento;

            $insertar_fact=false;
            $insertar_fact_det=true;
            $insertar_numdoc =false;
            $suma_detalle = 0;

            $hora=date("H:i:s");
            $xdatos['typeinfo']='';
            $xdatos['msg']='';
            $xdatos['process']='';

            $dias_credito = 0;
            if ($tiene_credito==1)
            {
              $saldo=$total_general;
              $dias_credito = $_POST["dias_credito"];
            }

            $sql_apertura = _query("SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal'
              AND fecha='$fecha_actual' AND id_empleado = '$id_empleado'");
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
              }

            _begin();

            $a=1;
            $b=1;
            $c=1;
            $z=1;
            $j = 1 ;
            $k = 1 ;
            $l = 1 ;


            $tipo_entrada_salida='';

            $sql="select * from correlativo WHERE id_sucursal=$id_sucursal";
            $result= _query($sql);
            $rows=_fetch_array($result);
            $nrows=_num_rows($result);
            $ult_ccf=$rows['ccf']+1;
            $ult_cof=$rows['cof']+1;

            $numero_doc="";
            $num_fact_impresa='';

            $table_numdoc="correlativo";
            $data_numdoc="";

            if ($tipo_impresion =='COF') {
              $concepto='FACTURA CONSUMIDOR';
              $data_numdoc = array(
                'cof' => $ult_cof
              );
              $numero_doc=numero_tiquete($ult_cof, $tipo_impresion);
            }
            if ($tipo_impresion =='TIK') {
              $sql_corre = _query("SELECT * FROM caja WHERE id_caja = '$caja'");
              $row_corre = _fetch_array($sql_corre);
              $correlativo_dispo = $row_corre["correlativo_dispo"];
              $concepto ='TICKET';
              $data_numdoc = array(
                'correlativo_dispo' => $correlativo_dispo+1,
              );
              $num_fact_impresa=$correlativo_dispo;
              $numero_doc=numero_tiquete($correlativo_dispo, $tipo_impresion);
            }
            if ($tipo_impresion =='CCF') {
              $concepto ='CREDITO FISCAL';
              $data_numdoc = array(
                'ccf' => $ult_ccf
              );
              $numero_doc=numero_tiquete($ult_ccf, $tipo_impresion);
            }

            if($tipo_impresion != "TIK")
            {
              $where_clause_n=" WHERE id_sucursal='$id_sucursal'";
              $insertar_numdoc = _update($table_numdoc, $data_numdoc, $where_clause_n);
            }
            else
            {
              $tab = 'caja';
              $where_clause_c=" WHERE id_caja='$caja'";
              $insertar_numdoc = _update($tab, $data_numdoc, $where_clause_c);
            }


            $abono=0;
            $saldo=0;

            $serie="";
            $ultimo=0;

            if ($tipo_impresion == "TIK") {
              # code...
              $sql_corre = _query("SELECT * FROM caja WHERE id_caja = '$caja'");
              $row_corre = _fetch_array($sql_corre);
              $serie = $row_corre["serie"];

            }
            elseif ($tipo_impresion == "COF") {
              # code...
              $swl =_fetch_array(_query("SELECT * FROM sucursal where id_sucursal=$id_sucursal "));
              $serie=$swl['serie_cof'];


              $sql_ult=_query("SELECT MAX(CONVERT(num_fact_impresa,UNSIGNED INTEGER)) as ultimo FROM factura WHERE id_sucursal=$id_sucursal AND tipo_documento='COF' ");

              $num_rows_ul=_num_rows($sql_ult);
              if ($num_rows_ul>0) {
                # code...
                $ul=_fetch_array($sql_ult);
                $ultimo=$ul['ultimo'];
              }

            }
            else {
              # code...
              $swl =_fetch_array(_query("SELECT * FROM sucursal where id_sucursal=$id_sucursal "));
              $serie=$swl['serie_ccf'];

              $sql_ult=_query("SELECT MAX(CONVERT(num_fact_impresa,UNSIGNED INTEGER)) as ultimo FROM factura WHERE id_sucursal=$id_sucursal AND tipo_documento='CCF' ");

              $num_rows_ul=_num_rows($sql_ult);
              if ($num_rows_ul>0) {
                # code...
                $ul=_fetch_array($sql_ult);
                $ultimo=$ul['ultimo'];
              }

            }

            $table_fact= 'factura';
            $form_data_fact = array(
              'id_cliente' => $id_cliente,
              'fecha' => $fecha_movimiento,
              'numero_doc' => $numero_doc,
              'total' => $total_general,
              'id_usuario'=>$id_empleado,
              'id_empleado' => $id_empleado,
              'id_vendedor'=>$id_vendedor,
              'id_sucursal' => $id_sucursal,
              'tipo_pago' =>$tipo_impresion,
              'tipo' => $concepto,
              'hora' => $hora,
              'dias_credito' => $dias_credito,
              'credito' => $tiene_credito,
              'finalizada' => 0,
              'impresa' => 1,
              'num_fact_impresa' => 0,
              'abono'=>$abono,
              'saldo' => $saldo,
              'tipo_documento' => $tipo_impresion,
              'total_retencion'=>$retencion,
              'total_iva' => $iva,
              'id_contrato' => $id_contrato,
            );
            $insertar_fact = _insert($table_fact, $form_data_fact);
            $id_fact= _insert_id();
            echo _error();

            $table='movimiento_producto';
            $form_data = array(
              'id_sucursal' => $id_sucursal,
              'correlativo' => $numero_doc,
              'concepto' => "VENTA",
              'total' => $total,
              'tipo' => 'SALIDA',
              'proceso' => $tipo_documento,
              'referencia' => $numero_doc,
              'id_empleado' => $id_empleado,
              'fecha' => $fecha,
              'hora' => $hora,
              'id_suc_origen' => $id_sucursal,
              'id_suc_destino' => $id_sucursal,
              'id_proveedor' => 0,
              'id_factura' => $id_fact,
            );
            $insert_mov =_insert($table,$form_data);
            $x=1;
            if ($insert_mov) {
              $id_movimiento=_insert_id();
            }
            else {
              # code...
              $x=0;
            }

            $cre=1;
            if($tiene_credito==1)
            {
              $table="credito";
              $form_data = array(
                'id_cliente' => $id_cliente,
                'fecha' => $fecha_movimiento,
                'tipo_doc' => $tipo_impresion,
                'numero_doc' => $numero_doc,
                'id_factura' => $id_fact,
                'dias' =>  $dias_credito,
                'total' => $total_general,
                'abono' => 0,
                'saldo' => $total_general,
                'finalizada' => 0,
                'id_sucursal' => $id_sucursal,
              );
              $insert=_insert($table,$form_data);
              if ($insert) {
                # code...
              }
              else {
                # code...
                $cre=0;
              }
            }



          if ($cuantos>0)
          {
            $array = json_decode($array_json, true);
            foreach ($array as $fila) {
              if ($fila['precio']>=0 && $fila['cantidad']>0)
              {
                $id_producto=$fila['id_producto'];
                $cantidad=$fila['cantidad'];
                $precio_venta=$fila['precio'];
                $bandera = $fila["bandera"];
                if ($bandera == "producto")
                {
                  $tipoprodserv = "PRODUCTO";
                  $subtotal = round($precio_venta * $cantidad, 2);
                  $cantidado=$cantidad;
                  $cantidad_prod=$cantidad;
                  $existencias=0;
                  $nrow2=0;
                  //Primero revisar stock y q me facture solo las existencias reales
                  $sql2="SELECT productos.id_producto, productos.perecedero,
                  stock.stock as existencias, stock.costo_unitario
                  from productos,stock
                  where productos.id_producto='$id_producto'
                  and productos.id_producto=stock.id_producto
                  and stock.id_sucursal='$id_sucursal'";
                  $stock2=_query($sql2);
                  $nrow2=_num_rows($stock2);

                  //Actualizar en stock si  hay registro del producto
                  $cant_facturar=0;
                  $perecedero = 0;
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
                    'precio' => $precio_venta,
                    'stock_anterior'=>$existencias,
                    'stock_actual'=>$cantidad_stock,
                    'lote' => $lote,
                  );
                  $insert_mov_det = _insert($t_movdet, $form_movdet);

                  $table_fact_det= 'factura_detalle';
                  $data_fact_det = array(
                    'id_factura' => $id_fact,
                    'id_prod_serv' => $id_producto,
                    'cantidad' => $cantidad,
                    'precio_venta' => $precio_venta,
                    'subtotal' => $subtotal,
                    'tipo_prod_serv' => $tipoprodserv,
                    'id_empleado' => $id_empleado,
                    'id_sucursal' => $id_sucursal,
                    'fecha' => $fecha_movimiento,
                    'tipo' => 0,
                  );
                  if ($cantidad>0 && $id_fact > 0) {
                    $insertar_fact_det = _insert($table_fact_det, $data_fact_det);
                    $suma_detalle += 1;
                    echo _error();
                  }
                  $cant_sale=0;
                  $sql_4 = "SELECT su.id_su, su.id_producto, su.cantidad, su.id_ubicacion, su.id_sucursal, u.id_ubicacion, u.bodega
                  FROM stock_ubicacion AS su, ubicacion AS u
                  WHERE su.id_producto = '$id_producto' AND su.id_ubicacion = u.id_ubicacion AND u.bodega != 1 AND su.cantidad > 0
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
                  AND estado='VIGENTE' AND (vencimiento>='$fecha_actual' OR  vencimiento='0000-00-00')
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
                }
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
                    $id_servicio = _insert_id();
                  }
                  else
                  {
                      $id_servicio = $id_producto;
                  }
                  $tipoprodserv = "SERVICIO";
                  $table_fact_det= 'factura_detalle';
                  $data_fact_det = array(
                    'id_factura' => $id_fact,
                    'id_prod_serv' => $id_servicio,
                    'cantidad' => $cantidad,
                    'precio_venta' => $precio_venta,
                    'subtotal' => $subtotal,
                    'tipo_prod_serv' => $tipoprodserv,
                    'id_empleado' => $id_empleado,
                    'id_sucursal' => $id_sucursal,
                    'fecha' => $fecha_movimiento,
                    'tipo' => 1,
                  );
                  if ($cantidad>0 && $id_fact > 0) {
                    $insertar_fact_det = _insert($table_fact_det, $data_fact_det);
                    $suma_detalle += 1;
                    echo _error();
                  }
                }
                if($bandera == "cuota")
                {
                  $cuotas = $id_producto;
                  $ex = explode("|", $cuotas);
                  $cuen_cuo = count($ex);
                  for($jr=0; $jr < $cuen_cuo; $jr++)
                  {
                    $cuota = $ex[$jr];
                    $sql_da = _query("SELECT * FROM cuota_contrato WHERE id_cuota = '$cuota'");
                    $rrrr = _fetch_array($sql_da);
                    $abono = $rrrr["monto"];
                    $tav = "cuota_contrato";
                    $lista = array(
                      'cancelada' => 1,
                      'fecha_pago' => date('Y-m-d'),
                      'abono' => $abono,
                    );
                    $wc = "id_cuota = '".$cuota."'";
                    $up_c = _update($tav, $lista, $wc);
                  }
                  $descripcion = $fila["descripcion_ser"];
                  $subtotal = round($precio_venta * $cantidad, 2);
                  $tipoprodserv = "CUOTA";
                  $table_fact_det= 'factura_detalle';
                  $data_fact_det = array(
                    'id_factura' => $id_fact,
                    'cantidad' => $cantidad,
                    'precio_venta' => $precio_venta,
                    'subtotal' => $subtotal,
                    'tipo_prod_serv' => $tipoprodserv,
                    'id_empleado' => $id_empleado,
                    'id_sucursal' => $id_sucursal,
                    'fecha' => $fecha_movimiento,
                    'tipo' => 1,
                    'concepto' => $descripcion,
                  );
                  if ($cantidad>0 && $id_fact > 0) {
                    $insertar_fact_det = _insert($table_fact_det, $data_fact_det);
                    $suma_detalle += 1;
                    echo _error();
                  }
                }
                  /*} //si es perecedero

                  else {
                  $insertar4 =true;
                }*/
              } // if($fila['cantidad']>0 && $fila['precio']>0){
              }

              if($coti != "")
              {
                $sql_coti = _query("SELECT * FROM cotizacion WHERE id_cotizacion = '$coti' AND factura != 1");
                $cuenta_coti = _num_rows($sql_coti);
                if($cuenta_coti > 0)
                {
                  $tabla_coti = "cotizacion";
                  $form_coti = array(
                    'factura' => 1,
                    'referencia' => $numero_doc,
                  );
                  $wco = "id_cotizacion='".$coti."'";
                  $up_coti = _update($tabla_coti, $form_coti, $wco);
                }
              }

              if ($insertar_numdoc  && $insertar_fact && $insertar_fact_det)
              {
                _commit(); // transaction is committed
                $xdatos['typeinfo']='Success';
                $xdatos['msg']='Documento Numero: <strong>'.$numero_doc.'</strong>  Guardado con Exito !';
                $xdatos['process']='insert';
                $xdatos['factura']=$id_fact;
                $xdatos['numero_doc']=$numero_doc;
                $xdatos['numero_doc_print']=  $numero_doc;
                $xdatos['total_venta'] = $total_general;
                if($tipo_impresion == "TIK")
                {
                  $xdatos['num_dc'] = $correlativo_dispo;
                }
                if($tipo_impresion == "COF")
                {
                  $xdatos['num_dc'] = $ult_ccf;
                }
                if($tipo_impresion == "COF")
                {
                  $xdatos['num_dc'] = $ult_cof;
                }

                $xdatos["nombre_c"] = $name_cliente;
                $xdatos["direccion_c"] = $direccion_cliente;
                $xdatos["nit_c"] = $nit_cliente;
                $xdatos["nrc_c"] = $nrc_cliente;
                //$xdatos['insertados']=" num_doc :".$numero_doc." factura :".$insertar_fact." factura detalle:".$insertar_fact_det."  stock:".$insertar2." lote:".$insertar4  ;
                //$xdatos['insertados2']=" cant_sale_new:".$cant_sale;
              } else {
                _rollback(); // transaction rolls back
                $xdatos['typeinfo']='Error';
                $xdatos['msg']='Registro de Factura no pudo ser Actualizado !'._error();
                $xdatos['process']='noinsert';
                $xdatos['insertados']=" num_doc :".$numero_doc." factura :".$insertar_fact." factura detalle:".$insertar_fact_det." mov prod:".$insert_mov ."  mov prod_det:".$insert_mov_det." stock:".$insertar2." lote:".$insertar4  ;
              }
            }

      echo json_encode($xdatos);
    }

              function insertar_venta()
              {
                date_default_timezone_set('America/El_Salvador');
                $fecha_actual = date('Y-m-d');
                $hora_actual = date("H:i:s");
                $cuantos = $_POST['cuantos'];
                $total_venta = $_POST['total_ventas'];
                $id_cliente = $_POST['id_cliente'];
                $tipo_impresion = $_POST['tipo_impresion'];
                $array_json=$_POST['json_arr'];
                $id_empleado=  $_POST['id_empleado'];
                $id_factura=$_POST['id_factura'];
                $id_usuario=$_SESSION["id_usuario"];
                $id_sucursal=$_SESSION["id_sucursal"];
                $id_apertura = $_POST["id_apertura"];
                $turno = $_POST["turno"];
                $caja = $_POST["caja"];
                $abono=0;
                $saldo=0;

                $credito=$_POST["credito"];

                if($credito==1)
                {
                  $saldo=$total_venta;
                }

                $fecha_movimiento= date('Y-m-d');

                $insertar1=false;
                $insertar2=false;
                $insertar_fact=false;
                $insertar_fact_det=false;
                $insertar_numdoc =false;
                $insertar4 =false;
                $id='1';
                $xdatos['typeinfo']='';
                $xdatos['msg']='';
                $xdatos['process']='';
                #Crear tabla temporal
                $tmp_tbl='CREATE TEMPORARY TABLE IF NOT EXISTS tmp_det_fact  LIKE factura_detalle';
                $result0=_query($tmp_tbl);
                _begin();
                $sql="select * from correlativo";
                $result= _query($sql);
                $rows=_fetch_array($result);
                $nrows=_num_rows($result);
                $ult_ref1=$rows['ref'];
                $ult_ccf=$rows['ccf']+1;
                $ult_cof=$rows['cof']+1;
                $ult_tik=$rows['tik']+1;
                $ult_ref=$ult_ref1+1;
                $table_numdoc="correlativo";
                if ($tipo_impresion =='COF') {
                  $tipo_entrada_salida='FACTURA CONSUMIDOR';
                  $data_numdoc = array(
                    'cof' => $ult_cof
                  );
                  $numero_doc=numero_tiquete($ult_cof, $tipo_impresion);
                }

                if ($tipo_impresion =='TIK') {
                  $sql_corre = _query("SELECT * FROM caja WHERE id_caja = '$caja'");
                  $row_corre = _fetch_array($sql_corre);
                  $correlativo_dispo = $row_corre["correlativo_dispo"];
                  $tipo_entrada_salida='TICKET';
                  $data_numdoc = array(
                    'correlativo_dispo' => $correlativo_dispo+1,
                  );
                  $numero_doc=numero_tiquete($correlativo_dispo, $tipo_impresion);
                }
                if ($tipo_impresion =='CCF') {
                  $tipo_entrada_salida='CREDITO FISCAL';
                  $data_numdoc = array(
                    'ccf' => $ult_ccf
                  );
                  $numero_doc=numero_tiquete($ult_ccf, $tipo_impresion);
                }
                if ($nrows==0) {
                  if($tipo_impresion != "TIK")
                  {
                    $insertar_numdoc = _insert($table_numdoc, $data_numdoc);
                  }
                } else {
                  ///////////////Actualiza el numero en base de datos
                  if($tipo_impresion != "TIK")
                  {
                    $where_clause_n=" WHERE id_sucursal='$id_sucursal'";
                    $insertar_numdoc = _update($table_numdoc, $data_numdoc, $where_clause_n);
                  }
                  else
                  {
                    $tab = 'caja';
                    $where_clause_c=" WHERE id_caja='$caja'";
                    $insertar_numdoc = _update($tab, $data_numdoc, $where_clause_c);
                  }
                }

                $observaciones=$tipo_entrada_salida;
                if ($cuantos>0) {
                  //select a la tabla factura
                  $sql_fact="SELECT * FROM factura WHERE id_factura='$id_factura'";
                  $result_fact=_query($sql_fact);
                  $row_fact=_fetch_array($result_fact);
                  $nrows_fact=_num_rows($result_fact);
                  if ($nrows_fact>0) {
                    $table_fact= 'factura';
                    $form_data_fact = array(
                      'id_cliente' => $id_cliente,
                      'fecha' => $fecha_movimiento,
                      'numero_doc' => $numero_doc,
                      'total' => $total_venta,
                      'id_usuario'=>$id_usuario,
                      'id_empleado' => $id_empleado,
                      'id_cliente' => $id_cliente,
                      'finalizada' => 1,
                      'credito' => $credito,
                      'abono'=>$abono,
                      'saldo' => $saldo,
                      'id_apertura' => $id_apertura,
                      'turno' => $turno,
                      'caja' => $caja,
                      'hora' => $hora_actual,
                    );
                    $where_clause_fact="WHERE id_factura='$id_factura'";
                    $insertar_fact = _update($table_fact, $form_data_fact, $where_clause_fact);
                    echo _error();
                  }

                  $array = json_decode($array_json, true);
                  foreach ($array as $fila) {
                    if ($fila['cantidad']>0 && $fila['precio']>0) {
                      $id_producto=$fila['id'];
                      $cantidad=$fila['cantidad'];
                      $precio_venta=$fila['precio'];
                      $presentacion = $fila["presentacion"];
                      $bandera = $fila['bandera'];
                      //insertar el detalle de la factura
                      $subtotal=$precio_venta*$cantidad;
                      $sql_det_fact="SELECT * FROM factura_detalle
                      WHERE id_prod_serv='$id_producto' AND id_factura='$id_factura'
                      ";
                      $result_det_fact= _query($sql_det_fact);
                      $nrows_det_fact=_num_rows($result_det_fact);

                      if ($nrows_det_fact>0 && $bandera == "anterior") {
                        $rows_det_fact=_fetch_array($result_det_fact);
                        $id_productt=$rows_det_fact['id_prod_serv'];
                        $cantidadd=$rows_det_fact['cantidad'];
                        $table_fact_det= 'factura_detalle';
                        $data_fact_det = array(
                          'cantidad' => $cantidad,
                          'precio_venta' => $precio_venta,
                          'subtotal' => $subtotal,
                          'id_empleado' => $id_empleado,
                        );
                        $where_clause_fact_det="WHERE id_prod_serv='$id_producto' AND id_factura='$id_factura'";
                        $insertar_fact_det = _update($table_fact_det, $data_fact_det, $where_clause_fact_det);
                        //actualizar stock de producto
                        $table_pr='stock';
                        $sql_pr="SELECT * FROM $table_pr
                        WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal'
                        ";
                        $result_pr= _query($sql_pr);
                        $nrows_pr=_num_rows($result_pr);
                        if ($nrows_pr>0) {
                          $row_pr=_fetch_array($result_pr);
                          $stock_pr=$row_pr['stock'];
                        }
                        $qty=0;
                        if ($cantidad>$cantidadd) {
                          $qty=$cantidad-$cantidadd;
                          $stock_nuevo=$stock_pr-$qty;
                        }
                        if ($cantidadd>$cantidad) {
                          $qty=$cantidadd-$cantidad;
                          $stock_nuevo=$stock_pr+$qty;
                        }
                        if ($cantidad==$cantidadd) {
                          $qty=0;
                          $stock_nuevo=$stock_pr;
                        }
                        if ($cantidad>$stock_pr) {
                          $stock_nuevo=0;
                        }

                        $data_pr = array(
                          'stock' => $stock_nuevo,

                        );
                        $where_clause_pr="WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal'";
                        $insertar_pr = _update($table_pr, $data_pr, $where_clause_pr);

                      } else if($bandera == "nuevo") {
                        $table_fact_det= 'factura_detalle';
                        $data_fact_det = array(
                          'id_factura' => $id_factura,
                          'id_prod_serv' => $id_producto,
                          'cantidad' => $cantidad,
                          'precio_venta' => $precio_venta,
                          'subtotal' => $subtotal,
                          'id_empleado' => $id_empleado,
                          'fecha' => date('Y-m-d'),
                          'id_presentacion' => $presentacion,
                        );
                        $insertar_fact_det = _insert($table_fact_det, $data_fact_det);
                        //producto
                        $table_pr='stock';
                        $sql_pr="SELECT * FROM $table_pr
                        WHERE id_producto='$id_producto'
                        AND id_sucursal='$id_sucursal'";
                        $result_pr= _query($sql_pr);
                        $nrows_pr=_num_rows($result_pr);
                        if ($nrows_pr>0) {
                          $row_pr=_fetch_array($result_pr);
                          $stock_pr=$row_pr['stock'];
                        }
                        $sql_uus=_fetch_array(_query("SELECT * FROM `presentacion_producto` WHERE id_presentacion=$presentacion"));
                        $precio=$sql_uus['precio'];
                        $unidad_w=$sql_uus['unidad'];
                        $precio_venta_unit=$precio_venta;
                        $cantidad_real = ($cantidad * $unidad_w);

                        $stock_nuevo=$stock_pr-$cantidad;
                        $data_pr = array(
                          'stock' => $stock_nuevo,
                        );
                        $where_clause_pr="WHERE id_producto='$id_producto'  AND id_sucursal='$id_sucursal'";
                        $insertar_pr = _update($table_pr, $data_pr, $where_clause_pr);
                        $sql_4 = "SELECT su.id_su, su.id_producto, su.cantidad, su.id_ubicacion, su.id_sucursal, u.id_ubicacion, u.bodega FROM stock_ubicacion AS su, ubicacion AS u WHERE su.id_producto = '$id_producto' AND su.id_ubicacion = u.id_ubicacion AND u.bodega != 1 AND su.cantidad > 0 AND su.id_sucursal = '$id_sucursal' ORDER BY su.id_su ASC";
                        $result4 = _query($sql_4);
                        $num4 = _num_rows($result4);

                        $can_su = $cantidad_real;
                        if($num4 > 0)
                        {
                          while($row_su = _fetch_array($result4))
                          {
                            $id_su = $row_su["id_su"];
                            $id_pro_su = $row_su["id_producto"];
                            $cantidad = $row_su["cantidad"];
                            $tabla_su = "stock_ubicacion";
                            if($can_su > 0)
                            {
                              if($cantidad >= $can_su)
                              {
                                $sub_su = $cantidad - $can_su;
                                $form_su = array(
                                  'cantidad' => $sub_su,
                                );
                                $where_su = "id_su='".$id_su."'";
                                $actualiza_su = _update($tabla_su, $form_su, $where_su);
                                $can_su = 0;
                              }
                              else if($can_su >= $cantidad)
                              {
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
                      }
                      $data_fact_dt = array(
                        'id_factura' => $id_factura,
                        'id_prod_serv' => $id_producto,
                        'cantidad' => $cantidad,
                        'precio_venta' => $precio_venta,
                        'subtotal' => $subtotal,
                        'id_empleado' => $id_empleado,
                        'fecha' => date('Y-m-d'),
                        'id_presentacion' => $presentacion,
                        'id_sucursal' => $_SESSION['id_sucursal'],
                      );
                      $table3 = 'tmp_det_fact';
                      $updates3 = _insert($table3, $data_fact_dt);

                      $sql1="select * from movimiento_producto where id_producto='$id_producto'
                      and tipo_entrada_salida='$tipo_entrada_salida'
                      AND numero_doc='$numero_doc' and fecha_movimiento='$fecha_movimiento'
                      ";

                    } // if($fila['cantidad']>0 && $fila['precio']>0){
                    } //foreach ($array as $fila){

                      $sql_prod_deleted="SELECT * FROM factura_detalle
                      WHERE factura_detalle.id_factura='$id_factura' AND factura_detalle.id_prod_serv
                      NOT IN (SELECT id_prod_serv FROM tmp_det_fact)
                      ";
                      $result_prod_deleted= _query($sql_prod_deleted);
                      $nrows_prod_deleted=_num_rows($result_prod_deleted);
                      if ($nrows_prod_deleted>0) {
                        $row_prod_deleted=_fetch_array($result_prod_deleted);
                        $stock_prod_deleted=$row_prod_deleted['cantidad'];
                        $id_prod=$row_prod_deleted['id_producto'];
                        $table_pr= 'stock';
                        $sql_pr1="SELECT * FROM $table_pr
                        WHERE id_producto='$id_prod'
                        ";
                        $result_pr1= _query($sql_pr1);
                        $nrows_pr1=_num_rows($result_pr1);
                        if ($nrows_pr1>0) {
                          $row_pr1=_fetch_array($result_pr1);
                          $stock_pr1=$row_pr1['stock'];
                        }
                        $stock_nuevo=$stock_prod_deleted+$stock_pr1;


                        $data_pr = array(
                          'stock' => $stock_nuevo,
                        );
                        $where_clause_pr="WHERE id_producto='$id_prod' AND id_sucursal='$id_sucursal'";
                        $insertar_pr = _update($table_pr, $data_pr, $where_clause_pr);
                      }

                      $where_clause1=" WHERE factura_detalle.id_factura='$id_factura' AND factura_detalle.id_prod_serv
                      NOT IN (SELECT id_prod_serv FROM tmp_det_fact)
                      ";
                      //Delete the table  tmp
                      $table_fact_det='factura_detalle';
                      $delete1 = _delete($table_fact_det, $where_clause1);
                      $drop1=" DROP TABLE tmp_det_fact";
                      $resultx=_query($drop1);
                    }//if
                    if ($insertar_numdoc && $insertar_fact && $insertar_fact_det) {
                      _commit(); // transaction is committed
                      $xdatos['typeinfo']='Success';
                      $xdatos['msg']='Tiquete Numero: <strong>'.$numero_doc.'</strong>  Guardado con Exito !';
                      $xdatos['process']='insert';
                      $xdatos['factura']=$id_factura;
                      $xdatos['numero_doc']=$numero_doc;
                      $xdatos['id_cliente'] = $id_cliente;
                      $xdatos['insertados']=" ultimo_numdoc:".$insertar_numdoc." factura :".$insertar_fact." factura detalle:".$insertar_fact_det." mov prod:".$insertar1." stock:".$insertar2 ;
                    } else {
                      _rollback(); // transaction rolls back
                      $xdatos['typeinfo']='Error';
                      $xdatos['msg']='Registro de Factura no pudo ser Actualizado !';
                      $xdatos['process']='noinsert';
                      $xdatos['insertados']=" ultimo_numdoc:".$insertar_numdoc." temporal :".$updates3." factura :".$insertar_fact." factura detalle:".$insertar_fact_det." mov prod:".$insertar1." stock:".$insertar2 ;
                    }
                    echo json_encode($xdatos);
                  }

                  function imprimir_fact()
                  {
                    $numero_doc = $_POST['numero_doc'];
                    $tipo_impresion= $_POST['tipo_impresion'];
                    $id_factura= $_POST['id_factura'];
                    $id_sucursal=$_SESSION['id_sucursal'];
                    $nombreape= $_POST['nombre_cliente'];
                    $direccion= $_POST['direccion'];
                    $nit= $_POST['nit'];
                    $nrc= $_POST['nrc'];

                    if ($tipo_impresion=='COF') {
                      $tipo_entrada_salida="FACTURA CONSUMIDOR";
                    }
                    if ($tipo_impresion=='TIK') {
                      $tipo_entrada_salida="TICKET";
                    }
                    if ($tipo_impresion=='CCF') {
                      $tipo_entrada_salida="CREDITO FISCAL";
                    }
                    //Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
                    $info = $_SERVER['HTTP_USER_AGENT'];
                    if (strpos($info, 'Windows') == true) {
                      $so_cliente='win';
                    } else {
                      $so_cliente='lin';
                    }

                    $sql_fact="SELECT * FROM factura WHERE id_factura='$id_factura'";
                    $result_fact=_query($sql_fact);
                    $nrows_fact=_num_rows($result_fact);
                    if ($nrows_fact>0) {
                      $table_fact= 'factura';
                      # code...
                      $form_data_fact = array(
                        'finalizada' => '1',
                        'impresa' => '1',
                        'num_fact_impresa'=>$numero_doc,
                      );

                      $where_clause="id_factura='$id_factura'";
                      $actualizar = _update($table_fact, $form_data_fact, $where_clause);
                    }
                    //cambiar numero documento impreso para mostrar en reporte kardex
                    /*$where_clause1="WHERE
                    tipo_entrada_salida='$tipo_entrada_salida'
                    AND numero_doc='$numero_doc'
                    AND fecha_movimiento='$fecha_movimiento'
                    ";

                    $table1= 'movimiento_producto';
                    $form_data1 = array(
                    'numero_doc'=>$id_factura,
                  );
                  $insertar1 = _update($table1, $form_data1, $where_clause1);*/

                  if ($tipo_impresion=='COF') {
                    $info_facturas=print_fact($id_factura, $tipo_impresion, $nit, $nrc, $nombreape,$direccion);
                  }
                  /*if ($tipo_impresion=='ENV') {
                    $info_facturas=print_envio($id_factura, $tipo_impresion);
                  }*/

                  if ($tipo_impresion=='CCF') {
                    $info_facturas=print_ccf_tml($id_factura, $tipo_impresion, $nit, $nrc, $nombreape,$direccion);
                  }
                  //directorio de script impresion cliente
                  $headers="";
                  $footers="";
                  if ($tipo_impresion=='TIK') {
                    $info_facturas=print_ticket($id_factura, $tipo_impresion);
                    $sql_pos="SELECT *  FROM sucursal  WHERE id_sucursal='$id_sucursal' ";
                    $result_pos=_query($sql_pos);
                    $row1=_fetch_array($result_pos);
                    $headers=$row1['descripcion']."|".Mayu($row1['direccion'])."|".$row1['giro']."|";
                    $footers="GRACIAS POR SU COMPRA, VUELVA PRONTO......"."|";
                  }

                  $sql_dir_print="SELECT *  FROM config_dir WHERE id_sucursal='$id_sucursal'";
                  $result_dir_print=_query($sql_dir_print);
                  $row_dir_print=_fetch_array($result_dir_print);
                  $dir_print=$row_dir_print['dir_print_script'];
                  $shared_printer_win=$row_dir_print['shared_printer_matrix'];
                  $shared_printer_pos=$row_dir_print['shared_printer_pos'];
                  $nreg_encode['shared_printer_win'] =$shared_printer_win;
                  $nreg_encode['shared_printer_pos'] =$shared_printer_pos;
                  $nreg_encode['dir_print'] =$dir_print;
                  $nreg_encode['facturar'] =$info_facturas;
                  $nreg_encode['sist_ope'] =$so_cliente;
                  $nreg_encode['headers'] =$headers;
                  $nreg_encode['footers'] =$footers;

                  echo json_encode($nreg_encode);
                }
                function agregar_cliente()
                {
                  //$id_cliente=$_POST["id_cliente"];
                  $nombre=$_POST["nombress"];
                  $dui=$_POST["dui"];
                  $tel1=$_POST["tel1"];
                  $tel2=$_POST["tel2"];


                  $var1=preg_match('/\x{27}/u', $nombre);
                  $var2=preg_match('/\x{22}/u', $nombre);
                  if ($var1==true || $var2==true) {
                    $nombre =stripslashes($nombre);
                  }
                  $sql_result=_query("SELECT * FROM cliente WHERE nombre='$nombre'");
                  $numrows=_num_rows($sql_result);
                  $row_update=_fetch_array($sql_result);
                  $id_cliente=$row_update["id_cliente"];
                  $name_cliente=$row_update["nombre"];


                  //'id_cliente' => $id_cliente,
                  $table = 'cliente';
                  $form_data = array(
                    'nombre' => $nombre,
                    'dui' => $dui,
                    'telefono1' => $tel1,
                    'telefono2' => $tel2,
                  );

                  if ($numrows == 0 && trim($nombre)!='') {
                    $insertar = _insert($table, $form_data);
                    $id_cliente=_insert_id();
                    if ($insertar) {
                      $xdatos['typeinfo']='Success';
                      $xdatos['msg']='Registro insertado con exito!';
                      $xdatos['process']='insert';
                      $xdatos['id_client']=  $id_cliente;
                    } else {
                      $xdatos['typeinfo']='Error';
                      $xdatos['msg']='Registro no insertado !';
                    }
                  } else {
                    $xdatos['typeinfo']='Error';
                    $xdatos['msg']='Registro no insertado !';
                  }
                  echo json_encode($xdatos);
                }
                function mostrar_datos_cliente()
                {
                  $id_cliente=$_POST['id_cliente'];

                  $sql="SELECT * FROM clientes
                  WHERE
                  id_cliente='$id_cliente'";
                  $result=_query($sql);
                  $count=_num_rows($result);
                  if ($count > 0) {
                    for ($i = 0; $i < $count; $i ++) {
                      $row = _fetch_array($result);
                      $id_cliente=$row["id_cliente"];
                      $nombre=$row["nombre"];
                      $apellido="";
                      $nit=$row["nit"];
                      $dui=$row["dui"];
                      $retiene=$row["retiene"];
                      $retiene10=$row["retiene10"];
                      $percibe=$row["percibe"];
                      $direccion=$row["direccion"];
                      $telefono1=$row["telefono1"];
                      $giro=$row["giro"];
                      $registro=$row["nrc"];

                    }
                  }
                  $xdatos['nit']= $nit;
                  $xdatos['registro']= $registro;
                  $xdatos['nombreape']=   $nombre." ".$apellido;
                  $xdatos['percepcion']=   $percibe;
                  $xdatos['retencion']=   $retiene;
                  $xdatos['retencion10']=   $retiene10;
                  echo json_encode($xdatos); //Return the JSON Array
                }
                ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                function consultar_stock1(){
                  $id_producto = $_REQUEST['id_producto'];
                  $id_usuario=$_SESSION["id_usuario"];
                  $id_sucursal=$_SESSION['id_sucursal'];


                  $iva=13/100;
                  $precio=0;

                  //if ($tipo =='PRODUCTO'){
                  //ojo !!!!!!!!!!!!!!!!!!!!!!
                  //utilidad teneindo precio venta y costo  : utlidad=(precio_venta-costo)/costo;
                  /*$sql1="SELECT producto.id_producto,producto.descripcion,producto.unidad,producto.exento,producto.id_posicion,
                  producto.utilidad_activa,producto.utilidad_seleccion,producto.porcentaje_utilidad1,producto.descripcion,
                  producto.porcentaje_utilidad2,producto.porcentaje_utilidad3,
                  producto.porcentaje_utilidad4,producto.imagen,producto.combo,producto.perecedero,
                  stock.stock,stock.costo_promedio,
                  stock.utilidad, stock.pv_base, stock.precio_mayoreo,  stock.porc_desc_base , stock.stock_minimo,
                  stock.pv_desc_base ,  stock.porc_desc_max ,  stock.pv_desc_max,
                  stock.precio_oferta,stock.fecha_ini_oferta,stock.fecha_fin_oferta
                  FROM producto JOIN stock ON producto.id_producto=stock.id_producto
                  WHERE producto.id_producto='$id_producto'
                  AND stock.id_sucursal='$id_sucursal'
                  ";*/
                  $sql1 = "SELECT p.id_producto, p.barcode, p.descripcion, p.estado, p.perecedero, p.exento, p.id_categoria, p.id_sucursal,s.id_stock,s.stock, s.id_sucursal, s.precio_unitario, s.costo_unitario FROM producto AS p, stock AS s WHERE p.id_producto = s.id_producto AND p.id_producto ='$id_producto' AND s.id_sucursal='$id_sucursal'";
                  $stock1=_query($sql1);
                  $row1=_fetch_array($stock1);
                  $nrow1=_num_rows($stock1);
                  if ($nrow1>0){
                    //$unidades=$row1['unidad'];
                    //$utilidad_activa=$row1['utilidad_activa'];
                    //$utilidad_seleccion=$row1['utilidad_seleccion'];
                    $perecedero=$row1['perecedero'];
                    $barcode = $row1["barcode"];
                    $descripcion = $row1["descripcion"];
                    $estado = $row1["estado"];
                    $perecedero = $row1["perecedero"];
                    $exento = $row1["exento"];
                    $id_stock = $row1["id_stock"];
                    $stock = $row1["stock"];
                    $precio_unitario = $row1["precio_unitario"];
                    $costo_unitario = $row1["costo_unitario"];

                    //precio de venta
                    $fecha_hoy=date("Y-m-d");
                    $fecha_hoy2=date("d-m-Y");

                    //consultar si es perecedero
                    $sql_existencia = "SELECT su.id_producto, su.cantidad, su.id_ubicacion, u.id_ubicacion, u.bodega  FROM stock_ubicacion as su, ubicacion as u WHERE su.id_producto = '$id_producto' AND su.id_ubicacion = u.id_ubicacion AND u.bodega != 1 ORDER BY su.id_su ASC";
                    $resul_existencia = _query($sql_existencia);
                    $cuenta_existencia = _num_rows($resul_existencia);
                    $existencia_real = 0;
                    if($cuenta_existencia > 0)
                    {
                      while ($row_ex = _fetch_array($resul_existencia))
                      {
                        $cantidad_ex = $row_ex["cantidad"];
                        $existencia_real += $cantidad_ex;
                      }
                    }
                    $fecha_caducidad="0000-00-00";
                    $stock_fecha=0;
                    if($perecedero==1){
                      $sql_perecedero="SELECT id_lote, id_producto, fecha_entrada, precio, cantidad, estado, numero, id_sucursal, vencimiento, referencia FROM lote WHERE id_producto='$id_sucursal' AND id_sucursal='$id_sucursal' AND estado='VIGENTE' AND (vencimiento>='$fecha_hoy' OR  vencimiento='0000-00-00') ORDER BY vencimiento ASC";
                      $result_perecedero=_query($sql_perecedero);
                      $array_fecha=array();
                      $array_stock=array();
                      $nrow_perecedero=_num_rows($result_perecedero);
                      if($nrow_perecedero>0){
                        for ($i=0;$i<$nrow_perecedero;$i++){
                          $row_perecedero=_fetch_array($result_perecedero);
                          //$costos_pu=array($pu1,$pu2,$pu3,$pu4);
                          $entrada=$row_perecedero['cantidad'];
                          $id_lote_prod=$row_perecedero['id_lote'];
                          $fecha_caducidad=$row_perecedero['vencimiento'];
                          if($fecha_caducidad=="")
                          $fecha_caducidad="0000-00-00";
                          $fecha_caducidad=ED($fecha_caducidad);
                          $stock_fecha=$entrada-$salida;
                          $array_fecha[] =$id_lote_prod."|".$fecha_caducidad;
                          $array_stock[] =$id_lote_prod."|".$fecha_caducidad."|".$stock_fecha;
                        }
                      }

                    }
                    else{
                      $array_fecha="";
                      $array_stock="";
                    }
                  }
                  //$ubicacion=ubicacionn($id_posicion);
                  //si no hay stock devuelve cero a todos los valores !!!
                  if ($nrow1==0){
                    $existencias=0;
                    $precio_venta=0;
                    $costos_pu=array(0,0,0,0);
                    $precios_vta=array(0,0,0,0);
                    $cp=0;
                    $iva=0;
                    $unidades=" ";
                    $imagen='';
                    $combo=0;
                    $fecha_caducidad='0000-00-00';
                    $stock_fecha=0;
                    $oferta=0;
                  }
                  //}
                  //$xdatos['mayoreo'] = $mayoreo;
                  /*if($mayoreo)
                  {
                  $sql = _query("SELECT precio FROM precio_producto WHERE id_producto='$id_producto' AND '1' BETWEEN desde AND hasta");
                  if(_num_rows($sql)>0)
                  {
                  $datos = _fetch_array($sql);
                  $precio = $datos["precio"];
                  $xdatos["precio"] = $precio;
                }
                else
                {
                $xdatos["precio"] = 0;
              }
            }
            if(!$mayoreo && $precio>0)
            {

            $xdatos["typeinfo"] = 'Success';
          }*/
          /*inicio modificacion presentacion*/
          $i=0;
          $unidadp=0;
          $preciop=0;
          $descripcionp=0;

          $sql_p=_query("SELECT presentacion.nombre, presentacion_producto.descripcion,presentacion_producto.id_presentacion,presentacion_producto.unidad,presentacion_producto.precio FROM presentacion_producto JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion WHERE presentacion_producto.id_producto='$id_producto' AND presentacion_producto.activo=1");
          $select="<select class='sel form-control'>";
          while ($row=_fetch_array($sql_p)) {
            # code...
            if ($i==0) {
              # code...
              $unidadp=$row['unidad'];
              $preciop=$row['precio'];
              $descripcionp=$row['descripcion'];
            }


            $select.="<option value='$row[id_presentacion]'>$row[nombre]</option>";
            $i=$i+1;

          }
          $select.="</select>";
          /*fin modificacion presentacion*/

          //$precio_venta=round($precio_venta,2);
          $xdatos['existencias'] = $existencia_real;
          //$xdatos['precio_venta'] = $precio_unitario;
          //$xdatos['costo_prom'] = $cp;
          //$xdatos['iva'] = $iva;
          //$xdatos['unidades'] = $unidades;
          //$xdatos['imagen'] = $imagen;
          //$xdatos['combo'] = $combo;
          $xdatos['fecha_caducidad'] = $fecha_caducidad;
          $xdatos['stock_fecha'] =$stock_fecha;
          //$xdatos['oferta'] =$oferta;
          //$xdatos['precio_oferta'] =$precio_oferta;
          //$xdatos['porc_desc_base']=$porc_desc_base;
          //$xdatos['porc_desc_max']=$porc_desc_max;
          $xdatos['perecedero']=$perecedero;
          $xdatos['fechas_vence'] = $array_fecha;
          $xdatos['stock_vence'] = $array_stock;
          //$xdatos['fecha_ini_oferta']=$fecha_ini_oferta;
          //$xdatos['fecha_fin_oferta']=$fecha_fin_oferta2;
          $xdatos['fecha_hoy']= $fecha_hoy;
          //$xdatos['precios_vta']= $precios;
          //$xdatos['ubicacion']= $ubicacion;
          $xdatos['descripcion']= $descripcion;
          $xdatos['select']= $select;
          $xdatos['preciop']= $preciop;
          $xdatos['unidadp']= $unidadp;
          $xdatos['descripcionp']= $descripcionp;

          echo json_encode($xdatos); //Return the JSON Array
        }
        function cons_rank()
        {
          $id_sucursal = $_SESSION["id_sucursal"];



          $id_producto=$_POST['id_producto'];
          $id_presentacion=$_POST['id_presentacion'];
          $cantidad=$_POST['cantidad'];

          $id_usuario=$_SESSION["id_usuario"];
          $r_precios=_fetch_array(_query("SELECT precios FROM usuario WHERE id_usuario=$id_usuario"));
          $precios=$r_precios['precios'];
          $limit="";
          if ($precios==0) {
            $limit="AND $cantidad >= desde";
          }

          $select_rank="<select class='sel_r precio_r form-control'>";
          $sql_rank=_query("SELECT id_prepd,desde,hasta,precio
            FROM presentacion_producto_precio
            WHERE id_presentacion=$id_presentacion
            AND id_sucursal=$id_sucursal
            AND precio!=0
            $limit
            ORDER BY precio DESC
            ");
            $xc = 0;
            $preciop = 0;
            if(_num_rows($sql_rank)>0)
            {
              while ($rowr=_fetch_array($sql_rank))
              {
                $select_rank.="<option value='$rowr[precio]'";
                if(!$xc)
                {
                  $select_rank.=" selected ";
                  $preciop=$rowr['precio'];
                  $xc = 1;
                }
                $select_rank.=">$rowr[precio]</option>";
              }
            }
            else
            {
              $sqlq = _query("SELECT precio FROM presentacion_producto WHERE id_presentacion='$id_presentacion'");
              $datsq = _fetch_array($sqlq);
              $preciop=$datsq['precio'];
              $select_rank.="<option value='$datsq[precio]' selected>$datsq[precio]</option>";
            }
            $select_rank.="</select>";
            $xdatos["precio"] = $preciop;
            $xdatos["precios"] = $select_rank;
            echo json_encode($xdatos); //Return the JSON Array
          }
          function getpresentacion()
          {
            $id_sucursal=$_SESSION['id_sucursal'];
            $id_presentacion =$_REQUEST['id_presentacion'];
            $cant =$_REQUEST['cant'];
            $sql=_fetch_array(_query("SELECT * FROM presentacion_producto WHERE id_presentacion=$id_presentacion"));
            $precio=$sql['precio'];
            $unidad=$sql['unidad'];
            $descripcion=$sql['descripcion'];
            $id_producto=$sql['id_producto'];
            $sql_e=_fetch_array(_query("SELECT exento FROM producto WHERE id_producto=$id_producto"));
            $exento=$sql_e['exento'];

            $select_rank="<select class='sel_r precio_r form-control'>";
            $xc=0;
            $id_sucursal = $_SESSION['id_sucursal'];

            $id_usuario=$_SESSION["id_usuario"];
            $r_precios=_fetch_array(_query("SELECT precios FROM usuario WHERE id_usuario=$id_usuario"));
            $precios=$r_precios['precios'];
            $limit="";
            if ($precios==0) {
              $limit="AND '$cant' >= desde";
            }

            $sql_rank=_query("SELECT id_prepd,desde,hasta,precio
                              FROM presentacion_producto_precio
                              WHERE id_presentacion=$id_presentacion
                              AND id_sucursal=$id_sucursal
                              $limit
                              AND precio>0
                              ORDER BY precio DESC");

            while ($rowr=_fetch_array($sql_rank))
            {
              # code...
              $select_rank.="<option value='$rowr[precio]'";
              if(!$xc)
              {
                $select_rank.=" selected ";
                $precio=$rowr['precio'];
                $xc=1;
              }
              $select_rank.=">$rowr[precio]</option>";
            }
            if (_num_rows($sql_rank)==0) {
              # code...
              $select_rank.="<option value='$precio'";
              $select_rank.="selected";
              $select_rank.=">$precio</option>";
            }
            $select_rank.="</select>";

            $des = "<input type='text' id='ss' class='txt_box form-control' value='".$descripcion."' readonly>";
            $xdatos['precio']=$precio;

            if ($exento==1) {
              # code...
              $xdatos['preciop_s_iva']=$precio;
            }
            else {
              # code...
              $sqkl=_fetch_array(_query("SELECT iva FROM sucursal WHERE id_sucursal=$id_sucursal"));
              $iva=$sqkl['iva']/100;
              $iva=1+$iva;
              $xdatos['preciop_s_iva']= round(($precio/$iva),8,PHP_ROUND_HALF_DOWN);
            }
            $xdatos['unidad']=$unidad;
            $xdatos['descripcion']=$des;
            $xdatos['descripcion']=$des;
            $xdatos['select_rank']=$select_rank;
            echo json_encode($xdatos);
          }
          function insertar_preventa()
          {
            //date_default_timezone_set('America/El_Salvador');
            $id_factura=$_POST['id_factura'];
            $fecha_movimiento= $_POST['fecha_movimiento'];
            $id_cliente=$_POST['id_cliente'];

            $id_vendedor=$_SESSION['id_usuario'];
            $cuantos = $_POST['cuantos'];
            $array_json=$_POST['json_arr'];
            //  IMPUESTOS
            $total_percepcion= $_POST['total_percepcion'];

          	$subtotal=$_POST['subtotal'];
            $sumas=$_POST['sumas'];
            $suma_gravada=$_POST['suma_gravada'];
            $iva= $_POST['iva'];
            $retencion= $_POST['retencion'];
            $venta_exenta= $_POST['venta_exenta'];
            $total_menos_retencion=$_POST['total'];
            $total = $retencion+$_POST['total'];

            $id_empleado=$_SESSION["id_usuario"];
            $id_sucursal=$_SESSION["id_sucursal"];
            $fecha_actual = date('Y-m-d');
            $tipoprodserv = "PRODUCTO";

            $insertar_fact=false;
            $insertar_fact_dett=true;
            $insertar_numdoc =false;

            $hora=date("H:i:s");
            $xdatos['typeinfo']='';
            $xdatos['msg']='';
            $xdatos['process']='';

            _begin();

          	$a=1;
          	$b=1;
          	$c=1;

            if($id_factura==0)
            {
              $sql="SELECT ref FROM correlativo WHERE id_sucursal='$id_sucursal'";
              $result= _query($sql);
              $rows=_fetch_array($result);
              $ult=$rows['ref']+1;
              $numero_doc = str_pad($ult,7,"0",STR_PAD_LEFT)."_REF";
              $table_numdoc="correlativo";
              $data_numdoc = array(
                'ref' => $ult,
              );
              $where_clause_n="WHERE  id_sucursal='$id_sucursal'";
              $insertar_numdoc = _update($table_numdoc, $data_numdoc, $where_clause_n);

            	if (!$insertar_numdoc) {
            		# code...
            		$a=0;
            	}
            }
            else
            {
              $sql_num=_fetch_array(_query("SELECT * FROM factura where id_factura=$id_factura"));
              $numero_doc=$sql_num['numero_doc'];
              $ult=$sql_num['numero_ref'];
            }


          	$abono=0;
          	$saldo=0;
          	$tipo_documento=$_POST['tipo_impresion'];
          	$tipo_entrada_salida='NUM. REFERENCIA INTERNA';

            if ($id_factura=="0") {
              # code...
              $table_fact= 'factura';
              $form_data_fact = array(
                'id_cliente' => $id_cliente,
            		'fecha' => $fecha_movimiento,
            		'numero_doc' => $numero_doc,
                'referencia' => $numero_doc,
                'numero_ref' => $ult,
            		'subtotal' => $subtotal,
            		'sumas'=>$sumas,
                'suma_gravado'=>$suma_gravada,
            		'iva' =>$iva,
            		'retencion'=>$retencion,
                'venta_exenta'=>$venta_exenta,
                'total_menos_retencion'=>$total_menos_retencion,
            		'total' => $total,
            		'id_usuario'=>$id_empleado,
            		'id_empleado' => $id_vendedor,
            		'id_sucursal' => $id_sucursal,
            		'tipo' => $tipo_entrada_salida,
            		'hora' => $hora,
            		'finalizada' => '0',
            		'abono'=>$abono,
            		'saldo' => $saldo,
            		'tipo_documento' => $tipo_documento,
              );
              $insertar_fact = _insert($table_fact,$form_data_fact );
              $id_fact= _insert_id();

              if (!$insertar_fact) {
                # code...
                $b=0;
              }
            }
            else {
              # code...
              $table_fact= 'factura';
              $form_data_fact = array(
                'id_cliente' => $id_cliente,
            		'fecha' => $fecha_movimiento,
            		'numero_doc' => $numero_doc,
                'referencia' => $numero_doc,
                'numero_ref' => $ult,
            		'subtotal' => $subtotal,
            		'sumas'=>$sumas,
                'suma_gravado'=>$suma_gravada,
            		'iva' =>$iva,
            		'retencion'=>$retencion,
                'venta_exenta'=>$venta_exenta,
                'total_menos_retencion'=>$total_menos_retencion,
            		'total' => $total,
            		'id_usuario'=>$id_empleado,
            		'id_empleado' => $id_vendedor,
            		'id_sucursal' => $id_sucursal,
            		'tipo' => $tipo_entrada_salida,
            		'hora' => $hora,
            		'finalizada' => '0',
            		'abono'=>$abono,
            		'saldo' => $saldo,
            		'tipo_documento' => $tipo_documento,
              );
              $whereclause="id_factura='".$id_factura."'";
              $insertar_fact = _update($table_fact,$form_data_fact,$whereclause );
              $id_fact= $id_factura;

              if (!$insertar_fact) {
                # code...
                $b=0;
              }
              $table="factura_detalle";
              $where_clause="id_factura='".$id_fact."'";
              $delete=_delete($table,$where_clause);
              if (!$delete) {
                # code...
                $b=0;
              }

            }



            if ($cuantos>0)
          	{
          		    $array = json_decode($array_json, true);
          		    foreach ($array as $fila)
          		    {
          					$id_producto=$fila['id'];
          					$unidades=$fila['unidades'];
          					$subtotal=$fila['subtotal'];
                    $cantidad=$fila['cantidad'];
          					$id_presentacion=$fila['id_presentacion'];
          					$cantidad_real=$cantidad*$unidades;
          					$exento=$fila['exento'];
          					$precio_venta=$fila['precio'];

          					$table_fact_det= 'factura_detalle';
          					$data_fact_det = array(
          						'id_factura' => $id_fact,
          						'id_prod_serv' => $id_producto,
          						'cantidad' => $cantidad_real,
          						'precio_venta' => $precio_venta,
          						'subtotal' => $subtotal,
          						'tipo_prod_serv' => $tipoprodserv,
          						'id_empleado' => $id_empleado,
          						'id_sucursal' => $id_sucursal,
          						'fecha' => $fecha_movimiento,
          						'id_presentacion'=> $id_presentacion,
          						'exento' => $exento,
          					);
          					$insertar_fact_det = _insert($table_fact_det,$data_fact_det );
          					if (!$insertar_fact_det) {
          						# code...
          						$c=0;
          					}

          				} //foreach ($array as $fila){
                  if ($a&&$b&&$c)
                  {
                    _commit(); // transaction is committed
                    $xdatos['typeinfo']='Success';
                    $xdatos['msg']='Referenca Numero: <strong>'.$numero_doc.'</strong>  Guardado con Exito !';
                    $xdatos['referencia']=$ult;
                    $xdatos['tot']=number_format($total,2);
                  }
                  else
                  {
                    _rollback(); // transaction rolls back
                    $xdatos['typeinfo']='Error';
                    $xdatos['msg']='Registro no pudo ser ingresado!'.$a."-".$b."-".$c;
                  }
              }
                echo json_encode($xdatos);
          		}
              function borrar_preventa()
              {
                _begin();
                $id_factura=$_REQUEST['id_factura'];
                $table='factura';
                $where_clause="id_factura='".$id_factura."'";
                $delete=_delete($table,$where_clause);
                if($delete)
                {
                  $table="factura_detalle";
                  $where_clause="id_factura='".$id_factura."'";
                  $delete=_delete($table,$where_clause);
                  if($delete)
                  {
                    _commit();
                    $xdatos['typeinfo']="Success";
                    $xdatos['msg']="Registro eliminado correctamente";
                  }
                  else
                  {
                    _rollback();
                    $xdatos['typeinfo']="Error";
                    $xdatos['msg']="Error al insertar el registro";
                  }
                }
                else{
                  _rollback();
                  $xdatos['typeinfo']="Error";
                  $xdatos['msg']="Error al insertar el registro";

                }
                echo json_encode($xdatos);
              }
          //functions to load
          if (!isset($_REQUEST['process'])) {
            initial();
          }
          //else {
          if (isset($_REQUEST['process'])) {
            switch ($_REQUEST['process']) {
              case 'formEdit':
                initial();
                break;
                case 'insert':
                insertar();
                break;
                case 'insertar_venta':
                insertar_venta();
                break;
                case 'mostrar_datos_cliente':
                mostrar_datos_cliente();
                break;
                case 'consultar_stock':
                consultar_stock();
                break;
                case 'consultar_servicio':
                consultar_servicio();
                break;
                case 'cargar_empleados':
                cargar_empleados();
                break;
                case 'cargar_precios':
                cargar_precios();
                break;
                case 'total_texto':
                total_texto();
                break;

                case 'imprimir_fact':
                imprimir_fact();
                break;
                case 'print2':
                print2(); //Generacion de los datos de factura que se retornan para otro script que imprime!!!
                break;
                case 'mostrar_numfact':
                mostrar_numfact();
                break;
                case 'reimprimir':
                reimprimir();
                break;
                case 'agregar_cliente':
                agregar_cliente();
                break;
                case 'cargar_data':
                cargar_data();
                break;
                case 'consultar_stock1':
                consultar_stock1();
                break;
                case 'cons_rank':
                cons_rank();
                break;
                case 'getpresentacion':
                getpresentacion();
                break;
                case 'insert_preventa':
                insertar_preventa();
                break;
                case 'borrar_preventa':
                borrar_preventa();
                break;
              }

              //}
            }
            ?>
