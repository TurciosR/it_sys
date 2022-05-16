<?php
include_once "_core.php";
$query = $_REQUEST['query'];
$sql = "SELECT id_sistema as id, nombre FROM sistema WHERE nombre LIKE '%$query%'";
$result = _query($sql);
$array_prod = array();
while ($row = _fetch_array($result))
{
	$array_prod[] =$row['id']."|".$row['nombre'];
}
echo json_encode ($array_prod);
?>
