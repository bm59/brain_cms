<?
include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/include.php");
class CCSklad extends VirtualContent
{

	function init($settings){
        		global $SiteSections;
                VirtualContent::init($settings);

                $section = $SiteSections->get($this->getSetting('section'));
                
                $this->Settings['settings_personal']=$section['settings_personal'];

                $SiteSettings = new SiteSettings;
                $SiteSettings->init();
                $this->Settings['onpage'] = $this->Settings['settings_personal']['on_page']>0 ? $section['settings_personal']['on_page'] : 20;



                $this->like_array=array();/* Где нет в названии "name", но нужен поиск по like*/
                $this->not_like_array=array();/* Где есть в названии "name", но не нужен поиск по like*/
                $this->no_auto=array(); /*Не обрабатывать переменные, ручная обработка*/

                /*заменить на пустое в названиях переменны при поиске*/
                $this->field_tr=array('search_'=>'','_from'=>'','_to'=>'');

                /*подмена названий*/
                $this->field_change=array();


 				$this->getSearch();
 				
 				$basket_section=$SiteSections->getByPattern('PBasket');
 				if (!$basket_section['id']>0)
 				print '<h2>Не найден раздел с заказами</h2>';
 				
 				if ($basket_section['id']>0)
 				$this->basket_iface=getIface($SiteSections->getPath($basket_section['id']));
 				
 				
 				$goods_section=$SiteSections->getByPattern('PGoods');
 				if (!$goods_section['id']>0)
 				print '<h2>Не найден раздел с товарами</h2>';
 				
 				if ($goods_section['id']>0)
 				$this->goods_iface=getIface($SiteSections->getPath($goods_section['id']));
 				
 				
 				$categ_section=$SiteSections->getIdByPath($SiteSections->getPath($goods_section['id']).'categs/');
 				if (!$categ_section['id']>0)
 				print '<h2>Необходимо добавить дочерний раздел "categs" с категориями товаров</h2>';
 				else
 				$this->categ_iface=getIface($SiteSections->getPath($goods_section['id']).'categs/');
 				
   		        
   		       $this->goods_values=$this->getCategs();
   		                   


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
   function getFreeSkladKol($pub, $order_id){
   	
   		if ($pub['size_id']>0) $usl.=' and `size_id`='.$pub['size_id'];
   		if ($pub['color_id']>0) $usl.=' and `color_id`='.$pub['color_id'];
   		
   		$sklad=msr(msq("SELECT sum(kol) as kol FROM `".$this->getSetting('table')."` WHERE `good_id`=".$pub['good_id'].$usl));
   		
   		$status_usl='and `status_id`<>4';
   		if ($order_id>0) $status_usl=' and (`status_id`<>4 or `order_id`='.$order_id.')';
   		$q="SELECT sum(goods.kol) as sum_kol FROM `".$this->basket_iface->getSetting('table')."` basket, `site_site_order_goods` goods WHERE goods.order_id=basket.id ".$status_usl." and `good_id`=".$pub['good_id'].$usl;
	   			
	  	$sale=msr(msq($q));
 		
   		return floor($sklad['kol'])-floor($sale['sum_kol']);
   }
   function getFreeParamsKol()
   {
   		global $SiteSections;
   		
   		$pub['good_id']=$_POST['good_id'];
   		
   		$enable_ids='';
   		
   		$this_section=$SiteSections->getByPattern('PSklad');
   		$this_iface=getIface($SiteSections->getPath($this_section['id']));
   		
   		$dist=msq("SELECT distinct(`".$_POST['out_name']."_id`) FROM `".$this_iface->getSetting('table')."` WHERE `good_id`=".$_POST['good_id']." and `".$_POST['inp_name']."_id`='".$_POST['inp_val']."'");
   		
   		while ($ds=msr($dist))
   		{
   			$usl='';
   			$usl.=' and `'.$_POST['inp_name'].'_id`='.$_POST['inp_val'];
   			$usl.=' and `'.$_POST['out_name'].'_id`='.$ds[$_POST['out_name'].'_id'];
   			
   			
   			$pub[$_POST['out_name']."_id"]=$ds[$_POST['out_name']."_id"];
   			
   			$sklad=msr(msq("SELECT sum(kol) as kol FROM `".$this_iface->getSetting('table')."` WHERE `good_id`=".$pub['good_id'].$usl));
   			
   			
   			$q="SELECT sum(goods.kol) as sum_kol FROM `".$this->basket_iface->getSetting('table')."` basket, `site_site_order_goods` goods WHERE goods.order_id=basket.id and `status_id`<>4 and `good_id`=".$pub['good_id'].$usl;
   			
   			$sale=msr(msq($q));
   			
   			if ((floor($sklad['kol'])-floor($sale['sum_kol']))>0)
   			$enable_ids.=($enable_ids!='' ? ',':'').$ds[$_POST['out_name'].'_id']; 
   		}
   		
   		print json_encode(array('ids'=>$enable_ids));
   		
   }
   function getSearch(){
   	global $MySqlObject;
   
   	foreach ($_REQUEST as $k=>$v)
   		if (stripos($k,'search')!==false && $v!='' && $v!='-1')
   			if (!in_array($k,$this->no_auto))
   				if (strpos($k, 'nouse_')===false)
   				{
   					$this->$k=$v;
   
   					$mysql_k=strtr($k,$this->field_tr);
   					$mysql_k=strtr($mysql_k,$this->field_change);
   
   					$this->urlstr.='&'.$k.'='.$v;
   
   					if (!in_array($mysql_k,$this->field_change)) $mysql_k='`'.$mysql_k.'`';
   
   					if ($this->sqlstr =='') $sql_pref=' and ';  /*!!!Заменить на WHERE если нет других условий*/
   					else $sql_pref=' and ';
   
   
   					if ($_REQUEST['nouse_'.$k.'_type']=='CDCHOICE')
   						$this->sqlstr.=$sql_pref.$mysql_k." like '%,".$v.",%'";
   						elseif ((stripos($k,'name')!==false || in_array($k,$this->like_array)) and !in_array($k,$this->not_like_array))
   						$this->sqlstr.=$sql_pref.$mysql_k." like '%".$v."%'";
   						elseif (stripos($k, '_from') && $v!='')
   						$this->sqlstr.=$sql_pref.$mysql_k.">='".$MySqlObject->dateToDB($v)."'";
   						elseif (stripos($k, '_to') && $v!='')
   						$this->sqlstr.=$sql_pref.$mysql_k."<='".$MySqlObject->dateToDB($v)."'";
   						else $this->sqlstr.=$sql_pref.$mysql_k."='".$v."'";
   
   				}
   }
   function getList($page=0, $str_usl='', $str_order_by=''){
   
   	$retval = array();
   
   	$q = "SELECT 
   			sklad.id, 
   			sklad.date, 
   			sklad.kol, 
   			sklad.color_id, 
   			sklad.size_id, 
   			sklad.user_id, 
   			sklad.good_id, 
   			goods.name 
   		FROM `".$this->getSetting('table')."` sklad, `".$this->goods_iface->getSetting('table')."` goods WHERE sklad.good_id=goods.id ".$this->sqlstr.$str_usl;

   	$count = msq($q);
   	$count = @mysql_num_rows($count);
   
   	$page = floor($page);
   	if ($page==-1 || isset($this->Settings['settings_personal']['no_paging'])) $this->setSetting('onpage',10000);
   	if ($page<1) $page = 1;
   
   
   	$this->setSetting('pagescount',ceil($count/$this->getSetting('onpage')));
   	$this->setSetting('count',ceil($count));
   
   	if ($this->getSetting('pagescount')>0 && $page>$this->getSetting('pagescount')) $page = $this->getSetting('pagescount');
   	$this->setSetting('page',$page);
   
   
   	if ($_GET['sort']!='')
   	{
   		$order_by="ORDER BY `".$_GET['sort']."` ".$_GET['sort_type'];
   	}
   	else
   		$order_by=$this->Settings['settings_personal']['default_order']!='' ? $this->Settings['settings_personal']['default_order'] : "ORDER BY sklad.`date` DESC";
   
   		if ($str_order_by) $order_by=$str_order_by;
   
   
   		$this->order_by=$order_by;

   		$q = msq($q." ".$order_by." LIMIT ".(($page-1)*$this->getSetting('onpage')).",".$this->getSetting('onpage'));

   		while ($r = msr($q)) $retval[] = $r;
   
   		return $retval;
   }
   function drawPubsList($param=''){
   	global $SiteSections, $CDDataSet, $CDDataType, $MySqlObject, $SiteVisitor;
   	
   	if ($_GET['action']=='update') $this->updateSklad();
   
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
   					<div style="z-index: 10;width: 10%" class="place">
		        		<label>ID товара</label>
						<span class="input">
							<input type="text" value="<?=$_POST['search_good_id'] ?>" name="search_good_id">
						</span>
					</div>
					
   					<div style="z-index: 10;width: 27%" class="place">
		        		<label>Наименование или часть</label>
						<span class="input">
							<input type="text" value="<?=$_POST['search_name'] ?>" name="search_name">
						</span>
					</div>
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

									$("[name=search_date_from]").datepicker({
									showOn: "both",
									buttonImage: "/pics/editor/calendar.gif",
									buttonImageOnly: true
								});
								$.datepicker.setDefaults($.extend($.datepicker.regional["ru"]));

									$("[name=search_date_to]").datepicker({
									showOn: "both",
									buttonImage: "/pics/editor/calendar.gif",
									buttonImageOnly: true
								});

							});
							</script>
							<div style="z-index: 11; width: 158px;" id="date_calendar" class="place">
								<label>Дата c</label>
								<div><input type="text" name="search_date_from" value="<?=$_POST['search_date_from']?>" style="width: 100px; float: left;"></div>
							</div>
							<div style="z-index: 11; width: 158px;" id="date_calendar" class="place">
								<label>Дата по</label>
								<div><input type="text" name="search_date_to" value="<?=$_POST['search_date_to']?>" style="width: 100px; float: left;"></div>
							</div>
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
   				</form>
   				<a href="/manage/control/contents/?section=<?=$_GET['section'] ?>&action=update" class="button"><img src="/pics/editor/calc_white.png">Пересчитать склад</a>
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
   		                                <div class="place">
   		                                	<a href="./?section=<?=$section['id']?>&pub=new" class="button big" style="float: right;">Добавить</a>
   		                                </div>
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
   
   		$table_th[]=array('name'=>'', 'description'=>'#товара', 'class'=>'');
   		$table_th[]=array('name'=>'name', 'description'=>'Дата', 'class'=>'t_32width');
   		$table_th[]=array('name'=>'name', 'description'=>'Товар', 'class'=>'');
   		
   	
   		
   		foreach($show_fields as $sf){
   			$set=$dataset['types'][$sf]['face']->Settings;
   			$table_th[]=array('name'=>$set['name'], 'description'=>$set['description'], 'class'=>$set['settings']['list_class']);
   		}
   
   
   
   		/* Редактирование и удаление */
   		$table_th[]=array('name'=>'', 'description'=>'', 'class'=>'t_minwidth');
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
   			<td><?=$pub['good_id'] ?></td>
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
			   			<?=$MySqlObject->dateFromDBDot($pub['date']) ?>
			   			</td>
			   			<td style="text-align: left;">
				   			<?
				   			$good=$this->goods_iface->getPub($pub['good_id']);
				   			?>
				   			<div><strong><?=$good['name']?></strong></div>
				   			<?
				   			if ($pub['size_id']>0)
				   			{
				   				$param=msr(msq("SELECT * FROM `site_site_goods_size` WHERE `id`=".$pub['size_id']));	
				   				if ($param['id']>0)
				   				{
				   					?><div>Размер: <?=$param['name']?></div><?
				   				}
				   			}
				   			if ($pub['color_id']>0)
				   			{
				   				$param=msr(msq("SELECT * FROM `site_site_goods_color` WHERE `id`=".$pub['color_id']));
				   				if ($param['id']>0)
				   				{
				   					?><div>Цвет: <?=$param['name']?></div><?
				   				}
				   			}
				   			?>
			   			</td>
			   			
   			
   			<!-- Видимые поля -->
   			<?
   			foreach($show_fields as $sf)
   			{
   				$set=$dataset['types'][$sf]['face']->Settings;
   				$href=array();
   				if (in_array($sf,$editlink_double) && !isset($set['settings']['editable'])) $href=array('<a href="/manage/control/contents/?section='.$section['id'].'&pub='.$pub['id'].'" title="Редактировать">', '</a>');
   				?>
   				<td <?=$set['settings']['list_class']!='' ? 'class="'.$set['settings']['list_class'].'"' : ''?> <?=$set['settings']['list_style']!='' ? 'style="'.$set['settings']['list_style'].'"' : ''?>>
   					<?=$href[0]?><?=$CDDataType->get_view_field($dataset['types'][$sf],$pub[$sf], $pub);?><?=$href[1]?>
   				</td>
   			<?}?>
   			
   			
   			
   			<td><nobr><?=$SiteVisitor->getFIO($pub['user_id']) ?></nobr></td>
   
   
   
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
   
   		                        ?>
   		                </div>
   		  			</div>
   		                <?
   }
   function save(){
   	global $user;
   	
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
  
   	if ($_POST['is_size']==1 && $_POST['size_id']<=0)
   	$errors[]='Не заполнено поле "размер". Необходимо выбрать размер или в настройках товара отключить этот параметр';

   	if ($_POST['is_color']==1 && $_POST['color_id']<=0)
   	$errors[]='Не заполнено поле "цвет". Необходимо выбрать размер или в настройках товара отключить этот параметр';

   	if (count($errors)==0){
   		$update = '';
   		$pub = $this->getPub($_GET['pub']); $pub['id'] = floor($pub['id']);
   		if ($pub['id']<1){
   			msq("INSERT INTO `".$this->getSetting('table')."` (`show`) VALUES ('1')");
   			$pub['id'] = mslastid();
   		}
   		
   		foreach ($dataset['types'] as $dt)
   		{
   			$tface = $dt['face'];
   			$tface->init(array('uid'=>floor($pub['id'])));
   			$tface->postSave();
   			$update.= (($update!='')?',':'').$tface->getUpdateSQL((int)$_GET['section']);
   			$dataset['types'][$k]['face'] = $tface;
   		}
   
   		$update.=', `user_id`="'.$user['id'].'", `date`=NOW(), `good_id`="'.$_POST['good_id'].'"';
   		
   		$update.=', `size_id`='.floor($_POST['size_id']);
   		$update.=', `color_id`='.floor($_POST['color_id']);
   		

   		
   		
   		msq("UPDATE `".$this->getSetting('table')."` SET ".$update." WHERE `id`='".$pub['id']."'");
   		
   		print "UPDATE `".$this->getSetting('table')."` SET ".$update." WHERE `id`='".$pub['id']."'";
   		
   		/* Пишем лог */
   		if (isset($pub['kol']))
   		$comment='Количество: до '.$pub['kol'].': после '.$_POST['kol'];

   		WriteLog($pub['id'], (($_GET['pub']=='new') ? 'добавление':'редактирование').' записи', $comment,'','',$this->getSetting('section'));
   		
   		/* Обновляем склад */
   		$this->updateSklad($_POST['good_id']);
   	}
   
   	$this->setSetting('dataface',$dataset);
   	return $errors;
   }
   function deletePub($id,$updateprec = true){
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
   
   		WriteLog($id, 'удаление записи', 'good_id: '.$r['good_id'].'; кол: '.$r['kol'],'','',$this->getSetting('section'));
   
   		if ($updateprec) $this->updatePrecedence();
   		return true;
   	}
   	return false;
   }
   function ajax(){
   		

   		if ($_POST['action']=='ajax_getparamskol')
   		{
   			$this->init();
   			$this->getFreeParamsKol();	
   		}
   		elseif ($_POST['action']=='ajax_save')
   		{
   			$this->ajax_save();
   		}
   		elseif ($_POST['good_id']>0)
   		{
   			$this->init();
   			$good=msr(msq("SELECT * FROM `".$this->goods_iface->getSetting('table')."` WHERE `show`=1 and `id`=".$_POST['good_id']." ORDER BY `precedence`"));
   		
   			if ($good['is_size']==1) 	$good['sizes']=$this->getTypeItems($good, 'site_site_goods_size', 'Размер', 'size_id');
   			if ($good['is_color']==1) 	$good['colors']=$this->getTypeItems($good, 'site_site_goods_color', 'Цвет', 'color_id');
   		
   			print json_encode($good);
   		}
   		

   		
   }
   function start(){
   	global $CDDataSet;
   
   	$dataset = $CDDataSet->get($this->getSetting('dataset'));
   	$imagestorage = $this->getSetting('imagestorage');
   
   		if ($_GET['pub']>0)
   		$pub = $this->getPub(floor($_GET['pub']));
   		
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
   		elseif ($_POST['action']!='ajax_save'){
   			if (floor($_GET['delete'])>0) $this->deletePub($_GET['delete']);
   			$this->setSetting('dataface',$dataset);
   			$this->drawPubsList();
   		}
   }
   function ajax_save(){
   		global $SiteSections, $SiteVisitor;
   		include_once($_SERVER['DOCUMENT_ROOT']."/inc/SiteVisitor.php");
   		$this_section=$SiteSections->getByPattern('PSklad');
 		$this_iface=getIface($SiteSections->getPath($this_section['id']));
 		$SiteVisitor = new SiteVisitor;
		$SiteVisitor->init();
		$user = $SiteVisitor->getOne($_SESSION['visitorID']);

  		
   		if ($_POST['size_id']>0)	$_POST['is_size']=1;
   		if ($_POST['color_id']>0) 	$_POST['is_color']=1;
   		
   		$update.=', `user_id`="'.$user['id'].'", `date`=NOW(), `good_id`="'.$_POST['good_id'].'"';
   		 
   		$update.=', `size_id`='.floor($_POST['size_id']);
   		$update.=', `color_id`='.floor($_POST['color_id']);
   		 
   		
   		$comment=''; 
   		if ($_POST['size_id']>0)
   		{
   			$param=msr(msq("SELECT * FROM `site_site_goods_size` WHERE id=".$_POST['size_id']));
   			$comment.='[Размер: '.$param['name'].']&nbsp;&nbsp;';
   		}
      	if ($_POST['color_id']>0)
   		{
   			$param=msr(msq("SELECT * FROM `site_site_goods_color` WHERE id=".$_POST['color_id']));
   			$comment.='[Цвет: '.$param['name'].']&nbsp;&nbsp;';
   		}
   		$comment.=' количество: '.$_POST['kol'];
   		
   		msq("INSERT INTO `".$this_iface->Settings['table']."` 
   		(`show`, `user_id`, `good_id`, `size_id`, `color_id`, `date`, `kol`, `comment`) VALUES
   		(1, '".$user['id']."', '".$_POST['good_id']."', '".$_POST['size_id']."', '".$_POST['color_id']."', NOW(), '".$_POST['kol']."', '".$_POST['comment']."')");
   		
   		print json_encode(array('ok'=>'ok', 'comment'=>$comment));
   }
   function updateSklad($id=0, $dop='')
   {
   		if ($id>0) $dop=" WHERE `id`=$id";
   		
   		if ($dop!='') $dop=$dop;
   			
    	$goods=msq("SELECT * FROM `".$this->goods_iface->getSetting('table')."`".$dop);
    	
    	while ($good=msr($goods))
    	if ($good['id']>0)
    	{
    		$all_sklad=msr(msq("SELECT sum(kol) as sum_kol FROM `".$this->getSetting('table')."` WHERE good_id=".$good['id']));
    		
    		/* Продажи */
    		$q="SELECT sum(goods.kol) as sum_kol FROM `".$this->basket_iface->getSetting('table')."` basket, `site_site_order_goods` goods WHERE goods.order_id=basket.id and `status_id`<>4 and `good_id`=".$good['id'];
    		$all_sale=msr(msq($q));
    		$total=$all_sklad['sum_kol']-floor($all_sale['sum_kol']);
    		
    		if ($good['kol']!=floor($total) || $good['popular']!=$all_sale['sum_kol'])
    		msq("UPDATE `".$this->goods_iface->getSetting('table')."` SET `kol`='".floor($total)."', `popular`='".floor($all_sale['sum_kol'])."' WHERE `id`=".$good['id']);
    		
    		
    		if ($good['is_size']==1) $this->updateSkladParam('size', $good);
    		if ($good['is_color']==1) $this->updateSkladParam('color', $good);
    	}
   }
   function updateSkladParam($par_name, $good)
   {
   		$params=msq("SELECT * FROM `site_site_goods_".$par_name."` WHERE `good_id`=".$good['id']);

   		
   		while ($par=msr($params))
   		{
   			$all_sklad=msr(msq("SELECT sum(kol) as sum_kol FROM `".$this->getSetting('table')."` WHERE good_id=".$good['id']." and `".$par_name."_id`=".$par['id']));

   			/* Продажи */
   			$q="SELECT sum(goods.kol) as sum_kol FROM `".$this->basket_iface->getSetting('table')."` basket, `site_site_order_goods` goods WHERE goods.order_id=basket.id and `status_id`<>4 and `".$par_name."_id`=".$par['id'];
   			$all_sale=msr(msq($q));
   			$total=$all_sklad['sum_kol']-floor($all_sale['sum_kol']);
   			
   			
   			if ($par['kol']!=floor($total))
   			msq("UPDATE `site_site_goods_".$par_name."` SET `kol`='".floor($total)."' WHERE `id`=".$par['id']);
   			
   		}
   }
   function getTypeItems($good, $table, $label, $select_name, $value_only=false) {
   		
   	 	if ($value_only) $return=array();
   		else $return='';
   		
   		$return=array();
   		
   		if ($good['id']>0)
   		{
   			$items=msq("SELECT * FROM `$table` WHERE `good_id`=".$good['id']." ORDER BY `precedence`");
   			while ($item=msr($items))
   			{
   				if ($value_only) $return[$item['id']]=$item['name'];
   				else 
   				$return.='<option value="'.$item['id'].'">'.$item['name'].'</option>';
   			}
   		}
   		
   		if (!$value_only)
   		{
   			if ($return!='')
   			$return='<div class="clear"></div><div style="z-index: 10; width:180px;" class="place"><label>'.$label.' *</label><div class="input"><select name="'.$select_name.'"><option value="-1">&nbsp;</option>'.$return.'</select></div></div><div class="clear"></div>';
   			else $return='<h2>Ошибка. Необходимо либо отлючить у товара параметр "'.$label.'", либо доавить параметры "'.$label.'" у товара</h2>';
   		}
   		
   		return $return;
   }
   function drawAddEdit(){
   	global $CDDataSet,$SiteSections, $multiple_editor;
   	$section = $SiteSections->get($this->getSetting('section'));
   
   	$SectionPattern = new $section['pattern'];
   	$Iface = $SectionPattern->init(array('section'=>$section['id'],'mode'=>$delepmentmode,'isservice'=>0));

   	$init_pattern=$Iface->getSetting('pattern');
   	if ($this->editor_cnt>1)
   	{
   		$multiple_editor=true;
   		?><script type="text/javascript" src="/js/tinymce/tinymce.js"></script><?
   	}
   
   			$pub = $this->getPub($_GET['pub']);
   			
   			$pub['id'] = floor($pub['id']);
   			?>
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
									$('.select_table').hide();
									$('.pub_form').show();
									$('.pub_form').show();
									$('.btn').show();

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
			   				            }
			   				        });
								});

	               			    $(document).on('click','.choose_anoth', function() {
									$('.selected_good').html('');
									$('.select_table').show();
									$('.pub_form').hide();
									$('.pub_form').hide();
									$('.btn').hide();
	               	
	               			        return false;
	               			    });

	               			   <?
	               			   if (!$pub['id']>0){
	               			   ?>
	               				$('#editform').submit(function(){
									var error='';

									if (!$('[name=kol]').val()>0) error+='Заполните поле количество\r\n';
									if (!$('[name=good_id]').val()>0) error+='Выберите товар\r\n';
									if ($("[name=size_id]").is(":visible") && $('[name=size_id]').val()<=0) error+='Заполните поле размер\r\n';
									if ($("[name=color_id]").is(":visible") && $('[name=color_id]').val()<=0) error+='Заполните поле цвет\r\n';
									
									if (error!='') alert(error);
									else
									{
										$.ajax({
				   				            type: "POST",
				   				            url: "/inc/cclasses/CCSklad.php",
				   				            data: "action=ajax_save&good_id="+$('[name=good_id]').val()+'&size_id='+$('[name=size_id]').val()+'&color_id='+$('[name=color_id]').val()+'&kol='+$('[name=kol]').val()+'&comment='+$('[name=comment]').val(),
				   				            dataType: 'json',
				   				            success: function(data){

												var redirect=true;
												if ($("[name=color_id]").val()>0 || $("[name=size_id]").val()) redirect=false;

				   				            	$('[name=kol]').val('');
				   				            	$("[name=size_id]").val('-1');
				   				            	$("[name=color_id]").val('-1');
				   				            	$("[name=comment]").val('');

				   				            	$(".ajax_comment").append('<div>'+data.comment+'</div>');
				   				            	$(".ajax_comment").css('border', '3px solid #CCCCCC');
				   				            	$(".ajax_comment").css('padding', '20px 30px');
				   				            	$(".ajax_comment").css('margin-bottom', '20px');

												if (redirect)
				   				            	window.location.replace("/manage/control/contents/?section=<?=$_GET['section'] ?>");
				   				            	
				   				            }
				   				        });
									}
									return false;
		               			});
	               			   <?} ?>


	               				
						 });
						</script>
						<?
						if ($_REQUEST['good_id']>0)
						$good=$this->goods_iface->getPub($_REQUEST['good_id']);
						elseif ($pub['good_id']>0)
						$good=$this->goods_iface->getPub($pub['good_id']);
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
   
   										?>
   										<style>
   										button.ui-multiselect {width: 400px;!important}
   										</style>
   										<div class="ajax_comment">
   										</div>
   										<div style="border: 3px solid #CCCCCC; padding: 20px 30px">
					   						<h2>Выберите товар:</h2>
					   						<div class="selected_good">
					   							<?
					   							if ($good['id']>0)
					   							{
					   								?>
					   								<div class="selected_good"><h3>Выбран товар: <?=$good['name'] ?></h3><br><a onclick="return false" class="choose_anoth" href="#">Выбрать другой</a></div>
					   							<?} ?>
					   						</div>
					   						
					   						<div style="display: <?=$good['id']>0 ? 'none' : 'block' ?>" class="select_table_container">
											<table style="width: 100%;" class="select_table">
												<tr>
													<td style="width: 50%; vertical-align: top;">
														<div class="place" style="z-index: 10; width: 300px;">
															<select multiple="multiple" id="categs" name="categs[]">
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
														<div style="z-index: 10; width: 100%; margin-top:8px" class="place">
															<span class="input">
																<input type="text" value="" maxlength="20" name="search_name" placeholder="поиск по названию....">
															</span>
														</div>
														<div class="goods">
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
											</div>
					   					</div>
					   					<div class="clear"></div>
					   					<?
					   					$pub[]=$_POST;
					   					?>
					   					<div class="pub_form" style="display: <?=$good['id']>0 ? 'block' : 'none' ?>">
								   					<!--  <div class="place" style="z-index: 10; width:180px;">
								   						<label>Тип операции</label>
														<?print getSelectSinonim('type_id', $this->getSetting('types') ,$pub['type_id']);?>
								   					</div>-->
								   					<div class="type_items">
								   					<?
								   					if ($good['id']>0){
								   						?>
								   						<input type="hidden" value="<?=$good['id'] ?>" name="good_id">
								   						<?
								   					}
								   					
								   					if ($pub['size_id']>0)
								   					{
								   						$sizes=$this->getTypeItems($good, 'site_site_goods_size', 'Размер', 'size_id', true);
								   						?>
								   						<div class="clear"></div>
								   						<div class="place" style="z-index: 10; width:180px;">
								   						<label>Размер *</label><?
								   						print getSelectSinonim('size_id', $sizes ,$pub['size_id']);
								   						?>
								   						<input type="hidden" value="1" name="is_size">
								   						</div><?
								   					}
								   					if ($pub['color_id']>0)
								   					{
								   						$sizes=$this->getTypeItems($good, 'site_site_goods_color', 'Цвет', 'color_id', true);
								   						?>
   													   	<div class="clear"></div>
   													   	<div class="place" style="z-index: 10; width:180px;">
   													   	<label>Цвет *</label><?
   													   	print getSelectSinonim('color_id', $sizes ,$pub['color_id']);
   													   	?>
   													   	<input type="hidden" value="1" name="is_color">
   													   	</div><?
   													}
								   					?>
								   					</div>
			   										
			   										
			   										<?
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
			   
			   
			   		                                }
   
   
   		                       						?>
   		                       		
   
   
   		                        </table>
   		                        <div class="place btn" style="display: <?=$good['id']>0 ? 'block' : 'none' ?>">
   		                                <span style="float: right;">
   		                                	<input class="button big" type="submit" name="editform" value="<?=($pub['id']>0)?'Сохранить изменения':'Добавить'?>"/>
   		                                </span>
   		                        </div>
   
   		                        <span class="clear"></span>
   		                        </form>
   		                </div>
   		                <?
   		                if (isset($this->Settings['settings_personal']['reklama']))
   		                include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/reklama/add_pattern.php");
   	}


}
if ($_POST['action']=='ajax_sklad' || $_POST['action']=='ajax_getparamskol' || $_POST['action']=='ajax_save')
{
	$sklad=new CCSklad();
	$sklad->ajax();

}
?>