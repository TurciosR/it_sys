<?php
	include ("_core.php");
	// Page setup
	$_PAGE = array ();
	$_PAGE ['title'] = 'Administrar Factura';
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
	
 	//$sql="SELECT * FROM factura";
	$sql="SELECT factura.*, cliente.nombre,cliente.apellido 
	FROM factura JOIN cliente ON factura.id_cliente=cliente.id_cliente
	WHERE finalizada=0 AND anulada=0";	
	$result=mysql_query($sql);
	$count=mysql_num_rows($result);

?>

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row" id="row1">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<?php 
				//if ($admin=='t' && $active=='t'){
				echo"
					<div class='ibox-title'>
					<a href='facturacion.php' class='btn btn-primary' role='button'><i class='fa fa-plus icon-large'></i> Agregar factura</a>
					</div>";
				
				?>
				<div class="ibox-content">
					<!--load datables estructure html-->
					<header>
						<h4>Administrar facturas</h4>
					</header>
					<section>
						<table class="table table-striped table-bordered table-hover"id="editable">
							<thead>
								<tr>
									<th>Id factura</th>
									<th>Id Cliente</th>
									<th>Fecha</th>									
									<th>Numero Doc</th>
									<th>Total</th>
									<th>Acci&oacute;n</th>
								</tr>
							</thead>
							<tbody> 
				<?php	
 					if ($count>0){
						for($i=0;$i<$count;$i++){
							$row=mysql_fetch_array($result);
							$numero_doc=$row['numero_doc'];
							$anulada=$row['anulada'];
							$cliente=$row['nombre']." ".$row['apellido']; 
							if($anulada==1)
								$txt_anulada=' (ANULADA)';
							else
								$txt_anulada='';
							echo "<tr>";
							echo "<td>".$row['id_factura']."</td>";
							echo "<td>".$cliente."</td>";
							echo "<td>".$row['fecha']."</td>";
							echo "<td>".$numero_doc.$txt_anulada."</td>";
							
							echo "<td>"."$ ".$row['total']."</td>";
							echo "<td class='td-actions'>
									<a href='javascript:;' class='btn btn-small btn-primary'>
										<i class='btn-icon-only icon-ok'></i>										
									</a>
									
									<a href='javascript:;' class='btn btn-small'>
										<i class='btn-icon-only icon-remove'></i>										
									</a>
								</td>";
							/*
							echo"<td>";
							echo "<div class=\"btn-group\">
								<a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\"><i class=\"fa fa-user icon-white\"></i> Menu<span class=\"caret\"></span></a>
								<ul class=\"dropdown-menu dropdown-primary\">";
								
									echo "<li><a data-toggle='modal' href='reimprimir_factura.php?id_factura=".$row['id_factura']."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-print\"></i> Reimprimir</a></li>";				
									echo "<li><a data-toggle='modal' href='anular_factura.php?id_factura=" .  $row ['id_factura']."&process=formDelete"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i> Anular</a></li>";
									
									
							echo "	</ul>
									</div>";										
							echo "	</td>";
							*/			
							echo "	</tr>";
						}
					}
		
				?>			
							</tbody>		
						</table>
						 <input type="hidden" name="autosave" id="autosave" value="false-0">	
					</section>   
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
               	</div><!--div class='ibox-content'-->
       		</div><!--<div class='ibox float-e-margins' -->
		</div> <!--div class='col-lg-12'-->
	</div> <!--div class='row'-->  
</div><!--div class='wrapper wrapper-content  animated fadeInRight'-->			
<?php    
	include("footer.php");
	echo" <script type='text/javascript' src='js/funciones/funciones_admin_fact.js'></script>"; 	                         	     
?>
