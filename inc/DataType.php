<?
/*
Класс, описывающий тип данных
*/
class DataType extends VirtualClass
{
	function init(){
		$this->Settings['table'] = mstable(ConfigGet('pr_name').'_site','data','types',array(
			"dataset"=>"BIGINT(20)",
			"description"=>"VARCHAR(255)",
			"name"=>"VARCHAR(255)",
			"type"=>"VARCHAR(255)",
			"precedence"=>"BIGINT(20)",
			"settings"=>"TEXT",
			"setting_style_edit"=>"TEXT",
			"setting_style_search"=>"TEXT"
		));

		include_once($_SERVER['DOCUMENT_ROOT']."/inc/datatypes/VirtualType.php");

		if ($dir = @opendir($_SERVER['DOCUMENT_ROOT']."/inc/datatypes/")){
			while ($file = readdir($dir)){
				if ($file && $file!=".." && $file!="."){
					if ((preg_match('|.*\.php$|',$file)) && ($file!='VirtualType.php'))
					{
						include_once($_SERVER['DOCUMENT_ROOT']."/inc/datatypes/".$file);
						$this->Settings['all_types'][]=str_replace('.php', '',$file);
					}
				}
			}
			closedir($dir);
		}
		/* $dir = $_SERVER['DOCUMENT_ROOT']."/inc/datatypes/";
		include_once($dir."VirtualType.php");
		include_once($dir."CDDate.php");
		include_once($dir."CDImage.php");
		include_once($dir."CDText.php");
		include_once($dir."CDTextArea.php");
		include_once($dir."CDTextEditor.php");
		include_once($dir."CDVideo.php");
		include_once($dir."CDFile.php");
		include_once($dir."CDFloat.php");
		include_once($dir."CDFloatInfo.php");
		include_once($dir."CDInteger.php");
        include_once($dir."CDGallery.php");
        include_once($dir."CDBoolean.php");
        include_once($dir."CDSpinner.php");
        include_once($dir."CDChoice.php");
        include_once($dir."CDSlider.php"); */
	}

	function add($values, $add_column=false, $table_array='', $section_id){
		global $CDDataSet;
		
		$error='';
		if (!preg_match("|^[a-z0-9_]+$|",$values['name']))
		{
			$_SESSION['global_alert'].='Ошибка в названии :'.$values['name'];
			return false;
		}
		$values['description'] = trim($values['description']);
		if (strlen($values['description'])==0)
		{
			$_SESSION['global_alert'].='Ошибка в описании :'.$values['description'];
			return false;
		}
		
		$values['dataset'] = $CDDataSet->checkPresence($values['dataset'], '', $values['section_id']);		
		
		if ($values['dataset']==0)
		{
				$_SESSION['global_alert'].='Не найден dataset';
				return false;
		}


		if (is_array($values['settings']))
		$values['settings'] = $this->implode($values['settings']);

		if (msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `section_id`='".$section_id."' and `dataset`='".$values['dataset']."' AND `name`='".$values['name']."'")))
		{
			$_SESSION['global_alert'].='Поле с таким названием уже существует :'.$values['name'];
			return false;
		}
		$precedence = msr(msq("SELECT COUNT(id) AS prec FROM `".$this->getSetting('table')."` WHERE `dataset`='".$values['dataset']."'"));
		$precedence = floor($precedence['prec']);



		$cur_error=mysql_error();


		if ($add_column) $this->add_column($values, $table_array);

		/* Если при добавлении колонки не было ошибок */
		if ($cur_error==mysql_error())
		msq("INSERT INTO `".$this->getSetting('table')."` (`section_id`,`dataset`,`description`,`name`,`type`,`precedence`,`settings`,`setting_style_edit`,`setting_style_search`)
		VALUES ('".$section_id."','".$values['dataset']."','".addslashes($values['description'])."','".$values['name']."','".$values['type']."','".$values['precedence']."','".$values['settings']."','".$values['setting_style_edit']."','".$values['setting_style_search']."')");
		alert_mysql();
		$error.=mysql_error();

		return mslastid();
	}
	function get_search_field($data='', $search_fields_cnt)
	{
		$tface = $data['face'];
		$type=get_class($tface);
		if ($tface->Settings['setting_style_edit']['css']=='')
			$tface->Settings['setting_style_edit']['css']='width: '.round(80/$search_fields_cnt).'%';


		switch ($type) {
			case 'CDChoice': case 'CDSelect':
				//print_r($tface);
				$values=array('-1'=>'')+$tface->get_values($tface->Settings['settings']);

				if ($tface->Settings['settings']['type']=='multi'){?><input type="hidden" name="nouse_search_<?=$tface->getSetting('name')?>_type" value="<?=$type?>"><?}?>
				<div class="place" style="z-index: 10;<?=$tface->Settings['setting_style_edit']['css']?>">
					<label><?=htmlspecialchars($tface->getSetting('description'))?></label>
					<?print getSelectSinonim('search_'.$tface->getSetting('name'),$values,$_REQUEST['search_'.$tface->getSetting('name')]);?>
				</div>
				<?
				break;
				case 'CDColorStatus': 
					//print_r($tface);
					$values=array('-1'=>'')+$tface->get_values($tface->Settings['settings']);
				
					if ($tface->Settings['settings']['type']=='multi'){?><input type="hidden" name="nouse_search_<?=$tface->getSetting('name')?>_type" value="<?=$type?>"><?}?>
								<div class="place" style="z-index: 10;<?=$tface->Settings['setting_style_edit']['css']?>">
									<label><?=htmlspecialchars($tface->getSetting('description'))?></label>
									<?print getSelectSinonim_color('search_'.$tface->getSetting('name'),$values,$_REQUEST['search_'.$tface->getSetting('name')], $tface->Settings['settings'], 'search');?>
								</div>
								<?
				break;
			case 'CDDate':

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

									$("[name=search_<?=$tface->getSetting('name')?>_from]").datepicker({
									showOn: "both",
									buttonImage: "/pics/editor/calendar.gif",
									buttonImageOnly: true
								});
								$.datepicker.setDefaults($.extend($.datepicker.regional["ru"]));

									$("[name=search_<?=$tface->getSetting('name')?>_to]").datepicker({
									showOn: "both",
									buttonImage: "/pics/editor/calendar.gif",
									buttonImageOnly: true
								});

							});
							</script>
		<div style="z-index: 11; width: 158px;" id="date_calendar" class="place">
			<label>Дата c</label>
			<div><input type="text" name="search_<?=$tface->getSetting('name')?>_from" value="<?=$_POST['search_'.$tface->getSetting('name').'_from']?>" style="width: 100px; float: left;"></div>
		</div>
		<div style="z-index: 11; width: 158px;" id="date_calendar" class="place">
			<label>Дата по</label>
			<div><input type="text" name="search_<?=$tface->getSetting('name')?>_to" value="<?=$_POST['search_'.$tface->getSetting('name').'_to']?>" style="width: 100px; float: left;"></div>
		</div>
				<?
			break;
			default:
				?>
	        		<div class="place" style="z-index: 10;<?=$tface->Settings['setting_style_edit']['css']?>">
		        		<label><?=$tface->Settings['description'] ?></label>
						<span class="input">
							<input type="text" name="search_<?=$tface->getSetting('name')?>" value="<?=$_REQUEST['search_'.$tface->getSetting('name')]?>"/>
						</span>
					</div>
	        		<?
	        		break;
	        	}
	}
	function get_view_field($data='', $val, $pub='')
	{
		global $Storage, $MySqlObject;

		$tface = $data['face'];
		$type=get_class($tface);

		$settings=$tface->Settings['settings'];

		switch ($type) {
			case 'CDImage':
				$image=$Storage->getFile($val);
				if ($image['path']!='')
				print '<img src="'.$image['path'].'" width="'.(($image['width']>150) ? '150' : $image['width']).'px">';
				else print '&nbsp';
			break;
			case 'CDDate':
					print $MySqlObject->dateFromDBDot($val);
			break;
			case 'CDTextEditor':
				if (strlen($val)>300)
				$val=htmlspecialchars_decode(trim(mb_substr($val, 0, 300 )).'...');
				print $val;
			break;
			case 'CDChoice':
						$CDChoice=new CDChoice;
						$values=$CDChoice->get_values($settings);
						print $values[$val];
			break;
			case 'CDSelect':
					if (isset($settings['editable']))
					{
						$CDSelect=new CDSelect;
						$values=$CDSelect->get_values($settings);

						?>
						<script>
						$(function() {
							$('[name=<?=htmlspecialchars($tface->getSetting('name')).'_'.$pub['id']?>]').on('change', function() {

			   						$.ajax({
				   			            type: "POST",
				   			            url: "/inc/site_admin/pattern/ajax_class.php",
				   			            data: "action=edit_field&field_name=<?=$tface->getSetting('name')?>&id=<?=$pub['id']?>&section_id=<?=$_GET['section']?>&value="+$(this).val()+"&session_id="+session_id,
				   			            dataType: 'json',
				   			            success: function(data){
				   			            	elem.children('img').attr('src', '/pics/editor/'+data.signal);
				   			            }
				   			        });
							});
						});
						</script>
						<?
						print getSelectSinonim($tface->getSetting('name').'_'.$pub['id'],$values,stripslashes($val),true);
					}
					else
					print stripcslashes($val);
			break;
			case 'CDBoolean':
				if (isset($settings['editable']))
				{
					$CDSelect=new CDSelect;
					$values=$CDSelect->get_values($settings);
			
					?>
							<script>
							$(document).ready(function() {
								   $('input[name="<?=$tface->getSetting('name').'_'.$pub['id']?>"]').change(function() {


										var val=$(this).val()=='on' ? 1:0;
									   $.ajax({
					   			            type: "POST",
					   			            url: "/inc/site_admin/pattern/ajax_class.php",
					   			            data: "action=edit_field&field_name=<?=$tface->getSetting('name')?>&id=<?=$pub['id']?>&section_id=<?=$_GET['section']?>&value="+val+"&session_id="+session_id,
					   			            dataType: 'json',
					   			            success: function(data){
					   			            	elem.children('img').attr('src', '/pics/editor/'+data.signal);
					   			            }
					   			        });
								    });
							});
							</script>
	
							<div class="styled">
								<input type="checkbox" name="<?=$tface->getSetting('name').'_'.$pub['id']?>" id="<?=$tface->getSetting('name').'_'.$pub['id']?>" class="checkbox" <?=(($val || ($val=='' && $settings['default']==1)) ? 'checked="checked"':'')?>>
								<label for="<?=$tface->getSetting('name').'_'.$pub['id']?>"></label>
							</div>
				
						<?
				}
			break;
			case 'CDColorStatus':

					$CDColorStatus=new CDColorStatus;
					$values=$CDColorStatus->get_values($settings);
					?>
									<script>
									$(function() {

										function color_select()
										{
											var color=$("[name=<?=$tface->getSetting('name').'_'.$pub['id'] ?>] option:selected").attr('data-color');

											if (color!='')
											{
												$("[name=<?=$tface->getSetting('name').'_'.$pub['id'] ?>]").css('background', color);
											}
										}
										
										$('[name=<?=htmlspecialchars($tface->getSetting('name')).'_'.$pub['id']?>]').on('change', function() {
			
											color_select();
					   							$.ajax({
							   			            type: "POST",
							   			            url: "/inc/site_admin/pattern/ajax_class.php",
							   			            data: "action=edit_field&field_name=<?=$tface->getSetting('name')?>&id=<?=$pub['id']?>&section_id=<?=$_GET['section']?>&value="+$(this).val()+"&session_id="+session_id,
							   			            dataType: 'json',
							   			            success: function(data){
							   			            	elem.children('img').attr('src', '/pics/editor/'+data.signal);
							   			            }
							   			        });

							   			        
										});
										color_select();
	
									});
									</script>
									<?
									print getSelectSinonim_color($tface->getSetting('name').'_'.$pub['id'],$values,stripslashes($val), $settings);
								
						break;
			case 'CDCHOICE':
				if (isset($settings['editable']))
				{
					if ($settings['type']=='') $settings['type']='radio';

					$CDCHOICE=new CDCHOICE;
					?>
					  <script>
					  $(function() {
					    $( "#<?=$tface->getSetting('name').'_'.$pub['id']?>" ).buttonset();

					    $('#<?=$tface->getSetting('name').'_'.$pub['id']?>').change(function() {

					    	var val='';

							<?
							if ($settings['type']=='radio')
							{
								?>
								val=$("#<?=$tface->getSetting('name').'_'.$pub['id']?> [type=radio]:checked").val();
								<?
							}
							else
							{
								?>
							    $("#<?=$tface->getSetting('name').'_'.$pub['id']?> [type=checkbox]:checked").each(function() {
							    	val+=(val=='' ? ',':'')+$(this).val()+',';
								});

								<?
							}
							?>
		   					$.ajax({
		   			            type: "POST",
		   			            url: "/inc/site_admin/pattern/ajax_class.php",
		   			            data: "action=edit_field&field_name=<?=$tface->getSetting('name')?>&id=<?=$pub['id']?>&section_id=<?=$_GET['section']?>&value="+val+"&session_id="+session_id,
		   			            dataType: 'json',
		   			            success: function(data){
		   			            	elem.children('img').attr('src', '/pics/editor/'+data.signal);
		   			            }
		   			        });
					    });
					  });
					  </script>

					<div id="<?=$tface->getSetting('name').'_'.$pub['id']?>" class="radio_ui">
				    <?
				    $i=0;
				    $settings['values']=$CDCHOICE->get_values($settings);
				    foreach ($settings['values'] as $k=>$v)
				    {
				    	$i++;
				    	if ($k=='') $k=$v;

				    	$selected='';

				    	if ($k==$val || stripos($val, ','.$k.',')!==false)  $selected='checked="checked"';

				    	if ($settings['type']=='') $settings['type']='radio';

				    	if ($settings['type']=='radio')
				    	{?>
				    	<input type="radio" id="radio<?=$i?>_<?=$tface->getSetting('name').'_'.$pub['id']?>" value="<?=$k?>" name="<?=$tface->getSetting('name').'_'.$pub['id']?>" <?=$selected?>><label for="radio<?=$i?>_<?=$tface->getSetting('name').'_'.$pub['id']?>"><?=$v?></label>
				    	<?
				    	}
				    	else
				    	{
				       	?>
				       	<input type="checkbox" id="check<?=$i?>_<?=$tface->getSetting('name').'_'.$pub['id']?>" value="<?=$k?>" name="<?=$tface->getSetting('name').'_'.$pub['id']?>[]" <?=$selected?>><label for="check<?=$i?>_<?=$tface->getSetting('name').'_'.$pub['id']?>"><?=$v?></label>
				       	<?
				    	}


				    }
				    ?>
					</div>
					<?
				}
				else print stripcslashes($val);

			break;
			default:
				if (isset($settings['editable']))
				{					?>
					<script>
					$(function() {
						$('[name=<?=htmlspecialchars($tface->getSetting('name')).'_'.$pub['id']?>]').keyup(function ()
						{
			                var default_val='<?=stripslashes(htmlspecialchars($val))?>';

			                if ($(this).val()!=default_val)

			   					$.ajax({
			   			            type: "POST",
			   			            url: "/inc/site_admin/pattern/ajax_class.php",
			   			            data: "action=edit_field&field_name=<?=$tface->getSetting('name')?>&id=<?=$pub['id']?>&section_id=<?=$_GET['section']?>&value="+$(this).val()+"&session_id="+session_id,
			   			            dataType: 'json',
			   			            success: function(data){
			   			            	elem.children('img').attr('src', '/pics/editor/'+data.signal);
			   			            }
			   			        });
						});
					});
					</script>
					<input type="text" name="<?=htmlspecialchars($tface->getSetting('name')).'_'.$pub['id']?>"  value="<?=stripslashes(htmlspecialchars($val))?>" />
					<?
				}
				else
				print stripcslashes($val);
	        break;
		 }
	}
	function add_column($values, $table_array='', $default='') {
		$error='';
		if (!$values['dataset']>0) return false;

		$def=$default!='' ? $default : ' NULL DEFAULT NULL';

		foreach($table_array as $tab)
		if ($tab!='')
		{
			msq("ALTER TABLE `$tab` ADD `".$values['name']."` ".$values['table_type']."  $def COMMENT '".$values['description']."'");
			alert_mysql();
			$error.=mysql_error();

		}
		return $error;
	}
	function get_dataset_tables($pattern='') {
		global $SiteSections;
		$return=array();

		$q=msq("SELECT * FROM `site_site_sections` WHERE `pattern`='$pattern'");
		while ($r=msr($q))
		{
			$Section=$SiteSections->get($r['id']);
			$Section['id'] = floor($Section['id']);

			if ($Section['id']>0)
			{
				$Pattern = new $Section['pattern'];
				$Iface = $Pattern->init(array('section'=>$Section['id']));
				$return['tables'][]=$Iface->getSetting('table');
				$return['ids'][]=$Section['id'];
			}
		}
		return $return;

	}
	function update($id, $values, $table_columns, $table_type_options, $table_array, $section_id)
	{
		$error='';
		if (!$id>0) return;

		/* Проверяем есть ли изменения */
		$cur_data=msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`=$id and section_id=".$section_id));

		$changes=false;
		foreach ($cur_data as $k=>$v)
		{
			if (isset($values[$k]) && $v!=$values[$k])
			{
				/* print "changes $k: $v<>".$values[$k].'<br/>'; */
				$changes=true;
			}
		}

		/* Если изменили название поля, меняем его в таблице */
		if ($changes)
		{
			$update='';

			foreach($values as $k=>$v){ $update.=(($update!='') ? ',':'')."`$k`='$v'";}
			msq("UPDATE `".$this->getSetting('table')."` SET $update WHERE id=$id LIMIT 1");
			alert_mysql();
			$error.=mysql_error();

		}


		/* Если изменили тип колонки или описание*/
 		if (isset($table_columns[$cur_data['name']]) && isset($values['name']) && $cur_data['name']!=$values['name'])
		{

			foreach ($table_array as $tab)
			{
				msq("ALTER TABLE `$tab` CHANGE `".$cur_data['name']."` `".$values['name']."` ".$table_columns[$cur_data['name']]['Type']."  NULL DEFAULT NULL COMMENT '".$values['description']."'");

				alert_mysql();
				$error.=mysql_error();
				$changes=true;
			}
		}
		/* Если был изменены настройки колонки */
 		if ($table_columns[$cur_data['name']]['Type']!=$table_type_options)
		{
			foreach ($table_array as $tab)
			{
				msq("ALTER TABLE `$tab` CHANGE `".$values['name']."` `".$values['name']."` ".$table_type_options." NULL DEFAULT NULL COMMENT '".$values['description']."'");
				alert_mysql();
				$error.=mysql_error();
				$changes=true;
			}

		}
		if ($changes && $error=='') return true;
		else return $error;

	}
	function delete($id, $table_name){
		if (!$id>0) return;
		$cur_data=msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`=$id"));
		msq("DELETE FROM `".$this->getSetting('table')."` WHERE `id`=$id");
		msq("ALTER TABLE `$table_name` DROP `".$cur_data['name']."`");
	}
	function get_column_info($table_name){
	 	$columns=array();
		$q=msq("SHOW COLUMNS FROM `".$table_name."`");
		while ($r=msr($q))
		{
			$columns[$r['Field']]=$r;
		}
		return $columns;
	}
	function getDataSetList($datasetid, $section_id){
		global $Section;
		$datasetid = floor($datasetid);
		$section_id = floor($section_id);
		$retval = array();
		$q = msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `section_id`=$section_id and `dataset`='".$datasetid."' ORDER BY `precedence`");
		while ($r = msr($q)) $retval[$r['name']] = array('id'=>$r['id'],'description'=>$r['description'],'name'=>$r['name'],'type'=>$r['type'],'precedence'=>$r['precedence'],'settings'=>$this->explode($r['settings']),'setting_style_edit'=>$this->explode($r['setting_style_edit']),'setting_style_search'=>$this->explode($r['setting_style_search']));
		return $retval;
	}
}
?>