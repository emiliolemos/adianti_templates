<?php
class CreateClassModel extends TPage
{

    public function __construct()
    {
        parent::__construct();
    }


    public static function tableFields($database, $table_name)
    {
        $table_fields = [];

        try
        {
            TTransaction::open($database);


                $conn = TTransaction::get();
                $result = $conn->query("pragma table_info($table_name)");
                foreach ($result as $row)
                {
                    $table_fields[] = $row['name'];

                }
            TTransaction::close();

            return $table_fields;
        }
        catch (Exception $e)
        {

        }

        try
        {
            TTransaction::open($database);


                $conn = TTransaction::get();
                $result = $conn->query("describe $table_name");
                foreach ($result as $row)
                {
                    $table_fields[] = $row['Field'];

                }
            TTransaction::close();

            return $table_fields;
        }
        catch (Exception $e)
        {

        }


    }
    public static function create($database, $table_name, $class_name, $output_dir, $campo_id='id') 
    {

        $clas = file_get_contents(getcwd().'/app/control/lemarq/util/templates/TemplateModel.class.php');

        $table_fields = '';
        $fields = self::tableFields($database, $table_name);
        foreach ($fields as $field)
        {
            $table_fields .= "parent::addAttribute('$field');\r\n";

        }

        // TTransaction::open($database);

        //     $conn = TTransaction::get();
        //     $result = $conn->query("pragma table_info($table_name)");
        //     foreach ($result as $row)
        //     {
        //         $col_name = $row['name'];

        //     }
        // TTransaction::close();


        $clas = str_replace(['{DATABASE}',
                             '{TABLE_NAME}',
                             '{ACTIVE_RECORD}',
                             '{MODEL_FIELDS}',
                             '{CAMPO_ID}'

                        ],
                            [
                            $database,
                            $table_name,
                            $class_name,
                            $table_fields,
                            $campo_id
                            ],
                            $clas);

        $outputClas  = getcwd() . "/app/model/lemarq/{$output_dir}/{$class_name}.class.php";
        if (!file_exists($outputClas))
        {
            file_put_contents($outputClas,$clas);
        }
                    

    }

}