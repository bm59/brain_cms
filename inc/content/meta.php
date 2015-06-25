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
<link rel="stylesheet" type="text/css" href="/css/tablednd.css" media="all" />

<script src="/js/manage.js" language="JavaScript" type="text/javascript"></script>
<script src="/js/imag_manage.js" language="JavaScript" type="text/javascript"></script>
<script src="/js/jquery.js" language="JavaScript" type="text/javascript"></script>
<script src="/js/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.min.js" language="JavaScript" type="text/javascript"></script>
<script src="/js/prototype.js" language="JavaScript" type="text/javascript"></script>
<script src="/js/ajaxupload.3.5.js" language="JavaScript" type="text/javascript"></script>
<script src="/js/jquery.multiple.select.js" language="JavaScript" type="text/javascript"></script>
<script src="/js/jquery.tablednd.js" language="JavaScript" type="text/javascript"></script>
<script type="text/javascript">
	jQuery(function(){jQuery.fn.scrollToTop=function(){jQuery(this).hide().removeAttr("href");if(jQuery(window).scrollTop()!="0"){jQuery(this).fadeIn("slow")}var scrollDiv=jQuery(this);jQuery(window).scroll(function(){if(jQuery(window).scrollTop()=="0"){jQuery(scrollDiv).fadeOut("slow")}else{jQuery(scrollDiv).fadeIn("slow")}});jQuery(this).click(function(){jQuery("html, body").animate({scrollTop:0},"fast")})}});
	jQuery(function() {jQuery("#toTop").scrollToTop();});
</script>

<title>Панель управления <?=' - '.ConfigGet('pr_doptit')?></title>
</head>
<body>
 <div id="zbody">
<div id="margin">
<?
if (configGet("AskUrl")!='/'){

        if (!$_GET['section']>0 && !$_GET['edit']>0)
        $activeccid = $SiteSections->getIdByPath(ereg_replace('/manage/', '', configGet("AskUrl")));
        else
        {
        	$s_id=(($_GET['section']>0) ? $_GET['section'] : $_GET['edit']);
        	$activeccid=$s_id;
        }

        $plist = $SiteSections->getParentsList($activeccid);
        print '<div class="smallnav"><a href="/manage/">Панель управления</a>';
        foreach ($plist as $p){
                if ($p!=$activeccid){
                        $pobj = $SiteSections->get($p);
                        $href = !preg_match('|^'.$SiteSections->getPath($pobj['id']).'$|',configGet("AskUrl"));
                        $sctn = $SiteSections->get($pobj['id']); $sctn['id'] = floor($sctn['id']);
                        if ($sctn['id']>0){
                                $ptrn = new $sctn['pattern'];
                                $ifface = $ptrn->init(array('section'=>$sctn['id']));
                                if ($ptrn->getSetting('name')=='PFolder'){
                                        if ($sctn['path']=='control' || $sctn['path']=='access')
                                        $href = false;
                                }
                        }

                        if (!$s_id>0)
                        print ($href)?' — <a href="'.$SiteSections->getPath($pobj['id']).'">'.$pobj['name'].'</a>':' — '.$pobj['name'];
                        else
                        {
                         	if ($ptrn->getSetting('name')=='PFolder')
                         	print ($href)?' — <a href="/manage/control/contents/?edit='.$sctn['id'].'">'.$pobj['name'].'</a>':' — '.$pobj['name'];
                         	else
                         	print ($href)?' — <a href="/manage/control/contents/?section='.$sctn['id'].'">'.$pobj['name'].'</a>':' — '.$pobj['name'];
                        }
                }
        }
        $pobj = $SiteSections->get($activeccid);
        $href = !preg_match('|^'.$SiteSections->getPath($pobj['id']).'$|',configGet("AskUrl"));
        $sctn = $SiteSections->get($pobj['id']); $sctn['id'] = floor($sctn['id']);
        if ($sctn['id']>0){
                $ptrn = new $sctn['pattern'];
                $ifface = $ptrn->init(array('section'=>$sctn['id']));
                switch($ptrn->getSetting('name')){

                        case 'PFolder':
                                print ' — '.$pobj['name'];
                                break;
                        case 'PPublication':
                                print ($pub['id']>0)?' — <a href="'.$SiteSections->getPath($pobj['id']).'">'.$pobj['name'].'</a>':' — '.$pobj['name'];
                                break;
                        case 'PList':
                                print ($pub['id']>0)?' — <a href="'.$SiteSections->getPath($pobj['id']).'">'.$pobj['name'].'</a>':' — '.$pobj['name'];
                                break;
                        case 'PSheet1':
                                print ($pub['id']>0)?' — <a href="'.$SiteSections->getPath($pobj['id']).'">'.$pobj['name'].'</a>':' — '.$pobj['name'];
                                break;
                        default:
                                print ($href)?' — <a href="'.$SiteSections->getPath($pobj['id']).'">'.$pobj['name'].'</a>':' — '.$pobj['name'];
                                break;
                }
        }
        print '</div>';
}
?>
</div></div>
