

<a href="#" id="toTop"></a>
<div id="lean_overlay" style="display: none;"></div>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/callback.php");?>
</div>
<div class="footer">
		<div class="social">
			<a href="https://vk.com/" class="vk" target="_blank"></a>
			<a href="https://www.facebook.com/" class="fb" target="_blank"></a>
			<a href="https://twitter.com/" class="twi" target="_blank"></a>
			<a href="http://www.youtube.com/" class="yt" target="_blank"></a>
		</div>
		<!-- <div class="container">-->
		<div class="footer_menu">
			<a <?=configGet("AskUrl")=='/' ? 'class="active"':'' ?> href="/">Продукция</a>
				<?
	
			$mainmenu = $SiteSections->getList(6);
	
			$i=0;
			foreach ($mainmenu as $mm)
				if ($mm['visible']==1)
				{
					$i++;
					?><a <?=(
									(
										$_SERVER['REQUEST_URI']=='/'.$mm['path'].'/' ||
										(stripos($_SERVER['REQUEST_URI'], '/'.$mm['path'].'/')==0 && stripos($_SERVER['REQUEST_URI'], '/'.$mm['path'].'/')!==false)
									) ? 'class="active"':'')?> href="/<?=$mm['path']?>/"><?=$mm['name']?></a><?
				}
			?>
		</div>
		<!-- </div>-->
		
		<div class="dev"><a href="http://brain-site.ru" onclick="target='_blank';" title="Разработка сайта: brain-site.ru"><img src="/pics/dev_white.png" alt="Разработка сайта: brain-site.ru"/></a></div>
</div>

<?print setting('counters');?>
</body>
</html>