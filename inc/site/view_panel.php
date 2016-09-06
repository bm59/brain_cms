 <script type="text/javascript">
 $(function() {
		function scroll_to(name) {
			var elem=$(name);
	      	destination = elem.offset().top;
	        $("html, body").animate({scrollTop:destination},"slow")
		}

		<?if (isset($_GET['view_type']) || isset($_GET['sort_type'])) {?>
		scroll_to('.catalog');
		<?} ?>
});
 </script>		
			
			<div class="view_type">
				<div>Вид:</div>
				<?
				foreach ($view_array as $k=>$view)
				{
					$url='';
						
					$url=$view_url.((strpos($view_url, '?')!==false)  ? '&':'?').'view_type='.$k;
					
					if (isset($_GET['sort_type']))
					$url.='&sort_type='.$_GET['sort_type'].($_GET['sort_order']!='' ? '&sort_order='.$_GET['sort_order'] :'');
					
					if (isset($_GET['page']))
					$url.='&page='.$_GET['page'];
							
							
						?><a href="<?=$url ?>" class="<?=$k ?><?=(($_SESSION['view_type']=='' && $view['default']) || $_SESSION['view_type']==$k) ? ' active':'' ?>" title="<?=$view['name'] ?>"/></a><?
				}
				?>
			</div>
			
			<div class="sort_type">
				<div>Сортировать по:</div>
				<?
				$sort_array=array(
					'price'=>array('default'=>true, 'name'=>'цене', 'up_comment'=>'Сначала недорогие', 'down_comment'=>'Сначала дорогие'),
					/* 'popular'=>array('default'=>true, 'name'=>'популярности', 'down_comment'=>'Сначала популярные', 'up_comment'=>'Сначала менее популярные', 'normal_updown'=>false), */
					'name'=>array('name'=>'названию', 'up_comment'=>'По алфавиту', 'down_comment'=>'По алфавиту в обратном порядке')
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
					
					if (isset($_GET['page']))
					$url.='&page='.$_GET['page'];
					
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