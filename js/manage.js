    function set_order_up_down(el)
    {
            var child_count=jquery('#'+el.attr('id')+' TR').length;
   		    var i=0;
   		    jquery('#'+el.attr('id')+' TR').each(function()
			{
                i++;
                if (i==1) jquery(this).find('.up').attr('id', 'disable');
                else jquery(this).find('.up').removeAttr('id');

                if (i==child_count)
                jquery(this).find('.down').attr('id', 'disable');
                else
                jquery(this).find('.down').removeAttr('id');

			});
    }

    function set_click_up_down (table_id)
    {
        jquery("#"+table_id+" .up").click(function()
        {
        	var pdiv = jquery(this).parents('TR');
        	pdiv.insertBefore(pdiv.prev());
        	set_order_up_down(jquery(this).parents('TABLE'));
        	return false;
    	});
	    jquery("#"+table_id+" .down").click(function(){
	        var pdiv = jquery(this).parents('TR');
	        pdiv.insertAfter(pdiv.next());
	        set_order_up_down(jquery(this).parents('TABLE'));
	        return false;
	    });
    }

function ShowAndHide(obj) {
	jquery('#'+obj).toggle('fast');
	jquery('html, body').stop().animate({scrollLeft: 0, scrollTop:jquery('#'+obj).offset().top}, 1000);
}
function selectSinonimSelect(name){
	jquery('#'+name+'_items_container').toggle();
}
function selectSinonimHide(name){
	jquery('#'+name+'_items_container').hide();
}
function selectSinonimShow(name){
	jquery('#'+name+'_items_container').show();
}
function selectSinonimSetValue(name,value,obj){

    var newvalue=jquery(obj).html();
    if (newvalue=='&nbsp;') newvalue='';
    jquery('#'+name+'_show_item').val(newvalue);
    jquery('#'+name+'_value_item').val(value);
    selectSinonimSelect(name);

/*		var showitem = $(name+'_show_item');
	var valueitem = $(name+'_value_item');
	if (obj.innerHTML=='&nbsp;') newvalue='';
	else  newvalue=obj.innerHTML;
	showitem.value = newvalue;
	valueitem.value = value;
	selectSinonimSelect(name);
	if (valueitem.onchange) valueitem.onchange();*/
}
/* Асинхронная работа с файлами — НАЧАЛО */
function uploadFileAjax(obj,formid,iframeid,inputoldfile,uploadfilestorage,uploadfiletheme,uploadfilerubric,uploadfileuid,onstart,onfinish,del){
        var form = $(formid);
        var iframe = $(iframeid);
        var input = $(inputoldfile);
        var oldfileid = 0;
        if (input) oldfileid = input.value;
        if (form && iframe){
                eval(onstart);
                var oldtarget = form.target;
                var oldaction = form.action;
                form.target = iframeid;
                //form.enctype = "multipart/form-data";
                //form.setAttribute("target", iframeid);

                form.action = '/manage/ajax/fileupload.php?uploadfilename='+obj.name+'&oldfileid='+oldfileid+'&uploadfilestorage='+uploadfilestorage+'&uploadfiletheme='+uploadfiletheme+'&uploadfilerubric='+uploadfilerubric+'&uploadfileuid='+uploadfileuid+'&onfinish='+onfinish+'&delete='+del;
                form.submit();
                form.target = oldtarget;
                form.action = oldaction;
                obj.value = '';
        }
}