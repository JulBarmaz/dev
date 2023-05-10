<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class systemPluginrecaptcha extends Plugin {
	protected $_events=array("system.executeModuleBefore", "captcha.renderPicture","captcha.renderForm","captcha.checkResult");
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("using_plugin", "ro_string", Text::_("system_recaptcha_description"));
		$this->addParam("publickey", "string", "6LcyM8MSAAAAAC_YR63lRdol_enDVjWXzQVs_f4K");
		$this->addParam("privatekey", "string", "6LcyM8MSAAAAAB-KdKyg__BMnusAfCm2z8QjzD2-");
		$this->addParam("api_version", "select", "2.0", false, array("2.0"=>"2.0", "3.0"=>"3.0 (Beta version)"));
		$this->addParam("Google_reCaptcha_v3_minimum_score", "select", "0.5", false, array("0.1"=>"0.1", "0.2"=>"0.2","0.3"=>"0.3","0.4"=>"0.4","0.5"=>"0.5","0.6"=>"0.6","0.7"=>"0.7","0.8"=>"0.8","0.9"=>"0.9"));
	}
	protected function onRaise($event, &$data) {
		if (Portal::getInstance()->isDisabled() || Portal::getInstance()->inPrintMode()) return "";
		$api_version=$this->getParam("api_version");
		if($api_version=="2.0"){
			switch($event){
				case "system.executeModuleBefore":
					$js = "var reCaptchaRequested=false;
							function debugRecaptcha(obj,dir){".(siteConfig::$debugMode ? "" : "return true;")."
								if( typeof( dir ) != 'undefined'){
									console.dir(obj);
								} else {
									console.log(obj);
								}
							}
							function applyRecaptcha(parent_prefix){
								if( typeof( parent_prefix ) != 'undefined' && parent_prefix) _current_prefix_ws = parent_prefix + ' ';  else _current_prefix_ws='';
								if (reCaptchaRequested !== false && typeof( grecaptcha ) !== 'undefined') {
									$(_current_prefix_ws + '.recaptcha').each(function(){
										id=grecaptcha.render($(this).get(0), {'sitekey' : $(this).attr('data-sitekey')});
										$(this).attr('recaptcha-id', id);
									});
								}
							}
							function requestReCapture(parent_prefix){
								if (reCaptchaRequested==false && typeof( grecaptcha ) === 'undefined') {
									reCaptchaRequested = setTimeout(function() {
										$.getScript('https://www.google.com/recaptcha/api.js?onload=applyRecaptcha&render=explicit', function() { 
											debugRecaptcha('reCaptcha loaded');
										});
									}, 1000);
								} else {
									applyRecaptcha(parent_prefix);
								}
							}
							$(window).on('load',function() {
								addAfterContentLoadHandler('requestReCapture'); 
							});
							";
					Portal::getInstance()->addScriptDeclaration($js);
					break;
				case "captcha.renderForm":
					$js="$(window).on('load',function() { requestReCapture(); });";
					Portal::getInstance()->addScriptDeclaration($js);
					$publickey=$this->getParam("publickey");
					$html = "<div class=\"captcha captcha-".intval($api_version)."\">";
					$html.= "<div class=\"recaptcha\" data-sitekey=\"".$publickey."\"></div>";
					$html.= "</div>";
					return $html;
					break;
				case "captcha.checkResult":
					if($this->getParam("privatekey")) {
						$privatekey=$this->getParam("privatekey");
						$ch = curl_init();
						$RemoteURL = "https://www.google.com/recaptcha/api/siteverify";
						$RequestTimeout = 4;
						$RemoteQuery["secret"]=$privatekey;
						$RemoteQuery["response"]=Request::getSafe("g-recaptcha-response");
						$RemoteQuery["remoteip"]=User::getInstance()->getIP();
						curl_setopt($ch, CURLOPT_URL, $RemoteURL); // set url to post to
						curl_setopt($ch, CURLOPT_FAILONERROR, 1);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
						curl_setopt($ch, CURLOPT_TIMEOUT, $RequestTimeout); // times out after 4s
						curl_setopt($ch, CURLOPT_POST, 1); // set POST method
						curl_setopt($ch, CURLOPT_POSTFIELDS, $RemoteQuery); // add POST fields
						$result = curl_exec($ch); // run the whole process
						curl_close($ch);
						$resp=json_decode($result, true);
						if (is_array($resp) && $resp["success"] == true) {
							$msg="OK";
						} else {
							$msg =Text::_("Wrong captcha code");
						}
					} else {
						$msg =Text::_("Wrong captcha key");
					}
					$_SESSION['captcha_string']=$msg;
					break;
				default: break;
			}
		} elseif($api_version=="3.0"){
			switch($event){
				case "system.executeModuleBefore":
					$publickey=$this->getParam("publickey");
					$js = "var reCaptchaRequested=false;
							function debugRecaptcha(obj,dir){".(siteConfig::$debugMode ? "" : "return true;")."
								if( typeof( dir ) != 'undefined'){
									console.dir(obj);
								} else {
									console.log(obj);
								}
							}
							function applyRecaptcha(parent_prefix){
								if( typeof( parent_prefix ) != 'undefined' && parent_prefix) _current_prefix_ws = parent_prefix + ' ';  else _current_prefix_ws='';
								if (reCaptchaRequested !== false && typeof( grecaptcha ) !== 'undefined') {
									$(_current_prefix_ws + '.recaptcha').each(function(){
										var site_key = $(this).attr('data-sitekey');
										var badge_placeholder = $(this).get(0);
										var current_form = $(this).parents('form').get(0);
										var custom_data_target = 'form';
										if(typeof($(this).parent('form').attr('data-target')) != 'undefined') custom_data_target = $(this).parent('form').attr('data-target');
										custom_data_target = custom_data_target.replace('-', '_');
										grecaptcha.ready(function() {
											// Valid values for 'badge' are 'inline', 'bottomleft', 'bottomright', and 'bottom'. 'bottom' and 'bottomright' are synonyms.
											var clientId = grecaptcha.render(badge_placeholder, {'sitekey' : site_key, 'badge': 'inline', 'size': 'invisible' });
											$(badge_placeholder).attr('recaptcha-id', clientId);
											var oldSubmit = $(current_form)[0].onsubmit;
											$(current_form)[0].onsubmit = null;
											$(current_form).bind('submit', function(event) { debugRecaptcha('I was first'); });
											$(current_form).bind('submit', function(event) { debugRecaptcha('I was second'); });
											$(current_form).bind('submit', function(event) {
												debugRecaptcha('I was third. But I\'ll be FIRST');
												event.preventDefault();
												grecaptcha.execute(clientId, { action: custom_data_target }).then(function(token) {
													debugRecaptcha('reCaptcha executed for recaptcha-id ' + clientId + ', prefix '+_current_prefix_ws+', site-key: '+site_key);
													res = true; 
													if (oldSubmit != undefined && oldSubmit != null) {
	 													res = oldSubmit.call(current_form);
														if(res) event.currentTarget.submit();
													} else {
														event.currentTarget.submit();
													}
												});
											});

											var eventList = $._data($(current_form)[0], 'events');
											if(typeof(eventList) != 'undefined' && typeof(eventList.submit) != 'undefined'){
												eventList.submit.unshift(eventList.submit.pop());
											} else {
												debugRecaptcha('eventList empty :(');
											}

											debugRecaptcha('reCaptcha rendered for recaptcha-id ' + clientId + ', prefix '+_current_prefix_ws+', site-key: '+site_key);
										});
									});
								}
							}
							function requestReCapture(parent_prefix){
								if (reCaptchaRequested==false && typeof( grecaptcha ) === 'undefined') {
									reCaptchaRequested = setTimeout(function() {
										// $.getScript('https://www.google.com/recaptcha/api.js?render=".$publickey."', function() {
										$.getScript('https://www.google.com/recaptcha/api.js?render=explicit&onload=applyRecaptcha', function() { 
											debugRecaptcha('reCaptcha script loaded');
											// applyRecaptcha(parent_prefix); 
										});
									}, 1000);
								} else {
									applyRecaptcha(parent_prefix);
								}
							}
							$(window).on('load',function() {
								addAfterContentLoadHandler('requestReCapture');
							});
							";
					Portal::getInstance()->addScriptDeclaration($js);
					break;
				case "captcha.renderForm":
					$js="$(window).on('load',function() { requestReCapture(); });";
					Portal::getInstance()->addScriptDeclaration($js);
					$publickey=$this->getParam("publickey");
					$html = "<div class=\"captcha captcha-".intval($api_version)."\">";
					$html.= "	<div class=\"recaptcha\" data-sitekey=\"".$publickey."\"></div>";
					/*
					$html.= "	<div class=\"recaptcha\">";
					$html.= "		<div class=\"badge-placeholder\"></div>";
					$html.= "		<input type=\"hidden\" name=\"g-recaptcha-response\" class=\"recaptcha-input\" data-sitekey=\"".$publickey."\" />";
					$html.= "	</div>";
					*/
					$html.= "</div>";
					return $html;
					break;
				case "captcha.checkResult":
					if($this->getParam("privatekey")) {
						$privatekey=$this->getParam("privatekey");
						$min_score=floatval($this->getParam("Google_reCaptcha_v3_minimum_score"));
						$ch = curl_init();
						$RemoteURL = "https://www.google.com/recaptcha/api/siteverify";
						$RequestTimeout = 4;
						$RemoteQuery["secret"]=$privatekey;
						$RemoteQuery["response"]=Request::getSafe("g-recaptcha-response");
						$RemoteQuery["remoteip"]=User::getInstance()->getIP();
						curl_setopt($ch, CURLOPT_URL, $RemoteURL); // set url to post to
						curl_setopt($ch, CURLOPT_FAILONERROR, 1);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
						curl_setopt($ch, CURLOPT_TIMEOUT, $RequestTimeout); // times out after 4s
						curl_setopt($ch, CURLOPT_POST, 1); // set POST method
						curl_setopt($ch, CURLOPT_POSTFIELDS, $RemoteQuery); // add POST fields
						$result = curl_exec($ch); // run the whole process
						curl_close($ch);
						$resp=json_decode($result, true);
						if (is_array($resp) && $resp["success"] == true && floatval($resp['score']) >= $min_score) {
							$msg="OK";
						} else {
							$msg =Text::_("Wrong captcha");
						}
					} else {
						$msg =Text::_("Wrong captcha key");
					}
					$_SESSION['captcha_string']=$msg;
					break;
				default: break;
			}
		}
	}
}
?>