<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$return_link="index.php?module=blog&view=list&layout=all";
?>
<div class="container"><div class="row"><div class="col-md-12"><div class="acl-manager rounded-pan rounded-pan-mini">
	<h4 class="title"><?php echo Text::_('Blog rights')." (".Text::_('Blog')."&nbsp;".$this->blogName.")"; ?></h4>
	<form action="index.php" method="post">
		<input type="hidden" name="module" value="blog" />
		<input type="hidden" name="view" value="rights" />
		<input type="hidden" name="layout" value="modify" />
		<input type="hidden" name="blogid" value="<?php echo $this->blogId; ?>" />
		<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
	
		<div class="row">
			<div class="col-sm-6">
				<input type="radio" id="rb_user" name="subject" value="user" />
				<label for="rb_user" class="label"><?php echo Text::_('User login'); ?></label>
			</div>
			<div class="col-sm-6">
				<input class="form-control" type="text" id="t_user" name="userlogin" value="" />
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<input type="radio" id="rb_role" name="subject" value="role" checked="checked" />
				<label for="rb_role" class="label"><?php echo Text::_('User role'); ?></label>
			</div>
			<div class="col-sm-6">
				<select class="singleSelect" id="s_role" name="roleid">
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
</div></div></div></div>