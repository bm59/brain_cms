<?
class CDColorStatus extends VirtualType
{
	function init($settings){
		$settings['descr']='Статус цветной';
		$settings['help']=array(
				'default=1'=>'Статус по умолчанию',
				'noselect=true'=>'Без выбора',
				'noeditpage=true'=>'Не показывать на странице редактирования',
				'source=#source_type=spr#spr_path=%SOURCE_PATH%#spr_field=name#spr_usl=WHERE `show`=1#spr_order=ORDER BY `name`'=>'Источник справочник'
		
		);
		VirtualType::init($settings);
	}
	function get_values($settings) {
		global $SiteSections;
	
		if ($settings['source']!='')
		{
			$source=array();
			$source_sett=explode('#',$settings['source']);
			foreach($source_sett as $set){
				$cur_set=explode('=', $set);
				if ($cur_set[0]!='')
					$source[$cur_set[0]]=str_replace($cur_set[0].'=', '', $set);
			}
	
			$arr=array();
			switch ($source['source_type']) {
				case "spr":
					$SiteSections= new SiteSections;
					$SiteSections->init();
					$Section = $SiteSections->get($SiteSections->getIdByPath($source['spr_path']));
	
					if ($Section['id']>0)
					{
						$Pattern = new $Section['pattern'];
						$Iface = $Pattern->init(array('section'=>$Section['id']));
						if ($show!=0) $conditions.= (($conditions!='')?" AND ":"")."`show`>'0'";
						if ($conditions!='') $conditions = ' WHERE '.$conditions;
						$q = msq("SELECT * FROM `".$Iface ->getSetting('table')."`".$source['spr_usl']." ".$source['spr_order']);
						while ($r = msr($q))
						$arr[] =$r;
	
					}
	
					break;
	
			}
	
			$settings['values']=$arr;
	
		}
	
		if (!is_array($settings['values'])) $settings['values']=array();
		$this->setSetting('values', $settings['values']);
		return $settings['values'];
	
	}
	function drawEditor($divstyle = '',$span = true){
		global $SiteSections;

		$settings = $this->getSetting('settings');
		
		$values=$this->get_values($this->getSetting('settings'));
		
		if ($settings['noeditpage']=='true') return;
		
		$selection = '';
		if ($this->getSetting('value')=='' && $settings['default']!='')  $this->setSetting('value', $settings['default']);
		$settings['values']=$this->get_values($this->getSetting('settings')) ;

		$settings['values']=$settings['values'];
		?>									
		<script>
									$(function() {

										function color_select()
										{
											var color=$("[name=<?=$this->getSetting('name') ?>] option:selected").attr('data-color');

											if (color!='')
											{
												$("[name=<?=$this->getSetting('name') ?>]").css('background', color);
											}
										}
										
										$('[name=<?=htmlspecialchars($this->getSetting('name'))?>]').on('change', function() {
			
											color_select();

							   			        
										});
										color_select();
	
									});
									</script>
			<div class="place" <?=($divstyle!='')?$divstyle:''?>>
				<label><?=htmlspecialchars($this->getSetting('description'))?><?=((isset($settings['important']))?' <span class="important">*</span>':'')?></label>
				<?
				print getSelectSinonim_color($this->getSetting('name'),$settings['values'],stripslashes($this->getSetting('value')), $settings);
				?>
			</div>
		<?



	}
	function preSave(){
		$errors = array();
		$settings = $this->getSetting('settings');
		$newvalue = floor($_POST[$this->getSetting('name')]);
		if ((isset($settings['important'])) && ($newvalue<1)) $errors[] = 'Заполните поле «'.$this->getSetting('description').'»';
		$this->setSetting('value',$newvalue);
		return $errors;
	}
	function postSave(){
		global $Storage;
		$settings = $this->getSetting('settings');
		if ($settings['noeditpage']=='true') return;
		if (floor($this->getSetting('uid'))>0){
			$st = $Storage->getStorage(floor($this->getSetting($this->getSetting('name').'storage')));
			if (floor($st['id'])>0){
				$f = $Storage->getFile($this->getSetting('value'));
				if (floor($f['id'])>0){
					if (substr($f['name'],0,5)=='temp_'){
						if ($Storage->renameFile($f['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')))){
							$nf = $Storage->getFile($f['id']);
						}
					}
				}
				$flist = $Storage->getListByUID($st['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')));
				foreach ($flist as $f){
					if (!$f['id']==$this->getSetting('value')) $Storage->deleteFile($f['id']);
				}
			}
		}
	}
	function getValue(){ 
		$settings = $this->getSetting('settings');
		if ($settings['noeditpage']=='true') return;
		return $this->getSetting('value'); 
	}
	function getUpdateSQL(){ 
		$settings = $this->getSetting('settings');
		if ($settings['noeditpage']=='true') return;
		return "`".$this->getSetting('name')."`='".floor($this->getSetting('value'))."'";
	}
	function delete(){
		global $Storage;
		$st = $Storage->getStorage(floor($this->getSetting($this->getSetting('name').'storage')));
		$flist = $Storage->getListByUID($st['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')));
		foreach ($flist as $f) $Storage->deleteFile($f['id']);
	}
}
?>