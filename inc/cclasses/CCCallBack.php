<?

class CCCallBack extends VirtualContent
{

	function init($settings){
        		global $SiteSections;
                VirtualContent::init($settings);

                $section = $SiteSections->get($this->getSetting('section'));
                $this->Settings['settings_personal']=$section['settings_personal'];

                $SiteSettings = new SiteSettings;
                $SiteSettings->init();
                $this->Settings['onpage'] = $this->Settings['settings_personal']['on_page']>0 ? $section['settings_personal']['on_page'] : 20;



                $this->like_array=array('search_comment', 'search_phone', 'search_note');/* Где нет в названии "name", но нужен поиск по like*/
                $this->not_like_array=array();/* Где есть в названии "name", но не нужен поиск по like*/
                $this->no_auto=array(); /*Не обрабатывать переменные, ручная обработка*/

                /*заменить на пустое в названиях переменны при поиске*/
                $this->field_tr=array('search_'=>'','_from'=>'','_to'=>'');

                /*подмена названий*/
                $this->field_change=array();


 				$this->getSearch();


   }
   function drawPubsList($param=''){
   	global $SiteSections, $CDDataSet, $CDDataType, $MySqlObject;
   
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


	 		$('.note a').click(function ()
			{

					$.ajax({
   			            type: "POST",
   			            url: "/inc/site_admin/pattern/ajax_class.php",
   			            data: "action=edit_field&print_ok=true&field_name=note&id="+$(this).parents('.note').attr('id')+"&section_id=<?=$_GET['section']?>&value="+$(this).parents('.note').find('textarea').val()+"&session_id="+session_id,
   			            dataType: 'json',
   			            success: function(data){
   			            		if (data.ok=='ok')
   	   			            	alert('Комментарий сохранен');
   								else 
   								alert('Ошибка сохранения комментария');
   			            }
   			        });
			});


			$('.colorselect').on('change', function() {

				var id = $(this).attr('name').replace("status_id_","");
				
				$.ajax({
			            type: "POST",
			            url: "/inc/site_admin/pattern/ajax_class.php",
			            data: "action=edit_field_apnd&print_ok=true&add_date=true&field_name=status_history&id="+id+"&section_id=<?=$_GET['section']?>&value="+$(this).find("option:selected").text()+"&session_id="+session_id,
			            dataType: 'json'
			        });

   			        
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
   		$table_th[]=array('name'=>'', 'description'=>'');
   
   
   		foreach($show_fields as $sf){
   			$set=$dataset['types'][$sf]['face']->Settings;
   			$table_th[]=array('name'=>$set['name'], 'description'=>$set['description'], 'class'=>$set['settings']['list_class']);
   		}
   
   
   		$table_th[]=array('name'=>'', 'description'=>'');
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
   				<?=$MySqlObject->dateTimeFromDB($pub['date']) ?>
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
   				
   				<?
   				if ($sf=='status_id')
   	   			if ($pub['status_history']!='')
   				{
   					$status_txt='';
   					
   					$val=explode('|', $pub['status_history']);	

   					foreach ($val as $v)
   					{
   						$item_val=explode('#', $v);
   						$status_txt.="<div><nobr>".$item_val[1].'&nbsp;&nbsp;&nbsp;&nbsp;'.$item_val[0]."</nobr></div>";	
   					}
   					print '<div style="text-align: left;"><a class="status_history_link" href="#" onclick="$(this).parents(\'td\').find(\'.status_history\').toggle(); ($(this).parents(\'td\').find(\'.status_history\').is(\':visible\') ? $(this).parents(\'td\').find(\'.status_history_link\').html(\'Скрыть историю статусов\') : $(this).parents(\'td\').find(\'.status_history_link\').html(\'Показать историю статусов\')); return false;">Показать историю статусов</a></div>';
   					print '<div style="text-align: left; display: none; padding-top: 10px;" class="status_history">'.$status_txt.'</div>';
   				}

   				?>
   				
   				</td>
   			<?}?>
   
   			<td>
	   			<div class="note" id="<?=$pub['id']?>">
		   					<label>Ваша заметка:</label>
		                    <textarea rows="2" name="note_<?=$pub['id']?>" style="width: 100%;"><?=$pub['note']?></textarea>
		                 	<div style="float: right;margin-right: 5px;"><a href="#" onclick="return false;">Сохранить</a></div>
		                 	<div class="clear"></div>
		                 	<br/>
		         </div>
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
   		
   		function AddCallback ($values=array()){
   			
   			$keys=$vals='';
   			if ($values['phone']=='') return;
   			
   			foreach ($values as $k=>$v)
   			{
   				$keys.=($keys!='' ? ',':'')."`$k`";
   				$vals.=($vals!='' ? ',':'')."'$v'";
   			}
   			msq("INSERT INTO `".$this->getSetting('table')."` (`date`, `status_id`, $keys) VALUES (TIMESTAMPADD(HOUR,2,NOW()), '1', $vals)");
   		}


}
?>