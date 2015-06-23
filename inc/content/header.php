
<div id="header">
        <a href="/manage/"><!--//<img src="/pics/logo.jpg" class="logor" />//--></a>
        <?
        $user = $SiteVisitor->getOne(sessionGet('visitorID'));
        $group = $VisitorType->getOne($user['type']);
        $href = (floor(configGet('profileID'))==sessionGet('visitorID'))?array():array('<a href="/manage/access/users/profile/">','</a>');
        ?>
        <div class="profile">
	        <div class="avatar"><?=$href[0]?><img src="<?=($user['picture']['path'])?$user['picture']['path']:'/pics/i/empty_user.gif'?>" width="60" height="60" alt="" /><?=$href[1]?></div>
	        <div class="autor">
	                Здравствуйте,<br /><?=$user['secondname'].' '.$user['firstname'].' '.$user['parentname']?>
	                <div>
	                        <?
	                        if (floor(configGet('profileID'))!=sessionGet('visitorID')){
	                        ?>
	                        <a href="/manage/access/users/profile/" class="button">
	                                <span class="bl"></span>
	                                <span class="bc">Профиль</span>
	                                <span class="br"></span>
	                        </a>
	                        <?
	                        }
	                        ?>
	                        <a href="/manage/?userexit=exit" class="button">
	                                <span class="bl"></span>
	                                <span class="bc">Выйти из системы</span>
	                                <span class="br"></span>
	                        </a>
	                </div>
	        </div>
        </div>
</div>
 <div class="clear"></div><div class="headerhr"></div>

 <script>
 	var jquery=jQuery.noConflict();
 	var ctrlMode = false;
 	var altMode = false;
 	var enablectrlMode = true;

 	jquery(document).keydown(function(e)	{if(e.altKey){altMode = true;};});
 	jquery(document).keydown(function(e)	{if(e.ctrlKey){ctrlMode = true;};});
    jquery(document).keyup(function(e)		{ctrlMode = false; altMode = false;});

    jquery(function () {



        var dropMode = false;

		// jQuery UI Draggable
		jquery(".anchordrop").draggable({



			// возвращает товар обратно на свое место после перетаскивания
			revert:true,

			// как только начинается перетаскивание мы делаем прозрачными остальные объекты
			// добавляем класс CSS
			drag:function () {
				if (ctrlMode)
				jquery(this).addClass("active_prec");
				else if (altMode)
				jquery(this).addClass("active_prec_above");
				else jquery(this).addClass("active");

			},

			// удаляем CSS класс после перетаскивания
			stop:function () {
				//$(this).removeClass("active").closest("#product").removeClass("active");
				jquery(this).removeClass("active");
				jquery(this).removeClass("active_prec");
				jquery(this).removeClass("active_prec_above");
			}
		});

        // jQuery Ui Droppable
		jquery(".anchordrop").droppable({

			// CSS класс для корзины, срабатывает в момент начала перетаскивания товара
			activeClass:"dropactive",

			// CSS класс для корзины при появлении товара в области корзины
			hoverClass:"drophover",

			tolerance:"touch",
			over: function( event, ui )
			{
           		if (ctrlMode && ui.draggable.attr('data-parent')!=jquery(this).attr('data-parent'))
           		{
           			jquery(this).attr('class', 'dropactive');
           			enablectrlMode=false;
           		}
           		else enablectrlMode=true;

			},
			drop:function (event, ui)
			{
                if (enablectrlMode)
                {
		                if (ctrlMode)
		                {
		                	if (confirm('Поменять местами разделы?'))
		                	{
		                		window.location.href = './?drop='+parseInt(ui.draggable.attr('id'))+'&destination='+parseInt(jquery(this).attr('id'))+'&change_prec=true';
		                   	 	ui.draggable.hide('slow');
		                    }
		                }
		                if (altMode)
		                {
		                	if (confirm('Разместить раздел над текущим?'))
		                	{
		                		window.location.href = './?drop='+parseInt(ui.draggable.attr('id'))+'&destination='+parseInt(jquery(this).attr('id'))+'&change_prec_above=true';
		                   	 	ui.draggable.hide('slow');
		                    }
		                }
		                else
		                {
		                	if (confirm('Перенести раздел?'))
		                	{		                		window.location.href = './?drop='+parseInt(ui.draggable.attr('id'))+'&destination='+parseInt(jquery(this).attr('id'));
		                    	ui.draggable.hide('slow');
		               		}
		                }
		        }



           		//alert(jquery(this).attr('id') + ui.draggable.attr('id'));
           		//alert(parseInt(jquery(this).attr('id'))+'|'+parseInt(ui.draggable.attr('id')));
			}
		});

    });
</script>
