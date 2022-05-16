<?php
include_once "_core.php";
include ('num2letras.php');
//include ('reservascion_funcion_imprimir.php');
//include("escpos-php/Escpos.php");
function initial() {
	$id_transace=$_REQUEST["id_reserva"];
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

	$sql_fact="SELECT  reservas.fecha_doc, reservas.numero_doc,  reservas.total, clientes.nombre FROM reservas JOIN clientes
	ON reservas.id_cliente=clientes.id_cliente
	WHERE id_reserva='$id_transace'
	AND reservas.id_sucursal='$id_sucursal'
	";
	$result_fact = _query( $sql_fact);
	$row = _fetch_array ( $result_fact);
	$cliente=$row['nombre'];
	$factnum=$row['numero_doc'];
	$alias_tipodoc=$row['alias_tipodoc'];
	$total=$row['total'];
	$fecha=ED($row['fecha_doc']);

?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Ver reservas</h4>
</div>

<div class="modal-body">
		<div class="row" id="row1">
				<?php
					if ($links!='NOT' || $admin=='1' ){
				?>
						<div>
							<!--load datables estructure html-->
							<header><h4 class="text-danger">reservas No: &nbsp;<?php echo $alias_tipodoc." ".$factnum;  ?></h4>
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
										<!--th class='success'>Descuento</th-->
										<th class='success'>Subtotal</th>

										</tr>
									</thead>
									<tbody>
									<?php
										$sql_det = _query("SELECT p.descripcion, df.cantidad, df.precio, df.subtotal FROM productos as p, detalle_reservas as df WHERE df.id_producto=p.id_producto AND df.id_reserva='$id_transace'");
										while($row2 = _fetch_array($sql_det))
										{
											echo "<tr>";
											echo "<td>".$row2["cantidad"]."</td>";
											echo "<td>".$row2["descripcion"]."</td>";
											echo "<td id='pv' class='text-right'>".number_format($row2["precio"],2,".",",")."</td>";
											echo "<td id='cant1' class='text-right'>".number_format($row2["subtotal"],2,".",",")."</td>";
										//	echo "<td id='subtot' class='text-right'>".number_format($row2["gravado"],2,".",",")."</td>";
											//echo "<td id='combos' class='text-center'>".$combo_chk."</td>";

											echo "</tr>";
										}

									?>

									</tbody>
									<tfoot>
										<tr>
										<td class="thick-line"></td>
										<td class="thick-line"></td>


										<td class="thick-line text-center"><strong>TOTAL $:</strong></td>
										<td  class="thick-line text-right" id='total_dinero' ><strong><?php echo number_format($total,2,".",","); ?></strong></td>
									
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
				<input type="hidden" name="id_transace" id="id_transace" value="<?php echo $id_transace; ?>">
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
	include ('reservascion_funcion_imprimir.php');
	$id_reservas= $_POST['id_transace'];
	$id_sucursal=$_SESSION['id_sucursal'];
	$tipo_impresion=$_POST['tipo_impresion'];

	$voucher=-1;
	$id_pago=0;

	if($tipo_impresion=='CCF')
	{
		$tipo_entrada_salida="CREDITO FISCAL";
	}
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';

	$sql_fact="SELECT * FROM reservas WHERE id_reserva='$id_reservas'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$id_cliente = $row_fact["id_cliente"];
	$a_pagar=$row_fact["monto_pago"];
	$sql_dat_cli = _query("SELECT nombre, dui, nit, nrc FROM clientes WHERE id_cliente='$id_cliente'");
	$datos_cli = _fetch_array($sql_dat_cli);
	$nombreape = $datos_cli["nombre"];
	$nit = $datos_cli["nit"];
	$dui = $datos_cli["dui"];
	$nrc = $datos_cli["nrc"];

	$headers=""; $footers="";
	if ($tipo_impresion=='TIK'){
		$info_reservass=print_ticket($id_reservas,$tipo_impresion);
		$sql_pos="SELECT *  FROM config_pos  WHERE id_sucursal='$id_sucursal' AND alias_tipodoc='TIK'";

		$result_pos=_query($sql_pos);
		$row1=_fetch_array($result_pos);

		$headers=$row1['header1']."|".$row1['header2']."|".$row1['header3']."|".$row1['header4']."|".$row1['header5']."|";
		$headers.=$row1['header6']."|".$row1['header7']."|".$row1['header8']."|".$row1['header9']."|".$row1['header10'];
		$footers=$row1['footer1']."|".$row1['footer2']."|".$row1['footer3']."|".$row1['footer4']."|".$row1['footer5']."|";
		$footers.=$row1['footer6']."|".$row1['footer7']."|".$row1['footer8']."|".$row1['footer8']."|".$row1['footer10']."|";
	}
	if ($tipo_impresion=='DEV'){
		$info_reservass=print_ticket_dev($id_reservas,$dui,$nombreape);

		$sql_pos="SELECT *  FROM config_pos  WHERE id_sucursal='$id_sucursal' AND alias_tipodoc='DEV'";

		$result_pos=_query($sql_pos);
		$row1=_fetch_array($result_pos);

		$headers=$row1['header1']."|".$row1['header2']."|".$row1['header3']."|".$row1['header4']."|".$row1['header5']."|";
		$headers.=$row1['header6']."|".$row1['header7']."|".$row1['header8']."|".$row1['header9']."|".$row1['header10'];
		$footers=$row1['footer1']."|".$row1['footer2']."|".$row1['footer3']."|".$row1['footer4']."|".$row1['footer5']."|";
		$footers.=$row1['footer6']."|".$row1['footer7']."|".$row1['footer8']."|".$row1['footer8']."|".$row1['footer10']."|";
	}
	if ($tipo_impresion=='COF'){
		$info_reservass=print_fact($id_reservas,$tipo_impresion);
	}
	if ($tipo_impresion=='CCF'){
		$info_reservass=print_ccf($id_reservas,$tipo_impresion,$nit,$nrc,$nombreape);
	}
	//directorio de script impresion cliente
	$sql_dir_print="SELECT *  FROM config_dir WHERE id_sucursal='$id_sucursal'";
	$result_dir_print=_query($sql_dir_print);
	$row0=_fetch_array($result_dir_print);
	$dir_print=$row0['dir_print_script'];
	$shared_printer_win=$row0['shared_printer_matrix'];
	$shared_printer_pos=$row0['shared_printer_pos'];

	$nreg_encode['shared_printer_win'] =$shared_printer_win;
	$nreg_encode['shared_printer_pos'] =$shared_printer_pos;
	$nreg_encode['dir_print'] =$dir_print;
	$nreg_encode['reservasr'] =$info_reservass;
	$nreg_encode['sist_ope'] =$so_cliente;
	$nreg_encode['headers'] =$headers;
	$nreg_encode['footers'] =$footers;
  $nreg_encode['a_pagar'] =$a_pagar;
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
