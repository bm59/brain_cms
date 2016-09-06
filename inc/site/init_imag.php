<?
$section_basket=$SiteSections->getByPattern('PBasket');
if ($section_basket['id']>0)
{
	$basket=getIface($SiteSections->getPath($section_basket['id']));
	$order_tmp=$basket->GetTmpOrder();

	$total_basket_comment=$basket->GetTotalBasketComment($order_tmp);
	
	$basket_items=$basket->GetAllBasketItem($order_tmp);
	
}

$goods_iface=getIface('/sitecontent/goods/');

$cat_iface=getIface('/sitecontent/goods/categs/');




?>