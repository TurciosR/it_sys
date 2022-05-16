<?php
include_once "_core.php";
include ('num2letras.php');
include ('facturacion_funcion_imprimir.php');
//include("escpos-php/Escpos.php");
function initial() {

$id_producto = $_REQUEST['id_producto'];
$sql="SELECT p.id_producto,p.descripcion, pro.nombre FROM productos AS p LEFT JOIN proveedores AS pro ON p.id_proveedor = pro.id_proveedor WHERE p.id_producto='$id_producto'";
$result = _query($sql);

//permiso del script
//permiso del script
$id_user=$_SESSION["id_usuario"];
$admin=$_SESSION["admin"];

$uri = $_SERVER['SCRIPT_NAME'];
$filename=get_name_script($uri);
$links=permission_usr($id_user,$filename);
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title">Detalles de Producto</h4>
</div>
<div class="modal-body">
        <div class="row">
          <div class="col-lg-12">
    				<?php
    						//permiso del script
    						if ($links!='NOT' || $admin=='1' ){
    					?>
    					<?php
    						$row = _fetch_array($result);
    						$descripcion=$row['descripcion'];
    	          $nombre=$row['nombre'];
                echo "<h2>".$descripcion."</h2>";
                echo "<h3>".$nombre."</h3>";
        				?>
    			</div>
          <hr>
          <div class="col-lg-12">
            <table class="table table-bordered">
              <thead>
                <th class="col-md-2" style='text-align: center'>Costo</th>
                <th class="col-md-2" style='text-align: center'>Costo + IVA</th>
                <th class="col-md-2" style='text-align: center'>P. Venta</th>
                <th class="col-md-2" style='text-align: center'>P. Venta + IVA</th>
                <th class="col-md-2" style='text-align: center'>Utilidad %</th>
                <th class="col-md-2" style='text-align: center'>Ganancia</th>
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
<div class="modal-footer">
<?php
echo "<input type='hidden' nombre='id_producto' id='id_producto' value='$id_producto'>";
	//echo "<button type='button' class='btn btn-primary' id='btnPrintBcodes'>Imprimir Barcode</button>
        echo "<button type='button' class='btn btn-default' data-dismiss='modal'>Cerrar</button>
	</div><!--/modal-footer -->";
}
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}


function buscarprodcant(){
	$id_producto= trim($_POST['id_producto']);
  $qty= $_POST['qty'];
	$sql0="SELECT  pr.precio1, pr.numera, pr.estilo,
	pr.descripcion, pr.talla, c.nombre,pr.id_proveedor
	FROM  productos AS pr
	JOIN colores AS c ON (pr.id_color=c.id_color)
	WHERE pr.id_producto='$id_producto'";

	$result = _query($sql0);
	$array_prod = array();

	$numrows= _num_rows($result);

	$n=0;
 for ($i=0;$i<$numrows;$i++){
	 $row = _fetch_array($result);

	$talla=$row['talla'];
	$color=$row['nombre'];
	$descripcion=$row['descripcion'];
	$precio=$row['precio1'];
	$rango=$row['numera'];
	$id_proveedor=$row['id_proveedor'];
$estilo=$row['estilo'];
  $array_prod[] = array(
 		 'id_producto' => $id_producto,
		 'descripcion' => $descripcion,
		 'precio'=>  $precio,
		 'talla'=> $talla,
		 'estilo'=> $estilo,
		 'color' =>  $color,
		 'rango' =>  $rango,
     'id_proveedor' =>  $id_proveedor,
		'cantidad' => $qty,
		'fin' => "|",
  );
	$n+=1;
 }
 //Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
 $info = $_SERVER['HTTP_USER_AGENT'];
 if(strpos($info, 'Windows') == TRUE)
	 $so_cliente='win';
 else
	 $so_cliente='lin';
	//$xdatos['array_prod']=$array_prod;
	//directorio de script impresion cliente
	$sql_dir_print="SELECT *  FROM config_dir";
	$result_dir_print=_query($sql_dir_print);
	$row_dir_print=_fetch_array($result_dir_print);
	$dir_print=$row_dir_print['dir_print_script'];
	$shared_printer_barcode=$row_dir_print['shared_print_barcode'];
$array_prod[] = array(
	 'id_producto' => -1,
	 'descripcion'=> 'CONF',
	 'shared_printer_barcode' =>$shared_printer_barcode,
	 'dir_print' =>$dir_print,
	 'sist_ope' =>$so_cliente,
);
	echo json_encode ($array_prod); //Return the JSON Array
//echo json_encode ($array2); //Return the JSON Array
}
if(!isset($_REQUEST['process'])){
	initial();
}
//else {
if (isset($_REQUEST['process'])) {
	switch ($_REQUEST['process']) {
		case 'buscarprodcant' :
				buscarprodcant();
				break;
	}

 //}
}
?>
