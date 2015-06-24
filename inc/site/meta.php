<?
	$SiteSections= new SiteSections;
	$SiteSections->init();
	$ss = $SiteSections->get($SiteSections->getIdByPath('/sitecontent'.configGet("AskUrl")));

	$SiteSettings = new SiteSettings;
	$SiteSettings->init();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
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

<script src="/js/jquery.js"	type="text/javascript"></script>
<script src="/js/lightbox.js" type="text/javascript"></script>

<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<script src="http://phpbbex.com/oldies/oldies.js" charset="utf-8"></script>
<![endif]-->
<!--[if lte IE 7]>
<link href="/assets/ie6-7-5e39e02326eb9f4d81ffc04963e59794.css" media="screen" rel="stylesheet" type="text/css" />
<![endif]-->

</head>

<body>