<?
/*
Класс, описывающий тип (группу) посетителя сайта и права доступа
*/
class VisitorType extends VirtualClass
{
	function init(){
		$this->Settings['table'] = mstable(ConfigGet('pr_name').'_bk','users','types',array(
			"name"=>"VARCHAR(255)",
			"access"=>"TEXT",
			"settings"=>"TEXT"
		));
		$this->Settings['adminsId'] = 1; // ID администраторской группы, по идее не должно меняться никогда, но на всякий устанавливаем здесь (ID брать из базы)
		$this->Settings['guestsId'] = 2; // ID группы «Гости»(брать из базы)
	}
	function getRedirectContent($id,$cid){
		global $Content;
		$group = $this->getOne($id);
		$askcontent = $Content->getOne($cid);
		if ($askcontent['redirect']==1){
			$list = $Content->getList($askcontent['id']);
			foreach ($list as $k=>$v){
				if ($retval==0){
					if (isset($group['settings']['superaccess'])) $retval = $k;
					foreach ($group['access'] as $a) if ($k==$a) $retval = $k;
				}
			}
		}
		if ($retval==0){
/*			$retval = $Content->getParent($cid);
			if ($retval==0){
				$list = $Content->getList();
				foreach ($list as $k=>$v){
					if ($retval==0){
						if (isset($group['settings']['superaccess'])) $retval = $k;
						foreach ($group['access'] as $a) if ($k==$a) $retval = $k;
					}
				}
			}*/
			$retval=120;
		}
		return $retval;
	}
	function isAccessGranted($id,$cid){
		$id = floor($id);
		$cid = floor($cid);
		$retval = false;
		$group = $this->getOne($id);
		if (floor($group['id'])>0){
			if (isset($group['settings']['superaccess'])) $retval = true; // Если для группы открыт полный доступ
			if (in_array($cid,$group['access'])) $retval = true; // Если страница находится в списке доступа группы
		}
		return $retval;
	}
	function add($name = '',$access = array()){ /* Добавление новой группы */
		$errors = array();
		$name = trim($name);
		if ($name=='') $errors['name'] = 'Не указано название группы';
		if (!$this->isUniqueName($name)) $errors['name'] = 'Группа с таким названием уже существует';
		if (count($errors)==0){
			$name = addslashes($name);
			$accessstr = '';
			foreach ($access as $v) $accessstr.= '|'.floor($v);
			if ($accessstr) $accessstr.= '|';
			msq("INSERT INTO `".$this->getSetting('table')."` (`name`,`access`) VALUES ('".$name."','".$accessstr."')");
		}
		return $errors;
	}
	function edit($id,$name = '',$access = array()){ /* Редактирование группы */
		$id = floor($id);
		$data = $this->getOne($id);
		if (count($data)==0) $errors['id'] = 'Редактируемой группы не существует';
		if (isset($data['settings']['noedit'])) $errors['edit'] = 'Эту группу редактировать нельзя';
		if (isset($data['settings']['norename'])) $name = $data['name'];
		$errors = array();
		$name = trim($name);
		if ($name=='') $errors['name'] = 'Не указано название группы';
		if (!$this->isUniqueName($name,$id)) $errors['name'] = 'Группа с таким названием уже существует';
		if (count($errors)==0){
			$name = addslashes($name);
			$accessstr = '';
			foreach ($access as $v) $accessstr.= '|'.floor($v);
			if ($accessstr) $accessstr.= '|';
			msq("UPDATE `".$this->getSetting('table')."` SET `name`='$name', `access`='$accessstr' WHERE `id`='$id'");
		}
		return $errors;
	}
	function delete($id){
		global $SiteVisitor;
		$errors = array();
		$id = floor($id);
		if (!$this->checkTypePresence($id)) $errors[] = 'nogroup';
		$data = $this->getOne($id);
		if (isset($data['settings']['undeleteable'])) $errors[] = 'undeleteable';
		if (count($errors)==0){
			$userslist = $SiteVisitor->getList($id);
			foreach ($userslist as $userid) $SiteVisitor->changeGroup($userid,$this->Settings['adminsId']);
			$groupslist = $this->getCacheValue('groupsList');
			$newgroupslist = array();
			foreach ($groupslist as $v) if ($v!=$id) $newgroupslist[] = $v;
			$this->setCacheValue('groupsList',$newgroupslist);
			msq("DELETE FROM `".$this->getSetting('table')."` WHERE `id`='$id'");
		}
		return $errors;
	}
	function getList(){ /* Получение списка групп */
		$retval = $this->getCacheValue('groupsList');
		if (is_array($retval)) return $retval;
		$retval = array();
		$q = msq("SELECT * FROM `".$this->getSetting('table')."` ORDER BY `name`");
		while ($r = msr($q)) { $retval[] = $r['id']; $one = $this->getOne($r['id'],$r); }
		$this->setCacheValue('groupsList',$retval);
		return $retval;
	}
	function getOne($id = 0, $r = array()){ /* Получение информации о конкретной группе */
		global $SiteVisitor;
		$id = floor($id);
		$retval = $this->getCacheValue('group_'.$id);
		if (floor($retval['id'])>0) return $retval;
		$retval = array();
		if (count($r)==0){
			$r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'"));
			if (!is_array($r)) $r = array();
		}
		if (count($r)>0){
			$access = array();
			$accessstr = explode('|',$r['access']);
			foreach ($accessstr as $v) if (floor($v)>0) $access[] = floor($v);
			$settings = $this->explode($r['settings']);
			$retval = array('id'=>$r['id'],'name'=>$r['name'],'access'=>$access,'settings'=>$settings);
		}
		$count = msr(msq("SELECT COUNT(id) AS cnt FROM `".$SiteVisitor->getSetting('table')."` WHERE `type`='".$id."'"));
		$retval['userscount'] = floor($count['cnt']);
		$this->setCacheValue('group_'.$id,$retval);
		return $retval;
	}
	function checkTypePresence($id){ /* Проверка наличия группы по ID */
		$id = floor($id);
		$list = $this->getList();
		return (in_array($id,$list))?$id:0;
	}
	function isUniqueName($name,$exceptid = 0){ /* Проверка на уникальность названия, возвращает true если уникально */
		$retval = true;
		$name = addslashes($name);
		$exceptid = floor($exceptid);
		$q = msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `name`='".$name."'");
		while ($r = msr($q)) if ($r['id']!= $exceptid) $retval = false;
		return $retval;
	}
	function drawAccessCallback($id = 0,$level = -1,$checked = array()){ /* Построение html (разделы для доступа) для форм редактирования и добавления группы */
		global $Content;
		$id = floor($id);
		if (!is_array($checked)) $checked = array();
		if ($one = $Content->getOne($id)){
			if ($id>0){
				$addaccess = $chid = '';
				if (is_array($one['childs'])){
					$addaccess = '|';
					foreach ($one['childs'] as $v) $addaccess.= floor($v).'|';
					if ($addaccess!='|'){
						$chid = $addaccess;
						$addaccess = 'onclick="setGroupIncludesCheck(this);"';
					}
					else $addaccess = '';
				}
				$class = ($level<=0)?'':(($level==1)?' class="sub"':' class="subsub"');
				$check = (in_array($id,$checked))?' checked="checked"':'';
				print '
				<span'.$class.'><label><input id="'.$chid.'" type="checkbox"'.$check.' name="access_'.$id.'" '.$addaccess.' />'.$one['name'].'</label></span>';
			}
			if (is_array($one['childs'])){
				foreach ($one['childs'] as $v) $this->drawAccessCallback($v,$level+1,$checked);
			}
		}
	}
}
?>