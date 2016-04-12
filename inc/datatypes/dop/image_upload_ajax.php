<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/include_client.php");?>
<?php
session_start();
if(session_id() != $_POST['sid']) die('Access_denied');



$path='/storage/image_ajax/';
/*
if (isset($_POST['file_delete']))
{

 	if (stripos($_POST['file_delete'], '?')!==false)
 	$_POST['file_delete']=substr($_POST['file_delete'], 0, stripos($_POST['file_delete'], '?'));


 	$filename = $_SERVER['DOCUMENT_ROOT'].$_POST['file_delete'];
	if ( !(@unlink($filename)) ) echo('Ошибка удаления файла');
	else
    echo('true#%#Файл удален');
	die();
}
else*/
{

$ext = substr($_FILES['upl_file']['name'], 1 + strrpos($_FILES['upl_file']['name'], "."));
$ext = strtolower($ext);
$valid_ext = array('jpg', 'png'); // допустимые расширения
if(in_array($ext, $valid_ext)){

    $ttt = time(); // переименовываем файлик

    $filename = $ttt.'_temp.'.$ext;
    $path_file = $_SERVER['DOCUMENT_ROOT'].$path.$filename;
    if(!copy($_FILES['upl_file']['tmp_name'], $path_file)){
        echo 'Файл не загружен. Повторите попытку';
    }
    else
    {

        echo 'true#%#
              <img src="'.$path.$filename.'" class="img_prev">
        <input type="hidden" name="image_file'.$_POST['num'].'" value="'.$path.$filename.'" />';

    }
}else{
    echo 'Недопустимый формат файла.'.$ext;
}

}
?>