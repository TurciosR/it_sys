<?php
include_once "_core.php";
$query = $_REQUEST['query'];
	/*
	 SELECT id_servicio, descripcion, tipo_prod_servicio FROM servicio
	 * UNION ALL SELECT id_producto, descripcion,  tipo_prod_servicio FROM producto  WHERE descripcion LIKE '%{$query}%'"
	 */
	 //Version del autocomplete que me permite las busquedas ya sea por barcode o por descripcion 09 enero 2015
	 // $sql0="SELECT id_producto as id, descripcion, barcode, tipo_prod_servicio FROM producto  WHERE LIKE '%{$query}%'";

	 $sql0="SELECT id_producto as id, descripcion, barcode, estilo, id_color, talla FROM productos  WHERE barcode='$query' LIMIT 20";
	 $result = _query($sql0);
	 $numrows= _num_rows($result);

	if ($numrows==0){
	 $sql="
	 SELECT id_producto as id, descripcion, barcode,estilo, id_color, talla FROM productos  WHERE descripcion LIKE '%{$query}%' LIMIT 20
	 ";
	 $result = _query($sql);
	//$numrows = _num_rows($result);
	}
	//$sql = _query("SELECT producto.id_producto,producto.descripcion,producto.marca FROM producto WHERE descripcion LIKE '%{$query}%'");
	$array_prod = array();

	while ($row = _fetch_assoc($result)) {
			if ($row['barcode']=="")
				$barcod=" ";
			else
				$barcod=" [".$row['barcode']."] ";

			$estilo=" [".$row['estilo']."] ";
			$talla=" [".$row['talla']."] ";
			$sqls = _query("SELECT * FROM colores WHERE id_color='".$row["id_color"]."'");
			$dats = _fetch_array($sqls);
			$nombre=" [".$dats['nombre']."] ";
			$array_prod[] =$row['id']."|".$barcod.$row['descripcion'].$estilo.$nombre.$talla;

	}

	echo json_encode ($array_prod); //Return the JSON Array


?>
