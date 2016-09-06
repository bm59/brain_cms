$(function() {
			
	$('.goods .img img').each(function() 
	{

		var parent_width=$(this).parents('.img').width();
		var parent_height=$(this).parents('.img').height();

		
		if ((parent_width-$(this).width())<(parent_height-$(this).height()))
		{	
			var val=Math.round((parent_height-$(this).height())/2);
			if (val>0)
			{
				$(this).css('top',val+'px');
				$(this).css('position','absolute');
			}
			
		}
		else
		{
			var val=Math.round(($(this).height()-parent_height)/2);
			if (val>0)
			{
				$(this).css('top', '-'+val+'px');
				$(this).css('position','absolute');
			}
		}
	
	
	});


});