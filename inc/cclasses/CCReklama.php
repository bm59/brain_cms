<?

class CCReklama extends VirtualContent
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
   function getItem ($section_id, $item_id){
   	global $SiteSections;
   	
	   	$Section=$SiteSections->get($section_id);
	   	if ($Section['id']>0)
	   	{
	   		$Pattern = new $Section['pattern'];
	   		$Iface = $Pattern->init(array('section'=>$Section['id']));
	   	}
	   
	   	$table=$Iface->getSetting('table');
	   	
	   	$item=msr(msq("SELECT * FROM `$table` WHERE `id`=".$item_id." LIMIT 1"));
	   	
	   	return  $item;
	   	
   	
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
   				if ($sf=='section_id')
   				{
   					print '<td class="t_minwidth"><a target="_blank" href="/manage/control/contents/?section='.$pub['section_id'].'">'.$pub['section_name'].'</a></td>';	
   				}
   				elseif ($sf=='item_id')
   				{
   					print '<td class="t_minwidth"><a target="_blank" href="/manage/control/contents/?section='.$pub['section_id'].'&pub='.$pub['item_id'].'">'.$pub['item_id'].'</a></td>';
   				}
   				else 
   				{
   				
	   				$set=$dataset['types'][$sf]['face']->Settings;
	   				
	   				$href=array();
	   				if (in_array($sf,$editlink_double)) $href=array('<a href="/manage/control/contents/?section='.$section['id'].'&pub='.$pub['id'].'" title="Редактировать">', '</a>');
	   				?>
	   				<td <?=$set['settings']['list_class']!='' ? 'class="'.$set['settings']['list_class'].'"' : ''?>>
	   					<?=$href[0]?><?=$CDDataType->get_view_field($dataset['types'][$sf],$pub[$sf]);?><?=$href[1]?>
	   				</td>
	   			<?
   				}
   			
   			}?>
   			
   			
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
   	function getList($page=0, $str_usl='', $str_order_by=''){
   	
   		$retval = array();
   		 
   		$q = "SELECT  
                	rek.`id`  as id, 
                	rek.precedence as precedence, 
                	rek.`show`  as 'show', 
                	rek.name as name, 
                	sections.name as section_name, 	
                	date_start, date_end, href, section_id, item_id	
                	FROM `".$this->getSetting('table')."` rek,  `site_site_sections` sections WHERE rek.section_id=sections.id ".$this->sqlstr.$str_usl;
   		
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
   			$order_by=$this->Settings['settings_personal']['default_order']!='' ? $this->Settings['settings_personal']['default_order'] : "ORDER BY rek.`id` DESC";
   		 
   		if ($str_order_by) $order_by=$str_order_by;
   		 
   		 
   		$this->order_by=$order_by;
   		$q = msq($q." ".$order_by." LIMIT ".(($page-1)*$this->getSetting('onpage')).",".$this->getSetting('onpage'));
   		 
   		while ($r = msr($q)) $retval[] = $r;
   		 
   		return $retval;
   }
   function getPub($id){
   	$retval = array();
   	$id = floor($id); 
   	if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'")))
   
   		foreach ($r as $k=>$v)
   			$r[$k]=html_entity_decode(stripslashes($v));
   		$retval= $r;
   		return $retval;
   }
   function StuctureTable ($section_id){
	   	global $CDDataSet,$SiteSections, $CDDataType;
	   	$section = $SiteSections->get($section_id);
	   	 
	   	$SectionPattern = new $section['pattern'];
	   	$Iface = $SectionPattern->init(array('section'=>$section['id'],'mode'=>$delepmentmode,'isservice'=>0));
	   	
	
	   	$column_info=$CDDataType->get_column_info($Iface->getSetting('table'));
	   	
	   	$table_array=array($Iface->getSetting('table'));
	   	
	   	if (!isset($column_info['reklama'])) 
	   	$CDDataType->add_column(
	   			array('dataset'=>'1','name'=>'reklama', 'table_type'=>'INT(0)', 'description'=>'Рекламное место'),
	   			$table_array,
	   			' NOT NULL DEFAULT 0'
	   	);
	   	
	   	if (!isset($column_info['reklama_precedence']))
	   		$CDDataType->add_column(
	   				array('dataset'=>'1','name'=>'reklama_precedence', 'table_type'=>'INT(0)', 'description'=>'Приоритет рекламы'),
	   				$table_array,
	   				' NOT NULL DEFAULT 0'
	   	);
   	
   	
   }
   function getPubBySection($section_id, $item_id){
	   	$retval = array();
	   	$id = floor($id);
	   	if ($r = msr(msq("SELECT *, datediff(NOW(), date_start) as start, datediff(NOW(), date_end) as end FROM `".$this->getSetting('table')."` WHERE `section_id`='$section_id' and `item_id`='$item_id'")))
	   		 
	   		foreach ($r as $k=>$v)
	   			$r[$k]=html_entity_decode(stripslashes($v));
	   		$retval= $r;
	   		return $retval;
   }
   
   function drawAddEdit(){
	   	global $CDDataSet,$SiteSections;
	   	$section = $SiteSections->get($this->getSetting('section'));
	   		
	   	$SectionPattern = new $section['pattern'];
	   	$Iface = $SectionPattern->init(array('section'=>$section['id'],'mode'=>$delepmentmode,'isservice'=>0));
	   		
	   	$init_pattern=$Iface->getSetting('pattern');
	   		
	   	$pub = $this->getPub($_GET['pub']);
	   	$pub['id'] = floor($pub['id']);
	   	
	   	if ($pub['id']>0)
	   	{
	   		$_REQUEST['section_id']=$pub['section_id'];	
	   		$_REQUEST['item_id']=$pub['item_id'];
	   		
	   	}
   	?>
   		               
   		                <script>
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
   		                                        
   		                                        $section = $SiteSections->get($_REQUEST['section_id']);
   		                                        
   		                                        $item_id=$pub['item_id']>0 ? $pub['item_id'] : $_GET['item_id'];
   		    									
												if ($dt['name']=='item_id')
   		    									{
   		    										$item=$this->getItem($section['id'], $item_id);
   		    										
   		    										
   		    										?>
   		    									<input type="hidden" name="section_id" value="<?=$_REQUEST['section_id']?>">
   		    									<input type="hidden" name="item_id" value="<?=$_REQUEST['item_id']?>">
										   		<div class="place">
													<label>Раздел</label>
													<span class="input">
														<input type="text" value="<?=$section['name']?>" disabled="disabled" maxlength="255" name="item_descr">
													</span>
												</div>
										   		<div class="place">
													<label>Описание</label>
													<span class="input">
														<input type="text" value="<?=$item['name']?>" disabled="disabled" maxlength="255" name="item_descr">
													</span>
												</div>
   		    										<?
   		    									}
   		    									elseif ($dt['name']!='section_id')
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
   		               $cur_reklama=$pub;
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
   		{
   			$tface = $dt['face'];
   			$tface->init(array('uid'=>floor($pub['id'])));
   			$tface->postSave();
   			$update.= (($update!='')?',':'').$tface->getUpdateSQL((int)$_GET['section']);
   			$dataset['types'][$k]['face'] = $tface;
   		}
   
   
   		msq("UPDATE `".$this->getSetting('table')."` SET ".$update." WHERE `id`='".$pub['id']."'");
   		
   		$this->SetReklamaData($pub['id']);
   			
   			
   			
   		WriteLog($pub['id'], (($_GET['pub']=='new') ? 'добавление':'редактирование').' записи', '','','',$this->getSetting('section'));
   	}
   
   	$this->setSetting('dataface',$dataset);
   	return $errors;
   }
   function SetReklamaData($id){
	   	global $SiteSections;
	   	
	   	$active=false;
	   	$reklama_item=msr(msq("SELECT *, datediff(NOW(), date_start) as start, datediff(NOW(), date_end) as end FROM `".$this->getSetting('table')."` WHERE `id`='$id'"));
	   	
	   	$Section=$SiteSections->get($reklama_item['section_id']);
	   	if ($Section['id']>0)
	   	{
	   		$Pattern = new $Section['pattern'];
	   		$Iface = $Pattern->init(array('section'=>$Section['id']));
	   	}
	   	$table=$Iface->getSetting('table');
	   	
	   	

	    if (!$reklama_item['id']>0)
	    {
	    	msq("UPDATE `$table` SET `reklama`=0, `reklama_precedence`=0 WHERE id=".$item['id']);
	    	return false;
	    }
	    
	   	if ($reklama_item['start']>=0 && $reklama_item['end']<=0)
	   	$active=true;
	   		

	   	
	   	
	   	$item=msr(msq("SELECT * FROM `$table` WHERE id=".$reklama_item['item_id']));
	   	   	
	   	if ($active)
	   	msq("UPDATE `$table` SET `reklama`=1, `reklama_precedence`=".floor($reklama_item['precedence'])." WHERE id=".$item['id']);
	   	else 
	   	{
	   		msq("UPDATE `$table` SET `reklama`=0, `reklama_precedence`=0 WHERE id=".$item['id']);
	   	}

	  
	   	return  $item;
   }
   function deletePub($id,$updateprec = true){
   	global $SiteSections;
   	
   	$id = floor($id);
   	global $CDDataSet;
   	
   	$active=false;
   	$reklama_item=msr(msq("SELECT *, datediff(NOW(), date_start) as start, datediff(NOW(), date_end) as end FROM `".$this->getSetting('table')."` WHERE `id`='$id'"));
   	
   	$Section=$SiteSections->get($reklama_item['section_id']);
   	if ($Section['id']>0)
   	{
   		$Pattern = new $Section['pattern'];
   		$Iface = $Pattern->init(array('section'=>$Section['id']));
   	}
   	$table=$Iface->getSetting('table');
   	
   	$item=msr(msq("SELECT * FROM `$table` WHERE id=".$reklama_item['item_id']));
   	
   	msq("UPDATE `$table` SET `reklama`=0, `reklama_precedence`=0 WHERE id=".$item['id']);
   	
/*    	if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'"))){
   		$dataset = $CDDataSet->get($this->getSetting('dataset'));
   		$imagestorage = $this->getSetting('imagestorage');
   		foreach ($dataset['types'] as $dt){
   			$tface = new $dt['type'];
   			$tface->init(array('name'=>$dt['name'],'description'=>$dt['description'],'value'=>$r[$dt['name']],'imagestorage'=>floor($imagestorage['id']),'theme'=>$dataset['name'].'_'.$this->getSetting('section'),'rubric'=>$dt['name'],'uid'=>floor($r['id']),'settings'=>$dt['settings']));
   			$tface->delete();
   		}
   		msq("DELETE FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'");
   
   		WriteLog($id, 'удаление записи', '','','',$this->getSetting('section'));
   
   		if ($updateprec) $this->updatePrecedence();
   		return true;
   	}
   	return false; */
   }
   function SetReklamaAll(){

   		$list=$this->getList(-1);
	   	
	   	foreach ($list as $l)
	    $this->SetReklamaData($l['id']);
   }
   function addStat($id, $type){
   	
		if ($type=='' || !$id>0) return false;
	
		if ($id>0)
		$today_stat=msr(msq("SELECT * FROM `".$this->getSetting('table_stat')."` WHERE date(`date`)=date(NOW()) and `item_id`='".$id."'"));
	
	
		if (!$today_stat['id']>0)
		{
			msq("INSERT INTO `".$this->getSetting('table_stat')."` (`item_id`, `date`) VALUES ('".$id."', date(NOW()))");
			$today_stat=msr(msq("SELECT * FROM `".$this->getSetting('table_stat')."` WHERE date(`date`)=date(NOW()) and `item_id`='".$id."'"));
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
   function ExportXls($query){
   		global $MySqlObject;
   		
   		deleteTempFiles('/storage/xls/');
   	
   		include_once($_SERVER['DOCUMENT_ROOT']."/inc/excel/PHPExcel.php");
   		include_once($_SERVER['DOCUMENT_ROOT']."/inc/excel/PHPExcel/Writer/Excel2007.php");
   		
		// Create new PHPExcel object
		
		$objPHPExcel = new PHPExcel();
		
		
		
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('B2', iconv('windows-1251', 'utf-8', 'Дата'))
		            ->setCellValue('C2', iconv('windows-1251', 'utf-8', 'Показов'))
		            ->setCellValue('D2', iconv('windows-1251', 'utf-8', 'Кликов'))
		            ->setCellValue('E2', iconv('windows-1251', 'utf-8', 'Уникальных'));
		
		
		$styleHeader = array('font'=> array('bold'=>true), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		foreach(range('B','E') as $columnID) {
			$objPHPExcel->getActiveSheet()->getStyle($columnID.'2')->applyFromArray($styleHeader);
		}
		
		foreach(range('B','E') as $columnID) {
		    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
		        ->setAutoSize(true);
		}
		
		$objPHPExcel->getActiveSheet()->setTitle('statistics');
		
		$i=2;
		while ($r=msr($query))
		{
			$i++;
			
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('B'.$i, iconv('windows-1251', 'utf-8', $MySqlObject->dateFromDBDot($r['date'])))
			->setCellValue('C'.$i, iconv('windows-1251', 'utf-8', $r['show']))
			->setCellValue('D'.$i, iconv('windows-1251', 'utf-8', $r['click']))
			->setCellValue('E'.$i, iconv('windows-1251', 'utf-8', $r['unique']));
		}
		
		$styleArray = array(
				'borders' => array(
						'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)
				)
		);
		
		$objPHPExcel->getActiveSheet()->getStyle('B2:E'.$i)->applyFromArray($styleArray);
		
				
		// Save Excel 2007 file
		$file_name="temp_".time().".xlsx";
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save($_SERVER['DOCUMENT_ROOT']."/storage/xls/".$file_name);
		
		?>
		<script>
		window.location.href = "<?="/storage/xls/".$file_name?>";
		</script>
		<?
    }

  


}
?>