<?

$Content = new Content;
$Content->init();
$Storage = new Storage;
$Storage->init();
$Storage->delete_tmp_files();
$VisitorType = new VisitorType;
$VisitorType->init();
$SiteVisitor = new SiteVisitor;
$SiteVisitor->init();

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
?>