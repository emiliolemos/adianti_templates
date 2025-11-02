<?php

class MyXML
{

    private $doc;
    private $xsd_file;

    const OUTPUT_DIR = 'app/output';


    public function __construct()
    {

    }


    public static function newDocument()
    {
        $doc = new DOMDocument();
        $doc->formatOutput = true;
        return $doc;

    }
    public function __get($name)
    {
        return $this->name;
    }
    public function __set($name, $value)
    {
        $this->name = $value;
    }

    public function save($arquivo='')
    {

    }

    public static function deleteOldFiles($tempo=3600)
    {
        $dir = getcwd().self::OUTPUT_DIR."/";
        /*** percorrendo todos os arquivos da pasta ***/
        foreach (glob($dir."*.xml") as $file) {
            /*** 3600  = 1hora, 86400=24horas ***/
            if(time() - filectime($file) > $tempo) 
            {
                unlink($file);
            }
        }

        foreach (glob($dir."*.xml") as $file) {
            /*** 3600  = 1hora, 86400=24horas ***/
            if(time() - filectime($file) > $tempo) 
            {
                unlink($file);
            }
        }


    }


    public static function criaMarcador($doc, $pai, $nome_campo='', $valor_campo='')
    {
        if (is_null($valor_campo)) $valor_campo = '';
        
        $vl = trim($valor_campo);
        if ($vl=='') $vl='0';
        
        $k = $doc->createElement($nome_campo);
        $k->appendChild($doc->createTextNode($vl));
        $pai->appendChild($k);
    
    }


    public static function criaMarcadorSimples($doc, $raiz, $descricao, $valor)
    {
        $item = $doc->createElement($descricao);
        $item->appendChild($doc->createTextNode($valor));
        $raiz->appendChild($item);
    
    }

    public static function displayErrors()
    {

        // function displayError($_error)
        // {
        //     $return = "<br/>\n";
        //     switch ($_error->level) {
        //         case LIBXML_ERR_WARNING:
        //             $return .= "<b>Warning $_error->code</b>: ";
        //             break;
        //         case LIBXML_ERR_ERROR:
        //             $return .= "<b>Error $_error->code</b>: ";
        //             break;
        //         case LIBXML_ERR_FATAL:
        //             $return .= "<b>Fatal Error $_error->code</b>: ";
        //             break;
        //     }
        //     $return .= trim($_error->message);
        //     if ($_error->file) {
        //         $return .=    " in <b>$_error->file</b>";
        //     }
        //     $return .= " on line <b>$_error->line</b>\n";
        //     return $return;
        // }

		$_errors = libxml_get_errors();
        $_msg = '';
		foreach ($_errors as $_error) {
			$_msg .= self::displayError($_error);
		}
		libxml_clear_errors();

        return $_msg;
    }


    public static function displayError($_error)
    {
        $return = "<br/>\n";
        switch ($_error->level) {
            case LIBXML_ERR_WARNING:
                $return .= "<b>Warning $_error->code</b>: ";
                break;
            case LIBXML_ERR_ERROR:
                $return .= "<b>Error $_error->code</b>: ";
                break;
            case LIBXML_ERR_FATAL:
                $return .= "<b>Fatal Error $_error->code</b>: ";
                break;
        }
        $return .= trim($_error->message);
        if ($_error->file) {
            $return .=    " in <b>$_error->file</b>";
        }
        $return .= " on line <b>$_error->line</b>\n";
        return $return;
    }


}