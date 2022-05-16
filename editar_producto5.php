<?php
include_once "_core.php";

function initial(){
  if(!isset($_REQUEST['id_producto'])){
 	 $id_producto =-1;
  }
  else{
 	$id_producto = $_REQUEST['id_producto'];
 }
 //permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

  $sql="SELECT * FROM producto
    WHERE id_producto='$id_producto'";
  $result=_query($sql);
  $count=_num_rows($result);

if($count>0){
  for($i=0;$i<$count;$i++){
    $row=_fetch_array($result);
    $descripcion=$row['descripcion'];
    $imagen = $row['imagen'];
    $id_posicion = $row['id_posicion'];
  }
}

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
  <div class="row">
      <div class="col-lg-12">
          <div class="ibox">
							<?php
						//permiso del script
						if ($links!='NOT' || $admin=='1' ){
					?>
          <div class="ibox-title"><h5>Imágenes Producto</h5>  </div>
              <div class="ibox-content">
								<div class="row">
									<div class="col-md-8">
									 <form name="formulario1" id="formulario1">
                     <div class="col-md-12">
                         <div class="form-group has-info">
                             <label class="control-label">Seleccionar Imagen</label>
                             <input id="producto-img" name="producto-img[]" class="file" type="file" multiple data-min-file-count="1">
                         </div>
                     </div>
                         <input type="hidden" name="process" id="process" value="edited">
                         <input type="hidden" name="ruta" id="ruta" value="<?php echo $img_ruta;?>">
                         <input type="hidden" name="ipserver" id="ipserver" value="<?php echo $host;?>">
                         <input type="hidden" name="ruta_archivo" id="ruta_archivo" value="<?php echo $ruta_fin;?>">
                         <input type="hidden" name="id_producto" id="id_producto" value="<?php echo $_REQUEST['id_producto']?>">
                         <input type="hidden" name="descripcion" id="descripcion" value="descrip">
										</form>
									</div>
								</div>
								</div><!--div class='ibox-content'-->
							 </div><!--<div class='ibox float-e-margins' -->
							</div> <!--div class='col-lg-12'-->
						</div> <!--div class='row'-->
<?php
  echo "<script src='js/funciones/funciones_editar_producto5.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}
function upload_s(){
require_once 'class.upload.php';
$foo = new Upload($_FILES['archivo'],'es_ES');
if ($foo->uploaded) {
	$pref = uniqid()."_";
	$foo->file_force_extension = false;
	$foo->no_script = false;
	$foo->file_name_body_pre = $pref;
   // save uploaded image with no changes
   $foo->Process('img/producto/');
   if ($foo->processed) {
   	$id_producto = $_POST["id_producto"];

   	$archivo = $_FILES["archivo"]["name"];
   	$url = 'img/productos/'.$pref.$foo->file_src_name_body.".".$foo->file_src_name_ext;
  	$table = 'persona';
	  $form_data = array (
      'imagen' => $url
    );
    $where_clause = "id_producto='" . $id_producto . "'";
    $updates = _update( $table, $form_data, $where_clause );

	   if($updates){
		    $xdatos ['typeinfo'] = 'Success';
        $xdatos ['msg'] = "El archivo se subio con exito";
		      $xdatos ['url'] = $url;
	   }
	    else{
	       $xdatos ['typeinfo'] = 'Error';
         $xdatos ['msg'] = "El archivo no se guardo en la base de datos";
	      }
    }
    else{
     $xdatos ['typeinfo'] = 'Error';
     $xdatos ['msg'] = "El archivo no pudo ser subido, error: ".$foo->error;
     $xdatos ['error'] = $foo->error;
  }
  }else{
    $xdatos ['typeinfo'] = 'Error';
    $xdatos ['msg'] = "El archivo no pudo ser subido, error: ".$foo->error;
    $xdatos ['error'] = $foo->error;
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
	case 'upload_s':
		upload_s()();
		break;
	}
}
}
?>
