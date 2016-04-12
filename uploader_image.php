<?php
include "$DOCUMENT_ROOT/inc/site_admin/include.php";

session_start();
/*if(session_id() != $_POST['sid']) die('Access_denied');*/
header("Content-type: text/html; charset=utf-8");

if ($_POST['action']=='delete')
{ 	if ($_POST['old_id']>0) $Storage->deleteFile($_POST['old_id']);
 	echo('true#%#Файл удален');
}
else
{

	if ($_POST['old_id']>0) $Storage->deleteFile($_POST['old_id']);
	$uploadfile = $Storage->uploadFile($_POST['stid'],$_POST['theme'],$_POST['rubric'],$_POST['uid'],$_FILES['upl_file'], $_POST['str_settings']);

	$errors = '';
	foreach ($uploadfile['errors'] as $v) $errors.= '— '.$v.';
	';
	if ($errors) $errors = 'Ошибки при загрузке файла:
	'.$errors;


	if ($uploadfile['path']!='')
	print json_encode(array('result'=>'ok', 'ext'=>$uploadfile['ext'], 'path'=>$uploadfile['path'], 'filesize'=>get_filesize($path_file), 'id'=>$uploadfile['id']));
	else
	print json_encode(array('error'=>$errors));

}
?>