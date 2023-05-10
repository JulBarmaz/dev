<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$taxesInOrder=array();
$payment=$this->pt_class;
$delivery=$this->dt_class;
$order=$this->order;
$items=$this->order_items;
$summa=0; $total=0; $counter=0;
$currency_text=Currency::getShortName($order->o_currency);
$vendor=Vendor::getInstance()->getVendor($order->o_vendor);
?>
<div style="border:1px solid #ddd;padding:8px;">
	<h1><?php echo Text::_("Order")." № ".$order->o_id." ".Text::_("from")." ".Date::GetdateRus($order->o_date); ?></h1>
	<div style="margin-bottom: 12px;"><b><?php echo Text::_("Vendor").":</b> ".$vendor->v_name; ?></div>
	<div style="margin-bottom: 12px;">
		<b><?php echo Text::_("Delivery type"); ?>: </b><?php echo $order->o_dt_name; ?>
		<?php
		if ($delivery){
			$delivery_data = $delivery->getDecodedData($order->o_dt_data);
			echo $delivery->renderInfo($delivery_data);
		}
		?>
	</div>
	<div style="margin-bottom: 12px;">
		<b><?php echo Text::_("Payment type"); ?>: </b><?php echo $order->o_pt_name; ?>
		<?php 
		if ($payment) {
			$payment_data = $payment->getDecodedData($order->o_pt_data);
			echo $payment->renderInfo($payment_data);
		}
		?>
	</div>
	<?php 
	$ud = new catalogUserdata(0);
	$userdata = $ud->getDecodedData($order->o_userdata);
	if(is_array($userdata)){
		$_userdata_arr=array();
		if (array_key_exists("userdata_person", $userdata) && $userdata["userdata_person"])	$_userdata_arr[]= "<b>".Text::_("Contact person").":</b> ".$userdata["userdata_person"];
		if (array_key_exists("userdata_email", $userdata) && $userdata["userdata_email"]) $_userdata_arr[]= "<b>".Text::_("Contact email").":</b> ".$userdata["userdata_email"];
		if (array_key_exists("userdata_phone", $userdata) && $userdata["userdata_phone"]) $_userdata_arr[]= "<b>".Text::_("Contact phone").":</b> ".$userdata["userdata_phone"];
		if(count($_userdata_arr)) { ?><div class="order_row"><?php echo implode("<br />", $_userdata_arr); ?></div><?php }
	}
	?>
	<?php if(trim(strip_tags($order->o_comments))) { ?>
	<div style="margin-bottom: 12px;">
		<b><?php echo Text::_("Comments"); ?>: </b><?php echo htmlspecialchars($order->o_comments); ?>
	</div>
	<?php } ?>
	<div style="display:table;border-top:1px solid #ddd;border-left:1px solid #ddd; margin-bottom: 12px;width:100%;">
		<div style="display:table-row;background-color: #f2f2f2;">
			<div style="display:table-cell;white-space:nowrap;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
				<?php echo Text::_('NPP'); ?>
			</div>
			<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
				<?php echo Text::_('Articul'); ?>
			</div>
			<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
				<?php echo Text::_('Goods title'); ?>
			</div>
			<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
				<?php echo Text::_('Price'); ?>
			</div>
			<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
				<?php echo Text::_('Quantity'); ?>
			</div>
			<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
				<?php echo Text::_('Sum'); ?>
			</div>
		</div>
		<?php foreach($items as $g) { ?>
			<?php $counter++; ?>
			<?php if ($g->i_g_quantity) { ?>
			<div style="display:table-row;">
				<?php 
				$current_price=$g->i_g_price;
				$current_quantity=$g->i_g_quantity;
				$current_sum=$g->i_g_sum;
				$summa=$summa+$current_sum;
				if ($g->i_g_tax>0){
					$tax_name=base64_encode($g->i_g_tax_name);
					if (!isset($taxesInOrder[$tax_name]))$taxesInOrder[$tax_name]=0;
					$taxesInOrder[$tax_name]+=$g->i_g_tax;
				}
				?>
				<div style="display:table-cell;text-align:center;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
					<?php echo $counter; ?>
				</div>
				<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
					<?php echo $g->i_g_sku; ?>
				</div>
				<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
					<?php 
					echo $g->i_g_name;
					if($g->i_g_options_text){
						if(Portal::getInstance()->inPrintMode()){
							$i_g_options_title="";
							$i_g_options_text=$g->i_g_options_text;
						} else {
							$i_g_options_title=$g->i_g_options_text;
							$i_g_options_text=mb_substr($i_g_options_title, 0, siteConfig::$shortTextLength)."...";
						}
						echo  "<span title=\"".$i_g_options_title."\" class=\"basket-row-options\">".$i_g_options_text."</span>";
					}
					?>
				</div>
				<div style="display:table-cell;white-space:nowrap;text-align:right;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
					<?php echo number_format($current_price, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR); ?>
				</div>
				<div style="display:table-cell;white-space:nowrap;text-align:right;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
					<?php echo number_format($current_quantity, catalogConfig::$quantity_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Measure::getInstance()->getShortName($g->i_g_measure); ?>
				</div>
				<div id="sum_<?php echo $g->i_g_id;?>" style="display:table-cell;white-space: nowrap;text-align:right;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
					<?php echo number_format($current_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR); ?>
				</div>
			</div>
			<?php } ?>
		<?php } ?>

	</div>
	<div style="display:table;border-top:1px solid #ddd;border-left:1px solid #ddd; margin-bottom: 12px;width:100%;">
		<div style="display:table-row;background-color: #f2f2f2;">
			<?php if ($order->o_points>0) { ?>
				<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
					<?php echo Text::_('Total points'); ?>
				</div>
				<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
					<?php echo $order->o_points; ?>
				</div>
			<?php } else { ?>
				<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;"></div>
			<?php } ?>
			<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
				<?php echo Text::_('Sum'); ?>
			</div>
			<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
				<?php echo number_format($summa, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
			</div>
		</div>
	</div>
	<?php $total = $summa; ?>
	<div style="display:table;border-top:1px solid #ddd;border-left:1px solid #ddd; margin-bottom: 12px;width:100%;">
		<?php if (($order->o_dt_sum)&&(($order->o_dt_sum)>0)) { 
			if ($order->o_dt_tax_val){
				$tax_name=base64_encode($order->o_dt_tax_name);
				if (!isset($taxesInOrder[$tax_name])) $taxesInOrder[$tax_name]=0;
				$taxesInOrder[$tax_name]+=$order->o_dt_tax_sum;
			}
		?>
		<div style="display:table-row;">
			<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
				<?php echo Text::_("Delivery sum"); ?>
			</div>
			<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
				<?php echo number_format($order->o_dt_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
			</div>
		</div>
		<?php $total = $total + $order->o_dt_sum; ?>
		<?php } ?>
		<?php if (($order->o_pt_sum)&&($order->o_pt_sum>0)) { ?>
		<div style="display:table-row;">
			<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
				<?php echo Text::_("Payment commission"); ?>
			</div>
			<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
				<?php echo number_format($order->o_pt_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
			</div>
		</div>
		<?php $total = $total + $order->o_pt_sum; ?>
		<?php } ?>
		<?php if (floatval($order->o_discount_sum)) { ?>
		<div style="display:table-row;">
			<?php if ($order->o_discount_sum>0) { ?>
				<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
					<?php echo Text::_("Discount"); ?>
				</div>
			<?php } else { ?>
				<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
					<?php echo Text::_("Fee"); ?>
				</div>
			<?php } ?>
			<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
				<?php echo number_format($order->o_discount_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
			</div>
		</div>
		<?php $total = $total - $order->o_discount_sum; ?>
		<?php } ?>
		<?php if($total != $summa){ ?>
		<div style="display:table-row;">
			<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
				<?php echo Text::_("Total"); ?>
			</div>
			<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
				<?php echo number_format($total, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
			</div>
		</div>
		<?php } ?>
		<?php if (floatval($order->o_taxes_sum)) { ?>
			<div style="display:table-row;">
				<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
					<?php echo Text::_("Taxes in order"); ?>
				</div>
				<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
					<?php echo number_format($order->o_taxes_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
				</div>
			</div>
			<?php if (count($taxesInOrder)) { ?>
				<?php foreach($taxesInOrder as $kt=>$vt){ ?>
					<div style="display:table-row;">
						<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
							<?php print base64_decode($kt); ?>
						</div>
						<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
							<?php echo number_format($vt, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
						</div>
					</div>
				<?php } ?>
			<?php } ?>
		<?php } ?>
		<div style="display:table-row;">
			<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
				<?php echo Text::_("Grand total"); ?>
			</div>
			<div style="display:table-cell;padding:6px 4px;border-bottom:1px solid #ddd;border-right:1px solid #ddd;text-align:right;">
				<?php echo number_format($order->o_total_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
			</div>
		</div>
	</div>		

</div>
