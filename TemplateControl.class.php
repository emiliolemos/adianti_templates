<?php
/**
 * ObraTabChecklistForm Form
 * @author  <your name here>
 */

class {CLASS_NAME} extends TPage
{
    protected $form; 
    private $formTitle      = '{CLASS_NAME}';
    private $fieldFocus     = 'descricao';    
    private $embbed;
    private $larguraJanela  = '80%'; // 1000px

    // Se for imprimir alguma coisa
    private $pdf;
    private $empresa;
    private $anobase;
    private $periodo;
    private $titulos;

    //////////////////////////////////////////////////////////////////////////////
    // Quando o formulario usar Traits, colocar aqui

    use MFormTrait;    
    use MFuncoesTrait;

    //////////////////////////////////////////////////////////////////////////////
    // public function __construct( $param, $embbed=false )
    public function __construct( $param=null, $embbed=false )
    {
        parent::__construct();
        $this->embbed = $embbed; // indica se o formulario está embutido noutra coisa.
                                // Se estiver, alguns botoes e funcionalidades ficam limitados.

        if (!$this->embbed) 
        {
            // Se for trabalhar com cortina lateral, decomentar essa linha
            // parent::setTargetContainer('adianti_right_panel');
        }

        // creates the form
        $this->form = new BootstrapFormBuilder($this->getFormName());
        $this->form->setClientValidation(true);

        if (!$this->embbed) {
            $this->form->setFormTitle($this->formTitle);
        }
        
        /////////////////////////////////////////////////////////////////////////////
        // create the form fields

        $campo01 = MField::fieldUppercase('descricao');
        $campo02 = MField::TMFile('arquivo');
        $campo03 = MField::selectMes('mes');
        $campo04 = MField::fieldValor('valor');
        $campo05 = MField::fieldDate('data');
        $campo06 = MField::fieldInteger('integer');
        $campo06 = MField::fieldTelefone('telefone');
        $campo07 = MField::fieldHtmlEditor('editor');

        // $anobase                  = MField::fieldInteger('anobase');
        // $anobase->setValue(MSispubGlobal::getGlobalAnobase());
        // $anobase->setEditable(FALSE);
        
        $this->form->addFields( [ new TLabel('descricao') ], [ $campo01 ] );        
        $this->form->addFields( [ new TLabel('arquivo')   ], [ $campo02 ] );
        $this->form->addFields( [ new TLabel('mes')       ], [ $campo03 ] );
        $this->form->addFields( [ new TLabel('valor')     ], [ $campo04 ] );
        $this->form->addFields( [ new TLabel('data')      ], [ $campo05 ] );
        $this->form->addFields( [ new TLabel('Inteiro')   ], [ $campo06 ] );

        //   validacoes
        MField::validarCampo($campo01   ,'Descricao Resumida'  , ['req'=>'', 'minlen'=>'4']);
        MField::validarCampo($campo02   ,'Regime de Execução'  , ['req'=>'']);
        MField::validarCampo($campo03   ,'Tipo de Intervenção' , ['req'=>'']);
        MField::validarCampo($campo04   ,'Tipo da Obra'        , ['req'=>'']);
        MField::validarCampo($campo05   ,'Situação da Obra'    , ['req'=>'']);
        MField::validarCampo($campo06   ,'Categoria da Obra'   , ['req'=>'']);

        // set sizes
        $this->form->setFieldSizes('100%');        
        $this->setLarguraJanela($this->larguraJanela);

        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
        **/
         
        // create the form actions
        $btn = $this->form->addAction('Pesquisar', new TAction([$this, 'onSave']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';

        if (!$this->embbed) 
        {
            $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
            // $this->form->addHeaderAction(_t('Close'),  new TAction([$this->listView, 'onReload']), 'fa:eraser red');                        
            // Se for trabalhar com cortina lateral, descomentar o trecho a seguir
            // $this->form->addHeaderWidget(MField::btnCloseCurtain());
        }
        
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
            // TTransaction::open($this->database); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array

            // Renomeando o arquivo de upload
            // $file_path              = $this->renameArquivo($param['arquivo']);

            // $object = new $this->activeRecord;  // create an empty object
            // $object->fromArray( (array) $data); // load the object with data
            // $object->store(); // save the object
            
            // get the generated id
            // $data->{$this->keyField} = $object->{$this->keyField};
            
            $this->form->setData($data); // fill form data

            // $pdo = TTransaction::get();
            // if ($pdo && $pdo->inTransaction())
            // TTransaction::close(); // close the transaction

            // Caso deseje imprimir
            // $this->imprime();
            
            if (!$this->embbed) 
            {            
                // TScript::create("Template.closeRightPanel();");
                // TApplication::loadPage($this->listView, 'onReload', $param);
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
        // self::desabilitaSubmitEnter();        
    }

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

/*
    // Usar este metodo para renomear o arquivo de upload
    public function renameArquivo($nome_arquivo)
    {
        // Pasta onde o arquivo será salvo após o upload
        $diretorio_destino =  'app/files/brasoes';

        if ( !empty($nome_arquivo)) {
            $nm = json_decode(urldecode($nome_arquivo)); // aqui foi a solução
            if ($nm) {
                $fileName = $nm->fileName;

                if (file_exists($fileName)) {

                    $nm = substr((json_decode(urldecode($nome_arquivo))->fileName), 4); // aqui foi a solução
                    $extension = pathinfo($nm, PATHINFO_EXTENSION);
                    $len = strlen($extension);

                    $target_folder = $diretorio_destino;
                    // Primeira forma : O arquivo de upload é renomeado para um nome aleatorio
                    $target_file = $target_folder . '/' .  substr($nm,0, strlen($nm) - $len-1) . "-".uniqid(). ".". $extension;

                    // Segunda forma : O nome do arquivo original é mantido.
                    // $target_file = $target_folder . '/' .  substr($nm,0, strlen($nm) - $len-1) . ".". $extension;

                    // Excluindo arquivo na pasta destino, caso exista
                    if (file_exists($target_file)) {
                        unlink($target_file);
                    }

                    @mkdir($target_folder);
                    rename('tmp/' . $nm, $target_file);
                    $nome_arquivo = $target_file;
                }
            }
        }
        return $nome_arquivo;
    }
*/

/*
// Metodo para manipular Planilha Excel
public function geraPlanilha()
{
    // Criando uma planilha        
    // echo "Teste Excel";
    // $excel = new MyExcel();
    // $excel->novaPlanilha();
    // $excel->objPHPExcelModelo->getActiveSheet()->setCellValue('A1','TESTE');
    // $excel->Save();

    // Baixando uma planilha
    // echo "Teste Excel 2";
    // $excel = new MyExcel();
    // $excel->carregaPlanilhaRemota('http://lemarq.inf.br/file/doc/sispubweb/modelos/planilhas/casp/rgf-2023.xlsx');
    // $objPHPExcelModelo = $excel->objPHPExcelModelo;
    // $objPHPExcelModelo->setActiveSheetIndexByName('Anexo 2 - DCL');
    // $objPHPExcelModelo->getActiveSheet()->setCellValue("K10","TESTE XXXX");        
    // $excel->Save();

    // // Carregando uma planilha local
    // echo "Teste Excel 3";
    // $excel = new MyExcel();
    // $excel->carregaPlanilhaLocal('app/files/rgf-2023.xlsx');
    // $objPHPExcelModelo = $excel->objPHPExcelModelo;
    // $objPHPExcelModelo->setActiveSheetIndexByName('Anexo 2 - DCL');
    // $objPHPExcelModelo->getActiveSheet()->setCellValue("K10","TESTE HHHHHHH");        
    // $excel->Save();

    // // Mudar a aba
    // $objPHPExcelModelo->setActiveSheetIndexByName('Balanco-Orcamentario');
    // $objPHPExcelModelo->setActiveSheetIndex(0);

    // // Gravar valor numa celula
    // $objPHPExcelModelo->getActiveSheet()->setCellValue("N1", [global_anobase]);
    // $objPHPExcelModelo->getActiveSheet()->setCellValueByColumnAndRow(7, 1, $nmuf); // estado
    // $objPHPExcelModelo->getActiveSheet()->getCell("E10")->setValue("EM 31/12/$anoant");

    // // ler valor de uma celula
    // $codigo = $objPHPExcelModelo->getActiveSheet()->getCellByColumnAndRow(0,$linha)->getValue();	
    // $codigo = $objPHPExcelModelo->getActiveSheet()->getCell("J$linha")->getCalculatedValue();	
        
    // // Incluir linhas
    // $objPHPExcelModelo->getActiveSheet()->insertNewRowBefore($linha_inicial+1,$qt_linhas);

    // // Formula
    // $objPHPExcelModelo->getActiveSheet()->setCellValue("E$linha", "=C$linha - D$linha");
    // $objPHPExcelModelo->getActiveSheet()->setCellValue("H$linha", "=TRUNC(D$linha + E$linha-F$linha-G$linha,2)");			

    // // Formatando uma celula para texto
    // $objPHPExcelModelo->getActiveSheet()->setCellValueExplicit(
    //     "V$linha",
    //     $ds->fields['cd_fonte'],
    //     PHPExcel_Cell_DataType::TYPE_STRING
    // );

    // // Alinhamentos e formatacoes
    // $objPHPExcelModelo->getActiveSheet()->getStyle("B$linha")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    // $objPHPExcelModelo->getActiveSheet()->getStyle("I$linha")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    // $objPHPExcelModelo->getActiveSheet()->getStyle("J$linha")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    // $objPHPExcelModelo->getActiveSheet()->getStyle("I$linha:J$linha")->getNumberFormat()->setFormatCode('#,##0.00');		
    // $objPHPExcelModelo->getActiveSheet()->getStyle("K$linha:P$linha")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');		

    // // Criando uma nova aba
    // $objWorkSheet = $objPHPExcelModelo->createSheet();
    // $objWorkSheet->setTitle("NovaAba");
    // $objPHPExcelModelo->setActiveSheetIndexByName('NovaAba');


    
    public function geraExcel($param,$con)
    {

        $anobase    = intval($param['anobase']);
        $arquivo    = '';
        $mes_final  = $param['mes_final'];

        $excel = new MyExcel();
        $excel->novaPlanilha();
        // $excel->carregaPlanilhaLocal('app/resources/lemarq/casp/'.$arquivo);
        $objPHPExcelModelo = $excel->objPHPExcelModelo;
        // $excel->objPHPExcelModelo->getActiveSheet()->setCellValue('A1','TESTE');        

        $this->geraExcelCabecalho($param, $con, $objPHPExcelModelo);
        $this->geraExcel01($param, $con, $objPHPExcelModelo);

        $excel->save();
        $arquivo = $excel->arquivo_saida;
        // return $arquivo;

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add( new THyperLink('Baixar Planilha Excel' , $arquivo, 'blue', 12, 'bi'));
        parent::add( $vbox );


    }




}

*/
/*
    public function imprime()
    {
        $this->empresa            = MSispubFuncoes::getEmpresa(TSession::getValue('EMPRESA_ID'));
        $this->anobase            = MSispubGlobal::getGlobalAnobase();
        $this->periodo            = '';
        $this->titulos            = ['Relatorio Exemplo', 'Subtitulo1', 'Subtitulo2'];
        $this->pdf = new MYTCPDF('P', PDF_PAGE_FORMAT);
        $this->cabecalho();
        $this->imprimeParte1();
        $this->imprimeDetalhes();
        parent::openFile($this->pdf->Outputf());
    }
    private function cabecalho()
    {
        // $this->pdf->setNomeEmpresa('EMPRESA MODELO');
        // $this->pdf->setAnobase('2023');
        // $this->pdf->setPeriodo('JANEIRO/2023');
        // $this->pdf->setTitulos(['BALANCETE FINANCEIRO', 'CONSOLIDADO', 'SUBTITULO']);
        $this->pdf->setNomeEmpresa($this->empresa->nome);
        $this->pdf->setAnobase($this->anobase);
        $this->pdf->setPeriodo($this->periodo);
        $this->pdf->setTitulos($this->titulos);
        $this->pdf->setLarguras([10,50,30,10]);
        $this->pdf->setAlinhamentos(['l','l','l','r']);
        $this->pdf->setCabecalhos(['CODIGO', 'NOME', 'ENDERECO','CIDADE']  );
        $this->pdf->AddPage();
    }

    public function imprimeParte1()
    {
        $larguras     = [20,80];
        $alinhamentos = ['l','l'];
        $this->pdf->htmlClear();
        $this->pdf->openTable();
        $this->pdf->imprimeLinhaTable(["Data", "11/11/1111"],$larguras, $alinhamentos);
        $this->pdf->imprimeLinhaTable(["CAMPO ","VALOR"],$larguras, $alinhamentos);
        $this->pdf->imprimeTexto($this->pdf->hr());
        $this->pdf->closeTable();
        $this->pdf->imprimeHtml();
    }
    public function imprimeDetalhes()
    {
        $larguras     = [80,20];
        $alinhamentos = ['l','r'];
        $this->pdf->htmlClear();
        $this->pdf->openTable();
        $this->pdf->imprimeLinhaTable(['Descricao','Quantidade'],$larguras, $alinhamentos, false, true, true);
        $detalhes = $this->classeActiveRecord->detalhes;
        foreach ($detalhes as $detalhe)
        {
            if ($detalhe->campo)
            {
                $this->pdf->imprimeLinhaTable([$detalhe->campo->descricao,$detalhe->quantidade],$larguras, $alinhamentos);
            }
        }
        $this->pdf->imprimeTexto($this->pdf->hr());
        $this->pdf->closeTable();
        $this->pdf->imprimeHtml();
    }
*/
    public function onEdit($param)
    {
    }
}