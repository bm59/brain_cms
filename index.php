<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/include.php");?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/meta.php");?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/header.php");?>
<?
$item=msr(msq("SELECT * FROM `site_site_ptest_test_11` WHERE `id`=1"));
print $item['text'];
?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/footer.php");?>