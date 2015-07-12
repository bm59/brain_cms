<?
/*
Класс, описывающий визуальный редактор
*/
class CDTextEditor extends VirtualType
{
	function drawEditor(){
		global $multiple_editor;
		$settings = $this->getSetting('settings');
		?>
		<div class="place">
		<span class="input">
			<label><?=htmlspecialchars($this->getSetting('description'))?><?=((isset($settings['important']))?' <span class="important">*</span>':'')?></label>
		<?
		$css = ($this->getSetting('css')!='')?$this->getSetting('css'):'contentsite.css?1';
		$class = "mce".$settings['texttype'].htmlspecialchars($this->getSetting('name'))."Editor";
		if (!$multiple_editor)
		{			?><script type="text/javascript" src="/js/tinymce/tinymce.js"></script><?
		}

		if ($settings['texttype']=='full'){
			?>
			<script type="text/javascript">
			tinymce.init({
			selector: "textarea.tiny#<?=htmlspecialchars($this->getSetting('name'))?>",
//				        setup: function(editor) {
//	        editor.addButton('mybutton', {
//	            text: 'Архив изображений',
//	            icon: false,
//	            onclick: function() {
//	                    $('body').append('<p>This is the text in new element.<p>');
//	            }
//	        });
//	    } ,

			    theme: "modern",
			    skin: 'redskin',
			    height: 300,
			    language:"ru",
			    content_css: "/css/default.css?<?=rand();?>",
			    fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
			    theme_advanced_blockformats:"p,h1,h2,h3",
			    extended_valid_elements : "nobr[*]",

			    style_formats: [
			        {title: 'Headers', items: [
			            {title: 'h1', block: 'h1'},
			            {title: 'h2', block: 'h2'},
			            {title: 'h3', block: 'h3'}
			        ]},

			        {title: 'Blocks', items: [
			            {title: 'p', block: 'p'},
			            {title: 'div', block: 'div'},
			            {title: 'pre', block: 'pre'}
			        ]},
			        {title: 'Стилизованная таблица', selector: 'table', classes: 'table-content'},
			        {title: 'Абзац с отступом', block: 'p', styles: {'text-indent': '20px'}}

			    ],

			    plugins: [
			         "advlist autolink link image lists charmap print preview hr anchor pagebreak",
			         "searchreplace visualblocks visualchars insertdatetime media nonbreaking",
			         "table contextmenu directionality emoticons paste textcolor responsivefilemanager",
			         "code nonbreaking searchreplace charmap hr jbimages fullscreen"
			   ],
			   tools: "inserttable",

			   toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect | formatselect | fontsizeselect | mybutton",
			   toolbar2: "table | link unlink anchor | image media responsivefilemanager | jbimages | forecolor backcolor   |  pagebreak nonbreaking hr| searchreplace charmap insertdate | fullscreen | print preview code | storage_images" ,
			   image_advtab: true ,

			   external_filemanager_path:"/filemanager/",
			   filemanager_title:"Responsive Filemanager" ,
			   external_plugins: { "filemanager" : "/filemanager/plugin.min.js"}
			 });
			</script>
		<?
		}
		else
		{     			?>

			<script type="text/javascript">
			tinymce.init({
			selector: "textarea.tiny#<?=htmlspecialchars($this->getSetting('name'))?>",

			    theme: "modern",
			    skin: 'redskin',
			    height: 100,
			    language:"ru",
			    content_css: "/css/default.css?<?=rand();?>",
			    fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
			    theme_advanced_blockformats:"p,h1,h2,h3",
			    extended_valid_elements : "nobr[*]",
			    statusbar : false,
			    menubar : false,
			    paste_as_text: true,

			    style_formats: [],

			    plugins: [
			         "advlist autolink link image lists charmap print preview hr anchor pagebreak",
			         "searchreplace visualblocks visualchars insertdatetime media nonbreaking",
			         "table contextmenu directionality emoticons paste textcolor responsivefilemanager",
			         "code nonbreaking searchreplace charmap hr jbimages fullscreen"
			   ],
			   tools: "inserttable",

			   toolbar1: "undo redo | bold italic underline | ",
			   image_advtab: true ,

			   external_filemanager_path:"/filemanager/",
			   filemanager_title:"Responsive Filemanager" ,
			   external_plugins: { "filemanager" : "/filemanager/plugin.min.js"}
			 });
			</script>
		<?
		}
		?>
			<div><textarea class="tiny" id="<?=htmlspecialchars($this->getSetting('name'))?>" name="<?=htmlspecialchars($this->getSetting('name'))?>" class="<?=$class?>"><?=htmlspecialchars($this->getSetting('value'))?></textarea></div>
		</span>
		</div>
		<input type="hidden" name="<?=htmlspecialchars($this->getSetting('name'))?>_htmlview" id="<?=htmlspecialchars($this->getSetting('name'))?>_htmlview" value="">
		<span class="clear"></span>
		<?
	}
	function preSave(){
		$errors = array();
		$settings = $this->getSetting('settings');
		$newvalue = trim($_POST[$this->getSetting('name')]);
		if ((isset($settings['important'])) && ($newvalue=='')) $errors[] = 'Заполните поле «'.$this->getSetting('description').'»';
		$this->setSetting('value',stripslashes($newvalue));
		return $errors;
	}
	function postSave(){
/*		global $Storage;
		if (floor($this->getSetting('uid'))>0){
			$st = $Storage->getStorage(floor($this->getSetting('filestorage')));
			$imagesintext = $filesintext = $patterns = $replaces = array();
			if (floor($st['id'])>0){
				preg_match_all("|href=\"[\.\.\/]+\.\.".addslashes($st['path'])."([a-z_\.0-9]+)\"|i",$this->getSetting('value'),$files);
				if (is_array($files['1'])){
					foreach ($files['1'] as $file){
						$f = $Storage->getFile($Storage->getIdByName(trim($file)));
						if (floor($f['id'])>0){
							if (substr($f['name'],0,5)=='temp_'){
								if ($Storage->renameFile($f['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')))){
									$nf = $Storage->getFile($f['id']);
									$patterns[] = "|".str_replace('.','\.',$f['name'])."|";
									$replaces[] = $nf['name'];
								}
							}
							$filesintext[] = $f['id'];
						}
					}
				}
				$flist = $Storage->getListByUID($st['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')));
				foreach ($flist as $f) if (!in_array($f['id'],$filesintext)) $Storage->deleteFile($f['id']);
			}
			$st = $Storage->getStorage(floor($this->getSetting('imagestorage')));
			if (floor($st['id'])>0){
				preg_match_all("#[src|href]=\"[\.\.\/]+\.\.".addslashes($st['path'])."([a-z_\.0-9]+)\"#i",$this->getSetting('value'),$files);
				if (is_array($files['1'])){
					foreach ($files['1'] as $file){
						$f = $Storage->getFile($Storage->getIdByName(trim($file)));
						if (floor($f['id'])>0){
							if (substr($f['name'],0,5)=='temp_'){
								if ($Storage->renameFile($f['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')))){
									$nf = $Storage->getFile($f['id']);
									$patterns[] = "|".str_replace('.','\.',$f['name'])."|";
									$replaces[] = $nf['name'];
								}
							}
						}
						$imagesintext[] = $f['id'];
					}
				}
				$flist = $Storage->getListByUID($st['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')));
				foreach ($flist as $f){
					if (!in_array($f['id'],$imagesintext)) $Storage->deleteFile($f['id']);
				}
			}
			$patterns[] = "|[\.\.\/]+\.\.|";
			$replaces[] = '';
			if ((count($patterns)>0) && (count($patterns)==count($replaces))){
				$newvalue = preg_replace($patterns,$replaces,$this->getSetting('value'));
				$this->setSetting('value',$newvalue);
			}
		}*/
	}
	function getValue(){ return $this->getSetting('value'); }
	function getUpdateSQL(){ return "`".$this->getSetting('name')."`='".addslashes($this->getSetting('value'))."'"; }
	function delete(){
		global $Storage;
		$st = $Storage->getStorage(floor($this->getSetting('filestorage')));
		$flist = $Storage->getListByUID($st['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')));
		foreach ($flist as $f) $Storage->deleteFile($f['id']);
		$st = $Storage->getStorage(floor($this->getSetting('imagestorage')));
		$flist = $Storage->getListByUID($st['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')));
		foreach ($flist as $f) $Storage->deleteFile($f['id']);
	}
}
?>