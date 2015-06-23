<?
/*
Базовый класс функционала разделов
*/
class VirtualContent
{
	var $Settings = array();

	function init($settings = array()){
		if (is_array($settings)){
			foreach ($settings as $name=>$value) $this->setSetting($name,$value);
		}
		if ($dir = @opendir($_SERVER['DOCUMENT_ROOT']."/inc/cclasses/")){
			while ($file = readdir($dir)){
				if ($file && $file!=".." && $file!="."){
					if ((preg_match('|.*\.php$|',$file)) && ($file!='VirtualContent.php')) include_once($_SERVER['DOCUMENT_ROOT']."/inc/cclasses/".$file);
				}
			}
			closedir($dir);
		}
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