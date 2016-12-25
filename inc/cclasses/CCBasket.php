<?
include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/include.php");
class CCBasket extends VirtualContent
{

	public $report=true;
	
	function init($settings){
        		global $SiteSections;
                VirtualContent::init($settings);

                $section = $SiteSections->get($this->getSetting('section'));
                $this->Settings['settings_personal']=$section['settings_personal'];

                $SiteSettings = new SiteSettings;
                $SiteSettings->init();
                $this->Settings['onpage'] = $this->Settings['settings_personal']['on_page']>0 ? $section['settings_personal']['on_page'] : 20;



                $this->like_array=array('search_href');/* Где нет в названии "name", но нужен поиск по like - search_href*/
                $this->not_like_array=array();/* Где есть в названии "name", но не нужен поиск по like*/
                $this->no_auto=array(); /*Не обрабатывать переменные, ручная обработка*/

                /*заменить на пустое в названиях переменны при поиске*/
                $this->field_tr=array('search_'=>'','_from'=>'','_to'=>'');

                /*подмена названий*/
                $this->field_change=array();


 				$this->getSearch();
 				
 				/* Товары */
 				$goods_section=$SiteSections->getByPattern('PGoods');
 				if (!$goods_section['id']>0)
 				print '<h2>Не найден раздел с товарами</h2>';
 				
 				if ($goods_section['id']>0)
 				{
 					$this->goods_iface=getIface($SiteSections->getPath($goods_section['id']));
 					$this->setSetting('table_goods', $this->goods_iface->getSetting('table'));
 				}	
 				
 				$categ_section=$SiteSections->getIdByPath($SiteSections->getPath($goods_section['id']).'categs/');
 				if (!$categ_section['id']>0)
 				print '<h2>Необходимо добавить дочерний раздел "categs" с категориями товаров</h2>';
 				else
 				$this->categ_iface=getIface($SiteSections->getPath($goods_section['id']).'categs/');
		
 				
 				$start_settings='|nopathchange|nodestination|undrop|undeletable|enable_actions=view,add,edit,delete,report,offonly|';
 				if ($section['settings']!=$start_settings)
 				$SiteSections->update_settings($section['id'], $start_settings);
 				


   }
   function deletePub($id,$updateprec = true){
   	global $SiteSections;
   	$id = floor($id);
   	global $CDDataSet;
   	if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'"))){
   		$dataset = $CDDataSet->get($this->getSetting('dataset'));
   		$imagestorage = $this->getSetting('imagestorage');
   		foreach ($dataset['types'] as $dt){
   			$tface = new $dt['type'];
   			$tface->init(array('name'=>$dt['name'],'description'=>$dt['description'],'value'=>$r[$dt['name']],'imagestorage'=>floor($imagestorage['id']),'theme'=>$dataset['name'].'_'.$this->getSetting('section'),'rubric'=>$dt['name'],'uid'=>floor($r['id']),'settings'=>$dt['settings']));
   			$tface->delete();
   		}
   		msq("DELETE FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'");
   		msq("DELETE FROM `site_site_order_goods` WHERE `order_id`='".$id."'");
   		
   		/* Обновляем склад после удаления заказа */
   		if (isset($this->sklad_iface))
   		{
   			$this->sklad_iface->updateSklad();
   		} 
   		
   
   		WriteLog($id, 'удаление записи', '','','',$this->getSetting('section'));
   
   		if ($updateprec) $this->updatePrecedence();
   		return true;
   	}
   	
   	return false;
   }
   function PrintDoubleComment($pub){
   		if ($pub['phone']=='') return;
   		
   		$doubles=msr(msq("SELECT count(*) as `cnt` FROM `".$this->getSetting('table')."` WHERE `id`<>".$pub['id']." and `status_id`=3 and `phone`='".$pub['phone']."'"));
   		if ($doubles['cnt']>0)
   		print '<div><strong>[заверш. заказы: '.$doubles['cnt'].']</strong></div>';
   }
   function SaveOrder($order_tmp, $save)
   {
   		
   		$total=$this->GetTotalBasket($order_tmp);
   		
   		$save['summ']=$total['summ'];
   		$save['summ_clear']=$total['summ'];
   		
   		$keys=$values='';	
   		foreach ($save as $k=>$v)
   		{
   			$keys.=($keys!='' ? ', ':'')."`$k`";
   			$values.=($values!='' ? ', ':'')."'$v'";
   		}
   		msq("SET TIME_ZONE='+5:00'");
   		msq("INSERT INTO `".$this->getSetting('table')."` (`status_id`, `source_id`, `date`, $keys) VALUES ('1', '2', NOW(), $values)");
   		$id=mslastid();
   		
   		$goods=$this->GetAllBasketItem($order_tmp);
   		
   		foreach ($goods as $g)
   		{
   			$keys=$values='';
   			foreach ($g as $k=>$v)
   			if (!in_array($k, array('id', 'tmp_order_id')))
   			{
   				$keys.=($keys!='' ? ', ':'')."`$k`";
   				$values.=($values!='' ? ', ':'')."'$v'";
   			}
   			msq("INSERT INTO `".$this->getSetting('table_order_goods')."` (`order_id`, $keys) VALUES ('$id', $values)");
   			
   		}
   		
   		return $id;
   }
   function SendOrder($id, $rus_calls=array())
   {
   	 
   		include_once($_SERVER['DOCUMENT_ROOT']."/inc/idna_convert.class.php");
   		
   		$rus_calls_base=array('name'=>'Имя',
   				'phone'=>'Телефон',
   				'address_street'=>'Улица',
   				'address_house'=>'Дом',
   				'address_flat'=>'Квартира',
   				'address_entrance'=>'Подъезд',
   				'address_floor'=>'Этаж',
   				'comment'=>'Комментарий'
   		);

   		if (is_array($rus_calls))
   		$rus_calls=array_merge($rus_calls_base, $rus_calls);
   		else $rus_calls=$rus_calls_base;
   		
   		$idn = new idna_convert(array('idn_version'=>2008));
   	
   		$order=$this->GetSaveOrder($id);

   		foreach ($order as $k=>$v)
   		if (isset($rus_calls[$k]) && $v!='')
   		{
   			$msg.='<div>'.$rus_calls[$k].'='.stripcslashes($v).'</div>';
   		}
   		
   		$style='style="border: 1px solid #CCCCCC; padding: 10px;"';
   		
   		$msg.='<h2>Заказ № '.$order['id'].'</h2>';
   		
   		$msg.="<table><tr><th $style>Товар</th><th $style>Кол-во</th><th $style>Сумма</th></tr>";
   		
   	
   		$goods=$this->GetAllOrderItem($id);
   		foreach ($goods as $g)
   		{
   			$good_info=$this->GetGood($g['good_id']);
   			$categ_info=$this->GetGoodCateg($good_info['categs']);
   			if ($good_info['name']=='' && $good_info['diam']!='') $good_info['name']='Диаметр: '.$good_info['diam'];
   			
   			
   			$msg.="<tr><td $style>".$good_info['name'] ."</td><td $style>".$g['kol']."</td><td $style>".$g['summ']."</td></tr>";
   		   							
   		   							
   		}
   		
   		
   		$msg.='</table>';
   		
   		$msg.='<h2>Итого: '.$order['summ'].(($order['summ_discount']>0) ? '[скидка: '.$order['summ_discount'].']' : '').'</h2>';
   		
   		if ($order['paid']==1)
   		$msg.='<h2 style="color: #CC0033">[Заказ оплачен]</h2>';
   		
   		
   		
   		$msg.='</table><br/><br/>Для просмотра заказа <a href="http://'.$_SERVER['HTTP_HOST'].'/manage/control/contents/?section='.$this->getSetting('section').'" target="_blank">Перейдите по ссылке</a>';
   			
   		include_once($_SERVER['DOCUMENT_ROOT']."/inc/SendMailSmtpClass.php");
   		$mailSMTP = new SendMailSmtpClass('brainsite.sender@yandex.ru', 'BrainSite', 'ssl://smtp.yandex.ru', 'brainsite.sender@yandex.ru', 465);
   			
   		
   		$emails=setting('order_email');
		$em_ar=explode('|', $emails);
		foreach ($em_ar as $em)
		{
			if ($em!='') $mailSMTP->SendMail($em, 'заказ c сайта '.$idn->decode($_SERVER['HTTP_HOST']), $msg);
		}
   }
   function SaveCookie($save){
	   	foreach ($save as $k=>$v)
	   	{
	   		cookieSet('basket_'.$k, stripslashes($v),180);
	   	}			
   }
   function GetSaveCookie($fields){
	   	foreach ($fields as $f)
	   	{
	   		if (!isset($_POST[$f]) && cookieGet('basket_'.$f)!='')
	   		$_POST[$f]=stripslashes(htmlspecialchars(cookieGet('basket_'.$f)));
	   	}
   }
   function GetSaveOrder ($id){
   		return msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`=$id"));
   }
   function GetGood ($id){

   		return msr(msq("SELECT * FROM `".$this->getSetting('table_goods')."` WHERE id=$id"));
   		
   }
   function GetGoodCateg ($categs){
   		$return='';
   		$ids=clear_array_empty(explode(',', $categs));
   		foreach ($ids as $id)
   		{
   			$cat=msr(msq("SELECT * FROM `".$this->categ_iface->getSetting('table')."` WHERE id=$id"));
   			$return.=(($return!='') ? '-':'').$cat['name'];
   		}
   		return $return;		
   }
   function GetTotalBasket($tmp_order_id){
   	 
   		$return=msr(msq("SELECT sum(`kol`) as cnt, sum(`summ`) as summ FROM `".$this->getSetting('table_order_tmp_goods')."` WHERE `tmp_order_id`='$tmp_order_id'"));

   		return $return;
   }
   function GetTotalBasket_saved($order_id){
   	 
   	$return=msr(msq("SELECT sum(`summ`) as `summ` FROM `".$this->getSetting('table_order_goods')."` WHERE `order_id`='$order_id'"));
   
   	return $return;
   }
   function GetGoodBasket($tmp_order_id, $good_id){
   	 
   	$return=msr(msq("SELECT * FROM `".$this->getSetting('table_order_tmp_goods')."` WHERE `tmp_order_id`='$tmp_order_id' and `good_id`='$good_id'"));
   
   	return $return;
   }
   function GetGoodBasket_saved($order_id, $good_id){
   	 
   	$return=msr(msq("SELECT * FROM `".$this->getSetting('table_order_goods')."` WHERE `order_id`='$order_id' and `id`='$good_id'"));
   	
   	return $return;
   }
   function GetTotalBasketComment($tmp_order_id){
   	 
   		$return=$this->GetTotalBasket($tmp_order_id);
   
   		if ($return['summ']>0) 
   		return number_format($return['summ'], 0, '.', ' ').'<small> р.</small>'.' ('.$return['cnt'].' <small>шт</small>)';
   }
   function GetAllBasketItem ($tmp_order_id){
   		
   		$return=array();
   		
   		$q=msq("SELECT * FROM `".$this->getSetting('table_order_tmp_goods')."` WHERE `tmp_order_id`='$tmp_order_id'");
   		while ($r=msr($q))
   		{
   			$return[$r['good_id']]=$r;
   		}
   		return $return;
   }
   function GetAllOrderItem ($order_id){
   	 
   	$return=array();
   	 
   	$q=msq("SELECT * FROM `".$this->getSetting('table_order_goods')."` WHERE `order_id`='$order_id'");
   	while ($r=msr($q))
   	{
   		
   		$r['type_comment']=$this->getTypeComment($r);
   		
   		$return[$r['good_id']]=$r;
   	}
   	return $return;
   }
   function getTypeComment ($r){
	   	$type_comment='';
	   	if ($r['size_id']>0)
	   	{
	   		$type=msr(msq("SELECT * FROM `site_site_goods_size` WHERE id=".$r['size_id']));
	   		if ($type['name']!='') $type_comment='<div>[Размер: '.$type['name'].']</div>';
	   	}
	   	 
	   	if ($r['color_id']>0)
	   	{
	   		$type=msr(msq("SELECT * FROM `site_site_goods_color` WHERE id=".$r['color_id']));
	   		if ($type['name']!='') $type_comment.='<div>[Цвет: '.$type['name'].']</div>';
	   	}
	   	return $type_comment;
   	 
   }
   function preSave ($tmp_order_id)
   {
   		global $SiteSections;
   		$return=array();
   		$sklad_section=$SiteSections->getByPattern('PSklad');
   		if ($sklad_section['id']>0)
   		$this->sklad_iface=getIface($SiteSections->getPath($sklad_section['id']));
   		
   		$q=msq("SELECT * FROM `".$this->getSetting('table_order_tmp_goods')."` WHERE `tmp_order_id`='$tmp_order_id'");
   		while ($r=msr($q))
   		{
   			$usl=array('good_id'=>$r['good_id'],'size_id'=>$r['size_id'],'color_id'=>$r['color_id']);
   			
   			$free_kol=$this->sklad_iface->getFreeSkladKol($usl);
   			
   			if ($r['kol']>$free_kol)
   			{
   				$good=$this->GetGood($r['good_id']);
   				$return[]='Недостаточное количество товара "'.$good['name'].'" ('.floor($free_kol).'). Свяжитесь с менеджером интернет магазина';
   			}
   			
   			return $return; 
   		}
   }
   function AddGood ($tmp_order_id, $good_id, $kol, $price_field, $size_id, $color_id)
   {
   		global $SiteSections;
   		
   		$sklad_section=$SiteSections->getByPattern('PSklad');
   		if ($sklad_section['id']>0)
    	$this->sklad_iface=getIface($SiteSections->getPath($sklad_section['id']));
   	
    
    	$tmp_order=msr(msq("SELECT * FROM `".$this->getSetting('table_order_tmp')."` WHERE `id`='$tmp_order_id' LIMIT 1"));
   		
   		if (!$tmp_order['id']>0) 
   		{
   			$this->GetTmpOrder(true, true);
   			return array('error'=>'Ошибка. Не найден заказ tmp_order_id. Обновите страницу и попробуйте снова добавить товар');
   		}
   		
   		$good=$this->GetGood($good_id);
   		if (!$good['id']>0) return array('error'=>'Ошибка. Не найден товар good_id');
   		if (!$good['price']>0) return array('error'=>'Ошибка. Не найден параметр товара price');
   		
   		if ($size_id>0) $usl.=' and `size_id`='.$size_id;
   		if ($color_id>0) $usl.=' and `color_id`='.$color_id;
   		
   		$cur_kol=msr(msq("SELECT * FROM `".$this->getSetting('table_order_tmp_goods')."` WHERE `tmp_order_id`='$tmp_order_id' and `good_id`='$good_id'".$usl));
   		
   		
   		if ($this->sklad_iface && floor($kol)>0)
   		{

   			
   			$basket_kol=msr(msq("SELECT sum(kol) as kol FROM `".$this->getSetting('table_order_tmp_goods')."` WHERE `tmp_order_id`='$tmp_order_id' and `good_id`='$good_id'".$usl));
   			
   			$free_kol=$this->sklad_iface->getFreeSkladKol(array('good_id'=>$good['id'], 'size_id'=>$size_id, 'color_id'=>$color_id));
   			
   			if (!$free_kol>0) $error='Товар отсуствует на складе. Обновите страницу.';
   			
   			if ($basket_kol['kol']>0 && $basket_kol['kol']>=$free_kol && $kol>$basket_kol['kol']) 
   			$error='В вашей корзине: '.$basket_kol['kol'].' шт. этого товара, это все количество товара имеющееся на складе';
   		}
   		
   		if ($error!='') return array('error'=>$error);
   		

   		$summ=0;
   		if ($kol>0) $summ=$kol*$good['price'];
   		
  		if (floor($kol)==0){
  			msq("DELETE FROM `".$this->getSetting('table_order_tmp_goods')."` WHERE `id`=".$cur_kol['id']);
  		}
  		elseif ($cur_kol['id']>0)
  		{
  			msq("UPDATE `".$this->getSetting('table_order_tmp_goods')."` SET `kol`='$kol', `price`='".$good['price']."', `summ`='$summ' WHERE `id`=".$cur_kol['id']);
  		}
  		else
  		{
  			msq("INSERT INTO `".$this->getSetting('table_order_tmp_goods')."` 
  			(`tmp_order_id`, `good_id`, `kol`, `price`, `summ` ".($size_id>0 ? ",`size_id`":'').($color_id>0 ? ",`color_id`":'').") 
  			VALUES ('$tmp_order_id', '$good_id', '$kol', '".$good['price']."', '$summ'".($size_id>0 ? ",'".floor($size_id)."'":"").($color_id>0 ? ",'".floor($color_id)."'":'').")");
  		}
  		
   }
   function AddGood_saved($g){
 		global $SiteSections;
   		
   		$good=$this->goods_iface->getPub($g['good_id']);
   		
   		if ($good['id']>0)
   		msq("INSERT INTO `site_site_order_goods`
   		(`order_id`, `good_id`, `kol`, `price`, `summ`, `size_id`, `color_id`) 
   		VALUES('".$_POST['order_id']."','".$good['id']."','1','".$good['price']."','".$good['price']."', '".$g['size_id']."', '".$g['color_id']."')");
   		
   }
   function GetTmpOrder ($use_cookie=true, $create_new=false){
   		if ($_SESSION['tmp_order_num']>0 && !$create_new) 		return $_SESSION['tmp_order_num'];
   		elseif ((cookieGet('tmp_order_num')>0 && $use_cookie) && !$create_new) 	return cookieGet('tmp_order_num');
   		else
   		{
   			$q = msr(msq("SELECT max(id) as num FROM `".$this->getSetting('table_order_tmp')."`"));
   			msq("INSERT INTO `".$this->getSetting('table_order_tmp')."` (`id`, `date`) VALUES ('".(floor($q['num'])+1)."', NOW())");
   			$_SESSION['tmp_order_num']=floor($q['num'])+1;
   			cookieSet('tmp_order_num', floor($q['num'])+1,180);
   			
   			return floor($q['num'])+1;
   		}
   }
   function ShowReport()
   {
   		global $SiteSections, $MySqlObject;
   	
   		$dop_nav='&rarr; Отчет';
   		?>
   		 <script type="text/javascript">
							$(function(){

								$.datepicker.regional['ru'] =
								{
									closeText: 'Закрыть',
									prevText: '&#x3c;Пред',
									nextText: 'След&#x3e;',
									currentText: 'Сегодня',
									monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
									'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
									monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
									'Июл','Авг','Сен','Окт','Ноя','Дек'],
									dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
									dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
									dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
									dateFormat: 'dd.mm.yy',
									firstDay: 1,
									isRTL: false
								};

								$.datepicker.setDefaults($.extend($.datepicker.regional["ru"]));

									$("[name=search_date_add_from]").datepicker({
									showOn: "both",
									buttonImage: "/pics/editor/calendar.gif",
									buttonImageOnly: true
								});
								$.datepicker.setDefaults($.extend($.datepicker.regional["ru"]));

									$("[name=search_date_add_to]").datepicker({
									showOn: "both",
									buttonImage: "/pics/editor/calendar.gif",
									buttonImageOnly: true
								});

							});
		</script>
							
		<?
		$max_order_date=msr(msq("SELECT max(`date`) as dt FROM `".$this->getSetting('table')."` WHERE `status_id`=3"));
		$max_order_date['dt']=$MySqlObject->dateFromDBDot($max_order_date['dt']);
		
		$min_order_date=msr(msq("SELECT min(`date`) as dt FROM `".$this->getSetting('table')."` WHERE `status_id`=3"));
		
		$min_order_date['dt']=$MySqlObject->dateFromDBDot($min_order_date['dt']);
		
		if (isset($_POST['search_date_add_from'])) 	$min_order_date['dt']=$_POST['search_date_add_from'];
		if (isset($_POST['search_date_add_to'])) 	$max_order_date['dt']=$_POST['search_date_add_to'];
		
		?>					
   		<div id="content" class="forms">
   			<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
   			   	<form name="searchform" action="" method="POST">
			   		<div style="z-index: 11; width: 158px;" id="date_calendar" class="place">
						<label>Дата c</label>
						<div><input type="text" name="search_date_add_from" value="<?=$min_order_date['dt']?>" style="width: 100px; float: left;"></div>
					</div>
					<div style="z-index: 11; width: 158px;" id="date_calendar" class="place">
						<label>Дата по</label>
						<div><input type="text" name="search_date_add_to" value="<?=$max_order_date['dt']?>" style="width: 100px; float: left;"></div>
					</div>
					
				<?
				$values=array('0'=>'по дням', '1'=>'по неделям', '2'=>'по месяцам');
				?>
				<div class="place" style="z-index: 10; width: 15%;">
					<label>Группировать</label>
					<?print getSelectSinonim('search_group',$values,$_REQUEST['search_group']);?>
				</div>
				
				<?
				$values=array('0'=>'нет', '1'=>'да');
				?>
				<div class="place" style="z-index: 10; width: 15%;">
					<label>Выводить нули</label>
					<?print getSelectSinonim('show_null',$values,$_REQUEST['show_null']);?>
				</div>

   					 <div class="place" style="width: 8%;margin-left: 2%;">
   						<label>&nbsp;</label>
   						<span class="forbutton">
   							<span>
   								<input class="button" type="submit" value="Сформировать отчет" >
   							</span>
   						</span>
   					</div>
   					
   					<input type="hidden" name="action_report">
   		        </form>
   		        <div class="hr"><hr/></div>
   		        
   		        
				<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
				<script src="https://www.amcharts.com/lib/3/serial.js"></script>
				<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
				<style>
				#chartdiv {
					width	: 100%;
					height	: 700px;
				}
				</style>
   		        <?
   		        if (isset ($_POST['action_report']))
   		        {
					
   		        	$date_diff=(strtotime($max_order_date['dt']) - strtotime($min_order_date['dt']))/3600/24;
   		        	
   		        	switch ($_POST['search_group']) {
					    case '0':
					        ?>
					        <h2>Группировка по дням:</h2>
					        <?
					        if ($date_diff>0)
					        {
					        	$tab_td=array(); $chart=array();
					        	?>
					        	<table class="table-content stat">
					        	<tr>
					        		<th>Дата</th>
					        		<th>Кол. заказов</th>
					        		<th>Сумма</th>
					        	</tr>
					        	<?
					        	for ($i = 1; $i <= $date_diff; $i++) 
					        	{
					        		$cur_date = strtotime("+".$i." day", strtotime($min_order_date['dt']));
					        		$res=msr(msq("SELECT count(*) as cnt, sum(`summ`) as summ, date(`date`) as dt FROM `".$this->getSetting('table')."` WHERE `status_id`=3 and date(`date`)='".date('Y-m-d',$cur_date)."'"));
					        		
					        		if ($res['summ']>0 || $_POST['show_null']=='1')
					        		{
					        			
					        			$tab_td[]='					        			
                						<tr>
					        				<td>'.date('d.m.Y',$cur_date).'</td>
					        				<td>'.floor($res['cnt']).'</td>
					        				<td>'.number_format(floor($res['summ']) , 0, ' ', ' ').'<small>руб</small></td>
					        			</tr>';	
					        			
					        			$chart[]='{"date": "'.date('d.m.Y',$cur_date).'","value": '.floor($res['summ']).', "cnt": '.floor($res['cnt']).'}, ';
					        		}
					        	}
					        	
					        	$tab_td=array_reverse($tab_td);
					        	foreach ($tab_td as $td)
					        	print $td;
					        	?>
					        	</table>
					        	<div id="chartdiv"></div>
					        	<script type="text/javascript"> 
					        	AmCharts.shortMonthNames = ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'];
					        	AmCharts.monthNames = ['Январь','Февраль','Март','Апрель','Май','Июнь', 'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
					        	
					        	var chart = AmCharts.makeChart("chartdiv", {
					        	    "type": "serial",
					        	    "theme": "light",
					        	    "marginRight": 40,
					        	    "marginLeft": 40,
					        	    "marginTop": 150,
					        	    "autoMarginOffset": 20,
					        	    "mouseWheelZoomEnabled":true,
					        	    "dataDateFormat": "DD-MM-YYYY",
					        	    "valueAxes": [{
					        	        "id": "v1",
					        	        "axisAlpha": 0,
					        	        "position": "left",
					        	        "ignoreAxisWidth":true
					        	    }],
					        	    "balloon": {
					        	        "borderThickness": 1,
					        	        "shadowAlpha": 0
					        	    },
					        	    "graphs": [{
					        	        "id": "g1",
					        	        "balloon":{
					        	          "drop":true,
					        	          "adjustBorderColor":false,
					        	          "color":"#ffffff"
					        	        },
					        	        "bullet": "round",
					        	        "bulletBorderAlpha": 1,
					        	        "bulletColor": "#FFFFFF",
					        	        "bulletSize": 5,
					        	        "hideBulletsCount": 50,
					        	        "lineThickness": 2,
					        	        "title": "red line",
					        	        "useLineColorForBulletBorder": true,
					        	        "valueField": "value",
					        	        "balloonText": "<span style='font-size:18px;'>[[value]]руб <br/> (заказы: [[cnt]])</span>"
					        	    }],
					        	    "chartScrollbar": {
					        	        "graph": "g1",
					        	        "oppositeAxis":false,
					        	        "offset":30,
					        	        "scrollbarHeight": 80,
					        	        "backgroundAlpha": 0,
					        	        "selectedBackgroundAlpha": 0.1,
					        	        "selectedBackgroundColor": "#888888",
					        	        "graphFillAlpha": 0,
					        	        "graphLineAlpha": 0.5,
					        	        "selectedGraphFillAlpha": 0,
					        	        "selectedGraphLineAlpha": 1,
					        	        "autoGridCount":true,
					        	        "color":"#AAAAAA"
					        	    },
					        	    "chartCursor": {
					        	        "pan": true,
					        	        "valueLineEnabled": true,
					        	        "valueLineBalloonEnabled": true,
					        	        "cursorAlpha":1,
					        	        "cursorColor":"#258cbb",
					        	        "limitToGraph":"g1",
					        	        "valueLineAlpha":0.2,
					        	        "valueZoomable":true
					        	    },
					        	    "valueScrollbar":{
					        	      "oppositeAxis":false,
					        	      "offset":50,
					        	      "scrollbarHeight":10
					        	    },
					        	    "categoryField": "date",
					        	    "categoryAxis": {
					        	        "parseDates": true,
					        	        "dateFormats": [{period:'DD',format:'DD MMMM'},{period:'MM',format:'DD MMMM'}, {period:'YYYY',format:'DD MMMM'}],
					        	        "dashLength": 1,
					        	        "minorGridEnabled": true
					        	    },
					        	    "export": {
					        	        "enabled": true
					        	    },
					        	    "dataProvider": [
									<?
									foreach ($chart as $ch) {
										print $ch;
									}
									?>
									]
					        	});

					        	chart.addListener("rendered", zoomChart);

					        	zoomChart();

					        	function zoomChart() {
					        	    chart.zoomToIndexes(chart.dataProvider.length - 40, chart.dataProvider.length - 1);
					      
					        	   
					        	}
					        	</script> 
					        	<?
					        }
					        
					        break;
					    case '1':
   		        	 		?>
					        <h2>Группировка по неделям:</h2>
					        <?

					        if ($date_diff>0)
					        {
					        	$tab_td=array(); $chart=array();
					        	?>
					        	<table class="table-content stat">
					        	<tr>
					        		<th>Дата</th>
					        		<th>Кол. заказов</th>
					        		<th>Сумма</th>
					        	</tr>
					        	<?
					        	$j=0;
					        	for ($i = 1; $i <= $date_diff; $i++) 
					        	{
					        		$cur_date = strtotime("+".$i." day", strtotime($min_order_date['dt']));
					        		
					        		$start_week_day=date('w', $cur_date);
					        		if ($start_week_day>1)
					        		{
					        			$need_start_weekday = strtotime("-".($start_week_day-1)." day", $cur_date);
					        		}
					        		else $need_start_weekday=$cur_date;
					        		 
					        		$need_end_weekday=strtotime("+6 day", $need_start_weekday);
					        		
					        		$j++;
					        		if ($j%7==1) 
					        		{
						        		$res=msr(msq("SELECT count(*) as cnt, sum(`summ`) as summ, date(`date`) as dt FROM `site_site_basket_basket_9` WHERE `status_id`=3 and date(`date`)>='".date('Y-m-d',$need_start_weekday)."'  and date(`date`)<='".date('Y-m-d',$need_end_weekday)."'"));	
						        		if ($res['summ']>0 || $_POST['show_null']=='1')
						        		{
						        			
						        			$tab_td[]='
                							<tr>
						        				<td>'.date('d.m.Y',$need_start_weekday).'-'.date('d.m.Y',$need_end_weekday).'</td>
						        				<td>'.floor($res['cnt']).'</td>
						        				<td>'.number_format(floor($res['summ']) , 0, ' ', ' ').'<small>руб</small></td>
						        			</tr>';
						        			
						        			$chart[]='{"cap": "'.date('d.m.Y',$need_start_weekday).'-'.date('d.m.Y',$need_end_weekday).'","value": '.floor($res['summ']).', "cnt" : '.floor($res['cnt']).'}, ';
						        		}
					        		}
					        		
					        		
					        		
					        	}	
					        	
					        	$tab_td=array_reverse($tab_td);
					        	foreach ($tab_td as $td)
					        	print $td;
					        	?>
					        	</table>
					        	<div id="chartdiv"></div>
					        	<script type="text/javascript"> 
					        	var chart = AmCharts.makeChart("chartdiv", {
					        	    "theme": "light",
					        	    "type": "serial",
					        	    "marginTop": 50,
					        	    "dataProvider": [
									<?
									foreach ($chart as $ch) 
									{
										print $ch;
									}
									?>
									],
					        	    "startDuration": 1,
					        	    "graphs": [{
					        	        "balloonText": "[[value]]руб<br/> (заказы: [[cnt]])",
					        	        "fillAlphas": 0.9,
					        	        "lineAlpha": 0.2,
					        	        "title": "[[cap]]",
					        	        "type": "column",
					        	        "valueField": "value"
					        	    }],
					        	    "plotAreaFillAlphas": 0.1,
					        	    "depth3D": 60,
					        	    "angle": 30,
					        	    "categoryField": "cap",
					        	    "categoryAxis": {
					        	        "gridPosition": "start"
					        	    },
					        	    "export": {
					        	    	"enabled": true
					        	     }
					        	});
					        	jQuery('.chart-input').off().on('input change',function() {
					        		var property	= jQuery(this).data('property');
					        		var target		= chart;
					        		chart.startDuration = 0;

					        		if ( property == 'topRadius') {
					        			target = chart.graphs[0];
					        	      	if ( this.value == 0 ) {
					        	          this.value = undefined;
					        	      	}
					        		}

					        		target[property] = this.value;
					        		chart.validateNow();
					        	});
					        	</script>
					        	
					        	<?
					        }
					        break;
					    	case '2':
					        ?>
					        <h2>Группировка по месяцам:</h2>
					        <?

					        if ($date_diff>0)
					        {
					        	$tab_td=array();
					        	?>
					        	<table class="table-content stat">
					        	<tr>
					        		<th>Дата</th>
					        		<th>Кол. заказов</th>
					        		<th>Сумма</th>
					        	</tr>
					        	<?
					        	$j=0;
					        	for ($i = 1; $i <= $date_diff; $i++) 
					        	{
					        		
					        		
					        		$cur_date = strtotime("+".$i." day", strtotime($min_order_date['dt']));
					        		
					        		$start_day=date('j', $cur_date);
					        		
					        		
					        		if ($start_day>1)
					        		{
					        			$need_start = strtotime("-".($start_day-1)." day", $cur_date);
					        		}
					        		else $need_start=strtotime($need_start);
					        		
					        		
					        		$need_end=strtotime("+1 month", $need_start);
					        		/* Переход на следующий месяц */
					        		if ($need_end>$last_need_end) $j=1;
					        		
					        		if ($j==1) 
					        		{
						        		$res=msr(msq("SELECT count(*) as cnt, sum(`summ`) as summ, date(`date`) as dt FROM `site_site_basket_basket_9` WHERE `status_id`=3 and date(`date`)>='".date('Y-m-d',$need_start)."'  and date(`date`)<'".date('Y-m-d',$need_end)."'"));
						        		
						        		if ($res['summ']>0 || $_POST['show_null']=='1')
						        		{
						        			$tab_td[]=
						        			'<tr>
						        				<td>'.getMonthRusNameLower(date('m',$need_start)).' '.date('Y',$need_start).'</td>
						        				<td>'.floor($res['cnt']).'</td>
						        				<td>'.number_format(floor($res['summ']) , 0, ' ', ' ').'<small>руб</small></td>
						        			</tr>';	
						        			
						        			$chart[]='{"cap": "'.getMonthRusNameLower(date('m',$need_start)).' '.date('Y',$need_start).'","value": '.floor($res['summ']).', "cnt" : '.floor($res['cnt']).'}, ';
						        		}
					        		}
					        	
					        		$last_need_end=$need_end;
					        		$j++;
					        		
					        		
					        	}
					        	
					        	$tab_td=array_reverse($tab_td);
					        	foreach ($tab_td as $td)
					        	print $td;
					        	?>
					        	</table>
					        	<div id="chartdiv"></div>
					        	<script type="text/javascript"> 
					        	var chart = AmCharts.makeChart("chartdiv", {
					        	    "theme": "light",
					        	    "type": "serial",
					        	    "marginTop": 50,
					        	    "dataProvider": [
									<?
									foreach ($chart as $ch) 
									{
										print $ch;
									}
									?>
									],
					        	    "startDuration": 1,
					        	    "graphs": [{
					        	        "balloonText": "[[value]]руб<br/> (заказы: [[cnt]])",
					        	        "fillAlphas": 0.9,
					        	        "lineAlpha": 0.2,
					        	        "title": "[[cap]]",
					        	        "type": "column",
					        	        "valueField": "value"
					        	    }],
					        	    "plotAreaFillAlphas": 0.1,
					        	    "depth3D": 60,
					        	    "angle": 30,
					        	    "categoryField": "cap",
					        	    "categoryAxis": {
					        	        "gridPosition": "start"
					        	    },
					        	    "export": {
					        	    	"enabled": true
					        	     }
					        	});
					        	jQuery('.chart-input').off().on('input change',function() {
					        		var property	= jQuery(this).data('property');
					        		var target		= chart;
					        		chart.startDuration = 0;

					        		if ( property == 'topRadius') {
					        			target = chart.graphs[0];
					        	      	if ( this.value == 0 ) {
					        	          this.value = undefined;
					        	      	}
					        		}

					        		target[property] = this.value;
					        		chart.validateNow();
					        	});
					        	</script>
					        	<?
					        }
					        break;
					}
					

   		        }
   		        ?>
   		</div>
   		<?
   }
   function start(){
   	global $CDDataSet,$SiteSections;
   	
   	$sklad_section=$SiteSections->getByPattern('PSklad');
   	if ($sklad_section['id']>0)
    $this->sklad_iface=getIface($SiteSections->getPath($sklad_section['id']));
   	
    if ($_GET['del_good']>0)
    {
    	$this->GoodsEditDelete();
    }
    
    if (isset($this->sklad_iface))	$this->GoodsEditUpdateOrderSumm();
   		
  
    
    $this->GoodsEditKol();
   	$dataset = $CDDataSet->get($this->getSetting('dataset'));
   	$imagestorage = $this->getSetting('imagestorage');
   	
   	if ($_GET['type']=='report')
   	{
   		$this->ShowReport();
   		return;
   	}
   
   	if ($_GET['pub']>0)	$pub = $this->getPub(floor($_GET['pub']));
   	
   		foreach ($dataset['types'] as $k=>$dt){
   
   			if ($dt['type']=='CDTextEditor')
   				$this->editor_cnt++;
   				$tface = new $dt['type'];
   				$tface->init(array('name'=>$dt['name'],'value'=>$pub[$dt['name']], 'uid'=>floor($pub['id']),'imagestorage'=>floor($imagestorage['id']),'description'=>$dt['description'],'imagestorage'=>floor($imagestorage['id']),'theme'=>$dataset['name'].'_'.$this->getSetting('section'), 'theme'=>$dataset['name'].'_'.$this->getSetting('section'),'rubric'=>$dt['name'],'settings'=>$dt['settings']));
   				$dataset['types'][$k]['face'] = $tface;
    	}
   		
   
   
   		if (isset($_GET['pub'])){
   
   			$this->setSetting('dataface',$dataset);
   			if (floor($_POST['editformpost'])==1){
   				$this->setSetting('saveerrors',$this->save());
   				if (count($this->getSetting('saveerrors'))==0){
   					unset($_GET['pub']);
   					$this->start();
   					return;
   				}
   			}
   			if (!isset($_POST['searchaction']))
   				$this->drawAddEdit();
   				else $this->drawPubsList();
   		}
   		else{
   			if (floor($_GET['delete'])>0) $this->deletePub($_GET['delete']);
   			$this->setSetting('dataface',$dataset);
   			$this->drawPubsList();
   		}
   }
   function ajax()
   {
   		global $SiteSections;
   		$this->init();
   		$errors=array();
   		
   		$this_section=$SiteSections->getByPattern('PBasket');
   		$this_iface=getIface($SiteSections->getPath($this_section['id']));
   		

   		
   		if ($_POST['action']=='ajax_add_good')
   		{
   			$errors='';
   			
   			$param['good_id']=$_POST['good_id'];
   			
   			foreach ($_POST as $k=>$v)
   			if (!in_array($k, array('good_id', 'action', 'order_id')))
   			$param[$k]=$v;
   			
   			$sklad_section=$SiteSections->getByPattern('PSklad');
   			if ($sklad_section['id']>0)
   			{
   				$this->sklad_iface=getIface($SiteSections->getPath($sklad_section['id']));
   				$free_kol=$this->sklad_iface->getFreeSkladKol($param);
   				
   				/* $free_kol=0; */
   				
   				if (floor($free_kol)<=0)
   				$errors='Невозможно добавить товар. Товара нет на складе';
   			}
   			
   			if ($errors=='') 
   			{
   				$this->AddGood_saved($param);
   				print json_encode(array('ok'=>'ok'));
   			}
   			else
   			print json_encode(array('errors'=>$errors));
   			
   		}
   		
   		
   		
   		
   		if ($_POST['order_id']>0 && $_POST['action']!='ajax_add_good')
   		{
	   		
   			/* 	Accept */
   			$order=msr(msq("SELECT * FROM `".$this_iface->getSetting('table')."` WHERE id=".$_POST['order_id']));

	   		if ($order['accept']!=1)
	   		{
	   			msq("UPDATE `".$this_iface->getSetting('table')."` SET `date_accept`=NOW(), `accept`=1 WHERE id=".$_POST['order_id']);

	   		}
   			
   			$sklad_section=$SiteSections->getByPattern('PSklad');
	 						
	 		if ($sklad_section['id']>0)
	 		{
	 			$this->sklad_iface=getIface($SiteSections->getPath($sklad_section['id']));
	 			
	 			$order_items=msq("SELECT * FROM `site_site_order_goods` WHERE order_id=".$_POST['order_id']);
	 			
	 			while ($oi=msr($order_items))
	 			{
	 				$this->sklad_iface->updateSklad($oi['good_id']);
	 				
	 				if ($order['status_id']==4)
	 				{
		 				$free_kol=$this->sklad_iface->getFreeSkladKol($oi);
		 				if ($free_kol<$oi['kol'])
		 				{
		 					$good=$this->goods_iface->getPub($oi['good_id']);
		 					
		 					if ($_POST['status']!=4) 
		 					$errors[]=' - '.$good['name'].' '.$oi['kol'].'шт (на складе: '.$free_kol.'шт)';
	   					}
	 				}
	 			}
	 			
	 			if (count($errors)==0 || $_POST['status']==4)
	 			msq("UPDATE `".$this_iface->getSetting('table')."` SET `status_id`=".floor($_POST['status'])." WHERE id=".$_POST['order_id']);
	 			
	 			if (count($errors)>0 && $_POST['status']!=4) 
	 			msq("UPDATE `".$this_iface->getSetting('table')."` SET `status_id`=4 WHERE id=".$_POST['order_id']);

	 			print json_encode(array('errors'=>implode('
', $errors)));
	 		}
	 		else 
			msq("UPDATE `".$this_iface->getSetting('table')."` SET `status_id`=".floor($_POST['status'])." WHERE id=".$_POST['order_id']);
	 		
   		}
   }
   function drawPubsList($param=''){
   	global $SiteSections, $CDDataSet, $CDDataType, $VisitorType, $mode, $MySqlObject;
   	
   	$accessgranted = $VisitorType->isAccessGranted($group['id'],$_GET['section']);
   
   	$this->generateMeta('name');
   
   	$dataset = $this->getSetting('dataface');
   
   	$section = $SiteSections->get($this->getSetting('section'));
   
   	if (isset($_POST['showsave'])){
   		foreach ($_POST as $k=>$v){
   			if (preg_match('|^prec\_[0-9]+$|',$k)){
   				$p = preg_replace('|^prec\_([0-9]+)$|','\\1',$k);
   				msq("UPDATE `".$this->getSetting('table')."` SET `precedence`='".floor($_POST['prec_'.$p])."' WHERE `id`='$p'");
   			}
   		}
   		$this->updatePrecedence();
   	}
   	
   	$delivery_types=getSprValues('/sitecontent/basket/delivery_type/','',false);
   	?>
   		    	<div id="content" class="forms">
   		    	<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
   		        <form name="searchform" action="" method="POST">
   		        	<input type="hidden" name="searchaction">
   					<?
   					$search_fields_cnt=0;
   					?>
   					<!-- Влючен\Отключен -->
   					<?if (isset($this->Settings['settings_personal']['onoff'])){?>
   					<div class="place" style="z-index: 10; width: 10%;">
   						<label>Включен</label>
   						<?
   						if (!isset($this->search_show)) $this->search_show='-1';
   						$vals=array('-1'=>'','0'=>'Отключен', '1'=>'Включен');
   						print getSelectSinonim('search_show',$vals,$_POST['search_show'],true);
   
   						$search_fields_cnt++;
   						?>
   					</div>
   					<?}?>
   
   					<!-- Поля для поиска -->
   					<?
   					$search_fields=array();
   					foreach ($dataset['types'] as $dt)
   					{
   						if (isset($dt['settings']['show_search']) && !isset($dt['settings']['off']))
   						{
   							$search_fields[]=$dt['name'];
   							$search_fields_cnt++;
   						}
   					}
   
   					foreach ($search_fields as $sf){
   						$CDDataType->get_search_field($dataset['types'][$sf],$search_fields_cnt);
   					}
   
   					if ($search_fields_cnt>0)
   					{
   					?>
   					 <div class="place" style="width: 8%;margin-left: 2%;">
   						<label>&nbsp;</label>
   						<span class="forbutton">
   							<span>
   								<input class="button" type="submit" value="Найти" >
   							</span>
   						</span>
   					</div>
   					<span class="clear"></span>
   					<?
   					}
   					
   					if ($this->report)
   					if ($accessgranted || in_array('report',$group['new_settings'][$_GET['section']]))
   					{
   						?>
   					   					<div class="clear"></div>
   					   					<br/>
   										<a class="button" href="/manage/control/contents/?section=<?=$section['id'] ?>&type=report"><img src="/pics/editor/statistic_white.png">История продаж</a>
   					<?} ?>
  
   				</form>
   				<div class="hr"><hr/></div>
   		                        <?
   		                        $list = $this->getList($_GET['page']);
   		                        if (count($list)==0){
   		                                ?>
   		                                <p>Отсутствуют записи, удовлетворяющие заданным условиям</p>
   		                                <span class="clear"></span>
   		                                <div class="place">
   		                                	<a href="./?section=<?=$section['id']?>&pub=new" class="button big" style="float: right;">Добавить</a>
   		                                </div>
   		                                <?
   		                        }
   		                        else{
   		                         print 'Всего записей: '.$this->getSetting('count');
   		                         $Storage = new Storage;
   		                         $Storage ->init();
   		                                ?>
   		                                <form id="showsave" class="showsave" name="showsave" action="./?section=<?=$section['id']?><?=($this->getSetting('page')>1)?'&page='.$this->getSetting('page'):''?>" method="POST">
   		                                        <?
   		                                        /* Поля отображаемые в таблице */
   		                                        $show_fields=array();
   
   		                                        foreach ($dataset['types'] as $dt)
   		                                        {
   		                                        	if (isset($dt['settings']['show_list']) && !isset($dt['settings']['off']))
   		                                        	$show_fields[]=$dt['name'];
   		                                        }
   
   
   
   
   		                                        ?>
   		<script>
   		var session_id = '<?php echo session_id(); ?>';
   		$(function() {
   	   		
   		    $(document).on('click','.onoff', function() {
   		        var id=$(this).attr("data-id");
   		        var elem=$(this);
   				if (id>0)
   				{
   					$.ajax({
   			            type: "POST",
   			            url: "/inc/site_admin/pattern/ajax_class.php",
   			            data: "action=onoff&id="+id+"&table=<?=$this->getSetting('table')?>&session_id="+session_id,
   			            dataType: 'json',
   			            success: function(data){
   			            	elem.children('img').attr('src', '/pics/editor/'+data.signal);
   			            }
   			        });
   				}
   
   		        return false;
   		    });

   		    
   		});
   		</script>
   		<table class="table-content stat">
   		<tr class="template">
   		<?
   		$table_th=array();
   
   		if (isset($this->Settings['settings_personal']['onoff'])) 		$table_th[]=array('name'=>'show', 'description'=>'Вкл', 'class'=>'t_minwidth t_center');
   		if (isset($this->Settings['settings_personal']['show_id']))		$table_th[]=array('name'=>'id', 'description'=>'№', 'class'=>'t_minwidth  t_center');
   		if (isset($this->Settings['settings_personal']['precedence']))	$table_th[]=array('name'=>'precedence', 'description'=>'Порядок', 'class'=>'t_32width');
   		
   		$table_th[]=array('name'=>'', 'description'=>'', 'class'=>'t_minwidth');
   		
   
   		foreach($show_fields as $sf){
   			$set=$dataset['types'][$sf]['face']->Settings;
   			$table_th[]=array('name'=>$set['name'], 'description'=>$set['description'], 'class'=>$set['settings']['list_class']);
   		}
   
   		$table_th[]=array('name'=>'', 'description'=>'Контакты', 'class'=>'');
   
   		/* Редактирование и удаление */
   		$table_th[]=array('name'=>'', 'description'=>'', 'class'=>'t_minwidth');
   		$table_th[]=array('name'=>'', 'description'=>'', 'class'=>'t_minwidth');
   
   
   
   			foreach($table_th as $th){
   				$sort_button='';
   				$active_sort='';
   
   
   				if ($th['name']!='')
   				{
   					$type_sort='down';
   					if (stripos($this->order_by, '`'.$th['name'].'`')!==false)
   					{
   						$active_sort=' active';
   						$type_sort=stripos($this->order_by,'DESC') ? 'down':'up';
   					}
   
   					$sort_button='<a class="sort '.$type_sort.$active_sort.'" href="?section='.$section['id'].(($_GET['page']>1) ? '&page='.$_GET['page']:'').$this->urlstr.'&sort='.$th['name'].'&sort_type='.(($type_sort=='down') ? 'ASC':'DESC').'"></a>';
   				}
   
   
   				?>
   				<th <?=$th['class']!='' ? 'class="'.$th['class'].'"' :'' ?>>
   					<div><div><?=$th['description']?></div><div style="height: 8px;"><?=$sort_button?></div></div>
   				</th>
   				<?
   			}
   
   		?>
   		</tr>
   		<?
   		/* Поля ктр. дублируют ссылку на редактирование */
   		$editlink_double=array('name');
   
   		foreach ($list as $pub)
   		{
   			?>
   		<tr data-id="<?=$pub['id'] ?>">
   
   			<!-- Вкл. Откл -->
   			<?if (isset($this->Settings['settings_personal']['onoff'])){?>
   				<td class="t_minwidth  t_center">
   					<a href="#" onclick="return false;" class="onoff" data-id="<?=$pub['id']?>">
   						<img id="onoff_<?=$pub['id']?>" src="/pics/editor/<?=$pub['show']==0 ? 'off.png' : 'on.png'?>" title="<?=$pub['show']==0 ? 'Отключена' : 'Включена'?>" style="display: inline;">
   					</a>
   				</td>
   			<?}?>
   
   
   			<!-- ID, порядок -->
   			<?if (isset($this->Settings['settings_personal']['show_id'])){?>		<td class="t_minwidth  t_center"><?=$pub['id'] ?></td><?}?>
   			<?if (isset($this->Settings['settings_personal']['precedence'])){?>		<td class="t_32width  t_center"><input type="text" name="prec_<?=$pub['id']?>" value="<?=floor($pub['precedence'])?>"/></td><?}?>
   
   			<td>
   				<div><?=$MySqlObject->dateFromDBDot($pub['date']).' '.$MySqlObject->TimeFromDB($pub['date']) ?></div>
   				
   				<?if ($pub['date_accept']!='0000-00-00 00:00:00' && $pub['date_accept']!='') {?>
   				<div style="padding: 5px 0 0 0;">принят <?=dateDiffComment($pub['date_accept'], $pub['date']); ?>:<br/><?=$MySqlObject->dateFromDBDot($pub['date_accept']).' '.$MySqlObject->TimeFromDB($pub['date_accept']) ?></div>
   				<?} ?>
   			</td>

   			<!-- Видимые поля -->
   			<?
   			foreach($show_fields as $sf)
   			{
   				$set=$dataset['types'][$sf]['face']->Settings;
   				$href=array();
   				if (in_array($sf,$editlink_double) && !isset($set['settings']['editable'])) $href=array('<a href="/manage/control/contents/?section='.$section['id'].'&pub='.$pub['id'].'" title="Редактировать">', '</a>');
   				
   				if ($sf=='summ') $pub[$sf]='<strong>'.number_format($pub[$sf], 0, '.', ' ').'<small> р.</small></strong>';
   				
   				?>
   				<td <?=$set['settings']['list_class']!='' ? 'class="'.$set['settings']['list_class'].'"' : ''?> <?=$set['settings']['list_style']!='' ? 'style="'.$set['settings']['list_style'].'"' : ''?>>
   					<?=$href[0]?><?=$CDDataType->get_view_field($dataset['types'][$sf],$pub[$sf], $pub);?><?=$href[1]?>
   				
   					<?
   					if ($sf=='phone')
   					{
   						$this->PrintDoubleComment($pub);	
   					}
   					
   					if ($sf=='summ')
   					{
   						if ($pub['summ_discount']>0) print '[скидка: '.$pub['summ_discount'].'<small> р.</small>]';
   						if ($pub['paid']==1) print '<h2 style="color: #CC0033">[Заказ оплачен]</h2>';
   						
   						$goods=$this->GetAllOrderItem($pub['id']);
   						if (count($goods)>0)
   						{
	   						?>
	   						<div class="hr" style="margin: 10px 0;"><hr/></div>
   							<table class="table-content stat min">
	   						<?
   							foreach ($goods as $g)
	   						{
								$this->printGoodTd($g);
	   						}
	   						?></table><?
   						}

   						if ($pub['delivery_id']>0)
   						{
   							?>
   							<div>Доставка: <?=strip_tags(html_entity_decode(str_replace('"/','', htmlspecialchars_decode($delivery_types[$pub['delivery_id']], ENT_QUOTES))))?></div>
   							<?	
   						}
   						
   					}
   					?>
   				</td>
   			<?}?>
      			
   			<td>
   				<table class="table-content stat min">
   				<? if ($pub['name']!='') { ?><tr><td style="width: 80px;">Имя:</td><td><?=$pub['name'] ?></td></tr><?} ?>
   				<? if ($pub['phone']!='') { ?><tr><td>Телефон:</td><td><?=$pub['phone'] ?></td></tr><?} ?>
   				<? if ($pub['address']!='') { ?><tr><td>Адрес:</td><td><?=$pub['address'] ?></td></tr><?} ?>
   				<? if ($pub['email']!='') { ?><tr><td>Email:</td><td><?=$pub['email'] ?></td></tr><?} ?>
   				<? if ($pub['comment']!='') { ?><tr><td>Коммент:</td><td><?=$pub['comment'] ?></td></tr><?} ?>	
   				</table>
   				<?
   				$sources=array('1'=>'Панель управления', '2'=>'Сайт');
   				if ($pub['source_id']>0)
   				{
   					?><div class="air p10"></div><div class="left"><i>Источник: <?=$sources[$pub['source_id']] ?></i></div><?
   				}
   				?>	
   			</td>
   
   			<!-- Редактировать, Удалить -->
   			<td class="t_minwidth">
   				<a class="button txtstyle" href="/manage/control/contents/?section=<?=$section['id']?>&pub=<?=$pub['id']?>" title="Редактировать"><img src="/pics/editor/prefs.gif" alt="Редактировать"></a>
   			</td>
   			<td class="t_minwidth">
   				<a href="./?section=<?=$section['id']?>&delete=<?=$pub['id']?>" class="button txtstyle" onclick="if (!confirm('Удалить запись')) return false;">
   				<input type="button" style="background-image: url(/pics/editor/delete.gif)" title="Удалить запись"/>
   				</a>
   			</td>
   		</tr>
   		<?}?>
   		                                        </table>
   		                                        <span class="clear"></span>
   		                                        <div class="place">
   		                                        <?if (isset($this->Settings['settings_personal']['precedence'])){?>
   		                                                <span>
   		                                                	<input class="button big" type="submit" name="showsave" value="Сохранить порядок" />
   		                                                </span>
   		                                        <?} ?>
   		                                       	<a href="./?section=<?=$section['id']?>&pub=new" class="button big" style="float: right;">Добавить</a>
   		                                        </div>
   		                                        <span class="clear"></span>
   		                                </form>
   		                                <span class="clear"></span>
   		                                <?
   		                                $pagescount=$this->getSetting('pagescount');
   		                                if(!$_GET['page']>0) $_GET['page']=1;
   
   		                                if ($pagescount>1 && $_GET['id']==''){
   		                                	?>
   												<div class="hr"><hr/></div>
   												<div id="paging" class="nopad">
   													<?
   													$dif=5;
   
   													$href = '?section='.$section['id'].$this->urlstr;
   													if ($_REQUEST['sort']!='') $href .='&sort='.$_REQUEST['sort'];
   													if ($_REQUEST['sort_type']!='') $href .='&sort_type='.$_REQUEST['sort_type'];
   
   													if ($_GET['page']>$dif/2+1) print '<a href="'.$href.'">В начало</a>';
   
   													for ($i=1; $i<=$pagescount; $i++)
   													{
   														$inner = '';
   					        							$block = array('<a href="'.$href.'&page='.$i.'">','</a>');
   
   
   					        							if (
   					        									($i>($_GET['page']-($dif/2))) && ($i<($_GET['page']+($dif/2)))
   
   					        									|| ($i<=$dif && $_GET['page']<=$dif-$dif/2)
   					        									|| ($i>$pagescount-$dif && $_GET['page']>$pagescount-$dif/2+1)
   
   					        								)
   					        							{
   					        								$inner = $i;
   					        								if ($i==$_GET['page']) $block = array('<span>','</span>');
   					        							}
   
   					        							if ($inner!='') print $block[0].$inner.$block[1];
   													}
   
   													if ($_GET['page']!=$pagescount && $pagescount>1) print '<a href="'.$href."&page=".($_GET['page']+1).'">Следующая</a>';
   				        							if ($_GET['page']<$pagescount && $pagescount>$dif) print '<a href="'.$href."&page=".($_GET['page']+1).'">Последняя</a>';
   													?>
   												</div>
   		                                	<?
   		                                }
   		                        }
   
   		                        if ($param)
   		                        $this->get_txt_export();
   		                        ?>
   		                </div>
   		  			</div>
   		  			
   		  			<script type="text/javascript">
   		  			var session_id = '<?php echo session_id(); ?>';
   		  			
   		  			$(function() {
						function color_select_basket(name)
						{
							var color=$("[name="+name+"] option:selected").attr('data-color');

							if (color!='')
							{
								$("[name="+name+"]").css('background', color);
							}
						}
	   		  			$('.colorselect').unbind('change');

	   		  			var sel=1;
	   		  			
	   		   			$(".colorselect").change(function(e){
		   		   				color_select_basket($(this).attr('name'));

		   		   				var ord_id=$(this).parents('tr').attr('data-id');
		   		   				var status=$(this).val();

		   		   				var elem=$(this);

			   		   			$.ajax({
		   				            type: "POST",
		   				            url: "/inc/cclasses/CCBasket.php",
		   				            data: "action=ajax_basket&do=sklad_order&order_id="+ord_id+'&status='+$(this).val(),
		   				            dataType: 'json',
		   				         	success: function(data)
		   				            {
										if (data.errors!='') 
										{
											elem.val(4);
											elem.css('background', $("[name="+elem.attr('name')+"] option:selected").attr('data-color'));

											alert('Невозможно изменить статус заказа: \r\n'+data.errors);

										}
		   				            }
			   				           
		   				        });
			   		   			  
	   		   			});	
					});
   		  			</script>
   		             <?
   }
   function printGoodTd ($g){
	   	$good_info=$this->GetGood($g['good_id']);
	   		
	   	$categ_info=$this->GetGoodCateg($good_info['categs']);
	   	if ($good_info['name']=='' && $good_info['diam']!='') $good_info['name']='Диаметр: '.$good_info['diam'];
	   	?>
	   		   							<tr>
	   		   								<td>
	   		   									<?if ($categ_info!='') {?><div><small>[<?=$categ_info ?>]</small></div><?} ?>
	   		   									<div><strong><a href="/manage/control/contents/?section=<?=$this->goods_iface->getSetting('section') ?>&pub=<?=$good_info['id'] ?>" target="_blank"><?=$good_info['name'] ?></a></strong></div>
	   		   									<?=$this->getTypeComment($g) ?>
	   		   								</td>
	   		   								<td><nobr><?=$g['kol'] ?> шт.</nobr></td>
	   		   								<td><nobr><?=$g['summ'] ?> р.</nobr></td>
	   		   							</tr>
	   	<?
   }
   function drawEditGoods ($pub){
	   	?>
	   	<a name="goods"></a>
	   	<div class="place" style="border: 1px solid #CCCCCC; padding: 15px; width: 96%; margin: 10px 0;">
	   		<h3>Товары:</h3>
	   	   				
   						<?
   						$goods=$this->GetAllOrderItem($pub['id']);
   						
   						if (!count($goods)>0) print '<h3>В заказе нет товаров</h3>';
   						else 
   						{
	   						?>
	   						<table class="table-content stat"><?
	   						foreach ($goods as $g)
	   						{
	   							$good_info=$this->GetGood($g['good_id']);
	   							?>
	   							<tr>
	   								<td>
	   								<div><strong><?=$good_info['name'] ?></strong></div>
	   								<?=$g['type_comment'] ?>
	   								</td>
	   								<td>
	   									<a class="button" href="/manage/control/contents/?section=<?=$_GET['section'] ?>&pub=<?=$_GET['pub'] ?>&item=<?=$g['id'] ?>&editkol=true&type=minus"><img src="/pics/editor/minus_white.png" style="padding: 0;"></a>
	   									<nobr><?=$g['kol'] ?> шт.</nobr>
	   									<?
	   									if (isset($this->sklad_iface))
	   									$free_kol=$this->sklad_iface->getFreeSkladKol($g, $_GET['pub']);
	   									else $free_kol=1; 
	   									?>
	   									<a class="button<?=(!$free_kol>0) ? ' disabled' :'' ?>" href="/manage/control/contents/?section=<?=$_GET['section'] ?>&pub=<?=$_GET['pub'] ?>&item=<?=$g['id'] ?>&editkol=true&type=plus"><img src="/pics/editor/plus_white.png" style="padding: 0;"></a>
	   								</td>
	   								<td><nobr><?=$g['summ'] ?> р.</nobr></td>
	   								<td class="t_minwidth">
	   									<span class="button txtstyle">
			                            	<a title="Удалить" onclick="if (!confirm('Удалить запись')) return false;" href="/manage/control/contents/?section=<?=$_GET['section'] ?>&pub=<?=$_GET['pub'] ?>&del_good=<?=$g['id'] ?>"><img alt="Удалить" src="/pics/editor/delete.gif"></a>
			                            </span>
	   								</td>
	   							</tr>
	   							<?
	   						}
	   						?></table><?
   						}
   						?>
   						   						
   					<div style="float: right;">
   						<div class="air p10"></div>
   						<a class="button" href="#" onclick="show_modal_my('#popup_add'); return false;"><img src="/pics/editor/plus_white.png">Добавить</a>
   					</div>	
	   	</div>
	   	<?
   }
   function GoodsEditDelete ()
   {
   		if ($_GET['del_good']>0 && $_GET['pub']>0)
   		msq("DELETE FROM `".$this->getSetting('table_order_goods')."` WHERE `id`=".$_GET['del_good']." and `order_id`=".$_GET['pub']);

   }
   function GoodsEditKol(){
   	

   		if (isset($_GET['editkol']))
   		{
   			$good=$this->GetGoodBasket_saved($_GET['pub'], $_GET['item']);
   			
   			if (!$good['id']>0) return;
   			
   			if ($good['kol']<=1 && $_GET['type']=='minus') msq("DELETE FROM `".$this->getSetting('table_order_goods')."` WHERE `id`=".$good['id']);
   			else 
   			{
   				$new_kol=floor($good['kol']);
   				
   				if ($_GET['type']=='plus')
   				{
   					$new_kol++;
   					
   					/* Склад - проверка доступного количества */
   					if (isset($this->sklad_iface))
   					{
   						$free_kol=$this->sklad_iface->getFreeSkladKol($good, $_GET['pub']);		
   					}
   				}
   				if ($_GET['type']=='minus') $new_kol--;
   				
   				$new_summ=floor($good['price']*$new_kol);
   				
   				msq("UPDATE `".$this->getSetting('table_order_goods')."` SET `summ`=".$new_summ.", `kol`=".$new_kol." WHERE `id`=".$good['id']);
   				

   			}
   			
   			$this->GoodsEditUpdateOrderSumm();
   			?>
   			<script type="text/javascript">
   			$(function() {
   				window.location.href = "/manage/control/contents/?section=<?=$_GET['section'] ?>&pub=<?=$_GET['pub'] ?>";
			});
   			</script>
   			<?
   		}
   	
   }
   function GoodsEditUpdateOrderSumm($pub){
   		global $SiteSections;
   		
   		if (!$pub['id']>0) $pub['id']=$_GET['pub'];
   		
   		$order=$this->GetSaveOrder($pub['id']);

   		$total=$this->GetTotalBasket_saved($pub['id']);
   		$total['summ']=$new_summ=floor($total['summ']);

		if ($order['discount_id']>0)
		{
   		 		$section_discount=$SiteSections->get($SiteSections->getIdByPath('/sitecontent/basket/discount/'));
 				if ($section_discount['id']>0)
 				$discount_iface=getIface($SiteSections->getPath($section_discount['id']));
 				else print '<h2>Не найден раздел с скидками /sitecontent/basket/discount/</h2>';
 				
 				$discount=$discount_iface->getPub($order['discount_id']);
 				$discount['procent']=floor($discount['procent']);
 				
 				$new_summ=floor(($new_summ/100)*(100-$discount['procent']));
 				
 				$summ_discount=floor($total['summ']-$new_summ);
 				
		}
   		
   		if ($pub['id']>0)
   		msq("UPDATE `".$this->getSetting('table')."` SET `summ`='".$new_summ."', `summ_discount`=".floor($summ_discount).", `summ_clear`='".$total['summ']."' WHERE id=".$pub['id']);
   		
   }
   function getCategs(){
	   	$values=array();
	   	$parents=msq("SELECT * FROM `".$this->categ_iface->getSetting('table')."` WHERE `show`=1 and `parent_id`=0 ORDER BY `precedence`");
	   	
	   	while($r=msr($parents))
	   	{
	   		$values[]=array('level'=>0, 'id'=>$r['id'], 'name'=>$r['name']);
	   		$childs=msq("SELECT * FROM `".$this->categ_iface->getSetting('table')."` WHERE `show`=1 and `parent_id`=".$r['id']." ORDER BY `precedence`");
	   		while($ch=msr($childs))
	   		{
	   			$values[]=array('level'=>1, 'id'=>$ch['id'], 'name'=>$ch['name'], 'parent'=>$ch['parent_id']);
	   
	   			$childs2=msq("SELECT * FROM `".$this->categ_iface->getSetting('table')."` WHERE `show`=1 and `parent_id`=".$ch['id']." ORDER BY `precedence`");
	   
	   			while($ch2=msr($childs2))
	   			{
	   				$values[]=array('level'=>2, 'id'=>$ch2['id'], 'name'=>$ch2['name'], 'parent'=>$ch2['parent_id']);
	   			}
	   		}
	   	}
	   	return $values;
   }
   function AddForm ()
   {
   		$this->goods_values=$this->getCategs();
   		?>
   		 <script type="text/javascript">
	                     $(function() 
	    	             {

	                    	 $("#categs").multiselect({
	                    		   selectedText: "# из # выбрано",
	                    		   noneSelectedText: "Выберите раздел!",
	                    		   checkAllText: "Выбрать все", 
	                    		   uncheckAllText: "Очистить"
	                    		});	

	                    		$(".ui-multiselect-menu input").click(function() {
	                    			 var text = $(this).parents('li').attr('class');
	                    			 var regex = /parent\_(\d+)/gi;
		                    		 match = regex.exec(text);
		                    		 if (match[0]!='')
		                    		 {

			                    		if ($(this).parents('UL').find('.'+match[0]+' input:checked').length>0)
		                    			{
			                    			if(!$(this).parents('UL').find('.himself_'+match[0]+' input').prop('checked'))
			                    			$(this).parents('UL').find('.himself_'+match[0]+' input').trigger('click');
		                    			}
			                    		else if ($(this).find('li').length==0)
			                    		{
				                    		//
			                    		}
			                    		else
			                    		{
			                    			if($(this).parents('UL').find('.himself_'+match[0]+' input').prop('checked'))
				                    		$(this).parents('UL').find('.himself_'+match[0]+' input').trigger('click');		
			                    		}

			                    	}

				                    select_goods();
								});

								function select_goods(){
									var cnt=0;
									$('.goods div').hide();
									
									$('.ui-multiselect-checkboxes input:checked').each(function() {
										$('.goods div[data-categs*=",'+$(this).val()+',"]').show();
										cnt++;	
									});

									if (cnt==0) $('.goods div').show();

									$('.goods div:contains("'+$(this).val()+'")').show();
								}

	                    		$.expr[":"].contains = function( elem, i, match, array ) {
	                    		    return (elem.textContent || elem.innerText || jQuery.text( elem ) || "").toLowerCase().indexOf(match[3].toLowerCase()) >= 0;
	                    		}

	                    		$('[name=search_name]').keyup(function()
	                    		{
	                    			$('.goods div').hide();

	    	                    	$('.goods div:contains("'+$(this).val()+'")').show();
	                    		});

	                    		$(".goods a").click(function() {

		                    		
									$('.selected_good').html('<h3>Выбран товар: '+$(this).html()+'</h3><br/><a href="#" class="choose_anoth" onclick="return false">Выбрать другой</a>');
									$('.popup_add .button').show();
									$('.select_table').hide();

									$.ajax({
			   				            type: "POST",
			   				            url: "/inc/cclasses/CCSklad.php",
			   				            data: "action=ajax_sklad&good_id="+$(this).parents('div').attr('data-id'),
			   				            dataType: 'json',
			   				            success: function(data){
			   				            	$('.type_items').html('');
			   				            	if (data.sizes!='' && data.sizes!=undefined) $('.type_items').append(data.sizes+'<input type="hidden" name="is_size" value="1">');
			   				            	if (data.colors!='' && data.colors!=undefined) $('.type_items').append(data.colors+'<input type="hidden" name="is_color" value="1">');

			   				            	if (data.id>0) $('.type_items').append('<input type="hidden" name="good_id" value="'+data.id+'">');

			   				            	$('#popup_add .button').show();
				   				         }
			   				        });
									

									
								});

	               			    $(document).on('click','.choose_anoth', function() {
									$('#popup_add .button').hide();
									$('.select_table').show();
									$('.selected_good').html('');
									$('.type_items').html('');
	               	
	               			        return false;
	               			    });

	               			 	$('#popup_add .button').click(function() {
	               			 		var error=0;
	               			 		
		               			 	$('#popup_add select').each(function() {
			               			 	if ($(this).attr('name')!='categs[]')
			               			 	{
											if ($(this).val()<=0)
											error=1;
			               			 	}

									});



									if (error==1) alert('Укажите все значения параметров или отключите параметры у товара');
									else
									{

										var select_params='';
			               			 	$('#popup_add select').each(function() {
				               			 	if ($(this).attr('name')!='categs[]')
				               			 	select_params+='&'+$(this).attr('name')+'='+$(this).val();

										});

										$.ajax({
				   				            type: "POST",
				   				            url: "/inc/cclasses/CCBasket.php",
				   				            data: "action=ajax_add_good&order_id=<?=$_GET['pub'] ?>&good_id="+$('#popup_add [name=good_id]').val()+select_params,
				   				            dataType: 'json',
				   				            success: function(data){
					   				            if (data.errors!='' && data.errors!=undefined && data.errors!=null)
												alert(data.errors);
					   				            else
						   				        {

					   				          
					   				            	$('#popup_add').hide();
													$('#popup_add .button').hide();
													$('.select_table').show();
													$('.selected_good').html('');
													$('.type_items').html('');

													window.location.href = "/manage/control/contents/?section=<?=$_GET['section'] ?>&pub=<?=$_GET['pub'] ?>";
													
						   				        }
					   				         }
				   				        });	
									}

								});


	               				
						 });
						</script>

<link rel="stylesheet" type="text/css" href="/css/popup.css" media="all" />
<script src="/js/popup.js" type="text/javascript"></script>
<style>
.ui-multiselect {width: 250px !important;}
.ui-multiselect-menu {z-index: 999999999}
.goods {height: 200px; overflow: auto;width: 100%;}
</style>

<div id="popup_add" style="display: none; height: 400px; width: 600px;" class="popup">
	<div class="popup_close">
		<a href="#" title="Закрыть окно" onclick="return false;"></a>
	</div>
	<div class="popup_header">Добавить товар</div>
	
	<div class="popup_content form">
		<form action="/" class="-visor-no-click">
	
   										<table style="width: 100%;" class="select_table">
												<tr>
													<td style="width: 50%; vertical-align: top;">
														<div class="place" style="z-index: 10;">
															<select multiple="multiple" id="categs" name="categs[]" style="width: 100px;">
														        <?
														        if (count($this->goods_values)>0)
														        foreach ($this->goods_values as $val)
														        {
														        	?><option <?=strpos($pub['categs'],','.$val['id'].',')!==false ? 'selected' :''?> class="<?=(isset($val['level']) ? 'level_'.$val['level'] :'') ?><?=($val['level']>0 ? ' parent_'.$val['parent'].' himself_parent_'.$val['id'] : ' himself_parent_'.$val['id']) ?>" value="<?=$val['id']?>" <?=((in_array($k, $cur_sections)) ? 'checked="checked"':'')?>><?=$val['name']?></option><?
														        }
														        ?>
														    </select>
									   					</div>
													</td>
													<td style="width: 50%; vertical-align: top;">
														<div style="z-index: 10; width: 100%; margin-top:10px" class="place">
															<span class="input">
																<input type="text" value="" maxlength="20" name="search_name" placeholder="поиск по названию....">
															</span>
														</div>
														<div class="goods" style="height: 200px;">
														<?
														
														$list=$this->goods_iface->getList(-1, 'WHERE `show`=1', 'ORDER BY `name`');

														foreach ($list as $l)
														print '<div data-id="'.$l['id'].'" data-categs="'.$l['categs'].'"><a href="#" onclick="return false;">'.$l['name'].'</a></div>'
														?>
														</div>
													<?
														
													?>
													</td>
												</tr>
											</table>
	
	
	      	<div class="clear"></div>

			<div class="selected_good"></div>
			<div class="type_items"></div>
			<div style="float: right; padding: 10px 0px 15px 0;">
					<div>
						<a style="display: none;" class="button big" onclick="return false;" href="#"> Добавить </a>
					</div>
			</div>
	      	

	
		</form>
	</div>
</div>
   		<?
   }
   function drawAddEdit(){
   	global $CDDataSet,$SiteSections, $multiple_editor;
   	$section = $SiteSections->get($this->getSetting('section'));
   
   	$SectionPattern = new $section['pattern'];
   	$Iface = $SectionPattern->init(array('section'=>$section['id'],'mode'=>$delepmentmode,'isservice'=>0));
   	
   	
   	

   	/* Статусы заказа*/
       $this->createSubSection(
   			'status', 
   			'Статусы заказа',
   			array
   			(
   					array('dataset'=>$CDDataSet->GetIdByName('universal'), 'name'=>'name', 'description'=>'Наименование', 'type'=>'CDText', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(255)'),
   					array('dataset'=>$CDDataSet->GetIdByName('universal'), 'name'=>'color', 'description'=>'Цвет', 'type'=>'CDText', 'settings'=>'|show_search|show_list|', 'table_type'=>'VARCHAR(255)')	
   			),
   			array
   			(
   				array('name'=>'В обработке', 'color'=>'#666666', 'show'=>1),
   				array('name'=>'Принят', 'color'=>'#CC9900', 'show'=>1),
   				array('name'=>'Завершен', 'color'=>'#009933', 'show'=>1),
   				array('name'=>'Отменен', 'color'=>'#CC0000', 'show'=>1),
   			),
   			'|onoff|show_id|default_order=ORDER BY `id` ASC|'
   	);   
   	
   	/* Способы оплаты */
    	$this->createSubSection(
   			'paytype',
   			'Способы оплаты',
   			array
   			(
   					array('dataset'=>$CDDataSet->GetIdByName('universal'), 'name'=>'name', 'description'=>'Наименование', 'type'=>'CDText', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(255)')
   			),
   			array
   			(
   					array('name'=>'Наличные', 'show'=>1),
   					array('name'=>'Безнал', 'show'=>1),
   			),
   			'|onoff|show_id|default_order=ORDER BY `id` ASC|'
   	); 
   
   	/* Способы доставки
    	$this->createSubSection(
   			'paytype',
   			'Способы доставки',
   			array
   			(
   					array('dataset'=>$CDDataSet->GetIdByName('universal'), 'name'=>'name', 'description'=>'Наименование', 'type'=>'CDText', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(255)')
   			),
   			array
   			(
   					array('name'=>'Самовывоз', 'show'=>1),
   					array('name'=>'Доставка транспортной компанией', 'show'=>1)
   			),
   			'|onoff|show_id|default_order=ORDER BY `id` ASC|'
   	);  */
   	
   	/* Скидки */
    	$this->createSubSection(
   			'discount',
   			'Скидки',
   			array
   			(
   					array('dataset'=>$CDDataSet->GetIdByName('universal'), 'name'=>'name', 'description'=>'Наименование', 'type'=>'CDText', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(255)'),
   					array('dataset'=>$CDDataSet->GetIdByName('universal'), 'name'=>'procent', 'description'=>'Процент', 'type'=>'CDSpinner', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'BIGINT(20)'),
   					
   			),
   			array
   			(
   					array('name'=>'3 процента', 	'procent'=>'3', 'show'=>1),
   					array('name'=>'5 процентов', 	'procent'=>'5', 'show'=>1),
   					array('name'=>'10 процентов',	'procent'=>'10', 'show'=>1),
   					array('name'=>'15 процентов', 	'procent'=>'15', 'show'=>1)
   			),
   			'|onoff|show_id|default_order=ORDER BY `id` ASC|'
   	); 
   	
   	
   	$init_pattern=$Iface->getSetting('pattern');
   
   	if ($this->editor_cnt>1)
   	{
   		$multiple_editor=true;
   		?><script type="text/javascript" src="/js/tinymce/tinymce.js"></script><?
   			}
   
   			$pub = $this->getPub($_GET['pub']);
   			$pub['id'] = floor($pub['id']);
   			?>
   			<script>
   			$(function() {
   				var session_id = '<?php echo session_id(); ?>';

   				function get_discount(session_id, discount_id)
   				{
   						//if (!discount_id>0) 	{alert ('Ошибка. Не передан id скидки'); return;}
   				     	if (discount_id=='-1')
   				      	{
   				     		$('.discount_comment').html('');
   				     		$('[name=summ_discount]').val('0');

   				     		if ($('[name=summ_clear]').val()>0) $('[name=summ]').val($('[name=summ_clear]').val());
   				     		
   				      	}
   						$.ajax({
   				            type: "POST",
   				            url: "/ajax.php",
   				            data: "action=get_discount&session_id="+session_id+'&discount_id='+discount_id,
   				            dataType: 'json',
   				            success: function(data)
   				            {
   				            	if (data.error!='' && data.error!= undefined) alert(data.error);
   				            	
   				            	if (parseInt(data.discount)>0)
   				            	{
   				            		var summ_clear=parseInt($('[name=summ_clear]').val());
   				            		var discount=parseInt(data.discount);
									var summ_discount=Math.floor(summ_clear/100*discount);
   				            		var summ_new=Math.floor(summ_clear-summ_discount);

   				            		$('[name=summ_discount]').val(summ_discount);
   				            		$('[name=summ]').val(summ_new);

									if (summ_clear>summ_new)
   				            		$('.discount_comment').html('Сумма со скидкой: '+summ_new+'<br/>Сумма скидки: '+summ_discount+'<br/>Сумма без скидки: '+summ_clear);
   				            	}
   				            	

   				            }
   				        });
   				}
   				
				$("[name=discount_id]").change(function() {
					var summ_clear=parseInt($('[name=summ_clear]').val());
					
					if (!summ_clear>0) alert('Ошибка. Сумма заказа не задана. Сохраните заказ, затем выберите скидку.');
					else
					get_discount(session_id, $("[name=discount_id]").val());
	
					
				});
			});
   			</script>
   		                <div id="content" class="forms">
   		                        <?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
   		                        <?
   		                        if ($_GET['pub']=='new')
   		                        print '<h2>Для добавления товаров сохраните заказ и перейдите к его редактированию</h2>';
   		                        else
   		                        {
   		                        	$this->drawEditGoods($pub);
   		                        	$this->AddForm();
   		                        }
   		                 
   		                        $saveerrors = $this->getSetting('saveerrors');
   		                        if (!is_array($saveerrors)) $saveerrors = array();
   		                        if (count($saveerrors)>0){
   		                                print '
   		                                <p><strong>Сохранение не выполнено по следующим причинам:</strong></p>
   		                                <ul class="errors">';
   		                                        foreach ($saveerrors as $v) print '
   		                                        <li>'.$v.'</li>';
   		                                print '
   		                                </ul>
   		                                <div class="hr"><hr /></div>';
   		                        }
   		                        ?>
   		                        <p class="impfields">Поля, отмеченные знаком «<span class="important">*</span>», обязательные для заполнения.</p>
   		                        <form id="editform" name="editform" action="<?=$_SERVER['REQUEST_URI']?>" method="POST" enctype="multipart/form-data">
   		                                <input type="hidden" name="editformpost" value="1">
   		                                <?
   		                                $stylearray = array(
   		                                		"ptitle"=>'style="width:32%; margin-right:2%;"',
   		                                		"pdescription"=>'style="width:32%; margin-right:2%;"',
   		                                		"pseudolink"=>'style="width:32%;"'
   		                                );
   		                                $nospans = array("ptitle","pdescription");
   
   
   		                                $dataset = $this->getSetting('dataface');
   		                                foreach ($dataset['types'] as $dt)
   		                                {
   		                                		$tface = $dt['face'];
   
   		                                        if (isset($dt['setting_style_edit']['css'])) $stylearray[$dt['name']]='style="'.$dt['setting_style_edit']['css'].'"';
   		                                        if (isset($dt['settings']['nospan'])) $nospans[]=$dt['name'];
   
   		    									if (!isset($dt['settings']['off']))
   		                                        $tface->drawEditor($stylearray[$tface->getSetting('name')],((in_array($tface->getSetting('name'),$nospans))?false:true));
   
   
   		    									/* Подсказки у поля в паттерне: $this->setSetting('type_settings_settings', array('min_w|'=>'Минимальная ширина')); */
   		    									if (count($init_pattern->Settings['type_settings_'.$dt['name']])>0)
   		    									{
   		    										foreach ($init_pattern->Settings['type_settings_'.$dt['name']] as $k=>$v)
   		    										print $k.' - '.$v.'<br/>';
   
   		    										?><div class="clear"></div><?
   		    									}
   		    									
   		    									if ($dt['name']=='summ')
   		    									{
   		    										?>
   		    										<div class="discount_comment" style="font-weight: bold;">
   		    											<?if ($pub['summ_discount']>0) {?>
   		    												<div>Сумма со скидкой: <?=number_format($pub['summ'], 0, '.', ' ') ?></div>
   		    												<div>Сумма скидки: <?=number_format($pub['summ_discount'], 0, '.', ' ') ?></div>
   		    												<div>Сумма без скидки: <?=number_format($pub['summ_clear'], 0, '.', ' ') ?></div>
   		    											<?} ?>
   		    										</div>
   		    										<?
   		    									}
   
   
   		                                }
   
   
   		                        ?>
   
   
   		                        </table>
   		                        
   		                        <div class="place">
   		                                <span style="float: right;">
   		                                	<input class="button big" type="submit" name="editform" value="<?=($pub['id']>0)?'Сохранить изменения':'Добавить'?>"/>
   		                                </span>
   		                        </div>
   		                        <input type="hidden" name="summ_discount" value="<?=$pub['summ_discount'] ?>">
   								<input type="hidden" name="summ_clear" value="<?=(($pub['summ_clear']>0) ?  $pub['summ_clear'] : $pub['summ'])?>">
   							
   		                        <span class="clear"></span>
   		                        </form>
   		                </div>
   		                <?

   		                
   		                
   		                if (isset($this->Settings['settings_personal']['reklama']))
   		                include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/reklama/add_pattern.php");
   	}
   	function save(){
   		$errors = array();
   		$dataset = $this->getSetting('dataface');
   		foreach ($dataset['types'] as $k=>$dt){
   	
   			if (!isset($dt['settings']['off']))
   			{
   				$tface = $dt['face'];
   				$err = $tface->preSave();
   				foreach ($err as $v) $errors[] = $v;
   				$dataset['types'][$k]['face'] = $tface;
   			}
   	
   		}
   	
   		if (count($errors)==0){
   			$update = '';
   			$pub = $this->getPub($_GET['pub']); $pub['id'] = floor($pub['id']);
   			if ($pub['id']<1){
   				$count = msr(msq("SELECT COUNT(id) AS c FROM `".$this->getSetting('table')."`"));
   				$count = floor($count['c']);
   				$update.= (($update!='')?',':'')."`precedence`='$count'";
   				msq("INSERT INTO `".$this->getSetting('table')."` (`show`, `date`, `source_id`) VALUES ('1', NOW(), 1)");
   				$pub['id'] = mslastid();
   			}
   	
   			foreach ($dataset['types'] as $dt)
   			if ($dt['name']!='summ_discount' && $dt['name']!='summ_clear')
   			{
   				$tface = $dt['face'];
   				$tface->init(array('uid'=>floor($pub['id'])));
   				$tface->postSave();
   				$update.= (($update!='')?',':'').$tface->getUpdateSQL((int)$_GET['section']);
   				$dataset['types'][$k]['face'] = $tface;
   			}
   	
   			
   			$update.=', `summ_discount`="'.floor($_POST['summ_discount']).'"';
   			
   			
   			if (!$_POST['summ_clear']>0 && !$_POST['summ_discount']>0)
   			$_POST['summ_clear']=$_POST['summ'];
   			
   			$update.=', `summ_clear`="'.$_POST['summ_clear'].'"';
   			
   			msq("UPDATE `".$this->getSetting('table')."` SET ".$update." WHERE `id`='".$pub['id']."'");
   	
   		/* 	if (!$_POST['summ_clear']>0 && !$_POST['summ_discount']>0)
   			$_POST['summ_clear']= */
   			
   			
   			
   			WriteLog($pub['id'], (($_GET['pub']=='new') ? 'добавление':'редактирование').' записи', '','','',$this->getSetting('section'));
   		}
   	
   		$this->setSetting('dataface',$dataset);
   		return $errors;
   	}
  

}

if ($_POST['action']=='ajax_basket' || $_POST['action']=='ajax_add_good')
{
	$tbasket=new CCBasket();
	$tbasket->ajax();
}

?>