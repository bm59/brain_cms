<?
     foreach ($_POST as $k=>$v)
     {
        if (preg_match('|^size_val[0-9]+$|',$k) && $v=='')
        {
         $num=preg_replace('|^size_price([0-9]+)$|','\\1',$k);
         $errors[]='�������� � ������� '.$num.' �� ������ ���� ������';
        }
     }

     foreach ($_POST as $k=>$v)
     {
        if (preg_match('|^size_price[0-9]+$|',$k) && !is_numeric($v))
        {
         $num=preg_replace('|^size_price([0-9]+)$|','\\1',$k);
         $errors[]='���� � "'.$_POST['size_val'.$num].'" ������ ���� ������';
        }
     }

?>