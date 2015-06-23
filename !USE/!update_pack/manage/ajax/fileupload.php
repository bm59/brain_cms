<?
include "$DOCUMENT_ROOT/inc/include.php";
$onfinish = trim(strstr($_SERVER['REQUEST_URI'],'&onfinish='));
$onfinish = substr($onfinish,10);
$onfinish = preg_split('|\&[a-zA-Z_0-9]+\=|',$onfinish);
$onfinish = urldecode($onfinish[0]);
$oldfileid = floor($_GET['oldfileid']);
$uploadfile = $Storage->uploadFile($_GET['uploadfilestorage'],$_GET['uploadfiletheme'],$_GET['uploadfilerubric'],$_GET['uploadfileuid'],$_FILES[$_GET['uploadfilename']]);
$errors = '';
foreach ($uploadfile['errors'] as $v) $errors.= '— '.$v.';\n';
if ($errors) $errors = 'Ошибки при загрузке файла:\n'.$errors;
if (($errors=='') && ($oldfileid>0)) $Storage->deleteFile($oldfileid); // Если загрузили новый файл
if ($_GET['delete']=='anytime'){ // Если удалили
	$Storage->deleteFile($oldfileid);
	$errors = '';
}
?>
<script>
	<?=($errors)?'alert("'.$errors.'");':''?>
	var uploadimage = '<?=$uploadfile['path']?>';
	var uploadimageext = '<?=$uploadfile['ext']?>';
	var uploadimageid = '<?=floor($uploadfile['id'])?>';
	var uploadimagewidth = '<?=$uploadfile['width']?>';
	var uploadimageheight = '<?=$uploadfile['height']?>';
	var uploadimagesize = '<?=$uploadfile['sizestr']?>';
	eval('window.parent.<?=$onfinish?>');
</script>