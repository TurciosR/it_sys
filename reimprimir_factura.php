<?php
include ("_core.php");
include ('num2letras.php');
include ('facturacion_funcion_imprimir.php');

function initial(){
	$id_factura = $_REQUEST ['id_factura'];
	//$sql="SELECT * FROM factura WHERE id_factura='$id_factura'";
	$sql="SELECT factura.*, cliente.nombre,cliente.apellido FROM factura JOIN cliente
	ON factura.id_cliente=cliente.id_cliente
	WHERE id_factura='$id_factura'
	";
	$result = _query( $sql );
	$count = _num_rows( $result );
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Imprimir factura</h4>
</div>
<div class="modal-body">
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row" id="row1">
			<div class="col-lg-12">
				<table class="table table-bordered table-striped" id="tableview">
					<thead>
						<tr>
							<th>Campo</th>
							<th>Descripcion</th>
						</tr>
					</thead>
					<tbody>
							<?php
								if ($count > 0) {
									for($i = 0; $i < $count; $i ++) {
										$row = _fetch_array ( $result, $i );
										$cliente=$row['nombre']." ".$row['apellido'];
										echo "<tr><td>Id factura</th><td>$id_factura</td></tr>";
										echo "<tr><td>Id Cliente</td><td>".$cliente."</td>";
										echo "<tr><td>Numero Doc</td><td>".$row['numero_doc']."</td>";
										echo "<tr><td>Total $:</td><td>".$row['total']."</td>";
										echo "</tr>";

									}
								}
							?>
						</tbody>
				</table>
			</div>
		</div>
			<?php
			echo "<input type='hidden' nombre='id_factura' id='id_factura' value='$id_factura'>";
			?>
		</div>

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btnPrint">Imprimir</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

</div>
<!--/modal-footer -->

<?php

}
function imprimir_fact() {
	$id_factura = $_REQUEST['id_factura'];
	$sql_fact="SELECT * FROM factura WHERE id_factura='$id_factura'";
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
		$id_cliente=$row_fact['id_cliente'];
		$fecha=$row_fact['fecha'];
		$numero_doc=trim($row_fact['numero_doc']);
		$total=$row_fact['total'];


		list($num_fact,$tipo_impresion)=explode("_",$numero_doc);
		if ($tipo_impresion=='TIK'){
			$info_facturas=print_ticket($id_factura);
		}
		if ($tipo_impresion=='COF'){
			$info_facturas=print_fact($id_factura);
		}

		//directorio de script impresion cliente
		$sql_dir_print="SELECT dir_print_script FROM empresa";
		$result_dir_print=_query($sql_dir_print);
		$row_dir_print=_fetch_array($result_dir_print);
		$dir_print=$row_dir_print['dir_print_script'];
		$nreg_encode['dir_print'] =$dir_print;
			$nreg_encode['facturar'] =$info_facturas;
			$nreg_encode['sist_ope'] =$so_cliente;
			$nreg_encode['tipo_impresion'] =$tipo_impresion;
			echo json_encode($nreg_encode);

	}
}

if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'formDelete' :
				initial();
				break;
			case 'reimprimir' :
				reimprimir();
				break;
			case 'imprimir_fact' :
				imprimir_fact();
				break;
		}
	}
}

?>
