$(function() {

	var interval = setInterval(function()
    {


		var jcarousel = $('.jcarousel');

        jcarousel.jcarousel({
                wrap: 'circular'
            });

       jcarousel.jcarouselAutoscroll({
            interval: 3500,
            target: '+=1',
            autostart: true
        });

        $('.jcarousel-control-prev')
            .on('jcarouselcontrol:active', function() {
                $(this).removeClass('inactive');
            })
            .on('jcarouselcontrol:inactive', function() {
                $(this).addClass('inactive');
            })
            .jcarouselControl({
                target: '-=1'
            });

        $('.jcarousel-control-next')
            .on('jcarouselcontrol:active', function() {
                $(this).removeClass('inactive');
            })
            .on('jcarouselcontrol:inactive', function() {
                $(this).addClass('inactive');
            })
            .jcarouselControl({
                target: '+=1'
            });

  					$('.jcarousel-pagination')
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

       clearInterval(interval);

    }, 1000);

});