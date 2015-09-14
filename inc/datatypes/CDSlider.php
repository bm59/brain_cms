<?
class CDSlider extends VirtualType
{
	function init($settings){

		$settings['descr']='Слайдер';
		$settings['help']=array(
				'default=1'=>'Значение по умолчанию', 
				'min=1'=>'Минимальное значение',
				'max=10'=>'Максимальное значение', 
				'comment=комментарий'=>'Комментарий', 
				'range=true'=>'Выбор интервала (вместо default использовать values)',
				'values=[ 25, 50 ]'=>'Значение по умолчанию для интервала',
				
		);
		$maxlength = (floor($this->getSetting('maxlength'))>0)?floor($this->getSetting('maxlength')):255;
		$this->setSetting('maxlength',$maxlength);
		VirtualType::init($settings);
	}
	function drawEditor($divstyle = '',$span = true){
		$settings = $this->getSetting('settings');

		if ($this->getSetting('value')=='' && $settings['default']!='')  $this->setSetting('value', $settings['default']);
		if ($this->getSetting('value')!='' && $settings['values']!='') $settings['values']='['.str_replace(' - ', ',', $this->getSetting('value')).']';

		?>
  <script>
  $(function() {
    var <?=htmlspecialchars($this->getSetting('name'))?>_slider = $( "#slider-<?=htmlspecialchars($this->getSetting('name'))?>" ).slider({
      range: <?=$settings['range']!='' ? $settings['range'] : '"min"'?>,
      <?=$settings['min']!='' ? 'min: '.$settings['min'].',' : ''?>
      <?=$settings['max']!='' ? 'max: '.$settings['max'].',' : ''?>
      <?=$settings['range']!='true' ? 'value: '.floor($this->getSetting('value')).',' : ''?>
      <?=$settings['values']!='' ? 'values: '.$settings['values'].',' : ''?>
      slide: function( event, ui ) {
        <?if ($settings['range']!='true'){?>$( "input[name='<?=htmlspecialchars($this->getSetting('name'))?>']" ).val( ui.value );<?}?>
        <?if ($settings['range']=='true'){?>$( "input[name='<?=htmlspecialchars($this->getSetting('name'))?>']" ).val( ui.values[0] + " - " + ui.values[1] );<?}?>
        $( "input[name='<?=htmlspecialchars($this->getSetting('name'))?>']" ).focus();

      }
    });
    $("input[name='<?=htmlspecialchars($this->getSetting('name'))?>']").keyup(function(eventObject){<?=htmlspecialchars($this->getSetting('name'))?>_slider.slider( "value", $( "input[name='<?=htmlspecialchars($this->getSetting('name'))?>']" ).val() );});
    <?if ($settings['range']!='true'){?>
    	<?=htmlspecialchars($this->getSetting('name'))?>_slider.slider( "value", $( "input[name='<?=htmlspecialchars($this->getSetting('name'))?>']" ).val() );
    <?}else{?>
        $( "input[name='<?=htmlspecialchars($this->getSetting('name'))?>']" ).val( $( "#slider-<?=htmlspecialchars($this->getSetting('name'))?>" ).slider( "values", 0 ) +" - " + $( "#slider-<?=htmlspecialchars($this->getSetting('name'))?>" ).slider( "values", 1 ) );
    <?}?>
  });
  </script>
		<?


		?>
		<div class="place" <?=($divstyle!='')?$divstyle:''?>>
			<label><?=htmlspecialchars($this->getSetting('description'))?><?=((isset($settings['important']))?' <span class="important">*</span>':'')?></label>
			<?if ($settings['comment']!=''){?><small><?=$settings['comment']?></small><?}?>
			<table style="width: 100%;">
				<tr>
					<td style="width: 200px;">
					 <span class="input">
						<input type="text" value="<?=$this->getSetting('value')?>" name="<?=htmlspecialchars($this->getSetting('name'))?>" autocomplete="off" <?=$settings['range']=='true' ?  'readonly=""' : ''?>>
					</span>
					</td>
					<td><div id="slider-<?=htmlspecialchars($this->getSetting('name'))?>"></div></td>
				</tr>
			</table>
		</div>
		<?
		if ($span) print '
			<span class="clear"></span>';
	}
	function preSave(){
		$errors = array();
		$settings = $this->getSetting('settings');
		$newvalue = htmlspecialchars(trim($_POST[$this->getSetting('name')]));

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