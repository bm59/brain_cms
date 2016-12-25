<div class="line"></div>

<div class="container">

	<div class="header">	
		
			<div class="logo"><a href="/"><img src="/pics/logo.png" alt=""/></a></div>
			<div class="header_main"><?=setting('header_main') ?></div>
			<div class="header_comment"><?=setting('header_comment') ?></div>
			<div class="contacts">

				<div class="call_order">
					<a rel="leanModal" name="signup" href="#signup" onclick="show_modal_my('#popup_callback'); $('#phone').focus(); return false;"><img src="/pics/phone.png" height="20px" alt="Добавить в избранное"/>заказать звонок</a>
					<span>&nbsp;&nbsp;&nbsp;</span>
					<a href="#" title="добавить сайт в закладки"  onclick="return bookmark(this);"><img src="/pics/fav.png" height="20px" alt="Добавить в избранное"/>добавить в закладки</a>
				</div>
				<div class="fav"></div>
				<div class="phone"><?=setting('phone') ?></div>
				<div class="address"><?=setting('address') ?></div>
			</div>
			
			
	</div>

	<ul class="main_menu">
		<li class="item"><a class="home" href="/"><div class="icon"></div></a></li>
		<li class="item"><a <?=configGet("AskUrl")=='/' ? 'class="active"':'' ?> href="/">Товары</a></li>
		<?
		$mainmenu = $SiteSections->getList(6);

		$i=0;
		foreach ($mainmenu as $mm)
			if ($mm['visible']==1)
			{
				$i++;
				?><li class="item"><a <?=(
								(
									$_SERVER['REQUEST_URI']=='/'.$mm['path'].'/' ||
									(stripos($_SERVER['REQUEST_URI'], '/'.$mm['path'].'/')==0 && stripos($_SERVER['REQUEST_URI'], '/'.$mm['path'].'/')!==false)
								) ? 'class="active"':'')?> href="/<?=$mm['path']?>/"><?=$mm['name']?></a></li><?
			}
		?>
		<li class="basket btn"><a href="/basket/">Оформить заказ</a></li>
		<li class="basket info">
			<div><a href="/basket/"><img src="/pics/basket.png" alt="Корзина"/></a></div>
			<div class="comment"><?=$total_basket_comment!='' ? $total_basket_comment: 'Корзина (пусто)' ?></div>
		</li>
	</ul>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/small_menu.php");?>
	
