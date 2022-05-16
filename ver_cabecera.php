<?php
include ("_core.php");
function initial(){
	//$numero_doc = $_REQUEST ['numero_doc'];
	$id_conf = $_REQUEST["id_conf"];
	//$alias_tipodoc = $_REQUEST ['alias_tipodoc'];
	$sql_pos = _query("SELECT * FROM config_pos WHERE id_config_pos = '$id_conf'");
  	$cuenta = _num_rows($sql_pos);
  	if($cuenta > 0)
	{
	    $row = _fetch_array($sql_pos);
	    $header1=$row["header1"];
	    $header2=$row["header2"];
	    $header3=$row["header3"];
	    $header4=$row["header4"];
	    $header5=$row["header5"];
	    $header6=$row["header6"];
	    $header7=$row["header7"];
	    $header8=$row["header8"];
	    $header9=$row["header9"];
	    $header10=$row["header10"];
	    $footer1=$row["footer1"];
	    $footer2=$row["footer2"];
	    $footer3=$row["footer3"];
	    $footer4=$row["footer4"];
	    $footer5=$row["footer5"];
	    $footer6=$row["footer6"];
	    $footer7=$row["footer7"];
	    $footer8=$row["footer8"];
	    $footer9=$row["footer9"];
	    $footer10=$row["footer10"];
	}
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$id_sucursal = $_SESSION["id_sucursal"];
	$sql_sucursal = _query("SELECT * FROM sucursal WHERE id_sucursal = '$id_sucursal'");
	$suc = _fetch_array($sql_sucursal);
	$nombre = $suc["descripcion"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	//permiso del script

?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Detalles de Cabecera</h4>
</div>
<div class="modal-body">
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row" id="row1">
			<div class="col-lg-12">
				<div class='alert alert-success text-center' style='font-weight: bold;'>
					<label style='font-size: 15px;'><?php echo $nombre;?></label>
				</div>
				<?php
					//permiso del script
					if ($links!='NOT' || $admin=='1' ){
				?>

				<table class="table table-bordered table-striped" id="tableview">
					<thead>
						<tr>
							<th><label style='font-size: 15px;'>Campo</label></th>
							<th><label style='font-size: 15px;'>Descripción</label></th>
							<th><label style='font-size: 15px;'>Campo</label></th>
							<th><label style='font-size: 15px;'>Descripción</label></th>
						</tr>
					</thead>
					<tbody>
						<?php
							echo "
								<tr>
									<td><label style='font-size: 15px;'>Header1</label></td>
									<td>".$header1."</td>
									<td><label style='font-size: 15px;'>Footer1</label></td>
									<td>".$footer1."</td>
								</tr>";
							echo "
								<tr>
									<td><label style='font-size: 15px;'>Header2</label></td>
									<td>".$header2."</td>
									<td><label style='font-size: 15px;'>Footer2</label></td>
									<td>".$footer2."</td>
								</tr>";
							echo "
								<tr>
									<td><label style='font-size: 15px;'>Header3</label></td>
									<td>".$header3."</td>
									<td><label style='font-size: 15px;'>Footer3</label></td>
									<td>".$footer3."</td>
								</tr>";
							echo "
								<tr>
									<td><label style='font-size: 15px;'>Header4</label></td>
									<td>".$header4."</td>
									<td><label style='font-size: 15px;'>Footer4</label></td>
									<td>".$footer4."</td>
								</tr>";
							echo "
								<tr>
									<td><label style='font-size: 15px;'>Header5</label></td>
									<td>".$header5."</td>
									<td><label style='font-size: 15px;'>Footer5</label></td>
									<td>".$footer5."</td>
								</tr>";
							echo "
								<tr>
									<td><label style='font-size: 15px;'>Header6</label></td>
									<td>".$header6."</td>
									<td><label style='font-size: 15px;'>Footer6</label></td>
									<td>".$footer6."</td>
								</tr>";
							echo "
								<tr>
									<td><label style='font-size: 15px;'>Header7</label></td>
									<td>".$header7."</td>
									<td><label style='font-size: 15px;'>Footer7</label></td>
									<td>".$footer7."</td>
								</tr>";
							echo "
								<tr>
									<td><label style='font-size: 15px;'>Header8</label></td>
									<td>".$header8."</td>
									<td><label style='font-size: 15px;'>Footer8</label></td>
									<td>".$footer8."</td>
								</tr>";
							echo "
								<tr>
									<td><label style='font-size: 15px;'>Header9</label></td>
									<td>".$header9."</td>
									<td><label style='font-size: 15px;'>Footer9</label></td>
									<td>".$footer9."</td>
								</tr>";
							echo "
								<tr>
									<td><label style='font-size: 15px;'>Header10</label></td>
									<td>".$header10."</td>
									<td><label style='font-size: 15px;'>Footer10</label></td>
									<td>".$footer10."</td>
								</tr>";
						?>
					</tbody>
				</table>
			</div>
		</div>
			<?php
			echo "<input type='hidden' nombre='id_banco' id='id_banco' value=''>";
			?>
		</div>

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" data-dismiss="modal">Salir</button>

</div>
<!--/modal-footer -->

<?php
	} //permiso del script
	else
	{
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}
initial();
?>
