<?

class CCVoting extends VirtualContent
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

        
   }
   function drawAddEdit(){
   	global $CDDataSet,$SiteSections;
   	$section = $SiteSections->get($this->getSetting('section'));
   	$pub = $this->getPub($_GET['pub']); $pub['id'] = floor($pub['id']);
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
   		                        <?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
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
   		
   		                                }
   		                        ?>
   		
   		
   		                        </table>
   		                        <script>
   		                     		var session_id = '<?php echo session_id(); ?>';
   		                        	$(function() {
										$(".add_answer").click(function() {
											var template=$(".answers tr:last").html();

											$(".answers tr:last").after('<tr>'+template+'</tr>');

											var added=$(".answers tr:last");
											$(added).children(".td_delete").html('');
											$(added).children(".td_onoff").html('');
											var inp=$(added).find("input");
											inp.val(''); 
											var new_id=$('#sortable tr').length;
											$(inp).attr('name','answer_new'+new_id);
											
										});

										$('#sortable').sortable({
											handle: ".drag_icon"
										});

										$(document).on('click','.delete_answer', function() {

											if (confirm('Удалить запись'))
											{
												var id=$(this).attr("data-id");
										        var elem=$(this);
												if (id>0)
												{

													$.ajax({
											            type: "POST",
											            url: "/inc/site_admin/pattern/ajax_class.php",
											            data: "action=delitem&id="+id+"&table=site_site_voting_answers&session_id="+session_id,
											            dataType: 'json',
											            success: function(data){
											            	elem.children('img').attr('src', '/pics/editor/'+data.signal);
											            	elem.parents('tr').hide('slow');

													   }
											        }); 
												}
											}
											
									        return false;
									    });

									    $(document).on('click','.onoff_answer', function() {
									        var id=$(this).attr("data-id");
									        var elem=$(this);
											if (id>0)
											{
												$.ajax({
										            type: "POST",
										            url: "/inc/site_admin/pattern/ajax_class.php",
										            data: "action=onoff&id="+id+"&table=site_site_voting_answers&session_id="+session_id,
										            dataType: 'json', 
										            success: function(data){
										            	elem.children('img').attr('src', '/pics/editor/'+data.signal);
										            	$(this).parents("tr").hide();
										            }
										        }); 
											}
											
									        return false;
									    });
									});
   		                        </script>
 
	   		                        <h2>Варианты ответа:</h2>
	   		                        <a class="button add_answer" href="#" onclick="return false;"><img src="/pics/editor/add_white.png">Добавить вариант ответа</a>
	   		                        <div class="clear"></div><br/>
	   		                        <table class="table-content stat answers">
	   		                        <tbody id="sortable">
	   		                        <?
	   		                        if ($_GET['pub']!='new')
	   		                        {
	   		                        	$voting_answers=msq("SELECT * FROM `site_site_voting_answers` WHERE `voting_id`=".floor($_GET['pub'])." ORDER BY precedence");
	   		                        	$voting_answers=get_array_sql($voting_answers);
	   		                        }
	   		                        else
	   		                      	$voting_answers=array(
	   		                      			array('id'=>'new', 'show'=>'1', 'text'=>'Вариант ответа 1'),
	   		                      			array('id'=>'new2', 'show'=>'1', 'text'=>'Вариант ответа 2'),
	   		                      			array('id'=>'new3', 'show'=>'1', 'text'=>'Вариант ответа 3'),
	   		                      		
	   		                        );
	   		                        
	   		                        foreach ($voting_answers as $va)
	   		                        {
	   		                        	?>
	   		                        	<tr>
	   		                        		<td class="t_minwidth td_onoff">
		   		                        		<a href="#" onclick="return false;" class="onoff_answer" data-id="<?=$va['id']?>">
													<img id="onoff_<?=$va['id']?>" src="/pics/editor/<?=$va['show']==0 ? 'off.png' : 'on.png'?>" title="<?=$va['show']==0 ? 'Отключена' : 'Включена'?>" style="display: inline;">
												</a>
											</td>
	   		                        		<td class="t_minwidth"><img src="/pics/editor/up_down.png" class="drag_icon"></td>
	   		                        		<td><input type="text" value="<?=$va['text'] ?>" maxlength="255" name="answer_<?=$va['id'] ?>"></td>
	   		                        		<td>
	   		                        		<?
	   		                        		$settings=Array ('name'=>'image_'.$va['id'], 'value'=>$va['image'],'uid'=>1, 'imagestorage'=>'0', 'description'=>'Картинка', 'theme_'=>'voting_34', 'rubric'=>'image_'.$va['id'], 'settings' => Array () ) ;
	   		                        		$img=new CDImage();
	   		                        		$img->init($settings);
	   		                        		$img->drawEditor();
	   		                        		
	   		                        		?>
	   		                        		</td>
	   		                        		<td class="t_32width">
	   		                        		<?
	   		                        		$procent=round($va['result']*100/$pub['result']).'%';
	   		                        		print floor($va['result']).'<br>'.$procent;
	   		                        		?>
	   		                        		</td>
	   		                        		<td class="t_minwidth td_delete"><a href="#" class="delete_answer" onclick="return false;" title="Удалить" data-id="<?=$va['id']?>"><img src="/pics/editor/delete.gif" alt="Удалить"></a></td>
	   		                        	</tr>
	   		                        	<?
	   		                        	
	   		                        }
	   		                        ?>
	   		                        </tbody>
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
   	}
   	function drawPubsList(){
   		global $SiteSections, $CDDataSet, $CDDataType;
   		
   		if ($_GET['clear']>0)
   		{
   			msq("DELETE FROM `site_site_voting_log` WHERE `voting_id`=".$_GET['clear']);
   			
   		}
   	
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
   			$table_th[]=array('name'=>'', 'description'=>'Голосов', 'class'=>'t_minwidth');
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
   				
   				<td class="t_minwidth">
   					<?=floor($pub['result'])?>
   				</td>
   				<!-- Редактировать, Удалить -->
   				<td class="t_minwidth">
   					<?
   					$log_cnt=msr(msq("SELECT count(*) as `cnt` FROM `site_site_voting_log` WHERE `voting_id`=".$pub['id']));
   					
   					if ($log_cnt['cnt']>0)
   					{
   					?>
   					<a class="button txtstyle" href="/manage/control/contents/?section=<?=$section['id']?>&clear=<?=$pub['id']?>" onclick="if (!confirm('Очистить лог?')) return false;" title="Очистить лог"><img src="/pics/editor/clear.png" alt="Очистить лог"></a>
   					<?}
   					else{?>
   					<a class="button txtstyle" href="#" onclick="return false;" title="Очистить лог"><img src="/pics/editor/clear-disabled.png" alt="Очистить лог"></a>
   					<?} ?>
   				
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
   			/* print_r($_POST); */
   			$i=0;
   			
   			foreach ($_POST as $k=>$v)
   			if (preg_match('|^answer\_([a-z_A-Z_0-9]+)$|',$k,$arr))
   			{
   				if (stripos($arr[1], 'new')!==false)
   				$this->addAnswer(array('text'=>$v, 'precedence'=>$i, 'voting_id'=>$pub['id']));
   				elseif($arr[1]>0) $this->updateAnswer(array('text'=>$v, 'precedence'=>$i, 'image'=>$_POST['image_'.$arr[1]]), ' WHERE `id`='.$arr[1]);
   				
   				$i++;
   				
   			}
   				
   				
   			WriteLog($pub['id'], (($_GET['pub']=='new') ? 'добавление':'редактирование').' записи', '','','',$this->getSetting('section'));
   		}
   	
   		$this->setSetting('dataface',$dataset);
   		return $errors;
   	}
   	function addAnswer($data) {
   		$data['show']=1;
   		msq(get_insert_sql($data, 'site_site_voting_answers'));
   		return;
   			
   	}
   	function updateAnswer($data, $where) {
   		msq(get_update_sql($data, 'site_site_voting_answers', $where));
   		return;
   	
   	}
   	function addVote($answer_id, $voting_id,$server_info)
   	{
   		if ($this->addEnabled($voting_id, $server_info))
   		{
	   		$answer=$this->getAnswer($answer_id);
	   		if ($answer['id']>0 && $answer['result']>=0)
	   		{
	   			$result=floor($answer['result']+1);
	   			msq("UPDATE `site_site_voting_answers` SET `result`='".$result."' WHERE `id`=".$answer['id']." LIMIT 1");
	   			
	   			$this->updateTotal($answer['voting_id']);
	   			
	   			return $this->addLog($voting_id, $server_info);
	   		}
   		}
   		return false;
   		
   		
   	}
   	function updateTotal($voting_id)
   	{
   		if ($voting_id>0)
   		{
   			$total=msr(msq("SELECT sum(`result`) as summ FROM `site_site_voting_answers` WHERE `show`=1 and `voting_id`=$voting_id"));
   			msq("UPDATE `".$this->getSetting('table')."` SET `result`='".floor($total['summ'])."' WHERE `id`=$voting_id LIMIT 1");
   		}
   	
   	}
   	function getResultArray($voting_id)
   	{
   		
   		$return=array();
   		
   		$voting=$this->getById($voting_id);
   		if ($voting['id']>0 && $voting['result']>0)
   		{
   			$max_anr_result=msr(msq("SELECT max(result) as max_result FROM `site_site_voting_answers` WHERE `show`=1 and `voting_id`=".$voting['id']));
   			$answers=msq("SELECT * FROM `site_site_voting_answers` WHERE `voting_id`=".$voting['id']." and `show`=1 ORDER BY `result` DESC");
	   		while ($ans=msr($answers))
	   		{
	   			
	   			$ans['result_procent']=round($ans['result']*100/floor($voting['result']));
	   			$ans['result_procent_bymax']=round($ans['result']*100/floor($max_anr_result['max_result']));
	   			$return[]=$ans;
	   		}
   		}
   		
   		return $return;
   	}
   	function getTotalHtml($voting_id, $show_total)
   	{
   		$html='';
   		$voting=$this->getById($voting_id);
   		
   		$results=$this->getResultArray($voting_id);
   		foreach ($results as $res)
   		{
   			$vote_cnt='';
   			if (isset($show_total) && $res['result']>0)
   			$vote_cnt='<div class="comment">'.floor($res['result']).'</div>';
   		$html.='
   		<div class="item">
	   		<div class="col procent">'.floor($res['result_procent']).'%</div>
	   		<div class="col info">
	   		 	<div><i>'.$res['text'].'</i></div> 
	   		    <div class="clear"></div>
	   		    <div class="indicator"><div class="value" style="width: '.floor($res['result_procent_bymax']).'%"></div>'.$vote_cnt.'</div>                                   		
	   		</div>
	   		<div class="clear"></div>
   		</div>';
   		}
   		
   		if (isset($show_total))
   		$html.='<div class="center">Всего голосов: '.floor($voting['result']).'</div>';
   		$html='<div class="vote_result">'.$html.'</div>';
   		return $html;
   	}
   	function clearLog()
   	{
   	
   	}
   	function addLog($voting_id, $server_info)
   	{
   		$save_info=array(
   				'GEOIP_COUNTRY_CODE', 
   				'GEOIP_CONTINENT_CODE', 
   				'GEOIP_ADDR', 
   				'GEOIP_COUNTRY_NAME',
   				'GEOIP_REGION', 
   				'GEOIP_CITY',
   				'HTTP_USER_AGENT',
   				'HTTP_ACCEPT_LANGUAGE'
   		);
   		
   		$info='';
   		foreach ($server_info as $k=>$v)
   		if (in_array($k, $save_info) && strlen($v)<200)
   		{
   			$info.=$k.'='.$v.'<br>';		
   		}
   		
   		$q="INSERT INTO `site_site_voting_log` 
   		(`voting_id`, `ip`, `real_ip`, `time_page`, `time_vote`, `time_diff`, `page`, `server_info`)
   		VALUES
   		('".$voting_id."','".$server_info['REMOTE_ADDR']."','".$server_info['HTTP_X_REAL_IP']."','".$server_info['time_page']."', '".date('U')."', '".(date('U')-$server_info['time_page'])."', '".urldecode($server_info['page'])."', '".$info."')";
   		
   		msq($q);
   		return mslastid();
   		
   	}
   	function addEnabled($voting_id, $server_info)
   	{
   		$return=true;
   		
   		if ($server_info['HTTP_X_REAL_IP']!='') 
   		$where=" and `real_ip`='".$server_info['HTTP_X_REAL_IP']."'";
   		elseif ($server_info['REMOTE_ADDR']!='') 
   		$where.=" and `ip`='".$server_info['REMOTE_ADDR']."'";
   		
   		$log=msr(msq("SELECT * FROM `site_site_voting_log` WHERE `voting_id`=$voting_id ".$where));
   		
   		if ($log['id']>0) $return=false;
   		
   		return $return;
   	
   	}
   	function get()
   	{
   		
   		$result=msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `show`=1 ORDER BY `id` DESC LIMIT 1"));
   		
   		$answers=msq("SELECT * FROM `site_site_voting_answers` WHERE `voting_id`=".$result['id']." and `show`=1 ORDER BY `precedence`");
   		while ($ans=msr($answers))
   		{
   			$result['answers'][$ans['id']]=$ans;	
   		}
   		return $result;
   	}
   	function getById($id)
   	{
   		$result=msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `show`=1 and `id`=$id"));
   		return $result;
   	}
   	function getAnswer($id)
   	{
   		 
   		$answer=msr(msq("SELECT * FROM `site_site_voting_answers` WHERE `id`=".$id." LIMIT 1"));
   		return $answer;
   	}

}
?>