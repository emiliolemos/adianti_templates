<?php

/* Exemplo de uso 
        $file = new MyUploadFile($param['arquivo'],'tmp');
        $file->validateExtension(['zip','ofx']);
        $file->renameFile();
        $file->deleteOldFiles();
        $file_path = $file->getNewFileName();
*/

class MyUploadFile
{

    const EXTENSOES_VALIDAS = ['zip','txt','xls','xlsx','doc','docx','ofx'];

    private $uploaded_file;
    private $renamed_file;
    private $prefixo;
    private $diretorio_destino;
    private $dir_cwd;
    private $cdempresa;
    private $fileName;

    private $nmFile;
    private $extension;
    private $extension_len;

    
    public function __construct($uploaded_file='', $prefixo='')
    {
        $this->uploaded_file    = $uploaded_file;
        $this->prefixo          = $prefixo;
        if (empty($this->cdempresa)) $this->cdempresa = '0000';
        $this->diretorio_destino =          "app/files/{$this->cdempresa}/uploads";
        $this->dir_cwd           = getcwd()."/app/files/{$this->cdempresa}/uploads/";
        $this->extension         = '';

        if ( !empty($this->uploaded_file)) {
            $nm = json_decode(urldecode($this->uploaded_file)); // aqui foi a solução
            if ($nm) {
                $this->fileName = $nm->fileName;

                if (file_exists($this->fileName)) {

                    $this->nmFile        = substr((json_decode(urldecode($this->uploaded_file))->fileName), 4); // aqui foi a solução
                    $this->extension     = pathinfo($this->nmFile, PATHINFO_EXTENSION);
                    $this->extension_len = strlen($this->extension);

				}
			}
		}

    }


    public function renameFile($new_folder='')
    {

        // Pasta onde o arquivo será salvo após o upload

        if (!file_exists($this->diretorio_destino))
            mkdir($this->diretorio_destino, 0777, true);
            if (file_exists($this->fileName)) 
            {

                $target_folder = $this->diretorio_destino;
                // Primeira forma : O arquivo de upload é renomeado para um nome aleatorio
                $target_file = $target_folder . "/{$this->prefixo}_" .  substr($this->nmFile,0, strlen($this->nmFile) - $this->extension_len-1) . "-".uniqid(). ".". $this->extension;

                // Segunda forma : O nome do arquivo original é mantido.
                // $target_file = $target_folder . '/' .  substr($nm,0, strlen($nm) - $len-1) . ".". $extension;

                // Excluindo arquivo na pasta destino, caso exista
                if (file_exists($target_file)) {
                    unlink($target_file);
                }

                @mkdir($target_folder);
                rename('tmp/' . $this->nmFile, $target_file);
                $this->renamed_file = $target_file;
            }
    }

    public function deleteOldFiles($tempo=3600)
    {
        if (!empty($this->prefixo))
        {
            /*** percorrendo todos os arquivos da pasta ***/
            foreach (glob($this->dir_cwd."{$this->prefixo}_*") as $file) {
                /*** 3600  = 1hora, 86400=24horas ***/
                if(time() - filectime($file) > $tempo) 
                {
                    if (is_dir($file))
                        $this->delTree($file);
                    else 
                        unlink($file);
                }
            }
        }
    }

    private function delTree($dir)
    {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
          (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
    
        }
        return rmdir($dir);  
    }

    public function getNewFileName()
    {
        return $this->renamed_file;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function getNewFolderName()
    {
        return $this->diretorio_destino;
    }

    public function validateExtension($extensoes_validas=self::EXTENSOES_VALIDAS)
    {
        if (!in_array($this->extension,$extensoes_validas))
            throw new Exception("Extensão {$this->extension} é inválida.<br>Só serão aceitas extensões zip ou ofx.");

    }

}