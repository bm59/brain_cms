<?
include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/include.php");
include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/init.php");
include_once($_SERVER['DOCUMENT_ROOT']."/inc/ajax_securuty.php");


mysql_query("SET NAMES utf-8"); // для mysql
header("Content-type: text/html; charset=utf-8");

if ($_REQUEST['banner_id']>0)
{
	$banner_section=msr(msq("SELECT * FROM `site_site_sections` WHERE `pattern`='PBanners'"));
	$banner_Section = $SiteSections->get($banner_section['id']);

	$banner_iface=getiface($SiteSections->getPath($banner_Section['id']));

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

		if (session_id()!=$_POST['session_id']) die('Ошибка!!!');
		$banner_iface->addStat($_POST['banner_id'], 'click');

	}
}

if ($_GET['adv_id'])
{
	$rk_item=$rkIface->getPub($_GET['adv_id']);

	if ($rk_item['id']>0)
	{
		$rkIface->addStat($rk_item['id'], 'click');

	}
	else die('Ошибка, обратитесь в службу поддержки сайта!');

	$href=$_GET['url']!='inner' ? $_GET['url'] : $rk_item['href'];
	header("Location: ".$href."\n");

}



?>