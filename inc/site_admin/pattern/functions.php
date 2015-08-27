<?
function print_dt($ds) {
	global $save_fields,$columns,$type_array,$SectionPattern,$dt_settings_checkbox;
		
		if (!$ds['id']>0) $ds['id']='new';
		
		foreach ($save_fields as $sf)
		{
			if (isset($_POST[$sf.'_'.$ds['id']])) $ds[$sf]=$_POST[$sf.'_'.$ds['id']];

		} 

		$ds['table_type']=isset($_POST['table_type_'.$ds['id']]) ? $_POST['table_type_'.$ds['id']]:$columns[$ds['name']]['Type']; 
		?>
		<tr <?=(($ds['tr_type']!='') ? 'class="'.$ds['tr_type'].'"' : '' )?>>
			<td class="t_minwidth"><div class="drag_icon"><img src="/pics/editor/up_down.png"></div></td>
			<td class="t_minwidth"><?=$ds['id'] ?></td>
			<td class="t_left">
				<input type="hidden" name="id_<?=$ds['id'] ?>" value="1">
				<label>Name:</label>
				<span class="input">
					<input type="text" value="<?=$ds['name']?>" name="name<?='_'.$ds['id'] ?>">
				</span>
				
				<label>Описание:</label>
				<span class="input">
					<input type="text" value="<?=$ds['description']?>"  name="description<?='_'.$ds['id'] ?>">
				</span>
			</td>
			<td>
				<label>Тип данных:</label>
				<div class="input">
					<select name="type<?='_'.$ds['id'] ?>">
					<?
					foreach ($type_array as $k=>$v) {
						?><option <?=(($ds['type']==$k) ? 'selected="selected"':'') ?>  value="<?=$k?>" data-default="<?=$v['default_type'] ?>"><?=$v['description'] ?></option><? 
					}
					?>
						
					</select>
				</div>
				
				<label>Свойства колонки:</label>
				<span class="input">
					<input type="text" value="<?=$ds['table_type']?>" maxlength="255" name="table_type<?='_'.$ds['id'] ?>">
				</span>
			</td>
			<td class="t_minwidth">
				<div class="dt_settings" style="display: none;">
					<?
					$str_settings=(!isset($_POST['settings_'.$ds['id']]) ? $SectionPattern->implode($ds['settings']) : $_POST['settings_'.$ds['id']]);
					?>
					<!-- Настройки поля -->
					<label>Настройки поля (разделитель |):</label>
					<span class="input">
						<input type="text" value="<?=$str_settings?>"  class="setting_text" name="settings<?='_'.$ds['id'] ?>">
					</span>
					<?
					foreach($dt_settings_checkbox as $k=>$v)
					{
						/* Включена ли настройка */
						$set_on=false;
						if (stripos($str_settings, "|$k|")!==false) $set_on=true;
						
						?><input data-name="<?=$k?>"  id="<?=$k?><?='_'.$ds['id'] ?>" type="checkbox" <?=$set_on ? 'checked="checked"' : '' ?>><label for="<?=$k?><?='_'.$ds['id'] ?>"><?=$v?></label><br/><? 	
					}
					?>

					<!-- Стиль редактора -->
					<div class="clear"></div><div class="hr"></div>
					<label>Стиль (AdEdit):</label>
					<span class="input">
						<input type="text" value="<?=$ds['setting_style_edit']?>"  name="setting_style_edit<?='_'.$ds['id'] ?>">
					</span>
				</div>
			</td>
			<?
			$_SERVER['REQUEST_URI']=preg_replace('/(?:\&|\?)delete\=(.*)(?:$|\&)/', "", $_SERVER['REQUEST_URI']);
			?>
			<td class="t_minwidth">


			<span class="button txtstyle">
					<? 
					$off=((stripos($str_settings, '|off|')!==false) ? 1 : 0);
					?>
		        	<input type="hidden" name="off_<?=$ds['id'] ?>" value="<?=$off?>">
		        	<input type="button" data-inp="off_<?=$ds['id'] ?>" title="Включить\Отключить" class="on_off" onclick="return false" style="background-image: url(/pics/editor/<?=(($off) ? 'off.png':'on.png') ?>)">
		     	</span>
		     	<span class="button txtstyle">
		        	<input type="button" title="Настройки" class="show_settings" onclick="return false" style="background-image: url(/pics/editor/settings.png)">
		     	</span>
				<span class="button txtstyle">
		        	<input type="button" onclick="if (confirm('Вы действительно хотите удалить этот раздел?')) window.location.href = '<?=$_SERVER['REQUEST_URI']?>&delete=<?=$ds['id'] ?>';" title="Удалить" style="background-image: url(/pics/editor/delete.gif)">
		     	</span>
			</td>
		</tr>
		<?
}
?>