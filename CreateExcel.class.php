<?php
class CreateExcel extends TPage
{
    private $form ;

    public function __construct()
    {
        parent::__construct();

        // $classe = ;

        $this->form = new BootstrapFormBuilder('form_'. get_class($this) );
        $this->form->setClientValidation(true);


        $descricao              = new TEntry('descricao');
        $diretorio              = new TEntry('diretorio');




        $this->form->addFields( [ MField::requiredLabel('Nome da Classe Excel') ], [ $descricao ] );
        $this->form->addFields( [ MField::requiredLabel('Diretorio') ], [ $diretorio ] );

        MField::validarCampo($descricao           ,'Descricao'    , ['req'=>'','minlen'=>'4']);
        MField::validarCampo($diretorio           ,'Diretorio'    , ['req'=>'','minlen'=>'2']);


        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';


        parent::add($this->form);

    }

    public function onSave($param)
    {

        $this->form->validate();

        $form = file_get_contents(getcwd().'/app/control/lemarq/util/templates/TemplatePlanilhaExcel.class.php');

        $outputClass = $param['descricao'];
        $outputDir   = $param['diretorio'];

        $obj = new stdClass;



        $form = str_replace(
            [
                '{CLASS_NAME}'
        
            ],
            [
                $param['descricao']
            ],
            $form);



        $outputForm  = getcwd() . "/app/control/lemarq/{$outputDir}/{$outputClass}.php";

        if (!is_dir(getcwd() . "/app/control/lemarq/{$outputDir}")) {
            mkdir(  getcwd() . "/app/control/lemarq/{$outputDir}", 0755, true);
        }



        if (!file_exists($outputForm))
        {
            file_put_contents($outputForm,$form);
        }



    }

    public function criar_campos($prefixo, $quantidade=0)
    {
        $obj = new stdClass;
        $add_fields        = '';
        $add_form_fields   = '';
        $add_column_fields = '';
        $add_clear_fields  = '';
        $add_onedit_fields = '';
        $add_onadd_fields  = '';
        $add_onsave_fields = '';


        if ($quantidade > 0)
        {

            for ($i=1;$i<=$quantidade;$i++)
            {
                $campoLimpo         = "campo".str_pad($i,2,'0',STR_PAD_LEFT ) ;                
                $campo              = $prefixo."_campo".str_pad($i,2,'0',STR_PAD_LEFT ) ;
                $add_fields        .= "$"."$campo = new TEntry('$campo');\r\n";
                $add_form_fields   .= "$"."this->form->addFields( [new TLabel('$campoLimpo') , $"."$campo  ] );\r\n";
                $add_column_fields .= '$colunas[] = '." new TDataGridColumn('".$campoLimpo."' , '".$campoLimpo."', 'left','10%');\r\n";
                $add_clear_fields  .= "$"."data->$campo = '';\r\n";
                $add_onedit_fields .= "$"."data->$campo = $"."param['$campoLimpo'];\r\n";
                $add_onadd_fields  .=  ",'$campoLimpo' => $"."data->$campo\r\n";
                $add_onsave_fields .=  "$"."item->$campoLimpo = $"."param['$prefixo"."s_list_"."$campoLimpo"."'][$"."key];\r\n";

                
            }

        }

        $obj->add_fields        = $add_fields;
        $obj->add_form_fields   = $add_form_fields;
        $obj->add_column_fields = $add_column_fields;
        $obj->add_clear_fields  = $add_clear_fields;
        $obj->add_onedit_fields = $add_onedit_fields;
        $obj->add_onadd_fields  = $add_onadd_fields;
        $obj->add_onsave_fields = $add_onsave_fields;

        return $obj;

    }


}