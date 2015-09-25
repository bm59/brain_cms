<?
class CDDate extends VirtualType
{
	
	function init($settings){
		$settings['descr']='Дата';
		VirtualType::init($settings);
	}
	function drawEditor($divstyle = ''){
		if (preg_match("|^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$|",$this->getSetting('value')))
		$settings = $this->getSetting('settings');
		
		
		if (preg_match("|^[0-9]{4}\-[0-9]{2}\-[0-9]{2}|",$this->getSetting('value')))
		$this->setSetting('value', msdfromdb($this->getSetting('value')));
		?>
							<script type="text/javascript">
							$(function(){

								$.datepicker.regional['ru'] =
								{
									closeText: 'Закрыть',
									prevText: '&#x3c;Пред',
									nextText: 'След&#x3e;',
									currentText: 'Сегодня',
									monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
									'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
									monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
									'Июл','Авг','Сен','Окт','Ноя','Дек'],
									dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
									dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
									dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
									dateFormat: 'dd.mm.yy',
									firstDay: 1,
									isRTL: false
								};

								$.datepicker.setDefaults($.extend($.datepicker.regional["ru"]));

									$("#<?=htmlspecialchars($this->getSetting('name'))?>").datepicker({
									showOn: "both",
									buttonImage: "/pics/editor/calendar.gif",
									buttonImageOnly: true
								});
                                <?if ($this->getSetting('value')==''){?>$("#<?=htmlspecialchars($this->getSetting('name'))?>").datepicker( "setDate" , "0");<?}?>
							});
							</script>
        <?

        ?>
		<div class="place" id="<?=htmlspecialchars($this->getSetting('name'))?>_calendar" <?=($divstyle!='')?$divstyle:'style="z-index: 11; width: 158px;"'?>>
			<label><?=htmlspecialchars($this->getSetting('description'))?><?=((isset($settings['important']))?' <span class="important">*</span>':'')?></label>
			<div><input  id="<?=htmlspecialchars($this->getSetting('name'))?>" name="<?=htmlspecialchars($this->getSetting('name'))?>" type="text" style="width: 100px; float: left;" value="<?=htmlspecialchars($this->getSetting('value'))?>"/></div>
		</div>

		<span class="clear"></span>
		<?
	}
	function preSave(){
		$errors = array();
		$settings = $this->getSetting('settings');
		$newvalue = trim($_POST[$this->getSetting('name')]);
		if (!preg_match("|^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$|",$newvalue)) $newvalue = '';
		if (($settings['important']>0) && ($newvalue=='')) $newvalue = date("d.m.Y");
		$this->setSetting('value',$newvalue);
		return $errors;
	}
	function postSave(){ return; }
	function getValue(){ return $this->getSetting('value'); }
	function getUpdateSQL($type=''){if ($type=='')return "`".$this->getSetting('name')."`='".msdtodb($this->getSetting('value'))."'"; else return "`".$this->getSetting('name')."`='".msdtodb($this->getSetting('value'))." ".date("H:i:s")."'"; }
	function delete(){ return; }
}
?>