<?php
include_once "_core.php";
include ('num2letras.php');
include ('facturacion_funcion_imprimir.php');
//include("escpos-php/Escpos.php");
function initial() {
	$id_compras=$_REQUEST["id_compras"];
	//permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
  $fecha=date('d-m-Y');
	$id_sucursal=$_SESSION['id_sucursal'];
	$sql0="SELECT total, numero_doc,pares,items
	FROM compras AS cp
	WHERE cp.id_compras='$id_compras'";
	$result = _query($sql0);
	$numrows= _num_rows($result);
	for ($i=0;$i<$numrows;$i++){
	$row = _fetch_array($result);
	$total=$row['total'];
	$numero_doc=$row['numero_doc'];
	$pares=$row['pares'];
	$items=$row['items'];

  }
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Ver Compras e Imprimir Barcodes</h4>
</div>

<div class="modal-body">
		<div class="row">
	<div class="col-md-16">&nbsp;&nbsp;
	<h3 class="text-navy" id='title-table'>&nbsp;Lista  de Productos  Compra  No. <?php echo $numero_doc;?></h3>
	</div>
	<div class="form-group col-md-6">
		<label>Items&nbsp;
		<input type="text"  class='form-control input_header_panel'  id="items" value='<?php echo $items;?>' readOnly /></label>
	</div>
	<div class="form-group col-md-6">
		<label>Pares/Unidades&nbsp;
		<input type="text"  class='form-control input_header_panel'  id="pares"  value='<?php echo $pares;?>' readOnly /></label>
	</div>
	</div>


				<?php

						if ($links!='NOT' || $admin=='1' ){
					?>

					<div class="row" id="row1">
						<input type='hidden' name='id_compras' id='id_compras' value='<?php echo $id_compras;?>'>
						<input type='hidden' name='urlprocess' id='urlprocess'value="<?php echo $filename;?>">
							<h4  class='text-navy'>Fecha:<?php echo $fecha;?>&nbsp;</h4>
							</header>
							<section>
								<div class="table-responsive m-t">
									<table class="table table2 table-fixed table-striped "id="inventable">
										<thead class='thead1'>
										<tr class='tr1'>
											<th class="text-success col11 th1" >Código</th>
										 <th class="text-success col14 th1">Descripci&oacute;n</th>
										 <th class="text-success col11 th1">Cantidad</th>
										 <th class="text-success col11 th1">Costo</th>
										 <th class="text-success col11 th1">Descuento</th>
										 <th class="text-success col11 th1">Subtotal</th>
										</tr>
									</thead>
										<tbody class='tbody1 tbody2'>
										</tbody>
										<tfoot class='thead1'>
										<tr class='tr1'>
											<th class="text-success col11 th1" >Total</th>
										 <th class="text-success col14 th1"></th>
										 <th class="text-success col11 th1"></th>
										 <th class="text-success col11 th1"></th>
										 <th class="text-success col11 th1"></th>
										 <th class="text-success col11 th1"><?php echo $total;?></th>
										</tr>
									</tfoot>
								</table>
							</section>
								<?php
								//$total='1.0';
								number_format($total,2,".","");
								list($entero,$decimal)=explode('.',$total);
								$enteros_txt=num2letras($entero);

								if($entero==0)
									$dolar=" Cero dolares";
							if($entero>1)
								$dolar=" dolares";
							if($entero==1)
								$dolar=" dolar";
							$cadena_salida= "Son: <strong>".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.</strong>";
							echo "<div class='well m-t'  id='totaltexto'>".$cadena_salida." </div>";

								?>

						</div>
					</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="btnPrintcodes">Imprimir</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
				</div>
		</div>
	</div>


<?php

		echo "<script src='js/funciones/print_bcode_compras.js'></script>";
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
	$sql0="SELECT pr.id_producto, dc.cantidad,
	pr.descripcion,pr.talla,c.nombre,dc.descuento,dc.ultcosto,dc.subtotal
	FROM compras AS cp
	JOIN detalle_compras AS dc ON(cp.id_compras=dc.id_compras)
	JOIN productos AS pr ON(pr.id_producto=dc.id_producto)
	JOIN colores AS c ON (pr.id_color=c.id_color)
	WHERE cp.id_compras='$id_compras'";
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

	$descuento=$row['descuento'];
	$costo=$row['ultcosto'];
	$subt=$row['subtotal'];

	$array_prod[] = array(
 		 'id_producto' => $row['id_producto'],
		  'descripcion' => $descripcion,
 		 'cantidad' =>  $row['cantidad'],
		 'costo'=>  $costo,
		 'descuento'=>  $descuento,
		 'subt' =>  $subt,

  );
 }
	//$xdatos['array_prod']=$array_prod;
	echo json_encode ($array_prod); //Return the JSON Array
}
function buscarprodcant(){
	$id_compras= trim($_POST['id_compras']);
	$sql0="SELECT pr.id_producto, dc.cantidad,pr.precio1,pr.numera,pr.estilo,
	pr.descripcion,pr.talla,c.nombre,dc.descuento,dc.ultcosto,dc.subtotal
	FROM compras AS cp
	JOIN detalle_compras AS dc ON(cp.id_compras=dc.id_compras)
	JOIN productos AS pr ON(pr.id_producto=dc.id_producto)
	JOIN colores AS c ON (pr.id_color=c.id_color)
	WHERE cp.id_compras='$id_compras'";

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
