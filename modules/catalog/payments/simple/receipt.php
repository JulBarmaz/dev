<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");
/*
if(User::getInstance()->isLoggedIn()) $CompanyInfo=Userdata::getInstance(User::getInstance()->getID())->getCompany();
else $CompanyInfo=false;

if($CompanyInfo) {
	$payment_user = $CompanyInfo["surname"]." ".$CompanyInfo["firstname"]." ".$CompanyInfo["patronymic"]." ".$CompanyInfo["inn"];
} else {
	$payment_user = "";
}
*/
$payment_user = Request::getSafe("payment_user");
$payment_user_address = Request::getSafe("payment_user_address");
$payment_user_inn = Request::getSafe("payment_user_inn");
if($payment_user_inn) $payment_user_inn = "(ИНН ".$payment_user_inn.")";
?>
<style type="text/css">
body, table { font-size:12px; font-family:Tahoma, Arial, sans-serif;}
td, th {padding:2px 3px 2px 3px;}
.b_t, b_t2 {border-collapse:collapse;}
.b_t td, .b_t th {border:solid 1px #000000;}
.b_t2 {border:solid 1px #000000;}
table.big_t {width:630px;margin-left: auto; margin-right: auto;}
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
.kv {font-size:11px; font-family:Tahoma, Arial, sans-serif; }
.kv td {padding:1px 2px 0px 2px;}
.inner {padding: 5px;}
.small {font-size:5pt; border-top:solid 0.5pt #000000; text-align:center; vertical-align:top; padding:0px !important;}
.big_t {border: dotted 1px #000000; margin-top:70px; margin-bottom:70px;}
.btm {border-bottom:solid 0.5pt #000000;}
.ital {font-size:9pt; font-style:oblique; font-family:'Times New Roman', serif, Tahoma; font-weight:bold;}
.lc_size {width:180px;}
@media print {
		.printhidden, #debugger { visibility:hidden; display:none;}
}
</style>
<!-- Шаблон квитанции - начало -->
<table border="0" cellpadding="0" cellspacing="0" class="kv big_t">
  <tr>
    <td class="inner lc_size" style="border-right:solid 1px #000000; border-bottom: dotted 1px #000000;" align="center" valign="top">
			<div style="padding:5px 0 0 0;">Извещение</div>
			<div style="padding:210px 0 0 0;">Кассир</div>
	  </td>
    <td class="inner" style="border-bottom: dotted 1px #000000;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv">
    	  <tr>
      	  <td align="center" width="30%" style="font-size:6pt;">СБЕРБАНК РОССИИ ОАО<br /><span style="font-size:5pt;">Основан в 1841 году</span></td>
        	<td align="right" style="padding-right:15px; font-size:6pt;">Форма №ПД-4</td>
	      </tr>
  		</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv">
	      <tr>
  	      <td class="ital"><?php echo PAYMENT_DEFAULT_FIRM_NAME ?>&nbsp;</td>
    	  </tr>
      	<tr>
        	<td class="small">(наименование получателя платежа)</td>
	      </tr>
  	  </table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv">
			  <tr>
					<td class="ital">ИНН: <?php echo PAYMENT_DEFAULT_INN ?>&nbsp;</td>
					<td>&nbsp;</td>
					<td class="ital"><?php echo PAYMENT_DEFAULT_ACCOUNT_NUMBER ?>&nbsp;</td>
			  </tr>
			  <tr>
					<td class="small">ИНН налогового органа</td>
					<td>&nbsp;</td>
					<td class="small">(номер счета получателя платежа)</td>
			  </tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv">
			  <tr>
					<td width="5%">в</td>
					<td class="ital"><?php echo PAYMENT_DEFAULT_BANK_NAME ?>&nbsp;</td>
					<td width="5%">БИК</td>
					<td class="btm ital"><?php echo PAYMENT_DEFAULT_BIK ?>&nbsp;</td>
				</tr>
			  <tr>
	  			<td>&nbsp;</td>
					<td class="small">(наименование банка)</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
			  </tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv">
			  <tr>
					<td width="50%">Номер кор/сч банка получателя платежа</td>
					<td class="btm ital"><?php echo PAYMENT_DEFAULT_BANK_NUMBER ?>&nbsp;</td>
			  </tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv">
			  <tr>
					<td class="ital">по счету №<?php printf("%08d", $this->order->o_id)?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
			  </tr>
			  <tr>
					<td class="small">(наименование платежа)</td>
					<td>&nbsp;</td>
					<td class="small">(номер лицевого счета (код) плательщика)</td>
			  </tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv">
	  		<tr>
					<td width="20%">Ф.И.О.&nbsp;плательщика</td>
					<td class="btm ital"><?php echo $payment_user." ".$payment_user_inn;?>&nbsp;</td>
			  </tr>
			  <tr>
					<td width="20%">Адрес&nbsp;плательщика</td>
					<td class="btm ital"><?php echo $payment_user_address;?>&nbsp;</td>
			  </tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv">
			  <tr>
					<td width="20%">Сумма платежа</td>
					<td width="30%" class="btm ital"><?php printf("%.2f", $this->order->o_total_sum)?> p.</td>
					<td width="30%">Сумма платы за услуги</td>
					<td width="20%" class="btm">&nbsp;</td>
			  </tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv">
			  <tr>
					<td width="15%">Итого</td>
					<td width="30%" class="btm ital"><?php printf("%.2f", $this->order->o_total_sum)?> p.</td>
					<td width="5%">&nbsp;</td>
					<td width="25%" class="btm ital">'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'</td>
					<td width="5%">20</td>
					<td width="5%" class="btm ital">&nbsp;&nbsp;&nbsp;</td>
					<td width="15%">г.</td>
			  </tr>
	  		<tr>
	  			<td colspan="7" style="font-size:6pt;">С условиями приема указанной в платежном документе суммы в т.ч. с суммой взымаемой платы за услуги банка ознакомлен и согласен</td>
  			</tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv" style="margin-bottom:5px;">
	  		<tr>
					<td align="right" width="65%">Подпись плательщика</td>
					<td  class="btm">&nbsp;</td>
			  </tr>
			</table>
		</td>
	</tr>
	<tr>
	  <td class="inner lc_size" style="border-right:solid 1px #000000;" align="center" valign="top">
			<div style="padding:200px 0 0 0;">Квитанция</div>
			<div style="padding:10px 0 0 0;">Кассир</div>
		</td>
	  <td class="inner" style="padding-top:5px;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv">
		    <tr>
    		  <td class="ital"><?php echo PAYMENT_DEFAULT_FIRM_NAME ?>&nbsp;</td>
		    </tr>
		    <tr>
    		  <td class="small">(наименование получателя платежа)</td>
		    </tr>
		  </table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv">
		  	<tr>
					<td class="ital">ИНН: <?php echo PAYMENT_DEFAULT_INN ?>&nbsp;</td>
					<td>&nbsp;</td>
					<td class="ital"><?php echo PAYMENT_DEFAULT_ACCOUNT_NUMBER ?>&nbsp;</td>
			  </tr>
			  <tr>
					<td class="small">ИНН налогового органа</td>
					<td>&nbsp;</td>
					<td class="small">(номер счета получателя платежа)</td>
		  	</tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv">
			  <tr>
					<td width="5%">в</td>
					<td class="ital"><?php echo PAYMENT_DEFAULT_BANK_NAME ?>&nbsp;</td>
					<td width="5%">БИК</td>
					<td class="btm ital"><?php echo PAYMENT_DEFAULT_BIK ?>&nbsp;</td>
				</tr>
				<tr>
				  <td>&nbsp;</td>
					<td class="small">(наименование банка)</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
			  </tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv">
		  	<tr>
					<td width="50%">Номер кор/сч банка получателя платежа</td>
					<td class="btm ital"><?php echo PAYMENT_DEFAULT_BANK_NUMBER ?>&nbsp;</td>
		  	</tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv">
			  <tr>
					<td class="ital">по счету №<?php printf("%08d", $this->order->o_id)?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
			  </tr>
			  <tr>
					<td class="small">(наименование платежа)</td>
					<td>&nbsp;</td>
					<td class="small">(номер лицевого счета (код) плательщика)</td>
			  </tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv">
			  <tr>
					<td width="20%">Ф.И.О.&nbsp;плательщика</td>
					<td class="btm ital"><?php echo $payment_user." ".$payment_user_inn ;?>&nbsp;</td>
			  </tr>
				<tr>
					<td width="20%">Адрес&nbsp;плательщика</td>
					<td class="btm ital"><?php echo $payment_user_address;?></td>
			  </tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv">
			  <tr>
					<td width="20%">Сумма платежа</td>
					<td width="30%" class="btm ital"><?php printf("%.2f", $this->order->o_total_sum)?> p.</td>
					<td width="30%">Сумма платы за услуги</td>
					<td width="20%" class="btm">&nbsp;</td>
		  	</tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv">
		  	<tr>
					<td width="15%">Итого</td>
					<td width="30%" class="btm ital"><?php printf("%.2f", $this->order->o_total_sum)?> p.</td>
					<td width="5%">&nbsp;</td>
					<td width="25%" class="btm ital">'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'</td>
					<td width="5%">20</td>
					<td width="5%" class="btm ital">&nbsp;&nbsp;&nbsp;</td>
					<td width="15%">г.</td>
			  </tr>
			  <tr>
			  	<td colspan="7" style="font-size:6pt;">С условиями приема указанной в платежном документе суммы в т.ч. с суммой взымаемой платы за услуги банка ознакомлен и согласен</td>
		  	</tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kv" style="margin-bottom:5px;">
		  	<tr>
					<td align="right" width="65%">Подпись плательщика</td>
					<td class="btm">&nbsp;</td>
			  </tr>
			</table>
		</td>
	</tr>
</table>
<table width="630" border="0" cellspacing="0" cellpadding="0" class="t_m">
  <tr>
    <td style="padding: 10px;">Внимание ! Оплата квитанции третьими лицами не допустима. Зачисление денег на наш расчетный счет осуществляется в течение 2-3 банковских дней, для ускорения зачисления вышлите по факсу копию или по e-mail скан оплаченной квитанции.</td>
  </tr>
</table>
