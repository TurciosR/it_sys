<?php
include_once("unicode.php");

function print_ticket($id_factura){
	$id_sucursal=$_SESSION['id_sucursal'];
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	$id_empleado =$_SESSION['id_usuario'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';
	//Empresa
	$sql_empresa = "SELECT * FROM empresa";
	$result_empresa=_query($sql_empresa);
	$row_empresa=_fetch_array($result_empresa);
	$empresa=$row_empresa['nombre'];
	$razonsocial=$row_empresa['razonsocial'];
	$giro=$row_empresa['giro'];
	$nit=$row_empresa['nit'];
	$nrc=$row_empresa['nrc'];
	//Sucursal
	$sql_sucursal=_query("SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'");
	$array_sucursal=_fetch_array($sql_sucursal);
	$nombre_sucursal=$array_sucursal['descripcion'];
	$nombre_sucursal1=texto_espacios($nombre_sucursal,30);
	$empresa1=texto_espacios($empresa,30);
	$razonsocial1=texto_espacios($razonsocial,30);
	$giro1=texto_espacios($giro,30);
	//inicio datos
	$info_factura="";
	$info_factura.=$empresa1."|".$nombre_sucursal1."|".$razonsocial1."|".$giro1."|".$nit."|".$nrc."|";
	//Obtener informacion de tabla Factura
	$sql_fact="SELECT * FROM factura WHERE idtransace='$id_factura'";

	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact>0){
		$id_cliente=$row_fact['id_cliente'];
		$id_usuario=$row_fact['id_empleado'];
		$fecha=$row_fact['fecha_doc'];
		$hora=$row_fact['hora'];
		$fecha_fact=ed($fecha);
		$numero_doc=trim($row_fact['numero_doc']);
		$total=$row_fact['total'];
		$turno=$row_fact['turno'];
		$pares=$row_fact['pares'];
		$total_desc=$row_fact['descuento'];
		$id_vendedor=$row_fact['id_vendedor'];

		$numfact=espacios_izq($numero_doc,10);

		//Datos de empleados cajero
		$sql_user="select usuario,nombre from empleados where id_empleado='$id_usuario'";
		$result_user= _query($sql_user);
		$row_user=_fetch_array($result_user);
		$nrow_user=_num_rows($result_user);
		$usuario=$row_user['usuario'];
		$nombrecaj=trim($row_user['nombre']);
		$nombrecaje=explode(" ",$nombrecaj);
		$n_datos0=count($nombrecaje);
		$nombrecajero="";
		switch ($n_datos0) {
		case 1:
			$nombrecajero=$nombrecaj;
			break;
		case 2:
			$nombrecajero=$nombrecaj;
			break;
		case 3:
			$nombrecajero=$nombrecaje[0]." ".$nombrecaje[2];
			break;
		case 4:
			$nombrecajero=$nombrecaje[0]." ".$nombrecaje[2];
			break;
		case 5:
			$nombrecajero=$nombrecaje[0]." ".$nombrecaje[2];
			break;
		default:
			$nombrecajero=$nombrecaj;
			break;
		}
     ////Datos de empleados vendedor
 		$sql_user2="select usuario, nombre from empleados where id_empleado='$id_vendedor'";
 		$result_user2= _query($sql_user2);
 		$row_user2=_fetch_array($result_user2);
 		$nrow_user2=_num_rows($result_user2);
 		$usuariovendedor=$row_user2['usuario'];
 		$nombrevend=trim($row_user2['nombre']);
		$nombrevende=explode(" ",$nombrevend);
		$n_datos=count($nombrevende);
		$nombrevendedor="";
		switch ($n_datos) {
		case 1:
			$nombrevendedor=$nombrevend;
			break;
		case 2:
			$nombrevendedor=$nombrevend;
			break;
		case 3:
			$nombrevendedor=$nombrevende[0]." ".$nombrevende[2];
			break;
		case 4:
			$nombrevendedor=$nombrevende[0]." ".$nombrevende[2];
			break;
		case 5:
			$nombrevendedor=$nombrevende[0]." ".$nombrevende[2];
			break;
		default:
			$nombrevendedor=$nombrevend;
			break;
		}
		//Datos del Cliente
		$sql="select * from clientes where id_cliente='$id_cliente'";
		$result= _query($sql);
		$row1=_fetch_array($result);
		$nrow1=_num_rows($result);
		$nombre_cte=$row1['nombre'];
		$dui_cte=$row1['dui'];
		$nit_cte=$row1['nit'];
		$nrc_cte=$row1['nrc'];
		$giro_cte=$row1['giro'];
		$direccion_cte=$row1['direccion'];

		//Columnas y posiciones base

		$col0=1;		$col1=3; 		$col2=3;
		$col3=6;		$col4=5;		$sp1=2;
		$sp_prec=10;
		$sp=espacios_izq(" ",$sp1);
		$sp2=espacios_izq(" ",12);
		$esp_init=espacios_izq(" ",$col0);
		$esp_precios=espacios_izq(" ",$sp_prec);
		$esp_enc2=espacios_izq(" ",3);
		$esp_init2=espacios_izq(" ",23);
		$nombre_ape=texto_espacios($nombre_cte,32);
		$dir_txt=texto_espacios($direccion_cte,30);
		$total_final=0;
		//Datos del cliente
		$info_factura.=$esp_init."TRANSACC : ".$id_factura."|";
		$info_factura.=$esp_init."TIQUETE : ".$numero_doc."|";
		$info_factura.=$esp_init."FECHA: ".$fecha_fact."    HORA:".$hora."|";
		$info_factura.=$esp_init."CLIENTE: ".$nombre_cte."|";
		$info_factura.="CAJERO : ".$nombrecajero."|";
		$info_factura.="VENDEDOR : ".$nombrevendedor."|";
		$info_factura.="TURNO : ".$turno."|";
		$info_factura.="PARES : ".$pares."|";
		$info_factura.="TRANSACC : ".$id_factura."|";
		$info_factura.="DESCRIPCION                 CANT.  P. UNIT    SUBTOT.\n|";

		//Obtener informacion de tabla detalle_factura y producto o servicio
		$sql_fact_det="SELECT  productos.id_producto, productos.descripcion,productos.marca,productos.modelo,
		productos.serie,	productos.exento,detalle_factura.*
		FROM detalle_factura
		JOIN productos ON detalle_factura.id_producto=productos.id_producto
		WHERE  detalle_factura.idtransace='$id_factura'
		";

		$result_fact_det=_query($sql_fact_det);
		$nrows_fact_det=_num_rows($result_fact_det);
		$total_final=0;
		$lineas=6;
		$cuantos=0;
		$subt_exento=0;
		$subt_gravado=0;
		$total_exento=0;
		$total_gravado=0;

		for($i=0;$i<$nrows_fact_det;$i++){
			$row_fact_det=_fetch_array($result_fact_det);
			$id_producto =$row_fact_det['id_producto'];
			$descripcion =$row_fact_det['descripcion'];
			$marca=$row_fact_det['marca'];
			$modelo=$row_fact_det['modelo'];
			$serie=$row_fact_det['serie'];
			$exento=$row_fact_det['exento'];
			$id_detalle_factura =$row_fact_det['id_det_fact'];
			$cantidad =$row_fact_det['cantidad'];
			$precio_venta =$row_fact_det['precio'];
			$descuento =$row_fact_det['descuento'];
			$subt_g=$row_fact_det['gravado'];
			$subt_e =$row_fact_det['exento'];


			//linea a linea
			$descrip=texto_espacios($descripcion,27);
			$subt=$precio_venta*$cantidad-$descuento;
			$precio_unit=sprintf("%.2f",$precio_venta);
			$subtotal=sprintf("%.2f",$subt);
			$total_final=$total_final+$subtotal;
			if ($exento==0){
				$e_g="G";
				$subt_gravado=sprintf("%.2f",$subt);
				$total_gravado=$subt_gravado+$total_gravado;
			}
			else{
				$e_g="E";
				$subt_exento=sprintf("%.2f",$subt_e);
				$total_exento=$subt_exento+$total_exento;
			}
			if(strlen($cantidad)==1)
				$sp0=$col1;
			if(strlen($cantidad)==2)
				$sp0=$col1-1;
			if(strlen($cantidad)==3)
				$sp0=$col1-2;

			$esp_init=espacios_izq(" ",$sp0);
			if(strlen($precio_unit)>4){
				$sp2=$col2-1;
				$esp_col2=espacios_izq(" ",$sp2);
			}else{
				$sp2=$col2;
				$esp_col2=espacios_izq(" ",$sp2);
			}

			if(strlen($subtotal)<=3)
				$sp3=$col3;
			if(strlen($subtotal)==4)
				$sp3=$col3-1;
			if(strlen($subtotal)==5)
				$sp3=$col3-2;
			if(strlen($subtotal)==6)
				$sp3=$col3-3;
			if(strlen($subtotal)==7)
				$sp3=$col3-2;
			$esp_col3=espacios_izq(" ",$sp3);
			//$info_factura.=$cantidad."  ".$descrip.$esp_init." ".$esp_col2.$precio_unit.$esp_col3.$subtotal."\n";
			$info_factura.=$cantidad."  ".$descrip." ".$esp_col2.$precio_unit."   ".$descuento.$esp_col3.$subtotal."\n";
			$info_factura.="    ".$estilo."   ".$color."  ".$talla."  (".$e_g.") \n";
			$cuantos=$cuantos+1;

		}
	}
	$total_final_format=sprintf("%.2f",$total_final);
	list($entero,$decimal)=explode('.',$total_final_format);
	$enteros_txt=num2letras($entero);
	if(strlen($decimal)==1){
		$decimales_txt=$decimal."0";
	}
	else{
		$decimales_txt=$decimal;
	}

	$cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
	$esp=espacios_izq(" ",7);
	$total_value=sprintf("%.2f",$total);
	$sp3=10;
	$total_fin=$total_exento+$total_gravado;
	$total_value_exento=sprintf("%.2f",$total_exento);
	$total_value_gravado=sprintf("%.2f",$total_gravado);
	$total_value_fin=sprintf("%.2f",$total_fin);
	if(strlen($total_value)==3)
		$sp3=$sp3-1;
	if(strlen($total_value)==4)
		$sp3=$sp3-2;
	if(strlen($total_value)==5)
		$sp3=$sp3-3;
	if(strlen($total_value)==6)
		$sp3=$sp3-4;
	if(strlen($total_value)==7)
		$sp3=$sp3-5;

	$esp_init2=espacios_izq(" ",25);
	$esp_totales=espacios_izq(" ",$sp3);
	$info_factura.="|TOTAL GRAVADO".$esp_totales."  $ ".$total_value_gravado."\n";
	$info_factura.="TOTAL EXENTO ".$esp_totales."  $ ".$total_value_exento."\n";
	$info_factura.="TOTAL        ".$esp_totales."  $ ".$total_value_fin."\n";
	$info_factura.="|".$cadena_salida_txt."\n";
	$info_factura.="|"."TOTAL DESC   ".$esp_totales."  $ ".$total_desc."\n";
	$esp=espacios_izq(" ",30);
	//$info_factura.="\n";
	// retornar valor generado en funcion
	return ($info_factura);

}
function print_ticket_dev($id_factura,$dui_cte,$nombre_cte){
	$id_sucursal=$_SESSION['id_sucursal'];
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	$id_empleado =$_SESSION['id_usuario'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';
	//Empresa
	$sql_empresa = "SELECT * FROM empresa";
	$result_empresa=_query($sql_empresa);
	$row_empresa=_fetch_array($result_empresa);
	$empresa=$row_empresa['nombre'];
	$razonsocial=$row_empresa['razonsocial'];
	$giro=$row_empresa['giro'];
	$nit=$row_empresa['nit'];
	$nrc=$row_empresa['nrc'];
	//Sucursal
	$sql_sucursal=_query("SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'");
	$array_sucursal=_fetch_array($sql_sucursal);
	$nombre_sucursal=$array_sucursal['descripcion'];
	$nombre_sucursal1=texto_espacios($nombre_sucursal,30);
	$empresa1=texto_espacios($empresa,30);
	$razonsocial1=texto_espacios($razonsocial,30);
	$giro1=texto_espacios($giro,30);
	//inicio datos
	$info_factura="";
	$info_factura.=$empresa1."|".$nombre_sucursal1."|".$razonsocial1."|".$giro1."|".$nit."|".$nrc."|";
	//Obtener informacion de tabla Factura
	$sql_fact="SELECT * FROM factura WHERE idtransace='$id_factura'";

	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact>0){
		$id_cliente=$row_fact['id_cliente'];
		$id_usuario=$row_fact['id_empleado'];
		$fecha=$row_fact['fecha_doc'];
		$hora=$row_fact['hora'];
		$fecha_fact=ed($fecha);
		$numero_doc=trim($row_fact['numero_doc']);
		$total=$row_fact['total'];
		$turno=$row_fact['turno'];
		$pares=$row_fact['pares'];
		$descuento=$row_fact['descuento'];
		$id_vendedor=$row_fact['id_vendedor'];
		//$id_devolucion=$row_fact['id_devolucion'];
		$numfact=espacios_izq($numero_doc,10);
   $sql_dev="SELECT * FROM factura WHERE id_devolucion='$id_factura'";
	 $result_dev=_query($sql_dev);
 		$row_dev=_fetch_array($result_dev);
 		$nrows_dev=_num_rows($result_dev);
	  $id_fact_aplica_dev=$row_dev['idtransace'];
		//Datos de empleado
		$sql_user="select * from empleados where id_empleado='$id_usuario'";
		$result_user= _query($sql_user);
		$row_user=_fetch_array($result_user);
		$nrow_user=_num_rows($result_user);
		$usuario=$row_user['usuario'];
		$nombreusuario=$row_user['nombre'];

		//Datos del Cliente
		$sql="select * from clientes where id_cliente='$id_cliente'";
		$result= _query($sql);
		$row1=_fetch_array($result);
		$nrow1=_num_rows($result);
		//$nombre_cte=$row1['nombre'];
		//$dui_cte=$row1['dui'];
		$nit_cte=$row1['nit'];
		$nrc_cte=$row1['nrc'];
		$giro_cte=$row1['giro'];
		$direccion_cte=$row1['direccion'];

		//Columnas y posiciones base

		$col0=1;		$col1=3; 		$col2=3;
		$col3=6;		$col4=5;		$sp1=2;
		$sp_prec=10;
		$sp=espacios_izq(" ",$sp1);
		$sp2=espacios_izq(" ",12);
		$esp_init=espacios_izq(" ",$col0);
		$esp_precios=espacios_izq(" ",$sp_prec);
		$esp_enc2=espacios_izq(" ",3);
		$esp_init2=espacios_izq(" ",23);

		$nombre_cliente=texto_espacios($nombre_cte,32);
		$dir_txt=texto_espacios($direccion_cte,30);
		$total_final=0;
		//Datos del cliente
		//$info_factura.=$esp_init."TRANSACC : ".$id_factura."|";
		$info_factura.=$esp_init."TIQUETE : ".$numero_doc."|";
		$info_factura.=$esp_init."FECHA: ".$fecha_fact."    HORA:".$hora."|";
		$info_factura.=$esp_init."CLIENTE: ".$nombre_cte."|";
		$info_factura.=$esp_init."DUI: ".$dui_cte."|";
		$info_factura.="CAJERO : ".$nombreusuario."|";
		$info_factura.="VENDEDOR : ".$id_vendedor."|";
		$info_factura.="TURNO : ".$turno."|";
		$info_factura.="PARES : ".$pares."|";
		$info_factura.="TRANSACC : ".$id_fact_aplica_dev."|";
		$info_factura.="DESCRIPCION                 CANT.  P. UNIT    SUBTOT.\n|";

		//Obtener informacion de tabla detalle_factura y producto o servicio
		$sql_fact_det="SELECT  productos.id_producto, productos.descripcion,
		productos.estilo,productos.talla,
		colores.nombre as nombrecolor,
		productos.exento,detalle_factura.*
		FROM detalle_factura
		JOIN productos ON detalle_factura.id_producto=productos.id_producto
		JOIN colores  ON (productos.id_color=colores.id_color)
		WHERE  detalle_factura.idtransace='$id_factura'
		";

		$result_fact_det=_query($sql_fact_det);
		$nrows_fact_det=_num_rows($result_fact_det);
		$total_final=0;
		$lineas=6;
		$cuantos=0;
		$subt_exento=0;
		$subt_gravado=0;
		$total_exento=0;
		$total_gravado=0;

		for($i=0;$i<$nrows_fact_det;$i++){
			$row_fact_det=_fetch_array($result_fact_det);
			$id_producto =$row_fact_det['id_producto'];
			$descripcion =$row_fact_det['descripcion'];
			$color=$row_fact_det['nombrecolor'];
			$estilo=$row_fact_det['estilo'];
			$talla=$row_fact_det['talla'];
			$exento=$row_fact_det['exento'];
			$id_detalle_factura =$row_fact_det['id_det_fact'];
			$cantidad =$row_fact_det['cantidad'];
			$precio_venta =$row_fact_det['precio'];
			$subt =$row_fact_det['gravado'];
			$subt_e =$row_fact_det['exento'];


			//linea a linea
			$descrip=texto_espacios($descripcion,30);
			$subt=$precio_venta*$cantidad;
			$precio_unit=sprintf("%.2f",$precio_venta);
			$subtotal=sprintf("%.2f",$subt);
			$total_final=$total_final+$subtotal;
			if ($exento==0){
				$e_g="G";
				$subt_gravado=sprintf("%.2f",$subt);
				$total_gravado=$subt_gravado+$total_gravado;
			}
			else{
				$e_g="E";
				$subt_exento=sprintf("%.2f",$subt_e);
				$total_exento=$subt_exento+$total_exento;
			}
			if(strlen($cantidad)==1)
				$sp0=$col1;
			if(strlen($cantidad)==2)
				$sp0=$col1-1;
			if(strlen($cantidad)==3)
				$sp0=$col1-2;

			$esp_init=espacios_izq(" ",$sp0);
			if(strlen($precio_unit)>4){
				$sp2=$col2-1;
				$esp_col2=espacios_izq(" ",$sp2);
			}else{
				$sp2=$col2;
				$esp_col2=espacios_izq(" ",$sp2);
			}

			if(strlen($subtotal)<=3)
				$sp3=$col3;
			if(strlen($subtotal)==4)
				$sp3=$col3-1;
			if(strlen($subtotal)==5)
				$sp3=$col3-2;
			if(strlen($subtotal)==6)
				$sp3=$col3-3;
			if(strlen($subtotal)==7)
				$sp3=$col3-2;
			$esp_col3=espacios_izq(" ",$sp3);
			$info_factura.=$cantidad."  ".$descrip.$esp_init." ".$esp_col2.$precio_unit.$esp_col3.$subtotal."\n";
			$info_factura.="    ".$estilo."   ".$color."  ".$talla."  (".$e_g.") \n";
			$cuantos=$cuantos+1;
		}
	}
	$total_final_format=sprintf("%.2f",$total_final);
	list($entero,$decimal)=explode('.',$total_final_format);
	$enteros_txt=num2letras($entero);
	if(strlen($decimal)==1){
		$decimales_txt=$decimal."0";
	}
	else{
		$decimales_txt=$decimal;
	}

	$cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
	$esp=espacios_izq(" ",7);
	$total_value=sprintf("%.2f",$total);
	$sp3=10;
	$total_fin=$total_exento+$total_gravado;
	$total_value_exento=sprintf("%.2f",$total_exento);
	$total_value_gravado=sprintf("%.2f",$total_gravado);
	$total_value_fin=sprintf("%.2f",$total_fin);
	if(strlen($total_value)==3)
		$sp3=$sp3-1;
	if(strlen($total_value)==4)
		$sp3=$sp3-2;
	if(strlen($total_value)==5)
		$sp3=$sp3-3;
	if(strlen($total_value)==6)
		$sp3=$sp3-4;
	if(strlen($total_value)==7)
		$sp3=$sp3-5;

	$esp_init2=espacios_izq(" ",25);
	$esp_totales=espacios_izq(" ",$sp3);
	$info_factura.="|TOTAL GRAVADO".$esp_totales."  $ ".$total_value_gravado."\n";
	$info_factura.="TOTAL EXENTO ".$esp_totales."  $ ".$total_value_exento."\n";
	$info_factura.="TOTAL        ".$esp_totales."  $ -".$total_value_fin."\n";
	$info_factura.="|".$cadena_salida_txt."\n";
	$info_factura.="|"."TOTAL DESC  ".$esp_totales."  $ ".$descuento."\n";
	$esp=espacios_izq(" ",30);
	//$info_factura.="\n";
	// retornar valor generado en funcion
	return ($info_factura);

}
function print_ticket_res($id_factura,$dui_cte,$nombre_cte,$tel_cte1,$tel_cte2){
	$id_sucursal=$_SESSION['id_sucursal'];
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	$id_empleado =$_SESSION['id_usuario'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';
	//Empresa
	$sql_empresa = "SELECT * FROM empresa";
	$result_empresa=_query($sql_empresa);
	$row_empresa=_fetch_array($result_empresa);
	$empresa=$row_empresa['nombre'];
	$razonsocial=$row_empresa['razonsocial'];
	$giro=$row_empresa['giro'];
	$nit=$row_empresa['nit'];
	$nrc=$row_empresa['nrc'];
	//Sucursal
	$sql_sucursal=_query("SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'");
	$array_sucursal=_fetch_array($sql_sucursal);
	$nombre_sucursal=$array_sucursal['descripcion'];
	$nombre_sucursal1=texto_espacios($nombre_sucursal,30);
	$empresa1=texto_espacios($empresa,30);
	$razonsocial1=texto_espacios($razonsocial,30);
	$giro1=texto_espacios($giro,30);

	//inicio datos
	$info_factura="";
	$info_factura.=$empresa1."|".$nombre_sucursal1."|".$razonsocial1."|".$giro1."|".$nit."|".$nrc."|";
	//Obtener informacion de tabla reservas
	$sql_fact="SELECT * FROM reservas WHERE id_reserva='$id_factura'";

	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact>0){
		$id_cliente=$row_fact['id_cliente'];
		$id_usuario=$row_fact['id_empleado'];
		$fecha=$row_fact['fecha_doc'];
		$hora=$row_fact['hora'];
		$fecha_fact=ed($fecha);
		$numero_doc=trim($row_fact['numero_doc']);
		$total=$row_fact['total'];
		$abono= $row_fact['abono'];
		$saldo= $row_fact['saldo'];
		$turno=$row_fact['turno'];
		$total_desc=$row_fact['descuento'];
		$id_vendedor=$row_fact['id_vendedor'];
		$numfact=espacios_izq($numero_doc,10);

		//Datos de empleado cajero
		$sql_user="select * from empleados where id_empleado='$id_usuario'";
		$result_user= _query($sql_user);
		$row_user=_fetch_array($result_user);
		$nrow_user=_num_rows($result_user);
		$usuario=$row_user['usuario'];
		$nombreusuario=$row_user['nombre'];
		//Columnas y posiciones base
		$col0=1;		$col1=3; 		$col2=3;
		$col3=6;		$col4=5;		$sp1=2;
		$sp_prec=10;
		$sp=espacios_izq(" ",$sp1);
		$sp2=espacios_izq(" ",12);
		$esp_init=espacios_izq(" ",$col0);
		$esp_precios=espacios_izq(" ",$sp_prec);
		$esp_enc2=espacios_izq(" ",3);
		$esp_init2=espacios_izq(" ",23);
		$nombre_cliente=texto_espacios($nombre_cte,32);
	//	$dir_txt=texto_espacios($direccion_cte,30);
		$total_final=0;
		//vendeodr
		$nombrevendedor=vendedor($id_vendedor);
		//Datos del cliente

		$info_factura.=$esp_init."RESERVA # ".$numero_doc."|";
		$info_factura.=$esp_init."TRANSACC :".$id_factura."|";
		$info_factura.=$esp_init."FECHA: ".$fecha_fact."    HORA:".$hora."|";
		$info_factura.=$esp_init."CLIENTE: ".$nombre_cte."|";
		$info_factura.=$esp_init."DUI: ".$dui_cte."|";
		$info_factura.="CAJERO : ".$nombreusuario."|";
		$info_factura.="VENDEDOR : ".$nombrevendedor."|";
		$info_factura.="TURNO : ".$turno."|";

		$info_factura.="DESCRIPCION                 CANT.  P. UNIT    SUBTOT.\n|";

		//Obtener informacion de tabla detalle_reservas y producto o servicio
		//productos.estilo,productos.talla,
		$sql_fact_det="SELECT  productos.id_producto, productos.descripcion,
		productos.exento,productos.talla,productos.estilo,
		colores.nombre as nombrecolor,
		detalle_reservas.precio,detalle_reservas.cantidad,detalle_reservas.subtotal,
		detalle_reservas.id_det_reserva,detalle_reservas.descuento
		FROM detalle_reservas
		JOIN productos ON detalle_reservas.id_producto=productos.id_producto
		JOIN colores  ON (productos.id_color=colores.id_color)
		WHERE  detalle_reservas.id_reserva='$id_factura'
		";

		$result_fact_det=_query($sql_fact_det);
		$nrows_fact_det=_num_rows($result_fact_det);
		$total_final=0;
		$lineas=6;
		$cuantos=0;
		$subt_exento=0;
		$subt_gravado=0;
		$total_exento=0;
		$total_gravado=0;

		for($i=0;$i<$nrows_fact_det;$i++){
			$row_fact_det=_fetch_array($result_fact_det);
			$id_producto =$row_fact_det['id_producto'];
			$descripcion =$row_fact_det['descripcion'];
			$exento=0;
			$id_detalle_factura =$row_fact_det['id_det_reserva'];
			$cantidad =$row_fact_det['cantidad'];
			$precio_venta =$row_fact_det['precio'];
			$color=$row_fact_det['nombrecolor'];
			$estilo=$row_fact_det['estilo'];
			$talla=$row_fact_det['talla'];
			$descuento=$row_fact_det['descuento'];
			$subt =$row_fact_det['subtotal'];
			$subt_e =0;

			//linea a linea
			$descrip=texto_espacios($descripcion,27);
			//$subt=$precio_venta*$cantidad;
			$subt=$precio_venta*$cantidad-$descuento;
			$precio_unit=sprintf("%.2f",$precio_venta);
			$subtotal=sprintf("%.2f",$subt);
			$descuento1=sprintf("%.2f",$descuento);
			$total_final=$total_final+$subtotal;
			if ($exento==0){
				$e_g="G";
				$subt_gravado=sprintf("%.2f",$subt);
				$total_gravado=$subt_gravado+$total_gravado;
			}
			else{
				$e_g="E";
				$subt_exento=sprintf("%.2f",$subt_e);
				$total_exento=$subt_exento+$total_exento;
			}
			if(strlen($cantidad)==1)
				$sp0=$col1;
			if(strlen($cantidad)==2)
				$sp0=$col1-1;
			if(strlen($cantidad)==3)
				$sp0=$col1-2;

			$esp_init=espacios_izq(" ",$sp0);
			if(strlen($precio_unit)>4){
				$sp2=$col2-1;
				$esp_col2=espacios_izq(" ",$sp2);
			}else{
				$sp2=$col2;
				$esp_col2=espacios_izq(" ",$sp2);
			}

			if(strlen($subtotal)<=3)
				$sp3=$col3;
			if(strlen($subtotal)==4)
				$sp3=$col3-1;
			if(strlen($subtotal)==5)
				$sp3=$col3-2;
			if(strlen($subtotal)==6)
				$sp3=$col3-3;
			if(strlen($subtotal)==7)
				$sp3=$col3-2;
			$esp_col3=espacios_izq(" ",$sp3);

			//$info_factura.=$cantidad."  ".$descrip.$esp_init." ".$esp_col2.$precio_unit.$esp_col3.$subtotal."\n";
			$info_factura.=$cantidad."  ".$descrip." ".$esp_col2.$precio_unit."   ".$descuento1.$esp_col3.$subtotal."\n";
			$info_factura.="    ".$estilo."   ".$color."  ".$talla."  (".$e_g.") \n";
			$cuantos=$cuantos+1;
		}
	}
	$total_final_format=sprintf("%.2f",$total_final);
	list($entero,$decimal)=explode('.',$total_final_format);
	$enteros_txt=num2letras($entero);
	if(strlen($decimal)==1){
		$decimales_txt=$decimal."0";
	}
	else{
		$decimales_txt=$decimal;
	}

	$cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
	$esp=espacios_izq(" ",7);
	$total_value=sprintf("%.2f",$total);
	$sp3=10;
	$total_fin=$total_exento+$total_gravado+$total_desc;
	$total_value_exento=sprintf("%.2f",$total_exento);
	$total_value_gravado=sprintf("%.2f",$total_gravado);
	$total_value_fin=sprintf("%.2f",$total_fin);
	if(strlen($total_value)==3)
		$sp3=$sp3-1;
	if(strlen($total_value)==4)
		$sp3=$sp3-2;
	if(strlen($total_value)==5)
		$sp3=$sp3-3;
	if(strlen($total_value)==6)
		$sp3=$sp3-4;
	if(strlen($total_value)==7)
		$sp3=$sp3-5;
  $total_pagar=$total_exento+$total_gravado;
	$esp_init2=espacios_izq(" ",25);
	$esp_totales=espacios_izq(" ",$sp3);
	$total_desc_mostrar=sprintf("%.2f",$total_desc);
	$info_factura.="| TOTAL        ".$esp_totales."  $ ".$total_value_fin."\n";
	$info_factura.=" TOTAL DESC   ".$esp_totales."  $ ".$total_desc_mostrar."\n";
	$info_factura.=" TOTAL PAGAR  ".$esp_totales."  $ ".$total_pagar;
	$info_factura.="\n";
	$info_factura.="|"." ABONO        ".$esp_totales."  $ ".$abono."\n";
	$info_factura.=" SALDO        ".$esp_totales."  $ ".$saldo;
	$info_factura.="|".$cadena_salida_txt."\n";


	$esp=espacios_izq(" ",30);
	//$info_factura.="\n";
	// retornar valor generado en funcion
	return ($info_factura);

}
function print_fact2($id_fact,$tipo_id){
	$id_sucursal=$_SESSION['id_sucursal'];
	$id_factura=$id_fact;
	$tipo_id=$tipo_id;
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';
	//Empresa
	$sql_empresa = "SELECT * FROM empresa";
	$result_empresa=_query($sql_empresa);
	$row_empresa=_fetch_array($result_empresa);
	$empresa=$row_empresa['nombre'];
	$razonsocial=$row_empresa['razonsocial'];
	$giro=$row_empresa['giro'];
	//Sucursal
	$sql_sucursal=_query("SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'");
	$array_sucursal=_fetch_array($sql_sucursal);
	$nombre_sucursal=$array_sucursal['descripcion'];
	$nombre_sucursal1=texto_espacios($nombre_sucursal,30);
	$empresa1=texto_espacios($empresa,30);
	$razonsocial1=texto_espacios($razonsocial,30);
	$giro1=texto_espacios($giro,30);
	//inicio datos
	$info_factura="";
	$info_factura.=$empresa1."|".$nombre_sucursal1."|".$razonsocial1."|".$giro1."|";
	//Obtener informacion de tabla Factura
	if($tipo_id=='idfact'){
		$id_factura=$id_fact;
		$sql_fact="SELECT * FROM factura WHERE id_factura='$id_factura'";
	}
	if($tipo_id=='COF'){
		$numero_docx=$id_fact;
		$sql_fact="SELECT * FROM factura WHERE numero_doc='$numero_docx'";
	}
	if($tipo_id=='CCF'){
		$numero_docx=$id_fact."_CCF";
		$sql_fact="SELECT * FROM factura WHERE numero_doc='$numero_docx'";
	}
	$sql_fact="SELECT * FROM factura WHERE id_factura='$id_factura'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact>0){
		$id_cliente=$row_fact['id_cliente'];
		$id_factura = $row_fact['id_factura'];
		$id_usuario=$row_fact['id_usuario'];
		$fecha=$row_fact['fecha'];
		$fecha_fact=ed($fecha);
		$numero_doc=trim($row_fact['numero_doc']);
		$total=$row_fact['total'];

		$len_numero_doc=strlen($numero_doc)-4;
		$num_fact=substr($numero_doc,0,$len_numero_doc);
		$tipo_fact=substr($numero_doc,$len_numero_doc,4);
		$numfact=espacios_izq($num_fact,10);
		//Datos de empleado
		$sql_user="select * from usuario where id_usuario='$id_usuario'";
		$result_user= _query($sql_user);
		$row_user=_fetch_array($result_user);
		$nrow_user=_num_rows($result_user);
		$usuario=$row_user['usuario'];
		$nombreusuario=$row_user['nombre'];
		//$nombres=$row_user['apellido']." ".$row_user['nombre'];
		//Datos del Cliente
		$sql="select * from cliente where id_cliente='$id_cliente'";
		$result= _query($sql);
		$row1=_fetch_array($result);
		$nrow1=_num_rows($result);
		$nombres=$row1['apellido']." ".$row1['nombre'];
		$dui=$row1['dui'];
		$nit=$row1['nit'];
		$direccion=$row1['direccion'];

		//Columnas y posiciones base
		$base1=7;
		$col0=1;
		$col1=4;
		$col2=3;
		$col3=13;
		$col4=5;
		$sp1=2;
		$sp_prec=15;
		$sp=espacios_izq(" ",$sp1);
		$sp2=espacios_izq(" ",12);
		$esp_init=espacios_izq(" ",5);
		$esp_precios=espacios_izq(" ",$sp_prec);
		$esp_enc2=espacios_izq(" ",3);
		$esp_init2=espacios_izq(" ",45);
		$nombre_ape=texto_espacios($nombres,32);
		$dir_txt=texto_espacios($direccion,30);
		$total_final=0;
		$imprimir="";
		for($h=0;$h<5;$h++){
			$imprimir.="\n";
		}
		$info_factura.=$imprimir;
		//Datos encabezado factura
		list($diaa,$mess,$anio)=explode("-",$fecha_fact);
		$info_factura.=$esp_init2.$diaa."       ".$mess."             ".$anio."|";
		$info_factura.=$esp_init."FACTURA CONSUMIDOR # ".$num_fact."|";
		//Datos del cliente
		$info_factura.=$esp_init."            ".$nombre_ape."|";
		$info_factura.=$esp_init.$direccion."|";
		$info_factura.=$esp_init.$dui."|";
		$info_factura.=$esp_init.$nit."|";
		//Obtener informacion de tabla detalle_factura y producto o servicio
		$sql_fact_det="SELECT  productos.id_producto, productos.descripcion, productos.exento,detalle_factura.*
		FROM detalle_factura JOIN producto ON detalle_factura.id_producto=productos.id_producto
		WHERE  detalle_factura.id_factura='$id_factura' AND  detalle_factura.tipo_prod_serv='PRODUCTO'
		UNION ALL
		SELECT  servicio.id_servicio, servicio.descripcion,servicio.exento,detalle_factura.*
		FROM detalle_factura JOIN servicio ON detalle_factura.id_producto=servicio.id_servicio
		WHERE  detalle_factura.id_factura='$id_factura' AND  detalle_factura.tipo_prod_serv='SERVICIO'
		";

		$result_fact_det=_query($sql_fact_det);
		$nrows_fact_det=_num_rows($result_fact_det);
		$total_final=0;
		$lineas=6;
		$cuantos=0;
		$subt_exento=0;
		$subt_gravado=0;
		$total_exento=0;
		$total_gravado=0;

		for($i=0;$i<$nrows_fact_det;$i++){
			$row_fact_det=_fetch_array($result_fact_det);
			$id_producto =$row_fact_det['id_producto'];
			$descripcion =$row_fact_det['descripcion'];
			$exento=$row_fact_det['exento'];
			$id_detalle_factura =$row_fact_det['id_detalle_factura'];
			$id_producto =$row_fact_det['id_producto'];
			$cantidad =$row_fact_det['cantidad'];
			$precio_venta =$row_fact_det['precio_venta'];
			$subt =$row_fact_det['subtotal'];
			$id_empleado =$row_fact_det['id_empleado'];
			$tipo_prod_serv =$row_fact_det['tipo_prod_serv'];

			//linea por linea de productos
			$descrip=texto_espacios($descripcion,33);
			$subt=$precio_venta*$cantidad;
			$subt_sin_iva=$precio_venta*$cantidad;
			$subt_sin_iva_print=sprintf("%.2f",$subt_sin_iva);
			$precio_unit=sprintf("%.2f",$precio_venta);
			$subtotal=sprintf("%.2f",$subt);
			$total_final=$total_final+$subtotal;
			if ($exento==0){
				$e_g="G";
				$subt_gravado=sprintf("%.2f",$subtotal);
				$total_gravado=$subtotal+$total_gravado;
			}
			else{
				$e_g="E";
				$subt_exento=sprintf("%.2f",$subtotal);
				$total_exento=$subtotal+$total_exento;
			}

      $col2=2;
			$sp1=len_espacios($cantidad,6);
	 		$esp_col1=espacios_izq(" ",$sp1);
	 		$sp2=len_espacios($precio_unit,8);
	 		$esp_col2=espacios_izq(" ",$sp2);
	 		$sp3=len_espacios($subtotal,9);
	 		$esp_col3=espacios_izq(" ",$sp3);
	 		$esp_desc=espacios_izq(" ",5);
  		if ($exento==1){
				$info_factura.=$esp_col1.$cantidad.$esp_desc.$descrip.$esp_col2.$precio_unit.$esp_col3." ".$subtotal."\n";
  			}
  			if ($exento==0){
					$sp3=$sp3+8;
					$esp_col3=espacios_izq(" ",$sp3);
  				$info_factura.=$esp_col1.$cantidad.$esp_desc.$descrip.$esp_col2.$precio_unit.$esp_col3." ".$subtotal."\n";
				}
			$cuantos=$cuantos+1;
		}
	}
	$total_final_format=sprintf("%.2f",$total_final);
	list($entero,$decimal)=explode('.',$total_final_format);
	$enteros_txt=num2letras($entero);
	if(strlen($decimal)==1){
		$decimales_txt=$decimal."0";
	}
	else{
		$decimales_txt=$decimal;
	}

	$cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
	$esp=espacios_izq(" ",7);
	$total_value=sprintf("%.2f",$total);
	$sp3=10;
	$total_fin=$total_exento+$total_gravado;
	$total_value_exento=sprintf("%.2f",$total_exento);
	$total_value_gravado=sprintf("%.2f",$total_gravado);
	$total_value_fin=sprintf("%.2f",$total_fin);

	//totales
	$lineas_faltantes=11-$cuantos;
	$imprimir="";
	for($j=0;$j<$lineas_faltantes;$j++){
		$info_factura.="\n";
	}

	$info_factura.="\n";
	$info_factura.="\n";
	$esp_init2=espacios_izq(" ",25);
	$esp_totales=espacios_izq(" ",30);
	//generar 2 lineas del texto del total de la factura
	$total_txt0 =cadenaenlineas($cadena_salida_txt, 30,2);
	$concepto_print="";
	$tmplinea = array();
	$ln=0;
	$esp_init=espacios_izq(" ",6);

	foreach($total_txt0 as $total_txt1){
		$tmplinea[]=$total_txt1;
		$ln=$ln+1;
	}
	$esp_totales=espacios_izq(" ",52);
  $splentot1=len_espacios($total_value_exento,9);
			$esp_lentot1=espacios_izq(" ",$splentot1);
			$splentot2=len_espacios($total_value_gravado,9);
			$esp_lentot2=espacios_izq(" ",$splentot2);

			//$info_factura.=$esp_totales.$esp_lentot1.$total_value_exento.$esp_lentot2.$total_value_gravado."\n";
			$info_factura.=$esp_totales.$esp_lentot1."   ".$esp_lentot2.$total_value_gravado."\n";
			$linea0=strlen(trim($tmplinea[0]));
			$len_desc=(30-$linea0)+15;
			$esp_totales=espacios_izq(" ",$len_desc);
			$info_factura.=$esp_init.$tmplinea[0]."\n";

			if($ln>1){
						$len_desc=55-strlen(trim($tmplinea[1]));
						$esp_totales=espacios_izq(" ",$len_desc);
						$info_factura.=$esp_init.$tmplinea[1].$esp_totales.$esp_lentot2.$total_value_gravado."\n";
					}
					else{
						$esp_totales=espacios_izq(" ",62);
						$info_factura.=$esp_totales.$esp_lentot1.$total_value_gravado."\n";
					}
	/*if($ln==1){
	 $info_factura.="\n";
 }*/
	$esp_totales=espacios_izq(" ",62);
	$info_factura.="\n";
	$info_factura.=$esp_totales.$esp_lentot1.$total_value_exento."\n";
	$info_factura.=$esp_totales.$esp_lentot2.$total_final_format."\n";
	// retornar valor generado en funcion
	return ($info_factura);

}
function print_fact($id_factura,$tipo_id,$nitcte,$nrccte,$nombreapecte="")
{
	// $id_sucursal=$_SESSION['id_sucursal'];
 //
	// //Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	// $info = $_SERVER['HTTP_USER_AGENT'];
	// if(strpos($info, 'Windows') == TRUE)
	// 	$so_cliente='win';
	// else
	// 	$so_cliente='lin';
	// //Empresa
	// $sql_empresa = "SELECT * FROM empresa";
	// $result_empresa=_query($sql_empresa);
	// $row_empresa=_fetch_array($result_empresa);
	// $empresa=$row_empresa['nombre'];
	// $razonsocial=$row_empresa['razonsocial'];
	// $giro=$row_empresa['giro'];
	// //Sucursal
	// $empresa1=texto_espacios($empresa,30);
	// $razonsocial1=texto_espacios($razonsocial,30);
	// $giro1=texto_espacios($giro,30);
	// //inicio datos
	// $info_factura="";
	// $info_factura.=$empresa1."| |".$razonsocial1."|".$giro1."|";
	// //Obtener informacion de tabla Factura
 //
	// $sql_fact="SELECT * FROM factura WHERE id_factura='$id_factura'";
	// $result_fact=_query($sql_fact);
	// $row_fact=_fetch_array($result_fact);
	// $nrows_fact=_num_rows($result_fact);
	// if($nrows_fact>0){
	// 	$id_cliente=$row_fact['id_cliente'];
	// 	$id_factura = $row_fact['id_factura'];
	// 	$id_usuario=$row_fact['id_usuario'];
	// 	$fecha=$row_fact['fecha'];
	// 	$fecha_fact=ed($fecha);
	// 	$numero_doc=trim($row_fact['numero_doc']);
	// 	$total=$row_fact['total'];
 //
	// 	$len_numero_doc=strlen($numero_doc)-4;
	// 	$num_fact=substr($numero_doc,0,$len_numero_doc);
	// 	$tipo_fact=substr($numero_doc,$len_numero_doc,4);
	// 	$numfact=espacios_izq($num_fact,10);
	// 	//Datos de empleado
	// 	$sql_user="select * from usuario where id_usuario='$id_usuario'";
	// 	$result_user= _query($sql_user);
	// 	$row_user=_fetch_array($result_user);
	// 	$nrow_user=_num_rows($result_user);
	// 	$usuario=$row_user['usuario'];
	// 	$nombreusuario=$row_user['nombre'];
	// 	//$nombres=$row_user['apellido']." ".$row_user['nombre'];
	// 	//Datos del Cliente
	// 	$sql="select * from cliente where id_cliente='$id_cliente'";
	// 	$result= _query($sql);
	// 	$row1=_fetch_array($result);
	// 	$nrow1=_num_rows($result);
	// 	$nombres=$row1['apellido']." ".$row1['nombre'];
	// 	$dui=$row1['dui'];
	// 	$nit=$row1['nit'];
	// 	$direccion=$row1['direccion'];
 //
	// 	//Columnas y posiciones base
	// 	$base1=7;
	// 	$col0=1;
	// 	$col1=4;
	// 	$col2=3;
	// 	$col3=13;
	// 	$col4=5;
	// 	$sp1=2;
	// 	$sp_prec=15;
	// 	$sp=espacios_izq(" ",$sp1);
	// 	$sp2=espacios_izq(" ",12);
	// 	$esp_init=espacios_izq(" ",12);
	// 	$esp_precios=espacios_izq(" ",$sp_prec);
	// 	$esp_enc2=espacios_izq(" ",3);
	// 	$esp_init2=espacios_izq(" ",70);
	// 	$nombre_ape=texto_espacios($nombres,32);
	// 	$dir_txt=texto_espacios($direccion,30);
	// 	$total_final=0;
	// 	$imprimir="";
	// 	for($h=0;$h<8;$h++){
	// 		$imprimir.="\n";
	// 	}
	// 	$info_factura.=$imprimir;
	// 	//Datos encabezado factura
	// 	list($diaa,$mess,$anio)=explode("-",$fecha_fact);
	// 	$info_factura.=$esp_init2.$diaa."       ".$mess."           ".$anio."|";
	// 	$info_factura.=$esp_init."FACTURA CONSUMIDOR # ".$num_fact."|";
	// 	//Datos del cliente
	// 	$info_factura.=$esp_init."   ".$nombre_ape."|";
	// 	$info_factura.=$esp_init.$direccion."|";
	// 	$info_factura.=$esp_init.$dui."|";
	// 	$info_factura.=$esp_init.$nit."|";
	// 	//Obtener informacion de tabla detalle_factura y producto o servicio
	// 	$sql_fact_det="SELECT  productos.id_producto, productos.descripcion, productos.exento,detalle_factura.*
	// 	FROM detalle_factura JOIN producto ON detalle_factura.id_producto=productos.id_producto
	// 	WHERE  detalle_factura.id_factura='$id_factura'
	// 	";
 //
	// 	$result_fact_det=_query($sql_fact_det);
	// 	$nrows_fact_det=_num_rows($result_fact_det);
	// 	$total_final=0;
	// 	$lineas=6;
	// 	$cuantos=0;
	// 	$subt_exento=0;
	// 	$subt_gravado=0;
	// 	$total_exento=0;
	// 	$total_gravado=0;
	// 	$info_factura.="\n";
	// 	$info_factura.="\n";
	// 	$info_factura.= chr(27).chr(51)."2"; //espacio entre lineas 6 x pulgada
 //
	// 	for($i=0;$i<$nrows_fact_det;$i++){
	// 		$row_fact_det=_fetch_array($result_fact_det);
	// 		$id_producto =$row_fact_det['id_producto'];
	// 		$descripcion =$row_fact_det['descripcion'];
	// 		$exento=$row_fact_det['exento'];
	// 		$id_detalle_factura =$row_fact_det['id_detalle_factura'];
	// 		$id_producto =$row_fact_det['id_producto'];
	// 		$cantidad =$row_fact_det['cantidad'];
	// 		$precio_venta =$row_fact_det['precio_venta'];
	// 		$subt =$row_fact_det['subtotal'];
	// 		$id_empleado =$row_fact_det['id_empleado'];
	// 		$tipo_prod_serv ='PRODUCTO';
 //
	// 		//linea por linea de productos
	// 		$descrip=texto_espacios($descripcion,42);
	// 		$subt=$precio_venta*$cantidad;
	// 		$subt_sin_iva=$precio_venta*$cantidad;
	// 		$subt_sin_iva_print=sprintf("%.2f",$subt_sin_iva);
	// 		$precio_unit=sprintf("%.2f",$precio_venta);
	// 		$subtotal=sprintf("%.2f",$subt);
	// 		$total_final=$total_final+$subtotal;
	// 		if ($exento==0){
	// 			$e_g="G";
	// 			$subt_gravado=sprintf("%.2f",$subtotal);
	// 			$total_gravado=$subtotal+$total_gravado;
	// 		}
	// 		else{
	// 			$e_g="E";
	// 			$subt_exento=sprintf("%.2f",$subtotal);
	// 			$total_exento=$subtotal+$total_exento;
	// 		}
 //
 //      $col2=2;
	// 		$sp1=len_espacios($cantidad,7);
	// 		$esp_col1=espacios_izq(" ",$sp1);
	// 		$sp2=len_espacios($precio_sin_iva_print,8);
	// 		$esp_col2=espacios_izq(" ",$sp2+4);
	// 		$sp3=len_espacios($subt_sin_iva_print,10);
	// 		$esp_col3=espacios_izq(" ",$sp3+1);
	// 		$esp_desc=espacios_izq(" ",6);
 //  		if ($exento==1){
	// 			$info_factura.=$esp_col1.$cantidad.$esp_desc.$descrip.$esp_col2."".$precio_unit.$esp_col3.$subtotal."\n";
 //  			}
 //  			if ($exento==0){
	// 				$sp3=$sp3+11;
	// 				$esp_col3=espacios_izq(" ",$sp3);
 //  				$info_factura.=$esp_col1.$cantidad.$esp_desc.$descrip.$esp_col2.$precio_unit.$esp_col3.$subtotal."\n";
	// 			}
	// 		$cuantos=$cuantos+1;
	// 	}
	// }
	// $total_final_format=sprintf("%.2f",$total_final);
	// list($entero,$decimal)=explode('.',$total_final_format);
	// $enteros_txt=num2letras($entero);
	// if(strlen($decimal)==1){
	// 	$decimales_txt=$decimal."0";
	// }
	// else{
	// 	$decimales_txt=$decimal;
	// }
 //
	// $cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
	// $esp=espacios_izq(" ",7);
	// $total_value=sprintf("%.2f",$total);
	// $sp3=10;
	// $total_fin=$total_exento+$total_gravado;
	// $total_value_exento=sprintf("%.2f",$total_exento);
	// $total_value_gravado=sprintf("%.2f",$total_gravado);
	// $total_value_fin=sprintf("%.2f",$total_fin);
 //
	// //totales
	// $lineas_faltantes=12-$cuantos;
	// $imprimir="";
	// for($j=0;$j<$lineas_faltantes;$j++){
	// 	$info_factura.="\n";
	// }
 //  $info_factura.= chr(27).chr(50); //espacio entre lineas 6 x pulgada
 //
	// $esp_init2=espacios_izq(" ",25);
	// $esp_totales=espacios_izq(" ",40);
	// //generar 2 lineas del texto del total de la factura
	// $total_txt0 =cadenaenlineas($cadena_salida_txt, 40,2);
	// $concepto_print="";
	// $tmplinea = array();
	// $ln=0;
	// $esp_init=espacios_izq(" ",2);
 //
	// foreach($total_txt0 as $total_txt1){
	// 	$tmplinea[]=$total_txt1;
	// 	$ln=$ln+1;
	// }
	// $esp_totales=espacios_izq(" ",56);
 //  $splentot1=len_espacios($total_value_exento,8);
	// $esp_lentot1=espacios_izq(" ",$splentot1);
	// $splentot2=len_espacios($total_value_gravado,12);
	// $esp_lentot2=espacios_izq(" ",$splentot2);
 //
 //  //imprimir totales
 //
	// $linea0=strlen(trim($tmplinea[0]));
	// $len_desc=72-$linea0;
	// $esp_totales=espacios_izq(" ",$len_desc);
	// $esp_init=espacios_izq(" ",10);
	// $info_factura.="\n";
	// //$info_factura.="\n";
	// $info_factura.=$esp_init.$tmplinea[0].$esp_totales."  ".$esp_lentot2.$total_value_gravado."\n";
	// if($ln>1){
	// 			$esp_init=espacios_izq(" ",6);
	// 					$len_desc=76-strlen(trim($tmplinea[1]));
	// 					$esp_totales=espacios_izq(" ",$len_desc);
	// 					$info_factura.=$esp_init.$tmplinea[1].$esp_totales.$esp_lentot2." "."\n";
	// 					for($x=0;$x<2;$x++){
	// 					 $info_factura.="\n";
	// 				 }
	// }
	// else{
	// for($x=0;$x<3;$x++){
	//  $info_factura.="\n";
 // }
 // }
	// $esp_totales_g=espacios_izq(" ",83);
 //
 //  $info_factura.=$esp_totales_g."  ".$esp_lentot2.$total_value_gravado."\n";
 //
	// $esp_totales=espacios_izq(" ",83);
	// for($x=0;$x<2;$x++){
	//  $info_factura.="\n";
 // }
	// $info_factura.=$esp_totales.$esp_lentot2.$total_final_format."\n";
	// // retornar valor generado en funcion
	// return ($info_factura);
	$id_sucursal=$_SESSION['id_sucursal'];
	$id_factura=$id_factura;
	$tipo_id=$tipo_id;
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';
	//Empresa
	$sql_empresa = "SELECT * FROM empresa";
	$result_empresa=_query($sql_empresa);
	$row_empresa=_fetch_array($result_empresa);
	$empresa=$row_empresa['nombre'];
	$razonsocial=$row_empresa['razonsocial'];
	$giro_empresa=$row_empresa['giro'];
	$iva=$row_empresa['iva']/100;
	//Sucursal
	$sql_sucursal=_query("SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'");
	$array_sucursal=_fetch_array($sql_sucursal);
	$nombre_sucursal=$array_sucursal['descripcion'];
	$nombre_sucursal1=texto_espacios($nombre_sucursal,30);
	$empresa1=texto_espacios($empresa,30);
	$razonsocial1=texto_espacios($razonsocial,30);
	$giro1=texto_espacios($giro_empresa,30);
	//inicio datos
	$info_factura="";
	$info_factura.=$empresa1."|".$nombre_sucursal1."|".$razonsocial1."|".$giro1."|";
	//Obtener informacion de tabla Factura
	$sql_fact="SELECT * FROM factura WHERE id_factura='$id_factura'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact>0){
		$id_cliente=$row_fact['id_cliente'];
		$id_factura = $row_fact['id_factura'];
		$id_usuario=$row_fact['id_usuario'];
		$fecha=$row_fact['fecha'];
		$fecha_fact=ed($fecha);
		$numero_doc=trim($row_fact['numero_doc']);
		$total=$row_fact['total'];

		$len_numero_doc=strlen($numero_doc)-4;
		$num_fact=substr($numero_doc,0,$len_numero_doc);
		$tipo_fact=substr($numero_doc,$len_numero_doc,4);
		$numfact=espacios_izq($num_fact,10);
		//Datos de empleado
		$sql_user="select * from usuario where id_usuario='$id_usuario'";
		$result_user= _query($sql_user);
		$row_user=_fetch_array($result_user);
		$nrow_user=_num_rows($result_user);
		$usuario=$row_user['usuario'];
		$nombreusuario=$row_user['nombre'];
		//$nombres=$row_user['apellido']." ".$row_user['nombre'];
		//Datos del Cliente
		$sql="select * from clientes where id_cliente='$id_cliente'";
		$result=_query($sql);
		$count=_num_rows($result);
		if ($count > 0) {
				for ($i = 0; $i < $count; $i ++) {
						$row1 = _fetch_array($result);
						//$id_cliente=$row1["id_cliente"];
						$nombre=$row1["nombre"];
						$nombreapecte=$row1["nombre"];
						$nit=$row1["nit"];
						$nrc=$row1["nrc"];
						$dui=$row1["dui"];
						$direccion=$row1["direccion"];
						$telefono1=$row1["telefono1"];
						$giro_cte=substr($row1["giro"], 0, 22);
						$email=$row1["email"];
						$nombres=$row1['nombre'];
				}
		}
		//Columnas y posiciones base
		$base1=7;
		$col0=1;
		$col1=4;
		$col2=3;
		$col3=13;
		$col4=5;
		$sp1=2;
		$sp_prec=15;
		$sp=espacios_izq(" ",$sp1);
		$sp2=espacios_izq(" ",12);
		$esp_init=espacios_izq(" ",11);
		$esp_init1=espacios_izq(" ",14);
		$esp_precios=espacios_izq(" ",$sp_prec);
		$esp_enc2=espacios_izq(" ",3);
		$esp_init2=espacios_izq(" ",72);
		$nombre_ape=texto_espacios($nombres,65);
		$dir_txt=texto_espacios($direccion,45);
		$total_final=0;
		$imprimir="";

		//Datos encabezado factura
		$info_factura.= chr(27).chr(50); //espacio entre lineas 6 x pulgada
		//$info_factura.= chr(27).chr(51)."1"; //espacio entre lineas 6 x pulgada

		$imprimir="";
		for($s=0;$s<8;$s++){
			$imprimir.="\n";
		}
		$info_factura=$imprimir;
		list($diaa,$mess,$anio)=explode("-",$fecha_fact);
		$esp_init2=espacios_izq(" ",58);
		$info_factura.=$esp_init2.$diaa."   /   ".$mess."   /   ".$anio."||";
		$esp_init2=espacios_izq(" ",62);
		$info_factura.=$esp_init.$nombre_ape."|";
		$info_factura.="\n";
		//$info_factura.=$esp_init1.$dir_txt."|";
		//NRC
		$esp_init2=espacios_izq(" ",52);
		$info_factura.=$esp_init2.$nitcte."|";
		//NIT
		$esp_init2=espacios_izq(" ",52);
		$info_factura.=$esp_init2.$nrccte."|";
		$info_factura.="\n";
		//GIRO
		$esp_init2=espacios_izq(" ",49);
		$info_factura.=$esp_init2." ".$giro_cte."|";

		$info_factura.="\n";
		$info_factura.="\n";
		$info_factura.="\n";

		// $info_factura.= chr(27).chr(51)."1"; //espacio entre lineas 6 x pulgada
		// Obtener informacion de tabla detalle_factura y producto
		$sql_fact_det="SELECT  factura_detalle.*
		FROM factura_detalle
		WHERE id_factura='$id_factura'
		";

		$result_fact_det=_query($sql_fact_det);
		$nrows_fact_det=_num_rows($result_fact_det);
		$total_final=0;
		$lineas=6;
		$cuantos=0;
		$subt_exento=0;
		$subt_gravado=0;
		$total_exento=0;
		$total_gravado=0;

		for($i=0;$i<$nrows_fact_det;$i++)
		{
			$row_fact_det=_fetch_array($result_fact_det);
			$id_prod_serv =$row_fact_det['id_prod_serv'];
			//$descripcion =$row_fact_det['descripcion'];
			//$exento=$row_fact_det['exento'];
			$id_detalle_factura =$row_fact_det['id_factura_detalle'];
			//$id_producto =$row_fact_det['id_producto'];
			$cantidad =$row_fact_det['cantidad'];
			$precio_venta =$row_fact_det['precio_venta'];
			$subt =$row_fact_det['subtotal'];
			$id_empleado =$row_fact_det['id_empleado'];
			$tipo_prod_serv =$row_fact_det['tipo_prod_serv'];
			if ($tipo_prod_serv == "PRODUCTO")
			{
				$sql_pro = _query("SELECT * FROM productos WHERE id_producto = '$id_prod_serv'");
				$row_pro = _fetch_array($sql_pro);
				$descripcion = $row_pro["descripcion"];
				$exento = 0;
			}
			if ($tipo_prod_serv == "SERVICIO")
			{
				$sql_ser = _query("SELECT * FROM servicios WHERE id_servicio = '$id_prod_serv'");
				$row_ser = _fetch_array($sql_ser);
				$descripcion = $row_ser["descripcion"];
				$exento = 0;
			}
			//linea a linea
			//$descrip=texto_espacios($descripcion,35);
			$descrip = cadenaenlineas($descripcion, 37,2);
			$subt=$precio_venta*$cantidad;

			$tmplinea1 = array();
			$ln1=0;
			foreach($descrip as $des)
			{
				$tmplinea1[]=$des;
				$ln1=$ln1+1;
			}


			$precio_unit=sprintf("%.2f",$precio_venta);
			$subtotal=sprintf("%.2f",$subt);
			$total_final=$total_final+$subtotal;
			if ($exento==0){
				$e_g="G";
				$precio_sin_iva0 =$row_fact_det['precio_venta'];
				$precio_sin_iva =round($row_fact_det['precio_venta'],2);
				$subt_sin_iva=$precio_sin_iva0*$cantidad;
				$subt_gravado=sprintf("%.2f",$subt_sin_iva);
				$total_gravado=$subt_sin_iva+$total_gravado;
			}
			else{
				$e_g="E";
				$precio_sin_iva =round($row_fact_det['precio_venta'],2);
				$precio_sin_iva0 =$row_fact_det['precio_venta'];
				$subt_sin_iva=$precio_sin_iva0*$cantidad;
				$subt_exento=sprintf("%.2f",$subt_sin_iva);
				$total_exento=$subt_sin_iva+$total_exento;

			}
      $precio_sin_iva_print=sprintf("%.2f",$precio_sin_iva);

			$subt_sin_iva_print=sprintf("%.2f",$subt_sin_iva);
      $col2=2;
			$xp0=str_pad("", 5, " ", STR_PAD_RIGHT);
			$xp1=str_pad($cantidad, 5, " ", STR_PAD_RIGHT);
		  $sp1=len_espacios($cantidad,8);
			$esp_col1=espacios_izq(" ",$sp1);
			$sp2=len_espacios($precio_sin_iva_print,9);
			$xp3=str_pad($precio_sin_iva_print, 7, " ", STR_PAD_LEFT);
		  $esp_col2=espacios_izq(" ",$sp2);
			$sp3=len_espacios($subt_sin_iva_print,6);
			$xp4=str_pad($subt_sin_iva_print, 21, " ", STR_PAD_LEFT);
			$esp_col3=espacios_izq(" ",$sp3);
			$esp_desc=espacios_izq(" ",4);
			if ($exento==1){
				$info_factura.=$esp_col1.$cantidad.$esp_desc.$descrip.$esp_col2.$precio_sin_iva_print.$esp_col3.$subt_sin_iva_print."\n";
			}
			if ($exento==0)
			{
				$sp3=$sp3+13;
				$esp_col3=espacios_izq(" ",$sp3);
				//$info_factura.=$esp_col1.$cantidad.$esp_desc.$descrip.$esp_col2.$precio_sin_iva_print.$esp_col3."".$subt_sin_iva_print."\n";
				for ($ik=0; $ik < $ln1 ; $ik++)
				{
					$lista = $tmplinea1[$ik];
					$xp2=str_pad($lista, 45, " ", STR_PAD_RIGHT);
					$xp5 = str_pad("", 5, " ", STR_PAD_RIGHT);
					$xp6 = str_pad("", 8, " ", STR_PAD_LEFT);
					$xp7 = str_pad("", 7, " ", STR_PAD_LEFT);
					if($ik == 0)
					{
						$info_factura.=$xp0.$xp1.$xp2.$xp3.$xp4."\n";
					}
					else {
						$info_factura.=$xp0.$xp5.$xp2.$xp6.$xp7."\n";
					}
				}
			}
			$cuantos=$cuantos+1+($ln1-1);
		}
	}
	$calc_iva=round($iva*$total_gravado,2);
	$total_iva_format=sprintf("%.2f",$calc_iva);
	$total_final_format=sprintf("%.2f",$total_final);
	list($entero,$decimal)=explode('.',$total_final_format);
	$enteros_txt=num2letras($entero);
	if($entero=='100' && $decimal=='00'){
		$enteros_txt="CIEN";
	}
	if(strlen($decimal)==1){
		$decimales_txt=$decimal."0";
	}
	else{
		$decimales_txt=$decimal;
	}

	$cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
	$esp=espacios_izq(" ",7);
	$total_value=sprintf("%.2f",$total);
	$sp3=10;
	$total_fin=$total_exento+$total_gravado;
	$total_value_exento=sprintf("%.2f",$total_exento);
	$total_value_gravado=sprintf("%.2f",$total_gravado);
	$total_value_fin=sprintf("%.2f",$total_fin);
	//totales y n lineas
	$lineas_faltantes=21-$cuantos;
	$imprimir="";
	for($j=0;$j<$lineas_faltantes;$j++){
		$imprimir.="\n";
	}
	$info_factura.=$imprimir;

	$info_factura.= chr(27).chr(50); //espacio entre lineas 6 x pulgada

	$esp_init2=espacios_izq(" ",30);
	$esp_totales=espacios_izq(" ",35);
	//generar 2 lineas del texto del total de la factura
	$total_txt0 =cadenaenlineas($cadena_salida_txt, 35,2);
	$concepto_print="";
	$tmplinea = array();
	$ln=0;
	foreach($total_txt0 as $total_txt1){
		$tmplinea[]=$total_txt1;
		$ln=$ln+1;
	}
	//$info_factura.="\n";
	$esp_init=espacios_izq(" ",5);
	$esp_initx=espacios_izq(" ",9);
	$subtotal_gravado=round($total_gravado,2);
	$subtotal_exento=$total_exento;
	$total_final_todos=round($subtotal_exento+$subtotal_gravado,2);
	//$info_factura.=chr(27).chr(50);
	$esp_totales=espacios_izq(" ",47);
	$splentot1=len_espacios($total_value_exento,10);
	$esp_lentot1=espacios_izq(" ",$splentot1+5);
	$splentot2=len_espacios($total_value_gravado,10);
	$esp_lentot2=espacios_izq(" ",$splentot2+3);

  //$info_factura.="\n";
	$info_factura.="\n";

  $splentot_iva=len_espacios($total_iva_format,10);
  $esp_tot_iva=espacios_izq(" ",$splentot_iva);
	$len_desc=55-strlen(trim($tmplinea[0]));
	$esp_totales=espacios_izq(" ",$len_desc);
	$a=str_pad($total_value_gravado,18," ",STR_PAD_LEFT);
	$info_factura.=$esp_initx.$tmplinea[0].$esp_totales.$a."\n";
	//$info_factura.="\n";

	$subtotal_gravado_print=sprintf("%.2f",$subtotal_gravado);
	if($ln>1){
		$len_desc=57-strlen(trim($tmplinea[1]));
		$b=str_pad($total_iva_format,18," ",STR_PAD_LEFT);
		$esp_totales=espacios_izq(" ",$len_desc);
		$info_factura.=$esp_init.$tmplinea[1].$esp_totales.""."\n";
	}
	else{
		$esp_totales=espacios_izq(" ",67);
		$c=str_pad($total_value_gravado,19," ",STR_PAD_LEFT);
		$info_factura.=$esp_totales.""."\n";
	}
	$esp_totales=espacios_izq(" ",60);

	  $splentot_st=len_espacios($subtotal_gravado_print,10);
	  $esp_subt=espacios_izq(" ",$splentot_st);
	$c=str_pad($total_value_gravado,22," ",STR_PAD_LEFT);
	$info_factura.= $esp_totales.$c;
	for($k=0;$k<4;$k++){
		$info_factura.="\n";
	}

	$total_final_todoss=sprintf("%.2f",$total_final_todos);
	$esp_tot_fin=espacios_izq(" ",66);
	$d=str_pad($total_final_todoss,16," ",STR_PAD_LEFT);
	$info_factura.=$esp_tot_fin.$d;
	$info_factura.="|".$d;
	// retornar valor generado en funcion
	return ($info_factura);

}
function print_fact_dia($id_fact,$tipo_id){
	$id_sucursal=$_SESSION['id_sucursal'];
	$id_factura=$id_fact;
	$tipo_id=$tipo_id;
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';
	//Empresa
	$sql_empresa = "SELECT * FROM empresa";
	$result_empresa=_query($sql_empresa);
	$row_empresa=_fetch_array($result_empresa);
	$empresa=$row_empresa['nombre'];
	$razonsocial=$row_empresa['razonsocial'];
	$giro=$row_empresa['giro'];
	//Sucursal
	$sql_sucursal=_query("SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'");
	$array_sucursal=_fetch_array($sql_sucursal);
	$nombre_sucursal=$array_sucursal['descripcion'];
	$nombre_sucursal1=texto_espacios($nombre_sucursal,30);
	$empresa1=texto_espacios($empresa,30);
	$razonsocial1=texto_espacios($razonsocial,30);
	$giro1=texto_espacios($giro,30);
	//inicio datos
	$info_factura="";
	$info_factura.=$empresa1."|".$nombre_sucursal1."|".$razonsocial1."|".$giro1."|";

	$sql_fact="SELECT * FROM factura_dia WHERE id_factura_dia='$id_factura'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact>0){
		$id_cliente=$row_fact['id_cliente'];
		$id_factura = $row_fact['id_factura_dia'];
		$fecha=$row_fact['fecha'];
		$fecha_fact=ed($fecha);
		$total=$row_fact['total'];
		$num_fact=$id_factura;
		$numfact=espacios_izq($num_fact,10);

		//Datos del Cliente
		$sql="select * from cliente where id_cliente='$id_cliente'";
		$result= _query($sql);
		$row1=_fetch_array($result);
		$nrow1=_num_rows($result);
		$nombres=$row1['apellido']." ".$row1['nombre'];
		$dui=$row1['dui'];
		$nit=$row1['nit'];
		$direccion=$row1['direccion'];

		//Columnas y posiciones base
		$base1=7;
		$col0=1;
		$col1=4;
		$col2=3;
		$col3=13;
		$col4=5;
		$sp1=2;
		$sp_prec=15;
		$sp=espacios_izq(" ",$sp1);
		$sp2=espacios_izq(" ",12);
		$esp_init=espacios_izq(" ",12);
		$esp_precios=espacios_izq(" ",$sp_prec);
		$esp_enc2=espacios_izq(" ",3);
		$esp_init2=espacios_izq(" ",70);
		$nombre_ape=texto_espacios($nombres,32);
		$dir_txt=texto_espacios($direccion,30);
		$total_final=0;
		$imprimir="";
		for($h=0;$h<8;$h++){
			$imprimir.="\n";
		}
		$info_factura.=$imprimir;
		//Datos encabezado factura
		list($diaa,$mess,$anio)=explode("-",$fecha_fact);
		$info_factura.=$esp_init2.$diaa."       ".$mess."           ".$anio."|";
		$info_factura.=$esp_init."FACTURA CONSUMIDOR DIARIA# ".$num_fact."|";
		//Datos del cliente
		$info_factura.=$esp_init."   ".$nombre_ape."|";
		$info_factura.=$esp_init.$direccion."|";
		$info_factura.=$esp_init.$dui."|";
		$info_factura.=$esp_init.$nit."|";
		//Obtener informacion de tabla detalle_factura y producto o servicio
		$sql_fact_det="SELECT  productos.id_producto, productos.descripcion, productos.exento,detalle_factura_dia.*
		FROM detalle_factura_dia JOIN producto ON detalle_factura_dia.id_producto=productos.id_producto
		WHERE  detalle_factura_dia.id_factura_dia='$id_factura'
		";

		$result_fact_det=_query($sql_fact_det);
		$nrows_fact_det=_num_rows($result_fact_det);
		$total_final=0;
		$lineas=6;
		$cuantos=0;
		$subt_exento=0;
		$subt_gravado=0;
		$total_exento=0;
		$total_gravado=0;

		$info_factura.="\n";
		//$info_factura.="\n";
		$info_factura.= chr(27).chr(51)."2"; //espacio entre lineas 6 x pulgada
		//$info_factura.="\n";
		for($i=0;$i<$nrows_fact_det;$i++){
			$row_fact_det=_fetch_array($result_fact_det);
			$id_producto =$row_fact_det['id_producto'];
			$descripcion =$row_fact_det['descripcion'];
			$exento=$row_fact_det['exento'];
			$id_detalle_factura =$row_fact_det['id_factdet_dia'];
			$id_producto =$row_fact_det['id_producto'];
			$cantidad =$row_fact_det['cantidad'];
			$precio_venta =$row_fact_det['precio_venta'];
			$subt =$row_fact_det['subtotal'];

			//linea por linea de productos
			$descrip=texto_espacios($descripcion,42);
			$subt=$precio_venta*$cantidad;
			$subt_sin_iva=$precio_venta*$cantidad;
			$subt_sin_iva_print=sprintf("%.2f",$subt_sin_iva);
			$precio_unit=sprintf("%.2f",$precio_venta);
			$subtotal=sprintf("%.2f",$subt);
			$total_final=$total_final+$subtotal;
			if ($exento==0){
				$e_g="G";
				$subt_gravado=sprintf("%.2f",$subtotal);
				$total_gravado=$subtotal+$total_gravado;
			}
			else{
				$e_g="E";
				$subt_exento=sprintf("%.2f",$subtotal);
				$total_exento=$subtotal+$total_exento;
			}

      $col2=2;
			$sp1=len_espacios($cantidad,7);
			$esp_col1=espacios_izq(" ",$sp1);
			$sp2=len_espacios($precio_sin_iva_print,8);
			$esp_col2=espacios_izq(" ",$sp2+4);
			$sp3=len_espacios($subt_sin_iva_print,10);
			$esp_col3=espacios_izq(" ",$sp3+1);
			$esp_desc=espacios_izq(" ",6);
  		if ($exento==1){
				$info_factura.=$esp_col1.$cantidad.$esp_desc.$descrip.$esp_col2."".$precio_unit.$esp_col3.$subtotal."\n";
  			}
  			if ($exento==0){
					$sp3=$sp3+11;
					$esp_col3=espacios_izq(" ",$sp3);
  				$info_factura.=$esp_col1.$cantidad.$esp_desc.$descrip.$esp_col2.$precio_unit.$esp_col3.$subtotal."\n";
				}
			$cuantos=$cuantos+1;
		}
	}
	$total_final_format=sprintf("%.2f",$total_final);
	list($entero,$decimal)=explode('.',$total_final_format);
	$enteros_txt=num2letras($entero);
	if(strlen($decimal)==1){
		$decimales_txt=$decimal."0";
	}
	else{
		$decimales_txt=$decimal;
	}

	$cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
	$esp=espacios_izq(" ",7);
	$total_value=sprintf("%.2f",$total);
	$sp3=10;
	$total_fin=$total_exento+$total_gravado;
	$total_value_exento=sprintf("%.2f",$total_exento);
	$total_value_gravado=sprintf("%.2f",$total_gravado);
	$total_value_fin=sprintf("%.2f",$total_fin);

	//totales
	$lineas_faltantes=12-$cuantos;
	$imprimir="";
	for($j=0;$j<$lineas_faltantes;$j++){
		$info_factura.="\n";
	}
  $info_factura.= chr(27).chr(50); //espacio entre lineas 6 x pulgada

	$esp_init2=espacios_izq(" ",25);
	$esp_totales=espacios_izq(" ",40);
	//generar 2 lineas del texto del total de la factura
	$total_txt0 =cadenaenlineas($cadena_salida_txt, 40,2);
	$concepto_print="";
	$tmplinea = array();
	$ln=0;
	$esp_init=espacios_izq(" ",2);

	foreach($total_txt0 as $total_txt1){
		$tmplinea[]=$total_txt1;
		$ln=$ln+1;
	}
	$esp_totales=espacios_izq(" ",56);
  $splentot1=len_espacios($total_value_exento,8);
	$esp_lentot1=espacios_izq(" ",$splentot1);
	$splentot2=len_espacios($total_value_gravado,12);
	$esp_lentot2=espacios_izq(" ",$splentot2);

  //imprimir totales

	$linea0=strlen(trim($tmplinea[0]));
	$len_desc=72-$linea0;
	$esp_totales=espacios_izq(" ",$len_desc);
	$esp_init=espacios_izq(" ",10);
	$info_factura.="\n";
	$info_factura.="\n";
	$info_factura.=$esp_init.$tmplinea[0].$esp_totales."  ".$esp_lentot2.$total_value_gravado."\n";
	if($ln>1){
				$esp_init=espacios_izq(" ",6);
						$len_desc=76-strlen(trim($tmplinea[1]));
						$esp_totales=espacios_izq(" ",$len_desc);
						$info_factura.=$esp_init.$tmplinea[1].$esp_totales.$esp_lentot2." "."\n";
						for($x=0;$x<2;$x++){
						 $info_factura.="\n";
					 }
	}
	else{
	for($x=0;$x<3;$x++){
	 $info_factura.="\n";
 }
 }
	$esp_totales_g=espacios_izq(" ",83);

  $info_factura.=$esp_totales_g."  ".$esp_lentot2.$total_value_gravado."\n";

	$esp_totales=espacios_izq(" ",83);
	for($x=0;$x<2;$x++){
	 $info_factura.="\n";
 }
	$info_factura.=$esp_totales.$esp_lentot2.$total_final_format."\n";
	// retornar valor generado en funcion
	return ($info_factura);

}

function print_vale($id_movimiento){
  $id_sucursal=$_SESSION['id_sucursal'];
	//sucursal
	$sql_sucursal=_query("SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'");
	$array_sucursal=_fetch_array($sql_sucursal);
	$nombre_sucursal1=$array_sucursal['descripcion'];
	//$nombre_sucursal1=texto_espacios($nombre_sucursal,30);
	//consulta
	$sql="SELECT  e.id_empleado, e.nombre,
	mc.concepto, mc.valor,mc.fecha,mc.hora,mc.entrada,mc.salida,mc.id_sucursal
	FROM mov_caja AS mc
	JOIN empleados AS e ON(e.id_empleado=mc.id_empleado)
	WHERE  mc.id_movimiento='$id_movimiento'";
	$result=_query($sql);
	$nrow = _num_rows($result);
	$row = _fetch_array($result);
	$id_empleado = $row["id_empleado"];
	$concepto = $row["concepto"];
	$nombre = $row["nombre"];
	$hora= $row["hora"];
	$fecha= $row["fecha"];
	$valor= $row["valor"];
  $entrada= $row["entrada"];
	//$id_sucursal=$row["id_sucursal"];
	if($entrada==1){
		$tipo="INGRESO";
	}
	else{
		$tipo="EGRESO";
	}
	$line1=str_repeat("_",30)."\n";
  $valor= sprintf('%.2f', $valor);
	//Datos
	$col0=1;		$col1=3; 		$col2=3;
	$col3=6;		$col4=5;		$sp1=2;
	$sp_prec=10;
	$sp=espacios_izq(" ",$sp1);
	$sp2=espacios_izq(" ",12);
	$esp_init=espacios_izq(" ",$col0);
	$esp_precios=espacios_izq(" ",$sp_prec);
	$esp_enc2=espacios_izq(" ",3);
	$esp_init2=espacios_izq(" ",23);
	$info_factura="";
	$info_factura.=$esp_init."CALZADO MAYORGA "."\n";
	$info_factura.=$esp_init."SUCURSAL ".$nombre_sucursal1."\n";
	$info_factura.=$esp_init."VALE # : ".$id_movimiento."\n";
	$info_factura.=$esp_init."FECHA: ".$fecha."    HORA:".$hora."\n";
	$info_factura.=$esp_init."EMPLEADO: ".$nombre."\n";
	$info_factura.=$esp_init.$tipo."\n";
	$info_factura.=$esp_init."CONCEPTO: ".$concepto."\n";
	$info_factura.=$esp_init."VALOR $: ".$valor."\n";
	$info_factura.="\n";
	$info_factura.="\n";
	$info_factura.="F. ".$line1;


	return ($info_factura);

}

function print_corte($id_corte){
	include_once "_core.php";
  $id_sucursal=$_SESSION['id_sucursal'];
	//sucursal
	$sql_sucursal=_query("SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'");
	$array_sucursal=_fetch_array($sql_sucursal);
	$nombre_sucursal=$array_sucursal['descripcion'];
	//consulta
	$sql="SELECT c.caja, c.turno, c.cajero, c.tinicio, c.tfinal, c.totalnot, c.texento, c.tgravado,
	c.totalt, c.finicio, c.ffinal, c.totalnof, c.fexento, c.fgravado, c.totalf, c.cfinicio, c.cffinal, c.totalnocf,
	c.cfexento, c.cfgravado, c.totalcf, c.rinicio, c.rfinal, c.totalnor, c.rexento, c.rgravado, c.totalr,
	 c.cashinicial, c.vtacontado, c.vtaefectivo, c.vtatcredito, c.totalgral, c.subtotal, c.cashfinal, c.diferencia,
	  c.totalnodev, c.totalnoanu, c.depositos, c.vales, c.tarjetas, c.depositon, c.valen, c.tarjetan, c.ingresos,
		 c.tcredito, c.ncortex, c.ncortez, c.ncortezm, c.cerrado, c.id_empleado, c.id_sucursal, c.id_apertura,
		 c.fecha_corte, c.hora_corte, c.tipo_corte,e.nombre
		 FROM controlcaja AS c
		 JOIN empleados AS e ON(e.id_empleado=c.id_empleado)
		 WHERE c.id_corte='$id_corte'";
	$result=_query($sql);
	$nrow = _num_rows($result);
	$row = _fetch_array($result);
	$id_empleado = $row["id_empleado"];
	$nombre_emp = $row["nombre"];
	$hora= $row["hora_corte"];
	$fecha= $row["fecha_corte"];
  $tipo= $row["tipo_corte"];
	$tinicio= $row["tinicio"];
	$tfinal= $row["tfinal"];
	$finicio= $row["finicio"];
	$ffinal= $row["ffinal"];
	$cfinicio= $row["cfinicio"];
	$cffinal= $row["cffinal"];
	$cashini= $row["cashinicial"];
	$vtaefectivo= $row["vtaefectivo"];
	$ingresos= $row["ingresos"];
	$vales= $row["vales"];
	$totalgral= $row["totalgral"];
	$cashfinal= $row["cashfinal"];
	$diferencia= $row["diferencia"];
	$totalnot= $row["totalnot"];
	$totalnof= $row["totalnof"];
	$totalnocf= $row["totalnocf"];

	$texento= sprintf('%.2f', $row["texento"]);
	$tgravado= sprintf('%.2f', $row["tgravado"]);
	$totalt=  sprintf('%.2f', $row["totalt"]);
	$fexento= sprintf('%.2f', $row["fexento"]);
	$fgravado=sprintf('%.2f',  $row["fgravado"]);
	$totalf= sprintf('%.2f', $row["totalf"]);
	$cfexento= sprintf('%.2f', $row["cfexento"]);
	$cfgravado=sprintf('%.2f',  $row["cfgravado"]);
	$totalcf=sprintf('%.2f',  $row["totalcf"]);


  $vtaefectivo= sprintf('%.2f', $vtaefectivo);
  $cashini= sprintf('%.2f', $cashini);
  $ingresos= sprintf('%.2f', $ingresos);

	$vales=sprintf('%.2f', $vales);
	$cashfinal= sprintf('%.2f', $cashfinal);
	$diferencia= sprintf('%.2f', $diferencia);
	$esp_init0=espacios_izq(" ",1);
	$esp_init1=espacios_izq(" ",12);
	$esp_init2=espacios_izq(" ",20);
	$line1=str_repeat("_",40)."\n";
	$info_factura="";
  $tinicio= zfill($tinicio, 7);
	$tfinal= zfill($tfinal, 7);
	if($tipo=="C"){
		$desc_tipo='CORTE DE CAJA';
	}
	else{
		$desc_tipo=$tipo;
	}

	$info_factura.=$esp_init0."CALZADO MAYORGA "."\n";
	$info_factura.=$esp_init0."SUCURSAL ".$nombre_sucursal."\n";
	$info_factura.=$esp_init0."CORTE TIPO: ".$desc_tipo."\n";
	$info_factura.=$esp_init0."CORTE DE CAJA # : ".$id_corte."\n";
	$info_factura.=$esp_init0."EMPLEADO: ".$nombre_emp."\n";
	$info_factura.=$esp_init0."FECHA: ".$fecha."    HORA:".$hora."|";
	$info_factura.="\n";
	if($tipo=="C"){
		$subtotal=$cashini+$vtaefectivo+$ingresos;
		$totalcaja=$subtotal+$vales;
		$subtotal=sprintf('%.2f', $subtotal);
    $totalcaja=sprintf('%.2f', $totalcaja);
		$info_factura.=$esp_init1."DESDE:      HASTA:"."\n";
		$info_factura.=$line1;
		$info_factura.=$esp_init0."TIQUETES: ".$tinicio."   ".$tfinal."\n";
		$info_factura.=$esp_init0."FACTURAS: ".$finicio."   ".$ffinal."\n";
		$info_factura.=$esp_init0."FISCALES: ".$cfinicio."   ".$cffinal."\n";
    $info_factura.="\n";

    $n=10;
		$sp1=len_num($cashini,$n);
		$info_factura.=$esp_init0."SALDO INICIAL $: ".$sp1.$cashini."\n";
		$sp1=len_num($vtaefectivo,$n);
		$info_factura.=$esp_init0."(+) VENTA $:     ".$sp1.$vtaefectivo."\n";
		$sp1=len_num($ingresos,$n);
    $info_factura.=$esp_init0."INGRESOS $:      ".$sp1.$ingresos."\n";
		$info_factura.=$line1;
		$sp1=len_num($subtotal,$n);
		$info_factura.=$esp_init0."SUBTOTAL $:      ".$sp1.$subtotal."\n";
		$sp1=len_num($vales,$n);
		$info_factura.=$esp_init0."(-) VALES $:     ".$sp1.$vales."\n";
		$info_factura.=$line1;
		$sp1=len_num($totalcaja,$n);
		$info_factura.=$esp_init0."TOTAL CAJA $:    ".$sp1.$totalcaja."\n";
		$info_factura.="\n";
		$sp1=len_num($cashfinal,$n);
		$info_factura.=$esp_init0."EFECTIVO $:      ".$sp1.$cashfinal."\n";
		$sp1=len_num($diferencia,$n);
		$info_factura.=$esp_init0."DIFERENCIA $:    ".$sp1.$diferencia."\n";
	}

	if($tipo=="X" || $tipo=="Z"){
		//listar devoluciones
    $sql_dev="SELECT id_devolucion, id_corte, n_devolucion, t_devolucion FROM devoluciones WHERE id_corte='$id_corte'";
		$result_dev =_query($sql_dev);
		$nrow_dev = _num_rows($result_dev);


		$subtotal=$cashini+$vtaefectivo+$ingresos;
		$totalcaja=$subtotal+$vales;
		$tot_exent=$texento+$fexento+$cfexento;
		$tot_grav=$tgravado+$fgravado+$cfgravado;
		$tot_fin=$totalt+$totalf+$totalcf;
    $tot_exent=sprintf('%.2f', $tot_exent);
		$tot_grav=sprintf('%.2f', $tot_grav);
		$tot_fin=sprintf('%.2f', $tot_fin);
		$subtotal=sprintf('%.2f', $subtotal);
    $totalcaja=sprintf('%.2f', $totalcaja);
		$info_factura.=$esp_init1."   EXEN.    GRAV.     TOTAL"."\n";
		$info_factura.=$line1;
		$n=5;
		$sp1=len_num($texento,$n);
		$sp2=len_num($tgravado,$n);
		$sp3=len_num($totalt,$n);
		$info_factura.=$esp_init0."TIQUETES: ".$sp1.$texento."".$sp2.$tgravado."".$sp3.$totalt."\n";
		$sp1=len_num($fexento,$n);
		$sp2=len_num($fgravado,$n);
		$sp3=len_num($totalf,$n);
		$info_factura.=$esp_init0."FACTURAS: ".$sp1.$fexento."".$sp2.$fgravado."".$sp3.$totalf."\n";
		$sp1=len_num($cfexento,$n);
		$sp2=len_num($cfgravado,$n);
		$sp3=len_num($totalcf,$n);
		$info_factura.=$esp_init0."FISCALES: ".$sp1.$cfexento."".$sp2.$cfgravado."".$sp3.$totalcf."\n";
		$info_factura.=$line1;
		$sp1=len_num($tot_exent,$n);
		$sp2=len_num($tot_grav,$n);
		$sp3=len_num($tot_fin,$n);

		$info_factura.=$esp_init0."TOTAL $ : ".$sp1.$tot_exent.$sp2.$tot_grav.$sp3.$tot_fin."\n";
    $info_factura.="\n";

		$info_factura.=$esp_init1."INICIO     FINAL     TOTAL"."\n";
		$info_factura.=$line1;
		$n=5;
		$total_docs=$totalnot+$totalnof+$totalnocf;
		$sp1=len_num($tinicio,$n);
		$sp2=len_num($tfinal,$n);
		$sp3=len_num($totalnot,$n);
		$info_factura.=$esp_init0."TIQUETES: ".$sp1.$tinicio.$sp2.$tfinal.$sp3.$totalnot."\n";
		$sp1=len_num($finicio,$n);
		$sp2=len_num($ffinal,$n);
		$sp3=len_num($totalnof,$n);
		$info_factura.=$esp_init0."FACTURAS: ".$sp1.$finicio.$sp2.$ffinal.$sp3.$totalnof."\n";
		$sp1=len_num($cfinicio,$n);
		$sp2=len_num($cffinal,$n);
		$sp3=len_num($totalnocf,$n);
		$info_factura.=$esp_init0."FISCALES: ".$sp1.$cfinicio.$sp2.$cffinal.$sp3.$totalnocf."\n";
		$info_factura.=$line1;
		$sp1=len_num($total_docs,24);
		$info_factura.=$esp_init0."TOTAL   :".$sp1.$total_docs."\n";
		$info_factura.="\n";

    if($nrow_dev>0){
			$info_factura.=$esp_init0."DEVOLUCIONES   :"."\n";
			$info_factura.=$esp_init0."  NUMERO   TOTAL"."\n";
			for($j=0;$j<$nrow_dev;$j++){

				$row_dev = _fetch_array($result_dev);
				$n_devolucion=$row_dev['n_devolucion'];
				$t_devolucion=$row_dev['t_devolucion'];
				$sp1=len_num($n_devolucion,$n);
				$sp2=len_num($t_devolucion,$n);
				$info_factura.=$esp_init0.$sp1.$n_devolucion.$sp2.$t_devolucion."\n";
				//$info_factura.=$esp_init0."TOTAL   :".$sp1.$total_docs."\n";
			}
			$info_factura.="\n";
		}
	}
	return ($info_factura);

}
function len_num($subtotal,$col3){
		//$col3=5;
	if(strlen($subtotal)<=4)
		$sp3=$col3;
	if(strlen($subtotal)==5)
		$sp3=$col3-1;
	if(strlen($subtotal)==6)
		$sp3=$col3-2;
	if(strlen($subtotal)==7)
		$sp3=$col3-3;
	if(strlen($subtotal)==8)
		$sp3=$col3-4;
	//if(strlen($subtotal)==9)
	//	$sp3=$col3-5;
	$esp_col3=espacios_izq(" ",$sp3);
	return $esp_col3;
}

function texto_espacios($texto,$long){
	$countchars=0;
	$countch=0;
	$texto=trim($texto);
	$len_txt=strlen($texto);
	$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','Ñ','Á','É','Í','Ó','Ú');
    foreach($latinchars as $value){
		$countchars=substr_count($texto,$value);
        $countch= $countchars+$countch;
    }

	if($len_txt<=$long){
	 if($countch>0)
		$n=($long+$countch)-$len_txt;
	 else
		$n=$long-$len_txt;

		$texto_repeat=str_repeat(" ",$n);
		$texto_salida=$texto.$texto_repeat;
	}
	else{
		$long=$long-1;
		$texto_salida=substr($texto,0,$long).".";
	}
	return $texto_salida;
}
function espacios_izq($texto,$long){
	$len_txt=strlen($texto);

	if($len_txt<=$long){

			$alinear='STR_PAD_LEFT';
	 $texto_salida=str_pad($texto, $long, " ",STR_PAD_LEFT );
	}
	else{
	$texto_salida=substr($texto,0,$long);
	}
	return $texto_salida;
}
function cadenaenlineas( $text, $width = '80', $lines = '10', $break = '\n', $cut = 0 ) {
      $wrappedarr = array();
      $wrappedtext = wordwrap( $text, $width, $break , true );
       $wrappedtext = trim( $wrappedtext );
      $arr = explode( $break, $wrappedtext );
     return $arr;
}
function len_espacios($valor,$col){
	$valor=strlen($valor);
	if($valor==1){
		$sp=$col;
	}
	else{
		$sp=$col-($valor-1);
	}
 return $sp;
}
function vendedor($id_vendedor){
	////Datos de empleados vendedor
 $sql_user2="select usuario, nombre from empleados where id_empleado='$id_vendedor'";
 $result_user2= _query($sql_user2);
 $row_user2=_fetch_array($result_user2);
 $nrow_user2=_num_rows($result_user2);
 $usuariovendedor=$row_user2['usuario'];
 $nombrevend=trim($row_user2['nombre']);
 $nombrevende=explode(" ",$nombrevend);
 $n_datos=count($nombrevende);
 $nombrevendedor="";
 switch ($n_datos) {
 case 1:
	 $nombrevendedor=$nombrevend;
	 break;
 case 2:
	 $nombrevendedor=$nombrevend;
	 break;
 case 3:
	 $nombrevendedor=$nombrevende[0]." ".$nombrevende[2];
	 break;
 case 4:
	 $nombrevendedor=$nombrevende[0]." ".$nombrevende[2];
	 break;
 case 5:
	 $nombrevendedor=$nombrevende[0]." ".$nombrevende[2];
	 break;
 default:
	 $nombrevendedor=$nombrevend;
	 break;
 }
 return $nombrevendedor;
}

function print_ccf_tml($id_fact,$tipo_id,$nitcte,$nrccte,$nombreapecte){
	$id_sucursal=$_SESSION['id_sucursal'];
	$id_factura=$id_fact;
	$tipo_id=$tipo_id;

	$info_factura="";
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';
	//Empresa
	$sql_empresa = "SELECT * FROM empresa";
	$result_empresa=_query($sql_empresa);
	$row_empresa=_fetch_array($result_empresa);
	$empresa=$row_empresa['nombre'];
	$razonsocial=$row_empresa['razonsocial'];
	$giro_empresa=$row_empresa['giro'];
	$iva=$row_empresa['iva']/100;
	//Sucursal
	$sql_sucursal=_query("SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'");
	$array_sucursal=_fetch_array($sql_sucursal);
	$nombre_sucursal=$array_sucursal['descripcion'];
	$nombre_sucursal1=texto_espacios($nombre_sucursal,30);
	$empresa1=texto_espacios($empresa,30);
	$razonsocial1=texto_espacios($razonsocial,30);
	$giro1=texto_espacios($giro_empresa,30);
	//inicio datos
	$info_factura="";

	/*incializamos el arreglo con las lineas vacias*/
	$logitud_array=58;
	$arrayL= array();
	for ($i=0; $i < $logitud_array; $i++) {
		// code...
		$arrayL[$i]=st("",100)."\n";
	}


	//Obtener informacion de tabla Factura
	$sql_fact="SELECT * FROM factura WHERE id_factura='$id_factura'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact>0){
		$id_cliente=$row_fact['id_cliente'];
		$id_factura = $row_fact['id_factura'];
		$id_usuario=$row_fact['id_usuario'];
		$fecha=$row_fact['fecha'];
		$fecha_fact=ed($fecha);
		$numero_doc=trim($row_fact['numero_doc']);
		$total=$row_fact['total'];

		$len_numero_doc=strlen($numero_doc)-4;
		$num_fact=substr($numero_doc,0,$len_numero_doc);
		$tipo_fact=substr($numero_doc,$len_numero_doc,4);
		$numfact=espacios_izq($num_fact,10);
		//Datos de empleado
		$sql_user="select * from usuario where id_usuario='$id_usuario'";
		$result_user= _query($sql_user);
		$row_user=_fetch_array($result_user);
		$nrow_user=_num_rows($result_user);
		$usuario=$row_user['usuario'];
		$nombreusuario=$row_user['nombre'];
		//$nombres=$row_user['apellido']." ".$row_user['nombre'];
		//Datos del Cliente
		$sql="SELECT clientes.*,departamento.nombre_departamento FROM clientes left JOIN departamento ON departamento.id_departamento=clientes.depto WHERE id_cliente='$id_cliente'";
		$result=_query($sql);
		$count=_num_rows($result);
		if ($count > 0) {
				for ($i = 0; $i < $count; $i ++) {
						$row1 = _fetch_array($result);
						//$id_cliente=$row1["id_cliente"];
						$nombre=$row1["nombre"];
						$nit=$row1["nit"];
						$nrc=$row1["nrc"];
						$dui=$row1["dui"];
						$direccion=$row1["direccion"];
						$telefono1=$row1["telefono1"];
						$giro_cte=$row1["giro"];
						$email=$row1["email"];
						$nombres=$row1['nombre'];
						$nombreapecte=$row1['nombre'];
						$retiene = $row1["retiene"];
						$retiene10 = $row1["retiene10"];
						$departamento = $row1['nombre_departamento'];
				}
		}
		$total_final=0;

		list($diaa,$mess,$anio)=explode("-",$fecha_fact);

		$arrayL[7]=  p_set($arrayL[7],$diaa,61,72,"B");
		$arrayL[7]=  p_set($arrayL[7],$mess,72,84,"B");
		$arrayL[7]=  p_set($arrayL[7],$anio,84,95,"B");

		$arrayL[9]=  p_set($arrayL[9],$nombreapecte,11,95,"R");

		$arrayL[11]=  p_set($arrayL[11],$direccion,12,95,"R");


		$arrayL[13]=  p_set($arrayL[13],$departamento,17,60,"R");
		$arrayL[13]=  p_set($arrayL[13],$nrccte,70,95,"R");

		$arrayL[14]=  p_set($arrayL[14],$giro_cte,9,95,"R");

		$arrayL[16]=  p_set($arrayL[16],$nitcte,64,95,"R");

		$sql_fact_det="SELECT  factura_detalle.*
		FROM factura_detalle
		WHERE id_factura='$id_factura'
		";

		$result_fact_det=_query($sql_fact_det);
		$nrows_fact_det=_num_rows($result_fact_det);
		$total_final=0;

		$subt_exento=0;
		$subt_gravado=0;
		$total_exento=0;
		$total_gravado=0;

		$array_painc = array(
			0 => 21,
			1 => 22,
			2 => 23,
			3 => 24,
			4 => 25,
			5 => 26,
			6 => 27,
			7 => 28,
			8 => 29,
			9 => 30,
			10 => 31,
			11 => 32,
			12 => 33,
			13 => 34,
			14 => 35,
			15 => 36,
			16 => 37,
			17 => 38,
			18 => 39,
			19 => 40,
			20 => 41,
			21 => 42,
			22 => 43,
			23 => 44,
		);
		$j=0;
		for($i=0;$i<$nrows_fact_det;$i++)
		{
			$row_fact_det=_fetch_array($result_fact_det);
			$id_prod_serv =$row_fact_det['id_prod_serv'];
			$id_detalle_factura =$row_fact_det['id_factura_detalle'];
			$cantidad =$row_fact_det['cantidad'];
			$precio_venta =$row_fact_det['precio_venta'];
			$subt =$row_fact_det['subtotal'];
			$id_empleado =$row_fact_det['id_empleado'];
			$tipo_prod_serv =$row_fact_det['tipo_prod_serv'];
			if ($tipo_prod_serv == "PRODUCTO")
			{
				$sql_pro = _query("SELECT * FROM productos WHERE id_producto = '$id_prod_serv'");
				$row_pro = _fetch_array($sql_pro);
				$descripcion = $row_pro["descripcion"];
				$exento = 0;
			}
			if ($tipo_prod_serv == "SERVICIO")
			{
				$sql_ser = _query("SELECT * FROM servicios WHERE id_servicio = '$id_prod_serv'");
				$row_ser = _fetch_array($sql_ser);
				$descripcion = $row_ser["descripcion"];
				$exento = 0;
			}

			$exento = 0;

			$subt=$precio_venta*$cantidad;


			$precio_unit=sprintf("%.4f",$precio_venta);
			$subtotal=sprintf("%.4f",$subt);
			$total_final=$total_final+$subtotal;
			if ($exento==0){
				$e_g="G";
				$precio_sin_iva0 =$row_fact_det['precio_venta'];
				$precio_sin_iva =round($row_fact_det['precio_venta'],4);
				$subt_sin_iva=$precio_sin_iva0*$cantidad;
				$subt_gravado=sprintf("%.4f",$subt_sin_iva);
				$total_gravado=$subt_sin_iva+$total_gravado;
			}
			else{
				$e_g="E";
				$precio_sin_iva =round($row_fact_det['precio_venta'],4);
				$precio_sin_iva0 =$row_fact_det['precio_venta'];
				$subt_sin_iva=$precio_sin_iva0*$cantidad;
				$subt_exento=sprintf("%.4f",$subt_sin_iva);
				$total_exento=$subt_sin_iva+$total_exento;

			}
      $precio_sin_iva_print=sprintf("%.4f",$precio_sin_iva);

			$subt_sin_iva_print=sprintf("%.4f",$subt_sin_iva);

			if ($j<24)
			{
				$array_nocon= dtl($descripcion,49);
				foreach ($array_nocon as $key => $value) {
					// code...
					switch ($key) {
						case '0':
							if ($exento==0){
								$arrayL[$array_painc[$j]]=p_set($arrayL[$array_painc[$j]],$cantidad,1,8,"B");
								$arrayL[$array_painc[$j]]=p_set($arrayL[$array_painc[$j]],quitar_spc($value),9,58,"R");
								$arrayL[$array_painc[$j]]=p_set($arrayL[$array_painc[$j]],$precio_sin_iva_print,59,71,"L");
								$arrayL[$array_painc[$j]]=p_set($arrayL[$array_painc[$j]],number_format(($subt_sin_iva_print),4,".",""),80,93,"L");
							}
				  		if ($exento==1){
								$arrayL[$array_painc[$j]]=p_set($arrayL[$array_painc[$j]],$cantidad,1,8,"B");
								$arrayL[$array_painc[$j]]=p_set($arrayL[$array_painc[$j]],quitar_spc($value),9,58,"R");
								$arrayL[$array_painc[$j]]=p_set($arrayL[$array_painc[$j]],$precio_venta,59,80,"L");
								$arrayL[$array_painc[$j]]=p_set($arrayL[$array_painc[$j]],number_format(($subt_exento),4,".",""),80,93,"L");
							}
							$j++;
							break;
						case '1':
							if ($exento==0){
								$arrayL[$array_painc[$j]]=p_set($arrayL[$array_painc[$j]],"",0,8,"B");
								$arrayL[$array_painc[$j]]=p_set($arrayL[$array_painc[$j]],quitar_spc($value),9,58,"R");
								$arrayL[$array_painc[$j]]=p_set($arrayL[$array_painc[$j]],"",59,71,"L");
								$arrayL[$array_painc[$j]]=p_set($arrayL[$array_painc[$j]],"",80,93,"L");
							}
							if ($exento==1){
								$arrayL[$array_painc[$j]]=p_set($arrayL[$array_painc[$j]],"",0,8,"B");
								$arrayL[$array_painc[$j]]=p_set($arrayL[$array_painc[$j]],quitar_spc($value),9,58,"R");
								$arrayL[$array_painc[$j]]=p_set($arrayL[$array_painc[$j]],"",59,80,"L");
								$arrayL[$array_painc[$j]]=p_set($arrayL[$array_painc[$j]],"",80,93,"L");
							}
							$j++;
							break;
						default:
							break;
					}
				}
			}
		}
	}
	$calc_iva=round($iva*$total_gravado,4);
	$total_iva_format=sprintf("%.4f",$calc_iva);
	$total_final_format=sprintf("%.2f",$total);
	list($entero,$decimal)=explode('.',$total_final_format);
	$enteros_txt=num2letras($entero);
	if($entero=='100' && $decimal=='00'){
		$enteros_txt="CIEN";
	}
	if(strlen($decimal)==1){
		$decimales_txt=$decimal."0";
	}
	else{
		$decimales_txt=$decimal;
	}

	$cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";

	$total_value=sprintf("%.2f",$total);

	$total_fin=$total_exento+$total_gravado;
	$total_value_exento=sprintf("%.2f",$total_exento);
	$total_value_gravado=sprintf("%.4f",$total_gravado);
	$total_value_fin=sprintf("%.2f",$total_fin);

	$subtotal_gravado=round($total_gravado+$calc_iva,4);
	$subtotal_exento=$total_exento;
	$total_final_todos=round($subtotal_exento+$subtotal_gravado,4);


	//totales
	$array_painc = array(
		0 => 46,
		1 => 47,
		2 => 48,
	);
	$array_nocon= dtl($cadena_salida_txt,23);
	foreach ($array_nocon as $key => $value) {
		// code...
		$arrayL[$array_painc[$key]]=p_set($arrayL[$array_painc[$key]],$value,4,50,"B");
	}

	$arrayL[46] = p_set($arrayL[46],$total_value_gravado,75,93,"L");
	$arrayL[48] = p_set($arrayL[48],$total_iva_format,75,93,"L");

	$subtotal_gravado_print=sprintf("%.4f",$subtotal_gravado);

	$arrayL[49] = p_set($arrayL[49],$subtotal_gravado_print,75,93,"L");


	if($retiene == 1)
	{
		$total_retencion = round(($total_final_format / 1.13) * 0.01, 2);
		$arrayL[51] = p_set($arrayL[51],$total_retencion,75,93,"L");
	}
	else
	{
		$arrayL[51] = p_set($arrayL[51],"0.00",75,93,"L");
	}


	$total_final_todoss=sprintf("%.2f",$total);

	$arrayL[56] = p_set($arrayL[56],$total_final_format,75,93,"L");
	foreach ($arrayL as $key => $value) {
		$info_factura.=$value;
	}
	return ($info_factura);
}

function print_ccf_tml_0($id_fact,$tipo_id,$nitcte,$nrccte,$nombreapecte){
	$id_sucursal=$_SESSION['id_sucursal'];
	$id_factura=$id_fact;
	$tipo_id=$tipo_id;
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';
	//Empresa
	$sql_empresa = "SELECT * FROM empresa";
	$result_empresa=_query($sql_empresa);
	$row_empresa=_fetch_array($result_empresa);
	$empresa=$row_empresa['nombre'];
	$razonsocial=$row_empresa['razonsocial'];
	$giro_empresa=$row_empresa['giro'];
	$iva=$row_empresa['iva']/100;
	//Sucursal
	$sql_sucursal=_query("SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'");
	$array_sucursal=_fetch_array($sql_sucursal);
	$nombre_sucursal=$array_sucursal['descripcion'];
	$nombre_sucursal1=texto_espacios($nombre_sucursal,30);
	$empresa1=texto_espacios($empresa,30);
	$razonsocial1=texto_espacios($razonsocial,30);
	$giro1=texto_espacios($giro_empresa,30);
	//inicio datos
	$info_factura="";
	$info_factura.=$empresa1."|".$nombre_sucursal1."|".$razonsocial1."|".$giro1."|";
	//Obtener informacion de tabla Factura
	$sql_fact="SELECT * FROM factura WHERE id_factura='$id_factura'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact>0){
		$id_cliente=$row_fact['id_cliente'];
		$id_factura = $row_fact['id_factura'];
		$id_usuario=$row_fact['id_usuario'];
		$fecha=$row_fact['fecha'];
		$fecha_fact=ed($fecha);
		$numero_doc=trim($row_fact['numero_doc']);
		$total=$row_fact['total'];

		$len_numero_doc=strlen($numero_doc)-4;
		$num_fact=substr($numero_doc,0,$len_numero_doc);
		$tipo_fact=substr($numero_doc,$len_numero_doc,4);
		$numfact=espacios_izq($num_fact,10);
		//Datos de empleado
		$sql_user="select * from usuario where id_usuario='$id_usuario'";
		$result_user= _query($sql_user);
		$row_user=_fetch_array($result_user);
		$nrow_user=_num_rows($result_user);
		$usuario=$row_user['usuario'];
		$nombreusuario=$row_user['nombre'];
		//$nombres=$row_user['apellido']." ".$row_user['nombre'];
		//Datos del Cliente
		$sql="SELECT clientes.*,departamento.nombre_departamento FROM clientes left JOIN departamento ON departamento.id_departamento=clientes.depto WHERE id_cliente='$id_cliente'";
		$result=_query($sql);
		$count=_num_rows($result);
		if ($count > 0) {
				for ($i = 0; $i < $count; $i ++) {
						$row1 = _fetch_array($result);
						//$id_cliente=$row1["id_cliente"];
						$nombre=$row1["nombre"];
						$nit=$row1["nit"];
						$nrc=$row1["nrc"];
						$dui=$row1["dui"];
						$direccion=substr($row1["direccion"],0,62);
						$telefono1=$row1["telefono1"];
						$giro_cte=str_pad_unicode(substr($row1["giro"], 0, 60),60," ",STR_PAD_RIGHT);
						$email=$row1["email"];
						$nombres=$row1['nombre'];
						$nombreapecte=$row1['nombre'];
						$retiene = $row1["retiene"];
						$retiene10 = $row1["retiene10"];
						$departamento = $row1['nombre_departamento'];
				}
		}

		//Columnas y posiciones base
		$base1=7;
		$col0=1;
		$col1=4;
		$col2=3;
		$col3=13;
		$col4=5;
		$sp1=2;
		$sp_prec=15;
		$sp=espacios_izq(" ",$sp1);
		$sp2=espacios_izq(" ",12);
		$esp_init=espacios_izq(" ",11);
		$esp_init1=espacios_izq(" ",14);
		$esp_precios=espacios_izq(" ",$sp_prec);
		$esp_enc2=espacios_izq(" ",3);
		$esp_init2=espacios_izq(" ",72);
		$nombre_ape=texto_espacios($nombres,45);
		$dir_txt=texto_espacios($direccion,45);
		$total_final=0;
		$imprimir="";

		//Datos encabezado factura
		$info_factura.= chr(27).chr(50); //espacio entre lineas 6 x pulgada
		//$info_factura.= chr(27).chr(51)."1"; //espacio entre lineas 6 x pulgada

		$imprimir="";
		for($s=0;$s<8;$s++){
			$imprimir.="\n";
		}
		$info_factura=$imprimir;
		list($diaa,$mess,$anio)=explode("-",$fecha_fact);
		$esp_init2=espacios_izq(" ",50);

		//subimos la fecha una linea
		$info_factura.=$esp_init2.$diaa."      ".$mess."       ".$anio."|";
		//$info_factura.="\n";
		$info_factura.="          ".$nombreapecte."\n";
		$info_factura.="           ".$direccion."|";
		//$info_factura.=$esp_init1.$dir_txt."|";
		//NRC
		$esp_init2=espacios_izq(" ",55);
		$info_factura.=espacios_izq(" ",14).str_pad_unicode(mb_strtoupper(quitar_spc($departamento)), 41,' ',STR_PAD_RIGHT).$nrccte."|";

		//direccion
		$info_factura.=espacios_izq(" ",11).$giro_cte."|";

		//NIT
		$esp_init2=espacios_izq(" ",52);
		$info_factura.=$esp_init2.$nitcte."||";
		$info_factura.="\n";
		//GIRO
		$esp_init2=espacios_izq(" ",49);
		;

		$info_factura.="\n";
		$info_factura.="\n";
		$info_factura.="\n";

		$info_factura.= chr(27).chr(51)."1"; //espacio entre lineas 6 x pulgada
		//Obtener informacion de tabla detalle_factura y producto
		$sql_fact_det="SELECT  factura_detalle.*
		FROM factura_detalle
		WHERE id_factura='$id_factura'
		";

		$result_fact_det=_query($sql_fact_det);
		$nrows_fact_det=_num_rows($result_fact_det);
		$total_final=0;
		$lineas=6;
		$cuantos=0;
		$subt_exento=0;
		$subt_gravado=0;
		$total_exento=0;
		$total_gravado=0;

		for($i=0;$i<$nrows_fact_det;$i++)
		{
			$row_fact_det=_fetch_array($result_fact_det);
			$id_prod_serv =$row_fact_det['id_prod_serv'];
			//$descripcion =$row_fact_det['descripcion'];
			//$exento=$row_fact_det['exento'];
			$id_detalle_factura =$row_fact_det['id_factura_detalle'];
			//$id_producto =$row_fact_det['id_producto'];
			$cantidad =$row_fact_det['cantidad'];
			$precio_venta =$row_fact_det['precio_venta'];
			$subt =$row_fact_det['subtotal'];
			$id_empleado =$row_fact_det['id_empleado'];
			$tipo_prod_serv =$row_fact_det['tipo_prod_serv'];
			if ($tipo_prod_serv == "PRODUCTO")
			{
				$sql_pro = _query("SELECT * FROM productos WHERE id_producto = '$id_prod_serv'");
				$row_pro = _fetch_array($sql_pro);
				$descripcion = $row_pro["descripcion"];
				$exento = 0;
			}
			if ($tipo_prod_serv == "SERVICIO")
			{
				$sql_ser = _query("SELECT * FROM servicios WHERE id_servicio = '$id_prod_serv'");
				$row_ser = _fetch_array($sql_ser);
				$descripcion = $row_ser["descripcion"];
				$exento = 0;
			}
			//linea a linea
			//$descrip=texto_espacios($descripcion,35);
			$descrip = cadenaenlineas(trim($descripcion), 37);
			$subt=$precio_venta*$cantidad;

			$tmplinea1 = array();
			$ln1=0;
			foreach($descrip as $des)
			{
				$tmplinea1[]=trim($des);
				$ln1=$ln1+1;
			}


			$precio_unit=sprintf("%.4f",$precio_venta);
			$subtotal=sprintf("%.4f",$subt);
			$total_final=$total_final+$subtotal;
			if ($exento==0){
				$e_g="G";
				$precio_sin_iva0 =$row_fact_det['precio_venta'];
				$precio_sin_iva =round($row_fact_det['precio_venta'],4);
				$subt_sin_iva=$precio_sin_iva0*$cantidad;
				$subt_gravado=sprintf("%.4f",$subt_sin_iva);
				$total_gravado=$subt_sin_iva+$total_gravado;
			}
			else{
				$e_g="E";
				$precio_sin_iva =round($row_fact_det['precio_venta'],4);
				$precio_sin_iva0 =$row_fact_det['precio_venta'];
				$subt_sin_iva=$precio_sin_iva0*$cantidad;
				$subt_exento=sprintf("%.4f",$subt_sin_iva);
				$total_exento=$subt_sin_iva+$total_exento;

			}
      $precio_sin_iva_print=sprintf("%.4f",$precio_sin_iva);

			$subt_sin_iva_print=sprintf("%.4f",$subt_sin_iva);
      $col2=2;
			$xp0=str_pad("", 5, " ", STR_PAD_RIGHT);
			$xp1=str_pad($cantidad, 3, " ", STR_PAD_RIGHT);
		  $sp1=len_espacios($cantidad,6);
			$esp_col1=espacios_izq(" ",$sp1);
			$sp2=len_espacios($precio_sin_iva_print,7);
			$xp3=str_pad($precio_sin_iva_print, 8, " ", STR_PAD_LEFT);
		  $esp_col2=espacios_izq(" ",$sp2);
			$sp3=len_espacios($subt_sin_iva_print,7);
			$xp4=str_pad($subt_sin_iva_print, 18, " ", STR_PAD_LEFT);
			$esp_col3=espacios_izq(" ",$sp3);
			$esp_desc=espacios_izq(" ",2);
			if ($exento==1){
				$info_factura.=$esp_col1.$cantidad.$esp_desc.$descrip.$esp_col2.$precio_sin_iva_print.$esp_col3."  ".$subt_sin_iva_print."\n";
			}
			if ($exento==0)
			{
				$sp3=$sp3+13;
				$esp_col3=espacios_izq(" ",$sp3);
				//$info_factura.=$esp_col1.$cantidad.$esp_desc.$descrip.$esp_col2.$precio_sin_iva_print.$esp_col3."".$subt_sin_iva_print."\n";
				for ($ik=0; $ik < $ln1 ; $ik++)
				{
					$lista = $tmplinea1[$ik];
					$xp2=str_pad_unicode($lista, 37, " ", STR_PAD_RIGHT);
					$xp5 = str_pad("", 3, " ", STR_PAD_RIGHT);
					$xp6 = str_pad("", 7, " ", STR_PAD_LEFT);
					$xp7 = str_pad("", 7, " ", STR_PAD_LEFT);
					if($ik == 0)
					{
						$info_factura.=$xp0.$xp1.$xp2.$xp3.$xp4."\n";
					}
					else {
						$info_factura.=$xp0.$xp5.$xp2.$xp6.$xp7."\n";
					}
				}
			}
			$cuantos=$cuantos+1+($ln1-1);
		}
	}
	$calc_iva=round($iva*$total_gravado,4);
	$total_iva_format=sprintf("%.4f",$calc_iva);
	$total_final_format=sprintf("%.2f",$total_final+$total_iva_format);
	list($entero,$decimal)=explode('.',$total_final_format);
	$enteros_txt=num2letras($entero);
	if($entero=='100' && $decimal=='00'){
		$enteros_txt="CIEN";
	}
	if(strlen($decimal)==1){
		$decimales_txt=$decimal."0";
	}
	else{
		$decimales_txt=$decimal;
	}

	$cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
	$esp=espacios_izq(" ",7);
	$total_value=sprintf("%.2f",$total);
	$sp3=10;
	$total_fin=$total_exento+$total_gravado;
	$total_value_exento=sprintf("%.2f",$total_exento);
	$total_value_gravado=sprintf("%.4f",$total_gravado);
	$total_value_fin=sprintf("%.2f",$total_fin);

	$vall = 13;
	$lineas_faltantes=$vall-$cuantos;
	$imprimir="";
	for($j=0;$j<$lineas_faltantes;$j++){
		$imprimir.="\n";
	}

	$info_factura.=$imprimir;

	$info_factura.= chr(27).chr(50); //espacio entre lineas 6 x pulgada

	$esp_init2=espacios_izq(" ",25);
	$esp_totales=espacios_izq(" ",35);
	//generar 2 lineas del texto del total de la factura
	$total_txt0 =cadenaenlineas($cadena_salida_txt, 30,2);
	$concepto_print="";
	$tmplinea = array();
	$ln=0;
	foreach($total_txt0 as $total_txt1){
		$tmplinea[]=$total_txt1;
		$ln=$ln+1;
	}
	//$info_factura.="\n";
	$esp_init=espacios_izq(" ",5);
	$subtotal_gravado=round($total_gravado+$calc_iva,2);
	$subtotal_exento=$total_exento;
	$total_final_todos=round($subtotal_exento+$subtotal_gravado,2);
	//$info_factura.=chr(27).chr(50);
	$esp_totales=espacios_izq(" ",47);
	$splentot1=len_espacios($total_value_exento,10);
	$esp_lentot1=espacios_izq(" ",$splentot1+5);
	$splentot2=len_espacios($total_value_gravado,10);
	$esp_lentot2=espacios_izq(" ",$splentot2+3);

  //$info_factura.="\n";
	$esp_totales=espacios_izq(" ",58);
	$a=str_pad($total_value_gravado,13," ",STR_PAD_LEFT);
	$info_factura.=$esp_totales.$a."\n";

  $splentot_iva=len_espacios($total_iva_format,10);
  $esp_tot_iva=espacios_izq(" ",$splentot_iva);
	$len_desc=55-strlen(trim($tmplinea[0]));
	$esp_totales=espacios_izq(" ",$len_desc);
	$b=str_pad($total_iva_format,10," ",STR_PAD_LEFT);
	//$a=str_pad($total_value_gravado,10," ",STR_PAD_LEFT);
	$info_factura.=$esp_init." ".$tmplinea[0].$esp_totales.$b."\n";
	//$info_factura.="\n";

	$subtotal_gravado_print=sprintf("%.4f",$subtotal_gravado);
	$c=str_pad($subtotal_gravado_print,11," ",STR_PAD_LEFT);
	if($ln>1){
		$len_desc=55-strlen(trim($tmplinea[1]));
		$esp_totales=espacios_izq(" ",$len_desc);
		$info_factura.=$esp_init.$tmplinea[1].$esp_totales.$c."\n";
	}
	else{
		$esp_totales=espacios_izq(" ",65);
		$c=str_pad($total_iva_format,6," ",STR_PAD_LEFT);
		$info_factura.=$esp_totales.$c."\n";
	}

	$vac = 4;
	if($retiene == 1)
	{
		$total_retencion = round(($total_final_format / 1.13) * 0.01, 2);
		$cx=str_pad($total_retencion,58," ",STR_PAD_LEFT);
		$info_factura.= $esp_totales.$cx;
		$vac = 3;
	}
	else
	{
		$vac = 4;
	}


	for($k=0;$k<$vac;$k++){
		$info_factura.="\n";
	}
	//$info_factura .= "\n";
	$total_final_todoss=sprintf("%.2f",$total_final_todos);
	//$esp_tot_fin=espacios_izq(" ",66);
	$esp_tot_fin=espacios_izq(" ",64);
	$d=str_pad($total_final_todoss,7," ",STR_PAD_LEFT);
	$info_factura.=$esp_tot_fin.$d."\n";
	$info_factura.="|".$d;
	// retornar valor generado en funcion
	return ($info_factura);
}
function str_pad_unicode($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT) {
    $str_len = mb_strlen($str);
    $pad_str_len = mb_strlen($pad_str);
    if (!$str_len && ($dir == STR_PAD_RIGHT || $dir == STR_PAD_LEFT)) {
        $str_len = 1; // @debug
    }
    if (!$pad_len || !$pad_str_len || $pad_len <= $str_len) {
        return $str;
    }

    $result = null;
    $repeat = ceil($str_len - $pad_str_len + $pad_len);
    if ($dir == STR_PAD_RIGHT) {
        $result = $str . str_repeat($pad_str, $repeat);
        $result = mb_substr($result, 0, $pad_len);
    } else if ($dir == STR_PAD_LEFT) {
        $result = str_repeat($pad_str, $repeat) . $str;
        $result = mb_substr($result, -$pad_len);
    } else if ($dir == STR_PAD_BOTH) {
        $length = ($pad_len - $str_len) / 2;
        $repeat = ceil($length / $pad_str_len);
        $result = mb_substr(str_repeat($pad_str, $repeat), 0, floor($length))
                    . $str
                       . mb_substr(str_repeat($pad_str, $repeat), 0, ceil($length));
    }

    return $result;
}

function p_set($linea,$dato,$inicio,$fin,$a)
{
	//$dato = quitar_spc($dato);
	$linea= str_replace("\n", "", $linea);
	$in = Unicode::substr($linea,0,$inicio-1);
	$cuerpo =st(Unicode::substr($dato,0,($fin-$inicio)),($fin-$inicio)," ",$a);
	$complemento = st(" ",strlen($linea)-strlen($in)-strlen($cuerpo));
	return $in.$cuerpo.$complemento."\n";
}

function quitar_spc($cadena){
	$no_permitidas= array ("º","á","é","í","ó","ú","Á","É","Í","Ó","Ú","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
  $permitidas=     array(" ","a","e","i","o","u","A","E","I","O","U","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E",);
  $texto = str_replace($no_permitidas, $permitidas ,$cadena);
	$texto = preg_replace('/[^a-zA-Z0-9ñÑ.|\/\-\_\+*$:= ]/u',"",utf8_encode($cadena));
  return utf8_decode($texto);
}

function st($input,$lengt,$carac=" ",$di="R")
{
	// code..
	$r = "";
	switch ($di) {
		case 'L':
		// code...
		$r=str_pad_unicode($input, $lengt, $carac, STR_PAD_LEFT);
		break;
		case 'R':
		// code...
		$r=str_pad_unicode($input, $lengt, $carac, STR_PAD_RIGHT);
		break;
		case 'B':
		// code...
		$r=str_pad_unicode($input, $lengt, $carac, STR_PAD_BOTH);
		break;
		default:
		// code...
		break;
	}
	return $r;
}
function dtl( $text, $width = '80', $lines = '10', $break = '\n', $cut = 0 ) {
	$wrappedarr = array();
	$wrappedtext = wordwrap( $text, $width, $break , true );
	$wrappedtext = trim( $wrappedtext );
	$arr = explode( $break, $wrappedtext );
	return $arr;
}

?>
