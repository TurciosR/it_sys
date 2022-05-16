<?php
include_once "_core.php";
$query = $_REQUEST['query'];
$sql0="SELECT id_proveedor as id, nrc, nombre FROM proveedores WHERE nrc='$query'";
$result = _query($sql0);
if(_num_rows($result)==0)
{
	$sql = "SELECT id_proveedor as id, nrc, nombre FROM proveedores WHERE nrc LIKE '$query%'";
	$result = _query($sql);
}
$array_prod = array();
while ($row = _fetch_array($result))
{
	if($row['nrc']=="")
	$nrc=" ";
	else
	$array_prod[] =$row['nrc']."|".$row['nombre'];
}
echo json_encode ($array_prod);

?>
