<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/include.php");?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/init_imag.php");?>
<?
$pay_enable=true;
$pay_types=array(0=>'Наличными', 1=>'Онлайн оплата: картой, эл. деньгами, ....');


$sklad_section=$SiteSections->getByPattern('PSklad');
if ($sklad_section['id']>0)
{
	$skPattern = new $sklad_section['pattern'];
	$skIface = $skPattern->init(array('section'=>$sklad_section['id']));
}
	
include_once($_SERVER['DOCUMENT_ROOT']."/inc/include_site.php");
$Section = $SiteSections->getByPattern('PBasket');
if ($Section['id']>0)
{
	$Pattern = new $Section['pattern'];
	$Iface = $Pattern->init(array('section'=>$Section['id']));
	if ($Section['description']!='') $headerh1=$Section['description'];
}

$nav_text='<div>Корзина</div>';

$delivery_types=getSprValues('/sitecontent/basket/delivery_type/','',false);


if ($_POST['action']=='send'){
	
	if ($_POST['name']=='') $errors[] = 'не заполнено поле: Имя или ФИО';
	if (trim($_POST['phone'])=='') $errors[] = 'не заполнено поле: Телефон';
	
	if (trim($_POST['phone'])!='')
	{
		
		$clear_tel=strtr(trim($_POST['phone']), array("-" => '', " " => '', "+7" => '8', "+" => '8', "342" => '', "(" => '', ")" => '', "[" => '', "]" => '', "{" => '', "}" => ''));
		
		$number_kol=0;
		for ($i = 0; $i < strlen($clear_tel); $i++)
		{
			if ($clear_tel[$i]>0) $number_kol++;
		}
		/*Если нет цифр в телефоне*/
		if ($number_kol==0) $errors[] = 'не заполнено поле: Телефон';
		
		
		if ($number_kol>0 && strlen($clear_tel)<=6) $errors[] = 'не правильно заполнен телефон: в номере должно быть минимум 7 цифр';
		elseif ($number_kol>0 && (strlen($clear_tel)>7 && strlen($clear_tel)<10)) $errors[] = 'не правильно заполнен телефон: в номере мобильного должно быть минимум 10 цифр';
		
	}
	
	
	if ($skIface)
	{
		$slkad_errors=$basket->PreSave($order_tmp);
		
		if (count($slkad_errors)>0)
		foreach ($slkad_errors as $se) {
			$errors[]=$se;
		}
	}
	
	if (count($errors)==0){
		
		$save=array(
			'name'=>$_POST['name'],
			'phone'=>$clear_tel,
			'address'=>$_POST['address'],
/* 			'address_street'=>$_POST['address_street'],
			'address_house'=>$_POST['address_house'],
			'address_flat'=>$_POST['address_flat'],
			'address_entrance'=>$_POST['address_entrance'],
			'address_floor'=>$_POST['address_floor'], */
			'email'=>$_POST['email'],
			/* 'delivery_id'=>$_POST['delivery'], */
			'comment'=>$_POST['comment']
		);
		
		$_SESSION['saved_order_num']=$basket->SaveOrder($order_tmp, $save);
		
		if ($skIface)	$skIface->updateSklad();
		
		if ($_POST['pay_type']==1 && $pay_enable)
		$pay_order=$_SESSION['saved_order_num'];

		
	 	if (!$pay_order>0)
	 	{
			$basket->SendOrder($_SESSION['saved_order_num'], $rus_calls);
			
			
			$basket->SaveCookie($save);
			
	 		$_SESSION['tmp_order_num']='';
			cookieSet('tmp_order_num', '');
			/* header("Location: ".configGet("AskUrl")."?ok=ok\n"); */
	 	}
	}
}

	if ($_GET['payment_order_num']>0)
	$pay_order=$_GET['payment_order_num'];
	
?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/meta.php");?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/header.php");?>
<script>
$(function() {
	var session_id = '<?php echo session_id(); ?>';

			$(".kol_buttons a").click(function() 
			{

				$(this).parents('tr').find('.preloader').show();

				var inc_cnt=0;
				var new_cnt=0;

				
				if ($(this).hasClass("plus")) 
				inc_cnt=1;
				else inc_cnt=-1;

				new_cnt=parseInt($(this).parents('tr').find('[name=kol]').val())+inc_cnt;

				if (new_cnt<0) new_cnt=0; 

				if (new_cnt>0)
				{
					basket_add(session_id, '<?=$order_tmp ?>', $(this).parents('tr').attr('id'), new_cnt);
					basket_update_summ(session_id, '<?=$order_tmp ?>', $(this).parents('tr').attr('id'), $(this).parents('tr'));
				}
				else
				basket_delete(session_id, '<?=$order_tmp ?>', $(this).parents('tr').attr('id'), '0', $(this).parents('tr').find('.close'));

				basket_comment(session_id, '<?=$order_tmp ?>');

				$(this).parents('tr').find('.preloader').hide();
			});

			$("[name=pay_type]").change(function() {
				var button_comment=$(this).val()=='1' ? 'Далее' : 'Отправить';

				$('#send_button').html('<span>'+button_comment+'</span>');

			});
});
</script>
<div class="content_container styled">
<div class="mininav"><a href="/">Главная</a><img src="/pics/arrows/arrow_nav.png"><?=$nav_text?></div>
<?
if ($_GET['ok']=='ok')
{
	?><h2>Ваш заказ успешно отправлен, в ближайшее время с вами свяжется наш менеджер</h2><?	
}
else{
	if (count($errors)>0){
	?>
	         <h2 style="color: #DE1A10"><?=(($client_pay_type>0) ? 'Оплата заказа недоступна по следующим причинам:' : 'Не удается отправить заказ по следующим причинам:')?></h2>
	         <div class="styled">
	         <ul>
	            <?
	            foreach ($errors as $t) print '<li>'.$t.'</li>';
	            ?>
	         </ul>
	         </div>
	         <div class="air p20"></div>
	    <?
	    }
		?>
	<div class="order">
	<?
	if ($pay_order>0)
	{
		if (!isset($_POST['pay_type'])) $_POST['pay_type']=1;
		$total=$basket->GetTotalBasket_saved($pay_order);
		?>
		<h1><?=$pay_types[$_POST['pay_type']] ?></h1>
		<form method="post" action="https://www.payanyway.ru/assistant.htm"  target="_blank" id="moneta_form" name="moneta_form">
			<input type="hidden" name="MNT_ID" value="51827666">
			<input type="hidden" name="MNT_TRANSACTION_ID" value="<?=$pay_order?>">
			<input type="hidden" name="MNT_CURRENCY_CODE" value="RUB">
			<input type="hidden" name="MNT_AMOUNT" value="<?=$total['summ']?>">
		</form>
		<a href="#" class="btn big pay" onclick="document.getElementById('moneta_form').submit(); return false;" target="_blank" class="pay">Перейти к оплате</a> 
		<div class="clear"></div>
		<div class="air p30"></div>
		<?	
	}
	
	
	$basket_items=$basket->GetAllBasketItem($order_tmp);
	if (count($basket_items)>0)
	{
		?>
		<div class="order_table">
			<h2>Ваш заказ:</h2>
			<table>
				<tr>
					<th class="img"></th>
					<th>Наименование</th>
					<th>Цена</th>
					<th class="kol" style="width: 240px">Количество</th>
					<th>Сумма</th>
					<?if (!$pay_order>0) {?><th style="width:10px;"></th><?} ?>
				</tr>
			<?
			foreach ($basket_items as $item)
			{
				
				
				$good=$basket->GetGood($item['good_id']);
				$categ=$basket->GetGoodCateg($good['categ_id']);
				
				$images=clear_array_empty(explode('|', $good['gallery']));
   		
   				if ($images[0]>0)
   				$image=$Storage->getFile($images[0]);
				?>
				<tr id="<?=$good['id'] ?>">
					<td class="img">
						<div><a href="/goods/<?=$good['id'] ?>/"><img src="<?=$image['path'] ?>" alt=""/></a></div>
					</td>
					<td class="descr" style="text-align: left; padding-left: 40px;">
						<a href="/goods/<?=$good['id'] ?>/"><?=$good['name'] ?></a>
						<?
   							$comment=$basket->getTypeComment($item);
   							if ($comment!='') print '<div class="comment">'.$comment.'</div>';
   						?>
					</td>
					<td><nobr><?=number_format($item['price'], 0, '.', ' ') ?></nobr></td>
					<td class="kol">
					<?if ($pay_order>0) 
					{?>
						<?=$item['kol']?>
					<?
					}else { ?>
						<div class="kol_buttons form">
							<img src="/pics/loader_site.gif" alt="" class="preloader"/>
							<div><a class="btn big minus" href="#" onclick="return false;">-</a></div>
							<div><input id="kol" name="kol" type="text" value="<?=$item['kol'] ?>" disabled/></div>
							<div><a class="btn big plus" href="#" onclick="return false;">+</a></div>
						</div>
					<?} ?>
					</td>
					<td class="summ"><nobr><?=number_format($item['summ'], 0, '.', ' ') ?></nobr></td>
					<?if (!$pay_order>0) {?> 	
						<td><div class="close"><a onclick="basket_delete('<?php echo session_id(); ?>', '<?=$order_tmp ?>', '<?=$good['id'] ?>', '0', this); return false;" title="Удалить из корзины" href="#"></a></div></td>
					<?} ?>
				</tr>
				<?
				
			}
			?></table>
	<?}?>	
		</div>
	
		<div class="alert_empty" style="display: <?=count($basket_items)>0 ? 'none':'block' ?>"><h2>В корзине нет товаров. <a href="/">Выбрать товары</a></h2></div>	
	
	<?if (count($basket_items)>0 && !$pay_order>0){ 
		$basket->GetSaveCookie(array('name', 'phone', 'address', 'comment'));
		?>
		<div class="order_form">
			<br/><h2>Оформить заказ:</h2><br/>
				
				<form method="post" name="orderform" id="orderform" class="-visor-no-click">
			   		<div style="max-width: 600px;" class="form send">
			   					
		   					<div>
			   					<label>Имя или ФИО<span class="important">*</span></label>
								<input type="text" name="name" id="name" class="imp" value="<?=$_POST['name']?>" autocomplete="off"/>
							</div>
	
	                        <div class="inp">
						    	<label>Телефон <span class="important">*</span></label>
								<input type="text" name="phone" id="phone" class="imp" value="<?=$_POST['phone']?>" autocomplete="off"/>
	                        </div>
	                        
	                        <div class="styled">
						    <?
						    $i=0;
						    foreach ($delivery_types as $k=>$v)
						    {
						    	?><div class="delivery_type<?=$k ?>"><input id="delivery<?=$k ?>" name="delivery" value="<?=$k ?>" type="radio" <?=$i==0 ? 'checked="checked"':'' ?> ><label for="delivery<?=$k?>"><span><span></span></span><?=html_entity_decode(str_replace('"/','', htmlspecialchars_decode($v, ENT_QUOTES)))?></label></div><?
						    	$i++;
						    }
						    ?>
						    <br/>
							</div>
							
							<div class="inp">
						    	<label>Адрес <small>(при самовывозе не заполняется)</small></label>
								<input type="text" name="address" id="address" class="imp" value="<?=$_POST['address']?>" autocomplete="off"/>
	                        </div>
	                        
	                        <div class="inp">
						    	<label>Email</label>
								<input type="text" name="email" id="email" class="imp" value="<?=$_POST['email']?>" autocomplete="off"/>
	                        </div>
	                        
	                        
	                        <div class="inp">
								<label>Комментарий к заказу</label>
								<input type="text" name="comment" value="<?=$_POST['comment']?>" value="<?=$_POST['comment']?>" autocomplete="off"/>
		                    </div>

		                    <div class="button" style="float: right; padding-top: 10px;">
								<div id="order" name="order"><a href="#" class="btn big" onclick="$('#orderform').submit(); return false;" id="send_button"><span>Отправить</span></a></div>
							</div>
						
							<input type="hidden" name="action" value="send" />
		                    
			   		</div>
			   	</form>
			   	<div class="clear"></div>
		</div>
	<?} ?>

<?} ?>
</div>


</div>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/footer.php");?>