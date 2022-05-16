<?php
include_once "_core.php";
$query = $_REQUEST['query'];
$sql = "SELECT id_clausula as id, titulo, descripcion FROM clausula WHERE CONCAT(titulo, descripcion) LIKE '%$query%'";
$result = _query($sql);
$array_prod = array();
while ($row = _fetch_array($result))
{

  $titulo = $row["titulo"];
	$descripcion = $row["descripcion"];
	$array_prod[] =$row['id']."|".$titulo;

}
echo json_encode ($array_prod);

?>
