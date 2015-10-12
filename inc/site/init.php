<?

$Content = new Content;
$Content->init();
$Storage = new Storage;
$Storage->init();

$SiteSettings = new SiteSettings;
$SiteSettings->init();

$CDDataType = new DataType;
$CDDataType->init();

$CDDataSet = new DataSet;
$CDDataSet->init();

$VirtualContent = new VirtualContent;
$VirtualContent->init();

$VirtualPattern = new VirtualPattern;
$VirtualPattern->init();

$SiteSections = new SiteSections;
$SiteSections->init();

$banner_iface=getiface('/products/banners/');
$banner_Section = $SiteSections->get($SiteSections->getIdByPath('/products/banners/'));

$place_iface=getiface('/products/'.$banner_Section['path'].'/places/');
$place_Section = $SiteSections->get($SiteSections->getIdByPath('/products/'.$banner_Section['path'].'/places/'));

$rkSection = $SiteSections->get($SiteSections->getIdByPath('/products/reklama/'));
$rkSection['id'] = floor($rkSection['id']);
if ($rkSection['id']>0)
{
	$rkPattern = new $rkSection['pattern'];
	$rkIface = $rkPattern->init(array('section'=>$rkSection['id']));
}

$news_tag=getSprValuesEx('/sitecontent/spr/news_tag/');
?>