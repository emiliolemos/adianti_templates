<?php
trait {TRAITNAME}Trait
{


    public function add{TRAITNAME}Fields()
    {

        $this->form->appendPage('{TRAITNAME}');

        //////////////////////////////////////////////////////////////////
        // Definindo campos do formulario

        ${TRAITNAME}_uniqid    = new THidden('{TRAITNAME}_uniqid');
        ${TRAITNAME}_id        = new THidden('{TRAITNAME}_id');

        // ${TRAITNAME}_file_path = MField::TMFile('{TRAITNAME}_file_path');

// ****** gerador *************
// Os campos ID e MASTER_ID devem ser removidos daqui

{TRAIT_ADD_FIELDS}

// ****************************


        //////////////////////////////////////////////////////////////////
        // Adicionando campos no formulario

        $this->form->addFields([${TRAITNAME}_uniqid],[${TRAITNAME}_id] );
		
// ****** gerador *************
// Os campos ID e MASTER_ID devem ser removidos daqui
{TRAIT_ADD_FORM_FIELDS}
// ****************************


        // $this->form->addFields([new TLabel('Arquivo')       , ${TRAITNAME}_file_path] );
        // $row = $this->form->addFields([new TLabel('Data')     , ${traitname}_dtlanc     ], 
        //                               [new TLabel('Descricao'), ${traitname}_descricao] );
        // $row->layout = ['col-sm-10', 'col-sm-2'];

		// Adicionando botoes
        $btn1 = TButton::create('add_{TRAITNAME}', [$this, 'on{TRAITNAME}Add'], 'Registrar', 'fa:plus-circle green');
        $btn1->getAction()->setParameter('static','1');
        $btn2 = TButton::create('clear_{TRAITNAME}', [$this, 'on{TRAITNAME}Clear'], 'Limpar', 'fa:eraser red');
        $btn2->getAction()->setParameter('static','1');
        $row = $this->form->addFields( [], [$btn1],[$btn2] );
        $row->layout = ['col-sm-8', 'col-sm-2', 'col-sm-2'];


		// Datagrid do detalhe
        $this->{TRAITNAME}_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->{TRAITNAME}_list->setHeight(200);
        $this->{TRAITNAME}_list->makeScrollable();
        $this->{TRAITNAME}_list->setId('{TRAITNAME}s_list');
        $this->{TRAITNAME}_list->generateHiddenFields();
        $this->{TRAITNAME}_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";

		// Definindo colunas
		$colunas=[];
        $colunas[]  = new TDataGridColumn( 'uniqid', 'Uniqid', 'center');
        $colunas[]  = new TDataGridColumn( 'id', 'ID', 'center');

        // $col_path       = new TDataGridColumn( 'file_path', 'Arquivo', 'left'); // fica invisivel        
        // Coluna de total, se houver
        // $nome_da_coluna->enableTotal('sum', '', 2, ',', '.');

// ****** gerador *************
// Os campos ID e MASTER_ID devem ser removidos daqui
{TRAIT_ADD_COLUMN_FIELDS}
// ****************************

        // $col_path       = new TDataGridColumn( 'file_path', 'Arquivo', 'left'); // fica invisivel

		// Formatando colunas
        // MTransform::formatDataGridDate($col_dtlanc);
		
		// Adicionando colunas na datagrid
		foreach ($colunas as $coluna) {
			$this->{TRAITNAME}_list->addColumn( $coluna );			
		}

		// As 2 primeiras colunas serao sempre invisiveis
        $colunas[0]->setVisibility(false);        
        $colunas[1]->setVisibility(false);
        // $col_path->setVisibility(false);        

        // Criando acoes na datagrid
        $action1 = new TDataGridAction([$this, 'onEdit{TRAITNAME}'] );
        $action1->setFields( ['uniqid', '*'] );
        $action2 = new TDataGridAction([$this, 'onDelete{TRAITNAME}']);
        $action2->setField('uniqid');

        // $action_download = new TDataGridAction([__CLASS__, 'onDownload{TRAITNAME}']);
        // $action_download->setField('file_path');

        // Caso deseje incluir botao de impressao no detalhe
        // $action4 = new TDatagridAction([$this,'onPrint{TRAITNAME}']           , [$this->keyField=>'{'.$this->keyField.'}', 'register_state'=>'false']);
        // $action4->setParameter('static','1');
        // 


        // Adicionando acoes na datagrid
        $this->{TRAITNAME}_list->addAction($action1, _t('Edit'), 'far:edit blue');
        $this->{TRAITNAME}_list->addAction($action2, _t('Delete'), 'far:trash-alt red');
        // $this->{TRAITNAME}_list->addAction($action_download, _t('Download'), 'fa:download blue');

        $this->{TRAITNAME}_list->createModel();
        $panel = new TPanelGroup('Relação de {TRAITNAME}');

        $panel->add($this->{TRAITNAME}_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $panel->addFooter('');


        $this->form->addContent([$panel]);




    }

    // Limpando campos do formulario 
    public static function on{TRAITNAME}Clear($param)
    {

        $data = new stdClass;
        $data->{TRAITNAME}_uniqid     = '';
        $data->{TRAITNAME}_id         = '';

// ****** gerador *************        
// Os campos ID e MASTER_ID devem ser removidos daqui
{TRAIT_CLEAR_FIELDS}
// ****************************

        // send data, do not fire change/exit events
        TForm::sendData( self::getFormName(), $data, false, false );
        
    }

    // Preenchendo campos na edição
    public static function onEdit{TRAITNAME}($param)
    {
		// $param['campo'] se refere ao campo fisico da tabela
		
        $data = new stdClass;
        $data->{TRAITNAME}_uniqid     = $param['uniqid'];
        $data->{TRAITNAME}_id         = $param['id'];


// ****** gerador *************        
// Os campos ID e MASTER_ID devem ser removidos daqui
{TRAIT_ONEDIT_FIELDS}

// Quando houver campo de valor, fazer o seguinte. Usar number_format
// $data->SofcReceitaVinculadaNatureza_tx_percent        = number_format($param['tx_percent'],2,',','.');

// ****************************

        // send data, do not fire change/exit events
        TForm::sendData( self::getFormName(), $data, false, false );

    }


    // Excluindo o registro do detalhe
    public static function onDelete{TRAITNAME}($param)
    {

        self::on{TRAITNAME}Clear($param);
        
        // remove row
        TDataGrid::removeRowById('{TRAITNAME}s_list', $param['uniqid']);
    }


    /*
        public function onDownload{TRAITNAME}($param)
        {
            TPage::openFile($param['file_path']);
        }

    */

    // Adicionando registro no detalhe
    public function on{TRAITNAME}Add($param)
    {

        try
        {
            $this->form->validate();
            $data = $this->form->getData();
            
            
            $uniqid = !empty($data->{TRAITNAME}_uniqid) ? $data->{TRAITNAME}_uniqid : uniqid();
			
            // Configurando os campos que serao incluidos na lista
            $grid_data = ['uniqid'      => $uniqid
                          ,'id'          => $data->{TRAITNAME}_id

// ****** gerador *************						  
// Os campos ID e MASTER_ID devem ser removidos daqui
{TRAIT_ONADD_FIELDS}
// ****************************
            ];
            
            // inserindo linha dinamicamente no detalhe
            $row = $this->{TRAITNAME}_list->addItem( (object) $grid_data );
            $row->id = $uniqid;
            
            TDataGrid::replaceRowById('{TRAITNAME}s_list', $uniqid, $row);
            
            // Limpar o fomulario de detalhes apos insert
            self::on{TRAITNAME}Clear($param);

        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }        

    }

    // Persistindo o detalhe
    public function saveDetalhe{TRAITNAME}($param, $obj)
    {

        {ACTIVE_RECORD}::where('{master_id}', '=', $obj->{master_id})->delete();

        if( !empty($param['{TRAITNAME}s_list_id'] ))
        {
            foreach( $param['{TRAITNAME}s_list_id'] as $key => $item_id )
            {


                $item = new {ACTIVE_RECORD};

                $item->id          = $item_id;
                $item->{master_id} = $obj->{master_id};		

// ****** gerador *************                
// Os campos ID e MASTER_ID devem ser removidos daqui
{TRAIT_ONSAVE_FIELDS}
// ****************************				
                $item->store();


                
            }
            

        }

    }


    // Adicionando os detalhes numa lista
    public function onEditLoad{TRAITNAME}s($obj)
    {

        $items = $obj->get{TRAITNAME}s();
        foreach ($items as $item)
        {
            $item->uniqid = uniqid();
            $row = $this->{TRAITNAME}_list->addItem($item);
            $row->id = $item->uniqid;
        }

    }

}

/*

	No formulario Mestre, incluir as seguintes alteracoes
	
	Incluir a linha no inicio do formulario
    // Parte 1    
	use {TRAITNAME}Trait;
	
    // Parte 2
	apos o trecho $this->form->addFields.... incluir
	$this->add{TRAITNAME}Fields();

	// Parte 3
	no Evento onSave incluir apos $object->store();
	$this->saveDetalhe{TRAITNAME}($param, $object);
	
    // Parte 4
	no Evento onEdit, spos $object = new $this->activeRecord($key);
	$this->onEditLoad{TRAITNAME}($object);
	
	Na classe {CLASSE_MASTER} incluir o metodo
	
	public function get{TRAITNAME}s()
	{
		return {ACTIVE_RECORD}::where('{master_id}','=', $this->{classe_master_id})->load();
	}


    // Se for imprimir alguma coisa
    public function onPrint{TRAITNAME}($param)
    {
        / / Aqui a classe de impressao que sera chamada
        $pdf = new Rel{TRAITNAME}($param);
        $pdf->imprime();
    }
    
	
	
	
	
*/