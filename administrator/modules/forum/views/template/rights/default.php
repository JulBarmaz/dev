<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$return_link="index.php?module=forum";
?>
<div class="container"><div class="row"><div class="col-md-12"><div class="acl-manager rounded-pan rounded-pan-mini">
	<h4 class="title"><?php echo Text::_('Forum rights')." (".Text::_('Forum')."&nbsp;".$this->forum_name.")"; ?></h4>
	<form action="index.php" method="post">
		<input type="hidden" name="module" value="forum" />
		<input type="hidden" name="view" value="rights" />
		<input type="hidden" name="layout" value="modify" />
		<input type="hidden" name="psid" value="<?php echo $this->forum_id; ?>" />
		<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
	
		<div class="row">
			<div class="col-sm-6">
				<input type="radio" id="rb_user" name="subject" value="user" />
				<label for="rb_user"><?php echo Text::_('User login'); ?></label>
			</div>
			<div class="col-sm-6">
				<input class="form-control" onfocus="javascript:$('#rb_user').click();" type="text" id="t_user" name="userlogin" value="" />
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<input type="radio" id="rb_role" name="subject" value="role" checked="checked" />
				<label for="rb_role"><?php echo Text::_('User role'); ?></label>
			</div>
			<div class="col-sm-6">
				<select class="singleSelect" onfocus="javascript:$('#rb_role').click();" id="s_role" name="roleid">
				<?php foreach ($this->roles as $roleid=>$role) {?>
					<option value="<?php echo $roleid; ?>"><?php echo $role->ar_title; ?></option>
				<?php } ?>
				</select>
			</div>
		</div>
		<div class="buttons">
			<input type="submit" class="commonButton btn btn-info" value="<?php echo Text::_('Edit'); ?>" />
			<input type="button" class="commonButton btn btn-info" onclick="javascript:document.location.href='<?php echo Router::_($return_link); ?>'; return true;" value="<?php echo Text::_('Cancel'); ?>" name="cancel" />
		</div>
	</form>
</div>