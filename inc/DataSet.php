<?
/*
Класс, описывающий набор данных
*/
class DataSet extends VirtualClass
{
	function init($settings = array()){
		$this->Settings['table'] = mstable(ConfigGet('pr_name').'_site','data','sets',array(
			"description"=>"VARCHAR(255)",
			"name"=>"VARCHAR(255)",
			"settings"=>"TEXT"
		));
		if (is_array($settings)){
			foreach ($settings as $name=>$value) $this->setSetting($name,$value);
		}
		/* Добавление наборов */
		$this->add(array(
				'name'=>'sheet1',
				'description'=>'Лист (1 колонка)',
				'types'=>array(
					array(
						'description'=>'Текст',
						'name'=>'text',
						'type'=>'CDTextEditor',
						'settings'=>array('important'=>'','texttype'=>'full')
					)
					)
				)

		);
	}

	function get($id){
		global $CDDataType;
		$retval = array();
		$id = floor($id);
		if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='$id'"))){
			$retval['id'] = $id;
			$retval['description'] = $r['description'];
			$retval['name'] = $r['name'];
			$retval['settings'] = $this->explode($r['settings']);
			$retval['types'] = $CDDataType->getDataSetList($id);
		}
		return $retval;
	}
	function add($values){
		global $CDDataType;
		if (!is_array($values['types'])) $values['types'] = array();
		if (count($values['types'])==0) return false;
		if (!preg_match("|^[a-zA-Z_0-9]+$|",$values['name'])) return false;
		$values['description'] = trim($values['description']);
		if (strlen($values['description'])==0) return false;
		if ($this->checkPresence(0,$values['name'])) return false;
		$settings = $this->implode($values['settings']);
		msq("INSERT INTO `".$this->getSetting('table')."` (`description`,`name`,`settings`) VALUES ('".addslashes($values['description'])."','".$values['name']."','$settings')");
		$id = mslastid();
		foreach ($values['types'] as $type) $CDDataType->add(array('dataset'=>$id,'description'=>$type['description'],'name'=>$type['name'],'type'=>$type['type'],'settings'=>$type['settings']));
	}
	function checkPresence($id,$name = ''){
		$id = floor($id);
		if (trim($name)!='') if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `name`='".addslashes(trim($name))."'"))) return $r['id'];
		if (msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='$id'"))) return $id;
		return 0;
	}
}
?>