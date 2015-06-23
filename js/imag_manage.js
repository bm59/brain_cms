function accept_order(ordernum, action)
{
	jQuery.get("/ajax.php", { action: action, ordernum: ordernum},
	function(data)
	{
		if (data==0)
		{
                    alert('Ошибка!');
        }
        else
        {
			totaldata=data;
			data=eval('('+data+')');
			if (data.ok!='ok')
        	alert('Ошибка ответа скрипта!');
        	else
        	{
				document.getElementById('onoff_'+ordernum).src= '/pics/editor/'+data.signal;
				document.getElementById('reason_'+ordernum).style.display = ((data.signal=="on.gif" || data.signal=="valid.png")?"none":"block");
			}
		}
	});


}