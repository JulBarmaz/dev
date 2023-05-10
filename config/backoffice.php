<?php
//BARMAZ_COPYRIGHT_TEMPLATE
defined('_BARMAZ_VALID') or die("Access denied");

class backofficeConfig {
	public static $noRegistration=false;
	public static $floodDelay=5;
	public static $cryptoUserData=false;
	public static $cryptoPath="";
	public static $backupPath="";
	public static $allowSNLogin=false;
	public static $sameSitecookie="Strict";
	public static $secureCoockie=false;	
	public static $allowEmailLogin=false;
	public static $regConfirmation=1;
	public static $defaultUserRole=2;
	public static $allowAutoExchange=0;
	public static $autoExchangeLogin="autoExchangeLogin";
	public static $secretCode="Secret Code";
	public static $unlockKey="";
	public static $updatesBetaChannel=false;
}
?>