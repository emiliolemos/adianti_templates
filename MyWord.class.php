<?php
use PhpOffice\PhpWord;

class MyWord  
{

    private $arquivo_saida;
    private $word;

    public function __construct()
    {

    }

    public function __get($name)
    {
        return $this->$name;
    }

    public static function loadFile($arquivo)
    {
        return new \PhpOffice\PhpWord\TemplateProcessor($arquivo);
    }    

    public function Save()
    {
        $this->deleteOldFiles();
        $output = 'tmp_'.uniqid().'.docx';
        $folder_file = getcwd().'/app/output/'.$output; // caminho absoluto
        $this->arquivo_saida = 'app/output/'.$output;

        // Saving the document as OOXML file...
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($this->word, 'Word2007');
        $objWriter->save($this->arquivo_saida);

    }

    public static function saveTemplate($template, $arquivo)
    {
        self::deleteOldFiles();
        $output = 'tmp_'.uniqid().'.docx';
        $caminho_absoluto = getcwd().'/app/output/'.$output; // caminho absoluto
        $arquivo_saida = 'app/output/'.$output;

        $template->saveAs($caminho_absoluto);
        return $arquivo_saida;


    }


    public  static function deleteOldFiles($tempo=3600)
    {
        $dir = getcwd()."/app/output/";
        /*** percorrendo todos os arquivos da pasta ***/
        foreach (glob($dir."*.docx") as $file) {
            /*** 3600  = 1hora, 86400=24horas ***/
            if(time() - filectime($file) > $tempo) 
            {
                unlink($file);
            }
        }

        foreach (glob($dir."*.docx") as $file) {
            /*** 3600  = 1hora, 86400=24horas ***/
            if(time() - filectime($file) > $tempo) 
            {
                unlink($file);
            }
        }


    }

    // Exemplo se uso : 
    // $estilo = ['width' => 5000, 'unit'=>'PERCENT'];
    // $table = MyWord::newTable($estilo);
    public static function newTable($param)
    {
        $width  = (isset($param['width'])) ? $param['width'] : 1000;
        $unit   = (isset($param['unit'])) ? $param['unit'] : 'PERCENT';
        $unit   = self::tableUnit($unit);
        $estilo = ['width'=>$width, 'unit' => $unit];
        $tabela = new \PhpOffice\PhpWord\Element\Table($estilo);
        return $tabela;

    }



    public static function addTableRow($tabela, $titulos, $larguras, $alinhamentos)
    {
        $tabela->addRow();
        foreach($titulos as $index => $valor) {
            $celula      = $tabela->addCell($larguras[$index], ['borderSize' => 5]);
            $alinhamento = self::TableColumnAlign($alinhamentos[$index]);
            $celula->addText($valor, ['size' => 11], ['alignment' => $alinhamento]);
        }        
    }

    public static function TableColumnAlign($alinhamento)
    {
        switch ($alinhamento)
        {
            case 'L' :
            case 'l':
                return 'left'; break;
            case 'C' : 
            case 'c':
                return 'center'; break;
            case 'R' : 
            case 'r':
                return 'right'; break;
            default :
                return 'left'; break;

        }
    }

    public static function tableUnit($unit)
    {
        switch ($unit)
        {
            case 'PERCENT' :
                return \PhpOffice\PhpWord\SimpleType\TblWidth::PERCENT; break;
            default :
                return \PhpOffice\PhpWord\SimpleType\TblWidth::PERCENT; break;

        }

    }

 

}