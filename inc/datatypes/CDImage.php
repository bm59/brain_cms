<?
/*
�����, ����������� ��� ������������
*/
class CDImage extends VirtualType
{
	function init($settings){
		VirtualType::init($settings);
	}
	function drawEditor($divstyle = '',$span = true){
		$settings = $this->getSetting('settings');
		global $Storage;
		$st = $Storage->getStorage(floor($this->getSetting($this->getSetting('name').'storage')));
		if (!floor($st['id'])>0)
		$st = $Storage->getStorage(floor($this->getSetting('imagestorage')));
		$f = 0;
		if (floor($st['id'])>0){
			$image = $Storage->getFile($this->getSetting('value'));
			$f = floor($image['id']);
			$this->setSetting('value',$f);
			?>
   			<script>
        $(function(){
        var btnUpload<?='_'.$this->getSetting('name')?>=$('#upl_button<?='_'.$this->getSetting('name')?>');
        var status=$('.contentdesc<?='_'.$this->getSetting('name')?>');
        var upload_me=new AjaxUpload(btnUpload<?='_'.$this->getSetting('name')?>, {
            action: '/uploader_image.php',
            responseType: 'json',
            name: 'upl_file',
            data: {},
            onSubmit: function(file, ext){
                $('#loading').attr('src', '/pics/loading.gif').fadeIn(0);
                this.setData({sid : '<?=session_id()?>', theme: '<?=trim($this->getSetting('theme'))?>', rubric: '<?=trim($this->getSetting('rubric'))?>', stid: '<?=$st['id']?>', uid: <?=floor($this->getSetting('uid'))?>,old_id : $('#uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>').val()});
            },
            onComplete: function(file, response){
                status.html('');
                $('#file').html('');
                if(response.result==="ok"){
                    $('#loading<?='_'.$this->getSetting('name')?>').fadeOut(0);
                    $('#delete_container<?='_'.$this->getSetting('name')?>').fadeIn(0);
                    status.html('<img style="width: 170px" class="contentimg" src="'+response.path+'" class="contentimg">');
                    $('#uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>').val(response.id);
                }else{
                    $('#loading').fadeOut(0);
                    if (response.error!='') alert(response.error);


                }
            }
        });


    });
    	function delete_file<?='_'.$this->getSetting('name')?> ()
        {
       		if (!confirm('�� �������, ��� ������ ������� ���� ����?')) return false;

			    $.ajax({
			        url: "/uploader_image.php",
			        type: "post",
			        data: {action: 'delete',old_id : $('#uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>').val()},
			        success: function(result){
			            var arr_resp = result.split("#%#");
			            if(arr_resp[0]==="true")
			            {
                         	 $('#delete_container<?='_'.$this->getSetting('name')?>').fadeOut(0);
                         	 $('.contentdesc<?='_'.$this->getSetting('name')?>').html('');
			            }
			            else
			            {
			            	alert(result);
			            }
			        }
			    });
			    return false;
        }


			</script>

			<div class="place" <?=($divstyle!='')?$divstyle:''?>>
				<label><?=htmlspecialchars($this->getSetting('description'))?><?=((isset($settings['important']))?' <span class="important">*</span>':'')?></label>
			<?
			/*if ($this->getSetting('name')=='image' && $_GET['section']==7) 	print '<div style="padding: 0 5px; color: #ff0000">������������� ������:  1040x450 ��������</div>';*/
			?>
				<input type="hidden" id="uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>" name="<?=htmlspecialchars($this->getSetting('name'))?>" value="<?=$this->getSetting('value')?>">
                <div class="place forimage">
				<span id="upl_button<?='_'.$this->getSetting('name')?>" class="button">
					��������� �����������
				</span>

				<span class="button txtstyle" id="delete_container<?='_'.$this->getSetting('name')?>" <?=(floor($this->getSetting('value'))<1)?'style="display:none;"':''?>>
					<input type="button" title="������� �����������" style="background-image: url(/pics/editor/delete.gif)" id="delete_button<?='_'.$this->getSetting('name')?>" onclick="delete_file<?='_'.$this->getSetting('name')?>(); return false">
				</span>
				</div>
               <span class="clear"></span>
				<div id="upl_error<?='_'.$this->getSetting('name')?>"></div>
			    <div id="upl_status<?='_'.$this->getSetting('name')?>"></div>

				<span class="clear"></span>
				<?
					$desc = '';
					$exts = upper(str_replace(',',', ',$st['settings']['exts']));
					if ($exts!='') $desc.= ' ������� '.$exts;
					$wh = '';
					if (floor($st['settings']['imgw'])>0){
						$imgw = floor($st['settings']['imgw']);
						if (floor($st['settings']['imgwtype'])==1) $wh.= '������ ������ ���� ����� '.$imgw.'px';
						if (floor($st['settings']['imgwtype'])==2) $wh.= '������ ������ ���� ������ ��� ����� '.$imgw.'px';
						if (floor($st['settings']['imgwtype'])==3) $wh.= '������ ������ ���� ������ ��� ����� '.$imgw.'px';
					}
					if (floor($st['settings']['imgh'])>0){
						$imgh = floor($st['settings']['imgh']);
						if (floor($st['settings']['imghtype'])==1) $wh.= (($wh=='')?'':', � ').'������ ������ ���� ����� '.$imgh.'px';
						if (floor($st['settings']['imghtype'])==2) $wh.= (($wh=='')?'':', � ').'������ ������ ���� ������ ��� ����� '.$imgh.'px';
						if (floor($st['settings']['imghtype'])==3) $wh.= (($wh=='')?'':', � ').'������ ������ ���� ������ ��� ����� '.$imgh.'px';
					}
					if ($wh!='') $wh = ' ����� ����, '.$wh.'.';
					$desc.='.'.$wh;
				?>
				<?
				/*|auto_resize=true|auto_width=187|auto_height=120|*/
				if ($settings['auto_resize'] && ($settings['auto_width']>0 || $settings['auto_height']>0))
				{
					$img_descr='<small>������������� ������ �����������:';
					$img_descr.=(($settings['auto_width']>0) ? ' ������ '.$settings['auto_width'].'��������;' : '');
					$img_descr.=(($settings['auto_height']>0) ? ' ������ '.$settings['auto_height'].'��������;' : '');
					print $img_descr.' ���� ������� ������������ ����������� ������ - ��� ������� ����� ������������� ��������</small>';
				}
				?>

				<div><small><?=$desc?></small></div>
				<div id="<?=htmlspecialchars($this->getSetting('name'))?>imagecontent">
				<div class="contentdesc<?='_'.$this->getSetting('name')?>">
				<?
				if (floor($image['id'])>0){
					$wh = @getimagesize($image['fullpath']);
					/*if ((floor($wh[0])<=600) && (floor($wh[1])<=200))*/
					{
						if ($image['ext']=='swf'){
							$imgw = (floor($st['settings']['imgwtype'])==1)?floor($st['settings']['imgw']):'100%';
							$imgh = (floor($st['settings']['imghtype'])==1)?floor($st['settings']['imgh']):'100%';
							$iinner = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,65,0" width="'.$imgw.'" height="'.$imgh.'">';
							$iinner.= '<param name="allowScriptAccess" value="sameDomain">';
							$iinner.= '<param name="movie" value="'.$image['path'].'">';
							$iinner.= '<param name="quality" value="high">';
							$iinner.= '<param name="salign" value="lt">';
							$iinner.= '<param name="wmode" value="transparent">';
							$iinner.= '<param name="menu" value="false">';
							$iinner.= '<param name="scale" value="noborder">';
							$iinner.= '<embed src="'.$image['path'].'" width="'.$imgw.'" height="'.$imgh.'" quality="high" scale="noborder" salign="lt" wmode="transperent" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" menu="false"></embed>';
							$iinner.= '</object>';
							print $iinner;
						}
						else print '
						<img class="contentimg" src="'.$image['path'].'" style="width: 170px"/>';

						if ((floor($wh[0])>=200) && (floor($wh[1])>=200))
						print '
						<div class="contenttxt"><a href="'.$image['path'].'" target="_blank">������ �� ����</a></div>';
					}

					/*else print '
						<div class="contenttxt"><a href="'.$image['path'].'">������ �� ����</a></div>';*/
				}
				?>
				</div>
				</div>
			</div>
		<?
				if ($span) print '
			<span class="clear"></span>';
		}
	}
	function preSave(){
		$errors = array();
		$settings = $this->getSetting('settings');
		$newvalue = floor($_POST[$this->getSetting('name')]);
		if ((isset($settings['important'])) && ($newvalue<1)) $errors[] = '��������� ���� �'.$this->getSetting('description').'�';
		$this->setSetting('value',$newvalue);
		return $errors;
	}
	function postSave(){
		$settings = $this->getSetting('settings');
		global $Storage;

		if (floor($this->getSetting('uid'))>0){
			$st = $Storage->getStorage(floor($this->getSetting($this->getSetting('name').'storage')));
			if (!$st['id']>0) $st = $Storage->getStorage(floor($this->getSetting('imagestorage')));
			if (floor($st['id'])>0){
				$f = $Storage->getFile($this->getSetting('value'));
				if (floor($f['id'])>0){
					if (substr($f['name'],0,5)=='temp_'){
						if ($Storage->renameFile($f['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')))){
							$nf = $Storage->getFile($f['id']);

                            /*|auto_mini=true|auto_mini_width=210|auto_mini_height=220|*/
			                if ($settings['auto_mini'] && ($settings['auto_mini_width']>0 || $settings['auto_mini_height']>0))
							{
			              				$mini_fname=str_replace('.'.$nf['ext'], '_mini.'.$nf['ext'], $nf['fullpath']);
										copy($nf['fullpath'], $mini_fname);
			       						ResizeFrameMaxSide($mini_fname, 210,220);
			       						/*Crop($mini_fname, 210, 220);*/
							}
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
	function getUpdateSQL(){ return "`".$this->getSetting('name')."`='".floor($this->getSetting('value'))."'"; }
	function delete(){
		global $Storage;
		$st = $Storage->getStorage(floor($this->getSetting($this->getSetting('name').'storage')));
		$flist = $Storage->getListByUID($st['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')));
		foreach ($flist as $f) $Storage->deleteFile($f['id']);
	}
}
?>