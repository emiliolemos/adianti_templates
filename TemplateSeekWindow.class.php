<?php

class Seek{CLASSE} extends TWindow
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = {ACTIVE_RECORD}::DATABASE;
    private static $activeRecord = '{ACTIVE_RECORD}';
    private static $primaryKey = '{CAMPO_ID}';
    private static $formName = 'form_{CLASSE}SeekWindow';
    private $showMethods = ['onReload', 'onSearch'];
    private $limit = 20;

    use BuilderSeekWindowTrait;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();
        parent::setSize(0.8, 0.8);
        parent::setTitle("Janela de busca");
        parent::setProperty('class', 'window_modal');

        $param['_seek_window_id'] = $this->id;
        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        $this->limit = 10;

        // define the form title
        // $this->form->setFormTitle("Janela de busca de clientes");
        $this->form->setFieldSizes('100%');        


/* ------ gerador  ---------- */        
{CREATE_FORM_FIELDS}
/* -------------------------- */

        // $nome->setMaxLength(500);
        // $email->setMaxLength(255);
        // $documento->setMaxLength(20);

        // $id->setSize('100%');
        // $nome->setSize('100%');
        // $email->setSize('100%');
        // $documento->setSize('100%');

        // $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Nome:", null, '14px', null, '100%'),$nome],[new TLabel("Documento:", null, '14px', null, '100%'),$documento],[new TLabel("Email:", null, '14px', null, '100%'),$email]);
        // $row1->layout = [' col-sm-3',' col-sm-3',' col-sm-3',' col-sm-3'];

/* ------- gerador ---------------------*/        
{ADD_FORM_FIELDS};
/* --------------------------------------*/


        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 
        $this->setSeekParameters($btn_onsearch->getAction(), $param);

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = $this->getSeekFiltersCriteria($param);


        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);


/* --------gerador ------------ */
{CREATE_GRID_COLUMNS}
/* ---------------------------- */


/* --------gerador ------------ */
        
{ADD_GRID_COLUMNS}

/* ---------------------------- */
        // Formatacoes
        // MTransform::formatDataGridColumnValue($coluna_valor,2); // valor
        // MTransform::formatDataGridDate($col_dt_abert); // data
        // MTransform::formatDataGridDate($col_dt_encerra); // data

        $action_onSelect = new TDataGridAction(array($this, 'onSelect'));
        $action_onSelect->setUseButton(true);
        $action_onSelect->setButtonClass('btn btn-default btn-sm');
        $action_onSelect->setLabel("Selecionar");
        $action_onSelect->setImage('far:hand-pointer #44bd32');
        $action_onSelect->setField(self::$primaryKey);
        $this->setSeekParameters($action_onSelect, $param);

        $this->datagrid->addAction($action_onSelect);

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $navigationAction = new TAction(array($this, 'onReload'));
        $this->setSeekParameters($navigationAction, $param);
        $this->pageNavigation->setAction($navigationAction);
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';

        $panel->addFooter($this->pageNavigation);

        parent::add($this->form);
        parent::add($panel);

    }

    public static function onSelect($param = null) 
    { 
        try 
        {   
            $seekFields = self::getSeekFields($param);
            $formData = new stdClass();

            if(!empty($param['key']))
            {
                TTransaction::open(self::$database);

                $repository = new TRepository(self::$activeRecord);

                $criteria = self::getSeekFiltersCriteria($param);


                $criteria->add(new TFilter('{CAMPO_ID}', '=', $param['key']));
                $objects = $repository->load($criteria);

                if($objects)
                {
                    $object = end($objects);
                    if($seekFields)
                    {
                        foreach ($seekFields as $seek_field) 
                        {

                            $formData->{"{$seek_field['name']}"} = $object->render("{$seek_field['column']}");
                        }
                    }
                    // $formData->nm_credor = $object->nm_forn;
                    // $formData->vl_pg     = number_format($object->vl_saldo,2,',','.');
                    // $formData->cd_forn   = $object->cd_forn_liq;

                }
                elseif($seekFields)
                {
                    foreach ($seekFields as $seek_field) 
                    {
                        $formData->{"{$seek_field['name']}"} = '';
                    }   
                }
                TTransaction::close();
            }
            else
            {
                if($seekFields)
                {
                    foreach ($seekFields as $seek_field) 
                    {
                        $formData->{"{$seek_field['name']}"} = '';
                    }   
                }
            }

            TForm::sendData($param['_form_name'], $formData);

            if(!empty($param['_seek_window_id']))
            {
                TWindow::closeWindow($param['_seek_window_id']);
            }

            // limpando filtros
            // TSession::setValue(__CLASS__.'_filter_data', NULL);
            // TSession::setValue(__CLASS__.'_filters', NULL);

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        // get the search form data
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

/* ----- gerador ------- */
{ONSEARCH_FILTERS}        
/* --------------------- */


        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        if (isset($param['static']) && ($param['static'] == '1') )
        {
            $class = get_class($this);
            AdiantiCoreApplication::loadPage($class, 'onReload', ['offset' => 0, 'first_page' => 1]);
        }
        else
        {
            $this->onReload(['offset' => 0, 'first_page' => 1]);
        }
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'minierp'
            TTransaction::open(self::$database);

            // creates a repository for Pessoa
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = $this->primaryKey;    
            }
            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $this->limit);

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }


            // $criteria->add(new TFilter('cd_empresa','=',MSispubGlobal::getGlobalCodigoEmpresa()));
            // $criteria->add(new TFilter('anobase','=',MSispubGlobal::getGlobalAnobase()));

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid

                    $this->datagrid->addItem($object);

                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($this->limit); // limit

            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {

    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  $this->showMethods))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }

    public static function TSeek($nome_campo, $arr_auxiliar=[])
    {

        if (count($arr_auxiliar) === 0) 
        {  
            $arr_auxiliar = [ 
                ['name'=> "$nome_campo", 'column'=>'{{CAMPO_ID}}'],
            ];
        }

        ${CAMPO_ID} = new TSeekButton("$nome_campo");
        $seed = AdiantiApplicationConfig::get()['general']['seed'];
        ${CAMPO_ID}_seekAction = new TAction(['Seek{CLASSE}', 'onShow']);
        $seekFilters = [];
        $seekFields = base64_encode(serialize( $arr_auxiliar ));                
        $seekFilters = base64_encode(serialize($seekFilters));
        ${CAMPO_ID}_seekAction->setParameter('_seek_fields', $seekFields);
        ${CAMPO_ID}_seekAction->setParameter('_seek_filters', $seekFilters);
        ${CAMPO_ID}_seekAction->setParameter('_seek_hash', md5($seed.$seekFields.$seekFilters));
        ${CAMPO_ID}->setAction(${CAMPO_ID}_seekAction);
        ${CAMPO_ID}->style="display:none;"; // esconder campo de input.

        return ${CAMPO_ID};
    }


}

/*
        // No formulario, adicionar essas linhas no trecho onde sao definidos os campos do form

        ${CAMPO_ID} = new TSeekButton('{CAMPO_ID}');
        $seed = AdiantiApplicationConfig::get()['general']['seed'];
        ${CAMPO_ID}_seekAction = new TAction(['Seek{CLASSE}', 'onShow']);
        $seekFilters = [];
        $seekFields = base64_encode(serialize([
            ['name'=> '{CAMPO_ID}', 'column'=>'{CAMPO_ID}'],
            ['name'=> '{CAMPO_AUXILIAR}', 'column'=>'{CAMPO_AUXILIAR}']
        ]));
    
        $seekFilters = base64_encode(serialize($seekFilters));
        ${CAMPO_ID}_seekAction->setParameter('_seek_fields', $seekFields);
        ${CAMPO_ID}_seekAction->setParameter('_seek_filters', $seekFilters);
        ${CAMPO_ID}_seekAction->setParameter('_seek_hash', md5($seed.$seekFields.$seekFilters));
        ${CAMPO_ID}->setAction(${CAMPO_ID}_seekAction);


*/
