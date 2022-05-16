<?php
include_once "_core.php";

function initial() {

	$title="Generación de Facturas para impresión diaria";
	$_PAGE = array ();
	$_PAGE ['title'] = $title;
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2-bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/jqGrid/ui.jqgrid.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
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

$id_sucursal=$_SESSION['id_sucursal'];

?>

<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-lg-2"></div>
</div>
<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5><?php echo $title; ?></h5>
                        </div>
                        <div class="ibox-content">
                             <div class="row">
															 <div class="col-md-4">
																 <div class="form-group">
																	 <input type="text" placeholder="Fecha Inicio" class="datepick form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo date("Y-m-d");?>">
																 </div>
                							</div>

                							<div class="col-md-4">
																<div class="form-group">
																	<button type="button" id="btnGenerar" name="btnGenerar" class="btn btn-primary"><i class="fa fa-check"></i> Generar Facturas</button>
																</div>
                							</div>
															<div class="col-md-4">
																<div class="form-group">
																	<button type="button" id="btnMostrar" name="btnMostrar" class="btn btn-primary"><i class="fa fa-check"></i> Mostrar Facturas</button>
																</div>
                							</div>

														</div>

														<div class="row" id='tabla_facturas'></div>
														<!--load datables estructure html-->
														<div id='actualizarlista'>
														<section>
															<table class="table table-striped table-bordered table-hover" id="editable2">
																<thead>
																	<tr>
																		<th>Id factura</th>
																		<th>Fecha</th>
																		<th>Total</th>
																		<th>Estado</th>
																		<th>accion</th>
																	</tr>
																</thead>
																<tbody>

																</tbody>
															</table>
															 <input type="hidden" name="autosave" id="autosave" value="false-0">
														</section>
														</div><!-- para la parte del div que se actualiza-->
														<!--Show Modal Popups View & Delete -->
														<div class='modal fade' id='viewModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
															<div class='modal-dialog'>
																<div class='modal-content modal-sm'></div><!-- /.modal-content -->
															</div><!-- /.modal-dialog -->
														</div><!-- /.modal -->
														<div class='modal fade' id='deleteModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
															<div class='modal-dialog'>
																<div class='modal-content modal-sm'></div><!-- /.modal-content -->
															</div><!-- /.modal-dialog -->
														</div><!-- /.modal -->
														<!--Show Modal Popups View & Delete -->
														<div class='modal fade' id='viewModalFact' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
															<div class='modal-dialog'>
																<div class='modal-content modal-md'></div><!-- /.modal-content -->
															</div><!-- /.modal-dialog -->
														</div><!-- /.modal -->

                    	</div>
                </div>
            </div>

    </div>
</div><!--div class='wrapper wrapper-content  animated fadeInRight'-->

<?php
include_once ("footer.php");
echo "<script type='text/javascript' src='js/funciones/generar_facturas_dia3.js'></script>";
//echo" <script type='text/javascript' src='js/funciones/funciones_factura_dia.js'></script>";
}

function generar_facturas_diarias(){
$fecha1=$_REQUEST["fecha"];
$id_sucursal=$_SESSION["id_sucursal"];


$sql="SELECT  factura.fecha,producto.descripcion,factura.id_factura,
producto.id_producto,stock.costo_promedio, round(avg(factura_detalle.precio_venta),2) as precio_venta,
round(sum(factura_detalle.cantidad),2) as cant,round(sum(factura_detalle.subtotal),2) as subtotal
FROM producto,factura,factura_detalle,stock,sucursal
WHERE factura.id_factura=factura_detalle.id_factura
AND producto.id_producto=factura_detalle.id_prod_serv
AND stock.id_producto=factura_detalle.id_prod_serv
AND producto.id_producto= stock.id_producto
AND stock.id_sucursal=sucursal.id_sucursal
AND DATE(factura.fecha)='$fecha1'
AND sucursal.id_sucursal=factura_detalle.id_sucursal
AND factura.id_sucursal='$id_sucursal'
AND factura.anulada=0
AND factura.impresa_individual=0
AND factura_detalle.subtotal>0
GROUP BY producto.id_producto";



$result=_query($sql);
$count=_num_rows($result);
$lineas_factura=10;
$numero_fact_diaria=0;
$total=0;

$sql_fact="SELECT  factura.fecha,factura.id_factura
FROM factura,factura_detalle,sucursal
WHERE factura.id_factura=factura_detalle.id_factura
AND DATE(factura.fecha)='$fecha1'
AND sucursal.id_sucursal=factura_detalle.id_sucursal
AND factura.id_sucursal='$id_sucursal'
AND factura.anulada=0
AND factura.finalizada=0
AND factura.impresa_individual=0
AND factura_detalle.subtotal>0";
$result_sql_fact=_query($sql_fact);
$count_sql_fact=_num_rows($result_sql_fact);
for ($m=0;$m<$count_sql_fact;$m++){
	$arreglo_fact=_fetch_array($result_sql_fact);
	$id_factura=$arreglo_fact['id_factura'];
	//actualizar status finalizada en Factura
	$table_fact= 'factura';
	$form_data_fact = array(
		'finalizada' => '1'
	);
	$where_clause_fact="WHERE id_factura='$id_factura' and id_sucursal='$id_sucursal'";
	$actualizar = _update($table_fact,$form_data_fact, $where_clause_fact);

//fin actualizar status finalizada en Factura
}

if($count>0){
	$cuantas_facturas=ceil($count/$lineas_factura);
	if($cuantas_facturas==0){
		$cuantas_facturas=1;
	}
	$sql_fact_dia="SELECT id_factura_dia, total, fecha, generada, impresa
	FROM factura_dia
	WHERE  fecha='$fecha1'
	AND id_sucursal='$id_sucursal'
	";
	//AND impresa=0
	$result_fact_dia=_query($sql_fact_dia);
	$count_fact_dia=_num_rows($result_fact_dia);
	if($count_fact_dia>0){
		$table_fact= 'factura_dia';
		$where_fact=" WHERE  fecha='$fecha1'
		AND id_sucursal='$id_sucursal'";
		$del_fact = _delete($table_fact,$where_fact);

		$table_fact_dia_det= 'factura_detalle_dia';
		$where_fact_dia_det=" WHERE  fecha='$fecha1'
		AND id_sucursal='$id_sucursal'";
		$del_fact_dia_det = _delete($table_fact_dia_det, $where_fact_dia_det);
	}
	$n=1;
	for ($j=0;$j<$cuantas_facturas;$j++){
		//Paso 1: Agregar las facturas del dia, primero sin totales, solo con fecha ,para tener el id
		$table_fact= 'factura_dia';
		$form_data_fact = array(
		'fecha'=> $fecha1,
		'generada'=>'0',
		'total'=>0,
		'id_sucursal'=> $id_sucursal,
		'numero'=>$n,
		'id_cliente'=>'-1',
		);
		$n=$n+1;
		$insertar_fact = _insert($table_fact,$form_data_fact );
		$id_fact= _insert_id();
	 	$linea=0;

		for ($i=0;$i<$count;$i++){
			$arreglo_ventas=_fetch_array($result);
			$fecha=$arreglo_ventas['fecha'];
			$id_producto=$arreglo_ventas['id_producto'];
			$descripcion=$arreglo_ventas['descripcion'];
			$costo_promedio=$arreglo_ventas['costo_promedio'];
			$precio_venta=$arreglo_ventas['precio_venta'];
			$cant=$arreglo_ventas['cant'];
			$subtotal=$arreglo_ventas['subtotal'];
			$total=$subtotal+$total;

			$table_fact_dia= 'factura_detalle_dia';
			$form_data_fact_dia = array(
				'id_factura_dia'=>$id_fact,
				'fecha' => $fecha,
				'descripcion' =>$descripcion,
				'id_producto'=> $id_producto,
				'costo_promedio'=>$costo_promedio,
				'precio_venta'=>$precio_venta,
				'cantidad'=>$cant,
				'subtotal' =>$subtotal,
				'linea'=>$linea,
				'id_sucursal' => $id_sucursal
			);

			if($linea<=$lineas_factura && $id_producto!=''){
				$insertar_fact = _insert($table_fact_dia,$form_data_fact_dia);
			}
			$linea=$linea+1;
			//para hacer reiniciar numeracion y salto de numero de factura
			if($linea>$lineas_factura-1){
				break;
			}
		}
	}
}

$sql_total_fact_diario="SELECT id_factura_dia,ROUND(SUM(subtotal),2) AS total_fact
FROM factura_detalle_dia
WHERE fecha='$fecha1'
AND id_sucursal='$id_sucursal'
AND impresa=0
GROUP BY id_factura_dia";
$result_total_fact_diario=_query($sql_total_fact_diario);
$count_total_fact_diario=_num_rows($result_total_fact_diario);
for ($k=0;$k<$count_total_fact_diario;$k++){
		$arreglo_total_fact_diario=_fetch_array($result_total_fact_diario);
		$total_fact=$arreglo_total_fact_diario['total_fact'];
		$id_factura_dia=$arreglo_total_fact_diario['id_factura_dia'];

		$table_fact= 'factura_dia';
		$form_data_fact = array(
			'generada'=>'1',
			'total'=>$total_fact,
		);

		$where_fact=" WHERE  id_factura_dia='$id_factura_dia'
		AND fecha='$fecha1'
		AND id_sucursal='$id_sucursal'
		AND impresa=0 ";
		$update_fact = _update($table_fact,$form_data_fact,$where_fact );
	}

$sql_total_fact_dia="SELECT *
FROM factura_detalle_dia
WHERE fecha='$fecha1'
AND id_sucursal='$id_sucursal'
";
$result_total_fact_dia=_query($sql_total_fact_dia);
$count_total_fact_dia=_num_rows($result_total_fact_dia);
for ($l=0;$l<$count_total_fact_dia;$l++){
		$arreglo_total_fact_dia=_fetch_array($result_total_fact_dia);
		//$total_fact=$arreglo_total_fact_dia['total_fact'];
		$id_factura_dia=$arreglo_total_fact_dia['id_factura_dia'];
		$id_producto=$arreglo_total_fact_dia['id_producto'];

		$table_fact_det= 'factura_detalle';
		$form_data_fact_det = array(
			'id_factura_dia'=>$id_factura_dia
		);

		$where_fact_det=" WHERE fecha='$fecha1'
		AND id_sucursal='$id_sucursal'
		and id_prod_serv='$id_producto'
		";
		$update_fact2 = _update($table_fact_det,$form_data_fact_det,$where_fact_det);
}
	$fecha_dmy=ed($fecha1);
	$xdatos['typeinfo']="Success";
	$xdatos['msg']="Facturas del dia:".$fecha_dmy." fueron generadas";

	echo json_encode($xdatos); //Return the JSON Array
}

function ver_facturas_diarias(){
	$fecha1=$_REQUEST["fecha"];
	$id_sucursal=$_SESSION["id_sucursal"];
	$requestData= $_REQUEST;

	$sql_fact_dia="SELECT id_factura_dia, total, fecha, generada, impresa
	FROM factura_dia
	WHERE  fecha='$fecha1'
	AND id_sucursal='$id_sucursal'
	AND impresa=0
	";
	$result_fact_dia=_query($sql_fact_dia);
	$count_fact_dia=_num_rows($result_fact_dia);
	if($count_fact_dia>0){
		$data = array();

		for ($i=0;$i<$count_fact_dia;$i++){
			$nestedData=array();
			$arreglo_ventas=_fetch_array($result_fact_dia);
			$fecha=$arreglo_ventas['fecha'];
			$id_factura_dia=$arreglo_ventas['id_factura_dia'];
			$impresa=$arreglo_ventas['impresa'];
			$total=$arreglo_ventas['total'];

			$nestedData[] = $id_factura_dia;
			$nestedData[] = $fecha;
			$nestedData[] = $total;
			$nestedData[] = $impresa;
			$data[] = $nestedData;
	}
}
	$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $count_fact_dia),  // total number of records
			"recordsFiltered" => intval( $count_fact_dia), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
			);

echo json_encode($json_data);  // send data as json format
}

if(!isset($_POST['process'])){
	initial();
}
else{
	if(isset($_POST['process'])){
		switch ($_POST['process']) {
		case 'insert':
			insertar();
			break;
		case 'generar_facturas_diarias':
			generar_facturas_diarias();
			break;
		case 'ver_facturas_diarias':
			ver_facturas_diarias();
			break;
		}
	}
}
?>
