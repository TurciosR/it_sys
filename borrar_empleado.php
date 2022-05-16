<?php
include ("_core.php");
function initial(){
	$id_empleado = $_REQUEST ['id_empleado'];
	$sql="SELECT *FROM empleados WHERE id_empleado='$id_empleado'";
	$result = _query( $sql );
	$count = _num_rows( $result );
	//permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	//permiso del script
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Borrar empleado</h4>
</div>
<div class="modal-body">
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row" id="row1">
			<div class="col-lg-12">
				<?php
						//permiso del script
						if ($links!='NOT' || $admin=='1' ){
					?>
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
										echo "<tr><td>Id empleado</th><td>$id_empleado</td></tr>";
										echo "<tr><td>nombre empleado</td><td>".$row ['nombre']."</td>";
										echo "</tr>";

									}
								}
							?>
						</tbody>
				</table>
			</div>
		</div>
			<?php
			echo "<input type='hidden' nombre='id_empleado' id='id_empleado' value='$id_empleado'>";
			?>
		</div>

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-danger" id="btnDelete">Borrar</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>

</div>
<!--/modal-footer -->

<?php
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}
function deleted() {
	$id_empleado = $_POST ['id_empleado'];
	$table = 'empleados';
	$where_clause = "id_empleado='" . $id_empleado . "'";
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
