<?php
include_once "_core.php";

function initial()
{
	$title='Editar Producto';
	$_PAGE = array ();
	$_PAGE ['title'] = $title;
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
//	$_PAGE ['links'] .= '<link href="css/plugins/jqGrid/ui.jqgrid.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
  	$_PAGE ['links'] .= '<link href="css/plugins/upload_file/fileinput.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	$id_producto = $_REQUEST["id_producto"];
	$sql = _query("SELECT * FROM productos WHERE id_producto='$id_producto'");
	$datos = _fetch_array($sql);
	$descripcion = $datos["descripcion"];

	$id_proveedor = $datos["id_proveedor"];
	$comentario = $datos["comentario"];
	$barcode = $datos["barcode"];
	$ultcosto = $datos["ultcosto"];
	$descuento = $datos["descuento"];
	$inactivo = $datos["inactivo"];
	$exento = $datos["exento"];
	$serie = $datos["serie"];
	$descuento = $datos["descuento"];
	$creado = $datos["creado"];
	$actualiza = $datos["actualiza"];
	$tiene_garantia = $datos["tiene_garantia"];
	if($tiene_garantia == 1)
	{
		$textg = "checked";
		$txh = "";
	}
	else
	{
		$textg = "";
		$txh = "hidden";
	}
   $fechaini_gar_prov= $datos["fechaini_gar_prov"];
	 if( $fechaini_gar_prov=='0000-00-00'){
		  $fechaini_gar_prov= date('Y-m-d');
	 }
	$marca = $datos["marca"];
	$modelo = $datos["modelo"];
	$img = $datos["imagen"];
//	$serie = $datos["serie"];
	$dias_garantia=$datos["dias_garantia"];
	$numdoc_gar= $datos["numdoc_gar"];
	$tiempo_garantia = $datos["tiempo_garantia"];
	$tipo_tiempo = $datos["tipo_tiempo"];

	$sql2 = _query("SELECT nombre FROM proveedores WHERE id_proveedor='".$id_proveedor."'");
	$datos2 = _fetch_array($sql2);
	$nombr = $datos2["nombre"];


	$mes = date("m");
	$anio = date("Y");
	$primer = $anio."-".$mes."-01";
	$actu = date("Y-m-d");
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
	                <h5><?php echo $title; ?></h5>
									<a href="admin_productos.php" class="btn btn-danger pull-right" style="margin-top: -10px;"> <i class="fa fa-mail-reply"></i> Finalizar Edición</a>
	            </div>
	            <div class="ibox-content">
	            		<ul class="nav nav-tabs">
                        <li class="active" id="hdp"><a data-toggle="tab" href="#home">Datos Principales</a></li>
                        <li id="hprecios"><a data-toggle="tab" href="#lprecio">Precios</a></li>
                        <li id="hcaracte"><a data-toggle="tab" href="#lcaracte">Caracteristicas</a></li>
                        <li id="himg"><a data-toggle="tab" href="#rotacion">Imagen del producto</a></li>
                    </ul>
										<?php
											echo "<input type='hidden' name='id_producto' id='id_producto' value='".$id_producto."'>"
										?>
                    <div class="row">
                        <div class="tab-content">
                            <div id="home" class="tab-pane fade in active"><br>
                            	<div class="col-lg-12">
	            									<div class="row">
		            			   					<div class="col-lg-6">
		                              	<div class="form-group has-info single-line">
		                              		<label>Descripción</label>
		                              		<input type="text" placeholder="Descripcion" class="form-control upper" id="descripcion" name="descripcion" value="<?php echo $descripcion; ?>"><br>
		                              	</div>
		                            	</div>
																	<div class="col-lg-3">
																		<div class="form-group has-info single-line">
																			<label>Categoria</label>
																			<select class="form-control select" name="categoria" id="categoria">
																				<?php
																				  	$sql_categoria = _query("SELECT * FROM categoria");
																						$cuenta = _num_rows($sql_categoria);
																						if($cuenta > 0)
																						{
																							while ($row_ca = _fetch_array($sql_categoria))
																							{
																								$id_categoria = $row_ca["id_categoria"];
																								$nombre = $row_ca["nombre"];
																								echo "<option value='".$id_categoria."'>".$nombre."</option>";
																							}
																						}
																				?>
																			</select>
																		</div>
																	</div>
																	<div class="col-lg-3">
																		<div class="form-group has-info single-line">
																			<label>Serie</label>
																			<input type="text" name="serie" id="serie" class="form-control" value="<?php echo $serie; ?>">
																		</div>
																	</div>
																</div>
																<div class="row">
																	<div class="col-lg-3">
							                      	<div class="form-group has-info single-line">
								                      		<label>Marca</label>
								                      		<input type="text" placeholder="Marca" class="form-control upper" id="marca" name="marca" value="<?php echo $marca; ?>"><br>
								                  		</div>
							                    </div>
																	<div class="col-lg-3">
										              		<div class="form-group has-info single-line">
										                  		<label>Modelo</label>
										                   		<input type="text" placeholder="Modelo" class="form-control upper" id="modelo" name="modelo" value="<?php echo $modelo; ?>"><br>
										              		</div>
									       					</div>
																	<div class="col-lg-3">
				 														<div class="form-group has-info single-line">
				 																<label>Barcode</label>
				 																<input type="text" placeholder="Barcode" class="form-control upper" id="barcode" name="barcode" value="<?php echo $barcode; ?>"><br>
				 														</div>
				 												 </div>
				 												 <div class="col-lg-3">
				 														 <div class="form-group has-info single-line">
				 																 <div class="form-group">
				 																		 <label class="control-label">Tiene garantia </label>
				 																		 <div class="checkbox i-checks">
				 																				 <label>
				 																						 <input type="checkbox"  id="tiene_garantia" name="tiene_garantia" value='<?php echo $tiene_garantia ?>' <?php echo $textg; ?>>
				 																				 </label>
				 																		 </div>
				 																 </div>
				 														 </div>
				 												 </div>

												  		</div>
															<div class="row">
																<div class="col-lg-3">
						              					<div class="form-group has-info single-line">
						              						<label>Descuento %</label>
						              						<input type="text" class="form-control" id="descuento" name="descuento" value="<?php echo $descuento; ?>">
						              					</div>
						            	 			</div>
																<div class="col-lg-3" id="tg" <?php echo $txh; ?>>
																	<div class="form-group has-info single-line">
																	<label>Tiempo Garantia</label>
																	<div class="row">
																		<div class="col-md-6">
							              						<input type="text" placeholder="" class="form-control" id="tiempo_garantia" name="tiempo_garantia" value='<?php echo $tiempo_garantia; ?>'>
																		</div>
																		<div class="col-md-6">
							              						<select class="select form-control" name="perido" id="periodo">
																					<option value="0"
																					<?php if($tipo_tiempo == 0){ echo "selected"; } ?>
																					>Dia/s</option>
																					<option value="1"
																					<?php if($tipo_tiempo == 1){ echo "selected"; } ?>
																					>Mes/es</option>
																					<option value="2"
																					<?php if($tipo_tiempo == 2){ echo "selected"; } ?>
																					>Año/s</option>
							              						</select>
																		</div>
																	</div>
						            	 				</div>
					                			</div>
															</div>
					                    <div>
					                    	<!--<input type="hidden" name="id_producto" id="id_producto" value="<?php echo $id_producto; ?>">-->
				                        <input type="hidden" name="process" id="process" value="editar">
				                        <input type="hidden" name="origin" id="origin" value=""><br>
				                        <input type="submit" id="btn_edatos" name="btn_edatos" value="Guardar" class="btn btn-primary m-t-n-xs pull-right" />
					                    </div>
				                </div>
				            </div>
			                <div id="lprecio" class="tab-pane fade"><br><br>
			            			<div class="col-lg-12">
			            				<div class="row">
			            					<div class="col-lg-4">
			            						<div class="form-group has-info">
				            						<label>Costo</label>
				            						<input type="text" name="costo_e" id="costo_e" class="form-control numeric ccos" value="<?php echo $ultcosto; ?>">
					            				</div>
					            			</div>
														<div class="col-lg-2">
														</div>
														<div class="col-lg-6">
															<a id="plus_precio" name="plus_precio" class="btn btn-primary pull-right" style="margin-top: 50px;"> <i class="fa fa-plus"></i> Agregar Precio</a>
														</div>
			            				</div>
					             	</div>
												<div class="col-lg-12">
			            				<div class="row">
			            					<div class="col-lg-12">
															<table class="table table-striperd table-hover table-bordered">
																<thead>
																	<th class="col-md-2" style='text-align: center'>Costo</th>
																	<th class="col-md-2" style='text-align: center'>Costo mas IVA</th>
																	<th class="col-md-2" style='text-align: center'>P. Venta</th>
																	<th class="col-md-2" style='text-align: center'>P. Venta mas IVA</th>
																	<th class="col-md-2" style='text-align: center'>Utilidad %</th>
																	<th class="col-md-2" style='text-align: center'>Ganancia</th>
																	<th class="col-md-1" style='text-align: center'>Accion</th>
																</thead>
																<tbody id="precios">
																	<?php
																		$sql_precios = _query("SELECT * FROM precio_producto WHERE id_producto = '$id_producto'");
																		$cuenta_p = _num_rows($sql_precios);
																		$lista = "";
																		if($cuenta_p > 0)
																		{
																			while ($row_p = _fetch_array($sql_precios))
																			{
																					$id_precio = $row_p["id_precio"];
																					$costo = $row_p["costo"];
																					$ganancia = $row_p["ganancia"];
																					$porcentaje = $row_p["porcentaje"];
																					$total = $row_p["total"];
																					$total_iva = $row_p["total_iva"];
																					$costo_iva = $row_p["costo_iva"];
																					$lista .= "<tr>";

																					$lista .= "<td style='text-align: right' class='td_costo'><input type='hidden' class='form-control costo_td' id='costo_td' name='costo_td' value='".$costo."'>$ ".number_format($costo,2)."</td>";
																		      $lista .= "<td style='text-align: right' class='td_costo_iva'><input type='hidden' class='form-control costo_td_iva' id='costo_td_iva' name='costo_td_iva' value='".$costo_iva."'>$ ".number_format($costo_iva,2)."</td>";
																					$lista .= "<td style='text-align: right' class='td_precio'><input type='hidden' class='form-control precio_td' id='precio_td' name='precio_td' value='".$total."'>$ ".number_format($total, 2)."</td>";
																		      $lista .= "<td style='text-align: right' class='td_precio_iva'><input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='".$total_iva."'>$ ".number_format($total_iva, 2)."</td>";
																					$lista .= "<td style='text-align: right' class='td_porcentaje'><input type='hidden' class='form-control porcentaje_td' id='porcentaje_td' name='porcentaje_td' value='".$porcentaje."'>".number_format($porcentaje, 2)."%</td>";
																					$lista .= "<td style='text-align: right' class='td_ganancia'><input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value='".$ganancia."'>$ ".number_format($ganancia, 2)."</td>";
																					$lista .= "<td style='text-align: right'><input id='delete' type='button' class='btn btn-success fa delete'  value='&#xf1f8;'></td>";
																					$lista .= "</tr>";


																			}
																			echo $lista;
																		}
																	?>
																</tbody>
															</table>
					            			</div>
			            				</div>
					             	</div>
												<br>
												<div class="col-lg-12">
													<input type="submit" id="btn_eprecio" name="btn_eprecio" value="Guardar" class="btn btn-primary m-t-n-xs pull-right" />
												</div>
		            			</div>
											<div id="lcaracte" class="tab-pane fade"><br><br>
			            			<div class="col-lg-12">
														<a id="plus_item" name="plus_item" class="btn btn-primary m-t-n-xs pull-right" style="margin-top: -25px;"><i class="fa fa-plus"></i> Agregar ítem</a>
					             	</div>
												<br>
												<div class="col-lg-12">
			            				<div class="row">
			            					<div class="col-lg-12">
															<table class="table table-striperd table-hover table-bordered">
																<thead>
																	<th class="col-md-11">Descripción</th>
																	<th class="col-md-1">Acción</th>
																</thead>
																<tbody id="plus_detalle">
																<?php
																	$sql_caracte = _query("SELECT * FROM producto_detalle WHERE id_producto = '$id_producto'");
																	$cue_ca = _num_rows($sql_caracte);
																	if($cue_ca > 0)
																	{
																		$linea = "";
																		while ($row_ca = _fetch_array($sql_caracte))
																		{
																			$id_detalle = $row_ca["id_detalle"];
																			$des_ca = $row_ca["descripcion"];
																			$linea .= "<tr>";
																			$linea .= "<td><input type='hidden' id='item' name='item' class='form-control item' value='".$des_ca."'>".$des_ca."</td>";
																			$linea .= '<td class="Delete"><input  id="delprod" type="button" class="btn btn-danger fa pull-right"  value="&#xf1f8;"></td>';
																			$linea .= "</tr>";
																		}
																		echo $linea;
																	}
																?>
																</tbody>
															</table>
					            			</div>
			            				</div>
					             	</div>
												<br>
												<div class="col-lg-12">
													<input type="submit" id="btn_ecaracte" name="btn_ecaracte" value="Guardar" class="btn btn-primary m-t-n-xs pull-right" />
												</div>
		            			</div>
			                <div id="rotacion" class="tab-pane fade"><br>
			            			<div class="col-lg-12">
			            				<div class="row">
			            					<div class="col-lg-6">
			            						<div class="form-group has-info" id="caja_img">
				            						<img src="<?php echo $img; ?>" alt="" class="img-rounded" style="height: 300px; width: 350px;">
					            				</div>
					            			</div>
														<div class="col-lg-6">
															<form name="formulario_pro" id="formulario_pro" enctype='multipart/form-data' method="POST">
																<div class="row">
																	<div class="col-md-12">
																		<div class="form-group has-info single-line">
																				<label>Buscar Imagen</label>
																				<div class="in_file">
																					<input type="file" name="logo" id="logo" class="file" data-preview-file-type="image">
																				</div>
																				<input type="hidden" name="id_id_p" id="id_id_p" value='<?php echo $id_producto; ?>'>
																				<input type="hidden" name="process" id="process" value="editar_img">
																		</div>
																	</div>
																</div>
															</form>
														</div>
			            				</div>
			            			</div>
												<div class="col-lg-12">
													<input type="submit" id="btn_eimg" name="btn_eimg" value="Guardar" class="btn btn-primary m-t-n-xs pull-right" />
												</div>
			                </div>
			            </div>
			        </div>
				</div>
			</div>
        </div>
    </div>
</div>
<?php
	include_once ("footer.php");
		echo "<script src='js/funciones/funciones_producto.js'></script>";
	}
	else
	{
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function editar_datos()
{
	$id_producto=$_POST["id_producto"];
	$barcode=$_POST["barcode"];
  $descripcion=$_POST["descripcion"];
  $marca=$_POST['marca'];
	$modelo=$_POST['modelo'];

	$serie = $_POST["serie"];
	$categoria = $_POST["categoria"];
	$tiene_garantia=$_POST['tiene_garantia'];
	$descuento = $_POST["descuento"];
	$tiempo_garantia = $_POST["tiempo_garantia"];
	$periodo = $_POST["periodo"];
  $descripcion=trim($descripcion);
	$descripcion=strtoupper($descripcion);

  $name_producto="";
	$fecha_hoy=date("Y-m-d");
  $id_sucursal=$_SESSION["id_sucursal"];


	$descripcion_exit=false;
	$sql_producto=_query("SELECT id_producto FROM productos WHERE descripcion='$descripcion'
		 AND marca='$marca' AND modelo='$modelo' AND barcode='$barcode' AND id_producto!='$id_producto'");
	$row_desc=_num_rows($sql_producto);
	$table = 'productos';
	if ($row_desc == 0)
	{
		$form_data = array(
			'descripcion' => $descripcion,
			'barcode'=>$barcode,
			'actualiza' => $fecha_hoy,
			'marca'=>$marca,
			'modelo'=>$modelo,
			'serie'=>$serie,
			'tiene_garantia'=>$tiene_garantia,
			'id_categoria'=>$categoria,
			'descuento' => $descuento,
			'tiempo_garantia' => $tiempo_garantia,
			'tipo_tiempo' => $periodo,
	 	);
		$where = "id_producto = '".$id_producto."'";
	 	$insertar = _update($table,$form_data,$where);

		if($insertar){
			$xdatos['typeinfo']='Success';
			$xdatos['msg']='Registro modificado con exito!';
			$xdatos['process']='insert';
			$xdatos['id_producto']=$id_producto;
		}
		else
		{
			$xdatos['typeinfo']='Error';
			$xdatos['msg']='Registro no pudo ser modificado';
		}

 	}
 	else
 	{
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Ya existe un producto con estas caracteristicas!!';
 	}
	echo json_encode($xdatos);
}

function editar_precio()
{
	$costo = $_POST["costo"];
	$datos = $_POST["precios"];
	$cuantos = $_POST["cuantos"];
	$cuenta_np = 0;
	$id_producto = $_POST["id_producto"];
	$array = json_decode($datos, true);

	$tabla = "productos";
	$lista_data = array(
		'ultcosto' => $costo,
	);
	$w = "id_producto='".$id_producto."'";
	$upd = _update($tabla, $lista_data, $w);
	if($upd)
	{
		$tablax = "precio_producto";
		$delete = _delete($tablax, $w);
		if($delete)
		{
			foreach ($array as $fila)
			{
				$costo = $fila["costo"];
				$costo_iva = $fila["costo_iva"];
				$ganancia = $fila["ganancia"];
				$porcentaje = $fila["porcentaje"];
				$precio = $fila["precio"];
				$precio_iva = $fila["precio_iva"];

				$datos_lis = array(
					'id_producto' => $id_producto,
					'costo' => $costo,
					'costo_iva' => $costo_iva,
					'ganancia' => $ganancia,
					'porcentaje' => $porcentaje,
					'total' => $precio,
					'total_iva' => $precio_iva,
				);
				$insert_np = _insert($tablax, $datos_lis, $w);
				if($insert_np)
				{
					$cuenta_np += 1;
				}
			}
			if($cuantos == $cuenta_np)
			{
				$xdatos['typeinfo']='Success';
				$xdatos['msg']='Registro editado con exito!';
				$xdatos['process']='editar';
				$xdatos['id_producto']=$id_producto;
			}
		}
		else
		{
			$xdatos['typeinfo']='Error';
			$xdatos['msg']='Error inesperado'._error();
		}
	}
	else
	{
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Error al actualizar el costo'._error();
	}
	echo json_encode($xdatos);
}

function editar_carac()
{
	$datos = $_POST["items"];
	$cuantos = $_POST["cuantos"];
	$id_producto = $_POST["id_producto"];

	$i = 0;
	$tabla_item = "producto_detalle";
	$ww = "id_producto = '".$id_producto."'";
	$delete = _delete($tabla_item, $ww);
	if($delete)
	{
		$array = json_decode($datos, true);
		foreach ($array as $fila)
		{
			$item = $fila["item"];
			$lista_item = array(
				'descripcion' => $item,
				'id_producto' => $id_producto,
			);
			$inser_item = _insert($tabla_item, $lista_item);
			$i += 1;
		}
		if($cuantos == $i)
		{
			$xdatos['typeinfo']='Success';
			$xdatos['msg']='Datos editados correctamente !';
			$xdatos['process']='insert';
		}
		else
		{
			$xdatos['typeinfo']='Error';
			$xdatos['msg']='Error al editar las caracteristicas!';
		}
	}
	else
	{
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Error inesperado!';
	}
	echo json_encode($xdatos);
}

function editar_img()
{
		require_once 'class.upload.php';
		$id_producto = $_POST["id_id_p"];
		if ($_FILES["logo"]["name"]!="")
		{
		$foo = new Upload($_FILES['logo'],'es_ES');
		if ($foo->uploaded) {
				$pref = uniqid()."_";
				$foo->file_force_extension = false;
				$foo->no_script = false;
				$foo->file_name_body_pre = $pref;
			 // save uploaded image with no changes
			 $foo->Process('img/productos/');
			 if ($foo->processed)
			 {
				 $query = _query("SELECT imagen FROM productos WHERE id_producto='$id_producto'");
				 $result = _fetch_array($query);
				 $urlb=$result["imagen"];
				 if($urlb!="")
				 {
						 unlink($urlb);
				 }
				$cuerpo=quitar_tildes($foo->file_src_name_body);
				$cuerpo=trim($cuerpo);
				$url = 'img/productos/'.$pref.$cuerpo.".".$foo->file_src_name_ext;
				$table = 'productos';
				$form_data = array (
				'imagen' => $url,
				);
				$where_clause = "id_producto='".$id_producto."'";
				$editar =_update($table, $form_data, $where_clause);
				if($editar)
				{
					 $xdatos['typeinfo']='Success';
					 $xdatos['msg']='Datos editados correctamente !';
					 $xdatos['process']='edit';
					 $xdatos['img'] = $url;
				}
				else
				{
					 $xdatos['typeinfo']='Error';
					 $xdatos['msg']='Error al editar los datos!'._error();
				}
			 }
			 else
			 {
					$xdatos['typeinfo']='Error';
					$xdatos['msg']='Error al editar la imagen!';
			 }
		}
		else
		{
				$xdatos['typeinfo']='Error';
				$xdatos['msg']='Error al subir la imagen!';
		}
		}
		else
		{
			 $xdatos['typeinfo']='Success';
			 $xdatos['msg']='Datos editados correctamentexs !';
			 $xdatos['process']='edit';
		}
		echo json_encode($xdatos);
}

function costo()
{
	$costo = $_POST["costo"];
	$costo_iva = round($costo * 1.13, 2);
	$datos = $_POST["precios"];
	$cuantos = $_POST["cuantos"];
	$lista = "";
	$array = json_decode($datos, true);
	foreach ($array as $key)
	{
		$porcentaje = $key["porcentaje"];

		$resultado = round($costo * ($porcentaje / 100) , 2);
		$resultado1 = $costo + $resultado;
		$resultado2 = round($resultado1 * 1.13, 2);
		$lista .= "<tr>";
		$lista .= "<td style='text-align: right' class='td_costo'><input type='hidden' class='form-control costo_td' id='costo_td' name='costo_td' value='".$costo."'>$ ".number_format($costo, 2)."</td>";
		$lista .= "<td style='text-align: right' class='td_costo_iva'><input type='hidden' class='form-control costo_td_iva' id='costo_td_iva' name='costo_td_iva' value='".$costo_iva."'>$ ".number_format($costo_iva,2)."</td>";
		$lista .= "<td style='text-align: right' class='td_precio'><input type='hidden' class='form-control precio_td' id='precio_td' name='precio_td' value='".$resultado1."'>$ ".number_format($resultado1, 2)."</td>";
		$lista .= "<td style='text-align: right' class='td_precio_iva'><input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='".$resultado2."'>$ ".number_format($resultado2, 2)."</td>";
		$lista .= "<td style='text-align: right' class='td_porcentaje'><input type='hidden' class='form-control porcentaje_td' id='porcentaje_td' name='porcentaje_td' value='".$porcentaje."'>".number_format($porcentaje, 2)."%</td>";
		$lista .= "<td style='text-align: right' class='td_ganancia'><input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value='".$resultado."'>$ ".number_format($resultado, 2)."</td>";
		$lista .= "<td style='text-align: right'><input id='delete' type='button' class='btn btn-success fa delete'  value='&#xf1f8;'></td>";
		$lista .= "</tr>";
	}
	echo $lista;
}

if(!isset($_POST['process'])){
	initial();
}
else
{
if(isset($_POST['process'])){
switch ($_POST['process']) {
	case 'editar_datos':
		editar_datos();
			break;
	case 'editar_precio':
		editar_precio();
			break;
	case 'editar_img':
		editar_img();
			break;
	case 'costo':
		costo();
		break;
	case 'editar_carac':
		editar_carac();
			break;
	}
}
}
?>
