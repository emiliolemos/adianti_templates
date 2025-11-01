<?php
class CreateCrudSimples extends TPage
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
        $titulo                 = new TEntry('titulo');
        $campo_id               = new TEntry('campo_id');


        $this->form->addFields( [ MField::requiredLabel('Nome da Classe') ], [ $descricao ] );
        $this->form->addFields( [ MField::requiredLabel('Titulo') ], [ $titulo ] );

        $this->form->addFields( [ MField::requiredLabel('Database') ], [ $database ] );
        $this->form->addFields( [ MField::requiredLabel('Tabela') ], [ $tablename ] );
        $this->form->addFields( [ MField::requiredLabel('Campo ID') ], [ $campo_id ] );
        $this->form->addFields( [ MField::requiredLabel('Diretorio') ], [ $diretorio ] );

        MField::validarCampo($descricao           ,'Descricao' , ['req'=>'','minlen'=>'2']);
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

        $grid = file_get_contents(getcwd().'/app/control/lemarq/util/templates/TemplateCrudSimplesGrid.class.php');
        $form = file_get_contents(getcwd().'/app/control/lemarq/util/templates/TemplateCrudSimplesForm.class.php');
        $clas = file_get_contents(getcwd().'/app/control/lemarq/util/templates/TemplateModel.class.php');

        $outputClass = $param['descricao'];
        $outputDir   = $param['diretorio'];

        if (empty($param['campo_id'])) $param['campo_id'] = 'id';

        $fields = CreateClassModel::tableFields($param['database'], $param['tablename']);
        $create_form_fields = '';
        $add_form_fields    = '';
        $create_grid_columns = '';
        $add_grid_columns    = '';
        $create_filter_columns  = '';
        $add_filter_columns = '';
        $clear_filters       = '';
        $onsearch_filters    = '';
        $onreload_filters    = '';


        
        foreach ($fields as $field)
        {
            $create_form_fields .= "$".$field. " = new TEntry('$field');\r\n";
            $add_form_fields    .= "$"."this->form->addFields( [ new TLabel('$field') ], [ $".$field." ] );\r\n";

            $create_grid_columns .= "$"."col_"."$field = new TDataGridColumn('$field' , '$field' , 'left','5%');\r\n";
            $add_grid_columns    .= "$"."this->datagrid->addColumn($"."col_"."$field);\r\n";
            
            $create_filter_columns  .= "$"."$field = new TEntry('".$field."');\r\n";
            $add_filter_columns     .= "$"."this->filterForm->addFields( [ new TLabel('".$field."') ], [ $"."$field ] );\r\n";
        
            $clear_filters          .=  "TSession::setValue(__CLASS__.'_filter_".$field."',NULL);\r\n";
            $onsearch_filters       .= 
"
            if (isset($"."data->$field) AND ($"."data->$field)) {                   \r\n
                
                $"."filter = new TFilter('$field', 'like', \"%{"."$"."data->$field}%\");    \r\n             
                TSession::setValue(__CLASS__.'_filter_$field',   $"."filter);          \r\n
            }                                                                       \r\n
            
";
            $onreload_filters .= 
"            
if (TSession::getValue(__CLASS__.'_filter_$field')) {                                            \r\n
    $"."criteria->add(TSession::getValue(__CLASS__.'_filter_$field'));                              \r\n
}                                                                                                   \r\n
";



        }



        $grid = str_replace(['{DATABASE}',
                            '{TABLE_NAME}',
                            '{CLASSE_GRID}',
                            '{CLASSE_FORM}',
                            '{ACTIVE_RECORD}',
                            '{CREATE_GRID_COLUMNS}',
                            '{ADD_GRID_COLUMNS}',
                            '{TITULO}',
                            '{CAMPO_ID}',
                            '{CREATE_FILTER_COLUMNS}',
                            '{ADD_FILTER_COLUMNS}',
                            '{CLEAR_FILTERS}',
                            '{ONSEARCH_FILTERS}',
                            '{ONRELOAD_FILTERS}'
                        ],
                            [
                            $param['database'],
                            $param['tablename'],
                            "{$outputClass}Grid",
                            "{$outputClass}Form",
                            "{$outputClass}",
                            $create_grid_columns,
                            $add_grid_columns,
                            $param['titulo'],
                            $param['campo_id'],
                            $create_filter_columns,
                            $add_filter_columns,
                            $clear_filters,
                            $onsearch_filters,
                            $onreload_filters
                            ],
                            $grid);

        $form = str_replace(['{DATABASE}',
                            '{TABLE_NAME}',
                            '{CLASSE_GRID}',
                            '{CLASSE_FORM}',
                            '{ACTIVE_RECORD}',
                            '{CREATE_FORM_FIELDS}',
                            '{ADD_FORM_FIELDS}',
                            '{TITULO}',
                            '{CAMPO_ID}'

                        ],
                            [
                            $param['database'],
                            $param['tablename'],
                            "{$outputClass}Grid",
                            "{$outputClass}Form",
                            "{$outputClass}",
                            $create_form_fields,
                            $add_form_fields,
                            $param['titulo'],
                            $param['campo_id']

                            ],
                            $form);
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


        $outputGrid  = getcwd() . "/app/control/lemarq/{$outputDir}/{$outputClass}Grid.class.php";
        $outputForm  = getcwd() . "/app/control/lemarq/{$outputDir}/{$outputClass}Form.class.php";
        $outputClas  = getcwd() . "/app/model/lemarq/{$outputDir}/{$outputClass}.class.php";

        if (!is_dir(getcwd() . "/app/control/lemarq/{$outputDir}")) {
            mkdir(  getcwd() . "/app/control/lemarq/{$outputDir}", 0755, true);
        }
        if (!is_dir(getcwd() . "/app/model/lemarq/{$outputDir}")) {
            mkdir(  getcwd() . "/app/model/lemarq/{$outputDir}", 0755, true);
        }



        if (!file_exists($outputGrid))
        {
            file_put_contents($outputGrid,$grid);
        }

        if (!file_exists($outputForm))
        {
            file_put_contents($outputForm,$form);
        }

        // if (!file_exists($outputClas))
        // {
        //     file_put_contents($outputClas,$clas);
        // }


        CreateClassModel::create($param['database'], $param['tablename'],$param['descricao'],$param['diretorio'], $param['campo_id'] );

        // file_put_contents("test.txt","Hello World. Testing!");


    }
}