<?php
//BARMAZ_COPYRIGHT_TEMPLATE
defined('_BARMAZ_VALID') or die("Access denied");

class siteConfig {
	// Developer mode enabled/disabled
	public static	$license_key	= true;
//	public static	$gzipMode	= false;
	public static	$enableGeneratorMetaTag	= false;
	public static	$cacheLife	= 0;
	// Site enabled/disabled
	public static	$siteDisabled		= false;
	// Debug mode on/off
	public static	$debugMode			= 0;
	public static	$debugIP			= "";
	public static	$siteMemoryLimit		= 20;
	public static	$siteTimeLimit			= 30;
	public static	$cssOverride			= false;
	public static	$loadBootstrap			= true;
	public static	$useJQueryUIMini		= true;
	// Default language
	public static	$site_rules_article	= 2;
	public static	$privacy_policy_article	= 3;
	
	public static	$defaultLanguage	= 'ru';

	// Cookie life time (hours)
	public static	$cookieLifeTime		= 1440;	// Week

	public static	$pagesPerPanel		= 10;
	public static	$recordsPerPage		= 10;
	public static	$shortTextLength		= 350;
	public static	$searchTargetBlank	= true;
	public static	$searchMenuID		= 25;
	
	public static	$treeDepth		= 20;

	public static	$siteDomain			= 'barmaz.local';
	public static	$sitePort			= '80';
	public static	$siteSSLPort		= '443';
	// Site template
	public static	$siteTemplate		= 'zbs5';
	public static	$siteTemplatesByMenu= false;
	// default module and item in this module
	public static	$defaultModule		= 'site';
	public static	$defaultMenuID		= 32;
	// Site default module
	public static	$allowedType		= "bmp;csv;gif;jpeg;jpg;mp4;ogg;ogv;pdf;png;swf;webm;zip";
	// Site title
	public static	$metaTitle			= "BARMAZ DEMO site";
	// Site description
	public static	$metaDescription	= "";
	// Site keywords
	public static	$metaKeywords		= "";
	// Site support email
	public static	$sendFeedbacksByMail	= 1;
	// Module breadcrumbs
//	public static	$showBreadcrumbs	= true;
	public static	$useMultiVote = false;
	public static	$useTextAddress=true;
	public static	$use_points_system	= true;
	public static	$def_account_bonus	= 0;
	public static	$use_referral_system=true;
	public static	$intervalNumFilter=false;
}
?>