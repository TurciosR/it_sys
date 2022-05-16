<?php
 include ("_core.php");
 //$requestData= $_REQUEST;
 require('ssp.customized.class.php' );
// DB table to use
$table = 'clausula';

// Table's primary key
$primaryKey = 'id_clausula';
 // MySQL server connection information
 $sql_details = array(
 'user' => $username,
 'pass' => $password,
 'db'   => $dbname,
 'host' => $hostname
 );
 $joinQuery = "
 FROM clausula AS c";
$extraWhere="";

$columns = array(
  //array( 'db' => '`fac`.`id_factura`', 'dt' => 0, 'field' => 'id_factura' ),
  array( 'db' => 'c.id_clausula', 'dt' => 0, 'field' => 'id_clausula' ),
  array( 'db' => 'c.titulo', 'dt' => 1, 'field' => 'titulo' ),
  array( 'db' => 'c.descripcion', 'dt' => 2, 'field' => 'descripcion' ),
  array( 'db' => 'c.id_clausula','dt' => 3,'formatter' => function( $id_clausula, $row ) {
      $menudrop="<div class='btn-group'>
			<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
			<ul class='dropdown-menu dropdown-primary'>";
			$id_user=$_SESSION["id_usuario"];
			$admin=$_SESSION["admin"];

      $filename='editar_clausula.php';
      $link=permission_usr($id_user, $filename);
      if ($link!='NOT' || $admin=='1') {
          $menudrop.="<li><a href='$filename?id_clausula=".$id_clausula."'><i class='fa fa-pencil'></i> Editar</a></li>";
      }

      $filename='borrar_clausula.php';
      $link=permission_usr($id_user, $filename);
      if ($link!='NOT' || $admin=='1') {
          $menudrop.="<li><a data-toggle='modal' href='$filename?id_clausula=".$id_clausula."' data-target='#addModal' data-refresh='true'><i class='fa fa-eraser'></i> Eliminar</a></li>";
      }
			$menudrop.="</ul>
						</div>";
		return $menudrop;},
    'field' => 'id_clausula' ),

		);
  //echo $joinQuery;
	echo json_encode(
		SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
	);
?>
