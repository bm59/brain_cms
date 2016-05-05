// leanModal v1.1 by Ray Stone - http://finelysliced.com.au
// Dual licensed under the MIT and GPL

(function($){$.fn.extend({leanModal:function(options){var defaults={top:100,overlay:0.5,closeButton:null}; 
var overlay=$("<div id='lean_overlay'></div>");
$("body").append(overlay);
options=$.extend(defaults,options);
return this.each(function(){var o=options;
$(this).click(function(e){	var modal_id=$(this).attr("href");
		$("#lean_overlay").click(function(){close_modal(modal_id)});
		$(o.closeButton).click(function(){close_modal(modal_id)});
		var modal_height=$(modal_id).outerHeight();
		var modal_width=$(modal_id).outerWidth();
		$("#lean_overlay").css({"display":"block",opacity:0});
		$("#lean_overlay").fadeTo(200,o.overlay);
		$(modal_id).css({"display":"block","position":"fixed","opacity":0,"z-index":99999999,"left":50+"%","margin-left":-(modal_width/2)+"px","top":o.top+"px"});
		$(modal_id).fadeTo(200,1);
		e.preventDefault()})});

function close_modal(modal_id){$("#lean_overlay").fadeOut(200);$(modal_id).css({"display":"none"})}}})})(jQuery);


function close_modal_my(modal_id)
{
	$("#lean_overlay").fadeOut(200);
 	$(modal_id).css({"display":"none"});
}

function show_modal_my(modal_id)
{
	$("#lean_overlay").fadeTo(200,0.5);
 	var modal_height=$(modal_id).outerHeight();
	var modal_width=$(modal_id).outerWidth();
	
	$(modal_id).css({"display":"block","position":"absolute","opacity":0,"z-index":99999999,"left":50+"%","margin-left":-(modal_width/2)+"px","top":jQuery(window).scrollTop()+(($(window).height()-modal_height)/2)+"px"});
	$(modal_id).fadeTo(200,1);


	$(modal_id).find('.popup_close').click(function(){		close_modal_my(modal_id);
	});

}
function show_modal_feedback(modal_id, number)
{

	$.ajax({
        type: "POST",
        url: "/imag.php",
        data: "session_id="+session_id+"&action=get_feed_info&number="+number,
        dataType: 'json',
        success: function(data){
            	if (data.ok=='ok' && data.name!='')
            	{
            		$('.popup_header').html('Отзывы: '+data.name);
      
            		$('#feedback_list').show();
            		$('[name=number]').val(number);
            		$.ajax({
            	        type: "POST",
            	        url: "/imag.php",
            	        data: "session_id="+session_id+"&action=get_feed_text&number="+number,
            	        success: function(html){
            	        	$('#feedback_list').html(html);
            	        	
            	        	if (html=='')
            	        	$('#feedback_list').html('<small>Отзывов пока нет. Ваш отзыв может быть первым.</small>');
            	        }
            		});
            		
            		show_modal_my(modal_id);
            	}
            	else $('[name=number]').val(0);
            	
        }
    });

}

function imgLoaded(img){
    var $img = $(img);

    $img.parent().addClass('loaded');
};
