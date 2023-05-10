<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_TEMPLATE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div id="outer_wrapper">
	<div id="inner_wrapper" class="float-fix">
		<div id="error_header">
			<div id="header">
				<div class="container">
					<div class="row">
						<div id="sitetitle" class="col-sm-8"><?php echo siteConfig::$metaTitle; ?></div>
						<div id="sitelogo" class="col-sm-4"><a onclick="javascript: document.getElementById('loginForm').style.display='block';return false;" rel="nofollow" href="/"><img width="1" height="1" alt="" src="/images/blank.gif" /></a></div>
					</div>
				</div>
			</div><!-- header -->
		</div>
		<div id="content">
			<div id="loginForm"	style="display: none; margin: 20px auto; text-align: center; max-width: 400px;">
				<form action="<?php echo Router::_("index.php"); ?>" method="post">
					<input name="option" value="login" type="hidden">
					<div class="container">
						<div class="row">
							<div class="col-sm-5"><label for="username" style="margin-top: 6px;">User:</label></div>
							<div class="col-sm-7"><input class="commonEdit loginEdit form-control" id="username" name="username" value="" type="text"></div>
						</div>
						<div class="row">
							<div class="col-sm-5"><label for="password" style="margin-top: 6px;">Password:</label></div>
							<div class="col-sm-7"><input class="commonEdit loginEdit form-control" name="userpass" value="" type="password"></div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<input class="commonButton btn btn-info" value="Log in" onclick="javascript: $('#loginForm').hide('fast'); return true;" type="submit">
							</div>
						</div>
					</div>
				</form>
			</div>
			<div id="disabledMessageBlock">
				<div id="disabledMessage"><?php echo Text::_('Site is currently disabled'); ?>.</div>
				<div id="disabledMessageSmall"><?php echo Text::_('Visit us later'); ?>.</div>
			</div>
		</div>
	</div>
	<div id="bufer_wrapper"></div>
</div>
<div id="footer">
	<div id="copyright">
		<div id="copyrightText"><?php echo $this->getCopyright(); ?></div>
	</div>
</div>
