<?php
include_once "_core.php";
// Page setup
$_PAGE = array();
$_PAGE['title'] = 'Dashboard';
$_PAGE['links'] = null;
$_PAGE['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/plugins/morris/morris-0.4.3.min.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/animate.css" rel="stylesheet">';
$_PAGE['links'] .= '<link href="css/style.css" rel="stylesheet">';


include_once "header.php";
include_once "main_menu.php";
 //permiso del script
$id_user=$_SESSION["id_usuario"];
$admin=$_SESSION["admin"];
$id_sucursal=$_SESSION["id_sucursal"];
$uri = $_SERVER['SCRIPT_NAME'];
$filename=get_name_script($uri);
$links=permission_usr($id_user,$filename);

date_default_timezone_set('America/El_Salvador');
$fecha_actual = date("Y-m-d");
$sql_apertura = _query("SELECT * FROM apertura_caja WHERE id_sucursal = '$id_sucursal' AND vigente = 1");
$cuenta = _num_rows($sql_apertura);
if($cuenta > 0)
{
    $row_a = _fetch_array($sql_apertura);
    $id_apertura = $row_a["id_apertura"];
    $fecha_ape = $row_a['fecha'];
    if($fecha_actual != $fecha_ape)
    {
        $tabla = "apertura_caja";
        $form_data = array(
            'vigente' => 0,
            'turno_vigente' => 0,
            );
        $where_up = "id_apertura='".$id_apertura."'";
        $update = _update($tabla, $form_data, $where_up);
        if($update)
        {
            $table_up = "detalle_apertura";
            $form_up = array(
                'vigente' => 0,
                );
            $where_deta = "id_apertura='".$id_apertura."' AND vigente = 1";
            $up_date = _update($table_up,$form_up, $where_deta);

        }
    }
}
//permiso del script
if ($links!='NOT' || $admin=='1' ){
    $qsucursal=_query("SELECT descripcion FROM sucursal WHERE id_sucursal='$id_sucursal'");
    $row_sucursal=_fetch_array($qsucursal);
    $sucursal=$row_sucursal["descripcion"];
?>
        <div class="row">
            <div class="col-lg-12">
            <div class="wrapper wrapper-content">
                <div class="row">
                <div class="col-lg-3">
                    <a href="admin_proveedores.php">
                    <div class="widget style1 lazur-bg">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-truck fa-4x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <span> Gestionar</span>
                                <h2 class="font-bold">Proveedor</h2>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="admin_cliente.php">
                    <div class="widget style1 navy-bg">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-briefcase fa-4x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <span>Gestionar</span>
                                <h2 class="font-bold">Clientes</h2>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-lg-3">
                     <a href="admin_productos.php">
                    <div class="widget style1 navy-bg">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-archive fa-4x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <span> Gestionar </span>
                                <h2 class="font-bold">Productos</h2>
                            </div>
                        </div>
                    </div>
                </a>
                </div>
                <div class="col-lg-3">
                    <a href="admin_stock.php">
                    <div class="widget style1 navy-bg">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-barcode fa-4x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <span> Gestionar </span>
                                <h2 class="font-bold">Inventario</h2>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
                 </div>
            <div class="row">

                <div class="col-lg-3">
                    <a href="ventas.php">
                    <div class="widget style1 yellow-bg">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-shopping-cart fa-4x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <span> Punto de Venta </span>
                                <h2 class="font-bold">Factura</h2>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>

                <div class="col-lg-3">
                    <a href="admin_caja.php">
                    <div class="widget style1 lazur-bg">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-money fa-4x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <span>Gestionar</span>
                                <h2 class="font-bold">Caja</h2>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>

                <div class="col-lg-3">
                    <a href="reporte_ventas.php">
                    <div class="widget style1 navy-bg">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-file-pdf-o fa-4x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <span>Reporte</span>
                                <h2 class="font-bold">Ventas</h2>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="admin_empleado.php">
                    <div class="widget style1 lazur-bg">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-users fa-4x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <span> Empleados</span>
                                <h2 class="font-bold">Gestionar</h2>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
                <!--<div class="col-lg-3">
                    <a href="admin_caja_chica.php">
                    <div class="widget style1 yellow-bg">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-money fa-4x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <span> Caja chica </span>
                                <h2 class="font-bold">Gestionar</h2>
                            </div>
                        </div>
                    </div>
                     </a>
                </div>-->

            </div>
             <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5 style="color:#000;">Productos mas Vendidos</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                  <i class="fa fa-chevron-up" style="color:#000;"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content" style="margin-top: 1.8px;">
                            <div>
                                <canvas id="myChart" style="width: 495px; height: 250px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
             </div>
             <div class="row">
                <div class="col-lg-6">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5 style="color:#000;">Ventas Por Mes</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                  <i class="fa fa-chevron-up" style="color:#000;"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content" style="margin-top: 1.8px;">
                            <div>
                                <canvas id="myChart1" style="width: 495px; height: 250px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
             </div>

			  </div>
		</div>
</div>
<?php
include("footer.php");
echo "<script src='js/funciones/funciones_dashboard.js'></script>";
} //permiso del script
else {
        echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
    }
?>
