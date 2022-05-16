<?php
include ("_core.php");
$id_usuario = $_REQUEST['id_usuario'];
$sql="SELECT * FROM usuario WHERE id_usuario='$id_usuario'";
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
	<h4 class="modal-title">Detalle usuario</h4>
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
										$id_usuario=$row["id_usuario"];
										$nombre=$row["nombre"];
										$usuario=$row["usuario"];
										$tipo_usuario=$row["tipo_usuario"];

										 if($tipo_usuario==1)
										 {
					                      $admin="Administrador";
					                     }
					                     else
					                     {
					                      $admin="Usuario Normal";
					                     }

										echo"<tr><td>Id Usuario </td><td>".$id_usuario."</td></tr>";
										echo"<tr><td>Nombre</td><td>".$nombre."</td></tr>";
										echo"<tr><td>Usuario</td><td>".$usuario."</td></tr>";
										echo"<tr><td>Tipo Usuario</td><td>".$admin."</td></tr>
										</tr>";



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
