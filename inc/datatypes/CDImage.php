<?
/*
�����, ����������� ��� ������������
*/
class CDImage extends VirtualType
{
	function init($settings){
		$settings['descr']='��������';
		$settings['help']=array(
				'auto_resize=true|auto_width=187|auto_height=120'=>'�������������� ������� �����������',
				'imgw=245|imgwtype=1|imgh=400|imghtype=1'=>'�������� �� ������ �����������',
				'comment=�����������'=>'�����������',
				'auto_mini=true|auto_mini_width=210|auto_mini_height=220'=>'�������������� �������� ��������', 
				'exts=jpg,gif,jpeg,png'=>'����������',
				'editor_proport=auto'=>'��������� 3:4 ��� auto',
				'editor_imgh=200|editor_imgw=150'=>'���. ������ � ���������',
				'editor_minh=200|editor_minw=100'=>'�������������� � �������',
				'editor_min_more=1'=>'������������ ���� ������� ���������� ������� ������',
				'editor_as_min=1'=>'��������� ����������� �� ��������� ��� ���������',
		
		);
		VirtualType::init($settings);
	}
	function drawEditor($divstyle = '',$span = true){
		global $Storage, $VirtualContent;
		
		$settings = $this->getSetting('settings');
		
		if ($this->getSetting('use_str_settings'))
		$str_settings=$VirtualContent->implode($settings);
		
		$st = $Storage->getStorage($this->getSetting($this->getSetting('name').'storage'));

		if (!floor($st['id'])>0)
		$st = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'��������','exts'=>array('jpg','gif','jpeg', 'png', 'swf')));
		
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
                $('#loading').show();
                this.setData({sid : '<?=session_id()?>', str_settings: '<?=$str_settings?>',theme: '<?=trim($this->getSetting('theme'))?>', rubric: '<?=trim($this->getSetting('rubric'))?>', stid: '<?=$st['id']?>', uid: <?=floor($this->getSetting('uid'))?>,old_id : $('#uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>').val()});
            },
            onComplete: function(file, response){
                status.html('');
                $('#file').html('');
                if(response.result==="ok"){
                    $('#loading<?='_'.$this->getSetting('name')?>').fadeOut(0);
                    $('#delete_container<?='_'.$this->getSetting('name')?>').fadeIn(0);
                    $('#link_container<?='_'.$this->getSetting('name')?>').fadeIn(0);
                    $('.link<?='_'.$this->getSetting('name')?>').attr('href', response.path);
                    $('#uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>').val(response.id);
                    $('#loading').hide();
                    
                    if (response.ext!='swf')
                    {
                    	$('#editor_container<?='_'.$this->getSetting('name')?>').fadeIn(0);
                    	$('.editor<?='_'.$this->getSetting('name')?>').attr('href', '/inc/datatypes/photo_editor/?file='+response.path+'&rubric=<?=$this->getSetting('rubric') ?>&section=<?=$_GET['section']?>&str_settings=<?=$str_settings?>');
                    	status.html('<img <?=(($st['name']!='�������') ? 'style="width: 170px"':'')?> class="contentimg" id="contentimg<?='_'.$this->getSetting('name')?>" src="'+response.path+'">');
                    }
                    else
                    {
                    	status.html('<embed width="100%" height="100%" src="'+response.path+'" allowscriptaccess="always" menu="true" loop="true" play="true" wmode="opaque" quality="best" type="application/x-shockwave-flash">');
                    }
                }
                else{
                    $('#loading').hide();
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
                         	 $('#link_container<?='_'.$this->getSetting('name')?>').fadeOut(0);
                         	 $('#editor_container<?='_'.$this->getSetting('name')?>').fadeOut(0);
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
    	function wait_editor()
    	{
    		/* alert('start_wait'+'change_photo<?='_'.$_GET['section']?><?='_'.$this->getSetting('name')?>');*/
    		var interval = setInterval(function()
    		{

    			
    			if ($.cookie('change_photo<?='_'.$_GET['section']?><?='_'.$this->getSetting('name')?>')=='1' && $.cookie('change_photo<?='_'.$_GET['section']?><?='_'.$this->getSetting('name')?>')!='null')
    			{
    	            
					/* alert('���� ���������'); */
    	            $.cookie('change_photo<?='_'.$_GET['section']?><?='_'.$this->getSetting('name')?>', null, {path: '/'});
    	            
    	            var src=$('#contentimg<?='_'.$this->getSetting('name')?>').attr("src").split("?")[0] + "?" + Math.random();
    	            $('#contentimg<?='_'.$this->getSetting('name')?>').attr('src','');
    	            $('#contentimg<?='_'.$this->getSetting('name')?>').attr('src',src);
    	            
    	            clearInterval(interval);
    			}



    		}, 1000);


    	}

			</script>

			<div class="place" <?=($divstyle!='')?$divstyle:''?>>
				<label><?=htmlspecialchars($this->getSetting('description'))?><?=((isset($settings['important']))?' <span class="important">*</span>':'')?></label>
				<?if ($settings['comment']!=''){?><small><?=$settings['comment']?></small><?}?>

				<input type="hidden" id="uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>" name="<?=htmlspecialchars($this->getSetting('name'))?>" value="<?=$this->getSetting('value')?>">
                <div class="place forimage">
				<span id="upl_button<?='_'.$this->getSetting('name')?>" class="button">
					��������� �����������
				</span>
				<img src="/pics/inputs/loading2.gif" id="loading" style="position: absolute; top: 0; left: 170px; display: none;">

				<span class="button txtstyle" id="delete_container<?='_'.$this->getSetting('name')?>" <?=(floor($this->getSetting('value'))<1)?'style="display:none;"':''?>>
					<input type="button" title="������� �����������" style="background-image: url(/pics/editor/delete.gif)" id="delete_button<?='_'.$this->getSetting('name')?>" onclick="delete_file<?='_'.$this->getSetting('name')?>(); return false">
				</span>
				<span class="button txtstyle" id="link_container<?='_'.$this->getSetting('name')?>" <?=(floor($this->getSetting('value'))<1)?'style="display:none;"':''?>><a class="link<?='_'.$this->getSetting('name')?>" target="_blank" title="������ �� ��������" href="<?=$image['path'] ?>"><img alt="������ �� ��������" src="/pics/editor/link.png" style="margin-top: -4px;"></a></span>
				<?if ($image['ext']!='swf'){?>
				<span class="button txtstyle" id="editor_container<?='_'.$this->getSetting('name')?>" <?=(floor($this->getSetting('value'))<1)?'style="display:none;"':''?>><a class="editor<?='_'.$this->getSetting('name')?>" target="_blank" title="�������� ����" href="/inc/datatypes/photo_editor/?file=<?=$image['path'] ?>&rubric=<?=$this->getSetting('rubric') ?>&section=<?=$_GET['section'] ?>&str_settings=<?=urlencode($str_settings) ?>" onclick="wait_editor();"><img alt="�������� ����" src="/pics/editor/photo_editor.png" style="margin-top: -4px;"></a></span>
				<?}?>
				</div>
               <span class="clear"></span>
				<div id="upl_error<?='_'.$this->getSetting('name')?>"></div>
			    <div id="upl_status<?='_'.$this->getSetting('name')?>"></div>

				<span class="clear"></span>
				<?
					$desc = '';
					$exts = upper(str_replace(',',', ',$settings['exts']));
					if ($exts!='') $desc.= ' ������� '.$exts;
					$wh = '';
					if (floor($settings['imgw'])>0){
						$imgw = floor($settings['imgw']);
						if (floor($settings['imgwtype'])==1) $wh.= '������ ������ ���� ����� '.$imgw.'px';
						if (floor($settings['imgwtype'])==2) $wh.= '������ ������ ���� ������ ��� ����� '.$imgw.'px';
						if (floor($settings['imgwtype'])==3) $wh.= '������ ������ ���� ������ ��� ����� '.$imgw.'px';
					}
					if (floor($settings['imgh'])>0){
						$imgh = floor($settings['imgh']);
						if (floor($settings['imghtype'])==1) $wh.= (($wh=='')?'':', � ').'������ ������ ���� ����� '.$imgh.'px';
						if (floor($settings['imghtype'])==2) $wh.= (($wh=='')?'':', � ').'������ ������ ���� ������ ��� ����� '.$imgh.'px';
						if (floor($settings['imghtype'])==3) $wh.= (($wh=='')?'':', � ').'������ ������ ���� ������ ��� ����� '.$imgh.'px';
					}
					if ($wh!='') $desc.=(($exts!='') ? '. ':'').$wh.'.';

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
				<div class="contentdesc<?='_'.$this->getSetting('name')?>" style="text-align: left;float: left;">
				<?
				if (floor($image['id'])>0){
					$wh = @getimagesize($image['fullpath']);
					/*if ((floor($wh[0])<=600) && (floor($wh[1])<=200))*/
					{
						if ($image['ext']=='swf'){
							$imgw = (floor($settings['imgwtype'])==1)?floor($settings['imgw']).'px':'100%';
							$imgh = (floor($settings['imghtype'])==1)?floor($settings['imgh']).'px':'100%';
							$inner = '<embed width="'.$imgw.'" height="'.$imgh.'" src="'.$image['path'].'" allowscriptaccess="always" menu="true" loop="true" play="true" wmode="opaque" quality="best" type="application/x-shockwave-flash">';
							print $inner;
						}
						else print '
						<img class="contentimg" id="contentimg'.$this->getSetting('name').'" src="'.$image['path'].'" '.(($st['name']!='�������') ? 'style="width: 170px"':'').'/>';

					}

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
			if (!$st['id']>0) $st = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'����������� ����� (�����)','exts'=>array('jpg','gif','jpeg', 'png', 'swf')));
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
							
							/* ��������� ������ �� ��������� � ��������� */
							if ($settings['editor_as_min'])
							{
								
								
								$mini_fname_old=str_replace('.'.$f['ext'], '_mini.'.$f['ext'], $f['path']);
								$mini_fname=str_replace('.'.$nf['ext'], '_mini.'.$nf['ext'], $nf['path']);
								rename($_SERVER['DOCUMENT_ROOT'].$mini_fname_old, $_SERVER['DOCUMENT_ROOT'].$mini_fname);
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
		if (!$st['id']>0) $st = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'����������� ����� (�����)','exts'=>array('jpg','gif','jpeg', 'png', 'swf')));
		$flist = $Storage->getListByUID($st['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')));
		foreach ($flist as $f) $Storage->deleteFile($f['id']);
	} 
}
?>