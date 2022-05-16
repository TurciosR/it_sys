<?php
include_once "_core.php";
include ('num2letras.php');
include ('facturacion_funcion_imprimir.php');
function initial() {
	$_PAGE = array ();
	$title= 'Corte de Caja Diario';
	$_PAGE ['title'] =$title;
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';

	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	date_default_timezone_set('America/El_Salvador');

	$fecha_actual=date("Y-m-d");
	$id_sucursal=$_SESSION['id_sucursal'];
	$sql_sucursal=_query("SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'");
	$array_sucursal=_fetch_array($sql_sucursal);
	$nombre_sucursal=$array_sucursal['descripcion'];

	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$aper_id = $_REQUEST["aper_id"];
	$sql_apertura = _query("SELECT * FROM apertura_caja WHERE id_apertura = '$aper_id' AND vigente = 1 AND id_sucursal = '$id_sucursal'");
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
  //echo "SELECT * FROM mov_caja WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND id_sucursal = '$id_sucursal' AND turno = '$turno'";
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
	$sql_pendiente = _query("SELECT * FROM factura WHERE fecha = '$fecha_actual' AND id_apertura = '$id_apertura' AND turno = '$turno' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND pagada = 0 AND tipo_pago = 'PEN'");
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

	/////////////////////////////////####################### DOCUMENTOS CORTE ZX ####################/////////////////////////////
	$tike_min_2 = 0;
	$tike_max_2 = 0;
	$factura_min_2 = 0;
	$factura_max_2 = 0;
	$credito_fiscal_min_2 = 0;
	$credito_fiscal_max_2 = 0;
	$sql_min_max_2 = _query("SELECT MIN(num_fact_impresa) as minimo, MAX(num_fact_impresa) as maximo FROM factura WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND tipo_documento = 'TIK' AND id_sucursal = '$id_sucursal' AND anulada = 0 UNION ALL SELECT MIN(num_fact_impresa) as minimo, MAX(num_fact_impresa) as maximo FROM factura WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND tipo_documento = 'COF' AND id_sucursal = '$id_sucursal' AND anulada = 0 UNION ALL SELECT MIN(num_fact_impresa) as minimo, MAX(num_fact_impresa) as maximo FROM factura WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND tipo_documento = 'CCF' AND id_sucursal = '$id_sucursal' AND anulada = 0" );
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

	$sql_corte_caja = _query("SELECT * FROM factura WHERE fecha = '$fecha_apertura' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND pagada = 1 AND id_apertura ='$id_apertura' AND tipo_pago != 'CRE' AND turno = '$turno' AND devolucion=0");
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
			$devol=$row_corte['devolucion'];

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
			else if($tipo_documento == 'DEV' )
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

	/////////////////////////############### VENTA XZ #############////////////////////////////////
	$sql_corte_2 = _query("SELECT * FROM factura WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND devolucion=0");
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
	$sql_dev = _query("SELECT * FROM factura WHERE fecha = '$fecha_apertura' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND id_apertura_pagada ='$id_apertura' AND turno = '$turno' AND devolucion=1");
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
			$devolucion=$row_dev['devolucion'];

			if($devolucion==1)
			{
				$afecta = $row_dev["afecta"];
				$sql_afecta = _query("SELECT * FROM factura WHERE id_factura = '$afecta'");
				$row_afecta = _fetch_array($sql_afecta);
				$id_afecta = $row_afecta["id_factura"];
				$numero =$row_afecta["tipo_documento"]." ".$row_afecta["num_fact_impresa"] ;
				$total_dev_2 += $total;
				$t_dev_2 += 1;
				$lista_dev .= $numero_im.",".$total.",".$numero.",".$tipo_documento."|";
			}
			/*if($tipo_documento == 'NC')
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
			}*/
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
	//////////////////////////TOTALES XZ///////////////////////////////////////
	$total_venta_xz = $total_tike + $total_factura + $total_credito_fiscal;
	$total_docZ = $t_tike + $t_factuta + $t_credito;
	$total_corte_z = $total_venta_xz - $total_dev_2 - $total_nc_2;


	//$total_exx = $total_tike_e+$total_factura_e+$total_credito_fiscal_e+$total_reserva_e;
	//$total_graa = $total_tike_g+$total_factura_g+$total_credito_fiscal_g+$total_reserva_g;
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	//permiso del script
	?>


	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
				<div class="ibox ">
					<?php
						//permiso del script
					if ($links!='NOT' || $admin=='1' ){
						?>
						<div class="ibox-title">
							<h5>Registrar Corte <?php echo $nombre_sucursal;?></h5>
						</div>
						<div class="ibox-content">


							<div class="row">
								<div class="col-md-6">
									<div class="form-group has-info single-line">
										<label>Tipo de corte</label>
										<select id="tipo_corte" name="tipo_corte" class="form-control">
											<option value="C">Corte de caja</option>
											<option value="X">Corte X</option>
											<option value="Z">Corte Z</option>
										</select>
									</div>
								</div>
								<?php
								$fecha_actual=date("Y-m-d");

								$nrows_tot_sist=0;
								$total_diario =0;

								echo "<div class='col-md-6' >";
								echo "<div class='form-group has-info single-line'><label>Fecha:</label> <input type='text' class='form-control' id='fecha' name='fecha' value='$fecha_actual' readonly></div>";
								echo "</div>";
								?>
							</div>

							<!--div class="row"-->
							<br>
							<div class="row" id="caja_contado">
								<div class='alert alert-success text-center' style='font-weight: bold;'>
									<label style='font-size: 15px;'>Ventas de Contado</label>
								</div>
							</div>
							<div class="table-responsive">
								<table class="table table-border" id="tabla_contado">
									<thead id="encabeza_contado">
										<tr>
											<th class='col-lg-2'>TIPO DOCUMENTO</th>
											<th class='col-lg-1'>N° INICIO</th>
											<th class='col-lg-1'>N° FINAL</th>
											<th class='col-lg-2'><i class='pull-right'>EFECTIVO</i></th>
											<th class='col-lg-2'><i class='pull-right'>TRANSFERENCIA</i></th>
											<th class='col-lg-2'><i class='pull-right'>CHEQUE</i></th>
											<th class='col-lg-2'><i class='pull-right'>TOTAL</i></th>
										</tr>
									</thead>
									<tbody id='tabla_doc'>
										<tr>
											<td>TIQUETE</td>
											<td><?php echo $tike_min;?></td>
											<td><?php echo $tike_max;?></td>
											<td><label class='pull-right'><?php echo number_format($total_contado_tik, 2);?></label></td>
											<td><label class='pull-right'><?php echo number_format($total_transferencia_tik, 2);?></label></td>
											<td><label class='pull-right'><?php echo number_format($total_cheque_tik, 2);?></label></td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_tike_2, 2);?></label></td>
										</tr>
										<tr>
											<td>FACTURA</td>
											<td><?php echo $factura_min;?></td>
											<td><?php echo $factura_max;?></td>
											<td><label class='pull-right'><?php echo number_format($total_contado_fac, 2);?></label></td>
											<td><label class='pull-right'><?php echo number_format($total_transferencia_fac, 2);?></label></td>
											<td><label class='pull-right'><?php echo number_format($total_cheque_fac, 2);?></label></td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_factura_2, 2);?></label></td>
										</tr>
										<tr>
											<td>CREDITO FISCAL</td>
											<td><?php echo $credito_fiscal_min;?></td>
											<td><?php echo $credito_fiscal_max;?></td>
											<td><label class='pull-right'><?php echo number_format($total_contado_ccf, 2);?></label></td>
											<td><label class='pull-right'><?php echo number_format($total_transferencia_ccf, 2);?></label></td>
											<td><label class='pull-right'><?php echo number_format($total_cheque_ccf, 2);?></label></td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_credito_fiscal_2, 2);?></label></td>
										</tr>

										<tr>
											<td colspan="3">TOTAL CONTADO</td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_contado_n, 2);?></label></td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_transferencia_n, 2);?></label></td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_cheque_n, 2);?></label></td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_general_contado, 2);?></label></td>
										</tr>
									</tbody>
								</table>
								<!--////////////////////////////////////////////////////////////////////////////////////////-->

							</div>

							<div class="row" id="caja_ventaxz" hidden>
								<div class='alert alert-success text-center' style='font-weight: bold;'>
									<label style='font-size: 15px;'>Ventas</label>
								</div>
							</div>
							<div class="table-responsive">
								<table class="table table-border" id="tabla_ventaxz" hidden>
									<thead id="encabeza_contado">
										<tr>
											<th class='col-lg-2'>TIPO DOCUMENTO</th>
											<th class='col-lg-1'>N° INICIO</th>
											<th class='col-lg-1'>N° FINAL</th>
											<th class='col-lg-1'>N° DOCUMENTOS</th>
											<th class='col-lg-2'><i class='pull-right'>TOTAL</i></th>
										</tr>
									</thead>
									<tbody id='tabla_doc'>
										<tr>
											<td>TIQUETE</td>
											<td><?php echo $tike_min_2;?></td>
											<td><?php echo $tike_max_2;?></td>
											<td><?php echo $t_tike;?></td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_tike, 2);?></label></td>
										</tr>
										<tr>
											<td>FACTURA</td>
											<td><?php echo $factura_min_2;?></td>
											<td><?php echo $factura_max_2;?></td>
											<td><?php echo $t_factuta;?></td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_factura, 2);?></label></td>
										</tr>
										<tr>
											<td>CREDITO FISCAL</td>
											<td><?php echo $credito_fiscal_min_2;?></td>
											<td><?php echo $credito_fiscal_max_2;?></td>
											<td><?php echo $t_credito;?></td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_credito_fiscal, 2);?></label></td>
										</tr>

										<tr>
											<td colspan="3">TOTAL CONTADO</td>
											<td><?php echo $total_docZ;?></td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_venta_xz, 2);?></label></td>
										</tr>
									</tbody>
								</table>
								<!--////////////////////////////////////////////////////////////////////////////////////////-->

							</div>
							<!--Venta pendiente-->
							<!--<div class="row" id='caja_no_pago'>
								<div class='alert alert-success text-center' style='font-weight: bold;'>
									<label style='font-size: 15px;'>Venta Pendiente</label>
								</div>
							</div>
							<table class="table table-border" id='tabla_no_pago'>
								<thead>
									<tr>
										<th class="col-lg-3">Tipo Documento</th>
										<th class="col-lg-2"><i class='pull-right'>Crédito</i></th>
										<th class="col-lg-2"><i class='pull-right'>Pendiente</i></th>
										<th class="col-lg-3"><i class='pull-right'>Total</i></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>TIQUETE</td>
										<td><label class='pull-right'><?php echo "$ ".number_format($total_tike_cre,2);?></label></td>
										<td><label class='pull-right'><?php echo "$ ".number_format($total_tike_npago,2);?></label></td>
										<td><label class='pull-right'><?php echo "$ ".number_format($total_tike_npago + $total_tike_cre,2);?></label></td>
									</tr>
									<tr>
										<td>FACTURA</td>
										<td><label class='pull-right'><?php echo "$ ".number_format($total_factura_cre,2);?></label></td>
										<td><label class='pull-right'><?php echo "$ ".number_format($total_factura_npago, 2);?></label></td>
										<td><label class='pull-right'><?php echo "$ ".number_format($total_factura_npago + $total_factura_cre, 2);?></label></td>
									</tr>
									<tr>
										<td>CREDITO FISCAL</td>
										<td><label class='pull-right'><?php echo "$ ".number_format($total_credito_fiscal_cre,2);?></label></td>
										<td><label class='pull-right'><?php echo "$ ".number_format($total_credito_fiscal_npago, 2);?></label></td>
										<td><label class='pull-right'><?php echo "$ ".number_format($total_credito_fiscal_npago + $total_credito_fiscal_cre, 2);?></label></td>
									</tr>
									<tr>
										<td>TOTAL VENTA PENDIENTE</td>
										<td><label class='pull-right'><?php echo "$ ".number_format($total_credito, 2);?></label></td>
										<td><label class='pull-right'><?php echo "$ ".number_format($total_venta_pendiente, 2);?><l/abel></td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_venta_pendiente+$total_credito, 2);?></label></td>
										</tr>
									</tbody>
								</table>-->

								<!--Recuperacion-->

							<!--<div class="row" id="caja_recuperacion">
									<div class='alert alert-success text-center' style='font-weight: bold;'>
										<label style='font-size: 15px;'>Recuperación: Venta Pendiente</label>
									</div>
								</div>-->
								<!--<div class="table-responsive" id="tabla_recuperacion">
									<table class="table table-border">
										<thead>
											<tr>
												<th class='col-lg-2'>TIPO DOCUMENTO</th>
												<th class='col-lg-2'><i class='pull-right'>EFECTIVO</i></th>
												<th class='col-lg-2'><i class='pull-right'>TRANSFERENCIA</i></th>
												<th class='col-lg-2'><i class='pull-right'>CHEQUE</i></th>
												<th class='col-lg-2'><i class='pull-right'>TOTAL</i></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>TIQUETE</td>
												<td><label class='pull-right'><?php echo number_format($total_contado_tik_p, 2);?></label></td>
												<td><label class='pull-right'><?php echo number_format($total_transferencia_tik_p, 2);?></label></td>
												<td><label class='pull-right'><?php echo number_format($total_cheque_tik_p, 2);?></label></td>
												<td><label class='pull-right'><?php echo "$ ".number_format($total_tike_r, 2);?></label></td>
											</tr>
											<tr>
												<td>FACTURA</td>
												<td><label class='pull-right'><?php echo number_format($total_contado_fac_p, 2);?></label></td>
												<td><label class='pull-right'><?php echo number_format($total_transferencia_fac_p, 2);?></label></td>
												<td><label class='pull-right'><?php echo number_format($total_cheque_fac_p, 2);?></label></td>
												<td><label class='pull-right'><?php echo "$ ".number_format($total_factura_r, 2);?></label></td>
											</tr>
											<tr>
												<td>CREDITO FISCAL</td>
												<td><label class='pull-right'><?php echo number_format($total_contado_ccf_p, 2);?></label></td>
												<td><label class='pull-right'><?php echo number_format($total_transferencia_ccf_p, 2);?></label></td>
												<td><label class='pull-right'><?php echo number_format($total_cheque_ccf_p, 2);?></label></td>
												<td><label class='pull-right'><?php echo "$ ".number_format($total_credito_fiscal_r, 2);?></label></td>
											</tr>

											<tr>
												<td>TOTAL CONTADO</td>
												<td><label class='pull-right'><?php echo "$ ".number_format($total_contado_r, 2);?></label></td>
												<td><label class='pull-right'><?php echo "$ ".number_format($total_transferencia_r, 2);?></label></td>
												<td><label class='pull-right'><?php echo "$ ".number_format($total_cheque_r, 2);?></label></td>
												<td><label class='pull-right'><?php echo "$ ".number_format($total_general_recuperacion, 2);?></label></td>
											</tr>
										</tbody>
									</table>
								</div>-->
								<!--////////////////////////////////////////////////////////////////////////////////////////-->
								<div class="row" id="caja_recuperacion_1">
									<div class='alert alert-success text-center' style='font-weight: bold;'>
										<label style='font-size: 15px;'>Recuperación: Abono Crédito</label>
									</div>
								</div>
								<div class="table-responsive" id="tabla_recuperacion_1">
									<table class="table table-border" >
										<thead>
											<tr>
												<th class='col-lg-2'>TIPO DOCUMENTO</th>
												<th class='col-lg-2'><i class='pull-right'>EFECTIVO</i></th>
												<th class='col-lg-2'><i class='pull-right'>TRANSFERENCIA</i></th>
												<th class='col-lg-2'><i class='pull-right'>CHEQUE</i></th>
												<th class='col-lg-2'><i class='pull-right'>TOTAL</i></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>TIQUETE</td>
												<td><label class='pull-right'><?php echo number_format($total_abono_tik_e, 2);?></label></td>
												<td><label class='pull-right'><?php echo number_format($total_abono_tik_t, 2);?></label></td>
												<td><label class='pull-right'><?php echo number_format($total_abono_tik_c, 2);?></label></td>
												<td><label class='pull-right'><?php echo "$ ".number_format($total_abono_tik, 2);?></label></td>
											</tr>
											<tr>
												<td>FACTURA</td>
												<td><label class='pull-right'><?php echo number_format($total_abono_fac_e, 2);?></label></td>
												<td><label class='pull-right'><?php echo number_format($total_abono_fac_t, 2);?></label></td>
												<td><label class='pull-right'><?php echo number_format($total_abono_fac_c, 2);?></label></td>
												<td><label class='pull-right'><?php echo "$ ".number_format($total_abono_fac, 2);?></label></td>
											</tr>
											<tr>
												<td>CREDITO FISCAL</td>
												<td><label class='pull-right'><?php echo number_format($total_abono_ccf_e, 2);?></label></td>
												<td><label class='pull-right'><?php echo number_format($total_abono_ccf_t, 2);?></label></td>
												<td><label class='pull-right'><?php echo number_format($total_abono_ccf_c, 2);?></label></td>
												<td><label class='pull-right'><?php echo "$ ".number_format($total_abono_ccf, 2);?></label></td>
											</tr>

											<tr>
												<td>TOTAL CONTADO</td>
												<td><label class='pull-right'><?php echo "$ ".number_format($total_abono_efectivo, 2);?></label></td>
												<td><label class='pull-right'><?php echo "$ ".number_format($total_abono_transferencia, 2);?></label></td>
												<td><label class='pull-right'><?php echo "$ ".number_format($total_abono_cheque, 2);?></label></td>
												<td><label class='pull-right'><?php echo "$ ".number_format($total_abono_credito, 2);?></label></td>
											</tr>
										</tbody>
									</table>

									<!--////////////////////////////////////////////////////////////////////////////////////////-->

								</div>



								<div class="row" id="caja_dev">
									<div class='alert alert-success text-center' style='font-weight: bold;'>
										<label style='font-size: 15px;'>Devoluciones</label>
									</div>
								</div>
								<table class="table table-border" id="table_dev">
									<thead>
										<tr>
											<th>N°</th>
											<th>Tipo</th>
											<th>Numero</th>
											<th>Afecta</th>
											<th><i class="pull-right">Total</i></th>
										</tr>
									</thead>
									<tbody>

										<?php
										$n = 1;
			                        			//print_r($lista_dev);
										$explora = explode("|", $lista_dev);
										for ($i=0; $i < ($t_dev_2) ; $i++) {
											$data = explode(",", $explora[$i]);
											$dev_n = $data[0];
											$dev_p = $data[1];
											$afe = $data[2];
											$ali = $data[3];
											if($ali == "TIK")
											{
												$txt = "TIQUETE";
											}
											else if($ali == "COF")
											{
												$txt = "FACTURA";
											}

											echo "<tr>
											<td>".$n."</td>
											<td>".$ali."</td>
											<td>".$dev_n."</td>
											<td>".$afe."</td>
											<td><label class='pull-right'>$ ".number_format($dev_p,2)."</label></td>
											</tr>";

											$n++;
										}
										?>
										<tr>
											<td colspan="4">TOTAL DEVOLUCIÓN</td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_dev_2, 2);?></label></td>
											<td></td>
											<td></td>
										</tr>
									</tbody>
								</table>

								<!--Notas de credito-->
								<div hidden class="row" id="caja_dev">
									<div class='alert alert-success text-center' style='font-weight: bold;'>
										<label style='font-size: 15px;'>Notas de Crédito</label>
									</div>
								</div>
								<table hidden class="table table-border" id="table_dev">
									<thead>
										<tr>
											<th>N°</th>
											<th>N° Documento</th>
											<th>Documento Afecta</th>
											<th>N° Afecta</th>
											<th><i class="pull-right">Total</i></th>
										</tr>
									</thead>
									<tbody>

										<?php
										$n = 1;
																//print_r($lista_dev);
										$explora = explode("|", $lista_nc);
										for ($i=0; $i < ($t_nc_2) ; $i++) {
											$data = explode(",", $explora[$i]);
											$dev_n = $data[0];
											$dev_p = $data[1];
											$afe = $data[2];
											$ali = $data[3];
											if($ali == "TIK")
											{
												$txt = "TIQUETE";
											}
											else if($ali == "COF")
											{
												$txt = "FACTURA";
											}
											else if($ali == "CCF")
											{
												$txt = "CREDITO FISCAL";
											}

											echo "<tr>
											<td>".$n."</td>
											<td>".$dev_n."</td>
											<td>".$txt."</td>
											<td>".$afe."</td>
											<td><label class='pull-right'>$ ".number_format($dev_p,2)."</label></td>
											</tr>";

											$n++;
										}
										?>
										<tr>
											<td colspan="4">TOTAL NOTAS DE CREDITO</td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_nc_2, 2);?></label></td>
											<td></td>
											<td></td>
										</tr>
									</tbody>
								</table>
								<!--Fin nota de credito-->

								<div class="row" id="caja_facturado">
									<div class='alert alert-success text-center' style='font-weight: bold;'>
										<label style='font-size: 15px;'>Facturado</label>
									</div>
								</div>
								<table class="table table-border" id="table_facturado">
									<tbody id='caja_o'>
										<tr>
											<td class="col-md-11">TOTAL VENTAS CONTADO</td>
											<td class="col-md-1"><label id="id_total12" class='pull-right'><?php echo "$ ".number_format($total_primario, 2);?></label></td>
										</tr>
										<tr>
											<td class="col-md-11">TOTAL VENTAS PENDIENTE</td>
											<td class="col-md-1"><label id="id_total12" class='pull-right'><?php echo "$ ".number_format($total_venta_pendiente, 2);?></label></td>
										</tr>
										<tr>
											<td class="col-md-11">TOTAL VENTA AL CREDITO</td>
											<td class="col-md-1"><label id="id_total12" class='pull-right'><?php echo "$ ".number_format($total_credito, 2);?></label></td>
										</tr>
										<tr>
											<td class="col-md-11">TOTAL FACTURADO</td>
											<td class="col-md-1"><label id="id_total12" class='pull-right'><?php echo "$ ".number_format($total_facturado, 2);?></label></td>
										</tr>
									</tbody>
								</table>
								<hr>
								<div class="row" id="caja_mov">
									<div class='alert alert-success text-center' style='font-weight: bold;'>
										<label style='font-size: 15px;'>Movimientos de Caja Chica</label>
									</div>
								</div>
								<table class="table table-border" id="table_mov">
									<thead>
										<tr>
											<th class="col-md-11">Tipo Movimiento</th>
											<th class="col-md-1"><i class="pull-right">Total</i></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>APERTURA CAJA CHICA</td>
											<td><label class='pull-right'><?php echo "$ ".number_format($monto_ch, 2);?></label></td>
										</tr>
										<tr>
											<td>VALES</td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_salida_caja, 2);?></label></td>
										</tr>
										<tr>
											<td>VIATICOS</td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_viatico, 2);?></label></td>
										</tr>
										<tr>
											<td>INGRESOS</td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_entrada_caja, 2);?></label></td>
										</tr>

										<tr>
											<td>SALDO CAJA CHICA</td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_caja_chica, 2);?></label></td>
										</tr>
									</tbody>
								</table>



								<div class="row" id="caja_t">
									<div class='alert alert-success text-center' style='font-weight: bold;'>
										<label style='font-size: 15px;' id='tt_fin'>Saldo Caja</label>
									</div>
								</div>
								<table class="table table-border" id="table_t">
									<tbody id="table_data">
										<tr>
											<td colspan="3">MONTO APERTURA</td>
											<td><label class='pull-right'><?php echo "$ ".number_format($monto_apertura, 2);?></label></td>
										</tr>
										<tr>
											<td colspan="3">SALDO CAJA CHICA</td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_caja_chica, 2);?></label></td>
										</tr>
										<tr>
											<td><label >EFECTIVO</label></td>
											<td><label class="pull-right">SOBRANTE</label></td>
											<td><label class="pull-right">FALTANTE</label></td>
											<td><label class="pull-right">TOTAL SALDO</label></td>
										</tr>
										<tr>
											<td><label class=''><input class="form-control" id='saldo_caja' name='saldo_caja'></label></td>
											<td><label class='pull-right sobrante' style="color:blue">0.00</label></td>
											<td><label class='pull-right faltante' style="color:red"><?php echo $total_caja_chica + $monto_apertura;?></label></td>
											<td><label class='pull-right'><?php echo "$ ".number_format($total_caja_chica + $monto_apertura, 2);?></label></td>
										</tr>
									</tbody>
								</table>


								<!--Remesaaa-->
								<div class="row" id="caja_aper">
									<div class='alert alert-success text-center' style='font-weight: bold;'>
										<label style='font-size: 15px;'>Remesa</label>
									</div>
								</div>
								<table class="table table-border" id="table_aper">
									<tbody id='caja_o'>
										<tr>
											<td class="col-md-12" colspan="2" style="text-align: center"><label>TOTALES EN EFECTIVO</label></td>
										</tr>
										<tr>
											<td class="col-md-11">VENTAS CONTADO</td>
											<td class="col-md-1"><label id="id_total12" class='pull-right'><?php echo "$ ".number_format($total_contado_n, 2);?></label></td>
										</tr>
										<tr>
											<td class="col-md-11">(+)RECUPERACIÓN: VENTA PENDIENTE</td>
											<td class="col-md-1"><label id="id_total12" class='pull-right'><?php echo "$ ".number_format($total_contado_r, 2);?></label></td>
										</tr>
										<tr>
											<td class="col-md-11">(+)RECUPERACIÓN: ABONO CREDITO</td>
											<td class="col-md-1"><label id="id_total12" class='pull-right'><?php echo "$ ".number_format($total_abono_efectivo, 2);?></label></td>
										</tr>
										<tr>
											<td class="col-md-11">(-)DEVOLUCIONES</td>
											<td class="col-md-1"><label id="id_total12" class='pull-right'><?php echo "$ ".number_format($total_dev_2, 2);?></label></td>
										</tr>
										<tr>
											<td class="col-md-11">(-)NOTAS DE CREDITO</td>
											<td class="col-md-1"><label id="id_total12" class='pull-right'><?php echo "$ ".number_format($total_nc_2, 2);?></label></td>
										</tr>
										<tr>
											<td class="col-md-11">TOTAL REMESA</td>
											<td class="col-md-1"><label id="id_total12" class='pull-right'><?php echo "$ ".number_format($total_remesa, 2);?></label></td>
										</tr>
									</tbody>
								</table>

								<div class="row" id="caja_ve" hidden>
									<div class='alert alert-success text-center' style='font-weight: bold;'>
										<label style='font-size: 15px;'>Venta del dia</label>
									</div>
								</div>
								<table class="table table-border" id="table_ve" hidden>
									<tbody>
										<tr>
											<td class="col-md-11">VENTA</td>
											<td class="col-md-1"><label class="pull-right"><?php echo "$ ".number_format($total_venta_xz,2);?></label></td>
										</tr>
										<tr>
											<td class="col-md-11">(-)DEVOLUCIONES</td>
											<td class="col-md-1"><label class="pull-right"><?php echo "$ ".number_format($total_dev_2,2);?></label></td>
										</tr>
										<tr>
											<td class="col-md-11">(-)NOTAS DE CREDITO</td>
											<td class="col-md-1"><label class="pull-right"><?php echo "$ ".number_format($total_nc_2,2);?></label></td>
										</tr>
										<tr>
											<td class="col-md-11">TOTAL VENTA DEL DIA</td>
											<td class="col-md-11"><label class="pull-right"><?php echo "$ ".number_format($total_venta_xz,2);?></label></td>
										</tr>
									</tbody>
								</table>

								<!--Fin remesa-->
								<div class="row" id="add">
															<!--<div class="col-lg-4 col-md-4 col-sm-4">
		                              <div class="form-group">
		                                  <label style="color:blue">N° REMESA </label><input class="form-control" id='n_remesa' name='n_remesa'></label>
		                              </div>
		                          </div>-->
		                          <div class="col-lg-3 col-md-3 col-sm-3">
		                          	<div class="form-group">
		                          		<label style="color:blue">N° REMESA </label><input class="form-control clear" id='n_remesaa' name='n_remesaa'>
		                          	</div>
		                          </div>
		                          <div class="col-lg-2 col-md-2 col-sm-2">
		                          	<div class="form-group">
		                          		<label style="color:blue">REMESA </label><input class="form-control clear" id='remesaa' name='remesaa'>
		                          	</div>
		                          </div>
		                          <div class="col-lg-3 col-md-3 col-sm-3">
		                          	<div class="form-group">
		                          		<label style="color:blue">Banco </label>
		                          		<select id="bancoo" name="bancoo" class="form-control select">
		                          			<option value="">Seleccione</option>
		                          			<?php
		                          			$sql = _query("SELECT * FROM banco ORDER BY nombre");
		                          			while ($row = _fetch_array($sql))
		                          			{
		                          				echo "<option value='".$row["id_banco"]."'>".$row["nombre"]."</option>";
		                          			}
		                          			?>
		                          		</select>
		                          	</div>
		                          </div>
		                          <div class="col-lg-3 col-md-3 col-sm-3">
		                          	<div>
		                          		<label style="color:blue">Cuenta</label>
		                          		<select id="cuentaa" name="cuentaa" class="form-control select">
		                          			<option value="">Seleccione un Banco</option>
		                          		</select>
		                          	</div>
		                          </div>
		                         <div class="col-md-1">
									<div class="form-group">
										<br>
										<button  type="button" class="btn btn-primary" id="add_pre">Agregar</button>
									</div>
								</div>
								<!-- TABLA CON LAS REMESAS QUE DESEE AGREGAR-->
								<div class="row">
									<div class="col-md-12">
										<table class="table table-hover table-striped table-bordered">
											<thead>
												<tr>
													<th class="col-md-3">N° REMESA</th>
													<th class="col-md-3">REMESA</th>
													<th class="" hidden>CUENTA</th>
													<th class="col-lg-4" >CUENTA</th>
													<th class="col-md-2">Eliminar</th>
												</tr>
											</thead>
											<tbody id="presentacion_table">

											</tbody>
										</table>
									</div>
								</div>

								<!-- FINALIZA LA TABLA CON LAS REMESAS-->
		                          <div class="col-lg-12">
		                          	<div class="form-group">
		                          		<label>PEDIDOS PENDIENTES </label><input type="text" id="pedido_pendiente" name="pedido_pendiente" value=""  class="form-control ">
		                          	</div>
		                          </div>
		                          <div class="col-lg-12">
		                          	<div class="form-group">
		                          		<label>COBROS PENDIENTES </label><input type="text" id="cobro_pendiente" name="cobro_pendiente" value=""  class="form-control ">
		                          	</div>
		                          </div>
		                          <div class="col-lg-12">
		                          	<div class="form-group">
		                          		<label>MENSAJE </label><input type="text" id="mensaje" name="mensaje" value=""  class="form-control ">
		                          	</div>
		                          </div>
		                      </div>
		                      <div>
		                      	<input type="hidden" name="process" id="process" value="insert"><br>
															<!--
															<input type="hidden" name="lista_tike" id="lista_tike" value="<?php print_r($lista_tike);?>">
															<input type="hidden" name="lista_factura" id="lista_factura" value="<?php print_r($lista_factura);?>">
															<input type="hidden" name="lista_credito_fiscal" id="lista_credito_fiscal" value="<?php print_r($lista_credito_fiscal);?>">-->
															<input type="hidden" name="lista_dev" id="lista_dev" value="<?php print_r($lista_dev);?>">
															<input type="hidden" name="lista_nc" id="lista_nc" value="<?php print_r($lista_nc);?>">

															<input type="hidden" name="t_tike" id="t_tike" value="<?php echo $t_tike_2;?>">
															<input type="hidden" name="t_factuta" id="t_factuta" value="<?php echo $t_factuta_2;?>">
															<input type="hidden" name="t_credito" id="t_credito" value="<?php echo $t_credito_2;?>">

															<input type="hidden" name="t_tikexz" id="t_tikexz" value="<?php echo $t_tike;?>">
															<input type="hidden" name="t_factutaxz" id="t_facturaxz" value="<?php echo $t_factuta;?>">
															<input type="hidden" name="t_creditoxz" id="t_creditoxz" value="<?php echo $t_credito;?>">

															<input type="hidden" name="t_dev" id="t_dev" value="<?php echo $t_dev_2;?>">
															<input type="hidden" name="t_nc" id="t_nc" value="<?php echo $t_nc_2;?>">

															<input type="hidden" name="total_tike" id="total_tike" value="<?php echo $total_contado_tik;?>">
															<input type="hidden" name="total_factura" id="total_factura" value="<?php echo $total_contado_fac;?>">
															<input type="hidden" name="total_credito" id="total_credito" value="<?php echo $total_contado_ccf;?>">

															<input type="hidden" name="total_tikexz" id="total_tikexz" value="<?php echo $total_tike;?>">
															<input type="hidden" name="total_facturaxz" id="total_facturaxz" value="<?php echo $total_factura;?>">
															<input type="hidden" name="total_creditoxz" id="total_creditoxz" value="<?php echo $total_credito_fiscal;?>">

															<input type="hidden" name="total_dev" id="total_dev" value="<?php echo $total_dev_2;?>">
															<input type="hidden" name="total_nc" id="total_nc" value="<?php echo $total_nc_2;?>">


															<input type="hidden" name="id_empleado" id="id_empleado" value="<?php echo $empleado;?>">
															<input type="hidden" name="turno" id="turno" value="<?php echo $turno;?>">
															<input type="hidden" name="id_apertura" id="id_apertura" value="<?php echo $id_apertura;?>">
															<input type="hidden" name="caja_apertura" id="caja_apertura" value="<?php echo $caja;?>">

															<input type="hidden" name="tike_min" id="tike_min" value="<?php echo $tike_min;?>">
															<input type="hidden" name="tike_max" id="tike_max" value="<?php echo $tike_max;?>">
															<input type="hidden" name="factura_min" id="factura_min" value="<?php echo $factura_min;?>">
															<input type="hidden" name="factura_max" id="factura_max" value="<?php echo $factura_max;?>">
															<input type="hidden" name="credito_fiscal_min" id="credito_fiscal_min" value="<?php echo $credito_fiscal_min;?>">
															<input type="hidden" name="credito_fiscal_max" id="credito_fiscal_max" value="<?php echo $credito_fiscal_max;?>">
															<input type="hidden" name="dev_min" id="dev_min" value="<?php echo $dev_min;?>">
															<input type="hidden" name="dev_max" id="dev_max" value="<?php echo $dev_max;?>">

															<input type="hidden" name="tike_minxz" id="tike_minxz" value="<?php echo $tike_min_2;?>">
															<input type="hidden" name="tike_maxxz" id="tike_maxxz" value="<?php echo $tike_max_2;?>">
															<input type="hidden" name="factura_minxz" id="factura_minxz" value="<?php echo $factura_min_2;?>">
															<input type="hidden" name="factura_maxxz" id="factura_maxxz" value="<?php echo $factura_max_2;?>">
															<input type="hidden" name="credito_fiscal_minxz" id="credito_fiscal_minxz" value="<?php echo $credito_fiscal_min_2;?>">
															<input type="hidden" name="credito_fiscal_maxxz" id="credito_fiscal_maxxz" value="<?php echo $credito_fiscal_max_2;?>">

															<input type="hidden" name="monto_apertura" id="monto_apertura" value="<?php echo $monto_apertura;?>">
															<input type="hidden" name="recuperacion" id="recuperacion" value="<?php echo $total_recuperacion;?>">
															<input type="hidden" name="total_ch" id="total_ch" value="<?php echo $total_caja_chica;?>">
															<input type="hidden" name="monto_ch" id="monto_ch" value="<?php echo $monto_ch;?>">
															<input type="hidden" name="aper_id" id="aper_id" value="<?php echo $aper_id;?>">
															<input type="hidden" name="viaticos" id="viaticos" value="<?php echo $total_viatico;?>">
															<input type="hidden" name="abono_credito" id="abono_credito" value="<?php echo $total_abono_credito;?>">

															<input type="hidden" id="total_corte" name="total_corte" value="<?php echo $total_corte_2;?>">
															<input type="hidden" id="total_corte_z" name="total_corte_z" value="<?php echo $total_corte_z;?>">

															<input type='hidden' id='total_entrada' name='total_entrada' value='<?php echo $total_entrada_caja;?>'>
															<input type='hidden' id='total_salida' name='total_salida' value='<?php echo $total_salida_caja;?>'>

															<input type='hidden' id='total_vcontado' name='total_vcontado' value='<?php echo $total_contado_n;?>'>
															<input type='hidden' id='total_vcheque' name='total_vcheque' value='<?php echo $total_cheque_n;?>'>
															<input type='hidden' id='total_vtransferencia' name='total_vtransferencia' value='<?php echo $total_transferencia_n;?>'>
															<input type='hidden' id='total_vpendiente' name='total_vpendiente' value='<?php echo $total_venta_pendiente;?>'>
															<input type='hidden' id='total_vcredito' name='total_vcredito' value='<?php echo $total_credito;?>'>

															<input type="hidden" name="recuperacion_doc" id="recuperacion_doc" value="<?php echo $total_general_recuperacion;?>">
															<input type="hidden" name="total_rcredito" id="total_rcredito" value="<?php echo $total_abono_efectivo;?>">
															<input type="hidden" name="remesa" id="remesa" value="<?php echo $total_remesa;?>">
															<input type="hidden" name="total_facturado" id="total_facturado" value="<?php echo $total_facturado;?>">
															<input type="hidden" name="caja_saldo" id="caja_saldo" value="<?php echo $saldo_caja;?>">
															<input type="hidden" name="caja_saldo1" id="caja_saldo1" value="">
															<input type="hidden" name="sobrante" id="sobrante" value="">
															<input type="hidden" name="faltante" id="faltante" value="">

															<!--Abonos credito-->
															<input type="hidden" name="abono_creditoE" id="abono_creditoE" value="<?php echo $total_abono_efectivo;?>">
															<input type="hidden" name="abono_creditoC" id="abono_creditoC" value="<?php echo $total_abono_cheque;?>">
															<input type="hidden" name="abono_creditoT" id="abono_creditoT" value="<?php echo $total_abono_transferencia;?>">

															<!--Recuperacion Venta pendiente-->
															<input type="hidden" name="total_RE" id="total_RE" value="<?php echo $total_contado_r;?>">
															<input type="hidden" name="total_RC" id="total_RC" value="<?php echo $total_cheque_r;?>">
															<input type="hidden" name="total_RT" id="total_RT" value="<?php echo $total_transferencia_r;?>">

															<input type="submit" id="submit1" name="submit1" value="Guardar" class="btn btn-primary m-t-n-xs" />
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<?php
									include_once ("footer.php");
									echo "<script src='js/funciones/funciones_corte.js'></script>";
} //permiso del script
else {
	echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
}
}

function corte()
{
	date_default_timezone_set('America/El_Salvador');
	$fecha_corte = $_POST["fecha"];
	$total_corte = $_POST["total_corte"];
	$t_tike = $_POST["t_tike"];
	$t_factuta = $_POST["t_factuta"];
	$t_credito = $_POST["t_credito"];
	$t_dev = $_POST["t_dev"];
	$t_nc = $_POST["t_nc"];
	$fecha_actual = date("Y-m-d");
	$hora_actual = date("H:i:s");
	$id_sucursal = $_SESSION["id_sucursal"];
	$id_empleado = $_POST["id_empleado"];
	$sql_usuario=_fetch_array(_query("SELECT nombre FROM empleados WHERE id_empleado='$id_empleado'"));
	$usuario_name=$sql_usuario["nombre"];
	$turno = $_POST["turno"];
	$id_apertura = $_POST["id_apertura"];
	$tike_min = $_POST["tike_min"];
	$tike_max = $_POST["tike_max"];
	$factura_min = $_POST["factura_min"];
	$factura_max = $_POST["factura_max"];
	$credito_fiscal_min = $_POST["credito_fiscal_min"];
	$credito_fiscal_max = $_POST["credito_fiscal_max"];
	$monto_apertura = $_POST["monto_apertura"];
	$tipo_corte = $_POST["tipo_corte"];
	////////////////////////////////////
	$total_entrada = $_POST["total_entrada"];
	$total_salida = $_POST["total_salida"];
	$total_viatico = $_POST["viaticos"];

	$recuperacion = $_POST["recuperacion"];
	$abono_credito = $_POST["abono_credito"];

	$recuperacion_doc = $_POST["recuperacion_doc"];

	$total_vcontado = $_POST["total_vcontado"];
	$total_vcheque = $_POST["total_vcheque"];
	$total_vtransferencia = $_POST["total_vtransferencia"];
	$total_vpendiente = $_POST["total_vpendiente"];
	$total_vcredito = $_POST["total_vcredito"];

	$total_rcredito = $_POST["total_rcredito"];

	$remesa = $_POST["remesa"];
	$n_remesa = $_POST["n_remesa"];

	$total_facturado = $_POST["total_facturado"];
	$total_caja_chica = $_POST["total_ch"];


	$saldo_caja2 = $_POST["saldo_caja"];
	$sobrante = $_POST["sobrante"];
	$faltante = $_POST["faltante"];

	$pedido_pendiente = $_POST["pedido_pendiente"];
	$cobro_pendiente = $_POST["cobro_pendiente"];
	$mensaje = $_POST["mensaje"];

	///////////////////////////////////
	$lista_dev = $_POST["lista_dev"];
	$lista_nc = $_POST["lista_nc"];
	$total_contado = $_POST["total_vcontado"];
	$monto_ch = $_POST["monto_ch"];
	$caja = $_POST["caja_apertura"];
	$sql_cajax = _query("SELECT correlativo_dispo FROM caja WHERE id_caja = '$caja'");
	$rc = _fetch_array($sql_cajax);
	$correlativo_dispo = $rc["correlativo_dispo"];
	$nn_tik = $correlativo_dispo + 1;
	//$tike = $total_tike_e + $total_tike_g;
	//$factura = $total_factura_e + $totabono_creditoal_factura_g;
	//$credito = $total_credito_fiscal_e + $total_credito_fiscal_g;
	//$reserva = $total_reserva_g + $total_reserva_e;
	//$dev = $total_dev_e + $total_dev_g;
	$total_tike= $_POST["total_tike"];
	$total_factura = $_POST["total_factura"];
	$total_credito_fiscal = $_POST["total_credito"];

	$abono_creditoE = $_POST["abono_creditoE"];
	$abono_creditoC = $_POST["abono_creditoC"];
	$abono_creditoT = $_POST["abono_creditoT"];

	$total_RE = $_POST["total_RE"];
	$total_RC = $_POST["total_RC"];
	$total_RT = $_POST["total_RT"];

	if($tipo_corte=="C"){
		$lista=$_POST["lista"];
		$cuantos=$_POST["cuantos"];
	}else{
		$cuantos=0;
	}


	$tabla = "controlcaja";
	$form_data = array(
		'fecha_corte' => $fecha_actual,
		'hora_corte' => $hora_actual,
		'id_empleado' => $id_empleado,
		'id_sucursal' => $id_sucursal,
		'id_apertura' => $id_apertura,
		'totalt' => $total_tike,
		'totalf' => $total_factura,
		'totalcf' => $total_credito_fiscal,
		'totalgral' => $total_corte,
		'cashfinal' => $total_corte,
		'totalnot' => $t_tike,
		'totalnof' => $t_factuta,
		'totalnocf' => $t_credito,
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
		'saldo_caja' => $saldo_caja2,
		'faltante' => $faltante,
		'sobrante' => $sobrante,
		'n_remesa' => $n_remesa,
		'pedido_pendiente' => $pedido_pendiente,
		'cobro_pendiente' => $cobro_pendiente,
		'mensaje' => $mensaje,
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
	///remesas diarias
	if($cuantos>0){
		$lista = explode("|", $lista);
		$n = 0;
		for ($i=0; $i < $cuantos ; $i++)
		{
			list($n_reme,$reme,$id_cuenta)=explode(',',$lista[$i]);
			//$banco=$_POST["banco"];
			//$cuenta=$_POST["cuenta"];
		$tabla_remesa = "remesa_corte";
		$form_data_remesa = array(
			'id_apertura' => $id_apertura,
			'turno' => $turno,
			'n_remesa' => $n_reme,
			'remesa' => $reme,
			'fecha' => $fecha_actual,
			'banco' => 0,
			'cuenta' => $id_cuenta,
			);
			///registramos el mov cta banco
			$sql_saldo = _query("SELECT saldo FROM mov_cta_banco WHERE id_sucursal='$id_sucursal' AND id_cuenta='$id_cuenta' ORDER BY id_movimiento DESC LIMIT 1");
			$row_saldo = _fetch_array($sql_saldo);
			$saldo = $row_saldo["saldo"];
			$nsal = 0;
			$nsal = $saldo + $reme;
			$table_mv_banco = 'mov_cta_banco';
			$form_data_mv_banco = array (
				'id_cuenta' => $id_cuenta,
				'tipo' => "Ingreso",
				'alias_tipodoc' => "Remesa",
				'numero_doc' => $n_reme,
				'entrada' => $reme,
				'salida' => "0",
				'saldo' => $nsal,
				'fecha' => $fecha_actual,
				'responsable' => $usuario_name,
				'concepto' => "Corte #".$turno,
				'id_sucursal' => $id_sucursal,
			);
			///###insertando remesa y mov banco
			$insertar_remesa=_insert($tabla_remesa,$form_data_remesa);
			$insertar_remesa=_insert($table_mv_banco,$form_data_mv_banco);
		}

	}
	$sql_ = _query("SELECT * FROM controlcaja WHERE id_apertura = '$id_apertura' AND tipo_corte = 'Z'");
	$cuentax = _num_rows($sql_);
	if($cuentax == 0)
	{
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

                    	//$n++;
				}
			}



		}
		else if($tipo_corte == "X")
		{
			$extra = array('tiket' => $nn_tik ,);
			$resultx = array_merge($form_data, $extra);
			$insertar = _insert($tabla, $resultx);
			//$id_cortex= _insert_id();
			$id_cortex = _insert_id();
			if($insertar)
			{
				$t = "caja";
				$ff = array('correlativo_dispo' => $nn_tik,);
				$wp = "id_caja='".$caja."'";
				$upd = _update($t,$ff,$wp);


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

                    	//$n++;
				}
			}
		}
		else if($tipo_corte == "Z")
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

                    	//$n++;
					}
				}
			}
		}

		if($insertar)
		{
			$xdatos['typeinfo']='Success';
			$xdatos['msg']='Corte guardado correctamente !'.$correlativo_dispo.'';
			$xdatos['process']='insert';
			$xdatos['id_corte']=$id_cortex;
		}
		else
		{
			$xdatos['typeinfo']='Error';
			$xdatos['msg']='Error al guardar el corte !'._error();
		}
	}
	else
	{
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Ya existe un corte con esta apertura!';
	}
	echo json_encode($xdatos);
}

function  imprimir(){
	$id_corte = $_POST["id_corte"];
	$id_sucursal=$_SESSION['id_sucursal'];
	//directorio de script impresion cliente
	$sql_dir_print="SELECT *  FROM config_dir WHERE id_sucursal='$id_sucursal'";
	//$sql_dir_print="SELECT * FROM `config_dir` WHERE `id_sucursal`=1 ";
	$result_dir_print=_query($sql_dir_print);
	$row0=_fetch_array($result_dir_print);
	$dir_print=$row0['dir_print_script'];
	$shared_printer_win=$row0['shared_printer_matrix'];
	$shared_printer_pos=$row0['shared_printer_pos'];

	$info_mov=print_corte($id_corte);
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';

	$headers="";
	$sql_pos="SELECT *  FROM config_pos  WHERE id_sucursal='$id_sucursal' AND alias_tipodoc='TIK'";
	$result_pos=_query($sql_pos);
	$row1=_fetch_array($result_pos);
	for ($i=1; $i < 11; $i++) {
		// code...
		if ($row1['header'.$i]!="") {
			// code...
			$headers.=$row1['header'.$i]."\n";
		}
	}
	$footers=$row1['footer1']."|".$row1['footer2']."|".$row1['footer3']."|".$row1['footer4']."|".$row1['footer5']."|";
	$footers.=$row1['footer6']."|".$row1['footer7']."|".$row1['footer8']."|".$row1['footer8']."|".$row1['footer10']."|";
	$info_mov=$headers.$info_mov;
	$nreg_encode['shared_printer_win'] =$shared_printer_win;
	$nreg_encode['shared_printer_pos'] =$shared_printer_pos;
	$nreg_encode['dir_print'] =$dir_print;
	$nreg_encode['movimiento'] =$info_mov;
	$nreg_encode['sist_ope'] =$so_cliente;
	echo json_encode($nreg_encode);
}
function imprimir2(){
	$fecha_sent = MD($_POST["fecha"]);
	$turno_sent = $_POST["turno"];
	$tipo_corte_sent = $_POST["tipo_corte"];
	$id_sucursal=$_SESSION['id_sucursal'];
	//directorio de script impresion cliente
	$sql_dir_print="SELECT *  FROM config_dir WHERE id_sucursal='$id_sucursal'";
	//$sql_dir_print="SELECT * FROM `config_dir` WHERE `id_sucursal`=1 ";
	$result_dir_print=_query($sql_dir_print);
	$row0=_fetch_array($result_dir_print);
	$dir_print=$row0['dir_print_script'];
	$shared_printer_win=$row0['shared_printer_matrix'];
	$shared_printer_pos=$row0['shared_printer_pos'];

	$info_mov=print_corte2($fecha_sent,$turno_sent,$tipo_corte_sent);
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';

	$headers="";
	$sql_pos="SELECT *  FROM config_pos  WHERE id_sucursal='$id_sucursal' AND alias_tipodoc='TIK'";
	$result_pos=_query($sql_pos);
	$row1=_fetch_array($result_pos);
	for ($i=1; $i < 11; $i++) {
		// code...
		if ($row1['header'.$i]!="") {
			// code...
			$headers.=$row1['header'.$i]."\n";
		}
	}
	$footers=$row1['footer1']."|".$row1['footer2']."|".$row1['footer3']."|".$row1['footer4']."|".$row1['footer5']."|";
	$footers.=$row1['footer6']."|".$row1['footer7']."|".$row1['footer8']."|".$row1['footer8']."|".$row1['footer10']."|";
	$info_mov=$headers.$info_mov;
	$nreg_encode['shared_printer_win'] =$shared_printer_win;
	$nreg_encode['shared_printer_pos'] =$shared_printer_pos;
	$nreg_encode['dir_print'] =$dir_print;
	$nreg_encode['movimiento'] =$info_mov;
	$nreg_encode['sist_ope'] =$so_cliente;
	echo json_encode($nreg_encode);
}
function cambio()
{
	$tipo_corte = $_POST["tipo_corte"];
	$aper_id = $_POST["aper_id"];

	date_default_timezone_set('America/El_Salvador');
	$fecha_actual=date("Y-m-d");
	$hora_actual = date('H:i:s');
	$id_sucursal=$_SESSION['id_sucursal'];
	$sql_apertura1 = _query("SELECT * FROM apertura_caja WHERE id_apertura = '$aper_id' AND vigente = 1 AND id_sucursal = '$id_sucursal'");
	$cuenta1 = _num_rows($sql_apertura1);
	$row_apertura1 = _fetch_array($sql_apertura1);
	$id_apertura = $row_apertura1["id_apertura"];
	$tike_inicia = $row_apertura1["tiket_inicia"];
	$factura_inicia = $row_apertura1["factura_inicia"];
	$credito_inicia = $row_apertura1["credito_fiscal_inicia"];
	$empleado = $row_apertura1["id_empleado"];
	$dev_inicia = $row_apertura1["dev_inicia"];
	$turno = $row_apertura1["turno"];
	$fecha_apertura = $row_apertura1["fecha"];
	$hora_apertura = $row_apertura1["hora"];
	$monto_apertura = $row_apertura1["monto_apertura"];
	$monto_ch = $row_apertura1["monto_ch"];

	$tike_min = 0;
	$tike_max = 0;
	$factura_min = 0;
	$factura_max = 0;
	$credito_fiscal_min = 0;
	$credito_fiscal_max = 0;

	$t_tike = 0;
	$t_factuta = 0;
	$t_credito = 0;
	$t_dev = 0;
	$t_nc = 0;

	$total_tike = 0;
	$total_factura = 0;
	$total_credito_fiscal = 0;

	$total_contado = 0;
	$total_transferencia = 0;
	$total_cheque = 0;

	if($tipo_corte == "Z" || $tipo_corte == "X")
	{
		$sql_min_max = _query("SELECT MIN(num_fact_impresa) as minimo, MAX(num_fact_impresa) as maximo FROM factura WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND numero_doc LIKE '%TIK%' AND id_sucursal = '$id_sucursal' AND anulada = 0 UNION ALL SELECT MIN(num_fact_impresa) as minimo, MAX(num_fact_impresa) as maximo FROM factura WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND numero_doc LIKE '%COF%' AND id_sucursal = '$id_sucursal' AND anulada = 0 UNION ALL SELECT MIN(num_fact_impresa) as minimo, MAX(num_fact_impresa) as maximo FROM factura WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND numero_doc LIKE '%CCF%' AND id_sucursal = '$id_sucursal' AND anulada = 0" );
		$cuenta_min_max = _num_rows($sql_min_max);

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
				if($i == 3)
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
				$i += 1;
			}
		}

		$sql_corte = _query("SELECT * FROM factura WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND id_sucursal = '$id_sucursal' AND anulada = 0");
		$cuenta = _num_rows($sql_corte);
		//echo "SELECT * FROM factura WHERE fecha = '$fecha_apertura' AND id_apertura = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND id_sucursal = '$id_sucursal' AND anulada = 0";


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
				$tipo_pago = $row_corte["tipo_pago"];
				$pagada = $row_corte["pagada"];
				$alias_tipodoc = $row_corte["tipo_documento"];

				if($alias_tipodoc == 'TIK')
				{
					$total_tike += $total;
					if($tipo_pago == "CON")
					{
						$total_contado += $total;
					}
					else if($tipo_pago == "TRA")
					{
						$total_transferencia += $total;
					}
					else if($tipo_pago == "CHE")
					{
						$total_cheque += $total;
					}
					$t_tike += 1;
				}
				else if($alias_tipodoc == 'COF')
				{
					$total_factura += $total;
					if($tipo_pago == "CON")
					{
						$total_contado += $total;
					}
					else if($tipo_pago == "TRA")
					{
						$total_transferencia += $total;
					}
					else if($tipo_pago == "CHE")
					{
						$total_cheque += $total;
					}
					$t_factuta += 1;
				}
				else if($alias_tipodoc == 'CCF')
				{
					$total_credito_fiscal += $total;
					if($tipo_pago == "CON")
					{
						$total_contado += $total;
					}
					else if($tipo_pago == "TRA")
					{
						$total_transferencia += $total;
					}
					else if($tipo_pago == "CHE")
					{
						$total_cheque += $total;
					}
					$t_credito += 1;
				}
			}
		}
	}
	else
	{
		$sql_min_max = _query("SELECT MIN(num_fact_impresa) as minimo, MAX(num_fact_impresa) as maximo FROM factura WHERE fecha_pago = '$fecha_apertura' AND id_apertura_pagada = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND numero_doc LIKE '%TIK%' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND turno = '$turno' UNION ALL SELECT MIN(num_fact_impresa) as minimo, MAX(num_fact_impresa) as maximo FROM factura WHERE fecha_pago = '$fecha_apertura' AND id_apertura_pagada = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND numero_doc LIKE '%COF%' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND turno = '$turno' UNION ALL SELECT MIN(num_fact_impresa) as minimo, MAX(num_fact_impresa) as maximo FROM factura WHERE fecha_pago = '$fecha_apertura' AND id_apertura_pagada = '$id_apertura' AND hora BETWEEN '$hora_apertura' AND '$hora_actual' AND numero_doc LIKE '%CCF%' AND id_sucursal = '$id_sucursal' AND anulada = 0 AND turno = '$turno'" );
		$cuenta_min_max = _num_rows($sql_min_max);

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
				if($i == 3)
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
				$i += 1;
			}
		}
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




				if($tipo_documento == 'TIK')
				{
					$total_tike += $total;
					if($tipo_pago == "CON")
					{
						$total_contado += $total;
					}
					else if($tipo_pago == "TRA")
					{
						$total_transferencia += $total;
					}
					else if($tipo_pago == "CHE")
					{
						$total_cheque += $total;
					}
					$t_tike += 1;
				}
				else if($tipo_documento == 'COF')
				{
					$total_factura += $total;
					if($tipo_pago == "CON")
					{
						$total_contado += $total;
					}
					else if($tipo_pago == "TRA")
					{
						$total_transferencia += $total;
					}
					else if($tipo_pago == "CHE")
					{
						$total_cheque += $total;
					}
					$t_factuta += 1;
				}
				else if($tipo_documento == 'CCF')
				{
					$total_credito_fiscal += $total;
					if($tipo_pago == "CON")
					{
						$total_contado += $total;
					}
					else if($tipo_pago == "TRA")
					{
						$total_transferencia += $total;
					}
					else if($tipo_pago == "CHE")
					{
						$total_cheque += $total;
					}
					$t_credito += 1;
				}
			}
		}
	}

	$total_corte = $total_tike + $total_factura + $total_credito_fiscal;

	$xdatos['total_corte']=$total_corte;
	/////////////////////////////////
	$xdatos['t_tike']=$t_tike;
	$xdatos['t_factuta']=$t_factuta;
	$xdatos['t_credito']=$t_credito;
	/////////////////////////////////
	$xdatos['total_tike']=$total_tike;
	$xdatos['total_factura']=$total_factura;
	$xdatos['total_credito_fiscal']=$total_credito_fiscal;
	//////////////////////////////////
	$xdatos['total_contado'] = $total_contado;
	$xdatos['$total_transferencia'] = $total_transferencia;
	$xdatos['$total_cheque'] = $total_cheque;
	///////////////////////////////////
	$xdatos['tike_min'] = $tike_min;
	$xdatos['tike_max'] = $tike_max;
	$xdatos['factura_min'] = $factura_min;
	$xdatos['factura_max'] = $factura_max;
	$xdatos['credito_fiscal_min'] = $credito_fiscal_min;
	$xdatos['credito_fiscal_max'] = $credito_fiscal_max;
	////////////////////////////////////
	$xdatos['monto_apertura'] = $monto_apertura;
	$xdatos['monto_ch'] = $monto_ch;

	echo json_encode($xdatos);
}

if(!isset($_REQUEST['process'])){
	initial();
}
else
{
	if(isset($_REQUEST['process'])){
		switch ($_REQUEST['process']) {
			case 'insert':
			corte();
			break;
			case 'total_sistema':
		//total_sistema();
			break;
			case 'imprimir':
			imprimir();
			break;
			case 'imprimir2':
			imprimir2();
			break;
			case 'cambio':
			cambio();
			break;
		}
	}
}
?>
