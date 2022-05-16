<?php
include_once "_core.php";
function initial() {

  if(!isset($_REQUEST['id_producto']))
  {
    $id_producto =-1;
  }
  else
  {
    $id_producto = $_REQUEST['id_producto'];
  }
  $id_sucursal=$_SESSION["id_sucursal"];
 //permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,"editar_producto.php");

  //producto
  $sql =_query("SELECT descripcion,estilo,precio1,precio2,precio3,ultcosto FROM productos WHERE id_producto=$id_producto");
  $row =_fetch_array($sql);

$title="Precios y costos de los productos";
?>
    <div class="row">
      <div class="col-lg-12">
        <div class="ibox">
			  <?php
				//permiso del script
				if ($links!='NOT' || $admin=='1' ){
					?>
          <div class="ibox-title">  <h5><?php echo $title;?></h5></div>
          <div class="ibox-content">
            <div class="row">
              <div class="col-lg-12">
                <div class="form-group">
                  <input type="hidden" name="id_product" id="id_product" value="<?php echo $id_producto;?> ">
                    <label class="control-label" for="Descrip">Descripción Producto</label>
                    <input type="text" class="form-control" id="descripcion2" name="descripcion2" readonly value="<?php echo $row["descripcion"]." ".$row["estilo"]; ?>">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6">
                <div class="form-group has-info single-line"><label>Último costo</label> <input type="text" placeholder="Ultimo costo" class="form-control" id="ultcosto" name="ultcosto" value='<?php echo $row['ultcosto'] ?>'></div>
              </div>
              <div class="col-sm-6">
                <div class="form-group has-info single-line"><label>Precio 1</label> <input type="text" placeholder="Precio 1" class="form-control" id="precio1" name="precio1" value='<?php echo $row['precio1'] ?>'></div>
              </div>
            </div>
            <div class="row">

             <div class="col-sm-6">
              <div class="form-group has-info single-line"><label>Precio 2</label> <input type="text" placeholder="Precio 2" class="form-control" id="precio2" name="precio2" value="<?php echo $row['precio2'] ?>"></div>
             </div>
             <div class="col-sm-6">
              <div class="form-group has-info single-line"><label>Precio 3</label> <input type="text" placeholder="Precio 3" class="form-control" id="precio3" name="precio3" value="<?php echo $row['precio3'] ?>"></div>
             </div>
           </div>

              <div class="row">
                <div class="col-sm-3">
                  <div class="form-group">
                    <button id="btnPrecios" name="btnPrecios" class='btn btn-primary'>Guardar</button>
                  </div>
                </div>
            </div>
					</div><!--div class='ibox-content'-->
			   </div><!--<div class='ibox float-e-margins' -->
        </div> <!--div class='col-lg-12'-->
				</div> <!--div class='row'-->
<?php
echo "<script src='js/funciones/funciones_editar_producto2.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}


function guardar_precios(){
	$id_sucursal=$_SESSION["id_sucursal"];
	$id_producto=$_POST['id_producto'];
  $precio1=$_POST['precio1'];
  $precio2=$_POST['precio2'];
  $precio3=$_POST['precio3'];
  $ultcosto=$_POST['ultcosto'];


	$sql_producto="SELECT id_producto FROM productos WHERE id_producto=$id_producto";

    $result=_query($sql_producto);
    $count=_num_rows($result);

    $table= 'productos';
	if ($count>0)
  {
		$where_clause= "id_producto='" . $id_producto. "'";
		$form_data = array(
      'precio1'=>$precio1,
      'precio2'=>$precio2,
      'precio3'=>$precio3,
      'ultcosto'=>$ultcosto,
		);

	    $insertar = _update($table,$form_data, $where_clause);
      if($insertar){
  			$xdatos['typeinfo']='Info';
  			$xdatos['msg']='Registro actualizado con exito!';
  			$xdatos['process']='edited';
  		}
  		else{
  			$xdatos['typeinfo']='Error';
  			$xdatos['msg']='Registro no actualizado!';
  		}
	}
  else
  {
      $xdatos['typeinfo']='Error';
      $xdatos['msg']='Registro de producto desconocido';
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

          case 'guardar_precios' :
              guardar_precios();
              break;
  }
}
}
?>
