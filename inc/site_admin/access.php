<?


$path='/manage/control/contents/';
if ($_GET['section']>0) $path.='?section='.$_GET['section'];

/*���������� �������*/
if ($mode!='development' && !in_array('add', $group['new_settings'][$cid]))
{
?>
	<script>
  	$(function() {
       $( ".button:contains('��������')").addClass("disabled");
       $( ".button:contains('��������')").click(function(){return false;});
       $( "input[value*='��������']").addClass("disabled");
       $( "input[value*='��������']").click(function(){return false;});

       <?if (!$_GET['pub']>0){?>
       	$( ".button:contains('��������� ���������')").addClass("disabled");
       	$( ".button:contains('��������� ���������')").click(function(){return false;});
       	$( "input[value*='��������� ���������']").addClass("disabled");
       	$( "input[value*='��������� ���������']").click(function(){return false;});
       <?}?>
  	});
  </script>
<?
	if ($_GET['pub']=='new' || $_GET['pub']=='add')
	{
 		header('Location: '.$path);
 		die();
 	}
}
?>
<?
/*��������������*/
if ($mode!='development' && !in_array('edit', $group['new_settings'][$cid]))
{
?>
	<script>
  	$(function() {
       $( "a[title*='�������������'] IMG").attr("src","/pics/editor/prefs-disabled.gif");
       $( "a[title*='�������������']").attr("href","");
       $( "a[title*='�������������']").click(function(){return false;});

       $( "a:contains('�������������')").click(function(){return false;});
       $( "a:contains('�������������')").attr("href","");

       $( ".button:contains('���������')").addClass("disabled");
       $( ".button:contains('���������')").click(function(){return false;});
       $( "input[value*='���������']").addClass("disabled");
       $( "input[value*='���������']").click(function(){return false;});


  	});
  </script>



<?
 if (floor($_GET['pub'])>0 || floor($_POST['editformpost'])==1)
 {
 	header('Location: '.$path);
 	die();
 }
}

/*��������*/
if ($mode!='development' && !in_array('delete', $group['new_settings'][$cid]))
{
?>
	<script>
  	$(function() {
       $( "a[title*='�������'] IMG").attr("src","/pics/editor/delete-disabled.gif");
       $( "a[title*='�������']").attr("href","");
       $( "a[title*='�������']").click(function(){return false;});

       $( "a:contains('�������')").click(function(){return false;});
       $( "a:contains('�������')").attr("href","");

       $( "input[title*='�������']").css("backgroundImage", "url('/pics/editor/delete-disabled.gif')");
       $( "input[title*='�������']").click(function(){return false;});
  	});
  </script>



<?
 if (floor($_GET['delete'])>0)
 { 	header('Location: '.$path);
 	die();
 }

}
?>