<?
include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/include.php");

if ($_GET['section']>0 && $_GET['banner_id']>0)
{	$banner=msr(msq("SELECT * FROM `site_site_pbanners_banners_".$_GET['section']."` WHERE id=".$_GET['banner_id']));


	if ($banner['id']>0)
	{
		$today_click=msr(msq("SELECT * FROM `pr_banners_clicks` WHERE date(`date`)=date(NOW()) and `section_id`='".$_GET['section']."' and `banner_id`=".$_GET['banner_id']." LIMIT 1"));

	    if ($today_click['num']>0)
	    msq("UPDATE `pr_banners_clicks` SET `num`=`num`+1 WHERE id=".$today_click['id']);
	    else
	    msq("INSERT INTO `pr_banners_clicks` (`section_id`, `banner_id`, `date`, `num`) VALUES ('".$_GET['section']."','".$_GET['banner_id']."', date(NOW()), 1)");

	    header("Location: ".$banner['href']."\n");
		die();
    }
    else
    die('Ошибка, обратитесь в службу поддержки сайта!');
}



?>