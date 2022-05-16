<?php
include "_core.php";
include ('num2letras.php');
//include ('facturacion_funcion_imprimir.php');
//include("escpos-php/Escpos.php");
function initial() {
$id_factura = $_REQUEST ['id_factura'];
//	$id_sucursal=$_REQUEST['id_sucursal'];
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

	$sql_fact="SELECT factura.*, clientes.nombre FROM factura JOIN clientes
	ON factura.id_cliente=clientes.id_cliente
	WHERE id_factura='$id_factura'
	";
	/*and factura.id_sucursal='$id_sucursal*/
	$result_fact = _query( $sql_fact);
	$row = _fetch_array ( $result_fact);
	$cliente=$row['nombre'];
	$factnum=$row['num_fact_impresa'];
	$alias_tipodoc=$row['tipo_documento'];
	$total=$row['total'];
	$fecha=ED($row['fecha']);

?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Detalle de factura</h4>
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
										<th class='success'>Entregado</th>
										</tr>
									</thead>
									<tbody>
									<?php
										$sql_det = "SELECT p.descripcion, df.cantidad, df.precio_venta, df.subtotal
										FROM productos as p, factura_detalle as df
										 WHERE p.id_producto=df.id_prod_serv AND df.id_factura='$id_factura'";

										$result_det=_query($sql_det);
										$filas=1;
										while($row2 = _fetch_array($result_det))
										{
											echo "<tr>";
											echo "<td>".$row2["cantidad"]."</td>";
											echo "<td>".$row2["descripcion"]."</td>";
											echo "<td id='pv' class='text-center'><input type='checkbox' id='activar' name='activar' class='checkbox i-checks cort' >
											<input type='hidden'  id='cortesia' name='cortesia' value='0'>
											<input type='hidden'  id='idco' name='idco' value='".$filas."'></td>";

											echo "</tr>";
											$filas++;
										}

									?>

									</tbody>
									<!--tfoot>
										<tr>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<td class="thick-line text-center"><strong>TOTAL $:</strong></td>
										<td  class="thick-line text-right" id='total_dinero' ><strong><?php echo number_format($total,2,".",","); ?></strong></td>
										</tr>
									</tfoot-->
								</table>
								<?php
								/*
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
*/
								?>
					</section>

						</div>
				<input type="hidden" name="alias_tipodoc" id="alias_tipodoc" value="<?php echo $alias_tipodoc; ?>">
				<input type="hidden" name="id_factura" id="id_factura" value="<?php echo $id_factura; ?>">
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="btnDesp">Finalizar</button>
					<!--button type="button" class="btn btn-danger" data-dismiss="modal">Finalizar</button-->
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

function despacho() {
	$id_factura = $_POST["id_factura"];
	$sql_result=_query("SELECT id_factura FROM factura WHERE  id_factura='$id_factura'");
	$numrows=_num_rows($sql_result);

	$table = 'factura';
	$form_data = array (
	'entregado' => 1
	);
	$where_clause = "id_factura ='".$id_factura."'";
	if($numrows != 0)
	{
			$insertar = _update($table,$form_data, $where_clause);
			if($insertar)
			{
				 $xdatos['typeinfo']='Success';
				 $xdatos['msg']='Despacho realizado correctamente!';
				 $xdatos['process']='insert';
			}
			else
			{
				 $xdatos['typeinfo']='Error';
				 $xdatos['msg']='Despacho no pudo ser realizado!';
		}
	}
echo json_encode($xdatos);
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
		despacho();
		break;
	}

 //}
}
?>
