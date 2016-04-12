<?
/*
Базовый класс типа данных
*/
class VirtualType
{
	var $Settings = array();

	function init($settings){
		if (is_array($settings)){
			foreach ($settings as $name=>$value) $this->setSetting($name,$value);
		}
		if (trim($this->getSetting('editformid'))=='') $this->setSetting('editformid','editform');
	}
	function getSetting($name){ return $this->Settings[$name]; } // Получение значения, хранящегося в $Settings
	function setSetting($name,$value){ $this->Settings[$name] = $value; }
}
?>