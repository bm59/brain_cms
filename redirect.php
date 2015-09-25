<?
include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/include.php");

$banner_iface=getiface('/sitecontent/banners/');
$banner_Section = $SiteSections->get($SiteSections->getIdByPath('/sitecontent/banners/'));

$banner=$banner_iface->getPub($_GET['banner_id']);

if ($_GET['banner_id']>0)
{

	if ($banner['id']>0 && $banner['href']!='')
	{
		$banner_iface->addStat($banner['id'], 'click');
	    header("Location: ".$banner['href']."\n");
		die();
    }
    else
    die('Ошибка, обратитесь в службу поддержки сайта!');
}

if ($_POST['action']=='add_click' && $_POST['banner_id']>0)
{
	include_once($_SERVER['DOCUMENT_ROOT']."/inc/ajax_securuty.php");
	mysql_query("SET NAMES cp1251"); // для mysql
	header("Content-type: text/html; charset=windows-1251");
	
	
	if (session_id()!=$_POST['session_id']) die('Ошибка!!!');
	
	$banner_iface->addStat($_POST['banner_id'], 'click');
	
	
}



?>