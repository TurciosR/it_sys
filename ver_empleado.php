<?php
include ("_core.php");
$id_empleado = $_REQUEST['id_empleado'];
$sql="SELECT * FROM empleados WHERE id_empleado='$id_empleado'";
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
	<h4 class="modal-title">Detalle de empleado</h4>
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
								<th>Campo</th>
								<th>Detalle</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if ($count > 0) {
									for($i = 0; $i < $count; $i ++) {
										$row = _fetch_array ( $result, $i );
										$id_empleado=$row["id_empleado"];
										$nombre=$row["nombre"];
										$nit=$row["nit"];
										$dui=$row["dui"];
										$telefono1=$row["telefono1"];
										$telefono2=$row["telefono2"];
										$email=$row["email"];
										$salariobase=$row["salario"];

										echo"<tr><td>Id empleado </td><td>".$id_empleado."</td></tr>";
										echo"<tr><td>Nombre</td><td>".$nombre."</td></tr>";
										echo"<tr><td>NIT</td><td>".$nit."</td></tr>";
										echo"<tr><td>DUI</td><td>".$dui."</td></tr>";
										echo"<tr><td>Telefonos</td><td>".$telefono1." y ".$telefono2."</td></tr>";

										echo"<tr><td>Correo</td><td>".$email."</td></tr>";
										echo"<tr><td>Salario</td><td>".$salariobase."</td></tr>";



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
