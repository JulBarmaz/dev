<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

if (count($this->orders)>0) {
	echo "<h1 class=\"title\">".Text::_("Your orders")."</h1>";
	echo "<p>".Text::_("Attention. To cancel the order contact us via e-mail. Order cancellation is possible only before its payment.")."</p>";
	echo "<div class=\"orders-wrapper\">";
	echo "<table class=\"orders table table-bordered table-hover table-condensed sprav-table\"><thead>";
	echo "<tr>";
	echo "<th>".Text::_("Order number")."</th>";
	echo "<th>".Text::_("Order date")."</th>";
	if (siteConfig::$use_points_system) { echo "<th>".Text::_('Points')."</th>"; }
	echo "<th>".Text::_("Sum")."</th>";
	echo "<th>".Text::_("Is paid")."</th>";
	echo "<th>".Text::_("Status")."</th>";
	echo "</tr>";
	echo "</thead><tbody>";
	foreach ($this->orders as $order) {
		$view_href=Router::_("index.php?module=catalog&view=orders&layout=order&order_id=".$order->o_id);
		$view_title=Text::_("View order");
		echo "<tr>";
		echo "<td>".$order->o_id."</td>";
		echo "<td><a rel=\"nofollow\" title=\"".$view_title."\" href=\"".$view_href." \">".Date::fromSQL($order->o_date, true, true)."</a></td>";
		if (siteConfig::$use_points_system) echo "<td>".$order->o_points."</td>";
		echo "<td>".number_format($order->o_total_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName($order->o_currency)."</td>";
		if ($order->o_paid)	echo "<td class=\"green\">".Text::_("Is paid")."</td>";
		else	{
			$pay_href=Router::_("index.php?module=catalog&view=orders&layout=payment&order_id=".$order->o_id);
			$pay_title=Text::_("Pay")." ".Text::_("order")." № ".$order->o_id;
			echo "<td><a rel=\"nofollow\" title=\"".$pay_title."\" href=\"".$pay_href." \">".Text::_("N")."</a></td> \n";
		}
		echo "<td>".$this->status_arr[$order->o_status]->os_name."</td>";
		echo "</tr>";
	}
	echo "</tbody></table>";
	echo "</div>";
} else { echo "<h1>".Text::_("You have not orders yet")."</h1>"; }
?>