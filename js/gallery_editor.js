	$(document).ready(function()
	{
    	var j=0;
    	$('.photogallery').each(function()
		{
			j++;
			$(this).find('IMG').each(function(i,el)
			{
				 $(el).wrap('<div class="photo_row"><a href="' + $(el).attr('src') + '" rel="lightbox-'+j+'"></a></div>');
			});

		});
	});
