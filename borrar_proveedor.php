<?php
include ("_core.php");
function initial(){
	$id_proveedor = $_REQUEST ['id_proveedor'];
	$sql="SELECT *FROM proveedores WHERE id_proveedor='$id_proveedor'";
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
	<h4 class="modal-title">Borrar Proveedor</h4>
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
							<th>Descripción</th>
						</tr>
					</thead>
					<tbody>
							<?php
								while($row = _fetch_array($result))
								{
									echo "<tr><td>Id</th><td>$id_proveedor</td></tr>";
									echo "<tr><td>Nombre</td><td>".$row['nombre']."</td></tr>";
									echo "<tr><td>NIT</td><td>".$row['nit']."</td>";
									echo "</tr>";
								}
							?>
						</tbody>
				</table>
			</div>
		</div>
			<?php
			echo "<input type='hidden' nombre='id_proveedor' id='id_proveedor' value='$id_proveedor'>";
			?>
		</div>

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-danger" id="btnDelete">Borrar</button>
	<button type="button" class="btn btn-primary" data-dismiss="modal">Salir</button>

</div>
<!--/modal-footer -->

<?php
	} //permiso del script
	else
	{
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}
function deleted()
{
	$id_proveedor = $_POST ['id_proveedor'];
	$table = 'proveedores';
	$where_clause = "id_proveedor='" . $id_proveedor . "'";
	$delete = _delete ( $table, $where_clause );
	if ($delete)
	{
		$xdatos ['typeinfo'] = 'Success';
		$xdatos ['msg'] = 'Registro eliminado con exito!';
	} 
	else 
	{
		$xdatos ['typeinfo'] = 'Error';
		$xdatos ['msg'] = 'Registro no pudo ser eliminado!';
	}
	echo json_encode ( $xdatos );
}
if (! isset ( $_REQUEST ['process'] ))
{
	initial();
} 
else 
{
	if (isset ( $_REQUEST ['process'] ))
	{
		switch ($_REQUEST ['process'])
		{
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
