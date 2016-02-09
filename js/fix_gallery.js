$(function() {
			
	$('.photogallery img').each(function() 
	{
		
		var parent_width=$(this).parent().width();
		var parent_height=$(this).parent().height();

		
		if ((parent_width-$(this).width())<(parent_height-$(this).height()))
		{	
			
			$(this).css('max-height',parent_height+'px');
			$(this).css('max-width','none');
			
			var val=Math.round(($(this).width()-parent_width)/2);
			if (val>0)
			$(this).css('left', '-'+val+'px');
			
		}
		else
		{
			
			var val=Math.round(($(this).height()-parent_height)/2);
			if (val>0)
			$(this).css('top','-'+val+'px');
		}
	
	});


});