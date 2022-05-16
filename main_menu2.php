<nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element"> <span>
                            <img alt="image" class="img-circle" src="img/premier.png" width="90px">
                             </span>
                    </div>
                    <div class="logo-element">
                        PB
                    </div>
                    <li class="active">
                        <a href="dashboard.php"><i class="fa fa-star-o"></i> <span class="nav-label">Inicio</span></a>
                    </li>
                </li>
                 <li>
                <?php
                include_once '_core.php';
                $id_user=$_SESSION["id_usuario"];
						    $admin=$_SESSION["admin"];
								$sql_menus="SELECT id_menu, nombre, prioridad, administrador FROM menu";
								$result=_query($sql_menus);
								$numrows=_num_rows($result);
								$main_lnk='dashboard.php';
								for($i=0;$i<$numrows;$i++){
								$row=_fetch_array($result);
								$menuname=$row['nombre'];
								$id_menu=$row['id_menu'];
								echo "<li><a href='".$main_lnk."'>".$menuname."</a></li>";
								if($admin=='1'){
								$sql_links="SELECT distinct menu.id_menu, menu.nombre as nombremenu, menu.prioridad,
								modulo.id_modulo, modulo.nombre as nombremodulo, modulo.descripcion, modulo.filename, usuario.admin
								FROM menu, modulo, usuario_modulo, usuario
								WHERE usuario.id_usuario='$id_user'
								AND usuario.admin='1'
								AND menu.id_menu='$id_menu'
								AND menu.id_menu=modulo.id_menu
								AND modulo.mostrarmenu='1'
								";
								}
								else {
								$sql_links="
								SELECT menu.id_menu, menu.nombre as nombremenu, menu.prioridad,
								modulo.id_modulo,  modulo.nombre as nombremodulo, modulo.descripcion, modulo.filename,
								usuario_modulo.id_usuario,usuario.admin
								FROM menu, modulo, usuario_modulo, usuario
								WHERE usuario.id_usuario='$id_user'
								AND menu.id_menu='$id_menu'
								AND usuario.id_usuario=usuario_modulo.id_usuario
								AND usuario_modulo.id_modulo=modulo.id_modulo
								AND menu.id_menu=modulo.id_menu
								AND modulo.mostrarmenu='1'
								";
								}
								$result_modules=_query($sql_links);
								$numrow2=_num_rows($result_modules);
								if($numrow2>0){
									echo "<ul class='nav nav-second-level'>";
									for($j=0;$j<$numrow2;$j++){
										$row_modules=_fetch_array($result_modules);
										$lnk=strtolower($row_modules['filename']);
										$modulo=$row_modules['nombremodulo'];
										$id_modulo=$row_modules['id_modulo'];
										echo "<li><a href='".$lnk."'>".ucfirst($modulo)."</a></li>";
									}
									echo"</ul>";
								}
							}
							 echo"
                    </ul>
					</li>";
          ?>
        </div>
    </nav>
        <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
        <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
            <form role="search" class="navbar-form-custom" action="search_results.html">
                <div class="form-group">
                    <input type="text" placeholder="Buscar algo..." class="form-control" name="top-search" id="top-search">
                </div>
            </form>
        </div>
            <ul class="nav navbar-top-links navbar-right">
                <li>
                    <span class="m-r-sm text-muted welcome-message">Bienvenido al Dashboard <b><?php echo $_SESSION["nombre"]?> </b></span>
                </li>

                <li>
                    <a href="logout.php">
                        <i class="fa fa-sign-out"></i> Salir
                    </a>
                </li>
            </ul>

        </nav>
        </div>
