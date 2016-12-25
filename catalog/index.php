<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/include.php");?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/init_imag.php");?>
<?
if ($_GET['cat_id']!='')
{
	$categ_iface=getIface('/sitecontent/goods/categs/');
	
	$cat=$cat_info=$categ_iface->getPubByField('pseudolink', $_GET['cat_id']);

	if (!$cat['id']>0)
	{
		header( "HTTP/1.1 404 Not Found" );
		header("Location: /404.php");
	}
	
	$cat_header=$cat['name'];

	if ($cat['parent_id']>0)
	{
		$parent=$categ_iface->getPub($cat['parent_id']);
		$cat_header=$cat['name'].' - '.$parent['name'];
		
	}


		if ($parent['id']>0)
		{
			$cat['ptitle'].=' - '.$parent['name'];
		}

		configSet('contenttitle', stripslashes($cat['ptitle']).' | Сувениры из селенита');
		configSet('contentdescription', stripslashes($cat['pdescription']));

		$nav_text='<div>Каталог товаров</div>'.($parent['id']>0 ? '<img src="/pics/arrows/arrow_nav.png"><a href="/catalog/'.$parent['pseudolink'].'/">'.$parent['name'].'</a>' :'').'<img src="/pics/arrows/arrow_nav.png"><div>'.$cat['name'].'</div>';
}

if ($_GET['item_id']>0)
{
	
	$pub_iface=getIface('/sitecontent/goods/');
	$pub=$pub_iface->getPub($_GET['item_id']);
	
	if (!$pub['id']>0)
	{
		header( "HTTP/1.1 404 Not Found" );
		header("Location: /404.php");
	}
	
	$cat_info=$pub_iface->GetCategPath($pub);
	
	$nav_text='<div>Каталог товаров</div><img src="/pics/arrows/arrow_nav.png">'.$cat_info['nav'];
	
	configSet('contenttitle',stripslashes($pub['name']).$cat_info['title']);
	configSet('contentdescription', stripslashes($pub['name']).' - '.stripslashes($categs[$pub['main_categ_id']]['name']));
}
?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/meta.php");?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/header.php");?>
<div class="content_container">
<div class="mininav"><a href="/">Главная</a><img src="/pics/arrows/arrow_nav.png"><?=$nav_text?></div>
	<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/catalog.php");?>

	<div class="clear"></div>
</div>

<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/footer.php");?>