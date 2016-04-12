<?

class CCRubricator extends VirtualContent
{

	function init($settings){
        		global $SiteSections;
                VirtualContent::init($settings);

                $section = $SiteSections->get($this->getSetting('section'));
                $this->Settings['settings_personal']=$section['settings_personal'];

                $SiteSettings = new SiteSettings;
                $SiteSettings->init();
                $this->Settings['onpage'] = $this->Settings['settings_personal']['on_page']>0 ? $section['settings_personal']['on_page'] : 20;



                $this->like_array=array();/* Где нет в названии "name", но нужен поиск по like*/
                $this->not_like_array=array();/* Где есть в названии "name", но не нужен поиск по like*/
                $this->no_auto=array(); /*Не обрабатывать переменные, ручная обработка*/

                /*заменить на пустое в названиях переменны при поиске*/
                $this->field_tr=array('search_'=>'','_from'=>'','_to'=>'');

                /*подмена названий*/
                $this->field_change=array();


 				$this->getSearch();



   }
   function save(){
   	$errors = array();
   	$dataset = $this->getSetting('dataface');
   	foreach ($dataset['types'] as $k=>$dt){

   		if (!isset($dt['settings']['off']))
   		{
   			$tface = $dt['face'];
   			$err = $tface->preSave();
   			foreach ($err as $v) $errors[] = $v;
   			$dataset['types'][$k]['face'] = $tface;
   		}

   	}

   	if (count($errors)==0){
   		$update = '';
   		$pub = $this->getPub($_GET['pub']); $pub['id'] = floor($pub['id']);
   		if ($pub['id']<1){
   			$count = msr(msq("SELECT COUNT(id) AS c FROM `".$this->getSetting('table')."`"));
   			$count = floor($count['c']);
   			$update.= (($update!='')?',':'')."`precedence`='$count'";
   			msq("INSERT INTO `".$this->getSetting('table')."` (`show`) VALUES ('1')");
   			$pub['id'] = mslastid();
   		}

   		foreach ($dataset['types'] as $dt)
   		{
   			$tface = $dt['face'];
   			$tface->init(array('uid'=>floor($pub['id'])));
   			$tface->postSave();
   			$update.= (($update!='')?',':'').$tface->getUpdateSQL((int)$_GET['section']);
   			$dataset['types'][$k]['face'] = $tface;
   		}

   		if ($_GET['parent_id']>0) $update.=(($update=='') ? '' : ',').'`parent_id`='.$_GET['parent_id'];
   		msq("UPDATE `".$this->getSetting('table')."` SET ".$update." WHERE `id`='".$pub['id']."'");



   		WriteLog($pub['id'], (($_GET['pub']=='new') ? 'добавление':'редактирование').' записи', '','','',$this->getSetting('section'));
   	}

   	$this->setSetting('dataface',$dataset);
   	return $errors;
   }
   function getList($page=0){

   	if ($_GET['drop']>0 && $_GET['destination']>=0)
   	{
   		if (!isset($_GET['change_prec']))
   			msq("UPDATE `".$this->getSetting('table')."` SET `parent_id`=".$_GET['destination']." WHERE id=".$_GET['drop']);
   		else
   		{
   			$new_prec=msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE id=".$_GET['drop']));
   			$old_prec=msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE id=".$_GET['destination']));


   			msq("UPDATE `".$this->getSetting('table')."` SET `precedence`=".$new_prec['precedence']." WHERE `id`=".$old_prec['id']);
   			msq("UPDATE `".$this->getSetting('table')."` SET `precedence`=".$old_prec['precedence']." WHERE `id`=".$new_prec['id']);

   		}

   		?>
   		<script>
   		window.location.href = '/manage/control/contents/?section=<?=$_GET['section']?>';
   		</script>
   		<?

   	}
   	header("Location: /manage/control/contents/?section=".$_GET['section']."\n");

   	$retval = array();

   	$q = "SELECT * FROM `".$this->getSetting('table')."`".$this->sqlstr.(($this->sqlstr=='') ? ' WHERE ':' and ').' parent_id is NULL or parent_id=0';
   	$count = msq($q);
   	$count = mysql_num_rows($count);

   	$page = floor($page);
   	if ($page==-1 || isset($this->Settings['settings_personal']['no_paging'])) $this->setSetting('onpage',10000);
   	if ($page<1) $page = 1;


   	$this->setSetting('pagescount',ceil($count/$this->getSetting('onpage')));
   	$this->setSetting('count',ceil($count));

   	if ($this->getSetting('pagescount')>0 && $page>$this->getSetting('pagescount')) $page = $this->getSetting('pagescount');
   	$this->setSetting('page',$page);


   	if ($_GET['sort']!='')
   	{
   		$order_by="ORDER BY `".$_GET['sort']."` ".$_GET['sort_type'];
   	}
   	else
   	$order_by=$this->Settings['settings_personal']['default_order']!='' ? $this->Settings['settings_personal']['default_order'] : "ORDER BY `precedence` ASC";


   	$this->order_by=$order_by;

   	$q = msq($q." ".$order_by." LIMIT ".(($page-1)*$this->getSetting('onpage')).",".$this->getSetting('onpage'));


   	while ($r = msr($q)) $retval[] = $r;

   	return $retval;
   }
   function PrintPub($pub) {

   		$child_count=msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `parent_id`=".floor($pub['id'])." ORDER BY `precedence` ASC");
   		while ($r=msr($child_count))
   		{

   			print '<li>';
   			$this->PrintItem($r);
   				$cur_child_count=msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `parent_id`=".floor($r['id'])." ORDER BY `precedence` ASC");
   					if (mysql_num_rows($cur_child_count)>0)
   					{
   						print '<ul>';
   							$this->PrintPub($r);
   						print '</ul>';
   					}
   			print '</li>';

   		}

   }
   function drawAddEdit(){
   	if ($_GET['parent_id']>0)
   	{
   		$pub=msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`=".$_GET['parent_id']));
   		print '<div style="padding-left: 50px;"><h2>Добавление потомка к записи: '.$pub['name'].'</h2></div>';
   	}
   	VirtualContent::drawAddEdit();

   }
   function PrintItem ($pub)
   {

   			?>
   				<div class="item" id="<?=$pub['id'] ?>" data-parent="<?=floor($pub['parent_id'])?>">
   					<img src="/pics/editor/anchor.png" class="anchordrop">
   					<span class="val"><?=$pub['name'] ?></span>
   					<div class="controls">
   						<a title="Вкл.Откл." class="onoff" data-id="<?=$pub['id']?>" href="/manage/control/contents/?section=<?=$_GET['section'] ?>&pub=new&parent=<?=$pub['id'] ?>"><img alt="Вкл.Откл." src="/pics/editor/<?=$pub['show']==0 ? 'off.png' : 'on.png'?>" title="<?=$pub['show']==0 ? 'Отключена' : 'Включена'?>"></a>
   						<a title="Добавить потомка" href="/manage/control/contents/?section=<?=$_GET['section'] ?>&pub=new&parent_id=<?=$pub['id'] ?>"><img alt="Добавить потомка" src="/pics/editor/plus.gif"></a>
   						<a title="Редактировать" href="/manage/control/contents/?section=<?=$_GET['section'] ?>&pub=<?=$pub['id'] ?>"><img alt="Редактировать" src="/pics/editor/prefs.gif"></a>
   						<a title="Удалить" onclick="if (!confirm('Удалить запись')) return false;" href="/manage/control/contents/?section=<?=$_GET['section']?>&delete=<?=$pub['id']?>"><img alt="Удалить" src="/pics/editor/delete.gif"></a>
   					</div>
   				</div>
   			<?
   			return;
   }
   function deletePub($id,$updateprec = true){
   	$id = floor($id);
   	global $CDDataSet;
   	if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'"))){
    		$dataset = $CDDataSet->get($this->getSetting('dataset'));
   		$imagestorage = $this->getSetting('imagestorage');
   		foreach ($dataset['types'] as $dt){
   			$tface = new $dt['type'];
   			$tface->init(array('name'=>$dt['name'],'description'=>$dt['description'],'value'=>$r[$dt['name']],'imagestorage'=>floor($imagestorage['id']),'theme'=>$dataset['name'].'_'.$this->getSetting('section'),'rubric'=>$dt['name'],'uid'=>floor($r['id']),'settings'=>$dt['settings']));
   			$tface->delete();
   		}
   		msq("DELETE FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'");

   		$child_count=msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `parent_id`=".$id." ORDER BY `precedence` ASC");
   		while ($ch=msr($child_count))
   		{
   			$this->deletePub($ch['id']);
   		}

   		if ($updateprec) $this->updatePrecedence();
   		WriteLog($id, 'удаление записи', '','','',$this->getSetting('section'));
   		return true;
   	}
   	return false;
   }
   function drawPubsList(){
   	global $SiteSections, $CDDataSet, $CDDataType;

   	$this->generateMeta('name');

   	$dataset = $this->getSetting('dataface');

   	$section = $SiteSections->get($this->getSetting('section'));


   ?>
   <div id="content" class="forms">
	<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
<style>
UL UL	{margin: 0 0 0 30px;}
UL LI {background: none;}
UL img {display: block; float: left; margin-right: 15px; padding: 2px; border: 2px solid rgba(0, 0, 0, 0);}
.droppable {border: 2px solid rgba(0, 0, 0, 0); padding: 2px;}
UL .item {border: 1px solid #CCCCCC; line-height: 25px; margin: 5px 0; padding: 8px;}

.controls {position: absolute; right:5px; top: 10px;}
.controls img {margin-right: 5px;}

img.dropactive {border: 2px solid #5F5F5F;}
img.drophover {border: 2px solid #00cc00;}
img.active {border: 2px solid #cc0000;}
img.active_prec {border: 2px solid #0000cc;;}
</style>
<script>
var ctrlMode = false;
$(document).keydown(function(e)	{if(e.ctrlKey){ctrlMode = true;};});
$(document).keyup(function(e)	{ctrlMode = false; });
$(function() {

	$(".item").hover(function(){
		$(".item").css("background-color", "#FFFFF");
		$("div[data-parent='"+$(this).attr('data-parent')+"']").css("background-color", "#C8E6FF");
		$(this).parents('UL:first').find('ul > ul .item').css("background-color", "#99FFCC");
		$("div[data-parent='"+$(this).attr('id')+"']").css("background-color", "#99FFCC");
	});

	$('.main_ul').mouseleave(function(){
		$(".item").css("background-color", "#FFFFF");
	});

    var dropMode = false;

	// $ UI Draggable
	$(".anchordrop").draggable({

		// возвращает товар обратно на свое место после перетаскивания
		revert:true,

		// как только начинается перетаскивание мы делаем прозрачными остальные объекты
		// добавляем класс CSS
		drag:function () {
			if (ctrlMode)
			{
				$(this).addClass("active_prec");
				$( ".droppable" ).droppable({disabled: true});
			}
			else
			{
				$(this).addClass("active");
				$( ".droppable" ).droppable({disabled: false});
			}

		},

		// удаляем CSS класс после перетаскивания
		stop:function () {
			$(this).removeClass("active");
			$(this).removeClass("active_prec");
		}
	});

    // $ Ui Droppable
	$(".anchordrop, .droppable").droppable({

		// CSS класс для корзины, срабатывает в момент начала перетаскивания товара
		activeClass:"dropactive",

		// CSS класс для корзины при появлении товара в области корзины
		hoverClass:"drophover",

		tolerance:"touch",
		over: function( event, ui )
		{

       		if (ctrlMode && ui.draggable.attr('data-parent')!=$(this).attr('data-parent'))
       		{
       			$(this).attr('class', 'dropactive');
       			enablectrlMode=false;
       		}
       		else enablectrlMode=true;


		},
		drop:function (event, ui)
		{
            if (enablectrlMode)
            {
	                if (ctrlMode)
	                {
	                	if (confirm('Поменять местами разделы?'))
	                	{
	                		var href='/manage/control/contents/?section=<?=$_GET['section']?>&drop='+parseInt(ui.draggable.parent('div:first').attr('id'))+'&destination='+parseInt($(this).parent('div:first').attr('id'))+'&change_prec=true';
	                		window.location.href = href;
	                		ui.draggable.parent('div:first').hide('slow');
	                    }
	                }
	                else
	                {
	                	if (confirm('Перенести раздел?'))
	                	{
							var href='/manage/control/contents/?section=<?=$_GET['section']?>&drop='+parseInt(ui.draggable.parent('div:first').attr('id'))+'&destination='+parseInt($(this).parent('div:first').attr('id'));

	                		window.location.href = href;
	                    	ui.draggable.parent('div:first').hide('slow');
	               		}
	                }
	        }

		}
	});
});


var session_id = '<?php echo session_id(); ?>';
$(function() {
    $(document).on('click','.onoff', function() {
        var id=$(this).attr("data-id");
        var elem=$(this);
		if (id>0)
		{
			$.ajax({
	            type: "POST",
	            url: "/inc/site_admin/pattern/ajax_class.php",
	            data: "action=onoff&id="+id+"&table=<?=$this->getSetting('table')?>&session_id="+session_id,
	            dataType: 'json',
	            success: function(data){
	            	elem.children('img').attr('src', '/pics/editor/'+data.signal);
	            }
	        });
		}

        return false;
    });

    jQuery.expr[":"].contains = function( elem, i, match, array ) {
        return (elem.textContent || elem.innerText || jQuery.text( elem ) || "").toLowerCase().indexOf(match[3].toLowerCase()) >= 0;
    }

	$('[name=search_text]').keyup(function()
	{
		var s_text=$(this).val();

		$(".item").css("border", "1px solid rgb(204, 204, 204)");
		if (s_text!='')
		$(".item:contains('"+s_text+"')").css("border", "1px solid rgb(255, 0, 0)");

	});

	$( ".clear_search").click(function() {
		$(".item").css("border", "1px solid rgb(204, 204, 204)");
		$('[name=search_text]').val('');

	});

});
</script>
		<div class="place">
			<span class="input" style="width: 400px; margin-left: 30px; float: left;">
				<input type="text" value="" maxlength="255" name="search_text" placeholder="Поиск">
			</span>
			<div style="float: left; padding-top: 20px;"><a href="#" title="Очистить" onclick="if (!confirm('Удалить запись')) return false;" class="clear_search"><img src="/pics/inputs/clear.png" alt="Очистить"></a></div>
		</div>
		<div class="clear"></div>
   	<div class="hr"><hr></div>
   	<div id="0"><img src="/pics/editor/anchor_disabled.png" class="droppable"></div>
   	<ul style="padding-left: 10px;" class="main_ul">
   	<?
   	$list = $this->getList($_GET['page']);
   	$this->updatePrecedence();


   	$this->PrintPub($pub);


   	?>
	   	 <span class="clear"></span>
            <div class="place">
			   <a href="./?section=<?=$section['id']?>&pub=new" class="button big" style="float: right;">Добавить</a>
			</div>
   	</ul>
   	</div>
   	<?

  }


}
?>