<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class defaultViewcpanel extends View {
	public $icons_arr = array();
	protected $disabled_modules = array();
	
	public function prepare(){
		foreach (Portal::getInstance()->getDisabledModules() as $d_module) {
			$this->disabled_modules[$d_module] = true;
		}
		$divider = 0;
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=user", "icon"=>"user.png", "title"=>"Users", "class"=>"", "module"=>"user");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=aclmgr&view=roles", "icon"=>"roles.png", "title"=>"Roles", "class"=>"", "module"=>"aclmgr");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=user&view=auth_providers", "icon"=>"user.png", "title"=>"Auth providers", "class"=>"", "module"=>"user");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=conf&task=pi", "icon"=>"info.png", "title"=>"System info", "class"=>"", "module"=>"conf");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=conf&view=config", "icon"=>"settings.png", "title"=>"Settings", "class"=>"", "module"=>"conf");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=conf&view=modules", "icon"=>"modules.png", "title"=>"Modules", "class"=>"", "module"=>"conf");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=conf&view=plugins", "icon"=>"plugins.png", "title"=>"Plugins", "class"=>"", "module"=>"conf");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=conf&view=widgets", "icon"=>"widgets.png", "title"=>"Widgets", "class"=>"", "module"=>"conf");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=conf&task=selectVisio", "icon"=>"prepare_visio.png", "title"=>"Prepare visio", "class"=>"", "module"=>"conf");
		$divider++;
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=article", "icon"=>"articles.png", "title"=>"Articles", "class"=>"", "module"=>"article");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=blog", "icon"=>"blogs.png", "title"=>"Blogs", "class"=>"", "module"=>"blog");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=acrm&view=items&layout=all", "icon"=>"acrm.png", "title"=>"ACRM", "class"=>"", "module"=>"acrm");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=polls", "icon"=>"polls.png", "title"=>"Polls", "class"=>"", "module"=>"polls");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=feedback", "icon"=>"feedback.png", "title"=>"Feedback", "class"=>"", "module"=>"feedback");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=gallery", "icon"=>"gallery.png", "title"=>"Gallery", "class"=>"", "module"=>"gallery");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=videoset", "icon"=>"video.png", "title"=>"Video gallery", "class"=>"", "module"=>"videoset");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=comments", "icon"=>"comments.png", "title"=>"Comments", "class"=>"", "module"=>"comments");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=forum", "icon"=>"forum.png", "title"=>"Forum", "class"=>"", "module"=>"forum");
		$divider++;
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=catalog&view=orders", "icon"=>"orders.png", "title"=>"Orders", "class"=>"", "module"=>"catalog");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=catalog&view=goodsgroup", "icon"=>"goods.png", "title"=>"Goods groups", "class"=>"", "module"=>"catalog");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=catalog&view=options", "icon"=>"options.png", "title"=>"Goods options", "class"=>"", "module"=>"catalog");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=catalog&view=deliverytypes", "icon"=>"delivery.png", "title"=>"Delivery types", "class"=>"", "module"=>"catalog");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=catalog&view=paymenttypes", "icon"=>"payment.png", "title"=>"Payment types", "class"=>"", "module"=>"catalog");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=catalog&view=manufacturer_cats", "icon"=>"manufacturers.png", "title"=>"Manufacturers", "class"=>"", "module"=>"catalog");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=catalog&view=vendor_cats", "icon"=>"vendors.png", "title"=>"Vendors", "class"=>"", "module"=>"catalog");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=catalog&view=currency", "icon"=>"money.png", "title"=>"Currencies", "class"=>"", "module"=>"catalog");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=catalog&view=measures", "icon"=>"measures.png", "title"=>"Measures", "class"=>"", "module"=>"catalog");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=catalog&view=discounts", "icon"=>"discounts.png", "title"=>"Discounts and surcharges", "class"=>"", "module"=>"catalog");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=catalog&view=taxes", "icon"=>"taxes.png", "title"=>"Taxes", "class"=>"", "module"=>"catalog");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=catalog&view=import", "icon"=>"import.png", "title"=>"Import catalog data", "class"=>"", "module"=>"catalog");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=catalog&view=export", "icon"=>"export.png", "title"=>"Export catalog data", "class"=>"", "module"=>"catalog");
		$divider++;
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=service&view=db", "icon"=>"backup.png", "title"=>"Backup tables", "class"=>"", "module"=>"service");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=service&view=cachemanager", "icon"=>"cache.png", "title"=>"Cache manager", "class"=>"", "module"=>"service");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=service&task=startMediamanager", "icon"=>"files.png", "title"=>"Media manager", "class"=>"relpopupwt", "module"=>"service");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=installer", "icon"=>"installer.png", "title"=>"Installer", "class"=>"", "module"=>"installer");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=service&view=updater", "icon"=>"updates.png", "title"=>"Check for updates", "class"=>"", "module"=>"service");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=help", "icon"=>"help.png", "title"=>"Help", "class"=>"", "module"=>"help");
		$this->icons_arr[$divider][] = array("link"=>"index.php?module=help&layout=license", "icon"=>"license.png", "title"=>"License", "class"=>"", "module"=>"help");
		$this->icons_arr[$divider][] = array("link"=>Portal::getInstance()->getURI(1)."index.php?option=logout", "icon"=>"exit.png", "title"=>"Logout", "class"=>"", "module"=>"");
		Event::raise("admin.conf.cpanel.data.prepared", array(), $this->icons_arr);
	}
}
?>