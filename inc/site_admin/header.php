
<div id="header">
<?
            if (setting('admin_logo')>0)
            $image=$Storage->getfile(setting('admin_logo'));
            if ($image['path']!='')
            {
            ?><a href="/manage/"><img src="<?=$image['path']?>" class="logor" /></a><?
            }
            else{?><a href="/manage/"><img src="/pics/logo_cms.png" class="logor" /></a><?}

        $user = $SiteVisitor->getOne($_SESSION['visitorID']);
        $group = $VisitorType->getOne($user['type']);
        $href = (floor(configGet('profileID'))==$_SESSION['visitorID'])?array():array('<a href="/manage/access/users/profile/">','</a>');
        ?>
        <div class="profile">
	        <div class="autor">
	                ѕользователь: <?=$user['secondname'].' '.$user['firstname'].' '.$user['parentname']?>
	                <div>
	                        <?
	                        if (floor(configGet('profileID'))!=$_SESSION['visitorID']){
	                        ?>
	                        <a href="/manage/access/users/profile/" class="button"><img src="/pics/editor/profile.png">ѕрофиль</a>
	                        <?
	                        }
	                        ?>
	                        <a href="/manage/?userexit=exit" class="button"><img src="/pics/editor/exit.png">¬ыйти из системы</a>
	                </div>
	        </div>
        </div>
</div>
 <div class="clear"></div>

 <script>
 	var ctrlMode = false;
 	var altMode = false;
 	var enablectrlMode = true;

 	$(document).keydown(function(e)	{if(e.altKey){altMode = true;};});
 	$(document).keydown(function(e)	{if(e.ctrlKey){ctrlMode = true;};});
    $(document).keyup(function(e)		{ctrlMode = false; altMode = false;});

    $(function () {



        var dropMode = false;

		// $ UI Draggable
		$(".anchordrop").draggable({



			// возвращает товар обратно на свое место после перетаскивани€
			revert:true,

			// как только начинаетс€ перетаскивание мы делаем прозрачными остальные объекты
			// добавл€ем класс CSS
			drag:function () {
				if (ctrlMode)
				$(this).addClass("active_prec");
				else if (altMode)
				$(this).addClass("active_prec_above");
				else $(this).addClass("active");

			},

			// удал€ем CSS класс после перетаскивани€
			stop:function () {
				//$(this).removeClass("active").closest("#product").removeClass("active");
				$(this).removeClass("active");
				$(this).removeClass("active_prec");
				$(this).removeClass("active_prec_above");
			}
		});

        // $ Ui Droppable
		$(".anchordrop").droppable({

			// CSS класс дл€ корзины, срабатывает в момент начала перетаскивани€ товара
			activeClass:"dropactive",

			// CSS класс дл€ корзины при по€влении товара в области корзины
			hoverClass:"drophover",

			tolerance:"touch",
			over: function( event, ui )
			{
           		if (ctrlMode && ui.draggable.attr('data-parent')!=$(this).attr('data-parent'))
           		{
           			$(this).attr('class', 'dropactive');
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
		                	if (confirm('ѕомен€ть местами разделы?'))
		                	{
		                		window.location.href = './?drop='+parseInt(ui.draggable.attr('id'))+'&destination='+parseInt($(this).attr('id'))+'&change_prec=true';
		                   	 	ui.draggable.hide('slow');
		                    }
		                }
		                if (altMode)
		                {
		                	if (confirm('–азместить раздел над текущим?'))
		                	{
		                		window.location.href = './?drop='+parseInt(ui.draggable.attr('id'))+'&destination='+parseInt($(this).attr('id'))+'&change_prec_above=true';
		                   	 	ui.draggable.hide('slow');
		                    }
		                }
		                else
		                {
		                	if (confirm('ѕеренести раздел?'))
		                	{		                		window.location.href = './?drop='+parseInt(ui.draggable.attr('id'))+'&destination='+parseInt($(this).attr('id'));
		                    	ui.draggable.hide('slow');
		               		}
		                }
		        }



           		//alert($(this).attr('id') + ui.draggable.attr('id'));
           		//alert(parseInt($(this).attr('id'))+'|'+parseInt(ui.draggable.attr('id')));
			}
		});

    });
</script>
