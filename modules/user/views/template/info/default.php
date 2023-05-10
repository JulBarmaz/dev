<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$profile = $this->profile;
if(!empty($profile["pf_img"]["val"])) {
	$img_path=BARMAZ_UF_PATH."user".DS."i".DS."avatars".DS.$profile["pf_img"]["val"];
	if (is_file($img_path))	$img='<img width="100" src="'.BARMAZ_UF.'/user/i/avatars/'.$profile["pf_img"]["val"].'" alt="" />'; 	
	else $img='<img class="avatar" alt="" src="/templates/'.Portal::getInstance()->getTemplate().'/images/nofoto.png">';
} else $img='<img class="avatar" alt="" src="/templates/'.Portal::getInstance()->getTemplate().'/images/nofoto.png">';
$about = str_replace("\n", "<br />",$profile["pf_text"]["val"]);

Portal::getInstance()->setTitle( Text::_("User info")." ".$this->nickname );
?>

<div class="userinfo">
	<h1 class="title"><?php echo Text::_("User info")." ".$this->nickname;?></h1>
	<div id="usercontacts">
<?php	foreach($profile as $key=>$val) {
		if ($key=="pf_img" || $key=="pf_text") continue;
		echo "<div class=\"row\">";
		echo "<div class=\"col-md-6\">";
		echo HTMLControls::renderLabelField(false, $val["title"].": ");
		echo "</div><div class=\"col-md-6\">";
		echo $val["val"];
		echo "</div>";
		echo "</div>";
} ?>
	</div>
	<div id="userabout" class="row">
		<div class="col-md-4"><?php echo $img ?></div>
		<div class="col-md-8"><?php echo $about; ?></div>
	</div>
	<?php if($this->canWriteToUser) {?>
		<div id="userlink" class="buttons">
			<a class="commonButton btn btn-info" href="<?php echo Router::_("index.php?module=mail&view=write&recvuid=".$this->psid); ?>"><?php echo Text::_("Write personal message"); ?></a>
		</div>
	<?php } ?>
</div>
