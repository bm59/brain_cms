<?
configSet("AskUrl",preg_replace("/(\?|&|\&).*$/",'',strtolower($_SERVER['REQUEST_URI'])));
configSet("siteURL",'site.ru');
configSet("pr_name",'site');
configSet("pr_doptit",'');
configSet("pr_name_rus",'');

$log_enable=true;
$log_month_keep=6;
?>