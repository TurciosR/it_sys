<?php
	include ("_core.php");

	$fecha1=$_REQUEST["fecha"];
	$id_sucursal=$_SESSION["id_sucursal"];

$requestData= $_REQUEST;


	$sql_fact_dia="SELECT id_factura_dia, total, fecha, generada, impresa
	FROM factura_dia
	WHERE  fecha='$fecha1'
	AND id_sucursal='$id_sucursal'
	AND impresa=0
	union all
	SELECT id_factura_dia, total, fecha, generada, impresa
	FROM factura_dia
	WHERE  fecha='$fecha1'
	AND id_sucursal='$id_sucursal'
	AND impresa=1

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
			if ($impresa==0)
				$estado='PENDIENTE';
			else
				$estado='IMPRESA';
			$nestedData[] = $id_factura_dia;
			$nestedData[] = $fecha;
			$nestedData[] = $total;
			$nestedData[] = $estado;

			$menudrop="<div class=\"btn-group\">
								<a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\"><i class=\"fa fa-user icon-white\"></i> Menu<span class=\"caret\"></span></a>
								<ul class=\"dropdown-menu dropdown-primary\">";
						//$menudrop.="<li><a data-toggle='modal' href='anular_factura.php?id_factura=" .  $id_factura_dia."&process=formDelete"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i> Anular</a></li>";
			$menudrop.="<li><a data-toggle='modal' href='imprimir_factura_dia.php?id_factura=".$id_factura_dia."&fecha=".$fecha."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-print\"></i> Imprimir</a></li>";

			$menudrop.="<li><a data-toggle='modal' href='ver_factura_dia.php?id_factura=".$id_factura_dia."&fecha=".$fecha."' data-target='#viewModalFact' data-refresh='true'><i class=\"fa fa-check\"></i> Ver Factura</a></li>"	;
			$menudrop.="<li><a href='editar_factura_dia.php?id_factura=".$id_factura_dia."&fecha=".$fecha."'><i class=\"fa fa-pencil\"></i> Editar Factura</a></li>"	;
							$menudrop.="	</ul>
										</div>

										";
		$nestedData[] = $menudrop;
			$data[] = $nestedData;
	}

}
else{
	$empty="";
	$nestedData=array();
	$count_fact_dia=1;
			$nestedData[] = $empty;
			$nestedData[] = $empty;
			$nestedData[] =$empty;
			$nestedData[] =$empty;
			$nestedData[] =$empty;
			$data[] = $nestedData;
}
	$json_data = array(
		//	"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $count_fact_dia),  // total number of records
			"recordsFiltered" => intval( $count_fact_dia), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
			);

echo json_encode($json_data);  // send data as json format


?>
