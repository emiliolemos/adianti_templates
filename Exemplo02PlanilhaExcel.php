<?php

use Adianti\Database\TTransaction;

class Exemplo02PlanilhaExcel extends TPage
{


    private $pdf;
    private $form;
    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('TesteExcel'); //$this->getFormName());
        $this->form->setFieldSizes('100%');

        $this->form->addContent([new TLabel('Teste de Excel')]);
        $btn = $this->form->addAction('Gerar Planilha', new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';



        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        parent::add( $vbox );


    }


    public function onSave($param)
    {
       $arquivo = $this->geraPlanilha();
 
       parent::add( new THyperLink('Baixar Planilha Excel' , $arquivo, 'blue', 12, 'bi'));
 
 
    }



    public function geraPlanilha()
    {

        // Criando uma planilha        
        // echo "Teste Excel";
        $excel = new MyExcel();
        $excel->novaPlanilha();
        $excel->objPHPExcelModelo->getActiveSheet()->setCellValue('A1','TESTE');
        $excel->Save();

        $arquivo = $excel->arquivo_saida;
        return $arquivo;



        // Baixando uma planilha
        // echo "Teste Excel 2";
        // $excel = new MyExcel();
        // $excel->carregaPlanilhaRemota('http://exemploxxxxxxx.com.br/file/planilha.xlsx');
        // $objPHPExcelModelo = $excel->objPHPExcelModelo;
        // $objPHPExcelModelo->setActiveSheetIndexByName('Anexo 2 - DCL');
        // $objPHPExcelModelo->getActiveSheet()->setCellValue("K10","TESTE XXXX");        
        // $excel->Save();
        

        // // Carregando uma planilha local
        // echo "Teste Excel 3";
        // $excel = new MyExcel();
        // $excel->carregaPlanilhaLocal('app/files/rgf-2023.xlsx');
        // $objPHPExcelModelo = $excel->objPHPExcelModelo;
        // $objPHPExcelModelo->setActiveSheetIndexByName('Anexo 2 - DCL');
        // $objPHPExcelModelo->getActiveSheet()->setCellValue("K10","TESTE HHHHHHH");        
        // $excel->Save();
        



    }

	

}

/*
// Mudar a aba
$objPHPExcelModelo->setActiveSheetIndexByName('Balanco-Orcamentario');
$objPHPExcelModelo->setActiveSheetIndex(0);

// Gravar valor numa celula
$objPHPExcelModelo->getActiveSheet()->setCellValue("N1", [global_anobase]);
$objPHPExcelModelo->getActiveSheet()->setCellValueByColumnAndRow(7, 1, $nmuf); // estado
$objPHPExcelModelo->getActiveSheet()->getCell("E10")->setValue("EM 31/12/$anoant");



// ler valor de uma celula
$codigo = $objPHPExcelModelo->getActiveSheet()->getCellByColumnAndRow(0,$linha)->getValue();	
$codigo = $objPHPExcelModelo->getActiveSheet()->getCell("J$linha")->getCalculatedValue();	
	
// Incluir linhas
$objPHPExcelModelo->getActiveSheet()->insertNewRowBefore($linha_inicial+1,$qt_linhas);

// Formula
$objPHPExcelModelo->getActiveSheet()->setCellValue("E$linha", "=C$linha - D$linha");
$objPHPExcelModelo->getActiveSheet()->setCellValue("H$linha", "=TRUNC(D$linha + E$linha-F$linha-G$linha,2)");			

// Formatando uma celula para texto
$objPHPExcelModelo->getActiveSheet()->setCellValueExplicit(
	"V$linha",
	$ds->fields['cd_fonte'],
	PHPExcel_Cell_DataType::TYPE_STRING
);



// Alinhamentos e formatacoes
$objPHPExcelModelo->getActiveSheet()->getStyle("B$linha")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcelModelo->getActiveSheet()->getStyle("I$linha")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcelModelo->getActiveSheet()->getStyle("J$linha")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcelModelo->getActiveSheet()->getStyle("I$linha:J$linha")->getNumberFormat()->setFormatCode('#,##0.00');		

$objPHPExcelModelo->getActiveSheet()->getStyle("K$linha:P$linha")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');		


// Criando uma nova aba
$objWorkSheet = $objPHPExcelModelo->createSheet();
$objWorkSheet->setTitle("NovaAba");
$objPHPExcelModelo->setActiveSheetIndexByName('NovaAba');



*/
