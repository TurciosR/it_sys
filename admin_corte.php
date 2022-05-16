 	<?php
  include ("_core.php");

	function initial()
	{// Page setup
		$_PAGE = array ();
		$_PAGE ['title'] = 'Administrar Cortes';
		$_PAGE ['links'] = null;
		$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
		$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
		$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
		$_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
		$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
		$_PAGE ['links'] .= '<link href="css/plugins/select2/select2-bootstrap.css" rel="stylesheet">';
		$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
		$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
		$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
		$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
		$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
		$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
		$_PAGE ['links'] .= '<link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
		include_once "header.php";
		include_once "main_menu.php";
		$id_sucursal=$_SESSION['id_sucursal'];

		$id_user = $_SESSION["id_usuario"];
		$sql_user = _query("SELECT * FROM empleados WHERE id_empleado = '$id_user'");
		$row_user = _fetch_array($sql_user);
		$tipo_usuario = $row_user["admin"];

		date_default_timezone_set('America/El_Salvador');
		$fecha_actual = date("Y-m-d");
		$hora_actual = date("H:i:s");
	 	$id_user=$_SESSION["id_usuario"];
		$admin=$_SESSION["admin"];
		$fecha_2 = date('Y-m-d');
		$fecha_1 = date('Y-m-01');

		$uri = $_SERVER['SCRIPT_NAME'];
		$filename=get_name_script($uri);
		$links=permission_usr($id_user,$filename);
		//permiso del script
		if ($links!='NOT' || $admin=='1' ){
	?>
	<input type="hidden" name="admin" id="admin" value="<?php echo $admin;?>">
	<input type="hidden" name="id_emple" id="id_emple" value="<?php echo $id_user;?>">
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row" id="row1">
			<div class="col-lg-12">
				<div class="ibox float-e-margins">
					<!--?php
					echo"<div class='ibox-title'>
						<a href='facturacion.php' class='btn btn-primary' role='button'><i class='fa fa-plus icon-large'></i> Agregar factura</a>
						</div>";
					?-->
					<div class="ibox-content">
						<!--load datables estructure html-->
						<header>
							<h4>Administrar Cortes</h4>
						</header>
						<section>
							<?php
								$sql_apertura = _query("SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal' AND id_empleado = '$id_user'");
                //echo "SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal' AND id_empleado = '$id_user'";
								//echo "SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal' AND id_empleado = '$id_user'";
	    						$cuenta_apertura = _num_rows($sql_apertura);
	    						if($cuenta_apertura != 0 )
		    					{
	    							///////////////////////////////////////////////////////////////////////////////////////////
	    							$row_apertura = _fetch_array($sql_apertura);
	    							$id_apertura = $row_apertura["id_apertura"];
	    							$monto_apertura = $row_apertura["monto_apertura"];
	    							$id_empleado = $row_apertura["id_empleado"];
	    							$fecha_apertura = $row_apertura["fecha"];
	    							$hora_apertura = $row_apertura["hora"];
	    							$turno = $row_apertura["turno"];
                    $caja = $row_apertura["caja"];
                    $sistema = $row_apertura["sistema"];
	    							$turno_vigente = $row_apertura["turno_vigente"];
	    							$sql_empleado = _query("SELECT * FROM empleados WHERE id_empleado = '$id_empleado'");
	    							$rr = _fetch_array($sql_empleado);
	    							$nombre = $rr["nombre"];
	    							$turno_txt = "";
										echo "<input type='hidden' id='aper_id' name='aper_id' value='".$id_apertura."'>";
	    							/////////////////////////////////////////////////////////////////////////////////////////////
	    							$sql_corte = _query("SELECT * FROM factura WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND id_sucursal = '$id_sucursal' AND pagada = 1 AND anulada = 0 AND tipo_pago != 'CRE'");
									$cuenta = _num_rows($sql_corte);
                  //echo "SELECT * FROM factura WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND id_sucursal = '$id_sucursal' AND pagada = 1 AND anulada = 0 AND tipo_pago != 'CRE'";
									$total_tike = 0;
									$total_factura = 0;
									$total_credito_fiscal = 0;
									$total_dev = 0;
									if($cuenta > 0)
									{
										while ($row_corte = _fetch_array($sql_corte))
										{
											$id_factura = $row_corte["id_factura"];
								            $anulada = $row_corte["anulada"];
								            $subtotal = $row_corte["subtotal"];
								            $suma = $row_corte["sumas"];
								            $iva = $row_corte["iva"];
								            $total = $row_corte["total"];
								            $numero_doc = $row_corte["numero_doc"];

								            $ax = explode("_", $numero_doc);
								            $numero_co = $ax[0];
								            $alias_tipodoc = $ax[1];


											if($alias_tipodoc == 'TIK')
								            {
								                $total_tike += $total;
								            }
								            else if($alias_tipodoc == 'COF')
								            {
								                $total_factura += $total;
								            }
								            else if($alias_tipodoc == 'CCF')
								            {
								                $total_credito_fiscal += $total;
								            }



										}
									}

									$total_corte = $total_tike + $total_factura + $total_credito_fiscal;
	    						?>

			                        <div class="row">
			                        <input type="hidden" name="id_apertura" id="id_apertura" value="<?php echo $id_apertura;?>">
			                        <input type="hidden" name="caja_id" id="caja_id" value="<?php echo $caja;?>">
			                        	<table class="table table-bordered">
			                        		<thead>
			                        			<tr>
			                        				<th colspan="3" style="text-align: center"><label class="badge badge-success" style="font-size: 15px; ">Apertura Vigente</label></th>
			                        			</tr>
			                        			<tr>
			                        				<th>Nombre: <?php echo $nombre;?></th>
			                        				<th>Fecha Apertura: <?php echo ED($fecha_apertura);?></th>
			                        				<th>Hora Apertura: <?php echo $hora_apertura;?></th>
			                        			</tr>
			                        			<tr>
			                        				<th>Monto Apertura: <?php echo "$".$monto_apertura;?></th>
			                        				<th>Turno: <?php echo $turno;?></th>
			                        				<th>Monto Registrado: <?php echo $total_corte;?></th>
			                        			</tr>
			                        			<?php
			                        				$sql_d_ap = _query("SELECT * FROM detalle_apertura WHERE id_apertura = '$id_apertura' AND vigente = 1 AND id_usuario = '$id_user'");
			                        				$cuenta_a = _num_rows($sql_d_ap);
			                        				if($cuenta_a == 1)
			                        				{
                                        $text2 = "";
				                        			?>
				                        			<tr>
				                        				<th colspan="3" style="text-align: center">
				                        					<a <?php echo "href='corte_caja_diario.php?aper_id=".$id_apertura."'";?> id="generar_corte" name="generar_corte" class="btn btn-primary m-t-n-xs"> Realizar Corte</a>
				                        					<?php if($turno_vigente == 1){

                                          ?>
				                        					<a id="cerrar_turno" name="cerrar_turno" class="btn btn-primary m-t-n-xs">Cerrar Turno</a>
				                        					<?php
				                        					}
				                        					?>
				                        				</th>
				                        			</tr>
				                        			<?php
			                        				}
			                        				else
			                        				{
			                        					$sql_d_ap1 = _query("SELECT * FROM detalle_apertura WHERE id_apertura = '$id_apertura' AND vigente = 1");
			                        					$row_sp1 = _fetch_array($sql_d_ap1);
			                        					$id_d_ap = $row_sp1["id_detalle"];
			                        					$emp = $row_sp1["id_usuario"];
			                        					if($emp != 0)
			                        					{
			                        						$sql_empleado1 = _query("SELECT * FROM empleados WHERE id_empleado = '$emp'");
          							    							$rr1 = _fetch_array($sql_empleado1);
          							    							$nombre1 = $rr1["nombre"];
          							    							if($admin != 1)
          							    							{
          							    								echo "<tr>";
          					                        					echo "<th colspan='3' style='text-align: center'>";
          					                        					echo "Ya existe un turno vigente realizado por ".$nombre1;
          					                        					echo "</th>";
          					                        					echo "</tr>";
          							    							}
          							    							else
          							    							{
          							    								echo "<tr>";
					                        					echo "<th colspan='3' style='text-align: center'>";
					                        					echo "Ya existe un turno vigente realizado por ".$nombre1;
					                        					echo "</th>";
					                        					echo "</tr>";
					                        					echo "<tr>";
					                        					echo "<th colspan='3' style='text-align: center'>";
					                        					echo "<a href='corte_caja_diario.php?aper_id=".$id_apertura."' id='generar_corte' name='generar_corte' class='btn btn-primary m-t-n-xs' > Realizar Corte</a>";
                                            echo " <a id='cerrar_turno' name='cerrar_turno' class='btn btn-primary m-t-n-xs'>Cerrar Turno Vigente</a>";
					                        					echo "</th>";
					                        					echo "</tr>";
          							    							}

			                        					}
			                        					else
			                        					{
			                        						echo "<tr>";
				                        					echo "<th colspan='3' style='text-align: center'>";
				                        					echo "<a id='apertura_turno' name='apertura_turno' class='btn btn-primary m-t-n-xs' >Iniciar Turno</a>";
				                        					echo "</th>";
				                        					echo "</tr>";
				                        					echo "<input type='hidden' class='id_d_ap1' id='id_d_ap1' value='".$id_d_ap."'>";
			                        					}

			                        				}

                                      if($sistema == 1)
                                      {
                                        echo "<tr>";
                                        echo "<th colspan='3' style='text-align: center'>";
                                        echo "<label style='font-size: 15px;'>Esta apertura fue realizada por el sistema de forma automatica, esto se debe a que se excedio el tiempo limite.</label>";
                                        echo "</th>";
                                        echo "</tr>";
                                      }
			                        			?>

			                        		</thead>
			                        	</table>
			                        </div>
	    						<?php
	    						}
	    						else
	    						{
										if($admin == 1)
										{
                      echo "ADMIN";

											?>
											<div class="">
												<table class="table table-bordered">
													<thead>
														<tr>
															<td>
															<select class="select col-lg-6" name="id_caja" id="id_caja">
																<?php
																		$sql_caja = _query("SELECT * FROM caja WHERE activa = 1 ORDER BY id_caja  ASC");
																		while ($row_caja = _fetch_array($sql_caja))
																		{
																			$id_caja = $row_caja["id_caja"];
																			$nombre = $row_caja["nombre"];
																			echo "<option value='".$id_caja."'>".$nombre."</option>";
																		}

																?>
															</select>
															</td>
														</tr>
													</thead>
												</table>
												<div id="caja_caja">

												</div>
											</div>
                      <input type="hidden" name="caja_id" id="caja_id" value="0">
											<?php


										}
										else
										{

											$sql_coprueba = _query("SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal'");
		    							$cuenta_prueba = _num_rows($sql_coprueba);
		    							if ($cuenta_prueba > 0)
		    							{
		    								$row_comprueba = _fetch_array($sql_coprueba);
		    								$id_empleadox = $row_comprueba["id_empleado"];
                        $id_aperturac = $row_comprueba["id_apertura"];
                        $caja_x = $row_comprueba["caja"];
                        $id_empleado_p = $row_comprueba['id_empleado_p'];
                        $sql_d_ap1 = _query("SELECT * FROM detalle_apertura WHERE id_apertura = '$id_aperturac' AND vigente = 1");
                        $row_sp1 = _fetch_array($sql_d_ap1);
                        $id_d_ap = $row_sp1["id_detalle"];
                        $emp = $row_sp1["id_usuario"];
		    								$sql_em = _query("SELECT nombre FROM empleados WHERE id_empleado = '$id_empleadox'");
		    								$rrs = _fetch_array($sql_em);
		    								$nombre_em = $rrs["nombre"];
                        echo "<input type='hidden' name='id_apertura' id='id_apertura' value='".$id_aperturac."'>
                        <input type='hidden' name='caja_id' id='caja_id' value='".$caja_x."'>";
		    								if($id_empleadox != $id_user)
		    								{

  			    							echo "<div></div>
  			    							<div class='alert alert-warning text-center' style='font-weight: bold;' hidden>
  			    								<label style='font-size: 15px;'>Sin apertura de caja!!</label>
  			    								<br>
  			    								<br>";

  			    							echo "<a href='apertura_caja.php?id_caja=0' id='apertura' name='apertura' class='btn btn-primary m-t-n-xs'>Realizar Apertura</a>
  			    							</div>";

                          echo "<div></div>
  			    							<div class='alert alert-success text-center' style='font-weight: bold;'>";
                          echo "<table class='table'>";

                          echo "<tr>";
                          echo "<th colspan='3' style='text-align: center'><label class='badge badge-success' style='font-size: 15px; '>Apertura Vigente</label></th>";
                          echo "</tr>";
                          if($id_user != $id_empleado_p)
                          {
                            echo "<tr>";
                            echo "<th colspan='3' style='text-align: center'><label style='font-size: 15px; '>Esta apertura de caja fue realizada por ".$nombre_em."</label></th>";
                            echo "</tr>";
                          }
                          if($emp == 0)
                          {
                            echo "<tr>";
            								echo "<th colspan='3' style='text-align: center'>";
            								echo "<a id='apertura_turno' name='apertura_turno' class='btn btn-primary m-t-n-xs' >Iniciar Turno</a>";
            								echo "</th>";
            								echo "</tr>";
                          }
                          else
                          {
                              echo "<tr>";
              								echo "<th colspan='3' style='text-align: center'>";
              								echo "<label style='font-size: 15px; '>Actualmente la caja se encuentra en uso</label>";
              								echo "</th>";
              								echo "</tr>";
                          }

                          echo "</table>";
                          echo "<div>";
                          echo "<input type='hidden' class='id_d_ap1' id='id_d_ap1' value='".$id_d_ap."'>";
					    					 }
			    							}
			    							else
			    							{

  			    							echo "<div></div>
  			    							<div class='alert alert-warning text-center' style='font-weight: bold;'>
  			    								<label style='font-size: 15px;'>Sin apertura de caja</label>
  			    								<br>
  			    								<br>";

  			    							echo "<a href='apertura_caja.php?id_caja=0' id='apertura' name='apertura' class='btn btn-primary m-t-n-xs' >Realizar Apertura</a>
  			    							</div>";
			    							}
											}

	    						}
							?>

						</section>
						<section>
							<div class="widget">
							<div class="row">
								<div class="widget-content">
									<div class="col-lg-4">
                    <label>Desde:</label>
										<input type="text" name="fecha1" id="fecha1" class="form-control datepick" value="<?php echo date("01-m-Y");?>">
									</div>
									<div class="col-lg-4">
                    <label>Hasta</label>
										<input type="text" name="fecha2" id="fecha2" class="form-control datepick" value="<?php echo date("d-m-Y");?>">
									</div>
									<div class="col-lg-1" style="text-align: left;">
										<label>Buscar</label>
										<a id='search' name='search' class='btn btn-primary m-t-n-xs' style="margin-top: 0.5%;"><i class="fa fa-search"></i> Buscar</a>
									</div>
									<div class="col-lg-1" >
									</div>
									
								</div>
							</div>
							</div>
						</section>
						<section>
							<table class="table table-striped table-bordered table-hover" id="editable">
								<thead>
									<tr>
										<th>N°</th>
										<th>Fecha</th>
										<th>Hora</th>
										<th>Empleado</th>
										<th>Turno</th>
										<th>Tipo Corte</th>
										<th>Total</th>
										<th>Acci&oacute;n</th>
									</tr>
								</thead>
								<tbody id="caja_x">
								<?php
									$s = 1;
									$sql_cc =_query("SELECT * FROM controlcaja WHERE id_sucursal = '$id_sucursal' AND fecha_corte BETWEEN '$fecha_1' AND '$fecha_2' AND tipo_corte != '' ORDER BY id_corte DESC");
									$cuenta_corte = _num_rows($sql_cc);
									if($cuenta_corte > 0)
									{
										while ($row_cc = _fetch_array($sql_cc))
										{
											$id_corte = $row_cc["id_corte"];
											$fecha_corte = ED($row_cc["fecha_corte"]);
											$hora_corte = $row_cc["hora_corte"];
											$id_empleado_c = $row_cc["id_empleado"];
											$id_apertura = $row_cc["id_apertura"];
											$tipo_corte = $row_cc["tipo_corte"];
											$total = $row_cc["cashfinal"];
											$turno = $row_cc["turno"];

											$sql_empleadox = _query("SELECT * FROM empleados WHERE id_empleado = '$id_empleado_c'");
			    							$rr = _fetch_array($sql_empleadox);
			    							$nombre = $rr["nombre"];



			    							echo "<tr>";
			    							echo "<td>".$s."</td>";
			    							echo "<td>".$fecha_corte."</td>";
			    							echo "<td>".$hora_corte."</td>";
			    							echo "<td>".$nombre."</td>";
			    							echo "<td>".$turno."</td>";
			    							echo "<td>".$tipo_corte."</td>";
			    							echo "<td>".number_format($total,2,".",",")."</td>";
			    							echo "<td><div class=\"btn-group\">
													<a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\"><i class=\"fa fa-user icon-white\"></i> Menu<span class=\"caret\"></span></a>
													<ul class=\"dropdown-menu dropdown-primary\">";

														echo "
														<li><a data-toggle='modal' href='imprimir_corte.php?id_corte=".$id_corte."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-print\"></i> Imprimir</a></li>
														";
                            echo "
														<li><a href='corte_caja_pdf.php?id_corte=".$id_corte."' target='_blank'><i class=\"fa fa-print\"></i> Imprimir Reporte</a></li>
														";


												echo "	</ul>
															</div>
															</td>
															</tr>";
			    							$s += 1;
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
								<div class='modal-content'></div><!-- /.modal-content -->
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
		echo" <script type='text/javascript' src='js/funciones/funciones_corte_caja.js'></script>";
		} //permiso del script
	else {
    $mensaje = mensaje_permiso();
		echo "<br><br>$mensaje</div></div></div></div>";
		include "footer.php";
		}
	}
	function caja()
	{
		$admin=$_SESSION["admin"];
		$id_caja = $_POST["id_caja"];
		$id_empleado1 = $_POST["id_empleado"];
		$id_sucursal = $_SESSION["id_sucursal"];
		date_default_timezone_set('America/El_Salvador');
		$fecha_actual = date("Y-m-d");
		$hora_actual = date("H:i:s");
		$sql_inicio = _query("SELECT * FROM apertura_caja WHERE caja = '$id_caja' AND vigente = 1 AND id_sucursal = '$id_sucursal'");
		$cuenta = _num_rows($sql_inicio);
		$total_corte = 0;
		if($cuenta > 0)
		{
			$row_apertura = _fetch_array($sql_inicio);
			$id_apertura = $row_apertura["id_apertura"];
			$monto_apertura = $row_apertura["monto_apertura"];
			$id_empleado = $row_apertura["id_empleado"];
			$fecha_apertura = $row_apertura["fecha"];
			$hora_apertura = $row_apertura["hora"];
			$turno = $row_apertura["turno"];
			$turno_vigente = $row_apertura["turno_vigente"];
			$sql_empleado = _query("SELECT * FROM empleados WHERE id_empleado = '$id_empleado'");
			$rr = _fetch_array($sql_empleado);
			$nombre = $rr["nombre"];
			$turno_txt = "";
			echo "<input type='hidden' id='aper_id' name='aper_id' value='".$id_apertura."'>";
			/////////////////////////////////////////////////////////////////////////////////////////////
			$sql_corte = _query("SELECT * FROM factura WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND id_sucursal = '$id_sucursal' AND finalizada = 1 AND anulada = 0");
			//echo "SELECT * FROM factura WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND id_sucursal = '$id_sucursal' AND finalizada = 1 AND anulada = 0";
			$cuenta = _num_rows($sql_corte);
			$total_tike = 0;
			$total_factura = 0;
			$total_credito_fiscal = 0;
			$total_dev = 0;
			if($cuenta > 0)
			{
				while ($row_corte = _fetch_array($sql_corte))
				{
					$id_factura = $row_corte["id_factura"];
					$anulada = $row_corte["anulada"];
					$subtotal = $row_corte["subtotal"];
					$suma = $row_corte["sumas"];
					$iva = $row_corte["iva"];
					$total = $row_corte["total"];
					$numero_doc = $row_corte["numero_doc"];

					$ax = explode("_", $numero_doc);
					$numero_co = $ax[0];
					$alias_tipodoc = $ax[1];


					if($alias_tipodoc == 'TIK')
					{
							$total_tike += $total;
					}
					else if($alias_tipodoc == 'COF')
					{
							$total_factura += $total;
					}
					else if($alias_tipodoc == 'CCF')
					{
							$total_credito_fiscal += $total;
					}
				}
			}

		$total_corte = $total_tike + $total_factura + $total_credito_fiscal;
?>
		<div class="row">
		<input type="hidden" name="id_apertura" id="id_apertura" value="<?php echo $id_apertura;?>">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th colspan="3" style="text-align: center"><label class="badge badge-success" style="font-size: 15px; ">Apertura Vigente</label></th>
					</tr>
					<tr>
						<th>Nombre: <?php echo $nombre;?></th>
						<th>Fecha Apertura: <?php echo ED($fecha_apertura);?></th>
						<th>Hora Apertura: <?php echo $hora_apertura;?></th>
					</tr>
					<tr>
						<th>Monto Apertura: <?php echo "$".$monto_apertura;?></th>
						<th>Turno: <?php echo $turno;?></th>
						<th>Monto Registrado: <?php echo $total_corte;?></th>
					</tr>
					<?php
						$sql_d_ap = _query("SELECT * FROM detalle_apertura WHERE id_apertura = '$id_apertura' AND vigente = 1 AND id_usuario = '$id_empleado1'");
						//echo "SELECT * FROM detalle_apertura WHERE id_apertura = '$id_apertura' AND vigente = 1 AND id_usuario = '$id_empleado'";
						$cuenta_a = _num_rows($sql_d_ap);
						if($cuenta_a == 1)
						{
						?>
						<tr>
							<th colspan="3" style="text-align: center">
								<a <?php echo "href='corte_caja_diario.php?aper_id=".$id_apertura."'";?> id="generar_corte" name="generar_corte" class="btn btn-primary m-t-n-xs" > Realizar Corte</a>
								<?php if($turno_vigente == 1){?>
								<a data-toggle='modal' id="cerrar_turno" name="cerrar_turno" class="btn btn-primary m-t-n-xs" <?php  echo "href='cierre_turno.php?id_apertura=".$id_apertura."&turno=".$turno."&val=0'"?>
			          data-target='#viewModal' data-refresh='true' >Cerrar Turno</a>
								<?php
								}
								?>
							</th>
						</tr>
						<?php
						}
						else
						{
							$sql_d_ap1 = _query("SELECT * FROM detalle_apertura WHERE id_apertura = '$id_apertura' AND vigente = 1");
							$row_sp1 = _fetch_array($sql_d_ap1);
							$id_d_ap = $row_sp1["id_detalle"];
							$emp = $row_sp1["id_usuario"];
							if($emp != 0)
							{
								$sql_empleado1 = _query("SELECT * FROM empleados WHERE id_empleado = '$emp'");
								$rr1 = _fetch_array($sql_empleado1);
								$nombre1 = $rr1["nombre"];
								if($admin != 1)
								{
									echo "<tr>";
														echo "<th colspan='3' style='text-align: center'>";
														echo "Ya existe un turno vigente realizado por ".$nombre1;
														echo "</th>";
														echo "</tr>";
								}
								else
								{
									  echo "<tr>";
  									echo "<th colspan='3' style='text-align: center'>";
  									echo "Ya existe un turno vigente realizado por ".$nombre1;
  									echo "</th>";
  									echo "</tr>";
  									echo "<tr>";
  									echo "<th colspan='3' style='text-align: center'>";
  									echo "<a href='corte_caja_diario.php?aper_id=".$id_apertura."' id='generar_corte' name='generar_corte' class='btn btn-primary m-t-n-xs' > Realizar Corte</a>";
                    echo " <a id='cerrar_turno' name='cerrar_turno' class='btn btn-primary m-t-n-xs'>Cerrar Turno Vigente</a>";
  									echo "</th>";
  									echo "</tr>";
								}

							}
							else
							{
								echo "<tr>";
								echo "<th colspan='3' style='text-align: center'>";
								echo "<a id='apertura_turno' name='apertura_turno' class='btn btn-primary m-t-n-xs' >Iniciar Turno!!</a>";
								echo "</th>";
								echo "</tr>";
								echo "<input type='hidden' class='id_d_ap1' id='id_d_ap1' value='".$id_d_ap."'>";
							}

						}
					?>

				</thead>
			</table>
		</div>
<?php
		}
		else
		{
			$sql_coprueba = _query("SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal' AND caja = '$id_caja'");
			//echo "SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal'";
			$cuenta_prueba = _num_rows($sql_coprueba);
			if ($cuenta_prueba > 0)
			{
				$row_comprueba = _fetch_array($sql_coprueba);
				$id_empleadox = $row_comprueba["id_empleado"];
				$sql_em = _query("SELECT nombre FROM empleados WHERE id_empleado = '$id_empleadox'");
				$rrs = _fetch_array($sql_em);
				$nombre_em = $rrs["nombre"];
				if($id_empleadox != $id_empleado1)
				{
					echo "<div></div>
						<div class='alert alert-warning text-center' style='font-weight: bold;'>
							<label style='font-size: 15px;'>Ya existe una apertura de caja realizada ".$nombre_em."!!</label>
							<br>
							<label style='font-size: 15px;'>Debe de realizar el corte para poder iniciar una nueva apertura de caja.</label>

						</div>";
				}
			}
			else
			{
        $hora_valida = "18:59:59";
        $hora1 = strtotime($hora_actual);
        $hora2 = strtotime($hora_valida);
        if($hora1 > $hora2)
        {
          if($admin == 1)
          {
            $text2 = "";
          }
          else
          {
            $text2 = "disabled";
          }

        }
        else {
          $text2 = "";
        }
			echo "<div></div>
			<div class='alert alert-warning text-center' style='font-weight: bold;'>
				<label style='font-size: 15px;'>Sin apertura de caja</label>
				<br>
				<br>
				<a id='apertura' name='apertura' class='btn btn-primary m-t-n-xs aper'>Realizar Apertura</a>
			</div>";
			}
		}
	}
	function search()
	{
		$id_sucursal = $_SESSION["id_sucursal"];
		$fecha1 = MD($_POST["fecha1"]);
		$fecha2 = MD($_POST["fecha2"]);
		$s = 1;

		$sql_cc =_query("SELECT * FROM controlcaja WHERE id_sucursal = '$id_sucursal' AND fecha_corte BETWEEN '$fecha1' AND '$fecha2' AND tipo_corte != '' ORDER BY id_corte DESC");
		$cuenta_corte = _num_rows($sql_cc);
		$lista = "";
		if($cuenta_corte > 0)
		{
			while ($row_cc = _fetch_array($sql_cc))
			{
				$id_corte = $row_cc["id_corte"];
				$fecha_corte = ED($row_cc["fecha_corte"]);
				$hora_corte = $row_cc["hora_corte"];
				$id_empleado_c = $row_cc["id_empleado"];
				$id_apertura = $row_cc["id_apertura"];
				$tipo_corte = $row_cc["tipo_corte"];
				$total = $row_cc["cashfinal"];
				$turno = $row_cc["turno"];

				$sql_empleadox = _query("SELECT * FROM empleados WHERE id_empleado = '$id_empleado_c'");
				$rr = _fetch_array($sql_empleadox);
				$nombre = $rr["nombre"];


				$lista.= "<tr>";
				$lista.= "<td>".$s."</td>";
				$lista.= "<td>".$fecha_corte."</td>";
				$lista.= "<td>".$hora_corte."</td>";
				$lista.= "<td>".$nombre."</td>";
				$lista.= "<td>".$turno."</td>";
				$lista.= "<td>".$tipo_corte."</td>";
				$lista.= "<td>".number_format($total,2,".",",")."</td>";
        $lista.= "<td><div class=\"btn-group\">
          <a href=\"#\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\"><i class=\"fa fa-user icon-white\"></i> Menu<span class=\"caret\"></span></a>
          <ul class=\"dropdown-menu dropdown-primary\">";

            $lista.= "
            <li><a data-toggle='modal' href='imprimir_corte.php?id_corte=".$id_corte."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-print\"></i> Imprimir</a></li>
            ";
            $lista.= "
            <li><a href='corte_caja_pdf.php?id_corte=".$id_corte."' target='_blank'><i class=\"fa fa-print\"></i> Imprimir Reporte</a></li>
            ";


				$lista.= "	</ul>
								</div>
								</td>
								</tr>";
				$s += 1;
			}
		}
		echo $lista;

	}
  function apertura_auto()
  {
    date_default_timezone_set('America/El_Salvador');
    $hora_actual = date("H:i:s");
    $fecha_actual = date("Y-m-d");
    $n_ap = 0;
    $id_sucursal = $_POST["id_sucursal"];
    $sql_em = _query("SELECT apertura_automatica, empleado_apertura FROM sucursal WHERE id_sucursal = '$id_sucursal'");
    $row_em = _fetch_array($sql_em);
    $id_empleado = $row_em["empleado_apertura"];
    $apertura_auto = $row_em["apertura_automatica"];

    $sql_horario = _query("SELECT * FROM horarios WHERE id_sucursal = '$id_sucursal'");
    $row_horario = _fetch_array($sql_horario);
    $hora_apertura = $row_horario["apertura"];
    //echo $apertura_auto;
    if($apertura_auto == 1)
    {
      echo $hora_actual;
      $sql_caja = _query("SELECT * FROM caja WHERE id_sucursal = '$id_sucursal' AND activa = 1");
      $cuenta = _num_rows($sql_caja);
      if($cuenta > 0)
      {
        while($row = _fetch_array($sql_caja))
        {
          $caja = $row["id_caja"];
          $sql_apertura = _query("SELECT * FROM apertura_caja WHERE id_sucursal = '$id_sucursal' AND caja = '$caja' AND fecha = '$fecha_actual'");
          $cuenta_apertura = _num_rows($sql_apertura);
          if($cuenta_apertura == 0)
          {
            $hora1 = strtotime($hora_actual);
            $hora2 = strtotime($hora_apertura);
            if($hora1 >= $hora2)
            {
              $table = "apertura_caja";
              $form_data = array(
                  'fecha' => date("Y-m-d"),
                  'id_empleado' => $id_empleado,
                  'turno' => 1,
                  'monto_apertura' => "100",
                  'vigente' => 1,
                  'id_sucursal' => $id_sucursal,
                  'hora' => $hora_apertura,
                  'turno_vigente' => 1,
                  'caja' => $caja,
                  'monto_ch' => "125",
                  'monto_ch_actual' => "125",
                  'sistema' => 1,
                  );
                $insertar = _insert($table,$form_data);
                if($insertar)
                {
                  $id_apertura = _insert_id();
                  $tabla1 = "detalle_apertura";
                  $form_data1 = array(
                      'id_apertura' => $id_apertura,
                      'turno' => 1,
                      'id_usuario' => $id_empleado,
                      'fecha' => date("Y-m-d"),
                      'hora' => $hora_apertura,
                      'vigente' => 1,
                      'caja' => $caja,

                      );
                  $insert_de = _insert($tabla1,$form_data1);
                }
            }
          }
          else
          {
            $n_ap +=1;
          }
        }
      }
    }

    //echo $n_ap;
    if($n_ap == 0)
    {
      return true;
    }
    else
    {
      return false;
    }
    echo json_encode($xdatos);
  }
  function fin_turno()
  {
    date_default_timezone_set('America/El_Salvador');
    $hora_actual = date("H:i:s");
    $n_ap = 0;
    $id_sucursal = $_POST["id_sucursal"];
    $sql_em = _query("SELECT apertura_automatica, empleado_apertura FROM sucursal WHERE id_sucursal = '$id_sucursal'");
    $row_em = _fetch_array($sql_em);
    $id_empleado = $row_em["empleado_apertura"];
    $apertura_auto = $row_em["apertura_automatica"];
    $sql_horario = _query("SELECT * FROM horarios WHERE id_sucursal = '$id_sucursal'");
    $row_horario = _fetch_array($sql_horario);
    $hora_apertura = $row_horario["apertura"];
    $fin_turno = $row_horario["fin_turno_matutino"];
    $inicio_turno = $row_horario["inicio_turno_tarde"];

    if($apertura_auto == 1)
    {

      $sql_caja = _query("SELECT * FROM caja WHERE id_sucursal = '$id_sucursal' AND activa = 1");
      $cuenta = _num_rows($sql_caja);
      if($cuenta > 0)
      {
        while($row = _fetch_array($sql_caja))
        {
          $caja = $row["id_caja"];
          $sql_apertura = _query("SELECT * FROM apertura_caja WHERE id_sucursal = '$id_sucursal' AND vigente = 1 AND caja = '$caja'");
          $cuenta_apertura = _num_rows($sql_apertura);
          if($cuenta_apertura != 0)
          {
            $row_e = _fetch_array($sql_apertura);
            $id_apertura = $row_e["id_apertura"];

            $sql_turno_finalizado = _query("SELECT * FROM detalle_apertura WHERE id_apertura = '$id_apertura'");
            $cuenta_fin = _num_rows($sql_turno_finalizado);
            $sql_turno = _query("SELECT * FROM detalle_apertura WHERE id_apertura = '$id_apertura' AND vigente = 1");
            $row_turno = _fetch_array($sql_turno);
            $id_detalle = $row_turno["id_detalle"];
            $turno = $row_turno["turno"];
            $hora_turno = $row_turno["hora"];
            $id_us = $row_turno["id_usuario"];
            $turno_nuevo = $turno + 1;
            //echo $fin_turno;
            $hora1 = strtotime($hora_actual);
            $hora2 = strtotime($fin_turno);
            $hora3 = strtotime($inicio_turno);
            if($hora1 >= $hora2 && $hora1 <= $hora3)
            {
              if($id_us != 0)
              {
                $ha = corteaaaa($id_apertura);
                if($ha)
                {
                  $tabla_turno = "detalle_apertura";
                  $form_data = array(
                    'vigente' => 0,
                  );
                  $w_t = "id_detalle='".$id_detalle."'";
                  $update = _update($tabla_turno, $form_data, $w_t);
                  //echo $id_detalle;
                  if($update)
                  {
                    //echo "OK";
                    $formx = array(
                      'id_apertura' => $id_apertura,
                      'turno' => $turno_nuevo,
                      'fecha' => date("Y-m-d"),
                      'hora' => $fin_turno,
                      'vigente' => 1,
                      'caja' => $caja,
                    );
                    $insertar_turno = _insert($tabla_turno, $formx);
                    if($insertar_turno)
                    {
                        $tabla1 = "apertura_caja";
                        $form_data1 = array(
                            'turno' => $turno_nuevo,
                            'turno_vigente' => 1,
                            );
                        $where_up = "id_apertura='".$id_apertura."'";
                        $update1 = _update($tabla1, $form_data1, $where_up);
                    }
                  }
                }

              }
            }
          }
          else
          {
            $n_ap +=1;
          }
        }
      }
    }

    if($n_ap == 0)
    {
      return true;
    }
    else
    {
      return false;
    }
    echo json_encode($xdatos);
  }
  function inicio_turno()
  {
    date_default_timezone_set('America/El_Salvador');
    $hora_actual = date("H:i:s");
    $n_ap = 0;
    $id_sucursal = $_POST["id_sucursal"];
    $sql_em = _query("SELECT apertura_automatica, empleado_apertura FROM sucursal WHERE id_sucursal = '$id_sucursal'");
    $row_em = _fetch_array($sql_em);
    $id_empleado = $row_em["empleado_apertura"];
    $apertura_auto = $row_em["apertura_automatica"];
    $sql_horario = _query("SELECT * FROM horarios WHERE id_sucursal = '$id_sucursal'");
    $row_horario = _fetch_array($sql_horario);
    $hora_apertura = $row_horario["apertura"];
    $fin_turno = $row_horario["fin_turno_matutino"];
    $inicio_turno = $row_horario["inicio_turno_tarde"];
    $cierre = $row_horario["cierre"];

    if($apertura_auto == 1)
    {

      $sql_caja = _query("SELECT * FROM caja WHERE id_sucursal = '$id_sucursal' AND activa = 1");
      $cuenta = _num_rows($sql_caja);
      if($cuenta > 0)
      {
        while($row = _fetch_array($sql_caja))
        {
          $caja = $row["id_caja"];
          $sql_apertura = _query("SELECT * FROM apertura_caja WHERE id_sucursal = '$id_sucursal' AND vigente = 1 AND caja = '$caja'");
          $cuenta_apertura = _num_rows($sql_apertura);
          if($cuenta_apertura != 0)
          {
            $row_e = _fetch_array($sql_apertura);
            $id_apertura = $row_e["id_apertura"];
            $hora1 = strtotime($hora_actual);
            $hora2 = strtotime($fin_turno);
            $hora3 = strtotime($inicio_turno);
            $hora4 = strtotime($cierre);
            $hora5 = strtotime($hora_apertura);
            $sql_turno_finalizado = _query("SELECT * FROM detalle_apertura WHERE id_apertura = '$id_apertura' AND id_usuario != 0 AND hora BETWEEN '$fin_turno' AND '$inicio_turno'");
            $cuenta_fin = _num_rows($sql_turno_finalizado);

            $sql_turno = _query("SELECT * FROM detalle_apertura WHERE id_apertura = '$id_apertura' AND vigente = 1 AND id_usuario = 0");
            $row_turno = _fetch_array($sql_turno);
            $id_detalle = $row_turno["id_detalle"];
            $turno = $row_turno["turno"];
            $hora_turno = $row_turno["hora"];
            $id_us = $row_turno["id_usuario"];
            $turno_nuevo = $turno + 1;
            //echo $fin_turno;

            if($hora1 >= $hora3)
            {
              if($cuenta_fin == 0)
              {
                $tabla_turno = "detalle_apertura";
                $form_data = array(
                  'id_usuario' => $id_empleado,
                  'hora' => $inicio_turno,
                  'caja' => $caja,
                );
                $w_t = "id_detalle='".$id_detalle."'";
                $update = _update($tabla_turno, $form_data, $w_t);
              }
            }
          }
          else
          {
            $n_ap +=1;
          }
        }
      }
    }

    if($n_ap == 0)
    {
      return true;
    }
    else
    {
      return false;
    }
    echo json_encode($xdatos);
  }

  function fin_apertura()
  {
    date_default_timezone_set('America/El_Salvador');
    $fecha_actual = date("Y-m-d");
  	$hora_actual = date('H:i:s');
    $id_sucursal = $_SESSION["id_sucursal"];

    $sql_em = _query("SELECT apertura_automatica, empleado_apertura FROM sucursal WHERE id_sucursal = '$id_sucursal'");
    $row_em = _fetch_array($sql_em);
    $id_empleado = $row_em["empleado_apertura"];
    $apertura_auto = $row_em["apertura_automatica"];

    $sql_horario = _query("SELECT * FROM horarios WHERE id_sucursal = '$id_sucursal'");
    $row_horario = _fetch_array($sql_horario);
    $hora_apertura = $row_horario["apertura"];
    $fin_turno = $row_horario["fin_turno_matutino"];
    $inicio_turno = $row_horario["inicio_turno_tarde"];
    $cierre = $row_horario["cierre"];

    $hora1 = strtotime($hora_actual);
    $hora2 = strtotime($fin_turno);
    $hora3 = strtotime($inicio_turno);
    $hora4 = strtotime($cierre);
    $hora5 = strtotime($hora_apertura);

    //echo "SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal' AND fecha = '$fecha_actual' LIMIT 1";
    if($apertura_auto == 1)
    {
      if($hora1 >= $hora4)
      {
        $sql_apertura = _query("SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal' AND fecha = '$fecha_actual' LIMIT 1");
      	$cuenta = _num_rows($sql_apertura);
        if($cuenta == 1)
        {
          $row_apertura = _fetch_array($sql_apertura);
        	$id_apertura = $row_apertura["id_apertura"];
        	$empleado = $row_apertura["id_empleado"];
        	$turno = $row_apertura["turno"];
        	$fecha_apertura = $row_apertura["fecha"];
        	$hora_apertura = $row_apertura["hora"];
        	$monto_apertura = $row_apertura["monto_apertura"];
        	$monto_ch = $row_apertura["monto_ch"];
        	$caja = $row_apertura["caja"];

          $tipo_corte = "Z";
        	/////////////////////////////////####################### DOCUMENTOS CORTE ZX ####################/////////////////////////////
        	$tike_min_2 = 0;
        	$tike_max_2 = 0;
        	$factura_min_2 = 0;
        	$factura_max_2 = 0;
        	$credito_fiscal_min_2 = 0;
        	$credito_fiscal_max_2 = 0;
        	$sql_min_max_2 = _query("SELECT MIN(num_fact_impresa) as minimo, MAX(num_fact_impresa) as maximo FROM factura WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND numero_doc LIKE '%TIK%' AND id_sucursal = '$id_sucursal' AND anulada = 0 UNION ALL SELECT MIN(num_fact_impresa) as minimo, MAX(num_fact_impresa) as maximo FROM factura WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND numero_doc LIKE '%COF%' AND id_sucursal = '$id_sucursal' AND anulada = 0 UNION ALL SELECT MIN(num_fact_impresa) as minimo, MAX(num_fact_impresa) as maximo FROM factura WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND numero_doc LIKE '%CCF%' AND id_sucursal = '$id_sucursal' AND anulada = 0" );
        	$cuenta_min_max_2 = _num_rows($sql_min_max_2);

        	if($cuenta_min_max_2)
        	{
        			$i = 1;
        			while ($row_min_max = _fetch_array($sql_min_max_2))
        			{
        					if($i == 1)
        					{
        							$tike_min_2 = $row_min_max["minimo"];
        							$tike_max_2 = $row_min_max["maximo"];
        							if($tike_min_2 > 0)
        							{
        									$tike_min_2 = $tike_min_2;
        							}
        							else
        							{
        									$tike_min_2 = 0;
        							}

        							if($tike_max_2 > 0)
        							{
        									$tike_max_2 = $tike_max_2;
        							}
        							else
        							{
        									$tike_max_2 = 0;
        							}
        					}
        					if($i == 2)
        					{
        							$factura_min_2 = $row_min_max["minimo"];
        							$factura_max_2 = $row_min_max["maximo"];
        							if($factura_min_2 != "")
        							{
        									$factura_min_2 = $factura_min_2;
        							}
        							else
        							{
        									$factura_min_2 = 0;
        							}

        							if($factura_max_2 != "")
        							{
        									$factura_max_2 = $factura_max_2;
        							}
        							else
        							{
        									$factura_max_2 = 0;
        							}
        					}
        					if($i == 3)
        					{
        							$credito_fiscal_min_2 = $row_min_max["minimo"];
        							$credito_fiscal_max_2 = $row_min_max["maximo"];
        							if($credito_fiscal_min_2 != "")
        							{
        									$credito_fiscal_min_2 = $credito_fiscal_min_2;
        							}
        							else
        							{
        									$credito_fiscal_min_2 = 0;
        							}

        							if($credito_fiscal_max_2 != "")
        							{
        									$credito_fiscal_max_2 = $credito_fiscal_max_2;
        							}
        							else
        							{
        									$credito_fiscal_max_2 = 0;
        							}
        					}
        					$i += 1;
        			}
        	}
        	/////////////////////////////////####################### FIN DOCUMENTOS PARA ZX #################/////////////////////////////

        	/////////////////////////############### VENTA XZ #############////////////////////////////////
        	$sql_corte_2 = _query("SELECT * FROM factura WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND id_sucursal = '$id_sucursal' AND anulada = 0");
        	$cuenta_2 = _num_rows($sql_corte_2);

        	$total_tike = 0;
        	$total_factura = 0;
        	$total_credito_fiscal = 0;
        	$t_tike = 0;
        	$t_factuta = 0;
        	$t_credito = 0;

        	if($cuenta_2 > 0)
        	{
        		while ($row_corte = _fetch_array($sql_corte_2))
        		{
        			$id_factura = $row_corte["id_factura"];
        			$anulada = $row_corte["anulada"];
        			$subtotal = $row_corte["subtotal"];
        			$suma = $row_corte["sumas"];
        			$iva = $row_corte["iva"];
        			$total = $row_corte["total"];
        			$numero_doc = $row_corte["numero_doc"];
        			$tipo_pago = $row_corte["tipo_pago"];
        			$pagada = $row_corte["pagada"];
        			$alias_tipodoc = $row_corte["tipo_documento"];

        			if($alias_tipodoc == 'TIK')
        			{
        					$total_tike += $total;
        					$t_tike += 1;
        			}
        			else if($alias_tipodoc == 'COF')
        			{
        					$total_factura += $total;
        					$t_factuta += 1;
        			}
        			else if($alias_tipodoc == 'CCF')
        			{
        					$total_credito_fiscal += $total;
        					$t_credito += 1;
        			}
        		}
        	}
        	/////////////////////////############### FIN VENTA XZ #############////////////////////////////////

        	///////////////////////////////////############### DEVOLUCIONES ####################/////////////////////////////////////
        	$total_dev_2 = 0;
        	$t_dev_2 = 0;
        	$lista_dev = "";
        	$total_nc_2 = 0;
        	$t_nc_2 = 0;
        	$lista_nc = "";
        	$sql_dev = _query("SELECT * FROM factura WHERE fecha = '$fecha_apertura' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND id_apertura_pagada ='$id_apertura' AND turno = '$turno'");
        	//echo "SELECT * FROM factura WHERE fecha = '$fecha_apertura' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND id_apertura_pagada ='$id_apertura'";
        	$cuenta_dev = _num_rows($sql_dev);
        	if($cuenta_dev > 0)
        	{
        		while ($row_dev = _fetch_array($sql_dev))
        		{
        			$id_factura = $row_dev["id_factura"];
              $anulada = $row_dev["anulada"];
              $subtotal = $row_dev["subtotal"];
              $suma = $row_dev["sumas"];
              $iva = $row_dev["iva"];
              $total = $row_dev["total"];
              $numero_doc = $row_dev["numero_doc"];
        			$tipo_pago = $row_dev["tipo_pago"];
        			$pagada = $row_dev["pagada"];
        			$tipo_documento = $row_dev["tipo_documento"];
        			$numero_im = $row_dev["num_fact_impresa"];

        			if($tipo_documento == 'DEV')
              {
        				$afecta = $row_dev["afecta"];
        				$sql_afecta = _query("SELECT * FROM factura WHERE id_factura = '$afecta'");
        				$row_afecta = _fetch_array($sql_afecta);
        				$id_afecta = $row_afecta["id_factura"];
        				$numero = $row_afecta["num_fact_impresa"];
        				$tipo_documento = $row_afecta["tipo_documento"];
        				$total_dev_2 += $total;
        				$t_dev_2 += 1;
        				$lista_dev .= $numero_im.",".$total.",".$numero.",".$tipo_documento."|";
              }
        			if($tipo_documento == 'NC')
              {
        				$afecta = $row_dev["afecta"];
        				$sql_afecta = _query("SELECT * FROM factura WHERE id_factura = '$afecta'");
        				$row_afecta = _fetch_array($sql_afecta);
        				$id_afecta = $row_afecta["id_factura"];
        				$numero = $row_afecta["num_fact_impresa"];
        				$tipo_documento = $row_afecta["tipo_documento"];
        				$total_nc_2 += $total;
        				$t_nc_2 += 1;
        				$lista_nc .= $numero_im.",".$total.",".$numero.",".$tipo_documento."|";
              }
        		}
        	}
        	/////////////////////////////////############### FIN DEVOLUCIONES #############///////////////////////////

        	//////////////////////////TOTALES XZ///////////////////////////////////////
        	$total_venta_xz = $total_tike + $total_factura + $total_credito_fiscal;
        	$total_docZ = $t_tike + $t_factuta + $t_credito;
        	$total_corte_z = $total_venta_xz - $total_dev_2 - $total_nc_2;
          $remesa = $total_corte_z;

          $tabla = "controlcaja";
        	$form_data = array(
        		'fecha_corte' => $fecha_actual,
        		'hora_corte' => $hora_actual,
        		'id_empleado' => $empleado,
        		'id_sucursal' => $id_sucursal,
        		'id_apertura' => $id_apertura,
        		'totalt' => $total_tike,
        		'totalf' => $total_factura,
        		'totalcf' => $total_credito_fiscal,
        		'totalgral' => $total_corte_z,
        		'cashfinal' => $total_corte_z,
        		'totalnot' => $t_tike,
        		'totalnof' => $t_factuta,
        		'totalnocf' => $t_credito,
        		'turno' => $turno,
        		'tinicio' => $tike_min_2,
        		'tfinal' => $tike_max_2,
        		'finicio' => $factura_min_2,
        		'ffinal' => $factura_max_2,
        		'cfinicio' => $credito_fiscal_min_2,
        		'cffinal' => $credito_fiscal_max_2,
        		'cashinicial' => $monto_apertura,
        		'tipo_corte' => $tipo_corte,
        		'totalnodev' => $t_dev_2,
        		'monto_ch' => $monto_ch,
        		'caja' => $caja,
        		'remesa' => $remesa,
        		'total_facturado' => $total_corte_z,
        		'saldo_caja' => $monto_ch,
        	);
          $sql_cajax = _query("SELECT correlativo_dispo FROM caja WHERE id_caja = '$caja'");
        	$rc = _fetch_array($sql_cajax);
        	$correlativo_dispo = $rc["correlativo_dispo"];
        	$nn_tik = $correlativo_dispo + 1;

          $sql_ = _query("SELECT * FROM controlcaja WHERE id_apertura = '$id_apertura' AND tipo_corte = 'Z'");
        	$cuentax = _num_rows($sql_);
        	if($cuentax == 0)
        	{
        		if($tipo_corte == "Z")
        		{
        			$extra = array('tiket' => $nn_tik ,);
        			$resultx = array_merge($form_data, $extra);
        			$table_apertura = "apertura_caja";
        			$form_up = array(
        				'vigente' => 0,
        				'monto_vendido' => $remesa,
        			);
        			$where_apertura = "id_apertura='".$id_apertura."'";
        			$up_apertura = _update($table_apertura, $form_up, $where_apertura);
        			if($up_apertura)
        			{
        				$tab = "detalle_apertura";
        				$form_d = array(
        					'vigente' => 0 , );
        				$ww = "id_apertura='".$id_apertura."' AND turno='".$turno."'";
        				$up_turno = _update($tab,$form_d, $ww);

        				$insertar = _insert($tabla, $resultx);
        				$id_cortex = _insert_id();
        				if($insertar)
        				{
        					$t = "caja";
        					$ff = array('correlativo_dispo' => $nn_tik,);
        					$wp = "id_caja='".$caja."'";
        					$upd = _update($t,$ff,$wp);


        					$explora = explode("|", $lista_dev);
                			for ($i=0; $i < ($t_dev_2) ; $i++) {
                				$data = explode(",", $explora[$i]);
                				$dev_n = $data[0];
                				$dev_p = $data[1];
                				$afecta = $data[2];
                				$tipo = $data[3];
                				$table_dev = "devoluciones_corte";
                				$form_dev = array(
                					'id_corte' => $id_cortex,
                					'n_devolucion' => $dev_n,
                					't_devolucion' => $dev_p,
                					'afecta' => $afecta,
                					'tipo' => $tipo,
        									'fecha' => $fecha_corte,
        									'tipo_corte' => $tipo_corte,
                					);
                				$inser_dev = _insert($table_dev, $form_dev);

                            	//$n++;
                			}
        					$explora1 = explode("|", $lista_nc);
                			for ($j=0; $j < ($t_nc_2) ; $j++) {
                				$data1 = explode(",", $explora1[$j]);
                				$nc_n = $data1[0];
                				$nc_p = $data1[1];
                				$afecta = $data1[2];
                				$table_nc = "nc_corte";
                				$form_nc = array(
                					'id_corte' => $id_cortex,
                					'n_nc' => $nc_n,
                					't_nc' => $nc_p,
                					'afecta' => $afecta,
                					'tipo' => "CREDITO FISCAL",
                					);
                				$inser_nc = _insert($table_nc, $form_nc);

                            	//$n++;
                			}
        				}
        			}
        		}

        		if($insertar)
        		{
        			return true;
        		}
        		else
        		{
        		    return false;
        		}
        	}
          echo json_encode($xdatos);
        }
      }
    }

  }

  function corteaaaa($aper_id)
  {
    date_default_timezone_set('America/El_Salvador');
    $fecha_actual = date("Y-m-d");
  	$hora_actual = date('H:i:s');
    $id_sucursal = $_SESSION["id_sucursal"];

    $sql_em = _query("SELECT apertura_automatica, empleado_apertura FROM sucursal WHERE id_sucursal = '$id_sucursal'");
    $row_em = _fetch_array($sql_em);
    $id_empleado = $row_em["empleado_apertura"];
    $apertura_auto = $row_em["apertura_automatica"];

    $sql_horario = _query("SELECT * FROM horarios WHERE id_sucursal = '$id_sucursal'");
    $row_horario = _fetch_array($sql_horario);
    $hora_apertura = $row_horario["apertura"];
    $fin_turno = $row_horario["fin_turno_matutino"];
    $inicio_turno = $row_horario["inicio_turno_tarde"];
    $cierre = $row_horario["cierre"];

    $hora1 = strtotime($hora_actual);
    $hora2 = strtotime($fin_turno);
    $hora3 = strtotime($inicio_turno);
    $hora4 = strtotime($cierre);
    $hora5 = strtotime($hora_apertura);

    $tipo_corte = "C";
    if($apertura_auto == 1)
    {
      $id_user=$_SESSION["id_usuario"];
    	$admin=$_SESSION["admin"];

    	$sql_apertura = _query("SELECT * FROM apertura_caja WHERE id_apertura= $aper_id AND vigente = 1 AND id_sucursal = '$id_sucursal'");
    	$cuenta = _num_rows($sql_apertura);
    	$row_apertura = _fetch_array($sql_apertura);
    	$id_apertura = $row_apertura["id_apertura"];
    	$tike_inicia = $row_apertura["tiket_inicia"];
    	$factura_inicia = $row_apertura["factura_inicia"];
    	$credito_inicia = $row_apertura["credito_fiscal_inicia"];
    	$empleado = $row_apertura["id_empleado"];
    	$dev_inicia = $row_apertura["dev_inicia"];
    	$turno = $row_apertura["turno"];
    	$fecha_apertura = $row_apertura["fecha"];
    	$hora_apertura = $row_apertura["hora"];
    	$monto_apertura = $row_apertura["monto_apertura"];
    	$monto_ch = $row_apertura["monto_ch"];
    	$caja = $row_apertura["caja"];

    	$hora_actual = date('H:i:s');

    	//////////////////////////////////################## MOVIMIENTOS DE CAJA Y ABONOS A CREDITO ##################///////////////////
    	$sql_caja = _query("SELECT * FROM mov_caja WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND id_sucursal = '$id_sucursal' AND turno = '$turno'");
    	$cuenta_caja = _num_rows($sql_caja);

    	$total_entrada_caja = 0;
    	$total_abono_tik_e = 0;
    	$total_abono_fac_e = 0;
    	$total_abono_ccf_e = 0;
    	$total_abono_tik_c = 0;
    	$total_abono_fac_c = 0;
    	$total_abono_ccf_c = 0;
    	$total_abono_tik_t = 0;
    	$total_abono_fac_t = 0;
    	$total_abono_ccf_t = 0;
    	$total_salida_caja = 0;
    	$total_viatico = 0;
    	if($cuenta_caja > 0)
    	{
    		while ($row_caja = _fetch_array($sql_caja))
    		{
    			$monto = $row_caja["valor"];
    			$entrada = $row_caja["entrada"];
    			$salida = $row_caja["salida"];
    			$viatico = $row_caja["viatico"];
    			$idtransase = $row_caja["idtransace"];
    			$tipo_doc = $row_caja["tipo_doc"];
    			if($entrada == 1 && $salida == 0 && $viatico == 0)
    			{
    				if($idtransase != 0)
    				{
    					$sql_abono = _query("SELECT * FROM abono_credito WHERE id_abono_credito = '$idtransase'");
    					$row_abono = _fetch_array($sql_abono);
    					$tipo_pago_abo = $row_abono["tipo_pago"];
    					if($tipo_pago_abo == "Efectivo")
    					{
    						if($tipo_doc == "TIK")
    						{
    							$total_abono_tik_e += $monto;
    						}
    						else if($tipo_doc == "COF")
    						{
    							$total_abono_fac_e += $monto;
    						}
    						else if($tipo_doc == "CCF")
    						{
    							$total_abono_ccf_e += $monto;
    						}
    					}
    					if($tipo_pago_abo == "Cheque")
    					{
    						if($tipo_doc == "TIK")
    						{
    							$total_abono_tik_c += $monto;
    						}
    						else if($tipo_doc == "COF")
    						{
    							$total_abono_fac_c += $monto;
    						}
    						else if($tipo_doc == "CCF")
    						{
    							$total_abono_ccf_c += $monto;
    						}
    					}
    					if($tipo_pago_abo == "Transferencia")
    					{
    						if($tipo_doc == "TIK")
    						{
    							$total_abono_tik_t += $monto;
    						}
    						else if($tipo_doc == "COF")
    						{
    							$total_abono_fac_t += $monto;
    						}
    						else if($tipo_doc == "CCF")
    						{
    							$total_abono_ccf_t += $monto;
    						}
    					}
    				}
    				else
    				{
    					$total_entrada_caja += $monto;
    				}

    			}
    			else if($salida == 1 && $entrada == 0 && $viatico == 0)
    			{
    				$total_salida_caja += $monto;
    			}
    			else if($viatico == 1 && $entrada == 0  && $salida == 0)
    			{
    				$total_viatico += $monto;
    			}
    		}
    	}
    	//////////////////////////////############ FIN MOVIMIENTO DE CAJA Y ABONOS CREDTITO ##############///////////////////////

    	//////////////////////////////################# VENTA PENDIENTE ################/////////////////////////////////////////
    	$total_tike_npago = 0;
    	$total_factura_npago = 0;
    	$total_credito_fiscal_npago = 0;
    	$total_tike_credito = 0;
    	$total_factura_credito = 0;
    	$total_fiscal_credito = 0;
    	$total_devolucion = 0;
    	$sql_pendiente = _query("SELECT * FROM factura WHERE fecha = '$fecha_actual'  AND id_sucursal = '$id_sucursal' AND anulada = 0 AND pagada = 0 AND tipo_pago = 'PEN'");
    	$cuenta1 = _num_rows($sql_pendiente);

    	if($cuenta1 > 0)
    	{
    		while ($row_pendiente = _fetch_array($sql_pendiente))
    		{
    			$id_factura = $row_pendiente["id_factura"];
    			$anulada = $row_pendiente["anulada"];
    			$subtotal = $row_pendiente["subtotal"];
    			$suma = $row_pendiente["sumas"];
    			$iva = $row_pendiente["iva"];
    			$total = $row_pendiente["total"];
    			$numero_doc = $row_pendiente["numero_doc"];
    			$tipo_pago = $row_pendiente["tipo_pago"];
    			$pagada = $row_pendiente["pagada"];
    			$tipo_documento = $row_pendiente["tipo_documento"];

    			if($tipo_documento == "TIK")
    			{
    				if($tipo_pago != "CRE")
    				{
    					$total_tike_npago += $total;
    				}
    				else
    				{
    					$total_tike_credito += $total;
    				}
    			}
    			else if($tipo_documento == "COF")
    			{
    				if($tipo_pago != "CRE")
    				{
    					$total_factura_npago += $total;
    				}
    				else
    				{
    					$total_factura_credito += $total;
    				}
    			}
    			else if($tipo_documento == "CCF")
    			{
    				if($tipo_pago != "CRE")
    				{
    					$total_credito_fiscal_npago += $total;
    				}
    				else
    				{
    					$total_fiscal_credito += $total;
    				}
    			}
    		}

    	}
    	$total_venta_credito = $total_tike_credito + $total_factura_credito + $total_fiscal_credito;
    	$total_venta_pendiente = $total_tike_npago + $total_factura_npago + $total_credito_fiscal_npago;

    	///////////////////////////////////############### FIN VENTA PENDIENTE ################//////////////////////

    	///////////////////////////////////############### DOCUMENTOS PARA CORTE CAJA ############//////////////////
    	$sql_min_max = _query("SELECT MIN(num_fact_impresa) as minimo, MAX(num_fact_impresa) as maximo FROM factura WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND numero_doc LIKE '%TIK%' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND turno = '$turno' AND tipo_pago != 'CRE' AND tipo_pago != 'PEN' UNION ALL SELECT MIN(num_fact_impresa) as minimo, MAX(num_fact_impresa) as maximo FROM factura WHERE fecha_pago = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND numero_doc LIKE '%COF%' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND turno = '$turno' AND tipo_pago != 'CRE' AND tipo_pago != 'PEN' UNION ALL SELECT MIN(num_fact_impresa) as minimo, MAX(num_fact_impresa) as maximo FROM factura WHERE fecha_pago = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND numero_doc LIKE '%DEV%' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND turno = '$turno' AND tipo_pago != 'CRE' AND tipo_pago != 'PEN' UNION ALL SELECT MIN(num_fact_impresa) as minimo, MAX(num_fact_impresa) as maximo FROM factura WHERE fecha_pago = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND numero_doc LIKE '%CCF%' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND turno = '$turno' AND tipo_pago != 'CRE' AND tipo_pago != 'PEN'");
    	$cuenta_min_max = _num_rows($sql_min_max);

    	$tike_min = 0;
    	$tike_max = 0;
    	$factura_min = 0;
    	$factura_max = 0;
    	$credito_fiscal_min = 0;
    	$credito_fiscal_max = 0;
    	$dev_min = 0;
    	$dev_max = 0;
    	$res_min = 0;
    	$res_max = 0;

    	if($cuenta_min_max)
    	{
    	  $i = 1;
    	  while ($row_min_max = _fetch_array($sql_min_max))
    	  {
    	      if($i == 1)
    	      {
    	          $tike_min = $row_min_max["minimo"];
    	          $tike_max = $row_min_max["maximo"];
    	          if($tike_min > 0)
    	          {
    	              $tike_min = $tike_min;
    	          }
    	          else
    	          {
    	              $tike_min = 0;
    	          }

    	          if($tike_max > 0)
    	          {
    	              $tike_max = $tike_max;
    	          }
    	          else
    	          {
    	              $tike_max = 0;
    	          }
    	      }
    	      if($i == 2)
    	      {
    	          $factura_min = $row_min_max["minimo"];
    	          $factura_max = $row_min_max["maximo"];
    	          if($factura_min != "")
    	          {
    	              $factura_min = $factura_min;
    	          }
    	          else
    	          {
    	              $factura_min = 0;
    	          }

    	          if($factura_max != "")
    	          {
    	              $factura_max = $factura_max;
    	          }
    	          else
    	          {
    	              $factura_max = 0;
    	          }
    	      }
    	      if($i == 4)
    	      {
    	          $credito_fiscal_min = $row_min_max["minimo"];
    	          $credito_fiscal_max = $row_min_max["maximo"];
    	          if($credito_fiscal_min != "")
    	          {
    	              $credito_fiscal_min = $credito_fiscal_min;
    	          }
    	          else
    	          {
    	              $credito_fiscal_min = 0;
    	          }

    	          if($credito_fiscal_max != "")
    	          {
    	              $credito_fiscal_max = $credito_fiscal_max;
    	          }
    	          else
    	          {
    	              $credito_fiscal_max = 0;
    	          }
    	      }
    				if($i == 3)
    	      {
    	          $dev_min = $row_min_max["minimo"];
    	          $dev_max = $row_min_max["maximo"];
    	          if($dev_min != "")
    	          {
    	              $dev_min = $dev_min;
    	          }
    	          else
    	          {
    	              $dev_min = 0;
    	          }

    	          if($dev_max != "")
    	          {
    	              $dev_max = $dev_max;
    	          }
    	          else
    	          {
    	              $dev_max = 0;
    	          }
    	      }
    	      $i += 1;
    	  }
    	}
    	//////////////////////////////////###################### FIN DOCUMENTOS PARA CORTE CAJA ##################////////////////////

    	///////////////////////////////////################ VENTA CORTE CAJA ###################///////////////////////////////////////
    	$total_tike_2 = 0;
    	$total_factura_2 = 0;
    	$total_credito_fiscal_2 = 0;


    	$total_contado_tik = 0;
    	$total_transferencia_tik = 0;
    	$total_cheque_tik = 0;
    	$total_contado_fac = 0;
    	$total_transferencia_fac = 0;
    	$total_cheque_fac = 0;
    	$total_contado_ccf = 0;
    	$total_transferencia_ccf = 0;
    	$total_cheque_ccf = 0;

    	$t_tike_2 = 0;
    	$t_factuta_2 = 0;
    	$t_credito_2 = 0;

    	$sql_corte_caja = _query("SELECT * FROM factura WHERE fecha = '$fecha_apertura' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND pagada = 1 AND id_apertura ='$id_apertura' AND tipo_pago != 'CRE' AND turno = '$turno'");
    	$cuenta_caja = _num_rows($sql_corte_caja);
    	if($cuenta_caja > 0)
    	{
    		while ($row_corte = _fetch_array($sql_corte_caja))
    		{
    			$id_factura = $row_corte["id_factura"];
          $anulada = $row_corte["anulada"];
          $subtotal = $row_corte["subtotal"];
          $suma = $row_corte["sumas"];
          $iva = $row_corte["iva"];
          $total = $row_corte["total"];
          $numero_doc = $row_corte["numero_doc"];
    			$tipo_pago = $row_corte["tipo_pago"];
    			$pagada = $row_corte["pagada"];
    			$tipo_documento = $row_corte["tipo_documento"];
    			$numero_im = $row_corte["num_fact_impresa"];

    			if($tipo_documento == 'TIK')
          {
              $total_tike_2 += $total;
    					if($tipo_pago == "CON")
    					{
    						$total_contado_tik += $total;
    					}
    					else if($tipo_pago == "TRA")
    					{
    						$total_transferencia_tik += $total;
    					}
    					else if($tipo_pago == "CHE")
    					{
    						$total_cheque_tik += $total;
    					}
    					$t_tike_2 += 1;
          }
          else if($tipo_documento == 'COF')
          {
              $total_factura_2 += $total;
    					if($tipo_pago == "CON")
    					{
    						$total_contado_fac += $total;
    					}
    					else if($tipo_pago == "TRA")
    					{
    						$total_transferencia_fac += $total;
    					}
    					else if($tipo_pago == "CHE")
    					{
    						$total_cheque_fac += $total;
    					}
    					$t_factuta_2 += 1;
          }
          else if($tipo_documento == 'CCF')
          {
    					$total_credito_fiscal_2 += $total;
    					if($tipo_pago == "CON")
    					{
    						$total_contado_ccf += $total;
    					}
    					else if($tipo_pago == "TRA")
    					{
    						$total_transferencia_ccf += $total;
    					}
    					else if($tipo_pago == "CHE")
    					{
    						$total_cheque_ccf += $total;
    					}
    					$t_credito_2 += 1;
          }
    			else if($tipo_documento == 'DEV')
          {
    				$afecta = $row_corte["afecta"];
    				$sql_afecta = _query("SELECT * FROM factura WHERE id_factura = '$afecta'");
    				$row_afecta = _fetch_array($sql_afecta);
    				$id_afecta = $row_afecta["id_factura"];
    				$numero = $row_afecta["num_fact_impresa"];
    				$total_dev_2 += $total;
    				$t_dev_2 += 1;
    				$lista_dev .= $numero_im.",".$total.",".$numero.",".$alias_tipodoc1."|";
          }
    		}
    	}
    	/////////////////////////############### FIN VENTA CORTE DE CAJA #############////////////////////////////////

    	/////////////////////////############### RECUPERACION VENTA PENDIENTE ########////////////////////////////////
    	$total_tike_r = 0;
    	$total_factura_r = 0;
    	$total_credito_fiscal_r = 0;
    	$t_tike_r = 0;
    	$t_factuta_r = 0;
    	$t_credito_r = 0;

    	$total_contado_tik_p = 0;
    	$total_transferencia_tik_p = 0;
    	$total_cheque_tik_p = 0;
    	$total_contado_fac_p = 0;
    	$total_transferencia_fac_p = 0;
    	$total_cheque_fac_p = 0;
    	$total_contado_ccf_p = 0;
    	$total_transferencia_ccf_p = 0;
    	$total_cheque_ccf_p = 0;

    	$sql_recuperacion = _query("SELECT * FROM factura WHERE fecha_pago = '$fecha_apertura' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND pagada = 1 AND id_apertura_pagada ='$id_apertura' AND tipo_pago LIKE '%PEN|%' AND turno_pagado = '$turno'");
    	$cuenta_recuperacion = _num_rows($sql_recuperacion);
    	if($cuenta_recuperacion > 0)
    	{
    		while ($row_r = _fetch_array($sql_recuperacion))
    		{
    			$id_factura = $row_r["id_factura"];
          $anulada = $row_r["anulada"];
          $subtotal = $row_r["subtotal"];
          $suma = $row_r["sumas"];
          $iva = $row_r["iva"];
          $total = $row_r["total"];
          $numero_doc = $row_r["numero_doc"];
    			$tipo_pago = $row_r["tipo_pago"];
    			$ex = explode("|", $tipo_pago);
    			$tipo_pago =$ex[1];
    			$pagada = $row_r["pagada"];
    			$tipo_documento = $row_r["tipo_documento"];
    			$numero_im = $row_r["num_fact_impresa"];

    			if($tipo_documento == 'TIK')
          {
              $total_tike_r += $total;
    					if($tipo_pago == "CON")
    					{
    						$total_contado_tik_p += $total;
    					}
    					else if($tipo_pago == "TRA")
    					{
    						$total_transferencia_tik_p += $total;
    					}
    					else if($tipo_pago == "CHE")
    					{
    						$total_cheque_tik_p += $total;
    					}
    					$t_tike_r += 1;
          }
          else if($tipo_documento == 'COF')
          {
              $total_factura_r += $total;
    					if($tipo_pago == "CON")
    					{
    						$total_contado_fac_p += $total;
    					}
    					else if($tipo_pago == "TRA")
    					{
    						$total_transferencia_fac_p += $total;
    					}
    					else if($tipo_pago == "CHE")
    					{
    						$total_cheque_fac_p += $total;
    					}
    					$t_factuta_r += 1;
          }
          else if($tipo_documento == 'CCF')
          {
    					$total_credito_fiscal_r += $total;
    					if($tipo_pago == "CON")
    					{
    						$total_contado_ccf_p += $total;
    					}
    					else if($tipo_pago == "TRA")
    					{
    						$total_transferencia_ccf_p += $total;
    					}
    					else if($tipo_pago == "CHE")
    					{
    						$total_cheque_ccf_p += $total;
    					}
    					$t_credito_r += 1;
          }

    		}
    	}
    	////////////////////////////////////############## FIN RECUPERACION VENTA PENDIENTE #################/////////////////////

    	///////////////////////////////////############### DEVOLUCIONES ####################/////////////////////////////////////
    	$total_dev_2 = 0;
    	$t_dev_2 = 0;
    	$lista_dev = "";
    	$total_nc_2 = 0;
    	$t_nc_2 = 0;
    	$lista_nc = "";
    	$sql_dev = _query("SELECT * FROM factura WHERE fecha = '$fecha_apertura' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND id_apertura_pagada ='$id_apertura' AND turno = '$turno'");
    	//echo "SELECT * FROM factura WHERE fecha = '$fecha_apertura' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND id_apertura_pagada ='$id_apertura'";
    	$cuenta_dev = _num_rows($sql_dev);
    	if($cuenta_dev > 0)
    	{
    		while ($row_dev = _fetch_array($sql_dev))
    		{
    			$id_factura = $row_dev["id_factura"];
          $anulada = $row_dev["anulada"];
          $subtotal = $row_dev["subtotal"];
          $suma = $row_dev["sumas"];
          $iva = $row_dev["iva"];
          $total = $row_dev["total"];
          $numero_doc = $row_dev["numero_doc"];
    			$tipo_pago = $row_dev["tipo_pago"];
    			$pagada = $row_dev["pagada"];
    			$tipo_documento = $row_dev["tipo_documento"];
    			$numero_im = $row_dev["num_fact_impresa"];

    			if($tipo_documento == 'DEV')
          {
    				$afecta = $row_dev["afecta"];
    				$sql_afecta = _query("SELECT * FROM factura WHERE id_factura = '$afecta'");
    				$row_afecta = _fetch_array($sql_afecta);
    				$id_afecta = $row_afecta["id_factura"];
    				$numero = $row_afecta["num_fact_impresa"];
    				$tipo_documento = $row_afecta["tipo_documento"];
    				$total_dev_2 += $total;
    				$t_dev_2 += 1;
    				$lista_dev .= $numero_im.",".$total.",".$numero.",".$tipo_documento."|";
          }
    			if($tipo_documento == 'NC')
          {
    				$afecta = $row_dev["afecta"];
    				$sql_afecta = _query("SELECT * FROM factura WHERE id_factura = '$afecta'");
    				$row_afecta = _fetch_array($sql_afecta);
    				$id_afecta = $row_afecta["id_factura"];
    				$numero = $row_afecta["num_fact_impresa"];
    				$tipo_documento = $row_afecta["tipo_documento"];
    				$total_nc_2 += $total;
    				$t_nc_2 += 1;
    				$lista_nc .= $numero_im.",".$total.",".$numero.",".$tipo_documento."|";
          }
    		}
    	}
    	/////////////////////////////////############### FIN DEVOLUCIONES #############///////////////////////////

    	////////////////////////////////################ VENTA AL CREDITO #############///////////////////////////
    	$total_tike_cre = 0;
    	$total_factura_cre = 0;
    	$total_credito_fiscal_cre = 0;
    	$t_tike_cre = 0;
    	$t_factuta_cre = 0;
    	$t_credito_cre = 0;

    	$sql_credito = _query("SELECT * FROM factura WHERE fecha = '$fecha_apertura' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND id_apertura ='$id_apertura' AND turno = '$turno' AND credito = 1");
    	//echo "SELECT * FROM factura WHERE fecha = '$fecha_apertura' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND id_apertura ='$id_apertura' AND turno = '$turno' AND credito = 1";
    	$cuenta_cre = _num_rows($sql_credito);
    	if($cuenta_cre > 0)
    	{
    		while ($row_cre = _fetch_array($sql_credito))
    		{
    			$id_factura = $row_cre["id_factura"];
          $anulada = $row_cre["anulada"];
          $subtotal = $row_cre["subtotal"];
          $suma = $row_cre["sumas"];
          $iva = $row_cre["iva"];
          $total = $row_cre["total"];
          $numero_doc = $row_cre["numero_doc"];
    			$tipo_pago = $row_cre["tipo_pago"];
    			$pagada = $row_cre["pagada"];
    			$tipo_documento = $row_cre["tipo_documento"];
    			$numero_im = $row_cre["num_fact_impresa"];

    			if($tipo_documento == 'TIK')
          {
              $total_tike_cre += $total;

    					$t_tike_cre += 1;
          }
          else if($tipo_documento == 'COF')
          {
              $total_factura_cre += $total;
    					$t_factuta_cre += 1;
          }
          else if($tipo_documento == 'CCF')
          {
    					$total_credito_fiscal_cre += $total;
    					$t_credito_cre += 1;
          }

    		}
    	}
    	/////////////////////////////////////########## FIN VENTA AL CREDITO ###################////////////////////////

    	///////////////////////////////////// TOTALES /////////////////////////////////////////////////////////////////

    	//////////////////////////////////TOTALES VENTA AL CONTADO ///////////////////////////////////////////////////
    	$total_contado_n = $total_contado_tik + $total_contado_fac + $total_contado_ccf;
    	$total_cheque_n = $total_cheque_tik + $total_cheque_fac + $total_cheque_ccf;
    	$total_transferencia_n = $total_transferencia_tik + $total_transferencia_fac + $total_transferencia_ccf;
    	$total_general_contado = $total_contado_n + $total_transferencia_n + $total_cheque_n;

    	/////////////////////////////////TOTALES RECUPERACION///////////////////////////////////////////////////////
    	$total_contado_r = $total_contado_tik_p + $total_contado_fac_p + $total_contado_ccf_p;
    	$total_cheque_r = $total_cheque_tik_p + $total_cheque_fac_p + $total_cheque_ccf_p;
    	$total_transferencia_r = $total_transferencia_tik_p + $total_transferencia_fac_p + $total_transferencia_ccf_p;
    	$total_general_recuperacion = $total_contado_r + $total_transferencia_r + $total_cheque_r;

    	///////////////////////////////////TOTALES ABONOS A CREDITO//////////////////////////////////////////////////
    	$total_abono_tik = $total_abono_tik_e + $total_abono_tik_c + $total_abono_tik_t;
    	$total_abono_fac = $total_abono_fac_e + $total_abono_fac_c + $total_abono_fac_t;
    	$total_abono_ccf = $total_abono_ccf_e + $total_abono_ccf_c + $total_abono_ccf_t;

    	$total_abono_efectivo = $total_abono_tik_e + $total_abono_fac_e + $total_abono_ccf_e;
    	$total_abono_cheque = $total_abono_tik_c + $total_abono_fac_c + $total_abono_ccf_c;
    	$total_abono_transferencia = $total_abono_tik_t + $total_abono_fac_t + $total_abono_ccf_t;

    	$total_abono_credito = $total_abono_efectivo + $total_abono_cheque + $total_abono_transferencia;


    	$total_recuperacion = $total_contado_r;
    	$full_recuperacion = $total_abono_efectivo + $total_recuperacion;
    	$recuperacion_doc = $total_contado_r;
    	$total_nopagado = $total_tike_npago + $total_factura_npago + $total_credito_fiscal_npago;
    	$total_caja_chica = $monto_ch + $total_entrada_caja - $total_salida_caja - $total_viatico;
    	$total_primario = $total_general_contado;
    	$total_credito = $total_tike_cre + $total_factura_cre + $total_credito_fiscal_cre;
    	$total_facturado = $total_venta_pendiente + $total_credito + $total_primario;
    	$total_factura_faltante = $total_venta_pendiente + $total_venta_credito;
    	$total_remesa = $total_contado_n +  $total_abono_efectivo + $total_contado_r - $total_dev_2 - $total_nc_2;
    	$total_pen = $total_venta_credito + $total_venta_pendiente;

    	$total_caja = $total_primario + $total_recuperacion + $monto_apertura;
    	$total_caja2 = $total_contado_n + $total_abono_efectivo;
    	$total_corte_2 = $total_remesa;

    	$saldo_caja = $monto_apertura + $total_caja_chica;
    	$total_doc = $t_tike_2 + $t_factuta_2 + $t_credito_2;

      $n_remesa = str_replace("-", "", $fecha_actual);

      $tabla = "controlcaja";
    	$form_data = array(
    		'fecha_corte' => $fecha_actual,
    		'hora_corte' => $hora_actual,
    		'id_empleado' => $empleado,
    		'id_sucursal' => $id_sucursal,
    		'id_apertura' => $id_apertura,
    		'totalt' => $total_contado_tik,
    		'totalf' => $total_contado_fac,
    		'totalcf' => $total_contado_ccf,
    		'totalgral' => $total_corte_2,
    		'cashfinal' => $total_corte_2,
    		'totalnot' => $t_tike_2,
    		'totalnof' => $t_factuta_2,
    		'totalnocf' => $t_credito_2,
    		'turno' => $turno,
    		'tinicio' => $tike_min,
    		'tfinal' => $tike_max,
    		'finicio' => $factura_min,
    		'ffinal' => $factura_max,
    		'cfinicio' => $credito_fiscal_min,
    		'cffinal' => $credito_fiscal_max,
    		'cashinicial' => $monto_apertura,
    		'tipo_corte' => $tipo_corte,
    		'vtaefectivo' => $total_contado,
    		'vales' => $total_salida,
    		'ingresos' => $total_entrada,
    		'totalnodev' => $t_dev,
    		'monto_ch' => $monto_ch,
    		'caja' => $caja,
    		'viaticos' => $total_viatico,
    		'recuperacion' => $recuperacion_doc,
    		'abono_credito' => $total_rcredito,
    		'venta_pendiente' => $total_vpendiente,
    		'remesa' => $remesa,
    		'total_facturado' => $total_facturado,
    		'saldo_caja' => $saldo_caja,
    		'n_remesa' => $n_remesa,
    		'caja_chica' => $total_caja_chica,
    		'vtacontado' => $total_vcontado,
    		'vtatcredito' => $total_vcredito,
    		'vcheque' => $total_vcheque,
    		'vtransferencia' => $total_vtransferencia,
    		'abono_creditoE' => $abono_creditoE,
    		'abono_creditoC' => $abono_creditoC,
    		'abono_creditoT' => $abono_creditoT,
    		'total_RE' => $total_RE,
    		'total_RC' => $total_RC,
    		'total_RT' => $total_RT,


    	);
    	echo _error();

    		if($tipo_corte == "C")
    		{
    			$insertar = _insert($tabla, $form_data);
    			$id_cortex= _insert_id();
    			if($insertar)
    			{
    				$explora = explode("|", $lista_dev);
        			for ($i=0; $i < ($t_dev) ; $i++) {
        				$data = explode(",", $explora[$i]);
        				$dev_n = $data[0];
        				$dev_p = $data[1];
        				$afecta = $data[2];
        				$tipo = $data[3];
        				$table_dev = "devoluciones_corte";
        				$form_dev = array(
        					'id_corte' => $id_cortex,
        					'n_devolucion' => $dev_n,
        					't_devolucion' => $dev_p,
        					'afecta' => $afecta,
        					'tipo' => $tipo,
    							'fecha' => $fecha_corte,
    							'tipo_corte' => $tipo_corte,
        					);
        				$inser_dev = _insert($table_dev, $form_dev);

                    	//$n++;
        			}
    					$explora1 = explode("|", $lista_nc);
            			for ($j=0; $j < ($t_nc) ; $j++) {
            				$data1 = explode(",", $explora1[$j]);
            				$nc_n = $data1[0];
            				$nc_p = $data1[1];
            				$afecta = $data1[2];
            				$table_nc = "nc_corte";
            				$form_nc = array(
            					'id_corte' => $id_cortex,
            					'n_nc' => $nc_n,
            					't_nc' => $nc_p,
            					'afecta' => $afecta,
            					'tipo' => "CREDITO FISCAL",
            					);
            				$inser_nc = _insert($table_nc, $form_nc);
            }
    			}
    		}

    		if($insertar)
    		{
    			return true;
          echo "HECHO";
    		}
    		else
    		{
    			return false;
          echo "REGADA";
    		}
    }
  }
	if (!isset($_REQUEST['process'])) {
	    initial();
	}
	//else {
	if (isset($_REQUEST['process'])) {
	    switch ($_REQUEST['process']) {
	    case 'ok':
	        search();
	        break;
			case 'caja':
					caja();
					break;
      case 'verificar':
					apertura_auto();
          fin_turno();
          inicio_turno();
          fin_apertura();
					break;
	    }

	 //}
	}
	?>
