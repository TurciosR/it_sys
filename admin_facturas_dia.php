<?php
	include ("_core.php");

function initial() {	
	// Page setup
	$_PAGE = array ();
	$title='Administrar Factura';
	$_PAGE ['title'] = $title;
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
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

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row" id="row1">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<?php 
				
				//permiso del script
						if ($links!='NOT' || $admin=='1' ){
						echo"
                        <div class='ibox-title'>
                            <h5>$title</h5>
                        </div>";
                        ?>	
				<div class="ibox-content">
				
				<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<!--label>Fecha Facturacion</label--> 
										<input type="text" placeholder="Fecha Inicio" class="datepick form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo date("Y-m-d");?>">
									</div>
                                </div>
                                <!--/div>
                              <div class="row"-->
                               <div class="col-md-4">
									<div class="form-group">
									<!--label>Generar</label--> 
										<button type="button" id="btnMostrar" name="btnMostrar" class="btn btn-primary"><i class="fa fa-check"></i> Mostrar Facturas</button>
									</div>
                                </div>     
							</div>
				
				
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
				<!--?php
				$fecha1=date('Y-m-d');
				$sql_fact_dia="SELECT id_factura_dia, total, fecha, generada, impresa 
	FROM factura_dia 
	WHERE  fecha='$fecha1'
	AND id_sucursal='$id_sucursal'  
	AND impresa=0
	";
	$result_fact_dia=_query($sql_fact_dia);
	$count_fact_dia=_num_rows($result_fact_dia);
	if($count_fact_dia>0){	
 					
						for($i=0;$i<$count_fact_dia;$i++){
							$arreglo_ventas=_fetch_array($result_fact_dia);
							$fecha=$arreglo_ventas['fecha']; 
			$id_factura_dia=$arreglo_ventas['id_factura_dia'];
			$impresa=$arreglo_ventas['impresa'];
			$total=$arreglo_ventas['total']; 	
			echo "<tr>";
			echo "<td>".$id_factura_dia."</td>";
			echo "<td>".$fecha."</td>";
			echo "<td>".$total."</td>";
			echo "<td>".$impresa."</td>";
			echo"</tr>"	;	
						}
					}
		
				?-->			
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
						
               	</div><!--div class='ibox-content'-->
       		</div><!--<div class='ibox float-e-margins' -->
		</div> <!--div class='col-lg-12'-->
	</div> <!--div class='row'-->  
</div><!--div class='wrapper wrapper-content  animated fadeInRight'-->			
<?php    
	include("footer.php");
	echo" <script type='text/javascript' src='js/funciones/funciones_factura_dia.js'></script>"; 	    
		} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
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
		case 'ver_facturas_diarias':
			ver_facturas_diarias();
			break;
		} 
	}			
}	
		                     	     
?>
