<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_TEMPLATE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="comment_body">
	<?php if ($intro) { ?>
	<div class="comment_text">
		<?php echo Text::_("Comments group")." : ".$this->title." <a href=\"".Router::_("index.php?module=".$this->module."&view=".$this->view."&psid=".$this->obj_id)."\">".Text::_("for object")."</a>";?>
	</div>
	<?php } ?>
	<?php if ($a_link) { ?> 
		<div class="comment_avatar"><a rel="nofollow" href="<?php echo $user_link; ?>"><img src="<?php echo $a_link; ?>" /></a></div>
	<?php } else { ?> 
		<div class="comment_no_avatar"></div>
	<?php } ?> 
	<div id="comment_date_<?php echo $comment_id; ?>" class="comment_date"><?php echo Date::fromSQL($mess->cm_date); ?></div>
<!--	<div id="comment_date_<?php echo $comment_id; ?>" class="comment_date"><a href="<?php echo $cm_link;?>"><?php echo Date::fromSQL($mess->cm_date); ?></a></div>  -->
	<?php if ($user_link) { ?> 
		<div class="comment_author"><a title="<?php echo Text::_("View user profile"); ?>" class="comment_author" rel="nofollow" href="<?php echo $user_link ?>"><span id="comment_author_<?php echo $comment_id; ?>"><?php echo $mess->cm_nickname; ?></span></a></div>
	<?php } else { ?> 
		<div id="comment_author_<?php echo $comment_id ?>" class="comment_author"><?php echo $mess->cm_nickname; ?></div>
	<?php } ?>
	<div class="comment_title">
		<span class="comment_title"><?php echo $mess->cm_title; ?></span>
	</div>
	<?php if (($mess->cm_cat)||($mess->cm_type)){ ?>
		<div class="comment_tps"><?php echo $this->renderTypeMessage($mess->cm_type); ?>&nbsp;<?php echo $this->renderCatMessage($mess->cm_cat); ?></div>	
	<?php } ?>				
	<div class="comment_text">
	<?php if ($this->bbcode) { 
		Event::raise("bbcode.parse",array(),$mess->cm_text); 
		echo $mess->cm_text; 
	} else echo Text::toHtml($mess->cm_text); ?>
	</div>
	<?php if ($canModerate || $canWrite || $mess->cm_children || $this->vote_comms || $uplink) { ?>
		<?php if ($this->vote_comms) { ?>
		<div class="comment_controls comment_rating">
			<div class="rating"><?php echo Text::_("Rating")." : ".$mess->cm_rating; ?></div>
			<?php if($canVote) echo Event::raise("rating.rendervotepanel",array("module"=>$this->module,"view"=>$this->view,"element"=>"comment","psid"=>$comment_id, "mess"=>Text::_("Vote this comment"))); ?>
		</div>
		<?php } ?>
		<?php echo HTMLControls::renderHiddenField("comm_published_".$comment_id, $mess->cm_published); ?>
		<?php echo HTMLControls::renderHiddenField("comm_deleted_".$comment_id, $mess->cm_deleted); ?>
		<div class="comment_controls">
			<?php if ($canModerate) { ?>
				<a onclick="<?php echo $publishJS; ?>" class="linkButton commentFooterAdminLink btn btn-warning" rel="nofollow">
					<?php 	if ($mess->cm_published) echo Text::_("Disable"); else echo Text::_("Enable"); ?>
				</a>
				<a onclick="<?php echo $deleteJS; ?>" class="linkButton commentFooterAdminLink btn btn-warning" rel="nofollow">
					<?php if ($mess->cm_deleted) echo Text::_("Undelete"); else echo Text::_("Delete"); ?>
				</a>
			<?php } ?>
			<?php if ($childrenJS) { ?> <a id="expander"<?php echo $comment_id; ?>" onclick="<?php echo $childrenJS; ?>" class="linkButton btn btn-info" rel="nofollow"><?php echo Text::_("Answers")." (".$mess->cm_children; ?>)</a><?php } ?>
			<?php if ($answerJS) { ?><a onclick="<?php echo $answerJS; ?>" href="#commentEditor" class="linkButton btn btn-info" rel="nofollow"><?php echo Text::_("Answer"); ?></a><?php } ?>
			<?php if ($uplink) { ?><a class="linkButton" href="<?php echo $uplink;?>"><?php echo $uplink_text; ?></a><?php } ?>
		</div>
	<?php } ?>
</div>
<div class="comment_footer"></div>