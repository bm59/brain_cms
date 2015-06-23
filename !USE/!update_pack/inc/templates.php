<?
function echo404Error(){ // Вывод 404 ошибки
$settings= new SiteSettings;
$settings->init();
$sitetitle=($settings->getOne($settings->getIdByName('sitetitle')));
$sitedescr=($settings->getOne($settings->getIdByName('sitedescr')));
$sitekey=($settings->getOne($settings->getIdByName('sitekey')));
        ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html;charset=windows-1251" />
		<meta http-equiv="content-language" content="ru" />
		<link rel="icon" href="/favicon.ico" type="image/x-icon" />
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
		<link rel="stylesheet" type="text/css" href="/css/content.css" media="all" />
		<link rel="stylesheet" type="text/css" href="/css/main.css" media="all" />
		<title>Страница не найдена. Ошибка 404</title>
	</head>
<body id="error404">
<div id="content" >
<div class="errlogo"><img src="/pics/logo.jpg" alt="<?=ConfigGet('pr_name_rus')?>"  /></div>

	<div class="errtxt">
		<h1>Страница не найдена!</h1>
		<p>Страницы, на которую вы хотите попасть, у нас нет.<br />Возможно, вы ошиблись, набирая адрес, либо данная страница была удалена.</p>
			<div>Перейти на <a href="/">главную страницу</a></div>
			<div>Вернуться к <a href="javascript:history.go(-1)">предыдущей странице</a></div>
	</div>
</div>
</body>
</html>
        <?
        die();
}
function getSelectSinonim($name,$values = array(),$selected = '|nokey|',$always = false){ // Возвращает код для вставки «заменителя SELECT»
        if (count($values)==0) return '';
        //if ((count($values)==1) && (!$always)) foreach ($values as $k=>$v) return '<input type="hidden" name="'.$name.'" value="'.$k.'">';
        $firstval = '|nodata|';
        $firstshowval = '';
        foreach ($values as $k=>$v){
                if ($firstval==='|nodata|'){ $firstval = $k; $firstshowval = $v; }
                if ($k==$selected){ $firstval = $k; $firstshowval = $v; }
        }
        if ($firstval==='|nodata|') $firstval = '';


	if ($firstshowval==='&nbsp') $firstshowval = '';
	setlocale(LC_CTYPE, 'ru_RU.cp1251');


if (!function_exists('toLower')) {
	function toLower($content) {
$content = strtr($content, "АБВГДЕЁЖЗИЙКЛМНОРПСТУФХЦЧШЩЪЬЫЭЮЯ",
"абвгдеёжзийклмнорпстуфхцчшщъьыэюя");
return strtolower($content);
}

}


	//<span class="bc"><div id="'.$name.'_show_item" onClick="selectSinonimSelect(\''.$name.'\')" onMouseOut="selectSinonimHide(\''.$name.'\')">'.$firstshowval.'</div></span>

        $retval = '
                <input type="hidden" id="'.$name.'_value_item" name="'.$name.'" value="'.$firstval.'">
                <span class="input">
                        <span class="bl"></span>
                        <span class="bc"><input autocomplete="off" type="text" id="'.$name.'_show_item" class="suggestSelect" onClick="selectSinonimSelect(\''.$name.'\')" onMouseOut="selectSinonimHide(\''.$name.'\')" value="'.$firstshowval.'" /></span>
                        <span class="br"></span>
                </span>
                <div class="forselect">
                        <div id="'.$name.'_items_container" class="items" onMouseOut="selectSinonimHide(\''.$name.'\')" onMouseOver="selectSinonimShow(\''.$name.'\')" style="display: none;">';
        foreach ($values as $k=>$v)
        {        	$retval.= '
                <a onclick="selectSinonimSetValue(\''.$name.'\',\''.$k.'\',this); return false;" title="'.htmlspecialchars(toLower($v)).'">'.$v.'</a>';
                }
        $retval.= '
                        </div>
                </div>';
        return $retval;
}
function getSelectSinonimSimple($name,$values = array(),$selected = '|nokey|',$titles = array(),$width = ''){ // Возвращает код для вставки «заменителя SELECT»
		if (count($values)==0) return '';
        /*if (count($values)==1) foreach ($values as $k=>$v) return '<input type="hidden" name="'.$name.'" value="'.$k.'">';*/
        $firstval = '|nodata|';
        $firstshowval = '';
        foreach ($values as $k=>$v){
            if ($firstval==='|nodata|'){ $firstval = $k; $firstshowval = $v; }
            if ($k==$selected){ $firstval = $k; $firstshowval = $v; }
        }
        if ($firstval=='|nodata|') $firstval = '';
        if ($width!='') $stylewidth='width:'.$width;
        $retval ='<select name="'.$name.'" style="'.$stylewidth.'">';
        foreach ($values as $k=>$v){
            $title = ($titles[$k]!='')?' title="'.stripslashes($titles[$k]).'"':'';
            $retval.= '
            <option value="'.$k.'">'.$v.'</option>';
        }
        $retval.= '
               </select>';

		return $retval;
}
?>