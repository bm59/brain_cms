<?
/*
Класс, описывающий тип «Текстовая строка»
*/
class CDFLOATINFO extends VirtualType
{
	function init($settings){
		VirtualType::init($settings);
	}
	function drawEditor($divstyle = '',$span = true){
		$settings = $this->getSetting('settings');
		$selection = '';
		print $this->getSetting('description').': '.stripslashes($this->getSetting('value'));
		if ($span) print '
			<span class="clear"></span>';
	}
	function preSave(){
		$errors = array();
		$settings = $this->getSetting('settings');
		$newvalue = htmlspecialchars(trim($_POST[$this->getSetting('name')]));
		$newvalue = substr($newvalue,0,$this->getSetting('maxlength'));
		$newvalue = floatval(str_replace(',', '.', $newvalue));
		if ((isset($settings['important'])) && (!is_float($newvalue) || $newvalue=='0')) $errors[] = 'Заполните поле «'.$this->getSetting('description').'»';
		elseif ((isset($settings['important'])) && ($newvalue=='')) $errors[] = 'Заполните поле «'.$this->getSetting('description').'»';
		if ($newvalue!='0')
		$this->setSetting('value',$newvalue);
		return $errors;
	}
	function postSave(){ return; }
	function getValue(){ return $this->getSetting('value'); }
	function getUpdateSQL(){ return "`".$this->getSetting('name')."`='".addslashes(floor($this->getSetting('value')))."'"; }
	function delete(){ return; }
}
?>