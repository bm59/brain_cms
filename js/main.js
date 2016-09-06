$(function() {

	
	 $(window).scroll(function() {	
		 made_menu();
	 });
	 $(window).on('resize', function(){
		 	$('.main_menu').css('position', 'absolute');
			$('.main_menu').css('left', 'auto');
			$('.main_menu').css('top', 'auto');
			$('.main_menu').css('bottom', '-7px');
	 });
	 
	 function made_menu (){

		var offset = $('.main_menu').offset();

		if ($(window).width()>630)
		{
			 if ($(window).scrollTop() >= $('.header').height()+7)
			{
				$('.main_menu').css('position', 'fixed');
				$('.main_menu').css('top', '0');
				$('.main_menu').css('left', offset.left);
			}
			else
			{
				$('.main_menu').css('position', 'absolute');
				$('.main_menu').css('left', 'auto');
				$('.main_menu').css('top', 'auto');
				$('.main_menu').css('bottom', '-7px');
			}
		}

	 }
	 
	 made_menu();
});