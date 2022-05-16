<?php
include_once "_core.php";
$query = $_REQUEST['query'];
	/*
	 SELECT id_servicio, descripcion, tipo_prod_servicio FROM servicio
	 * UNION ALL SELECT id_producto, descripcion,  tipo_prod_servicio FROM producto  WHERE descripcion LIKE '%{$query}%'"
	 */
	 //Version del autocomplete que me permite las busquedas ya sea por barcode o por descripcion 09 enero 2015
	 // $sql0="SELECT id_producto as id, descripcion, barcode, tipo_prod_servicio FROM producto  WHERE LIKE '%{$query}%'";

	 $sql0="SELECT id_producto as id, descripcion, barcode, tipo_prod_servicio FROM producto  WHERE barcode='$query'";
	 $result = _query($sql0);
	 $numrows= _num_rows($result);

	if ($numrows==0){
	 $sql="
	 SELECT id_producto as id, descripcion, barcode, tipo_prod_servicio FROM producto  WHERE descripcion LIKE '%{$query}%'
	 UNION ALL
	  SELECT id_servicio as id, descripcion, null as barcode, tipo_prod_servicio FROM servicio WHERE descripcion LIKE '%{$query}%'
	 ";
	 $result = _query($sql);
	//$numrows = _num_rows($result);
	}
	//$sql = _query("SELECT producto.id_producto,producto.descripcion,producto.marca FROM producto WHERE descripcion LIKE '%{$query}%'");
	$array_prod = array();

	while ($row = _fetch_assoc($result)) {
		if($row['tipo_prod_servicio']=='SERVICIO'){
			$array_prod[] =$row['id']."-".$row['descripcion']."(".$row['tipo_prod_servicio'].")";
		}
		else{
			if ($row['barcode']=="")
				$barcod=" ";
			else
				$barcod=" [".$row['barcode']."] ";


			$array_prod[] =$row['id']."-".$barcod.$row['descripcion']." (".$row['tipo_prod_servicio'].")";
		}
	}

	echo json_encode ($array_prod); //Return the JSON Array


?>
