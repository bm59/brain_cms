

<a href="#" id="toTop"></a>
<div id="lean_overlay" style="display: none;"></div>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/callback.php");?>
</div>
<div class="footer">
		<div class="container">
			<div class="footer_logo"><img src="/pics/logo.png"/></div>
			
			<div class="footer_menu">
				<a <?=configGet("AskUrl")=='/' ? 'class="active"':'' ?> href="/">Товары</a>
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
			
			<div class="dev"><a href="http://brain-site.ru" onclick="target='_blank';" title="Создание сайта: brain-site.ru"><img src="/pics/dev_white.png" alt="Создание сайта: brain-site.ru"/></a></div>
		</div>
</div>

<?print setting('counters');?>
</body>
</html>