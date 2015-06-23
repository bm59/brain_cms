<?php

session_start();
if(session_id() != $_POST['sid']) die('Access_denied');
header("Content-type: text/html; charset=windows-1251");


function get_filesize($file)
{
    // идем файл
    if(!file_exists($file)) return "Файл  не найден";
   // теперь определяем размер файла в несколько шагов
  $filesize = filesize($file);
   // Если размер больше 1 Кб
   if($filesize > 1024)
   {
       $filesize = ($filesize/1024);
       // Если размер файла больше Килобайта
       // то лучше отобразить его в Мегабайтах. Пересчитываем в Мб
       if($filesize > 1024)
       {
            $filesize = ($filesize/1024);
           // А уж если файл больше 1 Мегабайта, то проверяем
           // Не больше ли он 1 Гигабайта
           if($filesize > 1024)
           {
               $filesize = ($filesize/1024);
               $filesize = round($filesize, 1);
               return $filesize." ГБ";
           }
           else
           {
               $filesize = round($filesize, 1);
               return $filesize." MБ";
           }
       }
       else
       {
           $filesize = round($filesize, 1);
           return $filesize." Кб";
       }
   }
   else
   {
       $filesize = round($filesize, 1);
       return $filesize." байт";
   }
}
if ($_POST['old_file']!='')
unlink($_SERVER['DOCUMENT_ROOT'].$_POST['old_file']);

$ext = substr($_FILES['upl_file']['name'], 1 + strrpos($_FILES['upl_file']['name'], "."));
$ext = strtolower($ext);



    $filename = time().'.'.$ext; // переименовываем файлик

    $path_file = $_SERVER['DOCUMENT_ROOT'].$_POST['storage_path'].$filename;
    $filesize=filesize($path_file);
    if(!copy($_FILES['upl_file']['tmp_name'], $path_file)){
        print json_encode(array('error'=>'Файл не загружен. Повторите попытку'));
    }else{
        /**
         * Тут можно сделать, например, запись в БД...
         */
        /*echo 'true#%#'.$filename; */// Возврат статуса загрузки и имени файла
        print json_encode(array('file'=>$filename, 'result'=>'ok', 'full_path'=>$_POST['storage_path'].$filename, 'filesize'=>get_filesize($path_file)));
    }

?>