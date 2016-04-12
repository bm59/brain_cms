<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/include.php");?>
<?

	$SiteSettings = new SiteSettings;
	$SiteSettings->init();

	$Section = $SiteSections->get($SiteSections->getIdByPath('/sitecontent'.configGet("AskUrl")));

	$Section['id'] = floor($Section['id']);
	if ($Section['id']>0)
	{
		$Pattern = new $Section['pattern'];
		$Iface = $Pattern->init(array('section'=>$Section['id']));
	}

	if ($Section['description']!='') $headerh1=$Section['description'];
	else $headerh1=$Section['name'];

	$nav_text='<div>'.$Section['name'].'</div>';

?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/meta.php");?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/header.php");?>

<div class="clear"></div>
<div class="content" style="padding: 20px 30px;">
<div class="mininav"><a href="/">Главная</a><img src="/pics/arrows/arrow_nav.png"><?=$nav_text?></div>
<H1><?=$headerh1?></H1>
<?
	$sheet = $Iface->get();
	print $sheet['text'];
?>
</div>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/footer.php");?>