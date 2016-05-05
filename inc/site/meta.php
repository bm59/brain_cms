<!DOCTYPE html>
<head>
<meta charset="utf-8">

<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="Content-Language" content="ru">

<title><?=html_entity_decode($SiteSections->getTitle($ss['id']))?></title>
<meta name="description" content="<?=$SiteSections->getDescription($ss['id'])?>" />
<meta name="keywords" content="<?=$SiteSections->getKeywords($ss['id'])?>" />

<link rel="icon" href="/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

<link rel="stylesheet" type="text/css" href="/css/universal.css" media="all" />
<link rel="stylesheet" type="text/css" href="/css/site.css" media="all" />
<link rel="stylesheet" type="text/css" href="/css/lightbox.css" media="all" />
<link rel="stylesheet" type="text/css" href="/css/popup.css" media="all" />
<link rel="stylesheet" type="text/css" href="/css/controls.css" media="all" />

<script src="/js/jquery.js"	type="text/javascript"></script>
<script src="/js/lightbox.js" type="text/javascript"></script>

<script src="/js/popup.js" type="text/javascript"></script>
<!-- <script src="/js/html/gallery_editor.js" type="text/javascript"></script>  -->
<!-- <script src="/js/html/fix_gallery.js" type="text/javascript"></script> -->

<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<script src="http://phpbbex.com/oldies/oldies.js" charset="utf-8"></script>
<![endif]-->
<!--[if lte IE 7]>
<link href="/assets/ie6-7-5e39e02326eb9f4d81ffc04963e59794.css" media="screen" rel="stylesheet" type="text/css" />
<![endif]-->
<script type="text/javascript">

var small_menu_shown = false;

$(document).ready(function() {


    jQuery(window).scroll(function() {

		if (jQuery(window).scrollTop() >= 160){
					if(!small_menu_shown){
						small_menu_shown = true;
						$('.small_menu_container').css({display: 'block'});
						$('.small_menu_container').css({top: '-100px'});
						$('.small_menu_container').animate({top : 0});
					}
				}else{
					if(small_menu_shown){
						small_menu_shown = false;
							$('.small_menu_container').animate({top : '-100px'}, 300, 'swing', function(){
							$('.small_menu_container').css({top:0});
							$('.small_menu_container').css({display: 'none'});
						})
					}
				}

	});


});


$(function(){
	  $.fn.scrollToTop=function(){
	    $(this).hide().removeAttr("href");
	    if($(window).scrollTop()!="0"){
	        $(this).fadeIn("slow")
	  }
	  var scrollDiv=$(this);
	  $(window).scroll(function(){
	    if($(window).scrollTop()=="0"){
	    $(scrollDiv).fadeOut("slow")
	    }else{
	    $(scrollDiv).fadeIn("slow")
	  }
	  });
	    $(this).click(function(){
	      $("html, body").animate({scrollTop:0},"slow")
	    })

	  }
	});
	$(function() {$("#toTop").scrollToTop();});
</script>
</head>

<body>