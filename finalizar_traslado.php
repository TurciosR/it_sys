<?php
include_once "_core.php";
include ('num2letras.php');
/*
include("escpos-php/Escpos.php");
include ('facturacion_funcion_imprimir.php');
*/
function initial() {
	$title='Finalizar Traslado';
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
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	$id_traslado= $_REQUEST['id_traslado'];
	$id_usuario=$_SESSION["id_usuario"];
	$id_sucursal_destino= $_REQUEST['id_sucursal_destino'];
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

	$sql_user="select * from usuario where id_usuario='$id_user'";
	$result_user= _query($sql_user);
	$row_user=_fetch_array($result_user);
	$nrow=_num_rows($result_user);
	$id_sucursal=$row_user['id_sucursal'];
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
						echo"
                        <div class='ibox-title'>
                            <h5>$title</h5>
                        </div>";
                        ?>
                        <div class="ibox-content">
					  <div class="row">
							<?php

							$sql="SELECT traslado.*,
							usuario.nombre as nombreuser
							FROM traslado
							JOIN usuario ON traslado.id_usuario=usuario.id_usuario
							WHERE finalizada=0
							AND id_traslado='$id_traslado'
							AND traslado.id_sucursal_destino='$id_sucursal_destino'";

							$result=_query($sql);
							$count=_num_rows($result);
							$rows=_fetch_array($result);
							$numero_doc=$rows['numero_doc'];
							//$nombre_cliente=$rows['nombre']." ".$rows['apellido'];
							$nombre_empleado=$rows['nombreuser'];
							$finalizada=$rows['finalizada'];
							$fecha_actual=date("Y-m-d");
							echo "<div class='col-lg-6' id='mostrar_numero_doc'>";
							echo "<div class='form-group has-info single-line'><label>Número de Traslado</label> <input type='text' placeholder='Numero de Factura' class='form-control' id='numero_doc' name='numero_doc' value='$numero_doc'></div>";
							echo "</div>";
							echo "<div class='col-lg-6'>";
							echo "<div class='form-group has-info single-line'><label>Fecha:</label> <input type='text' placeholder='Fecha' class='datepick form-control' id='fecha' name='fecha' value='$fecha_actual'></div>";
							echo "</div>";
							/*
							echo "<div class='col-lg-6'>";
							echo "<div class='form-group has-info single-line'><label>Cliente:</label><input type='text' value='$nombre_cliente' class='form-control' readOnly></div>";
							echo "</div>";
							*/
							echo "<div class='col-lg-6'>";
							echo "<div class='form-group has-info single-line'><label>Vendedor:</label><input type='text' value='$nombre_empleado' class='form-control' readOnly></div>";
							echo "</div>";
						?>
					</div>  <!--div class="row"-->
					<div class="ibox ">
					<div class="ibox-content">
					<!--load datables estructure html-->
					<header><h4 class="text-navy">traslado</h4></header>
					<section>
					<div class="table-responsive m-t">
						<table class="table  table-bordered table-condensed" id="inventable">
							<thead>
								<tr>
									<th>Id</th>
									<th>Nombre</th>
									<th>Precio Vta.</th>
									<th>Cant Enviada</th>
									<th>Subtotal</th>
									<th>Cant Trasladar</th>
									<th>Dev. Stock</th>
									<th>Subt Trasladar</th>
								</tr>
							</thead>
						<tbody>
					<?php
		if ($finalizada==0){
	     //Datos de Consignacin y traslado_detalle
		$sql_fact_det="SELECT  producto.id_producto, producto.descripcion,traslado_detalle.*
		FROM traslado_detalle
		JOIN producto ON traslado_detalle.id_prod_serv=producto.id_producto
		WHERE  traslado_detalle.id_traslado='$id_traslado'
		AND  traslado_detalle.tipo_prod_serv='PRODUCTO' 		";

		//AND traslado_detalle.id_sucursal_destino='$id_sucursal_destino'";

		$result_fact_det=_query($sql_fact_det);
		$nrows_fact_det=_num_rows($result_fact_det);
		$total_cant_consigna=0;
		$total_dinero_consigna=0;
		for($i=0;$i<$nrows_fact_det;$i++){
			$row_fact_det=_fetch_array($result_fact_det);
			$id_producto =$row_fact_det['id_producto'];
			$descripcion =$row_fact_det['descripcion'];
			$id_traslado_detalle =$row_fact_det['id_traslado_detalle'];
			$id_prod_serv =$row_fact_det['id_prod_serv'];
			$enviado =$row_fact_det['enviado'];
			$precio_venta =$row_fact_det['precio_venta'];
			$subt =$row_fact_det['subtotal'];
			$id_empleado =$row_fact_det['id_empleado'];
			$tipo_prod_serv =$row_fact_det['tipo_prod_serv'];
			$total_cant_consigna=$enviado+$total_cant_consigna;
			$total_dinero_consigna=$subt+$total_dinero_consigna;
			$total_dinero_traslado=	sprintf("%.2f",$total_dinero_consigna);
		    echo "<tr>";
		    echo "<td>$id_producto</td>";
		    echo "<td>$descripcion</td>";
		    echo "<td>$precio_venta</td>";
		    echo "<td id='cant_consigna'>$enviado</td>";
		    echo "<td id='subtot'>$subt</td>";
		    echo "<td><input type='text'  class='form-control decimal' id='cant_fact' value='$enviado'></td>";
		    echo "<td id='dev_stock'>0</td>";
		    echo "<td id='subtot_fact'>$subt</td>";
		    echo "</tr>";
		 }
	 } //if ($finalizada==0){
		?>
							</tbody>
							<tfoot>
									<tr>
									<td></td>
									<td><strong>TOTALES:</strong></td>
									<td></td>
									<?php
									echo "<td id='total_cant_consigna'>$total_cant_consigna</td>";
									echo "<td id='total_dinero_consigna'>$total_dinero_traslado</td>";
									echo "<td id='total_cant_fact'>$total_cant_consigna</td>";
									echo "<td id='total_dev_stock'>0</td>";
									echo "<td id='total_dinero_fact'>$total_dinero_traslado</td>";

									list($entero,$decimal)=explode('.',$total_dinero_traslado);
									$enteros_txt=num2letras($entero);
									$decimales_txt=num2letras($decimal);

									if($entero>1)
										$dolar=" dolares";
									else
										$dolar=" dolar";
									$cadena_salida= " ".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.";
									//echo $cadena_salida;
									?>


									</tr>
								</tfoot>
						</table>
						</div>

						<div></div>
							<?php echo "<div class='well m-t'  id='totaltexto'><strong>Son: $cadena_salida</strong> </div>"; ?>
							<input type='hidden' name='totaltraslado' id='totaltraslado' value='0'>

					</section>

                    <div class="title-action" id='botones'>
						<button type="submit" id="submit1" name="submit1" class="btn btn-primary"><i class="fa fa-check"></i> Guardar</button>
						<!--button type="submit" id="print1" name="print1" class="btn btn-primary"><i class="fa fa-print"></i> Imprimir</button-->
                    </div>
                </div>


                </div><!--div class='ibox-content'-->
                </div><!--div class='ibox'-->

            <!--/div-->
 	<!-- Modal -->
	<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content modal-sm">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Pago y Cambio</h4>
				</div>
			<div class="modal-body">
				<div class="wrapper wrapper-content  animated fadeInRight">
					<div class="row" ><label>Factura No:</label><div id='fact_num'></div> </div>
					<div class="row">
								<div class="col-lg-12">
                                  <div class="form-group">
                                      <label><h4 class='text-navy'>Facturado $ </h5></label><input type="text" id="facturado" name="facturado" value=0  class="form-control decimal" readonly >
                                  </div>
								</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label>Efectivo $</label><input type="text" id="efectivo" name="efectivo" value=""  class="form-control decimal">
                            </div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label>Cambio $</label><input type="text" id="cambio" name="cambio" value=0 placeholder="cambio" class="form-control decimal" readonly >
                            </div>
						</div>
					</div>
				</div>
			</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="btnPrintFact">Imprimir</button>
					<button type="button" class="btn btn-primary" id="btnEsc">Salir</button>
				</div>
			</div>
		</div>
	</div>



            </div>

        <!--/div-->
        <!--/div-->

<?php
include_once ("footer.php");

echo "<script src='js/plugins/typehead/bootstrap3-typeahead.js'></script>";
echo "<script src='js/plugins/keynavigator/keynavigator.js'></script>";
echo "<script src='js/funciones/finalizar_traslado.js'></script>";

} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function insertar(){
	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];
	$id_usuario=$_SESSION["id_usuario"];
	$fecha_movimiento= $_POST['fecha_movimiento'];
	$numero_doc = $_POST['numero_doc'];
	$total_venta = $_POST['total_ventas'];
	$tipo_entrada_salida='DEVOLUCION POR TRASLADO No.'.$numero_doc;

	$id_sucursal=$_SESSION['id_sucursal'];
	$fecha_recibe=date('Y-m-d');
	//Traer datos encabezados de la traslado para finalizarlos
	$sql="SELECT *
		FROM traslado
		WHERE finalizada=0
		AND numero_doc='$numero_doc'
		AND traslado.id_sucursal_destino='$id_sucursal'";

	$result=_query($sql);
	$count=_num_rows($result);
	$rows=_fetch_array($result);

	$sql2="select * from ultimo_numdoc WHERE id_sucursal='$id_sucursal'";
	$result2= _query($sql2);
	$rows2=_fetch_array($result2);
	$nrows2=_num_rows($result2);
	$ult_cof=$rows2['ult_cof'];
	$ult_ccf=$rows2['ult_ccf'];
	$ult_csn=$rows2['ult_csn'];
	$ult_trs=$rows2['ult_trs'];
	$table_numdoc="ultimo_numdoc";

	$ult_trs=$ult_trs+1;

	$insertar1=false;
	$insertar2=false;
	$insertar3=false;
	$insertar4=false;
	$insertar5=false;

	$xdatos['typeinfo']='';
	$xdatos['msg']='';
	$xdatos['process']='';

	$observaciones=$tipo_entrada_salida;

	if ($cuantos>0){
		//Verificar los datos en tabla traslado
		$sql_traslado="SELECT * FROM traslado WHERE numero_doc='$numero_doc' AND id_sucursal_destino='$id_sucursal'";
		$result_traslado=_query($sql_traslado);
		$row_traslado=_fetch_array($result_traslado);
		$nrows_traslado=_num_rows($result_traslado);

		if($nrows_traslado>0){
			$id_traslado=$row_traslado['id_traslado'];
			$id_suc_origen=$row_traslado['id_sucursal'];
			_begin();

			$table_tr='traslado';
			$form_data_tr = array(
				'finalizada' => '1',
				'total' => $total_venta,
				'id_usuario_recibe'=> $id_usuario,
				'fecha_recibe'=> $fecha_recibe,
			);
			//Actualizarar los datos en tabla traslado, quine recibe, total y finalizado=1
			$where_clause_tr="WHERE numero_doc='$numero_doc' AND id_traslado='$id_traslado' AND id_sucursal_destino='$id_sucursal'";
			$insertar1 = _update($table_tr,$form_data_tr, $where_clause_tr );
		}
		//Verificar traslado detalle, las cantidades recibidas en destino
		$listadatos=explode('#',$stringdatos);
		for ($i=0;$i<$cuantos ;$i++){
			list($id_prod,$precio_venta,$recibido,$cant_dev)=explode('|',$listadatos[$i]);
			$subtotal=$precio_venta*$recibido;
			if ($recibido>0 ){
				$table_fact_det= 'traslado_detalle';
				$where_clause_tr_det="WHERE id_prod_serv='$id_prod' AND id_traslado='$id_traslado' AND id_sucursal='$id_suc_origen'";
				$data_fact_det = array(
					'recibido' => $recibido,
					'subtotal' => $subtotal
				);
				$insertar2 = _update($table_fact_det,$data_fact_det,$where_clause_tr_det);
			}
			//ver en movimiento producto cuando no se aceptan todos los productos a recibir en destino
			$sql1="select * from movimiento_producto where id_producto='$id_prod' and tipo_entrada_salida='$tipo_entrada_salida'
			AND numero_doc='$numero_doc' and fecha_movimiento='$fecha_movimiento' AND id_sucursal_destino='$id_sucursal'";

			$stock1=_query($sql1);
			$row1=_fetch_array($stock1);
			$nrow1=_num_rows($stock1);

            $table1= 'movimiento_producto';
			$form_data1 = array(
					'id_producto' => $id_prod,
					'fecha_movimiento' => $fecha_movimiento,
					'entrada' => $cant_dev,
					'observaciones' => $tipo_entrada_salida,
					'tipo_entrada_salida' => $tipo_entrada_salida,
					'numero_doc' => $numero_doc,
					'precio_venta' => $precio_venta,
					'id_sucursal_origen' => $id_sucursal
			);


			if ($nrow1==0 && $cant_dev>0 ){
				$insertar3 = _insert($table1,$form_data1 );
			}
			else{
				$insertar3 =true;
			}

            //$tipo_entrada_salida2='DEVOLUCION POR TRASLADO No.'.$numero_doc;
            //Para ver los detalles de stock en la sucursal ORIGEN
			$sql_stock_origen="select producto.id_producto,producto.unidad,stock.stock, stock.costo_promedio from producto,stock
			where producto.id_producto='$id_prod' and producto.id_producto=stock.id_producto
			AND stock.id_sucursal='$id_suc_origen'";
			$stock_origen=_query($sql_stock_origen);
			$row_stock_origen=_fetch_array($stock_origen);
			$nrow_stock_origen=_num_rows($stock_origen);
			$unidad_stock_origen=$row_stock_origen['unidad'];
			$existencias_stock_origen=$row_stock_origen['stock'];
			$costoprom_stock_origen=$row_stock_origen['costo_promedio'];


            //Buscar productos en stock de DESTINO primero para hacer el update
			$sql_stock_dest="select producto.id_producto,producto.unidad,stock.stock, stock.costo_promedio from producto,stock
					where producto.id_producto='$id_prod' and producto.id_producto=stock.id_producto
					AND stock.id_sucursal='$id_sucursal'";

			$stock_stock_dest=_query($sql_stock_dest);
			$row_stock_dest=_fetch_array($stock_stock_dest);
			$nrow_stock_dest=_num_rows($stock_stock_dest);
			$unidad=$row_stock_dest['unidad'];
			$existencias_dest=$row_stock_dest['stock'];
			$costoprom_stock_dest=$row_stock_dest['costo_promedio'];
			$cantidad_stock_dest=0;
			$cantidad_stock_dest=$existencias_dest+$recibido;
			if($costoprom_stock_dest>0)
				$costoprom_stock_dest=$costoprom_stock_dest;
			else
				$costoprom_stock_dest=$costoprom_stock_origen;



			$table_stock= 'stock';
			if ($nrow_stock_dest>0 ){
				$where_clause_stock="WHERE id_producto='$id_prod' AND id_sucursal='$id_sucursal'";

				$form_data_stock= array(
				'id_producto' => $id_prod,
				'stock' => $cantidad_stock_dest,
				'precio_sugerido' => $precio_venta,
				'costo_promedio' =>$costoprom_stock_dest,
				'id_sucursal'=>$id_sucursal
				);
				$insertar4 = _update($table_stock,$form_data_stock, $where_clause_stock );
			}
			else{
					$form_data_stock = array(
					'id_producto' => $id_prod,
					'stock' => $cantidad_stock_dest,
					'stock_minimo' => '1',
					'precio_sugerido' => $precio_venta,
					'id_sucursal'=>$id_sucursal
					);
					$insertar4 = _insert($table_stock,$form_data_stock);
				}



			//Actualizar en stock si  hay devolucion a la sucursal ORIGEN
			$cantidad_stock_origen=0;
			if ($cant_dev>0){
				$cantidad_stock_origen=$existencias_stock_origen+$cant_dev;

				if ($nrow_stock_origen>0 ){
					$where_clause_stock="WHERE id_producto='$id_prod' AND id_sucursal='$id_suc_origen'";

					$form_data_stock = array(
					'id_producto' => $id_prod,
					'stock' => $cantidad_stock_origen,
					'stock_minimo' => '0',
					'precio_sugerido' => $precio_venta,
					'id_sucursal'=>$id_suc_origen // verificar id sucursal origen $id_suc_origen
					);
					$insertar5 = _update($table_stock,$form_data_stock, $where_clause_stock );
				}
				else{
					$form_data_stock = array(
					'id_producto' => $id_prod,
					'stock' => $cantidad_stock_origen,
					'stock_minimo' => '1',
					'precio_sugerido' => $precio_venta,
					'id_sucursal'=>$id_suc_origen  // verificar id sucursal origen $id_suc_origen
					);
					$insertar5 = _insert($table_stock,$form_data_stock);
				}
		  }	//if ($cant_dev>0){
		  else{
			$insertar5 =true;
		  }

		  $form_datax = array(
				'costo_promedio'=>$costoprom_stock_origen
			);
			$tablex= 'stock';
			$where_clausex= "id_producto='" . $id_prod."'";
			$actualizar_costo =  _update($tablex,$form_datax, $where_clausex);
		}//for
		/*
		$table3='garantia';
		$form_data3 = array(
						'finalizada' => '1'
						);
		$where_clause3="WHERE numero_doc='$numero_doc' AND id_sucursal='$id_sucursal'";
		$insertar3 = _update($table3,$form_data3, $where_clause3 );
		*/
	}//if
	if($insertar1 && $insertar2 && $insertar3 && $insertar4 && $insertar5){
					_commit(); // transaction is committed
					$xdatos['typeinfo']='Success';
					$xdatos['msg']='Consignacion Numero: <strong>'.$numero_doc.'</strong>  finalizada con Exito !';
					$xdatos['process']='insert';
					$xdatos['insertados']=" numero doc".$numero_doc." mov prod:".$insertar1." stock:".$insertar2 ;
	}else{
					_rollback(); // transaction rolls back
					$xdatos['typeinfo']='Error';
					$xdatos['msg']='Registro  no pudo ser Actualizado !';
					$xdatos['process']='noinsert';
					$xdatos['insertados']=" numero doc".$numero_doc." mov prod:".$insertar1." stock:".$insertar2 ;
	}



	echo json_encode($xdatos);
}
function consultar_stock(){
	$id_producto = $_REQUEST['id_producto'];
	$tipo = $_REQUEST['tipo'];
	$id_user=$_SESSION["id_usuario"];
	$sql_user="select * from usuario where id_usuario='$id_user'";
	$result_user= _query($sql_user);
	$row_user=_fetch_array($result_user);
	$nrow=_num_rows($result_user);
	$id_sucursal=$row_user['id_sucursal'];

	if ($tipo =='PRODUCTO'){

		$sql1="SELECT producto.id_producto,producto.descripcion,producto.unidad,
		producto.utilidad_activa,producto.utilidad_seleccion,producto.porcentaje_utilidad1,
		producto.porcentaje_utilidad2,producto.porcentaje_utilidad3,
		producto.porcentaje_utilidad4,producto.imagen,
		stock.stock,stock.costo_promedio,stock.precio_sugerido
		FROM producto JOIN stock ON producto.id_producto=stock.id_producto
		WHERE producto.id_producto='$id_producto' AND stock.id_sucursal='$id_sucursal'";
		$stock1=_query($sql1);
		$row1=_fetch_array($stock1);
		$nrow1=_num_rows($stock1);
		$unidades=round($row1['unidad'],2);
		$utilidad_activa=$row1['utilidad_activa'];
		$utilidad_seleccion=$row1['utilidad_seleccion'];
		$pu1=$row1['porcentaje_utilidad1'];
		$pu2=$row1['porcentaje_utilidad2'];
		$pu3=$row1['porcentaje_utilidad3'];
		$pu4=$row1['porcentaje_utilidad4'];
		$cp=$row1['costo_promedio'];
		$existencias=$row1['stock'];
	    $precio_sugerido=$row1['precio_sugerido'];
		$imagen=$row1['imagen'];
		//$costos_pu=$pu1."|".$pu2."|".$pu3."|".$pu4."|".$cp."|";
		$costos_pu=array($pu1,$pu2,$pu3,$pu4);
		$precio_venta=0.0;

		 if( $utilidad_activa==true || $utilidad_activa==1){
			 switch ($utilidad_seleccion) {
			case 1:
				$precio_venta=$cp*(1+$pu1/100);
				//$precio_venta=$cp*(1+2.5/100);
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
		if ($unidades>1){
			//Precio para validar si tiene unidades mayores que 1 en producto
			$precio_venta=$precio_venta/$unidades;
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
		$xdatos['unidades'] = $unidades;
		$xdatos['imagen'] = $imagen;

	echo json_encode($xdatos); //Return the JSON Array

}
function mostrar_datos_cliente(){
	$id=$_REQUEST['id'];
	$sql="select * from cliente where id_cliente='$id'";
	$result= _query($sql);
	$row1=_fetch_array($result);
	$nrow1=_num_rows($result);
	$dui=$row1['dui'];
	$nit=$row1['nit'];
	$direccion=$row1['direccion'];
	$direccion=htmlentities($direccion);
	echo " <div class='col-lg-6'>";
	echo "<div class='form-group has-info single-line'><label>Dui</label> <input type='text' placeholder='DUI' class='form-control' id='dui' name='dui' value='$dui' ></div>";
	echo "</div>";
	echo " <div class='col-lg-6'>";
	echo "<div class='form-group has-info single-line'><label>Nit:</label> <input type='text' placeholder='NIT' class='form-control' id='nit' name='nit' value='$nit'></div>";
	echo "</div>";
	echo " <div class='col-lg-12'>";
	echo "<div class='form-group has-info single-line'><label>Direccion</label> <input type='text' placeholder='Direcci&oacute;n' class='form-control' id='direccion' name='direccion' value='$direccion'></div>";
	echo "</div>";

}

function cargar_empleados(){
	echo"<option value='-1'>Mostrador</option>  ";
	$qempleado=_query('SELECT * FROM empleado ORDER BY apellido');
	while($row_empleado=_fetch_array($qempleado)){
		$id_empleado=$row_empleado['id_empleado'];
		$nombres=$row_empleado['nombre']." ".$row_empleado['apellido'];
		echo "<option value='$id_empleado'>$nombres</option> ";
	}
}

function cargar_precios(){
	//echo"<option value='-1'>Mostrador</option>  ";
	$q_precios=_query('SELECT * FROM empleado ORDER BY apellido');
	while($row_precios=_fetch_array($q_precios)){
		$id_precios=$row_precios['id_precios'];
		$nombres=$row_precios['nombre']." ".$row_precios['apellido'];
		echo "<option value='$id_precios'>$nombres</option> ";
	}
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

function print1(){
	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];
	$id = $_POST['id'];

		$tipo_entrada_salida='CONSIGNACION';
		$id_cliente=$_POST['id_cliente'];
		$fecha_movimiento= $_POST['fecha_movimiento'];
		$numero_doc = $_POST['numero_doc'];
		$numero_docx = $numero_doc."_CSN";
		$total_venta = $_POST['total_ventas'];

	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';
	$Fecha=date("d-m-Y");
	$Hora=date("H:i");

	$sql="select * from cliente where id_cliente='$id_cliente'";
	$result= _query($sql);
	$row1=_fetch_array($result);
	$nrow1=_num_rows($result);
	$nombres=$row1['nombre']." ".$row1['apellido'];
	$dui=$row1['dui'];
	$nit=$row1['nit'];
	$direccion=$row1['direccion'];
	$info_consignacion="";


	$numfact=espacios_izq($numero_doc,81);
	//Columnas y posiciones base
	$base1=7;
	$col0=15;
	$col1=6;
	$col2=4;
	$col3=15;
	$col4=12;
	$sp1=$base1;
	$sp=espacios_izq(" ",$sp1);
	$sp2=espacios_izq(" ",12);
	$esp_init=espacios_izq(" ",$col0);
	$esp_enc2=espacios_izq(" ",3);
	$esp_init2=espacios_izq(" ",23);
	$nombre_ape=texto_espacios($nombres,60);
	$dir_txt=texto_espacios($direccion,60);
	$total_final=0;
	for($j=0;$j<2;$j++){
		$info_consignacion.="\n";
	}
	$info_consignacion.=$esp_init.$numfact."\n";
	for($j=0;$j<2;$j++){
		$info_consignacion.="\n";
	}
	$info_consignacion.=$esp_init2.$nombre_ape.$esp_enc2.$fecha_movimiento."\n";
	$info_consignacion.=$esp_init2.$dir_txt.$esp_enc2.$dui."\n";
	for($j=0;$j<3;$j++){
		$info_consignacion.="\n";
	}
	$listadatos=explode('#',$stringdatos);
	$lineas=6;
	$salto_linea=$lineas-$cuantos;
	if ($cuantos<=$lineas){
		for ($i=0;$i<$cuantos ;$i++){
			list($id_prod_serv,$descripcion,$tipoprodserv,$precio_venta,$cantidad,$id_empleado)=explode('|',$listadatos[$i]);
			$descrip=texto_espacios($descripcion,63);
			$subt=$precio_venta*$cantidad;
			$precio_unit=sprintf("%.2f",$precio_venta);
			$subtotal=sprintf("%.2f",$subt);
			$total_final=$total_final+$subtotal;

			if(strlen($cantidad)>1){
				$sp0=$col0-1;
				$esp_init=espacios_izq(" ",$sp0);
			}else{
				$sp0=$col0;
				$esp_init=espacios_izq(" ",$sp0);
			}

			if(strlen($precio_unit)>4){
				$sp2=$col2-1;
				$esp_col2=espacios_izq(" ",$sp2);
			}else{
				$sp2=$col2;
				$esp_col2=espacios_izq(" ",$sp2);
			}

			if(strlen($subtotal)>4){
				$sp3=$col3-1;
				$esp_col3=espacios_izq(" ",$sp3);
			}else{
				$sp3=$col3;
				$esp_col3=espacios_izq(" ",$sp3);
			}

			$info_consignacion.=$esp_init.$cantidad."   ".$descrip.$esp_col2.$precio_unit.$esp_col3.$subtotal."\n";
		}
		for($j=0;$j<$salto_linea;$j++){
			$info_consignacion.="\n";
		}
	}
	list($entero,$decimal)=explode('.',$total_venta);
	$enteros_txt=num2letras($entero);
	if(strlen($decimal)==1){
	$decimales_txt=$decimal."0";
	}
	else{
		$decimales_txt=$decimal;
	}
	$info_consignacion.="\n";
	$cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
	$salida_txt_total=texto_espacios($cadena_salida_txt,70);
	$esp=espacios_izq(" ",7);
	$total_value=sprintf("%.2f",$total_final);
	$esp_init2=espacios_izq(" ",25);
	$info_consignacion.=$esp_init2.$salida_txt_total.$esp."$ ".$total_value."\n";
	$esp=espacios_izq(" ",86);
	$info_consignacion.="\n";
	$info_consignacion.=$esp_init.$esp." $ ".$total_value."\n";
	$nreg_encode['consignacionr'] = $info_consignacion;
	$nreg_encode['sist_ope'] =$so_cliente;
	echo json_encode($nreg_encode);
}

function print2(){
	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];
	$id = $_POST['id'];
	if ($id=='1'){
		$tipo_entrada_salida='consignacion CONSUMIDOR';
		$fecha_movimiento= $_POST['fecha_movimiento'];
		$numero_doc = $_POST['numero_doc'];
		$numero_docx = $numero_doc."_COF";
		$id_cliente=$_POST['id_cliente'];
		$total_venta = $_POST['total_ventas'];
	}
	if ($id=='2'){
		$tipo_entrada_salida='consignacion CREDITO FISCAL';
		$id_cliente=$_POST['id_cliente'];
		$fecha_movimiento= $_POST['fecha_movimiento2'];
		$numero_doc = $_POST['numero_doc'];
		$numero_docx = $numero_doc."_CCF";
		$total_venta = $_POST['total_ventas'];
	}

	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';

	//$Fecha=date("d-m-Y");
	//$Hora=date("H:i");

	$sql="select * from cliente where id_cliente='$id_cliente'";
	$result= _query($sql);
	$row1=_fetch_array($result);
	$nrow1=_num_rows($result);
	$nombres=$row1['nombre']." ".$row1['apellido'];
	$dui=$row1['dui'];
	$nit=$row1['nit'];
	$direccion=$row1['direccion'];
	$info_consignacion="";


	$numfact=espacios_izq($numero_doc,65);
	$base1=7;
	//Columnas y posiciones base
	$col0=17;
	$col1=7;
	$col2=3;
	$col3=12;
	$col4=12;
	$sp1=$base1;
	$sp=espacios_izq(" ",$sp1);
	$sp2=espacios_izq(" ",12);
	$esp_init=espacios_izq(" ",$col0);
	$esp_init2=espacios_izq(" ",23);
	$nombre_ape=texto_espacios($nombres,56);
	$dir_txt=texto_espacios($direccion,56);
	$total_final=0;
	//$num_fact=texto_espacios($numero_doc,60);
	for($j=0;$j<2;$j++){
			$info_consignacion.="\n";
	}
	$info_consignacion.=$esp_init.$numfact."\n";
	for($j=0;$j<2;$j++){
			$info_consignacion.="\n";
	}
	$info_consignacion.=$esp_init2.$nombre_ape.$fecha_movimiento."\n";
	$info_consignacion.=$esp_init2.$dir_txt.$dui."\n";
	for($j=0;$j<2;$j++){
			$info_consignacion.="\n";
	}
	$listadatos=explode('#',$stringdatos);
	$lineas=8;
	$salto_linea=$lineas-$cuantos;
	if ($cuantos<=$lineas){
		for ($i=0;$i<$cuantos ;$i++){
			list($id_prod_serv,$descripcion,$tipoprodserv,$precio_venta,$cantidad,$id_empleado)=explode('|',$listadatos[$i]);
			$descrip=texto_espacios($descripcion,55);
			$subt=$precio_venta*$cantidad;
			$precio_unit=sprintf("%.2f",$precio_venta);
			$subtotal=sprintf("%.2f",$subt);
			$total_final=$total_final+$subtotal;

			if(strlen($cantidad)>1){
				$sp0=$col0-1;
				$esp_init=espacios_izq(" ",$sp0);
			}else{
				$sp0=$col0;
				$esp_init=espacios_izq(" ",$sp0);
			}

			if(strlen($precio_unit)>4){
				$sp2=$col2-1;
				$esp_col2=espacios_izq(" ",$sp2);
			}else{
				$sp2=$col2;
				$esp_col2=espacios_izq(" ",$sp2);
			}

			if(strlen($subtotal)>4){
				$sp3=$col3-1;
				$esp_col3=espacios_izq(" ",$sp3);
			}else{
				$sp3=$col3;
				$esp_col3=espacios_izq(" ",$sp3);
			}

			$info_consignacion.=$esp_init.$cantidad."  ".$descrip.$esp_col2.$precio_unit.$esp_col3.$subtotal."\n";
		}
		for($j=0;$j<$salto_linea;$j++){
			$info_consignacion.="\n";
		}
	}

	list($entero,$decimal)=explode('.',$total_venta);
	$enteros_txt=num2letras($entero);
	if(strlen($decimal)==1){
		$decimales_txt=$decimal."0";
	}
	else{
		$decimales_txt=$decimal;
	}
	$cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
	$salida_txt_total=texto_espacios($cadena_salida_txt,65);
	$esp=espacios_izq(" ",7);
	$total_value=sprintf("%.2f",$total_final);
	$esp_init2=espacios_izq(" ",25);
	$info_consignacion.=$esp_init2.$salida_txt_total.$esp."$ ".$total_value."\n";
	$esp=espacios_izq(" ",86);
	$info_consignacion.="\n";
	$info_consignacion.=$esp_init.$esp." $ ".$total_final."\n";
	$nreg_encode['consignacionr'] = $info_consignacion;
	$nreg_encode['sist_ope'] =$so_cliente;
	echo json_encode($nreg_encode);

}
function reimprimir() {
	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];
	$id = $_POST['id'];
		$tipo_entrada_salida='consignacion CONSUMIDOR';
		$fecha_movimiento= $_POST['fecha_movimiento'];
		$numero_doc = $_POST['numero_doc'];
		$numero_docx = $numero_doc."_CSN";
		$id_cliente= $_POST['id_cliente'];
		$total_venta = $_POST['total_ventas'];


	//$id_consignacion = $_REQUEST['id_consignacion'];
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';
	//Obtener informacion de tabla consignacion
	$info_consignacion="";
	$sql_fact="SELECT * FROM consignacion WHERE numero_doc='$numero_docx'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact>0){
		$id_cliente=$row_fact['id_cliente'];
		$id_consignacion = $row_fact['id_consignacion'];
		$fecha=$row_fact['fecha'];
		$numero_doc=trim($row_fact['numero_doc']);
		$total=$row_fact['total'];

		$len_numero_doc=strlen($numero_doc)-4;
		$num_fact=substr($numero_doc,0,$len_numero_doc);
		$tipo_fact=substr($numero_doc,$len_numero_doc,4);
		$numfact=espacios_izq($num_fact,81);

		//Datos del Cliente
		$sql="select * from cliente where id_cliente='$id_cliente'";
		$result= _query($sql);
		$row1=_fetch_array($result);
		$nrow1=_num_rows($result);
		$nombres=$row1['nombre']." ".$row1['apellido'];
		$dui=$row1['dui'];
		$nit=$row1['nit'];
		$direccion=$row1['direccion'];

		//Columnas y posiciones base
		$base1=7;
		$col0=15;
		$col1=6;
		$col2=4;
		$col3=15;
		$col4=12;
		$sp1=$base1;
		$sp=espacios_izq(" ",$sp1);
		$sp2=espacios_izq(" ",12);
		$esp_init=espacios_izq(" ",$col0);
		$esp_enc2=espacios_izq(" ",3);
		$esp_init2=espacios_izq(" ",23);
		$nombre_ape=texto_espacios($nombres,60);
		$dir_txt=texto_espacios($direccion,60);
		$total_final=0;
		for($j=0;$j<2;$j++){
			$info_consignacion.="\n";
		}
		$info_consignacion.=$esp_init.$numfact."\n";
		for($j=0;$j<2;$j++){
			$info_consignacion.="\n";
		}
		$info_consignacion.=$esp_init2.$nombre_ape.$esp_enc2.$fecha."\n";
		$info_consignacion.=$esp_init2.$dir_txt.$esp_enc2.$dui."\n";
		for($j=0;$j<3;$j++){
			$info_consignacion.="\n";
		}

		//Obtener informacion de tabla consignacion_detalle y producto o servicio
		$sql_fact_det="SELECT  producto.id_producto, producto.descripcion,consignacion_detalle.*
		FROM consignacion_detalle JOIN producto ON consignacion_detalle.id_prod_serv=producto.id_producto
		WHERE  consignacion_detalle.id_consignacion='$id_consignacion' AND  consignacion_detalle.tipo_prod_serv='PRODUCTO'
		UNION ALL
		SELECT  servicio.id_servicio, servicio.descripcion,consignacion_detalle.*
		FROM consignacion_detalle JOIN servicio ON consignacion_detalle.id_prod_serv=servicio.id_servicio
		WHERE  consignacion_detalle.id_consignacion='$id_consignacion' AND  consignacion_detalle.tipo_prod_serv='SERVICIO'
		";

		$result_fact_det=_query($sql_fact_det);
		$nrows_fact_det=_num_rows($result_fact_det);
		$total_final=0;
		$lineas=6;
		$cuantos=0;
		for($i=0;$i<$nrows_fact_det;$i++){
			$row_fact_det=_fetch_array($result_fact_det);
			$id_producto =$row_fact_det['id_producto'];
			$descripcion =$row_fact_det['descripcion'];
			$id_consignacion_detalle =$row_fact_det['id_consignacion_detalle'];
			$id_prod_serv =$row_fact_det['id_prod_serv'];
			$cantidad =$row_fact_det['cantidad'];
			$precio_venta =$row_fact_det['precio_venta'];
			$subt =$row_fact_det['subtotal'];
			$id_empleado =$row_fact_det['id_empleado'];
			$tipo_prod_serv =$row_fact_det['tipo_prod_serv'];

			//linea a linea
			$descrip=texto_espacios($descripcion,63);
			$subt=$precio_venta*$cantidad;
			$precio_unit=sprintf("%.2f",$precio_venta);
			$subtotal=sprintf("%.2f",$subt);
			$total_final=$total_final+$subtotal;

			if(strlen($cantidad)>1){
				$sp0=$col0-1;
				$esp_init=espacios_izq(" ",$sp0);
			}else{
				$sp0=$col0;
				$esp_init=espacios_izq(" ",$sp0);
			}

			if(strlen($precio_unit)>4){
				$sp2=$col2-1;
				$esp_col2=espacios_izq(" ",$sp2);
			}else{
				$sp2=$col2;
				$esp_col2=espacios_izq(" ",$sp2);
			}

			if(strlen($subtotal)>4){
				$sp3=$col3-1;
				$esp_col3=espacios_izq(" ",$sp3);
			}else{
				$sp3=$col3;
				$esp_col3=espacios_izq(" ",$sp3);
			}
			$info_consignacion.=$esp_init.$cantidad."   ".$descrip.$esp_col2.$precio_unit.$esp_col3.$subtotal."\n";
			$cuantos=$cuantos+1;
		}
	}
	$salto_linea=$lineas-$cuantos;
	for($j=0;$j<$salto_linea;$j++){
		$info_consignacion.="\n";
	}
	$total_final_format=sprintf("%.2f",$total_final);
	list($entero,$decimal)=explode('.',$total_final_format);
	$enteros_txt=num2letras($entero);
	if(strlen($decimal)==1){
	$decimales_txt=$decimal."0";
	}
	else{
		$decimales_txt=$decimal;
	}
	$info_consignacion.="\n";
	$cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
	$salida_txt_total=texto_espacios($cadena_salida_txt,70);
	$esp=espacios_izq(" ",7);
	$total_value=sprintf("%.2f",$total);
	$esp_init2=espacios_izq(" ",25);
	$info_consignacion.=$esp_init2.$salida_txt_total.$esp."$ ".$total_value."\n";
	$esp=espacios_izq(" ",86);
	$info_consignacion.="\n";
	$info_consignacion.=$esp_init.$esp." $ ".$total_value."\n";

	$nreg_encode['consignacionr'] = $info_consignacion;
	$nreg_encode['sist_ope'] =$so_cliente;
	echo json_encode($nreg_encode);

}

function imprimir_fact() {
	$numero_doc = $_POST['numero_doc'];
	//$id_ccf_cof= $_POST['id'];

	$id_user=$_SESSION["id_usuario"];
	$sql_user="select * from usuario where id_usuario='$id_user'";
	$result_user= _query($sql_user);
	$row_user=_fetch_array($result_user);
	$nrow=_num_rows($result_user);
	$id_sucursal=$row_user['id_sucursal'];

	$id_ccf_cof= '1';
	$tipo_fact="";
	if ($id_ccf_cof=='1'){
		$numero_docx = $numero_doc."_COF";
		$tipo_fact="COF";
	}
	else{
		$numero_docx = $numero_doc."_CCF";
		$tipo_fact="CCF";
	}
	$sql_fact="SELECT * FROM factura WHERE numero_doc='$numero_docx' AND id_sucursal='$id_sucursal'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';

	if($nrows_fact>0){
		$id_factura=trim($row_fact['id_factura']);
		$table_fact= 'factura';
		$form_data_fact = array(
			'finalizada' => '1'
		);
		$where_clause="WHERE numero_doc='$numero_docx' AND id_sucursal='$id_sucursal'";
		$actualizar = _update($table_fact,$form_data_fact, $where_clause );
	}


	$info_factura=print_fact($numero_doc,$tipo_fact);
	$nreg_encode['facturar'] = $info_factura;
	$nreg_encode['sist_ope'] =$so_cliente;
	echo json_encode($nreg_encode);
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
	case 'print1':
		print1();
		break;
	case 'print2':
		print2(); //Generacion de los datos de consignacion que se retornan para otro script que imprime!!!
		break;
	case 'mostrar_numfact':
		mostrar_numfact();
		break;
	case 'reimprimir' :
		reimprimir();
		break;
	case 'imprimir_fact':
		imprimir_fact();
		break;
	}

 //}
}
?>
