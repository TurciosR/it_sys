<?php
include_once "_core.php";
$query = $_REQUEST['query'];
$sql = _query("SELECT producto.id_producto,producto.descripcion,producto.marca FROM producto WHERE descripcion LIKE '%{$query}%'");
$array_prod = array();

	while ($row = _fetch_assoc($sql)) {
		$array_prod[] =$row['id_producto']."-".$row['descripcion']." ".$row['marca'];

	}
	echo json_encode ($array_prod); //Return the JSON Array
?>
