<?
/*
Структура сайта, разделы и сервисы
*/
class SiteSections extends VirtualClass
{
        function init(){
                $this->Settings['table'] = mstable(ConfigGet('pr_name').'_site','sections','',array(
                        "name"=>"VARCHAR(255)",
                        "path"=>"VARCHAR(255)",
                        "pattern"=>"VARCHAR(255)",
                        "parent"=>"BIGINT(20)",
                        "precedence"=>"BIGINT(20)",
                        "isservice"=>"INT(1)",
                        "keywords"=>"TEXT",
                        "title"=>"TEXT",
                        "tags"=>"TEXT",
                        "description"=>"TEXT",
                        "visible"=>"TINYINT(6)",
                        "settings"=>"TEXT"
                ));

        }

        function setPrecedenceBefore($id,$beforeid){
                $id = floor($id);
                $beforeid = floor($beforeid);
                $section = $this->get($id);
                $before = $this->get($beforeid);
                if (floor($section['parent'])==floor($before['parent'])){
                        $precedence = 0;
                        $q = msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `parent`='".$section['parent']."' AND `isservice`='".$section['isservice']."' ORDER BY `precedence` ASC");
                        $prec = $section['precedence'];
                        while ($r = msr($q)){
                                if ($r['id']==$before['id']){
                                        $prec = $precedence;
                                        $precedence++;
                                }
                                if ($r['id']!=$section['id']) msq("UPDATE `".$this->getSetting('table')."` SET `precedence`='$precedence' WHERE `id`='".$r['id']."'");
                                $precedence++;
                        }
                        msq("UPDATE `".$this->getSetting('table')."` SET `precedence`='$prec' WHERE `id`='".$section['id']."'");
                }
        }
        function setPrecedence($id,$prec){
                $id = floor($id);
                $section = $this->get($id);
                msq("UPDATE `".$this->getSetting('table')."` SET `precedence`='$prec' WHERE `id`='".$section['id']."'");
        }
        function setPrecedenceAbove($id, $parent_id){

                $id = floor($id);
                $section = $this->get($id);
                $parent=$this->get($parent_id);
                /*устанавливаем порядок переносимому элементу*/
                msq("UPDATE `".$this->getSetting('table')."` SET `precedence`='".floor($parent['precedence'])."', `parent`=".floor($parent['parent'])." WHERE `id`='".$section['id']."'");

                /*опускаем всех на 1 порядок ниже*/
                $parent_sub=msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `parent`=".floor($parent['parent'])." and `precedence`>=".floor($parent['precedence'])." ORDER BY `precedence` ASC");
                while ($ps=msr($parent_sub))
                if ($ps['id']!=$section['id'])
                {                	msq("UPDATE `".$this->getSetting('table')."` SET `precedence`='".floor($ps['precedence']+1)."' WHERE `id`='".$ps['id']."'");

                }
        }
        function setParent($id,$parent){
                $id = floor($id);
                $parent = floor($parent);
                $section = $this->get($id);
                if ((floor($section['id'])>0) && ($section['parent']!=$parent)){
                        $precedence = 0;
                        $q = msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `parent`='$parent' AND `isservice`='".$section['isservice']."' ORDER BY `precedence` ASC");
                        while ($r = msr($q)){
                                msq("UPDATE `".$this->getSetting('table')."` SET `precedence`='$precedence' WHERE `id`='".$r['id']."'");
                                $precedence++;
                        }
                        $section['parent'] = $parent;
                        msq("UPDATE `".$this->getSetting('table')."` SET `parent`='$parent',`precedence`='$precedence' WHERE `id`='".$section['id']."'");
                        $this->setCacheValue('section_'.floor($section['id']),$section);
                }
                return false;
        }
        function delete($id){
                $id = floor($id);
                $section = $this->get($id);
                if (floor($section['id'])>0){
                        $SectionPattern = new $section['pattern'];
                        $SectionPattern->init(array('section'=>$id));
                        if ($SectionPattern->delete()){
                                msq("DELETE FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'");
                                $this->setCacheValue('section_'.$id,array());
                        }
                }
        }
        function add($values){
                $errors = array();
                $name = htmlspecialchars(trim($values['name'])); if ($name=='') $errors[] = 'Не указано название';
                $path = lower(trim($values['path'])); if (!preg_match('|^[a-z_0-9]+$|',$path)) $errors[] = 'Путь должен состоять из символов латинского алфавита, цифр и символа подчекивания «_»';
                $pattern = '';
                foreach (configGet('registeredPatterns') as $v) if ($v['name']==$values['pattern']) $pattern = $v['name'];
                if ($pattern=='') $errors[] = 'Не указан тип раздела';
                $parent = -1;
                $precedence = 0;
                $isservice = floor($values['isservice']); if ($isservice>1) $errors[] = 'Не указано расположение (раздел или сервис)';
                $keywords = htmlspecialchars(trim($values['keywords']));
                $title = htmlspecialchars(trim($values['title']));
                $description = htmlspecialchars(trim($values['description']));
                $tags = htmlspecialchars(trim($values['tags']));

                $visible = $values['visible'];
                if($visible == "on")$visible = 1;else
                                    $visible = 0;

                $settings = $values['settings']; if (!is_array($settings)) $settings = array();
                if ($isservice>0){
                        $parent = 0;
                        $settings['noeditsettings'] = '';
                        $settings['undeletable'] = '';
                }
                $settings = $this->implode($settings);
                if (count($errors)==0){
                        msq("INSERT INTO `".$this->getSetting('table')."` (`name`,`path`,`pattern`,`parent`,`precedence`,`isservice`,`keywords`,`title`,`description`,`visible`,`settings`,`tags`) VALUES ('$name','$path','$pattern','$parent','$precedence','$isservice','$keywords','$title','$description','$visible','$settings','$tags')");
                }
                return $errors;
        }
        function edit($values){
                $errors = array();
                $name = htmlspecialchars(trim($values['name'])); if ($name=='') $errors[] = 'Не указано название';
                $path = lower(trim($values['path'])); if (!preg_match('|^[a-z_0-9]+$|',$path)) $errors[] = 'Путь должен состоять из символов латинского алфавита, цифр и символа подчекивания «_»';
                $isservice = floor($values['isservice']); if ($isservice>1) $errors[] = 'Не указано расположение (раздел или сервис)';
                $keywords = htmlspecialchars(trim($values['keywords']));
                $title = htmlspecialchars(trim($values['title']));
                $tags = htmlspecialchars(trim($values['tags']));
                $header = htmlspecialchars(trim($values['header']));
                
                $visible = $values['visible'];
                if($visible == "on")
                $visible = 1;else
                $visible = 0;

                $description = htmlspecialchars(trim($values['description']));
                if (count($errors)==0){
                        msq("UPDATE `".$this->getSetting('table')."` SET `name`='$name',`path`='$path',`keywords`='$keywords',`title`='$title',`visible`='$visible',`description`='$description',`tags`='$tags',`header`='$header' WHERE `id`='".$values['id']."'");
                }
                return $errors;
        }
        function get($id,$isservice = 0){
                $id = floor($id);
                $retval = array();
                if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='$id'"))){
                        $retval = $r;
                        $retval['settings'] = $this->explode($r['settings']);
                        if ($r['pattern']=='PFolder') $retval['settings']['noedit'] = 1;
                }
                return $retval;
        }
        function getTitle($id, $dop_ver=''){
                if (trim(configGet('contenttitle'))!='') $defaulttitle = configGet('contenttitle');
                else{
                        $SiteSettings = new SiteSettings;
                        $SiteSettings->init();
                        $Section = $this->get($id); $Section['id'] = floor($Section['id']);
                        $defaulttitle = $SiteSettings->getOne($SiteSettings->getIdByName('site_main_title'.$dop_ver));
                        $defaulttitle = stripslashes($defaulttitle['value']);
                        if ($Section['id']>0){
                                $defaulttitle = stripslashes($Section['name']);
                                if ($Section['title']!='') $defaulttitle = stripslashes($Section['title']);
                                $Pattern = new $Section['pattern'];
                                $Iface = $Pattern->init(array('section'=>$Section['id']));
                        }
                }
                if ($Section['id']!=6)
                $defaulttitle.= ' - '.ConfigGet('pr_doptit');
                return $defaulttitle;
        }
        function getDescription($id, $dop_ver=''){
                if (trim(configGet('contentdescription'))!='') return configGet('contentdescription');
                $SiteSettings = new SiteSettings;
                $SiteSettings->init();
                $Section = $this->get($id); $Section['id'] = floor($Section['id']);
                if (!$id>0)
                {
                	$defaulttitle = $SiteSettings->getOne($SiteSettings->getIdByName('site_main_descript'.$dop_ver));
                	$defaulttitle = stripslashes(htmlspecialchars($defaulttitle['value']));
                }

                if ($Section['description']!='') $defaulttitle = stripslashes(htmlspecialchars($Section['description']));
                return $defaulttitle;
        }
        function getKeywords($id, $dop_ver=''){
                if (trim(configGet('contentkeywords'))!='') return configGet('contentkeywords');
                $SiteSettings = new SiteSettings;
                $SiteSettings->init();
                $Section = $this->get($id); $Section['id'] = floor($Section['id']);
                $defaulttitle = $SiteSettings->getOne($SiteSettings->getIdByName('site_main_keywords'.$dop_ver));
                $defaulttitle = stripslashes(htmlspecialchars($defaulttitle['value']));
                if ($Section['keywords']!='') $defaulttitle = stripslashes(htmlspecialchars($Section['keywords']));
                return $defaulttitle;
        }
        function getIdByPath($path){
                $subs = explode("/",$path);
                $current_id = 0;
                $current_row = array();
                foreach($subs as $k) if (($k!="") && (!preg_match('|^[0-9]+$|',$k))){
                        if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `path`='$k' AND `parent`='$current_id'"))){
                                $current_id = $r["id"];
                                $current_row = $r;
                        }else return 0;
                }
                return $current_id;
        }
        function getPath($id){
                $parents = $this->getParentsList($id);
                $retval = '';
                if (count($parents)>0){
                        $retval = '/';
                        foreach ($parents as $p){
                                $section = $this->get($p);
                                $retval.= $section['path'].'/';
                        }
                }
                return $retval;
        }
        function getList($parent,$public = 0,$isservice = 0){
                $parent = floor($parent);
                $public = floor($public);
                /*$isservice = floor($isservice);*/
                $retval = array();
                $q = msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `parent`='$parent' ".(($isservice>-1) ? " AND `isservice`='$isservice'" : "")." ORDER BY `precedence`, `name`");
                while ($r = msr($q)){
                        $section = $this->get($r['id'],$isservice);
                        if (floor($section['settings']['public'])==$public) $retval[] = $section;
                }
                return $retval;
        }
        function getParentsList($id){
                $id = floor($id);
                $retval = array();
                while ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='$id'"))){
                        $retval[] = $r['id'];
                        $id = $r['parent'];
                }
                return array_reverse($retval);
        }
        function echoSectionList($parent,$num,$public,$isservice,$mode, $printnoparent=false){                global $SiteVisitor,$VisitorType,$Content, $aut_mode;

                $user = $SiteVisitor->getOne($_SESSION['visitorID']);
                $group = $VisitorType->getOne($user['type']);
 /*       		if ($printnoparent)
        		{
	        		$i=0;
	        		print '<p>';
	        		$noparentlist = $this->getList('0','','');
					foreach ($noparentlist as $section)
					{						$cid = $Content->getIdByPath($this->getPath($section['id']));
                		$accessgranted = $VisitorType->isAccessGranted($group['id'],$cid);
                		if ($accessgranted || $section['parent']==-1)
	                		{
							if ($i==0)
							print '<a href="#'.$section['id'].'">'.$section['name'].'</a>';
							else
							print '   |   <a href="#'.$section['id'].'">'.$section['name'].'</a>';
							$i++;
						}
					}
					print '</p>';

				}*/



                $list = $this->getList($parent,$public,$isservice);
                $counter = 0;

                foreach ($list as $section){
                		$cid = $section['id'];
                		$accessgranted = $VisitorType->isAccessGranted($group['id'],$section['id']);

                		$accessgranted_settings=array_key_exists($section['id'],$group['new_settings']);

                		$child_access =  $VisitorType->check_child_access($section['id'],$group['new_settings']);


                		if (($accessgranted || $accessgranted_settings || $child_access || $section['parent']==-1))
                		{
		                        $counter++;
		                        foreach (configGet('registeredPatterns') as $v) if ($v['name']==$section['pattern']) $pattern = $v['description'];
		                        $anchorcode = '';
		                        if (($mode=='development') || ($section['parent']==-1)){ // если в тестовом режиме
		                                $anchorcode = '<img id="'.$section['id'].'|'.$section['parent'].'" class="anchordrop" data-parent="'.$section['parent'].'" src="/pics/editor/anchor.gif" width="18" height="18" />';
		                        }
		                        elseif ((!isset($section['settings']['undrop'])) && (!isset($section['settings']['nodestination']))){ // таких вроде нет
		                                $anchorcode = '<img id="'.$section['id'].'" class="anchordrop" data-parent="'.$section['parent'].'" src="/pics/editor/anchor.gif" width="18" height="18" alt="construct"/>';
		                        }
		                        elseif (!isset($section['settings']['undrop'])){ // созданные админом
		                                $anchorcode = '<img id="'.$section['id'].'|'.$section['parent'].'" class="anchordrop" data-parent="'.$section['parent'].'" src="/pics/editor/anchor.gif" width="18" height="18" />';
		                        }
		                        elseif (!isset($section['settings']['nodestination'])){ // конструкторные разделы
		                                $anchorcode = '<img id="'.$section['id'].'" class="anchordrop" src="/pics/editor/anchor.gif" width="18" height="18" alt="construct"/>';
		                        }


		                        if ($section['isservice']==1) $anchorcode = '';

		                        if ($mode=='development' || in_array('view', $group['new_settings'][$section['id']]))
		                        $editcode = '<a href="./?section='.$section['id'].'">'.$section['name'].'</a>';
		                        else $editcode = $section['name'];




		                        if ((isset($section['settings']['noedit'])) || ($section['pattern']=='PFolder' || $section['pattern']=='PConference'))
		                        $editcode = $section['name'];
		                        if ($section['pattern']=='PFolder' && $section['parent']=='0')
		                        $editcode = '<a name="'.$section['id'].'"></a>'.$section['name'];
		                        if ($section['pattern']!='PFolder' && $section['parent']=='0')
		                        $editcode = '<a name="'.$section['id'].'"></a>'.'<a href="./?section='.$section['id'].'">'.$section['name'].'</a>';
		                        $h4class = (count($this->getParentsList($section['id']))>1)?' class="list'.count($this->getParentsList($section['id'])).'"':'';

		                        if (stripos($this->getPath($section['id']), '/control/')!== false && str_replace('/control/', '',$this->getPath($section['id']))!='')
		                        $editcode = '<a name="'.$section['id'].'"></a>'.'<a href="/manage'.$this->getPath($section['id']).'">'.$section['name'].'</a>';

		                        if (stripos($this->getPath($section['id']), '/access/')!== false && str_replace('/access/', '',$this->getPath($section['id']))!='')
		                        $editcode = '<a name="'.$section['id'].'"></a>'.'<a href="/manage'.$this->getPath($section['id']).'">'.$section['name'].'</a>';

		                        if (stripos($this->getPath($section['id']), '/log/')!== false && str_replace('/log/', '',$this->getPath($section['id']))!='')
		                        {

		                        	$ss = $this->get($this->getIdByPath('/control/log/'));
		                        	$editcode = '<a name="'.$section['id'].'"></a>'.'<a href="/manage/control/contents/?section='.$ss['id'].'">'.$section['name'].'</a>';
		                        }

		                        ?>
		                        <tr>
		                                <td class="t_left"><h4<?=$h4class?>><strong><?=$num.$counter.'.'?><?=$anchorcode?></strong><?=$editcode?><small><?=$this->getPath($section['id'])?></small></h4></td>
		                                <td class="t_left t_nowrap"><?=$pattern?></td>
		                                <td class="t_32width">
		                                <?
		                                if (isset($section['settings']['noeditsettings']) || $mode!='development'){
		                                        ?>
		                                        <span class="button txtstyle disabled">
		                                        	<input type="button" style="background-image: url(/pics/editor/settings-disabled.png)" title="Настройки недоступны" onclick="return false;" />
		                                        </span>
		                                        <?
		                                }
		                                else{
		                                        ?>
		                                        <span class="button txtstyle">
		                                        	<input type="button" style="background-image: url(/pics/editor/settings.png)" title="Настройки" onclick="window.location.href = './?edit=<?=$section['id']?>'" />
		                                        </span>
		                                        <?
		                                }
		                                ?>
		                                </td>
		                                <td class="t_32width">
		                                <?
		                                if ((isset($section['settings']['undeletable'])) && ($mode!='development')){
		                                        ?>
		                                        <span class="button txtstyle disabled">
		                                        	<input type="button" style="background-image: url(/pics/editor/delete-disabled.gif)" title="Невозможно удалить" onclick="return false;" />
		                                        </span>
		                                        <?
		                                }
		                                else{
		                                        ?>
		                                        <span class="button txtstyle">
		                                        	<input type="button" style="background-image: url(/pics/editor/delete.gif)" title="Удалить" onclick="if (confirm('Вы действительно хотите удалить этот раздел?')) window.location.href = './?delete=<?=$section['id']?>';" />
		                                        </span>
		                                <?
		                                }
		                                ?>
		                                </td>
		                        </tr>
		                        <?
                        }
                        $this->echoSectionList($section['id'],$num.$counter.'.',$public,$isservice,$mode);
                }
        }
}
?>