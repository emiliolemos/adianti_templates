<?php
class ObraObraView extends TPage
{

    private $deleteButton;
    private $database           = 'unit_database';
    private static  $application        = 'ObraObraView';
    private $activeRecord       = 'Obra';
    private $titulo             = 'Cadastro de Obras';
    private $editForm           = 'ObraObraForm';
    private $keyField           = 'id';
    private static $filter_limit = 10;
    private $filterForm;
    protected $pageNavigation;
    protected $datagrid;
    protected $loaded;
    protected $limit;



    use TMGridExportTrait;    

    use MExportTrait;
    use TMGridTrait;


    public function __construct()
    {

        parent::__construct();

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();


        $this->criaFilterForm();

        // creates the datagrid columns
        $column_id        = new TDataGridColumn($this->keyField, 'Id', 'right', '5%');
        $column_descricao = new TDataGridColumn('nm_obra', 'Descricao', 'left','80%');
        $column_valor     = new TDataGridColumn('vl_obra', 'Valor', 'right','10%');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_valor);

        $column_id->setTransformer([$this,'formatRow']);
        MTransform::formatDataGridColumnValue($column_valor);
        
        // creates the datagrid column actions
        $column_id->setAction(new TAction([$this, 'onReload']), ['order' => '{'.$this->keyField.'}']);
        $column_descricao->setAction(new TAction([$this, 'onReload']), ['order' => 'descricao']);

        $this->createDatagridActions();





        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->createPageNavigation();

        // $this->filterForm->addActionLink(_t('New'), new TAction(['($this->activeRecord)Form', 'onEdit']), 'fa:plus green');
        $panel = $this->adicionaPainel('<b>'.$this->titulo.'</b>');
        // $panel->addHeaderWidget($this->adiconaInputSearch('id,descricao'));

        // $panel->addHeaderActionLink('Exibir', new TAction([$this,'onExibeSelecao']),'fa:check-circle');        
        $panel->addHeaderWidget( $this->addSelectButtons() );

        $panel->addHeaderActionLink('Filtros', new TAction([$this, 'onShowCurtainFilters'],['register_state'=>'false']), 'fa:filter');    


        $panel->addHeaderWidget( $this->addExportButtons() );

        


        $panel->addHeaderActionLink (_t('New'), new TAction([$this->editForm, 'onEdit'],['register_state'=>'false']), 'fa:plus green');

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        // $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($panel);


        parent::add($vbox);



    }

    public function onExibeSelecao()
    {
        echo '<pre>';
        var_dump(TSession::getValue(__CLASS__.'_selecao'));
        echo '</pre>';
    }
    public function onSelect($param)
    {

        $selecao = TSession::getValue(__CLASS__.'_selecao');
        $id = $param[$this->keyField];

        if (isset($selecao[$id]))
        {
            unset($selecao[$id]);
        }
        else 
        {
            $selecao[$id] = $id;
        }


        TSession::setValue(__CLASS__.'_selecao',$selecao);
        // $this->onReload($param);
        $this->onReload( func_get_arg(0) );        


    }




    public function addSelectButtons()
    {
        $dropdown = new TDropDown('Seleção', 'fa:download');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction( 'Imprimir', new TAction([$this, 'onExibeSelecao'], ['register_state' => 'false', 'static'=>'1']), 'fa:table fa-fw blue' );
        $dropdown->addAction( 'Anular', new TAction([$this, 'onExibeSelecao'], ['register_state' => 'false', 'static'=>'1']), 'fa:file-excel fa-fw purple' );
        $dropdown->addAction( 'Excluir', new TAction([$this, 'onExibeSelecao'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf fa-fw red' );
        return $dropdown;

    }

    private function createDatagridActions()
    {
        // Acao de selecao de registro. Deve ser antes do create model.
        // register_state = false é usado para nao mudar a URL

        $action1 = new TDataGridAction([$this->editForm, 'onEdit']  , [$this->keyField=>'{'.$this->keyField.'}','register_state'=>'false']);
        $action2 = new TDataGridAction([$this, 'onDelete']          , [$this->keyField=>'{'.$this->keyField.'}']);
        $action3 = new TDatagridAction([$this,'onSelect']           , [$this->keyField=>'{'.$this->keyField.'}', 'register_state'=>'false']);

        // $this->datagrid->addAction($action3,'Selecionar','far:square fa-fw black');
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');


    }

    

    public function criaFilterForm()
    {
        // creates the form
        $this->filterForm = new BootstrapFormBuilder('form_search_Obra');
        $this->filterForm->setFormTitle($this->titulo);
        
        // create the form fields
        $id = new TEntry($this->keyField);
        $descricao = new TEntry('descricao');


        // add the fields
        $this->filterForm->addFields( [ new TLabel('Id') ], [ $id ] );
        $this->filterForm->addFields( [ new TLabel('Descricao') ], [ $descricao ] );


        // set sizes
        $id->setSize('100%');
        $descricao->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->filterForm->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->filterForm->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';

        $this->filterForm->addActionLink(_t('Clear'), new TAction([$this, 'onClearFilterForm']), 'fa:eraser red');


        $btn_close = new TButton('closeCurtain');
        $btn_close->onClick = "Template.closeRightPanel();$(adianti_right_panel).empty();";
        $btn_close->setLabel("Fechar");
        $btn_close->setImage('fas:times');
        
        // instantiate self class, populate filters in construct 
        
        $this->filterForm->addHeaderWidget($btn_close);


    }
    

 

    public function clearSessionFilters()
    {

        TSession::setValue(__CLASS__.'_filter_id'       ,NULL);
        TSession::setValue(__CLASS__.'_filter_descricao',NULL);
        TSession::setValue(__CLASS__ .'_filter_data'    ,NULL );



    }

    public function onSearch()
    {
        // get the search form data
        $data = $this->filterForm->getData();

        //////////////////////////////
        // clear session filters
        $this->clearSessionFilters();

        // TSession::setValue('List_filter_data', Null);
        // limpa dados do keepNavigation
        self::navigationClear(get_class($this));        
        //////////////////////////////
        


        // clear session filters
        // TSession::setValue(__CLASS__.'_filter_id',   NULL);
        // TSession::setValue(__CLASS__.'_filter_descricao',   NULL);

        if ( isset($data->{$this->keyField} ) AND ( $data->{$this->keyField} )  ) 
        {
            $filter = new TFilter($this->keyField, '=', $data->{$this->keyField}); // create the filter
            TSession::setValue(__CLASS__.'_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->descricao) AND ($data->descricao)) {
            $filter = new TFilter('descricao', 'like', "%{$data->descricao}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_descricao',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->filterForm->setData($data);
        
        // keep the search data in the session
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        
        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;


        TScript::create("Template.closeRightPanel();");
        TScript::create("$(adianti_right_panel).empty();");



        $this->onReload($param);
    }
    

    public function onReload($param = NULL)
    {


        $objects = NULL;

        try
        {

            TScript::create("$(adianti_right_panel).empty();");

            // open a transaction with database self::$database
            TTransaction::open($this->database);
            
            // creates a repository for ($this->activeRecord)
            $repository = new TRepository($this->activeRecord);
            $this->limit = self::$filter_limit;
            // creates a criteria
            $criteria = new TCriteria;

            // atualiza ou recupera os parametros de paginação com dados da sessão
            $param = self::navigationUpdate($param, get_class($this));            
            

            // default order
            if (empty($param['order']))
            {
                $param['order'] = $this->keyField;
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $this->limit);
            

            if (TSession::getValue(__CLASS__.'_filter_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_descricao')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_descricao')); // add the session filter
            }

            

            $objects = $this->onReloadFinal($repository, $criteria, $param);


            // close the transaction
            TTransaction::close();
            $this->loaded = true;

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }

        return $objects;
    }
    

    
    /**
     * Delete a record
     */
    public function Delete($param)
    {
        try
        {
            $key=$param['key']; // get the parameter $key
            TTransaction::open($this->database); // open a transaction with database
            $object = new $this->activeRecord($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            TApplication::loadPage(self::$application,'onReload',[]);
           
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    


    



}
