<?php
class CreateTemplates extends TPage
{
    /**
     * Constructor method
     */
    private $form;
    public function __construct()
    {
        parent::__construct();

        
        
        // create the HTML Renderer
        $this->html = new THtmlRenderer('app/resources/lemarq/template_center.html');
        
        try
        {
            // enable main section
            $this->html->enableSection('main');
            
            $panel = new TPanelGroup('Gerador de Classes');
            $panel->add($this->html);
            
            // wrap the page content using vertical box
            $vbox = new TVBox;
            $vbox->style = 'width: 100%';
//            $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $vbox->add($panel);
            
            parent::add($vbox);
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }


    }
    

    public function onSend($param)
    {
        $data = $this->form->getData(); 
        switch ($param['opcao'])
        {
            case 1 : $this->createCrudSimples();
                     break;
            case 2 : $this->createTraitSimples();
        }

    }

    /**
     * Load page by action
     */
    public static function onLoadPage($param)
    {
        // AdiantiCoreApplication::loadPage('CustomerDataGridView');
    }
    
    /**
     * Load window by action
     */
    public static function onLoadWindow($param)
    {
        // AdiantiCoreApplication::loadPage('WindowPDFView', null, ['register_state' => 'false']);
    }
    
    /**
     * Instantiate an existing window
     */
    public static function onInstantiateWindow($param)
    {
        // $window = WindowPDFView::create('PDF', 0.8, 0.8);
        // $window->show();
    }
    
    /**
     * Create an ondemand window
     */
    public static function onCreateWindow($param)
    {
        // $window = TWindow::create('On demand', 0.8, 0.8);
        
        // // create the HTML Renderer
        // $html = new THtmlRenderer('app/resources/page.html');
        
        // $replaces = [];
        // $replaces['title']  = 'Panel title';
        // $replaces['footer'] = 'Panel footer';
        // $replaces['name']   = 'Someone famous';
        // $html->enableSection('main', $replaces);
        
        // $window->add($html);
        // $window->show();
    }

    public function createCrudSimples()
    {

        // parent::setTargetContainer('adianti_right_panel');

        $form = new BootstrapFormBuilder('form_'. get_class($this) );
        $form->setClientValidation(true);

        $descricao              = new TEntry('descricao');
        $diretorio              = new TEntry('diretorio');
        $database               = new TEntry('database');
        $tablename              = new TEntry('tablename');


        $form->addFields( [ MField::requiredLabel('Nome da Classe') ], [ $descricao ] );
        $form->addFields( [ MField::requiredLabel('Database') ], [ $database ] );
        $form->addFields( [ MField::requiredLabel('Tabela') ], [ $tablename ] );
        $form->addFields( [ MField::requiredLabel('Diretorio') ], [ $diretorio ] );

        MField::validarCampo($descricao           ,'Descricao' , ['req'=>'','minlen'=>'4']);
        MField::validarCampo($database            ,'Database'  , ['req'=>'','minlen'=>'2']);
        MField::validarCampo($tablename           ,'Tabela'    , ['req'=>'','minlen'=>'2']);
        MField::validarCampo($diretorio           ,'Diretorio' , ['req'=>'','minlen'=>'2']);



        $btn = $form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';


        parent::add($form);
        

    }


    public function createTraitSimples()
    {
        echo "TRAIT SIMPLES"        ;
    }
    public function onSave($param)
    {

    }


    public $form_control;
    public function createControl()
    {

        $form_control = new BootstrapFormBuilder('form_control_'. get_class($this) );
        $form_control->setClientValidation(true);


        $descricao              = new TEntry('descricao');
        $diretorio              = new TEntry('diretorio');



        $form_control->addFields( [ MField::requiredLabel('Nome da Classe Control') ], [ $descricao ] );
        $form_control->addFields( [ MField::requiredLabel('Diretorio') ], [ $diretorio ] );

        MField::validarCampo($descricao           ,'Descricao'    , ['req'=>'','minlen'=>'4']);
        MField::validarCampo($diretorio           ,'Diretorio'    , ['req'=>'','minlen'=>'2']);


        $btn = $form_control->addAction(_t('Save'), new TAction([$this, 'onSaveControl']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';

        parent::add($form_control);

    }

    public function onSaveControl( $param)
    {

        // $this->form_control->validate();

        $form = file_get_contents(getcwd().'/app/control/lemarq/util/templates/TemplateControl.class.php');

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


    public function cloneControl()
    {

        $form_control = new BootstrapFormBuilder('form_control_'. get_class($this) );
        $form_control->setClientValidation(true);


        $caminho             = new TEntry('caminho');
        $origem              = new TEntry('origem');
        $destino             = new TEntry('destino');


        $form_control->addFields( [ MField::requiredLabel('Caminho') ], [ $caminho ] );
        $form_control->addFields( [ MField::requiredLabel('Classe/Trait Origem') ], [ $origem ] );
        $form_control->addFields( [ MField::requiredLabel('Novo Nome') ], [ $destino ] );

        MField::validarCampo($origem           ,'Origem'    , ['req'=>'','minlen'=>'4']);
        MField::validarCampo($destino           ,'Destino'    , ['req'=>'','minlen'=>'2']);


        $btn = $form_control->addAction(_t('Save'), new TAction([$this, 'onSaveClone']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';

        parent::add($form_control);

    }


    public function onSaveClone( $param)
    {

        $caminho = $param['caminho'];
        $origem  = $param['origem'];
        $destino = $param['destino'];        

        $inputfile = getcwd().'/'.$caminho . '/'. $origem;

        if (file_exists($inputfile))
        {
            $form = file_get_contents(getcwd().'/'.$caminho . '/'. $origem);

            $obj = new stdClass;

            $outputForm  = getcwd() . "/$caminho/{$destino}";

            if (!file_exists($outputForm))
            {
                file_put_contents($outputForm,$form);
            }
        }

    }

    public  function controlSeekWindow()
    {
        $form_control = new BootstrapFormBuilder('form_control_'. get_class($this) );
        $form_control->setClientValidation(true);
        $form_control->setFormTitle('Criação de Formulario de Busca');

        $classe              = new TEntry('classe');
        $active_record       = new TEntry('active_record');
        $campo_id            = new TEntry('campo_id');
        $campo_auxiliar      = new TEntry('campo_auxiliar');


        $form_control->addFields( [ MField::requiredLabel('Nome do Formulario') ], [ $classe ] );
        $form_control->addFields( [ MField::requiredLabel('Active Record') ], [ $active_record ] );
        $form_control->addFields( [ MField::requiredLabel('Campo Auxiliar') ], [ $campo_auxiliar ] );


        MField::validarCampo($classe           ,'Nome Formulario'  , ['req'=>'']);
        MField::validarCampo($active_record    ,'Active Record'    , ['req'=>'']);
        MField::validarCampo($campo_auxiliar         ,'Campo Auxiliar'        , ['req'=>'']);

        $btn = $form_control->addAction(_t('Save'), new TAction([$this, 'onCreateSeekWindow']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';

        parent::add($form_control);

    }

    public function onCreateSeekWindow($param)
    {


        if (!is_dir(getcwd() . "/app/control/lemarq/seek")) {
            mkdir(  getcwd() . "/app/control/lemarq/seek", 0755, true);
        }

        $inputFile = file_get_contents(getcwd().'/app/control/lemarq/util/templates/TemplateSeekWindow.class.php');


///////////////////
        // if (empty($param['campo_id'])) $param['campo_id'] = 'id';

        $active_record = $param['active_record'];

        $database = $active_record::DATABASE;
        $tablename = $active_record::TABLENAME;
        $campo_id  = $active_record::PRIMARYKEY;

        $fields = CreateClassModel::tableFields($database, $tablename);
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
                                            $"."filters[] = new TFilter('$field', 'like', \"%{"."$"."data->$field}%\");    \r\n             
                                        }                                                                       \r\n
                                                    
                                        ";
            $onreload_filters .=  "";

        }

//////////////////




        $inputFile = str_replace(
            ['{CLASSE}',
             '{ACTIVE_RECORD}',
             '{CAMPO_ID}',
             '{CREATE_GRID_COLUMNS}',
             '{ADD_GRID_COLUMNS}',
             '{CREATE_FILTER_COLUMNS}',
             '{ADD_FILTER_COLUMNS}',
             '{CLEAR_FILTERS}',
             '{ONSEARCH_FILTERS}',
             '{ONRELOAD_FILTERS}',
             '{CREATE_FORM_FIELDS}',
             '{ADD_FORM_FIELDS}',
             '{CAMPO_AUXILIAR}',



            ],
            [
                $param['classe'],
                $param['active_record'],
                $campo_id,
                $create_grid_columns,
                $add_grid_columns,
                $create_filter_columns,
                $add_filter_columns,
                $clear_filters,
                $onsearch_filters,
                $onreload_filters,
                $create_form_fields,
                $add_form_fields,
                $param['campo_auxiliar']




            ],
            $inputFile
        );


        $outputForm  = getcwd() . "/app/control/lemarq/seek/Seek".$param['classe'].".class.php";

        if (!file_exists($outputForm))
        {
            file_put_contents($outputForm,$inputFile);
        }


    }

}
