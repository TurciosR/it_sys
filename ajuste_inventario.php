<?php
include_once "_core.php";

function initial() {


	$_PAGE = array ();
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
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user, $filename);
	//permiso del script
	if ($links!='NOT' || $admin=='1' ){
?>

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-2"></div>
	</div>
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
				<!--Primero si e si es inv. inicial ,factura de compra, compra caja chica, traslado de otra sucursal; luego Registrar No. de Factura , lote, proveedor -->
				<div class="ibox ">
					<div class="ibox-content">


				<?php

				$fecha_actual=date("Y-m-d");
				$sql_stock="SELECT producto.id_producto,producto.descripcion,producto.unidad,
				stock.stock,stock.costo_promedio
				FROM producto,stock
				WHERE producto.id_producto=stock.id_producto
				AND stock.id_sucursal='$id_sucursal'
				order by producto.id_producto";
				$result=mysql_query($sql_stock);
				$count=mysql_num_rows($result);
				echo "<div class='row' id='form_fecha'>";
				echo "<header><h4>Ajuste de Inventario</h4></header>";
						echo " <div class='col-md-4'>";
							echo "<div class='form-group has-info single-line'><label>Fecha:</label> <input type='text' class='datepick form-control' value='$fecha_actual' id='fecha1' name='fecha1'></div>";
						echo "</div>";
				echo "</div>";

		?>
				<div class="row" id='buscador'>
						<div class="col-lg-6">
							<div class='form-group has-info single-line'><label>Buscar Producto o Servicio</label>
								<input type="text" id="producto_buscar" name="producto_buscar" size="20" class="producto_buscar form-control" placeholder="Ingrese nombre de producto"  data-provide="typeahead">
						</div>
					</div>

				</div>
				<!--div class="ibox"-->
					<div class="row">
						<!--div class="ibox-content"-->
						<header>
							<h4 class="text-navy">Ajuste de Inventario de Productos</h4>
						</header>
						<section>
							<!--table class="table table-striped table-bordered table-condensed"  id="inventable"-->
							<table class="table table-striped table-bordered table-condensed" id="editable">
								<thead>
									<tr>
										<th>Id Producto</th>
										<th>Nombre</th>
										<th>Unidades / Presentacion</th>
										<th>Costo Prom. Unit. $</th>
										<th>Existencias (Sistema)</th>
										<th>Conteo Físico</th>
										<th>Diferencia</th>
										<th>Lote/F. Caduc.</th>
										<th>Observaciones</th>
									</tr>
								</thead>
							<tbody>

					<?php
 					if ($count>0){
						for($i=0;$i<$count;$i++){
							$row=mysql_fetch_array($result);
							$id_producto=$row['id_producto'];
							$descripcion=$row['descripcion'];
							$unidades=round($row['unidad'],2);
							$cp=round($row['costo_promedio'],2);
							$existencias=$row['stock'];
							if ($unidades>0)
								$precio_unit=$cp/$unidades;
							else
								$precio_unit=$cp;
							$diferencia="";
							$precio_unit=round($precio_unit,2);
							$conteo_fis="<div class='col-xs-2'><input type='text'  class='form-control' id='cant' name='cant'  style='width:60px;'></div>";
							$observacion="<div class='col-xs-2'><input type='text'  class='form-control' id='observacion' name='observacion'  style='width:100px;'></div>";
							echo "<tr>";
							echo "<td>".$id_producto."</td>";
							echo "<td>".$descripcion."</td>";
							echo "<td>".$unidades."</td>";
							echo "<td>".$precio_unit."</td>";
							echo "<td>".$existencias."</td>";
							echo "<td>".$conteo_fis."</td>";
							echo "<td  id='diferencia'>".$diferencia."</td>";
							echo "<td>".$observacion."</td>";
							echo "</tr>";
						}
					}

				?>


							</tbody>
						</table>

					</section>

                      <input type="hidden" name="process" id="process" value="insert"><br>
                           <div class="title-action" id='botones'>
						<button type="submit" id="submit1" name="submit1" class="btn btn-primary"><i class="fa fa-check"></i> Guardar</button>
						<button type="submit" id="print1" class="btn btn-primary"><i class="fa fa-print"></i> Imprimir</button>
				    </div>

                        </div>
                         </div>
                 </div>
            </div>
   </div>
        </div>

<?php
include_once ("footer.php");
//echo "<script src='js/plugins/typehead/bootstrap3-typeahead.js'></script>";
echo "<script src='js/funciones/funciones_ajuste_inventario.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function insertar(){
	//Guardar datos del ajuste a la bd
	// StringDatos+=campo0+"|"+existencias+"|"+cantidad+"|"+diferencia+"|"+observacion+"|"+"#";
	/*
	 SELECT id_ajuste, id_producto, id_stock, stock_actual, conteo_fisico, diferencia,
	 fecha, observaciones FROM ajuste_inventario
	 */
	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];
	$fecha_movimiento= $_POST['fecha_movimiento'];
	$id_sucursal=$_SESSION['id_sucursal'];
	$insertar1=false;
	$insertar2=false;

	$tipo_entrada_salida='AJUSTE INVENTARIO';

	$observaciones=$tipo_entrada_salida;
	if ($cuantos>0){
		$listadatos=explode('#',$stringdatos);
		for ($i=0;$i<$cuantos ;$i++){
			list($id_producto,$existencias_sist,$cantidad,$diferencia,$observacion)=explode('|',$listadatos[$i]);
			 $sql1="select * from movimiento_producto
			 where id_producto='$id_producto'
			 and tipo_entrada_salida='$tipo_entrada_salida'
			 and fecha_movimiento='$fecha_movimiento'
			 AND id_sucursal_origen='$id_sucursal'";
			 $stock1=_query($sql1);
			 $row1=_fetch_array($stock1);
			 $nrow1=_num_rows($stock1);

			 $sql2="select producto.id_producto,producto.unidad,stock.stock, stock.costo_promedio
					from producto,stock
					where producto.id_producto='$id_producto'
					and producto.id_producto=stock.id_producto
					AND  stock.id_sucursal='$id_sucursal'";

			 $stock2=_query($sql2);
			 $row2=_fetch_array($stock2);
			 $nrow2=_num_rows($stock2);
			 $unidad=$row2['unidad'];
			 $existencias=$row2['stock'];
			 $costo_promedio=$row2['costo_promedio'];

			 $sql3="SELECT id_ajuste, id_producto, id_stock, stock_actual, conteo_fisico, diferencia,
				fecha, observaciones FROM ajuste_inventario
				WHERE id_producto='$id_producto' AND  fecha='$fecha_movimiento'
				AND id_sucursal='$id_sucursal'";
			 $stock3=_query($sql3);
			 $row3=_fetch_array($stock3);
			 $nrow3=_num_rows($stock3);

			 if($diferencia==0){
					$entrada=0;
					$salida=0;
					$observacion="";
			 }
			if ($diferencia>0){
					$entrada=$diferencia;
					$salida=0;
				}
			if ($diferencia<0){
					$entrada=0;
					$salida=abs($diferencia);
			}

			$table1= 'movimiento_producto';
			$form_data1=array(
			'id_producto' => $id_producto,
			'fecha_movimiento' => $fecha_movimiento,
			'entrada' => $entrada,
			'salida' => $salida,
			'observaciones' => $observacion,
			'tipo_entrada_salida' => $tipo_entrada_salida,
			'id_sucursal_origen'=>$id_sucursal
			);
			$table2= 'stock';
			$form_data2 = array(
			'id_producto' => $id_producto,
			'stock' => $cantidad,
			'stock_minimo' => 1,
			'costo_promedio'=>$costo_promedio,
			'ultimo_precio_compra'=>$costo_promedio,
			'id_sucursal'=>$id_sucursal
			);
			// stock_actual, conteo_fisico, diferencia,
			// fecha, observaciones
			$table3= 'ajuste_inventario';
			$form_data3 = array(
			'id_producto' => $id_producto,
			'stock_actual' => $existencias_sist,
			'conteo_fisico' => $cantidad,
			'diferencia' => $diferencia,
			'fecha'=>$fecha_movimiento,
			'observaciones'=>$observacion,
			'id_sucursal'=>$id_sucursal
			);

		if ($nrow1==0){
			$insertar1 = _insert($table1,$form_data1 );
		}
		if ($nrow1>0){
			$where_clause1="WHERE  id_producto='$id_producto' and tipo_entrada_salida='$tipo_entrada_salida'
							and fecha_movimiento='$fecha_movimiento' and id_sucursal_origen='$id_sucursal'";
			$insertar1 = _update($table1,$form_data1 , $where_clause1);
		}
		if ($nrow2>0 && $nrow1>=0){
			$where_clause2="WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal'";
			$insertar2 = _update($table2,$form_data2, $where_clause2 );
		}
		if ($nrow3==0){
			$insertar3 = _insert($table3,$form_data3 );
		}
		if ($nrow3>0){
			$where_clause3="WHERE  id_producto='$id_producto' and fecha='$fecha_movimiento' AND id_sucursal='$id_sucursal'";
			$insertar3 = _update($table3,$form_data3, $where_clause3);
		}
		}//for
	 }//if


    if ($insertar1 && $insertar2 && $insertar3){
     $xdatos['typeinfo']='Success';
       $xdatos['msg']='Registro de Inventario Actualizado !';
       $xdatos['process']='insert';
    }
    else{
       $xdatos['typeinfo']='Error';
       $xdatos['msg']='Registro de Inventario no pudo ser Actualizado !';
		}


	echo json_encode($xdatos);
}
/*
function consultar_stock(){
	$id_producto = $_REQUEST['id_producto'];
	$id_sucursal=$_SESSION['id_sucursal'];

	$sql1="SELECT producto.id_producto,producto.descripcion,producto.unidad,stock.stock,stock.costo_promedio
	FROM producto JOIN stock ON producto.id_producto=stock.id_producto
	WHERE producto.id_producto='$id_producto'
	AND stock.id_sucursal='$id_sucursal'";

	$stock1=_query($sql1);
	$row1=_fetch_array($stock1);
	$nrow1=_num_rows($stock1);
	$unidades=round($row1['unidad'],2);
	$cp=round($row1['costo_promedio'],2);
	$existencias=$row1['stock'];
	if ($unidades>0)
		$precio_unit=$cp/$unidades;
	else
		$precio_unit=$cp;
	$precio_unit=round($precio_unit,2);
	$xdatos['costo_prom'] = $cp;
	$xdatos['pre_unit'] = $precio_unit;
	$xdatos['existencias'] = $existencias;

	echo json_encode($xdatos); //Return the JSON Array

}
*/
function consultar_stock()
{
    $id_producto = $_REQUEST['id_producto'];
    $id_usuario=$_SESSION["id_usuario"];
    $id_sucursal=$_SESSION['id_sucursal'];
    $sql1="SELECT producto.id_producto,producto.descripcion,producto.unidad,stock.stock,
	stock.costo_promedio,producto.perecedero
	FROM producto JOIN stock ON producto.id_producto=stock.id_producto
	WHERE producto.id_producto='$id_producto'";

    $stock1=_query($sql1);
    $row1=_fetch_array($stock1);
    $nrow1=_num_rows($stock1);
    $unidades=round($row1['unidad'], 2);
    $cp=round($row1['costo_promedio'], 2);
    $perecedero=$row1['perecedero'];
    $existencias=$row1['stock'];
    if ($unidades>0) {
        $precio_unit=$cp/$unidades;
    } else {
        $precio_unit=$cp;
    }

    $fecha_hoy=date("Y-m-d");
    //consultar si es perecedero
    $fecha_caducidad="0000-00-00";
    $stock_fecha=0;
    if ($perecedero==1) {
        $sql_perecedero="SELECT id_lote_prod, id_producto, fecha_entrada, fecha_caducidad, entrada,
			salida, estado, numero_doc, id_sucursal
			FROM lote
			WHERE id_producto='$id_producto'
			AND id_sucursal='$id_sucursal'
			AND entrada>salida
			AND (fecha_caducidad>='$fecha_hoy'
			OR  fecha_caducidad='0000-00-00')
			ORDER BY fecha_caducidad ASC";
        $result_perecedero=_query($sql_perecedero);
        $array_fecha=array();
        $array_stock=array();
        $nrow_perecedero=_num_rows($result_perecedero);
        if ($nrow_perecedero>0) {
            for ($i=0;$i<$nrow_perecedero;$i++) {
                $row_perecedero=_fetch_array($result_perecedero);
                //$costos_pu=array($pu1,$pu2,$pu3,$pu4);
                $entrada=$row_perecedero['entrada'];
                $salida=$row_perecedero['salida'];
                $id_lote_prod=$row_perecedero['id_lote_prod'];
                $fecha_caducidad=$row_perecedero['fecha_caducidad'];
                $fecha_caducidad=ED($fecha_caducidad);
                $stock_fecha=$entrada-$salida;
                $array_fecha[] =$id_lote_prod."|".$fecha_caducidad;
                $array_stock[] =$id_lote_prod."|".$fecha_caducidad."|".$stock_fecha;
            }
        }
    } else {
        $array_fecha="";
        $array_stock="";
    }
    $precio_unit=round($precio_unit, 2);
    $xdatos['costo_prom'] = $cp;
    $xdatos['pre_unit'] = $precio_unit;
    $xdatos['existencias'] = $existencias;
    $xdatos['fechas_vence'] = $array_fecha;
    $xdatos['stock_vence'] = $array_stock;
    $xdatos['perecedero']=$perecedero;
    echo json_encode($xdatos); //Return the JSON Array
}
//functions to load
if(!isset($_REQUEST['process'])){
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
