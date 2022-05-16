<?php
 include ("_core.php");
	require('ssp.customized.class.php' );
 $requestData= $_REQUEST;

// DB table to use
$id_sucursal=$_SESSION["id_sucursal"];
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
  JOIN stock AS s ON (p.id_producto=s.id_producto)";
  $extraWhere = "s.id_sucursal='$id_sucursal'";
  $columns = array(
  // 	array( 'db' => '`fac`.`id_factura`', 'dt' => 0, 'field' => 'id_factura' ),
  array( 'db' => '`p`.`id_producto`', 'dt' => 0, 'field' => 'id_producto' ),
  array( 'db' => '`p`.`barcode`', 'dt' => 1, 'field' => 'barcode'),
  array( 'db' => '`p`.`descripcion`', 'dt' => 2, 'field' => 'descripcion'),
  array( 'db' => '`p`.`ultcosto`', 'dt' => 3, 'field' => 'ultcosto'),
  array( 'db' => '`p`.`precio1`', 'dt' =>4, 'field' => 'precio1'),
  array( 'db' => '`s`.`existencias`', 'dt' =>5, 'field' => 'existencias'),
  array( 'db' => '`p`.`id_posicion`', 'dt' => 6, 'formatter' => function( $id_posicion, $row ) {
  $id_posicion=$row['id_posicion'];
  $tiene_posicion=possition($id_posicion);
return $tiene_posicion;}, 'field' => 'id_posicion'),
  array( 'db' => '`p`.`id_producto`','dt' => 7,'formatter' => function($id_producto,$row){
  //array( 'db' => '`fac`.`id_factura`', 'dt' => 7, 'formatter' => function( $id_factura, $row ){
      $menudrop="<div class='btn-group'>
      <a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
      <ul class='dropdown-menu dropdown-primary'>";
      $id_sucursal=$_SESSION['id_sucursal'];
      $id_user=$_SESSION["id_usuario"];
      $admin=$_SESSION["admin"];
      $filename='editar_producto.php';
      $link=permission_usr($id_user,$filename);
      if ($link!='NOT' || $admin=='1' ){
        $menudrop.="<li><a href='$filename?id_producto=".$row['id_producto']."'><i class='fa fa-pencil'></i> Editar</a></li>";
      }
      $filename='borrar_producto.php';
      $link=permission_usr($id_user,$filename);
      if ($link!='NOT' || $admin=='1' ){
        $menudrop.="<li><a data-toggle='modal' href='$filename?id_producto=".$row['id_producto']."&process=formDelete"."' data-target='#deleteModal' data-refresh='true'><i class='fa fa-eraser'></i> Eliminar</a></li>";
      }
      $filename='ver_producto.php';
      $link=permission_usr($id_user,$filename);
      if ($link!='NOT' || $admin=='1' ){
        $menudrop.= "<li><a data-toggle='modal' href='$filename?id_producto=".$row['id_producto']."' data-target='#viewModal' data-refresh='true'><i class='fa fa-search'></i> Ver Detalle</a></li>";
      }

    $menudrop.="</ul>
          </div>";
  return $menudrop;},
  'field' => 'id_producto' ),
  );

	echo json_encode(
		SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
	);
  function  possition($id_pos){

		if($id_pos>0)
		{
			$sql="SELECT al.descripcion as alm,  es.descripcion as est,  po.posicion as poss, fl.fila as fil
			FROM  posicion po JOIN almacen al ON po.id_almacen=al.id_almacen
			JOIN estante es ON po.id_estante=es.id_estante
			JOIN filas fl ON po.id_fila=fl.id_fila
			WHERE po.id_posicion='$id_pos'
			";
			$query= _query($sql);
			$result = _fetch_array($query);
			if ($result['alm']!="" || $result['est']!="" || $result['fil']!="")
			$ubicacion= $result['alm'].", ".$result['est'].", FILA: ".$result['fil'].", POSICIÓN: ".$result['poss']." ";

			else
			$ubicacion = "NO ASIGNADO";
		}
		else
		{
			$ubicacion = "NO ASIGNADO";
		}
		return $ubicacion;
	}
?>
