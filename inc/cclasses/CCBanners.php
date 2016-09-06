<?

class CCBanners extends VirtualContent
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
   function getBanners($place_id=0, $str_usl='', $order_by=''){

   	$retval = array();

   	$usl=' WHERE `show`=1 and `date_start`<=date(NOW()) and `date_end`>=date(NOW())';

    if ($place_id>0) $usl.=" and `place_id`='$place_id'";

   	$q = "SELECT * FROM `".$this->getSetting('table')."`".$usl.$str_usl;

   	$count = msq($q);
   	$count = @mysql_num_rows($count);


   	$q = msq($q." ".$order_by);


   	while ($r = msr($q)) $retval[] = $r;

   	return $retval;
   }
   function printBanner($id){
   		global $Storage;

   		$banner=$this->getPub($id);
   		$image=$Storage->getFile($banner['image']);

   		if ($banner['id']>0)
   		{

	   		if ($banner['code']!='')
	   		{

	   			$place=$this->getPlace($banner['place_id']);

	   			if ($place['settings']['imgw']!='') $style.='width: '.$place['settings']['imgw'].'px;';
	   			if ($place['settings']['imgh']!='') $style.='height: '.$place['settings']['imgh'].'px;';
	   			if ($banner['border']==1) $style.='border: 1px solid #CCCCCC;';
	   			if ($style!='') $style='style="'.$style.'"';


	   			print '<div '.$style.' class="banner banner_code" id="'.$banner['id'].'">'.$banner['code'].'</div>';

	   		}
	   		elseif ($image['id']>0)
	   		{

	   			switch ($image['ext']){
		   		case 'swf':
		   			$style='';
		   			$place=$this->getPlace($banner['place_id']);

		   			if ($place['settings']['imgw']!='') $style.='width: '.$place['settings']['imgw'].'px;';
		   			if ($place['settings']['imgh']!='') $style.='height: '.$place['settings']['imgh'].'px;';
		   			if ($banner['border']==1) $style.='border: 1px solid #CCCCCC;';
		   			if ($style!='') $style='style="'.$style.'cursor:pointer;position: relative;"';
		   			/* echo '<a href="/redirect.php?banner_id='.$banner['id'].'&session="><object><embed width="'.(($place['settings']['imgw']>0) ? $place['settings']['imgw'].'px' : '100%').'" height="'.(($place['settings']['imgw']>0) ? $place['settings']['imgh'].'px' : '100%').'" src="'.$image['path'].'" allowscriptaccess="always" menu="true" loop="true" play="true" wmode="opaque" quality="best" type="application/x-shockwave-flash"></object></a>'; */

		   			if ($banner['inner_href']!=1)
		   			$href_code='<a width="'.(($place['settings']['imgw']>0) ? $place['settings']['imgw'].'px' : '100%').'" height="'.(($place['settings']['imgh']>0) ? $place['settings']['imgh'].'px' : '100%').'" class="overlay" href="/redirect.php?banner_id='.$banner['id'].'" id="'.$banner['id'].'"></a>';

		   			echo '<div '.$style.' id="'.$banner['id'].'" class="banner'.(($banner['inner_href']==1) ? ' inner_href':'').'">
		   					'.$href_code.'
		   					<object height="100%" width="100%">
									<param name="movie" value="'.$image['path'].'">
									<param name="allowScriptAccess" value="sameDomain" />
									<param name="allowFullScreen" value="false" />
									<param name="allowFullScreen" value="false" />
								  	<param name="quality" value="high" />
									<param name="wmode" value="transparent"/>
      							<embed src="'.$image['path'].'" width="'.(($place['settings']['imgw']>0) ? $place['settings']['imgw'] : '100%').'" height="'.(($place['settings']['imgh']>0) ? $place['settings']['imgh'].'px' : '100%').'" quality="high" bgcolor="#999999" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"   quality="high" bgcolor="#999999" wmode="transparent"></embed>
    					  	</object>

      					  </div>';
		   			break;

		   		default:
		   			echo '<div class="banner"><a href="/redirect.php?banner_id='.$banner['id'].'"><img '.(($banner['border']==1) ? 'style="border: 1px solid #CCCCCC"' : '').'" src="'.$image['path'].'"></a></div>';
		   			break;
		   		}

	   		}

	   		$this->addStat($id, 'show');
   		}
   }
   function addStat($id, $type){

		if ($type=='' || !$id>0) return false;

		if ($id>0)
		$today_stat=msr(msq("SELECT * FROM `".$this->getSetting('table_stat')."` WHERE date(`date`)=date(NOW()) and `banner_id`='".$id."'"));


		if (!$today_stat['id']>0)
		{
			msq("INSERT INTO `".$this->getSetting('table_stat')."` (`banner_id`, `date`) VALUES ('".$id."', date(NOW()))");
			$today_stat=msr(msq("SELECT * FROM `".$this->getSetting('table_stat')."` WHERE date(`date`)=date(NOW()) and `banner_id`='".$id."'"));
		}

		if ($today_stat['id']>0)
		msq("UPDATE `".$this->getSetting('table_stat')."` SET `".$type."`=".($today_stat[$type]+1)." WHERE id=".$today_stat['id']);



		if ($type=='click')
		{

			if (!$_SESSION['im_unique_'.$today_stat['id']]>0)
			{
				msq("UPDATE `".$this->getSetting('table_stat')."` SET `unique`=".($today_stat['unique']+1)." WHERE id=".$today_stat['id']);
	   			$_SESSION['im_unique_'.$today_stat['id']]=1;
			}

		}


		return;
   }
   function drawAddEdit(){
   	global $CDDataSet,$SiteSections, $MySqlObject;
   	$section = $SiteSections->get($this->getSetting('section'));

   	$SectionPattern = new $section['pattern'];
   	$Iface = $SectionPattern->init(array('section'=>$section['id'],'mode'=>$delepmentmode,'isservice'=>0));

   	$sectionPlace = $SiteSections->get($SiteSections->getIdByPath('/products/'.$section['path'].'/places/'));
   	$Pattern = new $sectionPlace['pattern'];
   	$IfacePlace = $Pattern->init(array('section'=>$sectionPlace['id']));

   	$pub = $this->getPub($_GET['pub']);
   	$pub['id'] = floor($pub['id']);

    if ($_GET['pub']=='new')
   	$place=$IfacePlace->getPub($_GET['place_id']);
   	else $place=$IfacePlace->getPub($pub['place_id']);

   	$init_pattern=$Iface->getSetting('pattern');




   	?>   		        <script>
   		                $(function() {

							$("#date_start").change(function() {
								if ($('#date_end').val()=='')
								{

									var val=$('#date_start').val();
									var dt = val.toString().split('.');
		   		                	var date = new Date(dt[2], dt[1], dt[0]);
		   		                	date.setMonth(date.getMonth());

		   		                	$( "#date_end" ).datepicker( "setDate", date );


								}

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
   		                                <h2><?=$place['name'] ?></h2>
   		                                <input type="hidden" name="editformpost" value="1">
   		                                <input type="hidden" name="place_id" value="<?=$place['id'] ?>">
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

   		                                		if ($dt['name']=='image')
   		                                		{
   		                                			$tface->setSetting('settings', array_merge($this->explode($place['settings']), $dt['settings']));
   		                                			$tface->setSetting('use_str_settings','1');

   		                                		}



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
   		                        <div class="place">
   		                                <span style="float: right;">
   		                                	<input class="button big" type="submit" name="editform" value="<?=($pub['id']>0)?'Сохранить изменения':'Добавить'?>"/>
   		                                </span>
   		                        </div>

   		                        <span class="clear"></span>
   		                        </form>

   		                        <?
   		                        $MySqlConnect = new MySqlConnect;

   		                        if (!isset($_POST['stat_end']))
   		                        {
   		                        	$default_date_end=msr(msq("SELECT max(date) as dt FROM `".$this->getSetting('table_stat')."` WHERE `banner_id`=".$pub['id']));
   		                        	$default_date_end=msdfromdb($default_date_end['dt']);
   		                        }
   		                        else $default_date_end=$_POST['stat_end'];

   		                        if (!isset($_POST['stat_start']))
   		                        {
   		                        	$default_date_start=msr(msq("SELECT min(date) as dt FROM `".$this->getSetting('table_stat')."` WHERE `banner_id`=".$pub['id']));
   		                        	$default_date_start=msdfromdb($default_date_start['dt']);
   		                        }
   		                        else $default_date_start=$_POST['stat_start'];



   		                        if ($_POST['search_stat']==1)
   		                        {
   		                        	 $default_limit=0;
   		                        	 if ($_POST['stat_start']!='') $usl.=" and `date`>='".$MySqlObject->dateToDB($_POST['stat_start'])."'";
   		                        	 if ($_POST['stat_end']!='') $usl.=" and `date`<='".$MySqlObject->dateToDB($_POST['stat_end'])."'";

   		                        	 ?>
   		                        	 <script>
									$(function() {
										var elem=$('#statistic');
										destination = elem.offset().top-50;
										$("html, body").animate({scrollTop:destination},"slow");
									});

									</script>
   		                        	 <?
   		                        }
   		                        if ($pub['id']>0)
   		                        {

   		                        	$stat=msr(msq("SELECT count(*) as cnt FROM `".$this->getSetting('table_stat')."` WHERE `banner_id`=".$pub['id'].$usl));

   		                        	if ($stat['cnt']>0)
   		                        	{
   		                        		?>
   		                        		<div class="hr"><hr/></div>
   		                        		<h2>Статистика</h2>

				<script type="text/javascript">
							$(function(){

								$("#stat_start").datepicker({
									showOn: "both",
									buttonImage: "/pics/editor/calendar.gif",
									buttonImageOnly: true
								});

								$("#stat_end").datepicker({
										showOn: "both",
										buttonImage: "/pics/editor/calendar.gif",
										buttonImageOnly: true
								});
							});
							</script>

							<form id="statistic" name="statistic" method="POST" enctype="multipart/form-data">
								<input type="hidden" name="search_stat" value="1">
								<div class="place" id="stat_start_calendar" style="width: 158px;">
									<label>Дата с</label>
									<div><input  id="stat_start" name="stat_start" type="text" style="width: 100px; float: left;" value="<?=$default_date_start ?>"/></div>
								</div>
								<div class="place" id="stat_end_calendar" style="width: 158px;">
									<label>Дата по</label>
									<div><input  id="stat_end" name="stat_end" type="text" style="width: 100px; float: left;" value="<?=$default_date_end ?>"/></div>
								</div>
								<div style="width: 200px;margin-left: 2%;" class="place">
			   						<label>&nbsp;</label>
			   						<span class="forbutton">
			   							<span>
			   								<input type="submit" value="Показать за выбранный период" class="button">
			   							</span>
			   						</span>
			   					</div>
			   					<div style="width: 8%;margin-left: 2%;" class="place">
			   						<label>&nbsp;</label>
			   						<span class="forbutton">
			   							<span>
			   								<input type="submit" value="Выгрузить в xls" class="button" name="export">
			   							</span>
			   						</span>
			   					</div>
							</form>

   		                        		<table class="table-content stat">
   		                        		<tr>
   		                        			<th class="t_center">Дата</th>
   		                        			<th>Показов</th>
   		                        			<th>Кликов</th>
   		                        			<th>Уникальных посетителей</th>
   		                        		</tr>
   		                        		<?
   		                        		$stat=msq("SELECT * FROM `".$this->getSetting('table_stat')."` WHERE `banner_id`=".$pub['id'].$usl." ORDER BY `date` DESC ".(($_POST['search_stat']=='')? ' LIMIT 0, 200':''));

   		                        		while ($st=msr($stat))
   		                        		{
   		                        			?>
   		                        			<tr>
   		                        				<td class="t_center"><?=$MySqlConnect->dateFromDBDot($st['date'])?></td>
   		                        				<td><?=$st['show']?></td>
   		                        				<td><?=$st['click']?></td>
   		                        				<td><?=$st['unique']?></td>
   		                        			</tr>
   		                        			<?
   		                        		}
   		                        		?>
   		                        		</table>
   		                        		<?

   		                        	/* 	if (isset($_POST['export']))
   		                        		$this->ExportXls($stat); */

	   		                        	if (isset($_POST['export']))
	   		                        	{
	   		                        		mysql_data_seek($stat, 0);
	   		                        		$this->ExportStatXls($stat);
	   		                        	}

   		                        	}

   		                        }
   		                        ?>
   		                </div>
   		                <?
   }
   function drawPubsList(){
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
   					if (count($dataset['types'])>0)
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
   					<div class="place" style="z-index: 10; width: 10%;">
   						<label>Место</label>
   						<?
   						if (!isset($this->search_show))
   						$this->search_show='-1';


   						$placelist=getSprValuesEx('/products/'.$section['path'].'/places/', '', false);


   						$places=array('-1'=>'');
   						foreach ($placelist as $li)
   						$places[$li['id']]=$li['name'];

   						print getSelectSinonim('search_place_id',$places,$_POST['search_place_id'],true);

   						$search_fields_cnt++;
   						?>
   					</div>
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
   				if (in_array($sf,$editlink_double)) $href=array('<a href="/manage/control/contents/?section='.$section['id'].'&pub='.$pub['id'].'" title="Редактировать">', '</a>');
   				?>
   				<td <?=$set['settings']['list_class']!='' ? 'class="'.$set['settings']['list_class'].'"' : ''?>>
   					<?=$href[0]?><?=$CDDataType->get_view_field($dataset['types'][$sf],$pub[$sf]);?><?=$href[1]?>
   				</td>
   			<?}?>


   			<!-- Редактировать, Удалить -->
   			<td class="t_minwidth">
   			<?
   			print $placelist[$pub['place_id']]['name'];
   			?>
   			</td>
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
   function getPlace($id){
   		global $SiteSections, $CDDataSet, $CDDataType;

   		$section = $SiteSections->get($this->getSetting('section'));

		$place_iface=getiface('/products/'.$section['path'].'/places/');

   		$place=$place_iface->getPub($id);

   		$place['settings']=$this->explode($place['settings']);

   		return $place;
   }
   function placeSelect(){
   		global $SiteSections, $CDDataSet, $CDDataType;

   		$dataset = $this->getSetting('dataface');

   		$section = $SiteSections->get($this->getSetting('section'));

   		$sectionPlace = $SiteSections->get($SiteSections->getIdByPath('/products/'.$section['path'].'/places/'));

   		if (!$sectionPlace['id']>0)
   		$error='<h2>Необходимо создать подраздел "places" с описанием мест для баннеров. Разделы должны быть в закладке /products/ на первом уровне</h2>';

   	?>
   	 <div id="content" class="forms">
   	 	 <?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
   	 	 <h2>Выберите место для размещения баннера</h2>
   	 	 <?
   	 		if ($error!='') print $error;
   	 		else
   	 		{
   	 				$Pattern = new $sectionPlace['pattern'];
					$IfacePlace = $Pattern->init(array('section'=>$sectionPlace['id']));

   	 				$list=$IfacePlace->getList(-1);

   	 				if (count($list)==0)
   	 				print '<h2>Необходимо добавить в подраздел "places" описание мест для баннеров</h2>';
   	 				else {

   	 				$places=array('-1'=>'');
   	 				foreach ($list as $li)
   	 				$places[$li['id']]=$li['name'];

   	 				?>
   	 				<script>
   	 				$(function() {
   	 					$("[name=place_id]" ).change(function() {
							var base_url='/manage/control/contents/?section=<?=$section['id'] ?>&pub=new';

   	 					 	if ($(this).val()>0)
   	 					 	{
   	   	 						$('#next').show();
   	   	 						$('#next a').attr('href', base_url+'&place_id='+$(this).val());
   	 					 	}
   	 					 	else
   	   	 					$('#next').hide();
   	 					});
					});
   	 				</script>
		   	 		<div class="place">
						<label>Место для размещения <span class="important">*</span></label>
						<?
						print getSelectSinonim('place_id',$places,'');
						?>
					</div>
					<span class="clear"></span>
					<div class="place" id="next" style="display: none;">
						<a class="button big" style="float: right;" href="">Продолжить</a>
					</div>
   	 				<?
   	 				}


   	 		}
   	 	 ?>
   	 </div>
   	<?
   }
   function start(){
		global $CDDataSet;

		$dataset = $CDDataSet->get($this->getSetting('dataset'));
		$imagestorage = $this->getSetting('imagestorage');

		if ($_GET['pub']>0) $pub = $this->getPub(floor($_GET['pub']));

		foreach ($dataset['types'] as $k=>$dt){
			$tface = new $dt['type'];
			$tface->init(array('name'=>$dt['name'],'value'=>$pub[$dt['name']], 'uid'=>floor($pub['id']),'imagestorage'=>floor($imagestorage['id']),'description'=>$dt['description'],'imagestorage'=>floor($imagestorage['id']),'theme'=>$dataset['name'].'_'.$this->getSetting('section'), 'theme'=>$dataset['name'].'_'.$this->getSetting('section'),'rubric'=>$dt['name'],'settings'=>$dt['settings']));
			$dataset['types'][$k]['face'] = $tface;
		}


		if (isset($_GET['pub']))
		{

			if ($_GET['pub']=='new' && !$_GET['place_id']>0)
			{
				$this->placeSelect();
				return;
			}

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

   	if (!$_POST['place_id']>0) $errors[]='Не выбрано место для баннера';
   	if ($_POST['date_start']==$_POST['date_end']) $errors[]='Дата начала и окончания размещения совпадают';
    if ($_POST['code']=='' && $_POST['href']=='' && $_POST['inner_href']!='on') $errors[]='Не заполнено поле ссылка';


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
   		{
   			$tface = $dt['face'];
   			$tface->init(array('uid'=>floor($pub['id'])));
   			$tface->postSave();
   			$update.= (($update!='')?',':'').$tface->getUpdateSQL((int)$_GET['section']);
   			$dataset['types'][$k]['face'] = $tface;
   		}


   		msq("UPDATE `".$this->getSetting('table')."` SET ".$update.", `place_id`='".$_POST['place_id']."' WHERE `id`='".$pub['id']."'");
   		msq("UPDATE `".$this->getSetting('table')."` SET `banner_precedence`=0 WHERE `banner_precedence` is NULL");


   		WriteLog($pub['id'], (($_GET['pub']=='new') ? 'добавление':'редактирование').' записи', '','','',$this->getSetting('section'));
   	}

   	$this->setSetting('dataface',$dataset);
   	return $errors;
   }


}
?>