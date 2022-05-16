<?php
include_once "_core.php";
function initial()
{

    if (!isset($_REQUEST['id_producto'])) {
        $id_producto = -1;
    } else {
        $id_producto = $_REQUEST['id_producto'];
    }
    //permiso del script
    $id_sucursal=$_SESSION["id_sucursal"];
    $id_user=$_SESSION["id_usuario"];
    $admin=$_SESSION["admin"];
    $uri = $_SERVER['SCRIPT_NAME'];
    $filename=get_name_script($uri);
    $links=permission_usr($id_user, "editar_producto.php");
    $img_ruta="img/productos/no_disponible.png";

    // Producto, si existe
    $sql=_query("SELECT productos.barcode,productos.descripcion,proveedores.id_proveedor,proveedores.nombre,inactivo,exento,comentario, id_color, estilo, talla, numera, letra FROM productos INNER JOIN proveedores ON productos.id_proveedor=proveedores.id_proveedor WHERE productos.id_producto = '$id_producto'");
    $row= _fetch_array($sql);
    $barcode = $row["barcode"];
    $descripcion = $row["descripcion"];
    $id_proveedor = $row["id_proveedor"];
    $nombre = $row["nombre"];
    $inactivo= $row["inactivo"];
    $exento = $row["exento"];
    $comentario = $row["comentario"];
    $id_color = $row["id_color"];
    $talla = $row["talla"];
    $estilo = $row["estilo"];
    $numera = $row["numera"];
    $letra = $row["letra"];
    $title = "Datos Generales";
?>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox">
        <?php
            if ($links!='NOT' || $admin=='1' ){
        ?>
            <div class="ibox-title">
                <h5><?php echo $title; ?></h5>
            </div>
            <div class="ibox-content">
                <form name="formulario" id="formulario">
                    <div class="row">
                        <input type="hidden" id="barcode" name="barcode" value="">
                        <div class="col-lg-6">
                            <div class="form-group has-info single-line">
                                <label>Barcode</label> 
                                <input type="text" readonly class="form-control" value="<?php echo $barcode; ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group has-info single-line">
                                <label>Descripción</label> 
                                <input type="text" placeholder="Descripcion" class="form-control" id="descripcion" name="descripcion" value="<?php echo $descripcion; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group has-info single-line">
                                <label>Talla</label> 
                                <input type="text" placeholder="Talla" class="form-control" id="talla" name="talla" value="<?php echo $talla; ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group has-info single-line">
                                <label>Estilo</label> 
                                <input type="text" placeholder="Estilo" class="form-control" id="estilo" name="estilo" value="<?php echo $estilo; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group has-info single-line">
                                <label>Color</label>
                                <select class="col-md-12 color1" style="width:100%" id="color1" name="color1">
                                    <option value="">Seleccione</option>
                                    <?php
                                        $sqld = "SELECT * FROM colores";
                                        $resultd=_query($sqld);
                                        while($tipo = _fetch_array($resultd))
                                        {
                                            echo "<option value='".$tipo["id_color"]."'";
                                            if($id_color == $tipo["id_color"])
                                            {
                                                echo " selected ";
                                            }
                                            echo">".$tipo["nombre"]."</option>";
                                        }
                                    ?>
                                </select>
                               
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group has-info single-line">
                                <label>Letra</label>
                                <select class="col-md-12 select" style="width:100%"  name='letra' id='letra' >
                                    <option value="">Seleccione</option>
                                    <?php
                                        $qletra=_query("SELECT * FROM letras");
                                        while($row_letra=_fetch_array($qletra))
                                        {
                                            $id_letra=$row_letra["letra"];
                                            $nombreltr=$id_letra."| ".$row_letra["descrip"];
                                            echo "<option value='$id_letra'";
                                            if($letra == $id_letra)
                                            {
                                                echo " selected ";
                                            }
                                            echo ">$nombreltr</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group has-info single-line">
                                <label>Numeración</label> 
                                <input type="text" placeholder="Numeracion" class="form-control" id="numeracion" name="numeracion" value="<?php echo $numera; ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group has-info single-line">
                                <label>Proveedor</label>
                                <input type="text" name="proveedor" id="proveedor" size="30" class="form-control"  placeholder="Ingrese criterio de busqueda" data-provide="typeahead">
                                <label id="mostrar_proveedor"><?php echo $nombre; ?></label>
                                <input hidden type="text" id="id_proveedor" name="id_proveedor" value="<?php echo $id_proveedor; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group has-info single-line">
                                <div class="form-group">
                                    <label class="control-label">Inactivo </label>
                                    <div class="checkbox i-checks">
                                        <label>
                                            <input type="checkbox"  id="inactivo" name="inactivo" value="<?php echo $inactivo;?>" <?php if($inactivo){ echo " checked "; }?>>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group has-info single-line">
                                <div class="form-group">
                                    <label class="control-label">Exento de IVA</label>
                                    <div class="checkbox i-checks">
                                        <label>
                                            <input type="checkbox"  id="exento" name="exento" value="<?php echo $exento;?>" <?php if($exento){ echo " checked "; }?>>
                                        </label>
                                    </div>
                                 </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group has-info single-line">
                                <label>Comentario</label>
                                <input type="text" placeholder="Comentario" class="form-control" id="comentario" name="comentario">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="process" id="process" value="edited"><br>
                    <div>
                        <input type="hidden" name="id_producto" id="id_producto" value="<?php echo $id_producto;?>">
                       <input type="submit" id="submit1" name="submit1" value="Guardar" class="btn btn-primary m-t-n-xs" />
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
echo "<script src='js/funciones/funciones_producto.js'></script>";
          } //permiso del script
    else {
        echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
    }
}
function editar1()
{
    $id_producto=$_POST["id_producto"];
    $id_proveedor=$_POST["id_proveedor"];
    $barcode=$_POST["barcode"];
    $descripcion=$_POST["descripcion"];
    $comentario=$_POST["comentario"];
    $inactivo=$_POST["inactivo"];
    $exento=$_POST["exento"];
    $estilo=$_POST['estilo'];
    $id_color=$_POST['id_color'];
    $talla=$_POST['talla'];
    $numera=$_POST['numera'];
    $letra=$_POST['letra'];

    $descripcion=trim($descripcion);
    $descripcion=strtoupper($descripcion);
    $comentario=trim($comentario);
    $comentario=strtoupper($comentario);
    
    $name_producto="";
    
    $exento=$_POST["exento"];
    $fecha_hoy=date("Y-m-d");

    $descripcion_exit=false;
    $sql_producto=_query("SELECT id_producto FROM productos WHERE descripcion='$descripcion' AND id_color='$id_color' AND talla='$talla' AND estilo='$estilo' AND id_producto!='$id_producto'");
    $row_desc=_num_rows($sql_producto);
    $table = 'productos';
    if ($row_desc == 0)
    {
        $form_data = array(
            'id_proveedor' => $id_proveedor,
            'descripcion' => $descripcion,
            'comentario' => $comentario,
            'inactivo' => $inactivo,
            'exento' => $exento,
            'actualiza' => $fecha_hoy,
            'estilo'=>$estilo,
            'id_color'=>$id_color,
            'talla'=>$talla,
            'numera'=>$numera,
            'letra'=>$letra,
        );
        $where = "id_producto='".$id_producto."'";
        $update = _update($table,$form_data,$where);
        if($update)
        {
            $xdatos['typeinfo']='Info';
            $xdatos['msg']='Registro actualizado con exito!';
            $xdatos['process']='edited';
            $xdatos['id_producto']=$id_producto;
        }
        else
        {
            $xdatos['typeinfo']='Error';
            $xdatos['msg']='Registro no actualizado!';
        }
                
    }
    else
    {
        $xdatos['typeinfo']='Error';
        $xdatos['msg']='Ya existe otro producto con estas caracteristicas!!';
    }
	echo json_encode($xdatos);
}

if (!isset($_POST['process'])) {
    initial();
} else {
    if (isset($_POST['process'])) {
        switch ($_POST['process']) {
          case 'edited':
            editar1();
            break;
          }
    }
}
?>
