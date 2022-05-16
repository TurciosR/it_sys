<?php
 include ("_core.php");
 $requestData= $_REQUEST;
 require('ssp.customized.class.php' );
// DB table to use
$table = 'contrato';

// Table's primary key
$primaryKey = 'id_contrato';
 // MySQL server connection information
 $sql_details = array(
 'user' => $username,
 'pass' => $password,
 'db'   => $dbname,
 'host' => $hostname
 );
 $joinQuery = "
 FROM  contrato  AS pr
 JOIN clientes AS cte ON (pr.id_cliente = cte.id_cliente)
 ";
$extraWhere="";

$columns = array(
  //array( 'db' => '`fac`.`id_factura`', 'dt' => 0, 'field' => 'id_factura' ),
  array( 'db' => 'pr.id_contrato', 'dt' => 0, 'field' => 'id_contrato' ),
  array( 'db' => 'cte.nombre', 'dt' => 1, 'field' => 'nombre' ),
  array( 'db' => 'pr.id_contrato', 'dt' => 2, 'formatter'=> function($id_contrato){
    $sql_monto=_query("SELECT monto, iva FROM contrato WHERE id_contrato='$id_contrato'");
    $row_m = _fetch_array($sql_monto);
    $monto = $row_m["monto"];
    $iva = $row_m["iva"];
    $sub_total = number_format(round($monto + $iva, 4), 2);

    return $sub_total;
    } ,'field' => 'id_contrato' ),
  array( 'db' => '`pr`.`fecha`', 'dt' => 3, 'formatter'=> function($fecha){
    $fecha = ED($fecha);
    return $fecha;
    } ,'field' => 'fecha' ),
  array( 'db' => '`pr`.`fecha_vence`', 'dt' => 4, 'formatter'=> function($fecha_vence){
    $fecha_vence = ED($fecha_vence);
    return $fecha_vence;
    } ,'field' => 'fecha_vence' ),
  array( 'db' => '`pr`.`tipo`', 'dt' => 5, 'formatter'=> function($tipo){
    if($tipo == "GER")
    {
      $text = "GENERAL";
    }
    if($tipo == "SER")
    {
      $text = "SERVICIO";
    }
    if($tipo == "SIS")
    {
      $text = "SISTEMA";
    }
    return $text;
    } ,'field' => 'tipo' ),
  array( 'db' => '`pr`.`activo`', 'dt' => 6, 'formatter'=> function($activo){
    if($activo == 1)
    {
      $text = "VIGENTE";
    }
    else
    {
      $text = "FINALIZADO";
    }
    return $text;
    } ,'field' => 'activo' ),
  array( 'db' => 'id_contrato','dt' => 7,'formatter' => function( $id_contrato, $row ) {
       	$menudrop="<div class='btn-group'>
			<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
			<ul class='dropdown-menu dropdown-primary'>";
			$id_user=$_SESSION["id_usuario"];
			$admin=$_SESSION["admin"];

      // $filename='editar_contrato.php';
      // $link=permission_usr($id_user, $filename);
      // if ($link!='NOT' || $admin=='1') {
      //     $menudrop.="<li><a data-toggle='modal' href='$filename?id_contrato=".$id_contrato."' data-target='#editarModal' data-refresh='true'><i class='fa fa-edit'></i> Editar</a></li>";
      // }
      $filename='ver_cuota.php';
      $link=permission_usr($id_user, $filename);
      if ($link!='NOT' || $admin=='1') {
          $menudrop.="<li><a data-toggle='modal' href='$filename?id_contrato=".$id_contrato."' data-target='#viewModal' data-refresh='true'><i class='fa fa-search'></i> Ver Cuotas</a></li>";
      }

      $filename='clausula_contrato.php';
      $link=permission_usr($id_user, $filename);
      if ($link!='NOT' || $admin=='1') {
          $menudrop.="<li><a data-toggle='modal' href='$filename?id_contrato=".$id_contrato."' data-target='#addModal' data-refresh='true'><i class='fa fa-plus'></i> Agregar cláusula</a></li>";
      }

      $filename='contrato_pdf.php';
      $link=permission_usr($id_user, $filename);
      if ($link!='NOT' || $admin=='1') {
          $menudrop.="<li><a href='$filename?id_contrato=".$id_contrato."' target='_blanck'><i class='fa fa-print'></i> Imprimir</a></li>";
      }
			$menudrop.="</ul>
						</div>";
		return $menudrop;},
    'field' => 'id_contrato' ),

		);
	echo json_encode(
		SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
	);
?>
