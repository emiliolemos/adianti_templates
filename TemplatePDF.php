<?php
class {CLASS_NAME} extends TPage
{


    private $pdf;
    private $empresa;
    private $anobase;
    private $periodo;
    private $titulos;

    public function __construct()
    {
        parent::__construct();

        $this->empresa            = "001";
        $this->anobase            = Date('Y');
        $this->periodo            = '';
        $this->titulos            = ['Relatorio Exemplo',
                                     'Subtitulo1',
                                    'Subtitulo2'];


        $this->pdf = new MYTCPDF('P', PDF_PAGE_FORMAT);
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

    public function imprime()
    {

        $this->cabecalho();
        $this->imprimeParte1();
        $this->imprimeDetalhes();
        parent::openFile($this->pdf->Outputf());

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
                $this->pdf->imprimeLinhaTable([$detalhe->campo->descricao,$detalhe->quantidade],$larguras, $alinhamentos);


        }


        $this->pdf->imprimeTexto($this->pdf->hr());
        $this->pdf->closeTable();

        $this->pdf->imprimeHtml();


    }


    // public function imprime()
    // {

    //     $this->pdf = new MYTCPDF('P', PDF_PAGE_FORMAT);

    //     $this->cabecalho();
        
    //     $this->pdf->htmlClear();
    //     $this->pdf->openTable();
    //     for ($i=0;$i<100;$i++)
    //     {
    //         $this->pdf->imprimeLinhaTable(["Codigo $i", "Nome $i", "Endereco $i","Cidade $i"]);
    //     }
    //     $this->pdf->hr();
    //     $this->pdf->imprimeLinhaTable(["TOTAL", "", "","99999999"]);
    //     $this->pdf->imprimeLinhaTable(['texto simples'],['100'],['c'])        ;        
    //     $this->pdf->closeTable();
    //     $this->pdf->imprimeHtml();


    //     parent::openFile($this->pdf->Outputf());

    // }





}
