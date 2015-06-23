<?
/*$alltags='';
if ($curtags!='')
{
	$maintagslist=explode('|',$curtags);
	$i=0;
  			foreach ($maintagslist as $tag)
  			{
             	if ($tag!='')
             	{
                 	if ($i==0)
                 	$size='H2';
                 	elseif ($size!='H4') $size='H4';
                 	elseif ($size!='H3') $size='H3';
                    else $size='H5';

                 	$alltags.='<'.$size.'><a href="/sitesearch.php?searchtext='.$tag.'">'.$tag.'</a></'.$size.'>';
                 	$i++;
             	}

  			}
}

if ($alltags!='')
print  '<div style="padding: 20px 0 10px 0;"><div class="tags">'.$alltags.'</div></div>';*/
?>
<div class="clear"></div>
<div class="hr"></div>
<div class="clear"></div>
<?
$footerdescription=$settings->getOneVal($settings->getIdByName('sitefooterdescr'));
if  ($footertext=='') print '<p>'.$footerdescription.'</p>';
else print '<p>'.$footertext.'</p>';
?>

<noindex>
<div class="counters">
<span>
<!--LiveInternet counter--><script type="text/javascript"><!--
document.write("<a href='http://www.liveinternet.ru/click' "+
"target=_blank><img src='http://counter.yadro.ru/hit?t45.1;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";"+Math.random()+
"' alt='' title='LiveInternet' "+
"border='0' width='31' height='31'><\/a>")
//--></script><!--/LiveInternet-->
</span>

<span>
<!-- begin of Top100 code -->
<script id="top100Counter" type="text/javascript" src="http://counter.rambler.ru/top100.jcn?2115125"></script><noscript><img src="http://counter.rambler.ru/top100.cnt?2115125" alt="" width="1" height="1" border="0"/></noscript>
<!-- end of Top100 code -->

<!-- begin of Top100 logo -->
<a href="http://top100.rambler.ru/home?id=2115125" target="_blank"><img src="http://top100-images.rambler.ru/top100/banner-88x31-rambler-gray2.gif" alt="Rambler's Top100" width="88" height="31" border="0" /></a>
<!-- end of Top100 logo -->
</span>

<span>
 <!--Rating@Mail.ru counter-->
<script language="javascript" type="text/javascript"><!--
d=document;var a='';a+=';r='+escape(d.referrer);js=10;//--></script>
<script language="javascript1.1" type="text/javascript"><!--
a+=';j='+navigator.javaEnabled();js=11;//--></script>
<script language="javascript1.2" type="text/javascript"><!--
s=screen;a+=';s='+s.width+'*'+s.height;
a+=';d='+(s.colorDepth?s.colorDepth:s.pixelDepth);js=12;//--></script>
<script language="javascript1.3" type="text/javascript"><!--
js=13;//--></script><script language="javascript" type="text/javascript"><!--
d.write('<a href="http://top.mail.ru/jump?from=1801918" target="_top">'+
'<img src="http://de.c7.bb.a1.top.mail.ru/counter?id=1801918;t=216;js='+js+
a+';rand='+Math.random()+'" alt="Рейтинг@Mail.ru" border="0" '+
'height="31" width="88"><\/a>');if(11<js)d.write('<'+'!-- ');//--></script>
<noscript><a target="_top" href="http://top.mail.ru/jump?from=1801918">
<img src="http://de.c7.bb.a1.top.mail.ru/counter?js=na;id=1801918;t=216"
height="31" width="88" border="0" alt="Рейтинг@Mail.ru"></a></noscript>
<script language="javascript" type="text/javascript"><!--
if(11<js)d.write('--'+'>');//--></script>
<!--// Rating@Mail.ru counter-->
</span>


<span id="ProPermClickFix"></span>
<!-- ProPerm.ru -->
<script language="javascript">
document.observe('dom:loaded', function() {
Md=document;
Md.cookie="pro=b";Mc=0;if(Md.cookie) Mc=1;
My="";My+="<a href='http://properm.ru/internet/top/gotop.php?sid=5348'"+
"target='_blank'>";
My+="<img src='http://properm.ru/internet/top/counter.php?sid=5348&c="+Mc+
"' alt='Рейтинг ProPerm.ru' border='0'>";
My+="</a>";
document.getElementById('ProPermClickFix').innerHTML = My;
});
 </script>
 <!-- ProPerm.ru -->
<span>
<!-- Начало кода счетчика UralWeb -->
<script language="JavaScript" type="text/javascript">
<!--
  uralweb_d=document;
  uralweb_a='';
  uralweb_a+='&r='+escape(uralweb_d.referrer);
  uralweb_js=10;
//-->
</script>
<script language="JavaScript1.1" type="text/javascript">
<!--
  uralweb_a+='&j='+navigator.javaEnabled();
  uralweb_js=11;
//-->
</script>
<script language="JavaScript1.2" type="text/javascript">
<!--
  uralweb_s=screen;
  uralweb_a+='&s='+uralweb_s.width+'*'+uralweb_s.height;
  uralweb_a+='&d='+(uralweb_s.colorDepth?uralweb_s.colorDepth:uralweb_s.pixelDepth);
  uralweb_js=12;
//-->
</script>
<script language="JavaScript1.3" type="text/javascript">
<!--
  uralweb_js=13;
//-->
</script>
<script language="JavaScript" type="text/javascript">
<!--
uralweb_d.write('<a href="http://www.uralweb.ru/rating/go/rperm">'+
'<img border="0" src="http://hc.uralweb.ru/hc/rperm?js='+
uralweb_js+'&amp;rand='+Math.random()+uralweb_a+
'" width="88" height="31" alt="Рейтинг UralWeb" /><'+'/a>');
//-->
</script>

<noscript>
<a href="http://www.uralweb.ru/rating/go/rperm">
<img border="0" src="http://hc.uralweb.ru/hc/rperm?js=0" width="88" height="31" alt="Рейтинг UralWeb" /></a>
</noscript>
<!-- конец кода счетчика UralWeb -->
</span>
 <span style="position:absolute;">По всем вопросам и предложениям обращайтесь <a href="mailto:info@rperm.ru">info@rperm.ru</a></span>


</div>
</noindex>
