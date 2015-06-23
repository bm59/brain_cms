<?
	$SiteSections= new SiteSections;
	$SiteSections->init();
	$ss = $SiteSections->get($SiteSections->getIdByPath('/sitecontent'.configGet("AskUrl")));

	$SiteSettings = new SiteSettings;
	$SiteSettings->init();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html;charset=cp1251"/>
<meta http-equiv="content-language" content="ru" />

<title><?=html_entity_decode($SiteSections->getTitle($ss['id']))?></title>
<meta name="description" content="<?=$SiteSections->getDescription($ss['id'])?>" />
<meta name="keywords" content="<?=$SiteSections->getKeywords($ss['id'])?>" />

<!--//<link rel="icon" href="/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />//-->

<link rel="stylesheet" type="text/css" href="/css/site.css" media="all" />
<link rel="stylesheet" type="text/css" href="/css/lightbox.css" media="all" />

<script src="/js/jquery.js" language="JavaScript" type="text/javascript"></script>
<script src="/js/lightbox.js" language="JavaScript" type="text/javascript"></script>

</head>

<body>