<?
$aut_mode = 'alladmin';

// �������� �� ����������� � cookies ������
if (floor(sessionGet('visitorID'))==0){
	$enter = explode('|',cookieGet('enter'));
	if ($user = $SiteVisitor->auth($SiteVisitor->getIdByLoginAndPswd($enter[0],'',$enter[1]))){
		/* �������� cookies */
		cookieSet('enter',$user['login'].'|'.$user['pswd'],30);
	}
}

// ��� ����� � ����� ������ � ������
if (isset($_POST['enter'])){
	if ($user = $SiteVisitor->auth($SiteVisitor->getIdByLoginAndPswd($_POST['login'],$_POST['password']))){
		if ($_POST['saveme']=='on') cookieSet('enter',$user['login'].'|'.$user['pswd'],30);
	}
}

if ((!$SiteVisitor->isAuth()) && (sessionGet('visitorID')>0)) $SiteVisitor->auth(sessionGet('visitorID'));

// ����� �� �������
if ($_GET["userexit"]=='exit') $SiteVisitor->unAuth();

// �������� ������� � ��������
$redirect = '';
$exceptions = array(
	'/manage/ajax/fileupload.php',
	'/manage/ajax/itemdelete.php'
); // ��������, ��� ������� ������ ������ ������
if (!$SiteVisitor->isAuth()) $redirect='/manage/';
else{
	/*$exceptions[] = '/profile/index.php';*/
	if ($_GET['section']>0)
	{
		$cid = $Content->getIdByPath($SiteSections->getPath($_GET['section']));

		if ($aut_mode=='alladmin')
		$sec=$SiteSections->get($_GET['section']);
	}
	else
	$cid = $Content->getIdByPath(configGet("AskUrl")); // ��� ������������� ��������

    if ($_GET['section']>0)
    $sec=$SiteSections->get($_GET['section']);

	$askcontent = $Content->getOne($cid);
	$user = $SiteVisitor->getOne(sessionGet('visitorID')); // ������ ������������
	$group = $VisitorType->getOne($user['type']); // ������ ������
	$accessgranted = $VisitorType->isAccessGranted($group['id'],$cid);
	if ((!$accessgranted) || ($cid==0 && !$sec['id']>0) || ($askcontent['redirect']==1))
	$redirect = '/manage'.$Content->getPath($SiteVisitor->getRedirectContent($user['id'],$cid));
}
if (($_SERVER['PHP_SELF']=='/manage/index.php') && ($redirect=='/manage/')) $redirect = '';

if (preg_match('|\/templates\/|',$_SERVER['PHP_SELF'])) $redirect = '';
if (preg_match('|\/load\/|',$_SERVER['PHP_SELF'])) $redirect = '';


if (($redirect!='') && (!in_array($_SERVER['PHP_SELF'],$exceptions))){
	header('Location: '.$redirect);
	die();
}
?>