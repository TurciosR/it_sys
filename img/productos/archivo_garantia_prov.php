<?php
include ("_core.php");
function initial(){
	$fecha_hoy=date('Y-m-d');
	$id_garantia = $_REQUEST["id_garantia"];

	$sqlJoined=" SELECT *";
  $sqlFrom=" FROM  garantia_proveedor";
	$sqlWhere=" WHERE id_garantia='$id_garantia'";

	 $sql_final=$sqlJoined.$sqlFrom.$sqlWhere;
	$query = _query($sql_final);
	$num_rows = _num_rows($query);
	$row=_fetch_array($query);
	$imagen=$row['image_url'];
	//para subir archivos !!!!
	$ruta0= $_SERVER['DOCUMENT_ROOT'] ;
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
	$img_ruta=$imagen;
	if($imagen!=''){
		$img_ruta=$imagen;
	}
	else{
		$img_ruta="img/productos/no_disponible.png";
	}
	$ruta_fin=$dirfin;
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Documentos de Garantia</h4>
</div>
<div class="modal-body">
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row" id="row1">
			<div class="col-lg-12">
			<div class="widget-content">
						<div class="row">
						<div class="col-md-12">
							<!--Utilizando jasny solo para el control del input file-->
							<form name="formulario1" id="formulario1">
								<div class="col-md-12">
				 <div class="form-group has-info">
						 <label class="control-label">Seleccionar Imagen</label>
						 <input id="producto-img" name="producto-img[]" class="file" type="file" multiple data-min-file-count="1">
				 </div>
			 </div>
			 <div class="col-md-12">
<div class="form-group has-info">
									<input type="submit" id="submit2" name="submit2" value="Guardar" class="btn btn-primary m-t-n-xs" />
								</div>
							</div>
	              	<input type="hidden" name="process" id="process" value="edited">
	              	<input type="hidden" name="ruta" id="ruta" value="<?php echo $img_ruta;?>">
	              	<input type="hidden" name="ipserver" id="ipserver" value="<?php echo $host;?>">
	              	<input type="hidden" name="ruta_archivo" id="ruta_archivo" value="<?php echo $ruta_fin;?>">
									<!-- HIDDEN INPUT-->
										<input type="hidden" name="fecha" id="fecha" value="<?php echo $fecha_hoy;?>">
										<input type="hidden" name="id_garantia" id="id_garantia" value="<?php echo $id_garantia;?>">
	              	<input type="hidden" name="descripcion" id="descripcion" value="descrip">
							</form>
						</div>
					</div>
	</div>
	<div class="col-lg-12">
		<div class="form-group">
		<div class="col-md-6"></div>
		</div>
	</div>
	</div>
</div>
</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal" id="btnEsc">Cerrar</button>
</div>
<!--/modal-footer -->

<?php
  echo "<script src='js/funciones/garantia_prov.js'></script>";
}

function upload_s()
{
	require_once 'class.upload.php';
	$fecha_hoy=date("d-m-Y");
	$foo = new Upload($_FILES['archivo'],'es_ES');
	if ($foo->uploaded)
	{
		$posterior ="_".uniqid();
		$anterior=$fecha_hoy."_";
		$foo->file_force_extension = false;
		$foo->no_script = false;
		$foo->file_name_body_pre = $anterior;
		$foo->file_name_body_add=$posterior;
		$directory='docs/';
		   // save uploaded image with no changes

		$foo->Process($directory);
		if ($foo->processed)
		{
		   	$fecha = $_POST["fecha"];
		   	$id_garantia = $_POST["id_garantia"];
		   /*	$nombre = $_POST["nombre"];
		   	$descripcion = $_POST["descripcion"];*/
		   	$archivo = $_FILES["archivo"]["name"];
		   //	$url = quitar_tildes($directory.$anterior.$foo->file_src_name_body.$posterior.".".$foo->file_src_name_ext);
			 	$url = $directory.$anterior.$foo->file_src_name_body.$posterior.".".$foo->file_src_name_ext;
			$table = "garantia_proveedor";
			$form_data = array(
				'image_url' => $url,
			);
			$where_clause="WHERE id_garantia='$id_garantia'";
			$update = _update($table, $form_data,$where_clause);
			if($update)
			{
				$xdatos['typeinfo']='Success';
				$xdatos['msg']='Archivo guardado';
				$xdatos['process']='insertar';
		 	}
		 	else
		 	{
				$xdatos['typeinfo']='Warning';
				$xdatos['msg']='Archivo no pudo ser subido'._error();
		 	}
		}
		else
		{
			$xdatos ['typeinfo'] = 'Error';
			$xdatos ['msg'] = "El archivo no pudo ser subido ".$foo->error;
			$xdatos ['Error'] = $foo->error;
		}
	 	echo json_encode($xdatos);
	}
}
if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'upload_s' :
				upload_s();
				break;
		}
	}
}

?>
