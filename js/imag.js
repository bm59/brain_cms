$(function() {
	
	$(".goods li, .main_image").hover(
	        function () {
	             //$(this).find('.price').stop(true, true).animate({opacity: 0.3}, 200);
	             //$(this).find('.hit').stop(true, true).animate({opacity: 0.3}, 200);
	        }, 
	        function () {
	        	 //$(this).find('.price').stop(true, true).animate({opacity: 1}, 200);
	        	 //$(this).find('.hit').stop(true, true).animate({opacity: 1}, 200);
	        }
	);
	
	$(".cat_menu .main a").click(function() {
		
		if ($('.cat_menu .sub_'+$(this).parents('li').attr('id')))
		{
			var el=$('.cat_menu #sub_'+$(this).parents('li').attr('id'));
			if (el.is(':hidden'))
			{
				$(this).addClass('expand');
				el.slideDown();
			}
			else 
			{	
				$(this).removeClass('expand');
				el.slideUp();
			}
		
		}

	});
	
});


function basket_add(session_id, tmp_order_id, good_id, kol, price_field, size_id, color_id)
{
			
		if (!tmp_order_id>0) 	{alert ('Ошибка. Не передан номер заказа'); return;}
		if (!good_id>0) 		{alert ('Ошибка. Не передан id товара'); return;}
		
		$.ajax({
            type: "POST",
            url: "/ajax.php",
            data: "action=basket_add&session_id="+session_id+'&good_id='+good_id+'&tmp_order_id='+tmp_order_id+'&kol='+kol+'&price_field='+price_field+'&size_id='+size_id+'&color_id='+color_id,
            dataType: 'json',
            success: function(data)
            {
            	if (data.error!='' && data.error!= undefined) 
            	alert(data.error);
            	else
            	{
            		/*if (kol>0)
            		{
            			$('.good_right').find('.mybtn.main').html('в корзине: '+kol);
                		$('.good_right').find('[name=good_kol]').val(kol);
                		$('.good_right').find('.mybtn.main').addClass('active');
                		$('.good_right').find('.basket_clear').show();
            		}
            		else
            		{
            			$('.good_right').find('.mybtn.main').html('в корзину');
            			$('.good_right').find('.mybtn').removeClass('active');
            			$('.good_right').find('[name=good_kol]').val(0);
            			$('.good_right').find('.basket_clear').hide();

            			$('.sizes a, .colors a').show();
            			$('.sizes a, .colors a').removeClass('active');
            		}*/
            		
            		$('.order_table #'+good_id).find('[name=kol]').val(kol);

            	}

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
        	var comment='Корзина';
        	
        	if (data.comment!='' && data.comment!=null)
        	{
        		comment=data.comment;
        		$('.basket_small .button').show();
        		
        	}
        	else
        	{
        		$('.basket_small .button').hide();
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
