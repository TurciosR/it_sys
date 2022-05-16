<?php
include ("_core.php");
function initial(){
	$id_kardex = $_REQUEST['id_kardex'];
	$alias_tipodoc = $_REQUEST['alias_tipodoc'];
	$sql_k = _query("SELECT * FROM kardex WHERE id_kardex = '$id_kardex'");
	$k = _fetch_array($sql_k);
	$concepto = $k["concepto"];
	$fecha_grupo = $k["fechadoc"];
	if($alias_tipodoc == "DES")
	{
		$tipo_descargo = $k["tipo_salida"];
	}

	$sql="SELECT * FROM detalle_mov WHERE id_mov = '$id_kardex'";
	//echo $sql;
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
	<h4 class="modal-title">Detalles de <?php if($alias_tipodoc == "DES"){echo "Descarga de Inventario";}else{echo "Carga de inventario";}?></h4>
</div>
<div class="modal-body">
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row" id="row1">
			<div class="col-lg-12">
				<?php
				echo "<div class='alert alert-success' style='font-weight: bold;'>
						<table class='table'>
							<tr>
								<td><label style='font-size: 15px;'>Concepto:</label></td>
								<td><label style='font-size: 15px;'>".$concepto."  </label></td>
								<td><label style='font-size: 15px;'>Fecha:</label></td>
								<td><label style='font-size: 15px;'>".$fecha_grupo."</label>";
						if($alias_tipodoc == "DES")
						{
								echo "<td><label style='font-size: 15px;'>Tipo:</label></td>
								<td><label style='font-size: 15px;'>".$tipo_descargo."</label>";
						}
						echo "	</tr>
						</table>
					</div>";
						//permiso del script
						if ($links!='NOT' || $admin=='1' ){
						?>

				<table class="table table-bordered table-striped" id="tableview">
					<thead>
						<tr>
							<?php
								echo "<th>N°</th>";
								echo "<th>Producto</th>";
								echo "<th>Cantidad</th>";
								
							?>
						</tr>
					</thead>
					<tbody>
							<?php
							if($count > 0)
							{
								$n = 1;
								while($row = _fetch_array($result))
								{
									$id_producto = $row["id_producto"];
									$sql_producto = _query("SELECT * FROM productos WHERE id_producto = '$id_producto'");
									$p = _fetch_array($sql_producto);
									$nombre = $p["descripcion"];
									$cantidade = $row["cant"];

									echo "<tr>";
									echo "<td>".$n."</td>";
									echo "<td>".$nombre."</td>";
									echo "<td>".$cantidade."</td>";
									
								
								$n += 1;	
								}	
							}
								
							?>
						</tbody>
				</table>
			</div>
		</div>
			<?php
			echo "<input type='hidden' nombre='id_banco' id='id_banco' value=''>";
			?>
		</div>

</div>
<div class="modal-footer">
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
initial();
?>
