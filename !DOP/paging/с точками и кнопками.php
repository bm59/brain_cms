<?
if ($pagescount>1 && $_GET['id']==''){
	?>
<div class="paging">
	<?
	$start_dif=$dif=2;

	if ($_GET['page']<$dif*2) $dif=$dif*2+1-$_GET['page'];
	else $dif=$start_dif+1;

	if ($_GET['page']>$dif+1) print '<a href="'.configGet("AskUrl").'">В начало</a>';

	for ($i=1; $i<=$pagescount; $i++)
	{
		$inner = '';
		$block = array('<a href="?page='.$i.$urlstr.'">','</a>');
		if ($i==($_GET['page']-($dif))){
			$inner = ($i>1)?'<strong>&hellip;</strong>':$i;
		}
		if (($i>($_GET['page']-$dif)) && ($i<($_GET['page']+$dif))){
			$inner = $i;
			if ($i==$_GET['page']) $block = array('<span>','</span>');
		}
		if ($i==($_GET['page']+$dif)){
			$inner = ($i<$pagescount)?'<strong>&hellip;</strong>':$i;
		}
		if ($inner!='') print '
		'.$block[0].$inner.$block[1];
	}

	if ($_GET['page']>1) print '<a href="?page='.($_GET['page']+1).$urlstr.'">Следующая</a>';
	if ($_GET['page']+$dif<$pagescount) print '<a href="?page='.$pagescount.$urlstr.'">Последняя</a>';
	?>
</div>
		        					<?

		        				}
?>