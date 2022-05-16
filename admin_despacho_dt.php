<?php
	include ("_core.php");

	$requestData= $_REQUEST;
	$fechai= $_REQUEST['fechai'];
	$fechaf= $_REQUEST['fechaf'];

	require('ssp.customized.class.php' );
	// DB table to use
	$table = 'despacho';
	// Table's primary key
	$primaryKey = 'id_despacho';

	// MySQL server connection information
	$sql_details = array(
  'user' => $username,
  'pass' => $password,
  'db'   => $dbname,
  'host' => $hostname
  );
  /*
	<th>Id factura</th>
	<th>Tipo Doc</th>
	<th>Numero Doc</th>
	<th>Proveedor</th>
	<th>Empleado</th>
	<th>Total</th>
	<th>Fecha Doc</th>
	<th>Fecha Vence</th>
	<th>Estado</th>
	*/
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

	$id_sucursal=$_SESSION['id_sucursal'];

	$joinQuery = "
	FROM  despacho  AS des
	JOIN factura AS fat ON (des.id_factura = fat.id_factura)
	JOIN clientes AS cli ON (fat.id_cliente = cli.id_cliente)
	JOIN empleados AS usr ON (des.responsable=usr.id_empleado)
	";
	/*
	$extraWhere = "com.id_sucursal='$id_sucursal'
	AND com.fechadoc BETWEEN '$fechai' AND '$fechaf'";
*/
	$extraWhere = "fat.finalizada=1 AND fat.entregado=1";
	$columns = array(
	array( 'db' => '`des`.`id_despacho`', 'dt' => 0, 'field' => 'id_despacho' ),
	array( 'db' => '`cli`.`nombre`', 'dt' => 1, 'field' => 'nombre' ),
	array( 'db' => '`fat`.`numero_doc`', 'dt' => 2, 'field' => 'numero_doc' ),
	array( 'db' => '`usr`.`nombre`', 'dt' => 3, 'field' => 'nombreuser' , 'as' => 'nombreuser' ),
	array( 'db' => '`des`.`fecha_des`', 'dt' => 4, 'field' => 'fecha_des'),
	array( 'db' => '`fat`.`total`', 'dt' =>5, 'field' => 'total' ),
//	array( 'db' => '`com`.`fecha`', 'dt' =>6, 'field' => 'fecha' ),
	array( 'db' => '`des`.`id_despacho`', 'dt' => 6, 'formatter' => function( $id_despacho, $row ){
		$txt_estado=estado($id_despacho);
		return $txt_estado;
		},'field' => 'id_despacho'),
	array( 'db' => '`des`.`id_despacho`', 'dt' => 7, 'formatter' => function( $id_despacho, $row ){
		$menudrop="<div class='btn-group'>
			<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
			<ul class='dropdown-menu dropdown-primary'>";

			$sql="select fat.finalizada,fat.anulada from factura as fat,despacho as des where fat.id_factura=des.id_factura and des.id_despacho='$id_despacho'";
			$result=_query($sql);
			$count=_num_rows($result);
			$row=_fetch_array($result);
			$anulada=$row['anulada'];
			$finalizada=$row['finalizada'];
			$id_user=$_SESSION["id_usuario"];
			$id_sucursal=$_SESSION["id_sucursal"];
			$admin=$_SESSION["admin"];
			$filename='ver_despacho.php';
			$link=permission_usr($id_user,$filename);
			if ($link!='NOT' || $admin=='1' )
				$menudrop.="<li><a data-toggle='modal' href='$filename?id_despacho=" .$id_despacho."&process=formDelete"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eye\"></i> Ver</a></li>";
/*
			$filename='editar_compras.php';
			$link=permission_usr($id_user,$filename);

			if ($link!='NOT' || $admin=='1'){
				//if($finalizada==0 && $anulada==0){
					$menudrop.="<li><a  href='$filename?id_despacho=$id_despacho' ><i class=\"fa fa-pencil\"></i> Editar</a></li>";
			//	}
			}*/

			$menudrop.="</ul>
						</div>";
		return $menudrop;},
		'field' => 'id_despacho' ),

	);
	echo json_encode(
		SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
	);
function estado($id_despacho){
	$id_sucursal=$_SESSION["id_sucursal"];
	$sql="select fat.finalizada,fat.anulada from factura as fat,despacho as des where fat.id_factura=des.id_factura and des.id_despacho='$id_despacho'";
	$result=_query($sql);
	$count=_num_rows($result);
	$row=_fetch_array($result);
	$anulada=$row['anulada'];
	$finalizada=$row['finalizada'];
	$txt_estado="";
	if($finalizada==1 && $anulada==0)
		$txt_estado="<h5 class='text-mutted'>".'FINALIZADA'."</h5>";

	if($finalizada==0 && $anulada==1 )
		$txt_estado="<h5 class='text-warning'>".'NULA'."</h5>";

	if($finalizada==1 && $anulada==1 )
		$txt_estado="<h5 class='text-warning'>".'NULA'."</h5>";

	if($finalizada==0 && $anulada==0)
		$txt_estado="<h5 class='text-danger'>".'PENDIENTE'."</h5>";

		return $txt_estado;
}
?>
