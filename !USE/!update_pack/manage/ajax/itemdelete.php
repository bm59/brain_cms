<?
include "$DOCUMENT_ROOT/inc/include.php";
/* �������� ��������� (�������������, ����� � �.�.) */

$type = trim($_POST['type']);
$id = trim($_POST['id']);
switch ($type){
	case 'usergroups': // �������� ������ �������������
		$errors = $VisitorType->delete($id);
		if (count($errors)==0) print "ok";
		break;
	case 'users': // �������� ������������
		$errors = $SiteVisitor->delete($id);
		if (count($errors)==0) print "ok";
		break;
	case 'sitesettings': // �������� ��������� �����
		$iface = new SiteSettings;
		$iface->init();
		$errors = $iface->delete($id);
		if (count($errors)==0) print "ok";
		break;
	default:
		print "wrongtype";
}
?>