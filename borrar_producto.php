<?php
include ("_core.php");
function initial(){
	$id_producto = $_REQUEST ['id_producto'];
	$sql="SELECT productos.id_producto,productos.descripcion,productos.barcode,productos.estilo,productos.talla,productos.precio1,productos.precio2,productos.descuento,productos.numera,productos.letra,proveedores.nombre,colores.nombre AS color FROM productos INNER JOIN proveedores ON productos.id_proveedor=proveedores.id_proveedor LEFT JOIN colores ON colores.id_color=productos.id_color WHERE id_producto='$id_producto'";
	$result = _query($sql);
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
	<h4 class="modal-title">Borrar producto</h4>
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
					<?php
						$row = _fetch_array($result);
						$descripcion=$row['descripcion'];
						$barcode=$row['barcode'];
	                    $estilo=$row['estilo'];
	                    $talla=$row['talla'];
	                    $precio1=$row['precio1'];
	                    $numera=$row['numera'];
	                    $letra=$row['letra'];
	                    $descuento=$row['descuento'];
	                    $nombre=$row['nombre'];
	                    $color=$row['color'];
						echo"<tr><td style='width:25%;'>Descripcion:</td><td colspan='3' style='width:75%;'>".$descripcion."</td></tr>";
						echo"<tr><td>Proveedor :</td><td colspan='3'>".$nombre."</td></tr>";
						echo"<tr><td style='width:25%;'>Barcode:</td><td style='width:25%;'>".$barcode."</td>";
						echo"<td style='width:25%;'>Estilo:</td><td style='width:25%;'>".$estilo."</td></tr>";
						echo"<tr><td>Color:</td><td>".$color."</td>";
						echo"<td>Letra:</td><td>".$letra."</td></tr>";
	                    echo"<tr><td>Numeracion:</td><td>".$numera."</td>";
						echo"<td>Talla:</td><td>".$talla."</td></tr>";
	                    echo"<tr><td>Precio:</td><td>$".$precio1."</td>";
	                    echo"<td>Descuento:</td><td>".$descuento."%</td>";
	                    $sqla = _query("SELECT id_sucursal FROM sucursal");
	                    $cont = 0;
	                    $n = 1;
	                    $hay = 0;
	                    echo"<tr>";	
	                    while ($row2 = _fetch_array($sqla))
	                    {
	                    	$sqlb = _query("SELECT existencias FROM stock WHERE id_producto='$id_producto' AND id_sucursal='".$row2['id_sucursal']."'");
	                    	$dat = _fetch_array($sqlb);
	                    	$exis = $dat["existencias"];
	                    	if($exis >0)
	                    	{
	                    		$hay = 1;
	                    	}
	                    	else
	                    	{
	                    		$exis = 0;
	                    	}
	                    	if($cont == 2)
	                    	{
	                    		echo"</tr><tr>";
	                    		$cont = 0;
	                    	}
	                    	echo "<td>Existencia S".$n.":</td><td>".$exis."</td>";
	                    	$cont ++;
	                    	$n ++;
	                    }
	                ?>
	            </table>
	            <?php
					echo "<input type='hidden' nombre='id_producto' id='id_producto' value='$id_producto'>";
				?>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer" style="margin-top:-5%; ">
	<?php
		if($hay)
		{
			echo "<div class='alert alert-danger text-center'>No puede eliminar el producto por que posee existencias</div>";
		} 
	?>
	<button type="button" class="btn btn-danger" id="btnDelete" <?php if($hay){ echo "disabled"; }?>>Borrar</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

</div>
<!--/modal-footer -->

<?php
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}
function deleted() {
	$id_producto = $_POST ['id_producto'];
	$table = 'productos';
	$where_clause = "id_producto='" . $id_producto . "'";
	$delete = _delete ( $table, $where_clause );
	if ($delete) {
		$xdatos ['typeinfo'] = 'Success';
		$xdatos ['msg'] = 'Registro Borrado!';
	} else {
		$xdatos ['typeinfo'] = 'Error';
		$xdatos ['msg'] = 'Registro no pudo ser Borrado ';
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
