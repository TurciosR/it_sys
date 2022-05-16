<?php
    include("_core.php");

    $requestData= $_REQUEST;
    $fechai= $_REQUEST['fechai'];
    $fechaf= $_REQUEST['fechaf'];

    require('ssp.customized.class.php');
    // DB table to use
    $table = 'garantia_cliente';
    // Table's primary key
    $primaryKey = 'id_garantia';

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
	   FROM  garantia_cliente  AS gc
     JOIN empleados AS usr ON (gc.id_empleado=usr.id_empleado)
     JOIN clientes AS cl ON (gc.id_cliente=cl.id_cliente)
	    ";
    $extraWhere = "gc.id_sucursal='$id_sucursal'
	   AND gc.fecha BETWEEN '$fechai' AND '$fechaf'";
    $columns = array(
    array( 'db' => '`gc`.`id_garantia`', 'dt' => 0, 'field' => 'id_garantia' ),
    array( 'db' => '`gc`.`numero_doc`', 'dt' => 1, 'field' => 'numero_doc' ),
    array( 'db' => '`gc`.`alias_tipodoc`', 'dt' => 2, 'field' => 'alias_tipodoc' ),
    array( 'db' => '`gc`.`fecha`', 'dt' =>3, 'field' => 'fecha' ),
      array( 'db' => '`usr`.`nombre`', 'dt' => 4, 'field' => 'nombreuser' , 'as' => 'nombreuser' ),
    array( 'db' => '`cl`.`nombre`', 'dt' => 5, 'field' => 'nombre'),

    array( 'db' => '`gc`.`id_garantia`', 'dt' => 6, 'formatter' => function ($id_garantia, $row) {
      $id_garantia=$row['id_garantia'];
        $txt_estado=estado($id_garantia);
        return $txt_estado;
    },
        'field' => 'id_garantia'),
        array( 'db' => '`gc`.`id_garantia`', 'dt' => 7, 'formatter' => function ($id_garantia, $row) {
            $menudrop="<div class='btn-group'>
						<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
						<ul class='dropdown-menu dropdown-primary'>";
            include("_core.php");
            $id_user=$_SESSION["id_usuario"];
            $id_sucursal=$_SESSION['id_sucursal'];
            $admin=$_SESSION["admin"];
            $id_garantia=$row['id_garantia'];
            $sql="SELECT numero_doc,finalizada,anulada,alias_tipodoc FROM garantia_cliente WHERE id_garantia='$id_garantia'";
            $result=_query($sql);
            $count=_num_rows($result);
            $row=_fetch_array($result);
            $anulada=$row['anulada'];
            $finalizada=$row['finalizada'];
            $numero_doc=$row['numero_doc'];
            $alias_tipodoc=$row['alias_tipodoc'];

            $filename='editar_politicas.php';
            $link=permission_usr($id_user, $filename);
            if ($link!='NOT' || $admin=='1') {
                $menudrop.="<li><a data-toggle='modal' href='$filename?id_garantia=$id_garantia' data-target='#editModal' data-refresh='true'><i class='fa fa-pencil'></i> Editar Politicas</a></li>";
            }

            $filename='garantia_pdf.php';
            $link=permission_usr($id_user, $filename);
            if ($link!='NOT' || $admin=='1') {
                $menudrop.="<li><a href='$filename?id_garantia=$id_garantia' target='_blanck'><i class='fa fa-print'></i> Imprimir</a></li>";
            }

            $menudrop.="</ul>
						</div>";
            return $menudrop;
        },
        'field' => 'id_garantia' ),

    );
    echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
    );
    function estado($id_garantia)
    {
        $sql="select finalizada,anulada from garantia_cliente where id_garantia='$id_garantia'";
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
            $txt_estado="<h5 class='text-warning'>".'VIGENTE'."</h5>";
        }

        return $txt_estado;
    }
