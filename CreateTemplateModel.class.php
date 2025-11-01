<?php
class CreateTemplateModel extends TPage
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
        $database               = new TEntry('database');
        $tablename              = new TEntry('tablename');
        $campo_id               = new TEntry('campo_id');
        $titulo                 = new TEntry('titulo');


        $this->form->addFields( [ MField::requiredLabel('Nome da Classe') ], [ $descricao ] );
        $this->form->addFields( [ MField::requiredLabel('Database') ], [ $database ] );
        $this->form->addFields( [ MField::requiredLabel('Tabela') ], [ $tablename ] );
        $this->form->addFields( [ MField::requiredLabel('Campo ID') ], [ $campo_id ] );
        $this->form->addFields( [ MField::requiredLabel('Diretorio') ], [ $diretorio ] );

        MField::validarCampo($descricao           ,'Descricao' , ['req'=>'','minlen'=>'4']);
        MField::validarCampo($database            ,'Database'  , ['req'=>'','minlen'=>'2']);
        MField::validarCampo($tablename           ,'Tabela'    , ['req'=>'','minlen'=>'2']);
        MField::validarCampo($diretorio           ,'Diretorio' , ['req'=>'','minlen'=>'2']);



        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';


        parent::add($this->form);

    }

    public function onSave($param)
    {

        $this->form->validate();

        $clas = file_get_contents(getcwd().'/app/control/lemarq/util/templates/TemplateModel.class.php');

        $outputClass = $param['descricao'];
        $outputDir   = $param['diretorio'];


        $fields = CreateClassModel::tableFields($param['database'], $param['tablename']);
        $create_form_fields = '';
        $add_form_fields    = '';
        $create_grid_columns = '';
        $add_grid_columns    = '';
        
        foreach ($fields as $field)
        {
            $create_form_fields .= "$".$field. " = new TEntry('$field');\r\n";
            $add_form_fields    .= "$"."this->form->addFields( [ new TLabel('$field') ], [ $".$field." ] );\r\n";

            $create_grid_columns .= "$"."col_"."$field = new TDataGridColumn('$field' , '$field' , 'left','5%');\r\n";
            $add_grid_columns    .= "$"."this->datagrid->addColumn($"."col_"."$field);\r\n";


        }


        $clas = str_replace(['{DATABASE}',
                            '{TABLE_NAME}',
                            '{CLASSE_GRID}',
                            '{CLASSE_FORM}',
                            '{ACTIVE_RECORD}',
                            '{CAMPO_ID}'

                        ],
                            [
                            $param['database'],
                            $param['tablename'],
                            "{$outputClass}Grid",
                            "{$outputClass}Form",
                            "{$outputClass}",
                            $param['campo_id']

                            ],
                            $clas);


        $outputClas  = getcwd() . "/app/model/lemarq/{$outputDir}/{$outputClass}.class.php";

        if (!is_dir(getcwd() . "/app/model/lemarq/{$outputDir}")) {
            mkdir(  getcwd() . "/app/model/lemarq/{$outputDir}", 0755, true);
        }



        CreateClassModel::create($param['database'], $param['tablename'],$param['descricao'],$param['diretorio'] , $param['campo_id']);



    }
}