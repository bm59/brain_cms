<?
/*
�����, ����������� ����� ������
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
		/* ���������� ������� */
		$this->add(array(
				'name'=>'sheet1',
				'description'=>'���� (1 �������)',
				'types'=>array(
					array(
						'description'=>'�����',
						'name'=>'text',
						'type'=>'CDTextEditor',
						'settings'=>array('important'=>'','texttype'=>'full')
					)
					)
				)

		);
	}

	function get($id, $section_id){
		global $CDDataType;
		if (!$section_id>0 && $_GET['section']>0) $section_id=$_GET['section'];
		$retval = array();
		$id = floor($id);
		if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='$id'"))){
			$retval['id'] = $id;
			$retval['description'] = $r['description'];
			$retval['name'] = $r['name'];
			$retval['settings'] = $this->explode($r['settings']);
			
			$retval['types'] = $CDDataType->getDataSetList($id,$section_id);
		}
		return $retval;
	}
	function add($values, $section_id=0){
		global $CDDataType;
		if (!is_array($values['types'])) $values['types'] = array();
		if (count($values['types'])==0) return false;
		if (!preg_match("|^[a-zA-Z_0-9]+$|",$values['name'])) return false;
		$values['description'] = trim($values['description']);
		if (strlen($values['description'])==0) return false;
		$settings = $this->implode($values['settings']);
		
		$id=$this->checkPresence(0,$values['name']);
		if ($id==0)
		{
			msq("INSERT INTO `".$this->getSetting('table')."` (`description`,`name`,`settings`) VALUES ('".addslashes($values['description'])."','".$values['name']."','$settings')");
			$id = mslastid();
		}
		/* ���� �� ���� ������ ��� id ������� */
		if ($this->checkDatatype($id,$values['name'],$section_id)==0)
		{
			foreach ($values['types'] as $type) 
			$CDDataType->add(array('section_id'=>$section_id,'dataset'=>$id,'description'=>$type['description'],'name'=>$type['name'],'type'=>$type['type'],'settings'=>$type['settings']));
		}
	}
	function checkPresence($id,$name = ''){
		$id = floor($id);
		if (trim($name)!='') if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `name`='".addslashes(trim($name))."'"))) return $r['id'];
		if (msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='$id'"))) return $id;
		return 0;
	}
	function update_prec($id,$section_id){
		$id = floor($id);
		
		if (!$id>0 || !$section_id>0) return false;
		$q=msq("SELECT *, 1 as ord FROM `site_site_data_types` WHERE section_id=$section_id AND dataset=$id AND settings NOT LIKE '%|off|%'
				UNION SELECT *, 2 as ord FROM `site_site_data_types` WHERE section_id=$section_id AND dataset=$id AND settings LIKE '%|off|%'  ORDER BY `ord`,`precedence`");
		$i=0;
		while ($r=msr($q))
		{
			msq("UPDATE  `site_site_data_types` SET `precedence`='$i' WHERE id=".$r['id']." LIMIT 1");
			$i++;
		}
		return true;
	}
	function checkDatatype($id,$name = '', $section_id){
		$id = floor($id);
	
		$cnt=msr(msq("SELECT count(*) as cnt FROM `site_site_data_types` WHERE `dataset`='".$id."' and section_id=".$section_id));
	
		return floor($cnt['cnt']);
	}
}
?>