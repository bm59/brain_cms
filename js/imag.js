$(function()
{
 	$(".good_info_size a").click(function ()
    {
   		var main_parent=$(this).parents('.good_item');
   		var parent=$(this).parents('.good_info_size');
   		var child_id=$(this).attr('id');
   		$(parent).children('a').removeClass();
   		$(this).addClass('active');

   		$(main_parent).find('.td_price_value DIV').html('<img src="/pics/loading.gif">');

   		$.get("/ajax.php", { action: 'get_size_price', id: $(this).attr('id')},
		function(data)
		{
			if (data!=0)
			{
				data=eval('('+data+')');
				if (data.price>0)
                $(main_parent).find('.td_price_value DIV').html(data.price);
                $(main_parent).children('input[name="size_id"]').val(child_id);
			}

		});

   		return false;
    });

    $(".good_item_adit a").click(function ()
    {
   		var main_parent=$(this).parents('.good_item');



   		$(main_parent).find('.good_item_adit_text').toggle('slow');
   		if ($(main_parent).find('.good_item_adit_text').is(":visible"))
   		$(this).text('кратко');
   		else
   		$(this).text('подробнее');



   		return false;
    });

     $(".add_cart").click(function ()
    {

   		var main_parent=$(this).parents('.good_item');
   		var parent=$(this).parents('.good_info_size');
   		var good_id=$(main_parent).children('input[name="good_id"]').val();
        var size_id=$(main_parent).children('input[name="size_id"]').val();


   		if (good_id>0)
   		{

   			var multicolor=$(main_parent).children('input[name="multicolor"]').val();

   			if (multicolor=='1')
   			{
   				var color_id=$(main_parent).find('input[name=color'+good_id+']:checked').val();
   				if (!color_id>0)
   				alert('Для добавления товара нужно выбрать цвет (круглый переключатель по образцом цвета)');
   			}


   			if (multicolor!='1' || color_id>0)
   			{
   				basket_add(good_id, size_id, color_id);

   			var target = $(main_parent).find('.good_item_image IMG:first');
   			var offset = target.offset();

		    var pos = target.position();
			var clone = target.clone()
			  .css({ position: 'absolute', 'z-index': '100', top: offset.top, left: offset.left-200, margin: '2px' })
			  .appendTo(".container")
			  .animate({top: 0, left: 850, width: 50, opacity: 0.6}, 700, function() {clone.remove();});
   			}

   		}


   		return false;
    });
});

function basket_add(good_id, size_id, color_id)
{



 		$.get("/ajax.php", { action: 'basket_add', good_id: good_id, size_id: size_id, color_id: color_id},
		function(data)
		{
			if (data!=0)
			{
				data=eval('('+data+')');
				if (data.ok=='ok')
                {                	$('.cart-info_comment').html('<a href="/basket/">оформить заказ</a>');
                	$('.cart-img').css('background-position','0 -60px');

                	if (data.cnt>0)
                	$('.cart-info--green').text('Корзина ('+data.cnt+')');

                	//alert('Товар добавлен, вы можете перейти в корзину и оформить заказ');
                }
			}
			else alert('Ошибка добавления товара');

		});
}