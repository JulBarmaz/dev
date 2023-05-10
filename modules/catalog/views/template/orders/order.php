<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

if ($this->override_css) { ?>
<style type=text/css>
	h1 { font-family:arial;font-size:14px; }
	div.one_order{ width:780px !important; font-size:12px; font-family:Tahoma, Arial, sans-serif;  margin-left: auto; margin-right: auto;}
		
</style>
<?php }
$taxesInOrder=array();
$payment=$this->pt_class;
$delivery=$this->dt_class;
$order=$this->order;
$items=$this->order_items;
$summa=0; $total=0; $counter=0;
if (is_object($order) && ($items) && count($items)) { 
	$currency_text=Currency::getShortName($order->o_currency);
	$vendor=Vendor::getInstance()->getVendor($order->o_vendor);
	/* hide prices check start */
	$cols4name = 3;
	$cols4price = 2;
	$cols4sum = 2;
	$show_prices = true;
	if(catalogConfig::$hide_prices) {
		$test_sum = 0;
		foreach($items as $g) { 
			$test_sum = $test_sum + $g->i_g_sum;
		}
		if(!$test_sum){
			$cols4name = $cols4name + $cols4price + $cols4sum;
			$show_prices = false;
		}
	}
	/* hide prices check stop */
?>
	<div class="one_order">
		<h1 class="title"><?php echo Text::_("Order")." № ".$order->o_id." ".Text::_("from")." ".Date::GetdateRus($order->o_date); ?></h1>
		<div class="basket-vendor"><b><?php echo Text::_("Vendor").":</b> ".$vendor->v_name; ?></div>
		<div class="submit_selector">
			<b><?php echo Text::_("Delivery type"); ?>: </b><?php echo $order->o_dt_name; ?>
			<?php
			if ($delivery){
				$delivery_data = $delivery->getDecodedData($order->o_dt_data);
				echo $delivery->renderInfo($delivery_data);
			}
			?>
		</div>
		<div class="order_row">
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
		<div class="order_row">
			<b><?php echo Text::_("Comments"); ?>: </b><?php echo htmlspecialchars($order->o_comments); ?>
		</div>
		<?php } ?>
		<div class="basket-header hidden-xs">
			<div class="row">
				<div class="basket-header-cell basket-header-points col-sm-1">
					<?php echo Text::_('NPP'); ?>
				</div>
				<div class="basket-header-cell basket-header-sku col-sm-2">
					<?php echo Text::_('Articul'); ?>
				</div>
				<div class="basket-header-cell basket-header-name col-sm-<?php echo $cols4name; ?>">
					<?php echo Text::_('Goods title'); ?>
				</div>
				<?php if($show_prices){?>
					<div class="basket-header-cell basket-header-price col-sm-<?php echo $cols4price; ?>">
						<?php echo Text::_('Price'); ?>
					</div>
				<?php } ?>
				<div class="basket-header-cell basket-header-quantity col-sm-2">
					<?php echo Text::_('Quantity'); ?>
				</div>
				<?php if($show_prices){?>
					<div class="basket-header-cell basket-header-sum col-sm-<?php echo $cols4sum; ?>">
						<?php echo Text::_('Sum'); ?>
					</div>
				<?php } ?>
			</div>
		</div>
		<div class="basket-body">
			<?php foreach($items as $g) { ?>
				<?php $counter++; ?>
				<?php if ($g->i_g_quantity) { ?>
				<div class="basket-row">
					<div class="row">
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
						<div class="basket-row-cell basket-row-points col-sm-1">
							<span class="hidden-sm hidden-md hidden-lg"><?php echo Text::_('NPP'); ?>: </span><?php echo $counter; ?>
						</div>
						<div class="basket-row-cell basket-row-sku col-sm-2">
							<span class="hidden-sm hidden-md hidden-lg"><?php echo Text::_('Articul'); ?>: </span><?php echo $g->i_g_sku; ?>
						</div>
						<div class="basket-row-cell basket-row-name col-sm-<?php echo $cols4name; ?>">
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
						<?php if($show_prices){?>
							<div class="basket-row-cell basket-row-price col-sm-<?php echo $cols4price; ?>">
								<span class="hidden-sm hidden-md hidden-lg"><?php echo Text::_('Price'); ?>: </span><?php echo number_format($current_price, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR); ?>
							</div>
						<?php } ?>
						<div class="basket-row-cell basket-row-quantity col-sm-2">
							<span class="hidden-sm hidden-md hidden-lg"><?php echo Text::_('Quantity'); ?>: </span>
							<?php echo number_format($current_quantity, catalogConfig::$quantity_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Measure::getInstance()->getShortName($g->i_g_measure); ?>
						</div>
						<?php if($show_prices){?>
							<div id="sum_<?php echo $g->i_g_id;?>" class="basket-row-cell basket-row-sum col-sm-<?php echo $cols4sum; ?>">
								<span class="hidden-sm hidden-md hidden-lg"><?php echo Text::_('Sum'); ?>: </span><?php echo number_format($current_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR); ?>
							</div>
						<?php } ?>
					</div>
				</div>
				<?php } ?>
			<?php } ?>
		</div>
		<div class="basket-footer">
			<div class="row">
				<?php if ($order->o_points>0) { ?>
					<div class="basket-footer-cell basket-footer-points col-xs-6 col-sm-4 text-right">
						<?php echo Text::_('Total points'); ?>
					</div>
					<div class="basket-footer-cell basket-footer-points col-xs-6 col-sm-1 text-right">
						<?php echo $order->o_points; ?>
					</div>
				<?php } else { ?>
					<div class="basket-footer-cell hidden-xs col-xs-12 col-sm-5 text-right"></div>
				<?php } ?>
				<?php if($summa){ ?>
					<div class="basket-footer-cell col-xs-6 col-sm-4 text-right">
						<?php echo Text::_('Sum'); ?>
					</div>
					<div class="basket-footer-cell col-xs-6 col-sm-3 text-right">
						<?php echo number_format($summa, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
					</div>
				<?php } else {?>
					<div class="basket-footer-cell hidden-xs col-xs-12 col-sm-7 text-right"></div>
				<?php }?>
			</div>
			<?php $total = $summa; ?>
			<div class="subtotals">
				<?php if ($order->o_dt_sum && $order->o_dt_sum>0) { 
					if ($order->o_dt_tax_val){
						$tax_name=base64_encode($order->o_dt_tax_name);
						if (!isset($taxesInOrder[$tax_name])) $taxesInOrder[$tax_name]=0;
						$taxesInOrder[$tax_name]+=$order->o_dt_tax_sum;
					}
				?>
				<?php if($order->o_dt_sum){ ?>
					<div class="row">
						<div class="basket-footer-cell col-xs-6 col-sm-9 text-right">
							<?php echo Text::_("Delivery sum"); ?>
						</div>
						<div class="basket-footer-cell col-xs-6 col-sm-3 text-right">
							<?php echo number_format($order->o_dt_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
						</div>
					</div>
				<?php } ?>
				<?php $total = $total + $order->o_dt_sum; ?>
				<?php } ?>
				<?php if ($order->o_pt_sum && $order->o_pt_sum>0) { ?>
				<div class="row pt-sum">
					<div class="basket-footer-cell col-xs-6 col-sm-9 text-right">
						<?php echo Text::_("Payment commission"); ?>
					</div>
					<div class="basket-footer-cell col-xs-6 col-sm-3 text-right">
						<?php echo number_format($order->o_pt_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
					</div>
				</div>
				<?php $total = $total + $order->o_pt_sum; ?>
				<?php } ?>
				<?php if (floatval($order->o_discount_sum)) { ?>
				<div class="row discount-sum">
					<?php if ($order->o_discount_sum>0) { ?>
						<div class="basket-footer-cell col-xs-6 col-sm-9 text-right">
							<?php echo Text::_("Discount"); ?>
						</div>
					<?php } else { ?>
						<div class="basket-footer-cell col-xs-6 col-sm-9 text-right">
							<?php echo Text::_("Fee"); ?>
						</div>
					<?php } ?>
					<div class="basket-footer-cell col-xs-6 col-sm-3 text-right">
						<?php echo number_format($order->o_discount_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
					</div>
				</div>
				<?php $total = $total - $order->o_discount_sum; ?>
				<?php } ?>
				<?php if($total != $summa){ ?>
				<div class="row total-sum">
					<div class="basket-footer-cell col-xs-6 col-sm-9 text-right">
						<?php echo Text::_("Total"); ?>
					</div>
					<div class="basket-footer-cell col-xs-6 col-sm-3 text-right">
						<?php echo number_format($total, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
					</div>
				</div>
				<?php } ?>
				<?php if (floatval($order->o_taxes_sum)) { ?>
					<div class="row">
						<div class="basket-footer-cell col-xs-6 col-sm-9 text-right">
							<?php echo Text::_("Taxes in order"); ?>
						</div>
						<div class="basket-footer-cell col-xs-6 col-sm-3 text-right">
							<?php echo number_format($order->o_taxes_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
						</div>
					</div>
					<?php if (count($taxesInOrder)) { ?>
						<?php foreach($taxesInOrder as $kt=>$vt){ ?>
							<div class="row">
								<div class="basket-footer-cell col-xs-6 col-sm-9 text-right">
									<?php print base64_decode($kt); ?>
								</div>
								<div class="basket-footer-cell col-xs-6 col-sm-3 text-right">
									<?php echo number_format($vt, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
								</div>
							</div>
						<?php } ?>
					<?php } ?>
				<?php } ?>
				<?php if(floatval($order->o_total_sum)>0){ ?>
				<div class="row grand-total-sum">
					<div class="basket-footer-cell col-xs-6 col-sm-9 text-right">
						<?php echo Text::_("Grand total"); ?>
					</div>
					<div class="basket-footer-cell col-xs-6 col-sm-3 text-right">
						<?php echo number_format($order->o_total_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
					</div>
				</div>
				<?php } ?>
			</div>
			<?php if($this->controls) { ?>
			<div class="buttons">
				<a id="print_but" target="_blank" class="linkButton btn btn-info" href="<?php echo Router::_("index.php?option=print&module=catalog&view=orders&layout=print&order_id=".$order->o_id."&order_hash=".$order->o_hash); ?>"><?php echo Text::_('Print'); ?></a>
				<?php if ($order->o_paid==0) { ?><a id="pay_but" class="linkButton btn btn-info" href="<?php echo Router::_("index.php?module=catalog&view=orders&layout=payment&order_id=".$order->o_id."&order_hash=".$order->o_hash); ?>"><?php echo Text::_('Pay'); ?></a><?php } ?>
			</div>
			<?php } ?>
		</div>
	</div>
<?php } else { ?>
	<h1><?php echo Text::_("Order not found"); ?></h1>
<?php } ?>
