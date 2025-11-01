<?php
/**
 * ObraTabChecklistForm Form
 * @author  <your name here>
 */
class {CLASSE_FORM} extends TPage
{
    protected $form; 
    private $database       = {ACTIVE_RECORD}::DATABASE; //'{DATABASE}';
    private $activeRecord   = '{ACTIVE_RECORD}';
    private $listView       = '{CLASSE_GRID}';
    private $fieldFocus     = 'descricao';
    private $keyField       = '{CAMPO_ID}';
    private $formTitle      = '{TITULO}';
    private $embbed;
    private $larguraJanela  = '80%'; // 1000px
    const   FORM_NAME       = 'form_'.__CLASS__;

    //////////////////////////////////////////////////////////////////////////////
    // Quando o formulario usar Traits, colocar aqui
    // Parte 1

    use MFormTrait;

    //////////////////////////////////////////////////////////////////////////////

    public function __construct( $param, $embbed=false )
    {
        parent::__construct();

        $this->embbed = $embbed; // indica se o formulario está embutido noutra coisa.
                                // Se estiver, alguns botoes e funcionalidades ficam limitados.

        if (!$this->embbed) 
        {
            // Se for trabalhar com cortina lateral, decomentar essa linha
            // parent::setTargetContainer('adianti_right_panel');
        }

        // $ano = intval(MSispubGlobal::getGlobalAnobase());
        // $this->formTitle .= ($ano>0) ? " - $ano " : " <span style='color:red;'> - EXERCICIO NAO INFORMADO</span>";


        // creates the form
        $this->form = new BootstrapFormBuilder(self::FORM_NAME); //$this->getFormName());
        $this->form->setClientValidation(true);

        if (!$this->embbed) {
            $this->form->setFormTitle($this->formTitle);
        }
        
        /////////////////////////////////////////////////////////////////////////////
        // create the form fields
        // $id                       = new THidden($this->keyField);

        /* ------- gerador ---------------------*/        

        {CREATE_FORM_FIELDS}

        // Quando o formulario tiver um botao de localizar
        /*
            $id_liq = new TSeekButton('id_liq');
            $nr_liq = new TEntry('nr_liq');
            $seed = AdiantiApplicationConfig::get()['general']['seed'];
            $id_liq_seekAction = new TAction(['SeekLiquidacaoViewNotaPagamento', 'onShow']);
            $seekFilters = [];
            $seekFields = base64_encode(serialize([
                ['name'=> 'id_liq', 'column'=>'{id_liq}'],
                ['name'=> 'nr_liq', 'column'=>'{nr_liq}']
            ]));

            $seekFilters = base64_encode(serialize($seekFilters));
            $id_liq_seekAction->setParameter('_seek_fields', $seekFields);
            $id_liq_seekAction->setParameter('_seek_filters', $seekFilters);
            $id_liq_seekAction->setParameter('_seek_hash', md5($seed.$seekFields.$seekFilters));
            $id_liq->setAction($id_liq_seekAction);
        */

        // Quando o form tiver campos de um formulario MESTRE
        // if (isset($param['{ACTIVE_RECORD}MASTER_ID']))
        //     $cd_unid->setValue($param['{ACTIVE_RECORD}MASTER_ID']);

        /* --------------------------------------*/        
        // Adicionando ANOBASE ao formulario
        // $anobase   = MField::fieldInteger('anobase','9999');
        // if (empty($anobase->getValue()))
        //   $anobase->setValue(MSispubGlobal::getGlobalAnobase());
        // $this->form->addFields( [ MField::requiredLabel('Anobase') ], [ $anobase ] );

        // Campo Tipo de Pessoa F/J
        // Primeira forma de resolver
        // $tipo_pfpj              = MField::selectTipoPessoaFisicaJuridica('tipo_pfpj');
        // $tipo_pfpj->setChangeAction(new TAction([$this,'onChangeTipoAction']));
        // A definicao do metodo está no final do arquivo.
        // A primeira forma nao atualiza a mascara em algumas situacoes

        // Segunda forma de resolver
        // Aparentemente a segunda forma funcionou melhor
        // $tipo_pfpj = new THidden('tipo_pfpj');

        // $campo01                  = MField::fieldUppercase('descricao');
        // $campo02                  = MField::TMFile('arquivo');
        // $campo03                  = MField::selectMes('mes');
        // $campo04                  = MField::fieldValor('valor');
        // $campo05                  = MField::fieldDate('data');
        // $campo06                  = MField::fieldInteger('integer');
        // $campo06                  = MField::fieldTelefone('telefone');
        // $campo07                  = MField::fieldHtmlEditor('editor');
        // $campo08                  = new TDBCombo('marca_id', $this->database, 'Marca', 'id', 'descricao');

        //////////////////////////////////////////////////////////////////////////////
        // add the fields
        // $this->form->addFields( [ new TLabel('Id')        ], [ $id ] );

        /* ------- gerador ---------------------*/        
        {ADD_FORM_FIELDS}
        /* --------------------------------------*/

        // $this->form->addFields( [MField::requiredLabel('Descricao') ], [ $descricao ] );
        //         $row = $this->form->addFields( 
        //             [new TLabel('Codigo da Obra')           ,$nr_obra ],
        //             [MField::requiredLabel('Exercicio')				,$anobase ],
        //             [ MField::requiredLabel('Valor da Obra')           ,$vl_obra ]                                 
        //         );                                  
        //         $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        //   validacoes
        // MField::validarCampo($nm_obra            ,'Descricao Resumida'  , ['req'=>'', 'minlen'=>'4']);
        // MField::validarCampo($id_regime_execucao ,'Regime de Execução'  , ['req'=>'']);
        // MField::validarCampo($id_tipo_intervencao,'Tipo de Intervenção' , ['req'=>'']);
        // MField::validarCampo($id_tipo_obra       ,'Tipo da Obra'        , ['req'=>'']);
        // MField::validarCampo($id_situacao_obra   ,'Situação da Obra'    , ['req'=>'']);
        // MField::validarCampo($id_categoria       ,'Categoria da Obra'   , ['req'=>'']);

        // interacoes entre campos
        // $campo_a->setExitAction( new TAction([$this,'onExitAction']));
        // $campo_b->setChangeAction(new TAction([$this,'onChangeAction']));

        // Campo CPJ/CNPJ. Segunda forma de resolver. Apos adicionar os campos.
        // $this->form->add(MField::scriptCPFCNPJ()); 

        /* ----------------------------------------------------------------------------- 
            Quando o formulario tiver varias abas, adicione os traits a partir daqui.
            Exemplo : $this->addObraDiarioFormEquipamentoFields();
          ------------------------------------------------------------------------------
        */
        // Parte 2        

        /////////////////////////////////////////////////////////////////////////////////

        // set sizes
        $this->form->setFieldSizes('100%');        
        $this->setLarguraJanela($this->larguraJanela);

        // if (!empty($id))
        // if (isset($param[$this->keyField]))        
        // {
        //     $id->setEditable(FALSE);
        // }
        // // Desabilitando alguns campos quando for edição
        // if (isset($param[$this->keyField]) && (intval($param[$this->keyField])>0))
        // {
        //     // $nr_cpf->setEditable(FALSE);
        // }
        
        /* samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
        */
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';

        if(!$this->embbed)
        {

            // Caso deseje adicionar botoes de acoes no formulario, basta descomentar essa linha
            // $this->form->addHeaderWidget($this->addActionButtons($param));            


            // Se for trabalhar com cortina lateral, descomentar o trecho a seguir
            // $btn_close = new TButton('closeCurtain');
            // $btn_close->onClick = "Template.closeRightPanel();";
            // $btn_close->setLabel(_t('Back'));
            // $btn_close->setImage('fas: fa-arrow-left red');        
            // $this->form->addHeaderWidget($btn_close);
        }
        $this->form->addHeaderActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:plus green');
        $this->form->addHeaderActionLink(_t('Back'),  new TAction([$this->listView, 'onReload']), 'fas: fa-arrow-left red');                        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }

    /*
        public static function onExitAction($param)
        {
            new TMessage('info', $param['campo1']);
            // enviando dados para um campo do formulario
            $obj = new stdClass;
            $obj->campo_destino = 'valor que deve ser enviado';
            TForm::sendData($this->getFormName(),$obj);

        }
        public static function onChangeAction($param)
        {
        }
    */

    // Botoes de acoes, caso seja necessario criar algo do tipo
    // Caso queira adicionar acoes, basta descomentar esse trecho
    public function addActionButtons($param)
    {
        // $dropdown = new TDropDown('Ações', 'fa:hammer');
        // $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        // $dropdown->addAction( 'Ação 1', new TAction([$this, 'onAcao1'], ['register_state' => 'false', 'static'=>'1']), 'fa:table fa-fw blue' );
        // $dropdown->addAction( 'Ação 2', new TAction([$this, 'onAcao2'], ['register_state' => 'false', 'static'=>'1']), 'fa:file-excel fa-fw purple' );
        // $dropdown->addAction( 'Ação 3', new TAction([$this, 'onAcao3'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf fa-fw red' );
        // return $dropdown;
    }

    private function setFocus($campo)
    {
        $str = str_replace('{campo}',$campo,'setTimeout(function() { $("input[name=\'{campo}\']").focus() }, 500);');        
        TScript::create($str);
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open($this->database); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $object = new $this->activeRecord;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data

            // self::validateBeforeSave($object);

            $object->store(); // save the object

            // self::onAfterSave($object);
            /* *********************************************************************
             Quando o formulario tiver detalhes em Traits, colocar as chamadas aqui
             Exemplo :  
                    $this->saveDetalheObraDiarioFormEquipamento($param, $object);

               *********************************************************************
            */
        	// Parte 3

            // get the generated id
            $data->{$this->keyField} = $object->{$this->keyField};
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            if (!$this->embbed) 
            {            
                // Fechando cortina lateral, quando houver.
                // TScript::create("Template.closeRightPanel();");
                TApplication::loadPage($this->listView, 'onReload', $param);
                // Se tiver que voltar pro mesmo formulario
                // $param['key'] = $data->{$this->keyField};                        
                // TApplication::loadPage(__CLASS__, 'onEdit', $param);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            // self::onChange($param);
            // $this->fireEvents((object) $param);
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open($this->database); // open a transaction
                $object = new $this->activeRecord($key); // instantiates the Active Record

                /* *******************************************************************
                    Quando o formulario tiver detalhes em traits, colocar as chamadas aqui
                    Exemplo : 
                    $this->onEditLoadObraDiarioFormEquipamentos($object);
                    ******************************************************************
                */
                // Parte 4

                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function show()
    {
        parent::show();
        $this->setFocus($this->fieldFocus);

        if (!$this->embbed) 
        {        
            // Se for usar cortina lateral, descomente este trecho
            // $str = "
            // $(document).ready(function(){
            //     $(document).bind('keydown', function(e) { 
            //         if (e.which == 27) {
            //             Template.closeRightPanel();
            //         }
            //     }); 
            // });
            // ";
            // TScript::create($str);        
        }
        self::desabilitaSubmitEnter();        
    }

    // Metodo usado em situacoes de interacoes dinamicas (onChange)
    // public function fireEvents($object)
    // {
    //     $obj = new stdClass;
    //     $obj->nr_bcmov         = $object->nr_bcmov;
    //     $obj->nr_bcmov_destino = $object->nr_bcmov_destino;
    //     $obj->cd_fonte_origem  = $object->cd_fonte_origem;
    //     $obj->cd_fonte_destino = $object->cd_fonte_destino;
    //     TForm::sendData('form_egresso', $obj);
    // }

    // public static function validateBeforeSave($object)
    // {
        // TTransaction::setLogger(new TLoggerSTD); // standard output        
        // $anobase = MSispubGlobal::getGlobalAnobase();
        // $cdempresa = MSispubGlobal::getGlobalCodigoEmpresa();

        // MSispubFuncoes::testaEmpresaSelecionada();
        // MSispubFuncoes::validarStatusAnualInscricaoDisponibilidade();

        // MSispubFuncoes::verificaDataValida(MSispubGlobal::getGlobalCodigoEmpresa(),
        // MSispubGlobal::getGlobalAnobase(),
        // $object->dt_interf,
        // MSispubGlobal::getGlobalMesref(),
        // $object->cd_ug_origem
        // );

        // MSispubFuncoes::verificaDataValida(MSispubGlobal::getGlobalCodigoEmpresa(),
        // MSispubGlobal::getGlobalAnobase(),
        // $object->dt_interf,
        // MSispubGlobal::getGlobalMesref(),
        // $object->cd_ug_destino
        // );

        // MSispubFuncoes::verificaStatuaConciliacao($object->nr_bcmov,$object->dt_interf);
        // MSispubFuncoes::verificaStatuaConciliacao($object->nr_bcmov_destino,$object->dt_interf);
        // MSispubFuncoes::verificaContaBancariaEncerrada($object->nr_bcmov,$object->dt_interf);
        // MSispubFuncoes::verificaContaBancariaEncerrada($object->nr_bcmov_destino,$object->dt_interf);
        // MSispubFuncoes::verificaBancoFonteAtiva($object->nr_bcmov, $anobase, $object->cd_fonte_origem);
        // MSispubFuncoes::verificaBancoFonteAtiva($object->nr_bcmov_destino, $anobase, $object->cd_fonte_destino);
        // MSispubFuncoes::verificaSaldoBancarioFonte($object->nr_bcmov, $object->dt_interf, $object->cd_fonte_origem,$object->vl_interf);

        // MSispubFuncoes::verificaEncerramentoPeriodo($object->dt_interf,$object->cd_ug_origem);
        // if (!empty($object->cd_ug_destino))
        //     MSispubFuncoes::verificaEncerramentoPeriodo($object->dt_interf,$object->cd_ug_destino);

    // }

    // public static function onAfterSave($object)
    // {
    //     $con = TTransaction::get();
    // }

    /*
        // Mudar mascara do campo cpf/cnpj de acordo com o tipo selecionado
        public static function onChangeTipoAction($param)
        {
            $form = $param["_form_name"];
            switch ($param['tipo_pfpj'])
            {
                case 'F' : TEntry::changeMask($form, 'cpf_cnpj','999.999.999-99'); break;
                case 'J' : TEntry::changeMask($form, 'cpf_cnpj','99.999.999/9999-99'); break;
            }
        }
    */
}

// Usando subform para abas internas
// formuario interno
// $subform = new BootstrapFormBuilder;
// $subform->setFieldSizes('100%');
// $subform->setProperty('style', 'border:none');

// $subform->appendPage('Orçamento');
// $row=$subform->addFields( [ new TLabel('ev_orcr_debito') , $ev_orcr_debito ] ,
//                           [ new TLabel('ev_orcr_credito') , $ev_orcr_credito ] );
// $row->layout = ['col-sm-6', 'col-sm-6'];
// $this->form->addContent( [$subform]);

///////////////////////////////////////////////////////////////////////
// Caso seja necessario usar um campo de cep, fazer o seguinte
// $cep        = MField::fieldCEP('cep');
// $cep->setExitAction(new TAction([$this,'onExitCEPAction']));        
// Configurar o evento conforme os campos existentes no formulario
// public static function onExitCEPAction($param)
// {
//     $form = $param["_form_name"];
//     $cep = $param['endereco_cep'];
//     $dados_cep = MSispubFuncoes::LocalizaCEP($cep);
//     $dados = new stdClass;
//     $dados->endereco_logradouro = $dados_cep->logradouro;
//     $dados->endereco_bairro     = $dados_cep->bairro;
//     $dados->endereco_cidade     = $dados_cep->localidade;
//     $dados->endereco_uf         = $dados_cep->uf;
//     TForm::sendData($form, $dados);
// }
///////////////////////////////////////////////////////////////////////

/*      
///////////////////////////////////////////////////////////////////////
// Este trecho é um exemplo de inclusão de campos especiais dentro 
// de campos HTMLEditor
// O subform aqui esta sendo usado opcionalmente, pois podem haver outros campos
// html dentro do formulario principal

$subform1 = new BootstrapFormBuilder('subform1');
$subform1->setFormTitle('Especificação de Ordem de Compra');
$campos_especiais1 = new TCombo('campos_especiais1');
$campos_especiais1->addItems(self::CAMPOS_ESPECIAIS1);
$subform1->addHeaderAction('Inserir Campo', new TAction(array($this, 'onInsereCampoEspecial'),
    ['form_name'=>$this->getFormName(),
    'campo_especial' => 'campos_especiais1',
    'field_name' => 'oc_especificacao']), 'fa:plus green');     

$subform1->addFields( [ new TLabel('Campos Especiais') , $campos_especiais1 ] );
$subform1->addFields( [  $oc_especificacao ] );
$this->form->addContent([$subform1]);

// Esta funcao insere texto dento do campo
public static  function onInsereCampoEspecial($param)
{
    $campo_especial = $param['campo_especial'];
    if (isset($param[$campo_especial])) 
    {
        $campo = $param[$campo_especial];
        if (!empty($campo))
        {
            $campo = '%'.$campo.'%';
        } else {
            $campo='';
        }
        THtmlEditor::insertText($param['form_name'], $param['field_name'], $campo);
    }
}  
///////////////////////////////////////////////////////////////////////
*/