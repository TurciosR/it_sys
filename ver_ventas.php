<?php
include_once "_core.php";
include ('num2letras.php');
//include ('facturacion_funcion_imprimir.php');
//include("escpos-php/Escpos.php");
function initial() {
	$id_factura=$_REQUEST["id_factura"];
	$id_sucursal=$_REQUEST['id_sucursal'];
		//permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

	//$id_sucursal=$_SESSION['id_sucursal'];

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

	$sql_fact="SELECT factura.*, clientes.nombre, clientes.direccion, clientes.nrc, clientes.nit FROM factura JOIN clientes
	ON factura.id_cliente=clientes.id_cliente
	WHERE id_factura='$id_factura'
	AND factura.id_sucursal='$id_sucursal'
	";
	$result_fact = _query( $sql_fact);
	$row = _fetch_array ( $result_fact);
	$cliente=$row['nombre'];
	$direccion = $row["direccion"];
	$factnum=$row['numero_doc'];
	$alias_tipodoc=$row['tipo_documento'];
	$total=$row['total'];
	$fecha=ED($row['fecha']);
	$nit = $row["nit"];
	$nrc = $row["nrc"];

?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Ver factura</h4>
</div>

<div class="modal-body">
		<div class="row" id="row1">
				<?php
					if ($links!='NOT' || $admin=='1' ){
				?>
						<div>
							<!--load datables estructure html-->
							<header><h4 class="text-danger">Factura No: &nbsp;<?php echo $alias_tipodoc." ".$factnum;  ?></h4>
							<h4  class='text-navy'>Fecha:<?php echo $fecha;  ?>&nbsp;
							Cliente:<?php echo $cliente; ?></h4>
							</header>
							<section>
								<div class="table-responsive m-t">
									<table class="table table-condensed table-striped" id="inventable">
									<thead class="thead-inverse">
										<tr>
										<th class='success'>Cantidad</th>
										<th class='success'>Descripci&oacute;n</th>
										<th class='success'>Precio</th>
										<th class='success'>Descuento</th>
										<th class='success'>Subtotal</th>

										</tr>
									</thead>
									<tbody>
									<?php
										$sql_det = _query("SELECT df.cantidad, df.precio_venta, df.descuento, df.subtotal, df.id_prod_serv, df.tipo_prod_serv FROM factura_detalle as df WHERE df.id_factura='$id_factura'");
										while($row2 = _fetch_array($sql_det))
										{
											$id_prod_serv = $row2["id_prod_serv"];
											$tipo_prod_serv = $row2["tipo_prod_serv"];

											if ($tipo_prod_serv == "PRODUCTO")
											{
												$sql_pro = _query("SELECT * FROM productos WHERE id_producto = '$id_prod_serv'");
												$row_pro = _fetch_array($sql_pro);
												$descripcion = $row_pro["descripcion"];
											}
											if ($tipo_prod_serv == "SERVICIO")
											{
												$sql_ser = _query("SELECT * FROM servicios WHERE id_servicio = '$id_prod_serv'");
												$row_ser = _fetch_array($sql_ser);
												$descripcion = $row_ser["descripcion"];
											}
											echo "<tr>";
											echo "<td>".$row2["cantidad"]."</td>";
											echo "<td>".$descripcion."</td>";
											echo "<td id='pv' class='text-right'>".number_format($row2["precio_venta"],2,".",",")."</td>";
											echo "<td id='cant1' class='text-right'>".number_format($row2["descuento"],2,".",",")."</td>";
											echo "<td id='subtot' class='text-right'>".number_format($row2["subtotal"],2,".",",")."</td>";
											//echo "<td id='combos' class='text-center'>".$combo_chk."</td>";

											echo "</tr>";
										}

									?>

									</tbody>
									<tfoot>
										<tr>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<!--td class="thick-line"></td>
										<td class="thick-line"></td-->
										<td class="thick-line text-center"><strong>TOTAL $:</strong></td>
										<td  class="thick-line text-right" id='total_dinero' ><strong><?php echo number_format($total,2,".",","); ?></strong></td>
										<!--td class="thick-line"></td>
										<td class="thick-line"></td-->
										</tr>
									</tfoot>
								</table>
								<?php
									$total = number_format($total,2,".","");
									list($entero,$decimal)=explode('.',$total);
									if($entero>0)
									{
										$enteros_txt=num2letras($entero);
									}
									else
									{
										$enteros_txt = "Cero";
									}
									if($entero>1)
									{
										$dolar=" dolares";
									}
									else
									{
										$dolar=" dolar";
									}
									$cadena_salida= "Son: <strong>".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.</strong>";
									echo "<div class='well m-t'  id='totaltexto'>".$cadena_salida." </div>";

								?>
					</section>

						</div>
				<input type="hidden" name="alias_tipodoc" id="alias_tipodoc" value="<?php echo $alias_tipodoc; ?>">
				<input type="hidden" name="nit" id="nit" value="<?php echo $nit; ?>">
				<input type="hidden" name="nrc" id="nrc" value="<?php echo $nrc; ?>">
				<input type="hidden" name="direccion" id="direccion" value="<?php echo $direccion; ?>">
				<input type="hidden" name="nombre_clie" id="nombre_clie" value="<?php echo $cliente; ?>">
				<input type="hidden" name="id_transace" id="id_transace" value="<?php echo $id_factura; ?>">
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="btnPrintFact" data-dismiss="modal">Imprimir</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>
				</div>
		</div>
	</div>


<?php
//include_once ("footer.php");
//echo "<script src='js/funciones/genera_venta.js'></script>";
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
function imprimir_fact() {
	include ('facturacion_funcion_imprimir.php');
	//$numero_doc = $_POST['numero_doc'];
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


if ($tipo_impresion=='COF') {
	$info_facturas=print_fact($id_factura, $tipo_impresion,$nombreape,$direccion);
}
/*if ($tipo_impresion=='ENV') {
	$info_facturas=print_envio($id_factura, $tipo_impresion);
}*/

if ($tipo_impresion=='CCF') {
	$info_facturas=print_ccf_tml($id_factura, $tipo_impresion, $nit, $nrc, $nombreape,$direccion);
	// $info_facturas=print_ccf($id_factura, $tipo_impresion, $nit, $nrc, $nombreape,$direccion);
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
	case 'imprimir_fact':
		imprimir_fact();
		break;
	}

 //}
}
?>
