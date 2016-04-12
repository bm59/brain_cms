<div class="hr"></div>
<?
if (configGet("AskUrl")!='/'){

        if (!$_GET['section']>0 && !$_GET['edit']>0)
        $activeccid = $SiteSections->getIdByPath(preg_replace('/manage/', '', configGet("AskUrl")));
        else
        {
        	$s_id=(($_GET['section']>0) ? $_GET['section'] : $_GET['edit']);
        	$activeccid=$s_id;
        }

        $plist = $SiteSections->getParentsList($activeccid);
        print '<H1><a href="/manage/">Панель управления</a>';
        foreach ($plist as $p){
                if ($p!=$activeccid){
                        $pobj = $SiteSections->get($p);
                        $href = !preg_match('|^'.$SiteSections->getPath($pobj['id']).'$|',configGet("AskUrl"));
                        $sctn = $SiteSections->get($pobj['id']); $sctn['id'] = floor($sctn['id']);
                        if ($sctn['id']>0){
                                $ptrn = new $sctn['pattern'];
                                $ifface = $ptrn->init(array('section'=>$sctn['id']));
                                if ($ptrn->getSetting('name')=='PFolder'){
                                        if ($sctn['path']=='control' || $sctn['path']=='access' || $sctn['path']=='sitecontent')
                                        $href = false;
                                }
                        }

                        if (!$s_id>0)
                        print ($href)?' &rarr; <a href="'.$SiteSections->getPath($pobj['id']).'">'.$pobj['name'].'</a>':' &rarr; '.$pobj['name'];
                        else
                        {
                         	if ($ptrn->getSetting('name')=='PFolder')
                         	print ($href)?' &rarr; <a href="/manage/control/contents/?edit='.$sctn['id'].'">'.$pobj['name'].'</a>':' &rarr; '.$pobj['name'];
                         	else
                         	print ($href)?' &rarr; <a href="/manage/control/contents/?section='.$sctn['id'].'">'.$pobj['name'].'</a>':' &rarr; '.$pobj['name'];
                        }
                }
        }
        $pobj = $SiteSections->get($activeccid);
        $href = !preg_match('|^'.$SiteSections->getPath($pobj['id']).'$|',configGet("AskUrl"));
        $sctn = $SiteSections->get($pobj['id']); $sctn['id'] = floor($sctn['id']);
        if ($sctn['id']>0){
                $ptrn = new $sctn['pattern'];
                $ifface = $ptrn->init(array('section'=>$sctn['id']));
                switch($ptrn->getSetting('name')){

                        case 'PFolder':
                                print ' &rarr; '.$pobj['name'];
                                break;
/*                        case 'PPublication':
                                print ($pub['id']>0)?' &rarr; <a href="'.$SiteSections->getPath($pobj['id']).'">'.$pobj['name'].'</a>':' &rarr; '.$pobj['name'];
                                break;
                        case 'PList':
                                print ($pub['id']>0)?' &rarr; <a href="'.$SiteSections->getPath($pobj['id']).'">'.$pobj['name'].'</a>':' &rarr; '.$pobj['name'];
                                break;
                        case 'PSheet1':
                                print ($pub['id']>0)?' &rarr; <a href="'.$SiteSections->getPath($pobj['id']).'">'.$pobj['name'].'</a>':' &rarr; '.$pobj['name'];
                                break;*/
                        default:
                                print ($href)?'&rarr; <a href="/manage/control/contents/?section='.$sctn['id'].'">'.$pobj['name'].'</a>':' &rarr; '.$pobj['name'];
                                break;
                }
        }

        if ($_GET['pub']>0 || $_GET['id']>0) print '&rarr; редактировать';
        if ($_GET['pub']=='new') print '&rarr; добавить';

        print '</H1>';

}
?>
<br/>