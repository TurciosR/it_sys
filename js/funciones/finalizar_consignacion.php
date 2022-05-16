<?php
include_once "_core.php";
include ('num2letras.php');
include("escpos-php/Escpos.php");
function initial() {
	$_PAGE = array ();
	$_PAGE ['title'] = 'Finalizar Consignacion';
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2-bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	$id_consignacion= $_REQUEST['id_consignacion'];
	$id_usuario=$_SESSION["id_usuario"];
	
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri=$_SERVER['REQUEST_URI'];
	list($uri1,$uri2,$filename)=explode("/",$uri);
	$poschar = strpos($filename, '?');
	if ($poschar !== false) {
		list($namelink1,$namelink1)=explode("?",$filename);
		$links=permission_usr($id_user,$namelink1);
	}
	else{
		 $links=permission_usr($id_user,$filename);
	}
?>
 
    <div class="row wrapper border-bottom white-bg page-heading">                
		<div class="col-lg-2"></div>
    </div>
        <div class="wrapper wrapper-content  animated fadeInRight">
           <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
						<?php  
						//permiso del script
						if ($links!='NOT' || $admin=='1' ){
						?>	
                        <div class="ibox-title">
                            <h5>Consignación</h5>
                        </div>
                        <div class="ibox-content">
					  <div class="row">
							<?php
							
							$sql="SELECT consignacion.*, cliente.nombre,cliente.apellido,
							empleado.nombre as nomempleado,empleado.apellido as apempleado
							FROM consignacion 
							JOIN cliente ON consignacion.id_cliente=cliente.id_cliente
							JOIN empleado ON consignacion.id_empleado=empleado.id_empleado
							WHERE finalizada=0
							AND id_consignacion='$id_consignacion' ";
							/*
							SELECT consignacion.*, cliente.nombre,cliente.apellido 
							FROM consignacion JOIN cliente ON consignacion.id_cliente=cliente.id_cliente
							WHERE finalizada=0
							 */ 	
							$result=mysql_query($sql);
							$count=mysql_num_rows($result);
							$rows=_fetch_array($result);
							$numero_doc=$rows['numero_doc'];
							$nombre_cliente=$rows['nombre']." ".$rows['apellido'];
							$nombre_empleado=$rows['nomempleado']." ".$rows['apempleado'];
							$fecha_actual=date("Y-m-d");
							echo "<div class='col-lg-6' id='mostrar_numero_doc'>";
							echo "<div class='form-group has-info single-line'><label>Número de Consignaci&oacute;n</label> <input type='text' placeholder='Numero de Factura' class='form-control' id='numero_doc' name='numero_doc' value='$numero_doc'></div>";
							echo "</div>";	
							echo "<div class='col-lg-6'>";
							echo "<div class='form-group has-info single-line'><label>Fecha:</label> <input type='text' placeholder='Fecha' class='datepick form-control' id='fecha' name='fecha' value='$fecha_actual'></div>";
							echo "</div>";
							echo "<div class='col-lg-6'>";
							echo "<div class='form-group has-info single-line'><label>Cliente:</label><input type='text' value='$nombre_cliente' class='form-control' readOnly></div>";
							echo "</div>";
							echo "<div class='col-lg-6'>";
							echo "<div class='form-group has-info single-line'><label>Vendedor:</label><input type='text' value='$nombre_empleado' class='form-control' readOnly></div>";
							echo "</div>";
							
							
							
							
							?>	        
					</div>  <!--div class="row"-->	
					
	

						<div class="ibox ">						
						<div class="ibox-content">
						<!--load datables estructure html-->
						<header><h4 class="text-navy">CONSIGNACION</h4></header>
						<section>
							
							 <div class="table-responsive m-t">
                                <table class="table  table-bordered table-condensed" id="inventable">
									
							<!--table class="table table-striped invoice-table table-bordered" id="inventable"-->
								<thead>
									<tr>
										<th>Id</th>
										<th>Nombre</th>
										<!--th>Existencias</th>	
										<th>Lista Precio</th-->						
										<th>Precio Vta.</th>
										<th>Cant consigna</th>	
										<th>Subtot  Cons.</th>	
										<th>Cant Facturar</th>
										<th>Dev. Stock</th>
										<th>Subt Facturar</th>
										<!--th>Atendido por</th-->
										
									</tr>
								</thead>
								
							<tbody> 
					<?php
	//Datos de Consignacin y consignacion_detalle
							$sql_fact_det="SELECT  producto.id_producto, producto.descripcion,consignacion_detalle.* 
		FROM consignacion_detalle JOIN producto ON consignacion_detalle.id_prod_serv=producto.id_producto
		WHERE  consignacion_detalle.id_consignacion='$id_consignacion' 
		AND  consignacion_detalle.tipo_prod_serv='PRODUCTO' 
		
		";
		
		$result_fact_det=_query($sql_fact_det);
		$nrows_fact_det=_num_rows($result_fact_det);
						for($i=0;$i<$nrows_fact_det;$i++){
			$row_fact_det=_fetch_array($result_fact_det);
			$id_producto =$row_fact_det['id_producto'];
			$descripcion =$row_fact_det['descripcion'];
			$id_consignacion_detalle =$row_fact_det['id_consignacion_detalle'];
			$id_prod_serv =$row_fact_det['id_prod_serv'];
			$cantidad =$row_fact_det['cantidad'];
			$precio_venta =$row_fact_det['precio_venta'];
			$subt =$row_fact_det['subtotal'];
			$id_empleado =$row_fact_det['id_empleado'];
			$tipo_prod_serv =$row_fact_det['tipo_prod_serv'];	
		      echo "<tr>";
		      echo "<td>$id_producto</td>"; 
		      echo "<td>$descripcion</td>";
		      echo "<td>$precio_venta</td>";
		      echo "<td id='cant_consign'>$cantidad</td>";
		      echo "<td id='subtot'>$subt</td>";
		      echo "<td><input type='text'  class='form-control' id='cant_fact'></td>";
		       echo "<td id='dev_stock'></td>";
		      echo "<td id='subtot_fact'>$subt</td>";
		      echo "</tr>";
		      }	
		      
		      		
					?>
							</tbody>
							<tfoot>
									<tr>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td><strong>TOTAL $:</strong></td>
									<td id='total_dinero'></td>
									<td></td>
									<td></td>
									</tr>
								</tfoot>		
						</table>
						</div>
			
						<div></div>
							<div class="well m-t"  id='totaltexto'><strong>Son:</strong> </div>
							<input type='hidden' name='totalconsignacion' id='totalconsignacion' value='0'>
							
					</section>   
						
                               
                     <!--div class="col-lg-4"-->
				<!--/form-->
                    <div class="title-action" id='botones'>
						<button type="submit" id="submit1" name="submit1" class="btn btn-primary"><i class="fa fa-check"></i> Guardar</button>
						<button type="submit" id="print1" name="print1" class="btn btn-primary"><i class="fa fa-print"></i> Imprimir</button>
						<!--a data-toggle="modal"  title="Imprimir" id ="submit1" class="print1 btn btn-primary" href="#viewModal">Guardar</a-->
					
                    </div>
                </div>
       
                               
                </div><!--div class='ibox-content'-->
                </div><!--div class='ibox'-->
              
            <!--/div-->
    <!--Show Modal Popups View & Delete -->
				<!-- Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content modal-sm">
      <div class="modal-header">
        <!--utton type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button-->
        <h4 class="modal-title" id="myModalLabel">Pago y Cambio</h4>
      </div>
      <div class="modal-body">
		  <div class="wrapper wrapper-content  animated fadeInRight">
		  <div class="row"> 					
								<div class="col-lg-12">
                                  <div class="form-group">
                                      <label><h4 class='text-navy'>Consignado $ </h5></label><input type="text" id="facturado" name="facturado" value=0  class="form-control decimal" readonly >
                                  </div>
								</div>
			 </div>
			<div class="row"> 
								   
								<div class="col-lg-12">
                                  <div class="form-group">
                                      <label>Efectivo $</label><input type="text" id="efectivo" name="efectivo" value=""  class="form-control decimal">
                                  </div>
								</div>
			 </div>					
			<div class="row"> 					
								<div class="col-lg-12">
                                  <div class="form-group">
                                      <label>Cambio $</label><input type="text" id="cambio" name="cambio" value=0 placeholder="cambio" class="form-control decimal" readonly >
                                  </div>
								</div>
			 </div>
			</div> 
      </div>
      <div class="modal-footer">
		  
	  <button type="button" class="btn btn-primary" id="btnPrintFact">Imprimir</button>
        <!--button type="button" class="btn btn-default" data-dismiss="modal">Salir</button-->

      </div>
    </div>
  </div>
</div>
              
            </div>

        <!--/div-->
        <!--/div-->
   
<?php
include_once ("footer.php");

echo "<script src='js/plugins/typehead/bootstrap3-typeahead.js'></script>";
echo "<script src='js/funciones/finalizar_consignacion.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}	
}

function insertar(){

	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];
	$id = $_POST['id'];
	$id_usuario=$_SESSION["id_usuario"];
	$id_empleado= $_POST['id_empleado'];
	//$hora=date('H:i:s');
	//if ($id=='1'){
		$tipo_entrada_salida='CONSIGNACION SALIDA';
		$fecha_movimiento= $_POST['fecha_movimiento'];
		$numero_docx = $_POST['numero_doc'];
		$numero_doc = $_POST['numero_doc']."_CSN";
		$id_cliente=$_POST['id_cliente'];
		$total_venta = $_POST['total_ventas'];
	
	
	$sql="select * from ultimo_numdoc";
	$result= _query($sql);
	$rows=_fetch_array($result);
	$nrows=_num_rows($result);	
	$ult_cof=$rows['ult_cof'];	
	$ult_ccf=$rows['ult_ccf'];
	$ult_csn=$rows['ult_csn'];
	$table_numdoc="ultimo_numdoc";	
	//if ($id=='1'){
			$data_numdoc = array(	
				'ult_csn' => $numero_docx
	);	
	if ($nrows==0){
			$insertar_numdoc = _insert($table_numdoc,$data_numdoc );
	}
	else {
			$insertar_numdoc = _update($table_numdoc,$data_numdoc );
		}		  
	//}
	
							
	$insertar1=false;
	$insertar2=false;
	
	//if ($id=='1'){
		$observaciones=$tipo_entrada_salida;
		if ($cuantos>0){	
			//insertar a la tabla consignacion
			//INSERT INTO consignacion(id_consignacion, id_cliente, fecha, numero_doc, total, id_usuario) VALUES ();
			$sql_fact="SELECT * FROM consignacion WHERE numero_doc='$numero_doc' and fecha='$fecha_movimiento'";
			$result_fact=_query($sql_fact);
			$row_fact=_fetch_array($result_fact);
			$nrows_fact=_num_rows($result_fact);
			if($nrows_fact==0){
				 $table_fact= 'consignacion';
				$form_data_fact = array(	
				'id_cliente' => $id_cliente,
				'fecha' => $fecha_movimiento,
				'numero_doc' => $numero_doc,				
				'total' => $total_venta,
				'id_usuario'=>$id_usuario
				);
				$insertar_fact = _insert($table_fact,$form_data_fact );	  
				$id_fact= mysql_insert_id(); 
			}	 
			$listadatos=explode('#',$stringdatos);	
			for ($i=0;$i<$cuantos ;$i++){
				list($id_prod_serv,$tipoprodserv,$precio_venta,$cantidad)=explode('|',$listadatos[$i]);
				$subtotal=$precio_venta*$cantidad;
				$table_fact_det= 'consignacion_detalle';
				$data_fact_det = array(	
				'id_consignacion' => $id_fact,
				'id_prod_serv' => $id_prod_serv,
				'cantidad' => $cantidad,
				'precio_venta' => $precio_venta,				
				'subtotal' => $subtotal,
				'tipo_prod_serv' => $tipoprodserv,
				'id_empleado' => $id_empleado,
				);
				$insertar_fact_det = _insert($table_fact_det,$data_fact_det );	  
				if ($tipoprodserv=='SERVICIO'){
					//INSERT INTO consignacion(id_consignacion, id_cliente, fecha, numero_doc, total, id_usuario) VALUES ();
					$sql="SELECT id_servicio as id, descripcion, tipo_prod_servicio FROM servicio WHERE id_servicio='$id_prod_serv'";
				}
				else {	
					$sql1="select * from movimiento_producto where id_producto='$id_prod_serv' and tipo_entrada_salida='$tipo_entrada_salida' 
					AND numero_doc='$numero_doc' and fecha_movimiento='$fecha_movimiento'";
					$stock1=_query($sql1);
					$row1=_fetch_array($stock1);
					$nrow1=_num_rows($stock1);

					$sql2="select producto.id_producto,producto.unidad,stock.stock, stock.costo_promedio from producto,stock 
					where producto.id_producto='$id_prod_serv' and producto.id_producto=stock.id_producto";
					$stock2=_query($sql2);
					$row2=_fetch_array($stock2);
					$nrow2=_num_rows($stock2);
					$unidad=$row2['unidad'];
					$existencias=$row2['stock'];			
					$table1= 'movimiento_producto';
					$form_data1 = array(	
					'id_producto' => $id_prod_serv,
					'fecha_movimiento' => $fecha_movimiento,
					'salida' => $cantidad,
					'observaciones' => $observaciones,
					'tipo_entrada_salida' => $tipo_entrada_salida,
					'numero_doc' => $numero_doc,
					'precio_venta' => $precio_venta
					);  
					$table2= 'stock';
					if($existencias>=$cantidad)
						$cantidad_stock=$existencias-$cantidad;
					else
						$cantidad_stock=0;
					if ($nrow1==0){
						$insertar1 = _insert($table1,$form_data1 );
					}
					//Actualizar en stock si  hay registro del producto
					if ($nrow2>0 && $nrow1==0){
						$where_clause="WHERE id_producto='$id_prod_serv'";
			
						
						$form_data2 = array(	
						'id_producto' => $id_prod_serv,
						'stock' => $cantidad_stock,
						'stock_minimo' => $cantidad,
						'precio_sugerido' => $precio_venta
						);    
						$insertar2 = _update($table2,$form_data2, $where_clause ); 
					}
				}//si es PRODUCTO		 
			}//for
		}//if
	//} //if $id=1

  
    if ($insertar1 && $insertar2){
     $xdatos['typeinfo']='Success';
       $xdatos['msg']='Registro Guardado !';
       $xdatos['process']='insert';
    }
    if ($insertar_fact  && $insertar_fact_det ){
     $xdatos['typeinfo']='Success';
       $xdatos['msg']='Registro Guardado !';
       $xdatos['process']='insert';
    }
    if(!$insertar1 && !$insertar2 && !$insertar_fact &&$insertar_fact_det){
       $xdatos['typeinfo']='Error';
       $xdatos['msg']='Registro de consignacion no pudo ser Actualizado !';
		}    

   
	echo json_encode($xdatos);
}
function consultar_stock(){
	$id_producto = $_REQUEST['id_producto'];
	$tipo = $_REQUEST['tipo'];
	if ($tipo =='PRODUCTO'){
	 
		$sql1="SELECT producto.id_producto,producto.descripcion,producto.unidad,
		producto.utilidad_activa,producto.utilidad_seleccion,producto.porcentaje_utilidad1,
		producto.porcentaje_utilidad2,producto.porcentaje_utilidad3,
		producto.porcentaje_utilidad4,producto.imagen,
		stock.stock,stock.costo_promedio,stock.precio_sugerido
		FROM producto JOIN stock ON producto.id_producto=stock.id_producto
		WHERE producto.id_producto='$id_producto'";
		$stock1=_query($sql1);
		$row1=_fetch_array($stock1);
		$nrow1=_num_rows($stock1);
		$unidades=round($row1['unidad'],2);
		$utilidad_activa=$row1['utilidad_activa'];
		$utilidad_seleccion=$row1['utilidad_seleccion'];
		$pu1=$row1['porcentaje_utilidad1'];
		$pu2=$row1['porcentaje_utilidad2'];
		$pu3=$row1['porcentaje_utilidad3'];
		$pu4=$row1['porcentaje_utilidad4'];
		$cp=$row1['costo_promedio'];
		$existencias=$row1['stock'];
	    $precio_sugerido=$row1['precio_sugerido'];
		$imagen=$row1['imagen'];
		//$costos_pu=$pu1."|".$pu2."|".$pu3."|".$pu4."|".$cp."|";
		$costos_pu=array($pu1,$pu2,$pu3,$pu4);
		$precio_venta=0.0;
				 
		 if( $utilidad_activa==true || $utilidad_activa==1){
			 switch ($utilidad_seleccion) {
			case 1:
				$precio_venta=$cp*(1+$pu1/100);
				//$precio_venta=$cp*(1+2.5/100);
				break;
			case 2:
				$precio_venta=$cp*(1+$pu2/100);
				break;	
			case 3:
				$precio_venta=$cp*(1+$pu3/100);
				break;	
			case 4:
				$precio_venta=$cp*(1+$pu4/100);
				break;			 
			 }//switch ($utilidad_seleccion]) {
		}//if( $utilidad_activa==true){
		else{
			$precio_venta=$precio_sugerido;
		}
		if ($unidades>1){
			//Precio para validar si tiene unidades mayores que 1 en producto
			$precio_venta=$precio_venta/$unidades;
		}
	}
	else{ //si es servicio traemos los datos de la tabla servicio
		
		$sql1="select * from servicio where id_servicio='$id_producto'";
			 $stock1=_query($sql1);
			 $row1=_fetch_array($stock1);
			 $nrow1=_num_rows($stock1);
			 
			 $precio_venta=$row1['precio'];
			 $existencias='1';
			 $costos_pu=$precio_venta;
			 $unidades='1';
	}
		 $precio_venta=round($precio_venta,2);
		$xdatos['existencias'] = $existencias;
		$xdatos['precio_venta'] = $precio_venta;
		$xdatos['costos_pu'] = $costos_pu;
		$xdatos['costo_prom'] = $cp;
		$xdatos['unidades'] = $unidades;
		$xdatos['imagen'] = $imagen;
	echo json_encode($xdatos); //Return the JSON Array

}
function mostrar_datos_cliente(){
	$id=$_REQUEST['id'];
	$sql="select * from cliente where id_cliente='$id'";
	$result= _query($sql);
	$row1=_fetch_array($result);
	$nrow1=_num_rows($result);		 
	$dui=$row1['dui'];
	$nit=$row1['nit'];
	$direccion=$row1['direccion'];
	$direccion=htmlentities($direccion);
	echo " <div class='col-lg-6'>";		
	echo "<div class='form-group has-info single-line'><label>Dui</label> <input type='text' placeholder='DUI' class='form-control' id='dui' name='dui' value='$dui' ></div>";
	echo "</div>";
	echo " <div class='col-lg-6'>";	
	echo "<div class='form-group has-info single-line'><label>Nit:</label> <input type='text' placeholder='NIT' class='form-control' id='nit' name='nit' value='$nit'></div>";
	echo "</div>";	
	echo " <div class='col-lg-12'>";		
	echo "<div class='form-group has-info single-line'><label>Direccion</label> <input type='text' placeholder='Direcci&oacute;n' class='form-control' id='direccion' name='direccion' value='$direccion'></div>";
	echo "</div>";

}	

function cargar_empleados(){
	echo"<option value='-1'>Mostrador</option>  ";                                  
	$qempleado=_query('SELECT * FROM empleado ORDER BY apellido');
	while($row_empleado=_fetch_array($qempleado)){
		$id_empleado=$row_empleado['id_empleado'];
		$nombres=$row_empleado['nombre']." ".$row_empleado['apellido'];
		echo "<option value='$id_empleado'>$nombres</option> ";
	}               	
}

function cargar_precios(){
	//echo"<option value='-1'>Mostrador</option>  ";                                  
	$q_precios=_query('SELECT * FROM empleado ORDER BY apellido');
	while($row_precios=_fetch_array($q_precios)){
		$id_precios=$row_precios['id_precios'];
		$nombres=$row_precios['nombre']." ".$row_precios['apellido'];
		echo "<option value='$id_precios'>$nombres</option> ";
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

function print1(){
	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];
	$id = $_POST['id'];
	
		$tipo_entrada_salida='CONSIGNACION';
		$id_cliente=$_POST['id_cliente'];
		$fecha_movimiento= $_POST['fecha_movimiento'];
		$numero_doc = $_POST['numero_doc'];
		$numero_docx = $numero_doc."_CSN";
		$total_venta = $_POST['total_ventas'];
	
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else	
		$so_cliente='lin';
	$Fecha=date("d-m-Y");
	$Hora=date("H:i");
	
	$sql="select * from cliente where id_cliente='$id_cliente'";
	$result= _query($sql);
	$row1=_fetch_array($result);
	$nrow1=_num_rows($result);	
	$nombres=$row1['nombre']." ".$row1['apellido'];	 
	$dui=$row1['dui'];
	$nit=$row1['nit'];
	$direccion=$row1['direccion'];
	$info_consignacion="";
	
	
	$numfact=espacios_izq($numero_doc,81);
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
		$info_consignacion.="\n";
	}
	$info_consignacion.=$esp_init.$numfact."\n";
	for($j=0;$j<2;$j++){
		$info_consignacion.="\n";
	}			
	$info_consignacion.=$esp_init2.$nombre_ape.$esp_enc2.$fecha_movimiento."\n";
	$info_consignacion.=$esp_init2.$dir_txt.$esp_enc2.$dui."\n";
	for($j=0;$j<3;$j++){
		$info_consignacion.="\n";
	}	
	$listadatos=explode('#',$stringdatos);	
	$lineas=6;
	$salto_linea=$lineas-$cuantos;
	if ($cuantos<=$lineas){
		for ($i=0;$i<$cuantos ;$i++){
			list($id_prod_serv,$descripcion,$tipoprodserv,$precio_venta,$cantidad,$id_empleado)=explode('|',$listadatos[$i]);
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
			
			$info_consignacion.=$esp_init.$cantidad."   ".$descrip.$esp_col2.$precio_unit.$esp_col3.$subtotal."\n"; 						     		
		}
		for($j=0;$j<$salto_linea;$j++){
			$info_consignacion.="\n";
		}	
	}
	list($entero,$decimal)=explode('.',$total_venta);
	$enteros_txt=num2letras($entero);
	if(strlen($decimal)==1){
	$decimales_txt=$decimal."0";
	}
	else{
		$decimales_txt=$decimal;
	}
	$info_consignacion.="\n";
	$cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
	$salida_txt_total=texto_espacios($cadena_salida_txt,70);
	$esp=espacios_izq(" ",7);
	$total_value=sprintf("%.2f",$total_final);
	$esp_init2=espacios_izq(" ",25);
	$info_consignacion.=$esp_init2.$salida_txt_total.$esp."$ ".$total_value."\n";
	$esp=espacios_izq(" ",86);
	$info_consignacion.="\n";
	$info_consignacion.=$esp_init.$esp." $ ".$total_value."\n";
	$nreg_encode['consignacionr'] = $info_consignacion;	
	$nreg_encode['sist_ope'] =$so_cliente;
	echo json_encode($nreg_encode);			
}

function print2(){
	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];
	$id = $_POST['id'];
	if ($id=='1'){
		$tipo_entrada_salida='consignacion CONSUMIDOR';
		$fecha_movimiento= $_POST['fecha_movimiento'];
		$numero_doc = $_POST['numero_doc'];
		$numero_docx = $numero_doc."_COF";
		$id_cliente=$_POST['id_cliente'];
		$total_venta = $_POST['total_ventas'];
	}
	if ($id=='2'){
		$tipo_entrada_salida='consignacion CREDITO FISCAL';
		$id_cliente=$_POST['id_cliente'];
		$fecha_movimiento= $_POST['fecha_movimiento2'];
		$numero_doc = $_POST['numero_doc'];
		$numero_docx = $numero_doc."_CCF";
		$total_venta = $_POST['total_ventas'];
	}
	
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else	
		$so_cliente='lin';
	
	//$Fecha=date("d-m-Y");
	//$Hora=date("H:i");
	
	$sql="select * from cliente where id_cliente='$id_cliente'";
	$result= _query($sql);
	$row1=_fetch_array($result);
	$nrow1=_num_rows($result);	
	$nombres=$row1['nombre']." ".$row1['apellido'];	 
	$dui=$row1['dui'];
	$nit=$row1['nit'];
	$direccion=$row1['direccion'];
	$info_consignacion="";
	
	
	$numfact=espacios_izq($numero_doc,65);
	$base1=7;
	//Columnas y posiciones base
	$col0=17;
	$col1=7;
	$col2=3;
	$col3=12;
	$col4=12;
	$sp1=$base1;
	$sp=espacios_izq(" ",$sp1);
	$sp2=espacios_izq(" ",12);
	$esp_init=espacios_izq(" ",$col0);
	$esp_init2=espacios_izq(" ",23);
	$nombre_ape=texto_espacios($nombres,56);	
	$dir_txt=texto_espacios($direccion,56);	   
	$total_final=0;
	//$num_fact=texto_espacios($numero_doc,60);	    
	for($j=0;$j<2;$j++){
			$info_consignacion.="\n";
	}
	$info_consignacion.=$esp_init.$numfact."\n";
	for($j=0;$j<2;$j++){
			$info_consignacion.="\n";
	}			
	$info_consignacion.=$esp_init2.$nombre_ape.$fecha_movimiento."\n";
	$info_consignacion.=$esp_init2.$dir_txt.$dui."\n";
	for($j=0;$j<2;$j++){
			$info_consignacion.="\n";
	}	
	$listadatos=explode('#',$stringdatos);	
	$lineas=8;
	$salto_linea=$lineas-$cuantos;
	if ($cuantos<=$lineas){
		for ($i=0;$i<$cuantos ;$i++){
			list($id_prod_serv,$descripcion,$tipoprodserv,$precio_venta,$cantidad,$id_empleado)=explode('|',$listadatos[$i]);
			$descrip=texto_espacios($descripcion,55);
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
			
			$info_consignacion.=$esp_init.$cantidad."  ".$descrip.$esp_col2.$precio_unit.$esp_col3.$subtotal."\n"; 						     		
		}
		for($j=0;$j<$salto_linea;$j++){
			$info_consignacion.="\n";
		}	
	}

	list($entero,$decimal)=explode('.',$total_venta);
	$enteros_txt=num2letras($entero);
	if(strlen($decimal)==1){
		$decimales_txt=$decimal."0";
	}
	else{
		$decimales_txt=$decimal;
	}
	$cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
	$salida_txt_total=texto_espacios($cadena_salida_txt,65);
	$esp=espacios_izq(" ",7);
	$total_value=sprintf("%.2f",$total_final);
	$esp_init2=espacios_izq(" ",25);
	$info_consignacion.=$esp_init2.$salida_txt_total.$esp."$ ".$total_value."\n";
	$esp=espacios_izq(" ",86);
	$info_consignacion.="\n";
	$info_consignacion.=$esp_init.$esp." $ ".$total_final."\n";
	$nreg_encode['consignacionr'] = $info_consignacion;	
	$nreg_encode['sist_ope'] =$so_cliente;
	echo json_encode($nreg_encode);

}
function reimprimir() {
	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];
	$id = $_POST['id'];
		$tipo_entrada_salida='consignacion CONSUMIDOR';
		$fecha_movimiento= $_POST['fecha_movimiento'];
		$numero_doc = $_POST['numero_doc'];
		$numero_docx = $numero_doc."_CSN";
		$id_cliente= $_POST['id_cliente'];
		$total_venta = $_POST['total_ventas'];
	
	
	//$id_consignacion = $_REQUEST['id_consignacion'];
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else	
		$so_cliente='lin';
	//Obtener informacion de tabla consignacion
	$info_consignacion="";
	$sql_fact="SELECT * FROM consignacion WHERE numero_doc='$numero_docx'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact>0){
		$id_cliente=$row_fact['id_cliente'];
		$id_consignacion = $row_fact['id_consignacion'];
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
			$info_consignacion.="\n";
		}	
		$info_consignacion.=$esp_init.$numfact."\n";
		for($j=0;$j<2;$j++){
			$info_consignacion.="\n";
		}	
		$info_consignacion.=$esp_init2.$nombre_ape.$esp_enc2.$fecha."\n";
		$info_consignacion.=$esp_init2.$dir_txt.$esp_enc2.$dui."\n";
		for($j=0;$j<3;$j++){
			$info_consignacion.="\n";
		}	
	  
		//Obtener informacion de tabla consignacion_detalle y producto o servicio
		$sql_fact_det="SELECT  producto.id_producto, producto.descripcion,consignacion_detalle.* 
		FROM consignacion_detalle JOIN producto ON consignacion_detalle.id_prod_serv=producto.id_producto
		WHERE  consignacion_detalle.id_consignacion='$id_consignacion' AND  consignacion_detalle.tipo_prod_serv='PRODUCTO' 
		UNION ALL
		SELECT  servicio.id_servicio, servicio.descripcion,consignacion_detalle.* 
		FROM consignacion_detalle JOIN servicio ON consignacion_detalle.id_prod_serv=servicio.id_servicio
		WHERE  consignacion_detalle.id_consignacion='$id_consignacion' AND  consignacion_detalle.tipo_prod_serv='SERVICIO'
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
			$id_consignacion_detalle =$row_fact_det['id_consignacion_detalle'];
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
			$info_consignacion.=$esp_init.$cantidad."   ".$descrip.$esp_col2.$precio_unit.$esp_col3.$subtotal."\n"; 	
			$cuantos=$cuantos+1;					     		
		}
	}
	$salto_linea=$lineas-$cuantos;	
	for($j=0;$j<$salto_linea;$j++){
		$info_consignacion.="\n";
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
	$info_consignacion.="\n";
	$cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
	$salida_txt_total=texto_espacios($cadena_salida_txt,70);
	$esp=espacios_izq(" ",7);
	$total_value=sprintf("%.2f",$total);
	$esp_init2=espacios_izq(" ",25);
	$info_consignacion.=$esp_init2.$salida_txt_total.$esp."$ ".$total_value."\n";
	$esp=espacios_izq(" ",86);
	$info_consignacion.="\n";
	$info_consignacion.=$esp_init.$esp." $ ".$total_value."\n";
	
	$nreg_encode['consignacionr'] = $info_consignacion;	
	$nreg_encode['sist_ope'] =$so_cliente;
	echo json_encode($nreg_encode);						
	
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

//mostrar el num de consignacion de consumidor o credito fiscal
function mostrar_numfact(){
	$id=$_REQUEST['id'];
	
	$sql="select * from ultimo_numdoc";
	$result= _query($sql);
	$rows=_fetch_array($result);
	$nrows=_num_rows($result);	
	$ult_cof=$rows['ult_cof'];	 
	$ult_ccf=$rows['ult_ccf'];
						
	$fecha_actual=date("Y-m-d");
	if ($nrows>0){ 
		if ($id=='1')
			$ult_doc=$ult_cof+1;
		else
			$ult_doc=$ult_ccf+1;	
	}	
	else{
		if ($id=='1')
			$ult_doc=1;
		else
			$ult_doc=1;
	}
	$len_ult_valor=strlen($ult_doc);	
	$long_num_fact=4;	
	$len_ult_valor=strlen($ult_doc);	
	$long_increment=$long_num_fact-$len_ult_valor;
	$valor_txt="";
	for ($j=0;$j<$long_increment;$j++){
		$valor_txt.="0";
	}
	$valor_txt=$valor_txt.$ult_doc;
	
	/*
	switch ($len_ult_valor) {
		case 1:
			$valor_txt='000'.$ult_doc;
			break;
		case 2:
			$valor_txt='00'.$ult_doc;
			break;	
		case 3:
			$valor_txt='0'.$ult_doc;
			break;	
		case 4:
			$valor_txt=$ult_doc;
			break;	
		}	
	*/
	echo "<div class='form-group has-info single-line'><label>Numero de consignacion</label> <input type='text' placeholder='Numero de consignacion' class='form-control' id='numero_doc' name='numero_doc' value='$valor_txt'></div>";
							
	//echo "<div class='col-md-4'>";
	//echo "<div class='form-group has-info single-line'><label>Numero de consignacion</label> <input type='text' placeholder='Numero de consignacion' class='form-control' id='numero_doc' name='numero_doc' value='$ult_doc'></div>";
	//echo "</div>";	
	//echo "<div class='col-md-4'>";
	//echo "<div class='form-group has-info single-line'><label>Fecha:</label> <input type='text' placeholder='Fecha' class='datepick form-control' id='fecha' name='fecha' value='$fecha_actual'></div>";
	//echo "</div>";								
}	

//functions to load
if(!isset($_REQUEST['process'])){
	initial(); 
} 
//else {
if (isset($_REQUEST['process'])) {
	
	
	switch ($_REQUEST['process']) {
	case 'insert':
		insertar();
		break;
	case 'mostrar_datos_cliente':
		mostrar_datos_cliente();
		break;				
	case 'consultar_stock':
		consultar_stock();
		break;
	case 'cargar_empleados':
		cargar_empleados();
		break;
	case 'cargar_precios':
		cargar_precios();
		break;	
	case 'total_texto':
		total_texto();
		break;
	case 'print1':
		print1();
		break;	
	case 'print2':
		print2(); //Generacion de los datos de consignacion que se retornan para otro script que imprime!!!
		break;
	case 'mostrar_numfact':
		mostrar_numfact();
		break;	
	case 'reimprimir' :
		reimprimir();
		break;								
	}
	
 //}
}
?>
