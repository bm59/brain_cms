<?
/*
Базовый класс шаблонов разделов
*/

class VirtualPattern
{
	var $Settings = array();

	function init($settings = array()){
		if (is_array($settings)){
			foreach ($settings as $name=>$value) $this->setSetting($name,$value);
		}
		if ($dir = @opendir($_SERVER['DOCUMENT_ROOT']."/inc/patterns/")){
			while ($file = readdir($dir)){
				if ($file && $file!=".." && $file!="."){
					if ((preg_match('|.*\.php$|',$file)) && ($file!='VirtualPattern.php')) include_once($_SERVER['DOCUMENT_ROOT']."/inc/patterns/".$file);
				}
			}
			closedir($dir);
		}
	}
	function get_default_type ($datatype)
	{
		$ttype = 'TEXT';
		switch ($datatype['type']){
			case 'CDDate':
				$ttype = 'DATE';
				break;
			case 'CDText':
				if ($datatype['settings']['maxlength']>0)
					$ttype = 'VARCHAR('.$datatype['settings']['maxlength'].')';
					else $ttype = 'VARCHAR(255)';
					break;
			case 'CDInteger':
				$ttype = 'BIGINT(20)';
				break;
			case 'CDImage':
				$ttype = 'BIGINT(20)';
				break;
			case 'CDFile':
				$ttype = 'VARCHAR(255)';
				break;
			case 'CDFloat':
				$ttype = 'FLOAT';
				break;
			case 'CDBoolean':
				$ttype = 'INT(1)';
				break;
			case 'CDSpinner':
				$ttype = 'BIGINT(20)';
				break;
			case 'CDChoice':
				$ttype = 'VARCHAR(1000)';
				break;
			case 'CDSlider':
				$ttype = 'VARCHAR(255)';
				break;
			case 'CDColorStatus':
					$ttype = 'BIGINT(20)';
				break;
			case 'CDGallery':
				$ttype = 'VARCHAR(1000)';
				break;
		}
		return $ttype;
	}
	function createDataSetTable($datasetid,$uid = 0,$tablefields = array()){
		$uid = floor($uid);
		global $CDDataSet;
		$datasetid = $CDDataSet->checkPresence($datasetid);
		if ($datasetid>0)
		{
			$dataset = $CDDataSet->get($datasetid, $uid);
			foreach ($dataset['types'] as $t){

				$ttype=$this->get_default_type($t);
				$tablefields[$t['name']] = $ttype.' COMMENT "'.$t['description'].'"';
			}
			return mstable(ConfigGet('pr_name').'_site',lower($this->getSetting('name')),$dataset['name'].(($uid>0)?'_'.$uid:''),$tablefields);
		}
		return '';
	}
	function getSetting($name){ return $this->Settings[$name]; } // Получение значения, хранящегося в $Settings
	function setSetting($name,$value){ $this->Settings[$name] = $value; }
	function implode($settings = array()){ // Формирует строку вида |name|name=value|name|name|...
		if (!is_array($settings)) $settings = array();
		$retval = '|';
		$doubles = array();
		foreach ($settings as $k=>$v){
			$k = lower(trim($k));
			$v = trim($v);
			if (($k!='') && (!in_array($k,$doubles))){
				$doubles[] = $k;
				if ($v!='') $retval.= $k.'='.$v.'|';
				else $retval.= $k.'|';
			}
		}
		if ($retval=='|') $retval = '';
		return $retval;
	}
	function explode($settings = ''){ // Формирует массив из строки вида |name|name=value|name|name|...
		$settings = explode('|',trim($settings));
		$retval = array();
		foreach ($settings as $v){
			$v = trim($v);
			if ($v!=''){
				$values = explode('=',$v);
				$retval[lower(trim($values[0]))] = trim($values[1]);
			}
		}
		return $retval;
	}
}
?>