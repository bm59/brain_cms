<?

class CCBasket extends VirtualContent
{

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


   }
   function createSubSection ($sub_path='', $sub_name='', $add_fields=array(), $add_values=array(), $settings_personal, $parent_path='')
   {
   		global $CDDataSet, $SiteSections, $DataType;
   			
   		if ($parent_path!='')
   		$parent = $SiteSections->get($SiteSections->getIdByPath($parent_path));
   		else
   		$parent = $SiteSections->get($this->getSetting('section'));
   		
   		
   		$parent_path = $SiteSections->getPath($this->getSetting('section'));
   		
   		$sub_section = $SiteSections->get($SiteSections->getIdByPath($parent_path.$sub_path.'/'));
   		
   		if (!$sub_section['id']>0)
   		{
   			if (!$parent_id>0) $parent_id=$this->getSetting('section');
   			
   			$SiteSections->add(array(
   					'name'=>$sub_name, 
   					'path'=>$sub_path, 
   					'pattern'=>'PUniversal',
   					'settings_personal'=>array($settings_personal)
   					
   			), $parent['id']);
   			
   			$sub_section = $SiteSections->get($SiteSections->getIdByPath($parent_path.$sub_path.'/'));
   			 
   			$dt=new DataType;
   			$dt->init();
   			 
   			/* Добавляем поля в раздел */
   			foreach ($add_fields as $add)
   			{
   				$add['section_id']=$sub_section['id'];
   				$dt->add($add, true, '', $sub_section['id']);
   			}
   			
   			 
   			/* Вставляем начальные данные в таблицу */
   			if ($sub_section['id']>0 && count($add_values)>0)
   			{
   				$Pattern = new $sub_section['pattern'];
   				$Iface = $Pattern->init(array('section'=>$sub_section['id']));
   				foreach ($add_values as $add)
   				{
   					$this->insertNotDouble($add, $Iface->getSetting('table'));
   				}
   			}
   		
   		}

   		
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
   		
   		msq("INSERT INTO `".$this->getSetting('table')."` (`status_id`, $keys) VALUES ('1', $values)");
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
   function SendOrder($id, $save, $rus_calls)
   {
   	 
   		include_once($_SERVER['DOCUMENT_ROOT']."/inc/idna_convert.class.php");
   		$idn = new idna_convert(array('idn_version'=>2008));
   	
   		$order=$this->GetSaveOrder($id);
   		foreach ($save as $k=>$v)
   		if (isset($rus_calls[$k]))
   		{
   			$msg.='<div>'.$rus_calls[$k].'='.stripcslashes($v).'</div>';
   		}
   		
   		$style='style="border: 1px solid #CCCCCC; padding: 10px;"';
   		
   		$msg.="<br/><table><tr><th $style>Товар</th><th $style>Кол-во</th><th $style>Сумма</th></tr>";
   		
   	
   		$goods=$this->GetAllOrderItem($id);
   		foreach ($goods as $g)
   		{
   			$good_info=$this->GetGood($g['good_id']);
   			$categ_info=$this->GetGoodCateg($good_info['categ_id']);
   			if ($good_info['name']=='' && $good_info['diam']!='') $good_info['name']='Диаметр: '.$good_info['diam'];
   			
   			
   			$msg.="<tr><td $style>".$categ_info['name'].': '.$good_info['name'] ."</td><td $style>".$g['kol']."</td><td $style>".$g['summ']."</td></tr>";
   		   							
   		   							
   		}
   		
   		$msg.='</table>';
   		
   		$msg.='<h2>Итого: '.$order['summ'].(($order['summ_discount']>0) ? '[скидка: '.$order['summ_discount'].']' : '').'</h2>';
   		
   		
   		
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
   function GetGoodCateg ($id){
   		return msr(msq("SELECT * FROM `".$this->getSetting('table_categs')."` WHERE id=$id"));
   }
   function GetTotalBasket($tmp_order_id){
   	 
   		$return=msr(msq("SELECT count(*) as cnt, sum(`summ`) as summ FROM `".$this->getSetting('table_order_tmp_goods')."` WHERE `tmp_order_id`='$tmp_order_id'"));

   		return $return;
   }
   function GetGoodBasket($tmp_order_id, $good_id){
   	 
   	$return=msr(msq("SELECT * FROM `".$this->getSetting('table_order_tmp_goods')."` WHERE `tmp_order_id`='$tmp_order_id' and `good_id`='$good_id'"));
   
   	return $return;
   }
   function GetTotalBasketComment($tmp_order_id){
   	 
   		$return=$this->GetTotalBasket($tmp_order_id);
   
   		if ($return['summ']>0) 
   		return 'Товаров: '.number_format($return['summ'], 0, '.', ' ').'	<small>руб</small> ('.$return['cnt'].' <small>шт</small>)';
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
   		$return[$r['good_id']]=$r;
   	}
   	return $return;
   }
   function AddGood ($tmp_order_id, $good_id, $kol, $price_field)
   {
   		$tmp_order=msr(msq("SELECT * FROM `".$this->getSetting('table_order_tmp')."` WHERE `id`='$tmp_order_id' LIMIT 1"));
   		if (!$tmp_order['id']>0) return array('error'=>'Ошибка. Не найден заказ tmp_order_id');
   		
   		$good=$this->GetGood($good_id);
   		if (!$good['id']>0) return array('error'=>'Ошибка. Не найден товар good_id');
   		if (!$good['price']>0) return array('error'=>'Ошибка. Не найден параметр товара price');
   		

   		$cur_kol=msr(msq("SELECT * FROM `".$this->getSetting('table_order_tmp_goods')."` WHERE `tmp_order_id`='$tmp_order_id' and `good_id`='$good_id'"));
   		
   		
   		
   		/* ---Несколько типов цены--- */
   		if ($price_field!='' && $good[$price_field]>0)
   		$price=$good[$price_field];
   		else
   		$price=$good['price'];
   		
   		if ($cur_kol['price']>0 && $cur_kol['price']!=$good['price'])
   		{
   			$price=$good['price_metr'];
   		}
   		/* ---Несколько типов цены--- */
   		
   		
   		$summ=0;
   		if ($kol>0) $summ=$kol*$price;
   		
  		if (floor($kol)==0){
  			msq("DELETE FROM `".$this->getSetting('table_order_tmp_goods')."` WHERE `id`=".$cur_kol['id']);
  		}
  		elseif ($cur_kol['id']>0)
  		{
  			msq("UPDATE `".$this->getSetting('table_order_tmp_goods')."` SET `kol`='$kol', `price`='$price', `summ`='$summ' WHERE `id`=".$cur_kol['id']);
  		}
  		else
  		{
  			msq("INSERT INTO `".$this->getSetting('table_order_tmp_goods')."` (`tmp_order_id`, `good_id`, `kol`, `price`, `summ`) VALUES ('$tmp_order_id', '$good_id', '$kol', '$price', '$summ')");
  		}
  		
   }
   function GetTmpOrder (){
   		if ($_SESSION['tmp_order_num']>0) 		return $_SESSION['tmp_order_num'];
   		elseif (cookieGet('tmp_order_num')>0) 	return cookieGet('tmp_order_num');
   		else
   		{
   			$q = msr(msq("SELECT max(id) as num FROM `".$this->getSetting('table_order_tmp')."`"));
   			msq("INSERT INTO `".$this->getSetting('table_order_tmp')."` (`id`) VALUES ('".(floor($q['num'])+1)."')");
   			$_SESSION['tmp_order_num']=floor($q['num'])+1;
   			cookieSet('tmp_order_num', floor($q['num'])+1,180);
   			
   			return floor($q['num'])+1;
   		}
   }
   function drawPubsList($param=''){
   	global $SiteSections, $CDDataSet, $CDDataType;
   
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
   
   					?>
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
   
   
   
   		foreach($show_fields as $sf){
   			$set=$dataset['types'][$sf]['face']->Settings;
   			$table_th[]=array('name'=>$set['name'], 'description'=>$set['description'], 'class'=>$set['settings']['list_class']);
   		}
   
   
   
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
   		<tr>
   
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
   
   
   			<!-- Видимые поля -->
   			<?
   			foreach($show_fields as $sf)
   			{
   				$set=$dataset['types'][$sf]['face']->Settings;
   				$href=array();
   				if (in_array($sf,$editlink_double) && !isset($set['settings']['editable'])) $href=array('<a href="/manage/control/contents/?section='.$section['id'].'&pub='.$pub['id'].'" title="Редактировать">', '</a>');
   				
   				if ($sf=='summ') $pub[$sf]=number_format($pub[$sf], 0, '.', ' ');
   				
   				?>
   				<td <?=$set['settings']['list_class']!='' ? 'class="'.$set['settings']['list_class'].'"' : ''?> <?=$set['settings']['list_style']!='' ? 'style="'.$set['settings']['list_style'].'"' : ''?>>
   					<?=$href[0]?><?=$CDDataType->get_view_field($dataset['types'][$sf],$pub[$sf], $pub);?><?=$href[1]?>
   				
   					<?
   					if ($sf=='summ')
   					{
   						if ($pub['summ_discount']>0) print '[скидка: '.$pub['summ_discount'].']';
   						
   						?>
   						<div class="hr" style="margin: 10px 0;"><hr/></div>
   						<table class="table-content stat">
   						<?
   						
   						$goods=$this->GetAllOrderItem($pub['id']);
   						foreach ($goods as $g)
   						{
   							$good_info=$this->GetGood($g['good_id']);
   							$categ_info=$this->GetGoodCateg($good_info['categ_id']);
   							if ($good_info['name']=='' && $good_info['diam']!='') $good_info['name']='Диаметр: '.$good_info['diam'];
   							?>
   							<tr>
   								<td><?=$categ_info['name'] ?></td>
   								<td><?=$good_info['name'] ?></td>
   								<td><nobr><?=$g['kol'] ?> шт.</nobr></td>
   								<td><nobr><?=$g['summ'] ?> р.</nobr></td>
   							</tr>
   							<?
   							
   							
   						}
   						?>   						
   						</table>
   						<?	
   						if ($pub['comment']!='')
   						{
   							?>
   						   	<div class="hr" style="margin: 10px 0;"><hr/></div>
   						   	<div><i>Комментарий: <?=$pub['comment'] ?></i></div>
   						   	<?	
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
   		                <?
   }
   function drawAddEdit(){
   	global $CDDataSet,$SiteSections, $multiple_editor;
   	$section = $SiteSections->get($this->getSetting('section'));
   
   	$SectionPattern = new $section['pattern'];
   	$Iface = $SectionPattern->init(array('section'=>$section['id'],'mode'=>$delepmentmode,'isservice'=>0));
   	
   	/* Статусы заказа */
/*     $this->createSubSection(
   			'status', 
   			'Статусы заказа',
   			array
   			(
   					array('dataset'=>2, 'name'=>'name', 'description'=>'Наименование', 'type'=>'CDText', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(255)'),
   					array('dataset'=>2, 'name'=>'color', 'description'=>'Цвет', 'type'=>'CDText', 'settings'=>'|show_search|show_list|', 'table_type'=>'VARCHAR(255)')	
   			),
   			array
   			(
   				array('name'=>'В обработке', 'color'=>'#666666', 'show'=>1),
   				array('name'=>'Принят', 'color'=>'#CC9900', 'show'=>1),
   				array('name'=>'Завершен', 'color'=>'#009933', 'show'=>1),
   				array('name'=>'Отменен', 'color'=>'#CC0000', 'show'=>1),
   			),
   			'|onoff|show_id|default_order=ORDER BY `id` ASC|'
   	); */
   	
   	/* Способы доставки */
/*    	$this->createSubSection(
   			'paytype',
   			'Способы оплаты',
   			array
   			(
   					array('dataset'=>2, 'name'=>'name', 'description'=>'Наименование', 'type'=>'CDText', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(255)')
   			),
   			array
   			(
   					array('name'=>'Наличные', 'show'=>1),
   					array('name'=>'Безнал', 'show'=>1),
   			),
   			'|onoff|show_id|default_order=ORDER BY `id` ASC|'
   	); */
   	
   	/* Скидки */
/*    	$this->createSubSection(
   			'discount',
   			'Скидки',
   			array
   			(
   					array('dataset'=>2, 'name'=>'name', 'description'=>'Наименование', 'type'=>'CDText', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(255)'),
   					array('dataset'=>2, 'name'=>'procent', 'description'=>'Процент', 'type'=>'CDSpinner', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'BIGINT(20)'),
   					
   			),
   			array
   			(
   					array('name'=>'3 процента', 	'procent'=>'3', 'show'=>1),
   					array('name'=>'5 процентов', 	'procent'=>'5', 'show'=>1),
   					array('name'=>'10 процентов',	'procent'=>'10', 'show'=>1),
   					array('name'=>'15 процентов', 	'procent'=>'15', 'show'=>1)
   			),
   			'|onoff|show_id|default_order=ORDER BY `id` ASC|'
   	); */
   	
   	/* Товары */
/*    	$this->createSubSection(
   			'goods',
   			'Товары',
   			array
   			(
   					array('dataset'=>2, 'name'=>'cat_id', 'description'=>'Категория товара', 'type'=>'CDSelect',  'settings'=>array('source'=>'#source_type=spr#spr_path=/sitecontent/basket/categories/#spr_field=name#spr_usl=WHERE `show`=1#spr_order=ORDER BY `id`')),
   					array('dataset'=>2, 'name'=>'name', 'description'=>'Наименование', 'type'=>'CDText', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(255)'),
   					array('dataset'=>2, 'name'=>'images', 'description'=>'Картинки', 'type'=>'CDGallery', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(1000)'),
   					array('dataset'=>2, 'name'=>'price', 'description'=>'Цена',  'type'=>'CDSpinner', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'BIGINT(20)'),
   					array('dataset'=>2, 'name'=>'description', 'description'=>'Описание', 'type'=>'CDText',  'settings'=>array()),
   					array('dataset'=>2, 'name'=>'ptitle', 'description'=>'Title страницы', 'type'=>'CDText','settings'=>array()),
   					array('dataset'=>2, 'name'=>'pdescription', 'description'=>'Description страницы', 'type'=>'CDText', 'settings'=>array()),
   					array('dataset'=>2, 'name'=>'pseudolink', 'description'=>'Псеводоним ссылки', 'type'=>'CDText', 'settings'=>array())
   	
   			),
   			array
   			(),
   			'|onoff|show_id|default_order=ORDER BY `id` ASC|',
   			'/sitecontent/'
   			); */
   	
   	/* Категории товара */
/*     $this->createSubSection(
   			'categories',
   			'Категории',
   			array
   			(
   					array('dataset'=>2, 'name'=>'name', 'description'=>'Наименование', 'type'=>'CDText', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(255)')
   			),
   			array
   			(		array('name'=>'Категория 1', 'show'=>1),
   					array('name'=>'Категория 2', 'show'=>1)
   					
   			),
   			'|onoff|show_id|default_order=ORDER BY `id` ASC|precedence|',
    		'/sitecontent/goods/'
   	); */ 
   	
   	
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
   						if (!discount_id>0) 	{alert ('Ошибка. Не передан id скидки'); return;}

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
									var summ_discount=summ_clear/100*discount;
   				            		var summ_new=summ_clear-summ_discount;

   				            		$('[name=summ_discount]').val(summ_discount);
   				            		$('[name=summ]').val(summ_new);

									if (summ_clear>summ_new)
   				            		$('.discount_comment').html('Сумма со скидкой: '+summ_new+'<br/>Сумма скидки: '+summ_discount+'<br/>Сумма без скидки: '+summ_clear);
   				            	}
   				            }
   				        });
   				}
   				
				$("[name=discount_id]").click(function() {
					var summ_clear=parseInt($('[name=summ_clear]').val());
					
					if (!summ_clear>0) alert('Ошибка. Сумма заказа не задана');
					else
					get_discount(session_id, $("[name=discount_id]").val());
	
					
				});
			});
   			</script>
   		                <div id="content" class="forms">
   		                        <?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
   		                        <?
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
   		    											<?if ($pub['summ_clear']>$pub['summ']) {?>
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
   								<input type="hidden" name="summ_clear" value="<?=$pub['summ_clear'] ?>">
   							
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
   				msq("INSERT INTO `".$this->getSetting('table')."` (`show`) VALUES ('1')");
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
   	
   			if ($_POST['summ_discount']>0) $update.=', `summ_discount`="'.$_POST['summ_discount'].'"';

   			msq("UPDATE `".$this->getSetting('table')."` SET ".$update." WHERE `id`='".$pub['id']."'");
   			
   			print "UPDATE `".$this->getSetting('table')."` SET ".$update." WHERE `id`='".$pub['id']."'";
   	
   	
   	
   			WriteLog($pub['id'], (($_GET['pub']=='new') ? 'добавление':'редактирование').' записи', '','','',$this->getSetting('section'));
   		}
   	
   		$this->setSetting('dataface',$dataset);
   		return $errors;
   	}
  

}
?>