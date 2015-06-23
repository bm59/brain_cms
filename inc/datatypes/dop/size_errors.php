<?
     foreach ($_POST as $k=>$v)
     {
        if (preg_match('|^size_val[0-9]+$|',$k) && $v=='')
        {
         $num=preg_replace('|^size_price([0-9]+)$|','\\1',$k);
         $errors[]='Название в размере '.$num.' не должно быть пустым';
        }
     }

     foreach ($_POST as $k=>$v)
     {
        if (preg_match('|^size_price[0-9]+$|',$k) && !is_numeric($v))
        {
         $num=preg_replace('|^size_price([0-9]+)$|','\\1',$k);
         $errors[]='Цена в "'.$_POST['size_val'.$num].'" должна быть числом';
        }
     }

?>