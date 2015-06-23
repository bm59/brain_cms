<?php
session_start();
if(session_id() != $_POST['sid']) die('Access_denied');

$ext = substr($_FILES['upl_file']['name'], 1 + strrpos($_FILES['upl_file']['name'], "."));
$ext = strtolower($ext);
$valid_ext = array('jpg','jpeg','png','bmp','gif','ico'); // допустимые расширения
if(in_array($ext, $valid_ext)){
    $filename = time().'.'.$ext; // переименовываем файлик
    $path_file = $_SERVER['DOCUMENT_ROOT'].'/files/'.$filename;
    if(!copy($_FILES['upl_file']['tmp_name'], $path_file)){
        echo 'Файл не загружен. Повторите попытку';
    }else{
        /**
         * Тут можно сделать, например, запись в БД...
         */
        echo 'true#%#'.$filename; // Возврат статуса загрузки и имени файла
    }
}else{
    echo 'Недопустимый формат файла.';
}
?>