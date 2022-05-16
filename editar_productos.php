<?php
include_once "_core.php";
header("Access-Control-Allow-Origin: *");
function initial() {
	$_PAGE = array ();
	$_PAGE ['title'] = 'Editar Producto';
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
     WHERE
     id_producto='$id_producto'";
     $result=_query($sql);
     $count=_num_rows($result);

    $id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$id_sucursal=$_SESSION["id_sucursal"];

	//permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
    //stock Agregar 16 jul 2016
    $sql_stock="SELECT producto.id_producto,stock.stock,stock.costo_promedio
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
	}
	else{
		$stock=0;
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
                            <h5>Editar producto</h5>
                        </div>
                        <div class="ibox-content">
                           <!--paneles -->
                            <div class="panel blank-panel">

                        <div class="panel-heading">
                            <div class="panel-title m-b-md"><h4>Informacion de Producto</h4></div>
                            <div class="panel-options">

                                <ul class="nav nav-tabs">
                                    <li class="active" id='tabsform'><a data-toggle="tab" href="#tab-1">Informacion General </a></li>
                                    <li class=""><a data-toggle="tab" href="#tab-2">Inventario -Costos - Precios</a></li>
                                    <li class=""><a data-toggle="tab" href="#tab-3">Imagen</a></li>
                                </ul>
                            </div> <!--div class="panel-options"-->

                        </div><!--div class="panel-heading"-->
						<!--paneles -->
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
										$presentacion=$row['presentacion'];
										$porcentaje_utilidad1=$row['porcentaje_utilidad1'];
										$porcentaje_utilidad2=$row['porcentaje_utilidad2'];
										$porcentaje_utilidad3=$row['porcentaje_utilidad3'];
										$porcentaje_utilidad4=$row['porcentaje_utilidad4'];
										$estado=$row['estado'];
										$combo=$row['combo'];
										$perecedero=$row['perecedero'];
										//$costo_promedio=$row['costo_promedio'];
										$existencias_min=$row['existencias_min'];
										$id_proveedor=$row['id_proveedor'];
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
                             <div class="form-group has-info single-line"><label>Presentación</label> <input type="text" placeholder="Presentacion" class="form-control" id="presentacion" name="presentacion" value="<?php echo $presentacion?>"></div>


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
                                <label>Seleccione Proveedor</label>
                                <div class="input-group">
                                <select  name='id_proveedor' id='id_proveedor'   style="width:200px;">
                                <option value=''>Seleccione</option>

                                  <?php
                                   $qproveedor=_query("SELECT *FROM proveedor ORDER BY nombre_proveedor ");
                                   while($row_p=_fetch_array($qproveedor))
                                   {
                                    //$id_categoria=$row_cat["id_categoria"];
                                    $nombre=$row_p["nombre_proveedor"];

                                      if($id_proveedor==$row_p['id_proveedor'])
                                      {
                                      echo "<option value='".$row_p['id_proveedor']."' selected>".$row_p['nombre_proveedor']."</option>";
                                      }
                                      else
                                      {
                                      echo "<option value='".$row_p['id_proveedor']."'>".$row_p['nombre_proveedor']."</option>";
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
									</div>
									</div>";
									}
								}

                                ?>
								<input type="hidden" name="process" id="process" value="edited">
							<input type='hidden' name='urlprocess' id='urlprocess'value="<?php echo $filename ?> ">
							   <input type="hidden" name="id_producto" id="id_producto" value="<?php echo $_REQUEST['id_producto']?> ">

								<div>

                                       <input type="submit" id="submit1" name="submit1" value="Guardar" class="btn btn-primary m-t-n-xs" />
                                        <!--input type="button" id="submit1" name="submit1" value="Guardar" class="btn btn-primary m-t-n-xs" /-->
                                    </div>
                                </form>

                    </div><!--div id="tab-1" class="tab-pane"-->
					<div id="tab-2" class="tab-pane">
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
							<div class="form-group has-info single-line">
								<label>Existencias Mínimas:</label>
								<input type="text" placeholder="Existencias Minimas" class="form-control" id="existencias_min" name="existencias_min" value="<?php echo $existencias_min ?>	">
							</div>
                          </div>
							<div class="col-sm-4">
								<div class="form-group has-info single-line">
									  <label>Stock</label>
									  <input type="text" placeholder="Stock" class="form-control" id="stock" name="stock" value="<?php echo $stock?>">
								</div>
							</div>
								<div class="col-sm-4">
									<div class="form-group has-info single-line">
                                      <label class="control-label" for="Costo">Costo $</label>
                                      <input type="text" id="costo_promedio" name="costo_promedio" placeholder="Costo Promedio" class="form-control" value="<?php echo $costo_promedio?>">
									</div>
								</div>

						</div>
						 <div class="row">
                              <div class="col-sm-3">
                                  <div class="form-group">
                                      <label class="control-label" for="Utilidad 1">% Utilidad Precio 1</label>
                                      <input type="text" id="porcentaje_utilidad1" name="porcentaje_utilidad1" placeholder="Utilidad Precio 1" class="form-control" value="<?php echo $porcentaje_utilidad1?>">
                                  </div>



                              </div>
                              <div class="col-sm-3">
                                  <div class="form-group">
                                      <label class="control-label" for="Utilidad 2">% Utilidad Precio 2</label>
                                      <input type="text" id="porcentaje_utilidad2" name="porcentaje_utilidad2" placeholder="Utilidad precio 2" class="form-control" value="<?php echo $porcentaje_utilidad2 ?>">
                                  </div>
                              </div>
                              <div class="col-sm-3">
                                  <div class="form-group">
                                      <label class="control-label" for="Utilidad 3">% Utilidad Precio 3</label>
                                      <input type="text" id="porcentaje_utilidad3" name="porcentaje_utilidad3" placeholder="Utilidad precio 3" class="form-control" value="<?php echo $porcentaje_utilidad3 ?>">
                                  </div>
                              </div>
                              <div class="col-sm-3">
                                  <div class="form-group">
                                      <label class="control-label" for="Utilidad 4">% Utilidad Precio 4</label>
                                      <input type="text" id="porcentaje_utilidad4" name="porcentaje_utilidad4" placeholder="Utilidad precio 4" class="form-control" value="<?php echo $porcentaje_utilidad4 ?>">
                                  </div>
                              </div>
                        </div>

							<?php
							//Precio = Costo + (Costo * %margen)
							//var pv =round((cp+(cp*utilidad)/100),2);
							$pv1=round(($costo_promedio+($costo_promedio*$porcentaje_utilidad1)/100),2);
							$pv2=round(($costo_promedio+($costo_promedio*$porcentaje_utilidad2)/100),2);
							$pv3=round(($costo_promedio+($costo_promedio*$porcentaje_utilidad3)/100),2);
							$pv4=round(($costo_promedio+($costo_promedio*$porcentaje_utilidad4)/100),2);
							$pv1=sprintf('%.2f',$pv1);
							$pv2=sprintf('%.2f',$pv2);
							$pv3=sprintf('%.2f',$pv3);
							$pv4=sprintf('%.2f',$pv4);
							?>
							<div class="row">
								<div class="col-sm-3">
									<label class="control-label" for="precio_venta1">Precio Venta 1</label>
									<div class="input-group">
										<span class="input-group-addon">$</span>
										<input type="text" id="precio_venta1" name="precio_venta1" placeholder="Precio Venta" class="form-control" readonly value="<?php echo $pv1?>">
									</div>
								</div>
								<div class="col-sm-3">
									<label class="control-label" for="Costo">Precio Venta 2</label>
									<div class="input-group">
										<span class="input-group-addon">$</span>
										<input type="text" id="precio_venta2" name="precio_venta2" placeholder="Precio Venta" class="form-control" readonly value="<?php echo $pv2?>">
									</div>
								</div>
								<div class="col-sm-3">
									<label class="control-label" for="Costo">Precio Venta 3</label>
									<div class="input-group">
										<span class="input-group-addon">$</span>
										<input type="text" id="precio_venta3" name="precio_venta3" placeholder="Precio Venta" class="form-control" readonly value="<?php echo $pv3?>">
									</div>
								</div>
								<div class="col-sm-3">
									<label class="control-label" for="Costo">Precio Venta 4 </label>
									<div class="input-group">
										<span class="input-group-addon">$</span>
										<input type="text" id="precio_venta4" name="precio_venta4" placeholder="Precio Venta" class="form-control" readonly value="<?php echo $pv4?>">
									</div>
								</div>
							</div>
                          <div class="row">
								<div class="col-md-12">
									<!--div class="title-action" id='botones'-->
									<div class="form-group">
										<br>
										<button id="btnPrecios" name="btnPrecios" class='btn btn-primary'>Guardar</button>
									</div>
								</div>
                             </div>
					</div><!--div id="tab-2" class="tab-pane"-->
					<div id="tab-3" class="tab-pane"><!--div id="tab-3" class="tab-pane"-->
						<div class="row">
							<div class="col-md-8">
									<div class="form-group has-info single-line">
										<label class="control-label">Seleccionar Imagen</label>
										<input id="producto-img" name="producto-img[]" class="file" type="file" multiple data-min-file-count="1">
									</div>
							</div>
                        </div>



								<input type="hidden" name="id_producto" id="id_product" value="<?php echo $_REQUEST['id_producto']?> ">
								<input type="hidden" name="ruta" id="ruta" value="<?php echo $img_ruta ?> ">
								<input type="hidden" name="ipserver" id="ipserver" value="<?php echo $host ?> ">
								 <input type="hidden" name="ruta_archivo" id="ruta_archivo" value="<?php echo $dirfin;?>">
								<!--div-->
					</div><!--div id="tab-3" class="tab-pane"-->
					</div><!--div class="tab-content"-->
				</div>
			</div>
		</div>
	</div>
</div>




<?php

include_once ("footer.php");
echo "<script src='js/funciones/funciones_productos.js'></script>";
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
	$presentacion=$_POST['presentacion'];
	$estado=$_POST['estado'];
	$combo=$_POST['combo'];
	$perecedero=$_POST['perecedero'];
	$existencias_min=$_POST['existencias_min'];
	$id_proveedor=$_POST['id_proveedor'];
	$id_categoria=$_POST['id_categoria'];


	$descripcion=trim($descripcion);
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
		'presentacion' => $presentacion,
		'estado' => $estado,
		'combo' => $combo,
		'id_proveedor' => $id_proveedor,
		'id_categoria' => $id_categoria,
		'perecedero' => $perecedero
		);

    $where_clause = "id_producto='" . $id_producto . "'";
	$updates = _update ( $table, $form_data, $where_clause );



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

	$porcentaje_utilidad1=$_POST['porcentaje_utilidad1'];
	$porcentaje_utilidad2=$_POST['porcentaje_utilidad2'];
	$porcentaje_utilidad3=$_POST['porcentaje_utilidad3'];
	$porcentaje_utilidad4=$_POST['porcentaje_utilidad4'];
	$stock=$_POST['stock'];
	$existencias_min=$_POST['existencias_min'];

	$table = 'producto';
	$form_data = array (
		'porcentaje_utilidad1' => $porcentaje_utilidad1,
		'porcentaje_utilidad2' => $porcentaje_utilidad2,
		'porcentaje_utilidad3' => $porcentaje_utilidad3,
		'porcentaje_utilidad4' => $porcentaje_utilidad4,
	);

    $where_clause = "id_producto='" . $id_producto . "'";
	$updates = _update ( $table, $form_data, $where_clause );


	$sql_stock2="SELECT producto.id_producto,stock.stock,stock.costo_promedio
    FROM producto,stock
    WHERE producto.id_producto=stock.id_producto
    AND stock.id_producto='$id_producto'
    AND stock.id_sucursal='$id_sucursal'";

    $result2=_query($sql_stock2);
    $count2=_num_rows($result2);
    $table2= 'stock';
    if ($count2>0){
			$rowstock=_fetch_array($result1);
			$stock_ante=$rowstock['stock'];
			$costo_promedioante=$rowstock['costo_promedio'];

		$where_clause2= "id_producto='" . $id_producto . "' AND id_sucursal='".$id_sucursal."'";
		//verificar stock sino insertarlo con valor 0
		$form_data2 = array(
		'id_sucursal'=>$id_sucursal,
		'id_producto'=>$id_producto,
		'costo_promedio'=>$costo_promedio,
		'stock'=>$stock,
		'stock_minimo'=>$existencias_min
		);

	    $insertar2 = _update($table2,$form_data2, $where_clause2);
	}
	else{

		//verificar stock sino insertarlo con valor 0
		$stock_ini=0;
		$form_data = array(
		'id_sucursal'=>$id_sucursal,
		'id_producto'=>$id_producto,
		'costo_promedio'=>$costo_promedio,
		'stock'=>$stock_ini,

		);
		$insertar2 = _insert($table2,$form_data);
	}
	$form_datax = array(
		'costo_promedio'=>$costo_promedio,
	);
	$tablex= 'stock';
	$where_clausex= "id_producto='" . $id_producto."'";
	$actualizar_costo =  _update($tablex,$form_datax, $where_clausex);

		if($updates && $insertar2){
			$xdatos['typeinfo']='Info';
			$xdatos['msg']='Registro guardado con exito!';
			$xdatos['process']='edited';
		}
		else{
			$xdatos['typeinfo']='Error';
			$xdatos['msg']='Registro no editado ';
		}
			$xdatos['id_producto']=$id_producto;
	echo json_encode($xdatos);
}
if(!isset($_REQUEST['process'])){
	initial();
}
else
{
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
	}
}
}
?>