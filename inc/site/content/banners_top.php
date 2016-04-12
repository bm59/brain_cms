<?

$place=$place_iface->getPub(1);/* Баннер верхний */


$list=$banner_iface->getBanners($place['id']);

foreach ($list as $b)
{
	$banner_iface->printBanner($b['id']);
}
?>
