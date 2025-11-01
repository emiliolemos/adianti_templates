<?php
class {ACTIVE_RECORD} extends TRecord
{

    use MModelTrait;

    const TABLENAME = '{TABLE_NAME}';
    const PRIMARYKEY= '{CAMPO_ID}';
    const IDPOLICY =  'serial'; // {max, serial}
    const DATABASE =  '{DATABASE}';
    
    // const CREATEDAT = 'created_at';
    // const UPDATEDAT = 'updated_at';
    // const DELETEDAT = 'deleted_at';

    // Campo que sera usado para teste de chave unica
    // private $unique_key =  ['NOME_DO_CAMPO' => ['cpf_cnpj'] ] ;
    // Se o campo for composto, basta fazer assim:
    // private $unique_key =  ['NOME_DO_CAMPO' => ['campo1','campo2'] ] ;


    // private $marca;
    
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);

/* ********* gerador *************** */        
// O campo id deve ser removido deste trecho

{MODEL_FIELDS}
       
/* ********************************* */

    }

/*

    // Os metodos a seguir so devem ser usados em caso de necessidade 

    public function onBeforeLoad($id)
    {

    }
    public function onAfterLoad($object)
    {

    }
    public function onAfterStore($object)
    {

    }

    public function onBeforeDelete($object)
    {
        // primeira forma
        // $modelos = FrotaVeiculoMarca::find($object->id)->hasMany('FrotaVeiculoModelo');
        // segunda forma
        // $registros = $this->hasMany('FrotaVeiculoModelo');

        // terceira forma
        //$registros = $this->hasMany('FrotaVeiculoModelo', 'id_modelo', 'id');

        // if (!empty($registros)) 
        // {
        //     throw new Exception('Objeto possui Vinculos');
        // }

    }    

    public function onAfterDelete($object)
    {
        
    }


    // public function get_marca()
    // {
    //     if (empty($this->marca))
    //     {
    //         $this->marca = new Marca( $this->marca_id);

    //     }
    //     return $this->marca;

    // }


// Exemplo envolvendo onBeforeStore
// Aqui eh possivel fazer tratamento nos campos antes da gravacao, ou retornar alguma mensagem de erro
//    public function onBeforeStore($object)
//    {
//        $object->cpf_cnpj = str_replace(['.','/','-'],['','',''],$object->cpf_cnpj);
//        switch (strlen($object->cpf_cnpj))
//        {
//            case 11 : $object->tipo_pfpj = 'F';break;
//            case 14 : $object->tipo_pfpj = 'J';break;
//            default : 
//                    throw new Exception ('CPF/CNPJ : Quantidade de Caracteres Inválida.');
//        }
//
//        $this->uniqueTest($object);


        if (intval($object->id)>0) {
            $old = {ACTIVE_RECORD}::find($object->id);
            if (  strtotime($old->updated_at) !== strtotime($object->updated_at))
            {
                throw new Exception("Registro foi alterado por outro usuario.");
            }
        }



//
//    }


/////////////////////////////////////////////
// TESTE DE CHAVE UNICA

    // Se for necessario fazer teste de chave unica, basta descomentar esse trecho 
    // Esse metodo deve ser usado quando se deseja ignorar a chave unica, e deve ser chamado antes do comando de gravacao
    // Geralmente ele so eh usado em situacoes de importacao de dados
    public function clearUnique()
    {
         $this->unique_key = '';
    }

    public function onBeforeStore($object)
    {
        if (!empty($this->unique_key))
            $this->uniqueTest($object);
    }
/////////////////////////////////////////////

*/
////////////////////////////
// Pegando valor numa tabela no banco de dados sispubgeral
// Necessario abrir uma transacao
// Esse metodo magico pode ser usado numa grid, por exemplo 
// $column_unidade   = new TDataGridColumn('{unidadeMedida->sigla}', 'UND', 'right','5%');

// public function get_unidadeMedida()
// {
//     try
//     {
//         $this->unidadeMedida = new stdClass;
//         $this->unidadeMedida->sigla='.';
//         TTransaction::open(ObraUnidadeMedida::DATABASE);
//         $this->unidadeMedida = new ObraUnidadeMedida( $this->obra_un_medida_id);
//         TTransaction::close();
//     }
//     catch (Exception $e)
//     {
//     }
//     return $this->unidadeMedida;

// }




}