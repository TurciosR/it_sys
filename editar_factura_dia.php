<?php
include_once "_core.php";
include ('num2letras.php');
//include ('facturacion_funcion_imprimir.php');
//include("escpos-php/Escpos.php");
function initial() {
	$id_factura=$_REQUEST["id_factura"];

	$title="Editar Factura   ";
	$_PAGE = array ();
	$_PAGE ['title'] = $title;
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2-bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/bootstrap-checkbox/bootstrap-checkbox.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

	echo "<style type='text/css'>
    #inventable{
    	font-family: 'Open Sans';
    	 font-style: normal;
    	 font-size: small;
		font-weight: 400;
		src: local('Open Sans'), local('OpenSans'), url(fonts/apache/opensans/OpenSans-Regular.ttf) format('truetype'), url(fonts/apache/opensans/OpenSans.woff) format('woff');
    }
    .table thead tr > th.success{
		background-color: #428bca !important;
		color: white !important;
	}
	.table > tfoot > tr > .thick-line {
		border-top: 2px solid;
	}
	</style>";
	include_once "header.php";
	include_once "main_menu.php";



	$sql="SELECT * FROM producto";
	$result=_query($sql);
	$count=_num_rows($result);

	$id_usuario=$_SESSION["id_usuario"];
	$id_sucursal=$_SESSION['id_sucursal'];
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	$sql_iva="select iva from empresa";
	$result=_query($sql_iva);
	$row=_fetch_array($result);
	$iva=$row['iva']/100;
	 $total=0;
?>

    <div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-2"></div>
    </div>
        <div class="wrapper wrapper-content  animated fadeInRight">
           <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
						<?php
						//permiso del script
						if ($links!='NOT' || $admin=='1' ){
							echo"<div class='ibox-title'>
                  <h5>$title</h5>
                  </div>";
            ?>
                        <div class="ibox-content">

					 <div class="row">
						 <div id='form_datos_cliente'>
                            <div class="col-lg-6">
                              <div class="form-group">
								  <label>Seleccione cliente</label>

							<?php
							$sql_cliente2="SELECT cliente.id_cliente,id_factura_dia,nombre,factura_dia.fecha,
               cliente.apellido
							 FROM cliente,factura_dia
               WHERE id_factura_dia='$id_factura'
							 AND factura_dia.id_cliente=cliente.id_cliente
               ORDER BY apellido";
							 $qcliente2=_query($sql_cliente2);
							 while($row_cliente2=_fetch_array($qcliente2)){
                	$id_cliente2=$row_cliente2['id_cliente'];
                  $nombre_cliente2=$row_cliente2['nombre']." ".$row_cliente2['apellido'];
									$fecha=$row_cliente2['fecha'];
									echo "<input type='text' class='form-control' id='id_cliente' name='id_cliente' value='$nombre_cliente2' readOnly>";
            	 }
								 echo "</div>"; //<div class='form-group'>
                 echo  "</div>";// <div class="col-lg-6">
                 echo "</div>";//<!--<div id='form_datos_cliente'> -->

                 $fecha_actual=date("Y-m-d");
								 echo "<div class='col-md-4'>";
								 echo "<div class='form-group has-info single-line'><label>Fecha:</label> <input type='text' placeholder='Fecha' class='form-control' id='fecha' name='fecha' value='$fecha' readOnly></div>";
								 echo "</div>";//<div class='col-md-4'>"
                 echo "</div>";//row
					echo "</div>";// <div class="ibox-content">
			?>
						<div class="ibox-content">
							<!--load datables estructure html-->
							<header><h4 class="text-navy">FACTURACION</h4></header>
							<section>
								<div class="table-responsive m-t">
									<table class="table table-condensed table-striped" id="inventable">
									<thead class="thead-inverse">
										<tr>
										<th class='success'>Id</th>
										<th class='success'>Descripci&oacute;n</th>
										<th class='success'>Stock</th>

										<th class='success'>Precio Vta.</th>
										<th class='success'>Cantidad</th>
										<th class='success' >Subtotal</th>

										<th class='success'>Acci&oacute;n</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$sql_fact_det="SELECT factura_dia.id_factura_dia, factura_dia.id_cliente, factura_dia.fecha, factura_dia.numero_doc, factura_dia.total,
									 factura_dia.id_sucursal,
										factura_detalle_dia.id_factdet_dia,factura_detalle_dia.cantidad,	factura_detalle_dia.id_producto,
										factura_detalle_dia.precio_venta, factura_detalle_dia.subtotal
										FROM factura_dia  JOIN factura_detalle_dia  ON factura_dia.id_factura_dia=factura_detalle_dia.id_factura_dia
										WHERE
										factura_dia.id_factura_dia='$id_factura'
										";

										$result_fact_det=_query($sql_fact_det);
										$count_fact_det=_num_rows($result_fact_det);

										$total_cant_consigna=0;
										$total_dinero_consigna=0;
										$total_cant_facturado_ante=0;
										$dev_stock=0;
										$total_dev_stock=0;
										$filas=0;
										for($i=0;$i<$count_fact_det;$i++){
											$row=_fetch_array($result_fact_det);
											$numero_doc=$row['numero_doc'];
											$id_factura=$row['id_factura_dia'];
											 $id_producto=$row['id_producto'];
											//$tipo_prod=$row['tipo_prod_serv'];
										//	$anulada=$row['anulada'];
											$cantidad=$row['cantidad'];
											$precio_venta=$row['precio_venta'];
											$subtotal=$row['subtotal'];
											$total=$row['total'];
									//		$id_usuario=$row['id_usuario'];
											$total=sprintf("%.2f", $total);

											$sql1="SELECT producto.id_producto,producto.descripcion,producto.unidad,producto.exento,producto.barcode,
											producto.utilidad_activa,producto.utilidad_seleccion,producto.porcentaje_utilidad1,producto.perecedero,
											producto.porcentaje_utilidad2,producto.porcentaje_utilidad3,
											producto.porcentaje_utilidad4,producto.imagen,producto.combo,
											stock.stock,stock.costo_promedio,stock.precio_sugerido
											FROM producto JOIN stock ON producto.id_producto=stock.id_producto
											WHERE producto.id_producto='$id_producto'
											";
											$stock1=_query($sql1);
											$row1=_fetch_array($stock1);
											$nrow1=_num_rows($stock1);
											$unidades=round($row1['unidad'],2);
											$utilidad_activa=$row1['utilidad_activa'];
											$utilidad_seleccion=$row1['utilidad_seleccion'];
											$id_producto=$row1['id_producto'];
											$descripcion=$row1['descripcion'];
											$barcode=$row1['barcode'];
											$combo=$row1['combo'];
											$perecedero=$row1['perecedero'];
											$descprod="[".$barcode."] ".$descripcion;

											//consultar si es perecedero
											$fecha_caducidad="0000-00-00";
											$stock_fecha=0;
											if($perecedero==1){
												$sql_perecedero="SELECT id_prod_perecedero, id_producto, fecha_entrada, fecha_caducidad, entrada,
												salida, estado, numero_doc, id_sucursal
												FROM producto_perecedero
												WHERE id_producto='$id_producto'
												AND entrada>salida
												AND estado='VIGENTE'
												ORDER BY fecha_caducidad";
												$result_perecedero=_query($sql_perecedero);
												$row_perecedero=_fetch_array($result_perecedero);
												$nrow_perecedero=_num_rows($result_perecedero);
												if($nrow_perecedero>0){
													$entrada=$row_perecedero['entrada'];
													$salida=$row_perecedero['salida'];
													$fecha_caducidad=$row_perecedero['fecha_caducidad'];
													$fecha_caducidad=ED($fecha_caducidad);
													$stock_fecha=$entrada-$salida;
												}

												//$descprod.="&nbsp;"."<label class='text-danger'>--> Existencias:".$stock_fecha." Fecha prox. Caducidad: ".$fecha_caducidad."</label>";

											}
											//consultar si es perecedero

											//agregar la descrip de caducidad y fecha
											//agregar el orden de precios de mayor a menor
											$pu1=$row1['porcentaje_utilidad1']/100;
											$pu2=$row1['porcentaje_utilidad2']/100;
											$pu3=$row1['porcentaje_utilidad3']/100;
											$pu4=$row1['porcentaje_utilidad4']/100;

											$cp=$row1['costo_promedio'];
											$existencias=$row1['stock'];
											$exento=$row1['exento'];
											$precio_sugerido=$row1['precio_sugerido'];
											$imagen=$row1['imagen'];
											$costos_pu=array($pu1,$pu2,$pu3,$pu4);

											$pv1=$cp+($cp*$pu1) ;
											$pv2=$cp+($cp*$pu2) ;
											$pv3=$cp+($cp*$pu3) ;
											$pv4=$cp+($cp*$pu4) ;

											if ($iva>0 && $exento==0){
												$pv1=$pv1+($pv1*$iva);
												$pv2=$pv2+($pv2*$iva);
												$pv3=$pv3+($pv3*$iva);
												$pv4=$pv4+($pv4*$iva);
											}
											$pv1=round($pv1,2);
											$pv2=round($pv2,2);
											$pv3=round($pv3,2);
											$pv4=round($pv4,2);


											$precios_vta=array($pv4,$pv3,$pv2,$pv1);
											rsort($precios_vta);
											$precios_vta0=array($pv4,$pv3,$pv2,$pv1);
											rsort($precios_vta0);
											//$pv_minimo = array_pop($precios_vta0);
											$pv_minimo = end($precios_vta);



											if($existencias<=$cantidad){
												$existencias=$cantidad;
											}
											$filas=$filas+1;
											?>
											<tr id="<?php echo $filas ?>">
											<?php
											echo "<td>".$id_producto."</td>";
											echo "<td>".$descprod."</td>";
											echo "<td>".$existencias."</td>";
											/*
											echo "<td>";
											echo "<select name='select_precios' id='select_precios".$filas."'  class='form-control'>";
												foreach ($precios_vta as $pv){
													echo "<option value='$pv'>$pv</option> ";
												}
												echo "</select>";
											echo "</td>";
											*/
												echo "<td class='text-right'>".$precio_venta."</td>";
											//echo "<td><input type='hidden'  id='precio_venta_inicial' name='precio_venta_inicial' value='".$pv_minimo."' ><div class='col-xs-2'><input type='text'  class='form-control decimal'  id='precio_venta' name='precio_venta' value='".$precio_venta."' style='width:80px;' readOnly></div></td>";
											echo "<td><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value=".$cantidad." style='width:60px;'></div></td>";
											echo "<td id='subtot' class='text-right'>".$subtotal."</td>";

											echo "<td class='Delete'><a href='#'><i class='fa fa-times-circle'></i></a></td>";
											echo "</tr>";
										}
										?>

									</tbody>
									<tfoot>
										<tr>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<td class="thick-line text-center"><strong>TOTAL $:</strong></td>
										<td  class="thick-line text-right" id='total_dinero' ><strong><?php echo $total; ?></strong></td>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										</tr>
									</tfoot>
								</table>
								<?php
								list($entero,$decimal)=explode('.',$total);
								$enteros_txt=num2letras($entero);
							$decimales_txt=num2letras($decimal);

							if($entero>1)
								$dolar=" dolares";
							else
								$dolar=" dolar";
							$cadena_salida= "Son: <strong>".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.</strong>";
							echo "<div class='well m-t'  id='totaltexto'>".$cadena_salida." </div>";
							echo "<input type='hidden' name='id_empleado' id='id_empleado' value='$id_usuario'>";
								?>

							<input type='hidden' name='totalfactura' id='totalfactura' value='0'>
							<input type='hidden' name='id_factura' id='id_factura' value='<?php echo $id_factura;?>'>
							<input type='hidden' name='urlprocess' id='urlprocess' value='editar_factura_dia.php'>
						</div><!--div class="table-responsive m-t"-->


					</section>

							<div class="title-action" id='botones'>
								<button type="submit" id="submit1" name="submit1" class="btn btn-primary"><i class="fa fa-check"></i> Guardar</button>
							</div>

								<!--/div-->
						</div><!--div class='ibox-content'-->
       		</div><!--<div class='ibox float-e-margins' -->
		</div> <!--div class='col-lg-12'-->
	</div> <!--div class='row'-->
</div><!--div class='wrapper wrapper-content  animated fadeInRight'-->

<?php
include_once ("footer.php");

echo "<script src='js/funciones/editar_factura_dia.js'></script>";
//echo "<script src='js/funciones/editar_factura_dia.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}
function numero_tiquete($ult_doc){
	$ult_doc=trim($ult_doc);
	$len_ult_valor=strlen($ult_doc);
	$long_num_fact=7;
	$long_increment=$long_num_fact-$len_ult_valor;
	$valor_txt="";
	if ($len_ult_valor<$long_num_fact){
		for ($j=0;$j<$long_increment;$j++){
			$valor_txt.="0";
		}
	}
	else{
			$valor_txt="";
	}
	$valor_txt=$valor_txt.$ult_doc."_COF";
	return $valor_txt;
}
function insertar(){
	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];
	$id = '1';
	$id_usuario=$_SESSION["id_usuario"];
	$id_sucursal=$_SESSION["id_sucursal"];
   $id_factura=$_POST['id_factura'];
	$tipo_entrada_salida='FACTURA LOTE CONSUMIDOR';
	$fecha_movimiento= $_POST['fecha_movimiento'];
	$id_cliente=$_POST['id_cliente'];
	$total_venta = $_POST['total_ventas'];
	$insertar1=false;
	$insertar2=false;
	$insertar_fact=false;
	$insertar_fact_det=false;
	$insertar_numdoc =true;
	$insertar4 =false;

	$xdatos['typeinfo']='';
	$xdatos['msg']='';
	$xdatos['process']='';

	$observaciones=$tipo_entrada_salida;
	if ($cuantos>0){
			$sql_fact="SELECT * FROM factura_dia WHERE id_factura_dia='$id_factura'";
			$result_fact=_query($sql_fact);
			$row_fact=_fetch_array($result_fact);
			$nrows_fact=_num_rows($result_fact);

			 _begin();
			 if($nrows_fact>0){
				$table_fact= 'factura_dia';

				$form_data_fact = array(
				'total' => $total_venta
				);
				$where_clause =" WHERE id_factura_dia='$id_factura' ";
				$insertar_fact=  _update($table_fact,$form_data_fact, $where_clause  );

			}
			$sql_fact_det="SELECT *
			FROM factura_detalle_dia
			WHERE id_factura_dia='$id_factura'
			";
			$result_fact_det2=_query($sql_fact_det);

			$nrows_fact_det=_num_rows($result_fact_det2);
			$array_existencias_previas=array();
			 if($nrows_fact_det>0){
					for ($i=0;$i<$nrows_fact_det ;$i++){
						$row_fact_det=_fetch_array($result_fact_det2);
						$id_producto= $row_fact_det['id_producto'];
						$cant_fact= $row_fact_det['cantidad'];
						$sql_stock="SELECT * FROM stock WHERE  id_sucursal='$id_sucursal' and id_producto='$id_producto' ";
						$result_stock=_query($sql_stock);
						$row_stock=_fetch_array($result_stock);
						$nrows_stock=_num_rows($result_stock);
						$stock_act= $row_stock['stock'];
						$precio_venta= $row_stock['precio_sugerido'];
						$stock_new=$cant_fact+$stock_act;
						$id_cant=$id_producto."|".$cant_fact;
						//saco las existencias previas para agregarlas cuando se necesita
						array_push($array_existencias_previas,  $id_cant);
						$table_st= 'stock';
						//Actualizar en stock si  hay registro del producto
						$where_clause_st="WHERE id_producto='$id_producto'";
						$form_data_st = array(
							'stock' => $stock_new
						);
						$insertar2 = _update($table_st,$form_data_st, $where_clause_st );
					}
			}


			$listadatos=explode('#',$stringdatos);
			for ($i=0;$i<$cuantos ;$i++){
				list($id_producto,$stock,$precio_venta1,$cantidad,$subtotal)=explode('|',$listadatos[$i]);

				$sql2="select producto.id_producto,producto.unidad,producto.perecedero,
					stock.stock,stock.costo_promedio
					from producto,stock
					where producto.id_producto='$id_producto'
					and producto.id_producto=stock.id_producto
					";

				$stock2=_query($sql2);
				$row2=_fetch_array($stock2);
				$nrow2=_num_rows($stock2);
				$unidad=$row2['unidad'];
				$existencias=$row2['stock'];
				$perecedero=$row2['perecedero'];
				$cant_facturar=0;
				$table2= 'stock';
				if($existencias>=$cantidad){
					$cantidad_stock=$existencias-$cantidad;
					$cant_facturar=$cantidad;
				}
				else{
					$cantidad_stock=0;
					$cant_facturar=$existencias;
				}
				//Actualizar en stock si  hay registro del producto
				if ($nrow2>0){
					$where_clause="WHERE id_producto='$id_producto'";

					$form_data2 = array(
					'stock' => $cantidad_stock
					);
					$insertar2 = _update($table2,$form_data2, $where_clause );
				}

				//$table_fd = 'factura_detalle_dia';
				//$where_clause_fd = "WHERE id_factura_dia='$id_factura' ";
				//$delete = _delete ($table_fd, $where_clause_fd );

				$table_mp = 'movimiento_producto';
				$where_clause_mp = "WHERE fecha_movimiento='$fecha_movimiento'
				AND id_producto='$id_producto'
				AND tipo_entrada_salida='$tipo_entrada_salida'";
				$delete2 = _delete ($table_mp, $where_clause_mp);

				$subtotal1=$precio_venta1*$cantidad;
				$table_fact_det= 'factura_detalle_dia';
				$data_fact_det = array(
					'id_factura_dia' => $id_factura,
					'id_producto' => $id_producto,
					'cantidad' => $cantidad,
					'precio_venta' => $precio_venta1,
					'subtotal' => $subtotal1,
				);
				$where_fact_det="WHERE id_producto='$id_producto' AND id_factura_dia='$id_factura'
				AND fecha='$fecha_movimiento'";
					if ($cantidad>0 && $stock_new>0){
						$insertar_fact_det = _update($table_fact_det,$data_fact_det, $where_fact_det);
					}
					$sql1="SELECT * FROM movimiento_producto
					WHERE id_producto='$id_producto'
					AND tipo_entrada_salida='$tipo_entrada_salida'
					AND  fecha_movimiento='$fecha_movimiento'
					";
					$stock1=_query($sql1);
					$row1=_fetch_array($stock1);
					$nrow1=_num_rows($stock1);


					$table1= 'movimiento_producto';
					$form_data1 = array(
					'id_producto' => $id_producto,
					'salida' => $cantidad,
					'observaciones' => $observaciones,
					'tipo_entrada_salida' => $tipo_entrada_salida,
					'precio_venta' => $precio_venta1,
					);

					if($existencias>=$cantidad)
						$cantidad_stock=$existencias-$cantidad;
					else
						$cantidad_stock=0;

					if ($nrow1==0){
						$insertar1 = _insert($table1,$form_data1 );
					}
					else{
						$where_clause1="WHERE id_producto='$id_producto' AND tipo_entrada_salida='$tipo_entrada_salida'
						AND fecha_movimiento='$fecha_movimiento'";
						$insertar1 =  _update($table1,$form_data1, $where_clause1 );
					}
					//Actualizar en stock si  hay registro del producto
					$table2= 'stock';
					if ($nrow2>0){
						$where_clause="WHERE id_producto='$id_producto' and id_sucursal='$id_sucursal'";
						$form_data2 = array(
						'id_producto' => $id_producto,
						'stock' => $cantidad_stock,
						'precio_sugerido' => $precio_venta1,
						'id_sucursal' => $id_sucursal
						);
						$insertar2 = _update($table2,$form_data2, $where_clause );
					}

					//si es perecedero no se esta usando de momento
					if($perecedero==1){
						$sql_perecedero="SELECT id_prod_perecedero, id_producto, fecha_entrada, fecha_caducidad, entrada,
						salida, estado, numero_doc, id_sucursal FROM producto_perecedero
						WHERE id_producto='$id_producto'
						AND id_sucursal='$id_sucursal'
						AND entrada>=salida
						AND estado='VIGENTE'
						ORDER BY fecha_caducidad";
						$result_perecedero=_query($sql_perecedero);

						$table_pp='producto_perecedero';
						$nrow_perecedero=_num_rows($result_perecedero);
						$fecha_mov=ED($fecha_movimiento);
						$diferencia=0;

						if($nrow_perecedero>0){
							for($i=0;$i<$nrow_perecedero;$i++){
								$row_perecedero=_fetch_array($result_perecedero);
								$entrada=$row_perecedero['entrada'];
								$salida=$row_perecedero['salida'];
								$fecha_caducidad=$row_perecedero['fecha_caducidad'];
								$id_prod_perecedero=$row_perecedero['id_prod_perecedero'];
								$id_product0=$row_perecedero['id_producto'];
								$fecha_caducidad=ED($fecha_caducidad);
								//Cantidad original guardada de producto para la venta
								foreach($array_existencias_previas as $id_cantidad){
									list($id_product,$qty1)=explode("|",$id_cantidad);
									if ($id_producto==$id_product){
										$cantidad=$cantidad-$qty1;
									}
								}
								$stock_fecha=$entrada-$salida;
								$comparafecha=compararFechas ("-",$fecha_caducidad,$fecha_mov);
								if($cantidad<$stock_fecha){
									$cant_sale=$cantidad+$salida;
									$diferencia=0;
									$cantidad=0;
									$estado='VIGENTE';
								}

								if($cantidad>=$stock_fecha){
									$cant_sale=$entrada;
									$diferencia=$cantidad-$stock_fecha;
									$cantidad=$diferencia;
									$estado='FINALIZADO';

								}
								//valida si la fecha de vencimineto ya expiro
								if($comparafecha<0)
									$estado='VENCIDO';

								$where_clause_pp="WHERE id_producto='$id_producto'
									AND id_sucursal='$id_sucursal'
									AND entrada>=salida
									AND id_prod_perecedero='$id_prod_perecedero'";
								$form_data_pp = array(
								'salida' => $cant_sale,
								'estado' => $estado
								);
							$insertar4 = _update($table_pp,$form_data_pp, $where_clause_pp );
							//si la cantidad vendida no se pasa de la existencia de x lote perecedero  se sale del bucle for
							if ($diferencia==0)
								break;

					    	}
						}
					} //si es perecedero
					else
						$insertar4 =true;

			}//for
		if($insertar_numdoc  && $insertar1 && $insertar2 && $insertar_fact && $insertar_fact_det ){
					_commit(); // transaction is committed
					$xdatos['typeinfo']='Success';
					$xdatos['msg']='<strong>Factura Actualizada con Exito! </strong>';
					$xdatos['process']='insert';
					$xdatos['factura']=$id_factura;
					$xdatos['insertados']=" factura :".$insertar_fact." factura detalle:".$insertar_fact_det." mov prod:".$insertar1." stock:".$insertar2 ;
					//$xdatos['uid_product']="id_prod:".$id_product." qty:".$qty1;
				}else{
					_rollback(); // transaction rolls back
					$xdatos['typeinfo']='Error';
					$xdatos['msg']='Registro de Factura no pudo ser Actualizado !';
					$xdatos['process']='noinsert';
					$xdatos['factura']="";
					$xdatos['insertados']="Numbdoc: ".$insertar_numdoc." insertar1: ".$insertar1." insertar2:  ".$insertar2." insertar_fact:".$insertar_fact." insertar_fact_det:".$insertar_fact_det." insertar4:".$insertar4 ;

				}
		}//if

	echo json_encode($xdatos);
}
function consultar_stock(){
	$id_producto = $_REQUEST['id_producto'];
	$tipo = $_REQUEST['tipo'];
	$id_usuario=$_SESSION["id_usuario"];
	$id_sucursal=$_SESSION['id_sucursal'];

	$iva=0;
	$sql_iva="select iva from empresa";
	$result=_query($sql_iva);
	$row=_fetch_array($result);
	$iva=$row['iva']/100;

	if ($tipo =='PRODUCTO'){

	$sql1="SELECT producto.id_producto,producto.descripcion,producto.unidad,producto.exento,
		producto.utilidad_activa,producto.utilidad_seleccion,producto.porcentaje_utilidad1,
		producto.porcentaje_utilidad2,producto.porcentaje_utilidad3,
		producto.porcentaje_utilidad4,producto.imagen,producto.combo,producto.perecedero,
		stock.stock,stock.costo_promedio,stock.precio_sugerido
		FROM producto JOIN stock ON producto.id_producto=stock.id_producto
		WHERE producto.id_producto='$id_producto'
		AND stock.id_sucursal='$id_sucursal'
		";
		$stock1=_query($sql1);
		$row1=_fetch_array($stock1);
		$nrow1=_num_rows($stock1);
		if ($nrow1>0){
		$unidades=round($row1['unidad'],2);
		$utilidad_activa=$row1['utilidad_activa'];
		$utilidad_seleccion=$row1['utilidad_seleccion'];
		$perecedero=$row1['perecedero'];

		$pu1=$row1['porcentaje_utilidad1']/100;
		$pu2=$row1['porcentaje_utilidad2']/100;
		$pu3=$row1['porcentaje_utilidad3']/100;
		$pu4=$row1['porcentaje_utilidad4']/100;
		$combo=$row1['combo'];
		$cp=$row1['costo_promedio']/$unidades;
		$existencias=$row1['stock'];
		$exento=$row1['exento'];
	    $precio_sugerido=$row1['precio_sugerido'];
		$imagen=$row1['imagen'];
		$costos_pu=array($pu1,$pu2,$pu3,$pu4);

		$pv1=$cp+($cp*$pu1) ;
		$pv2=$cp+($cp*$pu2) ;
		$pv3=$cp+($cp*$pu3) ;
		$pv4=$cp+($cp*$pu4) ;

		if ($iva>0 && $exento==0){
			$pv1=$pv1+($pv1*$iva);
			$pv2=$pv2+($pv2*$iva);
			$pv3=$pv3+($pv3*$iva);
			$pv4=$pv4+($pv4*$iva);
		}
		$pv1=round($pv1,2);
		$pv2=round($pv2,2);
		$pv3=round($pv3,2);
		$pv4=round($pv4,2);

		$precios_vta=array($pv4,$pv3,$pv2,$pv1);
		$precio_venta=0.0;

		 if( $utilidad_activa==true || $utilidad_activa==1){
			switch ($utilidad_seleccion) {
			case 1:
				$precio_venta=$cp*(1+$pu1/100);
				break;
			case 2:
				$precio_venta=$cp*(1+$pu2/100);
				break;
			case 3:
				$precio_venta=$cp*(1+$pu3/100);
				break;
			case 4:
				$precio_venta=$cp*(1+$pu4/100);
				break;
			 }//switch ($utilidad_seleccion]) {
		}//if( $utilidad_activa==true){
		else{
			$precio_venta=$precio_sugerido;
		}

		//consultar si es perecedero
		$fecha_caducidad="0000-00-00";
		$stock_fecha=0;
		if($perecedero==1){
			$sql_perecedero="SELECT id_prod_perecedero, id_producto, fecha_entrada, fecha_caducidad, entrada,
			salida, estado, numero_doc, id_sucursal FROM producto_perecedero
			WHERE id_producto='$id_producto'
			AND id_sucursal='$id_sucursal'
			AND entrada>salida
			AND estado='VIGENTE'
			ORDER BY fecha_caducidad";
			$result_perecedero=_query($sql_perecedero);
			$row_perecedero=_fetch_array($result_perecedero);
			$nrow_perecedero=_num_rows($result_perecedero);
			if($nrow_perecedero>0){
				$entrada=$row_perecedero['entrada'];
				$salida=$row_perecedero['salida'];
				$fecha_caducidad=$row_perecedero['fecha_caducidad'];
				$fecha_caducidad=ED($fecha_caducidad);
				$stock_fecha=$entrada-$salida;
			}

		}
		}
		//si no hay stock devuelve cero a todos los valores !!!
		if ($nrow1==0){
		$existencias=0;
		$precio_venta=0;
		$costos_pu=array(0,0,0,0);
		$precios_vta=array(0,0,0,0);
		$cp=0;
		$iva=0;
		$unidades=0;
		$imagen='';
	    $combo=0;
		$fecha_caducidad='0000-00-00';
		$stock_fecha=0;

		}
	}
	else{ //si es servicio traemos los datos de la tabla servicio

		$sql1="select * from servicio where id_servicio='$id_producto'";
			 $stock1=_query($sql1);
			 $row1=_fetch_array($stock1);
			 $nrow1=_num_rows($stock1);

			 $precio_venta=$row1['precio'];
			 $existencias='1';
			 $costos_pu=$precio_venta;
			 $unidades='1';
	}
		$precio_venta=round($precio_venta,2);
		$xdatos['existencias'] = $existencias;
		$xdatos['precio_venta'] = $precio_venta;
		$xdatos['costos_pu'] = $costos_pu;
		$xdatos['costo_prom'] = $cp;
		$xdatos['precios_vta'] = $precios_vta;
		$xdatos['iva'] = $iva;
		$xdatos['unidades'] = $unidades;
		$xdatos['imagen'] = $imagen;
		$xdatos['combo'] = $combo;
		$xdatos['fecha_caducidad'] = $fecha_caducidad;
		$xdatos['stock_fecha'] =$stock_fecha;
	echo json_encode($xdatos); //Return the JSON Array
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

function imprimir_fact() {
	$numero_doc = $_POST['numero_doc'];
	$id_ccf_cof= $_POST['id'];
	if ($id_ccf_cof=='1'){
		$numero_docx = $numero_doc."_COF";
		$tipo_fact="COF";
	}
	else{
		$numero_docx = $numero_doc."_CCF";
		$tipo_fact="CCF";
	}
	print_fact($numero_doc,$tipo_fact);
}

//functions to load
if(!isset($_REQUEST['process'])){
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
	case 'mostrar_datos_cliente':
		mostrar_datos_cliente();
		break;
	case 'consultar_stock':
		consultar_stock();
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
	case 'reimprimir' :
		reimprimir();
		break;
	}

 //}
}
?>
