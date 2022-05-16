<?php
	include ("_core.php");

	$requestData= $_REQUEST;
	$fechai= $_REQUEST['fechai'];
	$fechaf= $_REQUEST['fechaf'];

	require('ssp.customized.class.php' );
	// DB table to use
	$table = 'compra';
	// Table's primary key
	$primaryKey = 'id_compra';

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
	FROM  compra  AS com
	JOIN proveedores AS pro ON (com.id_proveedor = pro.id_proveedor)
	JOIN empleados AS usr ON (com.id_empleado=usr.id_empleado)
	";
	/*
	$extraWhere = "com.id_sucursal='$id_sucursal'
	AND com.fechadoc BETWEEN '$fechai' AND '$fechaf'";
*/
	$extraWhere = "";
	$columns = array(
	array( 'db' => '`com`.`id_compra`', 'dt' => 0, 'field' => 'id_compra' ),
	array( 'db' => '`com`.`alias_tipodoc`', 'dt' => 1, 'field' => 'alias_tipodoc' ),
	array( 'db' => '`com`.`numero_doc`', 'dt' => 2, 'field' => 'numero_doc' ),
	array( 'db' => '`pro`.`nombre`', 'dt' => 3, 'field' => 'nombreprov' , 'as' => 'nombreprov'),
	array( 'db' => '`usr`.`nombre`', 'dt' => 4, 'field' => 'nombreuser' , 'as' => 'nombreuser' ),
	array( 'db' => '`com`.`total`', 'dt' =>5, 'field' => 'total' ),
	array( 'db' => '`com`.`fecha`', 'dt' =>6, 'field' => 'fecha' ),
	array( 'db' => '`com`.`id_compra`', 'dt' => 7, 'formatter' => function( $id_compras, $row ){
		$txt_estado=estado($id_compras);
		return $txt_estado;
		},'field' => 'id_compra'),
	array( 'db' => '`com`.`id_compra`', 'dt' => 8, 'formatter' => function( $id_compras, $row ){
		$menudrop="<div class='btn-group'>
			<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
			<ul class='dropdown-menu dropdown-primary'>";

			$sql="select finalizada,anulada from compra where id_compra='$id_compras'";
			$result=_query($sql);
			$count=_num_rows($result);
			$row=_fetch_array($result);
			$anulada=$row['anulada'];
			$finalizada=$row['finalizada'];
			$id_user=$_SESSION["id_usuario"];
			$id_sucursal=$_SESSION["id_sucursal"];
			$admin=$_SESSION["admin"];
			$filename='anular_compras.php';
			$link=permission_usr($id_user,$filename);
			if ($link!='NOT' || $admin=='1' )
				$menudrop.="<li><a data-toggle='modal' href='$filename?id_compras=" .$id_compras."&process=formDelete"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i> Anular</a></li>";

			$filename='editar_compras.php';
			$link=permission_usr($id_user,$filename);

			if ($link!='NOT' || $admin=='1'){
				//if($finalizada==0 && $anulada==0){
					$menudrop.="<li><a  href='$filename?id_compras=$id_compras' ><i class=\"fa fa-pencil\"></i> Editar</a></li>";
			//	}
			}
			/*
			$filename='ver_compras.php';
			$link=permission_usr($id_user,$filename);
			if ($link!='NOT' || $admin=='1' )
				$menudrop.="<li><a data-toggle='modal' href='$filename?id_compras=$id_compras&id_sucursal=$id_sucursal'  data-target='#viewModalFact' data-refresh='true'><i class=\"fa fa-check\"></i> Ver Factura</a></li>";
				*/
				$filename='print_bcode_compras.php';
				$link=permission_usr($id_user,$filename);
			if ($link!='NOT' || $admin=='1' )
					$menudrop.="<li><a data-toggle='modal' href='$filename?id_compras=$id_compras&id_sucursal=$id_sucursal'  data-target='#viewModalFact' data-refresh='true'><i class=\"fa fa-print\"></i> Imprimir Barcodes</a></li>";

			//Reimprimir factura
			/*
			$filename='print_bcode_compras.php';
			$link=permission_usr($id_user,$filename);
			if ($link!='NOT' || $admin=='1' ){
				if($finalizada==1)
					$menudrop.="<li><a data-toggle='modal' href='$filename?id_compras=$id_compras&id_sucursal=$id_sucursal' data-target='#viewModalFact' data-refresh='true'><i class=\"fa fa-print\"></i> Reimprimir</a></li>";
			}
			*/
			$menudrop.="</ul>
						</div>";
		return $menudrop;},
		'field' => 'id_compra' ),

	);
	echo json_encode(
		SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
	);
function estado($id_compras){
	$id_sucursal=$_SESSION["id_sucursal"];
	$sql="select finalizada,anulada from compra where id_compra='$id_compras'";
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
