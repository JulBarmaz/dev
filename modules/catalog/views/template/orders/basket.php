<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

echo "<h1 class=\"title order_title\">".Text::_("Order registration")."</h1>";
echo "<div class=\"full_basket\">";
echo Basket::getInstance()->modifyBasket(false,true,true,true);
echo "</div>";
