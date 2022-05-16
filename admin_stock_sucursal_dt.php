<?php
	include ("_core.php");
	$requestData= $_REQUEST;
	$id_sucursal= $_REQUEST['id_sucursal'];
	require('ssp.customized.class.php' );
	// DB table to use
	$table = 'productos';
	// Table's primary key
	$primaryKey = 'id_producto';
	// MySQL server connection information
	$sql_details = array(
	'user' => $username,
	'pass' => $password,
	'db'   => $dbname,
	'host' => $hostname
	);
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

	$joinQuery ="
	FROM productos AS p
	JOIN stock AS s ON (p.id_producto=s.id_producto)
	 JOIN colores AS co ON (p.id_color = co.id_color)
	";

	$extraWhere = "s.id_sucursal='$id_sucursal'";
	$columns = array(
	array( 'db' => '`p`.`id_producto`', 'dt' => 0, 'field' => 'id_producto' ),
	array( 'db' => '`p`.`descripcion`', 'dt' => 1, 'field' => 'descripcion' ),
	array( 'db' => '`co`.`nombre`', 'dt' => 2, 'field' => 'nombre' ),
	array( 'db' => '`p`.`estilo`', 'dt' => 3, 'field' => 'estilo' ),
	array( 'db' => '`p`.`talla`', 'dt' => 4, 'field' => 'talla' ),
	array( 'db' => '`p`.`numera`', 'dt' => 5, 'field' => 'numera'),
	array( 'db' => '`s`.`existencias`', 'dt' =>6, 'field' => 'existencias' ),
	);
	echo json_encode(
		SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
	);
?>
