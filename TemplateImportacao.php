<?php
trait ImportaDadosSispub{TRAITNAME}Trait{
    
    private function importa{TRAITNAME}()
    {

        $classe           = '{ACTIVE_RECORD}';
        $database_destino = '{DATABASE_DESTINO}';
        $database_origem  = '{DATABASE_ORIGEM}';

        $this->excluiDados($database_destino,$classe);

        try
        {

            $i = 0;
            TTransaction::open($database_origem);
            $con = TTransaction::get();
            $result = $con->query("select * from {TABELA_ORIGEM} 
                
            ");

            foreach ($result as $row)
            {

                    try {
                        TTransaction::open($database_destino);

                        $obj = (new $classe);
                        // $obj->clearUnique();
                        // $obj->tp_receita    = $row['tp_receita'];
                        // $obj->descricao     = $row['nm_receita'];
/*
                        {TRAIT_ADD_FIELDS}

*/

                        $obj->store();
                        TTransaction::close();
                    }
                    catch (Exception $e)
                    {
                        TTransaction::rollback();   
                    }
            }
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error',$e->getMessage());
        }

    }


}