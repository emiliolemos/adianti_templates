<?php

use Adianti\Widget\Dialog\TMessage;

define ('IMG_SIZE','44px');

class MYTCPDF extends TCPDF
{

    private $grid = false;
	private $snCabecalho;
	private $borda;
	private $arrayCabecalho;
	private $arrayTit;
	private $arrayTit2;
	private $arrayLarg;
	private $geradoEm;
	private $carimbo;
	private $exibeDadosRodape;
	private $exibeUsuarioRodape;
	private $headerImage;
	private $backgroundColor;	

	private $empresa;
	private $sistema;
	private $anobase;
	private $periodo;

	private $cabecalhoColunas; // String que irá conter os tiitulos das colunas
	private $alinhamentoColunas; // Alinhamentos das colunas
	private $larguraColunas; // Larguras das colunas

	private $titulos; // titulos do cabecalho
	private $msg_rodape;

	private $saida_html;

	public $display_pagination_footer; // mostra paginacao no rodape
	private $html_header  = '';
	private $zebrado;
	private $flag_background;

	// Resolvi simplificar o construtor pra receber apenas 2 parametros. 
	// 
	// public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false) 
	public function __construct($orientation='P', $format='A4') 
    {

		parent::__construct($orientation, 'mm', $format) ;

		$this->display_pagination_footer = true;

		$this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);	
		
		$this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));		
		$this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $this->setNomeEmpresa('');
		$this->setNomeSistema('');
        $this->setTitulos(['']);
        $this->setAnobase('');
        $this->setPeriodo('');		

		$this->cabecalhoColunas = '';
		$this->larguraColunas = [];
		$this->alinhamentoColunas = [];
		$this->saida_html = '';
		$this->msg_rodape = '';
		$this->zebrado = false;
		$this->flag_background = false;

		$this->setHeaderConfig();        		


    }

    public function Outputf() 
    {

            $this->deleteOldFiles();
            $output = 'tmp_'.uniqid().'.pdf';
            $folder_file = getcwd().'/app/output/'.$output; // caminho absoluto
            $this->Output($folder_file, 'F');
			usleep(1000); // um pequeno delay de um milisegundo

            return 'app/output/'.$output;

    }

    private function deleteOldFiles($tempo=3600)
    {
        $dir = getcwd()."/app/output/";
        /*** percorrendo todos os arquivos da pasta ***/
        foreach (glob($dir."*.pdf") as $file) {
            /*** 3600  = 1hora, 86400=24horas ***/
            if(time() - filectime($file) > $tempo) 
            {
                unlink($file);
            }
        }
    }

		
    function Header(){

		if (!$this->headerImage)
		{
			$this->headerImage = getcwd().'/app/images/default.png';
		}
		

		$geradoEm = $this->geradoEm ? 'Gerado em: ' . date("d/m/Y, h:i:s") : '';
		$this->SetFont('courier', 'B', 8);
		$ct = count($this->titulos);
		$cnt = 1 + $ct;

		 $src = '';
		// Caso 1: se for URL
		if (filter_var($this->headerImage, FILTER_VALIDATE_URL)) {
			$src = $this->headerImage;

		// Caso 2: se for arquivo local existente
		} elseif (file_exists($this->headerImage)) {
			// Detecta o MIME do arquivo
			$mime = mime_content_type($this->headerImage);
			$data = file_get_contents($this->headerImage);
			$src  = 'data:' . $mime . ';base64,@' . base64_encode($data);

		// Caso 3: assume que já é uma string Base64
		} else {
			$src = 'data:image/png;base64,' . $this->headerImage;
		}
		if (empty($this->html_header)) :
			$html = '<table style="width:100%; border-collapse: collapse; font-family: courier; font-size: 8; font-weight: bold;border-bottom: .5px solid #000;border-top: .5px solid #000;" border="0"; cellpadding="1">
						<tbody>							
							<tr>
								<td width="8%" rowspan="' . $cnt . '" style="padding:5px;">
									<img 
										src="' . $src . '" 
										alt="" 
										width="' . IMG_SIZE . '" 
										height="' . IMG_SIZE . '" 
									/>
								</td>
								<td width="67%" style="text-align: left;">'.$this->empresa.'</td> 
								<td width="25%" style="text-align: right;">'.$this->anobase.'</td>
							</tr>
						';
			$cnt=1;
			// imprime no maximo 3 linhas de titulo
			foreach ($this->titulos as $item) {
				if ($cnt < 6) {
					$html .= '<tr>';
					$html .= '<td width="67%" style="text-align: left;">'.$item.'</td> ';
					switch ($cnt)
					{
						case 1 : 
								$html .= '<td width="25%" style="text-align: right;">'.$this->periodo.'</td>'; break;
						default : 
								$html .= '<td width="25%" style="text-align: right;"></td>';				
					}
					$html .= '</tr>';
				}
				$cnt++;

			}
			$html .= '
			</tbody>
			</table>	
			';			

		else :
			// $html = '<hr><br>'.$this->html_header;
			$html = $this->html_header;

		endif;

		


		$this->writeHTML($html, false, false, false, false);

		if (!empty($this->cabecalhoColunas)) {
			$this->SetFillColor(225,225,225);
			$table = '<table style="width:100%; border-collapse: collapse; font-family: courier; font-size: 8; font-weight: bold;border-bottom: .5px solid #000;border-top: .5px solid #000;" border="0"; cellpadding="1";>
			<tbody>							
				<tr>
					'.$this->cabecalhoColunas.'					
				</tr>
			</tbody>
			</table>
			';

			$this->writeHTML($table, true, true, true, false, '');
			// $this->Ln();

		}
		


    }
	
	//Rodape
    public function Footer(){		
        // Posicionado a 15mm do final da pagina
        // $this->SetY(-15);
        // Tipo de fonte
        $this->SetFont('courier', 'I', 8);
		
		
		$this->SetY(-10); 
		$this->writeHTML("<hr>");
		$this->SetY(-12);
		
		$this->Cell(0, 10, $this->msg_rodape, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        // Numero da Pagina
		if ($this->display_pagination_footer)
			$this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		
    }


	public function openTable($table_style='', $style='')
	{
		
		if ($table_style == '')
		{
			$this->saida_html .= '<table style="width:100%; border-collapse: collapse; font-family: Helvetica; font-size: 7pt; border-bottom: 0px solid #fff;border-top: 0px solid #000;" border="0"; cellpadding="0.55px";>';
		} 
		else
		{
			$this->saida_html .= $table_style;
		}

		if (!empty($style))
			$this->saida_html .= '<table style="'.$style.'">';
	}

	public function closeTable()
	{
		$this->saida_html.= '</table>';
	}

	public function imprimeLinhaTable(
		$arrayLinha=[], 
		$larguras=[],
		$alinhamentos=[],
		$fillBackground=false, 
		$is_title=false, 
		$negrito=false	,
		$rowspan=[],
		$colspan=[],
		$opcoes=NULL		


	)

	{

		// $borderStyle = '.5px solid #000';

		// switch($border) {
		// 		case 1://bordas nas laterais
		// 		$border = "border-left:$borderStyle;border-right:$borderStyle;";
		// 		break;
		// 		case 2://bordas no topo e fundo
		// 		$border = "border-top:$borderStyle;border-bottom:$borderStyle;";
		// 		break;
		// 		case 3://borda completa
		// 		$border = "border:$borderStyle;";
		// 		break;
		// 		case 4://sem borda no topo
		// 		$border = "border-left:$borderStyle; border-right:$borderStyle; border-bottom:$borderStyle;";
		// 		break;
		// 		case 5://sem borda no fundo
		// 		$border = "border-left:$borderStyle; border-right:$borderStyle; border-top:$borderStyle;";
		// 		break;
		// 		default:
		// 		$border = 'border:none;';
		// }

		
		$_html 			 = '';
		if (empty($arrayLinha))
		{
			$arrayLinha 	= [''];
			$larguras		= [100];
			$alinhamentos	= ['c'];
		}

		$backgroundColor = "background-color:#fff;";
		$fontweight 	 = "font-weight: normal;";
		$bottom_line     = "";
		$top_line = "";

		if ($this->zebrado)
		{
			$fillBackground = ($this->flag_background);
			$this->flag_background = !$this->flag_background;
		}


		if ($is_title ) {
			// $backgroundColor = "background-color:#E2E4E5;"; //"background-color:#cbe6f2;";
			$fontweight = "font-weight:bold;";
		}
		if ($fillBackground) {
			$backgroundColor = $opcoes['background-color'] ?? "background-color:#E2E4E5;"; //"background-color:#cbe6f2;";
		}

		if ($is_title)
		{
			$bottom_line = $opcoes['border-bottom'] ?? "border-bottom:0.5pt solid black;";
			$top_line    = $opcoes['border-top'] ?? "border-top:0.5pt solid black;";

		}
		else {

			$bottom_line = $opcoes['border-bottom'] ?? '';
			$top_line    = $opcoes['border-top']    ?? '';
		}

		if ($negrito) {
			$fontweight = "font-weight:bold;";
		}

		$td_ou_th = $is_title ? 'th' : 'td';
		$thead = $is_title ? '<thead>' : '';

		$_html .= '
		'.$thead.'
		<tr style="'.$backgroundColor.$fontweight.'">
		';

		if (empty($larguras)) {
			$larguras = $this->larguraColunas;
		}
		if (empty($alinhamentos)) {
			$alinhamentos = $this->alinhamentoColunas;
		}

		if (count($larguras) == count($arrayLinha)) {

			$i=0;
			for ($i=0; $i<count($arrayLinha);$i++) {

				$align = $alinhamentos[$i];
				$largura = $larguras[$i];
				$valor   = $arrayLinha[$i];

				$rowspanStyle = (($rowspan[$i] ?? 0) > 0) ? ' rowspan="'.$rowspan[$i].'" ' : '';
				$colspanStyle = (($colspan[$i] ?? 0) > 0) ? ' colspan="'.$colspan[$i].'" ' : '';				

				$_border = $opcoes['border_style'] ?? $bottom_line.$top_line;

				$_html .= '
				<'.$td_ou_th. $rowspanStyle . $colspanStyle . ' style="text-align:'.$align.'; width: '.$largura.'%;'.$_border.'">'
					.$valor.
				'</'.$td_ou_th.'>';
			}
		}
		$fecha_thead = $is_title ? '</thead>' : '';
		$_html .= '
		'.$fecha_thead.'
		</tr>
		';	
		$this->saida_html .= $_html;



	}

	public function setHeaderConfig()
	{

        $this->SetAutoPageBreak(TRUE, 12);

        // set header and footer fonts
        $this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $this->SetFont('helvetica','',8);

        $this->setFooterData(array('courier', '', 0), array('courier', '', 0));

        // set margins
        $this->SetMargins(10, 31, 10);
        $this->SetHeaderMargin(9);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);
    
        $this->SetFillColor(240, 240, 240);
    

	}

	public function setHeaderImage($img)
	{
		if (!empty($img))
			$this->headerImage = $img;
	}


	public function setNomeSistema($sistema='SISTEMA')
	{
		$this->sistema = $sistema;
	}
	public function setNomeEmpresa($empresa='EMPRESA MODELO')
	{
		$this->empresa = $empresa;
	}
	public function setAnobase($anobase='9999')
	{
		$this->anobase = $anobase;
	}

	public function setTitulos($_titulos = ['TITULO DO RELATORIO'])
	{
		$this->titulos = $_titulos;
	}

	public function setPeriodo($periodo = '')
	{
		$this->periodo = $periodo;
	}

	public function addColTitle($titulo, $width=10, $align='')
	{
		$align=strtoupper(substr($align,0,1));
	
		switch (strtoupper($align))
		{
			case 'R' : $align= 'right';break;
			case 'L' : $align= 'left' ;break;
			case 'C' : $align= 'center';break;
			default:
				$align='left';
		}

		$this->cabecalhoColunas .= "<td style=\"text-align:$align; width:{$width}%;\">{$titulo}</td>";
		

	}

	private function addColumnWidth($value)
	{
		$this->larguraColunas[] = "{$value}";
	}
	private function addColumnAlign($value='L')
	{
		$align = strtoupper(mb_substr($value,0,1));
		switch ($align)
		{
			case 'R' : $align = 'right'		; break;
			case 'C' : $align = 'center'	; break;
			case 'L' : $align = 'left'		; break;
			case 'J' : $align = 'justify' 	; break;
			default : $align = 'left';
		}

		$this->alinhamentoColunas[] = "{$align}";
	}


	public function setLarguras($values=[])
	{
		$this->larguraColunas=[];
		foreach ($values as $v) {
			$this->addColumnWidth($v);
		}
	}

	public function setAlinhamentos($values=[])
	{
		$this->alinhamentoColunas=[];
		foreach ($values as $v) {
			$this->addColumnAlign($v);
		}
	}

	public function setCabecalhos($_titulos=[], $_larguras=[], $_alinhamentos=[])
	{

		if (empty($_larguras)) 
		{
			$_larguras = $this->larguraColunas;
		}
		if (empty($_alinhamentos))
		{
			$_alinhamentos = $this->alinhamentoColunas;
		}


		for ($i=0; $i<count($_titulos); $i++)
		{
			$this->addColTitle($_titulos[$i], $_larguras[$i], $_alinhamentos[$i] );

		}


	}

	public function clearCabecalho()
	{
		$this->cabecalhoColunas = '';
		$this->larguraColunas = [];
		$this->alinhamentoColunas = [];

	}

	public function htmlClear()
	{
		$this->saida_html = '';
	}

	public function imprimeHtml()
	{

		$this->writeHTML($this->saida_html);
		$this->htmlClear();
	}

	public function hr()
	{
		return '<hr>';
	}

	public function h1($txt)
	{
		return "<h1>$txt</h1>";
	}

	public function h3($txt)
	{
		return "<h3>$txt</h3>";
	}

	public function h5($txt)
	{
		return "<h5>$txt</h5>";
	}

	public function imprimeTexto($txt)
	{
		$this->saida_html .= $txt ; 
	}

	public function newLine()
	{
		$this->saida_html .= "<br>"; 
	}

	// substitui espaco por &nbsp
	public function sp_to_nbsp($texto='')
	{

		return (empty($texto)) ? '' : str_replace(' ','&nbsp;', $texto);
	}

	// Retornar indentacao
	public function tab_to_sbsp($texto, $qtdTabs)
	{
		$tamanhoDaIndentacao = 4;
		$tabs = str_repeat("&nbsp;",$qtdTabs*$tamanhoDaIndentacao);
		return $tabs.$texto;		
	}

    public function geraPDFImprimeLinha(
						$arrayLinha, 
                        $larguras, 
                        $bold=false, 
                        $fillBackground=false, 
                        $border=0, 
                        $arrayAlign=[], 
                        $rowspan=[], 
                        $colspan=[]
                        )
    {

        $html = '';
        $backgroundColor = $fillBackground ? "background-color: #eee;" : '';
        $borderStyle = '.5px solid #000';
        
        switch($border) {
                case 1://bordas nas laterais
                $border = "border-left:$borderStyle;border-right:$borderStyle;";
                break;
                case 2://bordas no topo e fundo
                $border = "border-top:$borderStyle;border-bottom:$borderStyle;";
                break;
                case 3://borda completa
                $border = "border:$borderStyle;";
                break;
                case 4://sem borda no topo
                $border = "border-left:$borderStyle; border-right:$borderStyle; border-bottom:$borderStyle;";
                break;
                case 5://sem borda no fundo
                $border = "border-left:$borderStyle; border-right:$borderStyle; border-top:$borderStyle;";
                break;
                default:
                $border = 'border:none;';
        }
        
        $fontweight = $bold ? "font-weight:bold;" : '';
        
        $html .= '
        <tr style="'.$backgroundColor.$fontweight.'">
        ';
        
        if (count($larguras) === count($arrayLinha)) {
            foreach ($arrayLinha as $index => $valor) {
                
                $align = 'left';
                if (is_numeric($valor)) {
                    //if (is_double($valor)) {
                        // $valor = round($valor,2) !== 0.00 ? number_format($valor, 2, ',', '.') : '-';
                    //}
                    $align = 'right';
                }
        
                if (!empty($arrayAlign)) {
                    $arrayMapeiaAlinhamento = [
                        'c'=>'center',
                        'l'=>'left',
                        'r'=>'right'
                    ];
                    $align = $arrayMapeiaAlinhamento[$arrayAlign[$index]];
                }
                $rowspanStyle = isset($rowspan[$index]) && $rowspan[$index] > 0 ? ' rowspan="'.$rowspan[$index].'" ' : '';
                
                $colspanStyle = isset($colspan[$index]) && $colspan[$index] > 0 ? ' colspan="'.$colspan[$index].'" ' : '';
                
                
                $html .= '
                <td '.$rowspanStyle.$colspanStyle.' style="text-align:'.$align.';'.$border.' width: '.$larguras[$index].'%;">'
                    .$valor.
                '</td>';
            }
        }
        
        $html .= '
        </tr>
        ';

        return $html;
    }

	public function setHtmlHeader($html)
	{
		$this->html_header = $html;
	}

    public static function bordas_primeira_linha()
    {
      $border_style='1px solid gray';
      $bordas_primeira_linha = [
      "border-top: $border_style; border-left: $border_style;",
      "border-top: $border_style;",
      "border-top: $border_style; border-right: $border_style;"
      ];
      return $bordas_primeira_linha;
    }

    public  static function bordas_linhas_intermediarias()
    {
      $border_style='1px solid gray';
      $bordas_linhas_intermediarias = [
        "border-left: $border_style;",
        "",
        "border-right: $border_style;"
      ];
      return $bordas_linhas_intermediarias;
    }

    public  static function bordas_ultima_linha()
    {
      $border_style='1px solid gray';
      $bordas_ultima_linha = [
        "border-bottom: $border_style; border-left: $border_style;",
        "border-bottom: $border_style;",
        "border-bottom: $border_style; border-right: $border_style;"
      ];
      return $bordas_ultima_linha;
    }


	public function imprimeLinhaTable2($param)
	{
		$arrCol       = $param['colunas']   ?? [];
		$arrLargura     = $param['larguras'] ?? [];
		$arrAlign      = $param['alinhamentos'] ?? [];
		$fillBackground = $param['fill_background'] ?? false;
		$is_title       = $param['is_title']  ?? false;
		$negrito        = $param['negrito']   ?? false;
		$rowspan        = $param['rowspan']   ?? [];
		$colspan        = $param['colspan']   ?? [];

		$opcoes         = [];

		if (isset($param['border-bottom'])) $opcoes['border-bottom'] = $param['border-bottom'];
		if (isset($param['border-top'])) $opcoes['border-top'] = $param['border-top'];

		$this->imprimeLinhaTable($arrCol, $arrLargura, $arrAlign, $fillBackground, $is_title, $negrito, $rowspan, $colspan, $opcoes);


	}

	public function breakPage()
	{
		$this->saida_html .= '<br pagebreak="true"/>';
	}

	// metodo utilizado em alguns relatorios, como o rreo.
    public function imprimeLinha3(
            $arrayLinha, 
            $larguras, 
            $bold=false, 
            $fillBackground=false, 
            $border=0, 
            $arrayAlign=[], 
            $rowspan=[], 
            $colspan=[])
    {

        $html = '';
        $backgroundColor = $fillBackground ? "background-color: #eee;" : '';
        
        $borderStyle = '1px solid #000';
        
        switch($border) {
                case 1://bordas nas laterais
                $border = "border-left:$borderStyle;border-right:$borderStyle;";
                break;
                case 2://bordas no topo e fundo
                $border = "border-top:$borderStyle;border-bottom:$borderStyle;";
                break;
                case 3://borda completa
                $border = "border:$borderStyle;";
                break;
                case 4://laterais e fundo
                $border = "border-left:$borderStyle; border-right:$borderStyle; border-bottom:$borderStyle;";
                break;
                default:
                $border = 'border:none;';
        }
        
        $fontweight = $bold ? "font-weight:bold;" : '';
        
        $html .= '
        <tr style="'.$backgroundColor.$fontweight.'">
        ';

        if (count($larguras) === count($arrayLinha)) {
            foreach ($arrayLinha as $index => $valor) {
                
                $align = 'left';
                // if (is_numeric($valor)) {
                //     $valor = round($valor,2) !== 0.00 ? number_format($valor, 2, ',', '.') : '-';
                //     $align = 'right';
                // }
        
                if (!empty($arrayAlign)) {
                    $arrayMapeiaAlinhamento = [
                        'c'=>'center',
                        'l'=>'left',
                        'r'=>'right',
						'j'=>'justify'
                    ];
                    $align = $arrayMapeiaAlinhamento[$arrayAlign[$index]];
                }
                $rowspanStyle = isset($rowspan[$index]) && $rowspan[$index] > 0 ? ' rowspan="'.$rowspan[$index].'" ' : '';
                
                $colspanStyle = isset($colspan[$index]) && $colspan[$index] > 0 ? ' colspan="'.$colspan[$index].'" ' : '';
                
                
                $html .= '
                <td '.$rowspanStyle.$colspanStyle.' style="text-align:'.$align.';'.$border.' width: '.$larguras[$index].'%;">'
                    .$valor.
                '</td>';
            }
        }
        
        $html .= '
        </tr>
        ';

        $this->imprimeTexto($html);

    }

	public function setZebrado($flag_zebrado)
	{
		$this->zebrado = $flag_zebrado;
	}

	public function mini($str, $size=6)
    {
        return '<font size="'.$size.'">'.$str.'</font>';
    }

	public function imprimeCodigoBarras($param)
	{
		$codigo = $param['codigo'] ?? '';
		$x      = $param['x'] ?? 140;
		$y      = $param['y'] ?? $this->GetY()+1;
		$modo   = $param['modo'] ?? 'C128';
		$barcode_height = $param['height'] ?? 9;

		$style = array(
			'position' => '',
			'align' => 'C',
			'stretch' => false,
			'fitwidth' => true,
			'cellfitalign' => '',
			'border' => false, //true,
			'hpadding' => 'auto',
			'vpadding' => 'auto',
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255),
			'text' => false,
			'font' => 'helvetica',
			'fontsize' => 8,
			'stretchtext' => 4
		);
		

		$this->write1DBarcode($codigo, $modo, $x, $y, '', $barcode_height, 0.4, $style, 'N');

	}


	public function testaLinhasRestantes($altura=70)
	{

        if ($this->GetY() > $this->getPageHeight() - $altura)
        {
            $this->AddPage();
        }

	}

}

