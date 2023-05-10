<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_TEMPLATE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

define('_BARMAZ_HTML5',true);
define("_BARMAZ_IE_DROP", 7);

// примеры подключений 
// $my_css=Portal::getURI()."/templates/".Portal::getInstance()->getTemplate()."/css/mycss.css";
// Portal::getInstance()->addStyleSheet($my_css);
//
// Portal::getInstance()->addStyleSheet("https://fonts.googleapis.com/css2?family=PT+Sans+Narrow:wght@400;700&display=swap");
//
// $my_js=Portal::getURI()."/templates/".Portal::getInstance()->getTemplate()."/js/myjs.js";
// Portal::getInstance()->addScript($my_js);
//
// Portal::getInstance()->setMeta("viewport","width=device-width, initial-scale=1.0, maximum-scale=10.0");
// Получение некоторых важных переменных
/*
echo "option = ".Portal::getInstance()->get("option")."<br />";
echo "module через Portal = ".Portal::getInstance()->get("module")."<br />";
echo "module через Instance = ".Module::getInstance()->getName()."<br />";
echo "task через Portal = ".Portal::getInstance()->get("task")."<br />";
echo "view через Portal = ".Portal::getInstance()->get("view")."<br />";
echo "view через Controller & View = ".Module::getInstance()->getController()->getView()->getName()."<br />";
echo "layout через Router = ".Request::getSafe("layout")."<br />";
echo "layout через Controller & View = ".Module::getInstance()->getController()->getView()->getLayout()."<br />";
*/
// Если требуется скрыть на главной странице основной контент,
// то можно использовать условие:
// if (!Portal::getInstance()->isMainpage(1)) echo $this->moduleHTML;
// вместо
// echo $this->moduleHTML;
// $this->addScript("/redistribution/html5_css3/html5.js");
Portal::getInstance()->addScript("/templates/".Portal::getInstance()->getTemplate()."/js/jquery.nicefileinput.min.js");
Portal::getInstance()->addScript("/redistribution/jquery.plugins/jquery.maskedinput.min.js");
/********************************************************************************************/
$logo_arr=array(1=>"logo_bz.png",2=>"logo_bz2.png");
$logo_img=$logo_arr[rand(1,2)];
/********************************************************************************************/
$left=$this->countZone("left-top")+$this->countZone("left")+$this->countZone("left-bottom");
$right=$this->countZone("right-top")+$this->countZone("right")+$this->countZone("right-bottom");
if ($left && $right) $center_class="center_block col-md-6";
elseif ($left && !$right) $center_class="center_block col-md-9";
elseif (!$left && $right) $center_class="center_block col-md-9";
else $center_class="center_block_full col-md-12";
// Если системное сообщение выводится средствами системы то делаем так
echo $this->renderSystemMessage();
// Если нужно сверстать своё системное сообщение и обработать код сообщения то можно сделать так:
// $BARMAZ_message = Session::getVar('BARMAZ_message');
// $BARMAZ_message_code = Session::getVar('BARMAZ_message_code');
// ...... тут обрабатываем и верстаем
// потом вызываем обнуление и вывод сообщения об отключенном сайте для админа так:  
// echo $this->renderSystemMessage(true);
// или так:
// if(siteConfig::$siteDisabled && User::getInstance()->isLoggedIn() && User::getInstance()->isAdmin()) echo $this->renderSiteIsDisabledMsg();
// echo $this->renderSystemMessage(true, true);
?>
<div id="outer_wrapper">
	<div id="inner_wrapper" class="float-fix">
		<header id="site_header">
			<div class="container">
				<?php if ($this->countZone("top-left") || $this->countZone("top-right")) { ?>
					<div class="row">
						<div class="col-sm-6"><?php $this->placeZone("top-left");?></div>
						<div class="col-sm-6"><?php $this->placeZone("top-right");?></div>
					</div>
				<?php } ?>
				<div class="row">
					<div id="sitelogo" class="logo col-sm-4 col-md-3"><a rel="nofollow" href="<?php echo Portal::getInstance()->getURI(); ?>"><img width="100%" alt="" src="<?php echo "/templates/".Portal::getInstance()->getTemplate()."/images/".$logo_img; ?>" /></a></div>
					<div id="sitetitle" class="col-sm-8 col-md-9"><?php echo siteConfig::$metaTitle; ?></div>
				</div>
			</div>
		</header>
		<?php
		if ($this->countZone("top-menu")) { ?><nav class="maintopmenu"><div class="container"><?php $this->placeZone("top-menu");?></div></nav><?php } ?><!-- topmenu -->
		<?php if ($this->countZone("top-wide")) { ?><section id="top_wide"><?php $this->placeZone("top-wide");?></section><?php } ?><!-- top_wide -->
		<?php if ($this->countZone("top")) { ?><section id="itms_top"><div class="container"><div class="row"><div class="col-xs-12"><?php $this->placeZone("top");?></div></div></div></section><?php } ?><!-- itms_top -->
		<section id="wrapper">
			<div class="container">
				<div class="row">
					<?php if ($left) {?>
					<aside id="left_column" class="col-md-3">
						<?php if ($this->countZone("left-top")) { ?><div class="wzone row"><?php $this->placeZone("left-top","rounded_4");?></div><?php } ?>
						<?php if ($this->countZone("left")) { ?><div class="wzone row"><?php $this->placeZone("left","rounded_4");?></div><?php } ?>
						<?php if ($this->countZone("left-bottom")) { ?><div class="wzone row"><?php $this->placeZone("left-bottom","rounded_4");?></div><?php } ?>
					</aside><!-- left_column -->
					<?php } ?>
					<div class="<?php echo $center_class; ?>">
						<?php if ($this->countZone("center-top")) { ?><div id="t_quarters" class="wzone row row-cells-autoheight"><?php $this->placeZone("center-top","col_2");?></div><?php } ?>
						<div class="nopad">
							<?php if ($this->countZone("user-1")) { ?><div class="row"><div class="col-xs-12"><?php $this->placeZone("user-1"); ?></div></div><?php } ?>
							<?php 
							if (!Portal::getInstance()->isMainpage(1)) echo $this->moduleHTML;
							else Portal::getInstance()->setTitle(siteConfig::$metaTitle);
							?>
							<?php if ($this->countZone("user-2")) { ?><div class="row"><div class="col-xs-12"><?php $this->placeZone("user-2"); ?></div></div><?php } ?>
						</div><!-- nopad -->
						<?php if ($this->countZone("center-bottom")) { ?><div id="b_quarters" class="wzone row row-cells-autoheight"><?php $this->placeZone("center-bottom","col_2");?></div><?php } ?>
					</div><!-- main body counted-->
					<?php if ($right) {?>
					<aside id="right_column" class="col-md-3">
						<?php if ($this->countZone("right-top")) { ?><div class="wzone row"><?php $this->placeZone("right-top","rounded_4");?></div><?php } ?>
						<?php if ($this->countZone("right")) { ?><div class="wzone row"><?php $this->placeZone("right","rounded_4");?></div><?php } ?>
						<?php if ($this->countZone("right-bottom")) { ?><div class="wzone row"><?php $this->placeZone("right-bottom","rounded_4");?></div><?php } ?>
					</aside><!-- right_column -->
				<?php } ?>
				</div>
			</div>
		</section><!-- wrapper -->
		<?php if ($this->countZone("bottom")) { ?><section id="itms_bottom"><div class="container"><div class="row"><div class="col-xs-12"><?php $this->placeZone("bottom");?></div></div></div></section><?php } ?><!-- itms_bottom -->
		<?php if ($this->countZone("bottom-wide")) { ?><section id="bottom_wide"><?php $this->placeZone("bottom-wide");?></section><?php } ?><!-- bottom_wide -->
	</div><!-- inner_wrapper -->
	<div id="bufer_wrapper"></div>
</div><!-- outer_wrapper -->
<div id="footer_wrapper" class="autoheight">
	<div class="footer-wrapper-inner">
		<footer class="container">
			<div class="row row-cells-autoheight">
				<div class="footer-block footer-block-1 col-xs-12 col-sm-6 col-md-3"><div class="footer-block-inner row-cell-wrapper"><div class="logo"><a rel="nofollow" href="<?php echo Portal::getInstance()->getURI(); ?>"><img width="100%" alt="" src="<?php echo "/templates/".Portal::getInstance()->getTemplate()."/images/".$logo_img; ?>" /></a></div><div class="copyright"><?php echo $this->getCopyright(); ?></div><?php $this->placeZone("user-6");?></div></div>
				<div class="footer-block footer-block-2 col-xs-12 col-sm-6 col-md-3"><div class="footer-block-inner row-cell-wrapper"><?php $this->placeZone("user-7");?></div></div>
				<div class="footer-block footer-block-3 col-xs-12 col-sm-6 col-md-3"><div class="footer-block-inner row-cell-wrapper"><?php $this->placeZone("user-8");?></div></div>
				<div class="footer-block footer-block-4 col-xs-12 col-sm-6 col-md-3"><div class="footer-block-inner row-cell-wrapper"><?php $this->placeZone("user-9");?></div></div>
			</div>
		</footer>
	</div>
</div>
<a href="" id="go_top"></a>
<a href="" id="go_bottom"></a>
