<div class="small_menu_container" style="display: none;">
	<div class="call"><a rel="leanModal" name="signup" href="#signup" onclick="show_modal_my('#popup_callback'); $('#phone').focus(); return false;">заказать звонок</a></div>
	<div class="container">
		<div class="small_menu">
		<div><a href="#" class="img"><img src="/pics/logo_min.png" style="height: 50px;"></a></div>
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
			
			<div class="phone"><?=setting('phone')?></div>
			<div class="basket_small">
					<span class="basket"><a href="/basket/"><img src="/pics/basket_black.png"/></a></span>
					<span class="comment">товары не выбраны</span>
					<span class="button"><a href="/basket/">оформить заказ</a></span>
				</span>
			</div>
		</div>
	</div>
	
</div>