<?php
include ("_core.php");
function initial(){
	$id_pedido = $_REQUEST ['id_pedido'];
	$id_user=$_SESSION["id_usuario"];
	$id_sucursal=$_SESSION['id_sucursal'];

	$sql="SELECT pedidos.*
	FROM pedidos
	WHERE idtransace='$id_pedido'
	AND  id_sucursal='$id_sucursal'
	AND  finalizado=0";
	$result = _query( $sql );
	$count = _num_rows( $result );
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Anular Preingreso</h4>
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
										echo "<tr><td>Id pedido</th><td>$id_pedido</td></tr>";
										echo "<tr><td>Fecha:</td><td>".$row['fecha']."</td>";
										echo "<tr><td>Items:</td><td>".$row['items']."</td>";
										echo "<tr><td>Pares:</td><td>".$row['pares']."</td>";
										echo "</tr>";

									}
								}
							?>
						</tbody>
				</table>
			</div>
		</div>
			<?php
			echo "<input type='hidden' nombre='id_pedido' id='id_pedido' value='$id_pedido'>";
			?>
		</div>

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btnAnular">Anular</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

</div>
<!--/modal-footer -->

<?php

}
function deleted() {
	$id_pedido = $_POST ['id_pedido'];
	$id_user=$_SESSION["id_usuario"];

	$id_sucursal=$_SESSION['id_sucursal'];
	$table0 = 'detalle_pedidos';
	$table1 = 'pedidos';
	$where_clause = "idtransace='" . $id_pedido . "'";
 	$delete0=_delete($table0,$where_clause );
	$delete1=_delete($table1,$where_clause );
	if ($delete0 && $delete1) {
		$xdatos ['typeinfo'] = 'Success';
		$xdatos ['msg'] = 'pedido Anulado!';
	} else {
		$xdatos ['typeinfo'] = 'Error';
		$xdatos ['msg'] = 'pedido no Anulado! ';
	}
	echo json_encode ( $xdatos );
}
if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'formAnular' :
				initial();
				break;
			case 'deleted' :
				deleted();
				break;
		}
	}
}

?>
