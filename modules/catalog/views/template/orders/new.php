<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

Portal::getInstance()->addScript("/redistribution/jquery.plugins/jquery.maskedinput.min.js");
$_js='
	$(document).ready(function(){
		$("#userdata_phone").mask("+9(999) 999-9999");
		calculateDelivery(1);
	});';
Portal::getInstance()->addScriptDeclaration($_js);
?>
<h1 class="title order_title"><?php echo Text::_("Order registration"); ?></h1>
<?php if ($this->count_vendor_goods) { ?>
	<div id="order_data" class="full_basket"><?php echo Basket::getInstance()->modifyBasket(0, true, false,true); ?></div>
	<?php if(!$this->not_enough_sum && !$this->not_enough_quantity) { ?>
		<form id="dt_selector" name="dt_selector" action="<?php echo Router::_("index.php"); ?>" method="post" >
			<?php if (count($this->pt_list)>1||(count($this->pt_list)==1 && count($this->dt_list)>0)) {
				echo "<div class=\"selector_pane\">";
				echo "	<div id=\"payment_selector\">";
				echo HTMLControls::renderLabelField("payment_type", Text::_("Payment type")." : ", false, "", "payment");
				
				if (count($this->pt_list)){
					foreach ($this->pt_list as $k=>$obj){
						$current_pt = catalogPayment::getPaymentClass($obj->pt_id);
						$obj->pt_price=$current_pt->calculate(0);
						if($obj->pt_price>0){
							$this->pt_list[$k]->pt_name.=" (".number_format(Currency::getInstance()->convert($obj->pt_price, $obj->pt_currency), catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR, DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName(DEFAULT_CURRENCY).")";
						}
						if($obj->pt_comments){
							$this->pt_list[$k]->pt_name.="<span class=\"payment_comment\">".$obj->pt_comments."</span>";
						}
						if($obj->pt_logo){
							$filename=BARMAZ_UF_PATH."catalog".DS."pts".DS.Files::splitAppendix($obj->pt_logo,true);
							if (Files::isImage($filename)) {
								$filelink=BARMAZ_UF."/catalog/pts/".Files::splitAppendix($obj->pt_logo);
								$this->pt_list[$k]->pt_name.="<img class=\"pts_logo\" src=\"".$filelink."\" alt=\"\">";
							}
						}
					}
				}
				if (count($this->pt_list)==1){
					echo HTMLControls::renderLabelField(false,$this->pt_list[0]->pt_name);
					echo HTMLControls::renderHiddenField('payment_type',$this->pt_list[0]->pt_id);
				} else {
					echo HTMLControls::renderRadioGroup('payment_type', '', 'pt_id', 'pt_name', $this->pt_list, $this->pt_selected, "setDeliveryList()","", true);
				}
				
				echo "	</div>";
				echo "</div>";
				echo "<div class=\"selector_pane\">";
				echo "	<div id=\"delivery_selector\">";
				$this->includeLayout("dts");
				echo "	</div>";
				echo "</div>";
			}
			if ($this->err_message) echo "<h4 class=\"order_error\">".$this->err_message."</h4>";
			/* 
			 ** это уже общая форма, а не только доставки
			 ** название не меняем, иначе полетит у тех у кого свои шаблоны заказа
			 */
			?><div id="delivery_form"><?php echo $this->delivery_form; ?></div><?php 
			if (count($this->pt_list)>1||(count($this->pt_list)==1 && count($this->dt_list)>0)) {
				$_html = "";
				if (User::getInstance()->isLoggedIn()){
					$default_person=User::getInstance()->getNickname();
					$default_email=User::getInstance()->getEmail();
					$default_phone="";
				} else {
					$default_person="";
					$default_email="";
					$default_phone="";
				}
				if (Module::getInstance()->getParam("require_person")) $_html.="<div class=\"row\"><div class=\"col-sm-5\">".HTMLControls::renderLabelField("person", Text::_("Contact person"))."</div><div class=\"col-sm-7\">".HTMLControls::renderInputText("userdata_person", Request::getSafe("userdata_person",$default_person),100,100, "userdata_person", "", false, true, "", array("onchange"=>"orderUserdataChanged(this);"))."</div></div>";
				if (Module::getInstance()->getParam("require_email")) $_html.="<div class=\"row\"><div class=\"col-sm-5\">".HTMLControls::renderLabelField("email", Text::_("Contact email"))."</div><div class=\"col-sm-7\">".HTMLControls::renderInputText("userdata_email", Request::getSafe("userdata_email",$default_email),100,100, "userdata_email", "", false, true, "", array("onchange"=>"orderUserdataChanged(this);"))."</div></div>";
				if (Module::getInstance()->getParam("require_phone")) $_html.="<div class=\"row\"><div class=\"col-sm-5\">".HTMLControls::renderLabelField("phone", Text::_("Contact phone"))."</div><div class=\"col-sm-7\">".HTMLControls::renderInputText("userdata_phone", Request::getSafe("userdata_phone",$default_phone),100,100, "userdata_phone", "", false, true, "", array("onchange"=>"orderUserdataChanged(this);"))."</div></div>";
				if($_html) {
					echo "<fieldset id=\"common_contacts\"><legend>".Text::_("Contacts")."</legend>";
					echo $_html;
					echo "</fieldset>";
				}
				echo "<div class=\"submit_selector\">";
				echo HTMLControls::renderLabelField(false,Text::_("Order comments"));
				echo HTMLControls::renderBBCodeEditor("order_comments","order_comments","",50,3);
				echo "</div>";
				
				echo "<div id=\"info_container\"class=\"\">";
				// Контейнер для вывода финальной информации
				echo "</div>";
				
				/* catalog rules start */
				echo"<div class=\"privacy_policy_block\">";
				$sr_art=Module::getHelper('article','article')->getArticle(intval(catalogConfig::$catalog_rules_article));
				echo "<input class=\"commonEdit required\" type=\"checkbox\" name=\"i_agree\" value=\"1\" required=\"required\" />"
						."&nbsp;".Text::_('I have read and agreed with')
						." <a rel=\"nofollow\" class=\"relpopuptext\" href=\"".Router::_("index.php?module=article&amp;view=read&amp;psid=".$sr_art->a_id."&amp;alias=".$sr_art->a_alias."&amp;notmpl=1")."\">".Text::_('shop rules')."</a>";
						echo"</div>";
				/* catalog rules end */
						
				/* privacy policy start */
				echo"<div class=\"privacy_policy_block\">";
				$pp_art=Module::getHelper('article','article')->getArticle(intval(siteConfig::$privacy_policy_article));
				echo "<input class=\"commonEdit required\" type=\"checkbox\" name=\"privacy_policy_agree\" value=\"1\" required=\"required\" />"
					."&nbsp;".Text::_('I have read and agreed with')
					." <a rel=\"nofollow\" class=\"relpopuptext\" href=\"".Router::_("index.php?module=article&amp;view=read&amp;psid=".$pp_art->a_id."&amp;alias=".$pp_art->a_alias."&amp;notmpl=1")."\">".Text::_('privacy policy')."</a>";
				echo"</div>";
				/* privacy policy end */
				
				$dt_default_class=catalogDelivery::getDeliveryClass($this->dt_selected);
				?>
				<div class="buttons">
					<input type="hidden" name="module" value="catalog" />
					<input type="hidden" name="view" value="orders" />
					<input type="hidden" name="layout" value="new" />
					<input type="hidden" name="step" value="3" />		
					<input <?php echo ($dt_default_class->needRecalc() ? "" : "style=\"display:none;\"");?> id="calculate_delivery_button" class="shortcutButton btn btn-info" type="button" onclick="calculateDelivery(0); return false;" value="<?php echo Text::_("Calculate delivery"); ?>" />
					<input <?php echo ($dt_default_class->needRecalc() ? "style=\"display:none;\"" : "");?> id="submit_order_button" class="shortcutButton btn btn-info" type="button" onclick="orderOnSubmit();" value="<?php echo Text::_("Proceed order"); ?>" />
				</div>
			<?php } ?>
		</form>
	<?php } ?>
<?php } ?>