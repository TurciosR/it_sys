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
</style>

<?php
include("_core.php");
  $fecha1=$_GET["fecha_inicio"];
  $fecha2=$_GET["fecha_fin"];
  $id_sucursal=$_GET["sucursal"];
  $tipo_proceso=$_GET["tipo"];

  $sql_empresa=_query("SELECT *FROM empresa");
  $array_empresa=_fetch_array($sql_empresa);
  $nombre_empresa=$array_empresa['nombre'];
  $telefono=$array_empresa['telefono1'];

  $sql_sucursal="SELECT sucursal.descripcion FROM sucursal WHERE sucursal.id_sucursal='$id_sucursal'";
  $result_sucursal=_query($sql_sucursal);
  $row_sucursal=_fetch_array($result_sucursal);
  $nombre_sucursal=$row_sucursal["descripcion"];

  $sql="SELECT fecha_mov,proveedor.nombre_proveedor,proveedor.nit,proveedor.telefono1,numero_doc,ingreso,egreso,caja_chica_mov.observaciones FROM caja_chica_mov,proveedor WHERE caja_chica_mov.id_proveedor=proveedor.id_proveedor and tipo_proceso='$tipo_proceso' and DATE(fecha_mov) BETWEEN '$fecha1' AND '$fecha2' and caja_chica_mov.id_sucursal='$id_sucursal'";
  if($tipo_proceso=="ENTRADA")
    {
        $clausula="ingreso";
        $label="INGRESOS";
    }
  if($tipo_proceso=="SALIDA")
    {
        $clausula="egreso";
        $label="EGRESOS";
    }
    if($tipo_proceso=="CONSOLIDADO")
    {
        $label="CONSOLIDADO";
    }


  ?>
<page backtop="40mm" backbottom="40mm" backleft="15mm" backright="15mm" style="font-size: 12pt" backimgx="center" backimgy="bottom" backimgw="100%">
    <page_header>

        <table class="page_header" >
            <tr>
                <td colspan="3" style="width: 10%; color: #444444;">
                    <img style="width: 10%;" src="./img/premier.png">
                </td>

            </tr>
            <tr>
                <td align=center style='width:90%;'>REPORTE CAJA CHICA <?php echo $nombre_sucursal?></td>
            </tr>
             <tr>
                <td align=center style='width:90%;'>TIPO REPORTE: <?php echo $label?></td>
            </tr>
            <tr>
                <td align=center style='width:90%;'>FECHA DE IMPRESION: <?php echo date('d-m-Y H:i:s');?></td>
            </tr>

        </table>
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

    if($tipo_proceso!="CONSOLIDADO")
    {
    $ingreso_egreso=_query("SELECT SUM($clausula) as total_ingresos FROM caja_chica_mov,proveedor WHERE caja_chica_mov.id_proveedor=proveedor.id_proveedor and tipo_proceso='$tipo_proceso' and DATE(fecha_mov) BETWEEN '$fecha1' AND '$fecha2' and caja_chica_mov.id_sucursal='$id_sucursal'");
    $arreglo_ingreso_egreso=_fetch_array($ingreso_egreso);
    $total_ingresos_egresos=$arreglo_ingreso_egreso['total_ingresos'];

    echo "
    <table align='center' cellspacing='1' style='width: 100%; border: solid 1px black; background: #A9A9A9; text-align: center; font-size: 11pt; color:#FFFFFF; font-family:helvetica,serif;'>
        <tr>
            <th style='width:5%'>N&deg;</th>
            <th style='width:10%;text-align:left'>Fecha</th>
            <th style='width:20%;text-align:left'>Proveedor</th>
            <th style='width: 15%'>NIT</th>
            <th style='width: 15%'>N Documento</th>
            <th style='width: 10%'>Monto</th>
            <th style='width: 25%'>Observaciones</th>
        </tr>
    </table>";


    $exec_query=_query($sql);

	$i=1;
	while($row = _fetch_array($exec_query))
    {
    $fecha_mov=$row['fecha_mov'];
    $proveedor=$row['nombre_proveedor'];
    $nit=$row['nit'];
    $observaciones=$row['observaciones'];
    $numero_doc=$row['numero_doc'];
    if($clausula=="ingreso")
    {
      $monto=$row['ingreso'];
    }
    if($clausula=="egreso")
    {
      $monto=$row['egreso'];
    }


    echo "
    <table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>

        <tr>";
	    echo "<td style='width:5%'>$i</td>";
        echo "<td style='width:10%;text-align:left'>".$fecha_mov."</td>";
        echo "<td style='width:20%;text-align:left'>".$proveedor."</td>";
        echo "<td style='width:15%'>$nit</td>";
        echo "<td style='width:15%'>$numero_doc</td>";
        echo "<td style='width:10%'>"."$ ".$monto."</td>";
        echo "<td style='width:25%'>$observaciones</td>
        </tr>";
        echo "</table>";

	$i++;
    }


    echo "
    <br><br>
    <table border='0' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>

        <tr>

        <td style='width:100%;text-align:left;'>Total de: <b>".$label."</b> = $ ".$total_ingresos_egresos."</td>

        </tr>
    </table>";
 }

 if($tipo_proceso=="CONSOLIDADO")
 {
    $sql="SELECT caja_chica_mov.ingreso,caja_chica_mov.egreso,caja_chica_mov.fecha_mov,caja_chica_mov.numero_doc,caja_chica_mov.observaciones,proveedor.nombre_proveedor,proveedor.nit,caja_chica_mov.tipo_doc,caja_chica_mov.tipo_proceso FROM caja_chica_mov,proveedor WHERE caja_chica_mov.id_proveedor=proveedor.id_proveedor and caja_chica_mov.id_sucursal='$id_sucursal'";
    $result=_query($sql);
    $count=_num_rows($result);

    $ingreso=_query("SELECT SUM(ingreso) as total_ingresos FROM caja_chica_mov,proveedor WHERE caja_chica_mov.id_proveedor=proveedor.id_proveedor and tipo_proceso='ENTRADA' and DATE(fecha_mov) BETWEEN '$fecha1' AND '$fecha2'");
    $arreglo_ingreso=_fetch_array($ingreso);
    $total_ingresos=$arreglo_ingreso['total_ingresos'];

    $egreso=_query("SELECT SUM(egreso) as total_egresos FROM caja_chica_mov,proveedor WHERE caja_chica_mov.id_proveedor=proveedor.id_proveedor and tipo_proceso='SALIDA' and DATE(fecha_mov) BETWEEN '$fecha1' AND '$fecha2' and caja_chica_mov.id_sucursal='$id_sucursal'");
    $arreglo_egreso=_fetch_array($egreso);
    $total_egresos=$arreglo_egreso['total_egresos'];
    $saldo_general=$total_ingresos-$total_egresos;

    echo "
    <table align='center' cellspacing='1' style='width: 100%; border: solid 1px black; background: #A9A9A9; text-align: center; font-size: 11pt; color:#FFFFFF; font-family:helvetica,serif;'>
        <tr>
            <th style='width:5%'>N&deg;</th>
            <th style='width:10%;text-align:left'>Proveedor</th>
            <th style='width:10%;text-align:left'>Tipo Doc</th>
            <th style='width: 15%'>NIT</th>
            <th style='width: 10%'>Ingreso</th>
            <th style='width: 10%'>Egreso</th>
            <th style='width: 10%'>Fecha</th>
            <th style='width: 10%'>N DOc</th>
            <th style='width: 20%'>Observaciones</th>
        </tr>
    </table>";


    $exec_query=_query($sql);

    $i=1;
    while($row = _fetch_array($exec_query))
    {
    $proveedor=$row['nombre_proveedor'];
    $tipo_doc=$row['tipo_doc'];
    $nit=$row['nit'];
    $ingreso=$row['ingreso'];
    $egreso=$row['egreso'];
    $fecha=$row['fecha_mov'];
    $numero_doc=$row['numero_doc'];
    $observaciones=$row['observaciones'];



    echo "
    <table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>

        <tr>";
        echo "<td style='width:5%'>$i</td>";
        echo "<td style='width:10%;text-align:left'>".$proveedor."</td>";
        echo "<td style='width:10%;text-align:left'>".$tipo_doc."</td>";
        echo "<td style='width:15%'>$nit</td>";
        echo "<td style='width:10%'>$ingreso</td>";
        echo "<td style='width:10%'>$egreso</td>";
        echo "<td style='width:10%'>$fecha</td>";
        echo "<td style='width:10%'>$numero_doc</td>";
        echo "<td style='width:20%'>$observaciones</td>
        </tr>";
        echo "</table>";

    $i++;
    }


    echo "
    <br><br>
    <table border='0' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>
        <tr>
            <td style='width:100%;text-align:left;'>Total de: <b>Ingresos</b> = $ ".$total_ingresos."</td>
        </tr>
         <tr>
            <td style='width:100%;text-align:left;'>Total de: <b>Egresos</b> = $ ".$total_egresos."</td>
        </tr>
        <tr>
            <td style='width:100%;text-align:left;'><b>Saldo</b> = $ ".$saldo_general."</td>
        </tr>
    </table>";




 }

?>
</page>
