<?php
class {CLASSE_GRID} extends TPage
{
    private $deleteButton;
    private $database           = {ACTIVE_RECORD}::DATABASE; //'{DATABASE}';
    private static $application = '{CLASSE_GRID}';
    private $activeRecord       = '{ACTIVE_RECORD}';
    private $titulo             = '{TITULO}';
    private $editForm           = '{CLASSE_FORM}';
    private $keyField           = '{CAMPO_ID}';
    private static $filter_limit = 10;
    private $filterForm;
    protected $pageNavigation;
    protected $datagrid;
    protected $loaded;
    protected $limit;
    protected $master_id = '';
    protected $master_field = ''; // Aqui colocar o nome do campo que sera usado nos criterios
    private $quick_search; // form

    // use TMGridExportTrait;    
    use TMGridExportTrait;    

    use MExportTrait;
    use TMGridTrait;

    public function __construct($param=NULL)
    {
        parent::__construct();

        // Caso queira usar a GLOBAL_ANOBASE no titulo 
        // $ano = intval(MSispubGlobal::getGlobalAnobase());
        // $this->titulo .= ($ano>0) ? " - $ano " : " <span style='color:red;'> - EXERCICIO NAO INFORMADO</span>";

        // Este trecho so deve ser usado se a grid for chamada de outro lugar
        // if (isset ( $param['{ACTIVE_RECORD}MASTER_ID']) )
        // {
        //     TSession::setValue(__CLASS__ . "{ACTIVE_RECORD}MASTER_ID", $param['{ACTIVE_RECORD}MASTER_ID']);
        // }
        // $this->master_id = TSession::getValue(__CLASS__ . "{ACTIVE_RECORD}MASTER_ID");
        // $this->titulo .= ' - ' . $this->master_id;

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();

        // Habilitando scroll vertical
        // $this->datagrid->setHeight(500);
        // $this->datagrid->makeScrollable();

        // Agrupamento de coluna
        // $this->datagrid->setGroupColumn('nome_do_campo', "<b style='color:red'><i>{descricao_campo}</i></b>");

        $this->criaFilterForm();

        /// Limpando o filtro do grid detalhe
        // (new ClasseDetalheGrid())->clearSessionFilters();

        // creates the datagrid columns
        // $column_id        = new TDataGridColumn($this->keyField   , 'Id'        , 'right', '5%');
        // $column_descricao = new TDataGridColumn('descricao'       , 'Descricao' , 'left','90%');

        /* --------gerador ------------ */
        {CREATE_GRID_COLUMNS}
        /* ---------------------------- */

        // add the columns to the DataGrid
        // $this->datagrid->addColumn($column_id);
        // $this->datagrid->addColumn($column_descricao);

        // Escondendo colunas. Muito util quando se utiliza a opcao de SELECT
        // $col_id_empenho->setVisibility(false);

        /* --------gerador ------------ */
        {ADD_GRID_COLUMNS}
        /* ---------------------------- */

        // substituir pela variavel do ID
        $col_{CAMPO_ID}->setTransformer([$this,'formatRow']);

        // Formatacoes
        // MTransform::formatDataGridColumnValue($coluna_valor,2); // valor
        // MTransform::formatDataGridDate($coluna_data); // data

        // $coluna->setTransformer(
        //     function($value, $object, $row)
        //     {
		// 		$descricao = 'teste';
        //         return $descricao;
        //     }
        // );

        // Ordenacao de colunas
        // $column_id->setAction(new TAction([$this, 'onReload']), ['order' => "{$this->keyField}"]);
        // $column_descricao->setAction(new TAction([$this, 'onReload']), ['order' => 'descricao']);

        // Coluna de total, se houver
        // $nome_da_coluna->enableTotal('sum', '', 2, ',', '.');
        // Este comando permite mostrar o sum da coluna corretamente
        // $this->datagrid->generateHiddenFields();

        // Outra forma de calcular o total de uma coluna
        // $col_vl_emp->setTotalFunction( 
        //     function( $valores )
        //     {         
        //         return 'Total '.number_format(array_sum( (array) $valores ),2,',','.');
        // } );


        $this->createDatagridActions();

        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->createPageNavigation();

        // $this->filterForm->addActionLink(_t('New'), new TAction(['ObraTabChecklistForm', 'onEdit']), 'fa:plus green');
        $panel = $this->adicionaPainel('<b>'.$this->titulo.'</b>');
        // $panel->addHeaderWidget($this->adiconaInputSearch('id,descricao'));

        // Adicionando quicksearch no painel
        $panel->addHeaderWidget($this->addQuickSearch());


        // Se for necessario desabilitar botoes, caso o GLOBAL_ANOBASE nao esteja definido
        // if ($anobase > 0)
        // {
            // $panel->addHeaderActionLink('Exibir', new TAction([$this,'onExibeSelecao']),'fa:check-circle');        
            $panel->addHeaderWidget( $this->addSelectButtons() );
            $panel->addHeaderActionLink('Filtros', new TAction([$this, 'onShowCurtainFilters'],['register_state'=>'false']), 'fa:filter');    
            $panel->addHeaderWidget( $this->addExportButtons() );
            $panel->addHeaderActionLink (_t('New'), new TAction([$this->editForm, 'onEdit'],['register_state'=>'false'
                // , {ACTIVE_RECORD}MASTER_ID => $this->master_id
            ]), 'fa:plus green');
        // }
        // $panel->addHeaderActionLink('Voltar', new TAction(['{CLASSE_GRID}', 'onReload'],$param), 'fas: fa-arrow-left red');    

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

        // Botão para acessar outra aplicacao
        // $action4 = new TDatagridAction(['OutraAplicacaoGrid','onReload']    , [
        //     'MASTER_ID'=>'{'.$this->keyField.'}'
        // ]);
        // $this->datagrid->addAction($action4 ,'Outra Aplicacao', 'fas: fa-book');
    }

    public function criaFilterForm()
    {
        // creates the form
        $this->filterForm = new BootstrapFormBuilder('form_search_{ACTIVE_RECORD}');
        $this->filterForm->setFormTitle($this->titulo);
        
        // create the form fields
        // $id = new TEntry($this->keyField);
        // $descricao = new TEntry('descricao');
        /* ------------ gerador ----------------- */
        {CREATE_FILTER_COLUMNS}        
        /* -------------------------------------- */

        // add the fields
        // $this->filterForm->addFields( [ new TLabel('Id') ], [ $id ] );
        // $this->filterForm->addFields( [ new TLabel('Descricao') ], [ $descricao ] );

        {ADD_FILTER_COLUMNS}                

        // set sizes
        // $id->setSize('100%');
        // $descricao->setSize('100%');
        $this->filterForm->setFieldSizes('100%');        

        
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
        // Adicionando ANOBASE na filtragem
        // TSession::setValue(__CLASS__.'_filter_anobase' ,NULL);

        // TSession::setValue(__CLASS__.'_filter_id'       ,NULL);
        // TSession::setValue(__CLASS__.'_filter_descricao',NULL);

        /* --- gerador ----*/        
        {CLEAR_FILTERS}
        /* --------------- */

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

        // Adicionando ANOBASE na filtragem
        // $filter = new TFilter('anobase', '=', MSispubGlobal::getGlobalAnobase()); // create the filter
        // TSession::setValue(__CLASS__.'_filter_anobase',   $filter); // stores the filter in the session


        // if ( isset($data->{$this->keyField} ) AND ( $data->{$this->keyField} )  ) 
        // {
        //     $filter = new TFilter($this->keyField, '=', $data->{$this->keyField}); // create the filter
        //     TSession::setValue(__CLASS__.'_filter_id',   $filter); // stores the filter in the session
        // }

        // if (isset($data->descricao) AND ($data->descricao)) {
        //     $filter = new TFilter('descricao', 'like', "%{$data->descricao}%"); // create the filter
        //     TSession::setValue(__CLASS__.'_filter_descricao',   $filter); // stores the filter in the session
        // }

        /* ----- gerador ------- */
        {ONSEARCH_FILTERS}        
        /* --------------------- */
        
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
            // TTransaction::setLogger(new TLoggerSTD); // standard output
            
            // creates a repository for ObraTabChecklist
            $repository = new TRepository($this->activeRecord);
            $this->limit = self::$filter_limit;

            /* 
            // Quando for necessario criar algo como select * from tabela where (condicao1 or condicao2)
            and demais criterios, deve-se usar um novo TCriteria, e fazer algo assim:
            $criteria1 = new TCriteria;
            $criteria1->add(new TFilter( condicao1), TExpression::OR_OPERATOR)            
            $criteria1->add(new TFilter( condicao2), TExpression::OR_OPERATOR)            

            E depois incluir um criterio dentro do outro
            $criteria->($criteria1);
            */

            // creates a criteria
            $criteria = new TCriteria;

            // Criteria de quicksearch
            $criteria_qs = new TCriteria;
            if (TSession::getValue(__CLASS__.'_filter_input_quick_search')) {
                foreach (TSession::getValue(__CLASS__.'_filter_input_quick_search') as $filter) {
                    $criteria_qs->add($filter, TExpression::OR_OPERATOR); // add the session filter
                }
                $criteria->add($criteria_qs, TExpression::AND_OPERATOR);
            }

            // Descomentar essa linha, caso a grid seja chamada de outro lugar
            // $criteria->add(new TFilter($this->master_field,"=",$this->master_id),  TExpression::AND_OPERATOR);

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

            // Adicionando ANOBASE na filtragem
            // if (MSispubGlobal::getGlobalAnobase()) 
            // {
            //     $criteria->add(new TFilter('anobase','=',MSispubGlobal::getGlobalAnobase())); 
            // }

            // if (TSession::getValue(__CLASS__.'_filter_id')) {
            //     $criteria->add(TSession::getValue(__CLASS__.'_filter_id')); // add the session filter
            // }

            // if (TSession::getValue(__CLASS__.'_filter_descricao')) {
            //     $criteria->add(TSession::getValue(__CLASS__.'_filter_descricao')); // add the session filter
            // }

            /* --------- gerador ---------- */
            {ONRELOAD_FILTERS}
            /* ---------------------------- */

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
            $object = new $this->activeRecord($key,FALSE); // instantiates the Active Record
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

    public function onQuickSearch()
    {
        // get the search form data
        $dataQS = $this->quick_search->getData();
        
        $this->onClearSessionQuickSearch();
        // $this->onClearSessionSelectList();
        self::clearNavigation();

        if (isset($dataQS->input_quick_search) AND ($dataQS->input_quick_search)) 
        {
            $filterQS = [];
            // $filterQS[] = new TFilter('nm_funcao', 'like',   "%{$dataQS->input_quick_search}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_input_quick_search',        $filterQS); // stores the filter in the session
        }
        
        // fill the form with data again
        $this->quick_search->setData($dataQS);
        
        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_dataQS', $dataQS);

        $this->resetParamAndOnReload();
    }
}