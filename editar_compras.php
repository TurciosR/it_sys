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
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/style_table3.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";

	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	//id compras
	$id_compras=$_REQUEST["id_compras"];
	$id_sucursal=$_SESSION["id_sucursal"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

	//crear array proveedores
  $sql0="SELECT * FROM proveedores";
  $result0=_query($sql0);
  $count0=_num_rows($result0);
  $array0 =array(-1=>"Seleccione");
  for ($x=0;$x<$count0;$x++){
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
		for ($y=0;$y<$count2;$y++){
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
for ($z=0;$z<$count3;$z++){
	$row3=_fetch_array($result3);
	$id3=$row3['alias'];
	$description3=$row3['nombredoc'];
	$array3[$id3] = $description3;
}
//array de Pedidos
$sql4='SELECT * FROM pedidos WHERE  finalizado=0 AND anulado=0 ';
$result4=_query($sql4);
$count4=_num_rows($result4);
$array4= array(-1=>"Seleccione");
for ($a=0;$a<$count4;$a++){
	$row4=_fetch_array($result4);
	$id4=$row4['idtransace'];
	$fechapedido=ed($row4['fecha']);
	$description4=$row4['idtransace']." (".$fechapedido.")";
	$array4[$id4] = $description4;
}
//Datos de compras

$sql1="SELECT *
FROM compras
WHERE id_compras='$id_compras'";
$result1 = _query($sql1);

$numrows= _num_rows($result1);
for ($i=0;$i<$numrows;$i++){
 $row = _fetch_array($result1);
$id_proveedor =$row['id_proveedor'];
$id_pedido =$row['id_pedido'];
$fechadoc =$row['fechadoc'];
$numero_doc=$row['numero_doc'];
$alias_tipodoc=$row['alias_tipodoc'];
$dias_credito =$row['dias_credito'];
$total=$row['total'];
$total_percepcion=$row['total_percepcion'];
$total_retencion=$row['total_retencion'];
}
?>

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
						if ($links!='NOT' || $admin=='1' ){
						?>
					<div class="ibox-content">
						<input type='hidden' name='id_compras' id='id_compras' value='<?php echo $id_compras;?>'>
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
						<div class="row">
							<div class="widget stacked widget-table action-table">
								<div class="widget-header">&nbsp;&nbsp;
									<i class="fa fa-th-list"> </i>
									<h3 class="text-navy" id='title-table'>&nbsp;Encabezado Factura</h3>
								</div> <!-- /widget-header -->
					<div class='widget-content'>
				<div id='form_factura_compra'>
				<div class='row'>

				 <div class='col-md-4'>
					 <div class='form-group'><label>Fecha:</label> <input type='text' placeholder='Fecha' class='datepick form-control' value='<?php echo $fechadoc;?>' id='fecha2' name='fecha2'></div>
				</div>
				<div class="col-md-4">
					<div class="form-group">  <label>Tipo Documento &nbsp;</label></div>
					<?php
					$nombre_select0="select_documento";
					$idd0=-1;
					$style='width:300px';
					$select0=crear_select2($nombre_select0,$array3,$alias_tipodoc,$style);
					echo $select0;
					?>
				</div>
				 <div class='col-md-4'>
					 <div class='form-group'><label>Numero de Documento</label> <input type='text' placeholder='Numero de Documento' class='form-control' id='numero_doc2' name='numero_doc2' value='<?php echo $numero_doc;?>' ></div>
				</div>

	   </div>

<div class='row'>
		<div class="col-md-4">
			<div class="form-group">  <label>Proveedores &nbsp;</label></div>
			<?php
			$nombre_select0="select_proveedores";
			$idd0=-1;
			//$style='width:400px';
			$style='';
			$select0=crear_select2($nombre_select0,$array0,$id_proveedor,$style);
			echo $select0;
			?>
		</div>

		<div class="col-md-4">
			<div class="form-group">  <label>Cargar Pedido &nbsp;</label></div>
			<?php
			$nombre_select1="select_pedidos";
			$idd1=-1;
			$style='';
			$select1=crear_select2($nombre_select1,$array4,$id_pedido,$style);
			echo $select1;
			?>
		</div>
		<div class='col-md-4'>

			<div class='form-group'><label>Dias Vencimiento</label> <input type='text' placeholder='dias' class='form-control' id='numero_dias' name='numero_dias' value='<?php echo $dias_credito;?>'></div>
	 </div>
</div>
</div>
</div>
</div>
</div>


 <div class="widget stacked widget-table action-table">
	 <div class="widget-header">
		 <div class="row">

					<div class="col-md-6">&nbsp;&nbsp;
					<i class="fa fa-th-list"> </i>
					<h3 class="text-navy" id='title-table'>&nbsp;Lista  de Productos para Compra e ingreso a Inventario</h3>
					</div>
					<div class="form-group col-md-3">
						<label>Items&nbsp;
						<input type="text"  class='form-control input_header_panel'  id="items" value=0 readOnly /></label>
					</div>
					<div class="form-group col-md-3">
						<label>Pares/Unidades&nbsp;
						<input type="text"  class='form-control input_header_panel'  id="pares" value=0 readOnly /></label>
					</div>
					</div>

				</div> <!-- /widget-header -->

				<div class="widget-content">
						  <!--div class='container'-->

							<table class="table table2 table-fixed table-striped "id="inventable">
								<thead class='thead1'>
								<tr class='tr1'>
									<th class="text-success col11 th1" >Código</th>
								 <th class="text-success col14 th1">Nombre</th>
								 <th class="text-success col11 th1">Costo</th>
								 <th class="text-success col11 th1">Cantidad</th>
								 <th class="text-success col11 th1">% Dscto.</th>
								 <th class="text-success col11 th1">Exento</th>
								 <th class="text-success col11 th1">Gravado </th>
								 <th class="text-success col11 th1">Acci&oacute;n</th>
								</tr>
							</thead>
								<tbody class='tbody1 tbody2'>
								</tbody>
						</table>

						<table class="table table2">
							<tbody class='tbody3'>
 						 <tr>
 						 <td  class='col11' >Totales</td>
 						 <td class='col14' id='totaltexto1'></td>
 						 <td class='col11'> </td>
 						 <td class='col11' id='totcant'>0.00</td>
 						 <td class='col11' id='totdescto' >$0.00 </td>
 						 <td class='col11' id='total_exento'>$0.00</td>
 						 <td class='col11 text-danger' id='total_gravado'>$0.00</td>
 						 <td class='col1' > </td>
 						 </tr>
						</table>
					</div> <!-- /widget-content -->
					</div> <!-- /widget -->


<div class="row">
	<div class="widget stacked widget-table action-table">
		<div class="widget-header">
			<div class="row">
			<div class="col-md-4">&nbsp;&nbsp;
			<i class="fa fa-th-list"> </i>
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
		</div> <!-- /widget-header -->

			<div class="widget-content">
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
				$select=crear_select2($nombre_select,$array2,$id_val,$style);
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

		</div>

	<div class="post-wrapper">
			<div class="loading-overlay"><div class="overlay-content" id='reg_count0'>Cargando.....</div></div>

	</div>
 	</div>
	<div  class='widget-content' id="content">
		<div class="row">
		<div class="col-md-9">
	<table class="table" id='loadtable'>
		<thead class='thead1'>
		 <tr class='tr1'>
			 <th class="text-success col12 th1" >Código</th>
			<th class="text-success col13 th1">Nombre</th>
			<th class="text-success col12 th1">Costo</th>
			<th class="text-success col12 th1">Estilo</th>
			<th class="text-success col12 th1">Talla</th>
			<th class="text-success col12 th1">Color</th>
		 </tr>
	 </thead>
			<tbody class='tbody1 tbody2' id="mostrardatos">
			</tbody>
	</table>
 </div>

	 <div class="col-md-3 border_div" >
 <table class='table invoice-total'>
	 <tbody class='tbody3'>
 		<tr>
 			<td class="text-warning"><strong>SUMAS (SIN IVA) $:</strong></td>
 			<td  class="text-danger" id='total_gravado_sin_iva'></td>
 		</tr>
 		<tr>
 		<td class="text-warning"><strong>IVA  $:</strong></td>
 		<td id='total_iva'></td>
 	</tr>
 	<tr>
 		<td class="text-warning">SUBTOTAL  $:</strong></td>
 		<td id='total_gravado_iva'></td>
 	</tr>
 	<tr>
 	<td class="text-warning"><strong>VENTA EXENTA $:</strong></td>
 		<td id='total_exenta'></td>
 	</tr>
 	<tr>
 		<td class="text-warning"><strong>PERCEPCION $:</strong></td>
 		<td id='total_percepcion'></td>
 	</tr>
 	<tr>
 	<td class="text-warning"><strong>RETENCION $:</strong></td>
 		<td id='total_retencion'></td>
 	</tr>
 	<tr>
 	<td class="text-warning"><strong>TOTAL $:</strong></td>
 		<td class="text-danger"  id='total_final'></td>
 	</tr>
 </tbody>
 </table>
 </div>
 </div>
 </div>

	<div class='row'>
			<div class='col-md-12 pull-right' id="totaltexto"><h3 class='text-danger'></h3>&nbsp;</div>
	 </div>



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
echo "<script src='js/plugins/arrowtable/arrow-table.js'></script>";
//echo "<script src='js/plugins/arrowtable/navigatetable.js'></script>";
echo "<script src='js/funciones/editar_compras.js'></script>";

	} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function insertar(){
	//ACA se va insertar a las sig tablas:
	// compras, cxc, detalle_compras, correlativos, stock y kardex ,(otras probables cuenta_proveedor,)
	$cuantos = $_POST['cuantos'];
	$fecha_movimiento= $_POST['fecha_movimiento'];
	$numero_doc = $_POST['numero_doc'];
	$id_tipodoc = $_POST['id_tipodoc'];
	$id_pedido = $_POST['id_pedido'];
	$id_proveedor= $_POST['id_proveedor'];
	$total_compras = $_POST['total_compras'];
	$total_gravado= $_POST['total_gravado'];
  $total_exento= $_POST['total_exento'];
  $total_iva= $_POST['total_iva'];
  $total_retencion= $_POST['total_retencion'];
  $total_percepcion= $_POST['total_percepcion'];
  $array_json=$_POST['json_arr'];
	$id_compras=$_POST["id_compras"];
	$numero_dias= $_POST['numero_dias'];
	$items= $_POST['items'];
	$pares=$_POST['pares'];
	$id_usuario=$_SESSION["id_usuario"];
	$id_sucursal= $_SESSION['id_sucursal'];


	$insertar1=false;
	$insertar2=false;
	$insertar3=false;
  $fecha_vencimiento=sumar_dias_Ymd($fecha_movimiento, $numero_dias);

	if ($cuantos>0){
		#Crear tabla temporal
		$tmp_tbl='CREATE TEMPORARY TABLE IF NOT EXISTS tmp_compras  LIKE detalle_compras';
		$result0=_query($tmp_tbl);
		 _begin();
		 $hora=date("H:i:s");
		 $fecha_ing=date('Y-m-d');
		 $sql_compras="SELECT * FROM compras WHERE id_compras='$id_compras'
		  AND id_sucursal='$id_sucursal'";
			$result_fc=_query($sql_compras);
			$row_fc=_fetch_array($result_fc);
			$nrows_fc=_num_rows($result_fc);
			if($nrows_fc>0){
				$table_fc= 'compras';
				$form_data_fc = array(
				'id_proveedor' => $id_proveedor,
				'alias_tipodoc'=>$id_tipodoc,
				'fechadoc' => $fecha_movimiento,
				'fecha_ingreso' => $fecha_ing,
				'numero_doc' => $numero_doc,
				'total' => $total_compras,
				'total_retencion'=>$total_retencion,
				'total_percepcion'=>$total_percepcion,
				'id_empleado' => $id_usuario,
				'id_sucursal' => $id_sucursal,
				'iva' => $total_iva,
				'hora' => $hora,
				'dias_credito' => $numero_dias,
				'finalizada' =>1,
				'pares'=>$pares,
				'items'=>$items,
				);
				//
				$where_clause_fc="WHERE id_compras='$id_compras'";
				$insertar_fc = _update($table_fc,$form_data_fc,$where_clause_fc);

			}
			//Actualizar en stock pero restando las  cantidades de productos antes de los cambios de detalle compras
			$comentario=$id_tipodoc."  ".$numero_doc." ".$fecha_movimiento." ".$hora;
			$sql_dc0="SELECT dc.id_producto,dc.cantidad,st.existencias,(st.existencias-dc.cantidad) AS diferencia
			FROM detalle_compras AS dc, stock AS st
			WHERE dc.id_compras='$id_compras'
			AND st.id_sucursal='$id_sucursal'
			AND dc.id_producto=st.id_producto";
			$result_dc0=_query($sql_dc0);
			$nrows_dc0=_num_rows($result_dc0);
			if($nrows_dc0>0){
				for($a=0;$a<$nrows_dc0;$a++){
				$row_dc0=_fetch_array($result_dc0);
				$id_prod=$row_dc0['id_producto'];
				$exist_actual=$row_dc0['existencias'];
				$quantity=$row_dc0['cantidad'];
				$diferencia=$row_dc0['diferencia'];

				$table_stock='stock';
				$form_data_st = array(
				'existencias' => $diferencia,
				'id_sucursal' => $id_sucursal,
				'comentario' => $comentario,
				);

				$where_clause_st=" WHERE id_producto='$id_prod'
				AND id_sucursal='$id_sucursal'
				";
				$actualizar_stock = _update($table_stock,$form_data_st,$where_clause_st );
				}
			}


			//Pedidos si hay
			$table_ped= 'pedidos';
			$form_data_ped = array(
				'finalizado' =>1,
			);
			$where_clause_ped=" WHERE idtransace='$id_pedido'";
			$insertar_ped = _update($table_ped,$form_data_ped, $where_clause_ped);

			//cxp  Insertar a Cuentas por pagar !!!
			$table_cxp= 'cxp';
			$sql_cxp="SELECT * FROM $table_cxp WHERE id_compras='$id_compras'
 		  AND  fecha='$fecha_movimiento'
 			AND id_proveedor='$id_proveedor'
 			AND alias_tipodoc='$id_tipodoc'
 		  AND id_sucursal='$id_sucursal'";
 			$result_cxp=_query($sql_cxp);
 			$row_cxp=_fetch_array($result_cxp);
 			$nrows_cxp=_num_rows($result_cxp);
 			if($nrows_cxp==0){
 				$form_data_cxp = array(
 				'id_proveedor' => $id_proveedor,
 				'alias_tipodoc'=>$id_tipodoc,
 				'fecha' => $fecha_movimiento,
 				'fecha_vence' => $fecha_vencimiento,
 				'numero_doc' => $numero_doc,'dias_credito' => $numero_dias,
 				'monto' => $total_compras,
 				'id_empleado' => $id_usuario,
 				'id_sucursal' => $id_sucursal,
 				'hora' => $hora,
				'dias_credito' => $numero_dias,
 				);
 				//actualizar cxp
				$where_clause_cxp="WHERE id_compras='$id_compras'";
 				$insertar_cxp = _update($table_cxp,$form_data_cxp,$where_clause_cxp);
 			}

		 $array = json_decode($array_json,true);
		 foreach ($array as $fila){
		 		if( $fila['precio_compra']>0 && $fila['cantidad']>0 ){
		 			$id_producto=$fila['id'];
		 			$cantidad=$fila['cantidad'];
		 			$precio_compra=$fila['precio_compra'];
		 			$descto=$fila['descto'];
		 			$subt_exento=$fila['subt_exento'];
		 			$subt_gravado=$fila['subt_gravado'];
					if(	$subt_exento>0){
						$subtotal=$subt_exento;
						$exento=1;
					}
					else{
						$subtotal=$subt_gravado;
						$exento=0;
					}
     		//detalle de compras
				$table_dc= 'detalle_compras';
				$sql_dc="SELECT * FROM $table_dc WHERE id_compras='$id_compras' AND id_producto='$id_producto'
				 ";
				 $result_dc=_query($sql_dc);

				 $nrows_dc=_num_rows($result_dc);

		 		$form_data_dc = array(
		 			'id_compras' => $id_compras,
		 			'id_producto' => $id_producto,
					// 'numero_doc' => $numero_doc,
		 			'cantidad' => $cantidad,
		 			'ultcosto' => $precio_compra,
		 			'descuento' => $descto, //es el porcentaje descto sin dividir entre 100
		 			'subtotal' => $subtotal,
		 			'exento' => $exento,
		 		);
				if( $nrows_dc>0){
					$row_dc=_fetch_array($result_dc);
					$cantidad_previa=$row_dc['cantidad'];

					$where_clause_dc=" WHERE id_compras='$id_compras'   AND id_producto='$id_producto'";
					$insertar_dc = _update($table_dc,$form_data_dc,$where_clause_dc );

				}
				if( $nrows_dc==0){
		 			$insertar_dc = _insert($table_dc,$form_data_dc );
				}

				//insertar todo en el temporal para despues comprara
				$table_tmp = 'tmp_compras';
				$insert_tmp = _insert( $table_tmp , $form_data_dc );

				//Insertar a Productos
				$table_pr= 'productos';
				$sql_producto="SELECT *
		 		FROM $table_pr
		 		WHERE  id_producto='$id_producto'
		 		";
				$result_pr=_query($sql_producto);
		 		$row_pr=_fetch_array($result_pr);
		 		$nrows_pr=_num_rows($result_pr);
				if($nrows_pr>0){
					$form_data_pr = array(
						'ultcosto' =>  $precio_compra,
						'costopro' => $precio_compra,
					);

					$where_clause_pr=" WHERE  id_producto='$id_producto'

					";
					$insertar_pr = _update($table_pr,$form_data_pr,$where_clause_pr );
				}
     		// Insertar en stock
		 		$sql_stock="SELECT id_stock, id_producto, existencias, minimo, id_sucursal, retirado, comentario
		 		FROM stock
		 		WHERE  id_producto='$id_producto'
		 		AND id_sucursal='$id_sucursal'
		 		";
		 		$result_st=_query($sql_stock);
		 		$row_st=_fetch_array($result_st);
		 		$nrows_st=_num_rows($result_st);



		  	$table_stock= 'stock';
		 		if($nrows_st>0){
			 		$existencia_actual=$row_st['existencias'];
					$nueva_existencia=$cantidad+$existencia_actual;
					$form_data_st = array(
					'existencias' => $nueva_existencia,
					'id_sucursal' => $id_sucursal,
					'comentario' => $comentario,
					);

					$where_clause_st=" WHERE  id_producto='$id_producto'
			 		AND id_sucursal='$id_sucursal'
			 		";
					$insertar_st = _update($table_stock,$form_data_st,$where_clause_st );
		 	}
		 	else{
				$existencia_actual=0;
				$nueva_existencia=$cantidad;
			 	$form_data_st = array(
			 	'id_producto' => $id_producto,
			 	'existencias' =>  $nueva_existencia,
			 	'id_sucursal' => $id_sucursal,
			 	'comentario' => $comentario,
			 	);
			 	$insertar_st = _insert($table_stock,$form_data_st );
		 	}
			// Insertar en kardex
			$table_kardex= 'kardex';
			$sql_ka="SELECT *
			FROM $table_kardex
			WHERE  id_producto='$id_producto'
			AND fechadoc='$fecha_movimiento'
			AND id_proveedor='$id_proveedor'
 			AND alias_tipodoc='$id_tipodoc'
			AND numero_doc='$numero_doc'
		  AND id_sucursal_origen='$id_sucursal'
			";
			$result_ka=_query($sql_ka);
			$row_ka=_fetch_array($result_ka);
			$nrows_ka=_num_rows($result_ka);

	if($nrows_ka>0){
		$form_data_ka = array(
			'ultcosto' =>  $precio_compra,
			'cantidade' => $cantidad,
			'stock_anterior' =>$existencia_actual,
			'stock_actual' => $nueva_existencia,
			'fechadoc' => $fecha_movimiento,
		);

		$where_clause_ka="WHERE  id_producto='$id_producto'
		AND fechadoc='$fecha_movimiento'
		AND id_proveedor='$id_proveedor'
		AND alias_tipodoc='$id_tipodoc'
		AND numero_doc='$numero_doc'
		AND id_sucursal_origen='$id_sucursal'
		";
		$insertar_ka = _update($table_kardex, $form_data_ka, $where_clause_ka );
	}
	else{
		$form_data_ka = array(
			'id_producto' => $id_producto,
			'id_proveedor' => $id_proveedor,
			'id_transacc' => $id_compras,
			'id_sucursal_origen' => $id_sucursal,
			'numero_doc'=>$numero_doc,
			'alias_tipodoc'=>$id_tipodoc,
			'ultcosto' =>  $precio_compra,
			'cantidade' => $cantidad,
			'stock_anterior' =>$existencia_actual,
			'stock_actual' => $nueva_existencia,
			'fechadoc' => $fecha_movimiento,
		);
		$insertar_ka = _insert($table_kardex, $form_data_ka);
	}

	 } //  	if( $fila['precio_compra']>0 && $fila['cantidad']>0 ){
	} // FOREACH

	//Delete the values on table detalle_compras  if  not exist in tmp
		$where_clause_dcc=" WHERE detalle_compras.id_compras='$id_compras' AND detalle_compras.id_producto  NOT IN (SELECT id_producto FROM tmp_compras)";
		$delete1 = _delete( $table_dc ,$where_clause_dcc);
		$where_clause_kar=" WHERE kardex.id_transacc='$id_compras' AND kardex.id_producto  NOT IN (SELECT id_producto FROM tmp_compras)";
		$delete2 = _delete( $table_kardex ,$where_clause_kar);
		$drop1=" DROP TABLE tmp_compras";
		$resultx=_query($drop1);

}//if $cuantos>0

//  if ($insertar1 && $insertar2 && $insertar3){
if ($insertar_fc ){
	_commit(); // transaction is committed
	$xdatos['typeinfo']='Success';
		 $xdatos['msg']='Registro de Inventario Actualizado !';
		 $xdatos['process']='insert';
		$xdatos['guardar']="compras: ".$insertar_fc." det compra: ".$insertar_dc." ";
	}
	else{
	_rollback(); // transaction not committed
		 $xdatos['typeinfo']='Error';
		 $xdatos['msg']='Registro de Inventario no pudo ser Actualizado !';
		 $xdatos['guardar']="compras: ".$insertar_fc." det compra: ".$insertar_dc." ";
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


	$sql3="select p.*,c.nombre from productos AS p
		JOIN colores AS c ON (p.id_color=c.id_color)
		where id_producto='$id_producto'
		";
	$result3=_query($sql3);
  $count3=_num_rows($result3);
	if($count3>0){
	$row3=_fetch_array($result3);
	$cp=$row3['costopro'];
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
function buscarcompras(){
	$id_compras= trim($_POST['id_compras']);
	$sql0="SELECT dc.id_producto, dc.cantidad
	FROM compras AS cp
	JOIN detalle_compras AS dc ON(cp.id_compras=dc.id_compras)
	WHERE cp.id_compras='$id_compras'";
	$result = _query($sql0);
	$array_prod = array();
	$numrows= _num_rows($result);
 for ($i=0;$i<$numrows;$i++){
	 $row = _fetch_array($result);
	$id_producto =$row['id_producto'];
	$cantidad =$row['cantidad'];
	$array_prod[] = array(
 		 'id_producto' => $row['id_producto'],
 		 'cantidad' =>  $row['cantidad'],
  );
 }
	//$xdatos['array_prod']=$array_prod;
	echo json_encode ($array_prod); //Return the JSON Array
}
function buscarpedido(){
	$id_pedido= trim($_POST['id_pedido']);
	$sql0="SELECT dp.id_producto, dp.cantidad
	FROM pedidos AS p
	JOIN detalle_pedidos AS dp ON(p.idtransace=dp.idtransace)
	WHERE p.idtransace='$id_pedido'";
	$result = _query($sql0);
	$array_prod = array();
	$numrows= _num_rows($result);
 for ($i=0;$i<$numrows;$i++){
	 $row = _fetch_array($result);
	$id_producto =$row['id_producto'];
	$cantidad =$row['cantidad'];
	$array_prod[] = array(
 		 'id_producto' => $row['id_producto'],
 		 'cantidad' =>  $row['cantidad'],
  );
 }
	//$xdatos['array_prod']=$array_prod;
	echo json_encode ($array_prod); //Return the JSON Array
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
	$cadena_salida= "<h3 class='text-danger'>Son: <strong>".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.</strong>&nbsp;&nbsp;</h3>";
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
function traerdatos() {
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
    $sqlParcial=get_sql($keywords, $id_color, $estilo, $talla, $barcode,$limite);
    $sql_final= $sqlJoined." ".$sqlParcial." ";
    $query = _query($sql_final);

		$num_rows = _num_rows($query);
		$filas=0;
    if($num_rows > 0){
            while($row = _fetch_array($query)) {
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
								$btnSelect='<input type="button" id="btnSelect" class="btn btn-primary fa" value="&#xf00c;">';
						?>
						<tr class='tr1'  tabindex="<?php echo $filas;?>">
						 <td class='col12 td1'><input type='hidden'  id='exento' name='exento' value='<?php echo $exento;?>'> <h5><?php echo $id_producto;?></h5></td>
						 <td class='col13 td1'><h5><?php echo $descripcion;?></h5></td>
						 <td class='col12 td1'><h5><?php echo $cp;?></h5></td>
						 <td class='col12 td1'><h5 class='text-success'><?php echo $estilo;?></h5></td>
						 <td class='col12 td1'><h5 class='text-success'><?php echo $talla;?></h5></td>
						 <td class='col12 td1'><h5 class='text-success'><?php echo $nombre;?></h5></td>
					 </tr>

          <?php
					$filas+=1;
          }
				}
  echo '<input type="hidden" id="cuantos_reg"  value="'.$num_rows.'">';
}
function get_sql( $keywords, $id_color, $estilo, $talla, $barcode,$limite){
	$andSQL='';
 $whereSQL="  WHERE pr.id_color=co.id_color ";

	$keywords=trim($keywords);
	//$andSQL.= " AND co.id_color='$id_color'";

	if(!empty($barcode)){
			$andSQL.= " AND  pr.barcode LIKE '{$barcode}%'";
	}
	else{
  if(!empty($keywords)){
  $andSQL.= "AND  pr.descripcion LIKE '%".$keywords."%'";
      if(!empty($estilo)){
          $andSQL.= " AND pr.estilo LIKE '{$estilo}%' ";
      }
      if(!empty($talla)){
          $andSQL.= " AND pr.talla LIKE '%{$talla}%'";
      }
			if($id_color!=-1){
					$andSQL.= " AND co.id_color='$id_color'";
			}
  }

  if(empty($keywords)  && !empty($estilo)){
		$andSQL.= "AND  pr.estilo LIKE '".$estilo."%' ";
		if(!empty($talla)){
				$andSQL.= " AND pr.talla LIKE '%".$talla."%' ";
		}
		if($id_color!=-1){
				$andSQL.= " AND co.id_color='$id_color'";
		}
   }
	 if(empty($keywords)  && empty($estilo) && !empty($talla)){
		$andSQL.= "AND pr.talla LIKE '%".$talla."%' ";
		if($id_color!=-1){
				$andSQL.= " AND co.id_color='$id_color'";
		}
	 }
	 if(empty($keywords)  && empty($estilo) && empty($talla) && ($id_color!=-1)) {
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
	case 'buscarpedido' :
	 		buscarpedido();
			break;
	case 'buscarcompras' :
			buscarcompras();
			break;
	}

 //}
}
?>
