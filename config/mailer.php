<?php
//BARMAZ_COPYRIGHT_TEMPLATE
defined('_BARMAZ_VALID') or die("Access denied");

class mailerConfig {
	public static $robotNotifier	= 0;
	public static $robotNotifierMessages	= 10;
	
	public static $robotEmail 						= "admin@localhost";
	public static $useSMTP							= false;
	public static $logErrors						= true;
	public static $smtpServer 						= "127.0.0.1";
	public static $smtpPort 						= 25;
	public static $smtpUser 						= "guest";
	public static $smtpPassword						= "guest";
	public static $pop3Server 						= "127.0.0.1";
	public static $pop3Port 						= "110";
	public static $pop3User 						= "guest";
	public static $pop3Password 					= "guest";
	public static $exchangeEmail 					= "admin@localhost";
//	public static $exchangePassword					= "Волна";
	public static $smsEnabled						= false;
	public static $smsProvider						= "";
	public static $smsUser							= "";
	public static $smsPassword						= "";
	public static $smsSender						= "";
}
?>