<?php
trait MFormTrait
{

    public function setLarguraJanela($largura='')
    {


        if (!empty($largura)) {
            $this->style = 'width: 100%';
            $style = new TStyle("
            right-panel .container-part {
                width: 100% !important;
            }
            .right-panel {
                width: {$largura} !important;
            }
           
            
            ");
/*
            $style = new TStyle("
            right-panel .container-part {
                width: {$largura};
            }
            .right-panel {
                width: {$largura} !important;
            }
  
            
            ");
*/            


            $style->show(true);
            
        }
        
        
    }

    public static function getFormName()
    {
        return 'form_'.__CLASS__;
    }
    
    public static function desabilitaSubmitEnter()
    {
        // $str = "
        // $(document).ready(function() {
        //     $(window).keydown(function(event){
        //       if(event.keyCode == 13) {
        //         event.preventDefault();
        //         return false;
        //       }
        //     });
        //   });
        // ";
        $str = "

        $(document).ready(function () {
            $('input').keypress(function (e) {
                var code = null;
                code = (e.keyCode ? e.keyCode : e.which);                
                return (code == 13) ? false : true;
            });
        });
        ";        


        TScript::create($str);           
    }


}

?>
