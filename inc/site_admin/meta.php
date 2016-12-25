<?
$SiteSections= new SiteSections;
$SiteSections->init();

/*$ss = $SiteSections->get($SiteSections->getIdByPath(configGet("AskUrl")));*/
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">

<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="Content-Language" content="ru">

<meta name="title" content="Панель управления <?=' - '.ConfigGet('pr_doptit')?>" />
<link rel="icon" href="/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

<link rel="stylesheet" href="/css/manage.css" type="text/css" media="screen" />
<link rel="stylesheet" href="/js/jquery-ui-1.10.4.custom/css/blitzer/jquery-ui-1.10.4.custom.css" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="/css/inputs/checkbox.css" media="all" />
<!--//<link rel="stylesheet" type="text/css" href="/css/tablednd.css" media="all" />//-->
<link rel="stylesheet" type="text/css" href="/css/highslide.css" />
<link href="/css/inputs/jquery.contextMenu.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="/css/jquery.multiselect.css" media="all" />
<link rel="stylesheet" type="text/css" href="/css/universal.css" media="all" />


<script src="/js/jquery.js" type="text/javascript"></script>
<script src="/js/manage.js" type="text/javascript"></script>
<script src="/js/imag.js" type="text/javascript"></script>



<script src="/js/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.min.js" type="text/javascript"></script>
<script src="/js/jquery.multiselect.js" type="text/javascript"></script>
<script src="/js/spinner_default.js" type="text/javascript"></script>
<script src="/js/spinner_rub.js" type="text/javascript"></script>
<script src="/js/ajaxupload.3.5.js" type="text/javascript"></script>
<script src="/js/inputs/jquery.multiple.select.js" type="text/javascript"></script>
<script src="/js/highslide.js" type="text/javascript"></script>
<script src="/js/inputs/jquery.contextMenu.js" type="text/javascript"></script>
<script src="/js/jquery.cookie.js" language="JavaScript" type="text/javascript"></script>
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

