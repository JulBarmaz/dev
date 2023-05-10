<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$params=array(); $data=array(); $data_icons=array();
Event::raise("user.renderpanelpan",$params,$data);
Event::raise("user.renderpanelicons",$params,$data_icons);
$usr=User::getInstance();
$at=Request::getSafe('at','tab_1');
$profile = $this->profile;
if(!empty($profile["pf_img"]["val"])) {
	$img_path=BARMAZ_UF_PATH."user".DS."i".DS."avatars".DS.Files::getAppendix($profile["pf_img"]["val"]).DS.$profile["pf_img"]["val"];
	if (is_file($img_path))	$img='<img width="100" src="'.BARMAZ_UF.'/user/i/avatars/'.Files::getAppendix($profile["pf_img"]["val"])."/".$profile["pf_img"]["val"].'" alt="" />'; 	
	else $img='<img class="avatar" alt="" src="/templates/'.Portal::getInstance()->getTemplate().'/images/nofoto.png" />';
} else $img='<img class="avatar" alt="" src="/templates/'.Portal::getInstance()->getTemplate().'/images/nofoto.png" />';

$about = str_replace("\n", "<br />",$profile["pf_text"]["val"]);
?>
<div class="userpanel tab-content clearfix">
	<!-- Start switcher -->
	<ul class="nav nav-tabs" id="tabs">
		<li id="sw_tab_1" class="<?php echo ($at=="tab_1" ? " active" : "")?>">
			<a href="#tab_1" data-toggle="tab"><?php echo Text::_("Service panel"); ?></a>
		</li>
		<?php if (!$this->hide_profile_tab_base || !$this->hide_profile_tab_public) {?>
		<li id="sw_tab_2" class="<?php echo ($at=="tab_2" ? " active" : "")?>">
			<a href="#tab_2" data-toggle="tab"><?php echo Text::_("Profile"); ?></a>
		</li>
		<?php } ?>
		<?php if (!$this->hide_personal_tab) {?>
		<li id="sw_tab_3" class="<?php echo ($at=="tab_3" ? " active" : "")?>">
			<a href="#tab_3" data-toggle="tab"><?php echo Text::_("Private info"); ?></a>
		</li>
		<?php } ?>
		<?php if ($this->vendors && count($this->vendors)) {?>
		<li id="sw_tab_4" class="<?php echo ($at=="tab_4" ? " active" : "")?>">
			<a href="#tab_4" data-toggle="tab"><?php echo Text::_("Vendor panel"); ?></a>
		</li>
		<?php } ?>
		<?php if(count($data)){
				$npp=100;
				foreach($data as $row){
					$npp++;
					echo "<li id=\"sw_tab_".$npp."\" class=\"".($at=="tab_".$npp ? " active" : "")."\"><a href=\"#tab_".$npp."\" data-toggle=\"tab\">".$row['title']."</a></li>"; 
				}
			}
		?>
	</ul>
	<!-- End switcher -->
	<!-- Кнопки сервисов  -->
	<div class="tab-content clearfix">
		<div class="tab-pane<?php echo ($at=="tab_1" ? " active" : "")?>" id="tab_1">
			<?php if (!catalogConfig::$ordersDisabled) {?>
			<div class="panel_icon"> 
				<a href="<?php echo Router::_("index.php?module=catalog&amp;view=orders"); ?>">
					<img src="/images/package.png" height="48" width="48" alt="" /> <?php echo Text::_("Orders Info");?> 
				</a>
			</div>
			<div class="panel_icon"> 
				<a href="<?php echo Router::_("index.php?module=catalog&task=ShowBasket&option=ajax"); ?>" class="relpopupwt"> 
					<img src="/images/basket.png" height="48" width="48" alt="" /> <?php echo Text::_("Basket Info");?>
				</a> 
			</div>
			<?php } ?>
			<?php if (siteConfig::$use_referral_system) {?>
			<div class="panel_icon"> 
				<a href="<?php echo Router::_("index.php?module=user&amp;view=structure"); ?>">
					<img src="/images/money.jpg" height="48" width="48" alt="" /> 
					<?php echo Text::_("My structure");?> 
				</a>
			</div>
			<?php }?>
			<div class="panel_icon">
				<a href="<?php echo Router::_("index.php?module=feedback&view=messages"); ?>">
					<img src="/images/letter.png" height="48" width="48" alt="" /> <?php echo Text::_("My feedbacks");?> 
				</a> 
			</div>
			<?php if(User::getInstance()->isAdmin()){ ?>
			<div class="panel_icon"> 
				<a href="<?php echo Router::_("administrator/index.php"); ?>">
					<img src="/images/admin.png" height="48" width="48" alt="" /> <?php echo Text::_("Administration");?> 
				</a> 
			</div>
			<?php if(count($data_icons)) {
				foreach($data_icons as $icon){
					echo "<div class=\"panel_icon\"><a href=\"".$icon["link"]."\"><img src=\"".$icon["image"]."\" height=\"48\" width=\"48\" alt=\"\" /> ".$icon["title"]."</a></div>";
				}
			}?>
			<?php } ?>
		</div> <!-- End tab -->
		<!-- Start tab -->
		<?php if (!$this->hide_profile_tab_base || !$this->hide_profile_tab_public) {?>
		<div class="tab-pane<?php echo ($at=="tab_2" ? " active" : "")?>" id="tab_2">
			<?php if (!$this->hide_profile_tab_base) {?>
			<div class="add_info_text"> 
				<fieldset><legend><?php echo Text::_("Base info")?></legend>
					<div class="row"><div class="col-md-4"><?php echo HTMLControls::renderLabelField(false, Text::_('Login').":"); ?></div><div class="col-md-8"><?php echo $usr->u_source=="system" ? $usr->u_login : strtoupper($usr->u_source); ?> </div></div>
					<?php if($usr->u_source=="system") {?>
						<div class="row"><div class="col-md-12"><a href="<?php echo Router::_("index.php?module=user&view=reset"); ?>"><?php echo Text::_('Change password'); ?>&nbsp;<?php echo HTMLControls::renderIcon('minikeys'); ?></a> </div></div>
					<?php }?>
					<div class="row"><div class="col-md-4"><?php echo HTMLControls::renderLabelField(false, Text::_('Nickname').":"); ?></div><div class="col-md-8"><?php echo $usr->u_nickname; ?></div></div>
					<div class="row"><div class="col-md-4"><?php echo HTMLControls::renderLabelField(false, Text::_('Category').":"); ?></div><div class="col-md-8"><?php echo $usr->getRoleName(); ?></div></div>
					<div class="row"><div class="col-md-4"><?php echo HTMLControls::renderLabelField(false, Text::_('Rating').":"); ?></div><div class="col-md-8"><?php echo $usr->u_rating; ?></div></div>
					<?php if($usr->u_discount) { ?><div class="row"><div class="col-md-4"><?php echo HTMLControls::renderLabelField(false, Text::_('Discount').":"); ?></div><div class="col-md-8"><?php echo $usr->u_discount ?> %</div></div><?php } ?>
					<?php if (siteConfig::$use_points_system) {?><div class="row"><div class="col-md-4"><?php echo HTMLControls::renderLabelField(false, Text::_('Points').":"); ?></div><div class="col-md-8"><?php echo $usr->u_points; ?></div></div><?php }	?>
									
					<?php if (siteConfig::$use_referral_system) {?>
						<div class="row"><div class="col-md-4"><?php echo HTMLControls::renderLabelField(false, Text::_('Invite Link'),":"); ?></div><div class="col-md-8"><?php echo Portal::getURI(); ?>ref_<?php echo $usr->u_affiliate_code;?>.html</div></div>
						<div class="row"><div class="col-md-4"><?php echo HTMLControls::renderLabelField(false, Text::_('Account').":"); ?></div><div class="col-md-8"><?php echo $usr->u_account; ?></div></div>
					<?php }	?>
			 		<div class="row"><div class="col-md-4"><a href="<?php echo Router::_("index.php?module=user&view=reset&layout=email"); ?>"><?php echo Text::_('Change e-mail'); ?></a></div><div class="col-md-8"><?php echo User::getInstance()->getEmail(); ?></div></div>
				</fieldset>
			</div>
			<?php }	?>
			<?php if (!$this->hide_profile_tab_public) {?>
			<div class="add_info_text"> 
				<fieldset><legend><?php echo Text::_("Public info")?></legend>
					<p class="warning" style="color: #FF0000;"><?php echo Text::_("Popular information") ?>.</p>
					<p class="warning" style="color: #FF0000;"><?php echo Text::_("It is that information which you would like to share with ALL users of a network") ?>.</p>
					<div id="usercontacts">
						<?php foreach($profile as $key=>$val) {
							if ($key=="pf_img" || $key=="pf_text") continue;
							echo "<div class=\"row\">";
							echo "<div class=\"col-md-4\">";
							echo HTMLControls::renderLabelField(false, $val["title"].":");
							echo "</div><div class=\"col-md-8\">";
							echo $val["val"];
							echo "</div>";
							echo "</div>";
						} ?>
					</div>
					<div id="userabout" class="row">
						<div class="col-md-4"><?php echo $img ?></div>
						<div class="col-md-8"><?php echo $about; ?></div>
					</div>
					<div class="buttons"><a class="linkButton btn btn-info" href="<?php echo Router::_("index.php?module=user&amp;view=panel&amp;task=modifyProfile");?>"><?php echo Text::_("EDIT INFO");?></a></div>
				</fieldset>
			</div>
			<?php }	?>
		</div> <!-- End tab -->
		<?php } ?>
		
		<!-- Start tab -->
		<!--  Компания, адреса счета -->
		<?php if (!$this->hide_personal_tab) {?>
		<div class="tab-pane<?php echo ($at=="tab_3" ? " active" : "")?>" id="tab_3">
			<div class="add_info_text">
				<div class="private_warning">
					<p class="warning" style="color: #FF0000;"><?php echo Text::_("It is private information"); ?>.</p>
					<p class="warning" style="color: #FF0000;"><?php echo Text::_("It is stored in the ciphered kind and is accessible ONLY to you and the site personnel having necessary access"); ?>.</p>
					<p class="warning" style="color: #FF0000;"><?php echo Text::_("It is used ONLY when you fulfill those operations on site for which your identification required"); ?>.</p>
				</div>
				<div class="data">
					<h4><?php echo Text::_("Business info")?></h4>
					<table class="addresses table table-hover">
						<tr>
							<td class="add_info_label company_string">
							<?php
							$org_type=Userdata::getOrgType($this->company["org_type"]);
							if ($org_type) echo Text::_($org_type)." : "; else echo Text::_("Data absent");
							if ($this->company["fullname"])	echo $this->company["fullname"]; else echo $this->company["surname"]." ".$this->company["firstname"]." ".$this->company["patronymic"];
							?>
							</td>
							<th width="1%" align="right" class="editButton"><a title="<?php echo Text::_("Modify");?>" class="relpopupdate editButton" href="<?php echo Router::_("index.php?module=user&amp;view=panel&amp;task=modifyCompany"); ?>"></a></th>
						</tr>
					</table>
				</div>
				<div class="data">
					<h4><?php echo Text::_("Banks info")?></h4>
					<?php if (is_array($this->banks)){ ?>
						<table class="addresses table table-hover" width="100%">
						<?php 	foreach($this->banks as $key=>$bank){ ?>
							<tr>
								<th width="1%" class="editButton"><a title="<?php echo Text::_("Delete");?>" class="deleteButton" onclick="if (confirm('<?php echo Text::_("Do you want to delete"); ?> ?')) return true; else return false;" href="<?php echo Router::_("index.php?module=user&amp;view=panel&amp;psid=".$key."&amp;task=deleteBank"); ?>"></a></th>
								<td class="add_info_label"><?php if($bank["use_as_default"]) echo Text::_("By default");?></td>
								<td class="add_info_label bank_string"><?php echo $bank["rs"].", ".$bank["bank"].($bank["bank_address"] ? ", ".$bank["bank_address"]:"");?></td>
								<th width="1%" align="right" class="editButton"><a title="<?php echo Text::_("Modify");?>" class="relpopup editButton"	href="<?php echo Router::_("index.php?module=user&amp;view=panel&amp;psid=".$key."&amp;task=modifyBank"); ?>"></a></th>
							</tr>
						<?php } ?>
						</table>
					<?php } ?>
					<div class="buttons">
						<a class="relpopup linkButton btn btn-info"	href="<?php echo Router::_("index.php?module=user&amp;view=panel&amp;task=modifyBank");?>"><?php echo Text::_("Add bank");?></a>
					</div>
				</div>
				<div class="data">
					<h4><?php echo Text::_("Address info")?></h4>
					<?php if (is_array($this->addresses)){ ?>
						<table class="addresses table table-hover" width="100%">
						<?php 	foreach($this->addresses as $key=>$addr){ ?>
							<tr>
								<th width="1%" class="editButton"><a title="<?php echo Text::_("Delete");?>" class="deleteButton" onclick="if (confirm('<?php echo Text::_("Do you want to delete"); ?> ?')) return true; else return false;" href="<?php echo Router::_("index.php?module=user&amp;view=panel&amp;psid=".$key."&amp;task=deleteAddress"); ?>"></a></th>
								<td class="add_info_label"><?php if($addr["use_as_default"]) echo Text::_("By default");?></td>
								<td class="add_info_label"><?php echo Address::getTypeTitle($addr["type_id"]);?></td>
								<td class="add_info_label address_string"><?php echo $addr["fullinfo"];?></td>
								<th width="1%" align="right" class="editButton"><a title="<?php echo Text::_("Modify");?>" class="relpopup editButton"	href="<?php echo Router::_("index.php?module=user&amp;view=panel&amp;psid=".$key."&amp;task=modifyAddress"); ?>"></a></th>
							</tr>
						<?php } ?>
						</table>
					<?php } ?>
					<div class="buttons">
						<a class="relpopup linkButton btn btn-info"	href="<?php echo Router::_("index.php?module=user&amp;view=panel&amp;task=modifyAddress");?>"><?php echo Text::_("Add address");?></a>
					</div>
				</div>
			</div>
		</div> <!-- End tab -->
		<?php } ?>
			
		<?php if ($this->vendors&&count($this->vendors)) {?>
		<!-- Start tab -->
		<!--  кнопки вендоров -->
		<div class="tab-pane<?php echo ($at=="tab_4" ? " active" : "")?>" id="tab_4">
			<div class="panel_icon">
				<a href="<?php echo Router::_("index.php?module=catalog&view=sales"); ?>"> 
					<img src="/images/basket.png" align="middle"	height="48" width="48" border="0" alt="" /> <?php echo Text::_("My sales");?>
				</a> 
			</div>
		</div> <!-- End tab -->
		<?php }?>
	
		<!-- Start tab -->
		<?php reset($data);
		if(count($data)){
			$npp=100;
			foreach($data as $row){
				$npp++;	echo "<div class=\"tab-pane".($at=="tab_".$npp ? " active" : "")."\" id=\"tab_".$npp."\">".$row['text']."</div>";
			}
		}
		?>
	</div>
</div>
