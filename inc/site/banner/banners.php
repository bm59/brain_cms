
<div class="banners">
<?

$place=$place_iface->getPub(1);


$list=$banner_iface->getBanners($place['id'], '', ' ORDER BY precedence DESC, RAND();');

foreach ($list as $b)
{
	$banner_iface->printBanner($b['id']);		
}
?>
</div>