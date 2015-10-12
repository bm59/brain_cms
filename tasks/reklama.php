<?
/* DIR PHP SAPPI */
require_once dirname(__FILE__) . '/../inc/site/include.php';
?>
<?
	
	$reklama_section=msr(msq("SELECT * FROM `site_site_sections` WHERE `pattern`='PReklama'"));

	$RkSection = $SiteSections->get($reklama_section['id']);
	$RkSection['id'] = floor($RkSection['id']);
	
	if ($RkSection['id']>0)
	{
		
		$RkPattern = new $RkSection['pattern'];
		$RkIface = $RkPattern->init(array('section'=>$RkSection['id']));
	}
	
	$list=$RkIface->SetReklamaAll();
	
	print 'ok'

?>