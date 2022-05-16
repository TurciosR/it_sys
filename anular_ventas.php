<?php
include ("_core.php");
function initial(){
	$id_transace = $_REQUEST ['id_transace'];
	$id_sucursal = $_SESSION['id_sucursal'];
	$sql="SELECT factura.fecha_doc, factura.numero_doc, factura.alias_tipodoc, factura.total, clientes.nombre FROM factura JOIN clientes 
	ON factura.id_cliente=clientes.id_cliente
	WHERE idtransace='$id_transace' and factura.id_sucursal='$id_sucursal'
	";	
	$result = _query( $sql );
	$count = _num_rows( $result );
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Anular factura</h4>
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
										$cliente=$row['nombre'];
										echo "<tr><td>Fecha</th><td>".ED($row["fecha_doc"])."</td></tr>";
										echo "<tr><td>Id Cliente</td><td>".$cliente."</td>";
										echo "<tr><td>Numero Doc</td><td>".$row["alias_tipodoc"]." ".$row['numero_doc']."</td>";
										echo "<tr><td>Total $:</td><td>".number_format($row['total'],2,".",",")."</td>";
										echo "</tr>";
													
									}
								}	
							?>
						</tbody>
				</table>
			</div>
		</div>
			<?php 
			echo "<input type='hidden' nombre='id_transace' id='id_transace' value='$id_transace'>";
			?>
		</div>

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btnDelete">Anular</button>
	<button type="button" id="btnclose" class="btn btn-danger" data-dismiss="modal">Cerrar</button>

</div>
<!--/modal-footer -->

<?php

}
function deleted() {
	$id_transace = $_POST ['id_transace'];
	$id_sucursal=$_SESSION['id_sucursal'];
	
	$table = 'factura';
	$table1 = 'kardex';
	$table2 = 'stock';
	$where_clause = "idtransace='" . $id_transace . "'";
	
	$form_data = array(	
		'anulada' =>1
	); 
	_begin();
	$update = _update($table,$form_data, $where_clause ); 		
	$sql_fact = _query("SELECT numero_doc, alias_tipodoc FROM factura WHERE idtransace='$id_transace' AND id_sucursal='$id_sucursal'");
	$datos_fact = _fetch_array($sql_fact);
	$numero_doc = $datos_fact["numero_doc"];
	$alias_tipodoc = $datos_fact["alias_tipodoc"];
	//falta hacer update reingresar a stock si hay producto en factura anulada	!!!!!!

	$where_clause1 = "id_transacc='". $id_transace ."' AND numero_doc='$numero_doc' AND alias_tipodoc='$alias_tipodoc'";
	$update1 = _update($table1,$form_data,$where_clause1);

	$sql_det_fact = _query("SELECT cantidad, id_producto FROM detalle_factura WHERE idtransace='$id_transace'");
	$n = 0;
	$num = _num_rows($sql_det_fact);
	while ($row = _fetch_array($sql_det_fact)) 
	{
		$id_producto = $row["id_producto"];
		$cantidad = $row["cantidad"];
		$where = "id_producto='".$id_producto."' AND id_sucursal ='".$id_sucursal."'";
		$sql_stock = _query("SELECT existencias FROM stock WHERE $where");
		$datos_stock = _fetch_array($sql_stock);
		$cantidaddb = $datos_stock["existencias"];
		$cantidadn = $cantidaddb + $cantidad;
		$form_data2 = array(
			'existencias' => $cantidadn,
		);
		$update2 = _update($table2, $form_data2, $where);
		if($update2)
		{
			$n++;
		}

	}
	if ($update && $update1 && $n == $num)
	{
		_commit();
		$xdatos ['typeinfo'] = 'Success';
		$xdatos ['msg'] = 'Factura Anulada!';
	} 
	else 
	{
		_rollback();
		$xdatos ['typeinfo'] = 'Error';
		$xdatos ['msg'] = 'Factura no Anulada!';
	}
	echo json_encode ( $xdatos );
}
if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'formDelete' :
				initial();
				break;
			case 'deleted' :
				deleted();
				break;
		}
	}
}

?>
