<?
/*  msq("CREATE TABLE IF NOT EXISTS `dop_size` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `num` int(11) NOT NULL,
  `good_id` int(11) NOT NULL,
  `size_val` varchar(255) NOT NULL,
  `size_price` bigint(20) NOT NULL,
  `size_descr` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251;");*/


?>
        <script type="text/javascript">
		function add_size ()
		{

			var cur_count=$('#size_table tr').length;
			var tmp_id=9999999-cur_count;

                              	var cell_template='<tr>\n'+
                              	'<td class="dragHandle">&nbsp;</td>\n'+
                              	'<td style="width:25%">\n'+
                              		 	'<label>Размер</label>\n'+
                            				'<span class="input">\n'+
								'<span class="bl"></span>\n'+
								'<span class="bc"><input type="text" value="" maxlength="255" name="size_val'+tmp_id+'"></span>\n'+
								'<span class="br"></span>\n'+
							'</span>\n'+
                              	'</td>\n'+
                              	'<td style="width:20%">\n'+
                              		  	    '<label>Цена</label>\n'+
                            				'<span class="input">\n'+
								'<span class="bl"></span>\n'+
								'<span class="bc"><input type="text" value="" maxlength="255" name="size_price'+tmp_id+'"></span>\n'+
								'<span class="br"></span>\n'+
							'</span>\n'+
                              	'</td>\n'+
                              	'<td>\n'+
                           				'<label>Описание</label>\n'+
                            				'<span class="input">\n'+
								'<span class="bl"></span>\n'+
								'<span class="bc"><input type="text" value="" maxlength="255" name="size_descr'+tmp_id+'"></span>\n'+
								'<span class="br"></span>\n'+
							'</span>\n'+
                           	'</td>\n'+
                              	'<td style="width:4%; padding-top: 15px;">\n'+
                               				'<a onclick="if (confirm(\'Удалить строку\')) $(this).parents(\'TR\').remove(); return false;" class="button txtstyle" href="#">\n'+
                                                  '<span class="bl"></span>\n'+
                                                  '<span class="bc"></span>\n'+
                                                  '<span class="br"></span>\n'+
                                                  '<input type="button" title="Удалить строку" style="background-image: url(/pics/editor/delete.gif)">\n'+
                                                  '</a>\n'+
                           	'</td>\n'+
                              '</tr>';
                            $('#size_table').append(cell_template);

							 $('#size_table').tableDnD({
							        onDrop: function(table, row) {
							        },
							        dragHandle: ".dragHandle"
							    });


							  $("#size_table tr").hover(function() {
							          $(this.cells[0]).addClass('showDragHandle');
							    }, function() {
							          $(this.cells[0]).removeClass('showDragHandle');
							    });

		}


 $(document).ready(function() {

 $('#size_table').tableDnD({
        onDrop: function(table, row) {
        },
        dragHandle: ".dragHandle"
    });

  $("#size_table tr").hover(function() {
          $(this.cells[0]).addClass('showDragHandle');
    }, function() {
          $(this.cells[0]).removeClass('showDragHandle');
    });
});
</script>
                               <div style="padding: 10px 0 20px 20px; margin: 10px 5px; border: 1px dashed #CCC;">
                               <H2>Размеры:</H2>
		                          	<span>
		                                <a class="button txtstyle" href="#" onclick="add_size(); return false;">
		                                      <span class="bl"></span>
		                                      <span class="bc"></span>
		                                      <span class="br"></span>
		                                      <input type="button" title="Добавить" style="background-image: url(/pics/editor/plus.gif)">
		           						</a>
	           						</span>
	           						<span style="line-height: 30px;padding-left: 5px;"><a href="#" onclick="add_size(); return false;">Добавить размер</a></span>

           						    <table style="width: 100%;" id="size_table">
           						    <?
           						    if (!isset($_POST['editformpost']))
           						    {
                                    	$dop_cnt=msq("SELECT * FROM `dop_size` WHERE `good_id`=".$pub['id']);
                                    	$dop_cnt=mysql_num_rows($dop_cnt);
                                    	if ($dop_cnt>0)
                                    	{


                                    		$i=0;
                                    		$qu=msq("SELECT * FROM `dop_size` WHERE `good_id`=".$pub['id']." ORDER BY `prec` ASC");
                                    		while ($qa=msr($qu))
                                    		{
                                    			$_POST['size_val'.$qa['id']]=$qa['size_val'];
                                    			$_POST['size_price'.$qa['id']]=$qa['size_price'];
                                    			$_POST['size_descr'.$qa['id']]=$qa['size_descr'];
                                    			$i++;
                                    		}
                                    	}
           						    }
				           			foreach ($_POST as $k=>$v){
										if (preg_match('|^size_val[0-9]+$|',$k)){

											$p = preg_replace('|^size_val([0-9]+)$|','\\1',$k);
											?>
												<tr id="tr_<?=$p?>">
												<td class="dragHandle">&nbsp;</td>
	                                			<td style="width:20%">
	                                				<label>Размер</label>
	                                 				<span class="input">
														<span class="bl"></span>
														<span class="bc"><input type="text" value="<?=$v?>" maxlength="255" name="size_val<?=$p?>"></span>
														<span class="br"></span>
													</span>
	                                			</td>
	                                			<td style="width:20%">
	                                				<label>Цена</label>
	                                 				<span class="input">
														<span class="bl"></span>
														<span class="bc"><input type="text" value="<?=$_POST['size_price'.$p]?>" maxlength="255" name="size_price<?=$p?>"></span>
														<span class="br"></span>
													</span>
	                                			</td>
	                                			<td>
	                                				<label>Описание</label>
	                                 				<span class="input">
														<span class="bl"></span>
														<span class="bc"><input type="text" value="<?=$_POST['size_descr'.$p]?>" maxlength="255" name="size_descr<?=$p?>"></span>
														<span class="br"></span>
													</span>
	                                			</td>
	                                			<td style="width:4%;padding-top: 15px;">
                                     				<a onclick="if (confirm('Удалить строку')) $(this).parents('TR').remove(); return false;" class="button txtstyle" href="#">
                                                        <span class="bl"></span>
                                                        <span class="bc"></span>
                                                        <span class="br"></span>
                                                        <input type="button" title="Удалить строку" style="background-image: url(/pics/editor/delete.gif)">
                                                        </a>
	                                			</td>
											<?

										}
									}
           						    ?>
           						    </table>

           						</div>






