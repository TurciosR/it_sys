<style type='text/css'>
    table.page_header {width: 100%;margin-left:53px;  margin-top: 25px;margin-bottom: 25px;  border:none; background-color: #FFFFFF; font-family:helvetica,serif;font-weight: bold; font-size: 14px;}
    table.page_footer {width: 100%; border: none; background-color: #A9A9A9;  padding: 2mm;color:#FFFFFF; font-family:helvetica,serif; font-weight:bold;}
    div.note {border: solid 1mm #DDDDDD;background-color: #EEEEEE; padding: 2mm; border-radius: 2mm; width: 100%; }
    ul.main { width: 95%; list-style-type: square; }
    ul.main li { padding-bottom: 2mm; }
    h1 { text-align: center; font-size: 20mm}
    h3 { text-align:right; font-size: 14px; color:#000080}
    table { vertical-align: middle; }
    tr    { vertical-align: middle; }
    p {margin: 0px 5px 0px 5px;}
    span {margin: 5px;}
    img { border: 1px #000000;}  
    table.headers_perpage{margin-top:20px; margin-left:0px; margin-right:150px;}
    /*table.headers_perpage.th{text-align:left;}*/
</style>

<?php
include("_core.php");
//Obtener valores por $_GET

$numero_doc = $_GET['numero_doc'];
$numero_docx = $numero_doc."_TRS";

$id_sucursal=$_SESSION['id_sucursal'];

$sql_empresa=_query("SELECT * FROM empresa");
$array_empresa=_fetch_array($sql_empresa);
$nombre_empresa=$array_empresa['nombre'];
$telefono=$array_empresa['telefono1'];

$id_usuario=$_SESSION["id_usuario"];	
$id_sucursal=$_SESSION['id_sucursal'];


//Obtener informacion de tabla traslado
$info_traslado="";
$sql_fact="SELECT * FROM traslado WHERE numero_doc='$numero_docx' AND id_sucursal='$id_sucursal'";
$result_fact=_query($sql_fact);
$row_fact=_fetch_array($result_fact);
$nrows_fact=_num_rows($result_fact);
if($nrows_fact>0){
		$id_cliente=$row_fact['id_cliente'];
		$id_traslado = $row_fact['id_traslado'];
		$fecha=$row_fact['fecha'];
		$total=$row_fact['total'];
		$id_sucursal_destino= $row_fact['id_sucursal_destino'];
		$id_sucursal_origen= $row_fact['id_sucursal'];
		$len_numero_doc=strlen($numero_doc)-4;
		$num_fact=substr($numero_doc,0,$len_numero_doc);
		$tipo_fact=substr($numero_doc,$len_numero_doc,4);
		
		//Datos del Cliente
		$sql="select * from cliente where id_cliente='$id_cliente' ";
		$result= _query($sql);
		$row1=_fetch_array($result);
		$nrow1=_num_rows($result);	
		$nombres=$row1['nombre']." ".$row1['apellido'];	 
		$dui=$row1['dui'];
		$nit=$row1['nit'];
		$direccion=$row1['direccion'];
		//sucursal destino
		
		$sql_sucursal_destino=_query("SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal_destino'");
	$array_sucursal_destino=_fetch_array($sql_sucursal_destino);
	$nombre_sucursal_destino=$array_sucursal_destino['descripcion'];
	
	$sql_sucursal_origen=_query("SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal_origen'");
	$array_sucursal_origen=_fetch_array($sql_sucursal_origen);
	$nombre_sucursal_origen=$array_sucursal_origen['descripcion'];
}		


$tr_header="
    <table align='center' class='headers_perpage' cellspacing='1' style='width: 85.5%; border: solid 1px black; background: #A9A9A9; text-align: center; font-size: 11pt; color:#FFFFFF; font-family:helvetica,serif;'>
        <tr>
            <th style='width:5%'>N&deg;</th>
            <th style='width:7%;text-align:center'>Id</th>
            <th style='width:28%;text-align:center'>Descripcion</th> 
            <th style='width:10%;text-align:center'>Enviado</th> 
            <th style='width:10%;text-align:center'>Recibido</th> 
            <th style='width:10%;text-align:center'>P. U.</th>
            <th style='width:15%;text-align:center'>Subt Env</th>
             <th style='width:15%;text-align:center'>Subt Rec</th>
        </tr>
    </table>";

  ?>
<page backtop="40mm" backbottom="40mm" backleft="15mm" backright="15mm" style="font-size: 12pt" backimgx="center" backimgy="bottom" backimgw="100%">
    <page_header>
		
        <table class="page_header" >
            <tr>
                <td colspan="3" style="width: 10%; color: #444444;">
                    <img style="width: 10%;" src="./img/variedades.png">
                </td>
            
            </tr>
             <tr>
                <td align=center style='width:90%;'><?php echo  $nombre_empresa. " TRASLADO DE " .$nombre_sucursal_origen?></td>
            </tr>
            <tr>
                <td align=center style='width:90%;'>Traslado No. <?php echo $numero_doc;?></td>
            </tr>
			<tr>
                <td align=center style='width:90%;'>SUCURSAL DESTINO:  <?php echo  $nombre_sucursal_destino;?></td>
            </tr>
            <tr>
                <td align=center style='width:90%;'>FECHA DE IMPRESION: <?php echo date('d-m-Y H:i:s');?></td>
            </tr>

        </table>
     <?php echo $tr_header?>   
    </page_header>
    
    <page_footer>
        <table class="page_footer">
        
            <tr>
                <td style="width: 40%; text-align: left;">
                    <?php echo $nombre_empresa?>
                </td>
                <td style="width: 30%; text-align: center">
                    P&aacute;gina [[page_cu]]/[[page_nb]]
                </td>
                <td style="width: 30%; text-align: right;">
                    Tel: <?php echo $telefono?>
                </td>
            </tr>
        </table>
    </page_footer>
<?php
$totales=0;

//Obtener informacion de tabla traslado_detalle y producto o servicio
		$sql_fact_det="SELECT  producto.id_producto, producto.descripcion,traslado_detalle.* 
		FROM traslado_detalle JOIN producto ON traslado_detalle.id_prod_serv=producto.id_producto
		WHERE  traslado_detalle.id_traslado='$id_traslado' AND  traslado_detalle.tipo_prod_serv='PRODUCTO' 
		UNION ALL
		SELECT  servicio.id_servicio, servicio.descripcion,traslado_detalle.* 
		FROM traslado_detalle JOIN servicio ON traslado_detalle.id_prod_serv=servicio.id_servicio
		WHERE  traslado_detalle.id_traslado='$id_traslado' AND  traslado_detalle.tipo_prod_serv='SERVICIO'
		";
		
		$result_fact_det=_query($sql_fact_det);
		$nrows_fact_det=_num_rows($result_fact_det);
		$total_final=0;
		$total_enviado=0;
		$total_recibido=0;
		$total_enviado_dinero=0;
		$total_recibido_dinero=0;
		$lineas=6;
		$cuantos=0;
		$n=0;
		for($i=0;$i<$nrows_fact_det;$i++){
			$row_fact_det=_fetch_array($result_fact_det);
			$id_producto =$row_fact_det['id_producto'];
			$descripcion =$row_fact_det['descripcion'];
			$id_traslado_detalle =$row_fact_det['id_traslado_detalle'];
			$id_prod_serv =$row_fact_det['id_prod_serv'];
			$enviado =$row_fact_det['enviado'];
			$recibido =$row_fact_det['recibido'];
			$precio_venta =$row_fact_det['precio_venta'];
			$subt =$row_fact_det['subtotal'];
			$id_empleado =$row_fact_det['id_empleado'];
			$tipo_prod_serv =$row_fact_det['tipo_prod_serv'];
			$n=$n+1;
			//linea a linea
			
			$subt_rec=$precio_venta*$recibido;
			$subt_env=$precio_venta*$enviado;
			$precio_unit=sprintf("%.2f",$precio_venta);
			$subtotal_env=sprintf("%.2f",$subt_env);
			$subtotal_rec=sprintf("%.2f",$subt_rec);
			
			
						
			//$info_traslado.=$esp_init.$enviado."   ".$descrip.$esp_col2.$precio_unit.$esp_col3.$subtotal."\n"; 	
			$cuantos=$cuantos+1;					     		
		

	

    echo "
    <table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>
        <tr>";
	    echo "<td style='width:5%'>".$n."</td>";
	    echo "<td style='width:7%'>".$id_producto."</td>";
        echo "<td style='width:28%;text-align:left'>".$descripcion."</td>";
        echo "<td style='width:10%;text-align:right'> ".$enviado."</td>";
        echo "<td style='width:10%;text-align:right'> ".$recibido."</td>";
        echo "<td style='width:10%;text-align:right'>$ ".$precio_unit."</td>";
        echo "<td style='width:15%;text-align:right'> $ ".$subtotal_env."</td>";
         echo "<td style='width:15%;text-align:right'> $ ".$subtotal_rec."</td>";
        echo "</tr>";
      
         echo "</table>";
	$total_enviado_dinero=$total_enviado_dinero+$subtotal_env;
	$total_recibido_dinero=$total_recibido_dinero+$subtotal_rec;
	$total_enviado=$enviado+$total_enviado;
	$total_recibido=$recibido+$total_recibido;
	
    }
    $total_final_rec=sprintf("%.2f",$total_recibido_dinero);
    $total_final_env=sprintf("%.2f",$total_enviado_dinero);
    echo "<table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>";
    echo "<tr>";
	echo "<td style='width:40%;text-align:right;font-weight:bold'>"." TOTALES "."</td>";
    echo "<td style='width:10%;text-align:right;font-weight:bold'>".$total_enviado."</td>";
    echo "<td style='width:10%;text-align:right;font-weight:bold'>".$total_recibido."</td>";
    echo "<td style='width:10%;text-align:right;font-weight:bold'>"." "."</td>";
    echo "<td style='width:15%;text-align:right;font-weight:bold'>$ ".$total_final_env."</td>";
     echo "<td style='width:15%;text-align:right;font-weight:bold'>$ ".$total_final_rec."</td>";
    echo "</tr>";
    echo "</table>";
  
   
?> 
</page>
