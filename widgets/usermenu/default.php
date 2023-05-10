<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class usermenuWidget extends Widget {
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("show_greeting", "boolean", 1);
		$this->addParam("login_popup", "boolean", 1);
		$this->addParam("show_register", "boolean", 1);
		$this->addParam("show_reminder", "boolean", 1);
		$this->addParam("show_post", "boolean", 1);
		$this->addParam("show_basket", "boolean", 1);
		$this->addParam("basket_popup", "boolean", 1);
		$this->addParam("show_admin", "boolean", 1);
	}
	public function render() {
		$loginHTML= "<div class=\"cabinet_menu\">";
		if (User::getInstance()->isLoggedIn()){
			// выясняем есть ли у пользователя непрочитанная почта
			$countMail=Module::getInstance('mail')->getModel('letter')->getCountUnreadLetter(User::getInstance()->u_id);
			if($countMail) $mailtext="<span class=\"MailUnread\">".Text::_("My mail")."  <span class=\"badge\">".$countMail."</span></span>";
			else $mailtext="<span>".Text::_("My mail")."</span>";
			if($this->getParam('show_greeting')) $loginHTML.="<p class=\"greeting\">".Text::_("Greeting").", ".User::getInstance()->u_nickname."</p>";
			$loginHTML.="<ul class=\"dot\">
							<li><a class=\"logout\" rel=\"nofollow\" href=\"".Router::_("index.php?option=logout")."\"><span>".Text::_("Logout")."</span></a></li>
							<li><a class=\"cabinet\" rel=\"nofollow\" href=\"".Router::_("index.php?module=user&amp;view=panel&at=tab_1")."\"><span>".Text::_("Cabinet")."</span></a></li>
						";
			if($this->getParam('show_post')) $loginHTML.="<li><a class=\"posta\" rel=\"nofollow\" href=\"".Router::_("index.php?module=mail")."\"><span>".$mailtext."</span></a></li>";
			if(!catalogConfig::$ordersDisabled && $this->getParam('show_basket')){
				if($this->getParam('basket_popup')) {
					$loginHTML .= "<li><a class=\"but_basket relpopupwt\" title=\"".Text::_("Basket")."\" rel=\"nofollow\" href=\"".Router::_("index.php?module=catalog&amp;task=ShowBasket&amp;option=ajax")."\"><span>".Text::_("Basket")."</span></a></li>";
				} else {
					$loginHTML .= "<li><a class=\"but_basket\" rel=\"nofollow\" href=\"".Router::_("index.php?module=catalog&view=orders&layout=basket")."\"><span>".Text::_("Basket")."</span></a></li>";
				}
			}
			if(User::getInstance()->isAdmin() &&  $this->getParam('show_admin')){
				$loginHTML .= "<li><a class=\"admin-link\" rel=\"nofollow\" href=\"".Router::_("administrator/index.php")."\"><span>".Text::_("Administration")."</span></a></li>";
			}
			$loginHTML .= "</ul>";
		}	else {
			if($this->getParam('login_popup')) {
				$loginHTML.= "<ul class=\"dot\">";
				$loginHTML.= "<li><a rel=\"nofollow\" class=\"login relpopup\" href=\"".Router::_("index.php?module=user&amp;view=login")."\"><span>".Text::_("Log in")."</span></a></li>";
			}	else 	{
				$suffix='';
				$loginHTML.="<div class=\"authorizeBlock".$suffix."\">
								<div id=\"loginForm_w\" class=\"commonPopup\">
									<form action=\"".Router::_("index.php")."\" method=\"post\">
										<input type=\"hidden\" name=\"option\" value=\"login\" />
										<input type=\"hidden\" name=\"return_url\" value=\"".Util::getReturnUrl()."\" />
										<p class=\"login_label\">".(backofficeConfig::$allowEmailLogin ? HTMLControls::renderLabelField(false,'Email',true) : HTMLControls::renderLabelField(false,'Login name',true)).":</p>
										<p class=\"login_input\"><input type=\"text\"  class=\"commonEdit loginEdit\" name=\"username\" value=\"\" /></p>
										<p class=\"pwd_label\">".HTMLControls::renderLabelField(false,'Password',true).":</p>
										<p class=\"pwd_input\"><input type=\"password\"  class=\"commonEdit loginEdit\" name=\"userpass\" value=\"\" /></p>
										<p class=\"remember_label\"><input type=\"checkbox\" id=\"remember_mew\" name=\"remember\" />&nbsp;".HTMLControls::renderLabelField('remember_mew','Remember me',true)."</p>
										<div id=\"loginButtons_w\">".HTMLControls::renderButton("login_w",Text::_('Log in'),"submit")."</div>
									</form>
								</div>
							</div>";
				$loginHTML.= "<ul class=\"dot\">";
			}
			if (!backofficeConfig::$noRegistration && $this->getParam('show_register')) $loginHTML.= "	<li><a rel=\"nofollow\" class=\"register\" href=\"".Router::_("index.php?module=user&amp;view=register")."\"><span>".Text::_("Register")."</span></a></li>";
			if($this->getParam('show_reminder')) $loginHTML.= "<li><a rel=\"nofollow\" class=\"remind_pass relpopup\" href=\"".Router::_("index.php?module=user&amp;view=confirm&amp;layout=password")."\"><span>".Text::_("Remind password")."</span></a></li>";
			if(!catalogConfig::$ordersDisabled && $this->getParam('show_basket'))	$loginHTML .= "<li><a class=\"but_basket relpopupwt\" title=\"".Text::_("Basket")."\" rel=\"nofollow\" href=\"".Router::_("index.php?module=catalog&amp;task=ShowBasket&amp;option=ajax")."\"><span>".Text::_("Basket")."</span></a></li>";
			$loginHTML.= "</ul>";
		}
		
		$loginHTML .= "</div>";
		return $loginHTML;
	}
}
?>