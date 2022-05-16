<?php
include_once "_core.php";
$query = $_REQUEST['query'];
$sql = "SELECT id_servicio as id, descripcion FROM servicios WHERE descripcion LIKE '%$query%' AND estado = 1";
$result = _query($sql);
$array_prod = array();
while ($row = _fetch_array($result))
{
	$array_prod[] =$row['id']."|".$row['descripcion'];
}
echo json_encode ($array_prod);
?>
