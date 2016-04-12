<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/include.php");?>
<?include_once("ImportClass.php");?>
<? 
$ImportClass=new ImportClass();

$ImportClass->FileAnalize($_FILES['upl_file']['tmp_name']);
?>
