<?
class CDSPINNER extends VirtualType
{
	function init($settings){
		$settings['descr']='Спиннер (целое)';
		$settings['help']=array(
				'default=1'=>'Значение по умолчанию',
				'min=1'=>'Минимальное значение',
				'max=10'=>'Максимальное значение',
				'comment=комментарий'=>'Комментарий'
		
		);
		$maxlength = (floor($this->getSetting('maxlength'))>0)?floor($this->getSetting('maxlength')):255;
		$this->setSetting('maxlength',$maxlength);
		VirtualType::init($settings);
	}
	function drawEditor($divstyle = '',$span = true){
		$settings = $this->getSetting('settings');
		?>
		  <script>
		  $(function() {
		    var spinner = $( "input[name='<?=htmlspecialchars($this->getSetting('name'))?>']" ).spinner({
			min:  <?=(($settings['min']!='') ? $settings['min']:'0')?>,
		    <?=(($settings['max']!='') ? ' max: '.$settings['max']:'')?>
			  });
		  });
		  </script>
		<?
		if ($this->getSetting('value')=='' && $settings['default']!='')  $this->setSetting('value', $settings['default']);

		?>
		<div class="place" <?=($divstyle!='')?$divstyle:''?>>
			<label><?=htmlspecialchars($this->getSetting('description'))?><?=((isset($settings['important']))?' <span class="important">*</span>':'')?></label>
			<?if ($settings['comment']!=''){?><small><?=$settings['comment']?></small><?}?>
			<span class="input" style="padding: 10px 0;">
				<input type="text" name="<?=htmlspecialchars($this->getSetting('name'))?>" value="<?=stripslashes($this->getSetting('value'))?>" autocomplete="off" onfocus="$(this).parent().css({'border':'2px solid #c06eff'});"  onblur="$(this).parent().css({'border':'2px solid rgba(220, 220, 220, 1)'});">
			</span>
		</div>
		<?
		if ($span) print '
			<span class="clear"></span>';
	}
	function preSave(){
		$errors = array();
		$settings = $this->getSetting('settings');
		$newvalue = htmlspecialchars(trim($_POST[$this->getSetting('name')]));
		$newvalue = substr($newvalue,0,$this->getSetting('maxlength'));

		if ($newvalue!='' && $newvalue>-1)
		$newvalue = floatval(str_replace(',', '.', $newvalue));

		if ((isset($settings['important'])) && (!is_float($newvalue))) $errors[] = 'Заполните поле «'.$this->getSetting('description').'»';
		if ((isset($settings['important'])) && ($newvalue==='')) $errors[] = 'Заполните поле «'.$this->getSetting('description').'»';
		if ($newvalue!='0')
		$this->setSetting('value',$newvalue);
		return $errors;
	}
	function postSave(){ return; }
	function getValue(){ return $this->getSetting('value'); }
	function getUpdateSQL(){ return "`".$this->getSetting('name')."`='".addslashes($this->getSetting('value'))."'"; }
	function delete(){ return; }
}
?>