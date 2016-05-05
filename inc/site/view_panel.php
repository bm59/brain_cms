<?
$view_array=array
(
	'tile_big'=>array('name'=>'Крупная плитка', 'pic'=>'/pics/view/tile_big.png', 'default'=>true),
	'tile'=>array('name'=>'Плитка', 'pic'=>'/pics/view/tile.png', 'default'=>false),
	'list'=>array('name'=>'Список', 'pic'=>'/pics/view/list.png', 'default'=>false)
);

$view_url==$_SERVER['REQUEST_URI'];
foreach ($view_types as $k=>$v)
{
	$view_url=str_replace("&view_type=".$k, "", $view_url);
	$view_url=str_replace("?view_type=".$k, "", $view_url);
}

if (isset($_GET['view_type'])) 		$_SESSION['view_type']=$_GET['view_type'];
if ($_SESSION['view_type']=='') 	$_SESSION['view_type']='tile_big';
?>

<div class="view_panel">
	<div class="view_type">
		<div>Вид:</div>
		<?
		foreach ($view_array as $k=>$view)
		{
			$url='';
			
			$url=$view_url.((strpos($view_url, '?')!==false)  ? '&':'?').'view_type='.$k;
			if (isset($_GET['sort_type']))
			$url.='&sort_type='.$_GET['sort_type'].($_GET['sort_order']!='' ? '&sort_order='.$_GET['sort_order'] :'');
			
			
			?><a href="<?=$url ?>" <?=(($_SESSION['view_type']=='' && $view['default']) || $_SESSION['view_type']==$k) ? 'class="active"':'' ?>><img src="<?=$view['pic'] ?>" alt="<?=$view['name'] ?>" title="<?=$view['name'] ?>"/></a><?
		}
		?>
	</div>
	
	<script src="/js/custom_select.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="/css/custom_select.css" media="all" />
	<div class="sort_type">
		<div>Сортировать по:</div>
		<?
		$sort_array=array(
			'price'=>array('name'=>'цене', 'up_comment'=>'Сначала недорогие', 'down_comment'=>'Сначала дорогие'),
			'prior'=>array('default'=>true, 'name'=>'популярности', 'down_comment'=>'Сначала популярные', 'up_comment'=>'Сначала менее популярные', 'normal_updown'=>false),
			'name'=>array('name'=>'названию', 'up_comment'=>'По альфавиту', 'down_comment'=>'По алфавиту в обратном порядке')
		);
		
		
		if (isset($_GET['sort_type']))
		$cur_sort=$sort_array[$_GET['sort_type']]['name'];	
		else
		{
			foreach ($sort_array as $sort)
			if (array_key_exists('default', $sort))
			$cur_sort=$sort['name'];
		}
		
		
		?>
		<div class="select">
			<a href="javascript:void(0);" class="slct"><span class="sort<?=$_GET['sort_order']=='2' ? ' down':''?>"></span><?=$cur_sort ?></a>
			<ul class="drop">
			<?
			$url=$view_url.((strpos($view_url, '?')!==false)  ? '&':'?').'view_type='.$_SESSION['view_type'];
			
			foreach ($sort_array as $k=>$sort)
			{
				
				?><li><a <?=($_GET['sort_type']==$k && $_GET['sort_order']!='2') ? 'class="active"' :''?> href="<?=$url?>&sort_type=<?=$k?>" title="<?=($sort['normal_updown']!=false) ? $sort['down_comment'] : $sort['up_comment'] ?>"><span class="sort"></span><?=$sort['name'] ?></a></li><?
				?><li><a <?=($_GET['sort_type']==$k && $_GET['sort_order']=='2') ? 'class="active"' :''?> href="<?=$url?>&sort_type=<?=$k?>&sort_order=2" title="<?=(!$sort['normal_updown']!=false) ? $sort['down_comment'] : $sort['up_comment'] ?>"><span class="sort down"></span><?=$sort['name'] ?></a></li><?
			}
			?>
			</ul>
			<input type="hidden" id="select" />
		</div>
	</div>
</div>
<br/><br/>



