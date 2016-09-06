$( document ).ready(function() {

/*	var interval = setInterval(function()
    {
*/

		var jcarousel = $('.slider .jcarousel');

        jcarousel.jcarousel(
        		{
        			animation: {
        	            duration: 0 // make changing image immediately
        	        },
        	        wrap: 'circular'
        	        }
        		);
        jcarousel.on('jcarousel:animate', function (event, carousel) {
            $(carousel._element.context).find('li').hide().fadeIn(500);
        });
        
        jcarousel.hover(
        		function(){
        			jcarousel.jcarouselAutoscroll('stop');
        	    	},function(){
        	    		jcarousel.jcarouselAutoscroll('start');
        	    	}
        );
        
        $('.slider .jcarousel-control-prev').click(function() {jcarousel.jcarouselAutoscroll('stop');});
        $('.slider .jcarousel-control-next').click(function() {jcarousel.jcarouselAutoscroll('stop');});
        
        
       jcarousel.jcarouselAutoscroll({
            interval: 4800,
            target: '+=1',
            autostart: true
        });

        $('.slider .jcarousel-control-prev')
            .on('jcarouselcontrol:active', function() {
                $(this).removeClass('inactive');
            })
            .on('jcarouselcontrol:inactive', function() {
                $(this).addClass('inactive');
            })
            .jcarouselControl({
                target: '-=1'
            });

        $('.slider .jcarousel-control-next')
            .on('jcarouselcontrol:active', function() {
                $(this).removeClass('inactive');
            })
            .on('jcarouselcontrol:inactive', function() {
                $(this).addClass('inactive');
            })
            .jcarouselControl({
                target: '+=1'
            });

  					$('.slider .jcarousel-pagination')
                    .on('jcarouselpagination:active', 'a', function() {
                        $(this).addClass('active');
                    })
                    .on('jcarouselpagination:inactive', 'a', function() {
                        $(this).removeClass('active');
                    })
                    .on('click', function(e) {
                        e.preventDefault();
                    })
                    .jcarouselPagination({
                        item: function(page) {
                            return '<a href="#' + page + '">&nbsp;</a>';
                        }
                    });

/*       clearInterval(interval);

    }, 1000);*/

});