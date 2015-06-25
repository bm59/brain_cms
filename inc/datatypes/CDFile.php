<?
/*
�����, ����������� ��� ������
*/
class CDFile extends VirtualType
{
	function init($settings){
		VirtualType::init($settings);

	}
	function drawEditor(){
		$settings = $this->getSetting('settings');

		global $Storage;
		$st = $Storage->getStorage(floor($this->getSetting($this->getSetting('name').'_filestorage')));

		$f = 0;
		if (floor($st['id'])>0){
			$exts = upper(str_replace(',',', ',$st['settings']['exts']));
			?>
			<style type="text/css">
#add_file {margin-bottom: 10px; width: 200px; text-align: center;}
#upl_error {color: red}
#upl_button {
 	width: 100%; margin: 5px 0;
 	padding: 4px 0;
 	border-radius: 2px;
 	display: inline-block;
 	color: #FFF;
	border: solid 1px #555;
	background: #6e6e6e;
	background: -webkit-gradient(linear, left top, left bottom, from(#888), to(#575757));
	background: -moz-linear-gradient(top,  #888,  #575757);
	background: -ms-linear-gradient(top,  #888,  #575757);
        background-image: -o-linear-gradient(top,#888,  #575757);
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='#888888', endColorstr='#575757');
 	}
			</style>
			<script>
        $(function(){
        var btnUpload=$('#upl_button');
        var status=$('#upl_status');
        var error=$('#upl_error');
        var old_file='';
        var upload_me=new AjaxUpload(btnUpload, {
            action: '/uploader.php',
            responseType: 'json',
            name: 'upl_file',
            data: {sid : '<?=session_id()?>', storage_path: '<?=$st['path']?>', old_file: '<?=$this->getSetting('value')?>'},
            onSubmit: function(file, ext){
                <?if ($exts!=''){?>
                if (! (ext && /^(<?=strtolower(str_replace(', ', '|', $exts))?>)$/.test(ext))){
                    error.html('<nobr>���������� �������: <?=strtolower($exts)?></nobr>');
                    return false;
                }
                <?}?>
                $('#file').fadeOut(0);
                $('#loading').attr('src', '/pics/loading.gif').fadeIn(0);
            },
            onComplete: function(file, response){
                status.html('');
                error.html('');
                $('#file').html('');
                if(response.result==="ok"){
                    $('#loading').fadeOut(0);
                    /*$('#file').attr('src', '<?=$st['path']?>' + arr_resp[1]).fadeIn(0);*/
                    old_file=response.full_path;
                    upload_me.setData({'old_file': old_file, 'storage_path': '<?=$st['path']?>', sid : '<?=session_id()?>'});
                    status.html('<nobr><a href="'+response.full_path+'" target="_blank">'+response.file+'</a> ('+response.filesize+')</nobr>');
                    $("#<?=$this->getSetting('name')?>").val(response.full_path);
                }else{
                    status.html(response.error);
                    $('#loading').fadeOut(0);

                }
            }
        });
    });
			</script>
			<div class="place">
			<label><?=htmlspecialchars($this->getSetting('description'))?><?=((isset($settings['important']))?' <span class="important">*</span>':'')?></label>
			<input type="hidden" id="uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>" name="<?=htmlspecialchars($this->getSetting('name'))?>" value="<?=$this->getSetting('value')?>">

             	<span class="clear"></span>
				<?
					if ($exts!='') $desc.= ' ���� � ������� '.$exts;
				?>
				<div class="contentdesc"><small><?=$desc?></small></div>


			<div id="add_file" style="float: left;">
			        <div id="upl_button">��������� ����</div><br />
			        <img id="loading" src="/pics/loading.gif" height="28" style="display: none;" />
			        <!--//<img id="file" src="" height="150" style="display: none;" />//-->
			         <div id="upl_error"></div>
			         <div id="upl_status">
			         <?
			         if ($this->getSetting('value')!='')
			         print  '<a href="'.$this->getSetting('value').'" target="_blank">'.basename($this->getSetting('value')).' ('.get_filesize($_SERVER['DOCUMENT_ROOT'].$this->getSetting('value')).')'.'</a>';
			         ?>
			         </div>
			         <input type="hidden" name="<?=$this->getSetting('name')?>" id="<?=$this->getSetting('name')?>" value="<?=stripslashes($this->getSetting('value'))?>">
			    </div>
		    </div>
		<?
		}
	}
	function preSave(){
		$errors = array();
		$settings = $this->getSetting('settings');
		$newvalue = $_POST[$this->getSetting('name')];
		if ((isset($settings['important'])) && (!$newvalue!='')) $errors[] = '��������� ���� �'.$this->getSetting('description').'�';
		$this->setSetting('value',$newvalue);
		return $errors;
	}
	function postSave(){
		global $Storage;
		if (floor($this->getSetting('uid'))>0){
			$st = $Storage->getStorage(floor($this->getSetting($this->getSetting('name').'_filestorage')));
			if (floor($st['id'])>0){
				$f = $Storage->getFile($this->getSetting('value'));
				if (floor($f['id'])>0){
					if (substr($f['name'],0,5)=='temp_'){
						if ($Storage->renameFile($f['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')))){
							$nf = $Storage->getFile($f['id']);
						}
					}
				}
				$flist = $Storage->getListByUID($st['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')));
				foreach ($flist as $f){
					if (!$f['id']==$this->getSetting('value')) $Storage->deleteFile($f['id']);
				}
			}
		}
	}
	function getValue(){ return $this->getSetting('value'); }
	function getUpdateSQL(){ return "`".$this->getSetting('name')."`='".$this->getSetting('value')."'"; }
	function delete(){
		global $Storage;
		$st = $Storage->getStorage(floor($this->getSetting($this->getSetting('name').'_filestorage')));
		$flist = $Storage->getListByUID($st['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')));
		foreach ($flist as $f) $Storage->deleteFile($f['id']);
	}
}
?>