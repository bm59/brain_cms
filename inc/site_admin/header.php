
<div id="header">
        <a href="/manage/"><!--//<img src="/pics/logo.jpg" class="logor" />//--></a>
        <?
        $user = $SiteVisitor->getOne($_SESSION['visitorID']);
        $group = $VisitorType->getOne($user['type']);
        $href = (floor(configGet('profileID'))==$_SESSION['visitorID'])?array():array('<a href="/manage/access/users/profile/">','</a>');
        ?>
        <div class="profile">
	        <div class="avatar"><?=$href[0]?><img src="<?=($user['picture']['path'])?$user['picture']['path']:'/pics/i/empty_user.gif'?>" width="60" height="60" alt="" /><?=$href[1]?></div>
	        <div class="autor">
	                ������������,<br /><?=$user['secondname'].' '.$user['firstname'].' '.$user['parentname']?>
	                <div>
	                        <?
	                        if (floor(configGet('profileID'))!=$_SESSION['visitorID']){
	                        ?>
	                        <a href="/manage/access/users/profile/" class="button">
	                                <span class="bl"></span>
	                                <span class="bc">�������</span>
	                                <span class="br"></span>
	                        </a>
	                        <?
	                        }
	                        ?>
	                        <a href="/manage/?userexit=exit" class="button">
	                                <span class="bl"></span>
	                                <span class="bc">����� �� �������</span>
	                                <span class="br"></span>
	                        </a>
	                </div>
	        </div>
        </div>
</div>
 <div class="clear"></div><div class="headerhr"></div>

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



			// ���������� ����� ������� �� ���� ����� ����� ��������������
			revert:true,

			// ��� ������ ���������� �������������� �� ������ ����������� ��������� �������
			// ��������� ����� CSS
			drag:function () {
				if (ctrlMode)
				$(this).addClass("active_prec");
				else if (altMode)
				$(this).addClass("active_prec_above");
				else $(this).addClass("active");

			},

			// ������� CSS ����� ����� ��������������
			stop:function () {
				//$(this).removeClass("active").closest("#product").removeClass("active");
				$(this).removeClass("active");
				$(this).removeClass("active_prec");
				$(this).removeClass("active_prec_above");
			}
		});

        // $ Ui Droppable
		$(".anchordrop").droppable({

			// CSS ����� ��� �������, ����������� � ������ ������ �������������� ������
			activeClass:"dropactive",

			// CSS ����� ��� ������� ��� ��������� ������ � ������� �������
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


			drop:function (event, ui)
			{
                if (enablectrlMode)
                {
		                if (ctrlMode)
		                {
		                	if (confirm('�������� ������� �������?'))
		                	{
		                		window.location.href = './?drop='+parseInt(ui.draggable.attr('id'))+'&destination='+parseInt($(this).attr('id'))+'&change_prec=true';
		                   	 	ui.draggable.hide('slow');
		                    }
		                }
		                if (altMode)
		                {
		                	if (confirm('���������� ������ ��� �������?'))
		                	{
		                		window.location.href = './?drop='+parseInt(ui.draggable.attr('id'))+'&destination='+parseInt($(this).attr('id'))+'&change_prec_above=true';
		                   	 	ui.draggable.hide('slow');
		                    }
		                }
		                else
		                {
		                	if (confirm('��������� ������?'))
		                	{
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