<?php
include_once "_core.php";
include('num2letras.php');
include('facturacion_funcion_imprimir.php');
function initial()
{
    $_PAGE = array();
    $_PAGE ['title'] = 'Administrar Productos';
    $_PAGE ['links'] = null;
    $_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/style2.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/pagination.css" rel="stylesheet">';
      $_PAGE ['links'] .= '<link href="css/columns.css" rel="stylesheet">';
    include_once "header.php";
    include_once "main_menu.php";
    include('Pagination.php');
    //permiso del script
    $id_user=$_SESSION["id_usuario"];
    $admin=$_SESSION["admin"];

    $uri = $_SERVER['SCRIPT_NAME'];
    $filename=get_name_script($uri);
    $links=permission_usr($id_user, $filename);


    //crear array proveedores
    $sql0="SELECT * FROM proveedores";
    $result0=_query($sql0);
    $count0=_num_rows($result0);
    $array0 =array(-1=>"Seleccione");
    for ($x=0;$x<$count0;$x++) {
        $row0=_fetch_array($result0);
        $id0=$row0['id_proveedor'];
        $description=$row0['nombre'];
        $array0[$id0] = $description;
    }
    ?>
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
          <div class="ibox ">
					<?php
            //permiso del script
            if ($links!='NOT' || $admin=='1') {
          ?>
					<div class="ibox-content">
            <div>
            <?php
              $filename1='agregar_producto.php';
              $link1=permission_usr($id_user,$filename1);
              if ($link1!='NOT' || $admin=='1' )
                echo "<a href='agregar_producto.php' class='btn btn-primary' role='button'><i class='fa fa-plus icon-large'></i> Agregar Producto</a>";
            ?>
          </div>
						<?php
              //VENTA
              $fecha_actual=date("Y-m-d"); ?>
          <div class="col-lg-12">
          </div>
					<div class="widget">
						<div class="row">
							 <div class="col-md-12">
								 	<div class="row">
									 <div class="widget-header">
										 <div class="row">
										 <div class="col-md-4">&nbsp;&nbsp;
										 <i class="fa fa-th-list"> </i>
										 <h3 class="text-navy" id='title-table'>&nbsp;Productos por Sucursal</h3>
										 </div>
										 <div class="form-group col-md-4">
											 <!--label>Limite Busqueda&nbsp;
											 <input type="text"  class='form-control input_header_panel'  id="limite" value=400 /-->
										 </div>

										 <div class="form-group col-md-3">
											 <label>Reg. Encontrados&nbsp;
											 <input type="text"  class='form-control input_header_panel' id='reg_count' value=0 readOnly /></label>
										 </div>

										 </div>
									    </div>
										 <div class="widget-content">
										  <div class="row">
                      <div class="col-md-2" style="margin-left: 10px;">
                       <div class="form-group">
                         <input type="text" id="barcode" class='form-control' placeholder="Código Barra" />
                       </div>
                      </div>
										 	<div class="col-md-3">
											 	<div class="form-group">
												 	<input type="text" id="keywords" class='form-control' placeholder="Descripción"/>
											 	</div>
										 	</div>

										 	<div class="col-md-2">
											 	<div class="form-group">
												 	<input type="text" id="marca" class='form-control' placeholder="marca" />
											 	</div>
										 	</div>
                      <div class="col-md-2">
										 		<div class="form-group">
												 	<input type="text" id="modelo" class='form-control' placeholder="modelo" />
										 		</div>
										 	</div>
                      <div class="col-md-2">
										 		<div class="form-group">
												 	<input type="text" id="serie" class='form-control' placeholder="serie" />
										 		</div>
										 	</div>
										 	<div class="col-md-2">
										 	<div class="form-group">
											 	<?php
                                                    // se va filtrar por descripcion, serie, marca, color, barcode
                            $nombre_select="select_proveedores";
                            $id_val=-1;
                            $style='';
                            $select=crear_select2($nombre_select, $array0, $id_val, $style);
                            echo $select; ?>
										 		</div>
										 	</div>
										 </div>
                     <div class='row' id='encabezado_buscador'>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>&nbsp;Ordenar </label>
                        <select id="sortBy" onchange="searchFilter()">
                            <option value="asc">Ascendente</option>
                            <option value="desc">Descendente</option>
                        </select>
                  </div>
                </div>
                <div class="col-md-3">&nbsp;&nbsp;</div>
                <div class="col-md-3">&nbsp;&nbsp;</div>
                <div class="col-md-2 pull-right">
                  <div class="form-group">
                    <label>Registros </label>
                    <select id="records" onchange="searchFilter()">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                        <option value="500">500</option>
                    </select>
                  </div>
                  </div>

                  </div>
                  <div class="row">
                      <div class="loading-overlay col-md-6">
                        <div class="overlay-content text-warning" id='reg_count0'>Cargando.....</div>
                      </div>
                  </div>
									 </div>


						</div>
            </div>
            <div  class='widget-content' id="content">
												<div class="row">
											<div class="col-md-12">

												<table class="table table-striped" id='loadtable'>
													<thead class='thead1'>
														<tr class='tr1'>
														<th class="text-success col5 th1" >Id</th>
														<th class="text-success col25 th1">Descripcion</th>
														<th class="text-success col8 th1">Fecha Ini. Gar</th>
                            <th class="text-success col8 th1">Fecha Fin. Gar</th>
														<th class="text-success col8 th1">Costo</th>
                            <th class="text-success col8 th1">P.V.</th>
                            <th class="text-success col8 th1">Estado</th>
                            <th class="text-success col8 th1">Serie</th>
                            <th class="text-success col15 th1">Proveedor</th>
                            <th class="text-success col7 th1">Acci&oacute;n</th>


												</tr>
												</thead>
													<tbody class='tbody1 ' id="mostrardatos">
													</tbody>
												</table>
										</div>
									</div>
									<!--/div-->

	</div>
		</div>
		 <div id="paginador"></div>

               <!--Show Modal Popups View & Delete -->
               <div class='modal fade' id='viewModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                 <div class='modal-dialog'>
                   <div class='modal-content'></div><!-- /.modal-content -->
                 </div><!-- /.modal-dialog -->
               </div><!-- /.modal -->
               <div class='modal fade' id='deleteModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                 <div class='modal-dialog  modal-sm'>
                   <div class='modal-content modal-sm'></div><!-- /.modal-content -->
                 </div><!-- /.modal-dialog -->
               </div><!-- /.modal -->


					<input type='hidden' name='totalfactura' id='totalfactura' value='0'>
					<input type='hidden' name='urlprocess' id='urlprocess'value="<?php echo $filename; ?>">
          <input type="hidden" name="process" id="process" value="insert">


    </div>

  </div>
<?php
  } //permiso del script
else {
    echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
} ?>
</div>
</div>
</div>
</div>
<?php
include_once("footer.php");
    echo "<script src='js/plugins/arrowtable/arrow-table.js'></script>";
    echo "<script src='js/funciones/producto_admin.js'></script>";
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


    $sql3="select p.*,c.nombre from productos AS p
		JOIN proveedores AS c ON (p.id_proveedor=c.id_proveedor)
		where id_producto='$id_producto'
		";
    $result3=_query($sql3);
    $count3=_num_rows($result3);
    if ($count3>0) {
        $row3=_fetch_array($result3);
        $cp=$row3['costopro'];
        $pv_base=$row3['precio1'];
        $precio1=$row3['precio1'];
        $precio2=$row3['precio2'];
        $precio3=$row3['precio3'];
        $descuento=$row3['descuento'];
        $precios=array();
        if ($precio1>0) {
            $precios[] = $precio1; // agrego el precio1
        }
        if ($precio2>0) {
            $precios[] = $precio2; // agrego el precio2
        }
        if ($precio3>0) {
            $precios[] = $precio3; // agrego el precio1
        }
        $marca=$row3['marca'];
        $color=$row3['nombre'];
        $exento=$row3['exento'];
        $descripcion=$row3['descripcion'];

        $xdatos['descrip'] =$descripcion;
        $xdatos['costo_prom'] = $cp;
        $xdatos['pv_base'] = $pv_base;
        $xdatos['existencias'] = $existencias;
        $xdatos['color'] = $color;
        $xdatos['marca'] = $marca;
        $xdatos['exento'] = $exento;
        $xdatos['descuento'] = $descuento;
        $xdatos['precios_vta']= $precios;
        echo json_encode($xdatos); //Return the JSON Array
    }
}

function traerdatos(){
    $start = !empty($_POST['page'])?$_POST['page']:0;
	  $limit =$_POST['records'];
	  $sortBy = $_POST['sortBy'];
    $keywords = $_POST['keywords'];
    $marca = $_POST['marca'];
    $modelo= $_POST['modelo'];
    $barcode= $_POST['barcode'];
    $serie= $_POST['serie'];
    $id_proveedor= $_POST['id_proveedor'];
    $sqlJoined=" SELECT pr.id_producto,pr.barcode, pr.descripcion, pr.precio1, pr.precio2, pr.precio3,
       pr.costopro, pr.ultcosto, pr.modelo, pr.exento, pr.marca,  pr.modelo, pr.tiene_garantia, pr.descuento,
      pv.nombre,st.existencias, gp.fecha_ini,gp.fecha_fin, gp.serie
       FROM
       productos AS pr , stock AS st ,proveedores AS pv,
       garantia_prov_det as  gp
        ";
     $sqlParcial= get_sql($start,$limit,$sortBy,$keywords, $barcode, $marca, $modelo,$serie,$id_proveedor);
     $sql_final=$sqlJoined." ".$sqlParcial;
    $query = _query($sql_final);
		$num_rows = _num_rows($query);
      echo _error();
		$filas=0;
    $fecha_actual=date('Y-m-d');
    if($num_rows > 0){
            while($row = _fetch_array($query)) {
                $id_producto = $row['id_producto'];
                $descripcion=$row["descripcion"];
                $marca = $row['marca'];
								$modelo = $row['modelo'];
								$tiene_garantia = $row['tiene_garantia'];
								$exento = $row['exento'];
								$cp = $row['ultcosto'];
								$precio1 = $row['precio1'];
								$precio2 = $row['precio2'];
								$precio3 = $row['precio3'];
                $serie = $row['serie'];
                $fi = $row['fecha_ini'];
                $ff = $row['fecha_fin'];
								$barcode = $row['barcode'];
								$descuento = $row['descuento'];
                $nombre = $row['nombre'];
                $inputCant="<input type='text'  class='form-control decimal cant_in' id='cant_ini' name='cant_ini'  value='1' style='width:60px;'>";
                $btnSelect='<input type="button" id="btnSelect" class="selProd btn btn-primary fa" value="&#xf00c;">';
                $tg="NO";

                  $estado="VIGENTE";

                /*
              PENDIENTE VALIDAR SI FECHA ES MAYOR O MENOR OJO !!!!!!!! PARA ESTADO VENCIDO
                */

                  $datetime1 = new DateTime($fecha_actual);
                  $datetime2 = new DateTime($ff);
                  $interval = $datetime1->diff($datetime2);
                  if (isset($interval)){
                    if($datetime1 > $datetime2) {
                      $dias_tarde=$intervaloent->format('%d');
                        $estado="VENCIDO";
                      }
                }
                //  echo $interval->format('%R%a días');
            ?>
				   <tr class='tr1'  tabindex="<?php echo $filas;?>">
					  <td class='col5 td1'><input type='hidden'  id='exento' name='exento' value='<?php echo $exento;?>'> <h5><?php echo $id_producto;?></h5></td>
            <td class='col25 td1'><?php echo $descripcion." ".$marca." ".$modelo;?></td>
            <td class='col8 td1'><?php echo $fi;?></td>
            <td class='col8 td1'><?php echo $ff;?></td>
						<td class='col8 td1 td_success'><?php echo $cp;?></td>
            <td class='col8 td1 td_success'><?php echo $precio1;?></td>
            <td class='col8 td1 td_success'><?php echo $estado;?></td>
            <td class='col8 td1'><?php echo $serie;?></td>
            <td class='col15 td1'><?php echo $nombre;?></td>
						<!--td class='col7 td1'><!--?php echo $btnSelect;?></td-->


            <?php
                $acciones=acciones($id_producto);
                ?>
                <td class='col7 td1 td_success'><div clas='marg_der2'><?php echo $acciones;?></div></td>
                <?php
                  $filas+=1;
          }
				}
  echo '<input type="hidden" id="cuantos_reg"  value="'.$num_rows.'">';
}
//function get_sql( $keywords, $barcode, $marca, $modelo,$limite){
function  get_sql($start,$limit,$sortBy,$keywords, $barcode, $marca, $modelo,$serie,$id_proveedor){

	$id_sucursal= $_SESSION['id_sucursal'];

  $whereSQL=" WHERE pr.id_proveedor=pv.id_proveedor
        AND gp.id_producto=pr.id_producto
        AND st.id_producto=pr.id_producto
        AND st.existencias>=0
        AND st.id_sucursal='$id_sucursal'
     ";


     //
  $andSQL='';
	$keywords=trim($keywords);

	if(!empty($barcode)){
			$andSQL.= " AND   pr.barcode LIKE '{$barcode}%'";
	}
	else{
    if(!empty($keywords)){
      $andSQL.= " AND  pr.descripcion LIKE '%{$keywords}%'";
      if(!empty($marca)){
          $andSQL.= " AND pr.marca LIKE '%{$marca}%' ";
      }
      if(!empty($modelo)){
          $andSQL.= " AND pr.modelo LIKE '%{$modelo}%'";
      }
		  if(!empty($serie)){
			    $andSQL.= " AND pr.serie='$serie'";
			}
      if ($id_proveedor!=-1) {
          $andSQL.= " AND pv.id_proveedor='$id_proveedor'";
      }
    }
    if(empty($keywords)  && !empty($marca)){
		    $andSQL.= " AND pr.marca  LIKE '%{$marca}%' ";
		    if(!empty($modelo)){
				      $andSQL.= " AND pr.modelo LIKE '%{$modelo}%'";
		    }
		    if(!empty($serie)){
				      $andSQL.= " AND pr.serie='$serie'";
		    }
        if ($id_proveedor!=-1) {
            $andSQL.= " AND pv.id_proveedor='$id_proveedor'";
        }
    }
	  if(empty($keywords)  && empty($marca) && !empty($modelo)){
	     $andSQL.= " AND  pr.modelo LIKE '%{$modelo}%'";
       if(!empty($serie)){
            $andSQL.= " AND pr.serie='$serie'";
      }
       if ($id_proveedor!=-1) {
           $andSQL.= " AND pv.id_proveedor='$id_proveedor'";
       }
	  }
    if(empty($keywords)  && empty($marca) && empty($modelo) && !empty($serie)){
	     $andSQL.= " AND  pr.serie LIKE '%{$serie}%'";

       if ($id_proveedor!=-1) {
           $andSQL.= " AND pv.id_proveedor='$id_proveedor'";
       }

	  }
    if(empty($keywords)  && empty($marca) && empty($modelo) && empty($serie) && $id_proveedor!=-1){
	     //$andSQL.= "AND  pr.modelo LIKE '%{$modelo}%'";
       $limite=1000;
           $andSQL.= " AND pv.id_proveedor='$id_proveedor'";

	  }
	}
  $orderBy=" ORDER BY pr.id_producto,pr.descripcion, pr.barcode,pr.marca,pr.modelo ";
  $limitSQL= "LIMIT $start,$limit ";

	$sql_final=$whereSQL.$andSQL.$orderBy.$limitSQL;
  return $sql_final;
}


function traerpaginador(){
$start = !empty($_POST['page'])?$_POST['page']:0;
$limit =$_POST['records'];
$keywords = $_POST['keywords'];
$sortBy = $_POST['sortBy'];

$serie = $_POST['serie'];
$marca= $_POST['marca'];
$modelo= $_POST['modelo'];
$id_proveedor= $_POST['id_proveedor'];
$barcode= $_POST['barcode'];
$limite=50;
$whereSQL =$andSQL =  $orderSQL = '';
  if(isset($_POST['page'])){
      //Include pagination class file
      include('Pagination.php');
      //get partial values from sql sentence
			//$sqlParcial=get_sql($start,$limit,$keywords,$sortBy, $id_proveedor, $serie, $marca, $barcode);
      $sqlParcial=get_sql($start,$limit,$sortBy,$keywords, $barcode, $marca, $modelo,$serie,$id_proveedor);
      //get number of rows
      $sql1="SELECT COUNT(*) as numRecords  FROM productos AS pr, proveedores AS pv, stock AS st, garantia_prov_det as  gp";
      $sql_numrows=$sql1.$sqlParcial;
      $queryNum = _query($sql_numrows);
      $resultNum = _fetch_array($queryNum);
      $rowCount = $resultNum['numRecords'];

      //initialize pagination class
      $pagConfig = array(
      'currentPage' => $start,
      'totalRows' => $rowCount,
      'perPage' => $limit,
      'link_func' => 'searchFilter'
      );
      $pagination =  new Pagination($pagConfig);
      echo $pagination->createLinks();
        echo '<input type="hidden" id="cuantos_reg"  value="'.$rowCount.'">';
  }

}

//datos clientes
function mostrar_datos_cliente(){
    $id_cliente=$_POST['id_client'];

    $sql="SELECT * FROM clientes WHERE	id_cliente='$id_cliente'";
    $result=_query($sql);
    $count=_num_rows($result);
    if ($count > 0) {
        for ($i = 0; $i < $count; $i ++) {
            $row = _fetch_array($result);
            $id_cliente=$row["id_cliente"];
            $nombre=$row["nombre"];
            $nit=$row["nit"];
            $dui=$row["dui"];
            $giro=$row["giro"];
            $registro=$row["nrc"];
            $direccion=$row["direccion"];
        }
    }
    $xdatos['nit']= $nit;
    $xdatos['registro']= $registro;
    $xdatos['nombreape']=   $nombre;
    echo json_encode($xdatos); //Return the JSON Array
}
function acciones($id_producto){
  $menudrop="<div class='btn-group col-md'>
  <a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
  <ul class='dropdown-menu dropdown-primary'>";
  $id_user=$_SESSION["id_usuario"];
  $admin=$_SESSION["admin"];
  $filename='editar_producto.php';
  $link=permission_usr($id_user,$filename);
  if ($link!='NOT' || $admin=='1' ){
    $menudrop.="<li><a href='$filename?id_producto=".$id_producto."'><i class='fa fa-pencil'></i> Editar</a></li>";
  }
  $filename='borrar_producto.php';
  $link=permission_usr($id_user,$filename);
  if ($link!='NOT' || $admin=='1' ){
    $menudrop.="<li><a a data-toggle='modal' href='$filename?id_producto=".$id_producto."&process=formDelete"."' data-target='#viewModal' data-refresh='true'><i class='fa fa-eraser'></i> Eliminar</a></li>";
  }
  $filename='ver_producto.php';
  $link=permission_usr($id_user,$filename);
  if ($link!='NOT' || $admin=='1' ){
    $menudrop.="<li><a a data-toggle='modal' href='$filename?id_producto=".$id_producto."' data-target='#viewModal' data-refresh='true'><i class='fa fa-search'></i> Ver Detalle</a></li>";
  }
  $menudrop.="</ul>
        </div>";
  return $menudrop;
}

//Impresion
function imprimir_fact(){
    $numero_doc = $_POST['numero_doc'];
    $totalfact = $_POST['total'];
    $tipo_impresion= $_POST['tipo_impresion'];
    $id_factura= $_POST['num_doc_fact'];
    $id_sucursal=$_SESSION['id_sucursal'];
    $numero_factura_consumidor = $_POST['numero_factura_consumidor'];
    $cambio_fin=$_POST['cambio_fin'];
    $efectivo_fin=$_POST['efectivo_fin'];
    $voucher=-1;
    $id_pago=0;
    if (isset($_POST['numero_tarjeta'])) {
        $numero_tarjeta=$_POST['numero_tarjeta'];
    }
    if (isset($_POST['emisor'])) {
        $emisor=$_POST['emisor'];
    }
    if (isset($_POST['voucher'])) {
        $voucher=$_POST['voucher'];
        // SELECT id_pago_tarjeta, idtransace, alias_tipodoc, fecha, voucher, numero_tarjeta, emisor, monto FROM pago_tarjeta WHERE 1
        $sql_pt="SELECT * FROM pago_tarjeta  WHERE idtransace='$id_factura'";
        $result_pt=_query($sql_pt);
        $row_pt=_fetch_array($result_pt);
        $nrows_pt=_num_rows($result_pt);
        if ($nrows_pt==0) {
            $fecha_movimiento=$row_pt['fecha_doc'];
            $table_pt= 'pago_tarjeta';

            $form_data_pt = array(
                    'idtransace' => $id_factura,
                    'voucher' => $voucher,
                    'emisor' => $emisor,
                    'numero_tarjeta' => $numero_tarjeta,
                    'monto' => $totalfact,
            );

            $where_clause="WHERE idtransace='$id_factura'";
            $actualizar = _insert($table_pt, $form_data_pt);
            $id_pago= _insert_id();
        }
    }


    if ($tipo_impresion=='CCF') {
        $tipo_entrada_salida="CREDITO FISCAL";
        $nit= $_POST['nit'];
        $nrc= $_POST['nrc'];
        $nombreape= $_POST['nombreape'];
    }
    //Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
    $info = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($info, 'Windows') == true) {
        $so_cliente='win';
    } else {
        $so_cliente='lin';
    }

    $sql_fact="SELECT * FROM factura WHERE idtransace='$id_factura'";
    $result_fact=_query($sql_fact);
    $row_fact=_fetch_array($result_fact);
    $nrows_fact=_num_rows($result_fact);
    if ($nrows_fact>0) {
        $fecha_movimiento=$row_fact['fecha_doc'];
        //$total=$row_fact['total'];
        $table_fact= 'factura';

        $form_data_fact = array(
            'finalizada' => '1',
            'id_pago_tarjeta' => $id_pago,
            'efectivo' => $efectivo_fin,
            'cambio' => $cambio_fin,
        );

        $where_clause="WHERE idtransace='$id_factura'";
        $actualizar = _update($table_fact, $form_data_fact, $where_clause);
    }
    $headers="";
    $footers="";
    if ($tipo_impresion=='TIK') {
        $info_facturas=print_ticket($id_factura, $tipo_impresion);
        $sql_pos="SELECT *  FROM config_pos  WHERE id_sucursal='$id_sucursal' AND alias_tipodoc='TIK'";

        $result_pos=_query($sql_pos);
        $row1=_fetch_array($result_pos);

        $headers=$row1['header1']."|".$row1['header2']."|".$row1['header3']."|".$row1['header4']."|".$row1['header5']."|";
        $headers.=$row1['header6']."|".$row1['header7']."|".$row1['header8']."|".$row1['header9']."|".$row1['header10'];
        $footers=$row1['footer1']."|".$row1['footer2']."|".$row1['footer3']."|".$row1['footer4']."|".$row1['footer5']."|";
        $footers.=$row1['footer6']."|".$row1['footer7']."|".$row1['footer8']."|".$row1['footer8']."|".$row1['footer10']."|";
    }
    if ($tipo_impresion=='COF') {
        $info_facturas=print_fact($id_factura, $tipo_impresion);
    }
    if ($tipo_impresion=='CCF') {
        $info_facturas=print_ccf($id_factura, $tipo_impresion, $nit, $nrc, $nombreape);
    }
    //directorio de script impresion cliente
    $sql_dir_print="SELECT *  FROM config_dir WHERE id_sucursal='$id_sucursal'";
    $result_dir_print=_query($sql_dir_print);
    $row0=_fetch_array($result_dir_print);
    $dir_print=$row0['dir_print_script'];
    $shared_printer_win=$row0['shared_printer_matrix'];
    $shared_printer_pos=$row0['shared_printer_pos'];

    $nreg_encode['shared_printer_win'] =$shared_printer_win;
    $nreg_encode['shared_printer_pos'] =$shared_printer_pos;
    $nreg_encode['dir_print'] =$dir_print;
    $nreg_encode['facturar'] =$info_facturas;
    $nreg_encode['sist_ope'] =$so_cliente;
    $nreg_encode['headers'] =$headers;
    $nreg_encode['footers'] =$footers;

    echo json_encode($nreg_encode);
}
function buscar_reserva()
{
    $numero = $_POST['numero'];
    $sql0="SELECT idtransace, id_movimiento, cobrado, valor, concepto FROM mov_caja
		WHERE numero_doc='$numero' AND alias_tipodoc = 'RES'";
    $result0 = _query($sql0);
    if (_num_rows($result0)>0) {
        $xdatos["typeinfo"] = "Success";
        $row0 = _fetch_array($result0);
        $xdatos["valor"] = $row0["valor"];
        $xdatos["concepto"] = $row0["concepto"];
        $xdatos["id_vale"] = $row0["id_movimiento"];
        if ($row0['cobrado']) {
            $xdatos["cobrado"] = "Si";
        } else {
            $xdatos["cobrado"] = "No";
            $id_reserva =$row0['idtransace'];
            $sql1="SELECT dr.id_producto, dr.cantidad
			FROM detalle_reservas AS dr
			WHERE dr.id_reserva='$id_reserva'";
            $result1 = _query($sql1);
            $array_prod = array();
            $numrows= _num_rows($result1);
            $cantidades = "";
            $ids = "";
            for ($i=0;$i<$numrows;$i++) {
                $row = _fetch_array($result1);
                $id_producto =$row['id_producto'];
                $cantidad =$row['cantidad'];
                $cantidades.=$cantidad.",";
                $ids.=$id_producto.",";
            }
            $xdatos["ids"] = $ids;
            $xdatos["cantidades"] = $cantidades;
        }
    } else {
        $xdatos["typeinfo"] = "Error";
        $xdatos["msg"] = "No se encontro ninguna reserva que coincida con este numero";
    }
    //$xdatos['array_prod']=$array_prod;
    echo json_encode($xdatos); //Return the JSON Array
}

//functions to load
if (!isset($_REQUEST['process'])) {
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
    case 'buscarBarcode':
        buscarBarcode();
        break;
    case 'total_texto':
        total_texto();
        break;
    case 'datos_clientes':
        datos_clientes();
        break;
    case 'traerdatos':
        traerdatos();
        break;
    case 'buscartipo_pago':
        buscartipo_pago();
        break;
    case 'imprimir_fact':
        imprimir_fact();
        break;
    case 'mostrar_datos_cliente':
            mostrar_datos_cliente();
            break;
    case 'buscar_reserva':
            buscar_reserva();
            break;
    case 'traerpaginador':
          traerpaginador();
            break;
    }
}
?>
