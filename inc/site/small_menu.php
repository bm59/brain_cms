<div class="small_menu_container" style="display: none;">
	<div class="container">
		<div class="small_menu">
		<div><a href="#" class="img"><img src="/pics/logo.png" style="height: 50px;"></a></div>
		<div class="name"><?=setting('header_main') ?></div>	
			<div class="phone"><?=setting('phone')?></div>
			<div class="call"><a rel="leanModal" name="signup" href="#signup" onclick="show_modal_my('#popup_callback'); $('#phone').focus(); return false;">заказать звонок</a></div>
			<div class="basket_small">
					<span class="basket"><a href="/basket/"><img src="/pics/basket_black.png"/></a></span>
					<span class="comment"><?=$total_basket_comment!='' ? $total_basket_comment: 'выберите товары' ?></span>
					<span class="button" <?=$total_basket_comment!='' ? 'style="display: inline-block;"': 'style="display: none;"' ?>><a href="/basket/">оформить заказ</a></span>
				</span>
			</div>
		</div>
	</div>
	
</div>