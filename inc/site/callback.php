<?

if ($_POST['action']=='callback' && $_POST['phone']!='')
{	
	include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/include.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/inc/ajax_securuty.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/inc/idna_convert.class.php");
	
	$section_callback=$SiteSections->getByPattern('PCallBack');
	
	if ($section_callback['id']>0)
	{
		$callback_iface=getIface($SiteSections->getPath($section_callback['id']));
		$callback_iface->AddCallback(array('phone'=>$_POST['phone'], 'comment'=>$_POST['comment']));

		if (setting('callback_email')!='') 
		{
			include_once($_SERVER['DOCUMENT_ROOT']."/inc/SendMailSmtpClass.php");
			$mailSMTP = new SendMailSmtpClass('brainsite.sender@yandex.ru', 'BrainSite', 'ssl://smtp.yandex.ru', 'brainsite.sender@yandex.ru', 465);
			
			$idn = new idna_convert(array('idn_version'=>2008));
			
			$msg='<strong>Поступил заказ звонка с сайта '.$idn->decode($_SERVER['HTTP_HOST']).'</strong><br/>
    		Телефон: '.$_POST['phone'].'<br/>
    		Комментарий: '.$_POST['comment'].'<br/>
    		Время поступления: '.date('d.m.Y H:i:s', strtotime("+2 hours", time()));
			
			$emails=setting('callback_email');
			$em_ar=explode('|', $emails);
			foreach ($em_ar as $em)
			{
				if ($em!='') $mailSMTP->SendMail($em, 'заказ звонка с сайта '.$idn->decode($_SERVER['HTTP_HOST']), $msg);
			}
		}

	} 
	print '{"ok": "ok"}';
}
else{
?>
<script type="text/javascript">

function check_tel(obj)
{
	var val = $(obj).val();

	var cnt=check_int_count(val);

	if (cnt==7)
	{
		$('#popup_callback .phone').css('background','#DEF5E1 url(/pics/good.png) 370px center no-repeat');
		$('#popup_callback .result').text('');
	}
	else if (cnt>=10)
	{
		$('#popup_callback .phone').css('background','#DEF5E1 url(/pics/good.png) 370px center no-repeat');
		$('#popup_callback .result').text('');
	}
	else $('#popup_callback .phone').css('background','#FDE0E0');

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

function send_callback()
{
	var val = $('#popup_callback #phone').val();
	
	var cnt=check_int_count(val);
	var is_right=0;
	var session_id = '<?php echo session_id(); ?>';

	if (cnt==7) is_right=1;
	else if (cnt>=10) is_right=1;

	if (is_right==0)
	{
		$('#popup_callback .result').css('color','#FF0000');
		$('#popup_callback .result').text('Ошибка: номере должно быть минимум 7 цифр, в мобильном более 10 цифр');
	}
	else
	{
		$('.result').text('');

			$.ajax({
		            type: "POST",
		            url: "/inc/site/callback.php",
		            data: "action=callback&phone="+$('#phone').val()+"&comment="+$('#comment').val()+"&session_id="+session_id,
		            dataType: 'json',
		            success: function(data){
		          		if (data.ok=='ok')
		          		{
		        			$('#popup_callback .result').css('color','#019014');
					   		$('#popup_callback .phone').val('');
					   		$('#popup_callback .comment').val('');
					   		$('#popup_callback .phone').css('background','#FFFFFF')


							$('.result').text('Ваш запрос успешно отправлен, окно закроется автоматически через 5 секунд');

							setTimeout(function()
							{
					   			$("#lean_overlay").fadeOut(200);
					   			$(".popup").fadeOut(200);
					   			$(".popup").css({"display":"none"});
					   			$('#popup_callback .phone').css('background','#FDE0E0');
					   			$('#popup_callback .result').text('');


					   		},5000);

		          		}
		          		else
		          		$('.result').text('Ошибка: запрос не отправлен!');
		            }
		});


	}


}

$(document).ready(function(){
	 $("#popup_callback").keypress(function(e){
	   if(e.keyCode==13){
		  send_callback(); return false;
	   }
	 });

});

</script>
		
<div id="popup_callback" style="display: none;" class="popup">
	<div class="popup_close">
		<a href="#" title="Закрыть окно" onclick="return false;"></a>
	</div>
	<div class="popup_header">Заказать обратный звонок</div>
	
	<div class="popup_content form">
		<p>Мы свяжемся с вами в ближайшее время.</p>
		<form action="/" class="-visor-no-click">
	
			<div>
				<input id="phone" placeholder="Ваш телефон" class="phone" name="phone" type="text" onkeyup="check_tel(this); return false;" autocomplete="off"/>
			</div>
	
			<div>
				<input id="comment" placeholder="Комментарий (не обязательно)" class="comment" name="comment" type="text" />
			</div>
	
	
	      	<div class="clear"></div>

			<div class="result"></div>
			<div style="float: right; padding: 10px 0px 15px 0;">
					<div>
						<a class="btn big" onclick="send_callback(); return false;" href="#"> Отправить </a>
					</div>
			</div>
	      	

	
		</form>
	</div>
</div>
<?} ?>