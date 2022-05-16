<?php
include_once "_core.php";

function initial()
{
	$title='Agregar Producto';
	$_PAGE = array ();
	$_PAGE ['title'] = $title;
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
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
	$fecha_hoy=date("Y-m-d");
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
									<a id="btn_img" name="btn_img" class="btn btn-primary m-t-n-xs pull-right"><i class="fa fa-image"></i> Agregar Imagen</a>
	            </div>
	            <div class="ibox-content">
                        <div class="row">
                           	<div class="col-lg-6">
                              	<div class="form-group has-info single-line">
                              		<label>Descripción</label>
                              		<input type="text" placeholder="Descripcion" class="form-control upper" id="descripcion" name="descripcion"><br>
                              	</div>
                            </div>
                            <div class="col-lg-6" hidden>
                      			<div class="form-group has-info single-line">
															<label>Proveedor</label>
															<input type="text" name="proveedor" id="proveedor" size="30" class="form-control"  placeholder="Ingrese criterio de busqueda" data-provide="typeahead">
															<label id="mostrar_proveedor"></label>
															<input hidden type="text" id="id_proveedor" name="id_proveedor">
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
															<input type="text" name="serie" id="serie" class="form-control" value="">
														</div>
													</div>
                   		</div>
											<div class="row">
												<div class="col-lg-3">
														<div class="form-group has-info single-line">
															<label>Marca</label>
															<input type="text" placeholder="Marca" class="form-control upper" id="marca" name="marca" value=""><br>
													</div>
												</div>
												<div class="col-lg-3">
														<div class="form-group has-info single-line">
																<label>Modelo</label>
																<input type="text" placeholder="Modelo" class="form-control upper" id="modelo" name="modelo" value=""><br>
														</div>
												 </div>
												 <div class="col-lg-3">
														<div class="form-group has-info single-line">
																<label>Barcode</label>
																<input type="text" placeholder="Barcode" class="form-control upper" id="barcode" name="barcode" value=""><br>
														</div>
												 </div>
												 <div class="col-lg-3">
														 <div class="form-group has-info single-line">
																 <div class="form-group">
																		 <label class="control-label">Tiene garantia </label>
																		 <div class="checkbox i-checks">
																				 <label>
																						 <input type="checkbox"  id="tiene_garantia" name="tiene_garantia" value='0'>
																				 </label>
																		 </div>
																 </div>
														 </div>
												 </div>
											</div>
                   		<div class="row">
													<div class="col-lg-6">
														<div class="form-group has-info single-line">
															<label>Costo</label>
															<input type="text" placeholder="Ultimo costo" class="form-control numeric ccos" id="ultcosto" name="ultcosto">
														</div>
													</div>
													<div class="col-lg-3">
			              					<div class="form-group has-info single-line">
			              						<label>Descuento %</label>
			              						<input type="text" placeholder="Descuento" class="form-control" id="descuento" name="descuento">
			              					</div>
			            	 			</div>
													<div class="col-lg-3" id="tg" hidden>
														<div class="form-group has-info single-line">
														<label>Tiempo Garantia</label>
														<div class="row">
															<div class="col-md-6">
				              						<input type="text" placeholder="" class="form-control" id="tiempo_garantia" name="tiempo_garantia">
															</div>
															<div class="col-md-6">
				              						<select class="select form-control" name="perido" id="periodo">
																		<option value="0">Dia/s</option>
																		<option value="1">Mes/es</option>
																		<option value="2">Año/s</option>
				              						</select>
															</div>
														</div>
			            	 				</div>
		                			</div>
											</div>
											<div class="row">
												<div class="col-lg-6">
													<h4><b>Precios producto</b></h4>
												</div>
												<div class="col-lg-6">
													<a id="plus_precio" name="plus_precio" class="btn btn-primary pull-right" > <i class="fa fa-plus"></i> Agregar Precio</a>
												</div>
											</div>
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

														</tbody>
													</table>
												</div>
											</div>
											<br>
											<hr>
											<div class="row">
												<div class="col-lg-6">
													<h4><b>Caracteristicas producto</b></h4>
												</div>
												<div class="col-lg-6">
													<a id="plus_item" name="plus_item" class="btn btn-primary m-t-n-xs pull-right"><i class="fa fa-plus"></i> Agregar ítem</a>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<table class="table table-striperd table-hover table-bordered">
														<thead>
															<th class="col-md-11">Descripción</th>
															<th class="col-md-1">Acción</th>
														</thead>
														<tbody id="plus_detalle">

														</tbody>
													</table>
												</div>
											</div>
		            	<div class="row" hidden>
             				<div class="col-lg-2">
              					<div class="form-group has-info single-line">
              						<label>Descuento %</label>
              						<input type="text" placeholder="Descuento" class="form-control" id="descuento" name="descuento">
              					</div>
            	 			</div>
            	 			<div class="col-lg-4">
												<div class="form-group has-info single-line">
													<label>Comentario</label>
													<input type="text" placeholder="Comentario" class="form-control upper" id="comentario" name="comentario">
												</div>
										</div>
           				</div>
									<div class="row">
										<div class="col-lg-12">
											<input type="hidden" name="process" id="process" value="insert">
											<input type="hidden" name="origin" id="origin" value="">
											<input type="submit" id="btn_confirmar" name="btn_confirmar" value="Guardar" class="btn btn-primary m-t-n-xs pull-right" />
											<!--<a id="duplicar" name="duplicar" class="btn btn-primary m-t-n-xs">Guardar y Duplicar</a>-->
										</div>
									</div>

									<div class='modal fade' id='viewModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
										<div class='modal-dialog'>
											<div class='modal-content'>
												<div class="modal-header">
													<button type="button" class="close" id='cerrar_ven' data-dismiss="modal"
													aria-hidden="true">&times;</button>
													<h4 class="modal-title">Agregar Imagen de Producto</h4>
												</div>
												<div class="modal-body">
													<div class="wrapper wrapper-content  animated fadeInRight">
											            <form name="formulario_pro" id="formulario_pro" enctype='multipart/form-data' method="POST">
											              <div class="row">
											                <div class="col-md-12">
											                  <div class="form-group has-info single-line">
											                      <label>Producto</label>
											                      <input type="file" name="logo" id="logo" class="file" data-preview-file-type="image">
																						<input type="hidden" name="id_id_p" id="id_id_p">
																						<input type="hidden" name="process" id="process" value="insert_img">
											                  </div>
											                </div>
											              </div>
											            </form>
														</div>
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-primary" id="btnGimg">Guardar</button>
														<button type="button" class="btn btn-default bb" data-dismiss="modal">Cerrar</button>
													</div>
											</div><!-- /.modal-content -->
										</div><!-- /.modal-dialog -->
									</div><!-- /.modal -->
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

function insertar(){
	$id_producto=$_POST["id_producto"];
	$id_proveedor=$_POST["id_proveedor"];
	$barcode=$_POST["barcode"];
  $descripcion=$_POST["descripcion"];
  $comentario=$_POST["comentario"];
  $ultcosto=$_POST['ultcosto'];
  $descuento=$_POST['descuento'];
	$marca=$_POST['marca'];
	$modelo=$_POST['modelo'];
	$serie = $_POST["serie"];
	$categoria = $_POST["categoria"];
	$descuento = $_POST["descuento"];
	$tiempo_garantia = $_POST["tiempo_garantia"];
	$periodo = $_POST["periodo"];
	$datos = $_POST["precios"];
	$array_json=$_POST['items'];

	$n = 0;
	$tiene_garantia = $_POST["tiene_garantia"];

    $descripcion=trim($descripcion);
		$descripcion=strtoupper($descripcion);
		$comentario=strtoupper(trim($comentario));
		//$comentario=strtoupper($comentario);
    $name_producto="";
		$fecha_hoy=date("Y-m-d");

		$descripcion_exit=false;
		$sql_producto=_query("SELECT id_producto FROM productos WHERE descripcion='$descripcion'
			 AND marca='$marca' AND modelo='$modelo' AND id_producto!='$id_producto'");
		$row_desc=_num_rows($sql_producto);
		$table = 'productos';
		if ($row_desc == 0){
			$form_data = array(
			'id_proveedor' => $id_proveedor,
			'barcode' => $barcode,
			'descripcion' => $descripcion,
			'comentario' => $comentario,
			'creado' => $fecha_hoy,
			'actualiza' => $fecha_hoy,
	 		'ultcosto'=>$ultcosto,
			'marca'=>$marca,
			'modelo'=>$modelo,
			'tiene_garantia'=>$tiene_garantia,
			'serie'=>$serie,
			'id_categoria'=>$categoria,
			'descuento' => $descuento,
			'tiempo_garantia' => $tiempo_garantia,
			'tipo_tiempo' => $periodo,
			);
	 		$insertar = _insert($table,$form_data);
			$id_producto2= _insert_id();
			if($insertar){
				$tabla_precios = "precio_producto";
				$array = json_decode($datos, true);
				foreach ($array as $fila)
				{
					$costo = $fila["costo"];
					$costo_iva = $fila["costo_iva"];
					$ganancia = $fila["ganancia"];
					$porcentaje = $fila["porcentaje"];
					$precio = $fila["precio"];
					$precio_iva = $fila["precio_iva"];

					$lista = array(
						'id_producto' => $id_producto2,
						'costo' => $costo,
						'costo_iva' => $costo_iva,
						'ganancia' => $ganancia,
						'porcentaje' => $porcentaje,
						'total' => $precio,
						'total_iva' => $precio_iva,
					);
					$insert_precio = _insert($tabla_precios, $lista);
					if($lista)
					{
						$n += 1;
					}
				}
				$array1 = json_decode($array_json, true);
				foreach ($array1 as $fila1)
				{
					$item = $fila1["item"];
					$tabla_item = "producto_detalle";
					$lista_item = array(
						'descripcion' => $item,
						'id_producto' => $id_producto2,
					);
					$inser_item = _insert($tabla_item, $lista_item);
				}

				$xdatos['typeinfo']='Success';
				$xdatos['msg']='Registro guardado con exito!';
				$xdatos['process']='insert';
				$xdatos['id_producto']=$id_producto2;
			}
			else{
				$xdatos['typeinfo']='Error';
				$xdatos['msg']='Registro no guardado ';
			}
 		}
 		else{
			$xdatos['typeinfo']='Error';
			$xdatos['msg']='Ya existe un producto con estas caracteristicas!!';
 		}
	echo json_encode($xdatos);
}
function autocomplete()
{
  $query = $_REQUEST['query'];
  	 $sql="SELECT id_proveedor, nombre FROM proveedores WHERE nombre LIKE '%$query%'";
  	 $result = _query($sql);
  	$array_prod = array();
  	while ($row = mysqli_fetch_assoc($result)) {
  			$array_prod[] =$row['id_proveedor']."|".$row['nombre'];
  	}
  	echo json_encode ($array_prod); //Return the JSON Array
}
function cons_prod(){
	$id_producto = $_POST["id"];
	$sql = _query("SELECT * FROM productos WHERE id_producto='$id_producto'");
	$datos = _fetch_array($sql);
	$xdatos["descripcion"] = $datos["descripcion"];
	$xdatos["precio1"] = $datos["precio1"];
	$xdatos["precio2"] = $datos["precio2"];
	$xdatos["precio3"] = $datos["precio3"];
	$xdatos["ultcosto"] = $datos["ultcosto"];
	$xdatos["id_proveedor"] = $datos["id_proveedor"];
	$sql2 = _query("SELECT nombre FROM proveedores WHERE id_proveedor='".$datos["id_proveedor"]."'");
	$datos2 = _fetch_array($sql2);
	$xdatos["proveedor"] = $datos2["nombre"];
	$xdatos["descuento"] = $datos["descuento"];
	echo json_encode($xdatos);
}
function precios()
{
  $costo = $_POST["costo"];
	$costo_iva = round($costo * 1.13, 2);
  $lista = "";
  $sql_porcentaje = _query("SELECT * FROM porcentajes WHERE estado != 0");
  $cuenta = _num_rows($sql_porcentaje);
  if($cuenta > 0)
  {
    while ($row_por = _fetch_array($sql_porcentaje))
    {

      $id_porcentaje = $row_por["id_porcentaje"];
      $porcentaje = $row_por["porcentaje"];

      $resultado = round($costo * ($porcentaje / 100) , 2);
      $resultado1 = $costo + $resultado;
      $resultado2 = round($resultado1 * 1.13, 2);
      $lista .= "<tr>";

      $lista .= "<td style='text-align: right' class='td_costo'><input type='hidden' class='form-control costo_td' id='costo_td' name='costo_td' value='".$costo."'>$ ".number_format($costo,2)."</td>";
      $lista .= "<td style='text-align: right' class='td_costo_iva'><input type='hidden' class='form-control costo_td_iva' id='costo_td_iva' name='costo_td_iva' value='".$costo_iva."'>$ ".number_format($costo_iva,2)."</td>";
      $lista .= "<td style='text-align: right' class='td_precio'><input type='hidden' class='form-control precio_td' id='precio_td' name='precio_td' value='".$resultado1."'>$ ".number_format($resultado1, 2)."</td>";
      $lista .= "<td style='text-align: right' class='td_precio_iva'><input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='".$resultado2."'>$ ".number_format($resultado2, 2)."</td>";
			$lista .= "<td style='text-align: right' class='td_porcentaje'><input type='hidden' class='form-control porcentaje_td' id='porcentaje_td' name='porcentaje_td' value='".$porcentaje."'>".number_format($porcentaje,2)."%</td>";
			$lista .= "<td style='text-align: right' class='td_ganancia'><input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value='".$resultado."'>$ ".number_format($resultado, 2)."</td>";
      $lista .= "<td style='text-align: right'><input id='delete' type='button' class='btn btn-success fa delete'  value='&#xf1f8;'></td>";
      $lista .= "</tr>";
    }
  }
  echo $lista;
}
function insert_img()
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
					 $xdatos['msg']='Datos guardados correctamente !';
					 $xdatos['process']='edit';
				}
				else
				{
					 $xdatos['typeinfo']='Error';
					 $xdatos['msg']='Error al guardar los dartos!'._error();
				}
			 }
			 else
			 {
					$xdatos['typeinfo']='Error';
					$xdatos['msg']='Error al guardar la imagen!';
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
			 $xdatos['msg']='Datos guardados correctamente !';
			 $xdatos['process']='edit';
		}
		echo json_encode($xdatos);
}

if(!isset($_POST['process'])){
	initial();
}
else
{
if(isset($_POST['process'])){
switch ($_POST['process']) {
	case 'insert':
		insertar();
		break;
	case 'auto' :
		autocomplete();
		break;
	case 'consp' :
		cons_prod();
		break;
	case 'precios' :
		precios();
		break;
	case 'insert_img':
		insert_img();
		break;
	}
}
}
?>
