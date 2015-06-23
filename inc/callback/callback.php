<?

if ($_GET['action']=='callback')
{    include_once($_SERVER['DOCUMENT_ROOT']."/inc/include_site.php");

    $SiteSettings = new SiteSettings;
	$SiteSettings->init();

    date_default_timezone_set("UTC"); // ������������� ������� ���� �� ��������
  	$time = time(); // ��� ��� �������� ���������� � ����
  	$offset = 3; // ��������, � ������������ �������� ������������ �������� ���������� +3 ����
  	$time += 5 * 3600; // ��������� 3 ���� � ������� �� ��������

    $callback_email=$SiteSettings->getOne($SiteSettings->getIdByName('callback_email'));

    $msg='<strong>�������� ����� ������ � ����� '.$_SERVER['HTTP_HOST'].'</strong><br/>
    �������: '.$_GET['client_tel'].'<br/>
    �����������: '.$_GET['client_comment'].'<br/>
    ����� ����������� ������:'.date("d.m.Y H:i", $time);

	$em_ar=explode('|', $callback_email['value']);
 	foreach ($em_ar as $em)
 	{


 		if ($em!='')
		defaultEmail($em,$msg,'����� ������: '.$_GET['client_tel'].' � ����� '.$_SERVER['HTTP_HOST'],'noreply@'.$_SERVER['HTTP_HOST']);
 	}


    print
	'{
	   "ok": "ok"
	}';

	die();
}
?>

<link rel="stylesheet" type="text/css" href="/inc/callback/callback.css"  media="all" />
<script type="text/javascript" src="/inc/callback/jquery.leanModal.min.js"></script>


<script type="text/javascript">
		$(document).ready(function(){
	     	 $('a[rel*=leanModal]').leanModal({ top : 200, closeButton: ".modal_close" });

	     	 $("#signup").keypress(function(e){
	     	   if(e.keyCode==13){
	     	   send_callbak(); return false;
	     	   }
	     	 });

	     });

function check_tel(obj)
{
	var val = jQuery(obj).val();
	var cnt=check_int_count(val);

	if (cnt==7)
	{
		$('.client_tel').css('background','#DEF5E1 url(/inc/callback/good.png) 206px center no-repeat');
		$('.result').text('');
	}
	else if (cnt>=10)
	{
		$('.client_tel').css('background','#DEF5E1 url(/inc/callback/good.png) 206px center no-repeat');
		$('.result').text('');
	}
	else $('.client_tel').css('background','#FDE0E0');

}

function check_int_count(val)
{
	var int_count=0;
	for ( var i = 0;i < val.length; i++ )
	{
		if (val[i]!=' ' && (parseInt(val[i]) || val[i]==0)) int_count++;
	}
	return int_count;
}

function send_callbak()
{
	var val = $('.client_tel').val();
	var cnt=check_int_count(val);
	var is_right=0;

	if (cnt==7) is_right=1;
	else if (cnt>=10) is_right=1;

	if (is_right==0)
	{
		$('.result').css('color','#FF0000');
		$('.result').text('������: ������ ������ ���� ������� 7 ����, � ��������� ����� 10 ����');
	}
	else
	{
		$('.result').text('');

		jQuery.get("/inc/callback/callback.php", { action: 'callback', client_tel: $('.client_tel').val(), client_comment : $('.client_comment').val() },
		function(data)
		{
			 	data=eval('('+data+')');

          		if (data.ok=='ok')
          		{
        			$('.result').css('color','#019014');
			   		$('.client_tel').val('');
			   		$('.client_comment').val('');


					$('.result').text('��� ������ ������� ���������, ���� ��������� ������������� ����� 5 ������');

					setTimeout(function()
					{
			   			$("#lean_overlay").fadeOut(200); $("#signup").fadeOut(200);$("#signup").css({"display":"none"});
			   			$('.client_tel').css('background','#FDE0E0');
			   			$('.result').text('');


			   		},5000);

          		}
          		else
          		$('.result').text('������: ������ �� ���������!');





		});




	}


}


		</script>




		<div id="signup">
			<div id="signup-ct">
				<div id="signup-header">
					<h2>�������� �������� ������</h2>
					<p>�� ��� ���������� � ��������� �����</p>
					<a class="modal_close" href="#" onclick="return false;"></a>
				</div>

				<form action="" class="-visor-no-click">

				  <div class="txt-fld">
				    <label>��� �������<span class="important">*</span>:</label>
				    <input id="client_tel" class="client_tel" name="client_tel" type="text" onkeyup="check_tel(this); return false;" autocomplete="off"/>
				  </div>

				  <div class="txt-fld">
				    <label for=""><div>�����������</div><div class="comment">(�� �����������)</div></label>
				    <input id="client_comment" class="client_comment" name="client_comment" type="text" />
				  </div>

                  <div class="result"></div>

                  <div class="clear"></div>
					  <div class="button" style="float: right; padding: 0 19px 15px 0;">
							<div>
							<a class="callback_button" onclick="send_callbak(); return false;" href="#"> ��������� </a>
							</div>
					  </div>

				 </form>

			</div>
		</div>