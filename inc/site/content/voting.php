<?
$voting_iface=getiface('/sitecontent/voting/');
$voting_Section = $SiteSections->get($SiteSections->getIdByPath('/sitecontent/voting/'));



$voting=$voting_iface->get();

if ($voting['id']>0 && count($voting['answers'])>0)
{
	?>
	<script>
	var session_id = '<?php echo session_id(); ?>';
		$(function() {
			$(".voting .button").click(function() {
				var select_id=$('input[name=answer]:checked').attr('id');
				select_id=select_id.split('_');
				select_id=select_id[1];

				if (select_id>0)
				{
					$.ajax({
			            type: "POST",
			            url: "/ajax.php",
			            data: "action=send_vote&page=<?=urlencode(configGet("AskUrl"))?>&pt=<?=date('U')?>&vote_id=<?=$voting['id']?>&answer_id="+select_id+"&session_id="+session_id,
			            dataType: 'json',
			            success: function(data){
			            	$('.voting .content').html(data.result);
			            	if (data.alert!='') alert(data.alert);
					   }
			        });	
				}
			});
			
		});
	</script>
	<div class="voting">
		<div class="question"><?=$voting['name'] ?></div>
		<div class="content">
			<?
			if  (!cookieGet('vid')>0)
			{
			?>
				<div class="answers">
				<?
				foreach ($voting['answers'] as $k=>$v)
				{
					?><div><input id="answer_<?=$v['id']?>" name="answer" type="radio" data-name="<?=$k['text']?>"><label for="answer_<?=$v['id']?>"><span><span></span></span><?=$v['text']?></label></div><?
				}
				?>
				</div>
				<div class="clear"></div>
				<a class="button">����������</a>
			<?
			} 
			else 
			{
				?>
				<div class="vote_result">
				<?
				$show_total=$voting_Section['settings_personal']['show_vote_count'];
				
				$voting_result=$voting_iface->getTotalHtml($voting['id'], $show_total);
				print $voting_result;
				?>
				</div>
				<?
			}
			?>
			</div>
	</div>
	<?
}
?>