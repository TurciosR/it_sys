<?php
require('fpdf/fpdf.php');
require('htmlparser.inc');

class PDF_HTML_Table extends FPDF
{
var $B;
var $I;
var $U;
var $HREF;

function PDF($orientation='P', $unit='mm', $format='Letter')
{
	//Call parent constructor
	$this->FPDF($orientation,$unit,$format);
	//Initialization
	$this->B=0;
	$this->I=0;
	$this->U=0;
	$this->HREF='';
}

function Header()
{
	$fechaprint2=date('d-m-Y');	
	$titulo_encabezado="REPORTE DE VENTAS DIARIAS POR RANGO DE FECHA";
	$texto_fecha='IMPRESO  EN: ';
    $this->SetFont('Arial','B',12);
	$this->MultiCell(180,5,$titulo_encabezado,0,'C',0); 
	$this->MultiCell(180,5,$texto_fecha.$fechaprint2,0,'C',0);
	// x,y, ancho, alto
	$this->Image('img/premier.png',15,5,15,15);
    // Line break
    $this->Ln(5);
}
function Footer()
{
	include('_core.php');

 //header("Content-type: application/pdf");
 
 //Obtener valores por $_GET
 
 //$fecha= $_GET['fecha_movimiento'];
 
 // Consultar la base de datos configuracion
 
 $sql_organizacion="SELECT id_empresa, nombre, razonsocial, direccion, telefono1,
  telefono2, website, email, nit, iva FROM empresa 
 ";   

 $resultado_org=_query($sql_organizacion);
 $num_rows = _num_rows($resultado_org);

						
 while($row_org=_fetch_array($resultado_org)){		
				$empresa=utf8_decode($row_org['nombre']);	
				$telefonos=$row_org['telefono1'].'   '.$row_org['telefono2'];				
 }
		//Obtener la fecha
		$fecha=getdate();
		$anho=$fecha['year'];
		$dias=$fecha['mday'];
		$mess=$fecha['mon'];
		$this->SetFont('Arial','B',10);
	    //Print current  page number and date
	    $pag=utf8_decode("Página: ");

$this->SetTextColor( 255, 255, 255 );
$this->SetFont('Arial','B',10);
$this->SetY(-20);
$this->SetFillColor(190,190,190);
$pag=utf8_decode("Página: ");
$esp=str_repeat(" ", 40);	
$this->Cell( 0, 10, $empresa.$esp.$pag.$this->PageNo().'/{nb}', 0, 0, 'L' ,'true'); 

$this->Cell(0,10,"Tel: ".$telefonos,0,0,'R');	
 //$this->Cell(0,10,$pag.$this->PageNo().'/{nb}',0,0,'C');	   	
}

function WriteHTML2($html)
{
	//HTML parser
	$html=str_replace("\n",' ',$html);
	$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	foreach($a as $i=>$e)
	{
		if($i%2==0)
		{
			//Text
			if($this->HREF)
				$this->PutLink($this->HREF,$e);
			else
				$this->Write(5,$e);
		}
		else
		{
			//Tag
			if($e[0]=='/')
				$this->CloseTag(strtoupper(substr($e,1)));
			else
			{
				//Extract attributes
				$a2=explode(' ',$e);
				$tag=strtoupper(array_shift($a2));
				$attr=array();
				foreach($a2 as $v)
				{
					if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
						$attr[strtoupper($a3[1])]=$a3[2];
				}
				$this->OpenTag($tag,$attr);
			}
		}
	}
}

function OpenTag($tag, $attr)
{
	//Opening tag
	if($tag=='B' || $tag=='I' || $tag=='U')
		$this->SetStyle($tag,true);
	if($tag=='A')
		$this->HREF=$attr['HREF'];
	if($tag=='BR')
		$this->Ln(5);
	if($tag=='P')
		$this->Ln(10);
}

function CloseTag($tag)
{
	//Closing tag
	if($tag=='B' || $tag=='I' || $tag=='U')
		$this->SetStyle($tag,false);
	if($tag=='A')
		$this->HREF='';
	if($tag=='P')
		$this->Ln(10);
}

function SetStyle($tag, $enable)
{
	//Modify style and select corresponding font
	$this->$tag+=($enable ? 1 : -1);
	$style='';
	foreach(array('B','I','U') as $s)
		if($this->$s>0)
			$style.=$s;
	$this->SetFont('',$style);
}

function PutLink($URL, $txt)
{
	//Put a hyperlink
	$this->SetTextColor(0,0,255);
	$this->SetStyle('U',true);
	$this->Write(5,$txt,$URL);
	$this->SetStyle('U',false);
	$this->SetTextColor(0);
}

function WriteTable($data, $w)
{
	$this->SetLineWidth(.3);
	$this->SetFillColor(255,255,255);
	$this->SetTextColor(0);
	$this->SetFont('Arial');
	//SetFont('Arial','',8);
	$this->SetFontSize(9);
	$this->SetLeftMargin(15);
	$nlineas=0;
	$lineas=0;
	foreach($data as $row)
	{
		$nb=0;
		//para hacer el corte de pagina con las lineas que va llevar por página
		$linea_fin=9;
		$lineas=$lineas+1;
		if(fmod($lineas,$linea_fin)==0){
					//$this->AddPage($this->CurOrientation);	
					$this->AddPage('P','letter');	
		}
					
		for($i=0;$i<count($row);$i++)
			$nb=max($nb,$this->NbLines($w[$i],trim($row[$i])));
			$h=5*$nb;

			for($i=0;$i<count($row);$i++)
			{
				
				$x=$this->GetX();
				$y=$this->GetY();	
			
				switch ($i){
					case 0://No. Corr
						$w[$i]=20;
						break;
					case 1: //
						$w[$i]=50;
						break;    
					case 2: //
						$w[$i]=50;
						break;
					case 3: 
						$w[$i]=50;
						break;
 
					                  
				}		 	
				if ($lineas==1 || $lineas==$linea_fin){
				//$this->SetFont('Arial','B');
				$this->SetFont('Arial','B');
				$this->SetFillColor(255,255,255);
				$this->SetTextColor( 0, 0, 0 );
				
				//$this->Rect($x,$y,$w[$i],$h);	
				$this->MultiCell($w[$i],5,trim($row[$i]),0,'L',1);	
				}
				if ($lineas==2 || $lineas==($linea_fin+1)){
				//$this->SetFont('Arial','B');
				$this->SetFont('Arial','B');
				$this->SetFillColor(190,190,190);
				$this->SetTextColor( 255, 255, 255 );
				$this->Rect($x,$y,$w[$i],$h);	
				$this->MultiCell($w[$i],5,trim($row[$i]),0,'L',1);	
				}
				if ($lineas>2 && $lineas!=$linea_fin  && $lineas!=($linea_fin+1)){
				//$this->SetFont('Arial','');
				$this->SetFont('Arial','');
				$this->SetFillColor(255,255,255);
				$this->SetTextColor( 0, 0, 0 );
				$this->Rect($x,$y,$w[$i],$h);	
				$this->MultiCell($w[$i],5,trim($row[$i]),0,'L');	
				}
				
					   
				
				//Put the position to the right of the cell
				$this->SetXY($x+$w[$i],$y);						
		}
		$this->Ln($h);	
	}
	
}
function WriteTableBK($data, $w)
{
	//Esta funcion es en respaldo a WriteTable por si falla
	$this->SetLineWidth(.3);
	$this->SetFillColor(255,255,255);
	$this->SetTextColor(0);
	$this->SetFont('');
	foreach($data as $row)
	{
		$nb=0;
		for($i=0;$i<count($row);$i++)
			$nb=max($nb,$this->NbLines($w[$i],trim($row[$i])));
		$h=5*$nb;
		
		for($i=0;$i<count($row);$i++)
		{
			$x=$this->GetX();
			$y=$this->GetY();
			$this->Rect($x,$y,$w[$i],$h);
			$this->MultiCell($w[$i],5,trim($row[$i]),0,'C');
			//Put the position to the right of the cell
			$this->SetXY($x+$w[$i],$y);					
		}
		$this->Ln($h);

	}
}
function NbLines($w, $txt)
{
	//Computes the number of lines a MultiCell of width w will take
	$cw=&$this->CurrentFont['cw'];
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);
	if($nb>0 && $s[$nb-1]=="\n")
		$nb--;
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$nl=1;
	while($i<$nb)
	{
		$c=$s[$i];
		if($c=="\n")
		{
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
			continue;
		}
		if($c==' ')
			$sep=$i;
		$l+=$cw[$c];
		if($l>$wmax)
		{
			if($sep==-1)
			{
				if($i==$j)
					$i++;
			}
			else
				$i=$sep+1;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
		}
		else
			$i++;
	}
	return $nl;
}

function CheckPageBreak($h)
{
	//If the height h would cause an overflow, add a new page immediately
	if($this->GetY()+$h>$this->PageBreakTrigger)
		$this->AddPage($this->CurOrientation);
	
		
}
function MyCheckPageBreak($valor)
{
	//If the height h would cause an overflow, add a new page immediately
	/*if($this->GetY()+$h>$this->PageBreakTrigger)
		$this->AddPage($this->CurOrientation);*/
	if($valor%3==0)
		$this->AddPage($this->CurOrientation);	
		
}

function ReplaceHTML($html)
{
	$html = str_replace( '<li>', "\n<br> - " , $html );
	$html = str_replace( '<LI>', "\n - " , $html );
	$html = str_replace( '</ul>', "\n\n" , $html );
	$html = str_replace( '<strong>', "<b>" , $html );
	$html = str_replace( '</strong>', "</b>" , $html );
	$html = str_replace( '&#160;', "\n" , $html );
	$html = str_replace( '&nbsp;', " " , $html );
	$html = str_replace( '&quot;', "\"" , $html ); 
	$html = str_replace( '&#39;', "'" , $html );
	
	//$html=strtolower_utf8($html);
	return $html;	
}
function strtolower_utf8($string){
  $convert_from = array(
    "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U",
    "V", "W", "X", "Y", "Z", "À", "Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï",
    "Ð", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "Ù", "Ú", "Û", "Ü", "Ý"
  );
 $convert_to = array(
    "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u",
    "v", "w", "x", "y", "z", "&agrave;", "&aacute;", "&acirc;", "&atilde;", "&auml;", "&aring;", "&aelig;", 
    "&ccedil;", "&egrave;", "&eacute;", "&ecirc;", "&euml;", "&igrave;", "&iacute;", "&icirc;", "&iuml;",
    "&eth;", "&ntilde;", "&ograve;", "&oacute;", "&ocirc;", "&otilde;", "&ouml;", "&oslash;", "&ugrave;", "&uacute;", "&ucirc;", "&uuml;",
     "&yacute;"
  );
  return str_replace($convert_from, $convert_to, $string);
} 

function ParseTable($Table)
{
	$_var='';
	$htmlText = $Table;
	$parser = new HtmlParser ($htmlText);
	while ($parser->parse())
	{
		if(strtolower($parser->iNodeName)=='table')
		{
			if($parser->iNodeType == NODE_TYPE_ENDELEMENT)
				$_var .='/::';
			else
				$_var .='::';
		}

		if(strtolower($parser->iNodeName)=='tr')
		{
			if($parser->iNodeType == NODE_TYPE_ENDELEMENT)
				$_var .='!-:'; //opening row
			else
				$_var .=':-!'; //closing row
		}
		if(strtolower($parser->iNodeName)=='td' && $parser->iNodeType == NODE_TYPE_ENDELEMENT)
		{
			$_var .='#,#';
		}
		if ($parser->iNodeName=='Text' && isset($parser->iNodeValue))
		{
			$_var .= $parser->iNodeValue;
		}
	}
	$elems = explode(':-!',str_replace('/','',str_replace('::','',str_replace('!-:','',$_var)))); //opening row
	foreach($elems as $key=>$value)
	{
		if(trim($value)!='')
		{
			$elems2 = explode('#,#',$value);
			array_pop($elems2);
			$data[] = $elems2;
		}
	}
	return $data;
}

function WriteHTML($html)
{
	$html = $this->ReplaceHTML($html);
	//Search for a table
	//$end = strpos(strtolower($html),'</table');
	$start = strpos(strtolower($html),'<table');
	$end = strpos(strtolower($html),'</table');
	if($start!==false && $end!==false)
	{
		$this->WriteHTML2(substr($html,0,$start));

		$tableVar = substr($html,$start,$end-$start);
		$tableData = $this->ParseTable($tableVar);
		for($i=1;$i<=count($tableData[0]);$i++)
		{
			if($this->CurOrientation=='L')
				$w[] = abs(108/(count($tableData[0])-1))+24;
			else
				$w[] = abs(108/(count($tableData[0])-1))+5;
			
		}
		
		$this->WriteTable($tableData,$w);

		//$this->WriteHTML2(substr($html,$end+8,strlen($html)-1));
		$this->WriteHTML2(substr($html,$end+8,strlen($html)-1).'<BR>');
	}
	else
	{
		$this->WriteHTML2($html);
	}
}

function WriteHTMLHeader($html)
{
	$html = $this->ReplaceHTML($html);
	//Search for a table
	$start = strpos(strtolower($html),'<table');
	$end = strpos(strtolower($html),'</table');
	if($start!==false && $end!==false)
	{
		$this->WriteHTML2(substr($html,0,$start).'<BR>');

		$tableVar = substr($html,$start,$end-$start);
		$tableData = $this->ParseTable($tableVar);
		for($i=1;$i<=count($tableData[0]);$i++)
		{
			if($this->CurOrientation=='L')
				$w[] = abs(108/(count($tableData[0])-1))+24;
			else
				$w[] = abs(108/(count($tableData[0])-1))+5;
		}
		$this->WriteTable2($tableData,$w);

		$this->WriteHTML2(substr($html,$end+8,strlen($html)-1).'<BR>');
	}
	else
	{
		$this->WriteHTML2($html);
	}
}

function WriteTable2($data, $w)
{
	$this->SetLineWidth(.3);
	$this->SetFillColor(255,255,255);
	$this->SetTextColor(0);
	$this->SetFont('Arial');
	$this->SetFontSize(9);
	$this->SetLeftMargin(10);
	foreach($data as $row)
	{
		$nb=0;
		for($i=0;$i<count($row);$i++)
			$nb=max($nb,$this->NbLines($w[$i],trim($row[$i])));
			$h=5*$nb;
			$this->CheckPageBreak($h);
		
			for($i=0;$i<count($row);$i++)
			{
				$x=$this->GetX();
				$y=$this->GetY();	
		 
				switch ($i){
					case 0:
						$w[$i]=10;
						break;
					case 1:
						$w[$i]=60;
						break;    
					case 2:
						$w[$i]=20;
						break;
					case 3:
						$w[$i]=20;
						break;
					case 4:
						$w[$i]=20;
						break;    
					case 5:
						$w[$i]=20;
						break;        
					case 6:
						$w[$i]=15;
						break;
					case 7:
						$w[$i]=15;
						break;   
					case 8:
						$w[$i]=20;
						break;    
					case 9:
						$w[$i]=20;
						break;        
					case 10:
						$w[$i]=20;
						break;
					case 11:
						$w[$i]=20;
						break;  
					case 12:
						$w[$i]=20;
						break;    
					case 13:
						$w[$i]=20;
						break;        
					case 14:
						$w[$i]=20;
						break;
					case 15:
						$w[$i]=20;
						break;
					case 16:
						$w[$i]=20;
						break;                       
				}		 	
				$this->Rect($x,$y,$w[$i],$h);	
				$this->MultiCell($w[$i],5,trim($row[$i]),0,'L');
		
		   
			//Put the position to the right of the cell
			$this->SetXY($x+$w[$i],$y);					
		}
		//$h=5*$nb;
		$this->Ln($h);
		//Added for minimize high because i duplicate before.
		
	}
}

//Add for Rows Array 03/09/2013
//para los array de columnas
var $widths;
var $aligns;

function SetWidths($w)
{
//Set the array of column widths
	$this->widths=$w;
}

function SetAligns($a)
{
	//Set the array of column alignments
	$this->aligns=$a;
}

function Row($data)
{
	//Calculate the height of the row
	$nb=0;
	for($i=0;$i<count($data);$i++)
		$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
	$h=5*$nb;
	//Issue a page break first if needed
	$this->CheckPageBreak($h);
	//Draw the cells of the row
	for($i=0;$i<count($data);$i++)
	{
		$w=$this->widths[$i];
		$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
		//Save the current position
		$x=$this->GetX();
		$y=$this->GetY();
		//Draw the border modificado para No Generar el borde
		 //$this->Rect($x,$y,$w,$h);
		//Print the text
		$this->MultiCell($w,5,$data[$i],0,$a);
		//Put the position to the right of the cell
		$this->SetXY($x+$w,$y);
	}
	//Go to the next line
	$this->Ln($h);
}
//end Add Rows Array 03/09/2013

}



?>
