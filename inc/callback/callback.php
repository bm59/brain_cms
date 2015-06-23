<?

if ($_GET['action']=='callback')
{    include_once($_SERVER['DOCUMENT_ROOT']."/inc/include_site.php");

    $SiteSettings = new SiteSettings;
	$SiteSettings->init();

    date_default_timezone_set("UTC"); // Устанавливаем часовой пояс по Гринвичу
  	$time = time(); // Вот это значение отправляем в базу
  	$offset = 3; // Допустим, у пользователя смещение относительно Гринвича составляет +3 часа
  	$time += 5 * 3600; // Добавляем 3 часа к времени по Гринвичу

    $callback_email=$SiteSettings->getOne($SiteSettings->getIdByName('callback_email'));

    $msg='<strong>Поступил заказ звонка с сайта '.$_SERVER['HTTP_HOST'].'</strong><br/>
    Телефон: '.$_GET['client_tel'].'<br/>
    Комментарий: '.$_GET['client_comment'].'<br/>
    Время поступления заявки:'.date("d.m.Y H:i", $time);

	$em_ar=explode('|', $callback_email['value']);
 	foreach ($em_ar as $em)
 	{


 		if ($em!='')
		defaultEmail($em,$msg,'заказ звонка: '.$_GET['client_tel'].' с сайта '.$_SERVER['HTTP_HOST'],'noreply@'.$_SERVER['HTTP_HOST']);
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
		$('.result').text('Ошибка: номере должно быть минимум 7 цифр, в мобильном более 10 цифр');
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


					$('.result').text('Ваш запрос успешно отправлен, окно закроется автоматически через 5 секунд');

					setTimeout(function()
					{
			   			$("#lean_overlay").fadeOut(200); $("#signup").fadeOut(200);$("#signup").css({"display":"none"});
			   			$('.client_tel').css('background','#FDE0E0');
			   			$('.result').text('');


			   		},5000);

          		}
          		else
          		$('.result').text('Ошибка: запрос не отправлен!');





		});




	}


}


		</script>




		<div id="signup">
			<div id="signup-ct">
				<div id="signup-header">
					<h2>Заказать обратный звонок</h2>
					<p>Мы вам перезвоним в ближайшее время</p>
					<a class="modal_close" href="#" onclick="return false;"></a>
				</div>

				<form action="" class="-visor-no-click">

				  <div class="txt-fld">
				    <label>Ваш телефон<span class="important">*</span>:</label>
				    <input id="client_tel" class="client_tel" name="client_tel" type="text" onkeyup="check_tel(this); return false;" autocomplete="off"/>
				  </div>

				  <div class="txt-fld">
				    <label for=""><div>Комментарий</div><div class="comment">(не обязательно)</div></label>
				    <input id="client_comment" class="client_comment" name="client_comment" type="text" />
				  </div>

                  <div class="result"></div>

                  <div class="clear"></div>
					  <div class="button" style="float: right; padding: 0 19px 15px 0;">
							<div>
							<a class="callback_button" onclick="send_callbak(); return false;" href="#"> Отправить </a>
							</div>
					  </div>

				 </form>

			</div>
		</div>