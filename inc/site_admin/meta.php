<?
$SiteSections= new SiteSections;
$SiteSections->init();

/*$ss = $SiteSections->get($SiteSections->getIdByPath(configGet("AskUrl")));*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=windows-1251" />
<meta http-equiv="content-language" content="ru" />
<meta name="title" content="Панель управления <?=' - '.ConfigGet('pr_doptit')?>" />
<link rel="icon" href="/favico_manage.ico" type="image/x-icon" />
<link rel="shortcut icon" href="/favico_manage.ico" type="image/x-icon" />

<link rel="stylesheet" href="/css/manage.css" type="text/css" media="screen" />
<link rel="stylesheet" href="/js/jquery-ui-1.10.4.custom/css/blitzer/jquery-ui-1.10.4.custom.css" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="/css/multiple-select.css" media="all" />
<link rel="stylesheet" type="text/css" href="/css/checkbox.css" media="all" />
<!--//<link rel="stylesheet" type="text/css" href="/css/tablednd.css" media="all" />//-->
<link rel="stylesheet" type="text/css" href="/css/highslide.css" />

<script src="/js/manage.js" type="text/javascript"></script>

<script src="/js/jquery.js" type="text/javascript"></script>
<script src="/js/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.min.js" type="text/javascript"></script>
<script src="/js/ajaxupload.3.5.js" type="text/javascript"></script>
<script src="/js/jquery.multiple.select.js" type="text/javascript"></script>
<script src="/js/highslide.js" type="text/javascript"></script>
<!--//<script src="/js/jquery.tablednd.js" language="JavaScript" type="text/javascript"></script>//-->
<!--//<script src="/js/imag_manage.js" language="JavaScript" type="text/javascript"></script>//-->
<script type="text/javascript">
	hs.graphicsDir = '/pics/graphics/';
  	hs.outlineType = 'rounded-black';

	jQuery(function(){jQuery.fn.scrollToTop=function(){jQuery(this).hide().removeAttr("href");if(jQuery(window).scrollTop()!="0"){jQuery(this).fadeIn("slow")}var scrollDiv=jQuery(this);jQuery(window).scroll(function(){if(jQuery(window).scrollTop()=="0"){jQuery(scrollDiv).fadeOut("slow")}else{jQuery(scrollDiv).fadeIn("slow")}});jQuery(this).click(function(){jQuery("html, body").animate({scrollTop:0},"fast")})}});
	jQuery(function() {jQuery("#toTop").scrollToTop();});
 	$(document).ready(function(){
		 $( ".button.disabled" ).click(function()
		 {		 	/*alert('Кнопка недоступна');*/
		 	return false;
		 });
	});
</script>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/access.php");?>
<title>Панель управления <?=' - '.ConfigGet('pr_doptit')?></title>
</head>
<body>
<div id="zbody">

