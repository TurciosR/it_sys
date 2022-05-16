<?php
include_once "_core.php";
$query = $_REQUEST['query'];
$sql = "SELECT id_sistema as id, nombre as nombre, precio, precio_iva, 'sistema' as 'tipo' FROM `sistema` WHERE nombre LIKE '%$query%' UNION ALL SELECT id_servicio as id, descripcion as nombre, precio, precio_iva, 'servicio' as 'tipo' FROM `servicios` WHERE descripcion LIKE '%$query%' ";
$result = _query($sql);
$array_prod = array();
while ($row = _fetch_array($result))
{
	$array_prod[] =$row['id']."|".$row['nombre']." |".$row['tipo']."";
}
echo json_encode ($array_prod);
?>
