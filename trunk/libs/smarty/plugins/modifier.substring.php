<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty upper modifier plugin
 *
 * Type:     modifier<br>
 * Name:     upper<br>
 * Purpose:  convert string to uppercase
 * @link http://smarty.php.net/manual/en/language.modifier.upper.php
 *          upper (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @return string
 */
/*
function smarty_modifier_substring($str,$start,$end)
{
	   $strlen=$start+$end;    
       for($i=$start;$i<$strlen;$i++)  {    
               if(ord(substr($str,$i,1))>0xa0)  {    
                       $tmpstr.=substr($str,$i,2);    
                       $i++;    
               }  else    
                       $tmpstr.=substr($str,$i,1);    
       }    
       return  $tmpstr;      
}
*/

function smarty_modifier_substring($string, $sublen, $start = 0,$etc = "...", $code = 'UTF-8') 
{ 
    if($code == 'UTF-8') 
    { 
        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/"; 
        preg_match_all($pa, $string, $t_string); 

        if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen)).$etc; 
        return join('', array_slice($t_string[0], $start, $sublen)); 
    } 
    else 
    { 
        $start = $start*2; 
        $sublen = $sublen*2; 
        $strlen = strlen($string); 
        $tmpstr = ''; 

        for($i=0; $i<$strlen; $i++) 
        { 
            if($i>=$start && $i<($start+$sublen)) 
            { 
                if(ord(substr($string, $i, 1))>129) 
                { 
                    $tmpstr.= substr($string, $i, 2); 
                } 
                else 
                { 
                    $tmpstr.= substr($string, $i, 1); 
                } 
            } 
            if(ord(substr($string, $i, 1))>129) $i++; 
        } 
        if(strlen($tmpstr)<$strlen ) $tmpstr.= $etc; 
        return $tmpstr; 
    } 
} 
?>
