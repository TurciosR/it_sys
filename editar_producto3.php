<?php
include_once "_core.php";
function initial() {
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
	$img_ruta="img/productos/no_disponible.png";

  //producto
  $sql=_query("SELECT estilo,id_color,talla,numera FROM productos WHERE id_producto=$id_producto");
  $row=_fetch_array($sql);
  $title="Caracteristicas del producto"
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

                    <input type="hidden" name="id_producto" id="id_producto" value="<?php echo $id_producto;?>">
                  <input type="hidden" name="urlprocess" id="urlprocess" value="<?php echo $filename;?>">
                  <input type="hidden" name="tipo" id="tipo" value="1">

                  <div class="row">
                    <div class="col-sm-12">
                      <div class="form-group has-info single-line"><label>Estilo</label> <input type="text" placeholder="Estilo" class="form-control" id="estilo" name="estilo" value='<?php echo $row['estilo']; ?>'></div>
                    </div>
                  </div>

                  <div class="row">
                   <div class="col-sm-6">
                     <div class="form-group has-info single-line">
                     <label>Seleccione Color</label>
                      <div class="input-group col-lg-12">
                        <select class="col-md-12 color1" style="width:100%" id="color1" name="color1">
                          <?php
                              $sqld = "SELECT * FROM colores";
                              $resultd=_query($sqld);
                              while($tipo = _fetch_array($resultd))
                              {
                                if ($tipo["id_color"]==$row['id_color']) {
                                  # code...
                                  echo "<option value='".$tipo["id_color"]."' selected";
                                }
                                else {
                                  # code...
                                  echo "<option value='".$tipo["id_color"]."'";
                                }


                                  echo">".$tipo["nombre"]."</option>";
                              }
                          ?>
                        </select>
                       </div>
                      </div>
                   </div>
                   <div class="col-sm-6">
                    <div class="form-group has-info single-line"><label>Talla</label> <input type="text" placeholder="Talla" class="form-control" id="talla" name="talla" value='<?php echo $row['talla']; ?>'></div>
                   </div>
                  </div>

                  <div class="row">

                    <div class="col-sm-6">
                      <div class="form-group has-info single-line">
                      <label>Seleccione Letra</label>
                        <div class="input-group col-lg-12">
                          <select class="col-md-12 select" style="width:100%"  name='letra' id='letra' >
                          <!--option value=''>Seleccione</option-->
                             <?php
                             $qcategoria=_query("SELECT * FROM letras");
                             while($row_categoria=_fetch_array($qcategoria))
                             {
                                 $id_categoria=$row_categoria["letra"];
                                 $nombrecat=$id_categoria."|".$row_categoria["descrip"];
                                 echo "
                             <option value='$id_categoria'>$nombrecat</option>
                             ";
                             }
                             ?>
                            </select>
                           </div>
                         </div>
                    </div>
                   <div class="col-sm-6">
                    <div class="form-group has-info single-line"><label>Numeración</label> <input type="text" placeholder="Numeracion" class="form-control" id="numeracion" name="numeracion" value='<?php echo $row['numera']; ?>'></div>
                   </div>
                  </div>

                        <div class="row">
                          <div class="col-xs-8">
        										<div id='botones'>
        											<button role="button" id="guardarCaracteristicas" name="guardarCaracteristicas" class="btn btn-primary"><i class="fa fa-check"></i> Guardar</button>
        										</div>
        									</div>
                        </div>
										 </div><!--div class='ibox-content'-->
			        		</div><!--<div class='ibox float-e-margins' -->
                    <!--div class="title-action" id='botones'>
                    <input type="button" id="fianly2" name="finally" value="Finalizar" class="btn btn-success m-t-n-xs "/>
                  </div-->
					 		</div> <!--div class='col-lg-12'-->
				 	</div> <!--div class='row'-->

<?php

echo "<script src='js/funciones/funciones_editar_producto3.js'></script>";

} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function insert(){
  $id_sucursal=$_SESSION["id_sucursal"];
	$id_producto=$_POST['id_producto'];
  $estilo=$_POST['estilo'];
  $id_color=$_POST['id_color'];
  $talla=$_POST['talla'];
  $numera=$_POST['numera'];


	$sql_producto="SELECT id_producto FROM productos WHERE id_producto=$id_producto";

    $result=_query($sql_producto);
    $count=_num_rows($result);

    $table= 'productos';
	if ($count>0){
		$where_clause= "id_producto='" . $id_producto. "'";
		$form_data = array(
      'estilo'=>$estilo,
      'id_color'=>$id_color,
      'talla'=>$talla,
      'numera'=>$numera,
		);

	    $insertar = _update($table,$form_data, $where_clause);
      if($insertar){
  			$xdatos['typeinfo']='Info';
  			$xdatos['msg']='Registro guardado con exito!';
  			$xdatos['process']='edited';
  		}
  		else{
  			$xdatos['typeinfo']='Error';
  			$xdatos['msg']='Registro no editado ';
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
  case 'insert':
  		insert();
  		break;
	}
}
}
?>
