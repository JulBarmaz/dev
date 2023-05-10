<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$taxesInOrder=array();
/*
if(User::getInstance()->isLoggedIn()) $CompanyInfo=Userdata::getInstance(User::getInstance()->getID())->getCompany();
else $CompanyInfo=false;
if($CompanyInfo) {
	$payment_user = $CompanyInfo["surname"]." ".$CompanyInfo["firstname"]." ".$CompanyInfo["patronymic"];
	if ($CompanyInfo["inn"]) $payment_user_inn = "ИНН ".$CompanyInfo["inn"];
	else $payment_user_inn = "";
} else {
	$payment_user = "";
	$payment_user_inn = "";
}
*/
$payment_user = Request::getSafe("payment_user");
$payment_user_address = Request::getSafe("payment_user_address");
$payment_user_inn = Request::getSafe("payment_user_inn");
if($payment_user_inn) $payment_user_inn = "ИНН ".$payment_user_inn;
?>
<style type="text/css">
	#schet2{ width:700px; margin-left: auto; margin-right: auto;}
	#schet, #schet2 { font-size:12px; font-family:Tahoma, Arial, sans-serif;}
	td, th {padding:2px 3px 2px 3px;}
	.b_t, b_t2 {border-collapse:collapse;}
	.b_t td, .b_t th {border:solid 1px #000000;}
	.b_t2 {border:solid 1px #000000;}
	.big {font-size:14px; font-weight:bold;}
	.middle {font-weight:bold;}
	.head1 {border-top-width: 2px; border-top-style: solid; border-top-color: #000000; font-size:9px;}
	.head1 a {font-size:9px;}
	.n_btm_b {border-bottom: none 0px #FFFFFF;}
	.t_m {margin:10px 0 0 0;}
	.w_12 {width:12%;}
	.w_150 {width:150px;}
	.w_120 {width:120px;}
	.ErrMsg {font-size: 18px;color: #d71e1e;}
	.kv {font-size:11px; font-family:Tahoma, Arial, sans-serif;}
	.kv td {padding:1px 2px 0px 2px;}
	.inner {padding: 5px;}
	.small {font-size:5pt; border-top:solid 0.5pt #000000; text-align:center; vertical-align:top; padding:0px !important;}
	.big_t {border: dotted 1px #000000; margin-top:70px; margin-bottom:70px;}
	.btm {border-bottom:solid 0.5pt #000000;}
	.ital {font-size:9pt; font-style:oblique; font-family:'Times New Roman', serif, Tahoma; font-weight:bold;}
	.lc_size {width:180px;}
	#AutoNumber4{ width:100%; }
	#pechat		{display: block;	}
	#pechat1	{display: none;	}
	@media print {
		.printhidden, #debugger { visibility:hidden; display:none;}
	}
</style>
<div id="schet2" align="center">
	<table border="0" cellpadding="8" cellspacing="0" width="100%" id="AutoNumber1" style="border-collapse: collapse;">
		<tr>
			<td>
				<table width="100%">
					<tr>
						<td><b><?php echo PAYMENT_DEFAULT_FIRM_NAME; ?>, ИНН: <?php echo PAYMENT_DEFAULT_INN; ?>, ОГРН: <?php echo PAYMENT_DEFAULT_OGRN; ?></b><br />
								Адрес: <?php echo PAYMENT_DEFAULT_FIRM_ADDRESS; ?><br />
								тел/факс: <?php echo PAYMENT_DEFAULT_FIRM_PHONE; ?>
						</td>
						<td valign="middle" align="right">
							&nbsp;  <!--	<img hspace=10 src="images/logo.jpg"> -->
						</td>
					</tr>
				</table>
				<b>Образец заполнения платежного поручения:</b><br /><br />
				<div align="center">
					<table cellpadding="2" cellspacing="0" id="AutoNumber2" width="100%" style="border-collapse: collapse; border:1px solid #111111;">
						<tr>
							<td>ИНН &nbsp;<?php echo PAYMENT_DEFAULT_INN; ?></td>
							<td>КПП </td>
							<td>&nbsp;<?php echo PAYMENT_DEFAULT_KPP; ?></td>
						</tr>
						<tr>
							<td>Получатель:
								<br /><?php echo PAYMENT_DEFAULT_FIRM_NAME; ?>&quot;</td>
							<td>Сч. N</td>
							<td>&nbsp;<?php echo PAYMENT_DEFAULT_ACCOUNT_NUMBER; ?></td>
						</tr>
						<tr>
							<td rowspan="2">Банк получателя:<br /><?php echo PAYMENT_DEFAULT_BANK_NAME; ?><br /></td>
							<td>БИК</td>
							<td>&nbsp;<?php echo PAYMENT_DEFAULT_BIK; ?></td>
						</tr>
						<tr>
							<td>Сч. N</td>
							<td>&nbsp;<?php echo PAYMENT_DEFAULT_BANK_NUMBER; ?></td>
						</tr>
					</table>
				</div>

				<h2 align="center">СЧЕТ N <?php printf("%08d", $this->order->o_id); ?> от <?php echo Date::GetdateRus($this->order->o_date); ?></h2>
				<p align="left">Плательщик: <?php echo $payment_user.($payment_user_inn ? ",&nbsp;".$payment_user_inn : "")?>
					<?php echo ($payment_user_address ? "<br />Адрес пдлательщика: ".$payment_user_address : "");?>
				</p>
				<div align="center">
					<table cellpadding="2" cellspacing="0" width="100%" id="AutoNumber3" style="border-collapse: collapse; border:1px solid #000000;" >
						<tr>
							<td style="border:1px solid #000000;">&nbsp;&nbsp;&nbsp;N&nbsp;&nbsp;&nbsp;</td>
							<td style="border:1px solid #000000;">Наименование</td>
							<td style="border:1px solid #000000;" align="center">Единица<br />изме-<br />рения</td>
							<td style="border:1px solid #000000;" align="center">Кол-во</td>
							<td style="border:1px solid #000000;" align="center">Цена</td>
							<td style="border:1px solid #000000;" align="center">Сумма</td>
						</tr>
<?php
			 			$nppp=0;  $total_summa=0;
						if (count($this->items)>0) {
							foreach	($this->items as $grow) {								$nppp=$nppp+1;
								$cur_summa=$grow->i_g_quantity*$grow->i_g_price;
								$total_summa=$total_summa+$cur_summa;
								if ($grow->i_g_tax>0){
									$tax_name=base64_encode($grow->i_g_tax_name);
									if (!isset($taxesInOrder[$tax_name]))$taxesInOrder[$tax_name]=0;
									$taxesInOrder[$tax_name]+=$grow->i_g_tax;
								}
								
?>
								<tr>
									<td style="border:1px solid #000000;" align="center"><?php echo $nppp; ?></td>
									<td style="border:1px solid #000000;"><?php echo $grow->i_g_name; ?></td>
									<td style="border:1px solid #000000;" align="center"><?php echo Measure::getInstance()->getShortName($grow->i_g_measure); ?></td>
									<td style="border:1px solid #000000;" align="center"><?php echo $grow->i_g_quantity; ?></td>
									<td style="border:1px solid #000000; white-space:nowrap;" align="center"><?php printf("%01.2f", $grow->i_g_price); ?></td>
									<td style="border:1px solid #000000; white-space:nowrap;" align="right"><?php printf("%01.2f", $cur_summa); ?></td>
								</tr>
<?php
					    }
	    			}
?>
						<tr>
							<td colspan="5" style="border:1px solid #000000; white-space:nowrap;"><p align="right">Итого:</p></td>
							<td style="border:1px solid #000000; white-space:nowrap;" align="right"><?php printf("%01.2f", $total_summa); ?></td>
						</tr>
						<!-- Стоимость доставки и плата за отгрузку -->
<?php	      $shipping_total = $this->order->o_dt_sum;
    	      if ($shipping_total>0) {
				if ($this->order->o_dt_tax_val){
					$tax_name=base64_encode($this->order->o_dt_tax_name);
					if (!isset($taxesInOrder[$tax_name])) $taxesInOrder[$tax_name]=0;
					$taxesInOrder[$tax_name]+=$this->order->o_dt_tax_sum;
				}
?>

		        <tr>
		          <td colspan="5" align="right"><?php echo $this->order->o_dt_name ?> :</td>
	  	        <td align="right">
								<?php	printf("%01.2f", $shipping_total); ?>
		          </td>
		        </tr>

						<!-- Налог на стоимость заказа -->
<?php       }
				  	if ($this->order->o_discount_sum != 0) {
?>
		          <tr>
	              <td colspan="5" align="right">
<?php
	   		        if( $this->order->o_discount_sum > 0) echo Text::_('Discount');
  	           		else echo Text::_('Fee');
?>
	              </td>
	              <td align="right">
<?php                printf("%01.2f", abs($this->order->o_discount_sum)); ?>
              	</td>
		          </tr>
<?php				}
						if ($this->order->o_taxes_sum>0) {
							echo "<tr><td colspan=\"5\" align=\"right\">".Text::_("Taxes in order")." : "."</td>";
							echo "<td align=\"right\">".number_format($this->order->o_taxes_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."</td></tr> \n";
							if (count($taxesInOrder)) {
								foreach($taxesInOrder as $kt=>$vt){
									echo "<tr><td colspan=\"5\" align=\"right\">".base64_decode($kt)." : "."</td>";
									echo "<td align=\"right\">".number_format($vt, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."</td></tr> \n";
								}
							}
						}
					  
						$total_summa = $this->order->o_total_sum;
?>
            <!-- ИТОГО -->
						<tr>
							<td colspan="5" style="border:1px solid #000000; white-space:nowrap;"><p align="right">Всего к оплате:</p></td>
							<td style="border:1px solid #000000; white-space:nowrap;" align="right"><?php printf("%01.2f", $total_summa) ; ?></td>
						</tr>
					</table>
				</div>
				<p><b>Всего на сумму: &nbsp; <?php printf("%01.2f", $total_summa) ; ?></b>
				<br />(<?php echo RusText::SumInRus($total_summa); ?>.)
				</p>
				<p>&nbsp;</p>
				<table border="0" cellpadding="0" cellspacing="1" id="AutoNumber4">
					<tr>
						<td valign="top" style="white-space:nowrap;"><br />&nbsp;Генеральный директор&nbsp;&nbsp;&nbsp;&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;Главный бухгалтер&nbsp;</td>
						<td><img alt="Место печати" id="pechat" src="<?php echo PAYMENT_PECHAT_IMAGE; ?>" border="0" /><img alt="pechat1" id="pechat1" src="images/pechat1.gif" border="0" /></td>
						<td valign="top" style="white-space:nowrap;"><br />&nbsp;&nbsp;&nbsp;&nbsp;<?php echo PAYMENT_DEFAULT_DIRECTOR; ?><br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;&nbsp;&nbsp;&nbsp;<?php echo PAYMENT_DEFAULT_GLAVBUH; ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>