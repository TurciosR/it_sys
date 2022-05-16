<?php
include_once "_core.php";
include ('num2letras.php');
function initial() {


	$_PAGE = array ();
	$_PAGE ['title'] = 'Compra de Producto';
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
  $_PAGE ['links'] .= '<link href="css/style_table2.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";

	$sql="SELECT * FROM producto";

	$result=_query($sql);
	$count=_num_rows($result);
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

	//crear array proveedores
  $sql0="SELECT * FROM proveedores";
  $result0=_query($sql0);
  $count0=_num_rows($result0);
  $array0 = array("Seleccione",'-1');
  for ($x=0;$x<$count0;$x++){
    $row0=_fetch_array($result0);
    $id0=$row0['id_proveedor'];
    $description=$row0['nombre'];
    $array0[$id0] = $description;
  }
	//crear array pedidos
	  $array1 = array(0=>"Seleccione");
		//crear array colores
		$sql0="SELECT * FROM colores";
		$result0=_query($sql0);
		$count0=_num_rows($result0);
		$array2= array("Seleccione",'-1');
		for ($x=0;$x<$count0;$x++){
			$row0=_fetch_array($result0);
			$id0=$row0['id_color'];
			$description=$row0['nombrecolor'];
			$array2[$id0] = $description;
		}
	$iva=0;
	$sql_iva="select iva,monto_retencion1,monto_retencion10,monto_percepcion from empresa";
	$result_IVA=_query($sql_iva);
	$row_IVA=_fetch_array($result_IVA);
	$iva=$row_IVA['iva']/100;
	$monto_retencion1=$row_IVA['monto_retencion1'];
	$monto_retencion10=$row_IVA['monto_retencion10'];
	$monto_percepcion=$row_IVA['monto_percepcion'];
?>

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-2"></div>
	</div>
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
				<!--Primero si e si es inv. inicial ,factura de compra, compra caja chica, traslado de otra sucursal; luego Registrar No. de Factura , lote, proveedor -->
				<div class="ibox ">
					<?php
						//permiso del script
						if ($links!='NOT' || $admin=='1' ){
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
				?>

				<div id='form_factura_compra'>
				<div class='row'> <header><h4>Factura de Compra</h4></header>
				<div class='row'>
				 <div class='col-lg-4'>
					 <div class='form-group has-info single-line'><label>Fecha:</label> <input type='text' placeholder='Fecha' class='datepick form-control' value='<?php echo $fecha_actual;?>' id='fecha2' name='fecha2'></div>
				</div>
				 <div class='col-lg-4'>
					 <div class='form-group has-info single-line'><label>Numero de Factura</label> <input type='text' placeholder='Numero de Factura' class='form-control' id='numero_doc2' name='numero_doc2' ></div>
				</div>
			  </div>
	   </div>

<div class='row'>
		<div class="form-group col-lg-6">
			<div class="form-group">  <label>Proveedores &nbsp;</label></div>
			<?php
			$nombre_select0="select_proveedores";
			$idd0=-1;

			$style='width:400px';
			$select0=crear_select2($nombre_select0,$array0,$idd0,$style);
			echo $select0;
			?>
		</div>
		<div class="form-group col-lg-6">
			<div class="form-group">  <label>Cargar Pedido &nbsp;</label></div>
			<?php
			$nombre_select1="select_pedidos";
			$idd1=-1;
			$style='width:150px';
			$select1=crear_select2($nombre_select1,$array1,$idd1,$style);
			echo $select1;
			?>
		</div>
</div>

</div>

<div class="row">
	<div class="post-search-panel">
			<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label>Descripcion</label>
					<input type="text" id="keywords" class='form-control' placeholder="descripcion"/>

				</div>
			</div>

			<div class="col-md-2">
			<div class="form-group">
				<div><label>Color&nbsp;</label></div>
				<?php
				// se va filtrar por descripcion, estilo, talla, color, barcode
				$nombre_select="select_colores";
				$id_val=-1;
				$select=crear_select2($nombre_select,$array2,$id_val);
				echo $select;
				?>
			</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label>Barcode</label>
					<input type="text" id="barcode" class='form-control' placeholder="Barcode" />
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
					<label>Talla</label>
					<input type="text" id="talla" class='form-control' placeholder="Talla" />
				</div>
			</div>
			</div>

			<div class='row' id='encabezado_buscador'>
			<div class="col-md-4">
				<div class="form-group">
					<label>&nbsp;Ordenar </label>
					<select id="sortBy" onchange="searchFilter()">
							<option value="asc">Ascendente</option>
							<option value="desc">Descendente</option>
					</select>
		</div>
	</div>
	<div class="col-md-3">&nbsp;&nbsp;</div>
	<div class="col-md-3">&nbsp;&nbsp;</div>
	<div class="col-md-2 pull-right">
		<div class="form-group">
			<label>Registros </label>
			<select id="records" onchange="searchFilter()">
					<option value="5">5</option>
					<option value="10" selected>10</option>
					<option value="25">25</option>
					<option value="50">50</option>
					<option value="100">100</option>
					<option value="200">200</option>
					<option value="500">500</option>
			</select>
		</div>
		</div>
		</div>

	</div>
	<div class="post-wrapper">
			<div class="loading-overlay"><div class="overlay-content">Cargando.....</div></div>

	</div>
<section>
	<div id="content">

		<table class="table table-striped table-bordered table-hover" id="mostrardatos"></table>
 </div>
 <div id="paginador"></div>


					<div class="widget stacked widget-table action-table">
				<div class="widget-header">&nbsp;&nbsp;
					<i class="fa fa-th-list"> </i>
					<h3 class="text-navy" id='title-table'>&nbsp;Lista  de Productos para Compra e ingreso a Inventario</h3>
				</div> <!-- /widget-header -->

				<div class="widget-content">
						  <!--div class='container'-->

							<table class="table table2 table-fixed table-striped "id="inventable">
									<thead class='thead1'>
									<tr class='tr1'>
										<th class="text-success col1 th1" >Id Producto</th>
										<th class="text-success col2 th1">Nombre</th>
										<th class="text-success col3 th1">Prec. Compra</th>
										<!--th>Precio Vta.</th-->
										<th class="text-success col4 th1">Cantidad</th>
										<th class="text-success col5 th1">% Dscto.</th>
										<th class="text-success col6 th1">Exento</th>
										<th class="text-success col7 th1">Gravado</th>
										<th class="text-success col8 th1">Acci&oacute;n</th>
									</tr>
								</thead>
								<tbody class='tbody1 tbody2'>
								</tbody>
								<!--tfoot>
									<tr>
									<td colspan=8></td>
									</tr>
								</tfoot-->

						</table>
					<!--/div-->
						<table class="table table2">
							<tbody class='tbody3'>
							<tr>
							<td  class='col1' >Totales</td>

							<td class='col2' id='totaltexto'>Son:</td>
							<td class='col1'> </td>
							<td class='col1' id='totcant'>0.00</td>
							<td class='col1' id='totdescto' >$0.00 </td>
							<td  class='col1' id='total_exento'>$0.00</td>
							<td <td class='col1'id='total_gravado'>$0.00</td>

							<td class='col1' > </td>
							</tr>
							</tbody>
						</table>
						<!--div class="row"><div class="col-md-12"></div></div-->
						<div class="row">

							<div class="col-md-12">

								<div class="col-md-8">

									<div class="col-md-12 border_div" >
										<center>
											<h3 class="text-navy" id='title-table'>&nbsp;Busqueda de  Productos</h3>
										</center>
										<div class="col-md-6">
										 <div class="widget style1 text-center">
											 <div class='form-group'>
											 <div class='form-group'><label id='buscar_habilitado'>Buscar Producto (Por Descripción)</label></div>
											 <div class="form-group">
												 <input type="text" id="producto_buscar" name="producto_buscar" size="20" class="form-control" placeholder="Ingrese Descripcion de producto"  data-provide="typeahead" style="border-radius:0px" >
												 <input  type="text" id="producto_buscar2" name="producto_buscar2" size="20" class="form-control" placeholder="Ingrese  barcode producto"  style="border-radius:0px" >
												 <input  type="text" id="producto_buscar3" name="producto_buscar3" size="20" class="form-control" placeholder="Ingrese Estilo producto"  style="border-radius:0px" >
											 </div>
										 </div>
										 </div>
									 </div>


									 <div class="col-md-6">
										 <div class="widget style1 text-center">
											 <div class='form-group'>
												 <div class="form-group"><label id='buscar_habilitado2'>Buscar Por: </label></div>
												 <div class="form-group">
													 <label class="radio-inline  i-checks"> <input type="radio"  value="0" name="tipo" id="tipo0" checked> <i></i> Descripcion </label>
													 <label class="radio-inline  i-checks"> <input type="radio" value="1" name="tipo" id="tipo1" <i></i>  Barcode </label>
													 <label class="radio-inline  i-checks"> <input type="radio" value="1" name="tipo" id="tipo2" <i></i>  Estilo </label>
													 <p>&nbsp;</p>
												 </div>
												 </div>
											 </div>
									 </div>

									</div>
							</div>
							<div class="col-md-4 border_div" >
						<table class='table invoice-total'>
							<tbody class='tbody3'>
								<tr>
									<td><strong>SUMAS (SIN IVA) $:</strong></td>
									<td id='total_gravado_sin_iva'></td>
								</tr>
								<tr>
									<td><strong>IVA  $:</strong></td>
									<td id='total_iva'></td>
								</tr>
								<tr>
									<td><strong>SUBTOTAL  $:</strong></td>
									<td id='total_gravado_iva'></td>
								</tr>
								<tr>
									<td><strong>VENTA EXENTA $:</strong></td>
									<td id='total_exenta'></td>
								</tr>
								<tr>
									<td><strong>PERCEPCION $:</strong></td>
									<td id='total_percepcion'></td>
								</tr>
								<tr>
									<td><strong>RETENCION $:</strong></td>
									<td id='total_retencion'></td>
								</tr>
								<tr>
									<td><strong>TOTAL $:</strong></td>
									<td id='total_final'></td>
								</tr>

							</tbody>
						</table>
						</div>

						</div>
						</div>
					</div> <!-- /widget-content -->


							</div> <!-- /widget -->


							<div class='row'>
								<div class="col-md-12">&nbsp;
									<div class="title-action" id='botones'>
										<button type="submit" id="submit1" name="submit1" class="btn btn-primary"><i class="fa fa-save"></i> F9 Guardar</button>
									</div>

								</div>
						</div>

																		<input type='hidden' name='urlprocess' id='urlprocess'value="<?php echo $filename;?>">
                                    <input type="hidden" name="process" id="process" value="insert"><br>



              </div>
            </div>
   </div>
        </div>

<?php
include_once ("footer.php");

//echo "<script  src='js/funciones/table-fixed-header.js'></script>";
echo "
<script src='js/funciones/compras3.js'></script>
  ";
	} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function insertar(){
	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];
	$id = $_POST['id'];
	$fecha_movimiento= $_POST['fecha_movimiento'];
	$numero_doc = $_POST['numero_doc'];
	$lote = $_POST['lote'];

	$id_proveedor= $_POST['id_proveedor'];
	$total_compras = $_POST['total_compras'];

	$id_usuario=$_SESSION["id_usuario"];
	$id_sucursal= $_SESSION['id_sucursal'];
  $numero_doc=$numero_doc;
	$tipo_entrada_salida='FACTURA DE COMPRA';
	$observaciones=$tipo_entrada_salida;

	$insertar1=false;
	$insertar2=false;
	$insertar3=false;

	 if ($cuantos>0){
		 _begin();
		$listadatos=explode('#',$stringdatos);
		 for ($i=0;$i<$cuantos ;$i++){
			 list($id_producto,$precio_compra,$precio_venta,$cantidad,$subcantidad,$fecha_caduca)=explode('|',$listadatos[$i]);
			 //consulta productos para obtener unidad y si es perecedero
			 $sql_producto="select producto.id_producto,producto.unidad,producto.perecedero from producto  where producto.id_producto='$id_producto'";
			 $result=_query( $sql_producto);
			 $row_unidad=_fetch_array($result);
			 $unidad=$row_unidad['unidad'];
			 if  ($unidad<=0)
			 	$unidad=1;
			 $perecedero=$row_unidad['perecedero'];
			  //consulta movimiento_producto para verificar si ya existia una engtrada igual y hacer update
			 $sql1="select * from movimiento_producto where id_producto='$id_producto'  and fecha_movimiento='$fecha_movimiento'
			 and tipo_entrada_salida='$tipo_entrada_salida' and numero_doc='$numero_doc'
			 and id_sucursal_origen='$id_sucursal'";
			 $entrada_mov=0;
			 $stock1=_query($sql1);
			 $nrow1=_num_rows($stock1);
			 if ($nrow1>0){
			 	$row1=_fetch_array($stock1);
			 	$entrada_mov=$row1['entrada'];
		 	 }
			 else {
			 		$entrada_mov=0;
			 }
			  //consulta stock para verificar si ya existia una entrada igual y hacer update
			 $sql2="select producto.id_producto,producto.unidad,stock.stock, stock.costo_promedio from producto,stock
					where producto.id_producto='$id_producto' and producto.id_producto=stock.id_producto  and stock.id_sucursal='$id_sucursal' ";
			 $stock2=_query($sql2);
			 $row2=_fetch_array($stock2);
			 $nrow2=_num_rows($stock2);
        if ($nrow2>0){
			 		$existencias=$row2['stock'];
			 		$costo_promedio=$row2['costo_promedio'];
		 		}
				else {
					$existencias=0;
				 	$costo_promedio=0;
				}
			 if($unidad>0){
				 $cantidad=($cantidad*$unidad)+$entrada_mov+$subcantidad;
			 }
			 else {
			 	$cantidad=$cantidad+$entrada_mov;
			 }

     	$table1= 'movimiento_producto';
			if ($cantidad>0 && $nrow1==0){
				$cant_total=$cantidad+$existencias;
				$form_data1 = array(
			 'id_producto' => $id_producto,
			 'fecha_movimiento' => $fecha_movimiento,
			 'entrada' => $cantidad,
			 'observaciones' => $observaciones,
			 'tipo_entrada_salida' => $tipo_entrada_salida,
			 'numero_doc' => $numero_doc,
			 'precio_compra' => $precio_compra,
			 'lote' => $lote,
			 'salida' => 0,
			 'stock_anterior'=>$existencias,
			 'stock_actual'=>$cant_total,
			 'id_sucursal_origen' => $id_sucursal,
				'id_proveedor' => $id_proveedor
			 );
				$insertar1 = _insert($table1,$form_data1 );
			}
			if ($cantidad>0 && $nrow1>0){
				$existencias=0;
			  $cant_total=$cantidad+$existencias;
				 $where_clause="WHERE id_producto='$id_producto'
				 and fecha_movimiento='$fecha_movimiento'
				 and tipo_entrada_salida='$tipo_entrada_salida'
				 and id_sucursal_origen='$id_sucursal'
				 and numero_doc='$numero_doc'";

				 $form_data1 = array(
 			 'entrada' => $cantidad,
 			 'observaciones' => $observaciones,
 			 'tipo_entrada_salida' => $tipo_entrada_salida,
			 'precio_compra' => $precio_compra,
			 'lote' => $lote,
			 'salida' => 0,
			 'stock_anterior'=>$existencias,
			 'stock_actual'=>$cant_total,
			 'id_sucursal_origen' => $id_sucursal,
				'id_proveedor' => $id_proveedor
 			 );
				$insertar1 = _update($table1,$form_data1, $where_clause );
			}

		$table2= 'stock';
		if ($cantidad>0 && $nrow2==0){
			$cant_total=$cantidad;
			$form_data2 = array(
			'id_producto' => $id_producto,
			'stock' => $cant_total,
			'stock_minimo' => 1,
			'costo_promedio'=>$precio_compra,
			'ultimo_precio_compra'=>$precio_compra,
			'precio_sugerido'=>$precio_venta,
			'pv_base'=>$precio_venta,
			'id_sucursal' => $id_sucursal
			);
			$insertar2 = _insert($table2,$form_data2 );
		}
		if ($cantidad>0 && $nrow2>0 ){
			if($existencias<0)
			 $existencias=0;
			$cant_total=$cantidad+$existencias;
			$form_data2 = array(
			'id_producto' => $id_producto,
			'stock' => $cant_total,
			'stock_minimo' => 1,
			'costo_promedio'=>$precio_compra,
			'ultimo_precio_compra'=>$precio_compra,
			'precio_sugerido'=>$precio_venta,
			'pv_base'=>$precio_venta,
			'id_sucursal' => $id_sucursal
			);
			$where_clause="WHERE id_producto='$id_producto' and id_sucursal='$id_sucursal'";
			$cantidad_stock=$row2['stock']+$cantidad;

			$insertar2 = _update($table2,$form_data2, $where_clause );
		}

		if ($fecha_caduca!="0000-00-00" && $perecedero!=0 && $fecha_caduca!=""){
			$sql_caduca="SELECT * FROM lote WHERE id_producto='$id_producto' and fecha_entrada='$fecha_movimiento' and fecha_caducidad='$fecha_caduca' ";
			 $result_caduca=_query($sql_caduca);
			 $row_caduca=_fetch_array($result_caduca);
			 $nrow_caduca=_num_rows($result_caduca);
			 if($nrow_caduca==0){
				 $table_perece= 'lote';

				if($fecha_movimiento>=$fecha_caduca){
					$estado='VENCIDO';
				}
				else{
					$estado='VIGENTE';
				}
				$form_data_perece = array(
				'id_producto' => $id_producto,
				'numero_doc' => $numero_doc,
				'numero_lote' => $lote,
				'fecha_entrada' => $fecha_movimiento,
				'fecha_caducidad'=>$fecha_caduca,
				'entrada' => $cantidad,
				'estado'=>$estado,
				'id_sucursal' => $id_sucursal,
				'id_proveedor' => $id_proveedor
				);
				$insertar3 = _insert($table_perece,$form_data_perece );
			}
		}
		else{
 		 $sql_caduca="SELECT * FROM lote WHERE id_producto='$id_producto' and fecha_entrada='$fecha_movimiento'";
 			$result_caduca=_query($sql_caduca);
 			$row_caduca=_fetch_array($result_caduca);
 			$nrow_caduca=_num_rows($result_caduca);
 			if($nrow_caduca==0){
 				$table_perece= 'lote';
 				 $estado='VIGENTE';

 			 $form_data_perece = array(
 			 'id_producto' => $id_producto,
 			 'numero_doc' => $numero_doc,
 			 'numero_lote' => $lote,
 			 'fecha_entrada' => $fecha_movimiento,
 			 'entrada' => $cantidad,
 			 'estado'=>$estado,
 			 'id_sucursal' => $id_sucursal,
 			 'id_proveedor' => $id_proveedor
 			 );
 			 $insertar3 = _insert($table_perece,$form_data_perece );
 		 }

 	 }
		}//for
	}//if $cuantos>0

    if ($insertar1 && $insertar2 && $insertar3){
		_commit(); // transaction is committed
		$xdatos['typeinfo']='Success';
       $xdatos['msg']='Registro de Inventario Actualizado !';
       $xdatos['process']='insert';
			 $xdatos['guardar']="mov_prod:".$insertar1."stock: ".$insertar2."lote: ".$insertar3." ";
    }
    else{
		_rollback(); // transaction not committed
       $xdatos['typeinfo']='Error';
       $xdatos['msg']='Registro de Inventario no pudo ser Actualizado !';
			 $xdatos['guardar']="mov_prod: ".$insertar1." stock: ".$insertar2." lote: ".$insertar3." ";
	}
	echo json_encode($xdatos);
}
function consultar_stock(){
	$id_producto = $_REQUEST['id_producto'];
	$id_usuario=$_SESSION["id_usuario"];
   $id_sucursal=$_SESSION['id_sucursal'];
	$sql_user="select * from usuario where id_usuario='$id_usuario'";

	 $sql2="select * from stock where id_producto='$id_producto' and id_sucursal ='$id_sucursal'";
			 $stock2=_query($sql2);
			 $row2=_fetch_array($stock2);
			 $nrow2=_num_rows($stock2);
			 $existencias=$row2['existencias'];


	$sql3="select p.*,c.nombrecolor from productos AS p
		JOIN colores AS c ON (p.id_color=c.id_color)
		where id_producto='$id_producto'
		";
	$result3=_query($sql3);
	$row3=_fetch_array($result3);

	$cp=$row3['costopro'];
	$pv_base=$row3['precio1'];
	$talla=$row3['talla'];
	$color=$row3['nombrecolor'];
	$exento=$row3['exento'];
  //$exento=0;
	$xdatos['costo_prom'] = $cp;
	$xdatos['pv_base'] = $pv_base;
	$xdatos['existencias'] = $existencias;
	$xdatos['color'] = $color;
	$xdatos['talla'] = $talla;
	$xdatos['exento'] = $exento;
	echo json_encode($xdatos); //Return the JSON Array

}
function buscarBarcode(){
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
	echo json_encode ($xdatos); //Return the JSON Array
}
function total_texto(){
	$total=$_REQUEST['total'];
	list($entero,$decimal)=explode('.',$total);
	$enteros_txt=num2letras($entero);
	$decimales_txt=num2letras($decimal);

	if($entero>1)
		$dolar=" dolares";
	else
		$dolar=" dolar";
	$cadena_salida= "Son: <strong>".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.</strong>";
	echo $cadena_salida;
}
function datos_proveedores(){
	$id_proveedor = $_POST['id_proveedor'];
	$sql0="SELECT percibe, retiene, retiene10 FROM proveedores  WHERE id_proveedor='$id_proveedor'";
	$result = _query($sql0);
	$numrows= _num_rows($result);
	$row = _fetch_array($result);
	$retiene1=$row['retiene'];
	$retiene10=$row['retiene10'];
	$percibe=$row['percibe'];
	if ($percibe==1)
		$percepcion=round(1/100,2);
	else
		$percepcion=0;

	if ($retiene1==1)
			$retencion1=round(1/100,2);
	else
			$retencion1=0;

	if ($retiene10==1)
				$retencion10=round(10/100,2);
			else
					$retencion10=0;

	$xdatos['retencion1'] = $retencion1;
	$xdatos['retencion10'] = $retencion10;
	$xdatos['percepcion'] = $percepcion;
	echo json_encode($xdatos); //Return the JSON Array
}

function get_sql( $keywords, $id_color, $estilo, $talla, $barcode){
  if($id_color=='-1')
  $id_color='';

  $andSQL='';
  if(!empty($keywords)){
      $andSQL.= "AND pr.descripcion LIKE '%".$keywords."%'
    ";
      //if(!empty($id_marca) && $id_marca!='-1'){
      if(!empty($id_color)){
          $andSQL.= " AND pr.id_color = '".$id_color."' ";
      }
      if(!empty($estilo)){
          $andSQL.= " AND pr.estilo LIKE '%".$estilo."%' ";
      }
      if(!empty($talla)){
          $andSQL.= " AND pr.talla LIKE '%".$talla."%' ";
      }
			if(!empty($barcode)){
					$andSQL.= " AND pr.barcode LIKE '%".$barcode."%' ";
			}
  }
  if(empty($keywords) && !empty($id_color) ){
      $andSQL .= "AND pr.id_color = '".$id_color."'";
      if(!empty($estilo)){
          $andSQL.= " AND pr.estilo LIKE '%".$estilo."%' ";
      }
      if(!empty($talla)){
          $andSQL.= " AND pr.talla LIKE '%".$talla."%' ";
      }
			if(!empty($barcode)){
					$andSQL.= " AND pr.barcode LIKE '%".$barcode."%' ";
			}
  }
  if(empty($keywords) && empty($id_color) && !empty($estilo)){
		$andSQL.= " AND pr.estilo LIKE '%".$estilo."%' ";
		if(!empty($talla)){
				$andSQL.= " AND pr.talla LIKE '%".$talla."%' ";
		}
		if(!empty($barcode)){
				$andSQL.= " AND pr.barcode LIKE '%".$barcode."%' ";
		}
   }
	 if(empty($keywords) && empty($id_color) && empty($estilo) && !empty($talla)){
		$andSQL.= " AND pr.talla LIKE '%".$talla."%' ";
		if(!empty($barcode)){
				$andSQL.= " AND pr.barcode LIKE '%".$barcode."%' ";
		}
		}

//  }
   //si solo tenemos el barcode buscamos solo eso
   if(empty($keywords) && empty($id_color) && empty($estilo) && empty($talla)&& !empty($barcode)){
      $andSQL.= " AND pr.barcode LIKE '%".$barcode."%' ";
   }

  $whereSQL=" WHERE pr.id_color=co.id_color ";

  $sql_parcial=$whereSQL.$andSQL;
  return $sql_parcial;
}
function traerdatos() {
//if (isset($_POST['page'])){
    //set conditions for search


    $keywords = $_POST['keywords'];
    $estilo = $_POST['estilo'];
    $talla= $_POST['talla'];
    $id_color= $_POST['id_color'];
    $barcode= $_POST['barcode'];
    $whereSQL =$andSQL =  $orderSQL = '';
    //get rows
    $sqlJoined="SELECT pr.*,
    co.nombrecolor
    FROM productos pr, colores  co

    ";
    $sqlParcial=get_sql( $keywords, $id_color, $estilo, $talla, $barcode);

    $sql_final= $sqlJoined." ".$sqlParcial." ";

    $query = _query($sql_final);
		  $num_rows = _num_rows($query);
    if($num_rows > 0){ ?>
			<thead class='thead1'>
			 <tr class='tr1'>
				 <th class="text-success col1 th1" >Id Producto</th>
				 <th class="text-success col2 th1">Nombre</th>
				 <th class="text-success col3 th1">Prec. Compra</th>
				 <!--th>Precio Vta.</th-->
				 <th class="text-success col4 th1">Cantidad</th>
				 <th class="text-success col5 th1">% Dscto.</th>
				 <th class="text-success col6 th1">Exento</th>
				 <th class="text-success col7 th1">Gravado aqui vamos</th>
				 <th class="text-success col8 th1">Acci&oacute;n</th>
			 </tr>
		 </thead>
        <tbody class='tbody1 tbody2'>

        <?php
            while($row = _fetch_array($query)){
                $id_producto = $row['id_producto'];
                $descripcion=$row["descripcion"];
                $estilo = $row['estilo'];
                $talla = $row['talla'];
                $barcode = $row['barcode'];

            ?>
				  <tr>
					<td><h5><?php echo $id_producto; ?></h5></td>
            <td><h5><?php echo $descripcion;?></h5></td>
            <td><h5 class='text-success'><?php echo $estilo;?></h5></td>
            <td><h5 class='text-success'><?php echo $talla;?></h5></td>
            <td><h5 class='text-success'><?php echo $abarcode;?></h5></td>
						<td></td>
						<td></td>
						<td></td>
            </tr>
          <?php
            //}
          }

        ?>
        <!--/div-->

                 </tbody>

        <?php
    }
  //}
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
	case 'buscarBarcode' :
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

	}

 //}
}
?>
