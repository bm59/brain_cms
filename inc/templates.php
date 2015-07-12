<?
function echo404Error(){ // ����� 404 ������
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
		<title>�������� �� �������. ������ 404</title>
	</head>
<body id="error404">
<div id="content" >
<div class="errlogo"><img src="/pics/logo.jpg" alt=""  /></div>

	<div class="errtxt">
		<h1>�������� �� �������!</h1>
		<p>��������, �� ������� �� ������ �������, � ��� ���.<br />��������, �� ��������, ������� �����, ���� ������ �������� ���� �������.</p>
			<div>������� �� <a href="/">������� ��������</a></div>
			<div>��������� � <a href="javascript:history.go(-1)">���������� ��������</a></div>
	</div>
</div>
</body>
</html>
        <?
        die();
}
/*function getSelectSinonim($name,$values = array(),$selected = '|nokey|',$always = false){ // ���������� ��� ��� ������� ����������� SELECT�
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
$content = strtr($content, "�����Ũ��������������������������",
"��������������������������������");
return strtolower($content);
}

}


	//<span class="bc"><div id="'.$name.'_show_item" onClick="selectSinonimSelect(\''.$name.'\')" onMouseOut="selectSinonimHide(\''.$name.'\')">'.$firstshowval.'</div></span>

        $retval = '
                <input type="hidden" id="'.$name.'_value_item" name="'.$name.'" value="'.$firstval.'">
                <span class="input">
                        <input autocomplete="off" type="text" id="'.$name.'_show_item" class="suggestSelect" onClick="selectSinonimSelect(\''.$name.'\')" onMouseOut="selectSinonimHide(\''.$name.'\')" value="'.$firstshowval.'" />
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
}*/
function getSelectSinonim($name,$values = array(),$selected = '|nokey|',$always = false)
{

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
        $retval ='<div class="input"><select name="'.$name.'" style="'.$stylewidth.'">';
        foreach ($values as $k=>$v){
            $title = ($titles[$k]!='')?' title="'.stripslashes($titles[$k]).'"':'';
            $retval.= '
            <option value="'.$k.'" '.(($k==$selected) ? 'selected="selected"':'').'>'.$v.'</option>';
        }
        $retval.= '
               </select></div>';

		return $retval;
}
function getSelectSinonimSimple($name,$values = array(),$selected = '|nokey|',$titles = array(),$width = ''){ // ���������� ��� ��� ������� ����������� SELECT�
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
        $retval ='<div class="input"><select name="'.$name.'" style="'.$stylewidth.'">';
        foreach ($values as $k=>$v){
            $title = ($titles[$k]!='')?' title="'.stripslashes($titles[$k]).'"':'';
            $retval.= '
            <option value="'.$k.'" '.(($k==$selected) ? 'selected="selected"':'').'>'.$v.'</option>';
        }
        $retval.= '
               </select></div>';

		return $retval;
}
function getSelectSinonimTree($name,$values = array(),$selected = '|nokey|',$always = false){ // ���������� ��� ��� ������� ����������� SELECT�
        if (count($values)==0) return '';
        $firstval = '|nodata|';
        $firstshowval = '';
        foreach ($values as $k=>$v){
                if ($firstval==='|nodata|'){ $firstval = $k; $firstshowval = strip_tags($v); }
                if ($k==$selected){ $firstval = $k; $firstshowval = strip_tags($v); }
        }
        if ($firstval==='|nodata|') $firstval = '';


	if ($firstshowval==='&nbsp') $firstshowval = '';
	setlocale(LC_CTYPE, 'ru_RU.cp1251');


if (!function_exists('toLower')) {
	function toLower($content) {
$content = strtr($content, "�����Ũ��������������������������",
"��������������������������������");
return strtolower($content);
}

}


	//<span class="bc"><div id="'.$name.'_show_item" onClick="selectSinonimSelect(\''.$name.'\')" onMouseOut="selectSinonimHide(\''.$name.'\')">'.$firstshowval.'</div></span>

        $retval = '
                <input type="hidden" id="'.$name.'_value_item" name="'.$name.'" value="'.$firstval.'">
                <span class="input">
                	<input autocomplete="off" type="text" id="'.$name.'_show_item" class="suggestSelect" onClick="selectSinonimSelect(\''.$name.'\')" onMouseOut="selectSinonimHide(\''.$name.'\')" value="'.$firstshowval.'" />
                </span>
                <div class="forselect">
                        <div id="'.$name.'_items_container" class="items" onMouseOut="selectSinonimHide(\''.$name.'\')" onMouseOver="selectSinonimShow(\''.$name.'\')" style="display: none;">';
        foreach ($values as $k=>$v)
        {
        	if (stripos($v, 'select_header')===false)
        	$retval.= '
                <a onclick="selectSinonimSetValueTree(\''.$name.'\',\''.$k.'\',this,\''.strip_tags($v).'\'); return false;" title="'.htmlspecialchars(toLower($v)).'">'.$v.'</a>';
                else
                $retval.=$v;
                }
        $retval.= '
                        </div>
                </div>';
        return $retval;
}
?>