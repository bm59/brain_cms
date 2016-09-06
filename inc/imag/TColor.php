<?
include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/include.php");
class TColor
{
	public $prefix='color';
	public $add_title='Укажите цвет:';
	public $pub='';
	function __construct($pub) {
		
		
		/* Добавление новой строки */
		if ($_POST['action']=='gettr')										$this->GenTr('', $_POST['new_cnt']);
		
		/* Удаление записи ajax*/
		if ($_POST['action']=='delete')										$this->Delete();
		
		/* Сохранение записи */
		if ($_SESSION['template_action']==$this->prefix.'_save')			$this->Save();
		
		
		if ($_SESSION['template_action']==$this->prefix.'_edit')			$this->DrawEdit($pub);
			
		$_SESSION['template_action']='';
	}
	function Save()
	{	
		
		if ($_POST['is_'.$this->prefix]=='on')
		{
			
			create_table('site_site_goods_'.$this->prefix, array
					(
							'good_id'=>'BIGINT(20)',
							'kol'=>'BIGINT(20)',
							'name'=>'VARCHAR(255)',
							'color_code'=>'VARCHAR(255)',
							'color_image'=>'BIGINT(20)',
							'attach_image'=>'BIGINT(20)',
							'precedence'=>'BIGINT(20)'
					)
					);
		
			$prec=0;
			foreach ($_POST as $k=>$v)
			{
				/* Изменение существующих*/
				if (preg_match('|^'.$this->prefix.'_[0-9]+$|',$k) && $v!='')
				{
		
					$num=preg_replace('|^'.$this->prefix.'_([[0-9]+)$|','\\1',$k);
		
					$curent=msr(msq("SELECT * FROM `site_site_goods_".$this->prefix."` WHERE `id`='".$num."'"));
					
		
					if ($num>0)
					{
						msq("UPDATE `site_site_goods_".$this->prefix."` SET 
								`precedence`='$prec', 
								`name`='$v', 
								`color_code`='".$_POST[$this->prefix.'_code_'.$num]."',
								`color_image`='".$_POST[$this->prefix.'_image_'.$num]."',
								`attach_image`='".$_POST[$this->prefix.'_attach_image_'.$num]."'
								WHERE `id`=$num");
						
					}
					$prec++;
				}
		
		
				/* Добавление новых */
				if (preg_match('|^'.$this->prefix.'_new_[0-9]+$|',$k) && $v!='')
				{	
					$num=preg_replace('|^'.$this->prefix.'_new_([[0-9]+)$|','\\1',$k);
						
					$double=msr(msq("SELECT * FROM `site_site_goods_".$this->prefix."` WHERE `good_id`='".$_SESSION['template_pub']."' and `name`='".$v."'"));
						
					if (!$double['id']>0)
					{
						msq("INSERT INTO `site_site_goods_".$this->prefix."`
						(`good_id`, `precedence`, `name`, `color_code`, `color_image`)
						VALUES ('".$_SESSION['template_pub']['id']."', '".$prec."', '".$v."', '".$_POST[$this->prefix.'_new_code_'.$num]."', '".$_POST[$this->prefix.'_new_image_'.$num]."')");
						
						$prec++;
					}
						
				}
		
		
			}
		}
		
	}
	function Delete()
	{
		
		if ($_POST['id']>0)
		{
			msq("DELETE FROM `site_site_goods_".$this->prefix."` WHERE `id`=".$_POST['id']);
			print json_encode(array('ok'=>'ok'));
		}
		
	}
	function js()
	{
		?>
		<script type="text/javascript">
		$(function() {
		
			var session_id = '<?php echo session_id(); ?>';
			
			update_<?=$this->prefix?>_template_active();
		
		    $( "#sortable_<?=$this->prefix?>" ).sortable({
		        connectWith: ".connectedSortable",
		        handle: ".drag_icon"
		      });
		
			
			function add_<?=$this->prefix?>(name, color_code, color_image) {
				var cur_count=$('.<?=$this->prefix?>_table tr').has('[name^=<?=$this->prefix ?>_new]').length;
				cur_count=parseInt(cur_count)+1;


				$.ajax({
		            type: "POST",
		            url: "/inc/imag/<?=get_class()?>.php",
		            data: "action=gettr&new_cnt="+cur_count+"&name="+name+"&color_code="+color_code+"&color_image="+color_image,
		            success: function(html){
		            	$('.<?=$this->prefix?>_table > tbody:last').append(html);
		            	update_<?=$this->prefix?>_template_active ();
		            	update_<?=$this->prefix?>_empty_alert();
		            }
		        });

				
				
			}


			//Отмечаем уже подключенные шаблоны, делаем их не активными
			function update_<?=$this->prefix?>_template_active(){

				$('.<?=$this->prefix?>_template li').each(function() {

					if ($(this).find('[name=color_code]').val()!='')
					if ($('.<?=$this->prefix?>_table input[value="'+$(this).find('[name=color_code]').val()+'"]').length>0) 
					$(this).addClass('noact');

					if ($(this).find('[name=color_image_path]').val()!='')
					if ($('.<?=$this->prefix?>_table [src="'+$(this).find('[name=color_image_path]').val()+'"]').length>0) 
					$(this).addClass('noact');

				});

				if (!$('.color_template ul li').not('.noact').length>0)
				$('.color_template').hide();
			}

			//Добавить строку
			$(".add_<?=$this->prefix?>").click(function() {
				add_<?=$this->prefix?>('','','','');
		
			});

			//Добавить строку из шаблона
		    $(".<?=$this->prefix?>_template a").not('.noact').click(function() {
		    	if(!$(this).hasClass('noact')) { 
		    		add_<?=$this->prefix?>(
				    		$(this).parents('li').find('.name').html(), 
				    		$(this).parents('li').find('[name=color_code]').val(), 
				    		$(this).parents('li').find('[name=color_image]').val()
				    );
		    	}
			});

		  	//Подсвечивать пустые названия
		    function update_<?=$this->prefix?>_empty_alert()
			{
				$('.<?=$this->prefix?>_name').each(function() {
					if ($(this).val()!='')
					$(this).css('border','2px solid rgba(220, 220, 220, 1)');
					else
					$(this).css('border','2px solid #CC0000');	
				});
			}

			//При изменении названия
			$(document).on('keyup', ".<?=$this->prefix?>_name", function (e) { 
				update_<?=$this->prefix?>_empty_alert();
			});

			//Удалить
			$(document).on('click', ".delete_<?=$this->prefix?>.act a", function (e) { 
		
				var parent=$(this).parents('tr');
				if ($(this).attr('id')>0)
				{
					if (confirm('Вы действительно хотите удалить безвозвратно эту запись? Удаление записи происходит без кнопки "Сохранить изменения"'))
					$.ajax({
			            type: "POST",
			            url: "/inc/imag/<?=get_class()?>.php",
			            data: "action=delete&id="+$(this).attr('id'),
			            success: function(html){
			            	parent.remove(); 
			            	update_<?=$this->prefix?>_template_active();
			            }
			        });

				}
				else
				{
					$(this).parents('tr').remove(); 
					update_<?=$this->prefix?>_template_active();
				}
			});
		
			//Реакция на переключатель да\нет основного шаблона
			$('input[name="is_<?=$this->prefix?>"]').change(function() {
				if ($(this).prop("checked")) $('.<?=$this->prefix?>_select').show();
		    	else $('.<?=$this->prefix?>_select').hide(); 	
		    });

			//При изменении кода цвета, отображаем превью
			$(document).on('keyup', ".color_code", function (e) { 
				if ($(this).val().length>0 && ($(this).val().length!=7 || $(this).val()[0]!='#'))
				{
					$(this).css('border','2px solid #CC0000');
					$(this).parents('tr').find('.color_prev').html('<div></div>');
					$(this).parents('tr').find('.upload_status').html('');
					$(this).parents('tr').find("input[name^=color_image]").val('');
				}
				else
				{
					$(this).css('border','2px solid rgba(220, 220, 220, 1)');
					$(this).parents('tr').find('.color_prev').html('<div class="by_code"></div>');
					$(this).parents('tr').find('.color_prev div').css('background-color', $(this).val());
				}
			});

		    //Привязка цвета к картинке
			$(document).on('click', ".attach_<?=$this->prefix?>", function (e) { 
				
				var parent_td=$(this).parents('td');
				var parent_tr=$(this).parents('tr');
				
				var ul_content='';

				var gallery_count=$('.gallery_container img.contentimg').length;

				if (!gallery_count>0)
				alert('Для привязки изображений небходимо сперва загрузить изображения в галерею');
				else
				{

					$('.gallery_container img.contentimg').each(function() {
						ul_content=ul_content+'<li><a href="#" data-id="'+$(this).parents('li').attr('id')+'" onclick="return false;"><img src="'+$(this).attr('src')+'"></a></li>';
					});

					parent_td.find('.choose_image').remove();
					parent_td.append('<div class="choose_image"><ul>'+ul_content+'</ul></div>');


					if (parent_tr.find('.attach_image').val()>0)
					parent_tr.find('[data-id='+parent_tr.find('.attach_image').val()+']').addClass('active');

				}
			});

			//Привязка - клик по картинке
			$(document).on('click', ".choose_image a", function (e) { 
				if ($(this).attr('class')=='active')
				{
					$(this).parents('tr').find('.attach_image').val('');
					$(this).parents('ul').find('a').removeClass('active');
				}
				else
				{
					$(this).parents('ul').find('a').removeClass('active');
					$(this).addClass('active');
					$(this).parents('tr').find('.attach_image').val($(this).data('id'));
				}

				

				
			});



		});
		</script>
		<?	
	}
	function DrawEdit($pub)
	{
		global $SiteSections;
		$this->css();
		$this->js();
		?>
		<div class="clear"></div>
		<div class="<?=$this->prefix?>_select" style="display: <?=$pub['is_'.$this->prefix]==1 ? 'block':'none'?>">
			<h2><?$this->add_title?></h2>
			
			<table style="width: 100%;" class="table-content stat <?=$this->prefix?>_table">
			<tbody id="sortable_<?=$this->prefix?>" class="connectedSortable">
				<tr>
					<th></th>
					<th></th>
					<th>Название</th>
					<th>Цвет</th>
					<th class="min_width"></th>
				</tr>
				<?
				if ($pub['id']>0)
				{
					$items=msq("SELECT * FROM `site_site_goods_".$this->prefix."` WHERE `good_id`='".$pub['id']."' ORDER BY `precedence`");
					while ($item=msr($items))
					{
						$this->GenTr($item);
					}
				}
				?>
			</tbody>
			</table>
			<a class="button add_<?=$this->prefix ?>" href="#" onclick="return false;"><img src="/pics/editor/plus_white.png">Добавить строку</a>
			<?
			$this->GetTemplates();
			?>	
		</div>
		<?
	}
	function GenTr($item, $new_cnt='') 
	{
		global $Storage;
		if ($new_cnt>0)
		{
			$new_prefix='_new';
			$item['id']=$new_cnt;
			$item['name']=$_POST['name'];
			$item['comment']=$_POST['comment'];
		}
		
		if ($_POST['name']!='') 		$item['name']=$_POST['name'];
		if ($_POST['color_code']!='') 	$item['color_code']=$_POST['color_code'];
		if ($_POST['color_image']>0) 	$item['color_image']=$_POST['color_image'];
		?>
		<tr>
			<td><div class="drag_icon"><img src="/pics/editor/up_down.png"></div></td>
			<td class="color_prev">
				<?
				$img=array();
				if ($item['color_image']>0) $img=$Storage->getFile($item['color_image']);
						
				if ($img['id']>0)
				print '<div class="by_image"><img src="'.$img['path'].'"/></div>';
				
				if ($item['color_code']!='')
				print '<div style="background-color: '.$item['color_code'].'"></div>';		
				?>
			</td>
			<td>
				<span class="input">
					<input type="text" value="<?=$item['name'] ?>" class="<?=$this->prefix?>_name" maxlength="255" name="<?=$this->prefix?><?=$new_prefix ?>_<?=$item['id'] ?>" placeholder="Название цвета (например: Зеленый)">
				</span>
			</td>
			<td>
				<span class="input">
					<input type="text" class="color_code" value="<?=$item['color_code'] ?>" maxlength="255" name="<?=$this->prefix?><?=$new_prefix ?>_code_<?=$item['id'] ?>" placeholder="Код цвета (например: #339900)">
				</span>
				<div class="clear"></div>
				<div class="left">или загрузите картинку с изображением цвета:</div>
				<div class="clear"></div>
				<a class="button upload_image_<?=$item['id'] ?>" href="#" onclick="return false;"><img src="/pics/editor/camera_white.png">Загрузить картинку</a>
				<div class="clear"></div>
				
				<div class="upload_status">
				<?
				if ($item['color_image']>0)
				{
					$img=$image=$Storage->getFile($item['color_image']);
					?><a href="<?=$img['path'] ?>" target="_blank">Ссылка на изображение</a><?
				}
				$this->GetUploadScript($item, $new_prefix);
				?>
				</div>
				
				<input type="hidden" value="<?=$item['color_image']?>" name="<?=$this->prefix?><?=$new_prefix ?>_image_<?=$item['id'] ?>" class="color_image">
				<input type="hidden" value="<?=$item['attach_image']?>" name="<?=$this->prefix?><?=$new_prefix ?>_attach_image_<?=$item['id'] ?>" class="attach_image">
			</td>
			<td></td>
			<td>
				<?
				if (!$item['kol']>0)
				{
					?><span class="button txtstyle delete_<?=$this->prefix?> act"><a title="Удалить" onclick="return false;" href="#" id="<?=$item['id'] ?>"><img alt="Удалить" src="/pics/editor/delete.gif"></a></span><div class="clear"></div><br/><?	
				}
				?>
				<div class="clear"></div>
				<div><span class="button txtstyle attach_<?=$this->prefix?> act"><a title="Привязать к картинке товара" onclick="return false;" href="#" id="<?=$item['id'] ?>"><img alt="Привязать к картинке товара" src="/pics/editor/link.png"></a></span></div>
				<?
				if ($item['attach_image']>0)
				{
					$img=$Storage->getFile($item['attach_image']);
					?><div class="choose_image"><ul><li><a href="#" class="active" onclick="return false;"><img src="<?=$img['path'] ?>"></a></li></ul></div><?	
				}
				?>
			
			</td>
		</tr>
		<?	
	}
	function GetUploadScript($pub)
	{
		
		?>
				<script type="text/javascript">
					$(function() {

						var btn_upload=$('.upload_image_<?=$new_prefix.$pub['id'] ?>');
					    new AjaxUpload(btn_upload, {
					        action: '/inc/imag/uploader_image.php',
					        name: 'upl_file',
					        responseType: 'json',
					        onSubmit: function(file, ext){
					            this.setData({sid : '<?=session_id()?>', rubric: '<?=$_GET['section_id']?>', uid: '<?=$_SESSION['visitorID']?>'});
					            if (! (ext && /^(jpg|jpeg|png|gif)$/.test(ext))){
				                    alert('Допустимые форматы: jpg, png, gif');
				                    return false;
				                }
					            $(btn_upload).parents('tr').find('.color_image').val('');
					        },
					        onComplete: function(file, response){
						        if (response.result=='ok')
						        {
						        	$(btn_upload).parents('tr').find('.upload_status').html('<a href="'+response.path+'" target="_blank">Ссылка на изображение</a>');
						        	$(btn_upload).parents('tr').find('.color_prev').html('<div class="by_image"><img src="'+response.path+'"/></div>');
						        	$(btn_upload).parents('tr').find('.color_image').val(response.id);
						        	$(btn_upload).parents('tr').find('.color_code').val('');
							    }
					        }
					    });
					});
					</script>
		<?
	}
	function GetTemplates ()
	{
		global $SiteSections, $Storage;
		
		$template_section=$SiteSections->getIdByPath($SiteSections->getPath($_GET['section']).$this->prefix.'_template/');

		if (!$template_section['id']>0)
		print '<h2>Для добавления шаблонов размеров нужно создать дочерний рубрикатор '.$this->prefix.'_template</h2><br/><div class="clear"></div><br/>';
		else
		{
			$template_iface=getIface($SiteSections->getPath($_GET['section']).$this->prefix.'_template/');
			
			?>
			<div class="<?=$this->prefix?>_template">
			<div class="clear"></div>	
			<h1>Шаблоны:</h1>
			<?
				$item_parents=msq("SELECT * FROM `".$template_iface->getSetting('table')."` WHERE `show`=1 and `parent_id`=0 ORDER BY `precedence`");
				while ($parent=msr($item_parents))
				{
					
					?>
					<h3><?=$parent['name'] ?></h3>
					<ul>
					<?	
					$childs=msq("SELECT * FROM `".$template_iface->getSetting('table')."` WHERE `show`=1 and `parent_id`=".$parent['id']." ORDER BY `precedence`");
					while ($child=msr($childs))
					{
						$style=''; $img=$image='';
						if ($child['color_code']!='')
						{
							$style='background-color: '.$child['color_code'];
								
						}
							
						if ($child['color_code']=='' && $child['color_image']>0)
						{
							$img=$image=$Storage->getFile($child['color_image']);
							$img='<img src="'.$img['path'].'"/>';
						}
						
						?>
						<li>
							<div class="name"><?=$child['name'] ?></div>
							
							<a href="#" style="<?=$style ?>" onclick="return false;" title="<?=$child['description'] ?>"><?=$img ?></a>
							<input type="hidden" value="<?=$child['color_code']?>" name="color_code">
							<input type="hidden" value="<?=$child['color_image']?>" name="color_image">
							<input type="hidden" value="<?=$image['path']?>" name="color_image_path"> 
						</li>
						<?
					}
					?>
					</ul>
					<div class="clear"></div>	
					<?
				}
			?>
			</div>
			<?	
		}
	}
	function css ()
	{
		?>
		<style>
		.drag_icon img {display: inline-block;}
		
		.choose_image li {list-style: none; background: none;}
		.choose_image img {width: 100px; margin-bottom: 10px; border: 3px solid #CCCCCC;}	
		.choose_image .active {border: none;}
		.choose_image .active img {border: 3px solid #00BBA6;}	
		
		.<?=$this->prefix ?>_table .button.txtstyle {float: none;} 
		.<?=$this->prefix ?>_prev {text-align: center;}
		.<?=$this->prefix ?>_prev div {display: inline-block; height: 60px; width: 60px;}
		
		.<?=$this->prefix ?>_table .button {float: left;}
		
		.<?=$this->prefix ?>_table .upload_status {text-align:left; padding-left: 5px;}
		
		.<?=$this->prefix ?>_prev div {border: 1px solid #CCCCCC;}
		.<?=$this->prefix ?>_table .by_image {position: relative;  height: 60px; width: 60px;}
		.<?=$this->prefix ?>_table .by_image img {width: 100%; height: 100%;}
		.button.add_<?=$this->prefix ?> {float: right; margin: 15px 0 0 0; display: inline-block;}
		
		.<?=$this->prefix ?>_template ul {padding: 10px 0 20px 0;}
		.color_template ul li {float: left; list-style: none; background: none; padding: 0; margin: 0; text-align: center;}
		.color_template ul li a {
				border: 1px solid #CCCCCC;
				margin: 0 15px;
				text-decoration: none;
				display: inline-block;
				width: 60px;
				height: 60px;
				position: relative;
		}
		.color_template ul li.noact {display: none;}
		.<?=$this->prefix ?>_template ul li a img {width: 100%; height: 100%;}
		.<?=$this->prefix ?>_template ul li .noact {background-color: #CCCCCC;}
		</style>
		<?	
	}
	
}

$tcolor=new TColor($pub);

?>