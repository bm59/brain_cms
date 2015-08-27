<?
/*
Класс, описывающий тип данных
*/
class DataType extends VirtualClass
{
	function init(){
		$this->Settings['table'] = mstable(ConfigGet('pr_name').'_site','data','types',array(
			"dataset"=>"BIGINT(20)",
			"description"=>"VARCHAR(255)",
			"name"=>"VARCHAR(255)",
			"type"=>"VARCHAR(255)",
			"precedence"=>"BIGINT(20)",
			"settings"=>"TEXT",
			"setting_style_edit"=>"TEXT",
			"setting_style_search"=>"TEXT"
		));
		
		include_once($_SERVER['DOCUMENT_ROOT']."/inc/datatypes/VirtualType.php");

		if ($dir = @opendir($_SERVER['DOCUMENT_ROOT']."/inc/datatypes/")){
			while ($file = readdir($dir)){
				if ($file && $file!=".." && $file!="."){
					if ((preg_match('|.*\.php$|',$file)) && ($file!='VirtualType.php'))
					{
						include_once($_SERVER['DOCUMENT_ROOT']."/inc/datatypes/".$file);
						$this->Settings['all_types'][]=str_replace('.php', '',$file);
					}
				}
			}
			closedir($dir);
		}
		/* $dir = $_SERVER['DOCUMENT_ROOT']."/inc/datatypes/";
		include_once($dir."VirtualType.php");
		include_once($dir."CDDate.php");
		include_once($dir."CDImage.php");
		include_once($dir."CDText.php");
		include_once($dir."CDTextArea.php");
		include_once($dir."CDTextEditor.php");
		include_once($dir."CDVideo.php");
		include_once($dir."CDFile.php");
		include_once($dir."CDFloat.php");
		include_once($dir."CDFloatInfo.php");
		include_once($dir."CDInteger.php");
        include_once($dir."CDGallery.php");
        include_once($dir."CDBoolean.php");
        include_once($dir."CDSpinner.php");
        include_once($dir."CDChoice.php");
        include_once($dir."CDSlider.php"); */
	}

	function add($values, $add_column=false, $table_array=''){
		global $CDDataSet;

		$error='';
		if (!preg_match("|^[a-z0-9_]+$|",$values['name'])) 
		{
			$_SESSION['global_alert'].='Ошибка в названии :'.$values['name'];
			return false;
		}
		$values['description'] = trim($values['description']);
		if (strlen($values['description'])==0)
		{		
			$_SESSION['global_alert'].='Ошибка в описании :'.$values['description'];
			return false;
		}
		
		$values['dataset'] = $CDDataSet->checkPresence($values['dataset'], '', $values['section_id']);
		if ($values['dataset']==0)
		{
				$_SESSION['global_alert'].='Не найден dataset';
				return false;
		}
		
		$settings = $this->implode($values['settings']);
		if (msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `section_id`='".$values['section_id']."' `dataset`='".$values['dataset']."' AND `name`='".$values['name']."'"))) 
		{	
			$_SESSION['global_alert'].='Поле с таким названием уже существует :'.$values['name'];
			return false;
		}
		$precedence = msr(msq("SELECT COUNT(id) AS prec FROM `".$this->getSetting('table')."` WHERE `dataset`='".$values['dataset']."'"));
		$precedence = floor($precedence['prec']);
		
		
		msq("INSERT INTO `".$this->getSetting('table')."` (`section_id`,`dataset`,`description`,`name`,`type`,`precedence`,`settings`,`setting_style_edit`) 
		VALUES ('".$values['section_id']."','".$values['dataset']."','".addslashes($values['description'])."','".$values['name']."','".$values['type']."','$precedence','$settings','".$values['setting_style_edit']."')");

		if ($add_column) $this->add_column($values, $table_array);
		alert_mysql();
		$error.=mysql_error();
		
		return mslastid();
	}
	function add_column($values, $table_array='') {
		$error='';
		if (!$values['dataset']>0) return false;

		foreach($table_array as $tab)
		if ($tab!='')
		{
			msq("ALTER TABLE `$tab` ADD `".$values['name']."` ".$values['table_type']." NULL DEFAULT NULL COMMENT '".$values['description']."'");
			alert_mysql();
			$error.=mysql_error();
		
		}
		return $error;
	}
	function get_dataset_tables($pattern='') {
		global $SiteSections;
		$return=array();
		
		$q=msq("SELECT * FROM `site_site_sections` WHERE `pattern`='$pattern'");
		while ($r=msr($q))
		{
			$Section=$SiteSections->get($r['id']);
			$Section['id'] = floor($Section['id']);

			if ($Section['id']>0)
			{
				$Pattern = new $Section['pattern'];
				$Iface = $Pattern->init(array('section'=>$Section['id']));
				$return['tables'][]=$Iface->getSetting('table');
				$return['ids'][]=$Section['id'];
			}
		}
		return $return; 
	
	}
	function update($id, $values, $table_columns, $table_type_options, $table_array, $section_id)
	{
		$error='';
		if (!$id>0) return;
		
		/* Проверяем есть ли изменения */
		$cur_data=msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`=$id and section_id=".$section_id));
		
		$changes=false;
		foreach ($cur_data as $k=>$v)
		{
			if (isset($values[$k]) && $v!=$values[$k])
			$changes=true;	
		}
		
		/* Если изменили название поля, меняем его в таблице */
		if ($changes)
		{
			$update='';
			
			foreach($values as $k=>$v){ $update.=(($update!='') ? ',':'')."`$k`='$v'";}
			msq("UPDATE `".$this->getSetting('table')."` SET $update WHERE id=$id LIMIT 1");
			print "UPDATE `".$this->getSetting('table')."` SET $update WHERE id=$id LIMIT 1";
			alert_mysql();
			$error.=mysql_error();
	
		}
		
		
		/* Если изменили тип колонки */
 		if (isset($table_columns[$cur_data['name']]) && isset($values['name']) && $cur_data['name']!=$values['name'])
		{
			
			foreach ($table_array as $tab)
			{
				msq("ALTER TABLE `$tab` CHANGE `".$cur_data['name']."` `".$values['name']."` ".$table_columns[$cur_data['name']]['Type']." CHARACTER SET cp1251 COLLATE cp1251_general_ci NULL DEFAULT NULL COMMENT '".$values['description']."'");
				alert_mysql();
				$error.=mysql_error();
				$changes=true;
			}
		} 
		/* Если был изменены настройки колонки */
 		if ($table_columns[$cur_data['name']]['Type']!=$table_type_options)
		{
			foreach ($table_array as $tab)
			{
				msq("ALTER TABLE `$tab` CHANGE `".$values['name']."` `".$values['name']."` ".$table_type_options." NULL DEFAULT NULL COMMENT '".$values['description']."'");
				alert_mysql();
				$error.=mysql_error();
				$changes=true;
			}
			
		}
		if ($changes && $error=='') return true;
		else return $error;

	}
	function delete($id, $table_name){
		if (!$id>0) return;
		$cur_data=msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`=$id"));
		msq("DELETE FROM `".$this->getSetting('table')."` WHERE `id`=$id");
		msq("ALTER TABLE `$table_name` DROP `".$cur_data['name']."`");
	}
	function get_column_info($table_name){
	 	$columns=array();
		$q=msq("SHOW COLUMNS FROM `".$table_name."`");
		while ($r=msr($q))
		{
			$columns[$r['Field']]=$r;
		}
		return $columns;
	}
	function getDataSetList($datasetid, $section_id){
		global $Section;
		$datasetid = floor($datasetid);
		$section_id = floor($section_id);
		$retval = array();
		$q = msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `section_id`=$section_id and `dataset`='".$datasetid."' ORDER BY `precedence`");
		while ($r = msr($q)) $retval[] = array('id'=>$r['id'],'description'=>$r['description'],'name'=>$r['name'],'type'=>$r['type'],'precedence'=>$r['precedence'],'settings'=>$this->explode($r['settings']),'setting_style_edit'=>$r['setting_style_edit']);
		return $retval;
	}
}
?>