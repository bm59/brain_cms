<?
/*
����� �������� ��������
*/
class SiteSettings extends VirtualClass
{
	function init(){
		$this->Settings['table'] = mstable(ConfigGet('pr_name'),'settings','',array(
			"name"=>"VARCHAR(255)",
			"description"=>"VARCHAR(255)",
			"value"=>"TEXT",
			"settings"=>"TEXT"
		));
		$this->Settings['types'] = array(
			'integer'=>'����� �����',
			'email'=>'����� ����������� �����',
			'string'=>'������',
			'text'=>'�����',
			'int'=>'��/���'
		);
	}
	function check($value,$settings = array()){ // �������� �� ���������� �������� � ����������� �� ����
		$retval = array('error'=>'','value'=>trim($value));
		switch ($settings['type']){
			case 'integer':
				$retval['value'] = floor($retval['value']);
				if (isset($settings['notnull']) && ($retval['value']==0)) $retval['error'] = '�� ����� ����� ������ ��������';
			break;
			case 'email':
				$mail = array();
				$emails = explode(',',$retval['value']);
				foreach ($emails as $email){
					if ($email = checkEmail($email)) $mail[] = $email;
					else $retval['error'] = '���� ��� ��������� ������� ������� �������';
				}
				$retval['value'] = implode(',',$mail);
				if (isset($settings['notnull']) && ($retval['value']=='')) $retval['error'] = '�� ����� ����� ������ ��������';
			break;
			case 'string':
				if (isset($settings['notnull']) && ($retval['value']=='')) $retval['error'] = '�� ����� ����� ������ ��������';
			break;
		}
		return $retval;
	}
	function update($id,$value){ // �������������� ���������
		$error = '';
		$set = $this->getOne($id);
		$value = $this->check($value,$set['settings']);
		if ($value['error']!='') $error = $set['description'].': '.lower($value['error']);
		if ($error==''){
			$set['value'] = $value['value'];
			msq("UPDATE `".$this->getSetting('table')."` SET `value`='".addslashes($value['value'])."' WHERE `id`='".floor($set['id'])."'");
			$this->setCacheValue('setting_'.floor($set['id']),$set);
		}
		return $error;
	}
	function add($name,$description,$value,$settings = array()){ // ���������� ���������
		$errors = array();
		if (!is_array($settings)) $settings = array();
		$name = trim($name);

		$type = ''; foreach ($this->getSetting('types') as $t=>$d) if ($settings['type']==$t) $type = $t;
		if ($type=='') $errors[] = '������ ������������ ���'; $settings['type'] = $type;
		if (!preg_match('/[a-zA-Z_0-9]+/',$name)) $errors[] = '�������� ��������� ������ �������� �� ���� ���������� �������� � ����';
		if ($description=='') $errors[] = '�� ������� ��������';
		if (!$this->isUniqueName($name)) $errors[] = '��������� � ����� ��������� ��� �������, �������� ��������';
		$check = $this->check($value,$settings);
		if ($check['error']!='') $errors[] = $check['error'];
		$settings = $this->implode($settings);
		if (count($errors)==0){
			msq("INSERT INTO `".$this->getSetting('table')."` (`name`,`description`,`value`,`settings`) VALUES('".addslashes($name)."','".addslashes($description)."','".addslashes($value)."','".$settings."')");
		}
		return $errors;
	}
	function delete($id){
		$errors = array();
		$set = $this->getOne($id);
		if (floor($set['id'])>0){
			if (isset($set['settings']['undeletable'])) $errors[] = '���������� �������';
			if (count($errors)==0){
				msq("DELETE FROM `".$this->getSetting('table')."` WHERE `id`='".$set['id']."'");
			}
		}
		return $errors;
	}
	function isUniqueName($name,$exceptid = 0){ /* �������� �� ������������ ��������, ���������� true ���� ��������� */
		$retval = true;
		$name = addslashes($name);
		$exceptid = floor($exceptid);
		$q = msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `name`='".$name."'");
		while ($r = msr($q)) if ($r['id']!= $exceptid) $retval = false;
		return $retval;
	}
	function getList(){ // ��������� ������ ID ��������
		$retval = array();
		$q = msq("SELECT * FROM `".$this->getSetting('table')."`");
		while ($r = msr($q)){ $retval[] = $r['id']; $one = $this->getOne($r['id'],$r); }
		return $retval;
	}
	function getIdByName($name){
		$retval = 0;
		$name = trim($name);
		if (preg_match('/[a-zA-Z0-9]+/',$name)){
			if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `name`='$name'"))) $retval = $r['id'];
		}
		return $retval;
	}
	function getOne($id = 0, $r = array()){ /* ��������� ���������� � ���������� ��������� */
		$id = floor($id);
		$retval = $this->getCacheValue('setting_'.$id);
		if (floor($retval['id'])>0) return $retval;
		$retval = array();
		if (count($r)==0){
			if ($id>0) $r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'"));
			if (!is_array($r)) $r = array();
		}
		if (count($r)>0){
			$settings = $this->explode($r['settings']);
			$retval = array('id'=>$r['id'],'name'=>$r['name'],'description'=>$r['description'],'value'=>$r['value'],'settings'=>$settings);
			$this->setCacheValue('setting_'.$id,$retval);
		}
		return $retval;
	}
	function getOneVal($id = 0, $r = array()){ /* ��������� ���������� � ���������� ��������� */
		$id = floor($id);
		$retval = $this->getCacheValue('setting_'.$id);
		if (floor($retval['id'])>0) return $retval;
		$retval = array();
		if (count($r)==0){
			if ($id>0) $r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'"));
			if (!is_array($r)) $r = array();
		}
		if (count($r)>0){
			$settings = $this->explode($r['settings']);
			$retval = $r['value'];
			$this->setCacheValue('setting_'.$id,$retval);
		}
		return $retval;
	}
}
?>