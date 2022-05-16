<?php
include_once "_core.php";

function initial()
{
    $_PAGE = array();
    $_PAGE ['title'] = 'Ajuste de Inventario de Producto';
    $_PAGE ['links'] = null;
    $_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/jqGrid/ui.jqgrid.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

    include_once "header.php";
    include_once "main_menu.php";
    $id_sucursal=$_SESSION['id_sucursal'];
    $sql="SELECT * FROM producto";

    $result=_query($sql);
    $count=_num_rows($result);
    //permiso del script
    $id_user=$_SESSION["id_usuario"];
    $admin=$_SESSION["admin"];
    $uri = $_SERVER['SCRIPT_NAME'];
    $filename=get_name_script($uri);
    $links=permission_usr($id_user, $filename);
    //permiso del script
    if ($links!='NOT' || $admin=='1') {
        ?>

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-2"></div>
	</div>
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
			<div class="ibox ">
					<div class="ibox-content">
			<?php
                //1 AVERIA
                $fecha_actual=date("Y-m-d");
        echo "<div class='row' id='form_averia'>";
        echo "<header><h4>Ajuste</h4></header>";
        echo " <div class='col-lg-6'>";
        echo "<div class='form-group has-info single-line'><label>Fecha:</label> <input type='text' class='datepick form-control' value='$fecha_actual' id='fecha1' name='fecha1'></div>";
        echo "</div>";
        echo "</div>"; ?>
				<div class="row" id='buscador'>
						<div class="col-lg-6">
							<div class='form-group has-info single-line'><label>Buscar Producto o Servicio</label>
								<input type="text" id="producto_buscar" name="producto_buscar" size="20" class="producto_buscar form-control" placeholder="Ingrese nombre de producto"  data-provide="typeahead">
						</div>
					</div>

				</div>
				<div class="ibox">
					<div class="row">
						<div class="ibox-content">
						<!--load datables estructure html-->
						<header>
							<h4 class="text-navy">Salida de Inventario de Productos</h4>
						</header>
						<section>
							<table class="table table-striped table-bordered table-condensed" id="inventable">
								<thead>
									<tr>
										<th>Id Producto</th>
										<th>Nombre</th>
										<th>Existencias</th>
			              <th>Cant. G</th>
                    <th>SubCant</th>
                    <th>Observaciones.</th>
										<th>Acci&oacute;n</th>
                    <!--th>Id Producto</th>
                    <th>Nombre</th>
                    <th>Prec. Compr. Present.</th>
                    <th>Precio Vta. Present.</th>
                    <th>Cant. G</th>
                    <th>SubCant</th>
                    <th>Fecha Caduca</th>
                    <th>Acci&oacute;n</th-->
									</tr>
								</thead>

								<tfoot>
									<tr>
									<td></td>
									<td>Total Salida <strong>$</strong></td>
									<td id='total_dinero'>$0.00</td>
									<td colspan=2>Total Producto de Salida</td>
									<td id='totcant'>0.00</td>
									<!--td></td-->
									</tr>
								</tfoot>
							<tbody>

							</tbody>
						</table>
						 <input type="hidden" name="autosave" id="autosave" value="false-0">
						 <input type='hidden' name='urlscript' id='urlscript' value='<?php echo $filename; ?>'>
					</section>
						<input type="hidden" name="process" id="process" value="insert"><br>
            <div>
   					<input type="submit" id="submit1" name="submit1" value="Guardar" class="btn btn-primary m-t-n-xs" />
  			</div>
              </form>
                </div>
              </div>
                    </div>
                    </div><!--div class='ibox-content'-->
              </div>
            </div>
   </div>
        </div>

<?php
include_once("footer.php");

        //echo "<script src='js/plugins/typehead/bootstrap3-typeahead.js'></script>";
        echo "<script src='js/funciones/ajuste_inventario.js'></script>";
    } //permiso del script
    else {
        echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
    }
}

function insertar()
{
    $cuantos = $_POST['cuantos'];
    $stringdatos = $_POST['stringdatos'];
    $fecha_movimiento= $_POST['fecha_movimiento'];
    $totcant = $_POST['totcant'];
    $fecha_hoy=date('d-m-Y');
    $id_sucursal=$_SESSION['id_sucursal'];
    $id_sucursal_destino=$id_sucursal;
    $tipo_entrada_salida='AJUSTE INVENTARIO';
    $insertar1=false;
    $insertar2=false;
    $insertar3=false;
    $insertar4=false;
    $id='1';
    if ($cuantos>0) {
        $listadatos=explode('#', $stringdatos);
        for ($i=0;$i<$cuantos ;$i++) {
            //  StringDatos += id_producto+ "|" +existencias + "|" + cantidad + "|" + subcantidad+ observaciones + "#";
            list($id_producto, $existencias_actuales, $cantidad, $subcantidad, $unidad, $observaciones)=explode('|', $listadatos[$i]);
           //entrada de cantidades y subcantidades
           $nueva_cantidad=0;
            $nueva_cantidad=$cantidad*$unidad+$subcantidad;
            $diferencia=$nueva_cantidad-$existencias_actuales;

            //STOCK
            $sql2="select producto.id_producto,producto.unidad,stock.stock, stock.costo_promedio,producto.perecedero
            from producto,stock
            Where producto.id_producto='$id_producto'
            and producto.id_producto=stock.id_producto
            AND  stock.id_sucursal='$id_sucursal'";
            $stock2=_query($sql2);
            $row2=_fetch_array($stock2);
            $nrow2=_num_rows($stock2);
            $unidad=$row2['unidad'];
            $existencias=$row2['stock'];
            $perecedero=$row2['perecedero'];
            $costo_promedio=$row2['costo_promedio'];

            $table2= 'stock';
            $form_data2 = array(
            'id_producto' => $id_producto,
            'stock' =>  $nueva_cantidad,
            'id_sucursal'=>$id_sucursal
            );
            if ($nrow2>0) {
                $where_clause2=" WHERE id_producto='$id_producto' AND  id_sucursal='$id_sucursal'";
                $insertar2 = _update($table2, $form_data2, $where_clause2);
            }
            //Lote revisarlos bienpor lo de las diferencias que quedaran en cada lote!!!!!!! 18 ago 2017
          $sql_lote="SELECT id_lote_prod, id_producto, fecha_entrada, fecha_caducidad, entrada,
          salida, estado, numero_doc, id_sucursal
          FROM lote
          WHERE id_producto='$id_producto'
          AND id_sucursal='$id_sucursal'
          AND entrada>=salida
          AND estado='VIGENTE'
          ORDER BY fecha_caducidad LIMIT 1";
            $result_lote=_query($sql_lote);

            $table_lote='lote';
            $nrow_lote=_num_rows($result_lote);
            $fecha_mov=ED($fecha_movimiento);

            if ($nrow_lote>0) {
                for ($j=0;$j<$nrow_lote;$j++) {
                    $row_lote=_fetch_array($result_lote);
                    $entrada=$row_lote['entrada'];
                    $salida=$row_lote['salida'];
                    $fecha_caducidad=$row_lote['fecha_caducidad'];
                    $id_lote_prod=$row_lote['id_lote_prod'];
                    $fecha_caducidad=ED($fecha_caducidad);

              //$stock_lote=$entrada-$salida;
              $diferencia0=$nueva_cantidad-$entrada;
                    $diferencia1=$nueva_cantidad-$salida;
                    $diferencia2=$entrada-$salida;//10-9=1
              //nueva cantidad entrada menor a entrada y a la vez a salida
              $cant_entra=$nueva_cantidad;
                    $cant_sale=0;
                    $estado='VIGENTE';

                    $where_clause_lote="WHERE id_producto='$id_producto'
                AND id_sucursal='$id_sucursal'
                AND entrada>=salida
                AND id_lote_prod='$id_lote_prod'";
                    $form_data_lote = array(
              'salida' => $cant_sale,
              'entrada' => $cant_entra,
              'estado' => $estado
              );
                    $insertar4 = _update($table_lote, $form_data_lote, $where_clause_lote);
               //sirve para justes de inventario y para movimiento_producto
               if ($diferencia==0) {
                   $entrada_act=0;
                   $salida_act=0;
                   $observaciones="POR AJUSTE DE INVENTARIO ".$fecha_hoy;
                   $estado='FINALIZADO';
               }
                    if ($diferencia>0) {
                        $entrada_act=$diferencia;
                        $salida_act=0;
                        $observaciones="ENTRADA POR AJUSTE DE INVENTARIO ".$fecha_hoy;
                        $tipo_entrada_salida='ENTRADA POR AJUSTE DE INVENTARIO';
                        $estado='VIGENTE';
                   //$stock_ajuste=$diferencia;
                    }
                    if ($diferencia<0) {
                        $observaciones="SALIDA POR AJUSTE DE INVENTARIO ".$fecha_hoy;
                        $tipo_entrada_salida='SALIDA POR AJUSTE DE INVENTARIO';
                        $entrada_act=0;
                        $salida_act=abs($diferencia);
                        $estado='VIGENTE';
                    }
            //ajuste
              $sql3="SELECT id_ajuste, id_producto, id_stock, stock_actual, conteo_fisico, diferencia,
              fecha, observaciones FROM ajuste_inventario
              WHERE id_producto='$id_producto' AND  fecha='$fecha_movimiento'
              AND id_sucursal='$id_sucursal'
              AND  id_lote_prod='$id_lote_prod'";
                    $stock3=_query($sql3);
                    $row3=_fetch_array($stock3);
                    $nrow3=_num_rows($stock3);

                    $table3='ajuste_inventario';
                    $form_data3 = array(
                          'id_producto' => $id_producto,
                          'stock_actual' => $existencias_actuales,
                          'conteo_fisico' => $nueva_cantidad,
                          'diferencia' => $diferencia,
                          'fecha'=>$fecha_movimiento,
                          'observaciones'=>$observaciones,
                          'id_sucursal'=>$id_sucursal,
                          'id_lote_prod'=>$id_lote_prod
                          );
                    if ($nrow3==0) {
                        $insertar3 = _insert($table3, $form_data3);
                    }
                    if ($nrow3>0) {
                        $where_clause3="WHERE  id_producto='$id_producto' and fecha='$fecha_movimiento'
                              AND id_sucursal='$id_sucursal' AND  id_lote_prod='$id_lote_prod'";
                        $insertar3 = _update($table3, $form_data3, $where_clause3);
                    }
                      //movimiento producto
                          $sql1="select * from movimiento_producto
                          where id_producto='$id_producto'
              			      and tipo_entrada_salida='$tipo_entrada_salida'
                          and fecha_movimiento='$fecha_movimiento'
              			      AND id_sucursal_origen='$id_sucursal'
                          AND  id_lote_prod='$id_lote_prod'";
                    $stock1=_query($sql1);
                    $row1=_fetch_array($stock1);
                    $nrow1=_num_rows($stock1);
                    $table1= 'movimiento_producto';
                    $entrada_mov=$row1["entrada"];
                    $salida_mov=$row1["salida"];

                    if ($nrow1==0) {
                        $form_data1 = array(
                            'id_producto' => $id_producto,
                            'fecha_movimiento' => $fecha_movimiento,
                            'entrada' => $entrada_act,
                            'salida' => $salida_act,
                            'stock_anterior' =>$existencias_actuales,
                  					'stock_actual' => $nueva_cantidad,
                            'observaciones' =>$observaciones,
                            'tipo_entrada_salida' =>$tipo_entrada_salida,
                            'precio_compra' => $costo_promedio,
                            'id_sucursal_origen'=>$id_sucursal,
                              'id_lote_prod'=>$id_lote_prod
                            );
                        $insertar1 = _insert($table1, $form_data1);
                    }
                    if ($nrow1>0) {
                        if ($entrada_act>0) {
                            $entrada_act=$entrada_act+$entrada_mov;
                            $form_data1 = array(
                              'entrada' => $entrada_act,

                              );
                        }
                        if ($salida_act>0) {
                            $salida_act=$entrada_act+$salida_mov;
                            $form_data1 = array(
                              'salida' => $salida_act
                              );
                        }

                        $where_clause1="WHERE  id_producto='$id_producto' and tipo_entrada_salida='$tipo_entrada_salida'
                        							and fecha_movimiento='$fecha_movimiento' and id_sucursal_origen='$id_sucursal'
                                      AND  id_lote_prod='$id_lote_prod'";

                        $insertar1 = _update($table1, $form_data1, $where_clause1);
                    }
            //si la cantidad vendida no se pasa de la existencia de x lote perecedero  se sale del bucle for
            //if ($diferencia==0)
            //  break;
                }
            }
        }//for
    }//if

    if ($insertar1 && $insertar2 && $insertar3 && $insertar4) {
        $xdatos['typeinfo']='Success';
        $xdatos['msg']='Registro de Inventario Actualizado !';
        $xdatos['process']='insert';
        $xdatos['insertados']=$insertar1;
    } else {
        $xdatos['typeinfo']='Error';
        $xdatos['msg']='Registro de Inventario no pudo ser Actualizado !';
    }
    $xdatos['insertados']="ins1:".$insertar1." ins2: ".$insertar2." ins3:".$insertar3." ins4:".$insertar4 ;

    echo json_encode($xdatos);
}
function consultar_stock()
{
    $id_producto = $_REQUEST['id_producto'];
    $id_usuario=$_SESSION["id_usuario"];
    $id_sucursal=$_SESSION['id_sucursal'];

    $sql1="SELECT producto.id_producto,producto.descripcion,producto.unidad,stock.stock,
      stock.costo_promedio,producto.perecedero
      FROM producto JOIN stock ON producto.id_producto=stock.id_producto
      WHERE producto.id_producto='$id_producto'";

/*
    $sql1="SELECT producto.id_producto,producto.descripcion,producto.unidad,
    lote.id_lote_prod, lote.fecha_entrada, lote.fecha_caducidad,
lote.entrada,lote.salida,(lote.entrada-lote.salida) as stock,
stock.costo_promedio,producto.perecedero
FROM producto
JOIN lote ON producto.id_producto=lote.id_producto
JOIN stock ON producto.id_producto=stock.id_producto
WHERE  lote.entrada>lote.salida
AND producto.id_producto='$id_producto'";
*/
    $stock1=_query($sql1);

    $cuantos=_num_rows($stock1);
    $array_prod=array();
    for ($j=0;$j<$cuantos ;$j++) {
        $row1=_fetch_array($stock1);
        $unidades=round($row1['unidad'], 2);
        $cp=round($row1['costo_promedio'], 2);
        $perecedero=$row1['perecedero'];
        $existencias=$row1['stock'];
        //$id_lote_prod=$row1['id_lote_prod'];
        //$fecha_caducidad=$row1['fecha_caducidad'];

        if ($unidades>0) {
            $precio_unit=$cp/$unidades;
        } else {
            $precio_unit=$cp;
        }


        $fecha_hoy=date("Y-m-d");
        //consultar si es perecedero

        $stock_lote=0;
        if ($perecedero==0) {
            $fecha_caducidad="";
            $stock_lote="";
        }
        $precio_unit=round($precio_unit, 2);

        $array_prod[]=$id_producto."|".$cp."|".$unidades."|".$existencias;
    }
    $xdatos['filass']= $array_prod;
    echo json_encode($xdatos); //Return the JSON Array
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
    }

    //}
}
?>
