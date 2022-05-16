<?php
 include ("_core.php");
 $requestData= $_REQUEST;
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
 $joinQuery = "
 FROM  productos  AS pr
 ";
$extraWhere="";

$columns = array(
  //array( 'db' => '`fac`.`id_factura`', 'dt' => 0, 'field' => 'id_factura' ),
  array( 'db' => '`pr`.`id_producto`', 'dt' => 0, 'field' => 'id_producto' ),
  array( 'db' => '`pr`.`barcode`', 'dt' => 1, 'field' => 'barcode' ),
  array( 'db' => '`pr`.`descripcion`', 'dt' => 2, 'field' => 'descripcion' ),
  array( 'db' => '`pr`.`marca`', 'dt' => 3, 'field' => 'marca' ),
  array( 'db' => '`pr`.`modelo`', 'dt' => 4, 'field' => 'modelo' ),
  array( 'db' => 'id_producto','dt' => 5,'formatter' => function( $id_producto, $row ) {
       $sql_precio = _query("SELECT * FROM precio_producto WHERE id_producto = '$id_producto' ORDER BY porcentaje DESC LIMIT 1");
	   if(_num_rows($sql_precio) > 0){
			$row = _fetch_array($sql_precio);
			$precio = number_format(round($row['total_iva'], 2), 2, '.', ',');
	   }
	   else{
		   $precio = "0.00";
	   }
       
       return "$".$precio;
    },
    'field' => 'id_producto' ),
  array( 'db' => 'id_producto','dt' => 6,'formatter' => function( $id_producto, $row ) {
       	$menudrop="<div class='btn-group'>
			<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
			<ul class='dropdown-menu dropdown-primary'>";
			$id_user=$_SESSION["id_usuario"];
			$admin=$_SESSION["admin"];
			$filename='anular_factura.php';
			$link=permission_usr($id_user,$filename);
			$filename='editar_producto.php';
			$link=permission_usr($id_user,$filename);
			if ($link!='NOT' || $admin=='1' ){
			$menudrop.="<li><a href='editar_producto.php?id_producto=".$row['id_producto']."'><i class='fa fa-pencil'></i> Editar</a></li>";
								    }
								    $filename='borrar_producto.php';
								    $link=permission_usr($id_user,$filename);
								    if ($link!='NOT' || $admin=='1' ){
			$menudrop.="<li><a data-toggle='modal' href='borrar_producto.php?id_producto=" .  $row ['id_producto']."&process=formDelete"."' data-target='#deleteModal' data-refresh='true'><i class='fa fa-eraser'></i> Eliminar</a></li>";
								    }
								     $filename='ver_producto.php';
								     $link=permission_usr($id_user,$filename);
								    if ($link!='NOT' || $admin=='1' ){
			$menudrop.= "<li><a data-toggle='modal' href='ver_producto.php?id_producto=".$row['id_producto']."' data-target='#viewModal' data-refresh='true'><i class='fa fa-search'></i> Ver Detalle</a></li>";
								    }

			$menudrop.="</ul>
						</div>";
		return $menudrop;},
    'field' => 'id_producto' ),


		);
	echo json_encode(
		SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, "pr.id_producto")
	);
  function color($id_color){
    $sql="SELECT * FROM colores WHERE id_color='$id_color'";
		$result=_query($sql);
		$count=_num_rows($result);
		for ($i=0;$i<$count;$i++){
			$row=_fetch_array($result);
			$id=$row['id_color'];
			$color=$row['nombre'];
      return $color;
		}
  }
?>
