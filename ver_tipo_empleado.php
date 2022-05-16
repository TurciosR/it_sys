<?php
include ("_core.php");
$id_tipo_empleado = $_REQUEST['id_tipo_empleado'];
$sql="SELECT * FROM tipo_empleado WHERE id_tipo_empleado='$id_tipo_empleado'";
$result = _query( $sql);
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
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title">Detalle  Tipo Empleado</h4>
</div>
<div class="modal-body">
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row" id="row1">
			<div class="col-lg-12">
				<?php
						//permiso del script
						if ($links!='NOT' || $admin=='1' ){
					?>
					<table	class="table table-bordered table-striped" id="tableview">
						<thead>
							<tr>
								<th>Field</th>
								<th>descripcion</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if ($count > 0) {
									for($i = 0; $i < $count; $i ++) {
										$row = _fetch_array ( $result, $i );

										echo"<tr><td>Id tipo_empleado </td><td>".$row ['id_tipo_empleado']."</td></tr>";
										echo"<tr><td>descripcion</td><td>".$row ['descripcion']."</td></tr>";

									}
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<div class="modal-footer">


<?php

	echo "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
	</div><!--/modal-footer -->";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}

?>
