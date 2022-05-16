<?php
    include("_core.php");

    $requestData= $_REQUEST;
    $fechai= $_REQUEST['fechai'];
    $fechaf= $_REQUEST['fechaf'];

    require('ssp.customized.class.php');
    // DB table to use
    $table = 'traslado';
    // Table's primary key
    $primaryKey = 'idtransace';

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
    $joinQuery ="FROM traslado JOIN empleados ON traslado.id_empleado=empleados.id_empleado JOIN sucursal ON traslado.id_sucursal_origen=sucursal.id_sucursal  LEFT JOIN empleados AS rcv ON traslado.id_recibe=rcv.id_empleado ";


    $extraWhere = "traslado.id_sucursal_destino='$id_sucursal' AND traslado.anulado!=1
	AND traslado.fecha BETWEEN '$fechai' AND '$fechaf'";
    $columns = array(
    array( 'db' => '`traslado`.`idtransace`', 'dt' => 0, 'field' => 'idtransace' ),
    array( 'db' => '`sucursal`.`descripcion`', 'dt' =>1, 'field' => 'descripcion' ),
    array( 'db' => '`empleados`.`nombre`', 'dt' => 2, 'field' => 'nombreuser' , 'as' => 'nombreuser' ),
    array( 'db' => '`traslado`.`fecha`', 'dt' =>3, 'field' => 'fecha' ),
    array( 'db' => '`traslado`.`items`', 'dt' =>4, 'field' => 'items' ),
    array( 'db' => '`traslado`.`pares`', 'dt' =>5, 'field' => 'pares' ),

    array( 'db' => '`traslado`.`idtransace`', 'dt' => 6, 'formatter' => function ($id_pedido, $row) {
        $txt_estado=estado($id_pedido);
        return $txt_estado;
    },
        'field' => 'idtransace'),
        array( 'db' => '`rcv`.`nombre`', 'dt' =>7, 'field' => 'nombre' ),
        array( 'db' => '`traslado`.`idtransace`', 'dt' => 8, 'formatter' => function ($id_pedido, $row) {
            $menudrop="<div class='btn-group'>
      			<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
      			<ul class='dropdown-menu dropdown-primary'>";
            $sql="select idtransace,finalizado,anulado,verificado from traslado where idtransace='$id_pedido'";
            $result=_query($sql);
            $count=_num_rows($result);
            $row=_fetch_array($result);
            $anulada=$row['anulado'];
            $finalizada=$row['finalizado'];
            $verificado=$row['verificado'];

            $id_user=$_SESSION["id_usuario"];
            $admin=$_SESSION["admin"];
            $id_sucursal=$_SESSION["id_sucursal"];

            $filename='ver_reporte_ficha_traslado.php';
            $link=permission_usr($id_user, $filename);
            if ($link!='NOT' || $admin=='1') {
                $menudrop.= "<li><a href='$filename?id_traslado=$row[idtransace]&id_sucursal=$id_sucursal' target='_blank' ><i class='fa fa-search'></i> Ver Ficha Traslado</a></li>";
            }

            $filename='detalle_traslado.php';
            $link=permission_usr($id_user, $filename);
            if ($link!='NOT' || $admin=='1') {
                $menudrop.= "<li><a data-toggle='modal' href='$filename?id_traslado=$row[idtransace]&id_sucursal=$id_sucursal' data-target='#viewModal' data-refresh='true' ><i class='fa fa-search'></i> Ver Detalle</a></li>";
            }

            if ($finalizada==1) {
                # code...
                $filename='ver_reporte_traslados.php';
                $link=permission_usr($id_user, $filename);
                if ($link!='NOT' || $admin=='1') {
                    $menudrop.= "<li><a href='$filename?id_traslado=".$row ['idtransace']."&id_sucursal=$id_sucursal' target='_blank' ><i class=\"fa fa-search\"></i> Ver Detalle Ingreso</a></li>";
                }
            }



            if ($verificado==0) {
                $filename='verificar_traslado.php';
                $link=permission_usr($id_user, $filename);
                if ($link!='NOT' || $admin=='1') {
                    $menudrop.= "<li><a href='verificar_traslado.php?id_traslado=" .  $row ['idtransace'].""."'><i class=\"fa fa-check\"></i> Verificar Traslado</a></li>";
                }
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
    function estado($id_pedido)
    {
        $sql="select finalizado,anulado,verificado from traslado where idtransace='$id_pedido'";
        $result=_query($sql);
        $count=_num_rows($result);
        $row=_fetch_array($result);
        $anulada=$row['anulado'];
        $finalizada=$row['finalizado'];
        $verificado=$row['verificado'];
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
        if ($finalizada==0 && $anulada==0 && $verificado==1) {
            $txt_estado="<h5 class='text-mutted'>".'VERIFICADO'."</h5>";
        }

        return $txt_estado;
    }
