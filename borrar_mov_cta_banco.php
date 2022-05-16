<?php
include ("_core.php");
function initial(){
	$id_movimiento = $_REQUEST ['id_movimiento'];
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
	<h4 class="modal-title">Borrar Movimiento</h4>
</div>
<div class="modal-body">
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row" id="row1">
			<div class="col-lg-12">
				<?php
				//permiso del script
				if ($links!='NOT' || $admin=='1' ){
				?>
				<div class="alert alert-warning">
					Esta seguro que desea eliminar este movimiento? No podra deshacer esta accion.
				</div>
			</div>
		</div>
			<?php
			echo "<input type='hidden' nombre='id_movimiento' id='id_movimiento' value='$id_movimiento'>";
			?>
		</div>

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-danger" id="btnDelete">Borrar</button>
	<button type="button" class="btn btn-primary" data-dismiss="modal" id="clos">Salir</button>

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
	$id_movimiento = $_POST ['id_movimiento'];
	$table = 'mov_cta_banco';
	$where_clause = "id_movimiento='" . $id_movimiento . "'";
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
