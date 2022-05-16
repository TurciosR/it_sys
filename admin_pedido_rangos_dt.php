<?php
    include("_core.php");

    $requestData= $_REQUEST;
    $fechai= $_REQUEST['fechai'];
    $fechaf= $_REQUEST['fechaf'];

    require('ssp.customized.class.php');
    // DB table to use
    $table = 'pedidos';
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
    $joinQuery ="
	FROM  pedidos  AS  ped
	JOIN empleados AS usr ON (ped.id_empleado=usr.id_empleado)

  JOIN proveedores ON proveedores.id_proveedor=ped.id_proveedor
	";

    $extraWhere = "ped.id_sucursal='$id_sucursal'
	AND ped.fecha BETWEEN '$fechai' AND '$fechaf'";
    $columns = array(
    array( 'db' => '`ped`.`idtransace`', 'dt' => 0, 'field' => 'idtransace' ),
    array( 'db' => '`ped`.`fecha`', 'dt' =>1, 'field' => 'fecha' ),
    array( 'db' => '`usr`.`nombre`', 'dt' => 3, 'field' => 'nombreuser' , 'as' => 'nombreuser' ),
    array( 'db' => '`proveedores`.`nombre`', 'dt' => 2, 'field' => 'nombrepro' , 'as' => 'nombrepro' ),
    array( 'db' => '`ped`.`items`', 'dt' =>4, 'field' => 'items' ),
    array( 'db' => '`ped`.`pares`', 'dt' =>5, 'field' => 'pares' ),

    array( 'db' => '`ped`.`idtransace`', 'dt' => 6, 'formatter' => function ($id_pedido, $row) {
        $txt_estado=estado($id_pedido);
        return $txt_estado;
    },
        'field' => 'idtransace'),
        array( 'db' => '`ped`.`idtransace`', 'dt' => 7, 'formatter' => function ($id_pedido, $row) {
            $menudrop="<div class='btn-group'>
			<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
			<ul class='dropdown-menu dropdown-primary'>";

            $sql="select finalizado,anulado,verificado from pedidos where idtransace='$id_pedido'";
            $result=_query($sql);
            $count=_num_rows($result);
            $row=_fetch_array($result);
            $anulada=$row['anulado'];
            $finalizada=$row['finalizado'];
            $verificado=$row['verificado'];

            $id_user=$_SESSION["id_usuario"];
            $admin=$_SESSION["admin"];
            $id_sucursal=$_SESSION["id_sucursal"];

            $filename='anular_pedido.php';
            $link=permission_usr($id_user, $filename);
            if ($link!='NOT' || $admin=='1') {
                $menudrop.="<li><a data-toggle='modal' href='$filename?id_pedido=".$id_pedido."' data-target='#deleteModal' data-refresh='true'><i class='fa fa-eraser'></i> Anular</a></li>";
            }

            $filename='editar_pedidos.php';
            $link=permission_usr($id_user, $filename);
            if ($link!='NOT' || $admin=='1') {
                if ($finalizada==0 && $anulada==0 &&$verificado==0) {
                    $menudrop.="<li><a  href='$filename?id_pedido=$id_pedido&id_sucursal=$id_sucursal' ><i class='fa fa-pencil'></i> Editar</a></li>";
                }
            }
            /*$filename='ver_pedido.php';
            $link=permission_usr($id_user, $filename);
            if ($link!='NOT' || $admin=='1') {
                $menudrop.="<li><a data-toggle='modal' href='$filename?id_pedido=$id_pedido&id_sucursal=$id_sucursal'  data-target='#viewModalpedt' data-refresh='true'><i class='fa fa-check'></i> Ver Preingreso</a></li>";
            }*/
            //Reimprimir pedido
            /*$filename='reimprimir_pedido.php';
            $link=permission_usr($id_user, $filename);
            if ($link!='NOT' || $admin=='1') {
                if ($finalizada==1) {
                    $menudrop.="<li><a data-toggle='modal' href='$filename?id_pedido=".$id_pedido."' data-target='#viewModal' data-refresh='true'><i class='fa fa-print'></i> Reimprimir</a></li>";
                }
            }*/
            //verificar pedido
            $filename='verificar_pedido.php';
            $link=permission_usr($id_user, $filename);
            if ($link!='NOT' || $admin=='1') {
                if ($finalizada==0 && $anulada==0 && $verificado==0) {
                    $menudrop.="<li><a  href='$filename?id_pedido=$id_pedido&id_sucursal=$id_sucursal' ><i class='fa fa-check'></i> Verificar</a></li>";
                }
            }
            $filename='ver_reporte_preingreso.php';
            $link=permission_usr($id_user, $filename);
            if ($link!='NOT' || $admin=='1') {
                if ($anulada==0) {
                    $menudrop.="<li><a  href='$filename?idtransace=$id_pedido' target='_blank' ><i class='fa fa-check'></i> Reporte Preingreso</a></li>";
                }
            }
            $filename='print_bcode_preingreso.php';
            $link=permission_usr($id_user, $filename);
            if ($link!='NOT' || $admin=='1') {
                $menudrop.="<li><a data-toggle='modal' href='$filename?id_compras=$id_pedido'&id_sucursal=$id_sucursal  data-target='#viewModalFact' data-refresh='true'><i class=\"fa fa-print\"></i> Imprimir Barcodes</a></li>";
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
        $sql="select finalizado,anulado,verificado from pedidos where idtransace='$id_pedido'";
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
