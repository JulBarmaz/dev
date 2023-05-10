<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

if (is_object($this->poll)) {
	$poll = $this->poll;
	$total=(int)$poll->p_total_voted;	?>
	<div class="poll_list float-fix">
		<h3 class="poll_title"><?php echo $poll->p_title; ?></h3>
		<p class="poll_date">
			<?php echo Text::_("Vote time")."&nbsp;".Date::fromSQL($poll->p_startdate); ?>
			<?php echo "&nbsp;-&nbsp;".Date::fromSQL($poll->p_enddate); ?>
		</p>
		<div class="poll_items">
<?php if (count($this->items)) {
		foreach($this->items as $item) { 
			if($item->pi_poll_id==$poll->p_id) {
				$percentage=0;	if ($total) $percentage=round(100*($item->pi_hits)/$total, 2); ?>
				<p class="vote_label"> <?php echo htmlspecialchars($item->pi_text); ?></p>
				<div class="pollbar">
					<div class="percentage" style="width:<?php echo round((0.8*$percentage),0); ?>%"></div>&nbsp;<?php echo $percentage; ?>%
				</div>
<?php }	}	}?>
		</div>
		<p class="poll_total">
			<?php echo Text::_("Total voted")."&nbsp;".(int)$poll->p_total_voted; ?>
		</p>
	</div> 
<?php } ?>