
        <script type="text/javascript" src="/js/ajaxupload.3.5.js"></script>
        <script type="text/javascript">
		function add_image ()
		{
			var cur_count=$('#image_table tr').length;
			var tmp_id=9999999-cur_count;

            var cell_template='<tr id="tr_'+tmp_id+'">\n'+
                           '<td class="dragHandle">&nbsp;</td>\n'+
                           '<td style="width:15%">\n'+
                              		 	'<div>Выберите картинку:</div>\n'+
                              		 	'<div id="upl_button_photo_'+tmp_id+'" class="finger_button">\n'+
                              		 	'<img src="/pics/add_photo.png" style="height: 45px;">\n'+
                              		 	'</div>\n'+
							'</span>\n'+
                           '</td>\n'+
                           '<td style="width:20%">\n'+
                           				'<label>Название</label>\n'+
                            				'<span class="input">\n'+
								'<span class="bl"></span>\n'+
								'<span class="bc"><input type="text" value="" maxlength="255" name="image_name'+tmp_id+'"></span>\n'+
								'<span class="br"></span>\n'+
							'</span>\n'+
                           	'</td>\n'+
                            '<td>\n'+
                           				'<label>Описание</label>\n'+
                            				'<span class="input">\n'+
								'<span class="bl"></span>\n'+
								'<span class="bc"><input type="text" value="" maxlength="255" name="image_descr'+tmp_id+'"></span>\n'+
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

                                  //if (cur_count>0)
                                  //$('.contact_table tr').eq(0).before(cell_template);
                                  //else
                              	$('#image_table').append(cell_template);

						        $('#image_table').tableDnD({
							        onDrop: function(table, row) {
							        },
							        dragHandle: ".dragHandle"
							    });

							  	$("#image_table tr").hover(function() {
							          $(this.cells[0]).addClass('showDragHandle');
							    }, function() {
							          $(this.cells[0]).removeClass('showDragHandle');
							    });


  								         	var btnUploadPhoto=$('#upl_button_photo_'+tmp_id);
								            new AjaxUpload(btnUploadPhoto, {
								            action: '/inc/datatypes/dop/image_upload_ajax.php',
								            name: 'upl_file',
								            onSubmit: function(file, ext){

								                this.setData({sid : '<?=session_id()?>', num: tmp_id});

								                if (! (ext && /^(jpg|png|jpeg)$/.test(ext))){
								                    alert('Допустимые форматы: jpg, png');
								                    return false;
								                }
								                $(btnUploadPhoto).children('IMG').attr('src', '/pics/loading.gif');
								            },
								            onComplete: function(file, response){
								              var arr_resp = response.split("#%#");
								                if(arr_resp[0]==="true"){

								                 $(btnUploadPhoto).parent().html(arr_resp[1]);
													$("#image_table").tableDnD({
													    onDragClass: "myDragClass",
													    onDrop: function(table, row) {},onDragStart: function(table, row) {}
													});
								                }
								                else{
								                    $('#loading_photo').fadeOut(0);
								                    alert(response);
								                }
								            }
								        });

		}


 $(document).ready(function() {

	 $('#image_table').tableDnD({
	        onDrop: function(table, row) {
	        },
	        dragHandle: ".dragHandle"
	    });

	  $("#image_table tr").hover(function() {
	          $(this.cells[0]).addClass('showDragHandle');
	    }, function() {
	          $(this.cells[0]).removeClass('showDragHandle');
	    });
});

</script>


                               <div style="padding: 10px 0 20px 20px; margin: 10px 5px; border: 1px dashed #CCC;">
                               <H2>Цвета:</H2>
		                          	<span>
		                                <a class="button txtstyle" href="#" onclick="add_image(); return false;">
		                                      <span class="bl"></span>
		                                      <span class="bc"></span>
		                                      <span class="br"></span>
		                                      <input type="button" title="Добавить" style="background-image: url(/pics/editor/plus.gif)">
		           						</a>
	           						</span>
	           						<span style="line-height: 30px;padding-left: 5px;"><a href="#" onclick="add_image(); return false;">Добавить цвет</a></span>

           						    <table style="width: 100%;" id="image_table">
           						    <?
           						    if (!isset($_POST['editformpost']))
           						    {
                                    	$dop_cnt=msq("SELECT * FROM `dop_image` WHERE `good_id`=".$pub['id']);
                                    	$dop_cnt=mysql_num_rows($dop_cnt);
                                    	if ($dop_cnt>0)
                                    	{

                                    		$i=0;
                                    		$qu=msq("SELECT * FROM `dop_image` WHERE `good_id`=".$pub['id']." ORDER BY `prec` ASC");
                                    		while ($qa=msr($qu))
                                    		{
                                    			$_POST['image_file'.$qa['id']]=$qa['image_file'];
                                    			$_POST['image_name'.$qa['id']]=$qa['image_name'];
                                    			$_POST['image_descr'.$qa['id']]=$qa['image_descr'];
                                    			$i++;
                                    		}
                                    	}
           						    }

				           			foreach ($_POST as $k=>$v){
										if (preg_match('|^image_file[0-9]+$|',$k)){

											$p = preg_replace('|^image_file([0-9]+)$|','\\1',$k);
											?>
											<tr id="tr_<?=$p?>">
												<td class="dragHandle">&nbsp;</td>
	                                			<td style="width:15%">
              											<img src="/storage/image_ajax/<?=$_POST['image_file'.$p]?>" class="img_prev">
        												<input type="hidden"  value="/storage/image_ajax/<?=$_POST['image_file'.$p]?>" name="image_file<?=$p?>"/>
	                                			</td>
	                                			<td style="width:20%">
	                                				<label>Название<?=$p?></label>
	                                 				<span class="input">
														<span class="bl"></span>
														<span class="bc"><input type="text" value="<?=$_POST['image_name'.$p]?>" maxlength="255" name="image_name<?=$p?>"></span>
														<span class="br"></span>
													</span>
	                                			</td>
	                                			<td>
	                                				<label>Описание</label>
	                                 				<span class="input">
														<span class="bl"></span>
														<span class="bc"><input type="text" value="<?=$_POST['image_descr'.$p]?>" maxlength="255" name="image_descr<?=$p?>"></span>
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

