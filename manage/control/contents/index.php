<?
/*error_reporting(E_ALL);
ini_set('display_errors',1);*/
include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/include.php";



$contentStep = 1;
$section = $SiteSections->get(floor($_GET['section']),-1); $section['id'] = floor($section['id']);


if ($section['id']>0) $contentStep = 3;
if ($section['id']>0 && $_GET['type_edit']=='pattern') $contentStep = 4;

$editsection = $SiteSections->get(floor($_GET['edit']),-1); $editsection['id'] = floor($editsection['id']);
if ($editsection['id']>0){
        if (!isset($editsection['settings']['noeditsettings']) || ($delepmentmode=='development')){
                $contentStep = 2;
                /* Редактирование */
                if ($editsection['id']>0){
                        $editdata = $editsection;
                        $editerrors = array();
                        if (isset($_POST['sectionedit'])){
                                foreach ($_POST as $k=>$v) $editdata[$k] = trim($v);
                                $editdata['isservice'] = 0;

                                $editerrors = $SiteSections->edit($editdata);
                                if (count($editerrors)==0){
                                        $SiteSections->setCacheValue('section_'.floor($editsection['id']),array());
                                        $contentStep = 1;
                                }
                        }

                        WriteLog($editsection['id'], 'редактирование свойств раздела', $editsection['name']);
                }
        }
}
$deletesection = $SiteSections->get(floor($_GET['delete']),-1); $deletesection['id'] = floor($deletesection['id']);
?>
<?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/meta.php";?>
        <?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/header.php";?>
        <?
        if ($contentStep==1){

                /* Перенос */
                if (floor($_GET['drop'])>0)
                {
                        $drop = $SiteSections->get(floor($_GET['drop']),-1); $drop['id'] = floor($drop['id']);
                        $destination = $SiteSections->get(floor($_GET['destination']),-1); $destination['id'] = floor($destination['id']);
                        $before = $SiteSections->get(floor($_GET['before']),-1); $before['id'] = floor($before['id']);

                		if (isset($_GET['change_prec_above']))
                        {
                       		    if ((!isset($drop['settings']['undrop'])) || ($delepmentmode=='development')){
                                        $SiteSections->setPrecedenceAbove($drop['id'],floor($_GET['destination']));
                                        WriteLog($drop['id'], 'перенос раздела: разместить над', $drop['id'].'|'.$_GET['destination']);
                                }
                        }
                        elseif (isset($_GET['change_prec']))
                        {

                       		    if ((!isset($drop['settings']['undrop'])) || ($delepmentmode=='development')){
                                        $SiteSections->setPrecedence($drop['id'],$destination['precedence']);
                                        $SiteSections->setPrecedence($destination['id'],$drop['precedence']);
                                        WriteLog($drop['id'], 'перенос раздела: поменять местами', $drop['id'].'|'.$destination['id']);
                                }
                        }
                		elseif ($drop['id']>0){
                                if ((!isset($drop['settings']['undrop'])) || ($delepmentmode=='development')){
                                        if ($before['id']>0)
                                        {                                        	$SiteSections->setPrecedenceBefore($drop['id'],$before['id']);
                                        	WriteLog($drop['id'], 'перенос раздела: setPrecedenceBefore', $drop['id'].'|'.$destination['id']);
                                        }
                                        elseif ($destination['id']>0){
                                                if ((!isset($destination['settings']['nodestination'])) || ($delepmentmode=='development')){
                                                        $SiteSections->setParent($drop['id'],$destination['id']);
                                                        WriteLog($drop['id'], 'перенос раздела: добавть к', $drop['id'].'|'.$destination['id']);
                                                }
                                        }
                                        elseif (isset($_GET['destination'])){
                                                if ((floor($_GET['destination'])==0) && ($drop['parent']!=0))
                                                {
                                                	$SiteSections->setParent($drop['id'],0);
                                                	WriteLog($drop['id'], 'перенос раздела: в корень', $drop['id'].'|'.$destination['id']);
                                                }
                                                elseif ((floor($_GET['destination'])==-1) && ($drop['parent']!=-1))
                                                {                                                	$SiteSections->setParent($drop['id'],-1);
                                                	WriteLog($drop['id'], 'перенос раздела: в неопубликованное', $drop['id'].'|-1');
                                                }

                                        }
                                }
                        }

                }

                /* Добавление */
                $adddata = array();
                $adderrors = array();
                if (isset($_POST['sectionadd'])){
                        foreach ($_POST as $k=>$v) $adddata[$k] = trim($v);
                        $adddata['isservice'] = 0;
                        $addsets = array();
                        if ($delepmentmode=='development'){
                                $addsets['nopathchange'] = '';
                                $addsets['nodestination'] = '';
                                $addsets['undrop'] = '';
                                $addsets['undeletable'] = '';
                        }
                        else{
                                $addsets['nodestination'] = '';
                        }
                        $adddata['settings'] = $addsets;
                        $adderrors = $SiteSections->add($adddata);
                        if (count($adderrors)==0)
                        {                        	WriteLog(0, 'добавление раздела', $adddata['name']);
                        	$adddata = array();
                        }
                }

                /* Удаление */
                if ($deletesection['id']>0){
                        if ((!isset($section['settings']['undeletable'])) || ($delepmentmode=='development'))
                        {                        	 $SiteSections->delete($deletesection['id']);
                             WriteLog($deletesection['id'], 'удаление раздела', $deletesection['name']);
                        }

                }

                ?>
                <div id="content" class="listing">
                        <?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
                        <table class="table-content stat tusers" cellspacing='0'>
                                <?
                                $anchorcode = 'Название';
                                if ($delepmentmode=='development'){
                                        $anchorcode = 'Название';
                                }
                                ?>
                                <tr>
                                        <th><div class="anchor"><strong><img id="0" class="anchordrop" height="18" width="18" src="/pics/editor/anchor.gif"/></strong><?=$anchorcode?></div></th>
                                        <th class="t_nowrap t_minwidth">Тип раздела</th>
                                        <th colspan="2"></th>
                                </tr>
                                <?
                                $SiteSections->echoSectionList(0,'',0,-1,$delepmentmode,true);
                                ?>
                        </table>
                        <?
                        if ($delepmentmode=='development')
                        {
                        $unpubliclist = $SiteSections->getList(-1,-1,0);
                        ?>
                        <div class="hr"></div>
                        <table class="table-content stat tusers">
                                <?
                                $anchorcode = '<div class="anchor"><strong><img id="-1" class="anchordrop" src="/pics/editor/anchor.gif" width="18" height="18" /></strong>Раздел</div>';
                                ?>
                                <tr>
                                        <th><?=$anchorcode?></th>
                                        <th class="t_nowrap t_minwidth">Тип раздела</th>
                                        <th colspan="2"></th>
                                </tr>
                                <?
                                $SiteSections->echoSectionList(-1,'',0,0,$delepmentmode);
                                ?>
                        </table>
                        <h1><span onClick="ShowAndHide('formadd')" class="link">Добавить раздел</span></h1>
                        <?
                        if (count($adderrors)>0){
                                print '
                                <p><strong>Добавление раздела не выполнено:</strong></p>
                                <ul class="errors">';
                                        foreach ($adderrors as $v) print '
                                        <li>'.$v.'</li>';
                                print '
                                </ul>';
                        }
                        ?>
                        <form name="sectionadd" action="<?=configGet("AskUrl")?>" method="POST" class="hidden" id="formadd">
                                <div class="place" style="z-index: 10; margin-top: 0px;">
                                        <table style="width: 100%; table-layout: fixed;">
                                        <tr><td>
                                        <label>Название раздела</label>
                                        <span class="input">
                                             <input name="name" maxlength="100" value="<?=htmlspecialchars($adddata['name'])?>" />
                                        </span>
                                        </td><td>
                                        <label>Путь</label>
                                        <span class="input">
                                           	<input name="path" maxlength="50" value="<?=htmlspecialchars($adddata['path'])?>" />
                                        </span>
                                        </td><td>
                                        <label>Тип раздела</label>
                                        <?
                                        $values = array();
                                        $values[]="&nbsp";
                                        if (count(configGet('registeredPatterns'))>0)
                                        foreach (configGet('registeredPatterns') as $v){
                                                if (($v['useradd']==1) || ($delepmentmode=='development')) $values[$v['name']] = $v['description'];
                                        }
                                        asort($values);
                                        print getSelectSinonim('pattern',$values,$adddata['pattern']);
                                        ?>
                                        </td></tr></table>
                                </div>
                                <span class="clear"></span>
                                <div class="place">
                                        <table style="width: 100%; table-layout: fixed;">
                                        <tr><td>
                                        <label>Заголовок (title)</label>
                                        <span class="input">
											<input name="title" maxlength="500" value="<?=htmlspecialchars($adddata['title'])?>" />
                                        </span>
                                        </td><td>
                                        <label>Описание (description)</label>
                                        <span class="input">
                                        	<input name="description" maxlength="500" value="<?=htmlspecialchars($adddata['description'])?>" />
                                        </span>
                                        </td><td>
                                        <label>Ключевые слова (keyword)</label>
                                        <span class="input">
                                        	<input name="keywords" maxlength="500" value="<?=htmlspecialchars($adddata['keywords'])?>" />
                                        </span>
                                        </td></tr></table>
                               <table style="width: 100%; table-layout: fixed;">
                                        <tr><td>
                                        <label>Теги, разделитель - "|"</label>
                                        <span class="input">
                                        	<input name="tags" maxlength="100" value="<?=htmlspecialchars($adddata['tags'])?>" />
                                        </span>
                                        </td></td>
                                        </table>
                                </div>
                               <span class="clear"></span>
                                <div class="place">
                                   <table style="width: 100%">
                                       <tr>
                                        <td>
						                    <div class="styled">
												<input type="checkbox" name="visible" id="checkbox" class="checkbox">
												<label for="checkbox">Отображать на сайте</label>
											</div>
                                        </td>
                                        <td>
                                   			<input class="button big" type="submit" name="sectionadd" value="Добавить"  style="float: right;margin-right: 12px"/>
                                        </td>
                                       </tr>
                                   </table>
                                </div>

                                <span class="clear"></span>
                                <div class="place">

                                </div>
                        </form>
                        <?
                        }
                        ?>
                </div>
        <?
        }
        if ($contentStep==2){
                ?>
                <div id="content" class="listing">
                        <?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
                        <?
                        if (count($editerrors)>0){
                                print '
                                <p><strong>Редактирование раздела не выполнено:</strong></p>
                                <ul class="errors">';
                                        foreach ($editerrors as $v) print '
                                        <li>'.$v.'</li>';
                                print '
                                </ul>';
                        }
                        ?>
                        <form name="sectionedit" action="<?=configGet("AskUrl")?>?edit=<?=$editsection['id']?>" method="POST">
                                <div class="place" style="margin-top: 0px;">
                                        <table style="width: 100%; table-layout: fixed;">
                                        <tr><td>
                                        <label>Название раздела</label>
                                        <span class="input">
                                        	<input name="name" maxlength="100" value="<?=htmlspecialchars($editdata['name'])?>" />
                                        </span>
                                        </td>
                                        <?
                                        if ((!isset($editsection['settings']['nopathchange'])) || ($delepmentmode=='development')){
                                        ?>
                                        <td>
                                        <label>Путь</label>
                                        <span class="input">
                                        	<input name="path" maxlength="50" value="<?=htmlspecialchars($editdata['path'])?>" />
                                        </span>
                                        </td>
                                        <?
                                        }
                                        ?>
                                        </tr></table>
                                </div>
                                <span class="clear"></span>
                                <div class="place">
                                        <table style="width: 100%; table-layout: fixed;">
                                        <tr><td>
                                        <label>Заголовок (title)</label>
                                        <span class="input">
                                        	<input name="title" maxlength="500" value="<?=html_entity_decode(htmlspecialchars($editdata['title']))?>" />
                                        </span>
                                        </td><td>
                                        <label>Описание (description)</label>
                                        <span class="input">
                                        	<input name="description" maxlength="500" value="<?=html_entity_decode(htmlspecialchars($editdata['description']))?>" />
                                        </span>
                                        </td><td>
                                        <label>Ключевые слова (keyword)</label>
                                        <span class="input">
                                       		<input name="keywords" maxlength="500" value="<?=htmlspecialchars($editdata['keywords'])?>" />
                                        </span>
                                        </td></tr></table>
                                   <table style="width: 100%; table-layout: fixed;">
                                        <tr><td>
                                        <label>Теги, разделитель - "|"</label>
                                        <span class="input">
                                        	<input name="tags" maxlength="100" value="<?=htmlspecialchars($editdata['tags'])?>" />
                                        </span>
                                        </td></td>
                                        </table>
                                </div>

                <?
                if ($editsection['isservice']!='1' && $editsection['path']!='sitecontent')
                {
                ?>
                                <span class="clear"></span>
                                <div class="place">
                                   <table>
                                       <tr>
                                        <td>
                      <?

                      $visible = $editdata['visible'];

                      if($visible)
                      $checked = "CHECKED";
                      else
                      $checked = "";

                      ?>

                                       		<div class="styled">
												<input type="checkbox" name="visible" id="checkbox" class="checkbox" <?=(($visible) ? 'checked="checked"' :'')?>>
												<label for="checkbox">Отображать на сайте</label>
											</div>
                                        </td>
                                       </tr>
                                   </table>
                                </div>
                <?
                }
                ?>
                                <span class="clear"></span>
                                <div class="place">
                                        <span  style="float: right;">
                                        	<input class="button big" type="submit" name="sectionedit" value="Сохранить изменения" />
                                        </span>
                                </div>
                        </form>
                </div>
                <?
        }
        if ($contentStep==3){
                $SectionPattern = new $section['pattern'];
                $SectionPattern->init(array('section'=>$section['id'],'mode'=>$delepmentmode,'isservice'=>0));
                $SectionPattern->start();
                ?>
                <script>
           	 function getGet(name) {
     		    var s = window.location.search;
     		    s = s.match(new RegExp(name + '=([^&=]+)'));
     		    return s ? s[1] : false;
     			}
                 $(function() {
                	    $.contextMenu({
                	        selector: '#zbody',
                	        callback: function(key, options) {
                	            if (key=='save')
                	            {
                	            	$( "#editform" ).submit();
                	            }
                	            if (key=='section_add')
                	            {
                	            	$( "#editform" ).submit();
                	            }
                	            if (key=='edit_pattern')
                	            {
                	            	window.location.href = './?section='+getGet('section')+'&type_edit=pattern';
                	            }
                	        },
                	        items: {
<?if ($_GET['pub']=='new'){ ?>"section_add": {name: "Добавить", icon: "section_add"},<?} ?>
<?if ($_GET['pub']!='new'){ ?>"save": {name: "Сохранить", icon: "save"},<?} ?>
<?if ($mode=='development' && $section['pattern']!='PSheet1'){ ?>"edit_pattern": {name: "Редактировать шаблон", icon: "edit_pattern"},<?} ?>

                	        }
                	    });
                 });
                 </script>
                <?
        }
        if ($contentStep==4){
        	include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/pattern/edit.php";
        }
        ?>
        <?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/footer.php";?>

