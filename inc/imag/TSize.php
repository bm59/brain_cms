<?
include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/include.php");
class TSize
{
	public $prefix='size';
	public $add_title='Укажите размеры товара:';
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
							'comment'=>'VARCHAR(255)',
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
					
		
					if ($num>0 && ($curent['precedence']!=$prec || $curent['name']!=$v || $curent['comment']!=$_POST[$this->prefix.'_comment_'.$num]) )
					{
						msq("UPDATE `site_site_goods_".$this->prefix."` SET 
								`precedence`='$prec', 
								`name`='$v', 
								`comment`='".$_POST[$this->prefix.'_comment_'.$num]."' 
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
						(`good_id`, `precedence`, `name`, `comment`)
						VALUES ('".$_SESSION['template_pub']['id']."', '".$prec."', '".$v."', '".$_POST[$this->prefix.'_new_comment_'.$num]."')");
						
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
		
			
			function add_<?=$this->prefix?>(name, comment) {
				var cur_count=$('.<?=$this->prefix?>_table tr').has('[name^=<?=$this->prefix ?>_new]').length;
				cur_count=parseInt(cur_count)+1;

				$.ajax({
		            type: "POST",
		            url: "/inc/imag/<?=get_class()?>.php",
		            data: "action=gettr&new_cnt="+cur_count+"&name="+name+"&comment="+comment,
		            success: function(html){
		            	$('.<?=$this->prefix?>_table > tbody:last').append(html);
		            	update_<?=$this->prefix?>_template_active ();
		            }
		        });

				
				
			}

			//Отмечаем уже подключенные шаблоны, делаем их не активными
			function update_<?=$this->prefix?>_template_active(){

				$('.<?=$this->prefix?>_template a').each(function() {
					if ($('.<?=$this->prefix?>_table input[value="'+$(this).text()+'"]').length>0)
					$(this).addClass('noact');
					else
					$(this).removeClass('noact');
				});
			}

			//Добавить строку
			$(".add_<?=$this->prefix?>").click(function() {
				add_<?=$this->prefix?>('','');
		
			});

			//Добавлить строку из шаблона
		    $(".<?=$this->prefix?>_template a").not('.noact').click(function() {
		    	if(!$(this).hasClass('noact')) { 
		    		add_<?=$this->prefix?>($(this).text(),$(this).prop('title'));
		    	}
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
					<th>Наименование (пример: "XL" или "42")</th>
					<th>Комментарий (пример: XL - "50 российский размер")</th>
					<th style="width: 10%;">Кол-во</th>
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
		
		if ($new_cnt>0)
		{
			$new_prefix='_new';
			$item['id']=$new_cnt;
			$item['name']=$_POST['name'];
			$item['comment']=$_POST['comment'];
		}
		
		?>
		<tr>
			<td><div class="drag_icon"><img src="/pics/editor/up_down.png"></div></td>
			<td>
				<span class="input">
					<input type="text" value="<?=$item['name'] ?>" maxlength="255" name="<?=$this->prefix?><?=$new_prefix ?>_<?=$item['id'] ?>">
				</span>
			</td>
			<td>
				<span class="input">
					<input type="text" value="<?=$item['comment'] ?>" maxlength="255" name="<?=$this->prefix?><?=$new_prefix ?>_comment_<?=$item['id'] ?>">
				</span>
			</td>
			<td></td>
			<td>
				<?
				if (!$item['kol']>0)
				{
					?><span class="button txtstyle delete_<?=$this->prefix?> act"><a title="Удалить" onclick="return false;" href="#" id="<?=$item['id'] ?>"><img alt="Удалить" src="/pics/editor/delete.gif"></a></span><?	
				}
				?>
			</td>
		</tr>
		<?	
	}
	function GetTemplates ()
	{
		global $SiteSections;
		
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
						?><li><a href="#" onclick="return false;" title="<?=$child['description'] ?>"><?=$child['name'] ?></a></li><?
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
		
		.<?=$this->prefix ?>_table .button.txtstyle {float: none;} 
		.<?=$this->prefix ?>_select {border: 2px solid #CCCCCC; margin-top: 20px; padding: 20px;}
		.<?=$this->prefix ?>_template ul {padding: 10px 0 20px 0;}
		.<?=$this->prefix ?>_template ul li {float: left; list-style: none; background: none; padding: 0; margin: 0;}
		.<?=$this->prefix ?>_template ul li a {
			padding: 10px 15px; border: 1px solid #CCCCCC;
			margin-right: 5px;
			text-decoration: none;
		}
		.<?=$this->prefix ?>_template ul li .noact {background-color: #CCCCCC;}
		
		.button.add_<?=$this->prefix ?> {float: right; margin: 15px 0 0 0; display: inline-block;}
		
		</style>
		<?	
	}
	
}

$tsize=new TSize($pub);

?>