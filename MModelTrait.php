<?php
trait MModelTrait
{


    private function uniqueTest($object)
    {

        if (isset($this->unique_key) AND (!empty($this->unique_key)))
        {
            $repository = new TRepository(get_class($this));

            $arr = (array) $object;
            foreach ($this->unique_key as $nome_chave => $campos)
            {

                $criteria = new TCriteria;
                $criteria->add(new TFilter(self::PRIMARYKEY,'<>', $arr[self::PRIMARYKEY]));
                foreach($campos as $campo)
                {
                    $criteria->add(new TFilter($campo, '=', $arr[$campo]));
                }

                $count = $repository->count($criteria);

                if ($count>0)
                {
                    throw new Exception("Chave Unica : ".$nome_chave. " já existe."  );
                }
            }

        }

    }




}    

