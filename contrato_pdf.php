<?php
error_reporting(E_ERROR | E_PARSE);
include('num2letras.php');
require("_core.php");
require('fpdf/fpdf.php');



$id_sucursal = $_SESSION["id_sucursal"];
$sql_empresa = "SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'";

$resultado_emp=_query($sql_empresa);
$row_emp=_fetch_array($resultado_emp);
$tel1 = $row_emp['telefono'];
$nit = $row_emp['nit'];
$nrc = $row_emp['nrc'];
$n_nrc = documento($nrc);
$razonsocial = $row_emp['razonsocial'];
$descripcion = utf8_decode($row_emp['descripcion']);
$giro = $row_emp['giro'];
$telefonos="TEL. ".$tel1;

$id_contrato = $_REQUEST["id_contrato"];
$sql_contrato = _query("SELECT c.*, cl.nombre as name_cliente,
                        cl.dui as c_dui,
                        cl.nit as c_nit,
                        cl.direccion,
                        cl.depto,
                        cl.profecion,
                        e.nombre as name_empleado,
                        e.dui as e_dui,
                        e.nit as nit_e
                        FROM contrato as c
                        JOIN clientes as cl ON c.id_cliente = cl.id_cliente
                        JOIN empleados as e ON c.id_empleado = e.id_empleado
                        WHERE id_contrato = '$id_contrato'");
$row = _fetch_array($sql_contrato);
$numero_doc = $row["numero_doc"];
$fecha = $row["fecha"];
$hora = $row["hora"];
$monto = $row["monto"];
$iva = $row["iva"];
$tipo = $row["tipo"];
$cobro = $row["cobro"];
$fecha_vence = $row["fecha_vence"];
$name_cliente = $row["name_cliente"];
$name_empleado = $row["name_empleado"];
$name_empleado = str_replace("Ing. ","", $name_empleado);
$dui = $row["e_dui"];
$c_dui = $row["c_dui"];
$nit_em = $row["nit_e"];
$nit_e = documento($nit_em);
$nit_ea = $row["c_nit"];
$nit_c = documento($nit_ea);
$direccion_cl = $row["direccion"];
$periodo = $row["periodo"];
$profecion = $row["profecion"];
$concepto = $row["concepto"];

$datetime1 = new DateTime($fecha);
$datetime2 = new DateTime($fecha_vence);
$interval = $datetime1->diff($datetime2);
$diff = $interval->m;
$fecha_detalle = "";
if($fecha_vence!="")
{
    list($a,$m,$d) = explode("-", $fecha_vence);
    $fecha_detalle_text= num2letras((int)$d)." de ".ucwords(Minu(meses($m)))." de ".num2letras($a);
}

$id_departamento = $row["depto"];
$sql_depto = _query("SELECT * FROM departamento WHERE id_departamento = '$id_departamento'");
$row_d = _fetch_array($sql_depto);
$name_depto = $row_d["nombre_departamento"];

$sql_lista = _query("SELECT * FROM contrato_detalle WHERE id_contrato = '$id_contrato'");
$cuenta = _num_rows($sql_lista);
$des = "";
if($cuenta > 0)
{
  while ($row_list = _fetch_array($sql_lista))
  {
    $id_ser_sis = $row_list["id_ser_sis"];
    $tipo_ser_sis = $row_list["tipo_ser_sis"];
    //$des .= $tipo_ser_sis;
    if($tipo_ser_sis == "SERVICIO")
    {
      $sql_detalle = _query("SELECT * FROM servicios WHERE id_servicio = '$id_ser_sis'");
      $row_ss = _fetch_array($sql_detalle);
      $descripcion1 = $row_ss["descripcion"];
    }
    if($tipo_ser_sis == "SISTEMA")
    {
      $sql_detalle1 = _query("SELECT * FROM sistema WHERE id_sistema = '$id_ser_sis'");
      $row_ss1 = _fetch_array($sql_detalle1);
      $descripcion1 = $row_ss1["nombre"];
    }
    $des .= $descripcion1." ";
  }
}
$des = $concepto;

$sql_cuotas = _query("SELECT * FROM cuota_contrato WHERE id_contrato = '$id_contrato'");
$cuenta_cuotas = _num_rows($sql_cuotas);
$c_text = num2letras($cuenta_cuotas);

$p_cuota = 0;
$v_cuota = 0;
$nn = 0;
if($cuenta_cuotas > 0)
{
  while ($r_cc = _fetch_array($sql_cuotas))
  {
    $monto_cuota = $r_cc['monto'];
    if($nn==0)
    {
      $p_cuota = round($monto_cuota, 2);
    }
    else if($nn > 0)
    {
      $v_cuota = round($monto_cuota,2);
    }
    $nn += 1;
  }
}

$monto_text = num2letras($monto);
$p_cuota = total_texto($p_cuota);
$v_cuota = total_texto($v_cuota);
$sql_clausula = _query("SELECT cc.* FROM contrato_clausulas AS cc WHERE cc.id_contrato = '$id_contrato'");
$cuenta_clausula = _num_rows($sql_clausula);
$cadena = "";

$rr = 1;
if($cuenta_clausula > 0)
{
  while($row_clausula = _fetch_array($sql_clausula))
  {
    $romano = a_romano($rr);
    $titulo = $row_clausula["titulo"];
    $descripcion_clausula = $row_clausula["descripcion"];

    $ex = str_replace("[DATOS]",$des, $descripcion_clausula);
    $ex = str_replace("[PRECIO]",Mayu($monto_text), $ex);
    $ex = str_replace("[PERIODO]", minu($periodo)."mente", $ex);
    $ex = str_replace("[DIRECCION]", $direccion_cl, $ex);
    $ex = str_replace("[FECHA_FIN]", $fecha_detalle_text, $ex);
    $ex = str_replace("[MESES]", Mayu(num2letras($diff)), $ex);
    $ex = str_replace("[N_CUOTAS]", Mayu(num2letras($cuenta_cuotas)), $ex);
    $ex = str_replace("[V_CUOTA]", Mayu($v_cuota), $ex);
    $ex = str_replace("[P_CUOTA]", Mayu($p_cuota), $ex);

    $cadena .= $romano.".- ".$titulo.": ".$ex." ";
    $rr += 1;
  }
}

$logo = "img/logo_sys.jpg";
$impress = "Impreso: ".date("d/m/Y");
$id_sucursal_asignada = $_REQUEST["sucursal"];
$title = $descripcion;
$titulo = "REPORTE DE TRASLADOS";
$empresa = "COMERCIAL LA CAMPIÑA";
if($fecha!="")
{
    list($a,$m,$d) = explode("-", $fecha);
    $fech = utf8_decode("a los ".num2letras((int)$d)." días del mes de ".Minu(meses($m))." del año ".num2letras($a));
}

class PDF extends FPDF
{
    var $a;
    var $b;
    var $c;
    var $d;
    var $e;
    var $f;
    // Cabecera de página\
    public function Header()
    {
      if($this->PageNo() != 1)
      {
        $this->SetFont('Latin','',12);

        $this->Image("img/logo_sys.png",152,10,50,18);
        //$this->Cell(185,5,"",1,1,'L');
      }
    }

    public function Footer()
    {
      if($this->PageNo() != 1)
      {
        // Posición: a 1,5 cm del final
        $this->SetY(-20);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página requiere $pdf->AliasNbPages();
        //utf8_decode() de php que convierte nuestros caracteres a ISO-8859-1
        $this-> Cell(40, 5, utf8_decode("3ra calle Oriente, Barrio el Calvario, n 203 bis."), 0, 1, 'L');
        $this-> Cell(40, 5, utf8_decode("Esquina opuesta al Pollo Campestre de Ex-Correos, San Miguel"), 0, 1, 'L');
        $this-> Cell(40, 5, utf8_decode("2613-7470"), 0, 1, 'L');
      }
    }
    public function setear($a,$b,$c,$d,$e,$f,$g)
    {
      # code...
      $this->a=$a;
      $this->b=$b;
      $this->c=$c;
      $this->d=$d;
      $this->e=$e;
      $this->f=$f;
      $this->g=$g;
    }
}

$pdf=new PDF('P','mm', 'Letter');


$pdf->SetMargins(10,5);
$pdf->SetTopMargin(35);
$pdf->SetLeftMargin(15);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true,20);
$pdf->AddFont("latin","","latin.php");
$pdf->AddFont("bolt","","latin_bolt.php");
$pdf->AddPage();

$pdf->SetFont('bolt','',20);
$pdf->Cell(185,10,utf8_decode(Mayu("DOCUMENTO AUTENTICADO DE:")),0,1,'C',0);
$pdf->SetFont('bolt','',15);
$pdf->MultiCell(185,5,utf8_decode(Mayu(utf8_decode($des))),0,'C',0);

$pdf->Ln(25);
$pdf->SetFont('bolt','',20);
$pdf->MultiCell(185,10,utf8_decode(Mayu("OTORGADO POR:")),0,'C',0);
$pdf->SetFont('bolt','',15);
$pdf->MultiCell(185,5,utf8_decode(Mayu(utf8_decode($name_cliente))),0,'C',0);

$pdf->Ln(25);
$pdf->SetFont('bolt','',20);
$pdf->MultiCell(185,10,utf8_decode(Mayu("A FAVOR DE:")),0,'C',0);
$pdf->SetFont('bolt','',15);
$pdf->MultiCell(185,5,utf8_decode(Mayu(utf8_decode($name_empleado))),0,'C',0);

$pdf->Ln(25);
$pdf->SetFont('bolt','',20);
$pdf->MultiCell(185,10,utf8_decode(Mayu("ANTE LOS OFICIOS DE:")),0,'C',0);
$pdf->SetFont('bolt','',15);
$pdf->MultiCell(185,5,utf8_decode(Mayu(utf8_decode("LICDA. MARIA MERCEDES LEMUS NOLASCO"))),0,'C',0);

$pdf->Ln(35);
$pdf->SetFont('bolt','',15);
$pdf->MultiCell(185,5,utf8_decode("AÑO: ".date('Y')),0,'C',0);

$pdf->Ln(25);
$pdf->SetFont('bolt','',15);
$pdf->MultiCell(185,5,utf8_decode("Tel: 7522-5609 y 7796-6902"),0,'C',0);



$pdf->setear($title,$empresa,$titulo,$fech,$n_sucursal,$id_traslado,$destino);
$pdf->SetMargins(10,5);
$pdf->SetTopMargin(35);
$pdf->SetLeftMargin(15);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true,20);
$pdf->AddFont("latin","","latin.php");
$pdf->AddFont("bolt","","latin_bolt.php");
$pdf->AddPage();

$pdf->SetFont('Latin','',10);
$contratante = Mayu(utf8_decode($name_empleado));
$n_dui = documento($dui);
$cl_dui = documento($c_dui);

$doc = utf8_decode($contratante." mayor de edad, Ingeniero en Sistemas Informáticos, del domicilio de la Ciudad y Departamento de San Miguel, con documento único de identidad Numero: ".$n_dui.", con Numero de Identidad Tributaria: ".$nit_e.", actuando en calidad de propietario de la empresa ".Mayu($descripcion)." con numero de registro Numero. ".$n_nrc.", quien en adelante se denominará EL CONTRATISTA, y ".Mayu(utf8_decode($name_cliente))
.", mayor de edad, ".$profecion.", del domicilio de la Ciudad de ".$direccion_cl.", del Departamento de ".$name_depto." con documento único de identidad Numero: ".$cl_dui.", con Numero de Identidad Tributaria: ".$nit_c.", y quien para los efectos del presente documento se denominará EL CONTRATANTE, acuerdan celebrar el presente CONTRATO DE PRESTACIÓN DE SERVICIOS, el cual se regirá por las siguientes cláusulas: ".
$cadena);
$pdf->MultiCell(185,5,$doc,0,'J',0);
$pdf->Cell(185,5,"",0,1,'J',0);
$pdf->MultiCell(185,5,"Las partes suscriben el presente documento en dos ejemplares, ".$fech,0,'J',0);
$pdf->Ln(40);
$pdf->Cell(185,5,"",0,1,'J',0);
$set_x = $pdf->GetX();
$set_y = $pdf->GetY()+30;

$pdf->Line($set_x, $set_y, 100, $set_y);
$pdf->Line(115, $set_y, 200, $set_y);
$pdf->SetY($set_y);
$pdf->Cell(85,5,utf8_decode($name_cliente),0,0,'C',0);
$pdf->Cell(15,5,utf8_decode(""),0,0,'C',0);
$pdf->Cell(85,5,utf8_decode($contratante),0,1,'C',0);
$pdf->Cell(85,5,utf8_decode("CONTRATANTE"),0,0,'C',0);
$pdf->Cell(15,5,utf8_decode(""),0,0,'C',0);
$pdf->Cell(85,5,utf8_decode("CONTRATISTA"),0,1,'C',0);

$pdf->Ln(10);
$pdf->MultiCell(185,5,utf8_decode("DOY FE: Que la firma que antecede es AUTENTICA, por haber sido puesta a mi presencia de su puño y letra por los señores LUDWIN ALDUVI HERNANDEZ VASQUEZ, mayor de edad, Ingeniero en Sistemas Informáticos, del domicilio de la Ciudad y Departamento de San Miguel, a quien no conozco pero lo identifico  por medio de su Documento Único de Identidad: Cero dos seis siete dos ocho cuatro nueve - ocho, con Numero de Identidad Tributaria: Uno dos cero cuatro - uno siete  uno cero ocho tres - uno cero uno - cero, y ".Mayu(utf8_decode($name_cliente)).", mayor de edad, ".$profecion.", del domicilio de la Ciudad de ".$direccion_cl.", departamento de ".$name_depto.", a quien no conozco pero lo identifico  por medio de su Documento Único de Identidad ".$cl_dui." San Miguel, ").$fech.".",0,'J',0);

$pdf->Ln(30);
$set_x = $pdf->GetX();
$set_y = $pdf->GetY();
$pdf->Line($set_x+50, $set_y, 150, $set_y);
$pdf->Cell(185,5,utf8_decode("Licda. María Mercedes Lemus Nolasco"),0,1,'C',0);
// $pdf->Cell(185,5,utf8_decode("CONTRATANTE"),0,0,'C',0);

if($tipo == "SIS")
{
    $sql_mod = _query("SELECT mc.id_modulo, mc.id_contrato, ms.id_modulo AS mod_m, ms.nombre, ms.descripcion
      FROM modulos_contrato AS mc
      JOIN modulos_sistema AS ms ON mc.id_modulo = ms.id_modulo
      WHERE id_contrato = '$id_contrato'");
    $cuenta_mod = _num_rows($sql_mod);
    if($cuenta_mod > 0)
    {
      $pdf->SetMargins(10,5);
      $pdf->SetTopMargin(35);
      $pdf->SetLeftMargin(15);
      $pdf->AliasNbPages();
      $pdf->SetAutoPageBreak(true,20);
      $pdf->AddFont("latin","","latin.php");
      $pdf->AddPage();
      $pdf->SetFont('Latin','',12);
      $pdf->Cell(185,5,utf8_decode("MODULOS DE SISTEMA"),0,1,'C',0);
      $pdf->Ln(5);
      $pdf->SetFont('Latin','',9);
      $pdf->MultiCell(190,5,utf8_decode("El presente contrato comprende como requerimientos funcionales  los cuales se describen a continuación. Cabe mencionar que dichos modulos están sujetos a cambios que el cliente solicite siempre y cuando sean los que se reflejan en el presente contrato."),0,'J',0);
      $pdf->Ln(5);
      // WWW.PCTNEW ORG y brujo
      while ($row_mod = _fetch_array($sql_mod))
      {
        $id_modulo = $row_mod["id_modulo"];
        $nombre = $row_mod["nombre"];
        $descripcion = $row_mod["descripcion"];

        $pdf->SetFont('bolt','',9);
        // $pdf->SetXY($set_x, $set_y+$mm);
        $pdf->Cell(25,5,utf8_decode($nombre),0,1,'L');
        $pdf->SetFont('Latin','',9);
        $pdf->MultiCell(180,5,utf8_decode($descripcion),0,'J',0);

        $sql_detalle = _query("SELECT * FROM modulos_sistema_detalle WHERE id_modulo_sistema = '$id_modulo'");
        $cuenta_det = _num_rows($sql_detalle);
        if($cuenta_det > 0)
        {
          while ($row_det = _fetch_array($sql_detalle))
          {
            $nombre_mod = $row_det["nombre"];
            $descripcion_mod = $row_det["descripcion"];
            $pdf->SetFont('bolt','',9);
            // $pdf->SetXY($set_x, $set_y+$mm+$rr);
            $pdf->Cell(30,5,utf8_decode(""),0,0,'C');
            // $pdf->SetXY($set_x+35, $set_y+$mm+$rr);
            $pdf->Cell(5,5,utf8_decode("»"),0,0,'L');
            $pdf->SetFont('Latin','',9);
            // $pdf->SetXY($set_x+40, $set_y+$mm+$rr);
            $pdf->Cell(110,5,utf8_decode($nombre_mod." ".$descripcion_mod),0,0,'L');
            // $pdf->SetXY($set_x+135, $set_y+$mm+$rr);
            $pdf->Cell(30,5,utf8_decode(""),0,0,'R');
            // $pdf->SetXY($set_x+165, $set_y+$mm+$rr);
            $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
          }
        }
      }

    }
}

ob_clean();
$pdf->Output("contrato.pdf","I");

function documento($dui)
{
  $cadena = $dui;
  $lista = '';
  $n = 1;
  for ($i=0; $i <strlen($cadena) ; $i++) {
    switch ($cadena[$i]) {
      case '-':
      $lista .= "- ";
        break;
      default:
        $n_text=num2letras($cadena[$i]);
        if(strlen($cadena) == $n)
        {
          $lista .= $n_text;
        }
        else
        {
          $lista .= $n_text." ";
        }
      break;
      }
    $n += 1;
  }
  return ($lista);
}

function total_texto($total)
{
  list($entero, $decimal)=explode('.', $total);
  $enteros_txt=num2letras($entero);
  $decimales_txt=num2letras($decimal);

  if ($entero>1) {
    $dolar=" dolares";
  } else {
    $dolar=" dolar";
  }
  $cadena_salida= $enteros_txt.$dolar." con ".$decimales_txt." centavos";
  return $cadena_salida;
}

function a_romano($integer, $upcase = true)
{
  $table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100,
                 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9,
                 'V'=>5, 'IV'=>4, 'I'=>1);
  $return = '';
  while($integer > 0)
  {
      foreach($table as $rom=>$arb)
      {
          if($integer >= $arb)
          {
              $integer -= $arb;
              $return .= $rom;
              break;
          }
      }
  }
  return $return;
}
