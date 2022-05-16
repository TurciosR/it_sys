<?php
function reimprimir($tipo,$numero_doc) {
	//$tipo indica si es que lleva el numero de doc  al final _COF, _CCF o es  id autoincrement (primary key) de la factura
	//$numero_doc
	//vere si como funcion que recibe 2 parametros o un script que
	//recibe los parametros por $_POST
	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];
	$id = $_POST['id'];
	if ($id=='1'){
		$tipo_entrada_salida='FACTURA CONSUMIDOR';
		$fecha_movimiento= $_POST['fecha_movimiento'];
		$numero_doc = $_POST['numero_doc'];
		$numero_docx = $numero_doc."_COF";
		$id_cliente= $_POST['id_cliente'];
		$total_venta = $_POST['total_ventas'];
	}
	if ($id=='2'){
		$tipo_entrada_salida='FACTURA CREDITO FISCAL';
		$id_cliente=$_POST['id_cliente'];
		$fecha_movimiento= $_POST['fecha_movimiento2'];
		$numero_doc = $_POST['numero_doc'];
		$numero_docx = $numero_doc."_CCF";
		$total_venta = $_POST['total_ventas'];
	}
	
	//$id_factura = $_REQUEST['id_factura'];
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else	
		$so_cliente='lin';
	//Obtener informacion de tabla Factura
	$info_factura="";
	$sql_fact="SELECT * FROM factura WHERE numero_doc='$numero_docx'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact>0){
		$id_cliente=$row_fact['id_cliente'];
		$id_factura = $row_fact['id_factura'];
		$fecha=$row_fact['fecha'];
		$numero_doc=trim($row_fact['numero_doc']);
		$total=$row_fact['total'];
		
		$len_numero_doc=strlen($numero_doc)-4;
		$num_fact=substr($numero_doc,0,$len_numero_doc);
		$tipo_fact=substr($numero_doc,$len_numero_doc,4);
		$numfact=espacios_izq($num_fact,81);
		
		//Datos del Cliente
		$sql="select * from cliente where id_cliente='$id_cliente'";
		$result= _query($sql);
		$row1=_fetch_array($result);
		$nrow1=_num_rows($result);	
		$nombres=$row1['nombre']." ".$row1['apellido'];	 
		$dui=$row1['dui'];
		$nit=$row1['nit'];
		$direccion=$row1['direccion'];
		
		//Columnas y posiciones base
		$base1=7;
		$col0=15;
		$col1=6;
		$col2=4;
		$col3=15;
		$col4=12;
		$sp1=$base1;
		$sp=espacios_izq(" ",$sp1);
		$sp2=espacios_izq(" ",12);
		$esp_init=espacios_izq(" ",$col0);
		$esp_enc2=espacios_izq(" ",3);
		$esp_init2=espacios_izq(" ",23);
		$nombre_ape=texto_espacios($nombres,60);	
		$dir_txt=texto_espacios($direccion,60);	   
		$total_final=0;
		for($j=0;$j<2;$j++){
			$info_factura.="\n";
		}	
		$info_factura.=$esp_init.$numfact."\n";
		for($j=0;$j<2;$j++){
			$info_factura.="\n";
		}	
		$info_factura.=$esp_init2.$nombre_ape.$esp_enc2.$fecha."\n";
		$info_factura.=$esp_init2.$dir_txt.$esp_enc2.$dui."\n";
		for($j=0;$j<3;$j++){
			$info_factura.="\n";
		}	
	  
		//Obtener informacion de tabla Factura_detalle y producto o servicio
		$sql_fact_det="SELECT  producto.id_producto, producto.descripcion,factura_detalle.* 
		FROM factura_detalle JOIN producto ON factura_detalle.id_prod_serv=producto.id_producto
		WHERE  factura_detalle.id_factura='$id_factura' AND  factura_detalle.tipo_prod_serv='PRODUCTO' 
		UNION ALL
		SELECT  servicio.id_servicio, servicio.descripcion,factura_detalle.* 
		FROM factura_detalle JOIN servicio ON factura_detalle.id_prod_serv=servicio.id_servicio
		WHERE  factura_detalle.id_factura='$id_factura' AND  factura_detalle.tipo_prod_serv='SERVICIO'
		";
		
		$result_fact_det=_query($sql_fact_det);
		$nrows_fact_det=_num_rows($result_fact_det);
		$total_final=0;
		$lineas=6;
		$cuantos=0;
		for($i=0;$i<$nrows_fact_det;$i++){
			$row_fact_det=_fetch_array($result_fact_det);
			$id_producto =$row_fact_det['id_producto'];
			$descripcion =$row_fact_det['descripcion'];
			$id_factura_detalle =$row_fact_det['id_factura_detalle'];
			$id_prod_serv =$row_fact_det['id_prod_serv'];
			$cantidad =$row_fact_det['cantidad'];
			$precio_venta =$row_fact_det['precio_venta'];
			$subt =$row_fact_det['subtotal'];
			$id_empleado =$row_fact_det['id_empleado'];
			$tipo_prod_serv =$row_fact_det['tipo_prod_serv'];
			
			//linea a linea
			$descrip=texto_espacios($descripcion,63);
			$subt=$precio_venta*$cantidad;
			$precio_unit=sprintf("%.2f",$precio_venta);
			$subtotal=sprintf("%.2f",$subt);
			$total_final=$total_final+$subtotal;
			
			if(strlen($cantidad)>1){
				$sp0=$col0-1;
				$esp_init=espacios_izq(" ",$sp0);
			}else{
				$sp0=$col0;
				$esp_init=espacios_izq(" ",$sp0);
			}
			
			if(strlen($precio_unit)>4){
				$sp2=$col2-1;
				$esp_col2=espacios_izq(" ",$sp2);
			}else{
				$sp2=$col2;
				$esp_col2=espacios_izq(" ",$sp2);
			}
			
			if(strlen($subtotal)>4){
				$sp3=$col3-1;
				$esp_col3=espacios_izq(" ",$sp3);
			}else{
				$sp3=$col3;
				$esp_col3=espacios_izq(" ",$sp3);
			}			
			$info_factura.=$esp_init.$cantidad."   ".$descrip.$esp_col2.$precio_unit.$esp_col3.$subtotal."\n"; 	
			$cuantos=$cuantos+1;					     		
		}
	}
	$salto_linea=$lineas-$cuantos;	
	for($j=0;$j<$salto_linea;$j++){
		$info_factura.="\n";
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
	$info_factura.="\n";
	$cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
	$salida_txt_total=texto_espacios($cadena_salida_txt,70);
	$esp=espacios_izq(" ",7);
	$total_value=sprintf("%.2f",$total);
	$esp_init2=espacios_izq(" ",25);
	$info_factura.=$esp_init2.$salida_txt_total.$esp."$ ".$total_value."\n";
	$esp=espacios_izq(" ",86);
	$info_factura.="\n";
	$info_factura.=$esp_init.$esp." $ ".$total_value."\n";
	
	$nreg_encode['facturar'] = $info_factura;	
	$nreg_encode['sist_ope'] =$so_cliente;
	echo json_encode($nreg_encode);						
	
}

?>
