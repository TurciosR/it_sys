<?php
include ("_core.php");
function initial(){
	$id_categoria = $_REQUEST ['id_categoria'];
	$sql="SELECT *FROM categoria WHERE id_categoria='$id_categoria'";
	$result = _query( $sql );
	$count = _num_rows( $result );

?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Borrar Categoria</h4>
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
										echo "<tr><td>Id categoria</th><td>$id_categoria</td></tr>";
										echo "<tr><td>nombre categoria</td><td>".$row ['nombre']."</td>";
										echo "</tr>";

									}
								}
							?>
						</tbody>
				</table>
			</div>
		</div>
			<?php
			echo "<input type='hidden' nombre='id_categoria' id='id_categoria' value='$id_categoria'>";
			?>
		</div>

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btnDelete">Delete</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

</div>
<!--/modal-footer -->

<?php
/*
}
	else {
		echo "<div></div><br><br><div class='alert alert-warning'>You don't have permission to use this module.</div>";
	}
	*/
}
function deleted() {
	$id_categoria = $_POST ['id_categoria'];
	$table = 'categoria';
	$where_clause = "id_categoria='" . $id_categoria . "'";
	$delete = _delete ( $table, $where_clause );
	if ($delete) {
		$xdatos ['typeinfo'] = 'Success';
		$xdatos ['msg'] = 'Record deleted Successfully';
	} else {
		$xdatos ['typeinfo'] = 'Error';
		$xdatos ['msg'] = 'Record not deleted ';
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
