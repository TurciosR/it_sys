<?php
include("_core.php");
 $sql_empresa=_query("SELECT * FROM empresa ");
    $array_empresa=_fetch_array($sql_empresa);
    $logo_empresa=$array_empresa['logo'];
?>
  <nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
      <ul class="nav" id="side-menu">
        <li class="nav-header">
          <div class="dropdown profile-element"> <span>
                              <img alt="image" class="img-responsive" src="<?php echo $logo_empresa;?>" width="200px">
                             </span>


          </div>
          <div class="logo-element">
            PB
          </div>
        </li>
        <!--li-->
        <!--a href="index.html"><i class="fa fa-archive"></i> <span class="nav-label">Productos</span> <span class="fa arrow"></span></a-->
        <?php
                        //&& $active=='t'
                        include_once '_core.php';
                           $id_user=$_SESSION["id_usuario"];
                        $admin=$_SESSION["admin"];
                        $icono='fa fa-star-o';
                        $sql_menus="SELECT id_menu, nombre, prioridad,icono FROM menu order by prioridad";
                        $result=_query($sql_menus);
                        $numrows=_num_rows($result);
                        $main_lnk='dashboard.php';
                        if ($admin) {
                            echo  "<li class='active'>";
                            echo "<a href='dashboard.php'><i class='".$icono."'></i> <span class='nav-label'>Inicio</span></a>";
                            echo  "</li>";
                        } else {
                            echo  "<li class='active'>";
                            echo "<a href='ventas.php'><i class='".$icono."'></i> <span class='nav-label'>Inicio</span></a>";
                            echo  "</li>";
                        }
                        for ($i=0;$i<$numrows;$i++) {
                            $row=_fetch_array($result);
                            $menuname=$row['nombre'];
                            $id_menu=$row['id_menu'];
                            $icono=$row['icono'];


                            if ($admin) {
                                $sql_links="SELECT distinct menu.id_menu, menu.nombre as nombremenu, menu.prioridad,
									modulo.id_modulo, modulo.nombre as nombremodulo, modulo.descripcion, modulo.filename, empleados.admin
									FROM menu, modulo, empleados
									WHERE empleados.id_empleado='$id_user'
									AND empleados.admin='1'
									AND menu.id_menu='$id_menu'
									AND menu.id_menu=modulo.id_menu
									AND modulo.mostrarmenu='1'
								";
                            } else {
                                $sql_links="
									SELECT menu.id_menu, menu.nombre as nombremenu, menu.prioridad,
									modulo.id_modulo,  modulo.nombre as nombremodulo, modulo.descripcion, modulo.filename,
									usuario_modulo.id_usuario,empleados.admin
									FROM menu, modulo, usuario_modulo, empleados
									WHERE empleados.id_empleado='$id_user'
									AND menu.id_menu='$id_menu'
									AND empleados.id_empleado=usuario_modulo.id_usuario
									AND usuario_modulo.id_modulo=modulo.id_modulo
									AND menu.id_menu=modulo.id_menu
									AND modulo.mostrarmenu='1'
									";
                            }
                            $result_modules=_query($sql_links);
                            $numrow2=_num_rows($result_modules);
                            if ($numrow2>0) {
                                echo "<li><a href='".$main_lnk."'><i class='".$icono."'></i></i> <span class='nav-label'>".$menuname."</span> <span class='fa arrow'></span></a>";
                                echo " <ul class='nav nav-second-level'>";
                                for ($j=0;$j<$numrow2;$j++) {
                                    $row_modules=_fetch_array($result_modules);
                                    $lnk=strtolower($row_modules['filename']);
                                    $modulo=$row_modules['nombremodulo'];
                                    $id_modulo=$row_modules['id_modulo'];
                                    echo "<li><a href='".$lnk."'>".ucfirst($modulo)."</a></li>";
                                }
                                echo"</ul>";
                                echo" </li>";
                            }
                        }

                            /*
                            $sql_list_modules="SELECT DISTINCT ON(menu.nombre,menu.prioridad) menu.nombre as menuname,
                            module.name,module.filename,menu.priority,menu.main_menu
                            FROM module, menu
                            WHERE menu.id_menu=module.id_menu
                            AND admin!='t'
                            AND filename ILIKE '%.list.%' ORDER BY menu.priority";
                            $result=_query($sql_list);
                            $numrows=_num_rows($result);
                            for($i=0;$i<$numrows;$i++){
                                $row=_fetch_array($result,$i);
                                $lnk=strtolower($row['filename']);
                                $menuname=$row['menuname'];
                                $main_menu=$row['main_menu'];
                                echo "<li><a href='".$lnk."?mainmenu=".$main_menu."'>".$menuname."</a></li>";
                            }
                            */
                       //	}
                        ?>


          <!--ul class="nav nav-second-level">
                            <li class="active"><a href="admin_categoria.php">Categorias</a></li>
                            <li ><a href="admin_producto.php">Productos</a></li>

                        </ul>
                    </li>
                    <li>
                         <a href="admin_servicios.php"><i class="fa fa-ticket"></i> <span class="nav-label">Servicios</span></a>
                    </li>
                    <li>
                         <a href="admin_cliente.php"><i class="fa fa-briefcase"></i> <span class="nav-label">Clientes</span></a>
                    </li>

                    <li>
                        <a href="index.html"><i class="fa fa-shopping-cart"></i> <span class="nav-label">Facturación</span> <span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li class="active"><a href="facturacion.php">Facturar</a></li>
                            <li><a href="corte_caja_diario.php">Corte de Caja</a></li>
                            <li><a href="admin_factura.php">Gestionar Factura</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="admin_proveedores.php"><i class="fa fa-truck"></i> <span class="nav-label">Proveedores</span></a>
                    </li>

                     <li>
                        <a href="#"><i class="fa fa-users"></i> <span class="nav-label">Empleados</span> <span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li class="active"><a href="admin_tipo_empleado.php">Tipo Empleado</a></li>
                            <li ><a href="admin_empleado.php">Empleado</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-list"></i> <span class="nav-label">Inventario</span> <span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li class="active"><a href="inventario_inicial.php">Cargar Inventario</a></li>
                            <li ><a href="admin_stock.php">Consultar Stock</a></li>
                            <li ><a href="otras_salidas.php">Registro de salidas</a></li>
                            <li ><a href="ajuste_inventario.php">Ajuste de Inventario</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#"><i class="fa fa-money"></i> <span class="nav-label">Caja Chica</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="admin_caja_chica.php">Gestionar caja chica</a></li>

                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-file-text-o"></i> <span class="nav-label">Reportes</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="reporte_servicios.php">Reporte de Servicios</a></li>
                            <li><a href="ver_reporte_stock.php">Reporte de Inventario</a></li>
                            <li><a href="admin_ajustes.php">Reporte ajuste de Inventario</a></li>
                            <li><a href="reporte_ventas.php">Reporte de ventas</a></li>
                            <li><a href="reporte_caja_chica.php">Reporte caja chica</a></li>
                             <li><a href="reporte_cortecaja.php">Reporte corte de caja</a></li>


                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-gear"></i> <span class="nav-label">Utilidades</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <li><a href="admin_empresa.php">Gestionar Empresa</a></li>
                                <li><a href="admin_sucursal.php">Sucursales</a></li>
                                <li> <a href="admin_user.php"><span class="nav-label">Usuarios</span></a></li>
								<li> <a href="backup.php"><span class="nav-label">Crear copia de respaldo</span></a></li>
                        </ul>
                    </li>

            </ul-->
    </div>
  </nav>
  <div id="page-wrapper" class="gray-bg">
    <div class="row border-bottom">
      <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
          <a class="navbar-minimalize minimalize-styl-2 btn btn-primary"><i class="fa fa-bars"></i> </a>
        </div>
        <?php
                $id_sucursal=$_SESSION["id_sucursal"];
                $qsucursal=_query("SELECT descripcion FROM sucursal WHERE id_sucursal='$id_sucursal'");
                $row_sucursal=_fetch_array($qsucursal);
                $sucursal=$row_sucursal["descripcion"];

            ?>
          <ul class="nav navbar-top-links navbar-right">
            <li>

            </li>
            <li class="dropdown top-menu-item-xs">
                <a href="" class="dropdown-toggle " data-toggle="dropdown" aria-expanded="true" style="background:transparent"><span class="m-r-sm text-muted welcome-message">Bienvenido <b><?php echo $_SESSION["nombre"].", ".$sucursal ?> </b></span><img style="margin-left: -10px;margin-top: -25px;margin-bottom: -25px;  width:36px; height:36px;" src="<?php if($_SESSION["imagen"] !=""){echo $_SESSION["imagen"];} else{ echo "img/profile.svg"; } ?>" alt="user-img" class="img-circle"></a>
                <ul class="dropdown-menu" style="margin-top: 1px; border-top-left-radius: 0; border-top-right-radius: 0;  box-shadow:1px 1px 2px 1px #e7eaec;">
                    <li><a href="perfil.php" style="margin:0; margin-top: 5px; margin-bottom: 5px;  border-radius:0;"><i class="fa fa-user" style="color:rgb(69, 189, 241)"></i> Usuario</a></li>
                    <li><a href="logout.php" style="margin:0; margin-bottom: 5px; border-radius:0"><i class="fa fa-sign-out"  style="color:rgb(255, 117, 117)"></i> Cerrar sesión</a></li>
                </ul>
            </li>
            <!--
            <li>
              <a href="logout.php">
                        <i class="fa fa-sign-out"></i> Salir
                    </a>
            </li>
          -->
          </ul>

      </nav>
    </div>
