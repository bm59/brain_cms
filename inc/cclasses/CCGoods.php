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
   		                                <input type="hidden" name="editformpost" value="1">
   		                                
   		                   <?
   		                   $values=array();
   		                   $parents=msq("SELECT * FROM `site_site_rubricator_rubricator_25` WHERE `show`=1 and `parent_id`=0 ORDER BY `precedence`");
   		                   while($r=msr($parents))
   		                   {
   		                   		$values[]=array('level'=>0, 'id'=>$r['id'], 'name'=>$r['name']);
   		                   		$childs=msq("SELECT * FROM `site_site_rubricator_rubricator_25` WHERE `show`=1 and `parent_id`=".$r['id']." ORDER BY `precedence`");
   		                   		while($ch=msr($childs))
   		                   		{
   		                   			$values[]=array('level'=>1, 'id'=>$ch['id'], 'name'=>$ch['name'], 'parent'=>$ch['parent_id']);
   		                   		}
   		                   }
   		                   
   		                   ?>
   		<div class="place" style="z-index: 9999; width:100%; margin-right: 1%;">
						<label>Разделы</label>
						<select multiple="multiple" id="categs" name="categs[]">
					        <?

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
			                    			//$(this).parents('UL').find('.himself_'+match[0]+' input').attr("aria-selected", "false");
			                    			//$(this).parents('UL').find('.himself_'+match[0]+' input').attr('checked','');
		                    				//$(this).parents('UL').find('.himself_'+match[0]+' input').trigger('click');
			                    			//$(this).parents('UL').find('.himself_'+match[0]+' input').attr("aria-selected", "true");

			                    			if(!$(this).parents('UL').find('.himself_'+match[0]+' input').prop('checked'))
			                    			$(this).parents('UL').find('.himself_'+match[0]+' input').trigger('click');
		                    			}
			                    		else
			                    		{
			                    			if($(this).parents('UL').find('.himself_'+match[0]+' input').prop('checked'))
				                    		$(this).parents('UL').find('.himself_'+match[0]+' input').trigger('click');		
			                    		}

			                    		//$("#categs").multiselect("refresh"); 
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
   	function PrintGood($pub, $view_type='')
   	{
   		global $Storage, $categs, $basket_items, $comments;
   		
   		$images=clear_array_empty(explode('|', $pub['gallery']));
   		
   		$pub['kol']=$basket_items[$pub['id']]['kol'];
   		
   		if (!$pub['main_categ']>0) $pub['main_categ']=$this->SetMainCateg($pub);
   		
   		if ($images[0]>0)
   		$image=$Storage->getFile($images[0]);
   		
   		switch ($view_type) {
   			case 'tile_big':
   				?>
   		   		<li id="<?=$pub['id'] ?>">
   		   			<?if ($pub['is_hit']==1){?><div class="good_alert hit" title="Хит продаж">Хит</div><? }?>
   		   			<?if ($pub['is_action']==1){?><div class="good_alert action" title="Акция">Акция</div><? }?>
   		   			<?if ($pub['is_new']==1){?><div class="good_alert new" title="Акция">Новинка</div><? }?>
   		   			<div class="main_image"><a href="/main/katalog-produkczii/<?=$categs[$pub['main_categ']]['pseudolink'] ?>/item/<?=$pub['id'] ?>/"><img src="<?=$image['path'] ?>" alt=""/></a></div>
   		   			<div class="name"><a href="/main/katalog-produkczii/<?=$categs[$pub['main_categ']]['pseudolink'] ?>/item/<?=$pub['id'] ?>/"><?=$pub['name'] ?></a></div>
   		   			<div class="buy">
   		   				<div class="price"><?=$pub['price'] ?><small>&nbsp;руб</small></div>
   		   				<div>
   		   					<a class="mybtn <?=$pub['kol']>0 ? 'active' :'' ?>"><?=$pub['kol']>0 ? '<img src="/pics/basket_mini.png" alt=""/>&nbsp;:&nbsp;'.$pub['kol'] :'купить' ?></a>
   							<a style="display: <?=$pub['kol']>0 ? 'block' :'none' ?>" class="basket_clear" title="Удалить из корзины">&nbsp;</a>
   		   				</div>
   		   			</div>
   		   			<div class="clear"></div>
   		   			<div class="anons"><?=$pub['anons'] ?></div>
   		   			<div class="icons">
   		   				<?
   		   				$this->PrintIcon($pub, 'icon_weight');
   		   				$this->PrintIcon($pub, 'icon_radius');
   		   				$this->PrintIcon($pub, 'icon_age');
   		   				$this->PrintIcon($pub, 'icon_maxweight');
   		   				$this->PrintIcon($pub, 'icon_height');
   		   				?>
   		   			</div>
   		   			<input type="hidden" name="good_kol" value="<?=floor($pub['kol']) ?>">
		   		   	<?
		   			if ($pub['comment_text']!='') {?>
		   			<div class="comment" <?=$pub['comment_color']!='' ? 'style="color:'.$pub['comment_color'].'"':'' ?>><?=$pub['comment_text'] ?></div>
		   			<?} ?>
   		   			<div class="clear"></div>
   		   			
   		   		</li>
   		   		<?	
   			break;
   			case 'list':
   				?>
   			   		   		<li id="<?=$pub['id'] ?>">
   			   		   			<?if ($pub['is_hit']==1){?><div class="good_alert hit" title="Хит продаж">Хит</div><? }?>
   			   		   			<?if ($pub['is_action']==1){?><div class="good_alert action" title="Акция">Акция</div><? }?>
   			   		   			<?if ($pub['is_new']==1){?><div class="good_alert new" title="Акция">Новинка</div><? }?>
   			   		   			<div class="main_image"><a href="/main/katalog-produkczii/<?=$categs[$pub['main_categ']]['pseudolink'] ?>/item/<?=$pub['id'] ?>/"><img src="<?=$image['path'] ?>" alt=""/></a></div>
   			   		   			<div class="descr">
   			   		   				<div class="name"><a href="/main/katalog-produkczii/<?=$categs[$pub['main_categ']]['pseudolink'] ?>/item/<?=$pub['id'] ?>/"><?=$pub['name'] ?></a></div>
   			   		   				<div class="clear"></div>
   			   		   				<div class="anons"><?=$pub['anons'] ?></div>
   			   		   				<div class="icons">
   			   		   				<?
   			   		   				$this->PrintIcon($pub, 'icon_weight');
   			   		   				$this->PrintIcon($pub, 'icon_radius');
   			   		   				$this->PrintIcon($pub, 'icon_age');
   			   		   				$this->PrintIcon($pub, 'icon_maxweight');
   			   		   				$this->PrintIcon($pub, 'icon_height');
   			   		   				?>
   			   		   				</div>
   			   		   			</div>
   			   		   			<div class="buy">
   			   		   				<div class="price"><?=$pub['price'] ?><small>&nbsp;руб</small></div>
   			   		   				<div class="clear"></div>
   			   		   				<div>
   			   		   					<a class="mybtn <?=$pub['kol']>0 ? 'active' :'' ?>"><?=$pub['kol']>0 ? '<img src="/pics/basket_mini.png" alt=""/>&nbsp;:&nbsp;'.$pub['kol'] :'купить' ?></a>
   			   							<a style="display: <?=$pub['kol']>0 ? 'block' :'none' ?>" class="basket_clear" title="Удалить из корзины">&nbsp;</a>
   			   		   				</div>
   			   		   				<div class="clear"></div>
   			   		   				<?
	   					   			if ($pub['comment_text']!='') {?>
	   					   			<div class="comment" <?=$pub['comment_color']!='' ? 'style="color:'.$pub['comment_color'].'"':'' ?>><?=$pub['comment_text'] ?></div>
	   					   			<?} ?>
   			   		   			</div>
   			   		   			<div class="clear"></div>
   			   		   			
   			   		   			
   			   		   			<input type="hidden" name="good_kol" value="<?=floor($pub['kol']) ?>">
   					   		   	
   			   		   			<div class="clear"></div>
   			   		   			
   			   		   		</li>
   			   		   		<?	
   			break;
   			
   			case 'item_page':
   				?>
   				   			<ul class="item"><li id="<?=$pub['id'] ?>">
   				   			<div class="good_left">
   				   				<div class="main_image"><a rel="lightbox[1]" href="<?=$image['path'] ?>"><img src="<?=$image['path'] ?>" alt=""/></a></div>
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
   				   				<?if ($pub['is_hit']==1){?><div class="good_alert hit" title="Хит продаж">Хит</div><? }?>
   					   			<?if ($pub['is_action']==1){?><div class="good_alert action" title="Акция">Акция</div><? }?>
   					   			<?if ($pub['is_new']==1){?><div class="good_alert new" title="Акция">Новинка</div><? }?>
   					   			<br/><div style="padding-top:20px; float: left; width: 100%;"></div><br/>
   					   			<div class="hr" style="margin: 10px 0;"></div>
   					   			<?
   					   			$cnt=0;
   					   			if ($pub['buywith_ids']!='')
   					   			{
   					   				$buywith_ids=clear_array_empty(explode(',', $pub['buywith_ids']));
   					   				if (count($buywith_ids)>0)
   					   				{
   					   					?>
   					   					<script src="/js/jquery.jcarousel.js" language="JavaScript" type="text/javascript"></script>
   					   					<script src="/js/jcarousel_similar.js" language="JavaScript" type="text/javascript"></script>
   					   					<h2>Сопуствующие товары:</h2>
   											<div class="similar_container">
   												<div class="slider_similar">
   														<!-- Wrapper -->
   														<div class="wrapper">
   														    <!-- Carousel -->
   														    <div class="jcarousel">
   														        <ul>
   												   					<?
   												   					foreach ($buywith_ids as $bi)
   												   					{
   												   						$cnt++;
   												   						
   												   						$good=$this->getPub(trim($bi));
   												   						$img_arr=clear_array_empty(explode('|', $good['gallery']));
   												   						$image=$Storage->getFile($img_arr[0]);
   												   						
   												   						$good['main_categ']=$this->SetMainCateg($good); 
   												   						?>
   												   						<li>	
   												   							<a href="/main/katalog-produkczii/<?=$categs[$good['main_categ']]['pseudolink'] ?>/item/<?=$good['id'] ?>/"><img src="<?=$image['path'] ?>" alt=""/></a>
   												   							<div><a href="/main/katalog-produkczii/<?=$categs[$good['main_categ']]['pseudolink'] ?>/item/<?=$good['id'] ?>/"><?=$good['name'] ?></a></div>
   												   						</li>
   												   						<?
   												   					}
   												   					
   												   					?>
   												   				 </ul> 
   							    							</div>
   													<?if ($cnt>2) {?>
   												    <a href="#" class="jcarousel-control-prev-similar"></a>
   												    <a href="#" class="jcarousel-control-next-similar"></a>
   												    <?} ?>
   							                
   												</div>
   										</div>
   									</div>
   									<div class="hr" style="margin: 10px 0;"></div><?
   							   				}
   					   			}
   					   			?>
   					   			
   					   			
   					   			<br/><div style="float: left;">
   									<!-- Put this script tag to the <head> of your page -->
   									<script type="text/javascript" src="//vk.com/js/api/openapi.js?121"></script>
   									
   									<script type="text/javascript">
   									  VK.init({apiId: 5439759, onlyWidgets: true});
   									</script>
   									
   									<!-- Put this div tag to the place, where the Comments block will be -->
   									<div id="vk_comments"></div>
   									<script type="text/javascript">
   									VK.Widgets.Comments("vk_comments", {limit: 10, width: "400", attach: "*"});
   									</script>
   					   			</div>
   				   			</div>
   							<div class="good_right">
   					   			
   					   			
   					   			<h1><?=$pub['name'] ?></h1>
   					   			<div class="buy">
   					   				<div class="price"><?=$pub['price'] ?><small>&nbsp;руб</small></div>
   					   				<div>
   					   					<a class="mybtn <?=$pub['kol']>0 ? 'active' :'' ?>"><?=$pub['kol']>0 ? '<img src="/pics/basket_mini.png" alt=""/>&nbsp;:&nbsp;'.$pub['kol'] :'купить' ?></a>
   				   						<a style="display: <?=$pub['kol']>0 ? 'block' :'none' ?>" class="basket_clear" title="Удалить из корзины">&nbsp;</a>
   					   				</div>
   					   			</div>
   					   				<?
   						   			if ($pub['comment_text']!='') {?>
   						   			<div class="comment" <?=$pub['comment_color']!='' ? 'style="color:'.$pub['comment_color'].'"':'' ?>><?=$pub['comment_text'] ?></div>
   						   			<?} ?>
   					   			<div class="text styled"><?=$pub['text']?></div>
   					   			<div class="icons">
   					   				<?
   					   				$this->PrintIcon($pub, 'icon_weight');
   					   				$this->PrintIcon($pub, 'icon_radius');
   					   				$this->PrintIcon($pub, 'icon_age');
   					   				$this->PrintIcon($pub, 'icon_maxweight');
   					   				$this->PrintIcon($pub, 'icon_height');
   					   				?>
   					   			</div>
   					   			<div class="text" style="padding-top: 20px;"><?=$pub['video']?></div>
   					   			<input type="hidden" name="good_kol" value="<?=floor($pub['kol']) ?>">
   					   			<div class="clear"></div>
   				   			</div>
   				   			<div class="clear"></div>
   				   			</li></ul>
   				
   				   			   		<?	
   			break;
   			case 'tile': default:
   				?>
   				   		<li id="<?=$pub['id'] ?>">
   				   			<?if ($pub['is_hit']==1){?><div class="good_alert hit" title="Хит продаж">Хит</div><? }?>
   				   			<?if ($pub['is_action']==1){?><div class="good_alert action" title="Акция">Акция</div><? }?>
   				   			<?if ($pub['is_new']==1){?><div class="good_alert new" title="Акция">Новинка</div><? }?>
   				   			<div class="main_image"><a href="/main/katalog-produkczii/<?=$categs[$pub['main_categ']]['pseudolink'] ?>/item/<?=$pub['id'] ?>/"><img src="<?=$image['path'] ?>" alt=""/></a></div>
   				   			<div class="name"><a href="/main/katalog-produkczii/<?=$categs[$pub['main_categ']]['pseudolink'] ?>/item/<?=$pub['id'] ?>/"><?=$pub['name'] ?></a></div>
   				   			<div class="buy">
   				   				<div class="price"><?=$pub['price'] ?><small>&nbsp;руб</small></div>
   				   				<div>
   				   					<a class="mybtn <?=$pub['kol']>0 ? 'active' :'' ?>"><?=$pub['kol']>0 ? '<img src="/pics/basket_mini.png" alt=""/>&nbsp;:&nbsp;'.$pub['kol'] :'купить' ?></a>
   				   					<a style="display: <?=$pub['kol']>0 ? 'block' :'none' ?>" class="basket_clear" title="Удалить из корзины">&nbsp;</a>
   				   				</div>
   				   			</div>
   				   			<input type="hidden" name="good_kol" value="<?=floor($pub['kol']) ?>">
   				   			<?
   				   			if ($pub['comment_text']!='') {?>
   				   			<div class="comment" <?=$pub['comment_color']!='' ? 'style="color:'.$pub['comment_color'].'"':'' ?>><?=$pub['comment_text'] ?></div>
   				   			<?} ?>
   				   			<div class="clear"></div>
   				   			
   				   		</li>
   				   		<?	
   				
   			break;
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
   		
   		$all_categs=getSprValues('/sitecontent/goods/categories/');
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
   			
   			$table_th[]=array('name'=>'', 'description'=>'Раздел', 'class'=>'t_32width');
   	
   	
   	
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
   	
   			WriteLog($pub['id'], (($_GET['pub']=='new') ? 'добавление':'редактирование').' записи', '','','',$this->getSetting('section'));
   		}
   	
   		$this->setSetting('dataface',$dataset);
   		return $errors;
   	}


}
?>