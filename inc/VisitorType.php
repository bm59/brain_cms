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
	function check_child_access($parent=0,$settings=array(),$level=1){

            if ($level<=3)
            {

	    		$childs=msq("SELECT * FROM `site_site_sections` WHERE `parent`=".$parent);
	    		while ($ch=msr($childs))
	    		{
	    			if (array_key_exists($ch['id'],$settings))
	    			return true;
	    			else  if ($this->check_child_access($ch['id'],$settings,$level+1)) return true;
	    		}

    		}
    		else
    		return false;


	}
	function add($name = '',$access=''){ /* Добавление новой группы */
		$errors = array();
		$name = trim($name);
		if ($name=='') $errors['name'] = 'Не указано название группы';
		if (!$this->isUniqueName($name)) $errors['name'] = 'Группа с таким названием уже существует';
		if (count($errors)==0){
			$name = addslashes($name);
			$accessstr = '';
			msq("INSERT INTO `".$this->getSetting('table')."` (`name`,`access`) VALUES ('".$name."','".$access."')");
		}
		return $errors;
	}
	function edit($id,$name = '',$access = ''){ /* Редактирование группы */
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
			msq("UPDATE `".$this->getSetting('table')."` SET `name`='$name', `access`='$access' WHERE `id`='$id'");
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
			foreach ($accessstr as $v)
			{				$set_val=explode('=',$v);

/*				if ($set_val['1']!='' && $set_val['0']>0)
				{					$set_val_actions=explode(',',$set_val['1']);
					foreach ($set_val_actions as $set_val_actions_item)
					$new_settings[]='action_'.$set_val['0'].'_'.$set_val_actions_item;

				}
				elseif */
				if ($set_val['0']>0)
				$new_settings[$set_val['0']]=clear_array_empty(explode(',',$set_val['1']));

			}
			/*==foreach ($accessstr as $v) if (floor($v)>0) $access[] = floor($v);*/
			$settings = $this->explode($r['settings']);
			$retval = array('id'=>$r['id'],'name'=>$r['name'],'access'=>$access,'settings'=>$settings, 'new_settings'=>$new_settings);
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
        global $SiteSections,$new_settings;
        if ($level=='')  $level=0;
        $sec_list=$SiteSections->getList($id,0,-1);
/*        print '---';
        print_r($new_settings);*/


        $i=0;
        print '<UL id="'.$id.'">';
        foreach ($sec_list as $sl)
        {




        	$child_count=count($SiteSections->getList($sl['id'],0,-1));

        	print '<li id="li_'.$sl['id'].'">';

        	print '<label><input id="section_'.$sl['id'].'" name="section_'.$sl['id'].'" type="checkbox"'.$check.' '.(($_POST['section_'.$sl['id']]=='on' || array_key_exists($sl['id'],$new_settings)) ? ' checked="checked"':'').'/>'.$sl['name'].'</label>';


        	/*Получаем настройки доступных действий*/
        	if ($id>0)
        	{

	            $section_settings=$SiteSections->get($sl['id']);
	            $section_settings=$section_settings['settings']['enable_actions'];

	        	{	        		if ($section_settings=='' && $sl['pattern']!='PFolder')
	        		$section_settings='view,add,edit,delete';

	        		$actions=explode(',',$section_settings);
	        		$action_comments=array('view'=>'просмотр', 'add'=>'добавление', 'edit'=>'редактирование', 'delete'=>'удаление', 'onoff'=>'вкл\откл');
	        		?>
	        			<div class="actions">
	        			<?
	        			foreach ($actions as $act)
	        			if ($act!='')
	        			{	        				?><div><label><input type="checkbox" id="action_<?=$sl['id'].'_'.$act?>" name="action_<?=$sl['id'].'_'.$act?>" <?=(($_POST['action_'.$sl['id'].'_'.$act]=='on' || in_array($act,$new_settings[$sl['id']])) ? ' checked="checked"':'')?>><?=$action_comments[$act]?></label></div><?
	        			}
	        			?>
	        			</div>
	        		<?
	        	}
        	}



           if ($child_count>0)
            {           		$this->drawAccessCallback($sl['id'],$level+1,$checked);
            }

			print '</li>';

            $i++;

        }

        print '</UL>';

	}
}
?>