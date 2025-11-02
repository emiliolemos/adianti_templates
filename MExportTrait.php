<?php
trait MExportTrait
{

/* ---------------------------------------------------------------------------------------------
    EXPORTACAO PARA CSV
   ---------------------------------------------------------------------------------------------
*/


    // public function onExportCSV($param)
    // {
    //     try
    //     {
    //         $output = 'app/output/'.uniqid().'.csv';
    //         $this->exportToCSV( $output, $this->onExportObjects() );
    //         TPage::openFile( $output );
    //     }
    //     catch (Exception $e)
    //     {
    //         return new TMessage('error', $e->getMessage());
    //     }
    // }

    // public  function exportToCSV($output, $objects)
    // {

    //     if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
    //     {

    //             $handler = fopen($output, 'w');

    //             // $objects = $this->onExportObjects();
                
    //             if ($objects)
    //             {

    //                 $titulo = $objects[0];
    //                 $row=[];
    //                 foreach ($titulo as $coluna => $valor)
    //                 {
    //                     $row[]=$coluna;
    //                 }
    //                 fputcsv($handler, $row,';');                    


    //                 // iterate the collection of active records
    //                 $row=[];
    //                 foreach ($objects as $objeto)
    //                 {
    //                     $row=[];
    //                     foreach ($objeto as $coluna => $valor)
    //                     {
    //                         $row[]=utf8_decode($valor);
    
    //                     }
    //                     fputcsv($handler, $row,';');

    //                 }

    //             }

    //             fclose($handler);

    //     }

    // }    


/* ---------------------------------------------------------------------------------------------
    EXPORTACAO PARA PDF
   ---------------------------------------------------------------------------------------------
*/


    public  function m_onExportPDF($param)
    {
        try
        {
            $output = 'app/output/'.uniqid().'.pdf';
            // $this->exportToPDF($output, $this->onExportObjects());
            
            $this->m_exportToPDF($output);

            $window = TWindow::create('Export', 0.8, 0.8);
            $object = new TElement('object');
            $object->{'data'}  = $output;
            $object->{'type'}  = 'application/pdf';
            $object->{'style'} = "width: 100%; height:calc(100% - 10px)";
            $window->add($object);
            // $window->show();

            parent::openFile($output);

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }

    }

    // public function exportToPDF($output, $objects)
    // {
    //     if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
    //     {
            
    //         if ($objects)
    //         {


    //             $html = new TElement('table');
    //             $html->__set('border','1');
    //             $html->__set('width','100%');

    //             $tr = new TElement('tr');

    //             $titulo = $objects[0];

    //             foreach ($titulo as $coluna => $valor)
    //             {
    //                 if (!empty($coluna)) {
    //                     $td = new TElement('td');
    //                     $td->add($coluna);
    //                     $tr->add($td);
    //                 }
    //             }
    //             $html->add($tr);


    //             // iterate the collection of active records
    //             foreach ($objects as $objeto)
    //             {
    //                 $tr = new TElement('tr');
    //                 foreach ($objeto as $coluna => $valor)
    //                 {
                        
    //                     $td = new TElement('td');
    //                     $td->add(utf8_decode($valor));
    //                     $tr->add($td);
    //                 }
    //                 $html->add($tr);

    //             }


    //             $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();					
                
    //             $options = new \Dompdf\Options();
    //             $options-> setChroot (getcwd());
                
    //             // converts the HTML template into PDF
    //             $dompdf = new \Dompdf\Dompdf($options);
    //             $dompdf-> loadHtml ($contents);
    //             $dompdf-> setPaper ('A4', 'portrait');
    //             $dompdf-> render ();
    //             file_put_contents($output, $dompdf->output());

    //         }

    //     }
    //     else
    //     {
    //         throw new Exception(AdiantiCoreTranslator::translate('Permission denied') . ': ' . $output);
    //     }
    // }


    public function m_exportToPDF($output)
    {

        if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
        {
            $this->limit = 0;
            $objects = $this->onReload();


            if ($objects)
            {


                $html = new TElement('body');

                $html->add( $this->titulo );

                $table = new TElement('table');
                $table->__set('border','1');
                $table->__set('width','100%');

                $tr = new TElement('tr');

                $titulo = $objects[0];

                foreach ($titulo as $coluna => $valor)
                {
                    if (!empty($coluna)) {
                        $td = new TElement('td');
                        $td->add($coluna);
                        $tr->add($td);
                    }
                }
                $table->add($tr);


                // iterate the collection of active records
                foreach ($objects as $objeto)
                {
                    $tr = new TElement('tr');
                    foreach ($objeto as $coluna => $valor)
                    {
                        
                        $td = new TElement('td');
                        // $td->add(utf8_decode($valor));
                        $td->add(mb_convert_encoding($valor, "UTF-8", mb_detect_encoding($valor)));                        
                        $tr->add($td);
                    }
                    $table->add($tr);

                }

                $html->add($table);

                $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();					
                
                $options = new \Dompdf\Options();
                $options-> setChroot (getcwd());
                
                // converts the HTML template into PDF
                $dompdf = new \Dompdf\Dompdf($options);
                // $dompdf->addinfo('Title','Relatorio');
                $dompdf-> loadHtml ($contents);
                $dompdf-> setPaper ('A4', 'portrait');
                $dompdf-> render ();
                file_put_contents($output, $dompdf->output());

            }

        }
        else
        {
            throw new Exception(AdiantiCoreTranslator::translate('Permission denied') . ': ' . $output);
        }



    }


/* ---------------------------------------------------------------------------------------------
    EXPORTACAO PARA XLS
   ---------------------------------------------------------------------------------------------
*/

//     public  function onExportXLS($param)
//     {
//         new TMessage('info', 'Exportacao para XLS');

//     }


// /* ---------------------------------------------------------------------------------------------
//     EXPORTACAO PARA XML
//    ---------------------------------------------------------------------------------------------
// */

//     public  function onExportXML($param)
//     {
//         new TMessage('info', 'Exportacao para XML');
//     }


}
?>
