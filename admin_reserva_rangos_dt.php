<?php
    include("_core.php");

    $requestData= $_REQUEST;
    $fechai= $_REQUEST['fechai'];
    $fechaf= $_REQUEST['fechaf'];

    require('ssp.customized.class.php');
    // DB table to use
    $table = 'reservas';
    // Table's primary key
    $primaryKey = 'id_reserva';

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
    $links=permission_usr($id_user, $filename);

    $id_sucursal=$_SESSION['id_sucursal'];

    $joinQuery = "
	FROM  reservas  AS fac
  JOIN empleados AS usr ON (fac.id_vendedor=usr.id_empleado)
	";
    $extraWhere = "fac.id_sucursal='$id_sucursal'
	AND fac.fecha_doc BETWEEN '$fechai' AND '$fechaf'";
    $columns = array(
    array( 'db' => '`fac`.`id_reserva`', 'dt' => 0, 'field' => 'id_reserva' ),
    array( 'db' => '`fac`.`numero_doc`', 'dt' => 1, 'formatter' => function ($numero_doc, $row) {
        return intval($numero_doc);
    },
   'field' => 'numero_doc' ),
    array( 'db' => '`fac`.`nombre`', 'dt' => 2, 'field' => 'nombre' , 'as' => 'nombre'),
    array( 'db' => '`fac`.`telefono`', 'dt' => 3, 'field' => 'telefono'),
    array( 'db' => '`usr`.`nombre`', 'dt' => 4, 'field' => 'nombreuser' , 'as' => 'nombreuser' ),
    array( 'db' => '`fac`.`fecha_doc`', 'dt' =>5, 'field' => 'fecha_doc' ),
    array( 'db' => '`fac`.`total`', 'dt' =>6, 'field' => 'total' ),
    array( 'db' => '`fac`.`id_reserva`', 'dt' => 7, 'formatter' => function ($id_reserva, $row) {
        $txt_estado=estado($id_reserva);
        return $txt_estado;
    },
        'field' => 'id_reserva'),
        array( 'db' => '`fac`.`id_reserva`', 'dt' => 8, 'formatter' => function ($id_reserva, $row) {
            $menudrop="<div class='btn-group'>
						<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
						<ul class='dropdown-menu dropdown-primary'>";
            include("_core.php");
            $id_user=$_SESSION["id_usuario"];
            $id_sucursal=$_SESSION['id_sucursal'];
            $admin=$_SESSION["admin"];
            $sql="SELECT numero_doc,finalizada,anulada FROM reservas WHERE id_reserva='$id_reserva'";
            $result=_query($sql);
            $count=_num_rows($result);
            $row=_fetch_array($result);
            $anulada=$row['anulada'];
            $finalizada=$row['finalizada'];
            $numero_doc=$row['numero_doc'];
            //$alias_tipodoc=$row['alias_tipodoc'];

            $filename='anular_ventas.php';
            $link=permission_usr($id_user, $filename);
            if ($link!='NOT' || $admin=='1') {
            /*  if ($alias_tipodoc!='DEV'){
                $menudrop.="<li><a data-toggle='modal' href='$filename?id_transace=".$id_reserva."&process=formDelete"."' data-target='#deleteModal' data-refresh='true'><i class='fa fa-eraser'></i> Anular</a></li>";
              } */
            }

            $filename='editar_ventas.php';
            $link=permission_usr($id_user, $filename);
            if ($link!='NOT' || $admin=='1') {
                if ($finalizada==0 && $anulada==0 ) {
                    $menudrop.="<li><a  href='$filename?id_reserva=$id_reserva&numero_doc=$numero_doc&id_sucursal=$id_sucursal&process=formEdit' ><i class='fa fa-pencil'></i> Editar</a></li>";
                }
            }
						$filename='devolucion.php';
            $link=permission_usr($id_user, $filename);
            if ($link!='NOT' || $admin=='1') {
                if ( $anulada==0 ) {
                    $menudrop.="<li><a  href='$filename?id_reserva=$id_reserva' ><i class='fa fa-minus'></i> Devolucion</a></li>";
                }
            }
            $filename='ver_reservas.php';
            $link=permission_usr($id_user, $filename);
            if ($link!='NOT' || $admin=='1') {
                $menudrop.="<li><a data-toggle='modal' href='$filename?id_reserva=$id_reserva&numero_doc=$numero_doc&id_sucursal=$id_sucursal'  data-target='#viewModalFact' data-refresh='true'><i class='fa fa-check'></i> Ver Factura</a></li>";
            }

            $filename='admin_reservas_abonar.php';
            $link=permission_usr($id_user, $filename);
            if ($link!='NOT' || $admin=='1') {
                $menudrop.= "<li><a data-toggle='modal' href='$filename?id_reserva=$id_reserva&id_sucursal=$id_sucursal' data-target='#viewModal' data-refresh='true' ><i class='fa fa-money'></i> Abonar</a></li>";
            }

            $menudrop.="</ul>
						</div>";
            return $menudrop;
        },
        'field' => 'id_reserva' ),

    );
    echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
    );
    function estado($id_pedido)
    {
        $sql="select finalizada,anulada from reservas where id_reserva='$id_pedido'";
        $result=_query($sql);
        $count=_num_rows($result);
        $row=_fetch_array($result);
        $anulada=$row['anulada'];
        $finalizada=$row['finalizada'];
        $txt_estado="";
        if ($finalizada==1 && $anulada==0) {
            $txt_estado="<h5 class='text-mutted'>".'FINALIZADA'."</h5>";
        }

        if ($finalizada==0 && $anulada==1) {
            $txt_estado="<h5 class='text-warning'>".'NULA'."</h5>";
        }

        if ($finalizada==1 && $anulada==1) {
            $txt_estado="<h5 class='text-warning'>".'NULA'."</h5>";
        }

        if ($finalizada==0 && $anulada==0) {
            $txt_estado="<h5 class='text-danger'>".'PENDIENTE'."</h5>";
        }

        return $txt_estado;
    }
