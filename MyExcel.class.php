<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \PhpOffice\PhpSpreadsheet\IOFactory;

class MyExcel 
{

    const BORDER_THICK = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK;
    const BORDER_THIN = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN;
    const FILL_GRADIENT_LINEAR = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR;
    const FILL_SOLID = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID;
    const PROTECTION_PROTECTED = \PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED;
    const PROTECTION_UNPROTECTED = \PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED;


    private $arquivo_saida;
    private $versao_excel;

    private $objPHPExcelModelo;

    // Valores possiveis : Excel2007, Excel5
    public function __construct($versao='Excel2007')
    {

        $this->versao_excel = $versao;
    }

    public function __get($name)
    {
        return $this->$name;
    }


    // Carrega uma planilha já existente nas pastas locais
    // $excel = new MyExcel();        
    // $excel->carregaPlanilhaLocal('app/files/rgf-2023.xlsx');
    public function carregaPlanilhaLocal($arquivo)
    {
        $this->objPHPExcelModelo = IOFactory::load($arquivo);
    }

    // Carrega uma planilha localizada noutro servidor
    // Exemplo 
    // $excel = new MyExcel();    
    // $excel->carregaPlanilhaRemota('http://xxx.com.br/teste/planilha.xlsx');
    public function carregaPlanilhaRemota($url)
    {

        $servidor = $_SERVER['SERVER_NAME'];
           
        $output = 'tmp_'.uniqid().'.xlsx';
        $folder_file = getcwd().'/app/output/'.$output; // caminho absoluto
        file_put_contents($folder_file,file_get_contents($url));	

        $this->objPHPExcelModelo = IOFactory::load($folder_file);
    }


    // Cria uma panilha vazia
    // Exemplo
    // $excel = new MyExcel();
    // $excel->novaPlanilha();

    public function novaPlanilha()
    {
        $this->objPHPExcelModelo = new Spreadsheet();
        $activeWorksheet = $this->objPHPExcelModelo->getActiveSheet();
        
        
    }

    public static function borderStyle($style)
    {
        $result = '';
        switch ($style)
        {
           case 'BORDER_THICK' : $result = PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK; break;
           default : 
                $result = PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK;           
        }
        return $result;
    }

    public function Save($nmArquivo='') 
    {

            $this->deleteOldFiles();
            if (empty($nmArquivo)) :
                $output = 'tmp_'.uniqid().'.xlsx';
            else :
                $output = 'tmp_'.$nmArquivo.'_'.uniqid().'.xlsx';
            endif;

            $folder_file = getcwd().'/app/output/'.$output; // caminho absoluto
            $this->arquivo_saida = 'app/output/'.$output;

            $this->objPHPExcelModelo->setActiveSheetIndex(0);

            $writer = new Xlsx($this->objPHPExcelModelo);
            $writer->save($this->arquivo_saida);            


    }


    private function deleteOldFiles($tempo=3600)
    {
        $dir = getcwd()."/app/output/";
        /*** percorrendo todos os arquivos da pasta ***/
        foreach (glob($dir."*.xls") as $file) {
            /*** 3600  = 1hora, 86400=24horas ***/
            if(time() - filectime($file) > $tempo) 
            {
                unlink($file);
            }
        }

        foreach (glob($dir."*.xlsx") as $file) {
            /*** 3600  = 1hora, 86400=24horas ***/
            if(time() - filectime($file) > $tempo) 
            {
                unlink($file);
            }
        }


    }

    public static function incluirLinhas($objPHPExcelModelo, $linha_inicial, $qt_linhas)
    {
        if ($qt_linhas > 0 ) {
            $objPHPExcelModelo->getActiveSheet()->insertNewRowBefore($linha_inicial+1,$qt_linhas);
        }
    }


}

// Adicionar esse metodo na classe onde desejar usar cores nas celulas
// public function cellColor($objPHPExcelModelo, $cells, $color)
// {

//     $objPHPExcelModelo->getActiveSheet()->getStyle($cells)->getFill()
//     ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
//     ->getStartColor()->setARGB($color);        

// }


