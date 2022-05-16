<?php
include_once "_core.php";
function initial() {
  if(!isset($_REQUEST['id_producto'])){
   $id_producto =-1;
  }
  else{
    $id_producto = $_REQUEST['id_producto'];
  }
  $id_sucursal=$_SESSION["id_sucursal"];
  $title="Ubicacion";
 //permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

  $sql=_query("SELECT id_posicion FROM productos WHERE id_producto='$id_producto'");
  $ver=_fetch_array($sql);

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
              <div class="col-lg-4">
                <div class="form-group has-info single-line">
                  <label>Almacen</label>
                  <select class="col-md-12 select"  style="width:100%" id="select_almacen" name="select_almacen">
                    <option value="">Seleccione..</option>
                    <?php
                    if ($ver['id_posicion']==0) {
                      # code...
                      $sqld = "SELECT * FROM almacen";
                      $resultd=_query($sqld);
                      while($almacen = _fetch_array($resultd))
                      {
                          echo "<option value='".$almacen['id_almacen']."'";
                          echo">".$almacen['descripcion']."</option>";
                      }
                    }
                    else {
                      # code...
                      $sqlalmacen = _query("SELECT id_almacen FROM posicion WHERE id_posicion='$ver[id_posicion]'");
                      $alma=_fetch_array($sqlalmacen);
                      $sqld = "SELECT * FROM almacen";
                      $resultd=_query($sqld);
                      while($almacen = _fetch_array($resultd))
                      {
                          echo "<option value='".$almacen['id_almacen']."'";
                          if ($alma['id_almacen']==$almacen['id_almacen']) {
                            # code...
                            echo " selected";
                          }
                          echo">".$almacen['descripcion']."</option>";
                      }
                    }

                    ?>
                  </select>
                </div>
              </div>
                <div class="col-lg-4">
                  <div class="form-group has-info single-line">
                    <label>Estante</label>
                    <select class="col-md-12 select"  style="width:100%" id="select_estante" name="select_estante">
                      <?php
                      if ($ver['id_posicion']==0) {
                        # code...
                      }
                      else {
                        # code...
                        $sqlestante = _query("SELECT id_estante FROM posicion WHERE id_posicion='$ver[id_posicion]'");
                        $est=_fetch_array($sqlestante);
                        $sqld = "SELECT * FROM estante";
                        $resultd=_query($sqld);
                        while($estante = _fetch_array($resultd))
                        {
                            echo "<option value='".$estante['id_estante']."'";
                            if ($est['id_estante']==$estante['id_estante']) {
                              # code...
                              echo " selected";
                            }
                            echo">".$estante['descripcion']."</option>";
                        }
                      }

                      ?>
                    </select>
                  </div>

                </div>
                <div class="col-lg-4">
                  <div class="form-group has-info single-line">
                    <label>Posición</label>
                      <select class="col-md-12 select" style="width:100%" id="select_ubicacion" name="select_ubicacion">
                        <?php
                        if ($ver['id_posicion']==0) {
                          # code...
                        }
                        else {
                          # code...
                          $sqlposicion = _query("SELECT posicion FROM posicion WHERE id_posicion='$ver[id_posicion]'");
                          $pos=_fetch_array($sqlposicion);
                          $sqld = "SELECT * FROM posicion";
                          $resultd=_query($sqld);
                          while($posicion = _fetch_array($resultd))
                          {
                              echo "<option value='".$posicion['id_posicion']."'";
                              if ($pos['posicion']==$posicion['id_posicion']) {
                                # code...
                                echo " selected";
                              }
                              echo">".$posicion['posicion']."</option>";
                          }
                        }

                        ?>
                      </select>
                  </div>
                </div>
            </div>
						<div class="row">
							<div class="col-sm-3">
                <input hidden type="text" id="id_producto" name="id_producto" value="<?php echo $id_producto; ?>">
								<div class="form-group">
									<button id="btn" name="btn" class='btn btn-primary'>Guardar</button>
									</div>
							</div>
            </div>

					</div><!--div class='ibox-content'-->
			   </div><!--<div class='ibox float-e-margins' -->
        </div> <!--div class='col-lg-12'-->
				</div> <!--div class='row'-->
<?php
echo "<script src='js/funciones/funciones_editar_producto4.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function guardar(){

		$id_producto=$_POST['id_producto'];
    $id_posicion=$_POST['id_posicion'];
    $table="productos";
		$form_data = array(
		'id_posicion'=>$id_posicion,
		);
    $where_clause = "id_producto='" . $id_producto . "'";
		$insertar = _update($table,$form_data,$where_clause);

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
function genera_select(){
  $id_almacen=$_POST['id'];
  $sql="SELECT * FROM estante WHERE id_almacen='$id_almacen'";
  $result=_query($sql);
  $count=_num_rows($result);
if ($count>0){
  echo '<option value="" selected>Seleccione</option>';
  for ($y=0;$y<$count;$y++){
    $row=_fetch_array($result);
    $id1=$row['id_estante'];
    $description=$row['descripcion'];
    echo '<option value="'.$id1.'">'.$description.'</option>';
  }
  }
  else
  {
    echo '<option value="-1" selected>NO ASIGNADO</option>';
  }
}
function genera_selectt(){
  $id1=$_POST['id1'];
  $id2=$_POST['id2'];
  $sql="SELECT * FROM posicion WHERE id_almacen='$id1' AND id_estante='$id2' ";
  $result=_query($sql);
  $count=_num_rows($result);
  if ($count>0){
    echo '<option value="" selected>Seleccione</option>';
    for ($y=0;$y<$count;$y++){
      $row=_fetch_array($result);
      $id=$row['id_posicion'];
      $description=$row['posicion'];
      echo '<option value="'.$id.'">'.$description.'</option>';
    }
  }
  else
  {
    echo '<option value="-1" >NO ASIGNADO</option>';
  }
}

if(!isset($_POST['process'])){
	initial();
}
else{
  if(isset($_POST['process'])){
    switch ($_POST['process']){
      case 'guardar':
      guardar();
      break;
      case 'genera_select' :
          genera_select();
          break;
      case 'genera_selectt' :
          genera_selectt();
          break;
	   }
   }
}
?>
