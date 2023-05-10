<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

if (count($this->polls)) {
	foreach($this->polls as $poll) { 
		$total=(int)$poll->p_total_voted;	?>
		<div class="poll_list float-fix">
			<h3 class="poll_title"><a href="<?php echo Router::_("index.php?module=polls&view=poll&psid=".$poll->p_id."&alias=".$poll->p_alias); ?>"><?php echo $poll->p_title; ?></a></h3>
			<p class="poll_date">
				<?php echo Text::_("Vote time")."&nbsp;".Date::fromSQL($poll->p_startdate); ?>
				<?php echo "&nbsp;-&nbsp;".Date::fromSQL($poll->p_enddate); ?>
			</p>
			<p class="poll_total">
				<?php echo Text::_("Total voted")."&nbsp;".(int)$poll->p_total_voted; ?>
			</p>
		</div> 
<?php }
}
?>