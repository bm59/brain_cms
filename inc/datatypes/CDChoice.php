<?
class CDCHOICE extends VirtualType
{
	function init($settings){
		$maxlength = (floor($this->getSetting('maxlength'))>0)?floor($this->getSetting('maxlength')):255;
		$this->setSetting('maxlength',$maxlength);
		VirtualType::init($settings);
	}
	function drawEditor($divstyle = '',$span = true){

		$settings = $this->getSetting('settings');
		$selection = '';
		if ($this->getSetting('value')=='' && $settings['default']!='')  $this->setSetting('value', $settings['default']);

		?>
		<div class="place" <?=($divstyle!='')?$divstyle:''?>>
			<label><?=htmlspecialchars($this->getSetting('description'))?><?=((isset($settings['important']))?' <span class="important">*</span>':'')?></label>
			<?if ($settings['comment']!=''){?><small><?=$settings['comment']?></small><?}?>
  <script>
  $(function() {
    $( "#<?=$this->getSetting('name')?>" ).buttonset();
  });
  </script>
	<div class="clear"></div>
  	<div id="<?=$this->getSetting('name')?>" class="radio_ui">
    <?

    /*Внешний массив*/
    if (is_array($this->getSetting('values'))) $settings['values']=$this->getSetting('values');
    /*Массив из настроек типа*/
    else if ($settings['values']!='')
    {    	$arr=array();
    	$val=explode(',', $settings['values']);
    	foreach ($val as $v)
    	{    		$arr[]=trim($v);
    	}
    	$settings['values']=$arr;
    }

    $i=0;
    foreach ($settings['values'] as $k=>$v)
    {    	$i++;
    	if ($k=='') $k=$v;

    	if (stripos($v,'#')!==false)
    	{    		$val=explode('#',$v);
    		$k=trim($val[0]);
    		$v=trim($val[1]);
    	}

    	$selected='';


    	if ($k==$this->getSetting('value') || stripos($this->getSetting('value'), ','.$k.',')!==false)  $selected='checked="checked"';

    	if ($settings['type']=='radio')
    	{?>
    	<input type="radio" id="radio<?=$i?>_<?=$this->getSetting('name')?>" value="<?=$k?>" name="<?=$this->getSetting('name')?>" <?=$selected?>><label for="radio<?=$i?>_<?=$this->getSetting('name')?>"><?=$v?></label>
    	<?
    	}
    	else
    	{       	?>
       	<input type="checkbox" id="check<?=$i?>_<?=$this->getSetting('name')?>" value="<?=$k?>" name="<?=$this->getSetting('name')?>[]" <?=$selected?>><label for="check<?=$i?>_<?=$this->getSetting('name')?>"><?=$v?></label>
       	<?
    	}


    }
    ?>
	</div>
	</div>
		<?
		if ($span) print '
			<span class="clear"></span>';
	}
	function preSave(){        print_r($_POST);
		$errors = array();
		$settings = $this->getSetting('settings');

		if ($settings['type']=='multi')
		{			$val='';
			foreach ($_POST[$this->getSetting('name')] as $k=>$v)
			{				$val.=(($val=='') ? ',':'').$v.',';
			}

			$newvalue=$val;

		}
        else
		$newvalue = htmlspecialchars(trim($_POST[$this->getSetting('name')]));

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