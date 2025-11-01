<?php
class CreateTraitSimples extends TPage
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
        $master_id              = new TEntry('master_id');

        $database               = new TEntry('database');
        $classe_master          = new TEntry('classe_master');
        $classe_master_id       = new TEntry('classe_master_id');
        $tablename              = new TEntry('tablename');



        $this->form->addFields( [ MField::requiredLabel('Nome da Trait') ], [ $descricao ] );
        $this->form->addFields( [ MField::requiredLabel('Classe Active Record (Detalhe)') ], [ $active_record ] );
        $this->form->addFields( [ MField::requiredLabel('Diretorio') ], [ $diretorio ] );
        $this->form->addFields( [ MField::requiredLabel('Database') ], [ $database ] );
        $this->form->addFields( [ MField::requiredLabel('Nome Tabela Detalhe') ], [ $tablename ] );
        $this->form->addFields( [ MField::requiredLabel('Campo Ligação Master ID') ], [ $master_id ] );
        $this->form->addFields( [ MField::requiredLabel('Classe Master') ], [ $classe_master ] );
        $this->form->addFields( [ MField::requiredLabel('Classe Master ID') ], [ $classe_master_id] );

        

        MField::validarCampo($descricao           ,'Descricao'    , ['req'=>'','minlen'=>'4']);
        MField::validarCampo($active_record       ,'Active Record', ['req'=>'','minlen'=>'2']);
        MField::validarCampo($tablename          ,'Tabela', ['req'=>'','minlen'=>'2']);
        MField::validarCampo($database          ,'Database', ['req'=>'','minlen'=>'2']);

        MField::validarCampo($diretorio           ,'Diretorio'    , ['req'=>'','minlen'=>'2']);
        MField::validarCampo($master_id           ,'Master ID'    , ['req'=>'']);
        MField::validarCampo($classe_master       ,'Classe Master', ['req'=>'']);
        MField::validarCampo($classe_master_id       ,'Classe Master ID', ['req'=>'']);


        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';


        parent::add($this->form);

    }

    public function onSave($param)
    {

        $this->form->validate();

        $form = file_get_contents(getcwd().'/app/control/lemarq/util/templates/TemplateDetalheFormTrait.php');
        $clas = file_get_contents(getcwd().'/app/control/lemarq/util/templates/TemplateModel.class.php');

        $outputClass = $param['descricao'];
        $outputDir   = $param['diretorio'];
        $active_record = $param['active_record'];
        $database      = $param['database'];
        $tablename     = $param['tablename'];



        $obj = new stdClass;


        $obj = $this->criar_campos($database, $outputClass, $tablename);



        $minusculo = strtolower($param['descricao']);

        $clas = str_replace(['{ACTIVE_RECORD}',
                            '{TABLE_NAME}'
                           

                        ],
                            [
                            $param['active_record'],
                            $param['tablename']
                           

                            ],
                            $clas);

        $form = str_replace(['{TRAITNAME}',
                            '{traitname}',
                            '{ACTIVE_RECORD}',
                            '{master_id}',
                            '{CLASSE_MASTER}',
                            '{TRAIT_ADD_FIELDS}',
                            '{TRAIT_ADD_FORM_FIELDS}',
                            '{TRAIT_ADD_COLUMN_FIELDS}',
                            '{TRAIT_CLEAR_FIELDS}',
                            '{TRAIT_ONEDIT_FIELDS}',
                            '{TRAIT_ONADD_FIELDS}',
                            '{TRAIT_ONSAVE_FIELDS}',
                            '{classe_master_id}'
                            

                        ],
                            [
                            $param['descricao'],
                            $minusculo, 
                            $param['active_record'],
                            $param['master_id'],
                            $param['classe_master'],
                            $obj->add_fields,
                            $obj->add_form_fields,
                            $obj->add_column_fields,
                            $obj->add_clear_fields,
                            $obj->add_onedit_fields,
                            $obj->add_onadd_fields,
                            $obj->add_onsave_fields,
                            $param['classe_master_id']


                           
                            ],
                            $form);



        $outputForm  = getcwd() . "/app/control/lemarq/{$outputDir}/{$outputClass}Trait.php";
        $outputClas  = getcwd() . "/app/model/lemarq/{$outputDir}/{$active_record}.class.php";

        if (!is_dir(getcwd() . "/app/control/lemarq/{$outputDir}")) {
            mkdir(  getcwd() . "/app/control/lemarq/{$outputDir}", 0755, true);
        }

        if (!is_dir(getcwd() . "/app/model/lemarq/{$outputDir}")) {
            mkdir(  getcwd() . "/app/model/lemarq/{$outputDir}", 0755, true);
        }


        if (!file_exists($outputForm))
        {
            file_put_contents($outputForm,$form);
        }

        // if (!file_exists($outputClas))
        // {
        //     file_put_contents($outputClas,$clas);
        // }
        CreateClassModel::create($param['database'], $param['tablename'],$param['active_record'],$param['diretorio'] );


    }

    public function criar_campos($database, $class_name, $tablename)
    {

        $fields = CreateClassModel::tableFields($database, $tablename);
        $quantidade = count($fields);

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

            foreach ($fields as $field)
            {
                $campoLimpo         = $field;
                $campo              = $class_name."_".$field ;

                $add_fields        .= "$"."$campo = new TEntry('$campo');\r\n";
                $add_form_fields   .= "$"."this->form->addFields( [new TLabel('$campoLimpo') , $"."$campo  ] );\r\n";
                $add_column_fields .= '$colunas[] = '." new TDataGridColumn('".$campoLimpo."' , '".$campoLimpo."', 'left','10%');\r\n";
                $add_clear_fields  .= "$"."data->$campo = '';\r\n";
                $add_onedit_fields .= "$"."data->$campo = $"."param['$campoLimpo'];\r\n";
                $add_onadd_fields  .=  ",'$campoLimpo' => $"."data->$campo\r\n";
                $add_onsave_fields .=  "$"."item->$campoLimpo = $"."param['$class_name"."s_list_"."$campoLimpo"."'][$"."key];\r\n";
                
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