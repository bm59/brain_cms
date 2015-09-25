	$(document).ready(function(){
		$('.table-content tr:odd:has(td)').addClass('odd');
        $('.table-content tr:even:has(td)').addClass('even');

        $(".table-content").each(function() {
          $('th:first',this).addClass('first');
          $('th:last',this).addClass('last');
        });
	});
