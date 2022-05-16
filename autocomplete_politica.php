<?php
include_once "_core.php";
$query = $_REQUEST['query'];
$sql = "SELECT id_politica as id, descripcion FROM politica WHERE descripcion LIKE '%$query%'";
$result = _query($sql);
$array_prod = array();
while ($row = _fetch_array($result))
{

	$descripcion = $row["descripcion"];
	$array_prod[] =$row['id']."|".$descripcion;
}
echo json_encode ($array_prod);

?>
