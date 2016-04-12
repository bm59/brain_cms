<?
include_once($_SERVER['DOCUMENT_ROOT']."/inc/configuration.php");

include_once($_SERVER['DOCUMENT_ROOT']."/config/site.php");
include_once($_SERVER['DOCUMENT_ROOT']."/config/mysql.php");

include_once($_SERVER['DOCUMENT_ROOT']."/inc/functions.php");
include_once($_SERVER['DOCUMENT_ROOT']."/inc/templates.php");

include_once($_SERVER['DOCUMENT_ROOT']."/inc/VirtualClass.php");
include_once($_SERVER['DOCUMENT_ROOT']."/inc/MySqlConnect.php");
include_once($_SERVER['DOCUMENT_ROOT']."/inc/Content.php");
include_once($_SERVER['DOCUMENT_ROOT']."/inc/Storage.php");
include_once($_SERVER['DOCUMENT_ROOT']."/inc/VisitorType.php");
include_once($_SERVER['DOCUMENT_ROOT']."/inc/SiteVisitor.php");

include_once($_SERVER['DOCUMENT_ROOT']."/inc/DataType.php");
include_once($_SERVER['DOCUMENT_ROOT']."/inc/DataSet.php");
include_once($_SERVER['DOCUMENT_ROOT']."/inc/patterns/VirtualPattern.php");
include_once($_SERVER['DOCUMENT_ROOT']."/inc/cclasses/VirtualContent.php");

include_once($_SERVER['DOCUMENT_ROOT']."/inc/SiteSettings.php");
include_once($_SERVER['DOCUMENT_ROOT']."/inc/SiteSections.php");
include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/init.php");

$section = $SiteSections->get($_REQUEST['section_id']);


include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/pattern/var.php";
include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/pattern/functions.php";

mysql_query("SET NAMES uft8"); // для mysql
header("Content-type: text/html; charset=uft-8");

if ($_REQUEST['action']=='add_empty')
{
	print_dt(array('id'=>'new'.$_REQUEST['new_count']));
}
?>