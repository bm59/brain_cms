<?
/*error_reporting(E_ALL);
ini_set('display_errors',1);*/
include $_SERVER['DOCUMENT_ROOT']."/inc/include.php";



$contentStep = 1;
$section = $SiteSections->get(floor($_GET['section']),-1); $section['id'] = floor($section['id']);
if ($section['id']>0) $contentStep = 3;
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
                }
        }
}
$deletesection = $SiteSections->get(floor($_GET['delete']),-1); $deletesection['id'] = floor($deletesection['id']);
?>
<?include $_SERVER['DOCUMENT_ROOT']."/inc/content/meta.php";?>
<div id="zbody">
        <?include $_SERVER['DOCUMENT_ROOT']."/inc/content/header.php";?>
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
                                }
                        }
                        elseif (isset($_GET['change_prec']))
                        {

                       		    if ((!isset($drop['settings']['undrop'])) || ($delepmentmode=='development')){
                                        $SiteSections->setPrecedence($drop['id'],$destination['precedence']);
                                        $SiteSections->setPrecedence($destination['id'],$drop['precedence']);
                                }
                        }
                		elseif ($drop['id']>0){
                                if ((!isset($drop['settings']['undrop'])) || ($delepmentmode=='development')){
                                        if ($before['id']>0) $SiteSections->setPrecedenceBefore($drop['id'],$before['id']);
                                        elseif ($destination['id']>0){
                                                if ((!isset($destination['settings']['nodestination'])) || ($delepmentmode=='development')){
                                                        $SiteSections->setParent($drop['id'],$destination['id']);
                                                }
                                        }
                                        elseif (isset($_GET['destination'])){
                                                if ((floor($_GET['destination'])==0) && ($drop['parent']!=0)) $SiteSections->setParent($drop['id'],0);
                                                elseif ((floor($_GET['destination'])==-1) && ($drop['parent']!=-1)) $SiteSections->setParent($drop['id'],-1);
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
                        if (count($adderrors)==0) $adddata = array();
                }

                /* Удаление */
                if ($deletesection['id']>0){
                        if ((!isset($section['settings']['undeletable'])) || ($delepmentmode=='development')) $SiteSections->delete($deletesection['id']);
                }

                ?>
                <div id="content" class="listing">
                        <h1>Разделы сайта</h1>
                        <table class="table-content stat tusers">
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
                        <h1>Неопубликованное</h1>
                        <p>После добавления раздел попадает в «неопубликованное», в этом случае он не отображается на сайте, но можно редактировать его содержимое.<br>Чтобы опубликовать раздел, его необходимо перенести в один из публикуемых разделов. Сделать это можно, перетащив якорь непубликуемого раздела на якорь родительского.</p>
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
                                                <span class="bl"></span>
                                                <span class="bc"><input name="name" maxlength="100" value="<?=htmlspecialchars($adddata['name'])?>" /></span>
                                                <span class="br"></span>
                                        </span>
                                        </td><td>
                                        <label>Путь</label>
                                        <span class="input">
                                                <span class="bl"></span>
                                                <span class="bc"><input name="path" maxlength="50" value="<?=htmlspecialchars($adddata['path'])?>" /></span>
                                                <span class="br"></span>
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
                                                <span class="bl"></span>
                                                <span class="bc"><input name="title" maxlength="500" value="<?=htmlspecialchars($adddata['title'])?>" /></span>
                                                <span class="br"></span>
                                        </span>
                                        </td><td>
                                        <label>Описание (description)</label>
                                        <span class="input">
                                                <span class="bl"></span>
                                                <span class="bc"><input name="description" maxlength="500" value="<?=htmlspecialchars($adddata['description'])?>" /></span>
                                                <span class="br"></span>
                                        </span>
                                        </td><td>
                                        <label>Ключевые слова (keyword)</label>
                                        <span class="input">
                                                <span class="bl"></span>
                                                <span class="bc"><input name="keywords" maxlength="500" value="<?=htmlspecialchars($adddata['keywords'])?>" /></span>
                                                <span class="br"></span>
                                        </span>
                                        </td></tr></table>
                               <table style="width: 100%; table-layout: fixed;">
                                        <tr><td>
                                        <label>Теги, разделитель - "|"</label>
                                        <span class="input">
                                                <span class="bl"></span>
                                                <span class="bc"><input name="tags" maxlength="100" value="<?=htmlspecialchars($adddata['tags'])?>" /></span>
                                                <span class="br"></span>
                                        </span>
                                        </td></td>
                                        </table>
                                </div>
                               <span class="clear"></span>
                                <div class="place">
                                   <table>
                                       <tr>
                                        <td>
                                <span><label><input type="checkbox" CHECKED name="visible">&nbsp;Отображать на сайте</label></span>
                                        </td>
                                       </tr>
                                   </table>
                                </div>

                                <span class="clear"></span>
                                <div class="place">
                                        <span class="button big" style="float: right;">
                                                <span class="bl"></span>
                                                <span class="bc">Добавить</span>
                                                <span class="br"></span>
                                                <input type="submit" name="sectionadd" value="" />
                                        </span>
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
                        <h1><a href="./">Список разделов</a> &rarr; Редактирование раздела</h1>
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
                                                <span class="bl"></span>
                                                <span class="bc"><input name="name" maxlength="100" value="<?=htmlspecialchars($editdata['name'])?>" /></span>
                                                <span class="br"></span>
                                        </span>
                                        </td>
                                        <?
                                        if ((!isset($editsection['settings']['nopathchange'])) || ($delepmentmode=='development')){
                                        ?>
                                        <td>
                                        <label>Путь</label>
                                        <span class="input">
                                                <span class="bl"></span>
                                                <span class="bc"><input name="path" maxlength="50" value="<?=htmlspecialchars($editdata['path'])?>" /></span>
                                                <span class="br"></span>
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
                                                <span class="bl"></span>
                                                <span class="bc"><input name="title" maxlength="500" value="<?=html_entity_decode(htmlspecialchars($editdata['title']))?>" /></span>
                                                <span class="br"></span>
                                        </span>
                                        </td><td>
                                        <label>Описание (description)</label>
                                        <span class="input">
                                                <span class="bl"></span>
                                                <span class="bc"><input name="description" maxlength="500" value="<?=html_entity_decode(htmlspecialchars($editdata['description']))?>" /></span>
                                                <span class="br"></span>
                                        </span>
                                        </td><td>
                                        <label>Ключевые слова (keyword)</label>
                                        <span class="input">
                                                <span class="bl"></span>
                                                <span class="bc"><input name="keywords" maxlength="500" value="<?=htmlspecialchars($editdata['keywords'])?>" /></span>
                                                <span class="br"></span>
                                        </span>
                                        </td></tr></table>
                                   <table style="width: 100%; table-layout: fixed;">
                                        <tr><td>
                                        <label>Теги, разделитель - "|"</label>
                                        <span class="input">
                                                <span class="bl"></span>
                                                <span class="bc"><input name="tags" maxlength="100" value="<?=htmlspecialchars($editdata['tags'])?>" /></span>
                                                <span class="br"></span>
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

                                       <span><label><input type="checkbox" <?=$checked?> name="visible">&nbsp;Отображать на сайте</label></span>
                                        </td>
                                       </tr>
                                   </table>
                                </div>
                <?
                }
                ?>
                                <span class="clear"></span>
                                <div class="place">
                                        <span class="button big" style="float: right;">
                                                <span class="bl"></span>
                                                <span class="bc">Сохранить изменения</span>
                                                <span class="br"></span>
                                                <input type="submit" name="sectionedit" value="" />
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
        }
        ?>
        <?/*include $_SERVER['DOCUMENT_ROOT']."/inc/footer.php";*/?>
</div>
<a href="#" id="toTop"></a>
</body>
</html>
