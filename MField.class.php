<?php

use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TFile;
use Adianti\Widget\Form\TText;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Form\TSpinner;
use Adianti\Validator\TCPFValidator;
use Adianti\Widget\Form\THtmlEditor;
use Adianti\Widget\Form\TRadioGroup;
use Adianti\Validator\TCNPJValidator;
use Adianti\Widget\Form\TCheckButton;
use Adianti\Validator\TEmailValidator;
use Adianti\Validator\TMaxValueValidator;
use Adianti\Validator\TMinValueValidator;
use Adianti\Validator\TRequiredValidator;
use Adianti\Validator\TMaxLengthValidator;
use Adianti\Validator\TMinLengthValidator;

class MField
{

    const ARRAY_MESES = 
    [
        '1'  => 'Janeiro',
        '2'  => 'Fevereiro',
        '3'  => 'Março',
        '4'  => 'Abril',
        '5'  => 'Maio',
        '6'  => 'Junho',
        '7'  => 'Julho',
        '8'  => 'Agosto',
        '9'  => 'Setembro',
        '10' => 'Outubro',
        '11' => 'Novembro',
        '12' => 'Dezembro'
    ];

    const ARRAY_MESES_13 = 
    [
        '1'  => 'Janeiro',
        '2'  => 'Fevereiro',
        '3'  => 'Março',
        '4'  => 'Abril',
        '5'  => 'Maio',
        '6'  => 'Junho',
        '7'  => 'Julho',
        '8'  => 'Agosto',
        '9'  => 'Setembro',
        '10' => 'Outubro',
        '11' => 'Novembro',
        '12' => 'Dezembro',
        '13' => '13o'
    ];

    public static function TMFile($titulo)
    {
        $campo      = new TFile($titulo);
        $campo->setAllowedExtensions( ['doc','docx','xls','xlsx', 'png', 'jpg', 'jpeg','csv','zip','ofx','pdf'] );
        $campo->setLimitUploadSize(10);        
        // $campo->enableImageGallery();
        $campo->enableFileHandling();
        // $campo->enablePopover();
        return $campo;
    }

    public static function TMImage($titulo)
    {
        $campo      = new TFile($titulo);
        $campo->setAllowedExtensions( ['png', 'jpg', 'jpeg'] );
        $campo->setLimitUploadSize(10);        
        $campo->enableFileHandling();
        // $campo->enableImageGallery();
        // $campo->enablePopover();
        return $campo;
    }

    public static function  selectMes($nome_campo)
    {
        $mes = new TCombo($nome_campo);
        $mes->addItems(       
            self::ARRAY_MESES
        );
        return $mes;
    }


    public static function selectDias($nome_campo)
    {
        $dias = new TCombo($nome_campo);
        $arr  = [];
        for ($i=1;$i<=31;$i++) 
            $arr[$i] = str_pad($i,2,'0', STR_PAD_LEFT);
        $dias->addItems( $arr );
        return $dias;
    }

    public static function  selectTipoPessoaFisicaJuridica($nome_campo)
    {
        $tipo = new TCombo($nome_campo);
        $tipo->addItems(       
            array(
            'F'  => 'Fisica',
            'J'  => 'Juridica'
        ));
        return $tipo;
    }


    public static function fieldValor($nome_campo, $decimais=2, $allowNegative=FALSE)
    {
        $reverse = FALSE;
        $vl    = new MTEntry($nome_campo);
        $vl->setNumericMask($decimais, ',', '.', true, $reverse, $allowNegative);

        // $vl = new TNumeric($nome_campo, $decimais, ',', '.', true, $reverse, $allowNegative);

        return $vl;
    }

    public static function  fieldDate($nome_campo)
    {
        $campo = new TDate($nome_campo);
        $campo->setMask('dd/mm/yyyy'); 
        $campo->setDatabaseMask('yyyy-mm-dd');
        return $campo;
    }

    public static function  fieldTime($nome_campo)
    {
        $campo = new TEntry($nome_campo);
        $campo->setMask('99:99');
        // $campo->setOption('startView', 0);
        // $campo->setMask('H:i'); 
        // $campo->setDatabaseMask('hh:ii');
        return $campo;
    }

    public static function fieldTextUppercer($nome_campo)
    {
        $campo = new TText($nome_campo);
        $campo->forceUpperCase();
        return $campo;
    }

    public static function  fieldUppercase($nome_campo)
    {
        $campo = new TEntry($nome_campo);
        $campo->forceUppercase();
        return $campo;
    }

    public static function  fieldLowercase($nome_campo)
    {
        $campo = new TEntry($nome_campo);
        $campo->forceLowercase();
        return $campo;
    }

    public static function  fieldExercicio($nome_campo)
    {
        $campo = new TEntry($nome_campo);
        $campo->setMask('9999');
        return $campo;
    }

    public static function  fieldInteger($nome_campo, $mask='', $flag=false)
    {
        $campo = new TEntry($nome_campo);
        if (empty($mask)) {
            $campo->setMask('9!',$flag);
        } else {
            $campo->setMask( $mask , $flag);
        }
        return $campo;
    }

    public static function fieldTelefone($nome_campo, $mask='')
    {
        $campo = new TEntry($nome_campo);
        if (empty($mask)) {
            $campo->setMask('(99)9999-9999',true);
        } else {
            $campo->setMask( $mask , true);
        }
        return $campo;
    }

    public static function fieldHtmlEditor($nome_campo, $altura=200)
    {
        $campo = new THtmlEditor($nome_campo);
        $campo->setSize( '100%', $altura);
        return $campo;
    }

    // Este campo e usado em situacoes de cadastro de credor, onde é possivel informar CPF ou CNPJ no mesmo campo
    public static function fieldCPFCNPJ($nome_campo)
    {
        $campo = new TEntry($nome_campo);
        $campo->onKeyUp = 'fwFormatarCpfCnpj(this)';
        return $campo;
    }


    // Usar este metodo apenas em lugares onde so é necessario CNPJ
    public static function fieldCNPJ($nome_campo)
    {
        $campo = new TEntry($nome_campo);
        $campo->setMask('99.999.999/9999-99',true);
        return $campo;
    }

    // Usar este metodo apenas onde for necessario apenas CPF
    public static function fieldCPF($nome_campo)
    {
        $campo = new TEntry($nome_campo);
        $campo->setMask('999.999.999-99',true);
        return $campo;
    }

    public static function fieldText($nome_campo, $size=100, $height=50)
    {
        $campo = new TText($nome_campo);
        $campo->setSize("$size%",$height);
        return $campo;
    }

    public static function requiredLabel($txt)
    {
        return new TLabel($txt . ' *', 'red');
    }

    public static function  selectSN($nome_campo)
    {
        $campo = new TCombo($nome_campo);
        $campo->addItems(
            array(
            'S'  => 'SIM',
            'N'  => 'Não'
        ));
        return $campo;
    }

    public static function  select10($nome_campo)
    {
        $campo = new TCombo($nome_campo);
        $campo->addItems(       
            array(
            '1'  => 'SIM',
            '0'  => 'Não'
        ));
        return $campo;
    }

    public static function  check10($nome_campo, $layout='horizontal')
    {
        $campo = new TRadioGroup($nome_campo);
        $campo->setLayout($layout);
        $campo->setUseButton();
        $campo->addItems(
            array(
            '1'  => 'SIM',
            '0'  => 'Não'
        ));
        return $campo;
    }

    public static function  checkSN($nome_campo, $layout='horizontal')
    {
        $campo = new TRadioGroup($nome_campo);
        $campo->setLayout($layout);
        $campo->setUseButton();
        $campo->addItems(
            array(
            'S'  => 'SIM',
            'N'  => 'Não'
        ));
        return $campo;
    }

    // 1 = SIM. 0 = nao
    public static function checkButton10($nome_campo)
    {
        $check   = new TCheckButton($nome_campo);
        $check->setIndexValue(1);
        $check->setUseSwitch(true, 'blue');
        return $check;
    }

    // 0= sim 1 = nao
    public static function checkButton01($nome_campo)
    {
        $check   = new TCheckButton($nome_campo);
        $check->setIndexValue(0);
        $check->setUseSwitch(true, 'blue');
        return $check;
    }

    public static function labelDivision($texto,$cor='#7D78B6')
    {
        $label = new TLabel($texto, $cor, 12, 'bi');
        $label->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        return $label;
    }

    public static function fieldSpinner($campo, $inicio=0, $final=9999999, $step=1)
    {
        $c      = new TSpinner($campo);
        $c->setRange($inicio,$final,$step);
        return $c;
    }

    public static function fieldHTML($campo, $width='100%', $height='400')
    {
        $campo = new THtmlEditor($campo);
        $campo->setSize($width,$height);
        return $campo;
    }

    public static function fieldRadioButton($campo, $opcoes, $layout='horizontal')
    {
        $radio = new TRadioGroup($campo);
        $radio->setUseButton();
        $radio->addItems($opcoes);
        $radio->setLayout($layout);
        return $radio;
    }

    public static function fieldCombo($campo, $opcoes, $default = '')
    {
        $combo    = new TCombo($campo);
        $combo->addItems($opcoes);
        if (!empty($default))
            $combo->setValue($default);

        return $combo;
    }

    public function plainText($text)
    {
        $text = strip_tags($text, '<br><p><li>');
        $text = preg_replace ('/<[^>]*>/', PHP_EOL, $text);
        return $text;
    }

    public static function fieldCEP($nome_campo)
    {
        $campo = new TEntry($nome_campo);
        $campo->setMask('99.999-999',true);
        return $campo;
    }

	public static function btnCloseCurtain()
	{
		$btn_close = new TButton('closeCurtain');
        $btn_close->onClick = "Template.closeRightPanel();";
        $btn_close->setLabel("Fechar");
        $btn_close->setImage('fas:times red');        
		return $btn_close;
	}
    
    public static function scriptCPFCNPJ()
    {
        $script = new TElement('script'); 
        $script->type = 'text/javascript'; 
        $javascript = "
        // autoformatar CPF/CNPJ
        fwFormatarCpfCnpj = function(e) {
        var s = \"\";
        if( e )
        {
        s = e.value;
        }
        else
        {
        s = value;
        }
        s = s.replace(/[^0-9]/g,\"\");
        tam = s.length;
        if(tam < 12)
        {
        r = s.substring(0,3) + \".\" + s.substring(3,6) + \".\" + s.substring(6,9);
        r += \"-\" + s.substring(9,11);
        if ( tam < 4 )
        s = r.substring(0,tam);
        else if ( tam < 7 )
        s = r.substring(0,tam+1);
        else if ( tam < 10 )
        s = r.substring(0,tam+2);
        else
        s = r.substring(0,tam+3);
        }else{
        r = s.substring(0,2) + \".\" + s.substring(2,5) + \".\" + s.substring(5,8);
        r += \"/\" + s.substring(8,12) + \"-\" + s.substring(12,14);
        if ( tam < 3 )
        s = r.substring(0,tam);
        else if ( tam < 6 )
        s = r.substring(0,tam+1);
        else if ( tam < 9 )
        s = r.substring(0,tam+2);
        else if ( tam < 13 )
        s = r.substring(0,tam+3);
        else
        s = r.substring(0,tam+4);
        }
        if( e )
        {
        e.value = s;
        return true;
        }
        return s;
        };        
        ";

        $script->add($javascript); 
        return $script;
    }

    public static function validarCampo($campo, $titulo, $param)
    {
        foreach ($param as $item => $valor)
        {
            switch ($item)
            {
                case 'minlen' :
                    $campo->addValidation($titulo, new TMinLengthValidator, [$valor]);
                    break;
                case 'maxlen' :
                    $campo->addValidation($titulo, new TMaxLengthValidator, [$valor]);
                    break;
                case 'minval' :
                    $campo->addValidation($titulo, new TMinValueValidator, [$valor]);
                    break;
                case 'maxval' :
                    $campo->addValidation($titulo, new TMaxValueValidator, [$valor]);
                    break;
                case 'cpf' :
                    $campo->addValidation($titulo, new TCPFValidator, [$valor]);
                    break;
                case 'cnpj' :
                    $campo->addValidation($titulo, new TCNPJValidator, [$valor]);
                    break;
                case 'email' :
                    $campo->addValidation($titulo, new TEmailValidator, [$valor]);
                    break;
                case 'req' : 
                    $campo->addValidation($titulo, new TRequiredValidator);
                    break;
                case 'time' :
                    $campo->addValidation($titulo, new TTimeValidator, [$valor]);
                    break;
            }
        }
    }

    public static function  selectBimestre($nome_campo)
    {
        $campo = new TCombo($nome_campo);
        $campo->addItems(       
            MSispubConstant::ARRAY_PERIODO_BIMESTRAL
        );
        return $campo;
    }

    public static function  selectMes13($nome_campo)
    {
        $mes = new TCombo($nome_campo);
        $mes->addItems(       
            self::ARRAY_MESES_13
        );
        return $mes;
    }


}