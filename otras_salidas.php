<?php
include_once "_core.php";

function initial() {


	$_PAGE = array ();
	$_PAGE ['title'] = 'Salida de Inventario de Producto';
	$_PAGE ['links'] = null;
	$_PAGE ['links'] = null;
    $_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
   // $_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
    /*$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';*/
    $_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/style_table_ped.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/style_table3.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	$id_sucursal=$_SESSION['id_sucursal'];
	$sql="SELECT * FROM productos";

	$result=_query($sql);
	$count=_num_rows($result);
	$sql2="SELECT * FROM colores";
    $result2=_query($sql2);
    $count2=_num_rows($result2);
    $array2= array(-1=>"Seleccione");
    for ($y=0;$y<$count2;$y++) {
        $row2=_fetch_array($result2);
        $id2=$row2['id_color'];
        $description2=$row2['nombre'];
        $array2[$id2] = $description2;
    }
	//permiso del script
	$fecha_actual=date("Y-m-d");
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	//permiso del script

?>
<style media="screen">
      .fixed_header{
      width: 100%;
      table-layout: fixed;
      border-collapse: collapse;
      }

      .fixed_header tbody{
        display:block;
        width: 100%;
        overflow: auto;
        height: 120px;
      }

      .fixed_header thead tr {
         display: block;
      }

      .fixed_header tbody tr:hover {
        background-color: rgba(199, 199, 199, 0.38) !important
      }

      .fixed_header thead {

      }

      .fixed_header th, .fixed_header td {
       height: 30px;
      }
    </style>


<!--//////////////////////////////////////////////////////////////////////////-->

<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
              <?php
                //permiso del script
                if ($links!='NOT' || $admin=='1') {
              ?>
                <div class="ibox-content">
                    <div class="widget">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="row">
                                      <div class="col-lg-12">
                                        <div class="form-group has-info single-line text-center">
                                          <label>DESCARGA DE INVENTARIO</label>
                                        </div>
                                      </div>
                                    </div>
                                    	<div class="row">
											<div class="col-lg-6">
												<div class="form-group has-info single-line">
													<label>Seleccione Tipo de Salida</label>
													<select  name='tipo_entrada' id='tipo_entrada' class="form-control">
														<option value='0'>SELECCIONE</option>
														<option value='1'>AVERÍO</option>
														<option value='2'>EXTRAVÍO</option>
													</select>
					                            </div>
				                            </div>
										</div>
                                    <div class='row' id='form_invent_inicial'>
                                      <div class='col-lg-6'>
                                          <div class='form-group has-info single-line'>
                                            <label>Fecha:</label> 
                                            <input type='text' class='datepick form-control' value='<?php echo $fecha_actual;?>' id='fecha1' name='fecha1'>
                                          </div>
                                      </div>
                                      <div class="col-lg-6">
                                        <div class='form-group has-info single-line'>
                                          <label>Concepto</label>
                                          <input type="text" id="concepto" name="concepto" class="concepto form-control" data-provide="typeahead">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="widget-header" id="caja_y" hidden>
                                        <div class="row">
                                          <div class="col-lg-1">
              
                                          </div>
                                          <div class="col-lg-2">
                                            <label class="text-navy"><i class="fa fa-search"></i> Buscar producto</label>
                                          </div>
                                          <div class="form-group col-md-2">
                                            <label>Limite Busqueda&nbsp;</label>
                                          </div>
                                          <div class="form-group col-md-2">
                                            <input type="text"  class='form-control'  id="limite" value='400' />
                                          </div>
                                          <div class="form-group col-md-2">
                                            <label>Reg. Encontrados&nbsp;</label>
                                          </div>
                                          <div class="form-group col-lg-2">
                                            <input type="text"  class='form-control' id='reg_count' value='0' readOnly />
                                          </div>
                                        </div>
                                        <!--///-->
                                    </div>
                                    <br>
                                    <div class="" id="caja_x" hidden>
                                      <div class=" single-line">
                                        <div class="row">
                                          <div class="col-md-2">
                                            <div class="form-group">
                                              <label>Barcode</label>
                                              <input type="text" id="barcode" class='form-control' placeholder="Barcode" />
                                            </div>
                                          </div>
                                          <div class="col-md-4">
                                            <div class="form-group">
                                              <label>Descripción</label>
                                              <input type="text" id="keywords" class='form-control' placeholder="Descripción" />

                                            </div>
                                          </div>
                                          <div class="col-md-2">
                                            <div class="form-group">
                                              <label>Estilo</label>
                                              <input type="text" id="estilo" class='form-control' placeholder="Estilo" />
                                            </div>
                                          </div>
                                          <div class="col-md-2">
                                            <div class="form-group">
                                              <div><label>Color&nbsp;</label></div>
                                              <?php
                                              // se va filtrar por descripcion, estilo, talla, color, barcode
                                              $nombre_select="select_colores";
                                              $id_val=-1;
                                              $style='';
                                              $select=crear_select2($nombre_select, $array2, $id_val, $style);
                                              echo $select; ?>
                                            </div>
                                          </div>
                                          <div class="col-md-2">
                                            <div class="form-group">
                                              <label>Talla</label>
                                              <input type="text" id="talla" class='form-control' placeholder="Talla" />
                                            </div>
                                          </div>
                                        </div>
                                        <div class='row' id='encabezado_buscador'>

                                        </div>
                                        <div class="post-wrapper">
                                          <div class="loading-overlay">
                                            <div class="overlay-content" id='reg_count0'>Cargando.....</div>
                                          </div>

                                        </div>
                                      </div>

                                      <div class="row">
                                        <div class="col-md-12">&nbsp;&nbsp;
                                            <h3 class="text-navy" id='title-table' style="text-align: center">&nbsp;Lista  de Productos Encontrados</h3>
                                        </div>
                                      </div>
                                      <section>
                                        <div class='widget-content' id="content">
                                          <div class="row">
                                            <div class="col-md-12">
                                              <table class="fixed_header table-striped" id="loadtable">
                                                <thead>
                                                  	<tr>
								                        <th class="text-success col-lg-1" style="text-align: center;">ID</th>
								                        <th class="text-success col-lg-4">Descripción</th>
								                        <th class="text-success col-lg-1">Estilo</th>
								                        <th class="text-success col-lg-1">Color</th>
								                        <th class="text-success col-lg-1">Talla</th>
								                        <th class="text-success col-lg-1">Letra</th>
								                        <th class="text-success col-lg-1">Precio Vta.</th>
								                        <th class="text-success col-lg-1">Existencia</th>
								                        <th class="text-success col-lg-1">Acci&oacute;n</th>
							                     	</tr>
                                                </thead>

                                                <tbody id="mostrardatos">

                                                </tbody>
                                              </table>
                                            </div>
                                          </div>
                                        </div>
                                          
                                         <input type="hidden" name="autosave" id="autosave" value="false-0">
                                      </section>
                                      <br>
                                      <div class="widget-content">
                                        <header>
                                          <h3 class="text-navy" style="text-align: center;">Ingreso a Inventario de Productos</h4>
                                        </header>
                                        <table class="table table-hover table-striped table-responsive" id="inventable">
                                          	<tr class=''>
						                        <th class="text-success col col-lg-1">ID</th>
						                        <th class="text-success col col-lg-5">Descripci&oacute;n</th>
						                        <th class="text-success col col-lg-1">Estilo</th>
						                        <th class="text-success col col-lg-1">Color</th>
						                        <th class="text-success col col-lg-1">Talla</th>
						                        <th class="text-success col col-lg-1">Precio Vta.</th>
						                        <th class="text-success col col-lg-1">Existencias</th>
						                        <th class="text-success col col-lg-1">Cantidad</th>
						                        <th class="text-success col col-lg-1">Acci&oacute;n</th>
				                      		</tr>
                                        </table>
                                        <div class='row'>
                                          <div class='col-md-12 pull-right' id="totaltexto">
                                            <h3 class='text-danger'></h3>&nbsp;</div>
                                        </div>
                                                <!-- /widget-content -->
                                      </div>
                                      <input type="hidden" name="process" id="process" value="insert">
                                      
                                    </div>
                                    <br>
                                    <div>
                                        <input type="submit" id="submit1" name="submit1" value="Guardar" class="btn btn-primary m-t-n-xs" />
                                        <input type='hidden' name='urlprocess' id='urlprocess' value="<?php echo $filename ?> ">
                                    </div>
                                    <!--///////////-->

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

echo "<script src='js/plugins/typehead/bootstrap3-typeahead.js'></script>";
echo "<script src='js/funciones/funciones_otras_salidas.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function insertar(){
	//- hacer edicion de entradas
	// facturacion
	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];
	$id = $_POST['id'];
	$fecha_movimiento= $_POST['fecha_movimiento'];

	$total_compras = $_POST['total_compras'];

	$id_sucursal=$_SESSION['id_sucursal'];
	$concepto = $_POST["concepto"];
	//$empleado = $_SESSION['id_user'];

	$sql_tipo = _query("SELECT * FROM correlativos WHERE alias = 'DES' AND id_sucursal = '$id_sucursal'");
    $row_tipo = _fetch_array($sql_tipo);
    $numerop = $row_tipo["numero"];
    $idtipodoc = $row_tipo["id_correlativo"];
    $alias_tt = $row_tipo["alias"];

	//$insertar1=false;
	$insertar2=false;
	$numero_doc = "";
	if ($id=='1')
	{
		$numero_doc=$numero_doc.'_AV';
		$tipo_entrada_salida='AVERIA';
		$observaciones=$tipo_entrada_salida;
	}
	//2 VENCIMIENTO
	if ($id=='2')
	{
		$numero_doc=$numero_doc.'_VN';
		$tipo_entrada_salida='VENCIMIENTO';
		$observaciones='Salida de Producto por fecha de vencimiento caducada ';
	}

	//4 CONSUMO
	if ($id=='3')
	{
			$numero_doc=$numero_doc.'_CI';
			$tipo_entrada_salida='CONSUMO INTERNO';
			$observaciones='Salida de Producto para uso Interno ';
	}

	$table1= 'kardex';
	$form_data1 = array(	
	'fechadoc' => $fecha_movimiento,
	'tipo_salida' => $tipo_entrada_salida,
	'alias_tipodoc' => $alias_tt,
	'id_tipodoc' => $idtipodoc,
	'numero_doc' => $numerop,
	'concepto' => $concepto,
	);

	if ($cuantos>0)
	{
		$insertar1 = _insert($table1,$form_data1 );
		if($insertar1)
		{
			$id_mov = _insert_id();
			$listadatos=explode('#',$stringdatos);
			for ($i=0;$i<$cuantos ;$i++){
				list($id_producto,$cantidad)=explode('|',$listadatos[$i]);
				$sql1="SELECT * FROM kardex WHERE id_producto='$id_producto' AND fechadoc = '$fecha_movimiento' AND alias_tipodoc = '$alias_tt'";
				//echo $sql1;
				$stock1=_query($sql1);
				$row1=_fetch_array($stock1);
				$nrow1=_num_rows($stock1);

				$sql2="SELECT productos.id_producto, stock.existencias, productos.id_proveedor FROM productos,stock WHERE productos.id_producto='$id_producto' AND productos.id_producto=stock.id_producto AND stock.id_sucursal='$id_sucursal'";
				$stock2=_query($sql2);
				$row2=_fetch_array($stock2);
				$nrow2=_num_rows($stock2);
				$existencias=$row2['existencias'];
				$proveedor = $row2['id_proveedor'];
				 
				 
				$cant_total=$existencias-$cantidad;
	     		 
	     		
				echo _error();
				$table2= 'stock';
				$form_data2 = array(
				'existencias' => $cant_total,
				);
				//}

			//echo $cantidad."\n";
			//echo $nrow1."\n";
			//echo $nrow2."\n";

			
				
				if($cantidad > 0)
	            {
	              
	                $tabla_x = "detalle_mov";
	                $form_mov = array(
	                  'id_mov' => $id_mov, 
	                  'id_producto' => $id_producto,
	                  'cant' => $cantidad,
	                  );
	                $insertarx = _insert($tabla_x, $form_mov);
	                if($insertarx)
	                {
	                	if ($nrow2>0){
							$where_clause="WHERE id_producto='$id_producto' AND  id_sucursal='$id_sucursal'";
							$insertar2 = _update($table2,$form_data2, $where_clause );
						}
	                }
	              
	              echo _error();
	            }
				}//for
		//} //if $id=2



		    if ($insertar1 && $insertar2)
		    {
		    	$tabla_tipodoc = "correlativos";
			    $numerop = $numerop +1;
			    $form_tipodoc = array(
			       	'numero' => $numerop, 
			    );
			    $where_tipodoc = "id_correlativo = '".$idtipodoc."'";
			    $update_tipodoc = _update($tabla_tipodoc, $form_tipodoc, $where_tipodoc);

		    	$xdatos['typeinfo']='Success';
		       	$xdatos['msg']='Registro de Inventario Actualizado !';
		       	$xdatos['process']='insert';
		    }
		    else
		    {
			   $xdatos['typeinfo']='Error';
			   $xdatos['msg']='Registro de Inventario no pudo ser Actualizado !';
			}
		}
		echo _error();
	}
	
//} //if $id=2



    if ($insertar1 && $insertar2)
    {
    	$tabla_tipodoc = "correlativos";
	    $numerop = $numerop +1;
	    $form_tipodoc = array(
	       	'numero' => $numerop, 
	    );
	    $where_tipodoc = "id_correlativo = '".$idtipodoc."'";
	    $update_tipodoc = _update($tabla_tipodoc, $form_tipodoc, $where_tipodoc);

    	$xdatos['typeinfo']='Success';
       	$xdatos['msg']='Registro de Inventario Actualizado !';
       	$xdatos['process']='insert';
    }
    else
    {
	   $xdatos['typeinfo']='Error';
	   $xdatos['msg']='Registro de Inventario no pudo ser Actualizado !';
	}


	echo json_encode($xdatos);
}
function consultar_stock()
{
    $id_producto = $_REQUEST['id_producto'];
    $id_usuario=$_SESSION["id_usuario"];
    $id_sucursal=$_SESSION['id_sucursal'];
    $sql_user="select * from usuario where id_usuario='$id_usuario'";

    $sql2="select * from stock where id_producto='$id_producto' and id_sucursal ='$id_sucursal'";
    $stock2=_query($sql2);
    $row2=_fetch_array($stock2);
    $nrow2=_num_rows($stock2);
    $existencias=$row2['existencias'];


    $sql3="select p.talla,p.estilo,p.descripcion,p.id_color,p.exento,c.nombre,p.ultcosto,p.precio1,p.ultcosto
	FROM productos AS p
		JOIN colores AS c ON (p.id_color=c.id_color)
		where p.id_producto='$id_producto'
		";


    $result3=_query($sql3);
    $count3=_num_rows($result3);
    if ($count3>0) {
        $row3=_fetch_array($result3);
        $descripcion=$row3['descripcion'];
        $color=$row3['nombre'];
        $talla=$row3['talla'];
        $ultcosto=$row3['ultcosto'];
        $precio1=$row3['precio1'];
        $exento=$row3['exento'];
        $estilo=$row3['estilo'];
        $xdatos['descrip'] =$descripcion;
        $xdatos['color'] = $color;
        $xdatos['talla'] = $talla;
        $xdatos['exento'] = $exento;
        $xdatos['ultcosto'] = $ultcosto;
        $xdatos['precio1'] = $precio1;
        $xdatos['existencias'] = $existencias;
        $xdatos['estilo'] = $estilo;
        /*
        $cp=$row3['costopro'];
        $pv_base=$row3['precio1'];

        */
        /*
        $xdatos['costo_prom'] = $cp;
        $xdatos['pv_base'] = $pv_base;
        $xdatos['existencias'] = $existencias;

        */

        echo json_encode($xdatos); //Return the JSON Array
    }
}
function traerdatos()
{
    $id_sucursal=$_SESSION['id_sucursal'];
    $keywords = $_POST['keywords'];
    $estilo = $_POST['estilo'];
    $talla= $_POST['talla'];
    $id_color= $_POST['id_color'];
    $barcode= $_POST['barcode'];
    $limite= $_POST['limite'];
    //if(strlen(trim($keywords))>=0) {
    $sqlJoined="SELECT pr.id_producto,pr.descripcion, pr.precio1, pr.costopro, pr.talla,
		 pr.exento, pr.estilo, pr.barcode, co.id_color,co.nombre,st.existencias,  pr.letra
		 FROM productos AS pr JOIN colores as co ON co.id_color=pr.id_color INNER JOIN stock as st ON pr.id_producto=st.id_producto
		";
    $sqlParcial=get_sql($keywords, $id_color, $estilo, $talla, $barcode, $limite,$id_sucursal);
    $sql_final= $sqlJoined." ".$sqlParcial." ";
    $query = _query($sql_final);

    $num_rows = _num_rows($query);
    if ($num_rows > 0) {
        while ($row = _fetch_array($query)) {
            $id_producto = $row['id_producto'];
            $descripcion=$row["descripcion"];
            $estilo = $row['estilo'];
            $exento = $row['exento'];
            $cp = $row['costopro'];
            $precio = $row['precio1'];
            $talla = $row['talla'];
            $id_color2=$row['id_color'];
            $nombre = $row['nombre'];
            $barcode = $row['barcode'];
            $existencia=$row['existencias'];
            $letra = $row["letra"];
            //<i class="fa fa-check"></i>
            $btnSelect='<input type="button" id="btnSelect" class="btn btn-primary fa" value="&#xf00c;">'; ?>
      <tr>
        <td style="width: 100px; text-align: center;"><h5><?php echo $id_producto; ?></h5></td>
        <td style="width: 340px;"><h5><?php echo $descripcion; ?></h5></td>
        <td style="width: 85px;"><h5 class='text-success'><?php echo $estilo; ?></h5></td>
        <td style="width: 85px;"><h5 class='text-success'><?php echo $nombre; ?></h5></td>
        <td style="width: 85px;"><h5 class='text-success'><?php echo $talla; ?></h5></td>
        <td style="width: 85px;"><h5 class='text-success'><?php echo $letra; ?></h5></td>
        <td style="width: 90px;"><h5><?php echo $precio; ?></h5></td>
        <td style="width: 90px;"><h5><?php echo $existencia; ?></h5></td>
        <td style="width: 10px;"><h5 class='text-success'><?php echo $btnSelect; ?></h5></td>
      </tr>
      <?php
      /*echo "<tr>";
      echo "<td>".$id_producto."</td>";
      echo "<td>".$descripcion."</td>";
      echo "<td>".$estilo."</td>";
      echo "<td>".$nombre."</td>";
      echo "<td>".$talla."</td>";
      echo "<td>".$cp."</td>";
      echo "<td>".$existencia."</td>";
      echo "<td>".$btnSelect."</td>";*/
        }
    }
    echo '<input type="hidden" id="cuantos_reg"  value="'.$num_rows.'">';
}
function get_sql($keywords, $id_color, $estilo, $talla, $barcode, $limite,$id_sucursal)
{
    $andSQL='';
    $whereSQL="  WHERE st.id_sucursal='$id_sucursal' AND st.existencias>'0'  ";

    $keywords=trim($keywords);
    //$andSQL.= " AND co.id_color='$id_color'";

    if (!empty($barcode)) {
        $andSQL.= " AND  pr.barcode LIKE '{$barcode}%'";
    } else {
        if (!empty($keywords)) {
            $andSQL.= "AND  pr.descripcion LIKE '%".$keywords."%'";
            if (!empty($estilo)) {
                $andSQL.= " AND pr.estilo LIKE '{$estilo}%' ";
            }
            if (!empty($talla)) {
                $andSQL.= " AND pr.talla LIKE '%{$talla}%'";
            }
            if ($id_color!=-1) {
                $andSQL.= " AND co.id_color='$id_color'";
            }
        }

        if (empty($keywords)  && !empty($estilo)) {
            $andSQL.= "AND  pr.estilo LIKE '%".$estilo."' ";
            if (!empty($talla)) {
                $andSQL.= " AND pr.talla LIKE '%".$talla."%' ";
            }
            if ($id_color!=-1) {
                $andSQL.= " AND co.id_color='$id_color'";
            }
        }
        if (empty($keywords)  && empty($estilo) && !empty($talla)) {
            $andSQL.= "AND pr.talla LIKE '%".$talla."%' ";
            if ($id_color!=-1) {
                $andSQL.= " AND co.id_color='$id_color'";
            }
        }
        if (empty($keywords)  && empty($estilo) && empty($talla) && ($id_color!=-1)) {
            $limite=1000;
            $andSQL.= " AND co.id_color='".$id_color."'";
        }
    }

    $orderBy=" ";
    $limitSQL=" LIMIT ".$limite;
    $orderBy=" ORDER BY pr.id_producto,pr.descripcion, pr.barcode,pr.estilo,pr.talla,co.id_color ";

    $sql_parcial=$whereSQL.$andSQL.$orderBy.$limitSQL;
    return $sql_parcial;
}

//functions to load
if(!isset($_REQUEST['process'])){
	initial();
}
//else {
if (isset($_REQUEST['process'])) {


	switch ($_REQUEST['process']) {
	case 'insert':
		insertar();
		break;
	case 'consultar_stock':
		consultar_stock();
		break;
	case 'traerdatos':
        traerdatos();
        break;
	}

 //}
}
?>
