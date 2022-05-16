<?php
	include ("_core.php");

	$requestData= $_REQUEST;
	$fechai= $_REQUEST['fechai'];
	$fechaf= $_REQUEST['fechaf'];
	$id_cuenta= $_REQUEST['id_cuenta'];

	require('ssp.customized.class.php' );
	// DB table to use
	$table = 'mov_cta_banco';
	// Table's primary key
	$primaryKey = 'id_movimiento';

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

	$joinQuery = "";
	//FROM  cxp
	//JOIN proveedores AS pro ON (cxp.id_proveedor = pro.id_proveedor)
	//";
	$extraWhere = "mov_cta_banco.id_cuenta='$id_cuenta' AND mov_cta_banco.fecha BETWEEN '$fechai' AND '$fechaf'";
	//$extraWhere = "";
	$columns = array(
	array( 'db' => 'id_movimiento', 'dt' => 0, 'field' => 'id_movimiento' ),
	array( 'db' => 'fecha', 'dt' =>1, 'field' => 'fecha' ),
	array( 'db' => 'responsable', 'dt' => 2, 'field' => 'responsable'),
	array( 'db' => 'concepto', 'dt' => 3, 'field' => 'concepto'),
	array( 'db' => 'alias_tipodoc', 'dt' => 4, 'field' => 'alias_tipodoc' ),
	array( 'db' => 'numero_doc', 'dt' => 5, 'field' => 'numero_doc' ),
	array( 'db' => 'entrada', 'dt' =>6, 'field' => 'entrada' ),
	array( 'db' => 'salida', 'dt' =>7, 'field' => 'salida' ),
	array( 'db' => 'saldo', 'dt' =>8, 'field' => 'saldo' ),
	array( 'db' => 'id_movimiento', 'dt' => 9, 'formatter' => function( $id_movimiento, $row ){
			if(ultimo($id_movimiento))
			{
				$menudrop="<div class='btn-group'>
				<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
				<ul class='dropdown-menu dropdown-primary'>";
				$sqla=_fetch_array(_query("SELECT * FROM mov_cta_banco WHERE id_movimiento='$id_movimiento'"));
				$alias_tipodoc=$sqla['alias_tipodoc'];

				$id_user=$_SESSION["id_usuario"];
				$id_sucursal=$_SESSION["id_sucursal"];
				$admin=$_SESSION["admin"];

					if ($alias_tipodoc=='VOC') {
						# code...
						$a=0;
						$sql = _query("SELECT cxp.saldo_pend FROM mov_cta_banco JOIN facturas_mov ON facturas_mov.id_movimiento=mov_cta_banco.id_movimiento JOIN cxp ON cxp.idtransace=facturas_mov.idtransace WHERE mov_cta_banco.id_movimiento = $id_movimiento");
						while ($row=_fetch_array($sql)) {
							# code...
							if ($row['saldo_pend']>0) {
								# code...
								$a=1;
							}
						}

						if ($a==1) {
							# code...
							$filename='borrar_mov_cta_banco.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' )
									$menudrop.="<li><a data-toggle='modal' href='$filename?id_movimiento=$id_movimiento&id_sucursal=$id_sucursal'  data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-trash\"></i> Eliminar</a></li>";


							$filename='editar_pago_proveedores.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' )
							{
								$menudrop.="<li><a  href='$filename?id_movimiento=$id_movimiento&id_cuenta=$sqla[id_cuenta]'><i class=\"fa fa-pencil\"></i> Editar</a></li>";
							}

							$filename='finalizar_mov_cta_banco.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' )
							{
								$menudrop.="<li><a data-toggle='modal' href='$filename?id_movimiento=$id_movimiento'  data-target='#viewModal' data-refresh='true'><i class=\"fa fa-check\"></i> Finalizar</a></li>";
							}
						}
						else {
							# code...
							$filename='ver_voucher2.php';
							$link=permission_usr($id_user,$filename);
							if ($link!='NOT' || $admin=='1' )
							{
								$menudrop.="<li><a href=\"ver_voucher2.php?id_movimiento=$id_movimiento"."\" target='_blank'><i class=\"fa fa-print\"></i> Imprimir</a></li>";
							}
						}

					}
					else {
						# code...
						$filename='borrar_mov_cta_banco.php';
						$link=permission_usr($id_user,$filename);
						if ($link!='NOT' || $admin=='1' )
								$menudrop.="<li><a data-toggle='modal' href='$filename?id_movimiento=$id_movimiento&id_sucursal=$id_sucursal'  data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-trash\"></i> Eliminar</a></li>";

						$filename='editar_mov_cta_banco.php';
						$link=permission_usr($id_user,$filename);
						if ($link!='NOT' || $admin=='1' )
						{
							$menudrop.="<li><a data-toggle='modal' href='$filename?id_movimiento=$id_movimiento'  data-target='#viewModal' data-refresh='true'><i class=\"fa fa-pencil\"></i> Editar</a></li>";
						}
					}



			//Reimprimir factura
			/*
			$filename='print_bcode_compras.php';
			$link=permission_usr($id_user,$filename);
			if ($link!='NOT' || $admin=='1' ){
				if($finalizada==1)
					$menudrop.="<li><a data-toggle='modal' href='$filename?id_movimiento=$id_movimiento&id_sucursal=$id_sucursal' data-target='#viewModalFact' data-refresh='true'><i class=\"fa fa-print\"></i> Reimprimir</a></li>";
			}
			*/
			$menudrop.="</ul>
						</div>";
		return $menudrop;}
	},
		'field' => 'id_movimiento' ),

	);
	echo json_encode(
		SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
	);
	function ultimo($id)
	{
		$sql = _query("SELECT * FROM mov_cta_banco WHERE id_movimiento>'$id'");
		if(_num_rows($sql)>0)
		{
			return 0;
		}
		else
		{
			return 1;
		}
	}
?>
