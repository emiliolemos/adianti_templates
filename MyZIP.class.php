<?php
class MyZIP
{

    private $zip;
    private $zip_status;

    private $diretorio;
    private $zipfile;


    const OUTPUT_DIR = 'app/output';

    public function __construct( $diretorio='',$filename='')
    {
        $this->zip = new ZipArchive();
            // removendo a ultima barra
            $diretorio = rtrim($diretorio,'/\\');

            if ($diretorio =='') 
                $diretorio = 'app/output';
            else
                $diretorio = "app/output/$diretorio";

            $filename = str_replace(
                [ '*', '/', '\\' ], 
                [ '','',''        ], 
                $filename);

            

            if ($filename=='') $filename = "tmp_".uniqid() . '.zip';


            $this->diretorio = $diretorio;
            $this->zipfile   = $diretorio . '/'. $filename;


            if (!file_exists($this->diretorio))
                mkdir ($this->diretorio, 0777, true);

            $this->createZipFile();

    }

    public function createZipFile()
    {


        // apagando arquivos gerados anteriormente, caso existam        
        array_map('unlink', glob( $this->diretorio.'/*'.$this->zipfile.'*'));    
        $this->deleteOldFiles( $this->diretorio );

        $this->zip_status = $this->zip->open(getcwd()."/". $this->zipfile, ZIPARCHIVE::CREATE);


    }

    public function addFile($newfile, $titulo='')
    {
        if ($this->zip_status==TRUE)
        {
            if (file_exists($newfile) && is_file($newfile)) {

                $absolute_path_zip = getcwd()."/". $this->zipfile;
                $absolute_path_file = getcwd(). "/".$newfile;


                if ($titulo == '') $titulo = basename($newfile);

                $this->zip->addFile( $absolute_path_file , $titulo);
            }
        }
    }

    public function addFiles($arr)
    {
        if ($this->zip_status==TRUE)
        {
            foreach ($arr as $newFile)
            {
                $this->addFile($newfile);
            }
        }
    }


    public function deleteOldFiles($diretorio, $tempo=3600 )
    {


        $dir = getcwd()."/". "$diretorio/";


        /*** percorrendo todos os arquivos da pasta ***/
        foreach (glob($dir."*.zip") as $file) {
            /*** 3600  = 1hora, 86400=24horas ***/
            if(time() - filectime($file) > $tempo) 
            {
                unlink($file);
            }
        }

    }

    public function addFromString($newfile, $texto='')       
    {
        $this->zip->addFromString($newfile,$texto);
    }

    public function __get($atrib)
    {
        return $this->$atrib;
    }

    public function close()
    {
        if ($this->zip_status==TRUE)
            $this->zip->close();
    }

    



}