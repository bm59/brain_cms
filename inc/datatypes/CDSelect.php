<?
class CDSelect extends VirtualType
{
	function init($settings){
		$settings['descr']='Селект';
		$settings['help']=array(
				'values=первый, второй, третий'=>'Значения без id',
				'values=2#первый, 3#второй, 4#третий'=>'Значения с id',
				'comment=комментарий'=>'Комментарий',
				'editable'=>'Редактируемый',
				'source=#source_type=table#table_name=%SOURCE_TABLE%#table_field=name#table_usl=WHERE `show`=1#table_order=ORDER BY `name`#name_only=0'=>'Источник таблица',
				'source=#source_type=spr#spr_path=%SOURCE_PATH%#spr_field=name#spr_usl=WHERE `show`=1#spr_order=ORDER BY `name`#name_only=0'=>'Источник справочник'
		);

		VirtualType::init($settings);
	}
	function get_values($settings) {
		global $SiteSections;
	
		/*Внешний массив*/
		if (is_array($this->getSetting('values')))
		{
			$settings['values']=$this->getSetting('values');
			return $settings['values'];
		}
		 
		 
		 
		/*Массив из настроек типа*/
		else if ($settings['values']!='')
		{
			$arr=array();
			$val=explode(',', $settings['values']);
			foreach ($val as $v)
			{
				$v=trim($v);
				
				if (stripos($v,'#')!==false)
				{
					$val=explode('#',$v);
					$k=trim($val[0]);
					$v=trim($val[1]);
	
					$arr[$k]=trim($v);
				}
				else
				$arr[$v]=trim($v);
			}
			$settings['values']=$arr;
	
		}
		 
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
				case "table":
					$q=msq("SELECT * FROM `".$source['table_name']."` ".$source['table_usl']." ".$source['table_order']);
					while ($r=msr($q))
					{
						if ($source['name_only']==1)
							$arr[trim($r[$source['table_field']])]=trim($r[$source['table_field']]);
						else
							$arr[$r['id']]=trim($r[$source['table_field']]);
					}
						
					break;
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
							if ($source['name_only']==1)
								$arr[$r[$source['spr_field']]] =$r[$source['spr_field']];
							else
								$arr[$r['id']] =$r[$source['spr_field']];
	
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
		$selection = '';
		if ($this->getSetting('value')=='' && $settings['default']!='')  $this->setSetting('value', $settings['default']);
		$settings['values']=$this->get_values($this->getSetting('settings')) ;

		$settings['values']=array('-1'=>'')+$settings['values'];
		?>
			<div class="place" <?=($divstyle!='')?$divstyle:''?>>
				<label><?=htmlspecialchars($this->getSetting('description'))?><?=((isset($settings['important']))?' <span class="important">*</span>':'')?></label>
				<?
				print getSelectSinonim($this->getSetting('name'),$settings['values'],stripslashes($this->getSetting('value')),true);
				?>
			</div>	
		<?

    

	}
	function preSave(){
		$errors = array();
		$settings = $this->getSetting('settings');

		$newvalue = htmlspecialchars(trim($_POST[$this->getSetting('name')]));

		if ((isset($settings['important'])) && ($newvalue==='' || $newvalue==-1)) $errors[] = 'Заполните поле «'.$this->getSetting('description').'»';
		if ($newvalue!='0')
		$this->setSetting('value',$newvalue);
		return $errors;
	}
	function postSave(){ return; }
	function getValue(){ return $this->getSetting('value'); }
	function getUpdateSQL(){ return "`".$this->getSetting('name')."`='".addslashes($this->getSetting('value'))."'"; }
	function delete(){ return; }
}
?>