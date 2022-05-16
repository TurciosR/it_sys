<?php
include_once "_core.php";
include ('num2letras.php');
//include ('facturacion_funcion_imprimir.php');
//include("escpos-php/Escpos.php");
function initial() {
	$id_consignacion=$_REQUEST["id_consignacion"];

	//permiso del script
	$id_usuario=$_SESSION["id_usuario"];
	$id_sucursal=$_SESSION['id_sucursal'];
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

	//$id_sucursal=$_SESSION['id_sucursal'];

	echo "<style type='text/css'>
    #inventable{
    	font-family: 'Open Sans';
    	 font-style: normal;
    	 font-size: small;
		font-weight: 400;
		src: local('Open Sans'), local('OpenSans'), url(fonts/apache/opensans/OpenSans-Regular.ttf) format('truetype'), url(fonts/apache/opensans/OpenSans.woff) format('woff');
    }
    .table thead tr > th.success{
		background-color: #428bca !important;
		color: white !important;
	}
	.table > tfoot > tr > .thick-line {
		border-top: 2px solid;
	}
	</style>";

	
	$sql_consigna="SELECT consignacion.*, cliente.nombre,cliente.apellido
	FROM consignacion
	JOIN cliente ON consignacion.id_cliente=cliente.id_cliente
	WHERE id_consignacion='$id_consignacion'
	AND consignacion.id_sucursal='$id_sucursal'";

	$result_consigna = _query( $sql_consigna);
	$count_consigna = _num_rows( $result_consigna);
	$rows_consigna=_fetch_array($result_consigna);

	if ($count_consigna>0){
				 $total_consignado=$rows_consigna['total'];
				 $numconsigna=$rows_consigna['numero_doc'];
				 $cliente=$rows_consigna['nombre']." ".$rows_consigna['apellido'];
				 $fecha_consigna=$rows_consigna['fecha'];
				 $finalizada=$rows_consigna['finalizada'];
	 }
	 else
		 $total_consignado=0;


	$sql_fact="SELECT factura.*,
	usuario.nombre as nombreuser
	FROM factura
	JOIN usuario ON factura.id_usuario=usuario.id_usuario
	WHERE id_consignacion='$id_consignacion'
	AND factura.id_sucursal='$id_sucursal'
	";
	$result_fact = _query( $sql_fact);
	$count_fact = _num_rows( $result_fact);


	//Facturas de consignacion
	//Traer sumatoria de facturas hechas con este id consignacion
	$n_facturado=0;

	$sql_facturado="SELECT sum(total) as total_consignado_fact
				FROM factura WHERE id_consignacion='$id_consignacion'
				AND id_sucursal='$id_sucursal'
				";
	$result_facturado=_query($sql_facturado);
	$n_facturado=_num_rows($result_facturado);
	$rows_facturado=_fetch_array($result_facturado);

	if ($n_facturado>0){
				 $abono_consignado_fact=$rows_facturado['total_consignado_fact'];
	 }
	 else
		 $abono_consignado_fact=0;
	 $abono_consignado_format=sprintf("%.2f",  $abono_consignado_fact);

	$total_consignado_format=sprintf("%.2f",$total_consignado);
	//SALDO PENDIENTE
	$saldo_pendiente=0;
	$saldo_pendiente=$total_consignado-$abono_consignado_fact;
	$saldo_pendiente_format=sprintf("%.2f",$saldo_pendiente);
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title text-primary">&nbsp; Consignaci&oacute;n No: <?php echo $numconsigna;  ?></h4>
</div>

<div class="modal-body">
		<div class="row" id="row1">
				<?php

						if ($links!='NOT' || $admin=='1' ){
					?>
				<div>
							<!--load datables estructure html-->
							<header>

								<h4 class="text-success">&nbsp; Total Consignado: $<?php echo $total_consignado_format;  ?></h4>
								<?php if ($finalizada==0) { ?>
								<h4 class="text-danger">&nbsp; Saldo Pendiente: $<?php echo $saldo_pendiente_format;  ?></h4>
								<?php }
								else{
								 ?>
								 <h4 class="text-danger">&nbsp; Consignacion finalizada </h4>
								<?php }  ?>

								<h4  class='text-navy'>Fecha:<?php echo $fecha_consigna;  ?>&nbsp;
								Cliente:<?php echo $cliente; ?></h4>
							</header>
							<section>
								<div class="table-responsive m-t">
									<table class="table table-condensed table-striped" id="inventable">
									<thead class="thead-inverse">
										<tr>
										<th class='success'>Id</th>
										<th class='success'>Empleado</th>
										<th class='success'>Fecha</th>
										<th class='success'>Numero Doc.</th>
										<th class='success' >Total</th>

										</tr>
									</thead>
									<tbody>

						<?php
						if ($count_fact > 0) {
									for($i = 0; $i < $count_fact; $i ++) {
										$row = _fetch_array ( $result_fact, $i );
										//$cliente=$row['nombre']." ".$row['apellido'];
										$empleado=$row['nombreuser'];
										$factnum=$row['numero_doc'];
										$id_factura=$row['id_factura'];
										$total=$row['total'];
										$fecha=$row['fecha'];
										$total_format=sprintf("%.2f", $total);


											echo "<tr>";
											echo "<td>".$id_factura."</td>";
											echo "<td>".$empleado."</td>";
											echo "<td>".$fecha."</td>";
											echo "<td>".$factnum."</td>";
											/*
											echo "<td id='pv' class='text-right'>".$precio_venta."</td>";
											echo "<td id='cant1' class='text-right'>".$cantidad."</td>"; */
											echo "<td id='subtot' class='text-right'>".$total_format."</td>";
											//echo "<td id='combos' class='text-center'>".$combo_chk."</td>";

											echo "</tr>";
									}
						}

										?>

									</tbody>
									<tfoot>
										<tr>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<!--td class="thick-line"></td>
										<td class="thick-line"></td-->
										<td class="thick-line text-center"><strong><h4 class="text-warning">Total Abonado $:</h4></strong></td>
										<td  class="thick-line text-right" id='total_dinero' ><strong><h4 class="text-warning"><?php echo $abono_consignado_format; ?></h4></strong></td>
										<!--td class="thick-line"></td>
										<td class="thick-line"></td-->
										</tr>
									</tfoot>
								</table>
								<!--?php
								list($entero,$decimal)=explode('.',$total);
								$enteros_txt=num2letras($entero);
							$decimales_txt=num2letras($decimal);

							if($entero>1)
								$dolar=" dolares";
							else
								$dolar=" dolar";
							$cadena_salida= "Son: <strong>".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.</strong>";
							echo "<div class='well m-t'  id='totaltexto'>".$cadena_salida." </div>";

								?-->


					</section>

						</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
				</div>
		</div>
	</div>


<?php
//include_once ("footer.php");
//echo "<script src='js/funciones/genera_venta.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}


function total_texto(){
	$total=$_REQUEST['total'];
	list($entero,$decimal)=explode('.',$total);
	$enteros_txt=num2letras($entero);
	$decimales_txt=num2letras($decimal);

	if($entero>1)
		$dolar=" dolares";
	else
		$dolar=" dolar";
	$cadena_salida= "Son: <strong>".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.</strong>";
	echo $cadena_salida;
}

//functions to load
if(!isset($_REQUEST['process'])){
	initial();
}
//else {
if (isset($_REQUEST['process'])) {


	switch ($_REQUEST['process']) {
	case 'formEdit':
		initial();
		break;
	case 'consultar_stock':
		consultar_stock();
		break;
	}

 //}
}
?>
