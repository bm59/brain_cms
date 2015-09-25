$(function() {
			
	$('.photogallery img').each(function() 
	{
		
		if ($(this).width()>$(this).height())
		{	
			
			$(this).css('max-height','130px');
			$(this).css('max-width','none');
			
		}
	
	});


});