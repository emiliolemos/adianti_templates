<?php
class MTransform
{
    public static function formatDataGridColumnValue($coluna, $decimais=2)
    {
        switch ($decimais) 
        {
            case 2 :
            $format_value = function($vl) 
            {
                if (is_numeric($vl)) {
                    $number = number_format($vl, 2, ',', '.');                    
                    if($vl>=0)
                    {
                        return "<span style='color:black'>$number</span>";                        
                    } 
                    else
                    {
                        return "<span style='color:red'>$number</span>";
                    }
                }
                return $vl;
            };
            break;
            case 3 :
                $format_value = function($vl) 
                {
                    if (is_numeric($vl)) {
                        return number_format($vl, 3, ',', '.');
                    }
                    return $vl;
                };
                break;
            case 4 :
            $format_value = function($vl) 
            {
                if (is_numeric($vl)) {
                    return number_format($vl, 4, ',', '.');
                }
                return $vl;
            };
            break;
            default :
                $format_value = function($vl) 
                {
                    if (is_numeric($vl)) {
                        return number_format($vl, 0, ',', '.');
                    }
                    return $vl;
                };
                break;
        }
        $coluna->setTransformer($format_value);
    }

    public static function formatDataGridDate($dt)
    {
        $dt->setTransformer( function($value, $object, $row) 
        {
            if (!empty($value)) {
                $date = new DateTime($value);
                return $date->format('d/m/Y');
            } else {
                return '';
            }
        });
    }

    public static function formatDataGridDateComHora($dt)
    {
        $dt->setTransformer( function($value, $object, $row) 
        {
            if (!empty($value)) {
                $date = new DateTime($value);
                return $date->format('d/m/Y H:i');
            } else {
                return '';
            }
        });
    }

}