<?php
    include("_core.php");

    $requestData= $_REQUEST;
    $fechai= $_REQUEST['fechai'];
    $fechaf= $_REQUEST['fechaf'];

    require('ssp.customized.class.php');
    // DB table to use
    $table = 'factura';
    // Table's primary key
    $primaryKey = 'idtransace';

    // MySQL server connection information
    $sql_details = array(
          'user' => $username,
          'pass' => $password,
          'db'   => $dbname,
          'host' => $hostname
    );
    /*SELECT factura.fecha, CONCAT(cliente.nombre,' ',cliente.apellido) AS nombre, factura.numero_doc,factura.total,factura.abono,factura.saldo FROM factura JOIN cliente ON cliente.id_cliente=factura.id_cliente WHERE factura.credito=1 */
    //permiso del script
    $id_user=$_SESSION["id_usuario"];
    $admin=$_SESSION["admin"];
    $uri = $_SERVER['SCRIPT_NAME'];
    $filename=get_name_script($uri);
    $links=permission_usr($id_user, $filename);

    $id_sucursal=$_SESSION['id_sucursal'];
    $joinQuery =" ,clientes.nombre FROM factura JOIN clientes ON clientes.id_cliente=factura.id_cliente";

    $extraWhere = " factura.credito=1 AND factura.fecha_doc BETWEEN '$fechai' AND '$fechaf' ";/*	AND factura.fecha BETWEEN '$fechai' AND '$fechaf' */
    $columns = array(
    array( 'db' => '`factura`.`idtransace`', 'dt' => 0, 'field' => 'idtransace' ),
    array( 'db' => '`factura`.`fecha_doc`', 'dt' =>1, 'field' => 'fecha_doc' ),
    array( 'db' => 'nombre', 'dt' => 2, 'field' => 'nombre'),
    array( 'db' => '`factura`.`alias_tipodoc`', 'dt' =>3, 'field' => 'alias_tipodoc' ),
    array( 'db' => '`factura`.`numero_doc`', 'dt' =>4, 'field' => 'numero_doc' ),
    array( 'db' => '`factura`.`total`', 'dt' =>5, 'field' => 'total' ),
    array( 'db' => '`factura`.`abono`', 'dt' =>6, 'field' => 'abono' ),
    array( 'db' => '`factura`.`saldo`', 'dt' =>7, 'field' => 'saldo' ),
    array( 'db' => '`factura`.`idtransace`', 'dt' => 8, 'formatter' => function ($idtransace, $row) {
        $txt_estado=estado($idtransace);
        return $txt_estado;
    },
        'field' => 'idtransace'),
        array( 'db' => '`factura`.`idtransace`', 'dt' => 9, 'formatter' => function ($idtransace, $row) {
            $menudrop="<div class='btn-group'>
      			<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
      			<ul class='dropdown-menu dropdown-primary'>";
            $id_user=$_SESSION["id_usuario"];
            $admin=$_SESSION["admin"];
            $id_sucursal=$_SESSION["id_sucursal"];

            $filename='abono_credito.php';
            $link=permission_usr($id_user, $filename);
            if ($link!='NOT' || $admin=='1') {
                $menudrop.= "<li><a data-toggle='modal' href='$filename?idtransace=$idtransace&id_sucursal=$id_sucursal' data-target='#viewModal' data-refresh='true' ><i class='fa fa-money'></i> Abonar</a></li>";
            }

            $menudrop.="</ul>
						</div>";
            return $menudrop;
        },
        'field' => 'idtransace' ),

    );
    echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
    );
function estado($id_factura)
{
  $factura=$id_factura;
  $sql = _fetch_array(_query("SELECT factura.fecha_doc, clientes.nombre, factura.numero_doc,factura.total,factura.abono,factura.saldo FROM factura JOIN clientes ON clientes.id_cliente=factura.id_cliente WHERE factura.credito=1 AND factura.idtransace=$factura"));
  $abono=$sql['abono'];
  $saldo=$sql['saldo'];
  $total=$sql['total'];
  $txt_estado="";
  if ($saldo>0&&$abono<$total) {
    # code...
    $txt_estado="<h5 class='text-danger'>".'PENDIENTE'."</h5>";
  }
  else {
    $txt_estado="<h5 class='text-mutted'>".'FINALIZADA'."</h5>";
  }

  return $txt_estado;
}
