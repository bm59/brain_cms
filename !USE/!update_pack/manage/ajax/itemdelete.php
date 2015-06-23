<?
include "$DOCUMENT_ROOT/inc/include.php";
/* Удаление элементов (пользователей, групп и т.д.) */

$type = trim($_POST['type']);
$id = trim($_POST['id']);
switch ($type){
	case 'usergroups': // Удаление группы пользователей
		$errors = $VisitorType->delete($id);
		if (count($errors)==0) print "ok";
		break;
	case 'users': // Удаление пользователя
		$errors = $SiteVisitor->delete($id);
		if (count($errors)==0) print "ok";
		break;
	case 'sitesettings': // Удаление настройки сайта
		$iface = new SiteSettings;
		$iface->init();
		$errors = $iface->delete($id);
		if (count($errors)==0) print "ok";
		break;
	default:
		print "wrongtype";
}
?>