<?php
include_once "_core.php";

function initial() {


	$_PAGE = array ();
	$_PAGE ['title'] = 'Reporte de Ventas por Fechas';
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/jqGrid/ui.jqgrid.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	//permiso del script
	if ($links!='NOT' || $admin=='1' ){
?>
<style>
/* Center the loader */
.sect
{
  height: 400px;
}
#loader {
  position: absolute;
  left: 50%;
  top: 50%;
  z-index: 1;
  width: 150px;
  height: 150px;
  margin: -75px 0 0 -75px;
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 120px;
  height: 120px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
}

@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Add animation to "page content" */
.animate-bottom {
  position: relative;
  -webkit-animation-name: animatebottom;
  -webkit-animation-duration: 1s;
  animation-name: animatebottom;
  animation-duration: 1s
}

@-webkit-keyframes animatebottom {
  from { bottom:-100px; opacity:0 }
  to { bottom:0px; opacity:1 }
}

@keyframes animatebottom {
  from{ bottom:-100px; opacity:0 }
  to{ bottom:0; opacity:1 }
}
</style>
            <div class="row wrapper border-bottom white-bg page-heading">

                <div class="col-lg-2">

                </div>
            </div>
        <div class="wrapper wrapper-content  animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Reporte de Ventas</h5>
                        </div>
                        <div class="ibox-content">
                              <form name="formulario" id="formulario" method='GET' action='reporte_aventas.php' target='_blank'>
                              <div class="row">
                                <div class="col-md-3">
                                  <div class="form-group has-info single-line">
                                    <label>Fecha Inicio</label>
                                    <input type="text" placeholder="Fecha Inicio" class="datepick form-control input" id="fini" name="fini" value="<?php echo date("Y-m-d");?>">
                                  </div>
                                </div>
                                 <div class="col-md-3">
                                  <div class="form-group has-info single-line">
                                    <label>Fecha Fin</label>
                                    <input type="text" placeholder="Fecha Fin" class="datepick form-control input" id="fin" name="fin" value="<?php echo date("Y-m-d");?>">
                                  </div>
                                </div>
                                 <div class="col-md-3">
                                  <div class="form-group has-info single-line">
                                    <label class="control-label">Cantidad de Productos</label>
                                    <input type="text" name="l" class="form-control numeric input" id="las" value="10">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="form-group has-info single-line">
                                    <label class="control-label">Sucursal</label>
                                    <select class="col-md-12 select" id="sucursal" name="sucursal">
                                        <option value="General">General</option>
                                        <?php
                                            $sqld = "SELECT * FROM sucursal";
                                            $resultd=_query($sqld);
                                            while($depto = _fetch_array($resultd))
                                            {
                                                echo "<option value='".$depto["id_sucursal"]."'";

                                                echo">".$depto["descripcion"]."</option>";
                                            }
                                        ?>
                                    </select>
                                    </div>
                                  </div>
                              </div>
                               <div class="row">
                                <div class="col-lg-10">
                                  
                                </div>
                                <div class="col-lg-2">
                                   <a id="btn_ac" class="btn btn-primary"><i class="fa fa-search"></i> Ver</a>
                                  <button type="submit" id="print1" class="btn btn-primary"><i class="fa fa-print"></i> PDF</button>
                                </div>
                                </div>
                               </form>
                               <div class="row" id="res" hidden><br>
                                <div class="col-lg-12">
                                  <table class="table table-bordered table-striped table-hover">
                                    <thead id="encabezado">
                                     <tr>
                                        <th>N°</th>
                                        <th>Barcode</th>
                                        <th>Descripcion</th>
                                        <th>Estilo</th>
                                        <th>Color</th>
                                        <th>Talla</th>
                                        <th>Precio</th>
                                        <th>Venta</th>
                                        <th>Existencia</th>
                                      </tr>
                                    </thead>
                                    <tbody id="resultado">
                                      
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                              <div class="row" id="no-data" hidden><br>
                                <div class="col-lg-12">
                                 <div class="alert alert-warning">
                                  No se encontraron resultados que coincidan con los criterios de busqueda
                                 </div>
                                </div>
                              </div>
                              <div class="row" style="display: none;" id="divh">
                              <div class="col-lg-12">
                                <div class="ibox float-e-margins">
                                  <div class="ibox-content">
                                    <section class="sect">
                                      <div id="loader">
                                      </div>
                                    </section>
                                  </div>
                                </div>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
<?php
include_once ("footer.php");
?>
   <script type="text/javascript">
      $(document).ready(function(){
        $("select").select2();
        $("#las").numeric({negative:false, decimal:false});
        reporte();
        $("#btn_ac").click(function(){
          reporte();
        });
        $("#sucursal").change(function(){
          reporte();
        });
        $(".input").blur(function(){
          reporte();
        });
      }); 
      function reporte()
      {
        $("#res").attr("style","display: none;");
        $("#divh").attr("style","display: block;");
        var fini = $("#fini").val();
        var fin = $("#fin").val();
        var l = $("#las").val();
        var sucursal = $("#sucursal").val();
        $.ajax({
          type: 'POST',
          url: 'reporte_ventas.php',
          data: 'process=reporte&fini='+fini+'&fin='+fin+'&l='+l+'&id_sucursal='+sucursal,
          dataType: 'JSON',
          success: function(datax)
          {
            if(datax.typeinfo == "Success")
            {
              $("#no-data").hide();
              $("#res").show();
              $("#resultado").html(datax.table);
              $("#res").attr("style","display: block;");
              $("#divh").attr("style","display: none;");
       
            }
            else
            {
              $("#res").hide();
              $("#no-data").show();
              $("#resultado").html("");
              $("#divh").attr("style","display: none;");
       
            }
          }
        });
      }
    </script>

<?php
	} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}
function reporte()
{
    $min = $_POST["l"];
    $fini = $_POST["fini"];
    $fin = $_POST["fin"];
    $id_sucursal = $_POST["id_sucursal"];

    $sql = "SELECT sum(df.cantidad) as venta, productos.descripcion,productos.barcode,productos.talla,productos.estilo,colores.nombre, stock.existencias,productos.precio1 FROM detalle_factura as df, factura as f, productos, stock, colores WHERE df.idtransace=f.idtransace AND productos.id_producto=df.id_producto AND productos.id_producto=stock.id_producto AND productos.id_color=colores.id_color ";
    if($id_sucursal == "General")
    {

    }
    else
    { 

        $sql .= " AND stock.id_sucursal='$id_sucursal' ";
    }
    $sql.=" AND f.fecha_doc BETWEEN '$fini' AND '$fin' GROUP BY df.id_producto ORDER BY venta DESC LIMIT $min";
    $query = _query($sql);
    if(_num_rows($query)>0)
    {
        $xdata["typeinfo"] = "Success";
        $table = "";
        $n = 1;
        while ($row = _fetch_array($query))
        {
            $barcode = $row["barcode"];
            $descripcion = $row["descripcion"];
            $nombre = $row["nombre"];
            $estilo = $row["estilo"];
            $talla = $row["talla"];
            $precio1 = $row["precio1"];
            $existencias = $row["existencias"];
            $venta = $row["venta"];
            $table.="<tr>
                    <td>".$n."</td>
                    <td>".$barcode."</td>
                    <td>".$descripcion."</td>
                    <td>".$estilo."</td>
                    <td>".$nombre."</td>
                    <td>".$talla."</td>
                    <td>".$precio1."</td>
                    <td>".$venta."</td>
                    <td>".$existencias."</td>
                    </tr>";
            $n++;
        }
        $xdata["table"] = $table;
    }
    else
    {
        $xdata["typeinfo"] = 'Error';
    }
    echo json_encode($xdata);
}
if(!isset($_POST['process'])){
	initial();
}
else
{
if(isset($_POST['process']))
{
    switch ($_POST['process']) 
    {
      case 'reporte':
	    reporte();
	    break;
    }
  }
}
?>
