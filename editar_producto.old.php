<?php
include_once "_core.php";
header("Access-Control-Allow-Origin: *");
function initial() {
	$_PAGE = array ();
	$title='Editar Producto';
	$_PAGE ['title'] = $title;
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/jqGrid/ui.jqgrid.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/upload_file/fileinput.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	$id_producto= $_REQUEST['id_producto'];

  $sql="SELECT * FROM producto
  WHERE id_producto='$id_producto'";
  $result=_query($sql);
  $count=_num_rows($result);
	$id_sucursal=$_SESSION["id_sucursal"];
	//permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
    //stock Agregar 16 jul 2016
    $sql_stock="SELECT producto.id_producto,stock.stock,stock.costo_promedio,
    stock.utilidad, stock.utilidad2, stock.pv_base,stock.precio_mayoreo,stock.porc_desc_base, stock.stock_minimo,
    stock.pv_desc_base ,  stock.porc_desc_max ,  stock.pv_desc_max,
    stock.precio_oferta,stock.fecha_ini_oferta,stock.fecha_fin_oferta
    FROM producto,stock
    WHERE producto.id_producto=stock.id_producto
    AND stock.id_producto='$id_producto'
    AND stock.id_sucursal='$id_sucursal'";

    $result1=_query($sql_stock);
    $count1=_num_rows($result1);
    if ($count1>0){
			$row=_fetch_array($result1);
			$stock=$row['stock'];
			$costo_promedio=$row['costo_promedio'];
			$utilidad=$row['utilidad'];
			$utilidad2=$row['utilidad2'];
			$pv_base=$row['pv_base'];
			$precio_mayoreo=$row['precio_mayoreo'];
			$porc_desc_base=$row['porc_desc_base'];
			$pv_desc_base=$row['pv_desc_base'];
			$porc_desc_max=$row['porc_desc_max'];
			$pv_desc_max=$row['pv_desc_max'];
			$fecha_ini_oferta=$row['fecha_ini_oferta'];
			$fecha_fin_oferta=$row['fecha_fin_oferta'];
			$precio_oferta=$row['precio_oferta'];
			$stock_minimo=$row['stock_minimo'];
	}
	else{
		$stock=0;
		$costo_promedio=0;
		$porc_desc_base=0;
		$pv_base=0;
		$utilidad=0;
		$porc_desc_max=0;
		$pv_desc_max=0;
		$pv_desc_base=0;
		$precio_mayoreo=0;
		$utilidad2=0;
		$fecha_ini_oferta=date("Y-m-d");
		$dias=3;
		$fecha_fin_oferta=sumar_dias($fecha_ini_oferta,$dias);
	}
	$host= $_SERVER["HTTP_HOST"];
	$ruta1 = $_SERVER["PHP_SELF"];
	$dir_actual=explode("/",$ruta1);
	$nombre_archivo= array_pop($dir_actual);
	if(is_array($dir_actual))
		$numdir = count($dir_actual);
	else
		$numdir=0;

	$dirfin="";
	for($i=0;$i<$numdir; $i++){
		$dirfin.=trim($dir_actual[$i]).'/';
	}

?>


            <div class="row wrapper border-bottom white-bg page-heading">

                <div class="col-lg-2">

                </div>
            </div>
        <div class="wrapper wrapper-content  animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
					<?php
						if ($links!='NOT' || $admin=='1' ){
					?>
                        <div class="ibox-title">
                            <h5><?php echo $title;?></h5>
                             <input type="hidden" name="id_sucursal" id="id_sucursal" value="<?php echo $id_sucursal;?>">
                        </div>
                        <div class="ibox-content">
                            <div class="panel blank-panel">

                        <div class="panel-heading">
                            <div class="panel-title m-b-md"><h4>Informacion de Producto</h4></div>
                            <div class="panel-options">

                                <ul class="nav nav-tabs">
                                    <li class="active" id='tabsform'><a data-toggle="tab" href="#tab-1">Informacion General </a></li>
                                    <!--li class=""><a data-toggle="tab" href="#tab-2">Inventario - Lotes</a></li-->
                                    <li class=""><a data-toggle="tab" href="#tab-3">Costos - Precios</a></li>
                                    <li class=""><a data-toggle="tab" href="#tab-4">Ofertas</a></li>
                                    <li class=""><a data-toggle="tab" href="#tab-5">Imagen y Detalles Extra</a></li>
                                </ul>
                            </div> <!--div class="panel-options"-->

                        </div><!--div class="panel-heading"-->
						<div class="panel-body">
				<div class="tab-content">
				<div id="tab-1" class="tab-pane active">
                          <form name="formulario" id="formulario">
							  <?php
							  if ($count>0){

									for($i=0;$i<$count;$i++){
										$row=_fetch_array($result);
										$descripcion=$row['descripcion'];
										$barcode=$row['barcode'];
										$unidad=$row['unidad'];
										$marca=$row['marca'];
										$color=$row['color'];
										$embalaje=$row['embalaje'];
										$id_presentacion=$row['id_presentacion'];
										$porcentaje_utilidad1=$row['porcentaje_utilidad1'];
										$porcentaje_utilidad2=$row['porcentaje_utilidad2'];
										$porcentaje_utilidad3=$row['porcentaje_utilidad3'];
										$porcentaje_utilidad4=$row['porcentaje_utilidad4'];
										$estado=$row['estado'];
										$exento=$row['exento'];
										$combo=$row['combo'];
										$perecedero=$row['perecedero'];
										$id_ubicacion=$row['id_ubicacion'];
										$id_posicion=$row['id_posicion'];
										$existencias_min=$row['existencias_min'];
										$fecha_caducidad=$row['fecha_caducidad'];
										$id_categoria=$row['id_categoria'];
										$img_ruta=$row['imagen'];
										if ($fecha_caducidad=='0000-00-00'){
											$fecha_caducidad=date('m/d/Y');
										}
										else{
											$dd=substr($fecha_caducidad,8,2);
											$mm=substr($fecha_caducidad,5,2);
											$yy=substr($fecha_caducidad,0,4);
											$fecha_caducidad=$mm.'/'.$dd.'/'.$yy;
										}



								?>
                               <div class="form-group has-info single-line"><label>Código de Barra </label> <input type="text" placeholder="Digite Código de Barra" class="form-control" id="barcode" name="barcode" value="<?php echo $barcode ?>"></div>
                              <div class="form-group has-info single-line"><label>Descripción</label> <input type="text" placeholder="Descripcion" class="form-control" id="descripcion" name="descripcion" value="<?php echo $descripcion?>"></div>
                              <div class="row">
																<div class="col-sm-3">
																	<div class="form-group has-info single-line"><label>Unidad</label> <input type="text" placeholder="Unidad" class="form-control" id="unidad" name="unidad" value="<?php echo $unidad?>"></div>
																</div>
                                <div class="col-sm-3">
                                  <div class="form-group has-info single-line"><label>Marca</label> <input type="text" placeholder="Marca" class="form-control" id="marca" name="marca" value="<?php echo $marca?>"></div>
                               </div>
                               <div class="col-sm-3">
                                <div class="form-group has-info single-line"><label>Color</label> <input type="text" placeholder="Color" class="form-control" id="color" name="color" value="<?php echo $color?>"></div>
                               </div>
                               <div class="col-sm-3">
                                 <div class="form-group has-info single-line"><label>Embalaje</label> <input type="text" placeholder="embalaje" class="form-control" id="embalaje" name="embalaje" value="<?php echo $embalaje?>"></div>
							                 </div>
                              </div>

                          <div class="row">
							<div class="col-sm-4">
                                <div class="form-group has-info single-line">
                                <label>Seleccione Categoría</label>
                                <div class="input-group">
                                <select  name='categoria' id='categoria'  style="width:200px;">
                                <?php

                                   $qcategoria2=_query("SELECT * FROM categoria where id_categoria!='$id_categoria'");
                                   while($row_categoria2=_fetch_array($qcategoria2))
                                   {
                                       $id_categoria2=$row_categoria2["id_categoria"];
                                       $nombrecat2=$row_categoria2["nombre"];
                                       echo "
                                   <option value='$id_categoria2'>$nombrecat2</option>
                                   ";
                                   }
                                   $qcategoria=_query("SELECT * FROM categoria where id_categoria='$id_categoria'");
                                   while($row_categoria=_fetch_array($qcategoria))
                                   {
                                       $id_categoria=$row_categoria["id_categoria"];
                                       $nombrecat=$row_categoria["nombre"];
                                       echo "
                                   <option value='$id_categoria' selected>$nombrecat</option>
                                   ";
                                   }
                                   ?>
                                  </select>
                                   </div>
                                   </div>
                                 </div>

                                  <div class="col-sm-4">
                                   <div class="form-group has-info single-line">
                                <label>Seleccione presentacion</label>
                                <div class="input-group">
                                <select  name='id_presentacion' id='id_presentacion'   style="width:200px;">
                                <option value=''>Seleccione</option>

                                  <?php
                                   $qpresentacion=_query("SELECT * FROM presentacion ORDER BY descripcion ");
                                   while($row_p=_fetch_array($qpresentacion))
                                   {
                                    //$id_categoria=$row_cat["id_categoria"];
                                    $nombre=$row_p["descripcion"];

                                      if($id_presentacion==$row_p['id_presentacion'])
                                      {
                                      echo "<option value='".$row_p['id_presentacion']."' selected>".$row_p['descripcion']."</option>";
                                      }
                                      else
                                      {
                                      echo "<option value='".$row_p['id_presentacion']."'>".$row_p['descripcion']."</option>";
                                      }

                                   }
                                   ?>
                                  </select>
                                   </div>
                                   </div>
                                  </div>
                          </div>


                              <div class="row">


                                  <div class="col-sm-4">
                                   <div class="form-group has-info single-line">
                                 <div class="form-group"><label class="control-label">Activo </label>
                                    <?php
                                      if($estado==1 or $estado==true){
                                        echo "<div class=\"checkbox i-checks\"><label> <input type=\"checkbox\"  id=\"activo\" name=\"activo\" value=\"1\" checked> <i></i>  </label></div>";
                                      }
                                      else
                                      {
                                        echo "<div class=\"checkbox i-checks\"><label> <input type=\"checkbox\"  id=\"activo\" name=\"activo\" value=\"1\"> <i></i>  </label></div>";
                                      }
                                      ?>
                                    </div>
                                  </div>
                    			     </div>

                                <div class="col-sm-4">
                                   <div class="form-group has-info single-line">
                                 <div class="form-group"><label class="control-label">Producto perecedero </label>
                                    <?php
                                      if($perecedero==1){
                                        echo "<div class=\"checkbox i-checks\"><label> <input type=\"checkbox\"  id=\"perecedero\" name=\"perecedero\" value=\"1\" checked> <i></i>  </label></div>";
                                      }
                                      else
                                      {
                                        echo "<div class=\"checkbox i-checks\"><label> <input type=\"checkbox\"  id=\"perecedero\" name=\"perecedero\" value=\"1\"> <i></i>  </label></div>";
                                      }
                                      ?>
                                    </div>
                                  </div>
                               </div>
                              <?php

                              echo"
                               <div class=\"col-sm-4\">
                                   <div class=\"form-group has-info single-line\">
                                    <div class=\"form-group\"><label class=\"control-label\">Parte de Combo </label>
                                     ";
                                       if($combo==1){
                                          echo "<div class='checkbox i-checks'><label> <input type='checkbox'  id='combo' name='combo' value='1' checked> </i>  </label>
                                        ";
                                       }
                                       else
                                       {
                                         echo "<div class='checkbox i-checks'><label> <input type='checkbox'  id='combo' name='combo' value='1'> </i>  </label>
                                        ";
                                       }

                                   echo "
                                    </div>
                                    </div>
                                </div>


                                 </div>";


                                   if ($count1>0){
                                ?>


                               <?php } ?>
                               </div>
								<div class="row">
								  <div class="col-sm-4">
                                   <div class="form-group has-info single-line">
                                <label>Seleccione Ubicacion</label>
                                <div class="input-group">
                                <select  name='ubicacion' id='ubicacion'   style="width:200px;">
                                <option value=''>Seleccione</option>

                                <?php
									}
								}
								//Ver ubicacion del producto
								$sql_ubica="SELECT *
								FROM ubicacion
								WHERE id_ubicacion!='$id_ubicacion'";
								$result_ubica=_query($sql_ubica);
								 while($row_ubica=_fetch_array($result_ubica))
                                   {
                                       $id_ubica=$row_ubica["id_ubicacion"];
                                       $nombre_ubica=$row_ubica["descripcion"];
                                       echo "
                                   <option value='$id_ubica'>$nombre_ubica</option>
                                   ";
                                   }
                                  $sql_ubica2="SELECT *
								FROM ubicacion
								WHERE id_ubicacion='$id_ubicacion'";
								$result_ubica2=_query($sql_ubica2);
                               while($row_ubica2=_fetch_array($result_ubica2))
                                   {
                                         $id_ubica2=$row_ubica2["id_ubicacion"];
                                         $nombre_ubica2=$row_ubica2["descripcion"];
                                       echo "
                                   <option value='$id_ubica2' selected> $nombre_ubica2</option>
                                   ";
                                   }

                                ?>

                                  </select>
                                   </div>
                                   </div>
                                 </div>

                                  <div class="col-sm-4">
                                   <div class="form-group has-info single-line">
                                <label>Seleccione Posicion</label>
                                <div class="input-group">
                                <select  name='posicion' id='posicion'   style="width:200px;">
                                <!--option value=''>Seleccione</option-->
                                <?php
                                //Ver posicion del producto
								$sql_posicion="SELECT *
								FROM posicion_ubicacion
								WHERE id_ubicacion='$id_ubicacion'
								AND id_posicion!='$id_posicion'";
								$result_posicion=_query($sql_posicion);
								 while($row_posicion=_fetch_array($result_posicion))
                                   {
                                       $id_posicion=$row_posicion["id_posicion"];
                                       $nombre_posicion=$row_posicion["descripcion"];
                                       echo "
                                   <option value='$id_posicion'>$nombre_posicion</option>
                                   ";
                                   }
                                  $sql_posicion2="SELECT  posicion_ubicacion.id_posicion,posicion_ubicacion.id_ubicacion,posicion_ubicacion.descripcion
									FROM posicion_ubicacion,producto
									WHERE producto.id_ubicacion='$id_ubicacion'
									AND producto.id_producto='$id_producto'
									AND producto.id_ubicacion=posicion_ubicacion.id_ubicacion
									AND producto.id_posicion=posicion_ubicacion.id_posicion
								";
								$result_posicion2=_query($sql_posicion2);

                               while($row_posicion2=_fetch_array($result_posicion2))
                                   {
                                         $id_posicion2=$row_posicion2["id_posicion"];
                                         $nombre_posicion2=$row_posicion2["descripcion"];
                                       echo "
                                   <option value='$id_posicion2' selected> $nombre_posicion2</option>
                                   ";
                                   }

                                ?>

                                  </select>
                                   </div>
                                   </div>
                                 </div>


                                  </div>
                               <input type="hidden" name="process" id="process" value="edited">
                               <input type='hidden' name='urlprocess' id='urlprocess'value="<?php echo $filename ?> ">
							   <input type="hidden" name="id_producto" id="id_producto" value="<?php echo $_REQUEST['id_producto']?> ">
                               <div class="row">
                                   <div>

                                       <input type="submit" id="submit1" name="submit1" value="Guardar" class="btn btn-primary m-t-n-xs" />
                                        <!--input type="button" id="submit1" name="submit1" value="Guardar" class="btn btn-primary m-t-n-xs" /-->
                                    </div>
                               </div>
                                </form>

                                </div><!--div id="tab-1" class="tab-pane"-->

						<div id="tab-3" class="tab-pane">
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<input type="hidden" name="id_product" id="id_product" value="<?php echo $_REQUEST['id_producto']?> ">
										  <label class="control-label" for="Descrip">Descripción Producto</label>
											<input type="text" class="form-control" id="descripcion2" name="descripcion2" readonly value="<?php echo $descripcion?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
                                      <label class="control-label" for="Costo">Costo <?php echo $unidad?> unidad(es) </label>
                                      <input type="text" id="costo_promedio" name="costo_promedio" placeholder="Costo Promedio" class="form-control" value="<?php echo $costo_promedio?>">
									</div>
								</div>
								<div class="col-sm-3">
							 		<div class="form-group"><label class="control-label">Exento IVA</label>
										<?php
											if($exento==1 or $exento==true){
												echo "<div class='checkbox i-checks'><label> <input type='checkbox'  id='exento' name='exento' value='1' checked> <i></i>  </label></div>";
											}
											else
											{
												echo "<div class='checkbox i-checks'><label> <input type='checkbox'  id='exento' name='exento' value='1'> <i></i>  </label></div>";
											}
										?>
									</div>
						 		</div>



								</div>
								<div class="row">
									<div class="col-sm-3">
										<div class="form-group">
	                                      <label class="control-label" for="Costo">Precio Venta Base</label>
	                                      <input type="text" id="precio_base" name="precio_base" placeholder="Precio Venta" class="form-control"  value="<?php echo $pv_base ?>">
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
	                  	<label class="control-label" for="Utilidad">% Utilidad Precio Venta Base</label>
	                    <input type="text" id="utilidad" name="utilidad" placeholder="% Utilidad" class="form-control" readonly  value="<?php echo $utilidad;?>">
										</div>
										  <label class="control-label" for="Utilidad" id="util2"></label>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-3">
										<div class="form-group">
	                    	<label class="control-label" for="Costo">Precio Venta Mayoreo</label>
	                      <input type="text" id="precio_mayoreo" name="precio_mayoreo" placeholder="Precio Venta" class="form-control" value="<?php echo $precio_mayoreo;?>">
										</div>
									</div>
								<div class="col-sm-3">
									<div class="form-group">
                                      <label class="control-label" for="Costo">% Utilidad Precio Venta Mayoreo</label>
                                     <input type="text" id="utilidad2" name="utilidad2" placeholder="% Utilidad Precio Mayoreo" class="form-control" readonly  value="<?php echo $utilidad2 ?>">
									</div>
								</div>
                </div>
                             <div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<button id="btnPrecios" name="btnPrecios" class='btn btn-primary'>Guardar</button>
										</div>
								</div>
             </div>
						</div><!--div id="tab-3" class="tab-pane"-->
						<div id="tab-4" class="tab-pane">
								<?php
								if($fecha_ini_oferta=='0000-00-00' ||$fecha_fin_oferta=='0000-00-00' ){
									$fecha_ini_oferta=date("Y-m-d");
									$dias=3;
									$fecha_fin_oferta=sumar_dias($fecha_ini_oferta,$dias);
								}
								?>
								<div class="row">
									<div class="col-lg-8">
										<div class="form-group">
											<input type="hidden" name="id_product" id="id_product" value="<?php echo $_REQUEST['id_producto']?> ">
											<label class="control-label" for="Descrip">Descripción Producto</label>
											<input type="text" class="form-control" id="descripcion2" name="descripcion2" readonly value="<?php echo $descripcion?>">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label class="control-label" for="Costo">Costo Promedio</label>
											<input type="text" id="costo_promedio1" name="costo_promedio1" placeholder="Costo Promedio" class="form-control" readonly value="<?php echo $costo_promedio?>">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label class="control-label" for="oferta">Precio Oferta Temporal</label>
											<input type="text" id="precio_oferta" name="precio_oferta"  class="form-control decimal" value="<?php echo $precio_oferta;?>">
										</div>
									</div>
								</div>
								<div class="row">
									<div class='col-md-4' >
										<div class='form-group'>
											<label>Fecha Inicio Oferta:</label>
											<input type='text' class='datepick form-control' id='fecha_ini_oferta' name='fecha_ini_oferta' value="<?php echo $fecha_ini_oferta; ?>" >
										</div>
									</div>
									<div class='col-md-4' >
										<div class='form-group'>
											<label>Fecha finaliza Oferta:</label>
											<input type='text' class='datepick form-control' id='fecha_fin_oferta' name='fecha_fin_oferta' value="<?php echo $fecha_fin_oferta; ?>" >
										</div>
									</div>
								</div><!--div class="row"-->
								<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<button id="btnOfertas" name="btnOfertas" class='btn btn-primary'>Guardar</button>
										</div>
								</div>
                             </div>
						</div><!--div id="tab-4" class="tab-pane"-->
						<div id="tab-5" class="tab-pane">
							<div class="row">
								<div class="col-md-8">
									<div class="form-group has-info single-line">
										<label class="control-label">Seleccionar Imagen</label>
											<input id="producto-img" name="producto-img[]" class="file" type="file" multiple data-min-file-count="1">
									</div>
								</div>
                          </div>

                                <?php
								$ip2 = $_SERVER['SERVER_ADDR'];
								$host= $_SERVER["HTTP_HOST"];
                                ?>
																<input type="hidden" name="process" id="process" value="edited">
						 										<input type="hidden" name="id_producto" id="id_producto" value="<?php echo $_REQUEST['id_producto']?> ">
						 									 <input type="hidden" name="ruta" id="ruta" value="<?php echo $img_ruta ?> ">
						 									 <input type="hidden" name="ipserver" id="ipserver" value="<?php echo $host ?> ">
						 										<input type="hidden" name="ruta_archivo" id="ruta_archivo" value="<?php echo $dirfin;?>">
								<!--div-->
								</div><!--div id="tab-4" class="tab-pane"-->
							</div> <!--div class="panel blank-panel"-->
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php
include_once ("footer.php");
echo "<script src='js/funciones/funciones_producto.js'></script>";
//echo "<script src='js/plugins/select2/select2-cascade.js'></script>";
echo"
 <script>
     var ruta=$('#ruta').val();
		 var ruta_archivo=$('#ruta_archivo').val()+'upload.php';
     var ipserver=$('#ipserver').val();
     ipserver=ipserver.trim();
		 if (ipserver=='::1'){
	 var rutafinal='http://localhost/'+ruta_archivo;
	}
	else{
	 var rutafinal='http://'+ipserver+ruta_archivo;
	 }
    $('#producto-img').fileinput({
        language: 'es',
        previewFileType: 'image',
        uploadAsync: false,
        width: '200px',
        uploadUrl: rutafinal,
        allowedFileExtensions : ['jpg', 'png','gif'],
        uploadExtraData: function() {
            return {
                idProd: $('#id_producto').val(),
                username: $('#descripcion').val()
            };
        },
    initialPreview: [\"<img src='\"+ruta+\"' class='file-preview-image' alt='Desert' title='Desert'>\",],
    });

</script>
    ";

}
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function edited(){
	$id_sucursal=$_SESSION["id_sucursal"];
	$id_producto=$_POST['id_producto'];
	$descripcion=$_POST['descripcion'];
	$barcode=$_POST['barcode'];
	$unidad=$_POST['unidad'];
	$marca=$_POST['marca'];
	$color=$_POST['color'];
	$embalaje=$_POST['embalaje'];
	$id_presentacion=$_POST['id_presentacion'];
	$estado=$_POST['estado'];
	$combo=$_POST['combo'];
	$perecedero=$_POST['perecedero'];
	$id_categoria=$_POST['id_categoria'];

	$descripcion=trim($descripcion);
	$descripcion=strtoupper($descripcion);
    $barcode=trim($barcode);
    $name_producto="";
	$sql_result=_query("SELECT id_producto,descripcion,barcode FROM producto WHERE descripcion='$descripcion' AND id_producto!='$id_producto'");
    $numrows=_num_rows($sql_result);
    $row_update=_fetch_array($sql_result);
    $id_update=$row_update["id_producto"];
    $name_producto=trim($row_update["descripcion"]);
    $descrip_producto_existe=false;
    if($name_producto!="" && $descripcion!="" ){
		$descrip_producto_existe=true;
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Registro no insertado, Descripción de Producto ya existe! ';
		$xdatos['process']='noinsert';
	}

    if ($barcode=="")
		 $barcodeexiste=false;
    if ($barcode!=""){
		$sql_barcode="SELECT id_producto,descripcion,barcode FROM producto WHERE barcode='$barcode'  AND id_producto!='$id_producto'";
		$sql_result_barcode=_query($sql_barcode);
		$numrows_barcode=_num_rows($sql_result_barcode);
		if ( $numrows_barcode>0){
			$xdatos['typeinfo']='Error';
			$xdatos['msg']='El Barcode ya está asignado a otro producto!';
			$xdatos['process']='existbarcode';
			$barcodeexiste=true;
		}
		else{
		 $barcodeexiste=false;
	}
	}
	if($numrows == 0 && $descrip_producto_existe==false && $descripcion!=$name_producto  && $barcodeexiste==false && $id_update!=$id_producto){
		$table = 'producto';
		$form_data = array (
		'descripcion' => $descripcion,
		'barcode' => $barcode,
		'unidad' => $unidad,
		'marca' => $marca,
		'color' => $color,
		'embalaje' => $embalaje,
		'id_presentacion' => $id_presentacion,
		'estado' => $estado,
		'combo' => $combo,
		'id_categoria' => $id_categoria,
		'perecedero' => $perecedero
		);

	$where_clause = "id_producto='" . $id_producto . "'";
		$updates = _update ( $table, $form_data, $where_clause );
      if($perecedero==1){
		  $fecha_hoy=date("Y-m-d");
		  $sql_stock="SELECT producto.id_producto,stock.stock,
			stock.costo_promedio,producto.perecedero,stock.stock_minimo
			FROM producto,stock
			WHERE producto.id_producto=stock.id_producto
			AND stock.id_producto='$id_producto'
			AND stock.id_sucursal='$id_sucursal'";
		  $result=_query($sql_stock);
		  $count=_num_rows($result);
		$table= 'stock';

    if ($count>0){
		$rowstock=_fetch_array($result);
		$stock_total=$rowstock['stock'];
		$costo_promedio=$rowstock['costo_promedio'];
		//$stock_total=$stock_ante+$stock;

	}
	else
		$stock_total=0;

		$sql_lote="SELECT id_lote_prod, id_lote, id_producto, fecha_entrada, fecha_caducidad,
									entrada, salida, estado, numero_doc, id_sucursal
									FROM lote
									WHERE  lote.id_producto='$id_producto'
									AND lote.id_sucursal='$id_sucursal'
									AND entrada>=salida
									AND estado!='VENCIDO'";
									$result_lote=_query($sql_lote);
									$count_lote=_num_rows($result_lote);

		  if($count_lote==0){
			$table_lote='lote';
			$form_data_lote = array(
					'numero_doc'=>'00000',
					'id_sucursal'=>$id_sucursal,
					'id_producto'=>$id_producto,
					'fecha_entrada'=>$fecha_hoy,
					'fecha_caducidad'=>'000-00-00',
					'entrada'=>$stock_total,
					'estado'=>'VIGENTE'
			);

										$insertar2 = _insert($table_lote,$form_data_lote);
		  }
	  }

		if($updates){
			$xdatos['typeinfo']='Info';
			$xdatos['msg']='Registro guardado con exito!';
			$xdatos['process']='edited';
		}
		else{
			$xdatos['typeinfo']='Error';
			$xdatos['msg']='Registro no editado ';
		}
	}
	echo json_encode($xdatos);
}

function guardar_precios(){
	$id_sucursal=$_SESSION["id_sucursal"];
	$id_producto=$_POST['id_producto'];
	$costo_promedio=$_POST['costo_promedio'];
	$utilidad=$_POST['utilidad'];
	$utilidad2=$_POST['utilidad2'];
	$pv_base=$_POST['pv_base'];
	$precio_mayoreo=$_POST['precio_mayoreo'];
  $exento=$_POST['exento'];

	$table0 = 'producto';
	$form_data0 = array (
	'exento' => $exento,
	);
	$where_clause0= "id_producto='" . $id_producto."'";
	$insertar0 = _update($table0,$form_data0, $where_clause0);

	$sql_stock="SELECT *
    FROM producto,stock
    WHERE producto.id_producto=stock.id_producto
    AND stock.id_producto='$id_producto'
    AND stock.id_sucursal='$id_sucursal'";

    $result=_query($sql_stock);
    $count=_num_rows($result);


    $table= 'stock';
	if ($count>0){
		$where_clause= "id_producto='" . $id_producto . "' AND id_sucursal='".$id_sucursal."'";
		//verificar stock sino insertarlo con valor 0
		$form_data = array(
		'costo_promedio'=>$costo_promedio,
		'utilidad'=>$utilidad,
		'utilidad2'=>$utilidad2,
		'pv_base'=>$pv_base,
		'precio_mayoreo'=>$precio_mayoreo,
		);

	    $insertar = _update($table,$form_data, $where_clause);
	}
	else{

		//verificar stock sino insertarlo con valor 0
		$stock_ante=0;
		$form_data = array(
		'id_sucursal'=>$id_sucursal,
		'stock'=>$stock_ante,
		'id_producto'=>$id_producto,
		'costo_promedio'=>$costo_promedio,
		'utilidad'=>$utilidad,
		'utilidad2'=>$utilidad2,
		'pv_base'=>$pv_base,
		'precio_mayoreo'=>$precio_mayoreo,
		);
		$insertar = _insert($table,$form_data);
	}
		if($insertar){
			$xdatos['typeinfo']='Info';
			$xdatos['msg']='Registro guardado con exito!';
			$xdatos['process']='edited';
		}
		else{
			$xdatos['typeinfo']='Error';
			$xdatos['msg']='Registro no editado ';
		}

	echo json_encode($xdatos);
}


function guardar_ofertas(){
	$id_sucursal=$_SESSION["id_sucursal"];
	$id_producto=$_POST['id_producto'];
	$costo_promedio=$_POST['costo_promedio'];
	$utilidad=$_POST['utilidad'];
	$pv_base=$_POST['pv_base'];
	$porc_desc_base=$_POST['porc_desc_base'];
	$pv_desc_base=$_POST['pv_desc_base'];
	$porc_desc_max=$_POST['porc_desc_max'];
	$pv_desc_max=$_POST['pv_desc_max'];

	$precio_oferta=$_POST['precio_oferta'];
	$fecha_ini_oferta=$_POST['fecha_ini_oferta'];
	$fecha_fin_oferta=$_POST['fecha_fin_oferta'];

	$sql_stock="SELECT *
    FROM producto,stock
    WHERE producto.id_producto=stock.id_producto
    AND stock.id_producto='$id_producto'
    AND stock.id_sucursal='$id_sucursal'";

    $result=_query($sql_stock);
    $count=_num_rows($result);

    /*
    if ($count>0){
			$rowstock=_fetch_array($result);
			$stock_ante=$rowstock['stock'];
			$costo_promedio=$rowstock['costo_promedio'];
	}
	*/
    $table= 'stock';
	if ($count>0){

		/*
		'id_sucursal'=>$id_sucursal,
		'id_producto'=>$id_producto,
		'costo_promedio'=>$costo_promedio,
		'utilidad'=>$utilidad,
		'pv_base'=>$pv_base,
		'porc_desc_base'=>$porc_desc_base,
		'pv_desc_base'=>$pv_desc_base,
		'porc_desc_max'=>$porc_desc_max,
		'pv_desc_max'=>$pv_desc_max,
		* */
		$where_clause= "id_producto='" . $id_producto . "' AND id_sucursal='".$id_sucursal."'";
		$form_data = array(
		'precio_oferta'=>$precio_oferta,
		'fecha_ini_oferta'=>$fecha_ini_oferta,
		'fecha_fin_oferta'=>$fecha_fin_oferta
		);

	    $insertar = _update($table,$form_data, $where_clause);
	}
	else{
		//verificar stock sino insertarlo con valor 0
		$stock_ante=0;
		$form_data = array(
		'id_sucursal'=>$id_sucursal,
		'stock'=>$stock_ante,
		'id_producto'=>$id_producto,
		'costo_promedio'=>$costo_promedio,
		'utilidad'=>$utilidad,
		'pv_base'=>$pv_base,
		'porc_desc_base'=>$porc_desc_base,
		'pv_desc_base'=>$pv_desc_base,
		'porc_desc_max'=>$porc_desc_max,
		'pv_desc_max'=>$pv_desc_max,
		'precio_oferta'=>$precio_oferta,
		'fecha_ini_oferta'=>$fecha_ini_oferta,
		'fecha_fin_oferta'=>$fecha_fin_oferta

		);
		$insertar = _insert($table,$form_data);
	}
		if($insertar){
			$xdatos['typeinfo']='Info';
			$xdatos['msg']='Registro guardado con exito!';
			$xdatos['process']='edited';
		}
		else{
			$xdatos['typeinfo']='Error';
			$xdatos['msg']='Registro no editado ';
		}

	echo json_encode($xdatos);
}

function guardar_stock(){
	$id_sucursal=$_SESSION["id_sucursal"];
	$id_producto=$_POST['id_producto'];
	$costo_promedio=$_POST['costo_promedio'];
	$existencias_min=$_POST['existencias_min'];
	$stock=$_POST['stock'];
	$perecedero=$_POST['perecedero'];

	// para precios y ofertas
	/*
	$costo_promedio=$_POST['costo_promedio'];
	$utilidad=$_POST['utilidad'];
	$pv_base=$_POST['pv_base'];
	$porc_desc_base=$_POST['porc_desc_base'];
	$pv_desc_base=$_POST['pv_desc_base'];
	$porc_desc_max=$_POST['porc_desc_max'];
	$pv_desc_max=$_POST['pv_desc_max'];

	$precio_oferta=$_POST['precio_oferta'];
	$fecha_ini_oferta=$_POST['fecha_ini_oferta'];
	$fecha_fin_oferta=$_POST['fecha_fin_oferta'];
	*/
	// fin para precios y ofertas

	$sql_stock="SELECT producto.id_producto,stock.stock,
	stock.costo_promedio,producto.perecedero,stock.stock_minimo
    FROM producto,stock
    WHERE producto.id_producto=stock.id_producto
    AND stock.id_producto='$id_producto'
    AND stock.id_sucursal='$id_sucursal'";

    $result=_query($sql_stock);
    $count=_num_rows($result);

    $table= 'stock';

    if ($count>0){
		$rowstock=_fetch_array($result);
		$stock_ante=$rowstock['stock'];
		$costo_promedio=$rowstock['costo_promedio'];
		$stock_total=$stock_ante+$stock;

		$where_clause= "id_producto='" . $id_producto . "' AND id_sucursal='".$id_sucursal."'";

		$form_data = array(
			'stock' => $stock,
			'stock_minimo'=>$existencias_min
		);
		$insertar = _update($table,$form_data, $where_clause );
	}
	else{
		//verificar stock sino insertarlo con valor 0
		$stock_ante=0;
		$form_data = array(
		'id_sucursal'=>$id_sucursal,
		'stock'=>$stock,
		'id_producto'=>$id_producto,
		'costo_promedio'=>$costo_promedio,
		'stock_minimo'=>$existencias_min
		);

		$insertar = _insert($table,$form_data);
	}

		if($insertar){
			$xdatos['typeinfo']='Info';
			$xdatos['msg']='Registro guardado con exito!';
			$xdatos['process']='edited';
		}
		else{
			$xdatos['typeinfo']='Error';
			$xdatos['msg']='Registro no editado ';
		}

	echo json_encode($xdatos);
}
function buscar_posicion(){
	$id_ubicacion=$_POST["id_ubicacion"];
	$id_producto=$_POST["id_producto"];
                               $sql_posicion="SELECT  posicion_ubicacion.id_posicion,posicion_ubicacion.id_ubicacion,posicion_ubicacion.descripcion
									FROM posicion_ubicacion,producto
									WHERE producto.id_ubicacion='$id_ubicacion'
									AND producto.id_producto='$id_producto'
									AND producto.id_ubicacion=posicion_ubicacion.id_ubicacion
									AND producto.id_posicion=posicion_ubicacion.id_posicion
								";
								$result_posicion=_query($sql_posicion);
								$count_posicion=_num_rows($result_posicion);
                               while($row_posicion=_fetch_array($result_posicion))
                                   {
                                         $id_posicion=$row_posicion["id_posicion"];
                                         $nombre_posicion=$row_posicion["descripcion"];
                                       echo "
                                   <option value='$id_posicion'>$nombre_posicion</option>
                                   ";
                                   }

                                if($count_posicion==0){
									$id_posicion=-1;
								}

								$sql_posicion2="SELECT *
								FROM posicion_ubicacion
								WHERE id_posicion!='$id_posicion'
								and id_ubicacion='$id_ubicacion'";
								$result_posicion2=_query($sql_posicion2);
								$count_posicion2=_num_rows($result_posicion2);

								if($count_posicion2==0){
									$id_posicion2=-1;
									 $nombre_posicion2="No se encontraron resultados";
									 echo "<option value=$id_posicion2'>$nombre_posicion2</option>";

								}
								 while($row_posicion=_fetch_array($result_posicion2))
                                   {
                                       $id_posicion2=$row_posicion["id_posicion"];
                                       $nombre_posicion2=$row_posicion["descripcion"];
                                       echo "<option value='$id_posicion2'>$nombre_posicion2</option> ";
                                   }

}

/*
function buscar_ubicacion2(){
    $sql_ubica="SELECT * FROM ubicacion";
	$result_ubica=_query($sql_ubica);
	$returnData=array();
     while($row_ubica=_fetch_array($result_ubica))
                                   {
                                         $id_ubica=$row_ubica["id_ubicacion"];
                                         $nombre_ubica=$row_ubica["descripcion"];

      $returnData[]=array("id"=>$id_ubica, "text"=>$nombre_ubica);
                             }

	echo json_encode($returnData);
}

function buscar_posicion2(){
	$id_ubicacion=$_POST["id_ubicacion"];

      $sql_ubica="SELECT *
								FROM posicion_ubicacion
								WHERE id_ubicacion='$id_ubicacion'";
								$result_ubica=_query($sql_ubica);
	$returnData=array();
     while($row_ubica=_fetch_array($result_ubica))
                                   {
                                         $id_ubica=$row_ubica["id_ubicacion"];
                                         $nombre_ubica=$row_ubica["descripcion"];

      $returnData[]=array("id"=>$id_ubica, "text"=>$nombre_ubica);
                                   }

	echo json_encode($returnData);
}
*/
if(!isset($_REQUEST['process'])){
	initial();
}
else{
	if(isset($_REQUEST['process'])){
		switch ($_REQUEST['process']) {
			case 'edited':
				edited();
				break;
			case 'formEdit' :
				initial();
				break;
			case 'guardar_precios':
				guardar_precios();
				break;
			case 'guardar_ofertas':
				guardar_ofertas();
				break;
			case 'guardar_stock':
				guardar_stock();
				break;
			case 'buscar_posicion':
				buscar_posicion();
				break;
			case 'buscar_posicion2':
				buscar_posicion2();
				break;
			case 'buscar_ubicacion2':
				buscar_ubicacion2();
				break;
		}
	}
}
?>
