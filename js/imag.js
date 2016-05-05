
function basket_add(session_id, tmp_order_id, good_id, kol, price_field)
{
		if (!tmp_order_id>0) 	{alert ('Ошибка. Не передан номер заказа'); return;}
		if (!good_id>0) 		{alert ('Ошибка. Не передан id товара'); return;}

		$.ajax({
            type: "POST",
            url: "/ajax.php",
            data: "action=basket_add&session_id="+session_id+'&good_id='+good_id+'&tmp_order_id='+tmp_order_id+'&kol='+kol+'&price_field='+price_field,
            dataType: 'json',
            success: function(data)
            {
            	if (data.error!='' && data.error!= undefined) alert(data.error);

            }
        });
}
function basket_delete(session_id, tmp_order_id, good_id, kol, elem)
{
		if (!tmp_order_id>0) 	{alert ('Ошибка. Не передан номер заказа'); return;}
		if (!good_id>0) 		{alert ('Ошибка. Не передан id товара'); return;}
		$.ajax({
            type: "POST",
            url: "/ajax.php",
            data: "action=basket_add&session_id="+session_id+'&good_id='+good_id+'&tmp_order_id='+tmp_order_id+'&kol='+kol,
            dataType: 'json',
            success: function(data)
            {
            	if (data.error!='' && data.error!= undefined) alert(data.error);
            	$(elem).parents('tr').hide();
            	
            	
            	if ($('.order_table tr:visible').length<=1)
            	{
            		$('.order_table').hide();
            		$('.order_form').hide();
            		$('.order .alert_empty').show();
            	}
            	
            	basket_comment(session_id, tmp_order_id);
            	
            }
        });
}
function basket_comment (session_id, tmp_order_id)
{
	$.ajax({
        type: "POST",
        url: "/ajax.php",
        data: "action=basket_comment&session_id="+session_id+'&tmp_order_id='+tmp_order_id,
        dataType: 'json',
        success: function(data)
        {
        	var comment='товары не выбраны';
        	
        	if (data.comment!='' && data.comment!=null)
        	{
        		comment=data.comment;
        		$('.basket_small .button').show();
        		
        	}
        	else
        	{
        		$('.basket_small .button').hide();
        		comment='выберите товары';
        	}
        	
        	$('.basket_small .comment').html(comment);
        	$('.main_menu .basket .comment').html(comment);
        }
    });
}
function basket_update_summ(session_id, tmp_order_id, good_id, elem)
{
		if (!tmp_order_id>0) 	{alert ('Ошибка. Не передан номер заказа'); return;}
		if (!good_id>0) 		{alert ('Ошибка. Не передан id товара'); return;}

		$.ajax({
            type: "POST",
            url: "/ajax.php",
            data: "action=basket_update_summ&session_id="+session_id+'&good_id='+good_id+'&tmp_order_id='+tmp_order_id,
            dataType: 'json',
            success: function(data)
            {
            	if (data.error!='' && data.error!= undefined) alert(data.error);
            	
            	if (parseInt(data.summ)>0) elem.find('.summ').html(data.summ);
            }
        });
}
