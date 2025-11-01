<?php
class CreateTraitImportacao extends TPage
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
        $active_record          = new TEntry('active_record');
        $database_destino       = new TEntry('database_destino');
        $database_origem        = new TEntry('database_origem');
        $tabela_origem          = new TEntry('tabela_origem');
        $tabela_destino         = new TEntry('tabela_destino');

        $descricao->placeholder = 'Exemplo : PCaspEventoConta';        
        $descricao->setTip('Será criada uma trait Terminando com o nome informado');        
        $diretorio->placeholder = 'Exemplo : sispubweb';        
        $diretorio->setTip('A trait ficará dentro de app/control/lemarq/importacao/diretorio informado');        

        $this->form->addFields( [ MField::requiredLabel('Nome da Trait') ], [ $descricao ] );
        $this->form->addFields( [ MField::requiredLabel('Diretorio') ], [ $diretorio ] );

        $this->form->addContent([MField::labelDivision('Dados da Origem')]);
        $this->form->addFields( [ MField::requiredLabel('Database') ], [ $database_origem ] );
        $this->form->addFields( [ MField::requiredLabel('Nome da Tabela') ], [ $tabela_origem ] );

        $this->form->addContent([MField::labelDivision('Dados do Destino')]);
        $this->form->addFields( [ MField::requiredLabel('Database') ], [ $database_destino ] );
        $this->form->addFields( [ MField::requiredLabel('Nome da Tabela') ], [ $tabela_destino ] );
        $this->form->addFields( [ MField::requiredLabel('Classe Active Record') ], [ $active_record ] );





        

        MField::validarCampo($descricao           ,'Descricao'            , ['req'=>'','minlen'=>'4']);
        MField::validarCampo($diretorio           ,'Diretorio'            , ['req'=>'','minlen'=>'2']);

        MField::validarCampo($database_origem     ,'Database Origem'      , ['req'=>'','minlen'=>'2']);
        MField::validarCampo($tabela_origem       ,'Tabela Origem'        , ['req'=>'','minlen'=>'2']);
        
        MField::validarCampo($database_destino    ,'Database Destino'     , ['req'=>'']);
        MField::validarCampo($active_record       ,'Tabela Destino'       , ['req'=>'']);
        MField::validarCampo($active_record       ,'Classe Active Record' , ['req'=>'']);

        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';


        parent::add($this->form);

    }

    public function onSave($param)
    {

        $this->form->validate();

        $form = file_get_contents(getcwd().'/app/control/lemarq/util/templates/TemplateImportacao.php');

        $outputClass = $param['descricao'];
        $outputDir   = $param['diretorio'];
        $active_record = $param['active_record'];

        $database      = $param['database_destino'];
        $tablename     = $param['tabela_destino'];

        $obj = new stdClass;


        $obj = $this->criar_campos($database, $outputClass, $tablename);



        $minusculo = strtolower($param['descricao']);

        $form = str_replace(['{TRAITNAME}',
                            '{ACTIVE_RECORD}',
                            '{DATABASE_DESTINO}',
                            '{DATABASE_ORIGEM}',
                            '{TABELA_ORIGEM}',
                            '{TRAIT_ADD_FIELDS}'
                            

                        ],
                            [
                            $param['descricao'],
                            $param['active_record'],
                            $param['database_destino'],
                            $param['database_origem'],
                            $param['tabela_origem'],
                            $obj->add_fields

                           
                            ],
                            $form);



        $outputForm  = getcwd() . "/app/control/lemarq/util/importacao/{$outputDir}/ImportaDadosSispub{$outputClass}Trait.php";

        if (!is_dir(getcwd() . "/app/control/lemarq/util/importacao/{$outputDir}")) {
            mkdir(  getcwd() . "/app/control/lemarq/util/importacao/{$outputDir}", 0755, true);
        }


        if (!file_exists($outputForm))
        {
            file_put_contents($outputForm,$form);
        }



    }

    public function criar_campos($database, $class_name, $tablename)
    {

        $fields = CreateClassModel::tableFields($database, $tablename);
        $quantidade = count($fields);

        $obj = new stdClass;
        $add_fields        = '';


        if ($quantidade > 0)
        {

            foreach ($fields as $field)
            {
                $campoLimpo         = $field;
                $campo              = $class_name."_".$field ;
                $add_fields        .= "$"."obj->"."$field = "."$"."row['$field'];\r\n";
                
            }

        }

        $obj->add_fields        = $add_fields;

        return $obj;

    }


}