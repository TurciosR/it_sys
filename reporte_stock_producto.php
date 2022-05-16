<?php
include_once "_core.php";

function initial()
{
    $title='Reporte de Stock de Productos';
    $_PAGE = array();
    $_PAGE ['title'] = $title;
    $_PAGE ['links'] = null;
    $_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';

    $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

    include_once "header.php";
    include_once "main_menu.php";
    $id_sucursal=$_SESSION['id_sucursal'];
    //permiso del script
$id_user=$_SESSION["id_usuario"];
    $admin=$_SESSION["admin"];
    $uri = $_SERVER['SCRIPT_NAME'];
    $filename=get_name_script($uri);
    $links=permission_usr($id_user, $filename);
//permiso del script
if ($links!='NOT' || $admin=='1') {
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
  	<div class="col-lg-2"></div>
  </div>
  <div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5><?php echo $title; ?></h5>
                        </div>
                        <div class="ibox-content">
                              <!--form name="formulario" id="formulario" method='GET' action='reporte_mov_productos_pdf.php' target='_blank'-->
                              <div class="row">
                                <div class="col-md-3">
                                  <div class="form-group has-info single-line">
                                    <label>Existencias Minimas</label> 
                                    <input type="text" placeholder="" class="numeric form-control input" id="minimas" name="minimas" value="1">
                                </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="form-group has-info single-line">
                                    <label>Existencias Maximas</label>
                                    <input type="text" placeholder="" class="numeric form-control input" id="maximas" name="maximas" value="4">
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
                              
                              <div class="col-md-3"><br>
                                <button type="submit" id="btn_ac" class="btn btn-primary"><i class="fa fa-search"></i> Ver</button>
                                <button type="submit" id="print1" class="btn btn-primary"><i class="fa fa-file-pdf-o"></i> PDF</button>
                                <button type="submit" id="excel1" class="btn btn-primary"><i class="fa fa-check"></i> Excel</button>
                              </div>
                               <!--/form-->
                          </div>
                          <div class="row" id="res" hidden><br>
                                <div class="col-lg-12">
                                  <table class="table table-bordered table-striped table-hover">
                                    <thead id="encabezado">
                                     <tr id="sa">
                                        <th>N°</th>
                                        <th>Barcode</th>
                                        <th>Descripcion</th>
                                        <th>Estilo</th>
                                        <th>Color</th>
                                        <th>Talla</th>
                                        <th>Prec.</th>
                                        <th>% Desc.</th>
                                        <th>Existencia</th>
                                        <th>Valor</th>
                                      </tr>
                                      <tr id="ghe">
                                        <th>N°</th>
                                        <th>Barcode</th>
                                        <th>Descripcion</th>
                                        <th>Estilo</th>
                                        <th>Color</th>
                                        <th>Talla</th>
                                        <th>Prec.</th>
                                        <th>% Desc.</th>
                                        <th>Suc1</th>
                                        <th>Suc2</th>
                                        <th>Valor</th>
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
        var min = $("#minimas").val();
        var max = $("#maximas").val();
        var sucursal = $("#sucursal").val();
        $.ajax({
          type: 'POST',
          url: 'reporte_stock_producto.php',
          data: 'process=reporte&min='+min+'&max='+max+'&id_sucursal='+sucursal,
          dataType: 'JSON',
          success: function(datax)
          {
            if(datax.typeinfo == "Success")
            {
              $("#no-data").hide();
              $("#res").show();
              if(sucursal == "General")
              {
                $("#ghe").show();
                $("#sa").hide();
              }
              else
              {
                $("#ghe").hide();
                $("#sa").show();
              }
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
echo" <script type='text/javascript' src='js/funciones/funciones_stock.js'></script>";
} //permiso del script
else {
    echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
}
}
function reporte()
{
    $min = $_POST["min"];
    $max = $_POST["max"];
    $id_sucursal = $_POST["id_sucursal"];

    if($id_sucursal == "General")
    {
      $and = "";
      $sql = "SELECT productos.id_producto,productos.descripcion,productos.barcode,productos.talla,productos.estilo, productos.ultcosto, productos.descuento, colores.nombre, stock1.existencias AS suc1, stock2.existencias AS suc2
        FROM productos 
        LEFT JOIN stock AS stock1 
        ON stock1.id_producto=productos.id_producto 
        LEFT JOIN stock as stock2 
        ON stock2.id_producto=productos.id_producto
        JOIN colores ON productos.id_color=colores.id_color";
        $and.= " WHERE stock1.id_sucursal=1";
        if($max != "" && $max>0)
        {
            $and .= " AND stock1.existencias <= '$max'";
            if($min !="")
            {

              $and .= " AND stock1.existencias >= '$min'";
            }
        }
         else if($min !="")
        {

              $and .= " AND stock2.existencias >= '$min'";
        } 
        $and.=" AND stock2.id_sucursal=2";
        if($max != "" && $max>0)
        {
            $and .= " AND stock2.existencias <= '$max'";
            if($min !="")
            {

                $and .= " AND stock2.existencias >= '$min'";
            }
        }
        else if($min !="")
        {

            $and .= " AND stock2.existencias >= '$min'";
        }
        $sql.=$and." ORDER BY productos.descripcion ASC, productos.talla ASC, stock1.existencias ASC, stock2.existencias ASC LIMIT 100";
    }
    else
    { 
      $sql="SELECT productos.id_producto,productos.descripcion,productos.barcode,productos.talla,productos.estilo,colores.nombre, stock.existencias,productos.ultcosto, productos.descuento FROM productos,stock, colores WHERE productos.id_producto=stock.id_producto AND productos.id_color=colores.id_color";
      $sql .= " AND stock.id_sucursal='$id_sucursal'";
      $and = "";
      if($max != "" && $max>0)
      {
          $and .= " AND stock.existencias <= '$max'";
          if($min !="")
          {

            $and .= " AND stock.existencias >= '$min'";
          }
      }
      else if($min !="")
      {

          $and .= " AND stock.existencias >= '$min'";
      }
      $sql.=$and." ORDER BY productos.descripcion ASC, productos.talla ASC, stock.existencias ASC LIMIT 100";
    }
    $query = _query($sql);
    //echo $sql;
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
            $ultcosto = $row["ultcosto"];
            $descuento = $row["descuento"];
            $table.="<tr>
                    <td>".$n."</td>
                    <td>".$barcode."</td>
                    <td>".$descripcion."</td>
                    <td>".$estilo."</td>
                    <td>".$nombre."</td>
                    <td>".$talla."</td>
                    <td>".$ultcosto."</td>
                    <td>".$descuento."</td>
                    ";
                    if($id_sucursal == "General")
                    {
                      $suc1 = $row["suc1"];
                      $suc2 = $row["suc2"];
                      $valoo = ($suc1+$suc2) * $ultcosto;
                      $table.= "<td>".$suc1."</td>";
                      $table.= "<td>".$suc2."</td>";
                    }
                    else
                    {
                      $existencias = $row["existencias"];
                      $valoo = $existencias * $ultcosto;
                      $table.= "<td>".$existencias."</td>";
                    }
                    $table.= "<td>".$valoo."</td>";
                    $table.="</tr>";
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
if(!isset($_POST['process']))
{
    initial();
} 
else 
{
    if (isset($_POST['process']))
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
