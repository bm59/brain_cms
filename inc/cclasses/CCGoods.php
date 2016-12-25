<?

class CCGoods extends VirtualContent
{

	function init($settings){
        		global $SiteSections;
                VirtualContent::init($settings);

                $section = $SiteSections->get($this->getSetting('section'));
                $this->Settings['settings_personal']=$section['settings_personal'];

                $SiteSettings = new SiteSettings;
                $SiteSettings->init();
                $this->Settings['onpage'] = $this->Settings['settings_personal']['on_page']>0 ? $section['settings_personal']['on_page'] : 20;



                $this->like_array=array();/* Где нет в названии "name", но нужен поиск по like - search_href*/
                $this->not_like_array=array();/* Где есть в названии "name", но не нужен поиск по like*/
                $this->no_auto=array(); /*Не обрабатывать переменные, ручная обработка*/

                /*заменить на пустое в названиях переменны при поиске*/
                $this->field_tr=array('search_'=>'','_from'=>'','_to'=>'');

                /*подмена названий*/
                $this->field_change=array();


 				$this->getSearch();


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
   
   					if ($this->sqlstr =='') $sql_pref=' WHERE ';  /*!!!Заменить на WHERE если нет других условий*/
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
   			
   			if (!isset($_POST['searchaction']) && $_GET['categs']!='') $_POST['categs']=explode(',', $_GET['categs']);
   			
   					
   			if (is_array($_POST['categs']))
   			{
   				$cat_usl='';
   				if ($this->sqlstr =='') $sql_pref=' WHERE ';  /*!!!Заменить на WHERE если нет других условий*/
   				else $sql_pref=' and ';
   				
   				foreach ($_POST['categs'] as $cat) {
   					$cat_usl.=(($cat_usl!='') ? ' or ':'')."(`categs` like '%,".$cat.",%')";
   				}
   				
   				$cat_usl='('.$cat_usl.')';
   				
   				$this->sqlstr.=$sql_pref.$cat_usl;

   			}
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
   		                        <p class="impfields">Поля, отмеченные знаком «<span class="important">*</span>», обязательные для заполнения.</p>
   		                        <form id="editform" name="editform" action="<?=$_SERVER['REQUEST_URI']?>" method="POST" enctype="multipart/form-data">
	   		                        <div class="place">
	   		                                <span style="float: right;">
	   		                                	<input class="button big" type="submit" name="editform" value="<?=($pub['id']>0)?'Сохранить изменения':'Добавить'?>"/>
	   		                                </span>
	   		                        </div>
	   		                        <input type="hidden" name="editformpost" value="1">
   		                                
   		                   <?
   		                   
   		                   if (isset($pub['is_size']))
   		                   {             	
   		                   		$this->createSubSection(
   		                   			'size_template',
   		                   			'Шаблоны размеров',
   		                   			array
   		                   			(
   		                   				array('dataset'=>$CDDataSet->GetIdByName('rubricator'), 'name'=>'name', 'description'=>'Наименование', 'type'=>'CDText', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(255)'),
   		                   				array('dataset'=>$CDDataSet->GetIdByName('rubricator'), 'name'=>'description', 'description'=>'Описание', 'type'=>'CDText', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(255)'),
   		                   			),
   		                   			array
   		                   			(
   		                   					array('name'=>'Европа', 'parent_id'=>0, 'show'=>1),
   		                   						array('name'=>'XXS', 'description'=>'40 российский размер', 'parent_id'=>1, 'show'=>1),
   		                   						array('name'=>'XS', 'description'=>'42 российский размер', 'parent_id'=>1, 'show'=>1),
   		                   						array('name'=>'S', 'description'=>'44 российский размер', 'parent_id'=>1, 'show'=>1),
   		                   						array('name'=>'M', 'description'=>'46 российский размер', 'parent_id'=>1, 'show'=>1),
   		                   						array('name'=>'L', 'description'=>'48 российский размер', 'parent_id'=>1, 'show'=>1),
   		                   						array('name'=>'XL', 'description'=>'50 российский размер', 'parent_id'=>1, 'show'=>1),
   		                   						array('name'=>'XXL', 'description'=>'52 российский размер', 'parent_id'=>1, 'show'=>1),
   		                   						array('name'=>'XXXL', 'description'=>'54 российский размер', 'parent_id'=>1, 'show'=>1),
   		                   					
   		                   			),
   		                   			'|onoff|show_id|default_order=ORDER BY `id` ASC|',
   		                   			'',
   		                   			'PRubricator'
   		                   			);
   		                   }
   		                   
   		                   if (isset($pub['is_color']))
   		                   {
   		                   	$this->createSubSection(
   		                   			'color_template',
   		                   			'Шаблоны цветов',
   		                   			array
   		                   			(
   		                   					array('dataset'=>$CDDataSet->GetIdByName('rubricator'), 'name'=>'name', 'description'=>'Наименование', 'type'=>'CDText', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(255)'),
   		                   					array('dataset'=>$CDDataSet->GetIdByName('rubricator'), 'name'=>'color_image', 'description'=>'Изображение', 'type'=>'CDImage', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'BIGINT(20)'),
   		                   					array('dataset'=>$CDDataSet->GetIdByName('rubricator'), 'name'=>'color_code', 'description'=>'Код цвета (#ff0000)', 'type'=>'CDText', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(255)'),
   		                   			),
   		                   			array
   		                   			(
   		                   					array('name'=>'Базовые цвета', 'parent_id'=>0, 'show'=>1),
   		                   					array('name'=>'Белый', 'color_code'=>'#FFFFFF', 'parent_id'=>1, 'show'=>1),
   		                   					array('name'=>'Черный', 'color_code'=>'#000000', 'parent_id'=>1, 'show'=>1),
   		                   					array('name'=>'Красный', 'color_code'=>'#FF0000', 'parent_id'=>1, 'show'=>1),
   		                   					array('name'=>'Синий', 'color_code'=>'#00008B', 'parent_id'=>1, 'show'=>1),
   		                   					array('name'=>'Зеленый', 'color_code'=>'#008000', 'parent_id'=>1, 'show'=>1),
   		                   					array('name'=>'Желтый', 'color_code'=>'#FFFF00', 'parent_id'=>1, 'show'=>1)
   		                   					 
   		                   			),
   		                   			'|onoff|show_id|default_order=ORDER BY `id` ASC|',
   		                   			'',
   		                   			'PRubricator'
   		                   			);
   		                   }
   		                   $categ_section=$SiteSections->getIdByPath($SiteSections->getPath($section['id']).'categs/');
   		                   if (!$categ_section['id']>0)
   		                   print '<h2>Необходимо добавить дочерний раздел "categs" с категориями товаров</h2>';
   		                   else 
   		                   $categ_iface=getIface($SiteSections->getPath($section['id']).'categs/');
   		                   		

   		                   $values=array();
   		                   $parents=msq("SELECT * FROM `".$categ_iface->getSetting('table')."` WHERE `show`=1 and `parent_id`=0 ORDER BY `precedence`");
   		                   while($r=msr($parents))
   		                   {
   		                   		$values[]=array('level'=>0, 'id'=>$r['id'], 'name'=>$r['name']);
   		                   		$childs=msq("SELECT * FROM `".$categ_iface->getSetting('table')."` WHERE `show`=1 and `parent_id`=".$r['id']." ORDER BY `precedence`");
   		                   		while($ch=msr($childs))
   		                   		{
   		                   			$values[]=array('level'=>1, 'id'=>$ch['id'], 'name'=>$ch['name'], 'parent'=>$ch['parent_id']);
   		                   			
   		                   			$childs2=msq("SELECT * FROM `".$categ_iface->getSetting('table')."` WHERE `show`=1 and `parent_id`=".$ch['id']." ORDER BY `precedence`");
   		                   			
   		                   			while($ch2=msr($childs2))
   		                   			{
   		                   				$values[]=array('level'=>2, 'id'=>$ch2['id'], 'name'=>$ch2['name'], 'parent'=>$ch2['parent_id']);
   		                   			}
   		                   		}
   		                   }
   		                   
   		                   if (count($_POST['categs'])>0)
   		                   {
	   		                   	$pub['categs']='';
	   		                   	foreach ($_POST['categs'] as $cat)
	   		                   	$pub['categs'].=($pub['categs']!='' ? ',':'').$cat;
	   		                   	
	   		                   	$pub['categs']=','.$pub['categs'].',';
   		                   }
   		                   
   		                   ?>
   		<div class="place" style="z-index: 9999; width:100%; margin-right: 1%;">
						<label>Раздел</label>
						<select multiple="multiple" id="categs" name="categs[]">
					        <?
						 	if (!is_array($cur_sections)) $cur_sections=array();
						 	
						 	

						 		
					        foreach ($values as $val)
					        {
					        	?><option <?=strpos($pub['categs'],','.$val['id'].',')!==false ? 'selected' :''?> class="<?=(isset($val['level']) ? 'level_'.$val['level'] :'') ?><?=($val['level']>0 ? ' parent_'.$val['parent'].' himself_parent_'.$val['id'] : ' himself_parent_'.$val['id']) ?>" value="<?=$val['id']?>" <?=((in_array($k, $cur_sections)) ? 'checked="checked"':'')?>><?=$val['name']?></option><?
					        }
					        ?>
					    </select>
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

				                    		//$("#categs").multiselect('close');
				                    		

			                    	}
								});

	               				
						 });
						</script>
				</span>
			</div>
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
   		    									
   		    									if ($dt['name']=='is_size')
   		    									{
   		    										$_SESSION['template_action']='size_edit';
   		    										include($_SERVER['DOCUMENT_ROOT']."/inc/imag/TSize.php");
   		    									}
   
   		    									if ($dt['name']=='is_color')
   		    									{
   		    										$_SESSION['template_action']='color_edit';
   		    										include($_SERVER['DOCUMENT_ROOT']."/inc/imag/TColor.php");
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
   		                </div>
   		                <?
   		                if (isset($this->Settings['settings_personal']['reklama']))
   		                include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/reklama/add_pattern.php");
   	}
   	function GetCategNames ($ids='', $all_categs)
   	{
   
   		if ($ids=='') return;
   		
   		$return='';
   		
   		$ids=clear_array_empty(explode(',', $ids));
   		foreach ($ids as $id)
   		{
   			$return.='<nobr>'.(($return=='') ? '':',<br/>').$all_categs[$id].'</nobr>';
   		}
   		
   		return $return;
   	}
   	function PrintIcon($pub, $field_name)
   	{
   		global $icons;
   		
   		if ($pub[$field_name]!='')
   		{
   			print '<div><div><img src="'.$icons[$field_name]['path'].'" alt="'.$icons[$field_name]['descr'].'" title="'.$icons[$field_name]['descr'].'"/></div><div>'.stripcslashes($pub[$field_name]).'</div></div>';	
   		}
   	}
   	function SetMainCateg($pub){
   		
   		if ($pub['main_categ']>0) return $pub['main_categ'];
   		$categs=clear_array_empty(explode(',', $pub['categs']));
   		foreach ($categs as $cat)
   		$cat_txt.=($cat_txt=='' ? '':', ').$cat;
   		
   		$main_cat=msr(msq("SELECT * FROM `site_site_rubricator_rubricator_25` WHERE `parent_id`=0 and `show`=1 and `id` in ($cat_txt) LIMIT 1"));
   		
   		if ($main_cat['id']>0)
   		{
   			msq("UPDATE `".$this->getSetting('table')."` SET `main_categ_id`=".$main_cat['id']." WHERE `id`=".$pub['id']);
   			return $main_cat['id'];
   		}
   		
   		
   	}
   	function GetCategPath($pub){
   		global $SiteSections;
   		
   		$section = $SiteSections->get($this->getSetting('section'));
   		
   		$categ_section=$SiteSections->getIdByPath($SiteSections->getPath($section['id']).'categs/');
   		if (!$categ_section['id']>0)
   		print '<h2>Необходимо добавить дочерний раздел "categs" с категориями товаров</h2>';
   		else 
   		$categ_iface=getIface($SiteSections->getPath($section['id']).'categs/');
   		 
   		$categs=clear_array_empty(explode(',', $pub['categs']));

   		if (count($categs)==1)
   		{
   			$main=msr(msq("SELECT * FROM `".$categ_iface->getSetting('table')."` WHERE `parent_id`=0 and `show`=1 and `id`=".$categs[0]." LIMIT 1"));
   			
   			$return['nav']='<a href="/catalog/'.$main['pseudolink'].'/">'.$main['name'].'</a>';
   			$return['title']=' - '.$main['name'];
   			
   			return $return;
   		}
   		else 
   		{
   			$return='';
   			$main=msr(msq("SELECT * FROM `".$categ_iface->getSetting('table')."` WHERE `parent_id`=0 and `show`=1 and `id` in (".implode(",", $categs).") LIMIT 1"));
   			
   			if ($main['id']>0)
   			$return['nav'].='<a href="/catalog/'.$main['pseudolink'].'/">'.$main['name'].'</a>';
   			
   			
   			foreach ($categs as $cat)
   			if ($cat!=$main['id']);
   			{
   				$sub=msr(msq("SELECT * FROM `".$categ_iface->getSetting('table')."` WHERE `show`=1 and `id`=".$cat));
   				
   				if ($sub['id']>0)
   				{
   					$return['nav'].='<img src="/pics/arrows/arrow_nav.png"><a href="/catalog/'.$sub['pseudolink'].'/">'.$sub['name'].'</a>';
   					$return['title'].=' - '.$sub['name'];
   				}
   			}
   			
   			if ($main['id']>0)
   			$return['title'].=' - '.$main['name'];
   			
   			return $return;
   		}	 
   			 
   	}
   	function PrintGood($pub, $view_type='')
   	{
   		global $Storage, $categs, $basket_items, $comments;
   		
   		$images=clear_array_empty(explode('|', $pub['gallery']));
   		
   		$pub['kol']=$basket_items[$pub['id']]['kol'];
   		
   		if (!$pub['main_categ']>0) $pub['main_categ']=$this->SetMainCateg($pub);
   		
   		if ($images[0]>0)
   		$image=$Storage->getFile($images[0]);
   		
   		$pub['price']=number_format($pub['price'] , 0, ' ', ' ');
   		
   		$sklad_iface=getIface('/sitecontent/sklad/');
   		
   		
   		if ($pub['kol']>0)
   		$price='<img src="/pics/basket.png"> '.$pub['kol'].'<span> шт.</span>';
   		switch ($view_type) {
   			case 'tile': default:
   				?>
   			   				   		<li id="<?=$pub['id'] ?>" <?=$pub['kol']>0 ? 'class="added"' :'' ?>>
   										<div class="brd">
   			   				   				<div class="img"><a href="/goods/<?=$pub['id'] ?>/"><img src="<?=$image['path'] ?>" alt=""/></a></div>
   			   				   				<?if ($pub['hit']==1) {?><div class="hit"></div><?} ?>
   			   				   			</div>
   			   				   			
   			   				   			<div class="name"><a href="/goods/<?=$pub['id'] ?>/"><?=$pub['name'] ?></a></div>
   										<div class="buy">
   											<input name="good_kol" type="hidden" value="<?=floor($pub['kol']) ?>">
   											
   											<div class="price"><?=$pub['price'] ?><small> руб.</small></div>
   											
					   						<div class="basket_actions">
					   		   					<a style="display: <?=$pub['kol']>0 ? 'block' :'none' ?>" class="basket_clear" title="Удалить из корзины">&nbsp;</a>
					   		   					<a class="mybtn<?=$pub['kol']>0 ? ' active' :'' ?>"><?=$pub['kol']>0 ? '<img src="/pics/basket_mini.png" alt=""/>&nbsp;:&nbsp;'.$pub['kol'] :'купить' ?></a>
					   		   				</div>
   										</div>
   			   				   		</li>
   			   				   		<?	
   			   				
   			break;
   			case 'item_page':
   				?>
							<script>
							$(function() {
							
								$(".img_prev a").click(function() {
									$(".img_prev a").removeClass('active');
									$(this).addClass('active');
									$('.main_image img').attr('src', $(this).find('img').attr('src'));
									$('.main_image a').attr('href', $(this).find('img').attr('src'));
							
									var data_id=$(this).attr('data-id');
									$('.img_prev a').each(function() {
										if ($(this).attr('data-id')==data_id)
										$(this).attr('rel','');
										else $(this).attr('rel','lightbox[1]');
							
									});
									
									return false;
								});
							
								
							
							
							});
							</script>
   				   			
   				   			<ul class="item"><li id="<?=$pub['id'] ?>">
   				   			<div class="good_left">
   				   				<div class="main_image">
   				   					
   				   					<a rel="lightbox[1]" href="<?=$image['path'] ?>">
   				   						<?if ($pub['hit']==1) {?><div class="hit big"></div><?} ?>
   				   						<img src="<?=$image['path'] ?>" alt=""/>
   				   					</a>
   				   				</div>
   				   				<?
   				   				
   				   				$img_arr=clear_array_empty(explode('|', $pub['gallery']));
   				   				if (count($img_arr)>1)
   				   				{
   				   					?><div class="img_prev"><?
   				   								$j=0;
   				   								foreach ($img_arr as $img)
   				   								{
   				   									$image=$Storage->getFile($img);
   				   									?><a rel="lightbox[1]" href="<?=$image['path'] ?>" data-id="<?=$j?>" <?=$j==0 ? 'class="main_prev active"':'' ?>><img src="<?=$image['path'] ?>" data-image="<?=$image['path'] ?>"/></a><?
   				   									
   				   									$j++;
   				   								}
   				   					?></div><?
   				   				}
   				   				?>
   					   			<br/><div style="padding-top:20px; float: left; width: 100%;"></div><br/>

   				   			</div>
   							<div class="good_right">
   					   			
   					   			
   					   			<h1><?=$pub['name'] ?></h1>
   					   			<div class="buy">
   					   				<input name="good_kol" type="hidden" value="<?=floor($pub['kol']) ?>">
   					   				
   					   				<div class="price"><?=$pub['price'] ?><img src="/pics/rouble_big.png"/></div>
   					   				<div class="add">
   					   					<a class="mybtn big <?=$pub['kol']>0 ? 'active' :'' ?>"><?=$pub['kol']>0 ? '<img src="/pics/basket_mini.png" alt="">: '.$pub['kol'] :'в корзину ' ?></a>
   				   						<a style="display: <?=$pub['kol']>0 ? 'block' :'none' ?>" class="basket_clear big" title="Удалить из корзины"></a>
   				   						
   				   						<!--  <a style="display: <?=$pub['kol']>0 ? 'block' :'none' ?>" href="/basket/" class="mybtn basket_go" title="Перейти в корзину"><img src="/pics/basket_go.png"/></a>-->
   					   				</div>
   					   			</div>
   					   			
   					   			<div class="params">
   					   			<?
   					   				if ($pub['is_color']==1)
   					   				{
   					   					$colors=msq("SELECT * FROM `site_site_goods_color` WHERE `good_id`=".$pub['id']." and `kol`>0 ORDER BY `precedence`");
   					   					if (mysql_num_rows($colors)>0)
   					   					{
	   					   					?>
			   					   			<script type="text/javascript">
			   					   				$(function() {
				   					   				
													$(".colors a").click(function() 
													{

														if ($(this).hasClass("active"))
														{
															$(this).removeClass("active");
															$('.size a').show();
														}
														else 
														{
															$(this).parent().find("a").removeClass("active");
															$(this).addClass("active");

															
			   					   							<?if ($sklad_iface && $pub['is_color']){ ?>
															$.ajax({
														           type: "POST",
														           url: "/inc/cclasses/CCSklad.php",
														           data: "action=ajax_getparamskol&good_id=<?=$pub['id'] ?>&inp_name=color&inp_val="+$(this).data('id')+"&out_name=size",
														           dataType: 'json',
														           success: function(data){
															          var ids=data.ids.split(',');

															          $('.sizes a').hide();

															          for (index = 0; index < ids.length; ++index) {
															        	  $('.sizes [data-id='+ids[index]+']').show();
															          }
														           }
														   });
														   <?} ?>
														}
														
														
													});	
												});
			   					   				</script>
	   					   					<div class="colors">
	   					   						<div class="title">Цвет:</div>
		   					   					<?
	   					   						while ($col=msr($colors))
		   					   					{
		   					   						$img='';
		   					   						$style='';
		   					   						
		   					   						$class='';
		   					   						if ($basket_items[$pub['id']]['color_id']==$col['id']) $class='class="active"';
		   					   							
		   					   						
		   					   						if ($col['color_code']!='') $style='style="background-color: '.$col['color_code'].'"';	
		   					   						
		   					   						if ($col['color_image']>0)
		   					   						{
		   					   							$img=$image=$Storage->getFile($col['color_image']);
		   					   							$img='<img src="'.$img['path'].'">';
		   					   						}
		   					   						
		   					   						?><a <?=$class ?> onclick="return false;" href="#" data-id="<?=$col['id'] ?>" title="<?=$col['name'] ?>" <?=$style ?>><?=$img ?></a><?
		   					   					}
		   					   					?>
	   					   					</div>
	   					   					<?
   					   					}
   					   				}

   		   					   		if ($pub['is_size']==1)
   					   				{
   					   					
   					   					$sizes=msq("SELECT * FROM `site_site_goods_size` WHERE `good_id`=".$pub['id']." and `kol`>0 ORDER BY `precedence`");
   					   					
   					   					if (mysql_num_rows($sizes)>0)
   					   					{
	   					   						
		   					   				?>
			   					   				<script type="text/javascript">
			   					   				$(function() {
													$(".sizes a").click(function() 
													{

														if ($(this).hasClass("active"))
														{
															$(this).removeClass("active");
															$('.colors a').show();
														}
														else 
														{
															$(this).parent().find("a").removeClass("active");
															$(this).addClass("active");

															
			   					   							<?if ($sklad_iface && $pub['is_color']){ ?>
															$.ajax({
														           type: "POST",
														           url: "/inc/cclasses/CCSklad.php",
														           data: "action=ajax_getparamskol&good_id=<?=$pub['id'] ?>&inp_name=size&inp_val="+$(this).data('id')+"&out_name=color",
														           dataType: 'json',
														           success: function(data){
															          var ids=data.ids.split(',');

															          $('.colors a').hide();

															          for (index = 0; index < ids.length; ++index) {
															        	  $('.colors [data-id='+ids[index]+']').show();
															          }
														           }
														   });
														   <?} ?>
														}
														
														
													});	
												});
			   					   				</script>
	   					   					
	   					   					<div class="sizes">
	   					   						<div class="title">Размер:</div>
		   					   					<?
	   					   						while ($sz=msr($sizes))
		   					   					{
		   					   						$class='';
		   					   						if ($basket_items[$pub['id']]['size_id']==$sz['id']) $class='class="active"';
		   					   						
		   					   						?><a <?=$class ?> href="#" data-id="<?=$sz['id'] ?>" onclick="return false;" title="<?=$sz['name'] ?><?=$sz['comment']!='' ? ' ('.$sz['comment'].')' :''?>"><?=$sz['name'] ?></a><?
		   					   					}
		   					   					?>
	   					   					</div>
	   					   					<?
   					   					}
   					   				}   					   				   					   				
   								
   					   			?>
   					   			</div>
   					   				<?
   					   				if ($pub['anons']!='') $text=$pub['anons'];
   					   				if ($pub['text']!='') $text=$pub['text'];
   					   				
   						   			if ($text!='') {?><div class="text styled" style="float: none;"><br/><br/><div class="title">Описание:</div><?=$text ?></div><?} ?>
  
  								<input type="hidden" name="good_kol" value="<?=floor($pub['kol']) ?>">
   					   			<div class="clear"></div>
   				   			</div>
   				   			<div class="clear"></div>
   				   			</li></ul>
   				
   				   			   		<?	
   			break;

   		}
   		
   	}
   	function drawPubsList($param=''){
   		global $SiteSections, $CDDataSet, $CDDataType;
   	
   		$this->generateMeta('name');
   	
   		$dataset = $this->getSetting('dataface');
   	
   		$section = $SiteSections->get($this->getSetting('section'));
   		
   		$sklad_section=$SiteSections->getByPattern('PSklad');

   	
   		if (isset($_POST['showsave'])){
   			foreach ($_POST as $k=>$v){
   				if (preg_match('|^prec\_[0-9]+$|',$k)){
   					$p = preg_replace('|^prec\_([0-9]+)$|','\\1',$k);
   					msq("UPDATE `".$this->getSetting('table')."` SET `precedence`='".floor($_POST['prec_'.$p])."' WHERE `id`='$p'");
   				}
   			}
   			$this->updatePrecedence();
   		}
   		
   		$all_categs=getSprValues('/sitecontent/goods/categs/');
   		
   		$categ_section=$SiteSections->getIdByPath($SiteSections->getPath($section['id']).'categs/');
   		if (!$categ_section['id']>0)
   		print '<h2>Необходимо добавить дочерний раздел "categs" с категориями товаров</h2>';
   		else
   		$categ_iface=getIface($SiteSections->getPath($section['id']).'categs/');
   				
   		$values=array();
   		$parents=msq("SELECT * FROM `".$categ_iface->getSetting('table')."` WHERE `show`=1 and `parent_id`=0 ORDER BY `precedence`");
   		while($r=msr($parents))
   		{
   			$values[]=array('level'=>0, 'id'=>$r['id'], 'name'=>$r['name']);
   			$childs=msq("SELECT * FROM `".$categ_iface->getSetting('table')."` WHERE `show`=1 and `parent_id`=".$r['id']." ORDER BY `precedence`");
   			while($ch=msr($childs))
   			{
   				$values[]=array('level'=>1, 'id'=>$ch['id'], 'name'=>$ch['name'], 'parent'=>$ch['parent_id']);
   				 
   				$childs2=msq("SELECT * FROM `".$categ_iface->getSetting('table')."` WHERE `show`=1 and `parent_id`=".$ch['id']." ORDER BY `precedence`");
   				 
   				while($ch2=msr($childs2))
   				{
   					$values[]=array('level'=>2, 'id'=>$ch2['id'], 'name'=>$ch2['name'], 'parent'=>$ch2['parent_id']);
   				}
   			}
   		}
   		?>
   			    	<div id="content" class="forms">
   			    	<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
   			        <form name="searchform" action="" method="POST">
   			        	<input type="hidden" name="searchaction">
		   			    
		   			    <div class="place" style="z-index: 9999; width:100%; margin-right: 1%;">
								<label>Раздел</label>
								<?
								if (!is_array($cur_sections)) $cur_sections=array();
								if (!isset($_POST['searchaction'])) $_POST['categs']='';
								
								if (!isset($_POST['searchaction']) && $_GET['categs']!='') $_POST['categs']=explode(',', $_GET['categs']);
								?>
								<select multiple="multiple" id="categs" name="categs[]">
							        <?
							        foreach ($values as $val)
							        {
							        	?><option <?=in_array($val['id'], $_POST['categs']) ? 'selected' :''?> class="<?=(isset($val['level']) ? 'level_'.$val['level'] :'') ?><?=($val['level']>0 ? ' parent_'.$val['parent'].' himself_parent_'.$val['id'] : ' himself_parent_'.$val['id']) ?>" value="<?=$val['id']?>" <?=((in_array($k, $cur_sections)) ? 'checked="checked"':'')?>><?=$val['name']?></option><?
							        }
							        ?>
							    </select>
			                    <script type="text/javascript">
			                     $(function() 
			    	             {
		
			                    	 $("#categs").multiselect({
			                    		   selectedText: "# из # выбрано",
			                    		   noneSelectedText: "Выберите раздел!",
			                    		   checkAllText: "Выбрать все", 
			                    		   uncheckAllText: "Очистить"
			                    		});	
				
			               				
								 });
								</script>
						</span>
					</div>
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
   			
   			$table_th[]=array('name'=>'', 'description'=>'Раздел', 'class'=>'t_32width');
   	
   	
   	
   			foreach($show_fields as $sf){
   				$set=$dataset['types'][$sf]['face']->Settings;
   				$table_th[]=array('name'=>$set['name'], 'description'=>$set['description'], 'class'=>$set['settings']['list_class']);
   			}
   	
   			if ($sklad_section['id']>0)
   			$table_th[]=array('name'=>'', 'description'=>'Кол-во', 'class'=>'t_minwidth');
   			
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
   	
   				<td>
   				<?=$this->GetCategNames($pub['categs'],$all_categs) ?>
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
   				
   				<?if ($sklad_section['id']>0){ ?>
   				<td>
   					<?=floor($pub['kol']) ?>
   					<?
   					if ($pub['is_size']==1 || $pub['is_color']==1) $this->printParamsKol(array('size', 'color'), $pub);
   					?>
   				</td>
   				<?} ?>
   	
   	
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
   			                                
   			                                
   			                                if (is_array($_POST['categs'])) $this->urlstr.='&categs='.implode(',', $_POST['categs']);
   			                               
   			                                
   			                                
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
   			
   	function printParamsKol ($params, $pub)
   	{
   		global $SiteSections;
   		$sklad_section=$SiteSections->getByPattern('PSklad');
   		if ($sklad_section['id']>0)
   		{
   			$this->sklad_iface=getIface($SiteSections->getPath($sklad_section['id']));
   		}
   		
   		$basket_section=$SiteSections->getByPattern('PBasket');
   		if (!$basket_section['id']>0)
   		print '<h2>Не найден раздел с заказами</h2>';
   				
   		if ($basket_section['id']>0)
   		$this->basket_iface=getIface($SiteSections->getPath($basket_section['id']));
   		
   		if ($sklad_section['id']>0 && $basket_section['id']>0)
   		{
	   		$is_params=array();
	   		$wheres=array();
	   		$captions=array();
	   		foreach ($params as $par)
	   		{
	   			if ($pub['is_'.$par]==1) $is_params[]=$par.'_id';
	   		}
	   		
	
	   		
	   		if (count($is_params)>0)
	   		{
	   			/* Наборы параметров из склада */
	   			$store=msq("SELECT sum(kol), good_id, ".implode(',', $is_params)." FROM `".$this->sklad_iface->getSetting('table')."` WHERE `good_id`=".$pub['id']." GROUP BY ".implode(',', $is_params));
	   			
	   			while ($st=msr($store))
	   			{
	   				$cur='';
	   				$cap='';
	   				
	   				foreach ($is_params as $isp)
	   				{
	   					$caption=msr(msq("SELECT * FROM `site_site_goods_".str_replace('_id', '', $isp)."` WHERE `id`=".$st[$isp]));
	   					
	   					$cur.=" and `".$isp."`='".$st[$isp]."'";
	   					$cap.=(($cap!='') ? '; ':'').$caption['name'];
	   				}
	
	   				$wheres[]= $cur;
	   				$captions[]= $cap;
	   			} 
	   			
	   			$i=0;
	   			foreach ($wheres as $wh)
	   			{
	   				$all_sklad=msr(msq("SELECT sum(kol) as sum_kol FROM `".$this->sklad_iface->getSetting('table')."` WHERE good_id=".$pub['id'].$wh));
	   				
	   				$q="SELECT sum(goods.kol) as sum_kol FROM `".$this->basket_iface->getSetting('table')."` basket, `site_site_order_goods` goods WHERE goods.order_id=basket.id and `status_id`<>4 and `good_id`=".$pub['id'].$wh;
	   				/* print $q; */
	   				
	   				$all_sale=msr(msq($q));
	   				$total=$all_sklad['sum_kol']-floor($all_sale['sum_kol']);
	   				
	   				print '<div class="left"><nobr>['.$captions[$i].']: '.($all_sklad['sum_kol']-floor($all_sale['sum_kol'])).'</nobr></div>';
	   				$i++;
	   				
	   			}
	   			/* print_r($captions); */
	   		}
   		}
   		
/*    		$params=msq("SELECT * FROM `site_site_goods_".$parnam."` WHERE `good_id`=".$pub['id']." ORDER BY `precedence`");
   		while ($par=msr($params))
   		if ($par['kol']>0)
   		{
   			$return.='<div>'.$par['name'].': '.$par['kol'].'</div>';
   		}
   		if	($return!='')
   		print '<div style="margin-top: 5px; text-align: left; padding: 5px 3px; border: 1px solid #CCCCCC">'.$return.'</div>'; */
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
   		if (!count($_POST['categs'])>0) $errors[]='Заполните поле «Раздел»';

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

   			$cat_txt='';
   			foreach ($_POST['categs'] as $cat)
   			{
   				$cat_txt.=($cat_txt=='' ? ',' : '').$cat.',';
   			}
   			
   			$cat_txt=", `categs`='$cat_txt'";
   	
   			msq("UPDATE `".$this->getSetting('table')."` SET ".$update.$cat_txt." WHERE `id`='".$pub['id']."'");
   			
   			$_SESSION['template_action']='size_save';
   			$_SESSION['template_pub']=$pub;
   			include($_SERVER['DOCUMENT_ROOT']."/inc/imag/TSize.php");
   			
   			$_SESSION['template_action']='color_save';
   			$_SESSION['template_pub']=$pub;
   			include($_SERVER['DOCUMENT_ROOT']."/inc/imag/TColor.php");
   			
   			
   			WriteLog($pub['id'], (($_GET['pub']=='new') ? 'добавление':'редактирование').' записи', '','','',$this->getSetting('section'));
   		}
   	
   		$this->setSetting('dataface',$dataset);
   		return $errors;
   	}


}
?>