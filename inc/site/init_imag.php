<?
$section_basket=$SiteSections->getByPattern('PBasket');
$basket=getIface($SiteSections->getPath($section_basket['id']));
$order_tmp=$basket->GetTmpOrder();

$total_basket_comment=$basket->GetTotalBasketComment($order_tmp);
?>