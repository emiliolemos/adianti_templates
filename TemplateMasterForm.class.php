<?php

use Adianti\Widget\Wrapper\TDBUniqueSearch;

/**
 * ObraTabChecklistForm Form
 * @author  <your name here>
 */
class ObraObraForm extends TPage
{
    protected $form; 
    private $database       = 'unit_database';
    private $activeRecord   = 'Obra';
    private $listView       = 'ObraObraView';
    private $fieldFocus     = 'descricao';
    private $keyField       = 'id';
    private $formTitle      = 'Cadastro de Obra';
    private $embbed;
    private $larguraJanela  = '80%'; // 1000px
    
    private $historico_list;
    private $documento_list;
    private $contrato_list;

    use Adianti\Base\AdiantiFileSaveTrait;

    use MFormTrait;
    // use MFuncoesTrait;
    // use MObraFieldsTrait; // Contem as definicoes dos campos

    use ObraObraFormHistoricoTrait;
    use ObraObraFormDocumentosTrait;
    use ObraObraFormContratoTrait;
    use ObraObraFormFiscalTrait;
    use ObraObraFormFonteTrait;
    use ObraObraFormMedicaoTrait;

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param, $embbed=false )
    {
        parent::__construct();

        $this->embbed = $embbed; // indica se o formulario está embutido noutra coisa.
                                // Se estiver, alguns botoes e funcionalidades ficam limitados.

        // creates the form
        $this->form = new BootstrapFormBuilder($this->getFormName());
        $this->form->setClientValidation(true);

        if (!$this->embbed) {
            $this->setTargetContainer('adianti_right_panel');
            $this->setProperty('override', 'true');
            $this->setPageName(__CLASS__);            
        }


        if (!$this->embbed) {
            $this->form->setFormTitle($this->formTitle);
        }
        

        $this->form->setFieldSizes('100%');

        // create the form fields
        $id         = new TEntry($this->keyField);
        $descricao  = new TText('descricao');
        

        $nr_processo_despesa    = new TEntry('nr_processo_despesa');
        $anobase                = MField::fieldExercicio('anobase');
        $cd_empresa             = new TEntry('cd_empresa');
        $cd_ug                  = new TEntry('cd_ug');

        $id_regime_execucao      = MFieldObra::selectObraRegimeExecucao($this->database);
        $id_tipo_intervencao     = MFieldObra::selectObraTipoIntervencao($this->database); 
        $id_tipo_obra            = MFieldObra::selectObraTipoObra($this->database);        
        $id_situacao_obra        = MFieldObra::selectObraSituacaoObra($this->database);    
        $id_categoria            = MFieldObra::selectObraCategoriaObra($this->database);   
        $obra_ug_id              = MFieldObra::selectObraUGObra($this->database,'obra_ug_id');          
        $responsavel_paralisacao = MFieldObra::selectObraPessoa($this->database,'responsavel_paralisacao_id'); 

        $geo_localizacao_descricao_objeto   = new TEntry('geo_localizacao_descricao_objeto');
        $geo_localizacao_endereco_completo  = new TEntry('geo_localizacao_endereco_completo');
        $geo_localizacao_latitude           = new TEntry('geo_localizacao_latitude');
        $geo_localizacao_longitude          = new TEntry('geo_localizacao_longitude');
        $justificativa                      = new TEntry('justificativa');
        $nm_obra                            = MField::fieldUppercase('nm_obra');
        $vl_obra                            = MField::fieldValor('vl_obra'); 


        $percentual_vl_usado        = new TEntry('percentual_vl_usado');
        $nr_obra                    = new TEntry('nr_obra');
        $id_unidade_exeutora        = new TEntry('id_unidade_exeutora');
        $dt_cadastro                = MField::fieldDate('dt_cadastro');
        $nr_tce                     = new TEntry('nr_tce');
        $dt_inicio                  = MField::fieldDate('dt_inicio'); 
        $mes_termino                = MField::selectMes('mes_termino'); 
        $ano_termino                = MField::fieldExercicio('ano_termino');
        $prazo_dias                 = MField::fieldInteger('prazo_dias','99999');


        $dt_conclusao               = MField::fieldDate('dt_conclusao');
        $obra_unidade_medida_id     = new TEntry('obra_unidade_medida_id');
        $motivo_paralisacao         = new TEntry('motivo_paralisacao');

        $dt_retorno                 = MField::fieldDate('dt_retorno');

        $obra_licitacao_id = MFieldObra::selectObraLicitacao($this->database); 


        // Validacao de campos
        MField::validarCampo($nm_obra            ,'Descricao Resumida'  , ['req'=>'', 'minlen'=>'4']);
        MField::validarCampo($id_regime_execucao ,'Regime de Execução'  , ['req'=>'']);
        MField::validarCampo($id_tipo_intervencao,'Tipo de Intervenção' , ['req'=>'']);
        MField::validarCampo($id_tipo_obra       ,'Tipo da Obra'        , ['req'=>'']);
        MField::validarCampo($id_situacao_obra   ,'Situação da Obra'    , ['req'=>'']);
        MField::validarCampo($id_categoria       ,'Categoria da Obra'   , ['req'=>'']);


        // add the fields
        $this->form->appendPage('Principal');

        $row = $this->form->addFields( [ new TLabel('Id') ,  $id ] );


        $this->form->addFields([ MField::requiredLabel('Descricao Resumida') ,$nm_obra ] );
        $this->form->addFields([ MField::requiredLabel('Regime de Execução')	,$id_regime_execucao ]); 
        $this->form->addFields([MField::requiredLabel('Tipo de Intervencao')	,$id_tipo_intervencao ]);
        $this->form->addFields([MField::requiredLabel('Tipo de Obra')		,$id_tipo_obra ]);
        $this->form->addFields([MField::requiredLabel('Situação da Obra')	,$id_situacao_obra   ]);
        $this->form->addFields([MField::requiredLabel('Categoria')			,$id_categoria ]);

        $row = $this->form->addFields( 
                                [new TLabel('Codigo da Obra')           ,$nr_obra ],
                                [MField::requiredLabel('Exercicio')				,$anobase ],
                                [ MField::requiredLabel('Valor da Obra')           ,$vl_obra ]                                 
                            );                                  
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        // vetor com as proporcoes das colunas                                       
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];


        $this->form->appendPage('Licitação');
        $this->form->addFields([new TLabel('Processo de Despesa')	,$obra_licitacao_id ]);
        $this->form->addFields([new TLabel('Unidade Gestora')		,$obra_ug_id ]);  
        $this->form->addFields([new TLabel('Numero TCE')			,$nr_tce ]);  



        $this->form->appendPage('Localização');
        $this->form->addFields([new TLabel('Descrição do Objeto'),$geo_localizacao_descricao_objeto   ]);
        $this->form->addFields([new TLabel('Endereço completo')	 ,$geo_localizacao_endereco_completo ]);
        $this->form->addFields([new TLabel('Latitude')			 ,$geo_localizacao_latitude   ]);
        $this->form->addFields([new TLabel('Longitute')			 ,$geo_localizacao_longitude ]);


        $this->form->appendPage('Prazos');

        $this->form->addFields([new TLabel('Data Inicio')				,$dt_inicio ]);  
        $this->form->addFields([new TLabel('Mes Término')				,$mes_termino ]);  
        $this->form->addFields([new TLabel('Ano Término')				,$ano_termino ]);  
        $this->form->addFields([new TLabel('Prazo em Dias')				,$prazo_dias ]);  
        $this->form->addFields([new TLabel('Data Conclusão')			,$dt_conclusao ]);


        $this->form->appendPage('Paralisação');

        $this->form->addFields([new TLabel('Motivo da Paralisação')				,$motivo_paralisacao ]); 
        $this->form->addFields([new TLabel('Responsável pela Paralisação')		,$responsavel_paralisacao ]);
        $this->form->addFields([new TLabel('Data de Retorno')					,$dt_retorno ]);






        $this->addMedicaoFields();
        $this->addFonteFields();
        $this->addFiscalFields();
        $this->addDocumentoFields();
        $this->addContratoFields();
        $this->addHistoricoFields();
        







        // set sizes
        $this->form->setFieldSizes('100%');
/*        
        $id->setSize('100%');
*/        



        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';



        

        if (!$this->embbed) {
            $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
            
            $btn_close = new TButton('closeCurtain');
            $btn_close->onClick = "Template.closeRightPanel();";
            $btn_close->setLabel("Fechar");
            $btn_close->setImage('fas:times');        
            $this->form->addHeaderWidget($btn_close);
        }


        
        parent::add($this->form);

        $this->setLarguraJanela($this->larguraJanela);


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
            $object->store(); // save the object
            

            $this->saveDetalheHistorico($param, $object);
            $this->saveDetalheDocumento($param, $object);
            $this->saveDetalheContrato($param,$object);
            $this->saveDetalheFiscal($param,$object);
            $this->saveDetalheFonte($param,$object);
            $this->saveDetalheMedicao($param,$object);





            // get the generated id
            $data->{$this->keyField} = $object->{$this->keyField};
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            

            if (!$this->embbed) {            
                TScript::create("Template.closeRightPanel();");
                TApplication::loadPage($this->listView, 'onReload', $param);
            }

            
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
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

                $this->onEditLoadHistoricos($object);
                $this->onEditLoadDocumentos($object);
                $this->onEditLoadContratos($object);
                $this->onEditLoadFiscals($object);
                $this->onEditLoadFontes($object);
                $this->onEditLoadMedicaos($object);





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

        if (!$this->embbed) {        

            $str = "
            $(document).ready(function(){

                $(document).bind('keydown', function(e) { 
                    if (e.which == 27) {
                        Template.closeRightPanel();
                    }
                }); 

            });

            ";
            TScript::create($str);        
        }

        self::desabilitaSubmitEnter();

    


    }


    public function onLoad($param)
    {
        // $data = new stdClass;
        // $this->form->setData($data);
    }

}
