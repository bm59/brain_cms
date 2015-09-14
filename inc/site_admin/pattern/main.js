 $(function() {

	new_count=$("input[type=hidden][name*=id_new]").length+1;
	
  /*   $( "#sortable" ).sortable({
      handle: ".drag_icon"
    }); */
    $(function() {
        $( "#sortable, #sortable2" ).sortable({
          connectWith: ".connectedSortable",
          handle: ".drag_icon"
        });
      });

	/* Изменение селекта тип данных */
    $(document).on('change','select', function() {

     	cur_id=$(this).attr('name');
     	cur_id=cur_id.split('_');
		if (cur_id[1]!='')
		$('[name=table_type_'+cur_id[1]+']').val($(this).find(':selected').attr('data-default'));
		
        return false;
    });

    /* Чекбоксы в общих настройках раздела */
    $(document).on('change',".dt_settings input[type='checkbox']", function() {

		var inp_str=$(this).parents('.dt_settings').find('.setting_text').val();
		if ($(this).is(":checked")==false)
		{
			inp_str=inp_str.replace($(this).attr('data-name')+'|','');
			if (inp_str=='|') inp_str='';
		}
		else 
		inp_str+=(inp_str=='' ? '|':'')+$(this).attr('data-name')+'|';

		$(this).parents('.dt_settings').find('.setting_text').val(inp_str);
		//alert($(this).attr('data-name'));
        return false;
    });

    /* Чекбоксы в настройках колонки */
     $(document).on('change',".section_settings input[type='checkbox']", function() {

		var inp_str=$(this).parents('.section_settings').find('.setting_text').val();
		if ($(this).is(":checked")==false)
		{
			inp_str=inp_str.replace($(this).attr('data-name')+'|','');
			if (inp_str=='|') inp_str='';
		}
		else 
		inp_str+=(inp_str=='' ? '|':'')+$(this).attr('data-name')+'|';

		$(this).parents('.section_settings').find('.setting_text').val(inp_str);
		//alert($(this).attr('data-name'));
        return false;
    });

     /* Клик на подсказке */
     $(document).on('click',".help a", function() {

    	var inp_str=$(this).parents('.dt_settings').find('.setting_text').val();

		var search_str=$(this).html();
		var split_str=search_str.split('=');
		if (split_str[0]!='') search_str=split_str[0];

		    	
		if ($(this).css('color')!='rgb(255, 0, 0)')
		{
			inp_str+=(inp_str=='' ? '|':'')+$(this).html()+'|';
			$(this).css('color', 'rgb(255, 0, 0);');
			$(this).css('text-decoration', 'none');
			
			$(this).parents('.dt_settings').find('.setting_text').val(inp_str);
		}
		else
		{
			

			var result=new RegExp("("+search_str+"?[^\\|]+\\|)", "i").exec(inp_str); 
			if (result)
			{
				inp_str=inp_str.replace(result[0],'');
				
			}
				
				
			$(this).css('color', 'rgb(0, 0, 0);');
			$(this).css('text-decoration', 'underline');	
		}
		$(this).parents('.dt_settings').find('.setting_text').val(inp_str);
        return false;
    });

     /* Подсказки в основных настройках раздела */
     $(document).on('click',".settings_main a", function() {

    	var inp_str=$('.section_settings .setting_text').val();

    	inp_str=inp_str.replace($(this).html()+'|','');

    	inp_str+=(inp_str=='' ? '|':'')+$(this).html()+'|';

		
    	$('.section_settings .setting_text').val(inp_str);
        return false;
    });

     /* Подсказки в стилях Edit Search */
     $(document).on('click',".style_help a", function() {

     	var inp_str=$('[name='+$(this).attr('data-field')+']').val();

    	inp_str=inp_str.replace($(this).html()+'|','');

    	inp_str+=(inp_str=='' ? '|':'')+$(this).html()+'|';

		
    	$('[name='+$(this).attr('data-field')+']').val(inp_str);
        return false;
    });

     /* Иконка - показать настройки */
    $(document).on('click',".connectedSortable .show_settings", function() {


		var parent=$(this).parents('td').prev();
		if (!parent.find('.dt_settings').is(':visible'))
		{
			$('.dt_settings').show();
		}
		else
		{
			$('.dt_settings').hide();
		}
		var elem=$(this).parents('td');
      	destination = elem.offset().top-50;
        $("html, body").animate({scrollTop:destination},"fast");
        return false;
    });

    /* Включение\отключение колонок */
    $(document).on('click',".connectedSortable .on_off", function() {

		cur_val=$('[name='+$(this).attr('data-inp')+']');
    	var inp_str=$(this).parents('tr').find('.setting_text').val();
		if (cur_val.val()==1)
		{
			inp_str=inp_str.replace('off|','');
			cur_val.val(0);
			$(this).css('backgroundImage', 'url("/pics/editor/on.png")');
		}
		else
		{ 
			inp_str+=(inp_str=='' ? '|':'')+'off|';
			cur_val.val(1);
			$(this).css('backgroundImage', 'url("/pics/editor/off.png")');
		}

		$(this).parents('tr').find('.setting_text').val(inp_str);
		//alert($(this).attr('data-name'));
        return false;
        
    });


     /* Добавление шаблона */

    $('#sortable2 .drag_icon img').attr('src','/pics/editor/upload.png');

    $(document).on('click',"#sortable2 .drag_icon", function() {

    	$('#sortable').append('<tr>'+$(this).parents('TR').html()+'</tr>');
		$(this).parents('TR').hide();

		var elem=$('#sortable td:last');
		destination = elem.offset().top-50;
        $("html, body").animate({scrollTop:destination},"fast");

        return false;
        
    });
		
	
  });

  var new_count=1;
  function add_empty(section_id) {
  	$.ajax({
          type: "POST",
          url: "/inc/site_admin/pattern/ajax.php",
          data: "action=add_empty&section_id="+section_id+"&new_count="+new_count,
          success: function(html){
        	  $('#sortable').append(html);
        	  new_count++;
          }
      });
  }