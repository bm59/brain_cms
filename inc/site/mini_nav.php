<?
if ($nav_text=='' && $Section['name']!='')
$nav_text='<div>'.$Section['name'].'</div>';

if ($nav_text!=''){
?>
<div class="mininav"><a href="/">Главная</a><div>&nbsp;&nbsp;&nbsp;\&nbsp;&nbsp;&nbsp;</div><?=$nav_text?><div class="clear"></div></div>
<?}?>