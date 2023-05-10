<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$orders  = $this->orders;
$orders_items = $this->orders_items;
if (count($orders)) {
	foreach ($orders as $order){
		$taxesInOrder=array();
		$summa=0; $total=0;
		$oid=$order->o_id;
		$summa=0; $total=0;
		echo "<div class=\"one_order printA4V\">";
		if (Request::getSafe("option")=="ajax"){
			echo "<div class=\"picto_right\"><a target=\"_blank\" href=\"index.php?module=catalog&view=orders&task=printOrders&psid=".$order->o_id."\"><img class=\"sprav-button-print\" src=\"/images/blank.gif\" alt=\"\" title=\"".Text::_("Print")."\"></a></div>";
			echo "<div class=\"picto_right\"><a href=\"index.php?module=catalog&view=orders&layout=order&psid=".$order->o_id."\"><img class=\"sprav-button-modify\" src=\"/images/blank.gif\" alt=\"\" title=\"".Text::_("Goods list")."\"></a></div>";
		}
		echo "<h3 class=\"title\">".Text::_("Order")." № ".$order->o_id." ".Text::_("from")." ".Date::GetdateRus($order->o_date)."</h3>";
		echo "<div class=\"order_row\">";
		echo "<b>".Text::_("Delivery type")." : </b>".$order->o_dt_name;
		echo catalogDelivery::getDeliveryClass($order->o_dt_id)->renderInfo($order->o_dt_data);
		echo "</div>";
		echo "<div class=\"order_row\">";
		echo "<b>".Text::_("Payment type")." : </b>".$order->o_pt_name;
		echo catalogPayment::getPaymentClass($order->o_pt_id)->renderInfo($order->o_pt_data);
		echo "</div>";
		$userdata = $order->o_userdata;
		if(is_array($userdata)){
			$_userdata_arr=array();
			if (array_key_exists("userdata_person", $userdata) && $userdata["userdata_person"])	$_userdata_arr[]= "<b>".Text::_("Contact person").":</b> ".$userdata["userdata_person"];
			if (array_key_exists("userdata_email", $userdata) && $userdata["userdata_email"]) $_userdata_arr[]= "<b>".Text::_("Contact email").":</b> ".$userdata["userdata_email"];
			if (array_key_exists("userdata_phone", $userdata) && $userdata["userdata_phone"]) $_userdata_arr[]= "<b>".Text::_("Contact phone").":</b> ".$userdata["userdata_phone"];
			if(count($_userdata_arr)) { 
				echo "<div class=\"order_row\">".implode("<br />", $_userdata_arr)."</div>";
			}
		}
		if(count($order->o_pt_result)){
			echo "<div class=\"order_row\">";
			echo "<b>".Text::_("Payment results")." : </b>";
			foreach ($order->o_pt_result as $res_k=>$res_v){
				echo "<br /><b>".Text::_($res_k)." : </b>".htmlspecialchars($res_v);
			}
			echo "</div>";
		}
		echo "<div class=\"order_row\">";
		echo "<b>".Text::_("Comments")." : </b>".htmlspecialchars($order->o_comments);
		echo "</div>";
		echo "<div class=\"clr\"></div>";
		echo "<div class=\"full_order\"><table class=\"orders\"> \n\n";
		echo "<thead> \n";
		echo "<tr> \n";
		echo "<th class=\"npp\">".Text::_('NPP')."</th> \n";
		echo "<th>".Text::_('Articul')."</th> \n";
		echo "<th>".Text::_('Title')."</th>";
		echo "<th class=\"cur_price\">".Text::_('Price')."</th>";
		echo "<th class=\"cur_quantity\">".Text::_('Quantity')."</th>";
		echo "<th class=\"cur_sum\">".Text::_('Sum')."</th>";
		echo "</tr>";
		echo "</thead><tbody>";
		$counter=0;
		foreach($orders_items as $g) {
			if ($oid!=$g->i_order_id) continue;
			$counter++;
			echo "<tr>";
			echo "<td>".$counter."</td>";
			echo "<td>".$g->i_g_sku."</td>";
			if($g->i_g_options_text){
				$i_g_options_text = "<span class=\"basket-row-options\">".$g->i_g_options_text."</span>";
			} else {
				$i_g_options_text = "";
			}
				
			echo "<td>".$g->i_g_name.$i_g_options_text."</td>";
			$current_price=$g->i_g_price;
			$current_quantity=$g->i_g_quantity;
			$current_sum=$g->i_g_sum;
			echo "<td class=\"cur_price\">".number_format($current_price, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName($order->o_currency)."</td>";
			$summa=$summa+$current_sum;
			echo "<td class=\"cur_quantity\">".number_format($current_quantity, catalogConfig::$quantity_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Measure::getInstance()->getShortName($g->i_g_measure)."</td>";
			echo "<td class=\"cur_sum\">".number_format($current_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName($order->o_currency)."</td>";
			echo "</tr> \n";
			if ($g->i_g_tax>0){
				$tax_name=base64_encode($g->i_g_tax_name);
				if (!isset($taxesInOrder[$tax_name]))$taxesInOrder[$tax_name]=0;
				$taxesInOrder[$tax_name]+=$g->i_g_tax;
			}
		}
		echo "<tr><td class=\"subtotal\" colspan=\"5\">".Text::_("Sum by order")." : </td>";
		echo "<td class=\"subtotal_sum\" colspan=\"1\">".number_format($summa, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName($order->o_currency)."</td></tr> \n";
		$total=$summa+$order->o_dt_sum;
		
		if ($order->o_dt_sum>0) {
			if ($order->o_dt_tax_val){
				$tax_name=base64_encode($order->o_dt_tax_name);
				if (!isset($taxesInOrder[$tax_name])) $taxesInOrder[$tax_name]=0;
				$taxesInOrder[$tax_name]+=$order->o_dt_tax_sum;
			}
			echo "<tr><td class=\"deliveries\" colspan=\"5\">".Text::_("Delivery sum")." : </td>";
			echo "<td class=\"deliveries_sum\" colspan=\"1\">".number_format($order->o_dt_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName($order->o_currency)."</td></tr> \n";
		}
		
		$total=$total+$order->o_pt_sum;
		if ($order->o_pt_sum>0) {
			echo "<tr><td class=\"payments\" colspan=\"5\">".Text::_("Payment commission")." : </td>";
			echo "<td class=\"payments_sum\" colspan=\"1\">".number_format($order->o_pt_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName($order->o_currency)."</td></tr> \n";
		}
		
		$total=$total-$order->o_discount_sum;
		if ($order->o_discount_sum>0) {
			if ($order->o_discount_sum>0) {
				echo "<tr><td class=\"discounts\" colspan=\"5\">".Text::_("Discount")." : </td>";
				echo "<td class=\"discounts_sum\" colspan=\"1\">".number_format($order->o_discount_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName($order->o_currency)."</td></tr> \n";
			} else {
				echo "<tr><td class=\"discounts\" colspan=\"5\">".Text::_("Fee")." : </td>";
				echo "<td class=\"discounts_sum\" colspan=\"1\">".number_format(abs($order->o_discount_sum), catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName($order->o_currency)."</td></tr> \n";
			}
		}
		if ($total!=$summa){
			echo "<tr><td class=\"subtotal\" colspan=\"5\">".Text::_("Sum")." : </td>";
			echo "<td class=\"subtotal_sum\" colspan=\"1\">".number_format($total, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName($order->o_currency)."</td></tr> \n";
		}
		if ($order->o_taxes_sum) {
			echo "<tr><td class=\"taxes\" colspan=\"5\">".Text::_("Taxes in order")." : </td>";
			echo "<td class=\"taxes_sum\" colspan=\"1\">".number_format($order->o_taxes_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName($order->o_currency)."</td></tr> \n";
			if (count($taxesInOrder)) {
				foreach($taxesInOrder as $kt=>$vt){
					echo "<tr><td class=\"taxes\" colspan=\"5\">".base64_decode($kt)." : </td>";
					echo "<td class=\"taxes_sum\" colspan=\"1\">".number_format($vt, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName($order->o_currency)."</td></tr> \n";
				}
			}
		}
		echo "<tr><td class=\"total\" colspan=\"5\">".Text::_("Total")." : </td>";
		echo "<td class=\"total_sum\" colspan=\"1\">".number_format($order->o_total_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName($order->o_currency)."</td></tr> \n";
		echo "</tbody>";
		echo "</table></div>";
		echo "</div>";
	}
}
?>