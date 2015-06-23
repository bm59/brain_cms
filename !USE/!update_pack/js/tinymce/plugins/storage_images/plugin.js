tinymce.PluginManager.add('storage_images', function(editor) {

	function showDialog() {
		var gridHtml, x, y, win;

		function getParentTd(elm) {
			while (elm) {
				if (elm.nodeName == 'TD') {
					return elm;
				}

				elm = elm.parentNode;
			}
		}

		gridHtml = '<div style="width: 830px; height: 800px; overflow: scroll;"><table class="content_image">';
		//Разделитель |, можно загружать содержимое нескольких папок
		jQuery.get("/inc/editor.php", { action: 'get_files', folder: '/pics/test/'},
		function(data)
		{
			if (data==0)
			{
	                    alert('Ошибка!');
	        }
	        else
	        {

	        data=eval('('+data+')');
			var text=data.result;
			var arr = text.split(/[|]/);
			var add_text='';

              var j=1;
			  for (var i=0,len=arr.length;i<len;i++)
			  if (arr[i]!='')
			  {

			    if (j==1 || (j>3 && (j%5)==1) ) add_text+='<tr>';

			    add_text+='<td style="tex-align: center; vertical-align: middle; border: 1px solid #CCCCCC; padding: 2px; width: 160px; height: 160px;"><img id="img_'+j+'" style="max-width: 150px; max-height: 150px; cursor: pointer;" src="'+arr[i]+'"></td>';

			   	if ((j%5)==0) add_text+='</tr>';
			    j++;

			  }
			  jQuery('.content_image').append(add_text);
			}

		});

		gridHtml += '</table></div>';

		var charMapPanel = {
			type: 'container',
			html: gridHtml,
			onclick: function(e) {
				var target = e.target;
				if (target.nodeName == 'IMG') {
					var elem_id=target.id;
					if (confirm("Вставить изображение?"))
					{
						editor.execCommand('mceInsertContent', false, '<img src="'+jQuery('#'+elem_id).attr('src')+'">');
						win.close();
					}
				}
			},
			onmouseover: function(e) {
//				var td = getParentTd(e.target);

//				if (td) {
//					win.find('#preview').text(td.firstChild.firstChild.data);
//				}
			}
		};

		win = editor.windowManager.open({
			title: "Архив изображений",
			spacing: 10,
			padding: 10,
			items: [
				charMapPanel,
				{
					type: 'label',
					name: 'preview',
					text: ' ',
					style: 'font-size: 40px; text-align: center',
					border: 1,
					minWidth: 100,
					minHeight: 80
				}
			],
			buttons: [
				{text: "Close", onclick: function() {
					win.close();
				}}
			]
		});
	}

	editor.addButton('storage_images', {
		icon: 'storage_images',
		tooltip: 'Архив изображений',
		onclick: showDialog,
		image : '/pics/editor/on.gif'
	});

	editor.addMenuItem('Архив изображений', {
		icon: 'storage_images',
		text: 'Архив изображений',
		onclick: showDialog,
		context: 'insert',
		image : '/pics/editor/on.gif'
	});
});