<?
class Settings extends VirtualClass
 {
	function init()
	{
 		 $this->Settings['users'] = mstable(ConfigGet('pr_name'),'settings','',array(
               "name"=>"VARCHAR(250)",
               "value"=>"VARCHAR(250)",
               "descr"=>"VARCHAR(250)"
       ));

	}

 }
?>