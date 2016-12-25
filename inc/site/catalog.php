<?
$good_iface=getIface('/sitecontent/goods/');
$categ_iface=getIface('/sitecontent/goods/categs/');

$basket_items=$basket->GetAllBasketItem($order_tmp);
?>

<script>
$(function() {

	var id=0;
	var session_id = '<?php echo session_id(); ?>';

	$(function() {
		$(".mybtn").click(function() {
			var cur_kol=parseInt($(this).parents('li').find('[name=good_kol]').val());

			var new_kol=cur_kol+1;
			
			basket_add(session_id, '<?=$order_tmp ?>', $(this).parents('li').attr('id'), new_kol);
			basket_comment(session_id, '<?=$order_tmp ?>');

			$(this).html('<img src="/pics/basket_mini.png" alt=""/>&nbsp;:&nbsp;'+new_kol);
			$(this).parents('li').find('[name=good_kol]').val(new_kol);
			$(this).addClass('active');
			$(this).parents('li').find('.basket_clear').show();
		});

		$(".basket_clear").click(function() {
			
			basket_add(session_id, '<?=$order_tmp ?>', $(this).parents('li').attr('id'), 0);
			basket_comment(session_id, '<?=$order_tmp ?>');

			$(this).parents('li').find('.mybtn').html('купить');
			$(this).parents('li').find('.mybtn').removeClass('active');
			$(this).parents('li').find('[name=good_kol]').val(0);
			$(this).parents('li').find('.basket_clear').hide();
		});
	});

	


});
</script>
<?if (!$_GET['item_id']>0) {?>		
<div class="good_menu">
		<h2>Каталог сувениров:</h2>
		
		<div>
		<?
		$basket_items=$basket->GetAllBasketItem($order_tmp);

		$categs=getTableValuesOrder('/sitecontent/goods/categs/', '', '');
		
		
		$categories=msq("SELECT * FROM `".$categ_iface->getSetting('table')."` WHERE `parent_id`=0 and `show`=1 ORDER BY `precedence`");
		while ($cat=msr($categories)){
			$image=$Storage->getFile($cat['image']);
			
			?>
			<div <?=$_GET['cat_id']==$cat['pseudolink'] || $cat['id']==$parent['id'] ? 'class="active"':''; ?>><a href="/catalog/<?=$cat['pseudolink'] ?>/"><?=$cat['name'] ?></a></div>
			
			<?
			if ($_GET['cat_id']==$cat['pseudolink'] || $cat['id']==$parent['id'])
			{
				$sub_items=msq("SELECT * FROM `".$categ_iface->getSetting('table')."` WHERE `parent_id`=".$cat['id']." and `show`=1 ORDER BY `precedence`");
				if (mysql_num_rows($sub_items)>0) 
				{
					?><ul class="sub_categs"><?
					while ($sub=msr($sub_items))
					{
						?>
						<li <?=$_GET['cat_id']==$sub['pseudolink'] ? 'class="active"':''; ?>><a href="/catalog/<?=$cat['pseudolink'] ?>/<?=$sub['pseudolink'] ?>/"><?=$sub['name'] ?></a></li>
						<?
					}
					?></ul><?
				}
			}
		}
		
		?>
		</div>
		<div class="clear"></div>

		
<script>
$(function() {

	var id=0;
	var session_id = '<?php echo session_id(); ?>';

	$(function() {
		$(".price_filter input").click(function() {

			var param='';
			
			$('.price_filter input').each(function() 
			{
				
				if ($(this).prop('checked')==true)
				{
					param+=(param!='' ? '|':'')+$(this).data('from')+','+$(this).data('to');
				}
			});

			$.ajax({
	            type: "POST",
	            url: "/ajax.php",
	            data: "action=get_price_search&param="+param+"&session_id="+session_id<?=$cat_info['id']>0 ? '+"&cat_id="+'.$cat_info['id']:'' ?>,
	            success: function(html){
	            	$("ul.goods").html(html);
	            }
	        });
		});
		
	});

});
</script>
			
			<?
			$usl='';
			if ($cat_info['id']>0) $usl=" and `categs` like ('%,".$cat_info['id'].",%')";
			$min_price=msr(msq("SELECT min(`price`) as price FROM `".$good_iface->getSetting('table')."` WHERE `show`=1".$usl));
			$max_price=msr(msq("SELECT max(`price`) as price FROM `".$good_iface->getSetting('table')."` WHERE `show`=1".$usl));
	
			$start_price=0;
			$inp_count=0;
			
			$item_cnt=ceil($max_price['price']/1000);
			
			if ($min_price['price']>0 && $max_price['price']>0 && $item_cnt>2)
			{
				?>
				<div class="hr"></div>
				<div>
				<div class="price_filter styled">
				<h2>Фильтр по цене:</h2>
				<?
				for ($i = 0; $i <=$item_cnt; $i++)
				{
					if ($i*1000>$min_price['price'])
					{
						$start_price=($i-1)*1000;
						
						?>
						
							<input data-name="price_filter_<?=$i ?>" data-from="<?=$start_price ?>" data-to="<?=($i*1000) ?>" id="price_filter_<?=$i ?>" type="checkbox"><label for="price_filter_<?=$i ?>"><?=$start_price.'<small> р.</small> - '.($i*1000).'<small> р.</small>' ?></label>
							<div class="clear"></div>
							<div class="air p10"></div>
						<?
						
						$inp_count++;
					}
				}
				?>
				</div>
				</div>
				<?
			}
			?>
		
</div>
<?} ?>	

	<?
	if ($_GET['item_id']>0)
	{	
	?>
	<div class="good_content item_page">
	<?
		$good_iface->PrintGood($pub, 'item_page');
	}
	else 
	{
	?>
	<div class="good_content">
	<?
		if ($cat_header!='') print "<h1>$cat_header</h1>";
	?>
	<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/view_panel.php");?>
			
			<?
			$page=$_GET['page'];
			if (floor($page)==0) $page = 1;
			
			$cat=$categ_iface->getPubByField('pseudolink', $_GET['cat_id']);
			
			if ($cat['id']>0) $usl_sql=" and `categs` like '%".$cat['id']."%'";
			
			
			$limit=$view_array[$_SESSION['view_type']]['limit'];
			if (!$limit>0) $limit=20;

			$limit_sql=" LIMIT ".(($page-1)*$limit).",".$limit;
			
			$order_by='ORDER BY `price` ASC';
			if (isset($sort_array[$_GET['sort_type']]))
			{
				$order_by='ORDER BY  `'.trim($_GET['sort_type']).'`';
			
				if ($_GET['sort_order']!=2 && $sort_array[$_GET['sort_order']]['normal_updown']==false) $order_by.=' ASC';
				else  $order_by.=' DESC';
			}
			?>

			<ul class="goods <?=$_SESSION['view_type'] ?>">
			<?

			
			$cnt=msr(msq("SELECT count(*) as cnt FROM `".$good_iface->getSetting('table')."` WHERE `show`=1".$usl_sql));
			
			if (!$cnt['cnt']>0) print '<h2 class="left">По заданным условиям товары не найдены</h2>';
			
			$goods=msq("SELECT * FROM `".$good_iface->getSetting('table')."` WHERE `show`=1 ".$usl_sql.$order_by.$limit_sql);
			while ($good=msr($goods)){
				
				$good['kol']=$basket_items[$good['id']]['kol'];	
				$good_iface->PrintGood($good, $_SESSION['view_type']);
			}
			
			$pages_count=ceil($cnt['cnt']/$limit);
			if ($page>$pages_count) $page=1;
			
			
			$_GET['page']=$page;
			if ($pages_count>1)
			{
			
				$result.='<div class="paging">';
				$dif=5;
					
				$url=($_GET['sort_type']!='' ? '&sort_type='.$_GET['sort_type'] :'').($_GET['view_type']!='' ? '&view_type='.$_GET['view_type'] :'');
					
				for ($i=1; $i<=$pages_count; $i++)
				{
					$inner = '';
					$block = array('<a href="?page='.$i.$url.'">','</a>');
						
						
					if (
							($i>($_GET['page']-($dif/2))) && ($i<($_GET['page']+($dif/2)))
								
							|| ($i<=$dif && $_GET['page']<=$dif-$dif/2)
							|| ($i>$pages_count-$dif && $_GET['page']>$pages_count-$dif/2+1)
								
							)
					{
						$inner = $i;
						if ($i==$_GET['page']) $block = array('<span>','</span>');
					}
						
					if ($inner!='') $result.=$block[0].$inner.$block[1];
				}
			
				/* if ($_GET['page']!=$pages_count && $pages_count>3) $result.='<a href="?page='.$i.$url.'">Следующая</a>'; */
					
				if ($_GET['page']!=$pages_count && $pages_count>1) $result.='<a href="?page='.($_GET['page']+1).$url.'">Следующая</a>';
				if ($_GET['page']<$pages_count && $pages_count>$dif) $result.='<a href="?page='.$pages_count.$url.'">Последняя</a>';
				$result.='</div>';
			}
			print '<div class="clear"></div>'.$result;
			?>
			</ul>
	<?} ?>
	</div>