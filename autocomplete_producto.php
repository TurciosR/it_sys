<?php
include_once "_core.php";
$query = $_REQUEST['query'];
$sql0="SELECT id_producto as id, descripcion, barcode, marca FROM productos WHERE barcode='%$query%'";
$result = _query($sql0);
if(_num_rows($result)==0)
{
	$sql = "SELECT id_producto as id, descripcion, barcode, marca FROM productos WHERE CONCAT(descripcion, marca, modelo) LIKE '%$query%'";
	$result = _query($sql);
}
$array_prod = array();
while ($row = _fetch_array($result))
{
	if($row['barcode']=="")
	{
	$barcod=" ";
	}
	else
	{
		$barcod=" [".$row['barcode']."] ";
	}
	$marca = $row["marca"];
	$descripcion = $row["descripcion"];
	$array_prod[] =$row['id']."|".$barcod." ".$descripcion.", ".$marca;
}
echo json_encode ($array_prod);

?>
