    function set_order_up_down(el)
    {
            var child_count=$('#'+el.attr('id')+' TR').length;
   		    var i=0;
   		    $('#'+el.attr('id')+' TR').each(function()
			{
                i++;
                if (i==1) $(this).find('.up').attr('id', 'disable');
                else $(this).find('.up').removeAttr('id');

                if (i==child_count)
                $(this).find('.down').attr('id', 'disable');
                else
                $(this).find('.down').removeAttr('id');

			});
    }
    function Gotopage(url){
    	document.location.replace(url);
    }
    function set_click_up_down (table_id)
    {
        $("#"+table_id+" .up").click(function()
        {
        	var pdiv = $(this).parents('TR');
        	pdiv.insertBefore(pdiv.prev());
        	set_order_up_down($(this).parents('TABLE'));
        	return false;
    	});
	    $("#"+table_id+" .down").click(function(){
	        var pdiv = $(this).parents('TR');
	        pdiv.insertAfter(pdiv.next());
	        set_order_up_down($(this).parents('TABLE'));
	        return false;
	    });
    }

function ShowAndHide(obj) {
	$('#'+obj).toggle('fast');
	$('html, body').stop().animate({scrollLeft: 0, scrollTop:$('#'+obj).offset().top}, 1000);
}
function selectSinonimSelect(name){
	$('#'+name+'_items_container').toggle();
}
function selectSinonimHide(name){
	$('#'+name+'_items_container').hide();
}
function selectSinonimShow(name){
	$('#'+name+'_items_container').show();
}
function selectSinonimSetValue(name,value,obj){

    var newvalue=$(obj).html();
    if (newvalue=='&nbsp;') newvalue='';
    $('#'+name+'_show_item').val(newvalue);
    $('#'+name+'_value_item').val(value);
    selectSinonimSelect(name);

}
