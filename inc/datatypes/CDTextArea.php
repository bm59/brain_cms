<?
class CDTextArea extends VirtualType
{
	function init($settings){
		$settings['descr']='Текстовый блок';
		$maxlength = (floor($this->getSetting('maxlength'))>0)?floor($this->getSetting('maxlength')):0;
		$this->setSetting('maxlength',$maxlength);
		VirtualType::init($settings);
	}
	function drawEditor(){
		$settings = $this->getSetting('settings');
		?>
		<div class="place">
			<div class="input">
				<label><?=htmlspecialchars($this->getSetting('description'))?><?=((isset($settings['important']))?' <span class="important">*</span>':'')?></label>
				<div><textarea name="<?=htmlspecialchars($this->getSetting('name'))?>"><?=stripslashes(eregi_replace('</textarea>', "&lt;/textarea&gt;", $this->getSetting('value')))?></textarea></div>
		    </div>
		</div>
		<span class="clear"></span>
		<?
	}
	function preSave(){
		$errors = array();
		$settings = $this->getSetting('settings');
		$newvalue = trim($_POST[$this->getSetting('name')]);
		if ($this->getSetting('maxlength')>0) $newvalue = substr($newvalue,0,$this->getSetting('maxlength'));
		if ((isset($settings['important'])) && ($newvalue=='')) $errors[] = 'Заполните поле «'.$this->getSetting('description').'»';
		$this->setSetting('value',$newvalue);
		return $errors;
	}
	function postSave(){ return; }
	function getValue(){ return $this->getSetting('value'); }
	function getUpdateSQL(){ return "`".$this->getSetting('name')."`='".addslashes($this->getSetting('value'))."'"; }
	function delete(){ return; }
}
?>