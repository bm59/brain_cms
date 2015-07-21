<?
/*
Класс, описывающий тип «Текстовая строка»
*/
class CDBOOLEAN extends VirtualType
{
	function init($settings){
		$maxlength = (floor($this->getSetting('maxlength'))>0)?floor($this->getSetting('maxlength')):255;
		$this->setSetting('maxlength',$maxlength);
		VirtualType::init($settings);
	}
	function drawEditor($divstyle = '',$span = true){
		$settings = $this->getSetting('settings');
			?>
			<script>
			$(document).ready(function() {
				   $('input[name="<?=htmlspecialchars($this->getSetting('name'))?>"]').change(function() {
				        if ($(this).prop("checked"))
                        $(this).parents('.place').find('label span').html('[да]');
                        else
                        $(this).parents('.place').find('label span').html('[нет]');
				    });
			});
			</script>
		<div class="place" <?=($divstyle!='')?$divstyle:''?>>
					<label><?=htmlspecialchars($this->getSetting('description'))?>&nbsp;<span>[<?=(($this->getSetting('value') || ($this->getSetting('value')=='' && $settings['default']==1)) ? 'да':'нет')?>]</label>
					<div class="styled">
						<input type="checkbox" name="<?=htmlspecialchars($this->getSetting('name'))?>" id="checkbox" class="checkbox" <?=(($this->getSetting('value') || ($this->getSetting('value')=='' && $settings['default']==1)) ? 'checked="checked"':'')?>>
						<label for="checkbox"></label>
					</div>
		</div>
		<?
		if ($span) print '
			<span class="clear"></span>';
	}
	function preSave(){
		$errors = array();
		$settings = $this->getSetting('settings');

		if ($_POST[$this->getSetting('name')]=='on') $newvalue=1;
		else $newvalue=0;

		$this->setSetting('value',$newvalue);
		return $errors;
	}
	function postSave(){ return; }
	function getValue(){ return $this->getSetting('value'); }
	function getUpdateSQL(){return "`".$this->getSetting('name')."`='".addslashes(floor($this->getSetting('value')))."'"; }
	function delete(){ return; }
}
?>