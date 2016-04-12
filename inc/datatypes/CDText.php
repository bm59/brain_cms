<?
class CDText extends VirtualType
{
	function init($settings){
		$settings['descr']='Текстовое поле';
		$settings['help']=array(
				'ucfirst'=>'Делает первую букву заглавной',
				'editable'=>'Редактируемый'
		);
		$maxlength = (floor($this->getSetting('maxlength'))>0)?floor($this->getSetting('maxlength')):255;
		$this->setSetting('maxlength',$maxlength);
		VirtualType::init($settings);
	}
	function drawEditor($divstyle = '',$span = true){
		$settings = $this->getSetting('settings');
		$selection = '';
		if ($this->getSetting('optionvalues')!=''){
			?>
			<script>
				var auto_<?=htmlspecialchars($this->getSetting('name'))?>_values = new Array(<?=$this->getSetting('optionvalues')?>);
				var auto_<?=htmlspecialchars($this->getSetting('name'))?>_keyTyped = 0;
			</script>
			<?
			$selection = 'id="auto_'.htmlspecialchars($this->getSetting('name')).'" onkeypress="javascript:auto_onkeypress(this, event.charCode, event.keyCode);" onkeyup="javascript:auto_onchange(this);"';
		}
		if (isset($settings['ucfirst']))
		{        	?>
			<script>
			function ucfirst( str )
			{
				var f = str.charAt(0).toUpperCase();
				return f + str.substr(1, str.length-1);
			}

			$(function()
			{
			    $("[name=<?=htmlspecialchars($this->getSetting('name'))?>]").keyup(function ()
			    {
                   $(this).val(ucfirst($(this).val()));
			   	   return false;
			    });
			});
			</script>
			<?
		}
		?>
		<div class="place" <?=($divstyle!='')?$divstyle:''?>>
			<label><?=htmlspecialchars($this->getSetting('description'))?><?=((isset($settings['important']))?' <span class="important">*</span>':'')?></label>
			<span class="input">
				<input type="text" <?=$selection?> <?if ($this->getSetting('name')=='filelink') print "id=".htmlspecialchars($this->getSetting('name')) ?> name="<?=htmlspecialchars($this->getSetting('name'))?>"  maxlength="<?=$this->getSetting('maxlength')?>" value="<?=stripslashes(htmlspecialchars($this->getSetting('value')))?>" />
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