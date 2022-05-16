<?php
	include ("_core.php");

	$requestData= $_REQUEST;
	$fechai= MD($_REQUEST['fechai']);
	$fechaf= MD($_REQUEST['fechaf']);

	require('ssp.customized.class.php' );
	// DB table to use
	$table = 'cotizacion';
	// Table's primary key
	$primaryKey = 'id_cotizacion';

	// MySQL server connection information
	$sql_details = array(
	'user' => $username,
	'pass' => $password,
	'db'   => $dbname,
	'host' => $hostname
	);

	$id_sucursal=$_SESSION['id_sucursal'];

	$joinQuery = "
	FROM cotizacion
	JOIN clientes  ON cotizacion.id_cliente=clientes.id_cliente
	JOIN empleados  ON cotizacion.id_vendedor=empleados.id_empleado
	";
	$extraWhere = " cotizacion.fecha BETWEEN '$fechai' AND '$fechaf' AND cotizacion.id_sucursal = '$id_sucursal'";
	$columns = array(
	array( 'db' => '`cotizacion`.`id_cotizacion`', 'dt' => 0, 'field' => 'id_cotizacion' ),
	array( 'db' => '`cotizacion`.`fecha`', 'dt' =>1,'formatter'=> function($fecha){
		return ED($fecha);
		} ,'field' => 'fecha' ),
	array( 'db' => '`clientes`.`nombre`', 'dt' => 2, 'field' => 'nombrecli', 'as' => 'nombrecli'),
	array( 'db' => '`cotizacion`.`numero_doc`', 'dt' => 3, 'field' => 'numero_doc' ),
	array( 'db' => '`empleados`.`nombre`', 'dt' => 4, 'field' => 'nombrempleado' , 'as' => 'nombrempleado'),
	array( 'db' => '`cotizacion`.`total`', 'dt' =>5, 'formatter'=> function($total){
		return number_format($total,2,".",",");
		},'field' => 'total' ),
	array( 'db' => '`cotizacion`.`impresa`', 'dt' => 6, 'formatter' => function( $impresa, $row ){
		$imp = "NO";
		if($impresa)
		{
			$imp = "SI";
		}
		return $imp; },	'field' => 'impresa'),
	array( 'db' => '`cotizacion`.`id_cotizacion`', 'dt' => 7, 'formatter' => function( $id_cotizacion, $row){
			$sql_q = _query("SELECT tipo_cotizacion, factura FROM cotizacion WHERE id_cotizacion = '$id_cotizacion'");
			$roww = _fetch_array($sql_q);
			$tipo_c = $roww["tipo_cotizacion"];
			$factura = $roww["factura"];
			$id_user=$_SESSION["id_usuario"];
			$admin=$_SESSION["admin"];
			$menudrop="<div class='btn-group'>
			<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
			<ul class='dropdown-menu dropdown-primary'>";
			$filename='editar_cotizacion.php';
			$link=permission_usr($id_user,$filename);
			if ($link!='NOT' || $admin=='1'){
				$menudrop.="<li><a  href='$filename?id_cotizacion=$id_cotizacion' ><i class='fa fa-pencil'></i> Editar</a></li>";
			}
			$filename='ver_cotizacion.php';
			$link=permission_usr($id_user,$filename);
			if ($link!='NOT' || $admin=='1' ){
				$menudrop.="<li><a data-toggle='modal' href='$filename?id_cotizacion=$id_cotizacion'  data-target='#viewModalCot' data-refresh='true'><i class='fa fa-eye'></i> Ver detalles</a></li>";
			}
			if($tipo_c == 0)
			{
				$filename='cotizacion_pdf.php';
			}
			$link=permission_usr($id_user,$filename);
			if ($link!='NOT' || $admin=='1' ){
					$menudrop.="<li><a target='_blank' href='$filename?id_cotizacion=$id_cotizacion'><i class='fa fa-print'></i> Impimir</a></li>";
			}
			if($tipo_c == 0)
			{
				$filename='cotizacion_digital.php';
			}
			$link=permission_usr($id_user,$filename);
			if ($link!='NOT' || $admin=='1' ){
					$menudrop.="<li><a target='_blank' href='$filename?id_cotizacion=$id_cotizacion'><i class='fa fa-print'></i> Impimir Digital</a></li>";
			}
			if($factura == 0)
			{
				$filename='ventas.php';
				$link=permission_usr($id_user,$filename);
				if ($link!='NOT' || $admin=='1' ){
						$menudrop.="<li><a id='facturar' class='facturar' id_cotizacion = '$id_cotizacion' ><i class='fa fa-dollar'></i> Facturar</a></li>";
				}

				$filename='borrar_cotizacion.php';
				$link=permission_usr($id_user,$filename);
				if ($link!='NOT' || $admin=='1' ){
						$menudrop.="<li><a data-toggle='modal' href='$filename?id_cotizacion=$id_cotizacion' data-target='#deleteModal' data-refresh='true'><i class='fa fa-eraser'></i> Borrar</a></li>";
				}
			}



		$menudrop.="</ul>
				</div>";
		return $menudrop;}, 'field' => 'id_cotizacion'),
	);
	echo json_encode(
		SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
	);
?>
