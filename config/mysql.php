<?

	if ($_SERVER['REMOTE_ADDR']=='127.0.0.1')
	{
		configSet("DBUser","root");
		configSet("DBPassword","1asdf");
		configSet("DBHost","localhost");
		configSet("DBBase","brain_cms");
	}
	else
	{
	configSet("DBUser","bm59_braincms");
	configSet("DBPassword","braincms");
	configSet("DBHost","localhost");
	configSet("DBBase","bm59_braincms");
	}
?>