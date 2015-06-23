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
			"settings"=>"TEXT"
		));
		//include_once($_SERVER['DOCUMENT_ROOT']."/manage/classes/datatypes/VirtualType.php");

		/*if ($dir = @opendir($_SERVER['DOCUMENT_ROOT']."/manage/classes/datatypes/")){
			while ($file = readdir($dir)){
				if ($file && $file!=".." && $file!="."){
					if ((preg_match('|.*\.php$|',$file)) && ($file!='VirtualType.php')) include_once($_SERVER['DOCUMENT_ROOT']."/manage/classes/datatypes/".$file);
				}
			}
			closedir($dir);
		}*/
		$dir = $_SERVER['DOCUMENT_ROOT']."/inc/datatypes/";
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

	}

	function add($values){
		global $CDDataSet;
		if (!preg_match("|^[a-z0-9_]+$|",$values['name'])) return false;
		$values['description'] = trim($values['description']);
		if (strlen($values['description'])==0) return false;
		$values['dataset'] = $CDDataSet->checkPresence($values['dataset']);
		if ($values['dataset']==0) return false;
		$settings = $this->implode($values['settings']);
		if (msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `dataset`='".$values['dataset']."' AND `name`='".$values['name']."'"))) return false;
		$precedence = msr(msq("SELECT COUNT(id) AS prec FROM `".$this->getSetting('table')."` WHERE `dataset`='".$values['dataset']."'"));
		$precedence = floor($precedence['prec']);
		msq("INSERT INTO `".$this->getSetting('table')."` (`dataset`,`description`,`name`,`type`,`precedence`,`settings`) VALUES ('".$values['dataset']."','".addslashes($values['description'])."','".$values['name']."','".$values['type']."','$precedence','$settings')");
	}
	function getDataSetList($datasetid){
		$datasetid = floor($datasetid);
		$retval = array();
		$q = msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `dataset`='".$datasetid."' ORDER BY `precedence`");
		while ($r = msr($q)) $retval[] = array('id'=>$r['id'],'description'=>$r['description'],'name'=>$r['name'],'type'=>$r['type'],'precedence'=>$r['precedence'],'settings'=>$this->explode($r['settings']));
		return $retval;
	}
}
?>