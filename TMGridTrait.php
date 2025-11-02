<?php

use Adianti\Core\AdiantiCoreApplication;
use Adianti\Registry\TSession;

trait TMGridTrait
{

    
    protected function createPageNavigation()
    {
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
    }
    
    public function adicionaPainel($titulo, $form=NULL)
    {
        
        $panel = new TPanelGroup($titulo);
        if (isset($form))
            $panel->add($form);
        
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        return $panel;

    }

    // public function createDropDownFilter()
    // {
    //     $dropdown = new TDropDown( TSession::getValue(__CLASS__ . '_limit') ?? '10', '' );
    //     $dropdown->setPullSide('right');
    //     $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
    //     $dropdown->addAction('10 registros', new TAction([$this, 'onChangeLimit'], ['limit' => '10']));
    //     $dropdown->addAction('20 registros', new TAction([$this, 'onChangeLimit'], ['limit' => '20']));
    //     $dropdown->addAction('50 registros', new TAction([$this, 'onChangeLimit'], ['limit' => '50']));
    //     $dropdown->addAction('100 registros', new TAction([$this, 'onChangeLimit'], ['limit' => '100']));

    //     return $dropdown;
    // }

    // public function adicionaPainel($titulo)
    // {
    //     $panel = new TPanelGroup($titulo);
        
    //     $hbox = new THBox();
    //     $hbox->add($this->createDropDownFilter() . ' Visualizar');
    //     $hbox->add($this->pageNavigation); // Adicionando navegação à esquerda
    //     $hbox->style = 'display: flex; justify-content: space-between; align-items: center;'; // Flexbox
    //     $panel->addFooter($hbox);
        
    //     $panel->add($this->datagrid);
    //     return $panel;
    // }

    /** Colocar este método dentro das classes Datagrid  */
    // public static function onChangeLimit($param)
    // {
    //     TSession::getValue(__CLASS__ . '_limit', $param['limit']);
    //     AdiantiCoreApplication::loadPage(__CLASS_, 'onReload');
    // }

    /** Após a primeira declaração do TCriteria dentro do onReload das classes Datagrid adicionar esta linha */
    // $this->limit = TSession::getValue(__CLASS__ . '_limit') ?? self::$filter_limit;

    public function adiconaInputSearch($_campos)
    {
        // search box
        $input_search = new TEntry('input_search');
        $input_search->placeholder = _t('Search');
        $this->datagrid->enableSearch($input_search, $_campos);        
        // $input_search->setSize('100%');
        return $input_search;
    }

    public static function navigationClear($class_name)
    {
        // dados do keepNavigation
        TSession::setValue("{$class_name}_filter_order", NULL);
        TSession::setValue("{$class_name}_filter_offset", NULL);
        TSession::setValue("{$class_name}_filter_limit", NULL);
        TSession::setValue("{$class_name}_filter_direction", NULL);
        TSession::setValue("{$class_name}_filter_page", NULL);
        TSession::setValue("{$class_name}_filter_first_page", NULL);
    }

    public static function navigationUpdate($param, $class_name)
    {
        if (!isset($param['order'])){
            if (TSession::getValue("{$class_name}_filter_order"))
                $param['order'] = TSession::getValue("{$class_name}_filter_order");
        } else {
            TSession::setValue("{$class_name}_filter_order", $param['order']);
        }
        
        if (!isset($param['offset'])){
            if (TSession::getValue("{$class_name}_filter_offset"))
                $param['offset'] = TSession::getValue("{$class_name}_filter_offset");
        } else {
            TSession::setValue("{$class_name}_filter_offset", $param['offset']);
        }
        
        if (!isset($param['limit'])){
            if (TSession::getValue("{$class_name}_filter_limit"))
                $param['limit'] = TSession::getValue("{$class_name}_filter_limit");
        } else {
            TSession::setValue("{$class_name}_filter_limit", $param['limit']);
        }
        
        if (!isset($param['direction'])){
            if (TSession::getValue("{$class_name}_filter_direction"))
                $param['direction'] = TSession::getValue("{$class_name}_filter_direction");
        } else {
            TSession::setValue("{$class_name}_filter_direction", $param['direction']);
        }
        
        if (!isset($param['page'])){
            if (TSession::getValue("{$class_name}_filter_page"))
                $param['page'] = TSession::getValue("{$class_name}_filter_page");
        } else {
            TSession::setValue("{$class_name}_filter_page", $param['page']);
        }
        
        if (!isset($param['first_page'])){
            if (TSession::getValue("{$class_name}_filter_first_page"))
                $param['first_page'] = TSession::getValue("{$class_name}_filter_first_page");
        } else {
            TSession::setValue("{$class_name}_filter_first_page", $param['first_page']);
        }
        
        // retorna os parametros eventualmente modificados
        return $param;
    }


    public function onReloadFinal($repository,$criteria, $param  )
    {

        


            $limit = $criteria->getProperty('limit');

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            

            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }


            
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
            
            $old_criteria = clone $criteria;


            $criteria->resetProperties();
            $count= $repository->count($criteria);

            // $criteria = $old_criteria;


            
            $this->pageNavigation->setCount($count); // count of records
            
            /** Para contabilizar a quantidade de registros no pageNavigation, descomentar estas 3 linhas abaixo */
            // $this->pageNavigation->enableCounters();
            // $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
            // $this->pageNavigation->setWidth($this->datagrid->getWidth());

            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            if (isset($param['exporta']))
            {
                $objects = $repository->load($criteria, FALSE);
            } else {
                $objects = $repository->load($old_criteria, FALSE);

            }

            return $objects;

    }


    public function show()
    {


        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
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

        // fechando cortina lateral com ESC
        $str = "
        $(document).ready(function(){
            $(document).bind('keydown', function(e) { 
                if (e.which == 27) {
                    Template.closeRightPanel();
                    $(adianti_right_panel).empty();                    
                }
            }); 
        });
        ";
        // TScript::create($str);   

        parent::show();


    }

    public static function onDelete($param)
    {
        // define the delete action
        $action1 = new TAction([__CLASS__, 'Delete']);
        $action1->setParameters($param); // pass the key parameter ahead
        
        $action2 = NULL;

        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action1, $action2);
    }
    


    public function onClearFilterForm()
    {
        

        $this->filterForm->clear( TRUE );

        $this->clearSessionFilters();

        $this->onReload();


    }

    public  static function onShowCurtainFilters($param = null)
    {

        try
        {

            // create empty page for right panel
            

            $page = new TPage;

            $page->setTargetContainer('adianti_right_panel');
            
            $page->setProperty('override', 'true');
            $page->setPageName(__CLASS__);
            $embed = new self;

            $page->add($embed->filterForm);
            $page->setIsWrapped(true);
            $page->show();


        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    

    public function formatRow($id,$object,$row)
    {
        $selecao = TSession::getValue(__CLASS__.'_selecao');
        if ($selecao)
        {
            if (in_array( (int) $id, array_keys($selecao)))
            {
                $row->style = 'background:#abdef9';

                $button = $row->find('i', ['class'=>'far fa-square fa-fw black'])[0];

                if ($button)
                {
                    $button->class = 'far fa-check-square fa-fw black';
                }
            }
        }

        return $id;

    }


    public function addExportButtons()
    {
        $dropdown = new TDropDown('Ações', 'fa:hammer');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction( 'Exportar CSV', new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table fa-fw blue' );
        $dropdown->addAction( 'Exportar EXCEL', new TAction([$this, 'onExportXLS'], ['register_state' => 'false', 'static'=>'1']), 'fa:file-excel fa-fw purple' );
        $dropdown->addAction( 'Exportar PDF', new TAction([$this, 'm_onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf fa-fw red' );
        $dropdown->addAction( 'Exportar XML', new TAction([$this, 'onExportXML'], ['register_state' => 'false', 'static'=>'1']), 'fa:code fa-fw green' );
        return $dropdown;
    }


    public function addQuickSearch()
    {

        $input_quick_search = new TEntry('input_quick_search');
        $input_quick_search->setSize('200');
        $input_quick_search->placeholder = 'Buscar';
        $btnQS = TButton::create('find', [$this, 'onQuickSearch'], '', 'fa:search');
        $btnQS->style= 'height: 37px;';
        $btnQS->{'title'} = 'Buscar';
        $btnClearQS = TButton::create('clear', [$this, 'onClearQuickSearch'], '', 'fa:ban red');
        $btnClearQS->style= 'width: 32px; height: 37px;';
        $btnClearQS->{'title'} = 'Limpar filtro';
        
        $this->quick_search = new TForm('quick_search');
        $this->quick_search->style = 'float:left;display:flex';
        $this->quick_search->add($input_quick_search, true);
        $this->quick_search->add($btnQS, true);
        $this->quick_search->add($btnClearQS, true);
        
        $this->quick_search->setData( TSession::getValue(__CLASS__.'_filter_dataQS') );

        return $this->quick_search;

    }




    public function onClearQuickSearch($param = NULL)
    {
        if (isset($this->quick_search))
            $this->quick_search->clear(FALSE);
        $this->onClearSessionQuickSearch();
        self::clearNavigation();
        $this->resetParamAndOnReload();
    }
    
    public function onClearSessionQuickSearch()
    {
        // clear session filters
        TSession::setValue(__CLASS__.'_filter_input_quick_search', NULL);
        TSession::setValue(__CLASS__.'_filter_dataQS',             NULL);
    }

    function onClearSessionSelectList()
    {        
        // TSession::setValue(__CLASS__.'selected_ids', NULL);                
        // $this->onUpdateBtnSelectAll();
    }


	public function onUpdateBtnSelectAll()
	{
	    // $selected_ids = TSession::getValue(__CLASS__.'selected_ids');
        
        // if (isset($selected_ids) && !empty($selected_ids))
        // {
        //     $this->selectAll->setImage('far:check-square');
        //     $this->selectAll->{'title'} = 'Desmarcar todos';
        // }
        // else
        // {
        //     $this->selectAll->setImage('far:square');
        //     $this->selectAll->{'title'} = 'Marcar todos';
        // }        
	}


    public static function clearNavigation()
    {
        TSession::setValue(__CLASS__.'_filter_offset', NULL);
        TSession::setValue(__CLASS__.'_filter_page', NULL);
        TSession::setValue(__CLASS__.'_filter_first_page', NULL);
    }

    private function resetParamAndOnReload()
    {
        $param = [];
        $param['offset']     = 0;
        $param['first_page'] = 1;
        $this->onReload($param);
    }

    public function init()
    {
        $this->clearSessionFilters();
        $this->onClearQuickSearch();
    }

}
?>