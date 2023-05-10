<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

if (Basket::getInstance()->order_message) echo "<h1>".Basket::getInstance()->order_message."</h1>";
if ((isset($this->pt_class))&&($this->pt_class)) {
	$pt_class=$this->pt_class;
	$_mode=Request::getSafe('mode','show');
	if(method_exists($pt_class, $_mode)) $pt_class->{$_mode}();
	else die("Method absent");
} else { echo "<h1>".Text::_("Order not found")."</h1>";	}
?>