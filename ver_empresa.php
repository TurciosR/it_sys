<?php
include ("_core.php");
$id_empresa = $_REQUEST['id_empresa'];
$sql="SELECT * FROM empresa WHERE id_empresa='$id_empresa'";
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
	<h4 class="modal-title">Detalle de Empresa</h4>
</div>
<div class="modal-body">
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row" id="row1">
			<?php
			//permiso del script
			if ($links!='NOT' || $admin=='1' ){
			?>
			<div class="col-lg-12">
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
					                    $nombre=$row['nombre'];
					                    $razon=$row["razonsocial"];
					                    $direccion=$row["direccion"];
					                    $telefono1=$row["telefono1"];
					                    $telefono2=$row["telefono2"];
					                    $web=$row["website"];
					                    $email=$row["email"];
					                    $nit=$row["nit"];
					                    $iva=$row["iva"];

										echo"<tr><td>Id Empresa </td><td>".$id_empresa."</td></tr>";
										echo"<tr><td>Nombre</td><td>".$nombre."</td></tr>";
										echo"<tr><td>Razón Social</td><td>".$razon."</td></tr>";
										echo"<tr><td>Direccion</td><td>".$direccion."</td></tr>";
										echo"<tr><td>Telefono1</td><td>".$telefono1."</td></tr>";
										echo"<tr><td>Telefono2</td><td>".$telefono2."</td></tr>";
										echo"<tr><td>Website</td><td>".$web."</td></tr>";
										echo"<tr><td>Email</td><td>".$email."</td></tr>";
										echo"<tr><td>NIT</td><td>".$nit."</td></tr>";
										echo"<tr><td>IVA</td><td>".$iva."</td></tr>";


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
