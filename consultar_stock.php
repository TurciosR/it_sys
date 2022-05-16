<?php
include_once "_core.php";
//include("escpos-php/Escpos.php");
function initial() 
{
	//permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
  	$id_sucursal=$_SESSION['id_sucursal'];
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Consultar Stock</h4>
</div>

<div class="modal-body">
<div class="row">
<div class="form-group col-md-12">
	<label>Sucursal</label>
	<select class='form-control select' id="sucursal" name="suc" style="width: 100%;">
		<?php 
			$ini = 0;
			$sql = _query("SELECT * FROM sucursal WHERE id_sucursal!='$id_sucursal'");
			while ($row = _fetch_array($sql)) 
			{
				echo "<option value='".$row["id_sucursal"]."'";
				if($ini == 0)
				{
					echo " selected ";
				}
				echo ">".$row["descripcion"].": ".$row["direccion"]."</option>";
				$ini ++;
			}
		?>
	</select>
</div>
<div class="form-group col-md-4">
	<label>Descripcion</label>
	<input type="text" name="descripcionq" id="descripcionq" class="form-control busqa">
</div>
<div class="form-group col-md-4">
	<label>Estilo</label>
	<input type="text" name="estiloq" id="estiloq" class="form-control busqa">
</div>
<div class="form-group col-md-4">
	<label>Color</label>
	<select class='form-control select' id="colorq" name="colorq" style="width: 100%;">
		<option value="">Selccione</option>
		<?php 
			$sql = _query("SELECT * FROM colores");
			while ($row = _fetch_array($sql)) 
			{
				echo "<option value='".$row["id_color"]."'>".$row["nombre"]."</option>";
			}
		?>
	</select>
</div>
</div>
	<?php
	if ($links!='NOT' || $admin=='1' ){
	?>
		<div class="row" id="row1">
			<section>
				<div class="table-responsive m-t">
					<table class="table table2 table-fixed table-striped "id="inventable">
						<thead>
							<tr>
								<th>Barcode</th>
								<th>Descripcion</th>
								<th>Estilo</th>
								<th>Color</th>
								<th>Talla</th>
								<th>Precio</th>
								<th>Existencias</th>
							</tr>
						</thead>
						<tbody id="res">
							
						</tbody>
					</table>
				</div>
			</section>
		</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$(".select").select2();
		$("#descripcionq").focus();
		$(".busqa").keyup(function(e)
		{
			if(e.keyCode !=8)
			{
				if($(this).val().length >= 3)
				{
					buscaaar();
				}
			}
			else
			{
				buscaaar();
			}
		});
		$(".select").change(function(){
			buscaaar();
		});
		buscaaar();
	});
	function buscaaar()
	{
		var descripcion = $("#descripcionq").val();
		var estilo = $("#estiloq").val();
		var color = $("#colorq").val();
		var sucursal = $("#sucursal").val();
		$.ajax({
			type:'POST',
			url:'consultar_stock.php',
			data:'process=consultar_stock&id_sucursal='+sucursal+"&descripcion="+descripcion+"&estilo="+estilo+"&color="+color,
			dataType:'JSON',
			success: function(datax)
			{
				$("#res").html(datax.table);
			}
		});
	}
</script>
<div class="modal-footer">
	<button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>
</div>

<?php

} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}
function consultar_stock()
{
	$id_sucursal=$_POST['id_sucursal'];
	$descripcion=$_POST['descripcion'];
	$estilo=$_POST['estilo'];
	$color=$_POST['color'];
	$add = "";
	if($descripcion != "")
	{
		$add .= " AND p.descripcion LIKE '%$descripcion%'";
	}
	if($estilo != "")
	{
		$add .= " AND p.estilo LIKE '%$estilo%'";
	}
	if($color != "")
	{
		$add .= " AND p.id_color = '$color'";
	}

	$sql2="SELECT p.barcode, p.descripcion, co.nombre, p.estilo, p.talla, s.existencias, p.precio1 FROM productos as p, colores as co, stock as s WHERE p.id_producto=s.id_producto AND p.id_color=co.id_color AND s.id_sucursal ='$id_sucursal' AND s.existencias>0";
	$sql2 .= $add." LIMIT 10";
	$stock2=_query($sql2);
	$table = "";
	//echo $sql2; 
	while($row2=_fetch_array($stock2))
	{
		$table .= "<tr>
					<td>".$row2["barcode"]."</td>
					<td>".$row2["descripcion"]."</td>
					<td>".$row2["estilo"]."</td>
					<td>".$row2["nombre"]."</td>
					<td>".$row2["talla"]."</td>
					<td>".$row2["precio1"]."</td>
					<td>".$row2["existencias"]."</td>
				  </tr>";
	}
	$xdatos['table'] = $table;
	echo json_encode($xdatos); //Return the JSON Array
}
//functions to load
if(!isset($_REQUEST['process'])){
	initial();
}
//else {
if (isset($_REQUEST['process'])) 
{
	switch ($_REQUEST['process'])
	{
		case 'formEdit':
			initial();
			break;
		case 'consultar_stock':
			consultar_stock();
	}

 //}
}
?>
