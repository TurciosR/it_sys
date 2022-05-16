<?php
include_once "_core.php";
include ('num2letras.php');
include ('facturacion_funcion_imprimir.php');
//include("escpos-php/Escpos.php");
function initial() {
	$idtransace=$_REQUEST["id_traslado"];
	//permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
  $fecha=date('d-m-Y');
	$id_sucursal=$_SESSION['id_sucursal'];
	$idtransace=$_REQUEST['id_traslado'];
	$sql=_query("SELECT productos.barcode,productos.descripcion,productos.estilo,productos.talla,colores.nombre,detalle_traslado.cantidad from detalle_traslado INNER JOIN productos ON productos.id_producto=detalle_traslado.id_producto INNER JOIN colores ON colores.id_color=productos.id_color WHERE detalle_traslado.idtransace='$idtransace'");
  $sql_algo=_query("SELECT traslado.numero_doc,traslado.items,traslado.pares,traslado.fecha FROM traslado WHERE idtransace=$idtransace");
	$tn=_fetch_array($sql_algo);
	$numero_doc=$tn['numero_doc'];
	$fecha=$tn['fecha'];
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Ver Traslado</h4>
</div>

<div class="modal-body">
		<div class="row">
	<div class="col-md-16">&nbsp;&nbsp;
	<h3 class="text-navy" id='title-table'>&nbsp;Lista  del Traslado  No. <?php echo $numero_doc;?></h3>
	</div>
	<div class="form-group col-md-6">
		<label>Items&nbsp;
		<input type="text"  class='form-control input_header_panel'  id="items" value='<?php echo $tn['items'];?>' readOnly /></label>
	</div>
	<div class="form-group col-md-6">
		<label>Pares/Unidades&nbsp;
		<input type="text"  class='form-control input_header_panel'  id="pares"  value='<?php echo $tn['pares'];?>' readOnly /></label>
	</div>
	</div>


				<?php

						if ($links!='NOT' || $admin=='1' ){
					?>

					<div class="row" id="row1">
							<h4  class='text-navy'>Fecha:<?php echo $fecha;?>&nbsp;</h4>
							</header>
							<section>
								<div class="table-responsive m-t">
									<table class="table table2 table-fixed table-striped "id="inventable">
										<thead class='thead1'>
										<tr class='tr1'>
											<th class="text-success col11 th1" >Barcode</th>
										 <th class="text-success col14 th1">Descripci&oacute;n</th>
										 <th class="text-success col11 th1">Estilo</th>
										 <th class="text-success col11 th1">Color</th>
										 <th class="text-success col11 th1">Talla</th>
                     <th class="text-success col11 th1">Cantidad</th>

										</tr>
									</thead>
										<tbody class='tbody1 tbody2'>
											<?php
											$total_items=0;
											while ($row=_fetch_array($sql)) {
												# code...?>
											<tr>
												<td class="text-success col11 th1"><?php echo $row['barcode'] ?></td>
												<td class="text-success col14 th1"><?php echo $row['descripcion'] ?></td>
												<td class="text-success col11 th1"><?php echo $row['estilo'] ?></td>
												<td class="text-success col11 th1"><?php echo $row['nombre'] ?></td>
												<td class="text-success col11 th1"><?php echo $row['talla'] ?></td>
												<td class="text-success col11 th1"><?php echo $row['cantidad'] ?></td>

											</tr>
											<?php $total_items=$total_items+$row['cantidad'];
										} ?>
										</tbody>
										<tfoot class='thead1'>
										<tr class='tr1'>
											<th class="text-success col11 th1" >Total</th>
										 <th class="text-success col14 th1"></th>
										 <th class="text-success col11 th1"></th>
										 <th class="text-success col11 th1"></th>
										 <th class="text-success col11 th1"></th>
										 <th class="text-success col11 th1"><?php echo $total_items ?></th>
										</tr>
									</tfoot>
								</table>
							</section>

						</div>
					</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
				</div>
		</div>
	</div>


<?php

		echo "<script src='js/funciones/print_bcode_preingreso.js'></script>";
} //permiso del script
else {

		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
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
function buscarcompras(){
	$id_compras= trim($_POST['id_compras']);
	$sql0="SELECT pr.id_producto, dc.cantidad,dc.precio1,
	pr.descripcion,pr.talla,c.nombre,dc.ultcosto
	FROM pedidos AS cp
	JOIN detalle_pedidos AS dc ON(cp.idtransace=dc.idtransace)
	JOIN productos AS pr ON(pr.id_producto=dc.id_producto)
	JOIN colores AS c ON (pr.id_color=c.id_color)
	WHERE cp.idtransace='$id_compras'";
	$result = _query($sql0);
	$array_prod = array();
	$numrows= _num_rows($result);
 for ($i=0;$i<$numrows;$i++){
	 $row = _fetch_array($result);
	$id_producto =$row['id_producto'];
	$cantidad =$row['cantidad'];
	$talla=$row['talla'];
	$color=$row['nombre'];
	$descripcion=$row['descripcion']." ".$talla. " ".$color;

	$costo=$row['ultcosto'];

	$array_prod[] = array(
 		 'id_producto' => $row['id_producto'],
		  'descripcion' => $descripcion,
 		 'cantidad' =>  $row['cantidad'],
		 'costo'=>  $costo,
     'precio1'=>  $row['precio1'],

  );
 }
	//$xdatos['array_prod']=$array_prod;
	echo json_encode ($array_prod); //Return the JSON Array
}
function buscarprodcant(){
	$id_compras= trim($_POST['id_compras']);
	$sql0="SELECT pr.id_producto, dc.cantidad,dc.precio1,pr.numera,pr.estilo,
	pr.descripcion,pr.talla,c.nombre,dc.ultcosto
  FROM pedidos AS cp
	JOIN detalle_pedidos AS dc ON(cp.idtransace=dc.idtransace)
	JOIN productos AS pr ON(pr.id_producto=dc.id_producto)
	JOIN colores AS c ON (pr.id_color=c.id_color)
	WHERE cp.idtransace='$id_compras'";

	$result = _query($sql0);
	$array_prod = array();

	$numrows= _num_rows($result);

	$n=0;
 for ($i=0;$i<$numrows;$i++){
	 $row = _fetch_array($result);
	$id_producto =$row['id_producto'];
	$cantidad =$row['cantidad'];
	$talla=$row['talla'];
	$color=$row['nombre'];
	$descripcion=$row['descripcion'];
	$precio=$row['precio1'];
	$rango=$row['numera'];
	$estilo=$row['estilo'];
	$array_prod[] = array(
 		 'id_producto' => $id_producto,
		 'descripcion' => $descripcion,
		 'precio'=>  $precio,
		 'talla'=> $talla,
		 'estilo'=> $estilo,
		 'color' =>  $color,
		 'rango' =>  $rango,
		'cantidad' => $cantidad,
		'fin' => "|",
  );
	$n+=1;
 }
 //Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
 $info = $_SERVER['HTTP_USER_AGENT'];
 if(strpos($info, 'Windows') == TRUE)
	 $so_cliente='win';
 else
	 $so_cliente='lin';
	//$xdatos['array_prod']=$array_prod;
	//directorio de script impresion cliente
	$sql_dir_print="SELECT *  FROM config_dir";
	$result_dir_print=_query($sql_dir_print);
	$row_dir_print=_fetch_array($result_dir_print);
	$dir_print=$row_dir_print['dir_print_script'];
	$shared_printer_barcode=$row_dir_print['shared_print_barcode'];
$array_prod[] = array(
	 'id_producto' => -1,
	 'descripcion'=> 'CONF',
	 'shared_printer_barcode' =>$shared_printer_barcode,
	 'dir_print' =>$dir_print,
	 'sist_ope' =>$so_cliente,
);
	echo json_encode ($array_prod); //Return the JSON Array
//echo json_encode ($array2); //Return the JSON Array
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


	$sql3="select p.*,c.nombre  from productos AS p
		JOIN colores AS c ON (p.id_color=c.id_color)
		where p.id_producto='$id_producto'
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
	case 'consultar_stock':
		consultar_stock();
		break;
		case 'buscarcompras' :
				buscarcompras();
				break;
		case 'buscarprodcant' :
				buscarprodcant();
				break;
	}

 //}
}
?>
