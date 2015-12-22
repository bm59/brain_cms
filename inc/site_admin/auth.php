<?
$aut_mode = 'alladmin';

// Проверка на сохраненный в cookies пароль
if (floor($_SESSION['visitorID'])==0){
	$enter = explode('|',cookieGet('enter'));
	if ($user = $SiteVisitor->auth($SiteVisitor->getIdByLoginAndPswd($enter[0],'',$enter[1]))){
		/* Продляем cookies */
		cookieSet('enter',$user['login'].'|'.$user['pswd'],30);
		WriteLog($user['id'], 'успешная авторизация по кукам', '');
	}
}

// При вводе в форме логина и пароля
if (isset($_POST['enter'])){
	if ($user = $SiteVisitor->auth($SiteVisitor->getIdByLoginAndPswd($_POST['login'],$_POST['password'])))
	{
		if ($_POST['saveme']=='on') cookieSet('enter',$user['login'].'|'.$user['pswd'],30);
		WriteLog($user['id'], 'успешная авторизация', '');

		msq("DELETE FROM `".ConfigGet('pr_name')."_log` WHERE datediff( now( ) , `date` ) >180");
	}
	else WriteLog($user['id'], 'ошибка авторизации', $_POST['login'].'|'.$_POST['password']);
}

if (!$SiteVisitor->isAuth() && $_SESSION['visitorID']>0) $user = $SiteVisitor->auth($_SESSION['visitorID']);



// Проверка доступа к странице
$redirect = '';
$exceptions = array(
	'/uploader.php',
	'/uploader_image.php',
	'/uploader_txt.php',
	'/inc/datatypes/photo_editor/index.php'
); // Страницы, для которых всегда открыт доступ
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
    $cid = $Content->getIdByPath(configGet("AskUrl")); // Код запрашиваемой страницы

    if ($_GET['section']>0)
    $sec=$SiteSections->get($_GET['section']);

	$askcontent = $Content->getOne($cid);

    $user = $SiteVisitor->getOne($_SESSION['visitorID']); // Данные пользователя
	$group = $VisitorType->getOne($user['type']); // Данные группы


	if ($user['login']=='admin') {$delepmentmode = 'development';  $mode = 'development';}


	$accessgranted = $VisitorType->isAccessGranted($group['id'],$cid);
	
    $accessgranted_settings=@in_array('view', $group['new_settings'][$cid]);



	if ((!$accessgranted && !$accessgranted_settings) || ($cid==0 && !$sec['id']>0) || ($askcontent['redirect']==1))
	{
		$redirect='/manage/control/contents/';
	}

}
if (($_SERVER['PHP_SELF']=='/manage/index.php') && ($redirect=='/manage/')) $redirect = '';
if (($_SERVER['PHP_SELF']=='/manage/control/contents/index.php' && !$_GET['section']>0) && ($redirect=='/manage/control/contents/')) $redirect = '';

if (preg_match('|\/templates\/|',$_SERVER['PHP_SELF'])) $redirect = '';
if (preg_match('|\/load\/|',$_SERVER['PHP_SELF'])) $redirect = '';


if ($_GET["userexit"]=='exit')
{
	WriteLog($user['id'], 'выход из кабинета', '');
	$SiteVisitor->unAuth();
}

if (($redirect!='') && (!in_array($_SERVER['PHP_SELF'],$exceptions))){
	header('Location: '.$redirect);
	die();
}
?>