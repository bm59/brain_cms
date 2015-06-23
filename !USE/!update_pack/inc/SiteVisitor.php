<?
/*
�����, ����������� ���������� �����, ���������� � ���, ��� � �.�.
*/
class SiteVisitor extends VirtualClass
{
	function init(){
		global $Storage;
		$this->Settings['table'] = mstable(ConfigGet('pr_name').'_bk','users','info',array(
			"regdate"=>"DATE",
			"login"=>"VARCHAR(255)",
			"pswd"=>"VARCHAR(255)",
			"type"=>"BIGINT(20)",
			"firstname"=>"VARCHAR(255)",
			"secondname"=>"VARCHAR(255)",
			"parentname"=>"VARCHAR(255)",
			"email"=>"VARCHAR(255)",
			"picture"=>"BIGINT(20)",
			"settings"=>"TEXT"
		));
		$this->Settings['logs'] = mstable(ConfigGet('pr_name'),'logs','',array(
                        "date"=>"DATETIME",
                        "sectionname"=>"VARCHAR(255)",
                        "comment"=>"VARCHAR(255)",
                        "user_id"=>"VARCHAR(255)",
                        "section_id"=>"VARCHAR(255)",
                        "href"=>"VARCHAR(255)"

        ));
		$this->Settings['iconsstorage'] = $Storage->getStorage(0,array('path'=>'/users/icons/','name'=>'������ ��� ������������� ���-�����','imgw'=>60,'imgwtype'=>1,'imgh'=>60,'imghtype'=>1,'exts'=>array('jpg','gif','jpeg'),'images'=>1));
	}
	function isAuth(){
		$user = $this->getOne(sessionGet('visitorID'));
		if (isset($user['settings']['engage'])) return true;
		return false;
	}
	function SaveLog($sec_id, $comment='', $dophref=0){	  global $SiteSections;

	  $SiteSettings = new SiteSettings;
      $SiteSettings->init();

      $keep_logs=$SiteSettings->getOne($SiteSettings->getIdByName('day_kepp_logs'));

      if ($keep_logs['value']>0)
      msq("DELETE FROM `".$this->getSetting('logs')."` WHERE datediff( now( ) , `date` ) >".$keep_logs['value']);

	  $section=$SiteSections->get($sec_id);
      msq("INSERT INTO `".$this->getSetting('logs')."` (`date`,`sectionname`,`comment`,`user_id`,`href`, `section_id`) VALUES (NOW(), '".$section['name']."', '".$comment."', '".sessionGet('visitorID')."', '".$SiteSections->getPath($sec_id).$dophref."', '".$sec_id."' )");
	  return false;
	}
	function auth($id){
		$user = $this->getOne($id);
		if (floor($user['id'])>0){
			if (isset($user['settings']['engage'])){
				if (sessionGet('visitorID')!=$user['id']){
					$time = mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"));
					$settings = $user['settings'];
					$settings['lasttime'] = $time;
					msq("UPDATE `".$this->getSetting('table')."` SET `settings`='".$this->implode($settings)."' WHERE `id`='".$user['id']."'");
				}
				sessionSet('visitorID',$user['id']);
				return $user;
			}
		}
		return false;
	}
	function unAuth(){ sessionSet('visitorID',-1); }
	function getIdByLoginAndPswd($login,$pswd,$md5pswd = ''){
		$retval = 0;
		$login = upper(trim($login));
		$pswd = (trim($pswd))?md5(trim($pswd)):trim($md5pswd);
		if ((preg_match('|^[a-zA-Z\s0-9]+$|',$login)) && (preg_match('|^[a-zA-Z_0-9]+$|',$pswd))){
			$r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE UPPER(`login`)=UPPER('$login') AND `pswd`='$pswd'"));
			if (is_array($r)) $retval = $r['id'];
		}
		return $retval;
	}
	function getRedirectContent($id,$cid){
		global $VisitorType;
		$user = $this->getOne($id);
		return $VisitorType->getRedirectContent($user['type'],$cid);
	}
	function add($data){ // ���������� ������ ������������
		global $VisitorType,$Storage;
		$errors = array();
		$data['picture']['id'] = floor($data['picture']['id']);
		if (strlen($data['secondname'])==0) $errors['secondname'] = '�� ������� �������';
		if (strlen($data['firstname'])==0) $errors['firstname'] = '�� ������� ���';
		if (strlen($data['login'])<3) $errors['loginlength'] = '����� ������ �������� �� ����� ��� �� 3-� ��������';
		if (!preg_match('|^[a-zA-Z\s0-9]+$|',$data['login'])) $errors['login'] = '����� ������ �������� �� ���� ���������� ��������, �������� � ����';
		elseif (!$this->isUniqueLogin($data['login'])) $errors['loginunique'] = '������������ � ����� ������� ��� ���������������';
		if (strlen($data['pswd'])<4) $errors['pswdlength'] = '������ ������ �������� �� ����� ��� �� 4-� ��������';
		if (!preg_match('|^[a-zA-Z_0-9]+$|',$data['pswd'])) $errors['pswd'] = '������ ������ �������� �� ���� ���������� ��������, ����� ������������� � ����';
		/*if (!checkEmail($data['email'])) $errors['email'] = '������ ������������ email';*/
		$data['type'] = $VisitorType->checkTypePresence($data['type']);
		if ($data['type']<1) $errors['type'] = '�� ������� ������, ������������ ����� �������';
		if (count($errors)==0){
			$data['regdate'] = date("Y-m-d");
			msq("INSERT INTO `".$this->getSetting('table')."` (`regdate`,`login`,`pswd`,`type`,`firstname`,`secondname`,`parentname`,`email`,`picture`, `settings`) VALUES ('".$data['regdate']."','".$data['login']."','".md5($data['pswd'])."','".$data['type']."','".$data['firstname']."','".$data['secondname']."','".$data['parentname']."','".$data['email']."','".$data['picture']['id']."', '|engage|')");
			$uniqueid = mslastid();
			if ($newfile = $Storage->getFile($data['picture']['id'])) $Storage->renameFile($newfile['id'],'bk_users','icon',$uniqueid);
		}
		return $errors;
	}
	function edit($id,$data){ /* �������������� ������������ */
		global $VisitorType,$Storage;
		$id = floor($id);
		$olddata = $this->getOne($id);
		$errors = array();
		if (count($olddata)==0) $errors['id'] = '�������������� ������������ �� ����������';
		if (isset($olddata['settings']['noedit'])){ $errors['edit'] = '����� ������������ ������������� ������'; return $errors; }
		if ($olddata['picture']['id']!=$data['picture']['id']){
			$Storage->deleteFile($olddata['picture']['id']);
			if ($newfile = $Storage->getFile($data['picture']['id'])){
				$data['picture']['id'] = $newfile['id'];
				$Storage->renameFile($newfile['id'],'bk_users','icon',$olddata['id']);
			}
			msq("UPDATE `".$this->getSetting('table')."` SET `picture`='".$data['picture']['id']."' WHERE `id`='$id'");
		}
		if (isset($olddata['settings']['norename'])){
			$data['secondname'] = $olddata['secondname'];
			$data['firstname'] = $olddata['firstname'];
			$data['parentname'] = $olddata['parentname'];
		}
		else{
			if ($data['secondname']=='') $errors['secondname'] = '�� ������� �������';
			if ($data['firstname']=='') $errors['firstname'] = '�� ������� ���';
		}
		if (isset($olddata['settings']['nologinchange'])) $data['login'] = $olddata['login'];
		else{
			if (strlen($data['login'])<3) $errors['loginlength'] = '����� ������ �������� �� ����� ��� �� 3-� ��������';
			if (!preg_match('|^[a-zA-Z\s0-9]+$|',$data['login'])) $errors['login'] = '����� ������ �������� �� ���� ���������� ��������, �������� � ����';
			elseif (!$this->isUniqueLogin($data['login'],$id)) $errors['loginunique'] = '������������ � ����� ������� ��� ���������������';
		}
		if ($data['pswd']!=''){
			if (strlen($data['pswd'])<4) $errors['pswdlength'] = '������ ������ �������� �� ����� ��� �� 4-� ��������';
			if (!preg_match('|^[a-zA-Z_0-9]+$|',$data['pswd'])) $errors['pswd'] = '������ ������ �������� �� ���� ���������� ��������, ����� ������������� � ����';
		}
		/*if (!checkEmail($data['email'])) $errors['email'] = '������ ������������ email';*/
		$data['type'] = $VisitorType->checkTypePresence($data['type']);
		if ($data['type']<1) $errors['type'] = '�� ������� ������, ������������ ����� �������';
		if (count($errors)==0){
			msq("UPDATE `".$this->getSetting('table')."` SET `login`='".$data['login']."',`type`='".$data['type']."',`firstname`='".$data['firstname']."',`secondname`='".$data['secondname']."',`parentname`='".$data['parentname']."',`email`='".$data['email']."'".(($data['pswd']!='')?",`pswd`='".md5($data['pswd'])."'":"")." WHERE `id`='$id'");
		}
		return $errors;
	}
	function delete($id){ // �������� ������������
		global $Storage;
		$errors = array();
		$id = floor($id);
		if (!$this->checkUserPresence($id)) $errors[] = 'nouser';
		$data = $this->getOne($id);
		if (isset($data['settings']['undeletable'])) $errors[] = 'undeletable';
		if (count($errors)==0){
			$Storage->deleteFile($data['picture']['id']);
			msq("DELETE FROM `".$this->getSetting('table')."` WHERE `id`='$id'");
		}
		return $errors;
	}
	function changePassword($id,$oldpswd,$newpswd){
		$errors = array();
		$id = $this->checkUserPresence($id);
		$user = $this->getOne($id);
		if ($id<1) $errors['userid'] = '������ ������������ �� ����������';
		if (md5($oldpswd)!=$user['pswd']) $errors['oldpswd'] = '������ ������ �������';
		if (strlen($newpswd)<4) $errors['pswdlength'] = '������ ������ �������� �� ����� ��� �� 4-� ��������';
		if (!preg_match('|^[a-zA-Z_0-9]+$|',$newpswd)) $errors['pswd'] = '������ ������ �������� �� ���� ���������� ��������, ����� ������������� � ����';
		if (count($errors)==0){
			$user = $this->getCacheValue('user_'.$id);
			$user['pswd'] = md5($oldpswd);
			$this->setCacheValue('user_'.$id,$user);
			msq("UPDATE `".$this->getSetting('table')."` SET `pswd`='".md5($newpswd)."' WHERE `id`='$id'");
		}
		return $errors;
	}
	function changeGroup($id,$newgroupid){ // ����� ������ � ������������
		global $VisitorType;
		$id = $this->checkUserPresence($id);
		$newgroupid = $VisitorType->checkTypePresence($newgroupid);
		if (($id<1) || ($newgroupid==0)) return false;
		$user = $this->getCacheValue('user_'.$id);
		if (count($user)>0){
			$user['type'] = $newgroupid;
			$this->setCacheValue('user_'.$id,$user);
		}
		msq("UPDATE `".$this->getSetting('table')."` SET `type`='$newgroupid' WHERE `id`='$id'");
	}
	function switchOnOff($id,$type){
		$id = $this->checkUserPresence($id);
		if ($id>0){
			$user = $this->getOne($id);
			if (!isset($user['settings']['noswitch'])){
				unset($user['settings']['engage']);
				if ($type=='on') $user['settings']['engage'] = '';
				msq("UPDATE `".$this->getSetting('table')."` SET `settings`='".$this->implode($user['settings'])."' WHERE `id`='$id'");
			}
		}
	}
	function getList($group = 0,$ordertype = 0){ // ��������� ������ �������������
		global $VisitorType;
		$retval = array();
		$ordertypes = array('`secondname` ASC,`firstname` ASC,`parentname` ASC','`secondname` DESC,`firstname` DESC,`parentname` DESC','`type` ASC','`type` DESC');
		$order = ($ordertypes[floor($ordertype)]!='')?' ORDER BY '.$ordertypes[floor($ordertype)]:'';
		$group = $VisitorType->checkTypePresence($group);
		$conditions = ' WHERE `id`>0';
		if ($group>0) $conditions.= " AND `type`='$group'";
		$q = msq("SELECT * FROM `".$this->getSetting('table')."`".$conditions.$order);
		while ($r = msr($q)) { $retval[] = $r['id']; $one = $this->getOne($r['id'],$r); }
		return $retval;
	}
	function getOne($id = 0, $r = array()){ /* ��������� ���������� � ���������� ������������ */
		global $Storage;
		$id = floor($id);
		// $retval = $this->getCacheValue('user_'.$id);
		// if (is_array($retval)) return $retval;
		$retval = array();
		if (count($r)==0){
			$r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'"));
			if (!is_array($r)) $r = array();
		}
		if (count($r)>0){
			$settings = $this->explode($r['settings']);
			$retval = array('id'=>$r['id'],'regdate'=>msdfromdb($r['regdate']),'login'=>$r['login'],'pswd'=>$r['pswd'],'type'=>$r['type'],
							'firstname'=>$r['firstname'],'secondname'=>$r['secondname'],'parentname'=>$r['parentname'],'email'=>$r['email'],
							'settings'=>$settings,'picture'=>$Storage->getFile($r['picture']));
		}
		// $this->setCacheValue('user_'.$id,$retval);
		return $retval;
	}

	function getMany($ids= array()){ /* ��������� ���������� � ���������� ������������ */
		global $Storage;

		$where=array();
		foreach($ids as $id) {
            if(!in_array($id, $where))
                $where[] = $id;
		}

		if(!sizeof($where)) return array();

		$where = "id IN (".implode(",",$where).")";


		$retval = array();

			$res= (msq("SELECT * FROM `".$this->getSetting('table')."` WHERE ".$where.""));

        while($row = msr($res)) {
            $retval [$row['id']] =  $row ;
        }


		if (count($retval )>0){
			foreach($retval  as $r) {
                $settings = $this->explode($r['settings']);
                $r= array('id'=>$r['id'],'regdate'=>msdfromdb($r['regdate']),'login'=>$r['login'],'pswd'=>$r['pswd'],'type'=>$r['type'],
                                'firstname'=>$r['firstname'],'secondname'=>$r['secondname'],'parentname'=>$r['parentname'],'email'=>$r['email'],
                                'settings'=>$settings,'picture'=>$Storage->getFile($r['picture']));
			}
		}
		return $retval;
	}


	function checkUserPresence($id){ // �������� ������� ������������ �� ID
		$id = floor($id);
		$list = $this->getList();
		return (in_array($id,$list))?$id:0;
	}
	function isUniqueLogin($login,$exceptid = 0){ /* �������� �� ������������ ������, ���������� true ���� ��������� */
		$retval = true;
		$login = addslashes($login);
		$exceptid = floor($exceptid);
		$q = msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `login`='".$login."'");
		while ($r = msr($q)) if ($r['id']!= $exceptid) $retval = false;
		return $retval;
	}
}
?>