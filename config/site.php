<?
configSet("AskUrl",preg_replace("/(\?|&|\&).*$/",'',strtolower($_SERVER['REQUEST_URI'])));
configSet("pr_name",'site');


/*print '='.setting('pr_doptit').'|'.ConfigGet('pr_doptit');*/
?>